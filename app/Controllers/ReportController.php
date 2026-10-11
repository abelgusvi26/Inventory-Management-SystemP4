<?php
/**
 * Controller Laporan Inventaris dan Mutasi Stok (Reports)
 */

require_once __DIR__ . '/../Core/View.php';
require_once __DIR__ . '/../Models/Report.php';
require_once __DIR__ . '/../Models/Category.php';
require_once __DIR__ . '/../Helpers/Auth.php';
require_once __DIR__ . '/../Helpers/Sanitize.php';

class ReportController {
    public function index() {
        Auth::requireLogin();

        $categoryId = !empty($_GET['category_id']) ? (int) $_GET['category_id'] : null;
        $startDate  = !empty($_GET['start_date']) ? trim((string)$_GET['start_date']) : null;
        $endDate    = !empty($_GET['end_date']) ? trim((string)$_GET['end_date']) : null;

        $reportData = Report::getAggregateInventoryReport($categoryId, $startDate, $endDate);
        $categories = Category::getAll();

        View::render('reports/index', [
            'reportData' => $reportData,
            'categories' => $categories,
            'categoryId' => $categoryId,
            'startDate'  => $startDate,
            'endDate'    => $endDate,
        ]);
    }

    public function printView() {
        Auth::requireLogin();

        $categoryId = !empty($_GET['category_id']) ? (int) $_GET['category_id'] : null;
        $startDate  = !empty($_GET['start_date']) ? trim((string)$_GET['start_date']) : null;
        $endDate    = !empty($_GET['end_date']) ? trim((string)$_GET['end_date']) : null;

        $reportData = Report::getAggregateInventoryReport($categoryId, $startDate, $endDate);
        $categoryName = 'Semua Kategori';
        if ($categoryId) {
            $cat = Category::findById($categoryId);
            if ($cat) $categoryName = $cat['name'];
        }

        $currentUser = Auth::user();
        $printedBy = (isset($currentUser['full_name']) && $currentUser['full_name']) ? $currentUser['full_name'] : $currentUser['username'];

        View::render('reports/print', [
            'reportData'   => $reportData,
            'categoryName' => $categoryName,
            'startDate'    => $startDate,
            'endDate'      => $endDate,
            'printedBy'    => $printedBy,
            'printedAt'    => date('d F Y, H:i:s'),
        ], 'print');
    }

    public function exportCsv() {
        Auth::requireLogin();

        $categoryId = !empty($_GET['category_id']) ? (int) $_GET['category_id'] : null;
        $startDate  = !empty($_GET['start_date']) ? trim((string)$_GET['start_date']) : null;
        $endDate    = !empty($_GET['end_date']) ? trim((string)$_GET['end_date']) : null;

        $reportData = Report::getAggregateInventoryReport($categoryId, $startDate, $endDate);

        $filename = 'laporan-mutasi-inventaris-' . date('Ymd-His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // UTF-8 BOM untuk kompatibilitas Microsoft Excel
        fputs($output, "\xEF\xBB\xBF");

        // Header CSV
        fputcsv($output, [
            'No',
            'SKU',
            'Nama Produk',
            'Kategori',
            'Harga Jual (IDR)',
            'Total Masuk (IN)',
            'Total Keluar (OUT)',
            'Stok Akhir',
            'Batas Min.',
            'Nilai Total Aset (IDR)',
            'Status Stok'
        ], ';');

        $no = 1;
        foreach ($reportData as $row) {
            $status = 'Aman';
            if ((int)$row['current_stock'] === 0) {
                $status = 'Habis';
            } elseif ((int)$row['current_stock'] <= (int)$row['minimum_stock']) {
                $status = 'Menipis';
            }

            fputcsv($output, [
                $no++,
                $row['sku'],
                $row['product_name'],
                $row['category_name'],
                (float) $row['price'],
                (int) $row['total_in'],
                (int) $row['total_out'],
                (int) $row['current_stock'],
                (int) $row['minimum_stock'],
                (float) $row['total_asset_value'],
                $status
            ], ';');
        }

        fclose($output);
        exit;
    }
}
