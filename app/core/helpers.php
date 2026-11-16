<?php
/**
 * Helper global: escaping, URL, redirect, flash, CSRF, validasi WA,
 * formatter Rupiah, dan generator ilustrasi SVG Batik Kopi Pecah.
 */

declare(strict_types=1);

/** Escape output HTML (anti-XSS). */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Bangun URL internal: index.php?r=rute&params... */
function url(?string $route = null, array $params = []): string
{
    if ($route === null) {
        return 'index.php';
    }
    $query = array_merge(['r' => $route], $params);
    return 'index.php?' . http_build_query($query);
}

function redirect(string $route, array $params = []): never
{
    header('Location: ' . url($route, $params));
    exit;
}

function redirect_raw(string $url): never
{
    header('Location: ' . $url);
    exit;
}

/** Redirect kembali ke halaman sebelumnya (fallback ke rute tertentu). */
function redirect_back(string $fallbackRoute): never
{
    $ref = $_SERVER['HTTP_REFERER'] ?? '';
    if ($ref !== '' && str_starts_with($ref, (string) ($_SERVER['REQUEST_SCHEME'] ?? 'http'))) {
        redirect_raw($ref);
    }
    redirect($fallbackRoute);
}

// ---------------------------------------------------------------------
// FLASH MESSAGE (toast)
// ---------------------------------------------------------------------
function flash_set(string $message, string $type = 'success'): void
{
    $_SESSION['flash'][] = ['message' => $message, 'type' => $type];
}

function flash_take(): array
{
    $items = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $items;
}

// ---------------------------------------------------------------------
// CSRF PROTECTION
// ---------------------------------------------------------------------
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function csrf_verify(): void
{
    $posted = (string) ($_POST['_csrf'] ?? '');
    if ($posted === '' || !hash_equals($_SESSION['csrf_token'] ?? '', $posted)) {
        http_response_code(419);
        exit('Sesi kedaluwarsa atau token keamanan tidak valid. Silakan muat ulang halaman.');
    }
}

// ---------------------------------------------------------------------
// FORMAT & VALIDASI
// ---------------------------------------------------------------------
function format_rupiah(int|float|string $amount): string
{
    return 'Rp ' . number_format((float) $amount, 0, ',', '.');
}

/** Normalisasi nomor WA: 08... → 628..., buang non-digit. */
function normalize_wa(mixed $raw): string
{
    $digits = preg_replace('/\D/', '', (string) $raw) ?? '';
    if ($digits === '') {
        return '';
    }
    if (str_starts_with($digits, '0')) {
        $digits = '62' . substr($digits, 1);
    } elseif (str_starts_with($digits, '8')) {
        $digits = '62' . $digits;
    }
    return $digits;
}

/** Validasi nomor WA ternormalisasi: 62xxx, 10–15 digit. */
function validate_wa(string $normalized): array
{
    if ($normalized === '') {
        return [false, 'Nomor WhatsApp wajib diisi.'];
    }
    if (!str_starts_with($normalized, '62')) {
        return [false, 'Nomor harus diawali 08, 62, atau +62.'];
    }
    if (strlen($normalized) < 10) {
        return [false, 'Nomor terlalu pendek (' . strlen($normalized) . ' digit). Minimal 10 digit.'];
    }
    if (strlen($normalized) > 15) {
        return [false, 'Nomor terlalu panjang (' . strlen($normalized) . ' digit). Maksimal 15 digit.'];
    }
    return [true, ''];
}

/** Format tampilan: 6281234567890 → +62 812-3456-7890 */
function format_wa_display(string $normalized): string
{
    $digits = normalize_wa($normalized) ?: SUKARIA_DEFAULT_WA;
    if (!str_starts_with($digits, '62')) {
        return '+' . $digits;
    }
    $rest = substr($digits, 2);
    $len  = strlen($rest);
    if ($len <= 3)  return '+62 ' . $rest;
    if ($len <= 7)  return '+62 ' . substr($rest, 0, 3) . '-' . substr($rest, 3);
    return '+62 ' . substr($rest, 0, 3) . '-' . substr($rest, 3, 4) . '-' . substr($rest, 7);
}

/** Slug URL-safe. */
function slugify(string $text): string
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text) ?? '';
    $text = trim($text, '-');
    return $text !== '' ? $text : 'item';
}

