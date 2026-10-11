<?php
/**
 * Controller Dashboard Utama
 */

require_once __DIR__ . '/../Core/View.php';
require_once __DIR__ . '/../Models/Report.php';
require_once __DIR__ . '/../Models/Product.php';
require_once __DIR__ . '/../Models/StockMovement.php';
require_once __DIR__ . '/../Helpers/Auth.php';

class DashboardController {
    public function index() {
        Auth::requireLogin();

        $summary = Report::getDashboardSummary();
        $lowStockProducts = Product::getLowStockProducts(6);
        $recentMovements = StockMovement::getRecent(6);

        View::render('dashboard/index', [
            'summary'          => $summary,
            'lowStockProducts' => $lowStockProducts,
            'recentMovements'  => $recentMovements,
            'user'             => Auth::user(),
        ]);
    }
}
