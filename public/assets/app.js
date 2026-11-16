/* ==================== SUKARIA — Aplikasi Frontend ==================== */
'use strict';

/* ---------------------------------------------------------------------
 * UTILITAS
 * ------------------------------------------------------------------- */
function escapeHTML(value) {
  return String(value ?? '')
    .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
}

function formatRupiah(amount) {
  return 'Rp ' + new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 }).format(Number(amount) || 0);
}

/* Toast notification */
function showToast(message, type = 'success') {
  const container = document.getElementById('toast-container');
  if (!container) return;
  const isError = type === 'error';
  const toast = document.createElement('div');
  toast.className = `pointer-events-auto flex items-center gap-3 px-4 py-3.5 rounded-2xl shadow-xl border text-xs sm:text-sm font-semibold fade-in ${
    isError ? 'bg-osing-700 text-white border-osing-500' : 'bg-espresso-900 text-cotton-50 border-sogan-400/50'}`;
  const icon = isError
    ? '<svg class="w-4 h-4 text-sogan-200 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>'
    : '<svg class="w-4 h-4 text-sogan-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>';
  toast.innerHTML = `${icon}<span class="flex-1 leading-snug">${escapeHTML(message)}</span>`;
  container.appendChild(toast);
  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transform = 'translateY(6px)';
    toast.style.transition = 'all 0.2s ease';
    setTimeout(() => toast.remove(), 220);
  }, 3500);
}

/* ---------------------------------------------------------------------
 * FLASH DARI SERVER (PHP)
 * ------------------------------------------------------------------- */
(function initFlash() {
  const flashes = window.SUKARIA_FLASH || [];
  flashes.forEach((f) => showToast(f.message, f.type));
})();

/* ---------------------------------------------------------------------
 * MODAL KONFIRMASI (untuk semua form dengan data-confirm-title)
 * ------------------------------------------------------------------- */
(function initConfirmModals() {
  let pendingForm = null;

  function buildModal() {
    const root = document.createElement('div');
    root.id = 'confirm-modal';
    root.setAttribute('role', 'dialog');
    root.setAttribute('aria-modal', 'true');
    root.className = 'hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-espresso-900/65 backdrop-blur';
    root.innerHTML = `
      <div class="bg-cotton-50 w-full max-w-md rounded-3xl border border-cotton-300 shadow-2xl overflow-hidden fade-in">
        <div class="batik-border-strip" aria-hidden="true"></div>
        <div class="p-6 space-y-4">
          <div class="flex items-start gap-3.5">
            <div class="w-11 h-11 rounded-2xl bg-osing-100 text-osing-600 flex items-center justify-center shrink-0">
              <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
              </svg>
            </div>
            <div>
              <h3 id="confirm-modal-title" class="font-serif text-base sm:text-lg font-bold text-espresso-900">Konfirmasi Tindakan</h3>
              <p id="confirm-modal-message" class="text-xs sm:text-sm text-espresso-700 mt-1 leading-relaxed"></p>
            </div>
          </div>
          <div class="flex items-center justify-end gap-3 pt-2">
            <button type="button" id="confirm-modal-cancel" class="px-4 py-2.5 rounded-2xl bg-cotton-100 hover:bg-cotton-200 text-espresso-900 border border-cotton-300 text-xs sm:text-sm font-bold transition">Batal</button>
            <button type="button" id="confirm-modal-ok" class="px-5 py-2.5 rounded-2xl bg-osing-600 hover:bg-osing-700 text-white text-xs sm:text-sm font-bold transition shadow-sm">Ya, Lanjutkan</button>
          </div>
        </div>
      </div>`;
    document.body.appendChild(root);
    return root;
  }

  function openConfirm(form) {
    const modal = document.getElementById('confirm-modal') || buildModal();
    const title = form.getAttribute('data-confirm-title') || 'Konfirmasi Tindakan';
    const name = form.getAttribute('data-confirm-name') || 'item ini';
    modal.querySelector('#confirm-modal-title').textContent = title;
    modal.querySelector('#confirm-modal-message').textContent =
      `Anda akan melanjutkan tindakan pada "${name}". Tindakan ini tidak dapat dibatalkan.`;
    pendingForm = form;
    modal.classList.remove('hidden');
    modal.querySelector('#confirm-modal-cancel').focus();
  }

  document.addEventListener('click', (e) => {
    const modal = document.getElementById('confirm-modal');
    if (!modal) return;
    if (e.target.id === 'confirm-modal-cancel' || e.target.id === 'confirm-modal') {
      modal.classList.add('hidden');
      pendingForm = null;
    } else if (e.target.id === 'confirm-modal-ok') {
      modal.classList.add('hidden');
      if (pendingForm) pendingForm.submit();
      pendingForm = null;
    }
  });

  document.addEventListener('keydown', (e) => {
    if (e.key !== 'Escape') return;
    const modal = document.getElementById('confirm-modal');
    if (modal && !modal.classList.contains('hidden')) {
      modal.classList.add('hidden');
      pendingForm = null;
    }
  });

  document.addEventListener('submit', (e) => {
    const form = e.target;
    if (form.hasAttribute('data-confirm-title') && !form.dataset.confirmed) {
      e.preventDefault();
      openConfirm(form);
    }
  }, true);
})();

