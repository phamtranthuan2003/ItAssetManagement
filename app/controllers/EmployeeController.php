<?php
/**
 * File: app/controllers/EmployeeController.php
 * Controller quản lý nhân viên và thu hồi thiết bị khi nghỉ việc
 */

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../models/Employee.php';
require_once __DIR__ . '/../models/Department.php';
require_once __DIR__ . '/../models/Ticket.php';

class EmployeeController extends Controller {
    private Employee $empModel;
    private Department $deptModel;
    private Ticket $ticketModel;

    public function __construct() {
        Auth::requireLogin();
        $this->empModel = new Employee();
        $this->deptModel = new Department();
        $this->ticketModel = new Ticket();
    }

    /**
     * Danh sách nhân viên
     */
    public function index(): void {
        $status = $this->get('status', '');
        $deptId = $this->get('department_id', '');

        $employees = $this->empModel->getAll($status ?: null, $deptId ? (int)$deptId : null);
        $departments = $this->deptModel->getAll();

        $this->render('employees/index', [
            'pageTitle'   => 'Quản lý nhân viên',
            'employees'   => $employees,
            'departments' => $departments,
            'status'      => $status,
            'deptId'      => $deptId
        ]);
    }

    /**
     * Thêm mới nhân viên
     */
    public function store(): void {
        $this->validateCsrf();

        $code = strtoupper($this->post('code', ''));
        $fullname = $this->post('fullname', '');
        $email = $this->post('email', '');
        $phone = $this->post('phone', '');
        $deptId = (int)$this->post('department_id', 0);
        $position = $this->post('position', '');
        $status = $this->post('status', 'working');

        if (empty($code) || empty($fullname) || $deptId <= 0) {
            redirect('employees', 'Vui lòng điền mã nhân viên, họ tên và chọn phòng ban!', 'danger');
        }

        if ($this->empModel->isCodeExists($code)) {
            redirect('employees', "Mã nhân viên [{$code}] đã tồn tại!", 'danger');
        }

        $this->empModel->create([
            'code'          => $code,
            'fullname'      => $fullname,
            'email'         => $email,
            'phone'         => $phone,
            'department_id' => $deptId,
            'position'      => $position,
            'status'        => $status
        ]);

        redirect('employees', "Thêm nhân viên [{$fullname}] thành công!", 'success');
    }

    /**
     * Chi tiết nhân viên và danh sách thiết bị đang nắm giữ
     */
    public function show(int $id): void {
        $employee = $this->empModel->findById($id);

        if (!$employee) {
            redirect('employees', 'Không tìm thấy nhân viên!', 'danger');
        }

        $heldDevices = $this->empModel->getHeldDevices($id);

        $this->render('employees/show', [
            'pageTitle'   => 'Hồ sơ nhân viên: ' . $employee['fullname'],
            'employee'    => $employee,
            'heldDevices' => $heldDevices
        ]);
    }

    /**
     * Cập nhật thông tin nhân viên
     */
    public function update(int $id): void {
        $this->validateCsrf();

        $code = strtoupper($this->post('code', ''));
        $fullname = $this->post('fullname', '');
        $email = $this->post('email', '');
        $phone = $this->post('phone', '');
        $deptId = (int)$this->post('department_id', 0);
        $position = $this->post('position', '');
        $status = $this->post('status', 'working');

        if (empty($code) || empty($fullname) || $deptId <= 0) {
            redirect("employees/{$id}", 'Vui lòng điền mã nhân viên, họ tên và chọn phòng ban!', 'danger');
        }

        if ($this->empModel->isCodeExists($code, $id)) {
            redirect("employees/{$id}", "Mã nhân viên [{$code}] đã tồn tại!", 'danger');
        }

        $this->empModel->update($id, [
            'code'          => $code,
            'fullname'      => $fullname,
            'email'         => $email,
            'phone'         => $phone,
            'department_id' => $deptId,
            'position'      => $position,
            'status'        => $status
        ]);

        redirect("employees/{$id}", "Cập nhật thông tin nhân viên thành công!", 'success');
    }

    /**
     * Thu hồi hàng loạt tất cả thiết bị nhân viên đang giữ (Đặc biệt khi nghỉ việc)
     */
    public function bulkRevoke(int $id): void {
        $this->validateCsrf();

        $employee = $this->empModel->findById($id);
        if (!$employee) {
            redirect('employees', 'Không tìm thấy nhân viên!', 'danger');
        }

        $heldDevices = $this->empModel->getHeldDevices($id);
        if (empty($heldDevices)) {
            redirect("employees/{$id}", 'Nhân viên này hiện không nắm giữ thiết bị nào để thu hồi!', 'info');
        }

        $condition = $this->post('returned_condition', 'good');
        $reason = $this->post('reason', 'Thu hồi thiết bị khi nhân viên nghỉ việc/bàn giao lại tài sản');
        $notes = $this->post('notes', 'Thu hồi tự động hàng loạt từ hồ sơ nhân viên');

        // Tạo mảng thiết bị cần thu hồi
        $devicesItems = [];
        foreach ($heldDevices as $dev) {
            $devicesItems[] = [
                'device_id'          => $dev['id'],
                'accessories'        => 'Đầy đủ phụ kiện theo máy',
                'returned_condition' => $condition,
                'notes'              => $notes
            ];
        }

        $ticketData = [
            'ticket_type'        => 'revocation',
            'ticket_date'        => date('Y-m-d'),
            'from_employee_id'   => $id,
            'from_department_id' => $employee['department_id'],
            'to_employee_id'     => null,
            'to_department_id'   => null,
            'is_temporary'       => 0,
            'status'             => 'completed',
            'reason'             => $reason,
            'notes'              => $notes
        ];

        try {
            $ticketId = $this->ticketModel->createTicket($ticketData, $devicesItems, Auth::id());

            // Tự động cập nhật trạng thái nhân viên thành 'resigned' nếu người dùng chọn
            if ($this->post('mark_as_resigned') == '1') {
                $this->empModel->update($id, [
                    'code'          => $employee['code'],
                    'fullname'      => $employee['fullname'],
                    'email'         => $employee['email'],
                    'phone'         => $employee['phone'],
                    'department_id' => $employee['department_id'],
                    'position'      => $employee['position'],
                    'status'        => 'resigned'
                ]);
            }

            redirect("tickets/{$ticketId}", "Đã thu hồi thành công toàn bộ " . count($devicesItems) . " thiết bị của nhân viên! Biên bản thu hồi đã được tạo.", 'success');
        } catch (Exception $e) {
            redirect("employees/{$id}", "Lỗi khi thu hồi hàng loạt: " . $e->getMessage(), 'danger');
        }
    }

    /**
     * Xóa nhân viên (chỉ khi không còn giữ thiết bị)
     */
    public function delete(int $id): void {
        $this->validateCsrf();

        $heldDevices = $this->empModel->getHeldDevices($id);
        if (!empty($heldDevices)) {
            redirect('employees', 'Không thể xóa nhân viên đang giữ thiết bị! Vui lòng thu hồi trước.', 'danger');
        }

        $this->empModel->delete($id);
        redirect('employees', 'Đã xóa nhân viên thành công!', 'success');
    }
}
