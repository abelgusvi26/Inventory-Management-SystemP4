<?php
/**
 * Konfigurasi dan Koneksi Database PDO
 * Menggunakan prinsip Singleton PDO untuk efisiensi koneksi selama siklus request.
 */

class Database {
    private static $instance = null;

    private function __construct() {}
    private function __clone() {}

    public static function getConnection() {
        if (self::$instance === null) {
            $host = getenv('DB_HOST') ?: 'localhost';
            $port = getenv('DB_PORT') ?: '3306';
            $dbName = getenv('DB_NAME') ?: 'inventory_db';
            $user = getenv('DB_USER') ?: 'root';
            $pass = getenv('DB_PASS') ?: '';
            $charset = 'utf8mb4';

            $dsn = "mysql:host={$host};port={$port};dbname={$dbName};charset={$charset}";

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$instance = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                // Jangan ekspos password di pesan error produksi
                error_log("Database connection failure: " . $e->getMessage());
                die("Koneksi database gagal. Pastikan MySQL pada XAMPP telah aktif dan jalankan migrasi.");
            }
        }

        return self::$instance;
    }
}
