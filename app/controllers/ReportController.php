<?php
/**
 * File: app/controllers/ReportController.php
 * Controller xử lý báo cáo thống kê chuyên sâu
 */

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../models/Device.php';
require_once __DIR__ . '/../models/Department.php';

class ReportController extends Controller {
    private Device $deviceModel;
    private Department $deptModel;

    public function __construct() {
        Auth::requireLogin();
        $this->deviceModel = new Device();
        $this->deptModel = new Department();
    }

    /**
     * Trang báo cáo tổng hợp
     */
    public function index(): void {
        $stats = $this->deviceModel->getDashboardStats();
        $deptStats = $this->deviceModel->getDevicesByDepartment();
        $overdueDevices = $this->deviceModel->getOverdueDevices();
        $expiringDevices = $this->deviceModel->getExpiringWarrantyDevices(30);

        $this->render('reports/index', [
            'pageTitle'        => 'Báo cáo & Thống kê Kho thiết bị',
            'stats'            => $stats,
            'deptStats'        => $deptStats,
            'overdueDevices'   => $overdueDevices,
            'expiringDevices'  => $expiringDevices
        ]);
    }
}
