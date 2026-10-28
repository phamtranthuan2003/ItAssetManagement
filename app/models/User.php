<?php
/**
 * File: app/models/User.php
 * Model thao tác với bảng `users`
 */

require_once __DIR__ . '/../../core/Model.php';

class User extends Model {
    protected string $table = 'users';

    /**
     * Tìm user theo username
     */
    public function findByUsername(string $username): ?array {
        $sql = "SELECT * FROM {$this->table} WHERE username = :username AND status = 1 LIMIT 1";
        return $this->fetch($sql, ['username' => $username]);
    }

    /**
     * Tìm user theo ID
     */
    public function findById(int $id): ?array {
        $sql = "SELECT u.id, u.username, u.fullname, u.email, u.role, r.name AS role_name, u.status, u.created_at
                FROM {$this->table} u LEFT JOIN roles r ON r.code = u.role WHERE u.id = :id LIMIT 1";
        return $this->fetch($sql, ['id' => $id]);
    }

    /**
     * Lấy toàn bộ danh sách users
     */
    public function getAll(): array {
        $sql = "SELECT u.id, u.username, u.fullname, u.email, u.role, r.name AS role_name, u.status, u.created_at
                FROM {$this->table} u LEFT JOIN roles r ON r.code = u.role ORDER BY u.id DESC";
        return $this->fetchAll($sql);
    }

    public function roleExists(string $role): bool {
        return $this->count('SELECT COUNT(*) FROM roles WHERE code = :role', ['role' => $role]) > 0;
    }

    /**
     * Tạo tài khoản mới
     */
    public function create(array $data): int {
        $sql = "INSERT INTO {$this->table} (username, password, fullname, email, role, status) 
                VALUES (:username, :password, :fullname, :email, :role, :status)";
        $this->query($sql, [
            'username' => $data['username'],
            'password' => password_hash($data['password'], PASSWORD_BCRYPT),
            'fullname' => $data['fullname'],
            'email'    => $data['email'] ?? null,
            'role'     => $data['role'] ?? 'warehouse',
            'status'   => $data['status'] ?? 1
        ]);
        return $this->lastInsertId();
    }

    /**
     * Cập nhật thông tin tài khoản
     */
    public function update(int $id, array $data): bool {
        $fields = [
            'fullname = :fullname',
            'email = :email',
            'role = :role',
            'status = :status'
        ];
        $params = [
            'id'       => $id,
            'fullname' => $data['fullname'],
            'email'    => $data['email'] ?? null,
            'role'     => $data['role'],
            'status'   => $data['status']
        ];

        if (!empty($data['password'])) {
            $fields[] = 'password = :password';
            $params['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = :id";
        return (bool)$this->query($sql, $params);
    }

    /**
     * Xóa tài khoản
     */
    public function delete(int $id): bool {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        return (bool)$this->query($sql, ['id' => $id]);
    }

    /**
     * Kiểm tra username đã tồn tại chưa
     */
    public function isUsernameExists(string $username, int $excludeId = 0): bool {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE username = :username AND id != :excludeId";
        return (bool)$this->count($sql, ['username' => $username, 'excludeId' => $excludeId]);
    }
}