/* ---------------------------------------------------------------------
 * KATALOG PEMBELI (render sisi klien: pencarian, filter, sorting)
 * ------------------------------------------------------------------- */
(function initCatalog() {
  const data = window.SUKARIA_CATALOG;
  if (!data) return;

  const items = data.items;
  const state = { search: '', category: 'ALL', sort: 'featured' };

  const grid = document.getElementById('catalog-grid');
  const emptyState = document.getElementById('catalog-empty');
  const resultInfo = document.getElementById('catalog-result-info');
  const pillsBox = document.getElementById('catalog-category-pills');
  const resetBtn = document.getElementById('catalog-reset-filters');
  const clearBtn = document.getElementById('catalog-clear-search');
  const searchInput = document.getElementById('catalog-search');
  const sortSelect = document.getElementById('catalog-sort');

  function formatWaDisplay(digits) {
    if (!String(digits).startsWith('62')) return '+' + digits;
    const rest = String(digits).slice(2);
    if (rest.length <= 3) return '+62 ' + rest;
    if (rest.length <= 7) return `+62 ${rest.slice(0, 3)}-${rest.slice(3)}`;
    return `+62 ${rest.slice(0, 3)}-${rest.slice(3, 7)}-${rest.slice(7)}`;
  }

  function buildWaURL(item) {
    const msg = [
      'Halo Sukaria Banyuwangi! Saya ingin memesan oleh-oleh:',
      `• Produk: ${item.nama}`,
      `• Kategori: ${item.kategori}`,
      `• Toko: ${item.toko}`,
      `• Harga: ${formatRupiah(item.harga)}`,
      '',
      'Mohon konfirmasi ketersediaan stok dan informasi pengirimannya. Terima kasih!'
    ].join('\n');
    return `https://wa.me/${item.waNumber}?text=${encodeURIComponent(msg)}`;
  }

  function renderPills() {
    const counts = { ALL: items.length };
    items.forEach((it) => { counts[it.kategori] = (counts[it.kategori] || 0) + 1; });
    const cats = ['ALL', ...data.categories.map((c) => c.name)];
    pillsBox.innerHTML = cats.map((cat) => {
      const isAll = cat === 'ALL';
      const selected = state.category === cat;
      const label = isAll ? 'Semua Produk' : cat;
      const count = counts[cat] || 0;
      return `
        <button type="button" data-category-pill="${escapeHTML(cat)}" aria-pressed="${selected}"
          class="shrink-0 inline-flex items-center gap-2 px-3.5 py-2 rounded-full text-xs font-bold border transition-all duration-150 ${
            selected ? 'bg-espresso-900 text-cotton-50 border-espresso-900' : 'bg-cotton-100 hover:bg-cotton-200 text-espresso-800 border-cotton-300'}">
          <span>${escapeHTML(label)}</span>
          <span class="px-1.5 py-0.5 rounded-full text-[10px] font-extrabold ${selected ? 'bg-sogan-500 text-white' : 'bg-cotton-200 text-espresso-800'}">${count}</span>
        </button>`;
    }).join('');
  }

  function filtered() {
    const keyword = state.search.trim().toLowerCase();
    const list = items
      .map((item, idx) => ({ item, idx }))
      .filter(({ item }) => {
        const matchCat = state.category === 'ALL' || item.kategori === state.category;
        const matchText = !keyword ||
          item.nama.toLowerCase().includes(keyword) ||
          item.deskripsi.toLowerCase().includes(keyword) ||
          (item.toko || '').toLowerCase().includes(keyword);
        return matchCat && matchText;
      });

    list.sort((a, b) => {
      const A = a.item, B = b.item;
      switch (state.sort) {
        case 'price-asc':  return A.harga - B.harga || a.idx - b.idx;
        case 'price-desc': return B.harga - A.harga || a.idx - b.idx;
        case 'name-asc':   return A.nama.localeCompare(B.nama, 'id');
        case 'stock-desc': return B.stok - A.stok || a.idx - b.idx;
        default:
          if (A.unggulan !== B.unggulan) return A.unggulan ? -1 : 1;
          return a.idx - b.idx;
      }
    });
    return list.map((entry) => entry.item);
  }

  function renderGrid() {
    const list = filtered();
    const activeCat = state.category === 'ALL' ? 'semua kategori' : `"${state.category}"`;
    resultInfo.textContent = `Menampilkan ${list.length} dari ${items.length} produk (${activeCat})`;

    const isFiltering = state.search.trim() !== '' || state.category !== 'ALL';
    resetBtn.classList.toggle('hidden', !isFiltering);
    clearBtn.classList.toggle('hidden', state.search.trim() === '');

    if (list.length === 0) {
      grid.innerHTML = '';
      grid.classList.add('hidden');
      emptyState.classList.remove('hidden');
      return;
    }
    emptyState.classList.add('hidden');
    grid.classList.remove('hidden');

    grid.innerHTML = list.map((item) => {
      const out = item.stok <= 0;
      const low = item.stok > 0 && item.stok <= 5;

      const featuredBadge = item.unggulan
        ? `<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-espresso-900/90 text-sogan-200 border border-sogan-400/60 shadow-sm backdrop-blur"><span class="text-sogan-400">★</span> Unggulan</span>`
        : '';

      const stockBadge = out
        ? `<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-osing-100 text-osing-700 border border-osing-500/30"><span class="w-1.5 h-1.5 rounded-full bg-osing-600"></span>Stok Habis</span>`
        : low
          ? `<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-sogan-100 text-sogan-700 border border-sogan-400/50"><span class="w-1.5 h-1.5 rounded-full bg-sogan-500"></span>Stok Menipis (${item.stok})</span>`
          : `<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-coffeeleaf-100 text-coffeeleaf-700 border border-coffeeleaf-600/25"><span class="w-1.5 h-1.5 rounded-full bg-coffeeleaf-600"></span>Tersedia (${item.stok})</span>`;

      const waAction = out
        ? `<div class="grid grid-cols-1 gap-2">
             <button type="button" disabled aria-disabled="true" class="w-full py-2.5 px-4 rounded-2xl bg-cotton-200 text-espresso-700/55 border border-cotton-300 text-xs sm:text-sm font-bold flex items-center justify-center gap-2 cursor-not-allowed">
              <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
              <span>Stok Habis — Tidak Tersedia</span></button>
           </div>`
        : `<div class="grid grid-cols-2 gap-2">
             <a href="${escapeHTML(buildWaURL(item))}" target="_blank" rel="noopener noreferrer" title="Pesan langsung ke toko via WhatsApp"
              class="w-full py-2.5 px-3 rounded-2xl bg-coffeeleaf-600 hover:bg-coffeeleaf-700 text-white text-xs sm:text-sm font-bold flex items-center justify-center gap-1.5 transition shadow-sm">
              <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12.031 2c-5.516 0-9.969 4.453-9.969 9.969 0 1.758.459 3.473 1.331 4.986L2 22l5.188-1.36a9.927 9.927 0 0 0 4.843 1.258h.004c5.515 0 9.968-4.453 9.968-9.969A9.932 9.932 0 0 0 19.08 4.92 9.914 9.914 0 0 0 12.031 2Zm5.816 14.273c-.245.691-1.425 1.321-1.976 1.404-.507.076-1.148.108-1.852-.116-.427-.135-.975-.316-1.677-.619-2.952-1.274-4.88-4.246-5.027-4.442-.147-.197-1.2-1.596-1.2-3.045 0-1.449.76-2.161 1.03-2.456.27-.295.59-.369.786-.369.196 0 .393.002.565.01.181.009.424-.069.663.506.245.59.835 2.038.909 2.186.074.148.123.32.025.517-.098.197-.147.32-.295.492-.147.172-.31.384-.442.516-.148.148-.301.308-.129.603.172.295.764 1.26 1.641 2.041 1.128 1.005 2.079 1.317 2.374 1.464.295.148.467.123.639-.074.172-.197.737-.86.934-1.155.197-.295.393-.246.663-.148.27.098 1.719.811 2.014.958.295.148.491.221.565.344.074.123.074.713-.171 1.404Z"/></svg>
              <span class="whitespace-nowrap">Pesan WA</span></a>
             <button type="button" data-cart-add="${item.id}" title="Masukkan ke keranjang, bayar via transfer bank"
              class="w-full py-2.5 px-3 rounded-2xl bg-osing-600 hover:bg-osing-700 text-white text-xs sm:text-sm font-bold flex items-center justify-center gap-1.5 transition shadow-sm">
              <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
              <span class="whitespace-nowrap">+ Keranjang</span></button>
           </div>`;

      return `
        <article class="group bg-cotton-50 rounded-3xl border border-cotton-300 shadow-sm hover:shadow-md hover:border-sogan-400 transition-all duration-200 flex flex-col overflow-hidden ${out ? 'opacity-85' : ''}">
          <div class="relative aspect-[16/10] bg-cotton-200 overflow-hidden border-b border-cotton-300/70">
            <img src="${escapeHTML(item.gambar)}" alt="${escapeHTML(item.nama)}" loading="lazy"
                 class="w-full h-full object-cover group-hover:scale-[1.03] transition-transform duration-300 ${out ? 'grayscale-[45%]' : ''}" />
            <div class="absolute top-3 left-3 flex flex-wrap gap-1.5">${featuredBadge}</div>
            <div class="absolute bottom-2 right-2 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-espresso-900/80 text-cotton-50 backdrop-blur">🏬 ${escapeHTML(item.toko)}</div>
          </div>
          <div class="p-4 sm:p-5 flex-1 flex flex-col justify-between gap-4">
            <div class="space-y-2">
              <div class="flex flex-wrap items-center justify-between gap-1.5">
                <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-cotton-200 text-espresso-800 border border-cotton-300">${escapeHTML(item.kategori)}</span>
                ${stockBadge}
              </div>
              <h3 class="font-serif text-base sm:text-lg font-bold text-espresso-900 leading-snug">${escapeHTML(item.nama)}</h3>
              <p class="text-xs sm:text-sm text-espresso-700 line-clamp-2 leading-relaxed">${escapeHTML(item.deskripsi || 'Oleh-oleh khas Banyuwangi olahan UMKM lokal.')}</p>
            </div>
            <div class="pt-3 border-t border-cotton-200 space-y-3">
              <div class="flex items-baseline justify-between">
                <span class="text-xs font-semibold text-espresso-700">Harga</span>
                <span class="font-serif text-lg sm:text-xl font-bold text-espresso-900">${formatRupiah(item.harga)}</span>
              </div>
              ${waAction}
            </div>
          </div>
        </article>`;
    }).join('');
  }

  searchInput.addEventListener('input', (e) => { state.search = e.target.value; renderGrid(); });
  clearBtn.addEventListener('click', () => { state.search = ''; searchInput.value = ''; renderGrid(); searchInput.focus(); });
  sortSelect.addEventListener('change', (e) => { state.sort = e.target.value; renderGrid(); });
  pillsBox.addEventListener('click', (e) => {
    const pill = e.target.closest('[data-category-pill]');
    if (!pill) return;
    state.category = pill.getAttribute('data-category-pill');
    renderGrid();
  });
  const resetFilters = () => {
    state.search = ''; state.category = 'ALL'; searchInput.value = ''; renderGrid();
  };
  resetBtn.addEventListener('click', resetFilters);
  document.getElementById('catalog-empty-reset-btn').addEventListener('click', resetFilters);

  // Tambah ke keranjang (fetch POST; server memvalidasi stok)
  grid.addEventListener('click', async (e) => {
    const btn = e.target.closest('[data-cart-add]');
    if (!btn || btn.disabled) return;
    const cartCfg = window.SUKARIA_CART_ADD;
    if (!cartCfg) return;

    btn.disabled = true;
    btn.classList.add('opacity-60', 'cursor-wait');
    try {
      const body = new URLSearchParams({ _csrf: cartCfg.csrf, product_id: btn.getAttribute('data-cart-add'), qty: '1' });
      const resp = await fetch(cartCfg.url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'fetch' },
        body,
        redirect: 'manual',
      });
      if (resp.redirected || resp.type === 'opaqueredirect' || (resp.status >= 300 && resp.status < 400)) {
        showToast('Produk ditambahkan ke keranjang 🛒');
        updateCartBadge();
      } else if (resp.status === 419) {
        showToast('Sesi kedaluwarsa — muat ulang halaman.', 'error');
      } else {
        showToast('Gagal menambah ke keranjang. Coba lagi.', 'error');
      }
    } catch {
      showToast('Koneksi bermasalah. Coba lagi.', 'error');
    } finally {
      btn.disabled = false;
      btn.classList.remove('opacity-60', 'cursor-wait');
    }
  });

  function updateCartBadge() {
    document.querySelectorAll('a[href*="r=cart"] span.rounded-full').forEach((badge) => {
      const current = parseInt(badge.textContent, 10) || 0;
      badge.textContent = String(current + 1);
      badge.className = 'inline-flex items-center justify-center min-w-5 h-5 px-1 rounded-full text-[10px] font-extrabold bg-osing-600 text-white';
    });
  }

  renderPills();
  renderGrid();
})();

