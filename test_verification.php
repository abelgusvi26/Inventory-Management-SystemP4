<?php
/**
 * Test & Verification Suite untuk Inventory Management System
 * Menguji seluruh aturan bisnis, autentikasi, otorisasi, dan transaksi atomik.
 */

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/Helpers/Sanitize.php';
require_once __DIR__ . '/app/Helpers/Validator.php';
require_once __DIR__ . '/app/Helpers/Auth.php';
require_once __DIR__ . '/app/Models/User.php';
require_once __DIR__ . '/app/Models/Category.php';
require_once __DIR__ . '/app/Models/Supplier.php';
require_once __DIR__ . '/app/Models/Product.php';
require_once __DIR__ . '/app/Models/ProductSupplier.php';
require_once __DIR__ . '/app/Models/StockMovement.php';
require_once __DIR__ . '/app/Models/Report.php';

$passed = 0;
$failed = 0;

function assertTest($name, $condition, $details = '') {
    global $passed, $failed;
    if ($condition) {
        echo "  [PASS] {$name}" . ($details ? " ({$details})" : '') . PHP_EOL;
        $passed++;
    } else {
        echo "  [FAIL] {$name}" . ($details ? " - {$details}" : '') . PHP_EOL;
        $failed++;
    }
}

echo "=== MEMULAI TEST VERIFIKASI SISTEM INVENTARIS ===" . PHP_EOL . PHP_EOL;

// 1. Verifikasi Database
echo "1. Pengujian Koneksi Database & Tabel Relasional:" . PHP_EOL;
$pdo = Database::getConnection();
assertTest("Koneksi database PDO", $pdo instanceof PDO);
$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
$expectedTables = ['users', 'user_profiles', 'categories', 'suppliers', 'products', 'product_suppliers', 'stock_movements'];
foreach ($expectedTables as $t) {
    assertTest("Tabel '{$t}' tersedia", in_array($t, $tables, true));
}

// 2. Verifikasi Autentikasi
echo PHP_EOL . "2. Pengujian Autentikasi & Password Hash:" . PHP_EOL;
$adminUser = User::findByUsername('admin');
assertTest("Akun admin ditemukan", !empty($adminUser));
assertTest("Password admin 'admin123' valid", password_verify('admin123', $adminUser['password_hash']));
assertTest("Password admin salah ditolak", !password_verify('wrongpassword', $adminUser['password_hash']));

$staffUser = User::findByUsername('staff');
assertTest("Akun staff ditemukan", !empty($staffUser));
assertTest("Password staff 'staff123' valid", password_verify('staff123', $staffUser['password_hash']));
assertTest("Role staff sesuai", $staffUser['role'] === 'staff');

// 3. Verifikasi Relasi Many-to-Many Produk & Supplier
echo PHP_EOL . "3. Pengujian Relasi Many-to-Many (Product-Supplier):" . PHP_EOL;
$product1Suppliers = ProductSupplier::getByProductId(1);
assertTest("Produk 1 terhubung ke minimal 1 supplier", count($product1Suppliers) >= 1);
$supp1Products = Supplier::getSuppliedProducts(1);
assertTest("Supplier 1 menyuplai produk", count($supp1Products) >= 1);

// 4. Verifikasi Aturan Mutasi Stok & Transaksi Atomik
echo PHP_EOL . "4. Pengujian Transaksi Stok & Formula Mutasi (PRD Aturan 3 & 4):" . PHP_EOL;
$prod = Product::findById(1);
$initialStock = (int) $prod['stock'];

// Uji Barang Masuk (IN)
$inResult = StockMovement::record(1, 1, 'IN', 10, 'TEST-IN-01', 'Uji coba penambahan stok');
assertTest("Transaksi Barang Masuk berhasil", $inResult['success'] === true);
$prodAfterIn = Product::findById(1);
assertTest("Stok bertambah tepat (+10)", (int)$prodAfterIn['stock'] === ($initialStock + 10));

// Uji Barang Keluar (OUT)
$outResult = StockMovement::record(1, 1, 'OUT', 4, 'TEST-OUT-01', 'Uji coba pengeluaran stok');
assertTest("Transaksi Barang Keluar berhasil", $outResult['success'] === true);
$prodAfterOut = Product::findById(1);
assertTest("Stok berkurang tepat (-4)", (int)$prodAfterOut['stock'] === ($initialStock + 6));

// Uji Pencegahan Stok Negatif (Permintaan keluar melebihi stok)
$excessiveQty = (int)$prodAfterOut['stock'] + 1000;
$failOutResult = StockMovement::record(1, 1, 'OUT', $excessiveQty, 'TEST-OVER-01', 'Pengeluaran melebihi stok');
assertTest("Pengeluaran melebihi stok DITOLAK (Proteksi Stok Negatif)", $failOutResult['success'] === false);
$prodAfterFail = Product::findById(1);
assertTest("Stok tidak berubah setelah transaksi gagal", (int)$prodAfterFail['stock'] === (int)$prodAfterOut['stock']);

// 5. Verifikasi Integritas Referensial (RESTRICT)
echo PHP_EOL . "5. Pengujian Integritas Referensial (Foreign Key RESTRICT):" . PHP_EOL;
$canDeleteProd = Product::delete(1);
assertTest("Produk dengan histori transaksi DILARANG dihapus (RESTRICT)", $canDeleteProd === false);

$canDeleteCat = Category::delete(1);
assertTest("Kategori dengan produk aktif DILARANG dihapus (RESTRICT)", $canDeleteCat === false);

// 6. Verifikasi Listing, Filter, Sorting Allowlist
echo PHP_EOL . "6. Pengujian Listing Produk, Pencarian, & Allowlist Sorting:" . PHP_EOL;
$searchRes = Product::getPaginated('Samsung', null, 'price', 'DESC', 1, 10);
assertTest("Pencarian nama 'Samsung' menemukan data", count($searchRes['data']) > 0);

// Sorting dengan parameter ilegal / SQL injection attempt
$safeSortRes = Product::getPaginated('', null, "price; DROP TABLE dummy--", 'DESC', 1, 10);
assertTest("Sorting allowlist menetralkan input berbahaya", $safeSortRes['sort_by'] === 'created_at');

// 7. Verifikasi Laporan & Agregasi
echo PHP_EOL . "7. Pengujian Statistik Agregat & Dashboard Summary:" . PHP_EOL;
$summary = Report::getDashboardSummary();
assertTest("Total produk > 0", $summary['total_products'] > 0);
assertTest("Nilai inventaris terhitung", $summary['total_inventory_value'] > 0);
assertTest("Akumulasi barang masuk > 0", $summary['total_stock_in'] > 0);
assertTest("Akumulasi barang keluar > 0", $summary['total_stock_out'] > 0);

$report = Report::getAggregateInventoryReport();
assertTest("Laporan agregat mengembalikan baris produk", count($report) > 0);

echo PHP_EOL . "=== RINGKASAN HASIL TEST ===" . PHP_EOL;
echo "Lolos (PASSED): {$passed}" . PHP_EOL;
echo "Gagal (FAILED): {$failed}" . PHP_EOL;

if ($failed === 0) {
    echo "STATUS: SEMUA PENGUJIAN BERHASIL SEMPURNA (100% PASS)!" . PHP_EOL;
    exit(0);
} else {
    echo "STATUS: TERDAPAT PENGUJIAN YANG GAGAL." . PHP_EOL;
    exit(1);
}
