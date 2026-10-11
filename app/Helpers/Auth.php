<?php
/**
 * Helper Autentikasi, Sesi, dan Otorisasi Berbasis Peran (Role-Based Access Control)
 */

class Auth {
    /**
     * Memeriksa apakah user saat ini telah login
     */
    public static function check() {
        return !empty($_SESSION['user']) && !empty($_SESSION['user']['id']);
    }

    /**
     * Mendapatkan data user yang sedang login
     */
    public static function user() {
        return isset($_SESSION['user']) ? $_SESSION['user'] : null;
    }

    /**
     * Mendapatkan ID user login
     */
    public static function id() {
        return (isset($_SESSION['user']) && isset($_SESSION['user']['id'])) ? $_SESSION['user']['id'] : null;
    }

    /**
     * Mendapatkan peran user login (admin atau staff)
     */
    public static function role() {
        return (isset($_SESSION['user']) && isset($_SESSION['user']['role'])) ? $_SESSION['user']['role'] : null;
    }

    /**
     * Memeriksa apakah user memiliki peran admin
     */
    public static function isAdmin() {
        return self::role() === 'admin';
    }

    /**
     * Memeriksa apakah user memiliki peran staff
     */
    public static function isStaff() {
        return self::role() === 'staff';
    }

    /**
     * Menyimpan data user ke session dengan regenerasi ID session
     */
    public static function login(array $user) {
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id'        => (int) $user['id'],
            'username'  => $user['username'],
            'role'      => $user['role'],
            'full_name' => isset($user['full_name']) ? $user['full_name'] : $user['username'],
        ];
    }

    /**
     * Menghapus sesi dan cookie otentikasi saat logout
     */
    public static function logout() {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }

    /**
     * Memastikan user telah login; jika belum arahkan ke login
     */
    public static function requireLogin() {
        if (!self::check()) {
            $_SESSION['flash_error'] = 'Silakan login terlebih dahulu untuk mengakses sistem.';
            header('Location: ' . baseUrl('index.php?page=login'));
            exit;
        }
    }

    /**
     * Memastikan user adalah Admin; jika staf tolak dengan HTTP 403 Forbidden
     */
    public static function requireAdmin() {
        self::requireLogin();
        if (!self::isAdmin()) {
            http_response_code(403);
            require_once __DIR__ . '/../../views/errors/403.php';
            exit;
        }
    }
}