/** Slug unik terhadap tabel/kolom (untuk CREATE). */
function unique_slug(string $table, string $name, int $ignoreId = 0): string
{
    $base = slugify($name);
    $slug = $base;
    $i    = 2;
    while (true) {
        $stmt = Database::pdo()->prepare(
            "SELECT id FROM {$table} WHERE slug = :slug AND id != :id LIMIT 1"
        );
        $stmt->execute(['slug' => $slug, 'id' => $ignoreId]);
        if (!$stmt->fetch()) {
            return $slug;
        }
        $slug = $base . '-' . $i++;
    }
}

// ---------------------------------------------------------------------
// AKSES & OTORISASI
// ---------------------------------------------------------------------
function require_login(): array
{
    $user = Auth::user();
    if (!$user) {
        flash_set('Silakan login terlebih dahulu.', 'error');
        redirect('login');
    }
    return $user;
}

function require_role(string ...$roles): array
{
    $user = require_login();
    if (!in_array($user['role'], $roles, true)) {
        http_response_code(403);
        exit('403 — Anda tidak memiliki akses ke halaman ini.');
    }
    return $user;
}

// ---------------------------------------------------------------------
// POST GUARD (semua aksi tulis harus POST + CSRF)
// ---------------------------------------------------------------------
function require_post(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        http_response_code(405);
        exit('405 — Metode tidak diizinkan.');
    }
    csrf_verify();
}

/** Nilai lama form (repel setelah validasi gagal). */
function old(string $key, string $default = ''): string
{
    return e((string) ($_SESSION['form_old'][$key] ?? $default));
}

/** Nilai lama tanpa escaping (untuk perbandingan). */
function old_raw(string $key, mixed $default = ''): mixed
{
    return $_SESSION['form_old'][$key] ?? $default;
}

function flash_old(array $values): void
{
    $_SESSION['form_old'] = $values;
}

function clear_old(): void
{
    unset($_SESSION['form_old']);
}

// ---------------------------------------------------------------------
// GENERATOR ILUSTRASI SVG BATIK KOPI PECAH (port dari prototype)
// ---------------------------------------------------------------------
function batik_category_themes(): array
{
    return [
        'Kue Basah & Jajanan Pasar' => [
            'bgStart' => '#3D2011', 'bgEnd' => '#6E391D', 'accent' => '#DDA052',
            'shortLabel' => 'JAJANAN OSING',
            'iconPaths' => '<ellipse cx="0" cy="4" rx="18" ry="8" fill="#F2D5AA" fill-opacity="0.25" stroke="#F2D5AA" stroke-width="1.6"/><path d="M-12 2 C-12 -8, 12 -8, 12 2 Z" fill="#C87828" stroke="#FFFDF9" stroke-width="1.4"/><circle cx="0" cy="-6" r="2.2" fill="#F2D5AA"/>',
        ],
        'Keringan & Keripik' => [
            'bgStart' => '#462616', 'bgEnd' => '#82410B', 'accent' => '#F2D5AA',
            'shortLabel' => 'KERINGAN RENYAH',
            'iconPaths' => '<ellipse cx="-6" cy="0" rx="10" ry="13" transform="rotate(-18 -6 0)" fill="#C87828" stroke="#FFFDF9" stroke-width="1.4"/><ellipse cx="7" cy="2" rx="9" ry="12" transform="rotate(22 7 2)" fill="#DDA052" stroke="#2A160C" stroke-width="1.3"/><circle cx="-6" cy="-2" r="1.4" fill="#2A160C"/><circle cx="7" cy="1" r="1.4" fill="#2A160C"/>',
        ],
        'Sambal & Bumbu Khas' => [
            'bgStart' => '#4E1C11', 'bgEnd' => '#8E351E', 'accent' => '#F2D5AA',
            'shortLabel' => 'SAMBAL BLAMBANGAN',
            'iconPaths' => '<path d="M-16 0 C-16 11, 16 11, 16 0 Z" fill="#2A160C" stroke="#F2D5AA" stroke-width="1.5"/><path d="M-4 -2 C2 -12, 11 -10, 13 -3 C8 -1, 1 0, -4 -2 Z" fill="#C87828" stroke="#FFFDF9" stroke-width="1.3"/><path d="M13 -3 L17 -7" stroke="#84CC16" stroke-width="1.8" stroke-linecap="round"/>',
        ],
        'Kopi & Minuman Tradisional' => [
            'bgStart' => '#2A160C', 'bgEnd' => '#5C3622', 'accent' => '#C87828',
            'shortLabel' => 'KOPI PECAH KEMIREN',
            'iconPaths' => '<g transform="rotate(-24)"><ellipse cx="0" cy="0" rx="10" ry="15" fill="#C87828" stroke="#FFFDF9" stroke-width="1.5"/><path d="M-1.5 -13 L2 -4 L-2 3 L1.5 13" fill="none" stroke="#2A160C" stroke-width="2" stroke-linecap="round"/></g>',
        ],
        'Olahan Ikan & Hasil Laut' => [
            'bgStart' => '#1F3A32', 'bgEnd' => '#2D6A4F', 'accent' => '#F2D5AA',
            'shortLabel' => 'HASIL LAUT MUNCAR',
            'iconPaths' => '<path d="M-15 0 C-7 -10, 6 -10, 13 0 C6 10, -7 10, -15 0 Z" fill="#C87828" stroke="#FFFDF9" stroke-width="1.4"/><polygon points="12,0 19,-7 19,7" fill="#DDA052" stroke="#FFFDF9" stroke-width="1.2"/><circle cx="-8" cy="-2" r="1.6" fill="#2A160C"/>',
        ],
        'Paket Hampers & Oleh-Oleh' => [
            'bgStart' => '#361B0E', 'bgEnd' => '#702714', 'accent' => '#DDA052',
            'shortLabel' => 'HAMPERS EKSKLUSIF',
            'iconPaths' => '<rect x="-14" y="-5" width="28" height="18" rx="3" fill="#C87828" stroke="#FFFDF9" stroke-width="1.4"/><rect x="-16" y="-10" width="32" height="6" rx="2" fill="#8E351E" stroke="#F2D5AA" stroke-width="1.3"/><line x1="0" y1="-10" x2="0" y2="13" stroke="#F2D5AA" stroke-width="2"/>',
        ],
    ];
}

