<?php
/**
 * Panel Seller — Pesanan Toko (read-only): pesanan yang memuat produk toko ini.
 * Variabel: $orders, $user
 */
?>
<section class="space-y-6">

  <div class="bg-cotton-50 p-5 sm:p-6 rounded-3xl border border-cotton-300 shadow-sm">
    <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-bold bg-sogan-100 text-sogan-700 border border-sogan-400/40 mb-1.5">
      ❖ Pesanan • Keranjang &amp; Transfer
    </span>
    <h2 class="font-serif text-xl sm:text-2xl font-bold text-espresso-900">Pesanan Toko</h2>
    <p class="text-xs sm:text-sm text-espresso-700 mt-0.5">Pesanan pembeli yang memuat produk Anda. Verifikasi pembayaran dilakukan admin; siapkan pesanan bila sudah <strong>Lunas</strong>.</p>
  </div>

  <?php if (!$orders): ?>
    <div class="bg-cotton-50 rounded-3xl border border-cotton-300 p-10 text-center space-y-2">
      <p class="text-3xl">🛒</p>
      <h3 class="font-serif font-bold text-espresso-900">Belum ada pesanan untuk toko Anda</h3>
      <p class="text-sm text-espresso-700">Bila pembeli memasukkan produk Anda ke keranjang dan checkout, pesanannya muncul di sini.</p>
    </div>
  <?php else: ?>
    <div class="space-y-4">
      <?php foreach ($orders as $o): $meta = Order::statusMeta($o['status']); ?>
        <div class="bg-cotton-50 rounded-3xl border border-cotton-300 shadow-sm overflow-hidden">
          <div class="px-5 py-4 border-b border-cotton-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div class="flex items-center gap-3 flex-wrap">
              <a href="<?= e(url('order/view', ['no' => $o['order_no']])) ?>" target="_blank" rel="noopener" class="font-mono font-bold text-coffeeleaf-700 hover:underline"><?= e($o['order_no']) ?></a>
              <span class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-extrabold <?= $meta['class'] ?>"><?= e($meta['label']) ?></span>
              <span class="text-[10px] text-espresso-700"><?= e(date('d M Y H:i', strtotime($o['created_at']))) ?></span>
            </div>
            <p class="text-xs text-espresso-700">Pembeli: <span class="font-bold text-espresso-900"><?= e($o['buyer_name']) ?></span> <span class="font-mono"><?= e(format_wa_display($o['buyer_phone'])) ?></span></p>
          </div>
          <ul class="divide-y divide-dashed divide-cotton-200 px-5">
            <?php foreach ($o['items'] as $item): ?>
              <li class="py-2.5 flex items-center justify-between gap-3 text-sm">
                <div>
                  <p class="font-bold text-espresso-900"><?= e($item['product_name']) ?></p>
                  <p class="text-[11px] text-espresso-700"><?= (int) $item['qty'] ?> × <?= format_rupiah((int) $item['unit_price']) ?></p>
                </div>
                <span class="font-bold text-espresso-900"><?= format_rupiah((int) $item['subtotal']) ?></span>
              </li>
            <?php endforeach; ?>
          </ul>
          <div class="px-5 py-3 bg-cotton-100/60 text-[11px] text-espresso-700">
            Total seluruh pesanan pembeli: <span class="font-bold"><?= format_rupiah((int) $o['total_amount']) ?></span> (gabungan semua toko — transfer tunggal)
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <p class="text-[11px] text-espresso-700 px-2">Total: <?= count($orders) ?> pesanan memuat produk Anda.</p>
  <?php endif; ?>
</section>
