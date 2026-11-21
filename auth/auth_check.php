<?php
require_once __DIR__ . '/../config/bootstrap.php';

function require_admin(): void
{
    if (empty($_SESSION['user']['id'])) {
        flash('error', 'Silakan masuk terlebih dahulu.');
        redirect(app_url('auth/login.php'));
    }
}

function require_super_admin(): void
{
    require_admin();
    if (($_SESSION['user']['role'] ?? '') !== 'super_admin') {
        http_response_code(403);
        exit('Akses ditolak.');
    }
}
