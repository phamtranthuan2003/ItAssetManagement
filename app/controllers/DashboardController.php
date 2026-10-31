<?php
/**
 * File: app/controllers/DashboardController.php
 * Controller trang chủ / bảng điều khiển thống kê tổng quan
 */

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../models/Device.php';
require_once __DIR__ . '/../models/DeviceHistory.php';
require_once __DIR__ . '/../models/Ticket.php';

class DashboardController extends Controller {
    private Device $deviceModel;
    private DeviceHistory $historyModel;
    private Ticket $ticketModel;

    public function __construct() {
        Auth::requireLogin();
        $this->deviceModel = new Device();
        $this->historyModel = new DeviceHistory();
        $this->ticketModel = new Ticket();
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
        $monthlyAllocation = $this->ticketModel->getAllocationStatsByThreeMonths();

        $this->render('dashboard/index', [
            'pageTitle'          => 'Bảng Điều Khiển Tổng Quan',
            'stats'              => $stats,
            'deptStats'          => $deptStats,
            'overdueDevices'     => $overdueDevices,
            'expiringDevices'    => $expiringDevices,
            'recentActivities'   => $recentActivities,
            'monthlyAllocation'  => $monthlyAllocation
        ]);
    }

    /**
     * API AJAX lấy danh sách phiếu cấp phát trong tháng (phục vụ cuộn lướt đến đâu tải đến đó)
     */
    public function monthlyTickets(): void {
        $month = $this->get('month', date('Y-m'));
        if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
            $month = date('Y-m');
        }

        $page = max(1, (int)$this->get('page', 1));
        $limit = max(1, min(50, (int)$this->get('limit', 15)));

        $result = $this->ticketModel->getMonthlyAllocationsPaginated($month, $page, $limit);

        $formatted = [];
        foreach ($result['tickets'] as $t) {
            $formatted[] = [
                'id'                 => (int)$t['id'],
                'ticket_code'        => $t['ticket_code'],
                'ticket_date'        => formatDate($t['ticket_date']),
                'is_temporary'       => (bool)$t['is_temporary'],
                'to_employee_name'   => $t['to_employee_name'] ?: 'Chưa gán',
                'to_department_name' => $t['to_department_name'] ?: '---',
                'device_count'       => (int)$t['device_count'],
                'url'                => url('tickets/' . (int)$t['id'])
            ];
        }

        $this->json([
            'success'  => true,
            'month'    => $month,
            'page'     => $result['page'],
            'limit'    => $result['limit'],
            'total'    => $result['total'],
            'has_more' => $result['has_more'],
            'tickets'  => $formatted
        ]);
    }
}
