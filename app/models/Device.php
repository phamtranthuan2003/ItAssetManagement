<?php
/**
 * File: app/models/Device.php
 * Model quản lý thiết bị trong kho
 */

require_once __DIR__ . '/../../core/Model.php';
require_once __DIR__ . '/DeviceSpec.php';
require_once __DIR__ . '/DeviceHistory.php';

class Device extends Model {
    protected string $table = 'devices';

    /**
     * Xây dựng mệnh đề WHERE và danh sách tham số từ mảng bộ lọc
     */
    private function buildFilterConditions(array $filters): array {
        $where = [];
        $params = [];

        // Tìm kiếm theo loại, hãng, model và serial.
        if (!empty($filters['keyword'])) {
            $kw = '%' . trim($filters['keyword']) . '%';
            $where[] = "(d.type LIKE :kw1 OR d.brand LIKE :kw2 OR d.model LIKE :kw3 OR d.serial_number LIKE :kw4)";
            $params['kw1'] = $kw;
            $params['kw2'] = $kw;
            $params['kw3'] = $kw;
            $params['kw4'] = $kw;
        }

        // Lọc theo loại (PC, Laptop)
        if (!empty($filters['type'])) {
            $where[] = "d.type = :type";
            $params['type'] = $filters['type'];
        }

        // Lọc theo Hãng
        if (!empty($filters['brand'])) {
            $where[] = "d.brand = :brand";
            $params['brand'] = $filters['brand'];
        }

        // Lọc theo Trạng thái (in_stock, allocated, maintenance, liquidated)
        if (!empty($filters['status'])) {
            $where[] = "d.status = :status";
            $params['status'] = $filters['status'];
        }

        // Lọc theo Tình trạng (new, used, broken, under_warranty)
        if (!empty($filters['condition_state'])) {
            $where[] = "d.condition_state = :condition_state";
            $params['condition_state'] = $filters['condition_state'];
        }

        // Lọc theo Phòng ban đang sử dụng
        if (!empty($filters['department_id'])) {
            $where[] = "d.current_department_id = :department_id";
            $params['department_id'] = (int)$filters['department_id'];
        }

        if (!empty($filters['employee_id'])) {
            $where[] = "d.current_employee_id = :employee_id";
            $params['employee_id'] = (int)$filters['employee_id'];
        }

        // Lọc theo cấu hình CPU
        if (!empty($filters['cpu'])) {
            $where[] = "s.cpu LIKE :cpu";
            $params['cpu'] = '%' . trim($filters['cpu']) . '%';
        }

        // Lọc theo RAM tối thiểu
        if (!empty($filters['ram_gb'])) {
            $where[] = "s.ram_gb >= :ram_gb";
            $params['ram_gb'] = (int)$filters['ram_gb'];
        }

        // Lọc theo SSD
        if (!empty($filters['ssd_capacity'])) {
            $where[] = "s.ssd_capacity LIKE :ssd_capacity";
            $params['ssd_capacity'] = '%' . trim($filters['ssd_capacity']) . '%';
        }

        // Lọc thiết bị cấp tạm quá hạn
        if (!empty($filters['is_overdue']) && $filters['is_overdue'] == 1) {
            $where[] = "d.is_temporary = 1 AND d.status = 'allocated' AND d.expected_return_date < CURDATE()";
        }

        // Lọc thiết bị sắp hết hạn bảo hành (trong vòng 30 ngày)
        if (!empty($filters['expiring_warranty']) && $filters['expiring_warranty'] == 1) {
            $where[] = "d.warranty_expiry IS NOT NULL AND d.warranty_expiry >= CURDATE() AND d.warranty_expiry <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)";
        }

        $whereClause = !empty($where) ? "WHERE " . implode(' AND ', $where) : "";
        return [$whereClause, $params];
    }

    /**
     * Lấy danh sách thiết bị có phân trang và bộ lọc nâng cao
     */
    public function getList(array $filters = [], int $page = 1, int $perPage = 10): array {
        [$whereClause, $params] = $this->buildFilterConditions($filters);
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT d.*, 
                       s.cpu, s.mainboard, s.ram_gb, s.ram_type, s.ssd_capacity, s.hdd_capacity, s.gpu, s.screen_size,
                       e.fullname AS employee_name, e.code AS employee_code,
                       dep.name AS department_name, dep.code AS department_code
                FROM {$this->table} d
                LEFT JOIN device_specs s ON d.id = s.device_id
                LEFT JOIN employees e ON d.current_employee_id = e.id
                LEFT JOIN departments dep ON d.current_department_id = dep.id
                {$whereClause}
                ORDER BY d.id DESC
                LIMIT {$perPage} OFFSET {$offset}";

        return $this->fetchAll($sql, $params);
    }

