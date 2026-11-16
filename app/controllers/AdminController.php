<?php
/**
 * Controller Admin: dashboard + manajemen user.
 */
final class AdminController
{
    public static function dashboard(): never
    {
        $user = require_role('admin');
        $userStats = User::stats();
        $productStats = Product::adminStats();
        $orderStats = Order::adminStats();
        $sellers = Database::pdo()->query(
            "SELECT u.id, u.name, u.store_name, u.is_active,
                    COUNT(p.id) AS product_count,
                    COALESCE(SUM(p.stock), 0) AS total_stock
             FROM users u LEFT JOIN products p ON p.seller_id = u.id
             WHERE u.role = 'seller'
             GROUP BY u.id ORDER BY u.name ASC"
        )->fetchAll();
        $categories = Category::all();

        render('panel', 'admin/dashboard', [
            'pageTitle'    => 'Dashboard Admin — ' . SUKARIA_APP_NAME,
            'user'         => $user,
            'userStats'    => $userStats,
            'productStats' => $productStats,
            'orderStats'   => $orderStats,
            'sellers'      => $sellers,
            'categories'   => $categories,
        ]);
    }

    public static function users(): never
    {
        $user = require_role('admin');
        $search = trim((string) ($_GET['q'] ?? ''));
        render('panel', 'admin/users', [
            'pageTitle' => 'Manajemen User — ' . SUKARIA_APP_NAME,
            'user'      => $user,
            'users'     => User::all($search !== '' ? $search : null),
            'search'    => $search,
        ]);
    }

    public static function userForm(): never
    {
        $user = require_role('admin');
        $editId = (int) ($_GET['id'] ?? 0);
        $editing = $editId > 0 ? User::find($editId) : null;

        if ($editId > 0 && !$editing) {
            flash_set('User tidak ditemukan.', 'error');
            redirect('admin/users');
        }

        render('panel', 'admin/user_form', [
            'pageTitle' => ($editing ? 'Edit User' : 'Tambah User') . ' — ' . SUKARIA_APP_NAME,
            'user'      => $user,
            'editing'   => $editing,
            'errors'    => $_SESSION['form_errors'] ?? [],
        ]);
    }

    public static function saveUser(): never
    {
        require_role('admin');
        require_post();

        $id       = (int) ($_POST['id'] ?? 0);
        $name     = trim((string) ($_POST['name'] ?? ''));
        $email    = trim((string) ($_POST['email'] ?? ''));
        $role     = (string) ($_POST['role'] ?? 'seller');
        $store    = trim((string) ($_POST['store_name'] ?? ''));
        $active   = isset($_POST['is_active']) ? 1 : 0;
        $password = (string) ($_POST['password'] ?? '');
        $editing  = $id > 0 ? User::find($id) : null;

        $errors = [];
        if ($name === '')                                   $errors['name'] = 'Nama wajib diisi.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))     $errors['email'] = 'Email tidak valid.';
        elseif (User::emailTaken($email, $id))              $errors['email'] = 'Email sudah digunakan user lain.';
        if (!in_array($role, ['admin', 'seller'], true))    $errors['role'] = 'Role tidak valid.';
        if ($role === 'seller' && $store === '')            $errors['store_name'] = 'Nama toko wajib untuk seller.';
        if (!$editing && strlen($password) < 6)             $errors['password'] = 'Password minimal 6 karakter.';
        if ($editing && $password !== '' && strlen($password) < 6) $errors['password'] = 'Password baru minimal 6 karakter.';
        if ($editing && $editing['role'] === 'admin' && $role === 'seller') {
            $admins = (int) Database::pdo()->query("SELECT COUNT(*) FROM users WHERE role='admin' AND is_active=1")->fetchColumn();
            if ($admins <= 1) $errors['role'] = 'Minimal harus tersisa satu admin aktif.';
        }

        if ($errors) {
            $_SESSION['form_errors'] = $errors;
            flash_old($_POST);
            flash_set('Periksa kembali isian form.', 'error');
            redirect($editing ? 'admin/users/edit' : 'admin/users/create', $editing ? ['id' => $id] : []);
        }

        $data = [
            'name' => $name, 'email' => $email, 'role' => $role,
            'store_name' => $role === 'seller' ? $store : '', 'is_active' => $active,
        ];

        if ($editing) {
            User::update($id, $data);
            if ($password !== '') {
                User::updatePassword($id, $password);
            }
            flash_set('User "' . $name . '" berhasil diperbarui.');
        } else {
            User::create($data + ['password' => $password]);
            flash_set('User "' . $name . '" berhasil ditambahkan.');
        }
        clear_old();
        redirect('admin/users');
    }

