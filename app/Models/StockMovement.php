<?php
/**
 * Model Mutasi Stok (Stock Movements)
 * Menjalankan transaksi barang masuk (IN) dan barang keluar (OUT) dengan transaksi atomik PDO
 */

require_once __DIR__ . '/../../config/database.php';

class StockMovement {
    /**
     * Mencatat transaksi mutasi stok dengan transaksi database (ACID)
     * 
     * @param int $productId ID Produk
     * @param int $userId ID User pencatat transaksi
     * @param string $type Jenis mutasi ('IN' atau 'OUT')
     * @param int $quantity Jumlah barang
     * @param string $reference Nomor referensi dokumen/surat jalan
     * @param string $notes Catatan transaksi
     * @return array Hasil transaksi: ['success' => bool, 'message' => string, 'movement_id' => int|null]
     */
    public static function record($productId, $userId, $type, $quantity, $reference = '', $notes = '') {
        $type = strtoupper(trim((string)$type));
        $quantity = (int) $quantity;
        $productId = (int) $productId;
        $userId = (int) $userId;

        if (!in_array($type, ['IN', 'OUT'], true)) {
            return ['success' => false, 'message' => 'Tipe transaksi mutasi harus IN (Masuk) atau OUT (Keluar).'];
        }

        if ($quantity <= 0) {
            return ['success' => false, 'message' => 'Jumlah barang harus berupa bilangan bulat lebih dari nol.'];
        }

        $pdo = Database::getConnection();
        $pdo->beginTransaction();

        try {
            // 1. Kunci baris data produk dengan FOR UPDATE untuk mencegah race condition
            $stmtProduct = $pdo->prepare('SELECT id, name, sku, stock FROM products WHERE id = :id FOR UPDATE');
            $stmtProduct->execute(['id' => $productId]);
            $product = $stmtProduct->fetch();

            if (!$product) {
                $pdo->rollBack();
                return ['success' => false, 'message' => 'Produk tidak ditemukan dalam sistem.'];
            }

            $currentStock = (int) $product['stock'];

            // 2. Validasi Aturan Bisnis Khusus Barang Keluar
            if ($type === 'OUT') {
                if ($quantity > $currentStock) {
                    $pdo->rollBack();
                    return [
                        'success' => false,
                        'message' => "Stok tidak mencukupi untuk pengeluaran barang! Stok tersedia saat ini: {$currentStock} unit, permintaan keluar: {$quantity} unit."
                    ];
                }
                $newStock = $currentStock - $quantity;
            } else {
                $newStock = $currentStock + $quantity;
            }

            // 3. Perbarui stok produk
            $stmtUpdateStock = $pdo->prepare('UPDATE products SET stock = :stock WHERE id = :id');
            $stmtUpdateStock->execute(['stock' => $newStock, 'id' => $productId]);

            // 4. Catat histori mutasi stok ke tabel stock_movements
            $stmtMovement = $pdo->prepare('
                INSERT INTO stock_movements (product_id, user_id, type, quantity, reference_number, notes, created_at)
                VALUES (:product_id, :user_id, :type, :quantity, :reference, :notes, NOW())
            ');
            $stmtMovement->execute([
                'product_id' => $productId,
                'user_id'    => $userId,
                'type'       => $type,
                'quantity'   => $quantity,
                'reference'  => trim($reference) ?: null,
                'notes'      => trim($notes) ?: null,
            ]);
            $movementId = (int) $pdo->lastInsertId();

            // 5. Commit transaksi
            $pdo->commit();

            return [
                'success'     => true,
                'message'     => "Transaksi barang " . ($type === 'IN' ? 'masuk' : 'keluar') . " berhasil dicatat. Stok terkini: {$newStock} unit.",
                'movement_id' => $movementId,
                'new_stock'   => $newStock,
            ];
        } catch (Throwable $e) {
            $pdo->rollBack();
            error_log("Gagal memproses transaksi stok: " . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan sistem saat memproses transaksi: ' . $e->getMessage()];
        }
    }

    /**
     * Mengambil daftar histori pergerakan stok dengan filter dan paginasi
     */
    public static function getPaginated($productId = null, $type = null, $startDate = null, $endDate = null, $page = 1, $perPage = 15) {
        $pdo = Database::getConnection();

        $where = [];
        $params = [];

        if (!empty($productId)) {
            $where[] = 'sm.product_id = :product_id';
            $params['product_id'] = (int) $productId;
        }

        if (!empty($type) && in_array(strtoupper($type), ['IN', 'OUT'], true)) {
            $where[] = 'sm.type = :type';
            $params['type'] = strtoupper($type);
        }

        if (!empty($startDate)) {
            $where[] = 'DATE(sm.created_at) >= :start_date';
            $params['start_date'] = $startDate;
        }

        if (!empty($endDate)) {
            $where[] = 'DATE(sm.created_at) <= :end_date';
            $params['end_date'] = $endDate;
        }

        $whereSql = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        // Hitung total
        $countSql = "SELECT COUNT(*) FROM stock_movements sm {$whereSql}";
        $stmtCount = $pdo->prepare($countSql);
        $stmtCount->execute($params);
        $totalRecords = (int) $stmtCount->fetchColumn();

        $page = max(1, (int) $page);
        $perPage = max(1, (int) $perPage);
        $totalPages = (int) ceil($totalRecords / $perPage);
        $offset = ($page - 1) * $perPage;

        $sql = "
            SELECT sm.id, sm.product_id, sm.user_id, sm.type, sm.quantity,
                   sm.reference_number, sm.notes, sm.created_at,
                   p.sku, p.name AS product_name,
                   u.username, up.full_name AS user_full_name
            FROM stock_movements sm
            JOIN products p ON p.id = sm.product_id
            JOIN users u ON u.id = sm.user_id
            LEFT JOIN user_profiles up ON up.user_id = u.id
            {$whereSql}
            ORDER BY sm.created_at DESC, sm.id DESC
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
            'data'        => $records,
            'total'       => $totalRecords,
            'page'        => $page,
            'per_page'    => $perPage,
            'total_pages' => max(1, $totalPages),
        ];
    }

    /**
     * Transaksi mutasi terbaru untuk widget dashboard
     */
    public static function getRecent($limit = 6) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('
            SELECT sm.id, sm.type, sm.quantity, sm.reference_number, sm.created_at,
                   p.sku, p.name AS product_name,
                   up.full_name AS user_full_name
            FROM stock_movements sm
            JOIN products p ON p.id = sm.product_id
            JOIN users u ON u.id = sm.user_id
            LEFT JOIN user_profiles up ON up.user_id = u.id
            ORDER BY sm.created_at DESC, sm.id DESC
            LIMIT :limit
        ');
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
