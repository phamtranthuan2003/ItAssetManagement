<?php
/**
 * File: app/controllers/DashboardController.php
 * Controller trang chủ / bảng điều khiển thống kê tổng quan
 */

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../models/Device.php';
require_once __DIR__ . '/../models/DeviceHistory.php';

class DashboardController extends Controller {
    private Device $deviceModel;
    private DeviceHistory $historyModel;

    public function __construct() {
        Auth::requireLogin();
        $this->deviceModel = new Device();
        $this->historyModel = new DeviceHistory();
    }

    /**
     * Bảng điều khiển trung tâm
     */
    public function index(): void {
        $stats = $this->deviceModel->getDashboardStats();
        $deptStats = $this->deviceModel->getDevicesByDepartment();
        $overdueDevices = $this->deviceModel->getOverdueDevices();
        $expiringDevices = $this->deviceModel->getExpiringWarrantyDevices(30);
        $recentActivities = $this->historyModel->getRecent(8);

        $this->render('dashboard/index', [
            'pageTitle'        => 'Bảng Điều Khiển Tổng Quan',
            'stats'            => $stats,
            'deptStats'        => $deptStats,
            'overdueDevices'   => $overdueDevices,
            'expiringDevices'  => $expiringDevices,
            'recentActivities' => $recentActivities
        ]);
    }
}
