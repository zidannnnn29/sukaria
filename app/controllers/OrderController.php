<?php
/**
 * Controller pesanan pembeli: lihat status, unggah bukti transfer, cek nomor pesanan.
 */
final class OrderController
{
    public static function view(): never
    {
        $orderNo = trim((string) ($_GET['no'] ?? ''));
        $order   = $orderNo !== '' ? Order::findByNo($orderNo) : null;

        if ($orderNo !== '' && !$order) {
            flash_set('Pesanan dengan nomor "' . $orderNo . '" tidak ditemukan.', 'error');
            redirect('order/lookup');
        }

        render('public', 'order/view', [
            'pageTitle' => ($order ? 'Pesanan ' . $order['order_no'] : 'Cek Status Pesanan') . ' — ' . SUKARIA_APP_NAME,
            'order'     => $order,
            'accounts'  => SUKARIA_PAYMENT_ACCOUNTS,
            'proofUrl'  => $order ? proof_image_url($order['proof_path']) : null,
        ]);
    }

    public static function lookup(): never
    {
        render('public', 'order/lookup', [
            'pageTitle' => 'Cek Status Pesanan — ' . SUKARIA_APP_NAME,
            'orderNo'   => trim((string) ($_GET['no'] ?? '')),
        ]);
    }

    public static function lookupSubmit(): never
    {
        require_post();

        $orderNo = trim((string) ($_POST['order_no'] ?? ''));
        if ($orderNo === '') {
            flash_set('Masukkan nomor pesanan terlebih dahulu.', 'error');
            redirect('order/lookup');
        }

        $order = Order::findByNo($orderNo);
        if (!$order) {
            flash_set('Pesanan "' . $orderNo . '" tidak ditemukan. Periksa kembali nomornya.', 'error');
            redirect('order/lookup');
        }

        redirect('order/view', ['no' => $order['order_no']]);
    }

    /** Unggah bukti transfer (base64 hasil kompresi client-side). */
    public static function uploadProof(): never
    {
        require_post();

        $orderNo = trim((string) ($_POST['order_no'] ?? ''));
        $image   = trim((string) ($_POST['image_data'] ?? ''));
        $order   = Order::findByNo($orderNo);

        if (!$order) {
            flash_set('Pesanan tidak ditemukan.', 'error');
            redirect('order/lookup');
        }
        if (!in_array($order['status'], ['pending', 'rejected'], true)) {
            flash_set('Bukti transfer sudah pernah diunggah untuk pesanan ini.', 'error');
            redirect('order/view', ['no' => $order['order_no']]);
        }

        $filename = save_base64_image($image, SUKARIA_PROOF_DIR, 'pay');
        if ($filename === null) {
            flash_set('Foto bukti transfer tidak dapat diproses. Coba foto lain.', 'error');
            redirect('order/view', ['no' => $order['order_no']]);
        }

        delete_proof_image($order['proof_path']);
        Order::setProof((int) $order['id'], $filename);
        flash_set('Bukti transfer terkirim! Admin akan memverifikasi pembayaran Anda (1×24 jam).');
        redirect('order/view', ['no' => $order['order_no']]);
    }
}
