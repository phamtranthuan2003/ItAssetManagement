<?php
/**
 * File: app/models/TicketDetail.php
 * Model chi tiết danh sách thiết bị trong một phiếu (ticket_details)
 */

require_once __DIR__ . '/../../core/Model.php';

class TicketDetail extends Model {
    protected string $table = 'ticket_details';

    /**
     * Thêm thiết bị vào phiếu
     */
    public function create(int $ticketId, array $data): int {
        $sql = "INSERT INTO {$this->table} (ticket_id, device_id, accessories, returned_condition, notes)
                VALUES (:ticket_id, :device_id, :accessories, :returned_condition, :notes)";

        $this->query($sql, [
            'ticket_id'          => $ticketId,
            'device_id'          => (int)$data['device_id'],
            'accessories'        => !empty($data['accessories']) ? trim($data['accessories']) : null,
            'returned_condition' => !empty($data['returned_condition']) ? $data['returned_condition'] : null,
            'notes'              => !empty($data['notes']) ? trim($data['notes']) : null
        ]);

        return $this->lastInsertId();
    }

    /**
     * Lấy danh sách thiết bị kèm phụ kiện theo Ticket ID
     */
    public function getByTicketId(int $ticketId): array {
        $sql = "SELECT td.*, d.device_code, d.type, d.brand, d.model, d.serial_number,
                       d.condition_state, d.paired_pc_id, pc.device_code AS paired_pc_code,
                       s.cpu, s.mainboard, s.ram_gb, s.ram_type, s.ssd_capacity,
                       s.hdd_capacity, s.gpu, s.screen_size,
                       (SELECT GROUP_CONCAT(
                           CONCAT_WS(' ', pm.device_code, NULLIF(TRIM(pm.brand), ''), NULLIF(TRIM(pm.model), ''))
                           ORDER BY pm.id SEPARATOR ', '
                       )
                        FROM devices pm
                        WHERE pm.paired_pc_id = d.id) AS paired_monitor_list
                FROM {$this->table} td
                JOIN devices d ON td.device_id = d.id
                LEFT JOIN devices pc ON d.paired_pc_id = pc.id
                LEFT JOIN device_specs s ON d.id = s.device_id
                WHERE td.ticket_id = :ticket_id
                ORDER BY td.id ASC";

        return $this->fetchAll($sql, ['ticket_id' => $ticketId]);
    }
}
