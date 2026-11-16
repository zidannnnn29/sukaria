<?php
/**
 * Konfigurasi Aplikasi Sukaria
 * Sesuaikan kredensial database jika berbeda.
 */
return [
    'app_name'    => 'Sukaria',
    'app_tagline' => 'Sentra Katalog & Dashboard UMKM Oleh-Oleh Khas Banyuwangi',

    'db' => [
        'host'    => 'localhost',
        'port'    => 3307,               // MariaDB 11.4 ServBay (jalankan: ServBay > MariaDB, atau mysqld --port=3307)
        'name'    => 'sukaria_db',
        'user'    => 'sukaria_app',
        'pass'    => 'Sukaria#2026',
        'charset' => 'utf8mb4',
    ],

    // Kredensial admin (root) khusus untuk menjalankan database/setup.php sekali
    'db_admin' => [
        'user' => 'root',
        'pass' => 'ServBay.dev',
    ],

    // Nomor WhatsApp default (nonpribadi, sesuai spesifikasi)
    'default_wa' => '6281234567890',

    // Rekening tujuan transfer pembayaran keranjang (ditampilkan saat checkout)
    'payment_accounts' => [
        ['bank' => 'Bank BRI',  'number' => '0123-4567-8910-111', 'holder' => 'Sukaria Banyuwangi'],
        ['bank' => 'Bank BCA',  'number' => '9876-5432-1098-765', 'holder' => 'Sukaria Banyuwangi'],
    ],

    // Direktori unggahan foto produk (relatif ke public/uploads/products)
    'upload_dir_products' => dirname(__DIR__) . '/public/uploads/products',

    // Direktori unggahan bukti transfer (relatif ke public/uploads/proofs)
    'upload_dir_proofs' => dirname(__DIR__) . '/public/uploads/proofs',

    // Batas ukuran file foto mentah (bytes) = 10 MB
    'max_upload_bytes' => 10 * 1024 * 1024,
];
