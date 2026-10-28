<?php
/**
 * Role and permission management.
 */

require_once __DIR__ . '/../../core/Model.php';

class Role extends Model {
    private const PERMISSIONS = [
        'dashboard' => ['view' => 'Xem bảng điều khiển'],
        'devices' => [
            'view' => 'Xem kho thiết bị', 'create' => 'Thêm thiết bị', 'update' => 'Sửa thiết bị',
            'delete' => 'Xóa thiết bị', 'export' => 'Xuất danh sách thiết bị'
        ],
        'tickets' => [
            'view' => 'Xem phiếu', 'create' => 'Lập phiếu', 'print' => 'In phiếu'
        ],
        'maintenance' => [
            'view' => 'Xem bảo trì', 'create' => 'Tạo phiếu bảo trì', 'update' => 'Cập nhật bảo trì'
        ],
        'departments' => [
            'view' => 'Xem phòng ban', 'create' => 'Thêm phòng ban', 'update' => 'Sửa phòng ban',
            'delete' => 'Xóa phòng ban'
        ],
        'employees' => [
            'view' => 'Xem nhân viên', 'create' => 'Thêm nhân viên', 'update' => 'Sửa nhân viên',
            'delete' => 'Xóa nhân viên', 'revoke' => 'Thu hồi thiết bị nhân viên'
        ],
        'reports' => ['view' => 'Xem báo cáo'],
        'users' => ['manage' => 'Quản lý tài khoản'],
        'roles' => ['manage' => 'Quản lý vai trò & phân quyền']
    ];

    public static function permissionCatalog(): array {
        $catalog = [];
        foreach (self::PERMISSIONS as $module => $permissions) {
            foreach ($permissions as $action => $label) {
                $catalog[$module . '.' . $action] = $label;
            }
        }
        return $catalog;
    }

    public function getAllWithPermissions(): array {
        $roles = $this->fetchAll('SELECT id, code, name FROM roles ORDER BY id');
        foreach ($roles as &$role) {
            $role['permissions'] = $this->fetchAll(
                'SELECT p.code FROM role_permissions rp
                 INNER JOIN permissions p ON p.id = rp.permission_id
                 WHERE rp.role_id = :role_id',
                ['role_id' => $role['id']]
            );
            $role['permissions'] = array_column($role['permissions'], 'code');
        }
        unset($role);
        return $roles;
    }

    public function getAll(): array {
        return $this->fetchAll('SELECT id, code, name FROM roles ORDER BY name');
    }

    public function exists(string $code): bool {
        return $this->count('SELECT COUNT(*) FROM roles WHERE code = :code', ['code' => $code]) > 0;
    }

    public function create(string $code, string $name): int {
        $this->query('INSERT INTO roles (code, name) VALUES (:code, :name)', [
            'code' => $code,
            'name' => $name
        ]);
        return $this->lastInsertId();
    }

    public function updatePermissions(int $id, array $permissionCodes): void {
        $catalog = self::permissionCatalog();
        $permissionCodes = array_values(array_unique(array_filter(
            $permissionCodes,
            static fn($code): bool => is_string($code) && isset($catalog[$code])
        )));

        $this->beginTransaction();
        try {
            $this->query('DELETE FROM role_permissions WHERE role_id = :id', ['id' => $id]);
            foreach ($permissionCodes as $code) {
                $this->query(
                    'INSERT INTO role_permissions (role_id, permission_id)
                     SELECT :role_id, id FROM permissions WHERE code = :code',
                    ['role_id' => $id, 'code' => $code]
                );
            }
            $this->commit();
        } catch (Throwable $e) {
            $this->rollBack();
            throw $e;
        }
    }

    public function delete(int $id): bool {
        return (bool)$this->query('DELETE FROM roles WHERE id = :id', ['id' => $id]);
    }

    public function hasUsers(int $id): bool {
        return $this->count('SELECT COUNT(*) FROM users u INNER JOIN roles r ON r.code = u.role WHERE r.id = :id', [
            'id' => $id
        ]) > 0;
    }

    public function findById(int $id): ?array {
        return $this->fetch('SELECT id, code, name FROM roles WHERE id = :id', ['id' => $id]);
    }
}
