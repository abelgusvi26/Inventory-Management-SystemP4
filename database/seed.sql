-- Data Awal (Seed Data) untuk Inventory Management System
USE inventory_db;

-- 1. Seeding Pengguna (Users & User Profiles)
-- Password admin: admin123
-- Password staff: staff123
INSERT INTO users (id, username, password_hash, role, created_at) VALUES
(1, 'admin', '$2y$10$wcLZWYS7xIULHYhIKnS9.uaYJNxOMF514w0ZjDzGN7zqw2kSxrjiW', 'admin', NOW()),
(2, 'staff', '$2y$10$JBveVxSVwzpLn8PxhrT1OOa6LwOhEuLb.6ZCjape9fanVyfZYei5e', 'staff', NOW())
ON DUPLICATE KEY UPDATE username=VALUES(username);

INSERT INTO user_profiles (id, user_id, full_name, phone, bio) VALUES
(1, 1, 'Administrator Gudang Utama', '081234567890', 'Penanggung jawab operasional sistem pergudangan pusat.'),
(2, 2, 'Staf Petugas Inventaris', '089876543210', 'Petugas pencatatan mutasi barang masuk dan keluar.')
ON DUPLICATE KEY UPDATE full_name=VALUES(full_name);

-- 2. Seeding Kategori (Categories)
INSERT INTO categories (id, name, description, created_at) VALUES
(1, 'Elektronik & Display', 'Perangkat komputer, monitor, dan display visual interaktif', NOW()),
(2, 'Aksesoris & Periferal', 'Keyboard, mouse, kabel adaptor, dan periferal kerja', NOW()),
(3, 'Peralatan Jaringan', 'Router, switch hub, kabel LAN, dan perangkat transmisi data', NOW()),
(4, 'Peralatan Penyimpanan', 'SSD eksternal, flash drive, dan rak storage inventaris', NOW())
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- 3. Seeding Supplier
INSERT INTO suppliers (id, name, contact_person, phone, email, address, created_at) VALUES
(1, 'PT Nusantara Digital Suplai', 'Budi Santoso', '021-5551234', 'sales@nusantaradigital.co.id', 'Kawasan Industri Pulogadung Blok B No. 12, Jakarta Timur', NOW()),
(2, 'CV Mega Pratama Tech', 'Siti Rahmawati', '031-7788990', 'halo@megapratamatech.com', 'Ruko Central Bisnis No. 8A, Surabaya', NOW()),
(3, 'PT Sinar Logistik Global', 'Agus Setiawan', '022-4433221', 'kontak@sinarlogistik.id', 'Jl. Soekarno Hatta No. 450, Bandung', NOW())
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- 4. Seeding Produk (Products)
-- Formula Mutasi Stok: stok akhir = stok awal + total masuk - total keluar
INSERT INTO products (id, category_id, sku, name, price, stock, minimum_stock, created_at) VALUES
(1, 1, 'PRD-MN-001', 'Samsung 24 Inch Full HD Curved Monitor', 1850000.00, 15, 5, NOW()),
(2, 2, 'PRD-KB-002', 'Mechanical Keyboard RGB Red Switch Tenkeyless', 450000.00, 8, 10, NOW()),
(3, 2, 'PRD-MS-003', 'Ergonomic Wireless Optical Mouse 2.4GHz', 175000.00, 24, 5, NOW()),
(4, 3, 'PRD-RT-004', 'Gigabit WiFi 6 Dual-Band Router AX3000', 620000.00, 4, 8, NOW()),
(5, 3, 'PRD-CB-005', 'Cat6 UTP Gigabit Network Cable 305m Roll', 750000.00, 12, 3, NOW()),
(6, 4, 'PRD-SS-006', 'High Speed NVMe M.2 SSD 1TB PCIe 4.0', 1150000.00, 0, 5, NOW())
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- 5. Seeding Many-to-Many Relasi (Product Suppliers)
INSERT INTO product_suppliers (product_id, supplier_id, supplier_price) VALUES
(1, 1, 1600000.00),
(1, 3, 1650000.00),
(2, 1, 380000.00),
(2, 2, 375000.00),
(3, 2, 140000.00),
(4, 1, 530000.00),
(4, 3, 520000.00),
(5, 3, 640000.00),
(6, 1, 980000.00),
(6, 2, 970000.00)
ON DUPLICATE KEY UPDATE supplier_price=VALUES(supplier_price);

-- 6. Seeding Histori Transaksi Mutasi Barang (Stock Movements)
-- Mencatat kronologi mutasi yang konsisten dengan angka stok saat ini
INSERT INTO stock_movements (id, product_id, user_id, type, quantity, reference_number, notes, created_at) VALUES
(1, 1, 1, 'IN', 20, 'BM-2026-001', 'Penerimaan batch awal dari supplier PT Nusantara Digital', '2026-10-01 09:00:00'),
(2, 1, 2, 'OUT', 5, 'BK-2026-001', 'Pengeluaran kebutuhan lab komputer divisi multimedia', '2026-10-02 11:30:00'),
(3, 2, 1, 'IN', 15, 'BM-2026-002', 'Pengadaan perlengkapan kantor baru', '2026-10-02 14:15:00'),
(4, 2, 2, 'OUT', 7, 'BK-2026-002', 'Distribusi unit ke workstation analis data', '2026-10-03 10:00:00'),
(5, 3, 1, 'IN', 30, 'BM-2026-003', 'Restock perlengkapan mouse nirkabel', '2026-10-03 13:45:00'),
(6, 3, 2, 'OUT', 6, 'BK-2026-003', 'Penggantian mouse staf administrasi', '2026-10-04 09:20:00'),
(7, 4, 1, 'IN', 10, 'BM-2026-004', 'Pengadaan infrastruktur jaringan kantor cabang', '2026-10-04 15:00:00'),
(8, 4, 2, 'OUT', 6, 'BK-2026-004', 'Instalasi jaringan lantai 2 dan ruang rapat utama', '2026-10-05 16:30:00'),
(9, 5, 1, 'IN', 15, 'BM-2026-005', 'Pembelian rol kabel LAN proyek upgrade jaringan', '2026-10-05 10:15:00'),
(10, 5, 2, 'OUT', 3, 'BK-2026-005', 'Penarikan kabel workstation lantai 1', '2026-10-06 14:00:00'),
(11, 6, 1, 'IN', 8, 'BM-2026-006', 'Stok masuk komponen storage server', '2026-10-01 10:00:00'),
(12, 6, 2, 'OUT', 8, 'BK-2026-006', 'Pemasangan upgrade drive server database (stok habis)', '2026-10-06 17:00:00')
ON DUPLICATE KEY UPDATE quantity=VALUES(quantity);