    public static function toggleUser(): never
    {
        $admin = require_role('admin');
        require_post();

        $id   = (int) ($_POST['id'] ?? 0);
        $user = User::find($id);
        if (!$user) {
            flash_set('User tidak ditemukan.', 'error');
            redirect('admin/users');
        }
        if ((int) $user['id'] === (int) $admin['id']) {
            flash_set('Anda tidak dapat menonaktifkan akun sendiri.', 'error');
            redirect('admin/users');
        }
        if ($user['role'] === 'admin' && (int) $user['is_active'] === 1) {
            $activeAdmins = (int) Database::pdo()->query(
                "SELECT COUNT(*) FROM users WHERE role='admin' AND is_active=1"
            )->fetchColumn();
            if ($activeAdmins <= 1) {
                flash_set('Minimal harus tersisa satu admin aktif.', 'error');
                redirect('admin/users');
            }
        }

        User::setActive($id, (int) $user['is_active'] !== 1);
        flash_set('Status akun "' . $user['name'] . '" diperbarui.');
        redirect('admin/users');
    }

    public static function deleteUser(): never
    {
        $admin = require_role('admin');
        require_post();

        $id   = (int) ($_POST['id'] ?? 0);
        $user = User::find($id);
        if (!$user) {
            flash_set('User tidak ditemukan.', 'error');
            redirect('admin/users');
        }
        if ((int) $id === (int) $admin['id']) {
            flash_set('Anda tidak dapat menghapus akun sendiri.', 'error');
            redirect('admin/users');
        }
        if ($user['role'] === 'admin') {
            $activeAdmins = (int) Database::pdo()->query(
                "SELECT COUNT(*) FROM users WHERE role='admin' AND is_active=1"
            )->fetchColumn();
            if ($activeAdmins <= 1) {
                flash_set('Minimal harus tersisa satu admin aktif.', 'error');
                redirect('admin/users');
            }
        }

        User::delete($id);
        flash_set('User "' . $user['name'] . '" beserta seluruh produknya telah dihapus.');
        redirect('admin/users');
    }

    // ================= PESANAN TRANSFER =================================

    public static function orders(): never
    {
        $user = require_role('admin');
        $status = trim((string) ($_GET['status'] ?? ''));
        render('panel', 'admin/orders', [
            'pageTitle' => 'Pesanan Transfer — ' . SUKARIA_APP_NAME,
            'user'      => $user,
            'orders'    => Order::all($status !== '' ? $status : null),
            'status'    => $status,
        ]);
    }

    /** Tandai pesanan LUNAS (pembayaran transfer terverifikasi). */
    public static function confirmOrder(): never
    {
        require_role('admin');
        require_post();

        $order = Order::findByNo(trim((string) ($_POST['order_no'] ?? '')));
        if (!$order) {
            flash_set('Pesanan tidak ditemukan.', 'error');
            redirect('admin/orders');
        }
        if (!in_array($order['status'], ['pending', 'verifying'], true)) {
            flash_set('Pesanan ' . $order['order_no'] . ' sudah ditutup.', 'error');
            redirect('admin/orders');
        }

        Order::markPaid((int) $order['id']);
        flash_set('Pesanan ' . $order['order_no'] . ' ditandai LUNAS.');
        redirect('admin/orders');
    }

    /** Tolak bukti transfer: stok dikembalikan, pembeli bisa unggah ulang. */
    public static function rejectOrder(): never
    {
        require_role('admin');
        require_post();

        $order = Order::findByNo(trim((string) ($_POST['order_no'] ?? '')));
        if (!$order) {
            flash_set('Pesanan tidak ditemukan.', 'error');
            redirect('admin/orders');
        }
        if (!in_array($order['status'], ['pending', 'verifying'], true)) {
            flash_set('Pesanan ' . $order['order_no'] . ' sudah ditutup.', 'error');
            redirect('admin/orders');
        }

        Order::reject((int) $order['id']);
        flash_set('Pesanan ' . $order['order_no'] . ' ditolak. Stok produk dikembalikan.');
        redirect('admin/orders');
    }

    /** Batalkan pesanan yang belum dibayar: stok dikembalikan. */
    public static function cancelOrder(): never
    {
        require_role('admin');
        require_post();

        $order = Order::findByNo(trim((string) ($_POST['order_no'] ?? '')));
        if (!$order) {
            flash_set('Pesanan tidak ditemukan.', 'error');
            redirect('admin/orders');
        }
        if (!in_array($order['status'], ['pending', 'verifying'], true)) {
            flash_set('Pesanan ' . $order['order_no'] . ' sudah ditutup.', 'error');
            redirect('admin/orders');
        }

        Order::cancel((int) $order['id']);
        flash_set('Pesanan ' . $order['order_no'] . ' dibatalkan. Stok produk dikembalikan.');
        redirect('admin/orders');
    }
}
