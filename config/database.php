<?php
declare(strict_types=1);

$config = require __DIR__ . '/env.php';
$isProduction = ($config['APP_ENV'] ?? 'development') === 'production';

ini_set('display_errors', $isProduction ? '0' : '1');
ini_set('display_startup_errors', $isProduction ? '0' : '1');
error_reporting(E_ALL);
if ($isProduction) {
    ini_set('log_errors', '1');
    ini_set('error_log', __DIR__ . '/../logs/error.log');
}

date_default_timezone_set($config['TIMEZONE'] ?? 'Asia/Jakarta');

if (session_status() !== PHP_SESSION_ACTIVE) {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

try {
    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
        $config['DB_HOST'] ?? '127.0.0.1',
        $config['DB_PORT'] ?? '3306',
        $config['DB_NAME'] ?? ''
    );
    $pdo = new PDO($dsn, $config['DB_USER'] ?? '', $config['DB_PASS'] ?? '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    $pdo->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
} catch (PDOException $exception) {
    error_log('[DB] ' . $exception->getMessage());
    http_response_code(500);
    if (!$isProduction) {
        exit('Koneksi database gagal. Periksa config/env.php, pastikan MySQL aktif, dan database janatulaulia_web sudah diimpor.');
    }
    exit('Layanan sedang mengalami gangguan. Silakan coba kembali nanti.');
}