    /**
     * Đếm tổng số bản ghi phù hợp bộ lọc (để tính số trang)
     */
    public function countFiltered(array $filters = []): int {
        [$whereClause, $params] = $this->buildFilterConditions($filters);

        $sql = "SELECT COUNT(*) 
                FROM {$this->table} d
                LEFT JOIN device_specs s ON d.id = s.device_id
                {$whereClause}";

        return $this->count($sql, $params);
    }

    /**
     * Lấy toàn bộ danh sách phù hợp bộ lọc để xuất Excel / CSV
     */
    public function getAllForExport(array $filters = []): array {
        [$whereClause, $params] = $this->buildFilterConditions($filters);

        $sql = "SELECT d.*, 
                       s.cpu, s.mainboard, s.ram_gb, s.ram_type, s.ssd_capacity, s.hdd_capacity, s.gpu, s.screen_size,
                       (SELECT GROUP_CONCAT(
                           CONCAT_WS(' ', NULLIF(TRIM(dm.brand), ''), NULLIF(TRIM(dm.model), ''), IF(dm.size IS NOT NULL, CONCAT(dm.size, ' inch'), NULL))
                           ORDER BY dm.monitor_no SEPARATOR '; '
                       )
                        FROM device_monitors dm
                        WHERE dm.device_id = d.id) AS monitor_details,
                       e.fullname AS employee_name, e.code AS employee_code,
                       dep.name AS department_name
                FROM {$this->table} d
                LEFT JOIN device_specs s ON d.id = s.device_id
                LEFT JOIN employees e ON d.current_employee_id = e.id
                LEFT JOIN departments dep ON d.current_department_id = dep.id
                {$whereClause}
                ORDER BY d.id DESC";

        return $this->fetchAll($sql, $params);
    }

    /**
     * Tìm thiết bị theo ID kèm chi tiết cấu hình và người đang giữ
     */
    public function findByIdWithSpec(int $id): ?array {
        $sql = "SELECT d.*, 
                       s.cpu, s.mainboard, s.ram_gb, s.ram_type, s.ssd_capacity, s.hdd_capacity, s.gpu, s.screen_size,
                       s.notes AS spec_notes,
                       e.fullname AS employee_name, e.code AS employee_code, e.phone AS employee_phone, e.email AS employee_email,
                       dep.name AS department_name, dep.code AS department_code
                FROM {$this->table} d
                LEFT JOIN device_specs s ON d.id = s.device_id
                LEFT JOIN employees e ON d.current_employee_id = e.id
                LEFT JOIN departments dep ON d.current_department_id = dep.id
                WHERE d.id = :id LIMIT 1";

        $device = $this->fetch($sql, ['id' => $id]);
        if ($device) {
            $device['monitors'] = $this->fetchAll(
                "SELECT brand, model, size FROM device_monitors WHERE device_id = :device_id ORDER BY monitor_no ASC",
                ['device_id' => $id]
            );
        }
        return $device;
    }

    /**
     * Thêm mới thiết bị và cấu hình trong một Transaction
     */
    public function create(array $deviceData, array $specData, int $userId): int {
        $this->beginTransaction();
        try {
            do {
                $deviceCode = 'TB' . strtoupper(bin2hex(random_bytes(12)));
            } while ($this->isCodeExists($deviceCode));

            $sql = "INSERT INTO {$this->table} 
                    (device_code, type, brand, model, serial_number, import_date, warranty_expiry, condition_state, status, notes)
                    VALUES 
                    (:device_code, :type, :brand, :model, :serial_number, :import_date, :warranty_expiry, :condition_state, :status, :notes)";

            $this->query($sql, [
                'device_code'     => $deviceCode,
                'type'            => $deviceData['type'],
                'brand'           => trim($deviceData['brand']),
                'model'           => trim($deviceData['model']),
                'serial_number'   => !empty($deviceData['serial_number']) ? trim($deviceData['serial_number']) : null,
                'import_date'     => $deviceData['import_date'],
                'warranty_expiry' => !empty($deviceData['warranty_expiry']) ? $deviceData['warranty_expiry'] : null,
                'condition_state' => $deviceData['condition_state'] ?? 'new',
                'status'          => $deviceData['status'] ?? 'in_stock',
                'notes'           => !empty($deviceData['notes']) ? trim($deviceData['notes']) : null
            ]);

            $deviceId = $this->lastInsertId();

            // Lưu cấu hình chi tiết
            $specModel = new DeviceSpec();
            $specModel->create($deviceId, $specData);
            $this->replaceMonitors($deviceId, $specData['monitors'] ?? []);

            // Ghi nhật ký lịch sử nhập kho
            $historyModel = new DeviceHistory();
            $historyModel->log([
                'device_id'    => $deviceId,
                'ticket_id'    => null,
                'action_type'  => 'import',
                'performed_by' => $userId,
                'description'  => "Nhập kho thiết bị mới STT {$deviceId}: {$deviceData['type']} {$deviceData['brand']} {$deviceData['model']}"
            ]);

            $this->commit();
            return $deviceId;
        } catch (Exception $e) {
            $this->rollBack();
            throw $e;
        }
    }

