<?php
/**
 * Front Controller — satu-satunya titik masuk aplikasi.
 * Jalankan: php -S localhost:8000 -t public  (dari folder root proyek)
 */

declare(strict_types=1);

require dirname(__DIR__) . '/app/core/bootstrap.php';

$route = (string) ($_GET['r'] ?? 'catalog');

// Peta rute: r=<route> → [Class::method]
$routes = [
    // --- Publik / Pembeli ---
    'catalog'   => [CatalogController::class, 'index'],

    // --- Autentikasi ---
    'login'     => [AuthController::class, 'showLogin'],
    'do-login'  => [AuthController::class, 'login'],
    'logout'    => [AuthController::class, 'logout'],

    // --- Admin ---
    'admin/dashboard'      => [AdminController::class, 'dashboard'],
    'admin/users'          => [AdminController::class, 'users'],
    'admin/users/create'   => [AdminController::class, 'userForm'],
    'admin/users/edit'     => [AdminController::class, 'userForm'],
    'admin/users/save'     => [AdminController::class, 'saveUser'],
    'admin/users/toggle'   => [AdminController::class, 'toggleUser'],
    'admin/users/delete'   => [AdminController::class, 'deleteUser'],

    // --- Seller ---
    'seller/dashboard'         => [SellerController::class, 'dashboard'],
    'seller/products'          => [SellerController::class, 'products'],
    'seller/products/create'   => [SellerController::class, 'productForm'],
    'seller/products/edit'     => [SellerController::class, 'productForm'],
    'seller/products/save'     => [SellerController::class, 'saveProduct'],
    'seller/products/delete'   => [SellerController::class, 'deleteProduct'],
    'seller/products/toggle-featured' => [SellerController::class, 'toggleFeatured'],
    'seller/products/toggle-active'   => [SellerController::class, 'toggleActive'],
    'seller/categories'        => [SellerController::class, 'categories'],
    'seller/categories/save'   => [SellerController::class, 'saveCategory'],
    'seller/categories/toggle' => [SellerController::class, 'toggleCategory'],
    'seller/categories/delete' => [SellerController::class, 'deleteCategory'],
    'seller/settings'          => [SellerController::class, 'settings'],
    'seller/settings/save'     => [SellerController::class, 'saveSettings'],
    'seller/settings/reset'    => [SellerController::class, 'resetSettings'],
];

$handler = $routes[$route] ?? null;

if ($handler === null) {
    http_response_code(404);
    exit('404 — Halaman tidak ditemukan. <a href="' . e(url('catalog')) . '">Kembali ke Katalog</a>');
}

[$class, $method] = $handler;
$class::$method();
