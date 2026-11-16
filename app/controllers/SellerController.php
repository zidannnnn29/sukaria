<?php
/**
 * Controller Seller: dashboard, produk, kategori, & pengaturan WA.
 */
final class SellerController
{
    private static function seller(): array
    {
        return require_role('seller');
    }

    // ------------------------------------------------------------------
    // DASHBOARD
    // ------------------------------------------------------------------
    public static function dashboard(): never
    {
        $seller = self::seller();
        $stats  = Product::sellerStats((int) $seller['id']);
        $wa     = StoreSetting::get((int) $seller['id']);
        $products = Product::forSeller((int) $seller['id']);

        render('panel', 'seller/dashboard', [
            'pageTitle' => 'Dashboard Seller — ' . SUKARIA_APP_NAME,
            'user'      => $seller,
            'stats'     => $stats,
            'waNumber'  => $wa,
            'products'  => array_slice($products, 0, 5),
        ]);
    }

    // ------------------------------------------------------------------
    // PRODUK
    // ------------------------------------------------------------------
    public static function products(): never
    {
        $seller = self::seller();
        $catFilter = (string) ($_GET['cat'] ?? 'ALL');
        $q = trim((string) ($_GET['q'] ?? ''));

        $products = Product::forSeller((int) $seller['id']);
        if ($catFilter !== 'ALL') {
            $products = array_values(array_filter($products, fn($p) => $p['category_id'] === (int) $catFilter));
        }
        if ($q !== '') {
            $products = array_values(array_filter($products, function ($p) use ($q) {
                return stripos($p['name'], $q) !== false || stripos((string) $p['description'], $q) !== false;
            }));
        }

        render('panel', 'seller/products', [
            'pageTitle'  => 'Kelola Produk — ' . SUKARIA_APP_NAME,
            'user'       => $seller,
            'products'   => $products,
            'categories' => Category::all(),
            'catFilter'  => $catFilter,
            'q'          => $q,
        ]);
    }

    public static function productForm(): never
    {
        $seller = self::seller();
        $id = (int) ($_GET['id'] ?? 0);
        $editing = $id > 0 ? Product::findForSeller($id, (int) $seller['id']) : null;

        if ($id > 0 && !$editing) {
            flash_set('Produk tidak ditemukan.', 'error');
            redirect('seller/products');
        }

        render('panel', 'seller/product_form', [
            'pageTitle' => ($editing ? 'Edit Produk' : 'Tambah Produk') . ' — ' . SUKARIA_APP_NAME,
            'user'      => $seller,
            'editing'   => $editing,
            'categories'=> Category::allActive(),
            'errors'    => $_SESSION['form_errors'] ?? [],
        ]);
    }

