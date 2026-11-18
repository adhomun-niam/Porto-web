<?php
/**
 * KONFIGURASI DATABASE - Toko Madura Mart
 * Ubah 4 nilai di bawah ini sesuai kredensial dari cPanel hosting Anda.
 * (cPanel > MySQL Databases: nama database & user biasanya berawalan "cpanelusername_")
 */
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'toko_madura_mart';

// Ubah ke true hanya saat debugging. WAJIB false saat sudah online.
const APP_DEBUG = false;

date_default_timezone_set('Asia/Jakarta');
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $koneksi = new mysqli($db_host, $db_user, $db_pass, $db_name);
    $koneksi->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    error_log('[Toko Madura Mart] Koneksi database gagal: ' . $e->getMessage());
    http_response_code(503);
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Layanan Tidak Tersedia</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="bg-light d-flex align-items-center" style="min-height:100vh">
        <div class="container text-center">
            <h1 class="h3 mb-3">Sistem sedang tidak dapat diakses</h1>
            <p class="text-muted">Koneksi ke database gagal. Silakan coba lagi beberapa saat lagi
                atau hubungi administrator.</p>
            <?php if (APP_DEBUG): ?>
                <pre class="text-start bg-white border rounded p-3 small"><?= htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') ?></pre>
            <?php endif; ?>
        </div>
    </body>
    </html>
    <?php
    exit;
}
