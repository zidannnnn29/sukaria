# Sukaria

Aplikasi web **Sukaria — Sentra Katalog & Dashboard UMKM Oleh-Oleh Khas Banyuwangi**.
Versi **production** (PHP Native + MySQL), dikembangkan dari prototype `sukaria.html`
sesuai `spec.md` dengan **3 jenis akses**:

| Peran | Akses | Fitur |
|---|---|---|
| **Admin** | Login | Dashboard statistik global, **kelola user** (tambah/edit/nonaktif/hapus admin & seller, ganti password), **verifikasi pesanan transfer** (lunas/tolak/batalkan) |
| **Seller** | Login | **Kelola produk** (CRUD, foto terkompresi, unggulan, aktif/nonaktif, stok), **kelola kategori**, atur **nomor WhatsApp toko**, lihat **pesanan toko** (read-only) |
| **Pembeli** | Tanpa login | Katalog publik: cari, filter kategori, urutkan, lihat detail, lalu pilih **pesan via WhatsApp** atau **keranjang belanja → checkout → transfer bank** (unggah bukti transfer, cek status pesanan) |

---

## 1. Persyaratan

- PHP >= 8.1 (sudah ada: PHP 8.4 ✔)
- MySQL 8.x (sudah ada: MySQL80 service ✔)
- Ekstensi PHP: `pdo_mysql` (aktif bawaan XAMPP/Laragon)

Cek cepat: `php -m | findstr pdo_mysql`

## 2. Setup Database (Sekali Jalan)

File `database/bootstrap.sql` membuat **user khusus aplikasi** (`sukaria_app`), database `sukaria_db`, 6 tabel (users, categories, products, store_settings, **orders, order_items**), dan **data dummy lengkap**.

**Opsi A — Command line** (masukkan password root Anda saat diminta):

```powershell
& "C:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe" -u root -p < C:\umkm\sukaria\database\bootstrap.sql
```

**Opsi B — phpMyAdmin** (`http://localhost/phpmyadmin`, login root):
menu *Import* → pilih `database/bootstrap.sql` → *Go*.

> **Database lama** (sebelum ada fitur keranjang/transfer) cukup dimigrasi tanpa kehilangan data:
> `php database/migrate_add_orders.php`

> Konfigurasi koneksi ada di `config/config.php` (host `localhost`, port `3307`, db `sukaria_db`, user `sukaria_app`, pass `Sukaria#2026`). **Rekening tujuan transfer** juga diatur di `config.php` (`payment_accounts`).

## 3. Menjalankan Aplikasi

Dari folder proyek (`C:\umkm\sukaria`):

```powershell
php -S localhost:8000 -t public
```

Lalu buka **http://localhost:8000**

- Pembeli → langsung di halaman katalog
- Login pengelola → **http://localhost:8000/index.php?r=login**

> Alternatif Laragon: salin folder `sukaria` ke `C:\laragon\www\` lalu buka `http://sukaria.test`.

## 4. Akun Dummy

| Role | Email | Password | Keterangan |
|---|---|---|---|
| Admin | `admin@sukaria.id` | `admin123` | Bagus Prakoso (Administrator) |
| Seller | `seller@sukaria.id` | `seller123` | Toko Oleh-Oleh Kemiren — 8 produk |
| Seller | `seller2@sukaria.id` | `seller123` | Toko Hasil Laut Muncar — 3 produk |
| Seller | `seller3@sukaria.id` | `seller123` | Toko Bagiak Osing — **nonaktif** (contoh akun dinonaktifkan admin) |

**Data dummy:** 4 user, 6 kategori kuliner, **13 produk** Banyuwangi (semua memakai ilustrasi SVG Batik Kopi Pecah otomatis), nomor WA default `+62 812-3456-7890`.

## 5. Struktur Proyek

```text
sukaria/
├── config/config.php          # Kredensial DB, rekening transfer, konfigurasi aplikasi
├── database/bootstrap.sql     # User MySQL + skema (6 tabel) + data dummy
├── database/migrate_add_orders.php  # Migrasi tabel orders/order_items (DB lama)
├── public/                    # WEB ROOT (satu-satunya folder yang dilayani)
│   ├── index.php              # Front controller + router (?r=...)
│   ├── assets/app.css         # Tema Batik Kopi Pecah
│   ├── assets/app.js          # Katalog interaktif, keranjang, kompresi gambar, konfirmasi, toast
│   └── uploads/products/      # Foto produk terunggah
│   └── uploads/proofs/        # Foto bukti transfer terunggah
└── app/
    ├── core/                  # bootstrap, Database (PDO), Auth, View, Cart, helpers
    ├── models/                # User, Product, Category, StoreSetting, Order
    ├── controllers/           # Auth, Catalog, Cart, Order, Admin, Seller
    └── views/                 # layouts, auth, catalog, cart, order, admin, seller
```

