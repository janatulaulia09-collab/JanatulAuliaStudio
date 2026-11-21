<?php
declare(strict_types=1);
require_once __DIR__ . '/database.php';

function e($value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function csrf_token(): string
{
    return $_SESSION['csrf_token'] ?? '';
}

function verify_csrf(?string $token): bool
{
    return is_string($token) && $token !== '' && hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

function app_base_path(): string
{
    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    if (preg_match('#^(.+?)/(?:api|auth|modules)/#', $script, $matches)) {
        return rtrim($matches[1], '/');
    }
    $directory = rtrim(str_replace('\\', '/', dirname($script)), '/');
    return $directory === '.' ? '' : $directory;
}

function app_url(string $path = ''): string
{
    $base = rtrim(app_base_path(), '/');
    $path = ltrim($path, '/');
    return $base . ($path !== '' ? '/' . $path : '/');
}

function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}

function flash(string $key, ?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }
    $value = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $value;
}

function require_post(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        http_response_code(405);
        header('Allow: POST');
        exit('Metode tidak diizinkan.');
    }
}

function verify_post_csrf(): void
{
    require_post();
    if (!verify_csrf($_POST['csrf_token'] ?? null)) {
        http_response_code(419);
        exit('Token keamanan tidak valid atau sesi telah berakhir. Muat ulang halaman.');
    }
}

function slugify(string $value): string
{
    $value = trim(mb_strtolower($value));
    $value = preg_replace('/[^a-z0-9]+/u', '-', $value) ?? '';
    return trim($value, '-') ?: 'item';
}

function safe_http_url(string $url): bool
{
    if (!filter_var($url, FILTER_VALIDATE_URL)) return false;
    return in_array(strtolower((string)parse_url($url, PHP_URL_SCHEME)), ['http', 'https'], true);
}
