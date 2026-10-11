<?php
/**
 * Controller Transaksi Mutasi Barang (Stock In, Stock Out, & History)
 */

require_once __DIR__ . '/../Core/View.php';
require_once __DIR__ . '/../Models/StockMovement.php';
require_once __DIR__ . '/../Models/Product.php';
require_once __DIR__ . '/../Helpers/Auth.php';
require_once __DIR__ . '/../Helpers/Sanitize.php';
require_once __DIR__ . '/../Helpers/Validator.php';

class TransactionController {
    /**
     * Pencatatan Barang Masuk (IN)
     */
    public function in() {
        Auth::requireLogin();

        $products = Product::getAllSimple();
        $errors   = [];
        $data     = [
            'product_id'       => (int) (isset($_GET['product_id']) ? $_GET['product_id'] : 0),
            'quantity'         => '',
            'reference_number' => 'BM-' . date('Ymd-His'),
            'notes'            => '',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
            if (!Sanitize::verifyCsrf($token)) {
                $errors[] = 'Sesi form kedaluwarsa. Silakan ulangi.';
            } else {
                $data = [
                    'product_id'       => (int) (isset($_POST['product_id']) ? $_POST['product_id'] : 0),
                    'quantity'         => trim((string)(isset($_POST['quantity']) ? $_POST['quantity'] : '')),
                    'reference_number' => trim((string)(isset($_POST['reference_number']) ? $_POST['reference_number'] : '')),
                    'notes'            => trim((string)(isset($_POST['notes']) ? $_POST['notes'] : '')),
                ];

                $validator = new Validator($data);
                $validator->required('product_id', 'Produk')
                          ->required('quantity', 'Jumlah barang masuk')
                          ->positiveInteger('quantity', 'Jumlah barang masuk');

                if (!$validator->isValid()) {
                    $errors = $validator->getErrors();
                } else {
                    $result = StockMovement::record(
                        $data['product_id'],
                        Auth::id(),
                        'IN',
                        (int) $data['quantity'],
                        $data['reference_number'],
                        $data['notes']
                    );

                    if ($result['success']) {
                        $_SESSION['flash_success'] = $result['message'];
                        header('Location: ' . baseUrl('index.php?page=transactions&action=history'));
                        exit;
                    } else {
                        $errors[] = $result['message'];
                    }
                }
            }
        }

        View::render('transactions/in', [
            'products' => $products,
            'data'     => $data,
            'errors'   => $errors,
        ]);
    }

    /**
     * Pencatatan Barang Keluar (OUT) dengan validasi ketersediaan stok
     */
    public function out() {
        Auth::requireLogin();

        $products = Product::getAllSimple();
        $errors   = [];
        $data     = [
            'product_id'       => (int) (isset($_GET['product_id']) ? $_GET['product_id'] : 0),
            'quantity'         => '',
            'reference_number' => 'BK-' . date('Ymd-His'),
            'notes'            => '',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
            if (!Sanitize::verifyCsrf($token)) {
                $errors[] = 'Sesi form kedaluwarsa. Silakan ulangi.';
            } else {
                $data = [
                    'product_id'       => (int) (isset($_POST['product_id']) ? $_POST['product_id'] : 0),
                    'quantity'         => trim((string)(isset($_POST['quantity']) ? $_POST['quantity'] : '')),
                    'reference_number' => trim((string)(isset($_POST['reference_number']) ? $_POST['reference_number'] : '')),
                    'notes'            => trim((string)(isset($_POST['notes']) ? $_POST['notes'] : '')),
                ];

                $validator = new Validator($data);
                $validator->required('product_id', 'Produk')
                          ->required('quantity', 'Jumlah barang keluar')
                          ->positiveInteger('quantity', 'Jumlah barang keluar');

                if (!$validator->isValid()) {
                    $errors = $validator->getErrors();
                } else {
                    $result = StockMovement::record(
                        $data['product_id'],
                        Auth::id(),
                        'OUT',
                        (int) $data['quantity'],
                        $data['reference_number'],
                        $data['notes']
                    );

                    if ($result['success']) {
                        $_SESSION['flash_success'] = $result['message'];
                        header('Location: ' . baseUrl('index.php?page=transactions&action=history'));
                        exit;
                    } else {
                        $errors[] = $result['message'];
                    }
                }
            }
        }

        View::render('transactions/out', [
            'products' => $products,
            'data'     => $data,
            'errors'   => $errors,
        ]);
    }

    /**
     * Riwayat Seluruh Transaksi Mutasi Stok
     */
    public function history() {
        Auth::requireLogin();

        $productId = !empty($_GET['product_id']) ? (int) $_GET['product_id'] : null;
        $type      = !empty($_GET['type']) ? strtoupper(trim((string)$_GET['type'])) : null;
        $startDate = !empty($_GET['start_date']) ? trim((string)$_GET['start_date']) : null;
        $endDate   = !empty($_GET['end_date']) ? trim((string)$_GET['end_date']) : null;
        $page      = max(1, (int) (isset($_GET['p']) ? $_GET['p'] : 1));
        $perPage   = 15;

        $pagination = StockMovement::getPaginated($productId, $type, $startDate, $endDate, $page, $perPage);
        $products   = Product::getAllSimple();

        View::render('transactions/history', [
            'pagination' => $pagination,
            'products'   => $products,
            'productId'  => $productId,
            'type'       => $type,
            'startDate'  => $startDate,
            'endDate'    => $endDate,
        ]);
    }
}
