<?php
/**
 * File: app/controllers/TicketController.php
 * Controller xử lý quy trình Cấp phát, Chuyển giao, Thu hồi thiết bị và In biên bản
 */

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../models/Ticket.php';
require_once __DIR__ . '/../models/TicketDetail.php';
require_once __DIR__ . '/../models/Device.php';
require_once __DIR__ . '/../models/Department.php';
require_once __DIR__ . '/../models/Employee.php';
require_once __DIR__ . '/../models/User.php';

class TicketController extends Controller {
    private Ticket $ticketModel;
    private TicketDetail $detailModel;
    private Device $deviceModel;
    private Department $deptModel;
    private Employee $empModel;
    private User $userModel;

    public function __construct() {
        Auth::requireLogin();
        $this->ticketModel = new Ticket();
        $this->detailModel = new TicketDetail();
        $this->deviceModel = new Device();
        $this->deptModel = new Department();
        $this->empModel = new Employee();
        $this->userModel = new User();
    }

    /**
     * Danh sách phiếu
     */
    public function index(): void {
        $page = max(1, (int)$this->get('page', 1));
        $perPage = 10;
        $type = $this->get('type', '');
        $status = $this->get('status', '');
        $filters = [
            'type' => $type,
            'status' => $status,
            'to_employee_id' => $this->get('to_employee_id', ''),
            'created_by' => $this->get('created_by', '')
        ];

        $baseFilters = $filters; unset($baseFilters['type']);
        $typeCounts = [
            'all'        => $this->ticketModel->countFiltered(null, $status ?: null, $baseFilters),
            'allocation' => $this->ticketModel->countFiltered('allocation', $status ?: null, $baseFilters),
            'transfer'   => $this->ticketModel->countFiltered('transfer', $status ?: null, $baseFilters),
            'revocation' => $this->ticketModel->countFiltered('revocation', $status ?: null, $baseFilters),
        ];

        $totalTickets = $this->ticketModel->countFiltered($type ?: null, $status ?: null, $filters);
        $totalPages = (int)ceil($totalTickets / $perPage);
        $page = min($page, max(1, $totalPages));
        $tickets = $this->ticketModel->getAll($type ?: null, $status ?: null, $filters, $page, $perPage);
        $employees = $this->empModel->getForSelect();
        $users = $this->userModel->getAll();

        $this->render('tickets/index', [
            'pageTitle'   => 'Quản lý phiếu cấp phát / chuyển giao / thu hồi',
            'tickets'     => $tickets,
            'type'        => $type,
            'status'      => $status,
            'filters'     => $filters,
            'employees'   => $employees,
            'users'       => $users,
            'page'        => $page,
            'totalPages'  => $totalPages,
            'totalTickets' => $totalTickets,
            'typeCounts'   => $typeCounts
        ]);
    }

    /**
     * Giao diện tạo phiếu mới với bộ chọn đa thiết bị tương tác
     */
    public function create(): void {
        $type = $this->get('type', 'allocation'); // allocation, transfer, revocation
        $inStockDevices = $this->deviceModel->getAvailableInStock();
        $allocatedDevices = $this->deviceModel->getAllocatedDevices();
        $departments = $this->deptModel->getAll();
        $employees = $this->empModel->getForSelect($type === 'revocation' ? null : 'working');

        $this->render('tickets/create', [
            'pageTitle'        => 'Lập phiếu mới',
            'type'             => $type,
            'inStockDevices'   => $inStockDevices,
            'allocatedDevices' => $allocatedDevices,
            'departments'      => $departments,
            'employees'        => $employees
        ]);
    }

