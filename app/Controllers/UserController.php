<?php
/**
 * Controller Manajemen Pengguna & Profil (Users & Profiles)
 * Khusus role Admin
 */

require_once __DIR__ . '/../Core/View.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Helpers/Auth.php';
require_once __DIR__ . '/../Helpers/Sanitize.php';
require_once __DIR__ . '/../Helpers/Validator.php';

class UserController {
    public function index() {
        Auth::requireAdmin();
        $users = User::getAll();
        View::render('users/index', ['users' => $users]);
    }

    public function create() {
        Auth::requireAdmin();

        $errors = [];
        $data = [
            'username'  => '',
            'password'  => '',
            'role'      => 'staff',
            'full_name' => '',
            'phone'     => '',
            'bio'       => '',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
            if (!Sanitize::verifyCsrf($token)) {
                $errors[] = 'Sesi form kedaluwarsa. Silakan coba kembali.';
            } else {
                $data = [
                    'username'  => trim((string)(isset($_POST['username']) ? $_POST['username'] : '')),
                    'password'  => (string)(isset($_POST['password']) ? $_POST['password'] : ''),
                    'role'      => trim((string)(isset($_POST['role']) ? $_POST['role'] : 'staff')),
                    'full_name' => trim((string)(isset($_POST['full_name']) ? $_POST['full_name'] : '')),
                    'phone'     => trim((string)(isset($_POST['phone']) ? $_POST['phone'] : '')),
                    'bio'       => trim((string)(isset($_POST['bio']) ? $_POST['bio'] : '')),
                ];

                $validator = new Validator($data);
                $validator->required('username', 'Username')
                          ->minLength('username', 3, 'Username')
                          ->maxLength('username', 50, 'Username')
                          ->required('password', 'Kata sandi')
                          ->minLength('password', 6, 'Kata sandi')
                          ->required('full_name', 'Nama lengkap')
                          ->inArray('role', ['admin', 'staff'], 'Peran');

                if (User::findByUsername($data['username'])) {
                    $validator->addCustomError('username', "Username '{$data['username']}' sudah digunakan.");
                }

                if (!$validator->isValid()) {
                    $errors = $validator->getErrors();
                } else {
                    try {
                        User::create(
                            $data['username'],
                            $data['password'],
                            $data['role'],
                            $data['full_name'],
                            $data['phone'],
                            $data['bio']
                        );
                        $_SESSION['flash_success'] = "Pengguna baru '{$data['username']}' berhasil didaftarkan.";
                        header('Location: ' . baseUrl('index.php?page=users'));
                        exit;
                    } catch (Throwable $e) {
                        $errors[] = 'Gagal menyimpan pengguna: ' . $e->getMessage();
                    }
                }
            }
        }

        View::render('users/create', ['data' => $data, 'errors' => $errors]);
    }

    public function edit() {
        Auth::requireAdmin();

        $id = (int) (isset($_GET['id']) ? $_GET['id'] : 0);
        $user = User::findById($id);

        if (!$user) {
            $_SESSION['flash_error'] = 'Pengguna tidak ditemukan.';
            header('Location: ' . baseUrl('index.php?page=users'));
            exit;
        }

        $errors = [];
        $data = [
            'username'  => $user['username'],
            'role'      => $user['role'],
            'full_name' => $user['full_name'],
            'phone'     => $user['phone'],
            'bio'       => $user['bio'],
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
            if (!Sanitize::verifyCsrf($token)) {
                $errors[] = 'Sesi form kedaluwarsa. Silakan ulangi.';
            } else {
                $data = [
                    'username'  => $user['username'],
                    'role'      => trim((string)(isset($_POST['role']) ? $_POST['role'] : $user['role'])),
                    'full_name' => trim((string)(isset($_POST['full_name']) ? $_POST['full_name'] : '')),
                    'phone'     => trim((string)(isset($_POST['phone']) ? $_POST['phone'] : '')),
                    'bio'       => trim((string)(isset($_POST['bio']) ? $_POST['bio'] : '')),
                    'password'  => (string)(isset($_POST['password']) ? $_POST['password'] : ''),
                ];

                $validator = new Validator($data);
                $validator->required('full_name', 'Nama lengkap')
                          ->inArray('role', ['admin', 'staff'], 'Peran');

                if (!empty($data['password']) && mb_strlen($data['password']) < 6) {
                    $validator->addCustomError('password', 'Kata sandi baru minimal 6 karakter.');
                }

                // Jangan ubah role admin terakhir menjadi staff
                if ($user['role'] === 'admin' && $data['role'] === 'staff' && User::countAdmins() <= 1) {
                    $validator->addCustomError('role', 'Sistem harus memiliki setidaknya satu administrator aktif.');
                }

                if (!$validator->isValid()) {
                    $errors = $validator->getErrors();
                } else {
                    try {
                        User::update(
                            $id,
                            $data['role'],
                            $data['full_name'],
                            $data['phone'],
                            $data['bio'],
                            !empty($data['password']) ? $data['password'] : null
                        );
                        $_SESSION['flash_success'] = "Data profil pengguna '{$user['username']}' berhasil diperbarui.";
                        header('Location: ' . baseUrl('index.php?page=users'));
                        exit;
                    } catch (Throwable $e) {
                        $errors[] = 'Gagal memperbarui pengguna: ' . $e->getMessage();
                    }
                }
            }
        }

        View::render('users/edit', [
            'user'   => $user,
            'data'   => $data,
            'errors' => $errors,
        ]);
    }

    public function delete() {
        Auth::requireAdmin();

        $token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Sanitize::verifyCsrf($token)) {
            $_SESSION['flash_error'] = 'Permintaan tidak valid atau sesi kedaluwarsa.';
            header('Location: ' . baseUrl('index.php?page=users'));
            exit;
        }

        $id = (int) (isset($_POST['id']) ? $_POST['id'] : 0);
        $user = User::findById($id);

        if (!$user) {
            $_SESSION['flash_error'] = 'Pengguna tidak ditemukan.';
            header('Location: ' . baseUrl('index.php?page=users'));
            exit;
        }

        if ($id === Auth::id()) {
            $_SESSION['flash_error'] = 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif.';
            header('Location: ' . baseUrl('index.php?page=users'));
            exit;
        }

        if ($user['role'] === 'admin' && User::countAdmins() <= 1) {
            $_SESSION['flash_error'] = 'Tidak dapat menghapus administrator terakhir sistem.';
            header('Location: ' . baseUrl('index.php?page=users'));
            exit;
        }

        if (User::delete($id)) {
            $_SESSION['flash_success'] = "Pengguna '{$user['username']}' berhasil dihapus.";
        } else {
            $_SESSION['flash_error'] = 'Gagal menghapus pengguna.';
        }

        header('Location: ' . baseUrl('index.php?page=users'));
        exit;
    }
}
