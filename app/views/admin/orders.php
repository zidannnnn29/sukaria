<?php
/**
 * Panel Admin — Pesanan Transfer (keranjang belanja).
 * Konfirmasi lunas / tolak bukti / batalkan. Variabel: $orders, $status, $user
 */
$filters = [
    ''          => 'Semua',
    'pending'   => 'Menunggu Bayar',
    'verifying' => 'Verifikasi Bukti',
    'paid'      => 'Lunas',
    'rejected'  => 'Ditolak',
    'cancelled' => 'Dibatalkan',
];
$openActions = function (array $o) {
    return in_array($o['status'], ['pending', 'verifying'], true);
};
?>
<section class="space-y-6">

  <!-- Header -->
  <div class="bg-cotton-50 p-5 sm:p-6 rounded-3xl border border-cotton-300 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
      <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-bold bg-sogan-100 text-sogan-700 border border-sogan-400/40 mb-1.5">
        ❖ Kelola Pesanan • Transfer Bank
      </span>
      <h2 class="font-serif text-xl sm:text-2xl font-bold text-espresso-900">Pesanan Transfer</h2>
      <p class="text-xs sm:text-sm text-espresso-700 mt-0.5">Verifikasi bukti transfer pembeli. Konfirmasi = pesanan lunas; tolak/batalkan = stok dikembalikan.</p>
    </div>
    <a href="<?= e(url('order/lookup')) ?>" target="_blank" rel="noopener" class="shrink-0 inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-cotton-100 hover:bg-cotton-200 text-espresso-800 border border-cotton-300 text-xs font-bold transition">
      🔎 Cek Pesanan →
    </a>
  </div>

  <!-- Filter status -->
  <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-1">
    <?php foreach ($filters as $key => $label): ?>
      <a href="<?= e(url('admin/orders', $key === '' ? [] : ['status' => $key])) ?>"
         class="shrink-0 inline-flex items-center px-3.5 py-2 rounded-full text-xs font-bold border transition <?= $status === $key
           ? 'bg-espresso-900 text-cotton-50 border-espresso-900'
           : 'bg-cotton-100 text-espresso-800 border-cotton-300 hover:bg-cotton-200' ?>">
        <?= e($label) ?>
      </a>
    <?php endforeach; ?>
  </div>

  <?php if (!$orders): ?>
    <div class="bg-cotton-50 rounded-3xl border border-cotton-300 p-10 text-center space-y-2">
      <p class="text-3xl">🛒</p>
      <h3 class="font-serif font-bold text-espresso-900">Belum ada pesanan</h3>
      <p class="text-sm text-espresso-700">Pesanan dari keranjang belanja pembeli akan muncul di sini.</p>
    </div>
  <?php else: ?>
    <div class="bg-cotton-50 rounded-3xl border border-cotton-300 shadow-sm overflow-x-auto">
      <table class="w-full text-sm min-w-[900px]">
        <thead>
          <tr class="text-left text-[11px] uppercase tracking-wider text-espresso-700 border-b border-cotton-300">
            <th class="px-4 py-3">No. Pesanan</th>
            <th class="px-4 py-3">Pembeli</th>
            <th class="px-4 py-3">Item</th>
            <th class="px-4 py-3 text-right">Total</th>
            <th class="px-4 py-3">Status</th>
            <th class="px-4 py-3">Bukti</th>
            <th class="px-4 py-3 text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-dashed divide-cotton-200">
          <?php foreach ($orders as $o): $meta = Order::statusMeta($o['status']); ?>
          <tr class="hover:bg-cotton-100/50">
            <td class="px-4 py-3">
              <a href="<?= e(url('order/view', ['no' => $o['order_no']])) ?>" target="_blank" rel="noopener" class="font-mono font-bold text-coffeeleaf-700 hover:underline"><?= e($o['order_no']) ?></a>
              <p class="text-[10px] text-espresso-700"><?= e(date('d M Y H:i', strtotime($o['created_at']))) ?></p>
            </td>
            <td class="px-4 py-3">
              <p class="font-bold text-espresso-900"><?= e($o['buyer_name']) ?></p>
              <p class="text-[10px] text-espresso-700 font-mono"><?= e(format_wa_display($o['buyer_phone'])) ?></p>
            </td>
            <td class="px-4 py-3 text-espresso-800"><?= (int) $o['item_count'] ?> produk</td>
            <td class="px-4 py-3 text-right font-bold text-espresso-900"><?= format_rupiah((int) $o['total_amount']) ?></td>
            <td class="px-4 py-3">
              <span class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-extrabold <?= $meta['class'] ?>"><?= e($meta['label']) ?></span>
            </td>
            <td class="px-4 py-3">
              <?php $proofUrl = proof_image_url($o['proof_path']); ?>
              <?php if ($proofUrl): ?>
                <a href="<?= e($proofUrl) ?>" target="_blank" rel="noopener" title="Lihat bukti transfer">
                  <img src="<?= e($proofUrl) ?>" alt="Bukti transfer" class="w-14 h-10 object-cover rounded-lg border border-cotton-300" />
                </a>
                <p class="text-[9px] text-espresso-700 mt-0.5"><?= e(date('d M H:i', strtotime($o['proof_at']))) ?></p>
              <?php else: ?>
                <span class="text-[10px] text-espresso-700">—</span>
              <?php endif; ?>
            </td>
            <td class="px-4 py-3">
              <div class="flex items-center justify-end gap-1.5 flex-wrap">
                <?php if ($openActions($o)): ?>
                  <form method="post" action="<?= e(url('admin/orders/confirm')) ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="order_no" value="<?= e($o['order_no']) ?>" />
                    <button type="submit" class="px-3 py-1.5 rounded-xl bg-coffeeleaf-600 hover:bg-coffeeleaf-700 text-white text-[10px] font-bold transition">✓ Lunas</button>
                  </form>
                  <form method="post" action="<?= e(url('admin/orders/reject')) ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="order_no" value="<?= e($o['order_no']) ?>" />
                    <button type="submit" class="px-3 py-1.5 rounded-xl bg-osing-100 hover:bg-osing-500/20 text-osing-700 border border-osing-500/30 text-[10px] font-bold transition">✕ Tolak</button>
                  </form>
                  <form method="post" action="<?= e(url('admin/orders/cancel')) ?>" data-confirm-title="Batalkan Pesanan?" data-confirm-name="pesanan <?= e($o['order_no']) ?> (stok akan dikembalikan)">
                    <?= csrf_field() ?>
                    <input type="hidden" name="order_no" value="<?= e($o['order_no']) ?>" />
                    <button type="submit" class="px-3 py-1.5 rounded-xl bg-cotton-100 hover:bg-cotton-200 text-espresso-800 border border-cotton-300 text-[10px] font-bold transition">Batalkan</button>
                  </form>
                <?php else: ?>
                  <span class="text-[10px] text-espresso-700">Selesai</span>
                <?php endif; ?>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <p class="text-[11px] text-espresso-700 px-2">Total: <?= count($orders) ?> pesanan. Klik nomor pesanan untuk melihat detail lengkap (item, data pembeli, bukti transfer).</p>
  <?php endif; ?>
</section>
