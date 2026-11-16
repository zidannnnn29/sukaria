<section class="space-y-6">
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-cotton-50 p-5 sm:p-6 rounded-3xl border border-cotton-300 shadow-sm">
    <div>
      <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-bold bg-sogan-100 text-sogan-700 border border-sogan-400/40 mb-1.5">
        ❖ Kelola Akun • Admin &amp; Seller
      </span>
      <h2 class="font-serif text-xl sm:text-2xl font-bold text-espresso-900">Manajemen User</h2>
      <p class="text-xs sm:text-sm text-espresso-700 mt-0.5">Tambah, ubah, aktifkan/nonaktifkan, dan hapus akun admin &amp; seller.</p>
    </div>
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
      <form method="get" action="index.php" class="relative sm:w-64">
        <input type="hidden" name="r" value="admin/users" />
        <input type="text" name="q" value="<?= e($search) ?>" placeholder="Cari nama / email / toko..."
               class="w-full pl-9 pr-4 py-2.5 rounded-xl bg-cotton-100 border border-cotton-300 text-xs sm:text-sm text-espresso-900 placeholder:text-espresso-700/55 focus:outline-none focus:ring-2 focus:ring-sogan-500" />
        <svg class="w-4 h-4 text-espresso-700/60 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
      </form>
      <a href="<?= e(url('admin/users/create')) ?>"
         class="px-4 py-2.5 rounded-xl bg-espresso-900 hover:bg-espresso-800 text-cotton-50 text-xs sm:text-sm font-bold transition flex items-center justify-center gap-2 border border-sogan-500/40">
        <svg class="w-4 h-4 text-sogan-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
        Tambah User
      </a>
    </div>
  </div>

  <div class="bg-cotton-50 rounded-3xl border border-cotton-300 shadow-sm overflow-hidden">
    <div class="hidden md:block overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-cotton-200/70 border-b border-cotton-300 text-[11px] font-extrabold uppercase tracking-wider text-espresso-800">
            <th class="py-3.5 px-5">User</th>
            <th class="py-3.5 px-4">Role</th>
            <th class="py-3.5 px-4">Status</th>
            <th class="py-3.5 px-4 text-center">Produk</th>
            <th class="py-3.5 px-4">Terdaftar</th>
            <th class="py-3.5 px-5 text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-cotton-200 text-sm">
          <?php if (count($users) === 0): ?>
            <tr><td colspan="6" class="py-10 text-center text-espresso-700">Tidak ada user yang cocok dengan pencarian.</td></tr>
          <?php else: foreach ($users as $u): $isSelf = (int) $u['id'] === (int) $user['id']; ?>
            <tr class="hover:bg-cotton-100/80 transition <?= (int) $u['is_active'] === 0 ? 'opacity-70' : '' ?>">
              <td class="py-3.5 px-5">
                <p class="font-bold text-espresso-900"><?= e($u['name']) ?><?= $isSelf ? ' <span class="ml-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-espresso-900 text-sogan-200">ANDA</span>' : '' ?></p>
                <p class="text-xs text-espresso-700"><?= e($u['email']) ?></p>
                <?php if ($u['role'] === 'seller' && $u['store_name']): ?>
                  <p class="text-xs text-coffeeleaf-700 font-semibold mt-0.5">🏬 <?= e($u['store_name']) ?></p>
                <?php endif; ?>
              </td>
              <td class="py-3.5 px-4">
                <span class="px-2.5 py-1 rounded-full text-xs font-bold border <?= $u['role'] === 'admin' ? 'bg-espresso-900 text-sogan-200 border-sogan-400/50' : 'bg-cotton-200 text-espresso-800 border-cotton-300' ?>">
                  <?= $u['role'] === 'admin' ? '★ Admin' : 'Seller' ?>
                </span>
              </td>
              <td class="py-3.5 px-4">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold border <?= (int) $u['is_active'] === 1 ? 'bg-coffeeleaf-100 text-coffeeleaf-700 border-coffeeleaf-600/25' : 'bg-osing-100 text-osing-700 border-osing-500/30' ?>">
                  <span class="w-1.5 h-1.5 rounded-full <?= (int) $u['is_active'] === 1 ? 'bg-coffeeleaf-600' : 'bg-osing-600' ?>"></span>
                  <?= (int) $u['is_active'] === 1 ? 'Aktif' : 'Nonaktif' ?>
                </span>
              </td>
              <td class="py-3.5 px-4 text-center font-serif font-bold text-espresso-900"><?= $u['role'] === 'seller' ? (int) $u['product_count'] : '—' ?></td>
              <td class="py-3.5 px-4 text-xs text-espresso-700 whitespace-nowrap"><?= e(date('d M Y', strtotime($u['created_at']))) ?></td>
              <td class="py-3.5 px-5 text-right">
                <div class="inline-flex items-center gap-2">
                  <a href="<?= e(url('admin/users/edit', ['id' => $u['id']])) ?>"
                     class="px-3 py-1.5 rounded-xl bg-cotton-100 hover:bg-cotton-200 text-espresso-900 border border-cotton-300 text-xs font-bold transition">Edit</a>
                  <?php if (!$isSelf): ?>
                    <form method="post" action="<?= e(url('admin/users/toggle')) ?>">
                      <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $u['id'] ?>" />
                      <button type="submit" class="px-3 py-1.5 rounded-xl bg-cotton-100 hover:bg-cotton-200 text-espresso-800 border border-cotton-300 text-xs font-bold transition">
                        <?= (int) $u['is_active'] === 1 ? 'Nonaktifkan' : 'Aktifkan' ?>
                      </button>
                    </form>
                    <form method="post" action="<?= e(url('admin/users/delete')) ?>"
                          data-confirm-title="Hapus User?" data-confirm-name="<?= e($u['name']) ?>">
                      <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $u['id'] ?>" />
                      <button type="submit" class="px-3 py-1.5 rounded-xl bg-osing-100 hover:bg-osing-500/20 text-osing-700 border border-osing-500/30 text-xs font-bold transition">Hapus</button>
                    </form>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Kartu mobile -->
    <div class="md:hidden divide-y divide-cotton-200">
      <?php foreach ($users as $u): $isSelf = (int) $u['id'] === (int) $user['id']; ?>
        <div class="p-4 space-y-3">
          <div class="flex items-start justify-between gap-2">
            <div class="min-w-0">
              <p class="font-bold text-sm text-espresso-900 truncate"><?= e($u['name']) ?></p>
              <p class="text-xs text-espresso-700 truncate"><?= e($u['email']) ?></p>
            </div>
            <span class="px-2 py-0.5 rounded-full text-[11px] font-bold shrink-0 <?= $u['role'] === 'admin' ? 'bg-espresso-900 text-sogan-200' : 'bg-cotton-200 text-espresso-800' ?>"><?= $u['role'] === 'admin' ? 'Admin' : 'Seller' ?></span>
          </div>
          <div class="flex items-center gap-2">
            <span class="px-2 py-0.5 rounded-full text-[11px] font-bold <?= (int) $u['is_active'] === 1 ? 'bg-coffeeleaf-100 text-coffeeleaf-700' : 'bg-osing-100 text-osing-700' ?>"><?= (int) $u['is_active'] === 1 ? 'Aktif' : 'Nonaktif' ?></span>
            <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-cotton-200 text-espresso-800"><?= (int) $u['product_count'] ?> produk</span>
          </div>
          <div class="flex items-center gap-2 pt-2 border-t border-cotton-200">
            <a href="<?= e(url('admin/users/edit', ['id' => $u['id']])) ?>" class="px-3 py-2 rounded-xl bg-cotton-100 text-espresso-900 border border-cotton-300 text-xs font-bold">Edit</a>
            <?php if (!$isSelf): ?>
              <form method="post" action="<?= e(url('admin/users/toggle')) ?>">
                <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $u['id'] ?>" />
                <button type="submit" class="px-3 py-2 rounded-xl bg-cotton-100 text-espresso-800 border border-cotton-300 text-xs font-bold"><?= (int) $u['is_active'] === 1 ? 'Nonaktifkan' : 'Aktifkan' ?></button>
              </form>
              <form method="post" action="<?= e(url('admin/users/delete')) ?>" data-confirm-title="Hapus User?" data-confirm-name="<?= e($u['name']) ?>">
                <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $u['id'] ?>" />
                <button type="submit" class="px-3 py-2 rounded-xl bg-osing-100 text-osing-700 border border-osing-500/30 text-xs font-bold">Hapus</button>
              </form>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
