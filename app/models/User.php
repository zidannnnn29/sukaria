<?php
/**
 * Model User (admin & seller).
 */
final class User
{
    public static function all(?string $search = null): array
    {
        $sql = "SELECT u.*,
                (SELECT COUNT(*) FROM products p WHERE p.seller_id = u.id) AS product_count
                FROM users u";
        $params = [];
        if ($search !== null && $search !== '') {
            $sql .= " WHERE u.name LIKE :q OR u.email LIKE :q OR u.store_name LIKE :q";
            $params['q'] = '%' . $search . '%';
        }
        $sql .= " ORDER BY u.role ASC, u.name ASC";
        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function findByEmail(string $email): ?array
    {
        $stmt = Database::pdo()->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => strtolower($email)]);
        return $stmt->fetch() ?: null;
    }

    public static function emailTaken(string $email, int $ignoreId = 0): bool
    {
        $stmt = Database::pdo()->prepare(
            "SELECT id FROM users WHERE email = :email AND id != :id LIMIT 1"
        );
        $stmt->execute(['email' => strtolower($email), 'id' => $ignoreId]);
        return (bool) $stmt->fetch();
    }

    public static function create(array $data): int
    {
        $stmt = Database::pdo()->prepare(
            "INSERT INTO users (name, email, password_hash, role, store_name, is_active)
             VALUES (:name, :email, :password_hash, :role, :store_name, :is_active)"
        );
        $stmt->execute([
            'name'          => $data['name'],
            'email'         => strtolower($data['email']),
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role'          => $data['role'],
            'store_name'    => $data['store_name'] ?: null,
            'is_active'     => (int) $data['is_active'],
        ]);
        $id = (int) Database::pdo()->lastInsertId();
        if ($data['role'] === 'seller') {
            StoreSetting::ensure($id, SUKARIA_DEFAULT_WA);
        }
        return $id;
    }

    public static function update(int $id, array $data): bool
    {
        $user = self::find($id);
        if (!$user) return false;

        $stmt = Database::pdo()->prepare(
            "UPDATE users SET name = :name, email = :email, role = :role,
             store_name = :store_name, is_active = :is_active WHERE id = :id"
        );
        $ok = $stmt->execute([
            'name'       => $data['name'],
            'email'      => strtolower($data['email']),
            'role'       => $data['role'],
            'store_name' => $data['store_name'] ?: null,
            'is_active'  => (int) $data['is_active'],
            'id'         => $id,
        ]);

        // Role berubah → sinkronkan pengaturan toko
        if ($data['role'] === 'seller') {
            StoreSetting::ensure($id, SUKARIA_DEFAULT_WA);
        } else {
            Database::pdo()->prepare("DELETE FROM store_settings WHERE seller_id = :id")
                ->execute(['id' => $id]);
        }
        return $ok;
    }

    public static function updatePassword(int $id, string $password): bool
    {
        $stmt = Database::pdo()->prepare(
            "UPDATE users SET password_hash = :hash WHERE id = :id"
        );
        return $stmt->execute([
            'hash' => password_hash($password, PASSWORD_DEFAULT),
            'id'   => $id,
        ]);
    }

    public static function setActive(int $id, bool $active): bool
    {
        $stmt = Database::pdo()->prepare(
            "UPDATE users SET is_active = :active WHERE id = :id"
        );
        return $stmt->execute(['active' => (int) $active, 'id' => $id]);
    }

    public static function delete(int $id): bool
    {
        $user = self::find($id);
        if (!$user) return false;
        // Hapus foto produk milik user ini
        $stmt = Database::pdo()->prepare("SELECT image_path FROM products WHERE seller_id = :id AND image_path IS NOT NULL");
        $stmt->execute(['id' => $id]);
        foreach ($stmt->fetchAll() as $row) {
            delete_product_image($row['image_path']);
        }
        $stmt = Database::pdo()->prepare("DELETE FROM users WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public static function stats(): array
    {
        $pdo = Database::pdo();
        return [
            'total_users'   => (int) $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
            'total_admins'  => (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn(),
            'total_sellers' => (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'seller'")->fetchColumn(),
            'active_sellers' => (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'seller' AND is_active = 1")->fetchColumn(),
            'total_products' => (int) $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn(),
            'total_categories' => (int) $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn(),
        ];
    }
}
