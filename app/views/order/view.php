<?php
/**
 * Detail pesanan transfer: ringkasan item, instruksi transfer, unggah bukti, status.
 * Variabel: $order (null => tampilkan form cek), $accounts, $proofUrl
 */
$meta = $order ? Order::statusMeta($order['status']) : null;
?>
<section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-6">

  <div class="bg-batik-hero rounded-3xl p-6 sm:p-8 text-cotton-50 shadow-xl border border-sogan-500/40">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div class="space-y-1.5">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-sogan-500/20 text-sogan-200 border border-sogan-400/40">
          <span>🧾</span> Pesanan Transfer
        </div>
        <h2 class="font-serif text-2xl sm:text-3xl font-bold font-mono tracking-wide"><?= e($order['order_no']) ?></h2>
        <p class="text-xs text-cotton-200/85">Dibuat <?= e(date('d M Y, H:i', strtotime($order['created_at']))) ?> WIB</p>
      </div>
      <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-extrabold bg-cotton-50/95 text-espresso-900 shadow-sm">
        <span class="w-2 h-2 rounded-full <?= $order['status'] === 'paid' ? 'bg-coffeeleaf-600' : ($order['status'] === 'rejected' || $order['status'] === 'cancelled' ? 'bg-osing-500' : 'bg-sogan-500') ?>"></span>
        <?= e($meta['label']) ?>
      </span>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

    <!-- Kolom kiri: item pesanan + data pembeli -->
    <div class="lg:col-span-7 space-y-5">
      <div class="bg-cotton-50 rounded-3xl border border-cotton-300 shadow-sm overflow-hidden">
        <div class="p-5 sm:p-6 space-y-4">
          <h3 class="font-serif text-lg font-bold text-espresso-900 border-b border-cotton-200 pb-3">Rincian Produk</h3>
          <ul class="divide-y divide-dashed divide-cotton-200">
            <?php foreach ($order['items'] as $item): ?>
            <li class="py-3 flex items-center gap-3.5">
              <div class="w-14 h-14 rounded-xl overflow-hidden border border-cotton-300 bg-cotton-200 shrink-0">
                <img src="<?= e(product_image_url([
                    'image_path'    => $item['image_path'],
                    'name'          => $item['product_name'],
                    'category_name' => $item['category_name'] ?? '',
                ])) ?>" alt="<?= e($item['product_name']) ?>" class="w-full h-full object-cover" loading="lazy" />
              </div>
              <div class="flex-1 min-w-0">
                <p class="font-bold text-espresso-900 text-sm truncate"><?= e($item['product_name']) ?></p>
                <p class="text-[11px] text-espresso-700">🏬 <?= e($item['store_name']) ?> • <?= (int) $item['qty'] ?> × <?= format_rupiah($item['unit_price']) ?></p>
              </div>
              <span class="font-bold text-espresso-900 text-sm shrink-0"><?= format_rupiah($item['subtotal']) ?></span>
            </li>
            <?php endforeach; ?>
          </ul>
          <div class="flex items-center justify-between border-t-2 border-dashed border-cotton-300 pt-4">
            <span class="font-bold text-espresso-900">Total Transfer</span>
            <span class="font-serif text-3xl font-bold text-coffeeleaf-700"><?= format_rupiah((int) $order['total_amount']) ?></span>
          </div>
        </div>
      </div>

      <div class="bg-cotton-50 rounded-3xl border border-cotton-300 shadow-sm p-5 sm:p-6">
        <h3 class="text-xs font-bold uppercase tracking-wider text-espresso-700/80 mb-3">Data Penerima</h3>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-2 text-sm">
          <div><dt class="text-[11px] text-espresso-700">Nama</dt><dd class="font-bold text-espresso-900"><?= e($order['buyer_name']) ?></dd></div>
          <div><dt class="text-[11px] text-espresso-700">Nomor WhatsApp</dt><dd class="font-bold text-espresso-900 font-mono"><?= e(format_wa_display($order['buyer_phone'])) ?></dd></div>
          <?php if ($order['buyer_note']): ?>
          <div class="sm:col-span-2"><dt class="text-[11px] text-espresso-700">Catatan</dt><dd class="text-espresso-900"><?= e($order['buyer_note']) ?></dd></div>
          <?php endif; ?>
        </dl>
      </div>
    </div>

    <!-- Kolom kanan: pembayaran -->
    <div class="lg:col-span-5 space-y-5">

      <?php if ($order['status'] === 'pending' || $order['status'] === 'rejected'): ?>
        <div class="bg-cotton-50 rounded-3xl border border-cotton-300 shadow-md overflow-hidden">
          <div class="h-1.5 w-full bg-gradient-to-r from-osing-500 via-sogan-500 to-coffeeleaf-600" aria-hidden="true"></div>
          <div class="p-5 space-y-4">
            <h3 class="font-serif text-lg font-bold text-espresso-900">Instruksi Transfer</h3>
            <?php foreach ($accounts as $acc): ?>
              <div class="rounded-2xl bg-sogan-100/60 border border-sogan-400/40 p-3.5">
                <p class="text-xs font-bold text-espresso-900"><?= e($acc['bank']) ?></p>
                <p class="font-mono text-sm font-bold text-coffeeleaf-700 tracking-wide"><?= e($acc['number']) ?></p>
                <p class="text-[11px] text-espresso-700">a.n. <?= e($acc['holder']) ?></p>
              </div>
            <?php endforeach; ?>
            <div class="rounded-2xl bg-coffeeleaf-100/70 border border-coffeeleaf-600/25 p-3.5 text-xs text-coffeeleaf-700 space-y-1.5">
              <p>1. Transfer <strong>tepat <?= format_rupiah((int) $order['total_amount']) ?></strong> ke salah satu rekening di atas.</p>
              <p>2. Cantumkan berita transfer: <strong class="font-mono"><?= e($order['order_no']) ?></strong></p>
              <p>3. Foto/berkas bukti transfer, lalu unggah di bawah.</p>
            </div>

            <?php if ($order['status'] === 'rejected'): ?>
              <div class="rounded-2xl bg-osing-100 border border-osing-500/40 p-3 text-xs text-osing-700 font-semibold leading-relaxed">
                Bukti transfer sebelumnya <strong>ditolak admin</strong>. Silakan transfer ulang sesuai instruksi lalu unggah bukti yang baru.
              </div>
            <?php endif; ?>

            <form method="post" action="<?= e(url('order/pay')) ?>" id="proof-form" enctype="multipart/form-data" class="space-y-3.5 border-t border-cotton-200 pt-4">
              <?= csrf_field() ?>
              <input type="hidden" name="order_no" value="<?= e($order['order_no']) ?>" />
              <input type="hidden" name="image_data" id="proof-image-data" value="" />

              <div id="proof-dropzone" tabindex="0" role="button" aria-label="Pilih foto bukti transfer"
                   class="cursor-pointer rounded-2xl border-2 border-dashed border-sogan-400/60 bg-sogan-100/40 hover:bg-sogan-100/80 transition p-5 text-center space-y-2">
                <div class="w-11 h-11 mx-auto rounded-2xl bg-cotton-50 border border-cotton-300 flex items-center justify-center text-2xl">📸</div>
                <p class="text-xs font-bold text-espresso-900">Klik untuk pilih foto bukti transfer</p>
                <p class="text-[10px] text-espresso-700">PNG/JPG/WEBP — otomatis dikompresi sebelum diunggah</p>
                <input type="file" id="proof-imageFile" accept="image/png,image/jpeg,image/webp" class="sr-only" />
              </div>

              <img id="proof-preview" alt="Pratinjau bukti transfer" class="hidden w-full rounded-2xl border border-cotton-300" />
              <p id="proof-info" class="text-[11px] text-espresso-700 text-center"></p>
              <button type="button" id="proof-reset" class="hidden mx-auto block text-[11px] font-bold text-osing-600 hover:text-osing-700 underline underline-offset-2">Pilih foto lain</button>

              <button type="submit" id="proof-submit" disabled
                      class="w-full py-3.5 px-4 rounded-2xl bg-coffeeleaf-600 hover:bg-coffeeleaf-700 disabled:bg-cotton-200 disabled:text-espresso-700/50 disabled:cursor-not-allowed text-white text-sm font-bold transition shadow-sm flex items-center justify-center gap-2">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                Kirim Bukti Transfer
              </button>
            </form>
          </div>
        </div>

      <?php elseif ($order['status'] === 'verifying'): ?>
        <div class="bg-cotton-50 rounded-3xl border border-coffeeleaf-600/30 shadow-md p-5 space-y-4">
          <h3 class="font-serif text-lg font-bold text-coffeeleaf-700">⏳ Menunggu Verifikasi Admin</h3>
          <?php if ($proofUrl): ?>
            <p class="text-xs text-espresso-700">Bukti transfer Anda (diunggah <?= e(date('d M Y, H:i', strtotime($order['proof_at']))) ?>):</p>
            <a href="<?= e($proofUrl) ?>" target="_blank" rel="noopener">
              <img src="<?= e($proofUrl) ?>" alt="Bukti transfer pesanan <?= e($order['order_no']) ?>" class="w-full rounded-2xl border border-cotton-300" />
            </a>
          <?php endif; ?>
          <p class="text-xs text-espresso-700 leading-relaxed">Admin akan memeriksa pembayaran maksimal <strong>1×24 jam</strong>. Status berubah otomatis setiap halaman ini dimuat ulang. Simpan nomor pesanan Anda untuk cek status: <strong class="font-mono"><?= e($order['order_no']) ?></strong></p>
        </div>

      <?php elseif ($order['status'] === 'paid'): ?>
        <div class="bg-coffeeleaf-600 text-white rounded-3xl shadow-md p-6 space-y-3">
          <h3 class="font-serif text-xl font-bold flex items-center gap-2">
            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            Pembayaran Lunas!
          </h3>
          <p class="text-sm text-coffeeleaf-100 leading-relaxed">Terima kasih, <?= e($order['buyer_name']) ?>! Pembayaran sebesar <strong><?= format_rupiah((int) $order['total_amount']) ?></strong> telah kami verifikasi<?= $order['paid_at'] ? ' (' . e(date('d M Y, H:i', strtotime($order['paid_at']))) . ' WIB)' : '' ?>. Penjual akan segera menyiapkan pesanan Anda dan menghubungi via WhatsApp untuk pengiriman.</p>
        </div>

      <?php else: // cancelled ?>
        <div class="bg-cotton-50 rounded-3xl border border-osing-500/40 shadow-sm p-6 space-y-3">
          <h3 class="font-serif text-lg font-bold text-osing-700">Pesanan Dibatalkan</h3>
          <p class="text-xs text-espresso-700 leading-relaxed">Pesanan ini dibatalkan dan stok produk sudah dikembalikan. Bila ini tidak semestinya, hubungi admin melalui tombol WhatsApp di katalog.</p>
          <a href="<?= e(url('catalog')) ?>" class="inline-block px-4 py-2.5 rounded-2xl bg-espresso-900 hover:bg-espresso-800 text-cotton-50 text-xs font-bold transition">Belanja Lagi →</a>
        </div>
      <?php endif; ?>

      <div class="bg-cotton-50 rounded-2xl border border-cotton-300 p-4 text-center">
        <p class="text-[11px] text-espresso-700">Simpan link/nomor pesanan ini. <a href="<?= e(url('order/lookup')) ?>" class="font-bold text-osing-600 hover:text-osing-700 underline underline-offset-2">Cek status lain</a></p>
      </div>
    </div>
  </div>
