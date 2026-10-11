<?php
/**
 * Helper Sanitasi Output dan Proteksi Keamanan
 */

class Sanitize {
    /**
     * Escape output untuk mencegah XSS (Cross-Site Scripting)
     */
    public static function escape($data) {
        if (is_array($data)) {
            return array_map([self::class, 'escape'], $data);
        }
        return htmlspecialchars((string) $data, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Format angka ke format mata uang Rupiah
     */
    public static function rupiah($amount) {
        return 'Rp ' . number_format((float) $amount, 0, ',', '.');
    }

    /**
     * Format tanggal Indonesia
     */
    public static function date($datetime, $withTime = true) {
        if (!$datetime) return '-';
        $timestamp = strtotime($datetime);
        $format = $withTime ? 'd M Y, H:i' : 'd M Y';
        return date($format, $timestamp);
    }

    /**
     * Memvalidasi kolom sorting terhadap allowlist ketat
     */
    public static function sortColumn($requested, array $allowlist, $default) {
        if (in_array($requested, $allowlist, true)) {
            return $requested;
        }
        return $default;
    }

    /**
     * Memvalidasi arah sorting (ASC atau DESC)
     */
    public static function sortDirection($requested, $default = 'ASC') {
        $upper = strtoupper(trim((string)$requested));
        return in_array($upper, ['ASC', 'DESC'], true) ? $upper : $default;
    }

    /**
     * Membuat atau mengambil token CSRF
     */
    public static function csrfToken() {
        if (empty($_SESSION['csrf_token'])) {
            if (function_exists('random_bytes')) {
                $bytes = random_bytes(32);
            } elseif (function_exists('openssl_random_pseudo_bytes')) {
                $bytes = openssl_random_pseudo_bytes(32);
            } else {
                $bytes = md5(uniqid(mt_rand(), true));
            }
            $_SESSION['csrf_token'] = bin2hex($bytes);
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Memverifikasi validitas token CSRF
     */
    public static function verifyCsrf($token) {
        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }
}

// Fungsi shortcut e() untuk template view
function e($data) {
    return Sanitize::escape($data);
}
