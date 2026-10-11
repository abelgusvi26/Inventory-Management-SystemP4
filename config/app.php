<?php
/**
 * Konfigurasi Utama Aplikasi Inventory
 */

// Set timezone Indonesia
date_default_timezone_set('Asia/Jakarta');

// Konstanta Aplikasi
if (!defined('APP_NAME')) {
    define('APP_NAME', 'Inventory Hub');
}

if (!defined('APP_VERSION')) {
    define('APP_VERSION', '1.0.0');
}

// Inisialisasi session jika belum berjalan
if (session_status() === PHP_SESSION_NONE) {
    // Pengaturan cookie session aman
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_start();
}

// Helper untuk base URL otomatis
function baseUrl($path = '') {
    $scriptDir = dirname(isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '');
    $base = rtrim(str_replace('\\', '/', $scriptDir), '/');
    if ($path === '') {
        return $base ?: '/';
    }
    return ($base ?: '') . '/' . ltrim($path, '/');
}
