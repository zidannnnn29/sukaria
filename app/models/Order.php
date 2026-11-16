<?php
/**
 * Model pesanan transfer (keranjang belanja).
 */
final class Order
{
    public const STATUSES = ['pending', 'verifying', 'paid', 'rejected', 'cancelled'];

    /** Label & kelas warna status untuk UI. */
    public static function statusMeta(string $status): array
    {
        return match ($status) {
            'pending'   => ['label' => 'Menunggu Pembayaran', 'class' => 'bg-sogan-100 text-sogan-700 border border-sogan-400/50'],
            'verifying' => ['label' => 'Verifikasi Bukti Transfer', 'class' => 'bg-coffeeleaf-100 text-coffeeleaf-700 border border-coffeeleaf-600/30'],
            'paid'      => ['label' => 'Lunas', 'class' => 'bg-coffeeleaf-600 text-white border border-coffeeleaf-700'],
            'rejected'  => ['label' => 'Bukti Ditolak', 'class' => 'bg-osing-100 text-osing-700 border border-osing-500/40'],
            'cancelled' => ['label' => 'Dibatalkan', 'class' => 'bg-cotton-200 text-espresso-700 border border-cotton-300'],
            default     => ['label' => ucfirst($status), 'class' => 'bg-cotton-200 text-espresso-700 border border-cotton-300'],
        };
    }

    /** Buat pesanan + itemnya dari keranjang tervalidasi (transaksi, kurangi stok). */
    public static function createFromCart(array $items, int $total, string $buyerName, string $buyerPhone, string $note): ?array
    {
        $pdo = Database::pdo();
        $orderNo = self::generateOrderNo();

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare(
                "INSERT INTO orders (order_no, buyer_name, buyer_phone, buyer_note, total_amount, status)
                 VALUES (:order_no, :buyer_name, :buyer_phone, :buyer_note, :total_amount, 'pending')"
            );
            $stmt->execute([
                'order_no'    => $orderNo,
                'buyer_name'  => $buyerName,
                'buyer_phone' => $buyerPhone,
                'buyer_note'  => $note !== '' ? $note : null,
                'total_amount'=> $total,
            ]);
            $orderId = (int) $pdo->lastInsertId();

            $itemStmt = $pdo->prepare(
                "INSERT INTO order_items (order_id, product_id, seller_id, product_name, store_name, unit_price, qty, subtotal)
                 VALUES (:order_id, :product_id, :seller_id, :product_name, :store_name, :unit_price, :qty, :subtotal)"
            );
            $stockStmt = $pdo->prepare(
                "UPDATE products SET stock = GREATEST(stock - :qty, 0) WHERE id = :id"
            );

            foreach ($items as $item) {
                $itemStmt->execute([
                    'order_id'     => $orderId,
                    'product_id'   => $item['product_id'],
                    'seller_id'    => self::sellerIdOf($item['product_id']),
                    'product_name' => $item['name'],
                    'store_name'   => $item['store_name'],
                    'unit_price'   => $item['price'],
                    'qty'          => $item['qty'],
                    'subtotal'     => $item['subtotal'],
                ]);
                $stockStmt->execute(['qty' => $item['qty'], 'id' => $item['product_id']]);
            }

            $pdo->commit();
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log('SUKARIA order create failed: ' . $e->getMessage());
            return null;
        }

