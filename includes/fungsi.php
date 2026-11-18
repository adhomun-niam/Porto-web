<?php
const BATAS_STOK_MENIPIS = 5; // stok di bawah angka ini dianggap menipis
const DAFTAR_SATUAN = ['pcs', 'kg', 'liter', 'renteng'];

function e($v): string
{
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

function rupiah($n): string
{
    return 'Rp ' . number_format((float)$n, 0, ',', '.');
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

/* ---------- CSRF ---------- */
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function csrf_wajib(): void
{
    $ok = isset($_POST['csrf'], $_SESSION['csrf']) && hash_equals($_SESSION['csrf'], (string)$_POST['csrf']);
    if (!$ok) {
        http_response_code(419);
        exit('Sesi formulir tidak valid. Silakan kembali dan muat ulang halaman.');
    }
}

/* ---------- Flash message ---------- */
function flash_set(string $tipe, string $pesan): void
{
    $_SESSION['flash'] = ['tipe' => $tipe, 'pesan' => $pesan];
}

function flash_ambil(): ?array
{
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}

/* ---------- Query helper (prepared statement, return 1 nilai) ---------- */
function ambil_satu(mysqli $db, string $sql, string $types = '', array $params = [])
{
    $stmt = $db->prepare($sql);
    if ($types !== '') {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $row = $stmt->get_result()->fetch_row();
    $stmt->close();
    return $row[0] ?? 0;
}

/* ---------- Validasi form produk ---------- */
function validasi_produk(array $in): array
{
    $data = [
        'kode_barang' => strtoupper(trim($in['kode_barang'] ?? '')),
        'nama_barang' => trim($in['nama_barang'] ?? ''),
        'kategori_id' => trim($in['kategori_id'] ?? ''),
        'satuan'      => trim($in['satuan'] ?? ''),
        'harga_beli'  => trim($in['harga_beli'] ?? ''),
        'harga_jual'  => trim($in['harga_jual'] ?? ''),
        'stok'        => trim($in['stok'] ?? ''),
    ];
    $err = [];

    if ($data['kode_barang'] === '' || strlen($data['kode_barang']) > 20
        || !preg_match('/^[A-Z0-9_-]+$/', $data['kode_barang'])) {
        $err[] = 'Kode barang wajib diisi (maks. 20 karakter; huruf, angka, strip, atau underscore).';
    }
    if ($data['nama_barang'] === '' || mb_strlen($data['nama_barang']) > 100) {
        $err[] = 'Nama barang wajib diisi (maks. 100 karakter).';
    }
    if (!ctype_digit($data['kategori_id']) || (int)$data['kategori_id'] < 1) {
        $err[] = 'Kategori wajib dipilih.';
    }
    if (!in_array($data['satuan'], DAFTAR_SATUAN, true)) {
        $err[] = 'Satuan tidak valid.';
    }
    foreach (['harga_beli' => 'Harga beli', 'harga_jual' => 'Harga jual', 'stok' => 'Stok'] as $k => $label) {
        if (!ctype_digit($data[$k]) || strlen($data[$k]) > 9) {
            $err[] = $label . ' harus berupa angka bulat 0 atau lebih.';
        }
    }
    if (!$err && (int)$data['harga_jual'] < (int)$data['harga_beli']) {
        $err[] = 'Harga jual tidak boleh lebih kecil dari harga beli.';
    }
    return [$err, $data];
}
