<?php
/**
 * SUKARIA — Setup Database via PHP PDO
 * Menjalankan database/bootstrap.sql menggunakan kredensial di config/config.php.
 *
 * Jalankan dari folder root proyek:  php database/setup.php
 */

declare(strict_types=1);

$root   = dirname(__DIR__);
$config = require $root . '/config/config.php';
$sqlFile = $root . '/database/bootstrap.sql';

if (!is_file($sqlFile)) {
    fwrite(STDERR, "ERROR: bootstrap.sql tidak ditemukan.\n");
    exit(1);
}

$dsn = sprintf('mysql:host=%s;port=%d;charset=%s', $config['db']['host'], (int) $config['db']['port'], $config['db']['charset']);

// 1) Coba kredensial aplikasi (jika bootstrap sudah pernah dijalankan)
// 2) Fallback ke kredensial root (db_admin) untuk setup pertama kali
$pdo = null;
$usedCreds = null;
foreach (
    [
        ['user' => $config['db']['user'], 'pass' => $config['db']['pass'], 'label' => 'app'],
        ['user' => $config['db_admin']['user'] ?? 'root', 'pass' => $config['db_admin']['pass'] ?? '', 'label' => 'root'],
    ] as $creds
) {
    try {
        $pdo = new PDO($dsn, $creds['user'], $creds['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 5,
        ]);
        $usedCreds = $creds['label'];
        break;
    } catch (PDOException $e) {
        $lastError = $e;
    }
}

if ($pdo === null) {
    fwrite(STDERR, "GAGAL koneksi ke MySQL di {$config['db']['host']}:{$config['db']['port']}\n");
    fwrite(STDERR, 'Detail: ' . ($lastError ? $lastError->getMessage() : 'tidak diketahui') . "\n");
    exit(1);
}

echo "Terhubung: {$usedCreds}@{$config['db']['host']}:{$config['db']['port']}\n";
echo 'Server   : ' . $pdo->query('SELECT VERSION()')->fetchColumn() . "\n\n";

// --- Pecah SQL per statement (aman: tidak ada ';' di dalam string data) ---
$raw     = file_get_contents($sqlFile);
$lines   = preg_split('/\R/', $raw);
$clean   = [];
foreach ($lines as $line) {
    $trimmed = ltrim($line);
    if ($trimmed === '' || str_starts_with($trimmed, '--')) {
        continue; // buang komentar & baris kosong
    }
    $clean[] = $line;
}
$sql     = implode("\n", $clean);
$statements = array_filter(array_map('trim', explode(';', $sql)), fn($s) => $s !== '');

$done = 0;
foreach ($statements as $stmt) {
    try {
        $pdo->exec($stmt);
        $done++;
    } catch (PDOException $e) {
        fwrite(STDERR, "GAGAL statement:\n" . substr($stmt, 0, 120) . "...\n");
        fwrite(STDERR, 'Detail: ' . $e->getMessage() . "\n");
        exit(1);
    }
}

echo "Selesai: {$done} statement dieksekusi.\n";

// --- Verifikasi isi database ---
$pdo->exec("USE `{$config['db']['name']}`");
foreach (['users', 'categories', 'products', 'store_settings'] as $table) {
    $count = (int) $pdo->query("SELECT COUNT(*) FROM `{$table}`")->fetchColumn();
    echo sprintf("  %-16s %d baris\n", $table, $count);
}
echo "\nSetup database SUKSES!\n";
