<?php
/* Variabel yang dibutuhkan: $base (''/'../'), $judul, $menu ('dashboard'|'produk'|'kategori') */
$flash = flash_ambil();
$aktif = fn(string $m): string => (($menu ?? '') === $m) ? ' active' : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($judul ?? 'Halaman') ?> - Toko Madura Mart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= $base ?>assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-madura shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= $base ?>dashboard.php">Toko Madura Mart</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuUtama"
                aria-controls="menuUtama" aria-expanded="false" aria-label="Buka menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menuUtama">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link<?= $aktif('dashboard') ?>" href="<?= $base ?>dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link<?= $aktif('produk') ?>" href="<?= $base ?>produk/index.php">Data Produk</a></li>
                <li class="nav-item"><a class="nav-link<?= $aktif('kategori') ?>" href="<?= $base ?>kategori/index.php">Kategori</a></li>
            </ul>
            <span class="navbar-text me-3 small">Halo, <?= e($_SESSION['admin_nama'] ?? 'Admin') ?></span>
            <a class="btn btn-outline-light btn-sm" href="<?= $base ?>logout.php">Logout</a>
        </div>
    </div>
</nav>

<main class="container py-4">
    <?php if ($flash): ?>
        <div class="alert alert-<?= e($flash['tipe']) ?> alert-dismissible fade show" role="alert">
            <?= e($flash['pesan']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    <?php endif; ?>
