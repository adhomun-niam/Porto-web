<?php
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
        'secure'   => !empty($_SERVER['HTTPS']),
    ]);
    session_start();
}
require_once __DIR__ . '/fungsi.php';

/** Lindungi halaman admin. $base = path relatif ke folder root ('' atau '../'). */
function wajib_login(string $base = ''): void
{
    if (empty($_SESSION['admin_id'])) {
        redirect($base . 'login.php');
    }
}