</section>

<script>
  (function initProofUpload() {
    const form = document.getElementById('proof-form');
    if (!form) return;
    const dropzone = document.getElementById('proof-dropzone');
    const fileInput = document.getElementById('proof-imageFile');
    const hidden = document.getElementById('proof-image-data');
    const preview = document.getElementById('proof-preview');
    const info = document.getElementById('proof-info');
    const resetBtn = document.getElementById('proof-reset');
    const submitBtn = document.getElementById('proof-submit');

    dropzone.addEventListener('click', () => fileInput.click());
    dropzone.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); fileInput.click(); }
    });
    dropzone.addEventListener('dragover', (e) => { e.preventDefault(); dropzone.classList.add('bg-sogan-100'); });
    dropzone.addEventListener('dragleave', () => dropzone.classList.remove('bg-sogan-100'));
    dropzone.addEventListener('drop', (e) => {
      e.preventDefault();
      dropzone.classList.remove('bg-sogan-100');
      const file = e.dataTransfer?.files?.[0];
      if (file) compress(file);
    });
    fileInput.addEventListener('change', () => {
      const file = fileInput.files?.[0];
      if (file) compress(file);
    });

    function compress(file) {
      if (!['image/png', 'image/jpeg', 'image/webp'].includes(file.type)) { showToast('Format harus PNG, JPG, atau WEBP.', 'error'); return; }
      const reader = new FileReader();
      reader.onerror = () => showToast('Gagal membaca file.', 'error');
      reader.onload = (ev) => {
        const img = new Image();
        img.onerror = () => showToast('File bukan gambar valid.', 'error');
        img.onload = () => {
          const MAX = 800;
          let w = img.width, h = img.height;
          if (w > MAX || h > MAX) {
            if (w >= h) { h = Math.round(h * MAX / w); w = MAX; } else { w = Math.round(w * MAX / h); h = MAX; }
          }
          const canvas = document.createElement('canvas');
          canvas.width = w; canvas.height = h;
          canvas.getContext('2d').drawImage(img, 0, 0, w, h);
          const dataUrl = canvas.toDataURL('image/jpeg', 0.75);
          const kb = Math.max(1, Math.round(dataUrl.length * 3 / 4 / 1024));
          hidden.value = dataUrl;
          preview.src = dataUrl;
          preview.classList.remove('hidden');
          info.textContent = `Siap diunggah: ${w}×${h}px • ${kb} KB`;
          resetBtn.classList.remove('hidden');
          submitBtn.disabled = false;
        };
        img.src = ev.target.result;
      };
      reader.readAsDataURL(file);
    }

    resetBtn.addEventListener('click', () => {
      hidden.value = '';
      fileInput.value = '';
      preview.classList.add('hidden');
      info.textContent = '';
      resetBtn.classList.add('hidden');
      submitBtn.disabled = true;
    });
  })();
</script>
