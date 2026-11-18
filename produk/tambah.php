<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/koneksi.php';
$base = '../';
wajib_login($base);

$kategori = $koneksi->query('SELECT id, nama_kategori FROM kategori ORDER BY nama_kategori')->fetch_all(MYSQLI_ASSOC);

$errors = [];
$data = ['kode_barang' => '', 'nama_barang' => '', 'kategori_id' => '', 'satuan' => 'pcs',
         'harga_beli' => '', 'harga_jual' => '', 'stok' => '0'];

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
                'INSERT INTO produk (kode_barang, nama_barang, kategori_id, satuan, harga_beli, harga_jual, stok)
                 VALUES (?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->bind_param('ssisiii', $kode, $nama, $kat, $sat, $hb, $hj, $stok);
            $stmt->execute();
            $stmt->close();
            flash_set('success', 'Barang "' . $nama . '" berhasil ditambahkan.');
            redirect('index.php');
        } catch (mysqli_sql_exception $ex) {
            if ($ex->getCode() === 1062) {
                $errors[] = 'Kode barang "' . $kode . '" sudah digunakan.';
            } elseif ($ex->getCode() === 1452) {
                $errors[] = 'Kategori yang dipilih tidak valid.';
            } else {
                throw $ex;
            }
        }
    }
}

$judul = 'Tambah Barang';
$menu  = 'produk';
$tombol = 'Simpan';
require __DIR__ . '/../includes/header.php';
?>
<h1 class="h4 mb-3">Tambah Barang Baru</h1>
<div class="card shadow-sm"><div class="card-body">
    <?php require __DIR__ . '/_form.php'; ?>
</div></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
