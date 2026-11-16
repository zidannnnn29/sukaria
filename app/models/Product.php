<?php
/**
 * Model Product (produk UMKM).
 */
final class Product
{
    public static function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT p.*, c.name AS category_name
             FROM products p JOIN categories c ON c.id = p.category_id
             WHERE p.id = :id LIMIT 1"
        );
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function findForSeller(int $id, int $sellerId): ?array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT p.*, c.name AS category_name
             FROM products p JOIN categories c ON c.id = p.category_id
             WHERE p.id = :id AND p.seller_id = :seller LIMIT 1"
        );
        $stmt->execute(['id' => $id, 'seller' => $sellerId]);
        return $stmt->fetch() ?: null;
    }

    public static function forSeller(int $sellerId): array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT p.*, c.name AS category_name
             FROM products p JOIN categories c ON c.id = p.category_id
             WHERE p.seller_id = :seller
             ORDER BY p.is_featured DESC, p.created_at DESC"
        );
        $stmt->execute(['seller' => $sellerId]);
        return $stmt->fetchAll();
    }

    /** Katalog publik: hanya produk & seller & kategori aktif. */
    public static function forCatalog(): array
    {
        return Database::pdo()
            ->query(
                "SELECT p.id, p.name, p.slug, p.description, p.price, p.stock,
                        p.image_path, p.is_featured,
                        c.name AS category_name, c.slug AS category_slug,
                        u.store_name AS seller_name,
                        COALESCE(s.whatsapp_number, '" . SUKARIA_DEFAULT_WA . "') AS wa_number
                 FROM products p
                 JOIN categories c ON c.id = p.category_id AND c.is_active = 1
                 JOIN users u      ON u.id = p.seller_id AND u.is_active = 1
                 LEFT JOIN store_settings s ON s.seller_id = u.id
                 WHERE p.is_active = 1
                 ORDER BY p.is_featured DESC, p.created_at DESC"
            )
            ->fetchAll();
    }

    public static function slugTaken(string $slug, int $ignoreId = 0): bool
    {
        $stmt = Database::pdo()->prepare(
            "SELECT id FROM products WHERE slug = :slug AND id != :id LIMIT 1"
        );
        $stmt->execute(['slug' => $slug, 'id' => $ignoreId]);
        return (bool) $stmt->fetch();
    }

    public static function create(int $sellerId, array $data): int
    {
        $stmt = Database::pdo()->prepare(
            "INSERT INTO products (seller_id, category_id, name, slug, description, price, stock, image_path, is_featured, is_active)
             VALUES (:seller_id, :category_id, :name, :slug, :description, :price, :stock, :image_path, :is_featured, :is_active)"
        );
        $stmt->execute([
            'seller_id'   => $sellerId,
            'category_id' => (int) $data['category_id'],
            'name'        => $data['name'],
            'slug'        => unique_slug('products', $data['name']),
            'description' => $data['description'] ?: null,
            'price'       => (int) $data['price'],
            'stock'       => (int) $data['stock'],
            'image_path'  => $data['image_path'],
            'is_featured' => (int) $data['is_featured'],
            'is_active'   => (int) $data['is_active'],
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public static function update(int $id, int $sellerId, array $data): bool
    {
        $stmt = Database::pdo()->prepare(
            "UPDATE products SET category_id = :category_id, name = :name, slug = :slug,
             description = :description, price = :price, stock = :stock,
             image_path = :image_path, is_featured = :is_featured, is_active = :is_active
             WHERE id = :id AND seller_id = :seller_id"
        );
        return $stmt->execute([
            'category_id' => (int) $data['category_id'],
            'name'        => $data['name'],
            'slug'        => unique_slug('products', $data['name'], $id),
            'description' => $data['description'] ?: null,
            'price'       => (int) $data['price'],
            'stock'       => (int) $data['stock'],
            'image_path'  => $data['image_path'],
            'is_featured' => (int) $data['is_featured'],
            'is_active'   => (int) $data['is_active'],
            'id'          => $id,
            'seller_id'   => $sellerId,
        ]);
    }

    public static function toggleFeatured(int $id, int $sellerId): ?bool
    {
        $product = self::findForSeller($id, $sellerId);
        if (!$product) return null;
        $next = (int) $product['is_featured'] === 1 ? 0 : 1;
        $stmt = Database::pdo()->prepare(
            "UPDATE products SET is_featured = :v WHERE id = :id AND seller_id = :seller"
        );
        $stmt->execute(['v' => $next, 'id' => $id, 'seller' => $sellerId]);
        return (bool) $next;
    }

    public static function toggleActive(int $id, int $sellerId): ?bool
    {
        $product = self::findForSeller($id, $sellerId);
        if (!$product) return null;
        $next = (int) $product['is_active'] === 1 ? 0 : 1;
        $stmt = Database::pdo()->prepare(
            "UPDATE products SET is_active = :v WHERE id = :id AND seller_id = :seller"
        );
        $stmt->execute(['v' => $next, 'id' => $id, 'seller' => $sellerId]);
        return (bool) $next;
    }

    public static function delete(int $id, int $sellerId): ?array
    {
        $product = self::findForSeller($id, $sellerId);
        if (!$product) return null;
        $stmt = Database::pdo()->prepare("DELETE FROM products WHERE id = :id AND seller_id = :seller");
        $stmt->execute(['id' => $id, 'seller' => $sellerId]);
        return $product;
    }

    public static function sellerStats(int $sellerId): array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT
               COUNT(*) AS total_products,
               COALESCE(SUM(stock), 0) AS total_stock,
               SUM(is_featured = 1) AS featured_count,
               SUM(stock = 0) AS out_of_stock,
               SUM(stock > 0 AND stock <= 5) AS low_stock
             FROM products WHERE seller_id = :seller"
        );
        $stmt->execute(['seller' => $sellerId]);
        $row = $stmt->fetch() ?: [];
        return [
            'total_products' => (int) ($row['total_products'] ?? 0),
            'total_stock'    => (int) ($row['total_stock'] ?? 0),
            'featured_count' => (int) ($row['featured_count'] ?? 0),
            'out_of_stock'   => (int) ($row['out_of_stock'] ?? 0),
            'low_stock'      => (int) ($row['low_stock'] ?? 0),
        ];
    }

    public static function adminStats(): array
    {
        $pdo = Database::pdo();
        return [
            'total_products'   => (int) $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn(),
            'active_products'  => (int) $pdo->query("SELECT COUNT(*) FROM products WHERE is_active = 1")->fetchColumn(),
            'featured'         => (int) $pdo->query("SELECT COUNT(*) FROM products WHERE is_featured = 1")->fetchColumn(),
            'out_of_stock'     => (int) $pdo->query("SELECT COUNT(*) FROM products WHERE stock = 0")->fetchColumn(),
        ];
    }
}
