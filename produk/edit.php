<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/koneksi.php';
$base = '../';
wajib_login($base);

$id = (int)($_GET['id'] ?? 0);

$stmt = $koneksi->prepare('SELECT * FROM produk WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$row) {
    flash_set('danger', 'Barang tidak ditemukan.');
    redirect('index.php');
}

$kategori = $koneksi->query('SELECT id, nama_kategori FROM kategori ORDER BY nama_kategori')->fetch_all(MYSQLI_ASSOC);

$errors = [];
$data = [
    'kode_barang' => $row['kode_barang'],
    'nama_barang' => $row['nama_barang'],
    'kategori_id' => (string)$row['kategori_id'],
    'satuan'      => $row['satuan'],
    'harga_beli'  => (string)$row['harga_beli'],
    'harga_jual'  => (string)$row['harga_jual'],
    'stok'        => (string)$row['stok'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_wajib();
    [$errors, $data] = validasi_produk($_POST);

    if (!$errors) {
        $kode = $data['kode_barang'];
        $nama = $data['nama_barang'];
        $kat  = (int)$data['kategori_id'];
        $sat  = $data['satuan'];
        $hb   = (int)$data['harga_beli'];
        $hj   = (int)$data['harga_jual'];
        $stok = (int)$data['stok'];
        try {
            $stmt = $koneksi->prepare(
                'UPDATE produk SET kode_barang = ?, nama_barang = ?, kategori_id = ?, satuan = ?,
                        harga_beli = ?, harga_jual = ?, stok = ?
                 WHERE id = ?'
            );
            $stmt->bind_param('ssisiiii', $kode, $nama, $kat, $sat, $hb, $hj, $stok, $id);
            $stmt->execute();
            $stmt->close();
            flash_set('success', 'Barang "' . $nama . '" berhasil diperbarui.');
            redirect('index.php');
        } catch (mysqli_sql_exception $ex) {
            if ($ex->getCode() === 1062) {
                $errors[] = 'Kode barang "' . $kode . '" sudah digunakan barang lain.';
            } elseif ($ex->getCode() === 1452) {
                $errors[] = 'Kategori yang dipilih tidak valid.';
            } else {
                throw $ex;
            }
        }
    }
}

$judul = 'Edit Barang';
$menu  = 'produk';
$tombol = 'Simpan Perubahan';
require __DIR__ . '/../includes/header.php';
?>
<h1 class="h4 mb-3">Edit Barang</h1>
<div class="card shadow-sm"><div class="card-body">
    <?php require __DIR__ . '/_form.php'; ?>
</div></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
