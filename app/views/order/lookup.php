<?php
/**
 * Cek status pesanan transfer: masukkan nomor pesanan (SUK-XXXXXX-XXXX).
 * Variabel: $orderNo
 */
?>
<section class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14 space-y-6">

  <div class="bg-batik-hero rounded-3xl p-6 sm:p-8 text-cotton-50 shadow-xl border border-sogan-500/40">
    <div class="space-y-1.5">
      <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-sogan-500/20 text-sogan-200 border border-sogan-400/40">
        <span>🔎</span> Cek Status Pesanan
      </div>
      <h2 class="font-serif text-2xl sm:text-3xl font-bold">Lacak Pesanan Anda</h2>
      <p class="text-sm text-cotton-200/90">Masukkan nomor pesanan yang Anda terima setelah checkout (format <span class="font-mono font-bold">SUK-XXXXXX-XXXX</span>).</p>
    </div>
  </div>

  <form method="post" action="<?= e(url('order/find')) ?>" class="bg-cotton-50 rounded-3xl border border-cotton-300 shadow-md p-6 sm:p-8 space-y-4">
    <?= csrf_field() ?>
    <div class="space-y-1.5">
      <label for="f-order-no" class="block text-xs font-bold text-espresso-900">Nomor Pesanan <span class="text-osing-600">*</span></label>
      <input id="f-order-no" name="order_no" type="text" required value="<?= e($orderNo) ?>" placeholder="Contoh: SUK-261116-A3F2"
             class="w-full px-4 py-3 rounded-2xl bg-white border border-cotton-300 font-mono text-sm font-bold text-espresso-900 uppercase tracking-wider placeholder:font-normal placeholder:text-espresso-700/50 placeholder:normal-case focus:outline-none focus:ring-2 focus:ring-sogan-500 focus:border-sogan-500 transition" />
    </div>
    <button type="submit" class="w-full py-3.5 px-4 rounded-2xl bg-espresso-900 hover:bg-espresso-800 text-cotton-50 text-sm font-bold transition shadow-sm">
      Cek Status Pesanan
    </button>
    <p class="text-[11px] text-espresso-700 text-center">Belum pesan apa-apa? <a href="<?= e(url('catalog')) ?>" class="font-bold text-osing-600 hover:text-osing-700 underline underline-offset-2">Lihat katalog oleh-oleh →</a></p>
  </form>
</section>
