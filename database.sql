-- ============================================================
-- Database  : Toko Madura Mart
-- Cara pakai: buat database kosong di cPanel > MySQL Databases,
--             pilih database tsb di phpMyAdmin, lalu Import file ini.
-- Login awal: username  admin  |  password  password   (SEGERA GANTI!)
-- ============================================================
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS produk;
DROP TABLE IF EXISTS kategori;
DROP TABLE IF EXISTS admin;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE admin (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  username      VARCHAR(50)  NOT NULL,
  password      VARCHAR(255) NOT NULL,
  nama_lengkap  VARCHAR(100) NOT NULL,
  dibuat_pada   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uk_admin_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE kategori (
  id             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nama_kategori  VARCHAR(50)  NOT NULL,
  keterangan     VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uk_kategori_nama (nama_kategori)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE produk (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  kode_barang   VARCHAR(20)  NOT NULL,
  nama_barang   VARCHAR(100) NOT NULL,
  kategori_id   INT UNSIGNED NOT NULL,
  satuan        ENUM('pcs','kg','liter','renteng') NOT NULL DEFAULT 'pcs',
  harga_beli    INT UNSIGNED NOT NULL DEFAULT 0,
  harga_jual    INT UNSIGNED NOT NULL DEFAULT 0,
  stok          INT UNSIGNED NOT NULL DEFAULT 0,
  dibuat_pada   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  diubah_pada   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uk_produk_kode (kode_barang),
  KEY idx_produk_nama (nama_barang),
  KEY idx_produk_kategori (kategori_id),
  CONSTRAINT fk_produk_kategori FOREIGN KEY (kategori_id)
    REFERENCES kategori (id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Admin awal (password: "password", disimpan sebagai hash bcrypt)
INSERT INTO admin (username, password, nama_lengkap) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator');

INSERT INTO kategori (id, nama_kategori, keterangan) VALUES
(1, 'Sembako',            'Kebutuhan pokok sehari-hari'),
(2, 'Minuman',            'Air mineral, teh, kopi, dan susu'),
(3, 'Makanan Ringan',     'Snack, biskuit, dan wafer'),
(4, 'Kebutuhan Mandi',    'Sabun, sampo, dan perlengkapan mandi'),
(5, 'Bumbu Dapur',        'Bumbu dan penyedap masakan');

INSERT INTO produk (kode_barang, nama_barang, kategori_id, satuan, harga_beli, harga_jual, stok) VALUES
('SMB-001', 'Beras Premium 5 Kg',       1, 'pcs',     62000, 68000, 24),
('SMB-002', 'Gula Pasir',               1, 'kg',      16000, 18000, 30),
('SMB-003', 'Minyak Goreng Curah',      1, 'liter',   15000, 17000,  3),
('SMB-004', 'Telur Ayam',               1, 'kg',      26000, 29000, 12),
('SMB-005', 'Tepung Terigu',            1, 'kg',      11000, 13000,  4),
('SMB-006', 'Mi Instan Goreng',         1, 'pcs',      2800,  3500, 80),
('MNM-001', 'Air Mineral 600 ml',       2, 'pcs',      2200,  3000, 48),
('MNM-002', 'Teh Botol 450 ml',         2, 'pcs',      3500,  5000,  2),
('MNM-003', 'Kopi Sachet (10 sachet)',  2, 'renteng',  9500, 12000, 15),
('MNM-004', 'Susu Kental Manis',        2, 'pcs',     10500, 12500,  0),
('MKN-001', 'Keripik Singkong',         3, 'pcs',      5500,  7500, 18),
('MKN-002', 'Biskuit Kelapa',           3, 'pcs',      7000,  9000,  9),
('MKN-003', 'Wafer Cokelat (10 pcs)',   3, 'renteng', 11000, 14000,  6),
('KMD-001', 'Sabun Mandi Batang',       4, 'pcs',      3000,  4000, 25),
('KMD-002', 'Sampo Sachet (12 sachet)', 4, 'renteng',  9000, 12000,  4),
('KMD-003', 'Pasta Gigi 75 g',          4, 'pcs',      8500, 11000, 14),
('BDP-001', 'Garam Dapur 250 g',        5, 'pcs',      2000,  3000, 40),
('BDP-002', 'Kecap Manis Sachet',       5, 'renteng',  8000, 10500, 11),
('BDP-003', 'Bawang Putih',             5, 'kg',      32000, 38000,  5),
('BDP-004', 'Bawang Merah',             5, 'kg',      30000, 36000,  7);
