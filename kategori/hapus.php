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

$dipakai = (int)ambil_satu($koneksi, 'SELECT COUNT(*) FROM produk WHERE kategori_id = ?', 'i', [$id]);
if ($dipakai > 0) {
    flash_set('danger', "Kategori tidak dapat dihapus karena masih dipakai oleh $dipakai barang. Pindahkan atau hapus barangnya terlebih dahulu.");
    redirect('index.php');
}

try {
    $stmt = $koneksi->prepare('DELETE FROM kategori WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $terhapus = $stmt->affected_rows > 0;
    $stmt->close();
    flash_set($terhapus ? 'success' : 'danger', $terhapus ? 'Kategori berhasil dihapus.' : 'Kategori tidak ditemukan.');
} catch (mysqli_sql_exception $ex) {
    if ($ex->getCode() === 1451) {
        flash_set('danger', 'Kategori masih dipakai oleh data barang.');
    } else {
        throw $ex;
    }
}
redirect('index.php');
