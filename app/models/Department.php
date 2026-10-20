<?php
/**
 * File: app/models/Department.php
 * Model quản lý phòng ban (departments)
 */

require_once __DIR__ . '/../../core/Model.php';

class Department extends Model {
    protected string $table = 'departments';

    /**
     * Lấy toàn bộ danh sách phòng ban kèm số lượng nhân viên và số thiết bị đang sử dụng
     */
    public function getAll(): array {
        $sql = "SELECT d.*, 
                (SELECT COUNT(*) FROM employees e WHERE e.department_id = d.id AND e.status = 'working') AS active_employees_count,
                (SELECT COUNT(*) FROM devices dev WHERE dev.current_department_id = d.id AND dev.status = 'allocated') AS allocated_devices_count
                FROM {$this->table} d
                ORDER BY d.id ASC";
        return $this->fetchAll($sql);
    }

    /**
     * Tìm phòng ban theo ID
     */
    public function findById(int $id): ?array {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        return $this->fetch($sql, ['id' => $id]);
    }

    /**
     * Thêm mới phòng ban
     */
    public function create(array $data): int {
        $sql = "INSERT INTO {$this->table} (code, name, description) 
                VALUES (:code, :name, :description)";
        $this->query($sql, [
            'code'        => strtoupper(trim($data['code'])),
            'name'        => trim($data['data_name'] ?? $data['name']),
            'description' => trim($data['description'] ?? '')
        ]);
        return $this->lastInsertId();
    }

    /**
     * Cập nhật thông tin phòng ban
     */
    public function update(int $id, array $data): bool {
        $sql = "UPDATE {$this->table} 
                SET code = :code, name = :name, description = :description 
                WHERE id = :id";
        return (bool)$this->query($sql, [
            'id'          => $id,
            'code'        => strtoupper(trim($data['code'])),
            'name'        => trim($data['name']),
            'description' => trim($data['description'] ?? '')
        ]);
    }

    /**
     * Kiểm tra phòng ban có thể xóa không (nếu có nhân viên hoặc thiết bị thì chặn)
     */
    public function canDelete(int $id): bool {
        $empCount = $this->count("SELECT COUNT(*) FROM employees WHERE department_id = :id", ['id' => $id]);
        $devCount = $this->count("SELECT COUNT(*) FROM devices WHERE current_department_id = :id", ['id' => $id]);
        return ($empCount === 0 && $devCount === 0);
    }

    /**
     * Xóa phòng ban
     */
    public function delete(int $id): bool {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        return (bool)$this->query($sql, ['id' => $id]);
    }

    /**
     * Kiểm tra mã phòng ban đã tồn tại chưa
     */
    public function isCodeExists(string $code, int $excludeId = 0): bool {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE code = :code AND id != :excludeId";
        return (bool)$this->count($sql, ['code' => $code, 'excludeId' => $excludeId]);
    }
}
