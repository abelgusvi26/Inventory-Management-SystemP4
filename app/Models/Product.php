<?php
/**
 * Model Produk (Products)
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/ProductSupplier.php';

class Product {
    /**
     * Mengambil daftar produk dengan fitur Pencarian, Filter Kategori, Sorting Aman, dan Paginasi
     */
    public static function getPaginated($search = '', $categoryId = null, $sortBy = 'created_at', $sortDir = 'DESC', $page = 1, $perPage = 10) {
        $pdo = Database::getConnection();

        // 1. Validasi Allowlist Kolom Sorting
        $allowlist = ['name', 'price', 'stock', 'sku', 'created_at', 'category_name'];
        if (!in_array($sortBy, $allowlist, true)) {
            $sortBy = 'created_at';
        }
        $sortDir = (strtoupper($sortDir) === 'ASC') ? 'ASC' : 'DESC';

        // Mapping nama kolom untuk sorting SQL
        $sortColumnSql = ($sortBy === 'category_name') ? 'c.name' : 'p.' . $sortBy;

        // 2. Susun Klausul WHERE
        $where = [];
        $params = [];

        if (!empty($search)) {
            $where[] = '(p.name LIKE :search_name OR p.sku LIKE :search_sku)';
            $params['search_name'] = '%' . $search . '%';
            $params['search_sku']  = '%' . $search . '%';
        }

        if (!empty($categoryId)) {
            $where[] = 'p.category_id = :category_id';
            $params['category_id'] = (int) $categoryId;
        }

        $whereSql = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        // 3. Hitung Total Data (Paginasi)
        $countSql = "SELECT COUNT(*) FROM products p LEFT JOIN categories c ON c.id = p.category_id {$whereSql}";
        $stmtCount = $pdo->prepare($countSql);
        $stmtCount->execute($params);
        $totalRecords = (int) $stmtCount->fetchColumn();

        $page = max(1, (int) $page);
        $perPage = max(1, (int) $perPage);
        $totalPages = (int) ceil($totalRecords / $perPage);
        $offset = ($page - 1) * $perPage;

        // 4. Ambil Data Halaman Ini
        $sql = "
            SELECT p.id, p.category_id, p.sku, p.name, p.price, p.stock, p.minimum_stock,
                   p.created_at, p.updated_at,
                   c.name AS category_name,
                   COUNT(sm.id) AS movement_count
            FROM products p
            LEFT JOIN categories c ON c.id = p.category_id
            LEFT JOIN stock_movements sm ON sm.product_id = p.id
            {$whereSql}
            GROUP BY p.id, p.category_id, p.sku, p.name, p.price, p.stock, p.minimum_stock,
                     p.created_at, p.updated_at, c.name
            ORDER BY {$sortColumnSql} {$sortDir}
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $pdo->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue(':' . $k, $v);
        }
        $stmt->bindValue(':limit', (int) $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        $stmt->execute();
        $records = $stmt->fetchAll();

        return [
            'data'         => $records,
            'total'        => $totalRecords,
            'page'         => $page,
            'per_page'     => $perPage,
            'total_pages'  => max(1, $totalPages),
            'sort_by'      => $sortBy,
            'sort_dir'     => $sortDir,
        ];
    }

    public static function findById($id) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('
            SELECT p.*, c.name AS category_name
            FROM products p
            LEFT JOIN categories c ON c.id = p.category_id
            WHERE p.id = :id
            LIMIT 1
        ');
        $stmt->execute(['id' => (int) $id]);
        return $stmt->fetch();
    }

    public static function findBySku($sku, $excludeId = null) {
        $pdo = Database::getConnection();
        if ($excludeId) {
            $stmt = $pdo->prepare('SELECT id, sku, name FROM products WHERE sku = :sku AND id != :id LIMIT 1');
            $stmt->execute(['sku' => trim($sku), 'id' => (int) $excludeId]);
        } else {
            $stmt = $pdo->prepare('SELECT id, sku, name FROM products WHERE sku = :sku LIMIT 1');
            $stmt->execute(['sku' => trim($sku)]);
        }
        return $stmt->fetch();
    }

    public static function create($categoryId, $sku, $name, $price, $stock, $minimumStock, array $suppliers = []) {
        $pdo = Database::getConnection();
        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare('
                INSERT INTO products (category_id, sku, name, price, stock, minimum_stock, created_at)
                VALUES (:category_id, :sku, :name, :price, :stock, :minimum_stock, NOW())
            ');
            $stmt->execute([
                'category_id'   => (int) $categoryId,
                'sku'           => strtoupper(trim($sku)),
                'name'          => trim($name),
                'price'         => max(0, (float) $price),
                'stock'         => max(0, (int) $stock),
                'minimum_stock' => max(0, (int) $minimumStock),
            ]);
            $productId = (int) $pdo->lastInsertId();

            // Simpan relasi many-to-many ke suppliers
            if (!empty($suppliers)) {
                ProductSupplier::syncForProduct($productId, $suppliers);
            }

            // Jika stok awal > 0, catat mutasi stok masuk awal
            if ((int) $stock > 0 && !empty($_SESSION['user']['id'])) {
                $stmtMove = $pdo->prepare('
                    INSERT INTO stock_movements (product_id, user_id, type, quantity, reference_number, notes, created_at)
                    VALUES (:product_id, :user_id, "IN", :quantity, :reference, "Saldo stok awal saat produk baru didaftarkan", NOW())
                ');
                $stmtMove->execute([
                    'product_id' => $productId,
                    'user_id'    => (int) $_SESSION['user']['id'],
                    'quantity'   => (int) $stock,
                    'reference'  => 'INIT-' . strtoupper(trim($sku)),
                ]);
            }

            $pdo->commit();
            return $productId;
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function update($id, $categoryId, $name, $price, $minimumStock, array $suppliers = []) {
        $pdo = Database::getConnection();
        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare('
                UPDATE products
                SET category_id = :category_id,
                    name = :name,
                    price = :price,
                    minimum_stock = :minimum_stock
                WHERE id = :id
            ');
            $stmt->execute([
                'id'            => (int) $id,
                'category_id'   => (int) $categoryId,
                'name'          => trim($name),
                'price'         => max(0, (float) $price),
                'minimum_stock' => max(0, (int) $minimumStock),
            ]);

            // Sinkronisasi relasi supplier
            ProductSupplier::syncForProduct((int) $id, $suppliers);

            $pdo->commit();
            return true;
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function hasMovements($id) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM stock_movements WHERE product_id = :id');
        $stmt->execute(['id' => (int) $id]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public static function delete($id) {
        // Business Rule 5: Produk yang memiliki histori tidak boleh dihapus sembarangan
        if (self::hasMovements($id)) {
            return false;
        }

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('DELETE FROM products WHERE id = :id');
        return $stmt->execute(['id' => (int) $id]);
    }

    public static function getLowStockProducts($limit = 10) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('
            SELECT p.id, p.sku, p.name, p.price, p.stock, p.minimum_stock, c.name AS category_name
            FROM products p
            LEFT JOIN categories c ON c.id = p.category_id
            WHERE p.stock <= p.minimum_stock
            ORDER BY p.stock ASC, p.name ASC
            LIMIT :limit
        ');
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function getAllSimple() {
        $pdo = Database::getConnection();
        $sql = 'SELECT id, sku, name, stock, minimum_stock, price FROM products ORDER BY name ASC';
        return $pdo->query($sql)->fetchAll();
    }
}
