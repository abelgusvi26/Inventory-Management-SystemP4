<?php
/**
 * Skrip Migrasi & Seeder Otomatis Database Inventory Management System
 * Dapat dijalankan via CLI: php database/migrate.php
 * Atau diakses via browser untuk kemudahan evaluasi pengajar/asisten lab.
 */

// Konfigurasi koneksi MySQL XAMPP
$host = getenv('DB_HOST') ?: 'localhost';
$port = getenv('DB_PORT') ?: '3306';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$dbName = getenv('DB_NAME') ?: 'inventory_db';

$isCli = (php_sapi_name() === 'cli');

function logMessage($msg, $isCli) {
    if ($isCli) {
        echo "[MIGRATE] " . $msg . PHP_EOL;
    } else {
        echo "<p style='font-family: monospace; margin: 4px 0;'><strong>[MIGRATE]</strong> " . htmlspecialchars($msg) . "</p>";
    }
}

try {
    if (!$isCli) {
        echo "<!DOCTYPE html><html lang='id'><head><meta charset='utf-8'><title>Migrasi Database Inventory</title>";
        echo "<style>body { font-family: system-ui, sans-serif; background: #0f172a; color: #f8fafc; padding: 2rem; max-width: 800px; margin: 0 auto; line-height: 1.5; }</style></head><body>";
        echo "<h2>Menjalankan Migrasi Database Inventory</h2>";
    }

    logMessage("Menghubungkan ke server MySQL di {$host}:{$port}...", $isCli);
    $pdo = new PDO("mysql:host={$host};port={$port};charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    logMessage("Membuat database '{$dbName}' jika belum ada...", $isCli);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    $pdo->exec("USE `{$dbName}`;");

    // Jalankan schema.sql
    $schemaFile = __DIR__ . '/schema.sql';
    if (!file_exists($schemaFile)) {
        throw new Exception("File schema.sql tidak ditemukan di: {$schemaFile}");
    }
    logMessage("Mengeksekusi DDL schema.sql...", $isCli);
    $schemaSql = file_get_contents($schemaFile);
    // Pisahkan statement SQL
    $pdo->exec($schemaSql);
    logMessage("Tabel dan relasi berhasil dibuat.", $isCli);

    // Jalankan seed.sql
    $seedFile = __DIR__ . '/seed.sql';
    if (!file_exists($seedFile)) {
        throw new Exception("File seed.sql tidak ditemukan di: {$seedFile}");
    }
    logMessage("Mengeksekusi DML seed.sql...", $isCli);
    $seedSql = file_get_contents($seedFile);
    $pdo->exec($seedSql);
    logMessage("Seeding data pengguna, kategori, produk, supplier, dan transaksi berhasil.", $isCli);

    logMessage("SUKSES: Migrasi database selesai dengan sempurna!", $isCli);
    if (!$isCli) {
        echo "<hr style='border-color: #334155; margin: 1.5rem 0;'><a href='../index.php' style='display: inline-block; background: #4f46e5; color: #fff; padding: 10px 18px; border-radius: 6px; text-decoration: none;'>Buka Aplikasi Inventory</a>";
        echo "</body></html>";
    }
} catch (Throwable $e) {
    $err = "GAGAL: " . $e->getMessage();
    if ($isCli) {
        fwrite(STDERR, "[ERROR] " . $err . PHP_EOL);
        exit(1);
    } else {
        echo "<div style='background: #e11d48; color: #fff; padding: 12px; border-radius: 6px; margin-top: 1rem;'><strong>Error:</strong> " . htmlspecialchars($err) . "</div>";
        echo "</body></html>";
        exit(1);
    }
}
