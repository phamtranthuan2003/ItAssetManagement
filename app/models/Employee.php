<?php
/**
 * File: app/models/Employee.php
 * Model quản lý nhân viên (employees)
 */

require_once __DIR__ . '/../../core/Model.php';

class Employee extends Model {
    protected string $table = 'employees';

    /**
     * Lấy danh sách nhân viên kèm thông tin phòng ban và số lượng thiết bị đang giữ
     */
    public function getAll(?string $status = null, ?int $departmentId = null, int $page = 1, int $perPage = 10): array {
        $where = [];
        $params = [];
        $page = max(1, $page);
        $perPage = max(1, $perPage);
        $offset = ($page - 1) * $perPage;

        if ($status !== null && $status !== '') {
            $where[] = "e.status = :status";
            $params['status'] = $status;
        }

        if ($departmentId !== null && $departmentId > 0) {
            $where[] = "e.department_id = :department_id";
            $params['department_id'] = $departmentId;
        }

        $whereClause = !empty($where) ? "WHERE " . implode(' AND ', $where) : "";

        $sql = "SELECT e.*, d.name AS department_name, d.code AS department_code,
                (SELECT COUNT(*) FROM devices dev WHERE dev.current_employee_id = e.id AND dev.status = 'allocated') AS held_devices_count
                FROM {$this->table} e
                JOIN departments d ON e.department_id = d.id
                {$whereClause}
                ORDER BY e.id DESC
                LIMIT {$perPage} OFFSET {$offset}";

        return $this->fetchAll($sql, $params);
    }

    public function countFiltered(?string $status = null, ?int $departmentId = null): int {
        $where = [];
        $params = [];

        if ($status !== null && $status !== '') {
            $where[] = "status = :status";
            $params['status'] = $status;
        }

        if ($departmentId !== null && $departmentId > 0) {
            $where[] = "department_id = :department_id";
            $params['department_id'] = $departmentId;
        }

        $whereClause = !empty($where) ? "WHERE " . implode(' AND ', $where) : "";
        return $this->count("SELECT COUNT(*) FROM {$this->table} {$whereClause}", $params);
    }

    /**
     * Tìm nhân viên theo ID kèm tên phòng ban
     */
    public function findById(int $id): ?array {
        $sql = "SELECT e.*, d.name AS department_name, d.code AS department_code 
                FROM {$this->table} e
                JOIN departments d ON e.department_id = d.id
                WHERE e.id = :id LIMIT 1";
        return $this->fetch($sql, ['id' => $id]);
    }

    /**
     * Lấy danh sách thiết bị mà nhân viên này đang nắm giữ
     */
    public function getHeldDevices(int $employeeId): array {
        $sql = "SELECT dev.*, spec.cpu, spec.ram_gb, spec.ssd_capacity, spec.gpu
                FROM devices dev
                LEFT JOIN device_specs spec ON dev.id = spec.device_id
                WHERE dev.current_employee_id = :employee_id AND dev.status = 'allocated'
                ORDER BY dev.id DESC";
        return $this->fetchAll($sql, ['employee_id' => $employeeId]);
    }

    /**
     * Thêm mới nhân viên
     */
    public function create(array $data): int {
        do {
            $code = 'NV' . strtoupper(bin2hex(random_bytes(9)));
        } while ($this->isCodeExists($code));

        $sql = "INSERT INTO {$this->table} (code, fullname, email, phone, department_id, position, status)
                VALUES (:code, :fullname, :email, :phone, :department_id, :position, :status)";
        $this->query($sql, [
            'code'          => $code,
            'fullname'      => trim($data['fullname']),
            'email'         => trim($data['email'] ?? ''),
            'phone'         => trim($data['phone'] ?? ''),
            'department_id' => (int)$data['department_id'],
            'position'      => trim($data['position'] ?? ''),
            'status'        => $data['status'] ?? 'working'
        ]);
        return $this->lastInsertId();
    }

    /**
     * Cập nhật thông tin nhân viên
     */
    public function update(int $id, array $data): bool {
        $sql = "UPDATE {$this->table} 
                SET code = COALESCE(:code, code), fullname = :fullname, email = :email, phone = :phone,
                    department_id = :department_id, position = :position, status = :status
                WHERE id = :id";
        return (bool)$this->query($sql, [
            'id'            => $id,
            'code'          => isset($data['code']) ? strtoupper(trim($data['code'])) : null,
            'fullname'      => trim($data['fullname']),
            'email'         => trim($data['email'] ?? ''),
            'phone'         => trim($data['phone'] ?? ''),
            'department_id' => (int)$data['department_id'],
            'position'      => trim($data['position'] ?? ''),
            'status'        => $data['status']
        ]);
    }

    /**
     * Xóa nhân viên (chỉ khi không còn giữ thiết bị nào)
     */
    public function delete(int $id): bool {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        return (bool)$this->query($sql, ['id' => $id]);
    }

    /**
     * Kiểm tra mã nhân viên đã tồn tại chưa
     */
    public function isCodeExists(string $code, int $excludeId = 0): bool {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE code = :code AND id != :excludeId";
        return (bool)$this->count($sql, ['code' => $code, 'excludeId' => $excludeId]);
    }
}
