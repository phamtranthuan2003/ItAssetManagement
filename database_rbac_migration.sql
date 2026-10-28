-- Migration RBAC và thiết lập Super Admin cho database hiện hữu.
-- Có thể chạy lại sau khi tài khoản ID 6 đã được tạo.
USE `warehouse_db`;

ALTER TABLE `users`
    MODIFY `role` VARCHAR(40) NOT NULL DEFAULT 'warehouse' COMMENT 'Mã vai trò RBAC';

CREATE TABLE IF NOT EXISTS `roles` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(40) NOT NULL UNIQUE,
    `name` VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `permissions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(80) NOT NULL UNIQUE,
    `name` VARCHAR(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `role_permissions` (
    `role_id` INT NOT NULL,
    `permission_id` INT NOT NULL,
    PRIMARY KEY (`role_id`, `permission_id`),
    CONSTRAINT `fk_role_permission_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_role_permission_permission` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `roles` (`code`, `name`) VALUES
('admin', 'Quản trị viên'),
('warehouse', 'Nhân viên kho'),
('customer', 'Khách hàng');

-- Promote the designated account and demote the legacy admin once account ID 6 exists.
UPDATE `users` SET `role` = 'admin', `status` = 1 WHERE `id` = 6;
SET @superadmin_exists = (SELECT COUNT(*) FROM `users` WHERE `id` = 6);
UPDATE `users` SET `role` = 'warehouse'
WHERE `id` = 1 AND @superadmin_exists > 0;

INSERT IGNORE INTO `permissions` (`code`, `name`) VALUES
('dashboard.view', 'Xem bảng điều khiển'),
('devices.view', 'Xem kho thiết bị'), ('devices.create', 'Thêm thiết bị'), ('devices.update', 'Sửa thiết bị'), ('devices.delete', 'Xóa thiết bị'), ('devices.export', 'Xuất danh sách thiết bị'),
('tickets.view', 'Xem phiếu'), ('tickets.create', 'Lập phiếu'), ('tickets.print', 'In phiếu'),
('maintenance.view', 'Xem bảo trì'), ('maintenance.create', 'Tạo phiếu bảo trì'), ('maintenance.update', 'Cập nhật bảo trì'),
('departments.view', 'Xem phòng ban'), ('departments.create', 'Thêm phòng ban'), ('departments.update', 'Sửa phòng ban'), ('departments.delete', 'Xóa phòng ban'),
('employees.view', 'Xem nhân viên'), ('employees.create', 'Thêm nhân viên'), ('employees.update', 'Sửa nhân viên'), ('employees.delete', 'Xóa nhân viên'), ('employees.revoke', 'Thu hồi thiết bị nhân viên'),
('reports.view', 'Xem báo cáo'),
('users.manage', 'Quản lý tài khoản'),
('roles.manage', 'Quản lý vai trò & phân quyền');

DELETE rp FROM `role_permissions` rp
INNER JOIN `roles` r ON r.id = rp.role_id
INNER JOIN `permissions` p ON p.id = rp.permission_id
WHERE r.code = 'warehouse' AND p.code IN ('users.manage', 'roles.manage');
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id FROM `roles` r CROSS JOIN `permissions` p
WHERE r.code = 'warehouse' AND p.code NOT IN ('devices.delete', 'users.manage', 'roles.manage');

-- Give the Admin role its initial permission set only when it has never been configured.
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id FROM `roles` r CROSS JOIN `permissions` p
WHERE r.code = 'admin'
AND NOT EXISTS (SELECT 1 FROM `role_permissions` existing WHERE existing.role_id = r.id);

-- Existing Admin roles may already have permissions, so add account management explicitly.
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id
FROM `roles` r
INNER JOIN `permissions` p ON p.code = 'users.manage'
WHERE r.code = 'admin';
