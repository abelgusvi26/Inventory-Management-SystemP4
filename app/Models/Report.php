<?php
/**
 * Model Laporan dan Statistik Agregat Inventaris
 */

require_once __DIR__ . '/../../config/database.php';

class Report {
    /**
     * Menghitung ringkasan statistik untuk dashboard (PRD Bagian 9.1)
     */
    public static function getDashboardSummary() {
        $pdo = Database::getConnection();

        // 1. Total Produk & Total Nilai Inventaris
        $productStats = $pdo->query('
            SELECT 
                COUNT(*) AS total_products,
                COALESCE(SUM(stock * price), 0) AS total_inventory_value,
                SUM(CASE WHEN stock <= minimum_stock THEN 1 ELSE 0 END) AS low_stock_count,
                SUM(CASE WHEN stock = 0 THEN 1 ELSE 0 END) AS out_of_stock_count
            FROM products
        ')->fetch();

        // 2. Total Barang Masuk & Barang Keluar
        $movementStats = $pdo->query("
            SELECT 
                COALESCE(SUM(CASE WHEN type = 'IN' THEN quantity ELSE 0 END), 0) AS total_stock_in,
                COALESCE(SUM(CASE WHEN type = 'OUT' THEN quantity ELSE 0 END), 0) AS total_stock_out,
                COUNT(*) AS total_transactions
            FROM stock_movements
        ")->fetch();

        // 3. Total Kategori & Supplier
        $categoryCount = (int) $pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn();
        $supplierCount = (int) $pdo->query('SELECT COUNT(*) FROM suppliers')->fetchColumn();

        return [
            'total_products'        => (int) (isset($productStats['total_products']) ? $productStats['total_products'] : 0),
            'total_inventory_value' => (float) (isset($productStats['total_inventory_value']) ? $productStats['total_inventory_value'] : 0),
            'low_stock_count'       => (int) (isset($productStats['low_stock_count']) ? $productStats['low_stock_count'] : 0),
            'out_of_stock_count'    => (int) (isset($productStats['out_of_stock_count']) ? $productStats['out_of_stock_count'] : 0),
            'total_stock_in'        => (int) (isset($movementStats['total_stock_in']) ? $movementStats['total_stock_in'] : 0),
            'total_stock_out'       => (int) (isset($movementStats['total_stock_out']) ? $movementStats['total_stock_out'] : 0),
            'total_transactions'    => (int) (isset($movementStats['total_transactions']) ? $movementStats['total_transactions'] : 0),
            'total_categories'      => $categoryCount,
            'total_suppliers'       => $supplierCount,
        ];
    }

    /**
     * Menghasilkan Laporan Agregat Mutasi Inventaris Per Produk (PRD Bagian 9.2)
     * Formula: stok akhir = stok saat ini, akumulasi total_in dan total_out
     */
    public static function getAggregateInventoryReport($categoryId = null, $startDate = null, $endDate = null) {
        $pdo = Database::getConnection();

        $params = [];
        $joinConditions = ['sm.product_id = p.id'];

        if (!empty($startDate)) {
            $joinConditions[] = 'DATE(sm.created_at) >= :start_date';
            $params['start_date'] = $startDate;
        }

        if (!empty($endDate)) {
            $joinConditions[] = 'DATE(sm.created_at) <= :end_date';
            $params['end_date'] = $endDate;
        }

        $joinClause = implode(' AND ', $joinConditions);

        $where = [];
        if (!empty($categoryId)) {
            $where[] = 'p.category_id = :category_id';
            $params['category_id'] = (int) $categoryId;
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $sql = "
            SELECT 
                p.id,
                p.sku,
                p.name AS product_name,
                p.price,
                p.stock AS current_stock,
                p.minimum_stock,
                c.name AS category_name,
                COALESCE(SUM(CASE WHEN sm.type = 'IN' THEN sm.quantity ELSE 0 END), 0) AS total_in,
                COALESCE(SUM(CASE WHEN sm.type = 'OUT' THEN sm.quantity ELSE 0 END), 0) AS total_out,
                COUNT(sm.id) AS movement_count,
                (p.stock * p.price) AS total_asset_value
            FROM products p
            LEFT JOIN categories c ON c.id = p.category_id
            LEFT JOIN stock_movements sm ON ({$joinClause})
            {$whereClause}
            GROUP BY p.id, p.sku, p.name, p.price, p.stock, p.minimum_stock, c.name
            ORDER BY c.name ASC, p.name ASC
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
