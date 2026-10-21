<?php
/**
 * File: app/controllers/DeviceController.php
 * Controller quản lý thiết bị và cấu hình chi tiết
 */

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../models/Device.php';
require_once __DIR__ . '/../models/Department.php';
require_once __DIR__ . '/../models/DeviceHistory.php';
require_once __DIR__ . '/../models/Maintenance.php';

class DeviceController extends Controller {
    private Device $deviceModel;
    private Department $deptModel;
    private DeviceHistory $historyModel;
    private Maintenance $maintModel;

    public function __construct() {
        Auth::requireLogin();
        $this->deviceModel = new Device();
        $this->deptModel = new Department();
        $this->historyModel = new DeviceHistory();
        $this->maintModel = new Maintenance();
    }

    /**
     * Danh sách thiết bị có tìm kiếm, bộ lọc nâng cao và phân trang
     */
    public function index(): void {
        $page = max(1, (int)$this->get('page', 1));
        $perPage = 10;

        $filters = [
            'keyword'           => $this->get('keyword', ''),
            'type'              => $this->get('type', ''),
            'brand'             => $this->get('brand', ''),
            'status'            => $this->get('status', ''),
            'condition_state'   => $this->get('condition_state', ''),
            'department_id'     => $this->get('department_id', ''),
            'cpu'               => $this->get('cpu', ''),
            'ram_gb'            => $this->get('ram_gb', ''),
            'ssd_capacity'      => $this->get('ssd_capacity', ''),
            'is_overdue'        => $this->get('is_overdue', ''),
            'expiring_warranty' => $this->get('expiring_warranty', '')
        ];

        $totalDevices = $this->deviceModel->countFiltered($filters);
        $totalPages = (int)ceil($totalDevices / $perPage);
        $page = min($page, max(1, $totalPages));
        $devices = $this->deviceModel->getList($filters, $page, $perPage);

        $departments = $this->deptModel->getAll();

        $this->render('devices/index', [
            'pageTitle'    => 'Quản lý thiết bị',
            'devices'      => $devices,
            'filters'      => $filters,
            'page'         => $page,
            'totalPages'   => $totalPages,
            'totalDevices' => $totalDevices,
            'departments'  => $departments,
            'returnTo'     => 'devices?' . http_build_query(array_merge($filters, ['page' => $page]))
        ]);
    }

    /**
     * Giao diện thêm mới thiết bị
     */
    public function create(): void {
        $departments = $this->deptModel->getAll();

        $this->render('devices/create', [
            'pageTitle'   => 'Thêm mới thiết bị',
            'departments' => $departments
        ]);
    }

    /**
     * Xử lý lưu thiết bị mới
     */
    public function store(): void {
        $this->validateCsrf();

        $type = trim($this->post('type', ''));
        $brand = $this->post('brand', '');
        $model = $this->post('model', '');
        $serial = $this->post('serial_number', '');
        $importDate = $this->post('import_date', date('Y-m-d'));
        $warrantyExpiry = $this->post('warranty_expiry', '');
        $condition = $this->post('condition_state', 'new');
        $notes = $this->post('notes', '');

        // Specs
        $cpu = $this->post('cpu', '');
        $mainboard = $this->post('mainboard', '');
        $ramGb = $this->post('ram_gb', '');
        $ramType = $this->post('ram_type', '');
        $ssdCapacity = $this->post('ssd_capacity', '');
        $hddCapacity = $this->post('hdd_capacity', '');
        $gpu = $this->post('gpu', '');
        $screenSize = $this->post('screen_size', '');
        $specNotes = $this->post('spec_notes', '');
        $hasMonitors = $this->post('has_monitors', '') === '1';
        $monitors = $this->post('monitors', []);
        if (!is_array($monitors)) {
            $monitors = [];
        }
        $monitors = array_values(array_filter($monitors, static function ($monitor): bool {
            if (!is_array($monitor)) {
                return false;
            }
            foreach (['brand', 'model', 'size'] as $field) {
                if (isset($monitor[$field]) && is_scalar($monitor[$field]) && trim((string)$monitor[$field]) !== '') {
                    return true;
                }
            }
            return false;
        }));

        // Validate cơ bản
        if ($type === '') {
            redirect('devices/create', 'Vui lòng nhập loại thiết bị!', 'danger');
        }
        if ($hasMonitors && !$this->areMonitorDetailsValid($monitors)) {
            redirect('devices/create', 'Vui lòng nhập hãng và kích thước hợp lệ cho từng màn hình đi kèm!', 'danger');
        }

        try {
            $deviceData = [
                'type'            => $type,
                'brand'           => $brand,
                'model'           => $model,
                'serial_number'   => $serial,
                'import_date'     => $importDate,
                'warranty_expiry' => $warrantyExpiry,
                'condition_state' => $condition,
                'status'          => 'in_stock',
                'notes'           => $notes
            ];

            $specData = [
                'cpu'          => $cpu,
                'mainboard'    => $mainboard,
                'ram_gb'       => $ramGb,
                'ram_type'     => $ramType,
                'ssd_capacity' => $ssdCapacity,
                'hdd_capacity' => $hddCapacity,
                'gpu'          => $gpu,
                'screen_size'  => $screenSize,
                'monitors'     => $hasMonitors ? $monitors : [],
                'spec_notes'   => $specNotes
            ];

            $newId = $this->deviceModel->create($deviceData, $specData, Auth::id());
            redirect("devices/{$newId}", "Đã thêm thiết bị {$type} vào kho thành công!", 'success');
        } catch (Exception $e) {
            redirect('devices/create', 'Lỗi khi lưu thiết bị: ' . $e->getMessage(), 'danger');
        }
    }

