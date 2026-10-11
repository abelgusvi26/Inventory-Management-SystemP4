<?php
/**
 * Model Kategori Produk (Categories)
 */

require_once __DIR__ . '/../../config/database.php';

class Category {
    public static function getAll() {
        $pdo = Database::getConnection();
        $sql = '
            SELECT c.id, c.name, c.description, c.created_at,
                   COUNT(p.id) AS total_products,
                   COALESCE(SUM(p.stock), 0) AS total_stock
            FROM categories c
            LEFT JOIN products p ON p.category_id = c.id
            GROUP BY c.id, c.name, c.description, c.created_at
            ORDER BY c.name ASC
        ';
        return $pdo->query($sql)->fetchAll();
    }

    public static function findById($id) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT * FROM categories WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => (int) $id]);
        return $stmt->fetch();
    }

    public static function findByName($name, $excludeId = null) {
        $pdo = Database::getConnection();
        if ($excludeId) {
            $stmt = $pdo->prepare('SELECT id FROM categories WHERE name = :name AND id != :id LIMIT 1');
            $stmt->execute(['name' => $name, 'id' => (int) $excludeId]);
        } else {
            $stmt = $pdo->prepare('SELECT id FROM categories WHERE name = :name LIMIT 1');
            $stmt->execute(['name' => $name]);
        }
        return $stmt->fetch();
    }

    public static function create($name, $description = '') {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('
            INSERT INTO categories (name, description, created_at)
            VALUES (:name, :description, NOW())
        ');
        $stmt->execute([
            'name'        => trim($name),
            'description' => trim($description),
        ]);
        return (int) $pdo->lastInsertId();
    }

    public static function update($id, $name, $description = '') {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('
            UPDATE categories
            SET name = :name, description = :description
            WHERE id = :id
        ');
        return $stmt->execute([
            'id'          => (int) $id,
            'name'        => trim($name),
            'description' => trim($description),
        ]);
    }

    public static function hasProducts($id) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM products WHERE category_id = :id');
        $stmt->execute(['id' => (int) $id]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public static function delete($id) {
        if (self::hasProducts($id)) {
            return false;
        }
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('DELETE FROM categories WHERE id = :id');
        return $stmt->execute(['id' => (int) $id]);
    }
}
