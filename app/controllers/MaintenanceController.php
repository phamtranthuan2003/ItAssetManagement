<?php
/**
 * File: app/controllers/MaintenanceController.php
 * Controller quản lý bảo trì, sửa chữa và nâng cấp thiết bị
 */

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../models/Maintenance.php';
require_once __DIR__ . '/../models/Device.php';

class MaintenanceController extends Controller {
    private Maintenance $maintModel;
    private Device $deviceModel;

    public function __construct() {
        Auth::requireLogin();
        $this->maintModel = new Maintenance();
        $this->deviceModel = new Device();
    }

    /**
     * Danh sách phiếu bảo trì/sửa chữa/nâng cấp
     */
    public function index(): void {
        $status = $this->get('status', '');
        $records = $this->maintModel->getAll($status ?: null);
        $devices = $this->deviceModel->getList([], 1, 100);

        $this->render('maintenance/index', [
            'pageTitle' => 'Quản lý bảo trì & nâng cấp thiết bị',
            'records'   => $records,
            'devices'   => $devices,
            'status'    => $status
        ]);
    }

    /**
     * Giao diện tạo phiếu bảo dưỡng mới
     */
    public function create(): void {
        $deviceId = (int)$this->get('device_id', 0);
        $devices = $this->deviceModel->getList([], 1, 100);

        $this->render('maintenance/create', [
            'pageTitle'       => 'Tạo phiếu sửa chữa / nâng cấp',
            'devices'         => $devices,
            'defaultDeviceId' => $deviceId
        ]);
    }

    /**
     * Xử lý lưu phiếu bảo dưỡng mới
     */
    public function store(): void {
        $this->validateCsrf();

        $deviceId = (int)$this->post('device_id', 0);
        $type = $this->post('maintenance_type', 'repair');
        $startDate = $this->post('start_date', date('Y-m-d'));
        $cost = (float)$this->post('cost', 0);
        $provider = $this->post('provider', '');
        $description = $this->post('description', '');

        if ($deviceId <= 0 || empty($description)) {
            redirect('maintenance/create', 'Vui lòng chọn thiết bị và nhập mô tả sự cố/nội dung nâng cấp!', 'danger');
        }

        try {
            $this->maintModel->create([
                'device_id'        => $deviceId,
                'maintenance_type' => $type,
                'start_date'       => $startDate,
                'cost'             => $cost,
                'provider'         => $provider,
                'description'      => $description,
                'status'           => 'in_progress'
            ], Auth::id());

            redirect('maintenance', 'Tạo phiếu bảo trì/nâng cấp thành công! Thiết bị đã được chuyển trạng thái.', 'success');
        } catch (Exception $e) {
            redirect('maintenance/create', 'Lỗi: ' . $e->getMessage(), 'danger');
        }
    }

    /**
     * Cập nhật trạng thái hoàn thành bảo trì
     */
    public function updateStatus(int $id): void {
        $this->validateCsrf();
        $returnTo = $this->returnTo('maintenance', 'maintenance');

        $status = $this->post('status', 'completed');
        $endDate = $this->post('end_date', date('Y-m-d'));
        $cost = (float)$this->post('cost', 0);
        $notes = $this->post('notes', '');

        try {
            $this->maintModel->updateStatus($id, $status, $endDate, $cost, $notes, Auth::id());
            redirect($returnTo, 'Đã cập nhật trạng thái hoàn tất bảo dưỡng thành công!', 'success');
        } catch (Exception $e) {
            redirect($returnTo, 'Lỗi cập nhật: ' . $e->getMessage(), 'danger');
        }
    }
}
