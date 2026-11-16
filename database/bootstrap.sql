-- =====================================================================
-- SUKARIA — Sentra Katalog & Dashboard UMKM Oleh-Oleh Banyuwangi
-- Bootstrap Database (MySQL 8.x)
-- Jalankan file ini SEKALI sebagai root:
--   & "C:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe" -u root -p < database\bootstrap.sql
-- atau import melalui phpMyAdmin.
-- =====================================================================

-- ---------------------------------------------------------------------
-- 1. USER KHUSUS APLIKASI (jangan pakai root)
-- ---------------------------------------------------------------------
CREATE USER IF NOT EXISTS 'sukaria_app'@'localhost' IDENTIFIED BY 'Sukaria#2026';
CREATE USER IF NOT EXISTS 'sukaria_app'@'127.0.0.1' IDENTIFIED BY 'Sukaria#2026';

CREATE DATABASE IF NOT EXISTS sukaria_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

GRANT ALL PRIVILEGES ON sukaria_db.* TO 'sukaria_app'@'localhost';
GRANT ALL PRIVILEGES ON sukaria_db.* TO 'sukaria_app'@'127.0.0.1';
FLUSH PRIVILEGES;

USE sukaria_db;

-- ---------------------------------------------------------------------
-- 2. TABEL: users (admin & seller)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS store_settings;
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
  id            INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  name          VARCHAR(100)     NOT NULL,
  email         VARCHAR(150)     NOT NULL,
  password_hash VARCHAR(255)     NOT NULL,
  role          ENUM('admin','seller') NOT NULL,
  store_name    VARCHAR(150)     NULL,
  is_active     TINYINT(1)       NOT NULL DEFAULT 1,
  created_at    TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 3. TABEL: categories (6 kategori kuliner Banyuwangi)
