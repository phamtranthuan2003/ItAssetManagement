<?php
/**
 * File: app/controllers/DeviceController.php
 * Controller quản lý thiết bị (PC, Laptop) và cấu hình chi tiết
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

        $devices = $this->deviceModel->getList($filters, $page, $perPage);
        $totalDevices = $this->deviceModel->countFiltered($filters);
        $totalPages = (int)ceil($totalDevices / $perPage);

        $departments = $this->deptModel->getAll();

        $this->render('devices/index', [
            'pageTitle'    => 'Quản lý thiết bị (PC / Laptop)',
            'devices'      => $devices,
            'filters'      => $filters,
            'page'         => $page,
            'totalPages'   => $totalPages,
            'totalDevices' => $totalDevices,
            'departments'  => $departments
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

        $deviceCode = strtoupper($this->post('device_code', ''));
        $type = $this->post('type', 'Laptop');
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
        $ramGb = (int)$this->post('ram_gb', 8);
        $ramType = $this->post('ram_type', 'DDR4');
        $ssdCapacity = $this->post('ssd_capacity', '');
        $hddCapacity = $this->post('hdd_capacity', '');
        $gpu = $this->post('gpu', '');
        $screenSize = $this->post('screen_size', '');
        $specNotes = $this->post('spec_notes', '');

        // Validate cơ bản
        if (empty($deviceCode) || empty($brand) || empty($model) || empty($cpu)) {
            redirect('devices/create', 'Vui lòng điền đầy đủ các thông tin bắt buộc (*)', 'danger');
        }

        if ($this->deviceModel->isCodeExists($deviceCode)) {
            redirect('devices/create', "Mã thiết bị [{$deviceCode}] đã tồn tại trong hệ thống!", 'danger');
        }

        try {
            $deviceData = [
                'device_code'     => $deviceCode,
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
                'mainboard'    => $type === 'PC' ? $mainboard : null,
                'ram_gb'       => $ramGb,
                'ram_type'     => $ramType,
                'ssd_capacity' => $ssdCapacity,
                'hdd_capacity' => $hddCapacity,
                'gpu'          => $gpu,
                'screen_size'  => $type === 'Laptop' ? $screenSize : null,
                'spec_notes'   => $specNotes
            ];

            $newId = $this->deviceModel->create($deviceData, $specData, Auth::id());
            redirect("devices/{$newId}", "Thêm mới thiết bị {$deviceCode} thành công và lưu vào kho!", 'success');
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

        $this->render('devices/show', [
            'pageTitle'    => 'Chi tiết thiết bị: ' . $device['device_code'],
            'device'       => $device,
            'timeline'     => $timeline,
            'maintenances' => $maintenances
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
            'pageTitle' => 'Chỉnh sửa thiết bị: ' . $device['device_code'],
            'device'    => $device
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

        $deviceCode = strtoupper($this->post('device_code', ''));
        $type = $this->post('type', 'Laptop');
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
        $ramGb = (int)$this->post('ram_gb', 8);
        $ramType = $this->post('ram_type', 'DDR4');
        $ssdCapacity = $this->post('ssd_capacity', '');
        $hddCapacity = $this->post('hdd_capacity', '');
        $gpu = $this->post('gpu', '');
        $screenSize = $this->post('screen_size', '');
        $specNotes = $this->post('spec_notes', '');

        if (empty($deviceCode) || empty($brand) || empty($model) || empty($cpu)) {
            redirect("devices/{$id}/edit", 'Vui lòng điền đầy đủ các thông tin bắt buộc (*)', 'danger');
        }

        if ($this->deviceModel->isCodeExists($deviceCode, $id)) {
            redirect("devices/{$id}/edit", "Mã thiết bị [{$deviceCode}] đã tồn tại ở thiết bị khác!", 'danger');
        }

        try {
            $deviceData = [
                'device_code'     => $deviceCode,
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
                'mainboard'    => $type === 'PC' ? $mainboard : null,
                'ram_gb'       => $ramGb,
                'ram_type'     => $ramType,
                'ssd_capacity' => $ssdCapacity,
                'hdd_capacity' => $hddCapacity,
                'gpu'          => $gpu,
                'screen_size'  => $type === 'Laptop' ? $screenSize : null,
                'spec_notes'   => $specNotes
            ];

            $this->deviceModel->update($id, $deviceData, $specData);
            redirect("devices/{$id}", "Cập nhật thiết bị {$deviceCode} thành công!", 'success');
        } catch (Exception $e) {
            redirect("devices/{$id}/edit", 'Lỗi cập nhật: ' . $e->getMessage(), 'danger');
        }
    }

    /**
     * Xóa thiết bị (Chỉ cho phép xóa khi thiết bị không đang cấp phát)
     */
    public function delete(int $id): void {
        $this->validateCsrf();

        $device = $this->deviceModel->findByIdWithSpec($id);
        if (!$device) {
            redirect('devices', 'Không tìm thấy thiết bị!', 'danger');
        }

        if ($device['status'] === 'allocated') {
            redirect("devices/{$id}", 'Không thể xóa thiết bị đang được cấp phát cho nhân viên! Vui lòng thu hồi trước.', 'danger');
        }

        $this->deviceModel->delete($id);
        redirect('devices', "Đã xóa thiết bị [{$device['device_code']}] thành công!", 'success');
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
            'Mã Thiết Bị',
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
                $d['device_code'],
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
