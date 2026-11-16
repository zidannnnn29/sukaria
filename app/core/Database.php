<?php
/**
 * Koneksi PDO tunggal (singleton).
 */
final class Database
{
    private static ?PDO $pdo = null;

    public static function init(array $dbConfig): void
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $dbConfig['host'],
            (int) $dbConfig['port'],
            $dbConfig['name'],
            $dbConfig['charset']
        );

        try {
            self::$pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['pass'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo '<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><title>Setup Database — Sukaria</title>';
            echo '<style>body{font-family:system-ui,sans-serif;background:#FAF4E8;color:#2A160C;max-width:720px;margin:48px auto;padding:0 24px;line-height:1.6}'
                 . '.card{background:#FFFDF9;border:1px solid #E2D0B8;border-radius:16px;padding:24px;}code{background:#F3E8D4;padding:2px 8px;border-radius:6px;font-size:0.9em}</style></head><body>';
            echo '<div class="card"><h1>🍵 Sukaria — Database Belum Terhubung</h1>';
            echo '<p><strong>Detail:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '<p>Database belum dibuat atau kredensial di <code>config/config.php</code> belum sesuai.</p>';
            echo '<p><strong>Cara memperbaiki:</strong> jalankan <code>database/bootstrap.sql</code> sebagai root MySQL:</p>';
            echo '<pre style="background:#F3E8D4;padding:12px;border-radius:10px;overflow-x:auto">"C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysql.exe" -u root -p &lt; database\\bootstrap.sql</pre>';
            echo '<p>atau import lewat phpMyAdmin. Lalu muat ulang halaman ini.</p></div></body></html>';
            exit;
        }
    }

    public static function pdo(): PDO
    {
        if (self::$pdo === null) {
            throw new RuntimeException('Database belum diinisialisasi.');
        }
        return self::$pdo;
    }
}
