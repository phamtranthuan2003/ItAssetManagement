<?php
/**
 * File: app/controllers/DepartmentController.php
 * Controller quản lý danh mục phòng ban
 */

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../models/Department.php';

class DepartmentController extends Controller {
    private Department $deptModel;

    public function __construct() {
        Auth::requireLogin();
        $this->deptModel = new Department();
    }

    /**
     * Danh sách phòng ban
     */
    public function index(): void {
        $keyword = $this->get('keyword', '');
        $departments = $this->deptModel->getAll($keyword);

        $this->render('departments/index', [
            'pageTitle'   => 'Quản lý phòng ban',
            'departments' => $departments,
            'keyword'     => $keyword,
            'returnTo'    => 'departments' . ($keyword !== '' ? '?' . http_build_query(['keyword' => $keyword]) : '')
        ]);
    }

    /**
     * Thêm mới phòng ban
     */
    public function store(): void {
        $this->validateCsrf();
        $returnTo = $this->returnTo('departments', 'departments');

        $code = strtoupper($this->post('code', ''));
        $name = $this->post('name', '');
        $description = $this->post('description', '');

        if (empty($code) || empty($name)) {
            redirect($returnTo, 'Mã và tên phòng ban là bắt buộc!', 'danger');
        }

        if ($this->deptModel->isCodeExists($code)) {
            redirect($returnTo, "Mã phòng ban [{$code}] đã tồn tại!", 'danger');
        }

        $this->deptModel->create([
            'code'        => $code,
            'name'        => $name,
            'description' => $description
        ]);

        redirect($returnTo, "Thêm mới phòng ban [{$name}] thành công!", 'success');
    }

    /**
     * Cập nhật thông tin phòng ban
     */
    public function update(int $id): void {
        $this->validateCsrf();
        $returnTo = $this->returnTo('departments', 'departments');

        $code = strtoupper($this->post('code', ''));
        $name = $this->post('name', '');
        $description = $this->post('description', '');

        if (empty($code) || empty($name)) {
            redirect($returnTo, 'Mã và tên phòng ban là bắt buộc!', 'danger');
        }

        if ($this->deptModel->isCodeExists($code, $id)) {
            redirect($returnTo, "Mã phòng ban [{$code}] đã tồn tại ở phòng ban khác!", 'danger');
        }

        $this->deptModel->update($id, [
            'code'        => $code,
            'name'        => $name,
            'description' => $description
        ]);

        redirect($returnTo, "Cập nhật phòng ban [{$name}] thành công!", 'success');
    }

    /**
     * Xóa phòng ban (kiểm tra an toàn dữ liệu)
     */
    public function delete(int $id): void {
        $this->validateCsrf();
        $returnTo = $this->returnTo('departments', 'departments');

        if (!$this->deptModel->canDelete($id)) {
            redirect($returnTo, 'Không thể xóa phòng ban đang có nhân viên hoặc thiết bị liên kết!', 'danger');
        }

        $this->deptModel->delete($id);
        redirect($returnTo, 'Đã xóa phòng ban thành công!', 'success');
    }
}
