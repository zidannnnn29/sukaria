<section class="space-y-6">
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-cotton-50 p-5 sm:p-6 rounded-3xl border border-cotton-300 shadow-sm">
    <div>
      <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-bold bg-sogan-100 text-sogan-700 border border-sogan-400/40 mb-1.5">
        ❖ Panel Administrator
      </span>
      <h2 class="font-serif text-xl sm:text-2xl font-bold text-espresso-900">Dashboard Admin</h2>
      <p class="text-xs sm:text-sm text-espresso-700 mt-0.5">Pantau seluruh akun, toko UMKM, dan katalog Sukaria.</p>
    </div>
    <a href="<?= e(url('admin/users/create')) ?>"
       class="px-5 py-3 rounded-2xl bg-espresso-900 hover:bg-espresso-800 text-cotton-50 text-xs sm:text-sm font-bold transition shadow-md flex items-center gap-2 border border-sogan-500/40 self-start">
      <svg class="w-4 h-4 text-sogan-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
      <span>Tambah User Baru</span>
    </a>
  </div>

  <!-- Kartu Statistik -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-cotton-50 rounded-3xl p-5 border border-cotton-300 shadow-sm flex items-start justify-between">
      <div>
        <p class="text-xs font-bold uppercase tracking-wider text-espresso-700/75">Total User</p>
        <p class="font-serif text-3xl font-bold text-espresso-900 mt-1.5"><?= (int) $userStats['total_users'] ?></p>
        <p class="text-xs text-espresso-700 mt-1"><?= (int) $userStats['total_admins'] ?> admin • <?= (int) $userStats['total_sellers'] ?> seller</p>
      </div>
      <div class="w-11 h-11 rounded-2xl bg-espresso-900 text-sogan-400 flex items-center justify-center shrink-0">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      </div>
    </div>
    <div class="bg-cotton-50 rounded-3xl p-5 border border-cotton-300 shadow-sm flex items-start justify-between">
      <div>
        <p class="text-xs font-bold uppercase tracking-wider text-espresso-700/75">Seller Aktif</p>
        <p class="font-serif text-3xl font-bold text-coffeeleaf-700 mt-1.5"><?= (int) $userStats['active_sellers'] ?></p>
        <p class="text-xs text-espresso-700 mt-1">dari <?= (int) $userStats['total_sellers'] ?> seller terdaftar</p>
      </div>
      <div class="w-11 h-11 rounded-2xl bg-coffeeleaf-100 text-coffeeleaf-700 flex items-center justify-center shrink-0">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
      </div>
    </div>
    <div class="bg-cotton-50 rounded-3xl p-5 border border-cotton-300 shadow-sm flex items-start justify-between">
      <div>
        <p class="text-xs font-bold uppercase tracking-wider text-espresso-700/75">Total Produk</p>
        <p class="font-serif text-3xl font-bold text-osing-600 mt-1.5"><?= (int) $productStats['total_products'] ?></p>
        <p class="text-xs text-espresso-700 mt-1"><?= (int) $productStats['active_products'] ?> aktif • <?= (int) $productStats['out_of_stock'] ?> habis stok</p>
      </div>
      <div class="w-11 h-11 rounded-2xl bg-osing-100 text-osing-600 flex items-center justify-center shrink-0">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/></svg>
      </div>
    </div>
    <div class="bg-cotton-50 rounded-3xl p-5 border border-cotton-300 shadow-sm flex items-start justify-between">
      <div>
        <p class="text-xs font-bold uppercase tracking-wider text-espresso-700/75">Kategori</p>
        <p class="font-serif text-3xl font-bold text-sogan-600 mt-1.5"><?= (int) $userStats['total_categories'] ?></p>
        <p class="text-xs text-espresso-700 mt-1"><?= (int) $productStats['featured'] ?> produk unggulan</p>
      </div>
      <div class="w-11 h-11 rounded-2xl bg-sogan-100 text-sogan-600 flex items-center justify-center shrink-0">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
      </div>
    </div>
    <div class="bg-cotton-50 rounded-3xl p-5 border border-cotton-300 shadow-sm flex items-start justify-between">
      <div>
        <p class="text-xs font-bold uppercase tracking-wider text-espresso-700/75">Pesanan Transfer</p>
        <p class="font-serif text-3xl font-bold text-coffeeleaf-700 mt-1.5"><?= (int) $orderStats['total_orders'] ?></p>
        <p class="text-xs text-espresso-700 mt-1">
          <?= (int) $orderStats['verifying_count'] ?> verifikasi bukti •
          <a href="<?= e(url('admin/orders')) ?>" class="font-bold text-osing-600 hover:underline">Kelola →</a>
        </p>
      </div>
      <div class="w-11 h-11 rounded-2xl bg-coffeeleaf-100 text-coffeeleaf-700 flex items-center justify-center shrink-0">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="15" height="13" rx="2"/><path d="M16 8h4l3 4v4a2 2 0 0 1-2 2h-1"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="17.5" cy="18.5" r="2.5"/></svg>
      </div>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
    <!-- Ringkasan Seller -->
    <div class="bg-cotton-50 rounded-3xl border border-cotton-300 shadow-sm overflow-hidden">
      <div class="p-4 sm:p-5 border-b border-cotton-300">
        <h3 class="font-serif text-base sm:text-lg font-bold text-espresso-900">Ringkasan Toko Seller</h3>
      </div>
      <div class="divide-y divide-cotton-200">
        <?php if (count($sellers) === 0): ?>
          <p class="p-5 text-sm text-espresso-700">Belum ada seller terdaftar.</p>
        <?php else: foreach ($sellers as $s): ?>
          <div class="p-4 sm:px-5 flex items-center justify-between gap-3 hover:bg-cotton-100/60 transition">
            <div class="min-w-0">
              <p class="font-bold text-sm text-espresso-900 truncate"><?= e($s['store_name'] ?: $s['name']) ?></p>
              <p class="text-xs text-espresso-700 truncate"><?= e($s['name']) ?></p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
              <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-cotton-200 text-espresso-800 border border-cotton-300"><?= (int) $s['product_count'] ?> produk</span>
              <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold border <?= (int) $s['is_active'] === 1 ? 'bg-coffeeleaf-100 text-coffeeleaf-700 border-coffeeleaf-600/25' : 'bg-osing-100 text-osing-700 border-osing-500/30' ?>">
                <span class="w-1.5 h-1.5 rounded-full <?= (int) $s['is_active'] === 1 ? 'bg-coffeeleaf-600' : 'bg-osing-600' ?>"></span>
                <?= (int) $s['is_active'] === 1 ? 'Aktif' : 'Nonaktif' ?>
              </span>
            </div>
          </div>
        <?php endforeach; endif; ?>
      </div>
      <div class="p-4 border-t border-cotton-300 text-right">
        <a href="<?= e(url('admin/users')) ?>" class="text-xs font-bold text-osing-600 hover:text-osing-700 underline underline-offset-2">Kelola semua user →</a>
      </div>
    </div>

    <!-- Ringkasan Kategori -->
    <div class="bg-cotton-50 rounded-3xl border border-cotton-300 shadow-sm overflow-hidden">
      <div class="p-4 sm:p-5 border-b border-cotton-300">
        <h3 class="font-serif text-base sm:text-lg font-bold text-espresso-900">Sebaran Produk per Kategori</h3>
      </div>
      <div class="p-4 sm:p-5 space-y-3">
        <?php $maxCount = max(1, ...array_map(fn($c) => (int) $c['product_count'], $categories)); foreach ($categories as $c): ?>
          <div>
            <div class="flex items-center justify-between text-xs mb-1">
              <span class="font-bold text-espresso-900 truncate"><?= e($c['name']) ?></span>
              <span class="text-espresso-700 shrink-0 ml-2"><?= (int) $c['product_count'] ?> produk<?= (int) $c['is_active'] === 0 ? ' • nonaktif' : '' ?></span>
            </div>
            <div class="h-2 rounded-full bg-cotton-200 overflow-hidden">
              <div class="h-full rounded-full bg-gradient-to-r from-sogan-400 to-osing-600" style="width: <?= round(((int) $c['product_count'] / $maxCount) * 100) ?>%"></div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>
