<?php
/**
 * SUKARIA — Migrasi tambah fitur pesanan transfer (keranjang belanja).
 * Aman dijalankan berulang; hanya membuat tabel orders & order_items.
 * Jalankan dari folder root proyek:  php database/migrate_add_orders.php
 */

declare(strict_types=1);

$root   = dirname(__DIR__);
$config = require $root . '/config/config.php';

$dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $config['db']['host'], (int) $config['db']['port'], $config['db']['name'], $config['db']['charset']);

$pdo = null;
foreach ([
    ['user' => $config['db']['user'], 'pass' => $config['db']['pass']],
    ['user' => $config['db_admin']['user'] ?? 'root', 'pass' => $config['db_admin']['pass'] ?? ''],
] as $creds) {
    try {
        $pdo = new PDO($dsn, $creds['user'], $creds['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 5,
        ]);
        break;
    } catch (PDOException $e) {
        $last = $e;
    }
}
if ($pdo === null) {
    fwrite(STDERR, "GAGAL koneksi: " . ($last ? $last->getMessage() : '?') . "\n");
    exit(1);
}

$statements = [
    "CREATE TABLE IF NOT EXISTS orders (
        id           INT UNSIGNED  NOT NULL AUTO_INCREMENT,
        order_no     VARCHAR(20)   NOT NULL,
        buyer_name   VARCHAR(100)  NOT NULL,
        buyer_phone  VARCHAR(20)   NOT NULL,
        buyer_note   VARCHAR(500)  NULL,
        total_amount INT UNSIGNED  NOT NULL DEFAULT 0,
        status       ENUM('pending','verifying','paid','rejected','cancelled') NOT NULL DEFAULT 'pending',
        proof_path   VARCHAR(255)  NULL,
        proof_at     TIMESTAMP     NULL DEFAULT NULL,
        paid_at      TIMESTAMP     NULL DEFAULT NULL,
        created_at   TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at   TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY uq_orders_no (order_no),
        KEY idx_orders_status (status),
        KEY idx_orders_created (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

    "CREATE TABLE IF NOT EXISTS order_items (
        id           INT UNSIGNED  NOT NULL AUTO_INCREMENT,
        order_id     INT UNSIGNED  NOT NULL,
        product_id   INT UNSIGNED  NULL,
        seller_id    INT UNSIGNED  NULL,
        product_name VARCHAR(150)  NOT NULL,
        store_name   VARCHAR(150)  NOT NULL,
        unit_price   INT UNSIGNED  NOT NULL,
        qty          INT UNSIGNED  NOT NULL DEFAULT 1,
        subtotal     INT UNSIGNED  NOT NULL,
        PRIMARY KEY (id),
        KEY idx_order_items_order  (order_id),
        KEY idx_order_items_seller (seller_id),
        CONSTRAINT fk_order_items_order   FOREIGN KEY (order_id)   REFERENCES orders (id)     ON DELETE CASCADE,
        CONSTRAINT fk_order_items_product FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
];

foreach ($statements as $stmt) {
    $pdo->exec($stmt);
}

foreach (['orders', 'order_items'] as $table) {
    $count = (int) $pdo->query("SELECT COUNT(*) FROM `{$table}`")->fetchColumn();
    echo sprintf("  %-12s ada, %d baris\n", $table, $count);
}
echo "Migrasi SUKSES — fitur keranjang & transfer siap.\n";
