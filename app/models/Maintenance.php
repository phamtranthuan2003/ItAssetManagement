<?php
/**
 * File: app/models/Maintenance.php
 * Model quản lý bảo dưỡng, sửa chữa và nâng cấp linh kiện (maintenances)
 */

require_once __DIR__ . '/../../core/Model.php';
require_once __DIR__ . '/Device.php';
require_once __DIR__ . '/DeviceHistory.php';

class Maintenance extends Model {
    protected string $table = 'maintenances';

    /**
     * Lấy toàn bộ danh sách phiếu bảo trì/sửa chữa/nâng cấp
     */
    public function getAll(?string $status = null): array {
        $where = [];
        $params = [];

        if (!empty($status)) {
            $where[] = "m.status = :status";
            $params['status'] = $status;
        }

        $whereClause = !empty($where) ? "WHERE " . implode(' AND ', $where) : "";

        $sql = "SELECT m.*, d.device_code, d.type, d.brand, d.model, u.fullname AS creator_name
                FROM {$this->table} m
                JOIN devices d ON m.device_id = d.id
                JOIN users u ON m.created_by = u.id
                {$whereClause}
                ORDER BY m.start_date DESC, m.id DESC";

        return $this->fetchAll($sql, $params);
    }

    /**
     * Lấy danh sách lịch sử sửa chữa/nâng cấp của một thiết bị
     */
    public function getByDeviceId(int $deviceId): array {
        $sql = "SELECT m.*, u.fullname AS creator_name
                FROM {$this->table} m
                JOIN users u ON m.created_by = u.id
                WHERE m.device_id = :device_id
                ORDER BY m.start_date DESC, m.id DESC";

        return $this->fetchAll($sql, ['device_id' => $deviceId]);
    }

    /**
     * Tìm phiếu bảo trì theo ID
     */
    public function findById(int $id): ?array {
        $sql = "SELECT m.*, d.device_code, d.type, d.brand, d.model, u.fullname AS creator_name
                FROM {$this->table} m
                JOIN devices d ON m.device_id = d.id
                JOIN users u ON m.created_by = u.id
                WHERE m.id = :id LIMIT 1";

        return $this->fetch($sql, ['id' => $id]);
    }

    /**
     * Tạo phiếu bảo trì / sửa chữa / nâng cấp linh kiện
     */
    public function create(array $data, int $userId): int {
        $this->beginTransaction();
        try {
            $deviceId = (int)$data['device_id'];
            $type = $data['maintenance_type'];
            $cost = !empty($data['cost']) ? (float)$data['cost'] : 0;
            $provider = !empty($data['provider']) ? trim($data['provider']) : null;
            $desc = trim($data['description']);

            $sql = "INSERT INTO {$this->table} (device_id, maintenance_type, start_date, end_date, cost, provider, description, status, created_by)
                    VALUES (:device_id, :maintenance_type, :start_date, :end_date, :cost, :provider, :description, :status, :created_by)";

            $this->query($sql, [
                'device_id'        => $deviceId,
                'maintenance_type' => $type,
                'start_date'       => $data['start_date'] ?? date('Y-m-d'),
                'end_date'         => !empty($data['end_date']) ? $data['end_date'] : null,
                'cost'             => $cost,
                'provider'         => $provider,
                'description'      => $desc,
                'status'           => $data['status'] ?? 'in_progress',
                'created_by'       => $userId
            ]);

            $maintId = $this->lastInsertId();

            // Nếu tình trạng là 'in_progress' và loại là 'repair', chuyển thiết bị sang trạng thái 'maintenance'
            $deviceModel = new Device();
            $device = $deviceModel->findByIdWithSpec($deviceId);

            if ($data['status'] === 'in_progress' && $type === 'repair') {
                $this->query("UPDATE devices SET status = 'maintenance', condition_state = 'broken' WHERE id = :id", ['id' => $deviceId]);
            }

            // Ghi nhật ký thiết bị
            $historyModel = new DeviceHistory();
            $typeLabel = match ($type) {
                'repair'  => 'Sửa chữa bảo hành',
                'upgrade' => 'Nâng cấp linh kiện',
                'routine' => 'Bảo trì định kỳ'
            };

            $historyModel->log([
                'device_id'    => $deviceId,
                'ticket_id'    => null,
                'action_type'  => $type === 'upgrade' ? 'upgrade' : 'maintenance_start',
                'performed_by' => $userId,
                'description'  => "Bắt đầu {$typeLabel}: {$desc}. Đơn vị: " . ($provider ?: 'Nội bộ') . ". Chi phí dự kiến: " . formatMoney($cost)
            ]);

            $this->commit();
            return $maintId;
        } catch (Exception $e) {
            $this->rollBack();
            throw $e;
        }
    }

    /**
     * Cập nhật hoàn tất hoặc đổi trạng thái bảo trì
     */
    public function updateStatus(int $id, string $status, ?string $endDate, float $cost, string $notes, int $userId): bool {
        $this->beginTransaction();
        try {
            $maint = $this->findById($id);
            if (!$maint) {
                throw new Exception("Không tìm thấy thông tin phiếu bảo trì!");
            }

            $sql = "UPDATE {$this->table} 
                    SET status = :status, end_date = :end_date, cost = :cost 
                    WHERE id = :id";

            $this->query($sql, [
                'id'       => $id,
                'status'   => $status,
                'end_date' => $endDate ?: date('Y-m-d'),
                'cost'     => $cost
            ]);

            $deviceId = (int)$maint['device_id'];

            // Nếu hoàn thành bảo trì -> thiết bị trở lại 'in_stock' và tình trạng 'used' (hoặc nếu là PC nâng cấp)
            if ($status === 'completed') {
                $this->query("UPDATE devices SET status = 'in_stock', condition_state = 'used' WHERE id = :id", ['id' => $deviceId]);
            } elseif ($status === 'cannot_repair') {
                $this->query("UPDATE devices SET status = 'liquidated', condition_state = 'broken' WHERE id = :id", ['id' => $deviceId]);
            }

            // Ghi lịch sử
            $statusText = match ($status) {
                'completed'     => 'Hoàn tất bảo trì, đã chuyển thiết bị về Trong kho',
                'cannot_repair' => 'Không thể sửa chữa, chuyển sang diện Thanh lý',
                default         => 'Cập nhật trạng thái bảo trì'
            };

            $historyModel = new DeviceHistory();
            $historyModel->log([
                'device_id'    => $deviceId,
                'ticket_id'    => null,
                'action_type'  => 'maintenance_done',
                'performed_by' => $userId,
                'description'  => "{$statusText}. Chi phí thực tế: " . formatMoney($cost) . ". Ghi chú: {$notes}"
            ]);

            $this->commit();
            return true;
        } catch (Exception $e) {
            $this->rollBack();
            throw $e;
        }
    }
}
