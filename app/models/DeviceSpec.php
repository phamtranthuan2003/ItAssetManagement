<?php
/**
 * File: app/models/DeviceSpec.php
 * Model quản lý thông tin cấu hình phần cứng thiết bị (device_specs)
 */

require_once __DIR__ . '/../../core/Model.php';

class DeviceSpec extends Model {
    protected string $table = 'device_specs';

    /**
     * Lấy cấu hình theo ID thiết bị
     */
    public function findByDeviceId(int $deviceId): ?array {
        $sql = "SELECT * FROM {$this->table} WHERE device_id = :device_id LIMIT 1";
        return $this->fetch($sql, ['device_id' => $deviceId]);
    }

    /**
     * Tạo mới thông tin cấu hình
     */
    public function create(int $deviceId, array $data): bool {
        $sql = "INSERT INTO {$this->table} (device_id, cpu, mainboard, ram_gb, ram_type, ssd_capacity, hdd_capacity, gpu, screen_size, notes)
                VALUES (:device_id, :cpu, :mainboard, :ram_gb, :ram_type, :ssd_capacity, :hdd_capacity, :gpu, :screen_size, :notes)";
        return (bool)$this->query($sql, [
            'device_id'    => $deviceId,
            'cpu'          => !empty($data['cpu']) ? trim($data['cpu']) : null,
            'mainboard'    => !empty($data['mainboard']) ? trim($data['mainboard']) : null,
            'ram_gb'       => isset($data['ram_gb']) && $data['ram_gb'] !== '' ? (int)$data['ram_gb'] : null,
            'ram_type'     => !empty($data['ram_type']) ? trim($data['ram_type']) : null,
            'ssd_capacity' => !empty($data['ssd_capacity']) ? trim($data['ssd_capacity']) : null,
            'hdd_capacity' => !empty($data['hdd_capacity']) ? trim($data['hdd_capacity']) : null,
            'gpu'          => !empty($data['gpu']) ? trim($data['gpu']) : null,
            'screen_size'  => !empty($data['screen_size']) ? trim($data['screen_size']) : null,
            'notes'        => !empty($data['spec_notes']) ? trim($data['spec_notes']) : null
        ]);
    }

    /**
     * Cập nhật thông tin cấu hình
     */
    public function update(int $deviceId, array $data): bool {
        $exists = $this->findByDeviceId($deviceId);
        if (!$exists) {
            return $this->create($deviceId, $data);
        }

        $sql = "UPDATE {$this->table} SET 
                    cpu = :cpu, 
                    mainboard = :mainboard, 
                    ram_gb = :ram_gb, 
                    ram_type = :ram_type, 
                    ssd_capacity = :ssd_capacity, 
                    hdd_capacity = :hdd_capacity, 
                    gpu = :gpu, 
                    screen_size = :screen_size, 
                    notes = :notes
                WHERE device_id = :device_id";

        return (bool)$this->query($sql, [
            'device_id'    => $deviceId,
            'cpu'          => !empty($data['cpu']) ? trim($data['cpu']) : null,
            'mainboard'    => !empty($data['mainboard']) ? trim($data['mainboard']) : null,
            'ram_gb'       => isset($data['ram_gb']) && $data['ram_gb'] !== '' ? (int)$data['ram_gb'] : null,
            'ram_type'     => !empty($data['ram_type']) ? trim($data['ram_type']) : null,
            'ssd_capacity' => !empty($data['ssd_capacity']) ? trim($data['ssd_capacity']) : null,
            'hdd_capacity' => !empty($data['hdd_capacity']) ? trim($data['hdd_capacity']) : null,
            'gpu'          => !empty($data['gpu']) ? trim($data['gpu']) : null,
            'screen_size'  => !empty($data['screen_size']) ? trim($data['screen_size']) : null,
            'notes'        => !empty($data['spec_notes']) ? trim($data['spec_notes']) : null
        ]);
    }
}
