<?php
/**
 * Model Pengguna (User & User Profile)
 */

require_once __DIR__ . '/../../config/database.php';

class User {
    public static function findByUsername($username) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('
            SELECT u.id, u.username, u.password_hash, u.role, u.created_at,
                   up.full_name, up.phone, up.bio
            FROM users u
            LEFT JOIN user_profiles up ON up.user_id = u.id
            WHERE u.username = :username
            LIMIT 1
        ');
        $stmt->execute(['username' => $username]);
        return $stmt->fetch();
    }

    public static function findById($id) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('
            SELECT u.id, u.username, u.role, u.created_at,
                   up.full_name, up.phone, up.bio
            FROM users u
            LEFT JOIN user_profiles up ON up.user_id = u.id
            WHERE u.id = :id
            LIMIT 1
        ');
        $stmt->execute(['id' => (int) $id]);
        return $stmt->fetch();
    }

    public static function getAll() {
        $pdo = Database::getConnection();
        $sql = '
            SELECT u.id, u.username, u.role, u.created_at,
                   up.full_name, up.phone,
                   COUNT(sm.id) AS transaction_count
            FROM users u
            LEFT JOIN user_profiles up ON up.user_id = u.id
            LEFT JOIN stock_movements sm ON sm.user_id = u.id
            GROUP BY u.id, u.username, u.role, u.created_at, up.full_name, up.phone
            ORDER BY u.id ASC
        ';
        return $pdo->query($sql)->fetchAll();
    }

    public static function create($username, $password, $role, $fullName, $phone = '', $bio = '') {
        $pdo = Database::getConnection();
        $pdo->beginTransaction();

        try {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare('
                INSERT INTO users (username, password_hash, role, created_at)
                VALUES (:username, :hash, :role, NOW())
            ');
            $stmt->execute([
                'username' => $username,
                'hash'     => $hash,
                'role'     => $role,
            ]);
            $userId = (int) $pdo->lastInsertId();

            $stmtProfile = $pdo->prepare('
                INSERT INTO user_profiles (user_id, full_name, phone, bio)
                VALUES (:user_id, :full_name, :phone, :bio)
            ');
            $stmtProfile->execute([
                'user_id'   => $userId,
                'full_name' => $fullName,
                'phone'     => $phone,
                'bio'       => $bio,
            ]);

            $pdo->commit();
            return $userId;
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function update($id, $role, $fullName, $phone = '', $bio = '', $newPassword = null) {
        $pdo = Database::getConnection();
        $pdo->beginTransaction();

        try {
            if (!empty($newPassword)) {
                $hash = password_hash($newPassword, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare('UPDATE users SET role = :role, password_hash = :hash WHERE id = :id');
                $stmt->execute(['role' => $role, 'hash' => $hash, 'id' => $id]);
            } else {
                $stmt = $pdo->prepare('UPDATE users SET role = :role WHERE id = :id');
                $stmt->execute(['role' => $role, 'id' => $id]);
            }

            // Update atau insert profil jika belum ada
            $stmtProf = $pdo->prepare('
                INSERT INTO user_profiles (user_id, full_name, phone, bio)
                VALUES (:user_id, :full_name, :phone, :bio)
                ON DUPLICATE KEY UPDATE full_name = VALUES(full_name), phone = VALUES(phone), bio = VALUES(bio)
            ');
            $stmtProf->execute([
                'user_id'   => $id,
                'full_name' => $fullName,
                'phone'     => $phone,
                'bio'       => $bio,
            ]);

            $pdo->commit();
            return true;
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function delete($id) {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('DELETE FROM users WHERE id = :id');
        return $stmt->execute(['id' => (int) $id]);
    }

    public static function countAdmins() {
        $pdo = Database::getConnection();
        $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'");
        return (int) $stmt->fetchColumn();
    }
}
