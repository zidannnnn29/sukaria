<?php
/**
 * Keranjang belanja pembeli — disimpan di session, tanpa login.
 * Item selalu divalidasi ulang terhadap database (produk aktif, seller aktif,
 * stok tersedia) setiap kali dibaca, agar tidak bisa memesan produk mati.
 */
final class Cart
{
    private const SESSION_KEY = 'cart_items';

    /** Tambah produk (qty >= 1). Membatasi qty terhadap stok yang ada. */
    public static function add(int $productId, int $qty = 1): array
    {
        $product = self::purchasableProduct($productId);
        if (!$product) {
            return [false, 'Produk tidak tersedia atau sudah tidak dijual.'];
        }

        $cart = self::raw();
        $current = (int) ($cart[$productId] ?? 0);
        $desired = $current + max(1, $qty);
        $max     = max(0, (int) $product['stock']);

        if ($max === 0) {
            if ($current > 0) {
                unset($cart[$productId]);
                self::save($cart);
            }
            return [false, 'Stok "' . $product['name'] . '" sedang habis.'];
        }

        if ($desired > $max) {
            $cart[$productId] = $max;
            self::save($cart);
            return $desired <= 1
                ? [true, '"' . $product['name'] . '" ditambahkan ke keranjang (stok terbatas).']
                : [true, 'Stok "' . $product['name'] . '" hanya ' . $max . ' — jumlah disesuaikan.'];
        }

        $cart[$productId] = $desired;
        self::save($cart);
        return $current > 0
            ? [true, 'Jumlah "' . $product['name'] . '" diperbarui (' . $desired . ').']
            : [true, '"' . $product['name'] . '" ditambahkan ke keranjang.'];
    }

    /** Set jumlah tertentu (0 = hapus). Dibatasi stok yang ada. */
    public static function set(int $productId, int $qty): array
    {
        $cart = self::raw();
        if (!isset($cart[$productId]) && $qty > 0) {
            return self::add($productId, $qty);
        }

        if ($qty <= 0) {
            unset($cart[$productId]);
            self::save($cart);
            return [true, 'Produk dihapus dari keranjang.'];
        }

        $product = self::purchasableProduct($productId);
        if (!$product) {
            unset($cart[$productId]);
            self::save($cart);
            return [false, 'Produk tidak lagi tersedia dan dihapus dari keranjang.'];
        }

        $max = max(0, (int) $product['stock']);
        if ($qty > $max) {
            $qty = $max;
        }
        if ($qty <= 0) {
            unset($cart[$productId]);
            self::save($cart);
            return [false, 'Stok "' . $product['name'] . '" habis — produk dihapus dari keranjang.'];
        }

        $cart[$productId] = $qty;
        self::save($cart);
        return [true, 'Jumlah "' . $product['name'] . '" diperbarui (' . $qty . ').'];
    }

    public static function remove(int $productId): void
    {
        $cart = self::raw();
        unset($cart[$productId]);
        self::save($cart);
    }

    public static function clear(): void
    {
        self::save([]);
    }

    /** Jumlah jenis produk di keranjang (untuk badge header). */
    public static function count(): int
    {
        return count(self::raw());
    }

    /**
     * Item keranjang yang sudah divalidasi ulang dengan database.
     * Item tidak valid (produk nonaktif/seller nonaktif/habis) dibuang otomatis.
     *
     * @return array{0: list<array>, 1: list<string>} [items, warnings]
     */
    public static function validatedItems(): array
    {
        $cart = self::raw();
        if (!$cart) {
            return [[], []];
        }

        $ids  = array_map('intval', array_keys($cart));
        $ph   = implode(',', array_fill(0, count($ids), '?'));
        $stmt = Database::pdo()->prepare(
            "SELECT p.id, p.name, p.price, p.stock, p.image_path, u.store_name, u.is_active AS seller_active,
                    c.name AS category_name
             FROM products p
             JOIN users u ON u.id = p.seller_id
             JOIN categories c ON c.id = p.category_id
             WHERE p.id IN ($ph) AND p.is_active = 1"
        );
        $stmt->execute($ids);

        $products = [];
        foreach ($stmt->fetchAll() as $row) {
            $products[(int) $row['id']] = $row;
        }

        $items    = [];
        $warnings = [];
        foreach ($cart as $pid => $qty) {
            $pid = (int) $pid;
            $p   = $products[$pid] ?? null;
            if (!$p) {
                unset($cart[$pid]);
                $warnings[] = 'Sebuah produk sudah tidak tersedia dan dihapus dari keranjang.';
                continue;
            }
            if ((int) $p['seller_active'] !== 1) {
                unset($cart[$pid]);
                $warnings[] = '"' . $p['name'] . '" dihapus: toko penjual sedang dinonaktifkan.';
                continue;
            }
            $stock = max(0, (int) $p['stock']);
            if ($stock === 0) {
                unset($cart[$pid]);
                $warnings[] = '"' . $p['name'] . '" dihapus: stok habis.';
                continue;
            }
            $qty = min((int) $qty, $stock);
            if ((int) $qty !== (int) $cart[$pid]) {
                $warnings[] = 'Jumlah "' . $p['name'] . '" disesuaikan dengan stok (' . $qty . ').';
            }
            $items[] = [
                'product_id' => $pid,
                'name'       => (string) $p['name'],
                'store_name' => (string) ($p['store_name'] ?: 'Toko Sukaria'),
                'category'   => (string) $p['category_name'],
                'price'      => (int) $p['price'],
                'qty'        => $qty,
                'stock'      => $stock,
                'subtotal'   => (int) $p['price'] * $qty,
                'image'      => product_image_url([
                    'image_path'    => $p['image_path'],
                    'name'          => $p['name'],
                    'category_name' => $p['category_name'],
                ]),
            ];
        }

        self::save($cart);
        return [$items, array_values(array_unique($warnings))];
    }

    public static function subtotal(array $items): int
    {
        return array_sum(array_column($items, 'subtotal'));
    }

    // -----------------------------------------------------------------

    /** Produk boleh dibeli: aktif, kategori aktif, seller aktif, stok > 0. */
    private static function purchasableProduct(int $productId): ?array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT p.id, p.name, p.stock, p.is_active
             FROM products p
             JOIN users u        ON u.id = p.seller_id
             JOIN categories c   ON c.id = p.category_id
             WHERE p.id = :id
               AND p.is_active = 1
               AND u.is_active = 1
               AND c.is_active = 1
             LIMIT 1"
        );
        $stmt->execute(['id' => $productId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** @return array<int, int> product_id => qty */
    private static function raw(): array
    {
        $cart = $_SESSION[self::SESSION_KEY] ?? [];
        $clean = [];
        foreach ($cart as $pid => $qty) {
            $pid = (int) $pid;
            $qty = (int) $qty;
            if ($pid > 0 && $qty > 0) {
                $clean[$pid] = $qty;
            }
        }
        return $clean;
    }

    private static function save(array $cart): void
    {
        $_SESSION[self::SESSION_KEY] = $cart;
    }
}
