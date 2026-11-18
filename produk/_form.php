<?php
/* Partial form produk. Variabel: $data, $kategori, $errors, $tombol */
?>
<?php if ($errors): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post" action="" novalidate>
    <?= csrf_field() ?>
    <div class="row g-3">
        <div class="col-md-4">
            <label for="kode_barang" class="form-label">Kode Barang</label>
            <input type="text" class="form-control" id="kode_barang" name="kode_barang" maxlength="20"
                   value="<?= e($data['kode_barang']) ?>" placeholder="mis. SMB-001" required>
        </div>
        <div class="col-md-8">
            <label for="nama_barang" class="form-label">Nama Barang</label>
            <input type="text" class="form-control" id="nama_barang" name="nama_barang" maxlength="100"
                   value="<?= e($data['nama_barang']) ?>" required>
        </div>
        <div class="col-md-6">
            <label for="kategori_id" class="form-label">Kategori</label>
            <select class="form-select" id="kategori_id" name="kategori_id" required>
                <option value="">-- Pilih kategori --</option>
                <?php foreach ($kategori as $k): ?>
                    <option value="<?= (int)$k['id'] ?>" <?= (string)$data['kategori_id'] === (string)$k['id'] ? 'selected' : '' ?>>
                        <?= e($k['nama_kategori']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label for="satuan" class="form-label">Satuan</label>
            <select class="form-select" id="satuan" name="satuan" required>
                <?php foreach (DAFTAR_SATUAN as $s): ?>
                    <option value="<?= e($s) ?>" <?= $data['satuan'] === $s ? 'selected' : '' ?>><?= e($s) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label for="harga_beli" class="form-label">Harga Beli (Rp)</label>
            <input type="number" min="0" step="1" class="form-control" id="harga_beli" name="harga_beli"
                   value="<?= e($data['harga_beli']) ?>" required>
        </div>
        <div class="col-md-4">
            <label for="harga_jual" class="form-label">Harga Jual (Rp)</label>
            <input type="number" min="0" step="1" class="form-control" id="harga_jual" name="harga_jual"
                   value="<?= e($data['harga_jual']) ?>" required>
        </div>
        <div class="col-md-4">
            <label for="stok" class="form-label">Stok</label>
            <input type="number" min="0" step="1" class="form-control" id="stok" name="stok"
                   value="<?= e($data['stok']) ?>" required>
        </div>
    </div>
    <div class="mt-4 d-flex gap-2">
        <button type="submit" class="btn btn-success"><?= e($tombol) ?></button>
        <a href="index.php" class="btn btn-outline-secondary">Batal</a>
    </div>
</form>