/* ---------------------------------------------------------------------
 * FORM PRODUK: Kompresi gambar sisi klien (canvas → JPEG base64)
 * ------------------------------------------------------------------- */
(function initProductForm() {
  const form = document.getElementById('product-form');
  if (!form) return;

  const dropzone = document.getElementById('image-dropzone');
  const fileInput = document.getElementById('field-imageFile');
  const preview = document.getElementById('form-image-preview');
  const badge = document.getElementById('image-source-badge');
  const info = document.getElementById('image-compression-info');
  const resetBtn = document.getElementById('btn-reset-image');
  const hiddenData = document.getElementById('image-data');
  const hiddenRemove = document.getElementById('remove-image');

  const BADGE_CUSTOM = '<span>📷</span> Foto Upload Kustom';
  const BADGE_BATIK  = '<span>❖</span> Ilustrasi Batik Otomatis';
  const CLASS_CUSTOM = 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-coffeeleaf-100 text-coffeeleaf-700 border border-coffeeleaf-600/30';
  const CLASS_BATIK  = 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-sogan-100 text-sogan-700 border border-sogan-400/50';

  let hasUploadedImage = hiddenData.value !== '' || (badge && badge.textContent.includes('Foto'));

  dropzone.addEventListener('click', () => fileInput.click());
  dropzone.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); fileInput.click(); }
  });
  dropzone.addEventListener('dragover', (e) => { e.preventDefault(); dropzone.classList.add('border-sogan-500', 'bg-sogan-100/50'); });
  dropzone.addEventListener('dragleave', (e) => { e.preventDefault(); dropzone.classList.remove('border-sogan-500', 'bg-sogan-100/50'); });
  dropzone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropzone.classList.remove('border-sogan-500', 'bg-sogan-100/50');
    const file = e.dataTransfer?.files?.[0];
    if (file) handleFile(file);
  });
  fileInput.addEventListener('change', (e) => {
    const file = e.target.files?.[0];
    if (file) handleFile(file);
  });

  function handleFile(file) {
    const allowed = ['image/png', 'image/jpeg', 'image/webp'];
    if (!allowed.includes(file.type)) { showToast('Format gambar harus PNG, JPG, atau WEBP.', 'error'); return; }
    if (file.size > 15 * 1024 * 1024) { showToast('Ukuran file mentah maksimal 15 MB.', 'error'); return; }

    const reader = new FileReader();
    reader.onerror = () => showToast('Gagal membaca file gambar.', 'error');
    reader.onload = (event) => {
      const img = new Image();
      img.onerror = () => showToast('File bukan gambar yang valid.', 'error');
      img.onload = () => {
        const MAX = 640;
        let w = img.width, h = img.height;
        if (w > MAX || h > MAX) {
          if (w >= h) { h = Math.round((h * MAX) / w); w = MAX; }
          else { w = Math.round((w * MAX) / h); h = MAX; }
        }
        const canvas = document.createElement('canvas');
        canvas.width = w; canvas.height = h;
        const ctx = canvas.getContext('2d');
        ctx.fillStyle = '#FFFDF9';
        ctx.fillRect(0, 0, w, h);
        ctx.drawImage(img, 0, 0, w, h);
        const dataUrl = canvas.toDataURL('image/jpeg', 0.8);
        const sizeKB = Math.max(1, Math.round((dataUrl.length * 3 / 4) / 1024));

        hiddenData.value = dataUrl;
        hiddenRemove.value = '';
        preview.src = dataUrl;
        badge.innerHTML = BADGE_CUSTOM;
        badge.className = CLASS_CUSTOM;
        info.textContent = `Terkompresi: ${w}×${h}px • ${sizeKB} KB`;
        resetBtn.classList.remove('hidden');
        hasUploadedImage = true;
        showToast(`Foto berhasil dikompresi (${sizeKB} KB).`);
      };
      img.src = event.target.result;
    };
    reader.readAsDataURL(file);
  }

  resetBtn.addEventListener('click', () => {
    hiddenData.value = '';
    hiddenRemove.value = '1';
    fileInput.value = '';
    badge.innerHTML = BADGE_BATIK;
    badge.className = CLASS_BATIK;
    info.textContent = 'Akan memakai ilustrasi Batik otomatis setelah disimpan';
    resetBtn.classList.add('hidden');
    hasUploadedImage = false;
    showToast('Gambar dikembalikan ke ilustrasi Batik otomatis.');
  });
})();

