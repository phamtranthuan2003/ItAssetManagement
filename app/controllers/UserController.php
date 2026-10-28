<?php
/**
 * File: app/controllers/UserController.php
 * Controller quản lý tài khoản người dùng và phân quyền (Chỉ dành cho Admin)
 */

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Role.php';

class UserController extends Controller {
    private User $userModel;
    private Role $roleModel;

    public function __construct() {
        Auth::requirePermission('users.manage');
        $this->userModel = new User();
        $this->roleModel = new Role();
    }

    /**
     * Danh sách tài khoản
     */
    public function index(): void {
        $users = $this->userModel->getAll();

        $this->render('users/index', [
            'pageTitle' => 'Quản lý tài khoản hệ thống',
            'users'     => $users,
            'roles'     => array_values(array_filter(
                $this->roleModel->getAll(),
                static fn(array $role): bool => Auth::id() === 6 || $role['code'] !== 'admin'
            ))
        ]);
    }

    /**
     * Tạo tài khoản mới
     */
    public function store(): void {
        $this->validateCsrf();

        $username = trim($this->post('username', ''));
        $password = $this->post('password', '');
        $fullname = trim($this->post('fullname', ''));
        $email = trim($this->post('email', ''));
        $role = $this->post('role', 'warehouse');
        $status = (int)$this->post('status', 1);

        if (empty($username) || empty($password) || empty($fullname)) {
            redirect('users', 'Vui lòng điền đầy đủ tên đăng nhập, mật khẩu và họ tên!', 'danger');
        }

        if (strlen($password) < 6) {
            redirect('users', 'Mật khẩu phải có tối thiểu 6 ký tự!', 'danger');
        }

        if ($this->userModel->isUsernameExists($username)) {
            redirect('users', "Tên đăng nhập [{$username}] đã tồn tại!", 'danger');
        }
        if (!is_string($role) || !$this->userModel->roleExists($role)) {
            redirect('users', 'Vai trò được chọn không hợp lệ.', 'danger');
        }
        if ($role === 'admin' && Auth::id() !== 6) {
            redirect('users', 'Chỉ Super Admin mới được cấp role Admin.', 'danger');
        }

        $this->userModel->create([
            'username' => $username,
            'password' => $password,
            'fullname' => $fullname,
            'email'    => $email,
            'role'     => $role,
            'status'   => $status
        ]);

        redirect('users', "Tạo tài khoản [{$username}] thành công!", 'success');
    }

    /**
     * Cập nhật thông tin tài khoản
     */
    public function update(int $id): void {
        $this->validateCsrf();

        $targetUser = $this->userModel->findById($id);
        if (!$targetUser) {
            redirect('users', 'Không tìm thấy tài khoản.', 'danger');
        }
        if ($id === 6 && Auth::id() !== 6) {
            redirect('users', 'Chỉ Super Admin mới được quản lý tài khoản Super Admin.', 'danger');
        }
        if ($targetUser['role'] === 'admin' && Auth::id() !== 6) {
            redirect('users', 'Chỉ Super Admin mới được quản lý tài khoản thuộc role Admin.', 'danger');
        }

        $fullname = trim($this->post('fullname', ''));
        $email = trim($this->post('email', ''));
        $isSelf = $id === Auth::id();
        $role = ($id === 6 || $isSelf) ? $targetUser['role'] : $this->post('role', 'warehouse');
        $status = ($id === 6 || $isSelf) ? (int)$targetUser['status'] : (int)$this->post('status', 1);
        $password = $this->post('password', '');

        if (empty($fullname)) {
            redirect('users', 'Họ tên không được để trống!', 'danger');
        }
        if (!is_string($role) || !$this->userModel->roleExists($role)) {
            redirect('users', 'Vai trò được chọn không hợp lệ.', 'danger');
        }
        if ($role === 'admin' && Auth::id() !== 6) {
            redirect('users', 'Chỉ Super Admin mới được cấp role Admin.', 'danger');
        }

        $data = [
            'fullname' => $fullname,
            'email'    => $email,
            'role'     => $role,
            'status'   => $status
        ];

        if (!empty($password)) {
            if (strlen($password) < 6) {
                redirect('users', 'Mật khẩu mới phải có tối thiểu 6 ký tự!', 'danger');
            }
            $data['password'] = $password;
        }

        $this->userModel->update($id, $data);
        redirect('users', "Cập nhật tài khoản thành công!", 'success');
    }

    /**
     * Xóa tài khoản
     */
    public function delete(int $id): void {
        $this->validateCsrf();

        if ($id === 6) {
            redirect('users', 'Không thể xóa tài khoản Super Admin.', 'danger');
        }
        if ($id === Auth::id()) {
            redirect('users', 'Bạn không thể tự xóa tài khoản của chính mình!', 'danger');
        }

        $this->userModel->deactivate($id);
        redirect('users', 'Đã khóa tài khoản. Lịch sử thao tác được giữ nguyên để bảo toàn kiểm toán.', 'success');
    }
}
