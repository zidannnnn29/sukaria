<?php
/**
 * Layout publik (katalog pembeli & login) — tema Batik Kopi Pecah.
 *
 * Variabel tersedia: $content, $pageTitle
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= e($pageTitle ?? SUKARIA_APP_NAME) ?></title>

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,600;0,700;1,600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
            serif: ['"Lora"', 'Georgia', 'serif'],
          },
          colors: {
            cotton:   { 50:'#FFFDF9', 100:'#FAF4E8', 200:'#F3E8D4', 300:'#E2D0B8', 400:'#CBB292' },
            espresso: { 700:'#5C3622', 800:'#462616', 900:'#2A160C' },
            sogan:    { 100:'#F9EBD6', 200:'#F2D5AA', 400:'#DDA052', 500:'#C87828', 600:'#A55814', 700:'#82410B' },
            osing:    { 100:'#F9E4DE', 500:'#A64228', 600:'#8E351E', 700:'#702714' },
            coffeeleaf:{ 100:'#E3F1EA', 200:'#C4E2D3', 600:'#2D6A4F', 700:'#21503B' }
          }
        }
      }
    };
  </script>
  <link rel="stylesheet" href="assets/app.css" />
</head>
<body class="bg-batik-watermark text-espresso-900 font-sans antialiased min-h-screen flex flex-col selection:bg-sogan-500 selection:text-white">

  <?php if (!isset($hideHeader) || !$hideHeader): ?>
  <header class="sticky top-0 z-30 bg-cotton-50/95 backdrop-blur border-b border-cotton-300 shadow-sm">
    <div class="batik-border-strip" aria-hidden="true"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5 flex items-center justify-between gap-3">
      <a href="<?= e(url('catalog')) ?>" class="flex items-center gap-3.5">
        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-espresso-900 via-espresso-800 to-osing-600 flex items-center justify-center shadow-md shrink-0 border border-sogan-400/50" aria-hidden="true">
          <svg class="w-8 h-8" viewBox="0 0 40 40" fill="none">
            <polygon points="20,3 37,20 20,37 3,20" stroke="#F2D5AA" stroke-width="1.4" fill="rgba(200,120,40,0.14)" />
            <circle cx="20" cy="7.5" r="1.3" fill="#F2D5AA" /><circle cx="32.5" cy="20" r="1.3" fill="#F2D5AA" />
            <circle cx="20" cy="32.5" r="1.3" fill="#F2D5AA" /><circle cx="7.5" cy="20" r="1.3" fill="#F2D5AA" />
            <g transform="translate(20,20) rotate(-28)">
              <ellipse cx="0" cy="0" rx="6.2" ry="9.2" fill="#C87828" stroke="#FFFDF9" stroke-width="1.2" />
              <path d="M-1 -8.2 L1.4 -2.5 L-1.6 2.2 L1 8.2" stroke="#2A160C" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
            </g>
          </svg>
        </div>
        <div>
          <h1 class="font-serif text-xl sm:text-2xl font-bold tracking-tight text-espresso-900 leading-tight">Sukaria</h1>
          <p class="text-[11px] sm:text-xs font-medium text-osing-600">Katalog UMKM Oleh-Oleh Khas Banyuwangi</p>
        </div>
      </a>

      <nav class="flex items-center gap-2" aria-label="Navigasi utama">
        <?php $authed = Auth::user(); ?>
        <a href="<?= e(url('cart')) ?>" class="relative px-3.5 py-2.5 rounded-2xl bg-cotton-100 hover:bg-cotton-200 text-espresso-900 border border-cotton-300 text-xs sm:text-sm font-bold transition flex items-center gap-2" aria-label="Keranjang belanja">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
          <span class="hidden sm:inline">Keranjang</span>
          <?php $cartCount = Cart::count(); ?>
          <span class="inline-flex items-center justify-center min-w-5 h-5 px-1 rounded-full text-[10px] font-extrabold <?= $cartCount > 0 ? 'bg-osing-600 text-white' : 'bg-cotton-300 text-espresso-700' ?>"><?= $cartCount ?></span>
        </a>
        <a href="<?= e(url('order/lookup')) ?>" class="hidden md:inline-flex px-3.5 py-2.5 rounded-2xl bg-cotton-100 hover:bg-cotton-200 text-espresso-900 border border-cotton-300 text-xs sm:text-sm font-bold transition">
          Cek Pesanan
        </a>
        <?php if ($authed): ?>
          <a href="<?= e(url($authed['role'] === 'admin' ? 'admin/dashboard' : 'seller/dashboard')) ?>"
             class="px-4 py-2.5 rounded-2xl bg-espresso-900 hover:bg-espresso-800 text-cotton-50 text-xs sm:text-sm font-bold transition border border-sogan-500/40 flex items-center gap-2">
            <svg class="w-4 h-4 text-sogan-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>
            <span class="hidden sm:inline"><?= $authed['role'] === 'admin' ? 'Dashboard Admin' : 'Dashboard Toko' ?></span>
          </a>
          <form method="post" action="<?= e(url('logout')) ?>">
            <?= csrf_field() ?>
            <button type="submit" class="px-3.5 py-2.5 rounded-2xl bg-cotton-100 hover:bg-cotton-200 text-espresso-800 border border-cotton-300 text-xs sm:text-sm font-bold transition">Keluar</button>
          </form>
        <?php else: ?>
          <a href="<?= e(url('login')) ?>"
             class="px-4 py-2.5 rounded-2xl bg-espresso-900 hover:bg-espresso-800 text-cotton-50 text-xs sm:text-sm font-bold transition border border-sogan-500/40">
            Login Pengelola
          </a>
        <?php endif; ?>
      </nav>
    </div>
  </header>
  <?php endif; ?>

  <main class="flex-1 w-full">
    <?= $content ?>
  </main>

  <footer class="bg-cotton-50 border-t border-cotton-300 mt-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 flex flex-col md:flex-row items-center justify-between gap-2.5 text-xs text-espresso-700">
      <p class="font-serif font-bold text-espresso-900 text-center md:text-left">
        Sukaria — <?= e(SUKARIA_TAGLINE) ?>
      </p>
      <div class="flex flex-wrap items-center justify-center gap-x-3 gap-y-1">
        <span class="inline-flex items-center gap-1.5 font-semibold text-coffeeleaf-700">
          <span class="w-2 h-2 rounded-full bg-[#25D366]"></span>
          <span>Pesan via WhatsApp</span>
        </span>
        <span class="inline-flex items-center gap-1.5 font-semibold text-osing-600">
          <span class="w-2 h-2 rounded-full bg-osing-500"></span>
          <span>Keranjang &amp; Transfer Bank</span>
        </span>
        <span class="hidden sm:inline text-cotton-400">•</span>
        <span>Tema Batik Kopi Pecah Banyuwangi</span>
      </div>
    </div>
    <div class="batik-border-strip" aria-hidden="true"></div>
  </footer>

  <div id="toast-container" aria-live="polite" class="fixed bottom-5 right-5 z-50 flex flex-col gap-2.5 max-w-sm w-[calc(100vw-2.5rem)] pointer-events-none"></div>
  <div id="confirm-modal-root"></div>
  <script>window.SUKARIA_FLASH = <?= json_encode(flash_take(), JSON_UNESCAPED_UNICODE) ?>;</script>
  <script src="assets/app.js"></script>
</body>
</html>
