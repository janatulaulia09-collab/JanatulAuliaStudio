<?php
ob_start();
require_once __DIR__ . '/auth/auth_check.php';
require_admin();
$page = $_GET['page'] ?? 'dashboard';
$routes = [
    'dashboard' => __DIR__ . '/modules/dashboard/index.php',
    'services' => __DIR__ . '/modules/services/index.php',
    'produk' => __DIR__ . '/modules/services/index.php', // alias URL lama
    'tambah_produk' => __DIR__ . '/modules/services/index.php', // alias URL lama
    'portfolio' => __DIR__ . '/modules/portfolio/list.php',
    'articles' => __DIR__ . '/modules/articles/index.php',
    'inquiries' => __DIR__ . '/modules/inquiries/list.php',
];
if (!isset($routes[$page])) {
    http_response_code(404);
    $page = 'dashboard';
    flash('error', 'Halaman tidak ditemukan.');
}
require __DIR__ . '/layouts/header.php';
require __DIR__ . '/layouts/sidebar.php';
require __DIR__ . '/layouts/topbar.php';
require $routes[$page];
require __DIR__ . '/layouts/footer.php';