    public static function saveProduct(): never
    {
        $seller = self::seller();
        require_post();

        $id         = (int) ($_POST['id'] ?? 0);
        $name       = trim((string) ($_POST['name'] ?? ''));
        $categoryId = (int) ($_POST['category_id'] ?? 0);
        $priceRaw   = trim((string) ($_POST['price'] ?? ''));
        $stockRaw   = trim((string) ($_POST['stock'] ?? ''));
        $description= trim((string) ($_POST['description'] ?? ''));
        $featured   = isset($_POST['is_featured']) ? 1 : 0;
        $active     = isset($_POST['is_active']) ? 1 : 0;
        $imageData  = trim((string) ($_POST['image_data'] ?? ''));
        $removeImg = trim((string) ($_POST['remove_image'] ?? '')) !== '';

        $editing = $id > 0 ? Product::findForSeller($id, (int) $seller['id']) : null;
        $category = Category::find($categoryId);

        $errors = [];
        if ($name === '')                              $errors['name'] = 'Nama produk wajib diisi.';
        if (!$category || (int) $category['is_active'] !== 1) $errors['category_id'] = 'Pilih kategori yang aktif.';
        if ($priceRaw === '' || !ctype_digit($priceRaw)) $errors['price'] = 'Harga wajib angka bulat >= 0.';
        if ($stockRaw === '' || !ctype_digit($stockRaw)) $errors['stock'] = 'Stok wajib bilangan bulat >= 0.';

        if ($errors) {
            $_SESSION['form_errors'] = $errors;
            flash_old($_POST);
            flash_set('Periksa kembali isian form produk.', 'error');
            redirect($editing ? 'seller/products/edit' : 'seller/products/create', $editing ? ['id' => $id] : []);
        }

        $data = [
            'category_id' => $categoryId,
            'name'        => $name,
            'description' => $description,
            'price'       => (int) $priceRaw,
            'stock'       => (int) $stockRaw,
            'is_featured' => $featured,
            'is_active'   => $active,
            'image_path'  => $editing['image_path'] ?? null,
        ];

        // --- Penanganan gambar ---
        if ($removeImg) {
            delete_product_image($data['image_path']);
            $data['image_path'] = null;
        } elseif ($imageData !== '') {
            $newFile = save_base64_image($imageData);
            if ($newFile === null) {
                $_SESSION['form_errors'] = ['image_data' => 'File foto tidak dapat diproses. Coba foto lain.'];
                flash_old($_POST);
                flash_set('Gagal menyimpan foto produk.', 'error');
                redirect($editing ? 'seller/products/edit' : 'seller/products/create', $editing ? ['id' => $id] : []);
            }
            delete_product_image($data['image_path']);
            $data['image_path'] = $newFile;
        }

        if ($editing) {
            Product::update($id, (int) $seller['id'], $data);
            flash_set('Produk "' . $name . '" berhasil diperbarui.');
        } else {
            Product::create((int) $seller['id'], $data);
            flash_set('Produk "' . $name . '" berhasil ditambahkan.');
        }
        clear_old();
        redirect('seller/products');
    }

    public static function deleteProduct(): never
    {
        $seller = self::seller();
        require_post();

        $id = (int) ($_POST['id'] ?? 0);
        $product = Product::delete($id, (int) $seller['id']);
        if ($product) {
            delete_product_image($product['image_path']);
            flash_set('Produk "' . $product['name'] . '" telah dihapus.');
        } else {
            flash_set('Produk tidak ditemukan.', 'error');
        }
        redirect('seller/products');
    }

    public static function toggleFeatured(): never
    {
        $seller = self::seller();
        require_post();

        $id = (int) ($_POST['id'] ?? 0);
        $next = Product::toggleFeatured($id, (int) $seller['id']);
        if ($next === null) {
            flash_set('Produk tidak ditemukan.', 'error');
        } else {
            $p = Product::find($id);
            flash_set($next
                ? '"' . $p['name'] . '" ditandai sebagai Produk Unggulan.'
                : '"' . $p['name'] . '" dihapus dari Produk Unggulan.');
        }
        redirect_back('seller/products');
    }

    public static function toggleActive(): never
    {
        $seller = self::seller();
        require_post();

        $id = (int) ($_POST['id'] ?? 0);
        $next = Product::toggleActive($id, (int) $seller['id']);
        if ($next === null) {
            flash_set('Produk tidak ditemukan.', 'error');
        } else {
            $p = Product::find($id);
            flash_set($next
                ? 'Produk "' . $p['name'] . '" diaktifkan & tampil di katalog.'
                : 'Produk "' . $p['name'] . '" disembunyikan dari katalog.');
        }
        redirect_back('seller/products');
    }

    // ------------------------------------------------------------------
    // KATEGORI
    // ------------------------------------------------------------------
    public static function categories(): never
    {
        $seller = self::seller();
        render('panel', 'seller/categories', [
            'pageTitle' => 'Kelola Kategori — ' . SUKARIA_APP_NAME,
            'user'      => $seller,
            'categories'=> Category::all(),
            'editing'   => isset($_GET['edit']) ? Category::find((int) $_GET['edit']) : null,
            'errors'    => $_SESSION['form_errors'] ?? [],
        ]);
    }