/* ---------------------------------------------------------------------
 * FORM USER ADMIN: tampilkan/sembunyikan kolom toko sesuai role
 * ------------------------------------------------------------------- */
(function initUserForm() {
  const roleSelect = document.querySelector('[data-toggle-store]');
  if (!roleSelect) return;
  const storeField = document.querySelector('[data-store-field]');
  function sync() {
    const isSeller = roleSelect.value === 'seller';
    storeField.style.display = isSeller ? '' : 'none';
    storeField.querySelector('input').disabled = !isSeller;
  }
  roleSelect.addEventListener('change', sync);
  sync();
})();

/* ---------------------------------------------------------------------
 * PENGATURAN WA SELLER: pratinjau normalisasi real-time
 * ------------------------------------------------------------------- */
(function initWaSettings() {
  const input = document.getElementById('input-wa-number');
  if (!input) return;
  const hint = document.getElementById('hint-wa-normalized');
  const previewBtn = document.getElementById('btn-preview-wa');
  const testLink = document.getElementById('btn-test-wa');

  function normalize(raw) {
    let digits = String(raw || '').replace(/\D/g, '');
    if (!digits) return '';
    if (digits.startsWith('0')) digits = '62' + digits.slice(1);
    else if (digits.startsWith('8')) digits = '62' + digits;
    return digits;
  }

  function display(digits) {
    if (!digits.startsWith('62')) return '+' + digits;
    const rest = digits.slice(2);
    if (rest.length <= 3) return '+62 ' + rest;
    if (rest.length <= 7) return `+62 ${rest.slice(0, 3)}-${rest.slice(3)}`;
    return `+62 ${rest.slice(0, 3)}-${rest.slice(3, 7)}-${rest.slice(7)}`;
  }

  input.addEventListener('input', () => {
    const normalized = normalize(input.value);
    if (!normalized) {
      hint.innerHTML = 'Awalan <span class="font-bold">08</span> otomatis dikonversi menjadi <span class="font-bold">628</span> (10–15 digit) saat disimpan.';
    } else {
      hint.innerHTML = `Format tersimpan: <span class="font-mono font-bold text-coffeeleaf-700">${escapeHTML(normalized)}</span> (${escapeHTML(display(normalized))})`;
    }
  });

  if (previewBtn) {
    previewBtn.addEventListener('click', () => {
      const normalized = normalize(input.value);
      if (!normalized) { showToast('Isi nomor terlebih dahulu.', 'error'); return; }
      showToast(`Akan tersimpan: ${normalized} (${display(normalized)})`);
    });
  }
})();