    /**
     * Xem chi tiết thiết bị, thông số, người giữ và dòng thời gian lịch sử
     */
    public function show(int $id): void {
        $device = $this->deviceModel->findByIdWithSpec($id);

        if (!$device) {
            redirect('devices', 'Không tìm thấy thiết bị yêu cầu!', 'danger');
        }

        $timeline = $this->historyModel->getByDeviceId($id);
        $maintenances = $this->maintModel->getByDeviceId($id);
        $returnTo = $this->returnTo('devices', 'devices');

        $this->render('devices/show', [
            'pageTitle'    => 'Chi tiết thiết bị STT ' . $device['id'],
            'device'       => $device,
            'timeline'     => $timeline,
            'maintenances' => $maintenances,
            'returnTo'     => $returnTo
        ]);
    }

    /**
     * Giao diện chỉnh sửa thiết bị
     */
    public function edit(int $id): void {
        $device = $this->deviceModel->findByIdWithSpec($id);

        if (!$device) {
            redirect('devices', 'Không tìm thấy thiết bị yêu cầu!', 'danger');
        }

        $this->render('devices/edit', [
            'pageTitle' => 'Chỉnh sửa thiết bị STT ' . $device['id'],
            'device'    => $device,
            'returnTo'  => $this->returnTo('devices', 'devices')
        ]);
    }

    /**
     * Xử lý cập nhật thông tin thiết bị
     */
    public function update(int $id): void {
        $this->validateCsrf();

        $device = $this->deviceModel->findByIdWithSpec($id);
        if (!$device) {
            redirect('devices', 'Không tìm thấy thiết bị yêu cầu!', 'danger');
        }

        $type = trim($this->post('type', ''));
        $brand = $this->post('brand', '');
        $model = $this->post('model', '');
        $serial = $this->post('serial_number', '');
        $importDate = $this->post('import_date', date('Y-m-d'));
        $warrantyExpiry = $this->post('warranty_expiry', '');
        $condition = $this->post('condition_state', 'new');
        $status = $this->post('status', $device['status']);
        $notes = $this->post('notes', '');

        // Specs
        $cpu = $this->post('cpu', '');
        $mainboard = $this->post('mainboard', '');
        $ramGb = $this->post('ram_gb', '');
        $ramType = $this->post('ram_type', '');
        $ssdCapacity = $this->post('ssd_capacity', '');
        $hddCapacity = $this->post('hdd_capacity', '');
        $gpu = $this->post('gpu', '');
        $screenSize = $this->post('screen_size', '');
        $specNotes = $this->post('spec_notes', '');
        $hasMonitors = $this->post('has_monitors', '') === '1';
        $monitors = $this->post('monitors', []);
        if (!is_array($monitors)) {
            $monitors = [];
        }
        $monitors = array_values(array_filter($monitors, static function ($monitor): bool {
            if (!is_array($monitor)) {
                return false;
            }
            foreach (['brand', 'model', 'size'] as $field) {
                if (isset($monitor[$field]) && is_scalar($monitor[$field]) && trim((string)$monitor[$field]) !== '') {
                    return true;
                }
            }
            return false;
        }));

        if ($type === '') {
            redirect($this->returnTo("devices/{$id}/edit", 'devices'), 'Vui lòng nhập loại thiết bị!', 'danger');
        }
        if ($hasMonitors && !$this->areMonitorDetailsValid($monitors)) {
            redirect($this->returnTo("devices/{$id}/edit", 'devices'), 'Vui lòng nhập hãng và kích thước hợp lệ cho từng màn hình đi kèm!', 'danger');
        }

        try {
            $deviceData = [
                'type'            => $type,
                'brand'           => $brand,
                'model'           => $model,
                'serial_number'   => $serial,
                'import_date'     => $importDate,
                'warranty_expiry' => $warrantyExpiry,
                'condition_state' => $condition,
                'status'          => $status,
                'notes'           => $notes
            ];

            $specData = [
                'cpu'          => $cpu,
                'mainboard'    => $mainboard,
                'ram_gb'       => $ramGb,
                'ram_type'     => $ramType,
                'ssd_capacity' => $ssdCapacity,
                'hdd_capacity' => $hddCapacity,
                'gpu'          => $gpu,
                'screen_size'  => $screenSize,
                'monitors'     => $hasMonitors ? $monitors : [],
                'spec_notes'   => $specNotes
            ];

            $this->deviceModel->update($id, $deviceData, $specData);
            redirect($this->returnTo("devices/{$id}", 'devices'), "Cập nhật thiết bị STT {$id} thành công!", 'success');
        } catch (Exception $e) {
            redirect($this->returnTo("devices/{$id}/edit", 'devices'), 'Lỗi cập nhật: ' . $e->getMessage(), 'danger');
        }

    }