        return self::findByNo($orderNo);
    }

    public static function findByNo(string $orderNo): ?array
    {
        $stmt = Database::pdo()->prepare("SELECT * FROM orders WHERE order_no = :no LIMIT 1");
        $stmt->execute(['no' => $orderNo]);
        $order = $stmt->fetch();
        if (!$order) {
            return null;
        }
        $order['items'] = self::itemsOf((int) $order['id']);
        return $order;
    }

    public static function itemsOf(int $orderId): array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT oi.*, p.image_path, c.name AS category_name
             FROM order_items oi
             LEFT JOIN products p ON p.id = oi.product_id
             LEFT JOIN categories c ON c.id = p.category_id
             WHERE oi.order_id = :id
             ORDER BY oi.id ASC"
        );
        $stmt->execute(['id' => $orderId]);
        return $stmt->fetchAll();
    }

    /** Daftar semua pesanan (admin). */
    public static function all(?string $status = null): array
    {
        $sql = "SELECT o.*,
                       (SELECT COUNT(*) FROM order_items oi WHERE oi.order_id = o.id) AS item_count
                FROM orders o";
        $params = [];
        if ($status !== null && $status !== '' && in_array($status, self::STATUSES, true)) {
            $sql .= " WHERE o.status = :status";
            $params['status'] = $status;
        }
        $sql .= " ORDER BY o.created_at DESC, o.id DESC LIMIT 200";
        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** Pesanan yang memuat produk milik seller tertentu (read-only). */
    public static function forSeller(int $sellerId): array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT DISTINCT o.id, o.order_no, o.buyer_name, o.buyer_phone,
                    o.total_amount, o.status, o.created_at
             FROM orders o
             JOIN order_items oi ON oi.order_id = o.id
             WHERE oi.seller_id = :sid
             ORDER BY o.created_at DESC, o.id DESC
             LIMIT 200"
        );
        $stmt->execute(['sid' => $sellerId]);
        $orders = $stmt->fetchAll();

        if ($orders) {
            $ids = array_map(fn($o) => (int) $o['id'], $orders);
            $params = ['sid' => $sellerId];
            $phs = [];
            foreach ($ids as $i => $id) {
                $params["oid$i"] = $id;
                $phs[] = ":oid$i";
            }
            $ph = implode(',', $phs);
            $stmt = Database::pdo()->prepare(
                "SELECT oi.* FROM order_items oi
                 WHERE oi.order_id IN ($ph) AND oi.seller_id = :sid
                 ORDER BY oi.id ASC"
            );
            $stmt->execute($params);
            $itemsByOrder = [];
            foreach ($stmt->fetchAll() as $row) {
                $itemsByOrder[(int) $row['order_id']][] = $row;
            }
            foreach ($orders as &$order) {
                $order['items'] = $itemsByOrder[(int) $order['id']] ?? [];
            }
            unset($order);
        }

        return $orders;
    }

    /** Statistik pesanan untuk dashboard admin. */
    public static function adminStats(): array
    {
        $pdo = Database::pdo();
        $row = $pdo->query(
            "SELECT
                COUNT(*) AS total_orders,
                SUM(CASE WHEN status = 'pending'   THEN 1 ELSE 0 END) AS pending_count,
                SUM(CASE WHEN status = 'verifying' THEN 1 ELSE 0 END) AS verifying_count,
                SUM(CASE WHEN status = 'paid'      THEN 1 ELSE 0 END) AS paid_count,
                COALESCE(SUM(CASE WHEN status = 'paid' THEN total_amount ELSE 0 END), 0) AS paid_revenue
             FROM orders"
        )->fetch();
        return [
            'total_orders'    => (int) ($row['total_orders'] ?? 0),
            'pending_count'   => (int) ($row['pending_count'] ?? 0),
            'verifying_count' => (int) ($row['verifying_count'] ?? 0),
            'paid_count'      => (int) ($row['paid_count'] ?? 0),
            'paid_revenue'    => (int) ($row['paid_revenue'] ?? 0),
        ];
    }

    public static function setProof(int $orderId, string $filename): bool
    {
        $stmt = Database::pdo()->prepare(
            "UPDATE orders SET proof_path = :proof, proof_at = CURRENT_TIMESTAMP,
                    status = 'verifying'
             WHERE id = :id AND status IN ('pending', 'rejected')"
        );
        return $stmt->execute(['proof' => $filename, 'id' => $orderId]);
    }

    public static function markPaid(int $orderId): bool
    {
        $stmt = Database::pdo()->prepare(
            "UPDATE orders SET status = 'paid', paid_at = CURRENT_TIMESTAMP
             WHERE id = :id AND status IN ('pending', 'verifying')"
        );
        return $stmt->execute(['id' => $orderId]);
    }

    public static function reject(int $orderId): bool
    {
        return self::closeOrder($orderId, 'rejected');
    }

    public static function cancel(int $orderId): bool
    {
        return self::closeOrder($orderId, 'cancelled');
    }

    // -----------------------------------------------------------------

    /** Tutup pesanan (tolak/batalkan): kembalikan stok produk. */
    private static function closeOrder(int $orderId, string $status): bool
    {
        $pdo = Database::pdo();
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare(
                "UPDATE orders SET status = :status
                 WHERE id = :id AND status IN ('pending', 'verifying')"
            );
            $stmt->execute(['status' => $status, 'id' => $orderId]);
            $changed = $stmt->rowCount() > 0;

            if ($changed) {
                $items = $pdo->prepare("SELECT product_id, qty FROM order_items WHERE order_id = :id");
                $items->execute(['id' => $orderId]);
                $restore = $pdo->prepare("UPDATE products SET stock = stock + :qty WHERE id = :id");
                foreach ($items->fetchAll() as $item) {
                    if ($item['product_id'] !== null) {
                        $restore->execute(['qty' => (int) $item['qty'], 'id' => (int) $item['product_id']]);
                    }
                }
            }

            $pdo->commit();
            return $changed;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log('SUKARIA order close failed: ' . $e->getMessage());
            return false;
        }
    }

    private static function sellerIdOf(int $productId): ?int
    {
        $stmt = Database::pdo()->prepare("SELECT seller_id FROM products WHERE id = :id");
        $stmt->execute(['id' => $productId]);
        $sid = $stmt->fetchColumn();
        return $sid === false ? null : (int) $sid;
    }

    private static function generateOrderNo(): string
    {
        $pdo = Database::pdo();
        $attempt = 0;
        do {
            $no = 'SUK-' . date('ymd') . '-' . strtoupper(bin2hex(random_bytes(2)));
            $stmt = $pdo->prepare("SELECT id FROM orders WHERE order_no = :no LIMIT 1");
            $stmt->execute(['no' => $no]);
            $attempt++;
        } while ($stmt->fetch() && $attempt < 10);
        return $no;
    }
}