    /**
     * Cập nhật thông tin thiết bị và cấu hình
     */
    public function update(int $id, array $deviceData, array $specData): bool {
        $this->beginTransaction();
        try {
            $sql = "UPDATE {$this->table} SET 
                        type = :type,
                        brand = :brand,
                        model = :model,
                        serial_number = :serial_number,
                        import_date = :import_date,
                        warranty_expiry = :warranty_expiry,
                        condition_state = :condition_state,
                        status = :status,
                        notes = :notes
                    WHERE id = :id";

            $this->query($sql, [
                'id'              => $id,
                'type'            => $deviceData['type'],
                'brand'           => trim($deviceData['brand']),
                'model'           => trim($deviceData['model']),
                'serial_number'   => !empty($deviceData['serial_number']) ? trim($deviceData['serial_number']) : null,
                'import_date'     => $deviceData['import_date'],
                'warranty_expiry' => !empty($deviceData['warranty_expiry']) ? $deviceData['warranty_expiry'] : null,
                'condition_state' => $deviceData['condition_state'],
                'status'          => $deviceData['status'],
                'notes'           => !empty($deviceData['notes']) ? trim($deviceData['notes']) : null
            ]);

            // Cập nhật cấu hình
            $specModel = new DeviceSpec();
            $specModel->update($id, $specData);
            $this->replaceMonitors($id, $specData['monitors'] ?? []);

            $this->commit();
            return true;
        } catch (Exception $e) {
            $this->rollBack();
            throw $e;
        }

    }

    private function replaceMonitors(int $deviceId, array $monitors): void {
        $this->query("DELETE FROM device_monitors WHERE device_id = :device_id", ['device_id' => $deviceId]);
        $sql = "INSERT INTO device_monitors (device_id, monitor_no, brand, model, size)
                VALUES (:device_id, :monitor_no, :brand, :model, :size)";
        foreach (array_values($monitors) as $index => $monitor) {
            if (!is_array($monitor)) {
                continue;
            }
            $this->query($sql, [
                'device_id' => $deviceId,
                'monitor_no' => $index + 1,
                'brand' => trim((string)($monitor['brand'] ?? '')),
                'model' => !empty($monitor['model']) ? trim((string)$monitor['model']) : null,
                'size' => (float)($monitor['size'] ?? 0)
            ]);
        }
    }

    /**
     * Xóa thiết bị
     */
    public function delete(int $id): bool {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        return (bool)$this->query($sql, ['id' => $id]);
    }

    /**
     * Kiểm tra mã thiết bị đã tồn tại chưa
     */
    public function isCodeExists(string $code, int $excludeId = 0): bool {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE device_code = :code AND id != :excludeId";
        return (bool)$this->count($sql, ['code' => $code, 'excludeId' => $excludeId]);
    }

    /**
     * Lấy danh sách thiết bị đang "Trong kho" (in_stock) để cấp phát
     */
    public function getAvailableInStock(): array {
        $sql = "SELECT d.id, d.device_code, d.type, d.brand, d.model, s.cpu, s.ram_gb, s.ssd_capacity
                FROM {$this->table} d
                LEFT JOIN device_specs s ON d.id = s.device_id
                WHERE d.status = 'in_stock'
                ORDER BY d.id DESC";
        return $this->fetchAll($sql);
    }

    /**
     * Lấy danh sách thiết bị đang "Cấp phát" (allocated) để chuyển giao hoặc thu hồi
     */
    public function getAllocatedDevices(?int $employeeId = null): array {
        $where = "WHERE d.status = 'allocated'";
        $params = [];
        if ($employeeId !== null) {
            $where .= " AND d.current_employee_id = :emp_id";
            $params['emp_id'] = $employeeId;
        }
        $sql = "SELECT d.id, d.device_code, d.type, d.brand, d.model, d.current_employee_id, d.current_department_id,
                       e.fullname AS employee_name, dep.name AS department_name
                FROM {$this->table} d
                LEFT JOIN employees e ON d.current_employee_id = e.id
                LEFT JOIN departments dep ON d.current_department_id = dep.id
                {$where}
                ORDER BY d.id DESC";
        return $this->fetchAll($sql, $params);
    }