    private function areMonitorDetailsValid(array $monitors): bool {
        if (count($monitors) < 1) {
            return false;
        }

        foreach ($monitors as $monitor) {
            $brand = isset($monitor['brand']) && is_scalar($monitor['brand']) ? trim((string)$monitor['brand']) : '';
            $size = $monitor['size'] ?? '';
            if ($brand === '' || !is_scalar($size) || !is_numeric($size) || (float)$size < 1 || (float)$size > 100) {
                return false;
            }
        }

        return true;
    }

    /**
     * Xóa thiết bị (Chỉ cho phép xóa khi thiết bị không đang cấp phát)
     */
    public function delete(int $id): void {
        $this->validateCsrf();
        $returnTo = $this->returnTo('devices', 'devices');

        $device = $this->deviceModel->findByIdWithSpec($id);
        if (!$device) {
            redirect($returnTo, 'Không tìm thấy thiết bị!', 'danger');
        }

        if ($device['status'] === 'allocated') {
            redirect($returnTo, 'Không thể xóa thiết bị đang được cấp phát cho nhân viên! Vui lòng thu hồi trước.', 'danger');
        }

        $this->deviceModel->delete($id);
        redirect($returnTo, "Đã xóa thiết bị STT {$id} thành công!", 'success');
    }

    /**
     * Xuất danh sách thiết bị ra file CSV tương thích hoàn hảo với Microsoft Excel (UTF-8 BOM)
     */
    public function exportCsv(): void {
        $filters = [
            'keyword'           => $this->get('keyword', ''),
            'type'              => $this->get('type', ''),
            'brand'             => $this->get('brand', ''),
            'status'            => $this->get('status', ''),
            'condition_state'   => $this->get('condition_state', ''),
            'department_id'     => $this->get('department_id', ''),
            'cpu'               => $this->get('cpu', ''),
            'ram_gb'            => $this->get('ram_gb', ''),
            'ssd_capacity'      => $this->get('ssd_capacity', ''),
            'is_overdue'        => $this->get('is_overdue', ''),
            'expiring_warranty' => $this->get('expiring_warranty', '')
        ];

        $devices = $this->deviceModel->getAllForExport($filters);

        $filename = "danh_sach_thiet_bi_" . date('Ymd_His') . ".csv";

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // Ghi BOM UTF-8 để Microsoft Excel hiển thị đúng tiếng Việt không bị lỗi font
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Tiêu đề cột
        fputcsv($output, [
            'STT',
            'STT',
            'Loại',
            'Hãng',
            'Model',
            'Số Serial',
            'Trạng Thái',
            'Tình Trạng',
            'CPU',
            'Mainboard',
            'RAM',
            'SSD',
            'HDD',
            'GPU',
            'Màn Hình',
            'Người Đang Giữ',
            'Phòng Ban',
            'Ngày Nhập Kho',
            'Hết Hạn Bảo Hành',
            'Ghi Chú'
        ]);

        $stt = 1;
        foreach ($devices as $d) {
            $statusText = match ($d['status']) {
                'in_stock'    => 'Trong kho',
                'allocated'   => 'Đang cấp phát',
                'maintenance' => 'Bảo trì',
                'liquidated'  => 'Thanh lý',
                default       => $d['status']
            };

            $conditionText = match ($d['condition_state']) {
                'new'            => 'Mới',
                'used'           => 'Đang dùng',
                'broken'         => 'Hỏng',
                'under_warranty' => 'Bảo hành',
                default          => $d['condition_state']
            };

            fputcsv($output, [
                $stt++,
                $d['id'],
                $d['type'],
                $d['brand'],
                $d['model'],
                $d['serial_number'] ?? '',
                $statusText,
                $conditionText,
                $d['cpu'] ?? '',
                $d['mainboard'] ?? '',
                ($d['ram_gb'] ?? '') . 'GB ' . ($d['ram_type'] ?? ''),
                $d['ssd_capacity'] ?? '',
                $d['hdd_capacity'] ?? '',
                $d['gpu'] ?? '',
                $d['screen_size'] ?? '',
                $d['employee_name'] ?? 'Trong kho',
                $d['department_name'] ?? '',
                $d['import_date'],
                $d['warranty_expiry'] ?? '',
                $d['notes'] ?? ''
            ]);
        }

        fclose($output);
        exit;
    }
}
