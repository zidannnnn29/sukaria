<section class="space-y-6">
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-cotton-50 p-5 sm:p-6 rounded-3xl border border-cotton-300 shadow-sm">
    <div>
      <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-bold bg-sogan-100 text-sogan-700 border border-sogan-400/40 mb-1.5">
        ❖ CRUD Produk • Unggahan Foto
      </span>
      <h2 class="font-serif text-xl sm:text-2xl font-bold text-espresso-900">Kelola Produk Toko</h2>
      <p class="text-xs sm:text-sm text-espresso-700 mt-0.5">Tambah, ubah, sembunyikan, dan tandai produk unggulan toko Anda.</p>
    </div>
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
      <form method="get" action="index.php" class="relative sm:w-56">
        <input type="hidden" name="r" value="seller/products" />
        <input type="hidden" name="cat" value="<?= e($catFilter) ?>" />
        <input type="text" name="q" value="<?= e($q) ?>" placeholder="Cari nama produk..."
               class="w-full pl-9 pr-4 py-2.5 rounded-xl bg-cotton-100 border border-cotton-300 text-xs sm:text-sm text-espresso-900 placeholder:text-espresso-700/55 focus:outline-none focus:ring-2 focus:ring-sogan-500" />
        <svg class="w-4 h-4 text-espresso-700/60 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
      </form>
      <form method="get" action="index.php">
        <input type="hidden" name="r" value="seller/products" />
        <input type="hidden" name="q" value="<?= e($q) ?>" />
        <div class="relative sm:w-56">
          <select name="cat" onchange="this.form.submit()" class="w-full px-3.5 py-2.5 rounded-xl bg-cotton-100 border border-cotton-300 text-xs sm:text-sm font-semibold text-espresso-900 focus:outline-none focus:ring-2 focus:ring-sogan-500 appearance-none cursor-pointer">
            <option value="ALL">Semua Kategori</option>
            <?php foreach ($categories as $c): ?>
              <option value="<?= (int) $c['id'] ?>" <?= $catFilter === (string) $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
            <?php endforeach; ?>
          </select>
          <svg class="w-4 h-4 text-espresso-800 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
        </div>
      </form>
      <a href="<?= e(url('seller/products/create')) ?>"
         class="px-4 py-2.5 rounded-xl bg-espresso-900 hover:bg-espresso-800 text-cotton-50 text-xs sm:text-sm font-bold transition flex items-center justify-center gap-2 border border-sogan-500/40">
        <svg class="w-4 h-4 text-sogan-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
        Tambah Produk
      </a>
    </div>
  </div>

  <div class="bg-cotton-50 rounded-3xl border border-cotton-300 shadow-sm overflow-hidden">
    <div class="hidden md:block overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-cotton-200/70 border-b border-cotton-300 text-[11px] font-extrabold uppercase tracking-wider text-espresso-800">
            <th class="py-3.5 px-5">Produk</th>
            <th class="py-3.5 px-4">Kategori</th>
            <th class="py-3.5 px-4">Harga</th>
            <th class="py-3.5 px-4">Status Stok</th>
            <th class="py-3.5 px-4 text-center">Unggulan</th>
            <th class="py-3.5 px-4 text-center">Katalog</th>
            <th class="py-3.5 px-5 text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-cotton-200 text-sm">
          <?php if (count($products) === 0): ?>
            <tr><td colspan="7" class="py-10 text-center text-espresso-700">Tidak ada produk yang cocok.</td></tr>
          <?php else: foreach ($products as $p): ?>
            <tr class="hover:bg-cotton-100/80 transition <?= (int) $p['is_active'] === 0 ? 'opacity-70' : '' ?>">
              <td class="py-3.5 px-5">
                <div class="flex items-center gap-3.5">
                  <img src="<?= e(product_image_url($p)) ?>" alt="" class="w-16 h-11 rounded-xl object-cover border border-cotton-300 bg-cotton-200 shrink-0" />
                  <div class="min-w-0 max-w-xs lg:max-w-sm">
                    <p class="font-bold text-espresso-900 truncate"><?= e($p['name']) ?></p>
                    <p class="text-xs text-espresso-700 truncate"><?= e($p['description'] ?: '-') ?></p>
                  </div>
                </div>
              </td>
              <td class="py-3.5 px-4 whitespace-nowrap">
                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-cotton-200 text-espresso-800 border border-cotton-300"><?= e($p['category_name']) ?></span>
              </td>
              <td class="py-3.5 px-4 whitespace-nowrap font-serif font-bold text-espresso-900"><?= e(format_rupiah($p['price'])) ?></td>
              <td class="py-3.5 px-4 whitespace-nowrap">
                <?php if ((int) $p['stock'] <= 0): ?>
                  <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-osing-100 text-osing-700">Habis (0)</span>
                <?php elseif ((int) $p['stock'] <= 5): ?>
                  <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-sogan-100 text-sogan-700">Menipis (<?= (int) $p['stock'] ?>)</span>
                <?php else: ?>
                  <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-coffeeleaf-100 text-coffeeleaf-700"><?= (int) $p['stock'] ?> unit</span>
                <?php endif; ?>
              </td>
              <td class="py-3.5 px-4 text-center">
                <form method="post" action="<?= e(url('seller/products/toggle-featured')) ?>">
                  <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $p['id'] ?>" />
                  <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-bold border transition <?= (int) $p['is_featured'] === 1 ? 'bg-sogan-100 text-sogan-700 border-sogan-400 hover:bg-sogan-200/70' : 'bg-cotton-100 text-espresso-700 border-cotton-300 hover:text-espresso-900' ?>">
                    <?= (int) $p['is_featured'] === 1 ? '★ Unggulan' : '☆ Biasa' ?>
                  </button>
                </form>
              </td>
              <td class="py-3.5 px-4 text-center">
                <form method="post" action="<?= e(url('seller/products/toggle-active')) ?>">
                  <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $p['id'] ?>" />
                  <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold border transition <?= (int) $p['is_active'] === 1 ? 'bg-coffeeleaf-100 text-coffeeleaf-700 border-coffeeleaf-600/30' : 'bg-osing-100 text-osing-700 border-osing-500/30' ?>">
                    <span class="w-1.5 h-1.5 rounded-full <?= (int) $p['is_active'] === 1 ? 'bg-coffeeleaf-600' : 'bg-osing-600' ?>"></span>
                    <?= (int) $p['is_active'] === 1 ? 'Tampil' : 'Sembunyi' ?>
                  </button>
                </form>
              </td>
              <td class="py-3.5 px-5 text-right">
                <div class="inline-flex items-center gap-2">
                  <a href="<?= e(url('seller/products/edit', ['id' => $p['id']])) ?>" class="px-3 py-1.5 rounded-xl bg-cotton-100 hover:bg-cotton-200 text-espresso-900 border border-cotton-300 text-xs font-bold transition">Edit</a>
                  <form method="post" action="<?= e(url('seller/products/delete')) ?>" data-confirm-title="Hapus Produk?" data-confirm-name="<?= e($p['name']) ?>">
                    <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $p['id'] ?>" />
                    <button type="submit" class="px-3 py-1.5 rounded-xl bg-osing-100 hover:bg-osing-500/20 text-osing-700 border border-osing-500/30 text-xs font-bold transition">Hapus</button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Kartu mobile -->
    <div class="md:hidden divide-y divide-cotton-200">
      <?php foreach ($products as $p): ?>
        <div class="p-4 space-y-3">
          <div class="flex items-start gap-3">
            <img src="<?= e(product_image_url($p)) ?>" alt="" class="w-20 h-14 rounded-xl object-cover border border-cotton-300 shrink-0 bg-cotton-200" />
            <div class="min-w-0 flex-1">
              <div class="flex flex-wrap items-center gap-1.5 mb-1">
                <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-cotton-200 text-espresso-800"><?= e($p['category_name']) ?></span>
                <span class="px-2 py-0.5 rounded-full text-[11px] font-bold <?= (int) $p['stock'] <= 0 ? 'bg-osing-100 text-osing-700' : ((int) $p['stock'] <= 5 ? 'bg-sogan-100 text-sogan-700' : 'bg-coffeeleaf-100 text-coffeeleaf-700') ?>">
                  <?= (int) $p['stock'] <= 0 ? 'Stok Habis' : 'Stok: ' . (int) $p['stock'] ?>
                </span>
              </div>
              <h4 class="font-serif text-sm font-bold text-espresso-900 leading-snug"><?= e($p['name']) ?></h4>
              <p class="text-sm font-extrabold text-sogan-700 mt-0.5"><?= e(format_rupiah($p['price'])) ?></p>
            </div>
          </div>
          <div class="flex items-center justify-between gap-2 pt-2 border-t border-cotton-200">
            <form method="post" action="<?= e(url('seller/products/toggle-featured')) ?>">
              <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $p['id'] ?>" />
              <button type="submit" class="px-3 py-2 rounded-xl text-xs font-bold border transition <?= (int) $p['is_featured'] === 1 ? 'bg-sogan-100 text-sogan-700 border-sogan-400' : 'bg-cotton-100 text-espresso-700 border-cotton-300' ?>"><?= (int) $p['is_featured'] === 1 ? '★ Unggulan' : '☆ Tandai' ?></button>
            </form>
            <div class="flex items-center gap-2">
              <a href="<?= e(url('seller/products/edit', ['id' => $p['id']])) ?>" class="px-3.5 py-2 rounded-xl bg-cotton-100 text-espresso-900 border border-cotton-300 text-xs font-bold">Edit</a>
              <form method="post" action="<?= e(url('seller/products/delete')) ?>" data-confirm-title="Hapus Produk?" data-confirm-name="<?= e($p['name']) ?>">
                <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $p['id'] ?>" />
                <button type="submit" class="px-3.5 py-2 rounded-xl bg-osing-100 text-osing-700 border border-osing-500/30 text-xs font-bold">Hapus</button>
              </form>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
