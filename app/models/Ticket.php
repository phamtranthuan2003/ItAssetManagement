<?php
/**
 * File: app/models/Ticket.php
 * Model quản lý phiếu cấp phát, chuyển giao và thu hồi thiết bị
 */

require_once __DIR__ . '/../../core/Model.php';
require_once __DIR__ . '/TicketDetail.php';
require_once __DIR__ . '/Device.php';
require_once __DIR__ . '/DeviceHistory.php';

class Ticket extends Model {
    protected string $table = 'tickets';

    /**
     * Lấy danh sách phiếu có lọc theo loại phiếu và trạng thái
     */
    public function getAll(
        ?string $type = null,
        ?string $status = null,
        array $filters = [],
        int $page = 1,
        int $perPage = 10
    ): array {
        [$where, $params] = $this->buildFilterConditions($type, $status, $filters);
        $whereClause = !empty($where) ? "WHERE " . implode(' AND ', $where) : "";
        $page = max(1, $page);
        $perPage = max(1, $perPage);
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT t.*, u.fullname AS creator_name,
                       fe.fullname AS from_employee_name, fe.code AS from_employee_code,
                       te.fullname AS to_employee_name, te.code AS to_employee_code,
                       fd.name AS from_department_name,
                       td.name AS to_department_name,
                       (SELECT COUNT(*) FROM ticket_details WHERE ticket_id = t.id) AS device_count
                FROM {$this->table} t
                JOIN users u ON t.created_by = u.id
                LEFT JOIN employees fe ON t.from_employee_id = fe.id
                LEFT JOIN employees te ON t.to_employee_id = te.id
                LEFT JOIN departments fd ON t.from_department_id = fd.id
                LEFT JOIN departments td ON t.to_department_id = td.id
                {$whereClause}
                ORDER BY t.ticket_date DESC, t.id DESC
                LIMIT {$perPage} OFFSET {$offset}";

        return $this->fetchAll($sql, $params);
    }

    public function countFiltered(?string $type = null, ?string $status = null, array $filters = []): int {
        [$where, $params] = $this->buildFilterConditions($type, $status, $filters);
        $whereClause = !empty($where) ? "WHERE " . implode(' AND ', $where) : "";

        return $this->count("SELECT COUNT(*) FROM {$this->table} t {$whereClause}", $params);
    }

    private function buildFilterConditions(?string $type, ?string $status, array $filters): array {
        $where = [];
        $params = [];

        if (!empty($type)) {
            $where[] = "t.ticket_type = :type";
            $params['type'] = $type;
        }

        if (!empty($status)) {
            $where[] = "t.status = :status";
            $params['status'] = $status;
        }

        if (!empty($filters['to_employee_id'])) {
            $where[] = "t.to_employee_id = :to_employee_id";
            $params['to_employee_id'] = (int)$filters['to_employee_id'];
        }

        if (!empty($filters['created_by'])) {
            $where[] = "t.created_by = :created_by";
            $params['created_by'] = (int)$filters['created_by'];
        }

        return [$where, $params];
    }

    /**
     * Tìm chi tiết phiếu theo ID
     */
    public function findById(int $id): ?array {
        $sql = "SELECT t.*, u.fullname AS creator_name, u.username AS creator_username,
                       fe.fullname AS from_employee_name, fe.code AS from_employee_code, fe.position AS from_employee_pos,
                       te.fullname AS to_employee_name, te.code AS to_employee_code, te.position AS to_employee_pos,
                       fd.name AS from_department_name, fd.code AS from_department_code,
                       td.name AS to_department_name, td.code AS to_department_code
                FROM {$this->table} t
                JOIN users u ON t.created_by = u.id
                LEFT JOIN employees fe ON t.from_employee_id = fe.id
                LEFT JOIN employees te ON t.to_employee_id = te.id
                LEFT JOIN departments fd ON t.from_department_id = fd.id
                LEFT JOIN departments td ON t.to_department_id = td.id
                WHERE t.id = :id LIMIT 1";

        return $this->fetch($sql, ['id' => $id]);
    }

    /**
     * Sinh mã phiếu tự động theo tiền tố và ngày tháng (PX, PCG, PTH)
     */
    public function generateTicketCode(string $type): string {
        $prefix = match ($type) {
            'allocation' => 'PX',
            'transfer'   => 'PCG',
            'revocation' => 'PTH',
            default      => 'PH'
        };
        $dateStr = date('Ymd');
        $random = strtoupper(substr(uniqid(), -4));
        return "{$prefix}-{$dateStr}-{$random}";
    }

