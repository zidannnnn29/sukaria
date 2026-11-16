<?php
/**
 * Layout panel (admin & seller) dengan sidebar navigasi — tema Batik Kopi Pecah.
 *
 * Variabel tersedia: $content, $pageTitle, $user (user login)
 */
$role = $user['role'];
$isAdmin = $role === 'admin';
$currentRoute = (string) ($_GET['r'] ?? '');

$navItems = $isAdmin ? [
    ['route' => 'admin/dashboard', 'label' => 'Dashboard',      'icon' => '<rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/>'],
    ['route' => 'admin/users',    'label' => 'Manajemen User', 'icon' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>'],
] : [
    ['route' => 'seller/dashboard', 'label' => 'Dashboard',        'icon' => '<rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/>'],
    ['route' => 'seller/products',  'label' => 'Kelola Produk',    'icon' => '<path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/>'],
    ['route' => 'seller/categories','label' => 'Kelola Kategori',  'icon' => '<path d="M12 2H2v10l9.29 9.29c.94.94 2.48.94 3.42 0l6.58-6.58c.94-.94.94-2.48 0-3.42L12 2Z"/><path d="M7 7h.01"/>'],
    ['route' => 'seller/settings',  'label' => 'Pengaturan WA',    'icon' => '<path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/>'],
];

function panelNavIsActive(string $current, string $route): bool
{
    $prefix = preg_quote($route, '/');
    return (bool) preg_match('/^' . $prefix . '($|\/)/', $current);
}
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

  <header class="sticky top-0 z-30 bg-cotton-50/95 backdrop-blur border-b border-cotton-300 shadow-sm">
    <div class="batik-border-strip" aria-hidden="true"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between gap-3">
      <div class="flex items-center gap-3.5">
        <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-espresso-900 via-espresso-800 to-osing-600 flex items-center justify-center shadow-md shrink-0 border border-sogan-400/50" aria-hidden="true">
          <svg class="w-6 h-6" viewBox="0 0 40 40" fill="none">
            <polygon points="20,3 37,20 20,37 3,20" stroke="#F2D5AA" stroke-width="1.4" fill="rgba(200,120,40,0.14)" />
            <g transform="translate(20,20) rotate(-28)">
              <ellipse cx="0" cy="0" rx="6.2" ry="9.2" fill="#C87828" stroke="#FFFDF9" stroke-width="1.2" />
              <path d="M-1 -8.2 L1.4 -2.5 L-1.6 2.2 L1 8.2" stroke="#2A160C" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
            </g>
          </svg>
        </div>
        <div>
          <h1 class="font-serif text-lg font-bold tracking-tight text-espresso-900 leading-tight">Sukaria</h1>
          <p class="text-[11px] font-medium text-osing-600">Panel <?= $isAdmin ? 'Administrator' : 'Pengelola Toko' ?></p>
        </div>
      </div>

      <div class="flex items-center gap-2">
        <a href="<?= e(url('catalog')) ?>" class="hidden sm:inline-flex px-3.5 py-2 rounded-xl bg-cotton-100 hover:bg-cotton-200 text-espresso-800 border border-cotton-300 text-xs font-bold transition items-center gap-1.5">
          <svg class="w-4 h-4 text-osing-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
          Lihat Katalog
        </a>
        <div class="text-right hidden md:block mr-1">
          <p class="text-xs font-bold text-espresso-900 leading-tight"><?= e($user['name']) ?></p>
          <p class="text-[11px] text-espresso-700"><?= $isAdmin ? e($user['email']) : e($user['store_name'] ?: $user['email']) ?></p>
        </div>
        <form method="post" action="<?= e(url('logout')) ?>">
          <?= csrf_field() ?>
          <button type="submit" class="px-3.5 py-2 rounded-xl bg-osing-100 hover:bg-osing-500/20 text-osing-700 border border-osing-500/30 text-xs font-bold transition">Keluar</button>
        </form>
      </div>
    </div>

    <!-- Navigasi modul -->
    <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-3 flex items-center gap-2 overflow-x-auto no-scrollbar" aria-label="Menu panel">
      <?php foreach ($navItems as $item): ?>
        <a href="<?= e(url($item['route'])) ?>"
           class="shrink-0 inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs sm:text-sm font-bold border transition <?= panelNavIsActive($currentRoute, $item['route'])
             ? 'bg-espresso-900 text-cotton-50 border-espresso-900 shadow-sm'
             : 'bg-cotton-100 text-espresso-800 border-cotton-300 hover:bg-cotton-200' ?>">
          <svg class="w-4 h-4 <?= panelNavIsActive($currentRoute, $item['route']) ? 'text-sogan-400' : 'text-osing-600' ?>" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><?= $item['icon'] ?></svg>
          <?= e($item['label']) ?>
        </a>
      <?php endforeach; ?>
    </nav>
  </header>

  <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
    <?= $content ?>
  </main>

  <footer class="bg-cotton-50 border-t border-cotton-300 mt-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 text-center text-xs text-espresso-700">
      <p class="font-serif font-bold text-espresso-900">Sukaria — <?= e(SUKARIA_TAGLINE) ?></p>
    </div>
    <div class="batik-border-strip" aria-hidden="true"></div>
  </footer>

  <div id="toast-container" aria-live="polite" class="fixed bottom-5 right-5 z-50 flex flex-col gap-2.5 max-w-sm w-[calc(100vw-2.5rem)] pointer-events-none"></div>
  <div id="confirm-modal-root"></div>
  <script>window.SUKARIA_FLASH = <?= json_encode(flash_take(), JSON_UNESCAPED_UNICODE) ?>;</script>
  <script src="assets/app.js"></script>
</body>
</html>
