<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/koneksi.php';
$base = '../';
wajib_login($base);

$q = trim($_GET['q'] ?? '');
$batas = BATAS_STOK_MENIPIS;

$sql = 'SELECT p.*, k.nama_kategori
        FROM produk p JOIN kategori k ON k.id = p.kategori_id';
if ($q !== '') {
    $sql .= ' WHERE p.nama_barang LIKE ? OR p.kode_barang LIKE ?';
}
$sql .= ' ORDER BY p.kode_barang ASC';

$stmt = $koneksi->prepare($sql);
if ($q !== '') {
    $like = '%' . $q . '%';
    $stmt->bind_param('ss', $like, $like);
}
$stmt->execute();
$produk = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$judul = 'Data Produk';
$menu  = 'produk';
require __DIR__ . '/../includes/header.php';
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h1 class="h4 mb-0">Data Produk</h1>
    <a href="tambah.php" class="btn btn-success">+ Tambah Barang</a>
</div>

<form method="get" action="index.php" class="row g-2 mb-3">
    <div class="col-sm-8 col-md-6">
        <input type="search" name="q" class="form-control" placeholder="Cari nama atau kode barang..."
               value="<?= e($q) ?>">
    </div>
    <div class="col-auto">
        <button class="btn btn-outline-success" type="submit">Cari</button>
        <?php if ($q !== ''): ?><a href="index.php" class="btn btn-outline-secondary">Reset</a><?php endif; ?>
    </div>
</form>

<div class="small text-muted mb-2">
    <?= count($produk) ?> barang ditemukan.
    <span class="badge text-bg-warning">Menipis (&lt; <?= $batas ?>)</span>
    <span class="badge text-bg-danger">Habis</span>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Kode</th><th>Nama Barang</th><th>Kategori</th><th>Satuan</th>
                    <th class="text-end">Harga Beli</th><th class="text-end">Harga Jual</th>
                    <th class="text-end">Stok</th><th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!$produk): ?>
                <tr><td colspan="8" class="text-center text-muted py-4">Belum ada data barang.</td></tr>
            <?php endif; ?>
            <?php foreach ($produk as $p):
                $stok = (int)$p['stok'];
                $kelas = $stok === 0 ? 'table-danger' : ($stok < $batas ? 'table-warning' : '');
            ?>
                <tr class="<?= $kelas ?>">
                    <td class="fw-semibold"><?= e($p['kode_barang']) ?></td>
                    <td><?= e($p['nama_barang']) ?></td>
                    <td><?= e($p['nama_kategori']) ?></td>
                    <td><?= e($p['satuan']) ?></td>
                    <td class="text-end"><?= rupiah($p['harga_beli']) ?></td>
                    <td class="text-end"><?= rupiah($p['harga_jual']) ?></td>
                    <td class="text-end">
                        <?= $stok ?>
                        <?php if ($stok === 0): ?><span class="badge text-bg-danger">Habis</span>
                        <?php elseif ($stok < $batas): ?><span class="badge text-bg-warning">Menipis</span><?php endif; ?>
                    </td>
                    <td class="text-center text-nowrap">
                        <a href="edit.php?id=<?= (int)$p['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form method="post" action="hapus.php" class="d-inline"
                              data-pesan="Hapus barang &quot;<?= e($p['nama_barang']) ?>&quot;? Tindakan ini tidak dapat dibatalkan."
                              onsubmit="return confirm(this.dataset.pesan)">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
