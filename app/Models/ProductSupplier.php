<?php
/**
 * Model Junction Product Suppliers (Many-to-Many)
 * Menyimpan relasi produk dengan pemasok dan harga beli (supplier_price)
 */

require_once __DIR__ . '/../../config/database.php';

class ProductSupplier {
    public static function getByProductId($productId) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('
            SELECT ps.supplier_id, ps.supplier_price, s.name AS supplier_name, s.phone, s.email
            FROM product_suppliers ps
            JOIN suppliers s ON s.id = ps.supplier_id
            WHERE ps.product_id = :product_id
            ORDER BY s.name ASC
        ');
        $stmt->execute(['product_id' => (int) $productId]);
        return $stmt->fetchAll();
    }

    public static function syncForProduct($productId, array $suppliers) {
        $pdo = Database::getConnection();
        // Hapus relasi lama
        $stmtDel = $pdo->prepare('DELETE FROM product_suppliers WHERE product_id = :product_id');
        $stmtDel->execute(['product_id' => (int) $productId]);

        if (empty($suppliers)) {
            return;
        }

        $stmtIns = $pdo->prepare('
            INSERT INTO product_suppliers (product_id, supplier_id, supplier_price)
            VALUES (:product_id, :supplier_id, :supplier_price)
        ');

        foreach ($suppliers as $item) {
            $suppId = (int) (isset($item['supplier_id']) ? $item['supplier_id'] : 0);
            $price  = (float) (isset($item['supplier_price']) ? $item['supplier_price'] : 0);
            if ($suppId > 0) {
                $stmtIns->execute([
                    'product_id'     => (int) $productId,
                    'supplier_id'    => $suppId,
                    'supplier_price' => max(0, $price),
                ]);
            }
        }
    }
}