/** Pembungkus judul maksimal 2 baris @ 26 karakter. */
function batik_wrap_title(string $text, int $maxChars = 26): array
{
    $words  = preg_split('/\s+/', trim($text ?: 'Produk Sukaria')) ?: [];
    $lines  = [];
    $current = '';

    foreach ($words as $word) {
        $next = $current !== '' ? $current . ' ' . $word : $word;
        if (mb_strlen($next) <= $maxChars) {
            $current = $next;
            continue;
        }
        if ($current !== '') {
            $lines[] = $current;
        }
        $current = mb_strlen($word) > $maxChars
            ? mb_substr($word, 0, $maxChars - 1) . '…'
            : $word;
        if (count($lines) >= 2) {
            break;
        }
    }
    if ($current !== '' && count($lines) < 2) {
        $lines[] = $current;
    }
    return $lines ?: ['Oleh-Oleh Banyuwangi'];
}

/** Data URI SVG placeholder produk sesuai nama & kategori. */
function batik_placeholder_svg(string $nama, string $kategori): string
{
    $themes = batik_category_themes();
    $theme  = $themes[$kategori] ?? reset($themes);

    $titleLines = batik_wrap_title($nama, 26);
    $line1 = e($titleLines[0]);
    $line2 = $titleLines[1] ?? '';
    $badgeText = e($theme['shortLabel']);

    $svg = "<svg xmlns='http://www.w3.org/2000/svg' width='640' height='400' viewBox='0 0 640 400'>
      <defs>
        <linearGradient id='bgGrad' x1='0%' y1='0%' x2='100%' y2='100%'>
          <stop offset='0%' stop-color='{$theme['bgStart']}'/>
          <stop offset='100%' stop-color='{$theme['bgEnd']}'/>
        </linearGradient>
        <pattern id='batikGrid' width='80' height='80' patternUnits='userSpaceOnUse'>
          <polygon points='40,4 76,40 40,76 4,40' fill='none' stroke='{$theme['accent']}' stroke-opacity='0.18' stroke-width='1.2'/>
          <circle cx='40' cy='10' r='1.6' fill='#F2D5AA' fill-opacity='0.25'/>
          <circle cx='70' cy='40' r='1.6' fill='#F2D5AA' fill-opacity='0.25'/>
          <circle cx='40' cy='70' r='1.6' fill='#F2D5AA' fill-opacity='0.25'/>
          <circle cx='10' cy='40' r='1.6' fill='#F2D5AA' fill-opacity='0.25'/>
          <g transform='translate(40,40) rotate(-25)'>
            <ellipse cx='0' cy='0' rx='6' ry='9' fill='none' stroke='#F2D5AA' stroke-opacity='0.16' stroke-width='1.1'/>
            <path d='M-1 -8 L1 -2 L-1 2 L1 8' stroke='{$theme['accent']}' stroke-opacity='0.24' stroke-width='1.1' fill='none'/>
          </g>
        </pattern>
      </defs>
      <rect width='640' height='400' fill='url(#bgGrad)'/>
      <rect width='640' height='400' fill='url(#batikGrid)'/>
      <rect x='18' y='18' width='604' height='364' rx='22' fill='none' stroke='{$theme['accent']}' stroke-opacity='0.45' stroke-width='1.5' stroke-dasharray='6 4'/>
      <g transform='translate(320,136)'>
        <polygon points='0,-46 46,0 0,46 -46,0' fill='#2A160C' fill-opacity='0.72' stroke='{$theme['accent']}' stroke-width='2'/>
        {$theme['iconPaths']}
      </g>
      <g transform='translate(320,214)'>
        <rect x='-126' y='-14' width='252' height='28' rx='14' fill='#2A160C' fill-opacity='0.75' stroke='{$theme['accent']}' stroke-opacity='0.6' stroke-width='1'/>
        <text x='0' y='4' text-anchor='middle' fill='#F2D5AA' font-family='sans-serif' font-size='11' font-weight='700' letter-spacing='1.6'>{$badgeText}</text>
      </g>
      <text x='320' y='" . ($line2 !== '' ? '266' : '278') . "' text-anchor='middle' fill='#FFFDF9' font-family='Georgia, serif' font-size='25' font-weight='700'>{$line1}</text>"
      . ($line2 !== '' ? "<text x='320' y='298' text-anchor='middle' fill='#F2D5AA' font-family='Georgia, serif' font-size='23' font-weight='700'>" . e($line2) . "</text>" : '') . "
      <text x='320' y='352' text-anchor='middle' fill='#F2D5AA' fill-opacity='0.78' font-family='sans-serif' font-size='12' font-weight='600' letter-spacing='1'>❖ SUKARIA • KHAS BANYUWANGI ❖</text>
    </svg>";

    return 'data:image/svg+xml;charset=UTF-8,' . str_replace("'", '%27', rawurlencode($svg));
}

