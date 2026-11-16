<section class="space-y-6">
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-cotton-50 p-5 sm:p-6 rounded-3xl border border-cotton-300 shadow-sm">
    <div>
      <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-bold bg-sogan-100 text-sogan-700 border border-sogan-400/40 mb-1.5">
        ❖ Panel Pengelola Toko
      </span>
      <h2 class="font-serif text-xl sm:text-2xl font-bold text-espresso-900"><?= e($user['store_name'] ?: 'Dashboard Toko') ?></h2>
      <p class="text-xs sm:text-sm text-espresso-700 mt-0.5">Kelola produk, kategori, dan tujuan pesanan WhatsApp toko Anda.</p>
    </div>
    <a href="<?= e(url('seller/settings')) ?>" class="px-4 py-2.5 rounded-2xl bg-cotton-100 hover:bg-cotton-200 text-espresso-900 border border-cotton-300 text-xs sm:text-sm font-bold transition self-start flex items-center gap-2">
      <span class="w-2 h-2 rounded-full bg-[#25D366]"></span>
      WA Aktif: <strong><?= e(format_wa_display($waNumber)) ?></strong>
    </a>
  </div>

  <!-- 4 Kartu Statistik -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-cotton-50 rounded-3xl p-5 border border-cotton-300 shadow-sm flex items-start justify-between">
      <div>
        <p class="text-xs font-bold uppercase tracking-wider text-espresso-700/75">Total Produk</p>
        <p class="font-serif text-3xl font-bold text-espresso-900 mt-1.5"><?= (int) $stats['total_products'] ?></p>
        <p class="text-xs text-espresso-700 mt-1">Item terdaftar di katalog</p>
      </div>
      <div class="w-11 h-11 rounded-2xl bg-espresso-900 text-sogan-400 flex items-center justify-center shrink-0">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/></svg>
      </div>
    </div>
    <div class="bg-cotton-50 rounded-3xl p-5 border border-cotton-300 shadow-sm flex items-start justify-between">
      <div>
        <p class="text-xs font-bold uppercase tracking-wider text-espresso-700/75">Total Stok Unit</p>
        <p class="font-serif text-3xl font-bold text-coffeeleaf-700 mt-1.5"><?= (int) $stats['total_stock'] ?></p>
        <p class="text-xs text-espresso-700 mt-1">
          <?php if ($stats['low_stock'] > 0 || $stats['out_of_stock'] > 0): ?>
            <?= (int) $stats['low_stock'] ?> menipis • <?= (int) $stats['out_of_stock'] ?> habis
          <?php else: ?>Semua stok aman<?php endif; ?>
        </p>
      </div>
      <div class="w-11 h-11 rounded-2xl bg-coffeeleaf-100 text-coffeeleaf-700 flex items-center justify-center shrink-0">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 7h-9"/><path d="M14 17H5"/><circle cx="17" cy="17" r="3"/><circle cx="7" cy="7" r="3"/></svg>
      </div>
    </div>
    <div class="bg-cotton-50 rounded-3xl p-5 border border-cotton-300 shadow-sm flex items-start justify-between">
      <div>
        <p class="text-xs font-bold uppercase tracking-wider text-espresso-700/75">Produk Unggulan</p>
        <p class="font-serif text-3xl font-bold text-sogan-600 mt-1.5"><?= (int) $stats['featured_count'] ?></p>
        <p class="text-xs text-espresso-700 mt-1">Prioritas katalog utama</p>
      </div>
      <div class="w-11 h-11 rounded-2xl bg-sogan-100 text-sogan-600 flex items-center justify-center shrink-0">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
      </div>
    </div>
    <div class="bg-cotton-50 rounded-3xl p-5 border border-cotton-300 shadow-sm flex items-start justify-between">
      <div>
        <p class="text-xs font-bold uppercase tracking-wider text-espresso-700/75">Nomor WA Toko</p>
        <p class="font-serif text-lg font-bold text-osing-600 mt-2"><?= e(format_wa_display($waNumber)) ?></p>
        <p class="text-xs text-espresso-700 mt-1">Tujuan semua pesanan</p>
      </div>
      <div class="w-11 h-11 rounded-2xl bg-[#25D366]/15 text-[#1EBE5D] flex items-center justify-center shrink-0">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12.031 2c-5.516 0-9.969 4.453-9.969 9.969 0 1.758.459 3.473 1.331 4.986L2 22l5.188-1.36a9.927 9.927 0 0 0 4.843 1.258h.004c5.515 0 9.968-4.453 9.968-9.969A9.932 9.932 0 0 0 19.08 4.92 9.914 9.914 0 0 0 12.031 2Zm5.816 14.273c-.245.691-1.425 1.321-1.976 1.404-.507.076-1.148.108-1.852-.116-.427-.135-.975-.316-1.677-.619-2.952-1.274-4.88-4.246-5.027-4.442-.147-.197-1.2-1.596-1.2-3.045 0-1.449.76-2.161 1.03-2.456.27-.295.59-.369.786-.369.196 0 .393.002.565.01.181.009.424-.069.663.506.245.59.835 2.038.909 2.186.074.148.123.32.025.517-.098.197-.147.32-.295.492-.147.172-.31.384-.442.516-.148.148-.301.308-.129.603.172.295.764 1.26 1.641 2.041 1.128 1.005 2.079 1.317 2.374 1.464.295.148.467.123.639-.074.172-.197.737-.86.934-1.155.197-.295.393-.246.663-.148.27.098 1.719.811 2.014.958.295.148.491.221.565.344.074.123.074.713-.171 1.404Z"/></svg>
      </div>
    </div>
  </div>

  <!-- Produk Terbaru -->
  <div class="bg-cotton-50 rounded-3xl border border-cotton-300 shadow-sm overflow-hidden">
    <div class="p-4 sm:p-5 border-b border-cotton-300 flex items-center justify-between">
      <h3 class="font-serif text-base sm:text-lg font-bold text-espresso-900">Produk Terbaru</h3>
      <a href="<?= e(url('seller/products')) ?>" class="text-xs font-bold text-osing-600 hover:text-osing-700 underline underline-offset-2">Kelola semua produk →</a>
    </div>
    <div class="divide-y divide-cotton-200">
      <?php if (count($products) === 0): ?>
        <div class="p-6 text-center space-y-2">
          <p class="text-sm font-bold text-espresso-900">Belum ada produk</p>
          <a href="<?= e(url('seller/products/create')) ?>" class="inline-block px-5 py-2.5 rounded-2xl bg-espresso-900 hover:bg-espresso-800 text-cotton-50 text-xs font-bold transition">+ Tambah Produk Pertama</a>
        </div>
      <?php else: foreach ($products as $p): ?>
        <div class="p-4 sm:px-5 flex items-center gap-3.5 hover:bg-cotton-100/60 transition">
          <img src="<?= e(product_image_url($p)) ?>" alt="" class="w-14 h-10 rounded-xl object-cover border border-cotton-300 bg-cotton-200 shrink-0" />
          <div class="min-w-0 flex-1">
            <p class="font-bold text-sm text-espresso-900 truncate"><?= e($p['name']) ?></p>
            <p class="text-xs text-espresso-700 truncate"><?= e($p['category_name']) ?> • <?= e(format_rupiah($p['price'])) ?></p>
          </div>
          <?php if ((int) $p['stock'] <= 0): ?>
            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-osing-100 text-osing-700 shrink-0">Habis</span>
          <?php elseif ((int) $p['stock'] <= 5): ?>
            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-sogan-100 text-sogan-700 shrink-0">Menipis (<?= (int) $p['stock'] ?>)</span>
          <?php else: ?>
            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-coffeeleaf-100 text-coffeeleaf-700 shrink-0"><?= (int) $p['stock'] ?> unit</span>
          <?php endif; ?>
        </div>
      <?php endforeach; endif; ?>
    </div>
  </div>
</section>
