<?php
/**
 * Bootstrap aplikasi: session, konfigurasi, autoload sederhana.
 */

declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
    ]);
}

$config = require dirname(__DIR__, 2) . '/config/config.php';

// ---- Autoload sederhana (core, models, controllers) ----
$base = dirname(__DIR__, 2) . '/app';
foreach (['core', 'models', 'controllers'] as $group) {
    foreach (glob($base . '/' . $group . '/*.php') as $file) {
        require_once $file;
    }
}

Database::init($config['db']);

// ---- Konstanta runtime ----
define('SUKARIA_APP_NAME', $config['app_name']);
define('SUKARIA_TAGLINE', $config['app_tagline']);
define('SUKARIA_DEFAULT_WA', $config['default_wa']);
define('SUKARIA_UPLOAD_DIR', $config['upload_dir_products']);
define('SUKARIA_PROOF_DIR', $config['upload_dir_proofs'] ?? (dirname(__DIR__, 2) . '/public/uploads/proofs'));
define('SUKARIA_MAX_UPLOAD', (int) $config['max_upload_bytes']);
define('SUKARIA_PAYMENT_ACCOUNTS', (array) ($config['payment_accounts'] ?? []));

foreach ([SUKARIA_UPLOAD_DIR, SUKARIA_PROOF_DIR] as $upDir) {
    if (!is_dir($upDir)) {
        @mkdir($upDir, 0775, true);
    }
}
