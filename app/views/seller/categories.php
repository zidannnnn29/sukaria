<section class="space-y-6">
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-cotton-50 p-5 sm:p-6 rounded-3xl border border-cotton-300 shadow-sm">
    <div>
      <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-bold bg-sogan-100 text-sogan-700 border border-sogan-400/40 mb-1.5">
        ❖ 6+ Kategori Kuliner Banyuwangi
      </span>
      <h2 class="font-serif text-xl sm:text-2xl font-bold text-espresso-900">Kelola Kategori</h2>
      <p class="text-xs sm:text-sm text-espresso-700 mt-0.5">Kategori dipakai bersama seluruh toko. Menghapus kategori memindahkan produknya ke kategori lain.</p>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
    <!-- Form Tambah/Edit -->
    <div class="lg:col-span-5">
      <div class="bg-cotton-50 rounded-3xl border border-cotton-300 shadow-sm overflow-hidden lg:sticky lg:top-36">
        <div class="batik-border-strip" aria-hidden="true"></div>
        <div class="p-5 sm:p-6">
          <h3 class="font-serif text-base sm:text-lg font-bold text-espresso-900 mb-1">
            <?= $editing ? 'Edit Kategori' : 'Tambah Kategori Baru' ?>
          </h3>
          <p class="text-xs text-espresso-700 mb-4"><?= $editing ? 'Perubahan nama ikut memengaruhi seluruh produk di kategori ini.' : 'Nama kategori harus unik dan mudah dipahami pembeli.' ?></p>

          <form method="post" action="<?= e(url('seller/categories/save')) ?>" class="space-y-4" novalidate>
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= $editing ? (int) $editing['id'] : '' ?>" />
            <div>
              <label for="cat-name" class="block text-xs font-bold text-espresso-900 mb-1">Nama Kategori <span class="text-osing-600">*</span></label>
              <input id="cat-name" type="text" name="name" value="<?= old('name', $editing['name'] ?? '') ?>" placeholder="Contoh: Kerajinan & Souvenir"
                     class="w-full px-4 py-2.5 rounded-2xl bg-white border border-cotton-300 text-sm text-espresso-900 focus:outline-none focus:ring-2 focus:ring-sogan-500" />
              <?php if (isset($errors['name'])): ?><p class="text-xs text-osing-600 font-semibold mt-1"><?= e($errors['name']) ?></p><?php endif; ?>
            </div>
            <div>
              <label for="cat-desc" class="block text-xs font-bold text-espresso-900 mb-1">Deskripsi Singkat</label>
              <textarea id="cat-desc" name="description" rows="2" placeholder="Jelaskan isi kategori ini..."
                        class="w-full px-4 py-2.5 rounded-2xl bg-white border border-cotton-300 text-sm text-espresso-900 focus:outline-none focus:ring-2 focus:ring-sogan-500 resize-none"><?= old('description', $editing['description'] ?? '') ?></textarea>
            </div>
            <label class="flex items-center gap-3 p-3 rounded-2xl bg-sogan-100/70 border border-sogan-400/50 cursor-pointer select-none">
              <input type="checkbox" name="is_active" <?= !isset($editing['is_active']) || (int) $editing['is_active'] === 1 ? 'checked' : '' ?> class="w-4 h-4 rounded accent-espresso-900 cursor-pointer" />
              <span class="text-sm font-bold text-espresso-900">Kategori Aktif (tampil di katalog)</span>
            </label>
            <div class="flex items-center gap-2.5 pt-1">
              <button type="submit" class="px-5 py-2.5 rounded-2xl bg-espresso-900 hover:bg-espresso-800 text-cotton-50 text-sm font-bold transition shadow-md border border-sogan-500/40">
                <?= $editing ? 'Simpan Perubahan' : 'Tambah Kategori' ?>
              </button>
              <?php if ($editing): ?>
                <a href="<?= e(url('seller/categories')) ?>" class="px-4 py-2.5 rounded-2xl bg-cotton-100 hover:bg-cotton-200 text-espresso-900 border border-cotton-300 text-sm font-bold transition">Batal</a>
              <?php endif; ?>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Daftar Kategori -->
    <div class="lg:col-span-7">
      <div class="bg-cotton-50 rounded-3xl border border-cotton-300 shadow-sm overflow-hidden">
        <div class="hidden md:block overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-cotton-200/70 border-b border-cotton-300 text-[11px] font-extrabold uppercase tracking-wider text-espresso-800">
                <th class="py-3.5 px-5">Kategori</th>
                <th class="py-3.5 px-4 text-center">Produk</th>
                <th class="py-3.5 px-4 text-center">Status</th>
                <th class="py-3.5 px-5 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-cotton-200 text-sm">
              <?php foreach ($categories as $c): ?>
                <tr class="hover:bg-cotton-100/80 transition <?= (int) $c['is_active'] === 0 ? 'opacity-70' : '' ?>">
                  <td class="py-3.5 px-5">
                    <p class="font-bold text-espresso-900"><?= e($c['name']) ?></p>
                    <p class="text-xs text-espresso-700 truncate max-w-xs"><?= e($c['description'] ?: '-') ?></p>
                  </td>
                  <td class="py-3.5 px-4 text-center">
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-cotton-200 text-espresso-800 border border-cotton-300"><?= (int) $c['product_count'] ?> produk</span>
                  </td>
                  <td class="py-3.5 px-4 text-center">
                    <form method="post" action="<?= e(url('seller/categories/toggle')) ?>">
                      <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $c['id'] ?>" />
                      <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold border transition <?= (int) $c['is_active'] === 1 ? 'bg-coffeeleaf-100 text-coffeeleaf-700 border-coffeeleaf-600/30' : 'bg-osing-100 text-osing-700 border-osing-500/30' ?>">
                        <span class="w-1.5 h-1.5 rounded-full <?= (int) $c['is_active'] === 1 ? 'bg-coffeeleaf-600' : 'bg-osing-600' ?>"></span>
                        <?= (int) $c['is_active'] === 1 ? 'Aktif' : 'Nonaktif' ?>
                      </button>
                    </form>
                  </td>
                  <td class="py-3.5 px-5 text-right">
                    <div class="inline-flex items-center gap-2">
                      <a href="<?= e(url('seller/categories', ['edit' => $c['id']])) ?>" class="px-3 py-1.5 rounded-xl bg-cotton-100 hover:bg-cotton-200 text-espresso-900 border border-cotton-300 text-xs font-bold transition">Edit</a>
                      <form method="post" action="<?= e(url('seller/categories/delete')) ?>" data-confirm-title="Hapus Kategori?" data-confirm-name="<?= e($c['name']) ?>">
                        <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $c['id'] ?>" />
                        <button type="submit" class="px-3 py-1.5 rounded-xl bg-osing-100 hover:bg-osing-500/20 text-osing-700 border border-osing-500/30 text-xs font-bold transition">Hapus</button>
                      </form>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <!-- Kartu mobile -->
        <div class="md:hidden divide-y divide-cotton-200">
          <?php foreach ($categories as $c): ?>
            <div class="p-4 space-y-3">
              <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                  <p class="font-bold text-sm text-espresso-900 truncate"><?= e($c['name']) ?></p>
                  <p class="text-xs text-espresso-700 truncate"><?= e($c['description'] ?: '-') ?></p>
                </div>
                <span class="px-2 py-0.5 rounded-full text-[11px] font-bold shrink-0 <?= (int) $c['is_active'] === 1 ? 'bg-coffeeleaf-100 text-coffeeleaf-700' : 'bg-osing-100 text-osing-700' ?>"><?= (int) $c['is_active'] === 1 ? 'Aktif' : 'Nonaktif' ?></span>
              </div>
              <div class="flex items-center justify-between pt-2 border-t border-cotton-200">
                <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-cotton-200 text-espresso-800"><?= (int) $c['product_count'] ?> produk</span>
                <div class="flex items-center gap-2">
                  <a href="<?= e(url('seller/categories', ['edit' => $c['id']])) ?>" class="px-3 py-2 rounded-xl bg-cotton-100 text-espresso-900 border border-cotton-300 text-xs font-bold">Edit</a>
                  <form method="post" action="<?= e(url('seller/categories/toggle')) ?>">
                    <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $c['id'] ?>" />
                    <button type="submit" class="px-3 py-2 rounded-xl bg-cotton-100 text-espresso-800 border border-cotton-300 text-xs font-bold"><?= (int) $c['is_active'] === 1 ? 'Nonaktifkan' : 'Aktifkan' ?></button>
                  </form>
                  <form method="post" action="<?= e(url('seller/categories/delete')) ?>" data-confirm-title="Hapus Kategori?" data-confirm-name="<?= e($c['name']) ?>">
                    <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $c['id'] ?>" />
                    <button type="submit" class="px-3 py-2 rounded-xl bg-osing-100 text-osing-700 border border-osing-500/30 text-xs font-bold">Hapus</button>
                  </form>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>
