<?php
/**
 * Manage roles and their permissions.
 */

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../models/Role.php';

class RoleController extends Controller {
    private Role $roleModel;

    public function __construct() {
        Auth::requirePermission('roles.manage');
        $this->roleModel = new Role();
    }

    public function index(): void {
        $this->render('roles/index', [
            'pageTitle' => 'Quản lý vai trò & quyền',
            'roles' => $this->roleModel->getAllWithPermissions(),
            'permissionCatalog' => Role::permissionCatalog()
        ]);
    }

    public function store(): void {
        $this->validateCsrf();
        $code = $this->post('code', '');
        $name = $this->post('name', '');
        if (!is_string($code) || !is_string($name)) {
            redirect('roles', 'Dữ liệu vai trò không hợp lệ.', 'danger');
        }
        $code = strtolower(trim($code));
        $name = trim($name);

        if ($code === 'admin' && Auth::id() !== 6) {
            redirect('roles', 'Chỉ Super Admin mới được tạo hoặc phân quyền cho role admin.', 'danger');
        }
        if (!preg_match('/^[a-z][a-z0-9_]{1,39}$/', $code) || $name === '' || strlen($name) > 400) {
            redirect('roles', 'Mã vai trò cần bắt đầu bằng chữ thường, chỉ gồm chữ/số/gạch dưới (2-40 ký tự) và tên không được để trống.', 'danger');
        }
        if ($this->roleModel->exists($code)) {
            redirect('roles', 'Mã vai trò đã tồn tại.', 'danger');
        }

        $this->roleModel->create($code, $name);
        redirect('roles', 'Đã tạo vai trò. Hãy chọn quyền và lưu để hoàn tất cấu hình.', 'success');
    }

    public function updatePermissions(int $id): void {
        $this->validateCsrf();
        $role = $this->roleModel->findById($id);
        if (!$role) {
            redirect('roles', 'Không tìm thấy vai trò.', 'danger');
        }
        if ($role['code'] === 'admin' && Auth::id() !== 6) {
            redirect('roles', 'Chỉ Super Admin mới được phân quyền cho role admin.', 'danger');
        }

        $permissions = $this->post('permissions', []);
        $this->roleModel->updatePermissions($id, is_array($permissions) ? $permissions : []);
        redirect('roles', 'Đã cập nhật quyền cho vai trò ' . $role['name'] . '.', 'success');
    }

    public function delete(int $id): void {
        $this->validateCsrf();
        $role = $this->roleModel->findById($id);
        if (!$role) {
            redirect('roles', 'Không tìm thấy vai trò.', 'danger');
        }
        if (in_array($role['code'], ['admin', 'warehouse', 'customer'], true)) {
            redirect('roles', 'Không thể xóa các vai trò hệ thống mặc định.', 'danger');
        }
        if ($this->roleModel->hasUsers($id)) {
            redirect('roles', 'Không thể xóa vai trò đang được gán cho tài khoản.', 'danger');
        }

        $this->roleModel->delete($id);
        redirect('roles', 'Đã xóa vai trò.', 'success');
    }
}
