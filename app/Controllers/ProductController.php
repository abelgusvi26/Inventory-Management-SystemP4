<?php
/**
 * Controller Manajemen Produk (Products)
 */

require_once __DIR__ . '/../Core/View.php';
require_once __DIR__ . '/../Models/Product.php';
require_once __DIR__ . '/../Models/Category.php';
require_once __DIR__ . '/../Models/Supplier.php';
require_once __DIR__ . '/../Models/ProductSupplier.php';
require_once __DIR__ . '/../Models/StockMovement.php';
require_once __DIR__ . '/../Helpers/Auth.php';
require_once __DIR__ . '/../Helpers/Sanitize.php';
require_once __DIR__ . '/../Helpers/Validator.php';

class ProductController {
    public function index() {
        Auth::requireLogin();

        $search     = trim((string)(isset($_GET['q']) ? $_GET['q'] : ''));
        $categoryId = !empty($_GET['category_id']) ? (int) $_GET['category_id'] : null;
        $sortByRaw  = isset($_GET['sort']) ? $_GET['sort'] : 'created_at';
        $sortBy     = Sanitize::sortColumn($sortByRaw, ['name', 'price', 'stock', 'sku', 'created_at', 'category_name'], 'created_at');
        $sortDirRaw = isset($_GET['direction']) ? $_GET['direction'] : 'DESC';
        $sortDir    = Sanitize::sortDirection($sortDirRaw, 'DESC');
        $page       = max(1, (int) (isset($_GET['p']) ? $_GET['p'] : 1));
        $perPage    = 10;

        $pagination = Product::getPaginated($search, $categoryId, $sortBy, $sortDir, $page, $perPage);
        $categories = Category::getAll();

        View::render('products/index', [
            'pagination' => $pagination,
            'categories' => $categories,
            'search'     => $search,
            'categoryId' => $categoryId,
            'sortBy'     => $sortBy,
            'sortDir'    => $sortDir,
        ]);
    }

    public function detail() {
        Auth::requireLogin();

        $id = (int) (isset($_GET['id']) ? $_GET['id'] : 0);
        $product = Product::findById($id);

        if (!$product) {
            $_SESSION['flash_error'] = 'Produk tidak ditemukan.';
            header('Location: ' . baseUrl('index.php?page=products'));
            exit;
        }

        $suppliers = ProductSupplier::getByProductId($id);
        $movements = StockMovement::getPaginated($id, null, null, null, 1, 20);

        View::render('products/detail', [
            'product'   => $product,
            'suppliers' => $suppliers,
            'movements' => $movements['data'],
        ]);
    }

    public function create() {
        Auth::requireAdmin();

        $categories = Category::getAll();
        $suppliers  = Supplier::getAll();
        $errors     = [];
        $data       = [
            'category_id'   => '',
            'sku'           => '',
            'name'          => '',
            'price'         => '',
            'stock'         => '0',
            'minimum_stock' => '5',
            'suppliers'     => [],
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
            if (!Sanitize::verifyCsrf($token)) {
                $errors[] = 'Sesi form telah kedaluwarsa. Silakan ulangi.';
            } else {
                $data = [
                    'category_id'   => trim((string)(isset($_POST['category_id']) ? $_POST['category_id'] : '')),
                    'sku'           => strtoupper(trim((string)(isset($_POST['sku']) ? $_POST['sku'] : ''))),
                    'name'          => trim((string)(isset($_POST['name']) ? $_POST['name'] : '')),
                    'price'         => trim((string)(isset($_POST['price']) ? $_POST['price'] : '')),
                    'stock'         => trim((string)(isset($_POST['stock']) ? $_POST['stock'] : '0')),
                    'minimum_stock' => trim((string)(isset($_POST['minimum_stock']) ? $_POST['minimum_stock'] : '5')),
                    'suppliers'     => isset($_POST['suppliers']) ? $_POST['suppliers'] : [],
                ];

                $validator = new Validator($data);
                $validator->required('category_id', 'Kategori')
                          ->required('sku', 'SKU')
                          ->required('name', 'Nama produk')
                          ->required('price', 'Harga jual')
                          ->numericNonNegative('price', 'Harga jual')
                          ->positiveInteger('stock', 'Stok awal', true)
                          ->positiveInteger('minimum_stock', 'Batas stok minimum', true);

                if (Product::findBySku($data['sku'])) {
                    $validator->addCustomError('sku', "SKU '{$data['sku']}' sudah digunakan pada produk lain.");
                }

                if (!$validator->isValid()) {
                    $errors = $validator->getErrors();
                } else {
                    // Siapkan format array relasi supplier
                    $suppList = [];
                    if (!empty($data['suppliers']) && is_array($data['suppliers'])) {
                        foreach ($data['suppliers'] as $suppId => $item) {
                            if (!empty($item['selected'])) {
                                $suppList[] = [
                                    'supplier_id'    => (int) $suppId,
                                    'supplier_price' => (float) (isset($item['price']) ? $item['price'] : 0),
                                ];
                            }
                        }
                    }

                    try {
                        $newId = Product::create(
                            $data['category_id'],
                            $data['sku'],
                            $data['name'],
                            $data['price'],
                            $data['stock'],
                            $data['minimum_stock'],
                            $suppList
                        );

                        $_SESSION['flash_success'] = "Produk '{$data['name']}' berhasil ditambahkan ke inventaris.";
                        header('Location: ' . baseUrl('index.php?page=products&action=detail&id=' . $newId));
                        exit;
                    } catch (Throwable $e) {
                        $errors[] = 'Gagal menyimpan produk: ' . $e->getMessage();
                    }
                }
            }
        }

        View::render('products/create', [
            'categories' => $categories,
            'suppliers'  => $suppliers,
            'data'       => $data,
            'errors'     => $errors,
        ]);
    }