-- ---------------------------------------------------------------------
CREATE TABLE categories (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name        VARCHAR(100) NOT NULL,
  slug        VARCHAR(120) NOT NULL,
  description VARCHAR(255) NULL,
  is_active   TINYINT(1)   NOT NULL DEFAULT 1,
  created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_categories_name (name),
  UNIQUE KEY uq_categories_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 4. TABEL: products
-- ---------------------------------------------------------------------
CREATE TABLE products (
  id           INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  seller_id    INT UNSIGNED  NOT NULL,
  category_id  INT UNSIGNED  NOT NULL,
  name         VARCHAR(150)  NOT NULL,
  slug         VARCHAR(180)  NOT NULL,
  description  TEXT          NULL,
  price        INT UNSIGNED  NOT NULL DEFAULT 0,
  stock        INT           NOT NULL DEFAULT 0,
  image_path   VARCHAR(255)  NULL COMMENT 'Nama file di public/uploads/products (NULL = ilustrasi batik otomatis)',
  is_featured  TINYINT(1)    NOT NULL DEFAULT 0,
  is_active    TINYINT(1)    NOT NULL DEFAULT 1,
  created_at   TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at   TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_products_slug (slug),
  KEY idx_products_seller (seller_id),
  KEY idx_products_category (category_id),
  CONSTRAINT fk_products_seller   FOREIGN KEY (seller_id)   REFERENCES users (id)      ON DELETE CASCADE,
  CONSTRAINT fk_products_category FOREIGN KEY (category_id) REFERENCES categories (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 5. TABEL: store_settings (nomor WhatsApp per seller)
-- ---------------------------------------------------------------------
CREATE TABLE store_settings (
  id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  seller_id       INT UNSIGNED NOT NULL,
  whatsapp_number VARCHAR(20)  NOT NULL DEFAULT '6281234567890',
  updated_at      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_store_settings_seller (seller_id),
  CONSTRAINT fk_store_settings_seller FOREIGN KEY (seller_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 5b. TABEL: orders (pesanan transfer dari keranjang belanja)
-- ---------------------------------------------------------------------
CREATE TABLE orders (
  id           INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  order_no     VARCHAR(20)   NOT NULL COMMENT 'Contoh: SUK-261116-A3F2',
  buyer_name   VARCHAR(100)  NOT NULL,
  buyer_phone  VARCHAR(20)   NOT NULL COMMENT 'Nomor WhatsApp pembeli (628...)',
  buyer_note   VARCHAR(500)  NULL,
  total_amount INT UNSIGNED  NOT NULL DEFAULT 0,
  status       ENUM('pending','verifying','paid','rejected','cancelled') NOT NULL DEFAULT 'pending',
  proof_path   VARCHAR(255)  NULL COMMENT 'Nama file bukti transfer di public/uploads/proofs',
  proof_at     TIMESTAMP     NULL DEFAULT NULL,
  paid_at      TIMESTAMP     NULL DEFAULT NULL,
  created_at   TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at   TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_orders_no (order_no),
  KEY idx_orders_status (status),
  KEY idx_orders_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 5c. TABEL: order_items (snapshot produk saat pesanan dibuat)
-- ---------------------------------------------------------------------
CREATE TABLE order_items (
  id           INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  order_id     INT UNSIGNED  NOT NULL,
  product_id   INT UNSIGNED  NULL COMMENT 'NULL bila produk sudah dihapus',
  seller_id    INT UNSIGNED  NULL COMMENT 'NULL bila seller sudah dihapus',
  product_name VARCHAR(150)  NOT NULL,
  store_name   VARCHAR(150)  NOT NULL,
  unit_price   INT UNSIGNED  NOT NULL,
  qty          INT UNSIGNED  NOT NULL DEFAULT 1,
  subtotal     INT UNSIGNED  NOT NULL,
  PRIMARY KEY (id),
  KEY idx_order_items_order  (order_id),
  KEY idx_order_items_seller (seller_id),
  CONSTRAINT fk_order_items_order  FOREIGN KEY (order_id)   REFERENCES orders (id)     ON DELETE CASCADE,
  CONSTRAINT fk_order_items_product FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 6. DATA DUMMY
-- ---------------------------------------------------------------------

-- === Users ===
-- Password: admin123  (admin), seller123 (semua seller)
INSERT INTO users (name, email, password_hash, role, store_name, is_active) VALUES
('Bagus Prakoso (Administrator)', 'admin@sukaria.id',    '$2y$12$2cwJJY1vmhTGTRXECX8qiuh.WpSDJHDptxm.w4rto8EDdLuMerOiO', 'admin',  NULL,                        1),
('Ibu Sri — Toko Kemiren',        'seller@sukaria.id',   '$2y$12$WPOjGCSPM66cgbIw5RnvZekTepjnCpQkB/Gipth3uhYTV8Oc7kMFG', 'seller', 'Toko Oleh-Oleh Kemiren',    1),
('Pak Amir — Muncar Laut',        'seller2@sukaria.id',  '$2y$12$WPOjGCSPM66cgbIw5RnvZekTepjnCpQkB/Gipth3uhYTV8Oc7kMFG', 'seller', 'Toko Hasil Laut Muncar',    1),
('Bu Ratna — Bagiak Osing',       'seller3@sukaria.id',  '$2y$12$WPOjGCSPM66cgbIw5RnvZekTepjnCpQkB/Gipth3uhYTV8Oc7kMFG', 'seller', 'Toko Bagiak Osing Blambangan', 0);

-- === Store Settings ===
INSERT INTO store_settings (seller_id, whatsapp_number) VALUES
(2, '6281234567890'),
(3, '6281234567891');

-- === Categories (6 kategori spesifikasi) ===
INSERT INTO categories (name, slug, description, is_active) VALUES
('Kue Basah & Jajanan Pasar',      'kue-basah-jajanan-pasar',      'Jajanan tradisional Osing, klemben, dan kue pasar khas Banyuwangi.', 1),
('Keringan & Keripik',             'keringan-keripik',             'Bagiak, sale pisang, dan keripik renyah tahan lama.',                1),
('Sambal & Bumbu Khas',            'sambal-bumbu-khas',            'Sambal tempong dan bumbu dasar racikan dapur Blambangan.',           1),
('Kopi & Minuman Tradisional',     'kopi-minuman-tradisional',     'Kopi lanang/robusta lereng Ijen dan minuman kekinian lama.',         1),
('Olahan Ikan & Hasil Laut',       'olahan-ikan-hasil-laut',       'Abon, bakasang, dan olahan ikan tangkapan nelayan Muncar.',          1),
('Paket Hampers & Oleh-Oleh',      'paket-hampers-oleh-oleh',      'Hampers eksklusif berisi rangkaian oleh-oleh khas Banyuwangi.',      1);

-- === Products (Toko Oleh-Oleh Kemiren — 8 produk spesifikasi) ===
INSERT INTO products (seller_id, category_id, name, slug, description, price, stock, image_path, is_featured, is_active) VALUES
(2, 4, 'Kopi Robusta Lanang Kemiren 250g', 'kopi-robusta-lanang-kemiren-250g', 'Biji kopi lanang tunggal pilihan dari lereng Ijen Desa Kemiren, disangrai medium-dark dengan aroma cokelat pekat dan body tebal.', 48000, 24, NULL, 1, 1),
(2, 1, 'Kue Bagiak Original Susu Osing',   'kue-bagiak-original-susu-osing',   'Kue kering tradisional suku Osing berbahan tepung garut dan santan gurih yang renyah di luar namun langsung lumer di mulut.',       22000, 35, NULL, 1, 1),
(2, 3, 'Sambal Tempong Segar Botol 200ml', 'sambal-tempong-segar-botol-200ml', 'Racikan cabai rawit merah segar, ranti (tomat mawar Banyuwangi), terasi Puger bakar, dan perasan jeruk sambal khas.',               28000, 18, NULL, 1, 1),
(2, 2, 'Sale Pisang Agung Goreng Krispi',  'sale-pisang-agung-goreng-krispi',  'Irisan tipis pisang agung khas Lumajang-Banyuwangi yang dijemur alami lalu dibalut adonan renyah manis legit tanpa pemanis buatan.', 25000, 40, NULL, 0, 1),
(2, 6, 'Paket Hampers Batik Blambangan Eksklusif', 'paket-hampers-batik-blambangan-eksklusif', 'Kotak hantaran motif Batik Kopi Pecah berisi Kopi Lanang Kemiren, Kue Bagiak, Sale Pisang Agung, dan Sambal Tempong.', 145000, 8, NULL, 1, 1),
(2, 2, 'Ladrang Sabrang Ungu Renyah',      'ladrang-sabrang-ungu-renyah',      'Camilan stik renyah dari ubi ungu pilihan dengan sentuhan bawang putih dan daun seledri yang gurih untuk teman minum kopi.',        19000,  4, NULL, 0, 1),
(2, 1, 'Kue Klemben Jadul Panggang Mawar', 'kue-klemben-jadul-panggang-mawar', 'Bolu kering klasik berbentuk bunga mawar dengan aroma vanili dan telur kampung, dipanggang tradisional hingga kecokelatan.',        20000,  0, NULL, 0, 1),
(2, 5, 'Abon Ikan Tongkol Pedas Manis',    'abon-ikan-tongkol-pedas-manis',    'Suwiran daging ikan tongkol segar hasil tangkapan nelayan Muncar yang dimasak kering bersama rempah nusantara.',                    38000, 14, NULL, 1, 1);

-- === Products (Toko Hasil Laut Muncar — 3 produk) ===
INSERT INTO products (seller_id, category_id, name, slug, description, price, stock, image_path, is_featured, is_active) VALUES
(3, 5, 'Bakasang Ikan Teri Muncar',      'bakasang-ikan-teri-muncar',      'Bakasang (pencét) ikan teri goreng khas pesisir Muncar, gurih asin legit, favorit teman nasi hangat.',                             35000, 20, NULL, 1, 1),
(3, 5, 'Kerupuk Udang Rebus Rumah',      'kerupuk-udang-rebus-rumah',      'Kerupuk udang rebus buatan rumah dengan tebal pas, mengembang sempurna saat digoreng.',                                            18000, 50, NULL, 0, 1),
(3, 3, 'Petris Sambal Terasi Puger',     'petris-sambal-terasi-puger',     'Sambal petris ulek kasar dengan terasi bakar Puger dan minyak kelapa murni, pedas nendang.',                                       27000,  6, NULL, 0, 1);

-- === Products (Toko Bagiak Osing Blambangan — 2 produk, salah satu nonaktif) ===
INSERT INTO products (seller_id, category_id, name, slug, description, price, stock, image_path, is_featured, is_active) VALUES
(4, 1, 'Bagiak Cokelat Lumer Premium', 'bagiak-cokelat-lumer-premium', 'Bagiak klasik isi cokelat lumer, dibuat dari tepung garut asli tanpa pengenzat.', 26000, 30, NULL, 1, 1),
(4, 2, 'Keripik Singkong Balado Blambangan', 'keripik-singkong-balado-blambangan', 'Keripik singkong tipis renyah dengan bumbu balado khas, produksi musiman.', 15000, 25, NULL, 0, 0);
