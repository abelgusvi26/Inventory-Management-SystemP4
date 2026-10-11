<?php
/**
 * Front Controller Utama Aplikasi Inventory Management System
 * Mengorkestrasi request routing, inisialisasi sesi, dan dispatching controller.
 */

// Muat konfigurasi dasar
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';

// Muat helper keamanan & otentikasi
require_once __DIR__ . '/app/Helpers/Sanitize.php';
require_once __DIR__ . '/app/Helpers/Validator.php';
require_once __DIR__ . '/app/Helpers/Auth.php';

// Muat router utama
require_once __DIR__ . '/app/Core/Router.php';

// Eksekusi dispatch router
Router::dispatch();
