<?php
/**
 * Router Aplikasi untuk mendispatch request ke Controller yang sesuai
 */

require_once __DIR__ . '/View.php';
require_once __DIR__ . '/../Helpers/Auth.php';

class Router {
    public static function dispatch() {
        $page = trim((string)(isset($_GET['page']) ? $_GET['page'] : ''));
        $action = trim((string)(isset($_GET['action']) ? $_GET['action'] : ''));

        // Default redirect jika belum login
        if ($page === '') {
            $page = Auth::check() ? 'dashboard' : 'login';
        }

        switch ($page) {
            case 'login':
                require_once __DIR__ . '/../Controllers/AuthController.php';
                (new AuthController())->login();
                break;

            case 'logout':
                require_once __DIR__ . '/../Controllers/AuthController.php';
                (new AuthController())->logout();
                break;

            case 'dashboard':
                require_once __DIR__ . '/../Controllers/DashboardController.php';
                (new DashboardController())->index();
                break;

            case 'products':
                require_once __DIR__ . '/../Controllers/ProductController.php';
                $ctrl = new ProductController();
                if ($action === 'create') {
                    $ctrl->create();
                } elseif ($action === 'edit') {
                    $ctrl->edit();
                } elseif ($action === 'detail') {
                    $ctrl->detail();
                } elseif ($action === 'delete') {
                    $ctrl->delete();
                } else {
                    $ctrl->index();
                }
                break;

            case 'categories':
                require_once __DIR__ . '/../Controllers/CategoryController.php';
                $ctrl = new CategoryController();
                if ($action === 'create') {
                    $ctrl->create();
                } elseif ($action === 'edit') {
                    $ctrl->edit();
                } elseif ($action === 'delete') {
                    $ctrl->delete();
                } else {
                    $ctrl->index();
                }
                break;

            case 'suppliers':
                require_once __DIR__ . '/../Controllers/SupplierController.php';
                $ctrl = new SupplierController();
                if ($action === 'create') {
                    $ctrl->create();
                } elseif ($action === 'edit') {
                    $ctrl->edit();
                } elseif ($action === 'detail') {
                    $ctrl->detail();
                } elseif ($action === 'delete') {
                    $ctrl->delete();
                } else {
                    $ctrl->index();
                }
                break;

            case 'transactions':
                require_once __DIR__ . '/../Controllers/TransactionController.php';
                $ctrl = new TransactionController();
                if ($action === 'in') {
                    $ctrl->in();
                } elseif ($action === 'out') {
                    $ctrl->out();
                } else {
                    $ctrl->history();
                }
                break;

            case 'reports':
                require_once __DIR__ . '/../Controllers/ReportController.php';
                $ctrl = new ReportController();
                if ($action === 'print') {
                    $ctrl->printView();
                } elseif ($action === 'export') {
                    $ctrl->exportCsv();
                } else {
                    $ctrl->index();
                }
                break;

            case 'users':
                require_once __DIR__ . '/../Controllers/UserController.php';
                $ctrl = new UserController();
                if ($action === 'create') {
                    $ctrl->create();
                } elseif ($action === 'edit') {
                    $ctrl->edit();
                } elseif ($action === 'delete') {
                    $ctrl->delete();
                } else {
                    $ctrl->index();
                }
                break;

            default:
                http_response_code(404);
                View::render('errors/404', ['page' => $page], 'main');
                break;
        }
    }
}