## 6. Peta Rute

| Route (`index.php?r=...`) | Akses | Fungsi |
|---|---|---|
| `catalog` | Publik | Katalog pembeli (WA atau keranjang) |
| `cart`, `cart/add|update|remove|clear` | Publik | Keranjang belanja (session, tanpa login) |
| `checkout`, `checkout/submit` | Publik | Checkout: data penerima + total transfer |
| `order/view`, `order/lookup`, `order/find`, `order/pay` | Publik | Detail & status pesanan, unggah bukti transfer |
| `login` / `do-login` / `logout` | Publik | Autentikasi |
| `admin/dashboard` | Admin | Statistik global (+ pesanan transfer) |
| `admin/users`, `admin/users/create|edit|save|toggle|delete` | Admin | Kelola user |
| `admin/orders`, `admin/orders/confirm|reject|cancel` | Admin | Verifikasi pesanan transfer |
| `seller/dashboard` | Seller | Statistik toko |
| `seller/products`, `.../create|edit|save|delete|toggle-featured|toggle-active` | Seller | Kelola produk |
| `seller/categories`, `.../save|toggle|delete` | Seller | Kelola kategori |
| `seller/orders` | Seller | Pesanan toko (read-only) |
| `seller/settings`, `.../save|reset` | Seller | Nomor WhatsApp toko |

## 7. Keamanan

- **Password** di-hash bcrypt (`password_hash` / `password_verify`)
- **SQL injection** → PDO prepared statements di seluruh query
- **XSS** → semua output lewat `e()` (htmlspecialchars) dan `escapeHTML()` di JS
- **CSRF** → token session pada semua form POST (`csrf_field()` + verifikasi)
- **Session** → `httponly` + `SameSite=Lax`, `session_regenerate_id` saat login
- **Otorisasi** → middleware `require_role()`: seller hanya menyentuh produk miliknya sendiri; admin tidak boleh menghapus/dinonaktifkan admin terakhir atau dirinya sendiri
- **Upload** → gambar dikompresi di browser (canvas → JPEG ≤640px), diverifikasi tipe & ukuran di server, nama file acak

## 8. Catatan Perilaku Penting

**Pembeli punya dua cara memesan:**
- **Pesan via WhatsApp** → tombol per produk, format pesanan otomatis.
- **Keranjang Belanja** → tombol "+ Keranjang", semua produk digabung satu pesanan, bayar **transfer bank sekali** (rekening di `config.php`), unggah foto bukti transfer, admin memverifikasi.

**Siklus pesanan transfer:** `Menunggu Pembayaran` → (unggah bukti) `Verifikasi Bukti` → `Lunas` / `Ditolak` (bisa unggah ulang) / `Dibatalkan`.
- Stok **berkurang saat pesanan dibuat**, dan **dikembalikan** bila admin menolak/membatalkan pesanan.
- Item pesanan adalah *snapshot* (nama, harga, toko) — aman walau produk diubah/dihapus.
- Status pesanan bisa dicek siapa saja lewat nomor pesanan (`Cek Pesanan` di header).

**Lainnya:**
- Stok = 0 → tombol WA & "+ Keranjang" otomatis **disabled** ("Stok Habis").
- Stok 1–5 → lencana "Stok Menipis".
- Produk `is_active = 0`, seller nonaktif, atau kategori nonaktif → **tidak tampil** di katalog pembeli.
- Item keranjang divalidasi ulang tiap halaman: produk yang tidak lagi tersedia otomatis dibuang dari keranjang.
- Hapus kategori → produk dipindah otomatis ke kategori lain (tidak ada produk yatim).
- Hapus user seller → seluruh produk & fotonya ikut terhapus (FK CASCADE).
- Nomor WA dinormalisasi otomatis: `08...`/`+62...` → `628...`, validasi 10–15 digit.
