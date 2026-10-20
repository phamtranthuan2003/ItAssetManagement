<?php
/**
 * File: app/models/DeviceHistory.php
 * Model ghi nhật ký vòng đời và lịch sử thay đổi của thiết bị
 */

require_once __DIR__ . '/../../core/Model.php';

class DeviceHistory extends Model {
    protected string $table = 'device_history';

    /**
     * Ghi nhật ký lịch sử thiết bị
     */
    public function log(array $data): int {
        $sql = "INSERT INTO {$this->table} 
                (device_id, ticket_id, action_type, performed_by, from_employee_id, to_employee_id, from_department_id, to_department_id, description)
                VALUES 
                (:device_id, :ticket_id, :action_type, :performed_by, :from_employee_id, :to_employee_id, :from_department_id, :to_department_id, :description)";

        $this->query($sql, [
            'device_id'          => $data['device_id'],
            'ticket_id'          => $data['ticket_id'] ?? null,
            'action_type'        => $data['action_type'],
            'performed_by'       => $data['performed_by'],
            'from_employee_id'   => $data['from_employee_id'] ?? null,
            'to_employee_id'     => $data['to_employee_id'] ?? null,
            'from_department_id' => $data['from_department_id'] ?? null,
            'to_department_id'   => $data['to_department_id'] ?? null,
            'description'        => $data['description']
        ]);

        return $this->lastInsertId();
    }

    /**
     * Lấy toàn bộ dòng thời gian (timeline) của một thiết bị cụ thể
     */
    public function getByDeviceId(int $deviceId): array {
        $sql = "SELECT h.*, u.fullname AS performer_name, u.username AS performer_username,
                       fe.fullname AS from_employee_name, te.fullname AS to_employee_name,
                       fd.name AS from_dept_name, td.name AS to_dept_name,
                       t.ticket_code
                FROM {$this->table} h
                JOIN users u ON h.performed_by = u.id
                LEFT JOIN employees fe ON h.from_employee_id = fe.id
                LEFT JOIN employees te ON h.to_employee_id = te.id
                LEFT JOIN departments fd ON h.from_department_id = fd.id
                LEFT JOIN departments td ON h.to_department_id = td.id
                LEFT JOIN tickets t ON h.ticket_id = t.id
                WHERE h.device_id = :device_id
                ORDER BY h.action_date DESC, h.id DESC";

        return $this->fetchAll($sql, ['device_id' => $deviceId]);
    }

    /**
     * Lấy danh sách hoạt động gần đây nhất toàn hệ thống
     */
    public function getRecent(int $limit = 10): array {
        $sql = "SELECT h.*, u.fullname AS performer_name, d.device_code, d.model, d.type
                FROM {$this->table} h
                JOIN users u ON h.performed_by = u.id
                JOIN devices d ON h.device_id = d.id
                ORDER BY h.action_date DESC, h.id DESC
                LIMIT {$limit}";

        return $this->fetchAll($sql);
    }
}
