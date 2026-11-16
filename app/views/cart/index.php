<?php
/**
 * Keranjang belanja pembeli: daftar item, ubah jumlah, subtotal, lanjut checkout.
 * Variabel: $items (list item tervalidasi), $subtotal
 */
?>
<section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-6">

  <div class="bg-batik-hero rounded-3xl p-6 sm:p-8 text-cotton-50 shadow-xl border border-sogan-500/40">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div class="space-y-1.5">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-sogan-500/20 text-sogan-200 border border-sogan-400/40">
          <span>🛒</span> Keranjang Belanja • Transfer Bank
        </div>
        <h2 class="font-serif text-2xl sm:text-3xl font-bold">Keranjang Belanja Anda</h2>
        <p class="text-sm text-cotton-200/90">Semua produk dikumpulkan jadi satu pesanan. Bayar sekali transfer, tanpa akun.</p>
      </div>
      <?php if ($items): ?>
      <form method="post" action="<?= e(url('cart/clear')) ?>" data-confirm-title="Kosongkan Keranjang?" data-confirm-name="seluruh isi keranjang">
        <?= csrf_field() ?>
        <button type="submit" class="px-4 py-2.5 rounded-2xl bg-osing-600/80 hover:bg-osing-600 text-cotton-50 border border-osing-500/40 text-xs font-bold transition">Kosongkan Keranjang</button>
      </form>
      <?php endif; ?>
    </div>
  </div>

  <?php if (!$items): ?>

    <div class="bg-cotton-50 rounded-3xl border border-cotton-300 p-10 sm:p-14 text-center space-y-4">
      <div class="w-16 h-16 rounded-2xl bg-sogan-100 text-sogan-700 border border-sogan-400/40 mx-auto flex items-center justify-center">
        <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
      </div>
      <h3 class="font-serif text-xl font-bold text-espresso-900">Keranjang masih kosong</h3>
      <p class="text-sm text-espresso-700 max-w-md mx-auto">Jelajahi katalog oleh-oleh khas Banyuwangi, lalu klik <span class="font-bold">+ Keranjang</span> pada produk yang Anda sukai.</p>
      <div class="flex flex-wrap items-center justify-center gap-3 pt-2">
        <a href="<?= e(url('catalog')) ?>" class="px-5 py-2.5 rounded-2xl bg-espresso-900 hover:bg-espresso-800 text-cotton-50 text-sm font-bold transition shadow-sm">Lihat Katalog</a>
        <a href="<?= e(url('order/lookup')) ?>" class="px-5 py-2.5 rounded-2xl bg-cotton-100 hover:bg-cotton-200 text-espresso-900 border border-cotton-300 text-sm font-bold transition">Cek Status Pesanan</a>
      </div>
    </div>

  <?php else: ?>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
      <!-- Daftar item -->
      <div class="lg:col-span-8 space-y-4">
        <?php foreach ($items as $item): ?>
        <div class="bg-cotton-50 rounded-3xl border border-cotton-300 shadow-sm p-4 sm:p-5 flex gap-4">
          <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl overflow-hidden border border-cotton-300 bg-cotton-200 shrink-0">
            <img src="<?= e($item['image']) ?>" alt="<?= e($item['name']) ?>" class="w-full h-full object-cover" loading="lazy" />
          </div>
          <div class="flex-1 min-w-0 space-y-2">
            <div class="flex items-start justify-between gap-3">
              <div class="min-w-0">
                <h3 class="font-serif font-bold text-espresso-900 leading-snug truncate"><?= e($item['name']) ?></h3>
                <p class="text-[11px] text-espresso-700">🏬 <?= e($item['store_name']) ?> • <?= e($item['category']) ?></p>
              </div>
              <form method="post" action="<?= e(url('cart/remove')) ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="product_id" value="<?= (int) $item['product_id'] ?>" />
                <button type="submit" class="p-2 rounded-xl text-osing-600 hover:bg-osing-100 transition" title="Hapus dari keranjang" aria-label="Hapus <?= e($item['name']) ?>">
                  <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                </button>
              </form>
            </div>
            <div class="flex flex-wrap items-center justify-between gap-3">
              <div class="flex items-center gap-2.5">
                <form method="post" action="<?= e(url('cart/update')) ?>">
                  <?= csrf_field() ?>
                  <input type="hidden" name="product_id" value="<?= (int) $item['product_id'] ?>" />
                  <input type="hidden" name="qty" value="<?= max(0, (int) $item['qty'] - 1) ?>" />
                  <button type="submit" class="w-8 h-8 rounded-xl bg-cotton-100 hover:bg-cotton-200 border border-cotton-300 text-espresso-900 font-bold transition flex items-center justify-center" aria-label="Kurangi jumlah">−</button>
                </form>
                <span class="min-w-8 text-center font-extrabold text-espresso-900" title="Stok tersedia: <?= (int) $item['stock'] ?>"><?= (int) $item['qty'] ?></span>
                <form method="post" action="<?= e(url('cart/update')) ?>">
                  <?= csrf_field() ?>
                  <input type="hidden" name="product_id" value="<?= (int) $item['product_id'] ?>" />
                  <input type="hidden" name="qty" value="<?= min((int) $item['qty'] + 1, (int) $item['stock']) ?>" />
                  <button type="submit" <?= (int) $item['qty'] >= (int) $item['stock'] ? 'disabled' : '' ?> class="w-8 h-8 rounded-xl bg-cotton-100 hover:bg-cotton-200 disabled:opacity-40 border border-cotton-300 text-espresso-900 font-bold transition flex items-center justify-center" aria-label="Tambah jumlah">+</button>
                </form>
              </div>
              <div class="text-right">
                <p class="text-[11px] text-espresso-700"><?= format_rupiah($item['price']) ?> × <?= (int) $item['qty'] ?></p>
                <p class="font-serif text-lg font-bold text-espresso-900"><?= format_rupiah($item['subtotal']) ?></p>
              </div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- Ringkasan -->
      <div class="lg:col-span-4 lg:sticky lg:top-28 space-y-4">
        <div class="bg-cotton-50 rounded-3xl border border-cotton-300 shadow-md overflow-hidden">
          <div class="h-1.5 w-full bg-gradient-to-r from-osing-500 via-sogan-500 to-coffeeleaf-600" aria-hidden="true"></div>
          <div class="p-5 space-y-4">
            <h3 class="font-serif text-lg font-bold text-espresso-900">Ringkasan Belanja</h3>
            <div class="space-y-1.5 text-sm">
              <div class="flex items-center justify-between text-espresso-700">
                <span><?= count($items) ?> jenis produk</span>
                <span><?= array_sum(array_column($items, 'qty')) ?> unit</span>
              </div>
              <div class="flex items-center justify-between border-t border-dashed border-cotton-300 pt-3">
                <span class="font-bold text-espresso-900">Total</span>
                <span class="font-serif text-2xl font-bold text-espresso-900"><?= format_rupiah($subtotal) ?></span>
              </div>
            </div>
            <div class="rounded-2xl bg-coffeeleaf-100/70 border border-coffeeleaf-600/25 p-3 text-[11px] sm:text-xs text-coffeeleaf-700 leading-relaxed">
              <strong>Cara bayar:</strong> satu transfer bank untuk seluruh pesanan. Instruksi rekening muncul setelah checkout.
            </div>
            <a href="<?= e(url('checkout')) ?>" class="block w-full py-3.5 px-4 rounded-2xl bg-coffeeleaf-600 hover:bg-coffeeleaf-700 text-white text-sm font-bold text-center transition shadow-sm">
              Lanjut ke Checkout →
            </a>
            <a href="<?= e(url('catalog')) ?>" class="block text-center text-xs font-bold text-osing-600 hover:text-osing-700 underline underline-offset-2">← Tambah produk lain</a>
          </div>
        </div>
      </div>
    </div>

  <?php endif; ?>
</section>