    public static function saveCategory(): never
    {
        self::seller();
        require_post();

        $id          = (int) ($_POST['id'] ?? 0);
        $name        = trim((string) ($_POST['name'] ?? ''));
        $description = trim((string) ($_POST['description'] ?? ''));
        $active      = isset($_POST['is_active']) ? 1 : 0;
        $editing     = $id > 0 ? Category::find($id) : null;

        $errors = [];
        if ($name === '')                    $errors['name'] = 'Nama kategori wajib diisi.';
        if (mb_strlen($name) > 100)          $errors['name'] = 'Nama kategori maksimal 100 karakter.';
        if ($name !== '' && Category::nameTaken($name, $id)) $errors['name'] = 'Nama kategori sudah digunakan.';

        if ($errors) {
            $_SESSION['form_errors'] = $errors;
            flash_set('Periksa kembali isian kategori.', 'error');
            redirect($editing ? 'seller/categories' : 'seller/categories', $editing ? ['edit' => $id] : []);
        }

        $data = ['name' => $name, 'description' => $description, 'is_active' => $active];
        if ($editing) {
            Category::update($id, $data);
            flash_set('Kategori "' . $name . '" berhasil diperbarui.');
        } else {
            Category::create($data);
            flash_set('Kategori "' . $name . '" berhasil ditambahkan.');
        }
        clear_old();
        redirect('seller/categories');
    }

    public static function toggleCategory(): never
    {
        self::seller();
        require_post();

        $id = (int) ($_POST['id'] ?? 0);
        $category = Category::find($id);
        if (!$category) {
            flash_set('Kategori tidak ditemukan.', 'error');
            redirect('seller/categories');
        }
        if ((int) $category['is_active'] === 1) {
            $activeCount = (int) Database::pdo()->query(
                "SELECT COUNT(*) FROM categories WHERE is_active = 1"
            )->fetchColumn();
            if ($activeCount <= 1) {
                flash_set('Minimal harus tersisa satu kategori aktif.', 'error');
                redirect('seller/categories');
            }
        }
        Category::setActive($id, (int) $category['is_active'] !== 1);
        flash_set('Status kategori "' . $category['name'] . '" diperbarui.');
        redirect('seller/categories');
    }

    public static function deleteCategory(): never
    {
        self::seller();
        require_post();

        $id = (int) ($_POST['id'] ?? 0);
        $category = Category::find($id);
        if (!$category) {
            flash_set('Kategori tidak ditemukan.', 'error');
            redirect('seller/categories');
        }
        [$ok, $message] = Category::delete($id);
        if ($ok) {
            flash_set('Kategori "' . $category['name'] . '" dihapus. Produknya dipindahkan ke kategori lain.');
        } else {
            flash_set($message, 'error');
        }
        redirect('seller/categories');
    }

    // ------------------------------------------------------------------
    // PENGATURAN WHATSAPP TOKO
    // ------------------------------------------------------------------
    public static function settings(): never
    {
        $seller = self::seller();
        $current = StoreSetting::get((int) $seller['id']);

        render('panel', 'seller/settings', [
            'pageTitle' => 'Pengaturan WhatsApp Toko — ' . SUKARIA_APP_NAME,
            'user'      => $seller,
            'waNumber'  => $current,
            'waDisplay' => format_wa_display($current),
        ]);
    }

    public static function saveSettings(): never
    {
        $seller = self::seller();
        require_post();

        $normalized = normalize_wa($_POST['whatsapp_number'] ?? '');
        [$valid, $message] = validate_wa($normalized);
        if (!$valid) {
            flash_set($message, 'error');
            redirect('seller/settings');
        }

        StoreSetting::save((int) $seller['id'], $normalized);
        flash_set('Nomor WhatsApp toko diperbarui ke ' . format_wa_display($normalized) . '.');
        redirect('seller/settings');
    }

    public static function resetSettings(): never
    {
        $seller = self::seller();
        require_post();

        StoreSetting::save((int) $seller['id'], SUKARIA_DEFAULT_WA);
        flash_set('Nomor WhatsApp toko dikembalikan ke default (' . format_wa_display(SUKARIA_DEFAULT_WA) . ').');
        redirect('seller/settings');
    }
}
