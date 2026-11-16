<?php
/**
 * Fungsi render view: mengembalikan konten lalu dibungkus layout.
 */
function view(string $path, array $vars = []): string
{
    extract($vars, EXTR_SKIP);
    ob_start();
    require dirname(__DIR__) . '/views/' . $path . '.php';
    return ob_get_clean();
}

function render(string $layout, string $path, array $vars = []): never
{
    $content = view($path, $vars);
    extract($vars, EXTR_SKIP);
    require dirname(__DIR__) . '/views/layouts/' . $layout . '.php';
    exit;
}
