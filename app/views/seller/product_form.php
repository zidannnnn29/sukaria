<section class="space-y-6">
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-cotton-50 p-5 sm:p-6 rounded-3xl border border-cotton-300 shadow-sm">
    <div>
      <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-bold bg-sogan-100 text-sogan-700 border border-sogan-400/40 mb-1.5">
        ❖ <?= $editing ? 'Edit Produk' : 'Produk Baru' ?>
      </span>
      <h2 class="font-serif text-xl sm:text-2xl font-bold text-espresso-900"><?= $editing ? 'Edit: ' . e($editing['name']) : 'Tambah Produk Baru' ?></h2>
      <p class="text-xs sm:text-sm text-espresso-700 mt-0.5">Lengkapi detail produk dan unggah foto atau gunakan ilustrasi batik otomatis.</p>
    </div>
    <a href="<?= e(url('seller/products')) ?>" class="px-4 py-2.5 rounded-xl bg-cotton-100 hover:bg-cotton-200 text-espresso-900 border border-cotton-300 text-xs sm:text-sm font-bold transition self-start">← Kembali ke Daftar Produk</a>
  </div>

  <div class="bg-cotton-50 rounded-3xl border border-cotton-300 shadow-sm overflow-hidden">
    <div class="batik-border-strip" aria-hidden="true"></div>
    <form id="product-form" method="post" action="<?= e(url('seller/products/save')) ?>" class="p-6 space-y-5" novalidate>
      <?= csrf_field() ?>
      <input type="hidden" name="id" value="<?= $editing ? (int) $editing['id'] : '' ?>" />
      <input type="hidden" name="image_data" id="image-data" value="" />
      <input type="hidden" name="remove_image" id="remove-image" value="" />

      <!-- Upload Gambar & Pratinjau -->
      <div class="bg-cotton-100/90 rounded-2xl p-4 border border-cotton-300 space-y-3.5">
        <div class="flex flex-wrap items-center justify-between gap-2">
          <span class="text-xs font-extrabold uppercase tracking-wider text-espresso-900">Visual &amp; Foto Produk</span>
          <span id="image-source-badge" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-sogan-100 text-sogan-700 border border-sogan-400/50">
            <?php if ($editing && $editing['image_path']): ?>📷 Foto Upload Kustom<?php else: ?>❖ Ilustrasi Batik Otomatis<?php endif; ?>
          </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-center">
          <div class="sm:col-span-5">
            <div class="relative aspect-[16/10] rounded-2xl overflow-hidden border border-cotton-300 bg-cotton-200 shadow-sm">
              <img id="form-image-preview"
                   src="<?= $editing && $editing['image_path'] ? e(product_image_url($editing)) : e(batik_placeholder_svg($editing['name'] ?? '', $editing['category_name'] ?? '')) ?>"
                   alt="Pratinjau visual produk" class="w-full h-full object-cover" />
            </div>
            <p id="image-compression-info" class="text-[11px] text-espresso-700 mt-1.5 text-center font-medium">Otomatis dikompresi maks. 640px (hemat penyimpanan)</p>
          </div>

          <div class="sm:col-span-7 space-y-2.5">
            <input id="field-imageFile" type="file" accept="image/png,image/jpeg,image/webp" class="hidden" />
            <div id="image-dropzone" tabindex="0" role="button" aria-label="Unggah foto produk dengan klik atau seret file"
                 class="border-2 border-dashed border-cotton-400 hover:border-sogan-500 focus:border-sogan-500 bg-cotton-50 hover:bg-sogan-100/30 rounded-2xl p-4 text-center cursor-pointer transition group">
              <div class="w-9 h-9 rounded-xl bg-espresso-900 text-sogan-400 mx-auto flex items-center justify-center mb-2 group-hover:scale-105 transition-transform">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
              </div>
              <p class="text-xs sm:text-sm font-bold text-espresso-900">Klik pilih foto atau seret gambar ke sini</p>
              <p class="text-[11px] text-espresso-700 mt-0.5">PNG, JPG, atau WEBP • maks. 15 MB</p>
            </div>
            <?php if (isset($errors['image_data'])): ?><p class="text-xs text-osing-600 font-semibold"><?= e($errors['image_data']) ?></p><?php endif; ?>
            <button id="btn-reset-image" type="button"
                    class="<?= ($editing && $editing['image_path']) ? '' : 'hidden' ?> w-full py-2 px-3 rounded-xl bg-osing-100 hover:bg-osing-500/20 text-osing-700 border border-osing-500/30 text-xs font-bold transition flex items-center justify-center gap-1.5">
              <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
              <span>Hapus Foto / Gunakan Ilustrasi Batik</span>
            </button>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
          <label for="f-name" class="block text-xs font-bold text-espresso-900 mb-1">Nama Produk <span class="text-osing-600">*</span></label>
          <input id="f-name" type="text" name="name" value="<?= old('name', $editing['name'] ?? '') ?>" placeholder="Contoh: Kopi Robusta Lanang Kemiren 250g"
                 class="w-full px-4 py-2.5 rounded-2xl bg-white border border-cotton-300 text-sm text-espresso-900 focus:outline-none focus:ring-2 focus:ring-sogan-500" />
          <?php if (isset($errors['name'])): ?><p class="text-xs text-osing-600 font-semibold mt-1"><?= e($errors['name']) ?></p><?php endif; ?>
        </div>

        <div>
          <label for="f-category" class="block text-xs font-bold text-espresso-900 mb-1">Kategori Produk <span class="text-osing-600">*</span></label>
          <div class="relative">
            <select id="f-category" name="category_id"
                    class="w-full px-4 py-2.5 rounded-2xl bg-white border border-cotton-300 text-sm font-semibold text-espresso-900 focus:outline-none focus:ring-2 focus:ring-sogan-500 appearance-none cursor-pointer">
              <?php foreach ($categories as $c): ?>
                <option value="<?= (int) $c['id'] ?>" <?= (int) old_raw('category_id', $editing['category_id'] ?? 0) === (int) $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
              <?php endforeach; ?>
            </select>
            <svg class="w-4 h-4 text-espresso-800 absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="m6 9 6 6 6-6"/></svg>
          </div>
          <?php if (isset($errors['category_id'])): ?><p class="text-xs text-osing-600 font-semibold mt-1"><?= e($errors['category_id']) ?></p><?php endif; ?>
        </div>

        <div>
          <label for="f-price" class="block text-xs font-bold text-espresso-900 mb-1">Harga (Rupiah) <span class="text-osing-600">*</span></label>
          <div class="relative">
            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-espresso-700">Rp</span>
            <input id="f-price" type="number" min="0" step="500" name="price" value="<?= old('price', (string) ($editing['price'] ?? '')) ?>" placeholder="25000"
                   class="w-full pl-10 pr-4 py-2.5 rounded-2xl bg-white border border-cotton-300 text-sm text-espresso-900 focus:outline-none focus:ring-2 focus:ring-sogan-500" />
          </div>
          <?php if (isset($errors['price'])): ?><p class="text-xs text-osing-600 font-semibold mt-1"><?= e($errors['price']) ?></p><?php endif; ?>
        </div>

        <div>
          <label for="f-stock" class="block text-xs font-bold text-espresso-900 mb-1">Stok <span class="text-osing-600">*</span></label>
          <input id="f-stock" type="number" min="0" step="1" name="stock" value="<?= old('stock', (string) ($editing['stock'] ?? '')) ?>" placeholder="20"
                 class="w-full px-4 py-2.5 rounded-2xl bg-white border border-cotton-300 text-sm text-espresso-900 focus:outline-none focus:ring-2 focus:ring-sogan-500" />
          <?php if (isset($errors['stock'])): ?><p class="text-xs text-osing-600 font-semibold mt-1"><?= e($errors['stock']) ?></p><?php endif; ?>
        </div>
      </div>

      <div>
        <label for="f-desc" class="block text-xs font-bold text-espresso-900 mb-1">Deskripsi Singkat</label>
        <textarea id="f-desc" name="description" rows="3" placeholder="Jelaskan bahan utama, cita rasa, berat bersih, atau daya tahan produk..."
                  class="w-full px-4 py-2.5 rounded-2xl bg-white border border-cotton-300 text-sm text-espresso-900 focus:outline-none focus:ring-2 focus:ring-sogan-500 resize-none"><?= old('description', $editing['description'] ?? '') ?></textarea>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <label class="flex items-center gap-3 p-3.5 rounded-2xl bg-sogan-100/70 border border-sogan-400/50 cursor-pointer select-none">
          <input type="checkbox" name="is_featured" <?= (!isset($editing['is_featured']) || (int) $editing['is_featured'] === 1) && !$editing ? '' : ((int) ($editing['is_featured'] ?? 0) === 1 ? 'checked' : '') ?> class="w-4 h-4 rounded accent-espresso-900 cursor-pointer" />
          <div>
            <span class="text-sm font-bold text-espresso-900">Tandai sebagai Produk Unggulan (★)</span>
            <p class="text-xs text-espresso-700">Produk unggulan tampil di baris teratas katalog.</p>
          </div>
        </label>
        <label class="flex items-center gap-3 p-3.5 rounded-2xl bg-coffeeleaf-100/70 border border-coffeeleaf-600/30 cursor-pointer select-none">
          <input type="checkbox" name="is_active" <?= !isset($editing['is_active']) || (int) $editing['is_active'] === 1 ? 'checked' : '' ?> class="w-4 h-4 rounded accent-coffeeleaf-600 cursor-pointer" />
          <div>
            <span class="text-sm font-bold text-espresso-900">Tampilkan di Katalog</span>
            <p class="text-xs text-espresso-700">Hanya produk aktif yang terlihat pembeli.</p>
          </div>
        </label>
      </div>

      <div class="pt-2 flex items-center justify-end gap-3 border-t border-cotton-300">
        <a href="<?= e(url('seller/products')) ?>" class="px-5 py-2.5 rounded-2xl bg-cotton-100 hover:bg-cotton-200 text-espresso-900 border border-cotton-300 text-sm font-bold transition">Batal</a>
        <button type="submit" class="px-6 py-2.5 rounded-2xl bg-espresso-900 hover:bg-espresso-800 text-cotton-50 text-sm font-bold transition shadow-md border border-sogan-500/40">Simpan Produk</button>
      </div>
    </form>
  </div>
</section>
