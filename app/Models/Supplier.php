<?php
/**
 * Model Supplier (Pemasok)
 */

require_once __DIR__ . '/../../config/database.php';

class Supplier {
    public static function getAll() {
        $pdo = Database::getConnection();
        $sql = '
            SELECT s.*, COUNT(ps.product_id) AS total_supplied_products
            FROM suppliers s
            LEFT JOIN product_suppliers ps ON ps.supplier_id = s.id
            GROUP BY s.id, s.name, s.contact_person, s.phone, s.email, s.address, s.created_at
            ORDER BY s.name ASC
        ';
        return $pdo->query($sql)->fetchAll();
    }

    public static function findById($id) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT * FROM suppliers WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => (int) $id]);
        return $stmt->fetch();
    }

    public static function create($name, $contactPerson = '', $phone = '', $email = '', $address = '') {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('
            INSERT INTO suppliers (name, contact_person, phone, email, address, created_at)
            VALUES (:name, :contact_person, :phone, :email, :address, NOW())
        ');
        $stmt->execute([
            'name'           => trim($name),
            'contact_person' => trim($contactPerson),
            'phone'          => trim($phone),
            'email'          => trim($email),
            'address'        => trim($address),
        ]);
        return (int) $pdo->lastInsertId();
    }

    public static function update($id, $name, $contactPerson = '', $phone = '', $email = '', $address = '') {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('
            UPDATE suppliers
            SET name = :name,
                contact_person = :contact_person,
                phone = :phone,
                email = :email,
                address = :address
            WHERE id = :id
        ');
        return $stmt->execute([
            'id'             => (int) $id,
            'name'           => trim($name),
            'contact_person' => trim($contactPerson),
            'phone'          => trim($phone),
            'email'          => trim($email),
            'address'        => trim($address),
        ]);
    }

    public static function getSuppliedProducts($supplierId) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('
            SELECT p.id, p.sku, p.name, p.price, p.stock, ps.supplier_price
            FROM product_suppliers ps
            JOIN products p ON p.id = ps.product_id
            WHERE ps.supplier_id = :supplier_id
            ORDER BY p.name ASC
        ');
        $stmt->execute(['supplier_id' => (int) $supplierId]);
        return $stmt->fetchAll();
    }

    public static function delete($id) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('DELETE FROM suppliers WHERE id = :id');
        return $stmt->execute(['id' => (int) $id]);
    }
}