/** URL gambar produk: file unggahan (jika ada) atau placeholder batik. */
function product_image_url(array $product): string
{
    $path = (string) ($product['image_path'] ?? '');
    if ($path !== '') {
        $fsPath = SUKARIA_UPLOAD_DIR . '/' . basename($path);
        if (is_file($fsPath)) {
            return 'uploads/products/' . rawurlencode(basename($path)) . '?v=' . filemtime($fsPath);
        }
    }
    return batik_placeholder_svg((string) $product['name'], (string) ($product['category_name'] ?? ''));
}

/** Hapus file foto produk dari disk. */
function delete_product_image(?string $imagePath): void
{
    if (!$imagePath) {
        return;
    }
    $fsPath = SUKARIA_UPLOAD_DIR . '/' . basename($imagePath);
    if (is_file($fsPath)) {
        @unlink($fsPath);
    }
}

/** Simpan data-URL base64 hasil kompresi sisi klien sebagai file JPG. */
function save_base64_image(string $dataUrl): ?string
{
    if (!preg_match('/^data:image\/(png|jpeg|jpg|webp);base64,(.+)$/i', $dataUrl, $m)) {
        return null;
    }
    $binary = base64_decode($m[2], true);
    if ($binary === false || strlen($binary) < 100 || strlen($binary) > SUKARIA_MAX_UPLOAD) {
        return null;
    }
    if (!is_dir(SUKARIA_UPLOAD_DIR) && !mkdir(SUKARIA_UPLOAD_DIR, 0775, true)) {
        return null;
    }
    $filename = 'prd_' . bin2hex(random_bytes(8)) . '.jpg';
    if (file_put_contents(SUKARIA_UPLOAD_DIR . '/' . $filename, $binary) === false) {
        return null;
    }
    return $filename;
}
