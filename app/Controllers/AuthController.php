<?php
/**
 * Controller Autentikasi Pengguna
 */

require_once __DIR__ . '/../Core/View.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Helpers/Auth.php';
require_once __DIR__ . '/../Helpers/Sanitize.php';
require_once __DIR__ . '/../Helpers/Validator.php';

class AuthController {
    public function login() {
        if (Auth::check()) {
            header('Location: ' . baseUrl('index.php?page=dashboard'));
            exit;
        }

        $error = null;
        $username = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verifikasi CSRF
            $token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
            if (!Sanitize::verifyCsrf($token)) {
                $error = 'Sesi form telah kedaluwarsa. Silakan refresh halaman dan coba kembali.';
            } else {
                $username = trim((string)(isset($_POST['username']) ? $_POST['username'] : ''));
                $password = (string)(isset($_POST['password']) ? $_POST['password'] : '');

                $validator = new Validator($_POST);
                $validator->required('username', 'Nama pengguna')
                          ->required('password', 'Kata sandi');

                if (!$validator->isValid()) {
                    $error = $validator->getFirstError();
                } else {
                    $user = User::findByUsername($username);

                    if ($user && password_verify($password, $user['password_hash'])) {
                        // Login Berhasil
                        Auth::login($user);
                        $_SESSION['flash_success'] = "Selamat datang kembali, " . ($user['full_name'] ?: $user['username']) . "!";
                        header('Location: ' . baseUrl('index.php?page=dashboard'));
                        exit;
                    } else {
                        $error = 'Nama pengguna atau kata sandi tidak sesuai.';
                    }
                }
            }
        }

        View::render('auth/login', [
            'error'    => $error,
            'username' => $username,
        ], 'auth');
    }

    public function logout() {
        Auth::logout();
        session_start();
        $_SESSION['flash_success'] = 'Anda telah berhasil keluar dari sistem.';
        header('Location: ' . baseUrl('index.php?page=login'));
        exit;
    }
}
