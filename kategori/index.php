<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/koneksi.php';
$base = '../';
wajib_login($base);

$errors = [];
$nama = '';
$ket = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_wajib();
    $nama = trim($_POST['nama_kategori'] ?? '');
    $ket  = trim($_POST['keterangan'] ?? '');

    if ($nama === '' || mb_strlen($nama) > 50) {
        $errors[] = 'Nama kategori wajib diisi (maks. 50 karakter).';
    }
    if (mb_strlen($ket) > 255) {
        $errors[] = 'Keterangan maksimal 255 karakter.';
    }

    if (!$errors) {
        try {
            $stmt = $koneksi->prepare('INSERT INTO kategori (nama_kategori, keterangan) VALUES (?, ?)');
            $stmt->bind_param('ss', $nama, $ket);
            $stmt->execute();
            $stmt->close();
            flash_set('success', 'Kategori "' . $nama . '" berhasil ditambahkan.');
            redirect('index.php');
        } catch (mysqli_sql_exception $ex) {
            if ($ex->getCode() === 1062) {
                $errors[] = 'Nama kategori "' . $nama . '" sudah ada.';
            } else {
                throw $ex;
            }
        }
    }
}

$daftar = $koneksi->query(
    'SELECT k.id, k.nama_kategori, k.keterangan, COUNT(p.id) AS jumlah_produk
     FROM kategori k LEFT JOIN produk p ON p.kategori_id = k.id
     GROUP BY k.id, k.nama_kategori, k.keterangan
     ORDER BY k.nama_kategori'
)->fetch_all(MYSQLI_ASSOC);

$judul = 'Kategori Barang';
$menu  = 'kategori';
require __DIR__ . '/../includes/header.php';
?>
<h1 class="h4 mb-3">Kategori Barang</h1>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-semibold">Tambah Kategori</div>
            <div class="card-body">
                <?php if ($errors): ?>
                    <div class="alert alert-danger py-2">
                        <ul class="mb-0 ps-3"><?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul>
                    </div>
                <?php endif; ?>
                <form method="post" action="index.php">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="nama_kategori" class="form-label">Nama Kategori</label>
                        <input type="text" class="form-control" id="nama_kategori" name="nama_kategori"
                               maxlength="50" value="<?= e($nama) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan (opsional)</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="2"
                                  maxlength="255"><?= e($ket) ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Simpan</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>#</th><th>Kategori</th><th>Keterangan</th><th class="text-end">Jumlah Barang</th><th class="text-center">Aksi</th></tr>
                    </thead>
                    <tbody>
                    <?php if (!$daftar): ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">Belum ada kategori.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($daftar as $i => $k): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td class="fw-semibold"><?= e($k['nama_kategori']) ?></td>
                            <td class="text-muted small"><?= e($k['keterangan']) ?></td>
                            <td class="text-end"><?= (int)$k['jumlah_produk'] ?></td>
                            <td class="text-center text-nowrap">
                                <a href="edit.php?id=<?= (int)$k['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form method="post" action="hapus.php" class="d-inline"
                                      data-pesan="Hapus kategori &quot;<?= e($k['nama_kategori']) ?>&quot;?"
                                      onsubmit="return confirm(this.dataset.pesan)">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= (int)$k['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="form-text mt-2">Kategori yang masih dipakai barang tidak dapat dihapus.</div>
    </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