    /**
     * Thống kê tổng quan cho Dashboard
     */
    public function getDashboardStats(): array {
        $total = $this->count("SELECT COUNT(*) FROM {$this->table}");
        $inStock = $this->count("SELECT COUNT(*) FROM {$this->table} WHERE status = 'in_stock'");
        $allocated = $this->count("SELECT COUNT(*) FROM {$this->table} WHERE status = 'allocated'");
        $maintenance = $this->count("SELECT COUNT(*) FROM {$this->table} WHERE status = 'maintenance'");
        $liquidated = $this->count("SELECT COUNT(*) FROM {$this->table} WHERE status = 'liquidated'");
        
        $pcCount = $this->count("SELECT COUNT(*) FROM {$this->table} WHERE type = 'PC'");
        $laptopCount = $this->count("SELECT COUNT(*) FROM {$this->table} WHERE type = 'Laptop'");
        $otherCount = $this->count("SELECT COUNT(*) FROM {$this->table} WHERE type NOT IN ('PC', 'Laptop') OR type IS NULL");

        $overdueLoans = $this->count("SELECT COUNT(*) FROM {$this->table} WHERE is_temporary = 1 AND status = 'allocated' AND expected_return_date < CURDATE()");
        $expiringWarranty = $this->count("SELECT COUNT(*) FROM {$this->table} WHERE warranty_expiry IS NOT NULL AND warranty_expiry >= CURDATE() AND warranty_expiry <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)");

        return [
            'total'             => $total,
            'in_stock'          => $inStock,
            'allocated'         => $allocated,
            'maintenance'       => $maintenance,
            'liquidated'        => $liquidated,
            'pc_count'          => $pcCount,
            'laptop_count'      => $laptopCount,
            'other_count'       => $otherCount,
            'overdue_loans'     => $overdueLoans,
            'expiring_warranty' => $expiringWarranty
        ];
    }

    /**
     * Thống kê số thiết bị theo từng phòng ban
     */
    public function getDevicesByDepartment(): array {
        $sql = "SELECT dep.id AS department_id, dep.name AS department_name, dep.code AS department_code, COUNT(dev.id) AS device_count
                FROM departments dep
                LEFT JOIN devices dev ON dep.id = dev.current_department_id AND dev.status = 'allocated'
                GROUP BY dep.id, dep.name, dep.code
                ORDER BY device_count DESC";
        return $this->fetchAll($sql);
    }

    /**
     * Danh sách thiết bị cấp tạm quá hạn trả
     */
    public function getOverdueDevices(): array {
        $sql = "SELECT d.*, e.fullname AS employee_name, e.phone AS employee_phone, dep.name AS department_name
                FROM {$this->table} d
                LEFT JOIN employees e ON d.current_employee_id = e.id
                LEFT JOIN departments dep ON d.current_department_id = dep.id
                WHERE d.is_temporary = 1 AND d.status = 'allocated' AND d.expected_return_date < CURDATE()
                ORDER BY d.expected_return_date ASC";
        return $this->fetchAll($sql);
    }

    /**
     * Danh sách thiết bị sắp hết hạn bảo hành (30 ngày)
     */
    public function getExpiringWarrantyDevices(int $days = 30): array {
        $sql = "SELECT d.*, DATEDIFF(d.warranty_expiry, CURDATE()) AS days_left
                FROM {$this->table} d
                WHERE d.warranty_expiry IS NOT NULL 
                  AND d.warranty_expiry >= CURDATE() 
                  AND d.warranty_expiry <= DATE_ADD(CURDATE(), INTERVAL :days DAY)
                ORDER BY d.warranty_expiry ASC";
        return $this->fetchAll($sql, ['days' => $days]);
    }

    /**
     * Cập nhật trạng thái và người sử dụng của thiết bị
     */
    public function updateStatusAndAssignee(int $id, string $status, ?int $employeeId, ?int $departmentId, int $isTemporary = 0, ?string $returnDate = null, ?string $condition = null): bool {
        $fields = [
            'status = :status',
            'current_employee_id = :employee_id',
            'current_department_id = :department_id',
            'is_temporary = :is_temporary',
            'expected_return_date = :expected_return_date'
        ];
        $params = [
            'id'                   => $id,
            'status'               => $status,
            'employee_id'          => $employeeId,
            'department_id'        => $departmentId,
            'is_temporary'         => $isTemporary,
            'expected_return_date' => $returnDate
        ];

        if ($condition !== null) {
            $fields[] = 'condition_state = :condition_state';
            $params['condition_state'] = $condition;
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = :id";
        return (bool)$this->query($sql, $params);
    }
}
