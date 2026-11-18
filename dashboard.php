<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/koneksi.php';
$base = '';
wajib_login($base);

$batas = BATAS_STOK_MENIPIS;

$totalJenis    = (int)ambil_satu($koneksi, 'SELECT COUNT(*) FROM produk');
$totalMenipis  = (int)ambil_satu($koneksi, 'SELECT COUNT(*) FROM produk WHERE stok < ?', 'i', [$batas]);
$totalKategori = (int)ambil_satu($koneksi, 'SELECT COUNT(*) FROM kategori');
$totalUnit     = (int)ambil_satu($koneksi, 'SELECT COALESCE(SUM(stok),0) FROM produk');
$nilaiModal    = (float)ambil_satu($koneksi, 'SELECT COALESCE(SUM(stok * harga_beli),0) FROM produk');
$nilaiJual     = (float)ambil_satu($koneksi, 'SELECT COALESCE(SUM(stok * harga_jual),0) FROM produk');

// 8 barang dengan stok paling sedikit (di bawah batas)
$stmt = $koneksi->prepare(
    'SELECT p.id, p.kode_barang, p.nama_barang, p.satuan, p.stok, k.nama_kategori
     FROM produk p JOIN kategori k ON k.id = p.kategori_id
     WHERE p.stok < ? ORDER BY p.stok ASC, p.nama_barang ASC LIMIT 8'
);
$stmt->bind_param('i', $batas);
$stmt->execute();
$listMenipis = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Ringkasan per kategori
$perKategori = $koneksi->query(
    'SELECT k.nama_kategori, COUNT(p.id) AS jenis, COALESCE(SUM(p.stok),0) AS total_stok
     FROM kategori k LEFT JOIN produk p ON p.kategori_id = k.id
     GROUP BY k.id, k.nama_kategori ORDER BY k.nama_kategori'
)->fetch_all(MYSQLI_ASSOC);

$judul = 'Dashboard';
$menu  = 'dashboard';
require __DIR__ . '/includes/header.php';
?>
<h1 class="h4 mb-4">Dashboard Ringkasan</h1>

<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card card-stat shadow-sm h-100"><div class="card-body">
            <div class="text-muted small">Total Jenis Barang</div>
            <div class="angka"><?= number_format($totalJenis, 0, ',', '.') ?></div>
        </div></div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card card-stat bahaya shadow-sm h-100"><div class="card-body">
            <div class="text-muted small">Stok Menipis (&lt; <?= $batas ?>)</div>
            <div class="angka text-danger"><?= number_format($totalMenipis, 0, ',', '.') ?></div>
        </div></div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card card-stat info shadow-sm h-100"><div class="card-body">
            <div class="text-muted small">Total Kategori</div>
            <div class="angka"><?= number_format($totalKategori, 0, ',', '.') ?></div>
        </div></div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card card-stat kuning shadow-sm h-100"><div class="card-body">
            <div class="text-muted small">Total Unit Stok</div>
            <div class="angka"><?= number_format($totalUnit, 0, ',', '.') ?></div>
        </div></div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card shadow-sm h-100"><div class="card-body">
            <div class="text-muted small">Nilai Stok (harga beli)</div>
            <div class="fs-4 fw-semibold"><?= rupiah($nilaiModal) ?></div>
        </div></div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm h-100"><div class="card-body">
            <div class="text-muted small">Potensi Penjualan (harga jual)</div>
            <div class="fs-4 fw-semibold"><?= rupiah($nilaiJual) ?></div>
            <div class="small text-success">Potensi laba kotor: <?= rupiah($nilaiJual - $nilaiModal) ?></div>
        </div></div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-semibold">Barang Perlu Segera Dibelanjakan</div>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>Kode</th><th>Nama Barang</th><th>Kategori</th><th class="text-end">Stok</th></tr>
                    </thead>
                    <tbody>
                    <?php if (!$listMenipis): ?>
                        <tr><td colspan="4" class="text-center text-muted py-3">Semua stok aman.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($listMenipis as $b): ?>
                        <tr class="<?= (int)$b['stok'] === 0 ? 'table-danger' : 'table-warning' ?>">
                            <td><?= e($b['kode_barang']) ?></td>
                            <td><?= e($b['nama_barang']) ?></td>
                            <td><?= e($b['nama_kategori']) ?></td>
                            <td class="text-end fw-semibold"><?= (int)$b['stok'] ?> <?= e($b['satuan']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white text-end">
                <a href="produk/index.php" class="btn btn-sm btn-outline-success">Kelola Produk</a>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-semibold">Ringkasan per Kategori</div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr><th>Kategori</th><th class="text-end">Jenis</th><th class="text-end">Total Stok</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($perKategori as $k): ?>
                        <tr>
                            <td><?= e($k['nama_kategori']) ?></td>
                            <td class="text-end"><?= (int)$k['jenis'] ?></td>
                            <td class="text-end"><?= number_format((int)$k['total_stok'], 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