    /**
     * Xử lý lưu phiếu mới
     */
    public function store(): void {
        $this->validateCsrf();

        $ticketType = $this->post('ticket_type', 'allocation');
        $ticketDate = $this->post('ticket_date', date('Y-m-d'));
        $fromEmpId = $this->post('from_employee_id', null);
        $fromDeptId = $this->post('from_department_id', null);
        $toEmpId = $this->post('to_employee_id', null);
        $toDeptId = $this->post('to_department_id', null);
        $isTemporary = $this->post('is_temporary') == '1' ? 1 : 0;
        $returnDate = $this->post('expected_return_date', null);
        $reason = $this->post('reason', '');
        $notes = $this->post('notes', '');
        $status = $this->post('status', 'completed');

        // Mảng các thiết bị được chọn
        $deviceIds = $_POST['device_id'] ?? [];
        $accessories = $_POST['accessories'] ?? [];
        $returnedConditions = $_POST['returned_condition'] ?? [];
        $itemNotes = $_POST['item_notes'] ?? [];

        if (empty($deviceIds) || !is_array($deviceIds)) {
            redirect('tickets/create?type=' . $ticketType, 'Vui lòng chọn ít nhất một thiết bị vào phiếu!', 'danger');
        }

        if ($ticketType === 'revocation' || $ticketType === 'transfer') {
            if (empty($fromEmpId)) {
                $typeLabel = $ticketType === 'transfer' ? 'transfer' : 'revocation';
                redirect('tickets/create?type=' . $typeLabel, 'Vui lòng chọn nhân viên đang bàn giao thiết bị!', 'danger');
            }

            $employeeDeviceIds = array_map(
                'intval',
                array_column($this->deviceModel->getAllocatedDevices((int)$fromEmpId), 'id')
            );
            foreach ($deviceIds as $deviceId) {
                if (!empty($deviceId) && !in_array((int)$deviceId, $employeeDeviceIds, true)) {
                    redirect(
                        'tickets/create?type=' . $ticketType,
                        'Thiết bị được chọn không thuộc nhân viên đã chọn hoặc không còn được cấp phát!',
                        'danger'
                    );
                }
            }
        }

        // Kiểm tra logic chọn người/phòng ban
        if ($ticketType === 'allocation') {
            if (empty($toEmpId) || empty($toDeptId)) {
                redirect('tickets/create?type=allocation', 'Cấp phát yêu cầu phải chọn nhân viên nhận và phòng ban!', 'danger');
            }
        } elseif ($ticketType === 'transfer') {
            if (empty($toEmpId) || empty($toDeptId)) {
                redirect('tickets/create?type=transfer', 'Chuyển giao yêu cầu phải chọn nhân viên nhận mới và phòng ban mới!', 'danger');
            }
        }

        // Chuẩn bị danh sách thiết bị
        $devicesItems = [];
        foreach ($deviceIds as $index => $devId) {
            if (empty($devId)) continue;
            $devicesItems[] = [
                'device_id'          => (int)$devId,
                'accessories'        => $accessories[$index] ?? '',
                'returned_condition' => $returnedConditions[$index] ?? null,
                'notes'              => $itemNotes[$index] ?? ''
            ];
        }

        if (empty($devicesItems)) {
            redirect('tickets/create?type=' . $ticketType, 'Không có thiết bị hợp lệ nào được chọn!', 'danger');
        }

        $ticketData = [
            'ticket_type'          => $ticketType,
            'ticket_date'          => $ticketDate,
            'from_employee_id'     => $fromEmpId,
            'from_department_id'   => $fromDeptId,
            'to_employee_id'       => $toEmpId,
            'to_department_id'     => $toDeptId,
            'is_temporary'         => $isTemporary,
            'expected_return_date' => $returnDate,
            'status'               => $status,
            'reason'               => $reason,
            'notes'                => $notes
        ];

        try {
            $ticketId = $this->ticketModel->createTicket($ticketData, $devicesItems, Auth::id());
            redirect("tickets/{$ticketId}", 'Lập phiếu thành công! Bạn có thể xem hoặc in biên bản bàn giao bên dưới.', 'success');
        } catch (Exception $e) {
            redirect('tickets/create?type=' . $ticketType, 'Lỗi tạo phiếu: ' . $e->getMessage(), 'danger');
        }
    }

    /**
     * Chi tiết phiếu
     */
    public function show(int $id): void {
        $ticket = $this->ticketModel->findById($id);

        if (!$ticket) {
            redirect('tickets', 'Không tìm thấy phiếu yêu cầu!', 'danger');
        }

        $details = $this->detailModel->getByTicketId($id);

        $this->render('tickets/show', [
            'pageTitle' => 'Chi tiết phiếu: ' . $ticket['ticket_code'],
            'ticket'    => $ticket,
            'details'   => $details
        ]);
    }

    /**
     * Trang in Biên bản bàn giao / chuyển giao / thu hồi (HTML In đẹp có chữ ký)
     */
    public function printTicket(int $id): void {
        $ticket = $this->ticketModel->findById($id);

        if (!$ticket) {
            redirect('tickets', 'Không tìm thấy phiếu yêu cầu!', 'danger');
        }

        $details = $this->detailModel->getByTicketId($id);

        // Render view riêng không dùng layout chung (được tối ưu in ấn và hiển thị chỗ ký tên)
        $this->render('tickets/print', [
            'pageTitle' => 'In biên bản bàn giao: ' . $ticket['ticket_code'],
            'ticket'    => $ticket,
            'details'   => $details
        ], null);
    }
}
