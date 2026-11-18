<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/koneksi.php';
$base = '../';
wajib_login($base);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}
csrf_wajib();

$id = (int)($_POST['id'] ?? 0);
$stmt = $koneksi->prepare('DELETE FROM produk WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$terhapus = $stmt->affected_rows > 0;
$stmt->close();

if ($terhapus) {
    flash_set('success', 'Barang berhasil dihapus.');
} else {
    flash_set('danger', 'Barang tidak ditemukan atau sudah dihapus.');
}
redirect('index.php');
