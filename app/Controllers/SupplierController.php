<?php
/**
 * Controller Manajemen Supplier (Pemasok)
 */

require_once __DIR__ . '/../Core/View.php';
require_once __DIR__ . '/../Models/Supplier.php';
require_once __DIR__ . '/../Helpers/Auth.php';
require_once __DIR__ . '/../Helpers/Sanitize.php';
require_once __DIR__ . '/../Helpers/Validator.php';

class SupplierController {
    public function index() {
        Auth::requireLogin();
        $suppliers = Supplier::getAll();
        View::render('suppliers/index', ['suppliers' => $suppliers]);
    }

    public function detail() {
        Auth::requireLogin();

        $id = (int) (isset($_GET['id']) ? $_GET['id'] : 0);
        $supplier = Supplier::findById($id);

        if (!$supplier) {
            $_SESSION['flash_error'] = 'Supplier tidak ditemukan.';
            header('Location: ' . baseUrl('index.php?page=suppliers'));
            exit;
        }

        $products = Supplier::getSuppliedProducts($id);

        View::render('suppliers/detail', [
            'supplier' => $supplier,
            'products' => $products,
        ]);
    }

    public function create() {
        Auth::requireAdmin();

        $errors = [];
        $data = [
            'name'           => '',
            'contact_person' => '',
            'phone'          => '',
            'email'          => '',
            'address'        => '',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
            if (!Sanitize::verifyCsrf($token)) {
                $errors[] = 'Sesi form kedaluwarsa. Silakan ulangi.';
            } else {
                $data = [
                    'name'           => trim((string)(isset($_POST['name']) ? $_POST['name'] : '')),
                    'contact_person' => trim((string)(isset($_POST['contact_person']) ? $_POST['contact_person'] : '')),
                    'phone'          => trim((string)(isset($_POST['phone']) ? $_POST['phone'] : '')),
                    'email'          => trim((string)(isset($_POST['email']) ? $_POST['email'] : '')),
                    'address'        => trim((string)(isset($_POST['address']) ? $_POST['address'] : '')),
                ];

                $validator = new Validator($data);
                $validator->required('name', 'Nama supplier')
                          ->minLength('name', 3, 'Nama supplier');

                if (!$validator->isValid()) {
                    $errors = $validator->getErrors();
                } else {
                    try {
                        Supplier::create(
                            $data['name'],
                            $data['contact_person'],
                            $data['phone'],
                            $data['email'],
                            $data['address']
                        );
                        $_SESSION['flash_success'] = "Supplier '{$data['name']}' berhasil didaftarkan.";
                        header('Location: ' . baseUrl('index.php?page=suppliers'));
                        exit;
                    } catch (Throwable $e) {
                        $errors[] = 'Gagal menyimpan supplier: ' . $e->getMessage();
                    }
                }
            }
        }

        View::render('suppliers/create', ['data' => $data, 'errors' => $errors]);
    }

    public function edit() {
        Auth::requireAdmin();

        $id = (int) (isset($_GET['id']) ? $_GET['id'] : 0);
        $supplier = Supplier::findById($id);

        if (!$supplier) {
            $_SESSION['flash_error'] = 'Supplier tidak ditemukan.';
            header('Location: ' . baseUrl('index.php?page=suppliers'));
            exit;
        }

        $errors = [];
        $data = [
            'name'           => $supplier['name'],
            'contact_person' => $supplier['contact_person'],
            'phone'          => $supplier['phone'],
            'email'          => $supplier['email'],
            'address'        => $supplier['address'],
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
            if (!Sanitize::verifyCsrf($token)) {
                $errors[] = 'Sesi form kedaluwarsa. Silakan ulangi.';
            } else {
                $data = [
                    'name'           => trim((string)(isset($_POST['name']) ? $_POST['name'] : '')),
                    'contact_person' => trim((string)(isset($_POST['contact_person']) ? $_POST['contact_person'] : '')),
                    'phone'          => trim((string)(isset($_POST['phone']) ? $_POST['phone'] : '')),
                    'email'          => trim((string)(isset($_POST['email']) ? $_POST['email'] : '')),
                    'address'        => trim((string)(isset($_POST['address']) ? $_POST['address'] : '')),
                ];

                $validator = new Validator($data);
                $validator->required('name', 'Nama supplier')
                          ->minLength('name', 3, 'Nama supplier');

                if (!$validator->isValid()) {
                    $errors = $validator->getErrors();
                } else {
                    try {
                        Supplier::update(
                            $id,
                            $data['name'],
                            $data['contact_person'],
                            $data['phone'],
                            $data['email'],
                            $data['address']
                        );
                        $_SESSION['flash_success'] = "Data supplier '{$data['name']}' berhasil diperbarui.";
                        header('Location: ' . baseUrl('index.php?page=suppliers'));
                        exit;
                    } catch (Throwable $e) {
                        $errors[] = 'Gagal memperbarui supplier: ' . $e->getMessage();
                    }
                }
            }
        }

        View::render('suppliers/edit', [
            'supplier' => $supplier,
            'data'     => $data,
            'errors'   => $errors,
        ]);
    }

    public function delete() {
        Auth::requireAdmin();

        $token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Sanitize::verifyCsrf($token)) {
            $_SESSION['flash_error'] = 'Permintaan tidak valid atau sesi kedaluwarsa.';
            header('Location: ' . baseUrl('index.php?page=suppliers'));
            exit;
        }

        $id = (int) (isset($_POST['id']) ? $_POST['id'] : 0);
        $supplier = Supplier::findById($id);

        if (!$supplier) {
            $_SESSION['flash_error'] = 'Supplier tidak ditemukan.';
            header('Location: ' . baseUrl('index.php?page=suppliers'));
            exit;
        }

        if (Supplier::delete($id)) {
            $_SESSION['flash_success'] = "Supplier '{$supplier['name']}' berhasil dihapus.";
        } else {
            $_SESSION['flash_error'] = 'Gagal menghapus supplier.';
        }

        header('Location: ' . baseUrl('index.php?page=suppliers'));
        exit;
    }
}
