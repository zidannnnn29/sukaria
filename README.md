# Sukaria

Aplikasi web **Sukaria — Sentra Katalog & Dashboard UMKM Oleh-Oleh Khas Banyuwangi**.
Versi **production** (PHP Native + MySQL), dikembangkan dari prototype `sukaria.html`
sesuai `spec.md` dengan **3 jenis akses**:

| Peran | Akses | Fitur |
|---|---|---|
| **Admin** | Login | Dashboard statistik global, **kelola user** (tambah/edit/nonaktif/hapus admin & seller, ganti password) |
| **Seller** | Login | **Kelola produk** (CRUD, foto terkompresi, unggulan, aktif/nonaktif, stok), **kelola kategori**, atur **nomor WhatsApp toko** |
| **Pembeli** | Tanpa login | Katalog publik: cari, filter kategori, urutkan, lihat detail, **pesan via WhatsApp** |

---

## 1. Persyaratan

- PHP >= 8.1 (sudah ada: PHP 8.4 ✔)
- MySQL 8.x (sudah ada: MySQL80 service ✔)
- Ekstensi PHP: `pdo_mysql` (aktif bawaan XAMPP/Laragon)

Cek cepat: `php -m | findstr pdo_mysql`

## 2. Setup Database (Sekali Jalan)

File `database/bootstrap.sql` membuat **user khusus aplikasi** (`sukaria_app`), database `sukaria_db`, 4 tabel, dan **data dummy lengkap**.

**Opsi A — Command line** (masukkan password root Anda saat diminta):

```powershell
& "C:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe" -u root -p < C:\umkm\sukaria\database\bootstrap.sql
```

**Opsi B — phpMyAdmin** (`http://localhost/phpmyadmin`, login root):
menu *Import* → pilih `database/bootstrap.sql` → *Go*.

> Konfigurasi koneksi ada di `config/config.php` (host `127.0.0.1`, db `sukaria_db`, user `sukaria_app`, pass `Sukaria#2026`). Sesuaikan bila berbeda.

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
├── config/config.php          # Kredensial DB & konfigurasi aplikasi
├── database/bootstrap.sql     # User MySQL + skema + data dummy
├── public/                    # WEB ROOT (satu-satunya folder yang dilayani)
│   ├── index.php              # Front controller + router (?r=...)
│   ├── assets/app.css         # Tema Batik Kopi Pecah
│   ├── assets/app.js          # Katalog interaktif, kompresi gambar, konfirmasi, toast
│   └── uploads/products/      # Foto produk terunggah
└── app/
    ├── core/                  # bootstrap, Database (PDO), Auth, View, helpers
    ├── models/                # User, Product, Category, StoreSetting
    ├── controllers/           # Auth, Catalog, Admin, Seller
    └── views/                 # layouts, auth, catalog, admin, seller
```

## 6. Peta Rute

| Route (`index.php?r=...`) | Akses | Fungsi |
|---|---|---|
| `catalog` | Publik | Katalog pembeli |
| `login` / `do-login` / `logout` | Publik | Autentikasi |
| `admin/dashboard` | Admin | Statistik global |
| `admin/users`, `admin/users/create|edit|save|toggle|delete` | Admin | Kelola user |
| `seller/dashboard` | Seller | Statistik toko |
| `seller/products`, `.../create|edit|save|delete|toggle-featured|toggle-active` | Seller | Kelola produk |
| `seller/categories`, `.../save|toggle|delete` | Seller | Kelola kategori |
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

- Stok = 0 → tombol WhatsApp otomatis **disabled** ("Stok Habis").
- Stok 1–5 → lencana "Stok Menipis".
- Produk `is_active = 0`, seller nonaktif, atau kategori nonaktif → **tidak tampil** di katalog pembeli.
- Hapus kategori → produk dipindah otomatis ke kategori lain (tidak ada produk yatim).
- Hapus user seller → seluruh produk & fotonya ikut terhapus (FK CASCADE).
- Nomor WA dinormalisasi otomatis: `08...`/`+62...` → `628...`, validasi 10–15 digit.
