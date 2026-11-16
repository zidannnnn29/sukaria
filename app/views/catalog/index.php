<?php
/**
 * Katalog Pembeli — pencarian, filter kategori, sorting & pesan via WhatsApp.
 * Data dikirim sebagai JSON; interaksi ditangani assets/app.js.
 */
?>
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-6">

  <!-- Hero Banner Batik Kopi Pecah -->
  <div class="bg-batik-hero rounded-3xl p-6 sm:p-8 lg:p-9 text-cotton-50 shadow-xl relative overflow-hidden border border-sogan-500/40">
    <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
      <div class="max-w-2xl space-y-3">
        <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full text-xs font-bold bg-sogan-500/20 text-sogan-200 border border-sogan-400/40">
          <span>❖</span> Warisan Rasa Bumi Blambangan • Motif Batik Kopi Pecah
        </div>
        <h2 class="font-serif text-2xl sm:text-3xl lg:text-[34px] font-bold tracking-tight leading-snug text-cotton-50">
          Sajian Oleh-Oleh Autentik Dapur UMKM Banyuwangi
        </h2>
        <p class="text-sm sm:text-base text-cotton-200/90 leading-relaxed">
          Pilih buah tangan favorit Anda dari berbagai toko UMKM lokal, lalu pesan langsung melalui WhatsApp. Tanpa akun, tanpa ribet.
        </p>
      </div>
      <div class="flex flex-wrap sm:flex-nowrap lg:flex-col gap-3 shrink-0">
        <div class="flex items-center gap-3 bg-espresso-800/80 backdrop-blur px-4 py-3 rounded-2xl border border-sogan-400/30">
          <span class="w-2.5 h-2.5 rounded-full bg-sogan-400 shrink-0"></span>
          <div>
            <p class="text-xs font-bold text-cotton-50">Multi Toko UMKM</p>
            <p class="text-[11px] text-cotton-200/80">Produk dikelola seller terverifikasi</p>
          </div>
        </div>
        <div class="flex items-center gap-3 bg-espresso-800/80 backdrop-blur px-4 py-3 rounded-2xl border border-sogan-400/30">
          <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 shrink-0"></span>
          <div>
            <p class="text-xs font-bold text-cotton-50">Pesan Langsung via WhatsApp</p>
            <p class="text-[11px] text-cotton-200/80">Format pesanan otomatis siap kirim</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Panel Pencarian, Sorting & Pil Kategori -->
  <div class="bg-cotton-50 rounded-3xl p-4 sm:p-6 border border-cotton-300 shadow-sm space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-12 gap-3.5">
      <div class="md:col-span-8 relative">
        <label for="catalog-search" class="sr-only">Cari produk</label>
        <div class="relative flex items-center">
          <svg class="w-5 h-5 text-espresso-700/60 absolute left-3.5 pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8" /><path d="m21 21-4.3-4.3" />
          </svg>
          <input id="catalog-search" type="text" autocomplete="off"
                 placeholder="Cari nama oleh-oleh, bahan, atau deskripsi..."
                 class="w-full pl-11 pr-10 py-3 rounded-2xl bg-cotton-100/80 border border-cotton-300 text-sm text-espresso-900 placeholder:text-espresso-700/55 focus:outline-none focus:ring-2 focus:ring-sogan-500 focus:border-sogan-500 transition" />
          <button id="catalog-clear-search" type="button" aria-label="Hapus kata kunci"
                  class="hidden absolute right-3 p-1.5 rounded-full text-espresso-700 hover:text-espresso-900 hover:bg-cotton-200 transition">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M18 6 6 18" /><path d="m6 6 12 12" /></svg>
          </button>
        </div>
      </div>
      <div class="md:col-span-4">
        <label for="catalog-sort" class="sr-only">Urutkan produk</label>
        <div class="relative">
          <select id="catalog-sort" class="w-full px-4 py-3 rounded-2xl bg-cotton-100/80 border border-cotton-300 text-sm font-semibold text-espresso-900 focus:outline-none focus:ring-2 focus:ring-sogan-500 focus:border-sogan-500 transition appearance-none cursor-pointer">
            <option value="featured">Urutkan: Unggulan (Default)</option>
            <option value="price-asc">Harga Terendah</option>
            <option value="price-desc">Harga Tertinggi</option>
            <option value="name-asc">Nama (A–Z)</option>
            <option value="stock-desc">Stok Terbanyak</option>
          </select>
          <svg class="w-4 h-4 text-espresso-800 absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="m6 9 6 6 6-6" /></svg>
        </div>
      </div>
    </div>

    <div class="space-y-2">
      <div class="flex items-center justify-between">
        <span class="text-xs font-bold uppercase tracking-wider text-espresso-700/80">Filter Kategori Kuliner</span>
        <button id="catalog-reset-filters" type="button" class="hidden text-xs font-bold text-osing-600 hover:text-osing-700 underline underline-offset-2">Reset Filter</button>
      </div>
      <div id="catalog-category-pills" role="group" aria-label="Filter kategori produk" class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-1 sm:flex-wrap"></div>
    </div>

    <div class="batik-ornament-divider" aria-hidden="true"></div>

    <div class="flex flex-wrap items-center justify-between gap-2 text-xs sm:text-sm">
      <p id="catalog-result-info" class="text-espresso-800 font-semibold">Memuat produk...</p>
      <p class="text-xs text-espresso-700/75">Klik <span class="font-bold text-coffeeleaf-700">Pesan via WhatsApp</span> untuk terhubung ke toko</p>
    </div>
  </div>

  <!-- Grid Kartu Produk -->
  <div id="catalog-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5 sm:gap-6"></div>

  <!-- State Kosong -->
  <div id="catalog-empty" class="hidden bg-cotton-50 rounded-3xl border border-cotton-300 p-10 text-center space-y-3">
    <div class="w-14 h-14 rounded-2xl bg-sogan-100 text-sogan-700 border border-sogan-400/40 mx-auto flex items-center justify-center">
      <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8" /><path d="m21 21-4.3-4.3" /></svg>
    </div>
    <h3 class="font-serif text-lg font-bold text-espresso-900">Produk tidak ditemukan</h3>
    <p class="text-sm text-espresso-700 max-w-md mx-auto">Tidak ada produk yang cocok dengan kata kunci atau kategori yang dipilih.</p>
    <div class="pt-2">
      <button id="catalog-empty-reset-btn" type="button" class="px-5 py-2.5 rounded-2xl bg-espresso-900 hover:bg-espresso-800 text-cotton-50 text-sm font-bold transition shadow-sm">Tampilkan Semua Produk</button>
    </div>
  </div>
</section>

<script>
  window.SUKARIA_CATALOG = {
    items: <?= json_encode($items, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>,
    categories: <?= json_encode($categories, JSON_UNESCAPED_UNICODE) ?>
  };
</script>
