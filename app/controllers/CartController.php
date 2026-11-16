<?php
/**
 * Controller keranjang belanja & checkout pembeli (akses publik, tanpa login).
 */
final class CartController
{
    public static function view(): never
    {
        [$items, $warnings] = Cart::validatedItems();
        foreach ($warnings as $w) {
            flash_set($w, 'error');
        }

        render('public', 'cart/index', [
            'pageTitle' => 'Keranjang Belanja — ' . SUKARIA_APP_NAME,
            'items'     => $items,
            'subtotal'  => Cart::subtotal($items),
        ]);
    }

    public static function add(): never
    {
        require_post();

        $productId = (int) ($_POST['product_id'] ?? 0);
        $qty       = (int) ($_POST['qty'] ?? 1);
        if ($productId <= 0) {
            flash_set('Produk tidak valid.', 'error');
            redirect_back('catalog');
        }

        [$ok, $message] = Cart::add($productId, $qty);
        flash_set($message, $ok ? 'success' : 'error');
        redirect_back('catalog');
    }

    public static function update(): never
    {
        require_post();

        $productId = (int) ($_POST['product_id'] ?? 0);
        $qty       = (int) ($_POST['qty'] ?? 0);
        if ($productId <= 0) {
            redirect('cart');
        }

        [$ok, $message] = Cart::set($productId, $qty);
        flash_set($message, $ok ? 'success' : 'error');
        redirect('cart');
    }

    public static function remove(): never
    {
        require_post();

        Cart::remove((int) ($_POST['product_id'] ?? 0));
        flash_set('Produk dihapus dari keranjang.');
        redirect('cart');
    }

    public static function clear(): never
    {
        require_post();

        Cart::clear();
        flash_set('Keranjang dikosongkan.');
        redirect('cart');
    }

    // --- CHECKOUT -------------------------------------------------------

    public static function checkoutForm(): never
    {
        [$items, $warnings] = Cart::validatedItems();
        foreach ($warnings as $w) {
            flash_set($w, 'error');
        }

        $subtotal = Cart::subtotal($items);
        if (!$items) {
            flash_set('Keranjang masih kosong. Tambahkan produk terlebih dahulu.', 'error');
            redirect('catalog');
        }

        render('public', 'cart/checkout', [
            'pageTitle' => 'Checkout & Transfer — ' . SUKARIA_APP_NAME,
            'items'     => $items,
            'total'     => $subtotal,
            'accounts'  => SUKARIA_PAYMENT_ACCOUNTS,
            'errors'    => $_SESSION['form_errors'] ?? [],
        ]);
    }

    public static function submit(): never
    {
        require_post();

        $name  = trim((string) ($_POST['buyer_name'] ?? ''));
        $phone = normalize_wa($_POST['buyer_phone'] ?? '');
        $note  = trim((string) ($_POST['buyer_note'] ?? ''));

        [$items, $warnings] = Cart::validatedItems();
        $errors = [];
        if (!$items) {
            $errors['cart'] = 'Keranjang kosong atau produk di dalamnya sudah tidak tersedia.';
        }
        if (mb_strlen($name) < 3) {
            $errors['buyer_name'] = 'Nama penerima wajib diisi (minimal 3 karakter).';
        }
        [$validPhone] = validate_wa($phone);
        if (!$validPhone) {
            $errors['buyer_phone'] = 'Nomor WhatsApp tidak valid (format 08..., 62..., atau +62...).';
        }

        if ($errors) {
            $_SESSION['form_errors'] = $errors;
            flash_old($_POST);
            flash_set('Periksa kembali data checkout.', 'error');
            redirect('checkout');
        }

        foreach ($warnings as $w) {
            flash_set($w, 'error');
        }

        $order = Order::createFromCart($items, Cart::subtotal($items), $name, $phone, $note);
        if (!$order) {
            $_SESSION['form_errors'] = ['cart' => 'Terjadi kesalahan saat membuat pesanan. Coba lagi.'];
            flash_old($_POST);
            redirect('checkout');
        }

        Cart::clear();
        clear_old();
        flash_set('Pesanan ' . $order['order_no'] . ' berhasil dibuat! Silakan lakukan transfer sesuai instruksi.');
        redirect('order/view', ['no' => $order['order_no']]);
    }
}
