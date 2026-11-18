<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/koneksi.php';
$base = '../';
wajib_login($base);

$id = (int)($_GET['id'] ?? 0);

$stmt = $koneksi->prepare('SELECT id, nama_kategori, keterangan FROM kategori WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$row) {
    flash_set('danger', 'Kategori tidak ditemukan.');
    redirect('index.php');
}

$errors = [];
$nama = $row['nama_kategori'];
$ket  = (string)$row['keterangan'];

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
            $stmt = $koneksi->prepare('UPDATE kategori SET nama_kategori = ?, keterangan = ? WHERE id = ?');
            $stmt->bind_param('ssi', $nama, $ket, $id);
            $stmt->execute();
            $stmt->close();
            flash_set('success', 'Kategori berhasil diperbarui.');
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

$judul = 'Edit Kategori';
$menu  = 'kategori';
require __DIR__ . '/../includes/header.php';
?>
<h1 class="h4 mb-3">Edit Kategori</h1>
<div class="card shadow-sm" style="max-width:560px"><div class="card-body">
    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <ul class="mb-0"><?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul>
        </div>
    <?php endif; ?>
    <form method="post" action="">
        <?= csrf_field() ?>
        <div class="mb-3">
            <label for="nama_kategori" class="form-label">Nama Kategori</label>
            <input type="text" class="form-control" id="nama_kategori" name="nama_kategori"
                   maxlength="50" value="<?= e($nama) ?>" required>
        </div>
        <div class="mb-3">
            <label for="keterangan" class="form-label">Keterangan</label>
            <textarea class="form-control" id="keterangan" name="keterangan" rows="3" maxlength="255"><?= e($ket) ?></textarea>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-success">Simpan Perubahan</button>
            <a href="index.php" class="btn btn-outline-secondary">Batal</a>
        </div>
    </form>
</div></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