    public function edit() {
        Auth::requireAdmin();

        $id = (int) (isset($_GET['id']) ? $_GET['id'] : 0);
        $product = Product::findById($id);

        if (!$product) {
            $_SESSION['flash_error'] = 'Produk tidak ditemukan.';
            header('Location: ' . baseUrl('index.php?page=products'));
            exit;
        }

        $categories = Category::getAll();
        $allSuppliers = Supplier::getAll();
        $currentSuppliers = ProductSupplier::getByProductId($id);

        // Petakan supplier saat ini
        $mappedCurrentSuppliers = [];
        foreach ($currentSuppliers as $cs) {
            $mappedCurrentSuppliers[$cs['supplier_id']] = [
                'selected' => 1,
                'price'    => $cs['supplier_price'],
            ];
        }

        $errors = [];
        $data = [
            'category_id'   => $product['category_id'],
            'sku'           => $product['sku'],
            'name'          => $product['name'],
            'price'         => $product['price'],
            'minimum_stock' => $product['minimum_stock'],
            'suppliers'     => $mappedCurrentSuppliers,
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
            if (!Sanitize::verifyCsrf($token)) {
                $errors[] = 'Sesi form telah kedaluwarsa. Silakan ulangi.';
            } else {
                $data = [
                    'category_id'   => trim((string)(isset($_POST['category_id']) ? $_POST['category_id'] : '')),
                    'sku'           => $product['sku'],
                    'name'          => trim((string)(isset($_POST['name']) ? $_POST['name'] : '')),
                    'price'         => trim((string)(isset($_POST['price']) ? $_POST['price'] : '')),
                    'minimum_stock' => trim((string)(isset($_POST['minimum_stock']) ? $_POST['minimum_stock'] : '5')),
                    'suppliers'     => isset($_POST['suppliers']) ? $_POST['suppliers'] : [],
                ];

                $validator = new Validator($data);
                $validator->required('category_id', 'Kategori')
                          ->required('name', 'Nama produk')
                          ->required('price', 'Harga jual')
                          ->numericNonNegative('price', 'Harga jual')
                          ->positiveInteger('minimum_stock', 'Batas stok minimum', true);

                if (!$validator->isValid()) {
                    $errors = $validator->getErrors();
                } else {
                    $suppList = [];
                    if (!empty($data['suppliers']) && is_array($data['suppliers'])) {
                        foreach ($data['suppliers'] as $suppId => $item) {
                            if (!empty($item['selected'])) {
                                $suppList[] = [
                                    'supplier_id'    => (int) $suppId,
                                    'supplier_price' => (float) (isset($item['price']) ? $item['price'] : 0),
                                ];
                            }
                        }
                    }

                    try {
                        Product::update(
                            $id,
                            $data['category_id'],
                            $data['name'],
                            $data['price'],
                            $data['minimum_stock'],
                            $suppList
                        );

                        $_SESSION['flash_success'] = "Data produk '{$data['name']}' berhasil diperbarui.";
                        header('Location: ' . baseUrl('index.php?page=products&action=detail&id=' . $id));
                        exit;
                    } catch (Throwable $e) {
                        $errors[] = 'Gagal memperbarui produk: ' . $e->getMessage();
                    }
                }
            }
        }

        View::render('products/edit', [
            'product'      => $product,
            'categories'   => $categories,
            'allSuppliers' => $allSuppliers,
            'data'         => $data,
            'errors'       => $errors,
        ]);
    }

    public function delete() {
        Auth::requireAdmin();

        $token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Sanitize::verifyCsrf($token)) {
            $_SESSION['flash_error'] = 'Permintaan tidak valid atau token sesi kedaluwarsa.';
            header('Location: ' . baseUrl('index.php?page=products'));
            exit;
        }

        $id = (int) (isset($_POST['id']) ? $_POST['id'] : 0);
        $product = Product::findById($id);

        if (!$product) {
            $_SESSION['flash_error'] = 'Produk tidak ditemukan.';
            header('Location: ' . baseUrl('index.php?page=products'));
            exit;
        }

        if (Product::hasMovements($id)) {
            $_SESSION['flash_error'] = "Produk '{$product['name']}' tidak dapat dihapus karena telah memiliki catatan histori transaksi mutasi stok. Aturan bisnis melindungi konsistensi data.";
            header('Location: ' . baseUrl('index.php?page=products&action=detail&id=' . $id));
            exit;
        }

        if (Product::delete($id)) {
            $_SESSION['flash_success'] = "Produk '{$product['name']}' berhasil dihapus.";
        } else {
            $_SESSION['flash_error'] = 'Gagal menghapus produk dari database.';
        }

        header('Location: ' . baseUrl('index.php?page=products'));
        exit;
    }
}
