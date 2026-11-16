<?php
/**
 * Model Category (kategori kuliner).
 */
final class Category
{
    public static function all(): array
    {
        return Database::pdo()
            ->query(
                "SELECT c.*,
                 (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id) AS product_count
                 FROM categories c ORDER BY c.name ASC"
            )
            ->fetchAll();
    }

    public static function allActive(): array
    {
        return Database::pdo()
            ->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY name ASC")
            ->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare("SELECT * FROM categories WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function nameTaken(string $name, int $ignoreId = 0): bool
    {
        $stmt = Database::pdo()->prepare(
            "SELECT id FROM categories WHERE LOWER(name) = LOWER(:name) AND id != :id LIMIT 1"
        );
        $stmt->execute(['name' => $name, 'id' => $ignoreId]);
        return (bool) $stmt->fetch();
    }

    public static function create(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            "INSERT INTO categories (name, slug, description, is_active)
             VALUES (:name, :slug, :description, :is_active)"
        );
        $stmt->execute([
            'name'        => $data['name'],
            'slug'        => unique_slug('categories', $data['name']),
            'description' => $data['description'] ?: null,
            'is_active'   => (int) $data['is_active'],
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public static function update(int $id, array $data): bool
    {
        $stmt = Database::pdo()->prepare(
            "UPDATE categories SET name = :name, slug = :slug, description = :description, is_active = :is_active
             WHERE id = :id"
        );
        return $stmt->execute([
            'name'        => $data['name'],
            'slug'        => unique_slug('categories', $data['name'], $id),
            'description' => $data['description'] ?: null,
            'is_active'   => (int) $data['is_active'],
            'id'          => $id,
        ]);
    }

    public static function setActive(int $id, bool $active): bool
    {
        $stmt = Database::pdo()->prepare("UPDATE categories SET is_active = :active WHERE id = :id");
        return $stmt->execute(['active' => (int) $active, 'id' => $id]);
    }

    /**
     * Hapus kategori: produk di dalamnya dipindahkan ke kategori lain
     * terdekat yang masih ada (fallback kategori pertama), atau kategori
     * tidak boleh dihapus jika hanya tersisa satu.
     */
    public static function delete(int $id): array
    {
        $total = (int) Database::pdo()->query("SELECT COUNT(*) FROM categories")->fetchColumn();
        if ($total <= 1) {
            return [false, 'Minimal harus tersisa satu kategori.'];
        }

        $target = Database::pdo()->query(
            "SELECT id FROM categories WHERE id != {$id} ORDER BY is_active DESC, name ASC LIMIT 1"
        )->fetchColumn();
        if (!$target) {
            return [false, 'Tidak ada kategori pengganti.'];
        }

        Database::pdo()->beginTransaction();
        try {
            $move = Database::pdo()->prepare("UPDATE products SET category_id = :target WHERE category_id = :id");
            $move->execute(['target' => $target, 'id' => $id]);
            Database::pdo()->prepare("DELETE FROM categories WHERE id = :id")->execute(['id' => $id]);
            Database::pdo()->commit();
            return [true, ''];
        } catch (Throwable $e) {
            Database::pdo()->rollBack();
            return [false, 'Gagal menghapus kategori.'];
        }
    }
}
