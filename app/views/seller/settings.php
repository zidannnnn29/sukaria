<section class="space-y-6">
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-cotton-50 p-5 sm:p-6 rounded-3xl border border-cotton-300 shadow-sm">
    <div>
      <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-bold bg-sogan-100 text-sogan-700 border border-sogan-400/40 mb-1.5">
        ❖ Pengaturan Toko • WhatsApp
      </span>
      <h2 class="font-serif text-xl sm:text-2xl font-bold text-espresso-900">Pengaturan Nomor WhatsApp Toko</h2>
      <p class="text-xs sm:text-sm text-espresso-700 mt-0.5">Seluruh tombol "Pesan via WhatsApp" di katalog pembeli diarahkan ke nomor ini.</p>
    </div>
  </div>

  <div class="bg-cotton-50 rounded-3xl border border-cotton-300 shadow-sm overflow-hidden">
    <div class="h-1.5 w-full bg-gradient-to-r from-[#25D366] via-coffeeleaf-600 to-sogan-500" aria-hidden="true"></div>
    <div class="p-5 sm:p-6">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Status Nomor Aktif -->
        <div class="lg:col-span-5 space-y-3.5">
          <div class="flex items-start gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-[#25D366]/15 border border-[#25D366]/40 text-coffeeleaf-700 flex items-center justify-center shrink-0 shadow-sm">
              <svg class="w-6 h-6 text-[#1EBE5D]" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M12.031 2c-5.516 0-9.969 4.453-9.969 9.969 0 1.758.459 3.473 1.331 4.986L2 22l5.188-1.36a9.927 9.927 0 0 0 4.843 1.258h.004c5.515 0 9.968-4.453 9.968-9.969A9.932 9.932 0 0 0 19.08 4.92 9.914 9.914 0 0 0 12.031 2Zm5.816 14.273c-.245.691-1.425 1.321-1.976 1.404-.507.076-1.148.108-1.852-.116-.427-.135-.975-.316-1.677-.619-2.952-1.274-4.88-4.246-5.027-4.442-.147-.197-1.2-1.596-1.2-3.045 0-1.449.76-2.161 1.03-2.456.27-.295.59-.369.786-.369.196 0 .393.002.565.01.181.009.424-.069.663.506.245.59.835 2.038.909 2.186.074.148.123.32.025.517-.098.197-.147.32-.295.492-.147.172-.31.384-.442.516-.148.148-.301.308-.129.603.172.295.764 1.26 1.641 2.041 1.128 1.005 2.079 1.317 2.374 1.464.295.148.467.123.639-.074.172-.197.737-.86.934-1.155.197-.295.393-.246.663-.148.27.098 1.719.811 2.014.958.295.148.491.221.565.344.074.123.074.713-.171 1.404Z"/>
              </svg>
            </div>
            <div>
              <h3 class="font-serif text-base sm:text-lg font-bold text-espresso-900">Nomor WhatsApp Aktif</h3>
              <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#25D366]/15 text-coffeeleaf-700 border border-[#25D366]/35 mt-1">
                <span class="w-1.5 h-1.5 rounded-full bg-[#25D366]"></span> Tujuan Pesanan
              </span>
            </div>
          </div>

          <div class="bg-cotton-100/90 rounded-2xl p-3.5 border border-cotton-300 space-y-2">
            <div class="flex items-center justify-between gap-2">
              <span class="text-[11px] font-bold uppercase tracking-wider text-espresso-700/80">Nomor Terdaftar</span>
              <span id="active-wa-formatted" class="font-serif text-sm sm:text-base font-bold text-espresso-900"><?= e($waDisplay) ?></span>
            </div>
            <div class="flex items-center justify-between gap-2 pt-2 border-t border-cotton-300/70 text-xs">
              <span class="text-espresso-700 shrink-0">Pratinjau Tautan:</span>
              <code id="active-wa-link-preview" class="px-2 py-0.5 rounded-lg bg-cotton-50 border border-cotton-300 text-coffeeleaf-700 font-mono text-[11px] truncate">https://wa.me/<?= e($waNumber) ?></code>
            </div>
          </div>

          <a id="btn-test-wa" href="https://wa.me/<?= e($waNumber) ?>?text=<?= rawurlencode('Halo ' . ($user['store_name'] ?: 'Sukaria') . '! Saya menguji tautan WhatsApp pemesanan.') ?>"
             target="_blank" rel="noopener noreferrer"
             class="inline-flex px-4 py-2.5 rounded-2xl bg-[#25D366]/15 hover:bg-[#25D366]/25 text-coffeeleaf-700 border border-[#25D366]/45 text-xs sm:text-sm font-bold transition items-center gap-2">
            <svg class="w-4 h-4 text-[#1EBE5D] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
            Uji Chat WA
          </a>
        </div>

        <!-- Form -->
        <form method="post" action="<?= e(url('seller/settings/save')) ?>" novalidate class="lg:col-span-7 bg-cotton-100/60 rounded-2xl p-4 sm:p-5 border border-cotton-300 space-y-3.5">
          <?= csrf_field() ?>
          <div>
            <div class="flex flex-wrap items-center justify-between gap-1 mb-1.5">
              <label for="input-wa-number" class="block text-xs font-bold text-espresso-900">Nomor WhatsApp Admin Toko <span class="text-osing-600">*</span></label>
              <span class="text-[11px] text-espresso-700">Format <code class="font-mono text-espresso-900">08...</code>, <code class="font-mono text-espresso-900">62...</code>, atau <code class="font-mono text-espresso-900">+62...</code></span>
            </div>
            <div class="relative flex items-center">
              <span class="absolute left-3.5 inline-flex items-center gap-1.5 text-xs font-extrabold text-coffeeleaf-700 pointer-events-none">
                <span>WA</span><span class="text-cotton-400">|</span>
              </span>
              <input id="input-wa-number" name="whatsapp_number" type="tel" inputmode="tel" autocomplete="tel" value="<?= e($waNumber) ?>"
                     placeholder="Contoh: 0812-3456-7890 atau 6281234567890"
                     class="w-full pl-14 pr-4 py-2.5 rounded-2xl bg-white border border-cotton-300 text-sm font-semibold text-espresso-900 placeholder:font-normal placeholder:text-espresso-700/50 focus:outline-none focus:ring-2 focus:ring-[#25D366] focus:border-[#25D366] transition" />
            </div>
            <p id="hint-wa-normalized" class="text-[11px] text-espresso-700 mt-1.5">
              Format tersimpan: <span class="font-mono font-bold text-coffeeleaf-700"><?= e($waNumber) ?></span> (<?= e($waDisplay) ?>)
            </p>
          </div>

          <div class="flex flex-wrap items-center gap-2.5 pt-1">
            <button type="submit" class="px-4 py-2.5 rounded-2xl bg-coffeeleaf-600 hover:bg-coffeeleaf-700 text-white text-xs sm:text-sm font-bold transition shadow-sm flex items-center gap-2">
              <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><polyline points="20 6 9 17 4 12"/></svg>
              Simpan Nomor WA
            </button>
            <button type="button" id="btn-preview-wa" class="px-4 py-2.5 rounded-2xl bg-cotton-100 hover:bg-cotton-200 text-espresso-900 border border-cotton-300 text-xs sm:text-sm font-bold transition">
              Pratinjau Normalisasi
            </button>
          </div>
        </form>

        <!-- Reset default -->
        <form method="post" action="<?= e(url('seller/settings/reset')) ?>" data-confirm-title="Reset Nomor WhatsApp?" data-confirm-name="Kembalikan ke nomor default <?= e(SUKARIA_DEFAULT_WA) ?>?" class="lg:col-span-12 lg:-mt-3">
          <?= csrf_field() ?>
          <button type="submit" class="px-4 py-2.5 rounded-2xl bg-cotton-100 hover:bg-cotton-200 text-espresso-800 border border-cotton-300 text-xs sm:text-sm font-bold transition">
            Reset ke Nomor Default
          </button>
        </form>
      </div>
    </div>
  </div>
</section>
