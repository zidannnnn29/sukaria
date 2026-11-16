<?php
/**
 * Checkout keranjang: data pembeli + ringkasan + pratinjau rekening transfer.
 * Variabel: $items, $total, $accounts, $errors
 */
$err = fn(string $k) => isset($errors[$k]) ? '<p class="text-xs text-osing-600 font-semibold">' . e($errors[$k]) . '</p>' : '';
?>
<section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-6">

  <div class="bg-batik-hero rounded-3xl p-6 sm:p-8 text-cotton-50 shadow-xl border border-sogan-500/40">
    <div class="space-y-1.5">
      <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-sogan-500/20 text-sogan-200 border border-sogan-400/40">
        <span>💳</span> Checkout • Transfer Bank
      </div>
      <h2 class="font-serif text-2xl sm:text-3xl font-bold">Selesaikan Pesanan</h2>
      <p class="text-sm text-cotton-200/90">Isi data penerima, lalu transfer tepat sesuai total. Tanpa akun — status pesanan bisa dicek lewat nomor pesanan.</p>
    </div>
  </div>

  <?php if (isset($errors['cart'])): ?>
    <div class="bg-osing-100 border border-osing-500/40 text-osing-700 rounded-2xl p-4 text-sm font-semibold space-y-2">
      <p><?= e($errors['cart']) ?></p>
      <a href="<?= e(url('catalog')) ?>" class="inline-block px-4 py-2 rounded-xl bg-osing-600 text-white text-xs font-bold">← Kembali ke Katalog</a>
    </div>
  <?php else: ?>

  <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
    <!-- Form data pembeli -->
    <form method="post" action="<?= e(url('checkout/submit')) ?>" class="lg:col-span-7 bg-cotton-50 rounded-3xl border border-cotton-300 shadow-sm p-5 sm:p-7 space-y-5" novalidate>
      <?= csrf_field() ?>
      <h3 class="font-serif text-lg font-bold text-espresso-900 border-b border-cotton-200 pb-3">Data Penerima Pesanan</h3>

      <div class="space-y-1.5">
        <label for="f-buyer-name" class="block text-xs font-bold text-espresso-900">Nama Penerima <span class="text-osing-600">*</span></label>
        <input id="f-buyer-name" name="buyer_name" type="text" value="<?= old('buyer_name') ?>" placeholder="Contoh: Ibu Ani Wulandari"
               class="w-full px-4 py-2.5 rounded-2xl bg-white border border-cotton-300 text-sm text-espresso-900 focus:outline-none focus:ring-2 focus:ring-sogan-500 focus:border-sogan-500 transition" />
        <?= $err('buyer_name') ?>
      </div>

      <div class="space-y-1.5">
        <label for="f-buyer-phone" class="block text-xs font-bold text-espresso-900">Nomor WhatsApp <span class="text-osing-600">*</span></label>
        <input id="f-buyer-phone" name="buyer_phone" type="tel" inputmode="tel" value="<?= old('buyer_phone') ?>" placeholder="Contoh: 0812-3456-7890"
               class="w-full px-4 py-2.5 rounded-2xl bg-white border border-cotton-300 text-sm text-espresso-900 focus:outline-none focus:ring-2 focus:ring-sogan-500 focus:border-sogan-500 transition" />
        <p class="text-[11px] text-espresso-700">Dipakai untuk konfirmasi pesanan &amp; info pengiriman. Format 08... / 62... / +62...</p>
        <?= $err('buyer_phone') ?>
      </div>

      <div class="space-y-1.5">
        <label for="f-buyer-note" class="block text-xs font-bold text-espresso-900">Catatan (opsional)</label>
        <textarea id="f-buyer-note" name="buyer_note" rows="3" placeholder="Contoh: tolong dikemas rapi untuk oleh-oleh, konfirmasi pengiriman via WhatsApp."
                  class="w-full px-4 py-2.5 rounded-2xl bg-white border border-cotton-300 text-sm text-espresso-900 focus:outline-none focus:ring-2 focus:ring-sogan-500 focus:border-sogan-500 transition"></textarea>
      </div>

      <div class="rounded-2xl bg-sogan-100/70 border border-sogan-400/40 p-4 text-xs text-espresso-800 leading-relaxed">
        <p class="font-bold mb-1">❖ Cara pembayaran:</p>
        <ol class="list-decimal list-inside space-y-0.5">
          <li>Klik <strong>Buat Pesanan &amp; Lihat Instruksi Transfer</strong>.</li>
          <li>Transfer <strong>tepat sesuai total</strong> ke rekening yang tertera.</li>
          <li>Unggah <strong>foto bukti transfer</strong> di halaman pesanan.</li>
          <li>Admin verifikasi → status pesanan menjadi <strong>Lunas</strong>.</li>
        </ol>
      </div>

      <button type="submit" class="w-full py-3.5 px-4 rounded-2xl bg-coffeeleaf-600 hover:bg-coffeeleaf-700 text-white text-sm font-bold transition shadow-sm flex items-center justify-center gap-2">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><polyline points="20 6 9 17 4 12"/></svg>
        Buat Pesanan &amp; Lihat Instruksi Transfer
      </button>
    </form>

    <!-- Ringkasan pesanan -->
    <div class="lg:col-span-5 lg:sticky lg:top-28 space-y-4">
      <div class="bg-cotton-50 rounded-3xl border border-cotton-300 shadow-md overflow-hidden">
        <div class="h-1.5 w-full bg-gradient-to-r from-osing-500 via-sogan-500 to-coffeeleaf-600" aria-hidden="true"></div>
        <div class="p-5 space-y-4">
          <h3 class="font-serif text-lg font-bold text-espresso-900">Pesanan Anda</h3>
          <ul class="divide-y divide-dashed divide-cotton-200 text-sm">
            <?php foreach ($items as $item): ?>
            <li class="py-2.5 flex items-start justify-between gap-3">
              <div class="min-w-0">
                <p class="font-bold text-espresso-900 truncate"><?= e($item['name']) ?></p>
                <p class="text-[11px] text-espresso-700">🏬 <?= e($item['store_name']) ?> • <?= (int) $item['qty'] ?> × <?= format_rupiah($item['price']) ?></p>
              </div>
              <span class="font-bold text-espresso-900 shrink-0"><?= format_rupiah($item['subtotal']) ?></span>
            </li>
            <?php endforeach; ?>
          </ul>
          <div class="flex items-center justify-between border-t border-cotton-200 pt-3">
            <span class="font-bold text-espresso-900">Total Transfer</span>
            <span class="font-serif text-2xl font-bold text-coffeeleaf-700"><?= format_rupiah($total) ?></span>
          </div>
        </div>
      </div>

      <div class="bg-cotton-50 rounded-3xl border border-cotton-300 shadow-sm p-5 space-y-3">
        <h4 class="text-xs font-bold uppercase tracking-wider text-espresso-700/80">Rekening Tujuan Transfer</h4>
        <?php foreach ($accounts as $acc): ?>
          <div class="rounded-2xl bg-sogan-100/60 border border-sogan-400/40 p-3.5">
            <p class="text-xs font-bold text-espresso-900"><?= e($acc['bank']) ?></p>
            <p class="font-mono text-sm font-bold text-coffeeleaf-700 tracking-wide"><?= e($acc['number']) ?></p>
            <p class="text-[11px] text-espresso-700">a.n. <?= e($acc['holder']) ?></p>
          </div>
        <?php endforeach; ?>
        <p class="text-[11px] text-espresso-700">Cantumkan <strong>nomor pesanan</strong> Anda pada berita/catatan transfer agar cepat terverifikasi.</p>
      </div>

      <a href="<?= e(url('cart')) ?>" class="block text-center text-xs font-bold text-osing-600 hover:text-osing-700 underline underline-offset-2">← Kembali ke Keranjang</a>
    </div>
  </div>

  <?php endif; ?>
</section>