    /**
     * Tạo mới phiếu và áp dụng các nghiệp vụ ràng buộc toàn vẹn dữ liệu
     * 
     * @param array $ticketData Thông tin chung của phiếu
     * @param array $devicesItems Mảng các thiết bị kèm phụ kiện: [['device_id' => 1, 'accessories' => '...', 'returned_condition' => 'good'], ...]
     * @param int $userId ID tài khoản thực hiện
     */
    public function createTicket(array $ticketData, array $devicesItems, int $userId): int {
        if (empty($devicesItems)) {
            throw new Exception("Phiếu phải có ít nhất một thiết bị được chọn!");
        }

        $ticketType = $ticketData['ticket_type'];
        $toEmployeeId = !empty($ticketData['to_employee_id']) ? (int)$ticketData['to_employee_id'] : null;
        $toDepartmentId = !empty($ticketData['to_department_id']) ? (int)$ticketData['to_department_id'] : null;
        $fromEmployeeId = !empty($ticketData['from_employee_id']) ? (int)$ticketData['from_employee_id'] : null;
        $fromDepartmentId = !empty($ticketData['from_department_id']) ? (int)$ticketData['from_department_id'] : null;
        $isTemporary = !empty($ticketData['is_temporary']) ? 1 : 0;
        $expectedReturnDate = !empty($ticketData['expected_return_date']) ? $ticketData['expected_return_date'] : null;

        $this->beginTransaction();
        try {
            $deviceModel = new Device();
            $ticketDetailModel = new TicketDetail();
            $historyModel = new DeviceHistory();

            // Kiểm tra ràng buộc cho từng thiết bị trước khi tạo phiếu
            foreach ($devicesItems as $item) {
                $deviceId = (int)$item['device_id'];
                $device = $deviceModel->findByIdWithSpec($deviceId);

                if (!$device) {
                    throw new Exception("Thiết bị ID #{$deviceId} không tồn tại trong hệ thống!");
                }

                if ($ticketType === 'allocation') {
                    // Ràng buộc: Cấp phát CHỈ cho thiết bị đang "Trong kho" (in_stock)
                    if ($device['status'] !== 'in_stock') {
                        throw new Exception("Thiết bị STT {$deviceId} không ở trạng thái 'Trong kho' (hiện tại: {$device['status']}), không thể cấp phát!");
                    }
                } elseif ($ticketType === 'revocation') {
                    // Ràng buộc: Không thu hồi thiết bị đang ở kho
                    if ($device['status'] === 'in_stock') {
                        throw new Exception("Thiết bị STT {$deviceId} đang ở 'Trong kho', không thể thực hiện thu hồi!");
                    }
                } elseif ($ticketType === 'transfer') {
                    if (
                        $device['status'] !== 'allocated' ||
                        (int)$device['current_employee_id'] !== $fromEmployeeId
                    ) {
                        throw new Exception("Thiết bị STT {$deviceId} không thuộc nhân viên bàn giao đã chọn hoặc không còn được cấp phát!");
                    }
                }
            }

            // 1. Tạo phiếu
            $ticketCode = !empty($ticketData['ticket_code']) ? trim($ticketData['ticket_code']) : $this->generateTicketCode($ticketType);
            
            $sql = "INSERT INTO {$this->table} 
                    (ticket_code, ticket_type, ticket_date, created_by, from_employee_id, from_department_id, to_employee_id, to_department_id, is_temporary, expected_return_date, status, reason, notes)
                    VALUES 
                    (:ticket_code, :ticket_type, :ticket_date, :created_by, :from_employee_id, :from_department_id, :to_employee_id, :to_department_id, :is_temporary, :expected_return_date, :status, :reason, :notes)";

            $this->query($sql, [
                'ticket_code'          => $ticketCode,
                'ticket_type'          => $ticketType,
                'ticket_date'          => $ticketData['ticket_date'] ?? date('Y-m-d'),
                'created_by'           => $userId,
                'from_employee_id'     => $fromEmployeeId,
                'from_department_id'   => $fromDepartmentId,
                'to_employee_id'       => $toEmployeeId,
                'to_department_id'     => $toDepartmentId,
                'is_temporary'         => $isTemporary,
                'expected_return_date' => $expectedReturnDate,
                'status'               => $ticketData['status'] ?? 'completed',
                'reason'               => !empty($ticketData['reason']) ? trim($ticketData['reason']) : null,
                'notes'                => !empty($ticketData['notes']) ? trim($ticketData['notes']) : null
            ]);

            $ticketId = $this->lastInsertId();

            // 2. Thêm từng thiết bị vào chi tiết phiếu và cập nhật trạng thái thiết bị
            foreach ($devicesItems as $item) {
                $deviceId = (int)$item['device_id'];
                $accessories = $item['accessories'] ?? '';
                $returnedCondition = $item['returned_condition'] ?? null;
                $notes = $item['notes'] ?? '';

                $ticketDetailModel->create($ticketId, [
                    'device_id'          => $deviceId,
                    'accessories'        => $accessories,
                    'returned_condition' => $returnedCondition,
                    'notes'              => $notes
                ]);

                // Xử lý logic thay đổi trạng thái theo từng loại phiếu
                if ($ticketType === 'allocation') {
                    // Cấp phát: chuyển thành 'allocated', gán nhân viên & phòng ban
                    $deviceModel->updateStatusAndAssignee(
                        $deviceId,
                        'allocated',
                        $toEmployeeId,
                        $toDepartmentId,
                        $isTemporary,
                        $expectedReturnDate,
                        'used'
                    );

                    $accDesc = !empty($accessories) ? " (Phụ kiện: {$accessories})" : "";
                    $tempDesc = $isTemporary ? " [Cấp tạm thời đến " . formatDate($expectedReturnDate) . "]" : "";

                    $historyModel->log([
                        'device_id'          => $deviceId,
                        'ticket_id'          => $ticketId,
                        'action_type'        => 'allocate',
                        'performed_by'       => $userId,
                        'from_employee_id'   => null,
                        'to_employee_id'     => $toEmployeeId,
                        'from_department_id' => null,
                        'to_department_id'   => $toDepartmentId,
                        'description'        => "Cấp phát thiết bị theo phiếu {$ticketCode}{$tempDesc}{$accDesc}. Lý do: " . ($ticketData['reason'] ?? 'Không có')
                    ]);

                } elseif ($ticketType === 'transfer') {
                    // Chuyển giao: cập nhật người và phòng ban mới
                    $deviceModel->updateStatusAndAssignee(
                        $deviceId,
                        'allocated',
                        $toEmployeeId,
                        $toDepartmentId,
                        0,
                        null
                    );

                    $accDesc = !empty($accessories) ? " (Phụ kiện: {$accessories})" : "";

                    $historyModel->log([
                        'device_id'          => $deviceId,
                        'ticket_id'          => $ticketId,
                        'action_type'        => 'transfer',
                        'performed_by'       => $userId,
                        'from_employee_id'   => $fromEmployeeId,
                        'to_employee_id'     => $toEmployeeId,
                        'from_department_id' => $fromDepartmentId,
                        'to_department_id'   => $toDepartmentId,
                        'description'        => "Chuyển giao thiết bị theo phiếu {$ticketCode}{$accDesc}. Lý do: " . ($ticketData['reason'] ?? 'Điều chuyển công tác')
                    ]);

                } elseif ($ticketType === 'revocation') {
                    // Thu hồi:
                    // Nếu tình trạng trả: tốt -> Trong kho (in_stock)
                    // Nếu hỏng -> Bảo trì (maintenance)
                    $newStatus = ($returnedCondition === 'broken') ? 'maintenance' : 'in_stock';
                    $newCondition = match ($returnedCondition) {
                        'broken' => 'broken',
                        'missing_accessories' => 'used',
                        default => 'used'
                    };

                    $deviceModel->updateStatusAndAssignee(
                        $deviceId,
                        $newStatus,
                        null,
                        null,
                        0,
                        null,
                        $newCondition
                    );

                    $condText = match ($returnedCondition) {
                        'broken' => 'Hỏng hóc (chuyển sang Bảo trì)',
                        'missing_accessories' => 'Thiếu phụ kiện',
                        default => 'Tốt (trả về kho)'
                    };

                    $accDesc = !empty($accessories) ? " | Phụ kiện nhận lại: {$accessories}" : "";

                    $historyModel->log([
                        'device_id'          => $deviceId,
                        'ticket_id'          => $ticketId,
                        'action_type'        => 'revoke',
                        'performed_by'       => $userId,
                        'from_employee_id'   => $fromEmployeeId,
                        'to_employee_id'     => null,
                        'from_department_id' => $fromDepartmentId,
                        'to_department_id'   => null,
                        'description'        => "Thu hồi thiết bị theo phiếu {$ticketCode}. Tình trạng lúc nhận: {$condText}{$accDesc}. Ghi chú: {$notes}"
                    ]);
                }
            }

            $this->commit();
            return $ticketId;
        } catch (Exception $e) {
            $this->rollBack();
            throw $e;
        }
    }
}
