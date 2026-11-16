<section class="space-y-6">
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-cotton-50 p-5 sm:p-6 rounded-3xl border border-cotton-300 shadow-sm">
    <div>
      <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-bold bg-sogan-100 text-sogan-700 border border-sogan-400/40 mb-1.5">
        ❖ <?= $editing ? 'Edit Akun' : 'Akun Baru' ?>
      </span>
      <h2 class="font-serif text-xl sm:text-2xl font-bold text-espresso-900"><?= $editing ? 'Edit User: ' . e($editing['name']) : 'Tambah User Baru' ?></h2>
      <p class="text-xs sm:text-sm text-espresso-700 mt-0.5">Admin mengelola sistem; seller mengelola toko &amp; produknya sendiri.</p>
    </div>
    <a href="<?= e(url('admin/users')) ?>" class="px-4 py-2.5 rounded-xl bg-cotton-100 hover:bg-cotton-200 text-espresso-900 border border-cotton-300 text-xs sm:text-sm font-bold transition self-start">← Kembali ke Daftar User</a>
  </div>

  <div class="bg-cotton-50 rounded-3xl border border-cotton-300 shadow-sm overflow-hidden">
    <div class="batik-border-strip" aria-hidden="true"></div>
    <form method="post" action="<?= e(url('admin/users/save')) ?>" class="p-6 space-y-5" novalidate>
      <?= csrf_field() ?>
      <input type="hidden" name="id" value="<?= $editing ? (int) $editing['id'] : '' ?>" />

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
          <label for="f-name" class="block text-xs font-bold text-espresso-900 mb-1">Nama Lengkap <span class="text-osing-600">*</span></label>
          <input id="f-name" type="text" name="name" value="<?= old('name', $editing['name'] ?? '') ?>" placeholder="Contoh: Ibu Sri"
                 class="w-full px-4 py-2.5 rounded-2xl bg-white border border-cotton-300 text-sm text-espresso-900 focus:outline-none focus:ring-2 focus:ring-sogan-500" />
          <?php if (isset($errors['name'])): ?><p class="text-xs text-osing-600 font-semibold mt-1"><?= e($errors['name']) ?></p><?php endif; ?>
        </div>

        <div>
          <label for="f-email" class="block text-xs font-bold text-espresso-900 mb-1">Email <span class="text-osing-600">*</span></label>
          <input id="f-email" type="email" name="email" value="<?= old('email', $editing['email'] ?? '') ?>" placeholder="nama@sukaria.id"
                 class="w-full px-4 py-2.5 rounded-2xl bg-white border border-cotton-300 text-sm text-espresso-900 focus:outline-none focus:ring-2 focus:ring-sogan-500" />
          <?php if (isset($errors['email'])): ?><p class="text-xs text-osing-600 font-semibold mt-1"><?= e($errors['email']) ?></p><?php endif; ?>
        </div>

        <div>
          <label for="f-role" class="block text-xs font-bold text-espresso-900 mb-1">Role <span class="text-osing-600">*</span></label>
          <div class="relative">
            <select id="f-role" name="role" data-toggle-store class="w-full px-4 py-2.5 rounded-2xl bg-white border border-cotton-300 text-sm font-semibold text-espresso-900 focus:outline-none focus:ring-2 focus:ring-sogan-500 appearance-none cursor-pointer">
              <option value="seller" <?= ($editing['role'] ?? 'seller') === 'seller' ? 'selected' : '' ?>>Seller (Pengelola Toko)</option>
              <option value="admin"  <?= ($editing['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin (Administrator)</option>
            </select>
            <svg class="w-4 h-4 text-espresso-800 absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="m6 9 6 6 6-6"/></svg>
          </div>
          <?php if (isset($errors['role'])): ?><p class="text-xs text-osing-600 font-semibold mt-1"><?= e($errors['role']) ?></p><?php endif; ?>
        </div>

        <div data-store-field>
          <label for="f-store" class="block text-xs font-bold text-espresso-900 mb-1">Nama Toko (wajib untuk seller)</label>
          <input id="f-store" type="text" name="store_name" value="<?= old('store_name', $editing['store_name'] ?? '') ?>" placeholder="Contoh: Toko Oleh-Oleh Kemiren"
                 class="w-full px-4 py-2.5 rounded-2xl bg-white border border-cotton-300 text-sm text-espresso-900 focus:outline-none focus:ring-2 focus:ring-sogan-500" />
          <?php if (isset($errors['store_name'])): ?><p class="text-xs text-osing-600 font-semibold mt-1"><?= e($errors['store_name']) ?></p><?php endif; ?>
        </div>

        <div>
          <label for="f-password" class="block text-xs font-bold text-espresso-900 mb-1">
            <?= $editing ? 'Password Baru (opsional)' : 'Password <span class="text-osing-600">*</span>' ?>
          </label>
          <input id="f-password" type="password" name="password" autocomplete="new-password" placeholder="<?= $editing ? 'Isi hanya jika ingin ganti password' : 'Minimal 6 karakter' ?>"
                 class="w-full px-4 py-2.5 rounded-2xl bg-white border border-cotton-300 text-sm text-espresso-900 focus:outline-none focus:ring-2 focus:ring-sogan-500" />
          <?php if (isset($errors['password'])): ?><p class="text-xs text-osing-600 font-semibold mt-1"><?= e($errors['password']) ?></p><?php endif; ?>
        </div>

        <div class="flex items-end">
          <label class="flex items-center gap-3 p-3.5 w-full rounded-2xl bg-sogan-100/70 border border-sogan-400/50 cursor-pointer select-none">
            <input type="checkbox" name="is_active" <?= (!isset($editing['is_active']) || (int) $editing['is_active'] === 1) ? 'checked' : '' ?> class="w-4 h-4 rounded accent-espresso-900 cursor-pointer" />
            <span class="text-sm font-bold text-espresso-900">Akun Aktif (dapat login)</span>
          </label>
        </div>
      </div>

      <div class="pt-2 flex items-center justify-end gap-3 border-t border-cotton-300">
        <a href="<?= e(url('admin/users')) ?>" class="px-5 py-2.5 rounded-2xl bg-cotton-100 hover:bg-cotton-200 text-espresso-900 border border-cotton-300 text-sm font-bold transition">Batal</a>
        <button type="submit" class="px-6 py-2.5 rounded-2xl bg-espresso-900 hover:bg-espresso-800 text-cotton-50 text-sm font-bold transition shadow-md border border-sogan-500/40">
          <?= $editing ? 'Simpan Perubahan' : 'Tambah User' ?>
        </button>
      </div>
    </form>
  </div>
</section>
