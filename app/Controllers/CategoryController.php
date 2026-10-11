<?php
/**
 * Controller Manajemen Kategori Produk (Categories)
 */

require_once __DIR__ . '/../Core/View.php';
require_once __DIR__ . '/../Models/Category.php';
require_once __DIR__ . '/../Helpers/Auth.php';
require_once __DIR__ . '/../Helpers/Sanitize.php';
require_once __DIR__ . '/../Helpers/Validator.php';

class CategoryController {
    public function index() {
        Auth::requireLogin();
        $categories = Category::getAll();
        View::render('categories/index', ['categories' => $categories]);
    }

    public function create() {
        Auth::requireAdmin();

        $errors = [];
        $data = ['name' => '', 'description' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
            if (!Sanitize::verifyCsrf($token)) {
                $errors[] = 'Sesi form kedaluwarsa. Silakan coba kembali.';
            } else {
                $data = [
                    'name'        => trim((string)(isset($_POST['name']) ? $_POST['name'] : '')),
                    'description' => trim((string)(isset($_POST['description']) ? $_POST['description'] : '')),
                ];

                $validator = new Validator($data);
                $validator->required('name', 'Nama kategori')
                          ->minLength('name', 3, 'Nama kategori')
                          ->maxLength('name', 100, 'Nama kategori');

                if (Category::findByName($data['name'])) {
                    $validator->addCustomError('name', "Kategori dengan nama '{$data['name']}' sudah ada.");
                }

                if (!$validator->isValid()) {
                    $errors = $validator->getErrors();
                } else {
                    try {
                        Category::create($data['name'], $data['description']);
                        $_SESSION['flash_success'] = "Kategori '{$data['name']}' berhasil ditambahkan.";
                        header('Location: ' . baseUrl('index.php?page=categories'));
                        exit;
                    } catch (Throwable $e) {
                        $errors[] = 'Gagal menyimpan kategori: ' . $e->getMessage();
                    }
                }
            }
        }

        View::render('categories/create', ['data' => $data, 'errors' => $errors]);
    }

    public function edit() {
        Auth::requireAdmin();

        $id = (int) (isset($_GET['id']) ? $_GET['id'] : 0);
        $category = Category::findById($id);

        if (!$category) {
            $_SESSION['flash_error'] = 'Kategori tidak ditemukan.';
            header('Location: ' . baseUrl('index.php?page=categories'));
            exit;
        }

        $errors = [];
        $data = [
            'name'        => $category['name'],
            'description' => $category['description'],
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
            if (!Sanitize::verifyCsrf($token)) {
                $errors[] = 'Sesi form kedaluwarsa. Silakan coba kembali.';
            } else {
                $data = [
                    'name'        => trim((string)(isset($_POST['name']) ? $_POST['name'] : '')),
                    'description' => trim((string)(isset($_POST['description']) ? $_POST['description'] : '')),
                ];

                $validator = new Validator($data);
                $validator->required('name', 'Nama kategori')
                          ->minLength('name', 3, 'Nama kategori')
                          ->maxLength('name', 100, 'Nama kategori');

                if (Category::findByName($data['name'], $id)) {
                    $validator->addCustomError('name', "Kategori dengan nama '{$data['name']}' sudah ada.");
                }

                if (!$validator->isValid()) {
                    $errors = $validator->getErrors();
                } else {
                    try {
                        Category::update($id, $data['name'], $data['description']);
                        $_SESSION['flash_success'] = "Kategori '{$data['name']}' berhasil diperbarui.";
                        header('Location: ' . baseUrl('index.php?page=categories'));
                        exit;
                    } catch (Throwable $e) {
                        $errors[] = 'Gagal memperbarui kategori: ' . $e->getMessage();
                    }
                }
            }
        }

        View::render('categories/edit', [
            'category' => $category,
            'data'     => $data,
            'errors'   => $errors,
        ]);
    }

    public function delete() {
        Auth::requireAdmin();

        $token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Sanitize::verifyCsrf($token)) {
            $_SESSION['flash_error'] = 'Permintaan tidak valid atau sesi kedaluwarsa.';
            header('Location: ' . baseUrl('index.php?page=categories'));
            exit;
        }

        $id = (int) (isset($_POST['id']) ? $_POST['id'] : 0);
        $category = Category::findById($id);

        if (!$category) {
            $_SESSION['flash_error'] = 'Kategori tidak ditemukan.';
            header('Location: ' . baseUrl('index.php?page=categories'));
            exit;
        }

        if (Category::hasProducts($id)) {
            $_SESSION['flash_error'] = "Kategori '{$category['name']}' tidak dapat dihapus karena masih menampung produk aktif. Silakan pindahkan atau hapus produk terkait terlebih dahulu.";
            header('Location: ' . baseUrl('index.php?page=categories'));
            exit;
        }

        if (Category::delete($id)) {
            $_SESSION['flash_success'] = "Kategori '{$category['name']}' berhasil dihapus.";
        } else {
            $_SESSION['flash_error'] = 'Gagal menghapus kategori.';
        }

        header('Location: ' . baseUrl('index.php?page=categories'));
        exit;
    }
}
