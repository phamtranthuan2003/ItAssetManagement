-- ==========================================================
-- CƠ SỞ DỮ LIỆU QUẢN LÝ THIẾT BỊ KHO (PC, LAPTOP)
-- Hỗ trợ: MySQL 5.7+ / MariaDB 10.3+, Chạy local trên XAMPP
-- Bảng mã: UTF8MB4 (Đầy đủ tiếng Việt có dấu)
-- ==========================================================

CREATE DATABASE IF NOT EXISTS `warehouse_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `warehouse_db`;

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- 1. BẢNG TÀI KHOẢN NGƯỜI DÙNG (USERS)
DROP TABLE IF EXISTS `maintenances`;
DROP TABLE IF EXISTS `device_history`;
DROP TABLE IF EXISTS `ticket_details`;
DROP TABLE IF EXISTS `tickets`;
DROP TABLE IF EXISTS `device_monitors`;
DROP TABLE IF EXISTS `device_specs`;
DROP TABLE IF EXISTS `devices`;
DROP TABLE IF EXISTS `employees`;
DROP TABLE IF EXISTS `departments`;
DROP TABLE IF EXISTS `role_permissions`;
DROP TABLE IF EXISTS `permissions`;
DROP TABLE IF EXISTS `roles`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `fullname` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) DEFAULT NULL,
    `role` VARCHAR(40) NOT NULL DEFAULT 'warehouse' COMMENT 'Mã vai trò RBAC',
    `status` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1: Hoạt động, 0: Khóa',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `roles` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(40) NOT NULL UNIQUE,
    `name` VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `permissions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(80) NOT NULL UNIQUE,
    `name` VARCHAR(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `role_permissions` (
    `role_id` INT NOT NULL,
    `permission_id` INT NOT NULL,
    PRIMARY KEY (`role_id`, `permission_id`),
    CONSTRAINT `fk_role_permission_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_role_permission_permission` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. BẢNG PHÒNG BAN (DEPARTMENTS)
CREATE TABLE `departments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(20) NOT NULL UNIQUE,
    `name` VARCHAR(100) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. BẢNG NHÂN VIÊN (EMPLOYEES)
CREATE TABLE `employees` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(20) NOT NULL UNIQUE,
    `fullname` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) DEFAULT NULL,
    `phone` VARCHAR(20) DEFAULT NULL,
    `department_id` INT NOT NULL,
    `position` VARCHAR(100) DEFAULT NULL,
    `status` ENUM('working', 'resigned') NOT NULL DEFAULT 'working' COMMENT 'working: Đang làm việc, resigned: Đã nghỉ việc',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_emp_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. BẢNG THIẾT BỊ (DEVICES)
CREATE TABLE `devices` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `device_code` VARCHAR(50) NOT NULL UNIQUE COMMENT 'Mã thiết bị duy nhất',
    `type` VARCHAR(50) NOT NULL COMMENT 'Loại thiết bị: Laptop, PC, máy in, máy CNC...',
    `brand` VARCHAR(50) NOT NULL DEFAULT '' COMMENT 'Hãng sản xuất',
    `model` VARCHAR(100) NOT NULL DEFAULT '' COMMENT 'Tên model',
    `serial_number` VARCHAR(100) DEFAULT NULL COMMENT 'Số serial',
    `import_date` DATE NOT NULL COMMENT 'Ngày nhập kho',
    `warranty_expiry` DATE DEFAULT NULL COMMENT 'Ngày hết hạn bảo hành',
    `condition_state` ENUM('new', 'used', 'broken', 'under_warranty') NOT NULL DEFAULT 'new' COMMENT 'Tình trạng: Mới, Đang dùng, Hỏng, Bảo hành',
    `status` ENUM('in_stock', 'allocated', 'maintenance', 'liquidated') NOT NULL DEFAULT 'in_stock' COMMENT 'Trạng thái: Trong kho, Đang cấp phát, Bảo trì, Thanh lý',
    `current_employee_id` INT DEFAULT NULL COMMENT 'Nhân viên đang giữ thiết bị',
    `current_department_id` INT DEFAULT NULL COMMENT 'Phòng ban đang sử dụng',
    `is_temporary` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1: Cấp phát tạm thời, 0: Dài hạn',
    `expected_return_date` DATE DEFAULT NULL COMMENT 'Ngày dự kiến trả (nếu tạm thời)',
    `notes` TEXT DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_dev_employee` FOREIGN KEY (`current_employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_dev_department` FOREIGN KEY (`current_department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. BẢNG CẤU HÌNH CHI TIẾT THIẾT BỊ (DEVICE_SPECS)
CREATE TABLE `device_specs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `device_id` INT NOT NULL UNIQUE,
    `cpu` VARCHAR(100) DEFAULT NULL COMMENT 'Bộ vi xử lý CPU',
    `mainboard` VARCHAR(100) DEFAULT NULL COMMENT 'Chỉ áp dụng cho PC',
    `ram_gb` INT DEFAULT NULL COMMENT 'Dung lượng RAM tính bằng GB',
    `ram_type` VARCHAR(50) DEFAULT NULL COMMENT 'Loại RAM: DDR4, DDR5...',
    `ssd_capacity` VARCHAR(50) DEFAULT NULL COMMENT 'Dung lượng SSD: 256GB, 512GB...',
    `hdd_capacity` VARCHAR(50) DEFAULT NULL COMMENT 'Dung lượng HDD: 1TB, Không...',
    `gpu` VARCHAR(100) DEFAULT NULL COMMENT 'Card đồ họa rời hoặc Onboard',
    `screen_size` VARCHAR(50) DEFAULT NULL COMMENT 'Kích thước màn hình (cho Laptop)',
    `notes` TEXT DEFAULT NULL,
    CONSTRAINT `fk_spec_device` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `device_monitors` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `device_id` INT NOT NULL,
    `monitor_no` INT NOT NULL,
    `brand` VARCHAR(100) NOT NULL COMMENT 'Hãng màn hình',
    `model` VARCHAR(100) DEFAULT NULL COMMENT 'Model màn hình',
    `size` DECIMAL(4,1) NOT NULL COMMENT 'Kích thước màn hình tính bằng inch',
    UNIQUE KEY `uq_device_monitor_no` (`device_id`, `monitor_no`),
    CONSTRAINT `fk_monitor_device` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. BẢNG PHIẾU CẤP PHÁT / CHUYỂN GIAO / THU HỒI (TICKETS)
CREATE TABLE `tickets` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `ticket_code` VARCHAR(50) NOT NULL UNIQUE COMMENT 'Mã phiếu',
    `ticket_type` ENUM('allocation', 'transfer', 'revocation') NOT NULL COMMENT 'allocation: Cấp phát, transfer: Chuyển giao, revocation: Thu hồi',
    `ticket_date` DATE NOT NULL COMMENT 'Ngày thực hiện',
    `created_by` INT NOT NULL COMMENT 'Người tạo phiếu / thủ kho',
    `from_employee_id` INT DEFAULT NULL COMMENT 'Người giao / người bị thu hồi / người chuyển',
    `from_department_id` INT DEFAULT NULL,
    `to_employee_id` INT DEFAULT NULL COMMENT 'Người nhận cấp phát / chuyển giao',
    `to_department_id` INT DEFAULT NULL,
    `is_temporary` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Cấp phát tạm thời',
    `expected_return_date` DATE DEFAULT NULL COMMENT 'Hạn trả dự kiến',
    `status` ENUM('draft', 'pending', 'completed', 'cancelled') NOT NULL DEFAULT 'completed' COMMENT 'Trạng thái phiếu',
    `reason` TEXT DEFAULT NULL COMMENT 'Lý do cấp/chuyển/thu hồi',
    `notes` TEXT DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_tick_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
    CONSTRAINT `fk_tick_from_emp` FOREIGN KEY (`from_employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_tick_from_dept` FOREIGN KEY (`from_department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_tick_to_emp` FOREIGN KEY (`to_employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_tick_to_dept` FOREIGN KEY (`to_department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. BẢNG CHI TIẾT THIẾT BỊ TRONG PHIẾU (TICKET_DETAILS)
CREATE TABLE `ticket_details` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `ticket_id` INT NOT NULL,
    `device_id` INT NOT NULL,
    `accessories` VARCHAR(255) DEFAULT NULL COMMENT 'Phụ kiện đi kèm: Chuột, phím, sạc, cáp HDMI...',
    `returned_condition` ENUM('good', 'broken', 'missing_accessories') DEFAULT NULL COMMENT 'Tình trạng khi thu hồi',
    `notes` TEXT DEFAULT NULL,
    CONSTRAINT `fk_td_ticket` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_td_device` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. BẢNG LỊCH SỬ THIẾT BỊ & NHẬT KÝ THAO TÁC (DEVICE_HISTORY)
CREATE TABLE `device_history` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `device_id` INT NOT NULL,
    `ticket_id` INT DEFAULT NULL,
    `action_type` VARCHAR(50) NOT NULL COMMENT 'import, allocate, transfer, revoke, maintenance_start, maintenance_done, upgrade, liquidate',
    `action_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `performed_by` INT NOT NULL,
    `from_employee_id` INT DEFAULT NULL,
    `to_employee_id` INT DEFAULT NULL,
    `from_department_id` INT DEFAULT NULL,
    `to_department_id` INT DEFAULT NULL,
    `description` TEXT NOT NULL COMMENT 'Mô tả chi tiết hành động',
    CONSTRAINT `fk_dh_device` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_dh_ticket` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_dh_user` FOREIGN KEY (`performed_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. BẢNG BẢO TRÌ, SỬA CHỮA, NÂNG CẤP LINH KIỆN (MAINTENANCES)
CREATE TABLE `maintenances` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `device_id` INT NOT NULL,
    `maintenance_type` ENUM('repair', 'upgrade', 'routine') NOT NULL DEFAULT 'repair' COMMENT 'Sửa chữa, Nâng cấp linh kiện, Bảo trì định kỳ',
    `start_date` DATE NOT NULL,
    `end_date` DATE DEFAULT NULL,
    `cost` DECIMAL(12, 2) NOT NULL DEFAULT 0.00 COMMENT 'Chi phí bảo dưỡng/sửa chữa/nâng cấp',
    `provider` VARCHAR(100) DEFAULT NULL COMMENT 'Đơn vị thực hiện',
    `description` TEXT NOT NULL COMMENT 'Chi tiết sự cố hoặc nội dung nâng cấp linh kiện',
    `status` ENUM('in_progress', 'completed', 'cannot_repair') NOT NULL DEFAULT 'in_progress' COMMENT 'Đang xử lý, Hoàn thành, Không thể sửa chữa',
    `created_by` INT NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_maint_device` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_maint_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================================
-- DỮ LIỆU MẪU (SEED DATA)
-- ==========================================================

-- 1. Tài khoản mẫu (Mật khẩu đều là: password123)
INSERT INTO `users` (`id`, `username`, `password`, `fullname`, `email`, `role`, `status`) VALUES
(1, 'admin', '$2y$10$2yKvpupzQNm41o.MhNcV4OWwIt1OQkIRphDPOmewPgLhs35TdBxqK', 'Quản trị viên Hệ thống', 'admin@warehouse.local', 'admin', 1),
(2, 'thukho', '$2y$10$2yKvpupzQNm41o.MhNcV4OWwIt1OQkIRphDPOmewPgLhs35TdBxqK', 'Nguyễn Văn Kho', 'thukho@warehouse.local', 'warehouse', 1);

-- 1.1 Vai trò mặc định và danh mục quyền
INSERT INTO `roles` (`code`, `name`) VALUES
('admin', 'Quản trị viên'),
('warehouse', 'Nhân viên kho'),
('customer', 'Khách hàng');
INSERT INTO `permissions` (`code`, `name`) VALUES
('dashboard.view', 'Xem bảng điều khiển'),
('devices.view', 'Xem kho thiết bị'), ('devices.create', 'Thêm thiết bị'), ('devices.update', 'Sửa thiết bị'), ('devices.delete', 'Xóa thiết bị'), ('devices.export', 'Xuất danh sách thiết bị'),
('tickets.view', 'Xem phiếu'), ('tickets.create', 'Lập phiếu'), ('tickets.print', 'In phiếu'),
('maintenance.view', 'Xem bảo trì'), ('maintenance.create', 'Tạo phiếu bảo trì'), ('maintenance.update', 'Cập nhật bảo trì'),
('departments.view', 'Xem phòng ban'), ('departments.create', 'Thêm phòng ban'), ('departments.update', 'Sửa phòng ban'), ('departments.delete', 'Xóa phòng ban'),
('employees.view', 'Xem nhân viên'), ('employees.create', 'Thêm nhân viên'), ('employees.update', 'Sửa nhân viên'), ('employees.delete', 'Xóa nhân viên'), ('employees.revoke', 'Thu hồi thiết bị nhân viên'),
('reports.view', 'Xem báo cáo');
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id FROM `roles` r CROSS JOIN `permissions` p
WHERE r.code = 'warehouse' AND p.code <> 'devices.delete';

-- 2. Phòng ban mẫu
INSERT INTO `departments` (`id`, `code`, `name`, `description`) VALUES
(1, 'PB-IT', 'Phòng Công Nghệ Thông Tin', 'Quản lý hạ tầng mạng, máy chủ và thiết bị CNTT'),
(2, 'PB-KT', 'Phòng Kế Toán', 'Quản lý tài chính, thanh toán và chứng từ kế toán'),
(3, 'PB-HR', 'Phòng Hành Chính Nhân Sự', 'Quản lý nhân sự, tuyển dụng và văn phòng phẩm'),
(4, 'PB-KD', 'Phòng Kinh Doanh', 'Kinh doanh phát triển thị trường và khách hàng'),
(5, 'PB-MKT', 'Phòng Marketing', 'Truyền thông thương hiệu và tiếp thị đa kênh');

-- 3. Nhân viên mẫu
INSERT INTO `employees` (`id`, `code`, `fullname`, `email`, `phone`, `department_id`, `position`, `status`) VALUES
(1, 'NV-IT01', 'Trần Hữu Nam', 'nam.th@company.com', '0912345671', 1, 'Chuyên viên Quản trị Hệ thống', 'working'),
(2, 'NV-IT02', 'Lê Thị Thu', 'thu.lt@company.com', '0912345672', 1, 'Lập trình viên Fullstack', 'working'),
(3, 'NV-KT01', 'Phạm Minh Trang', 'trang.pm@company.com', '0912345673', 2, 'Kế toán tổng hợp', 'working'),
(4, 'NV-HR01', 'Vũ Hoàng Quân', 'quan.vh@company.com', '0912345674', 3, 'Chuyên viên Nhân sự', 'working'),
(5, 'NV-KD01', 'Đỗ Văn Thành', 'thanh.dv@company.com', '0912345675', 4, 'Trưởng phòng Kinh doanh', 'working'),
(6, 'NV-KD02', 'Nguyễn Mai Anh', 'anh.nm@company.com', '0912345676', 4, 'Nhân viên Kinh doanh', 'resigned');

-- 4. Thiết bị mẫu (PC và Laptop)
-- Thiết bị 1: Laptop Dell - Trong kho
-- Thiết bị 2: Laptop Thinkpad - Đang cấp phát
-- Thiết bị 3: PC Dell - Đang cấp phát
-- Thiết bị 4: PC Custom - Trong kho
-- Thiết bị 5: Laptop Asus - Đang bảo trì
-- Thiết bị 6: Laptop HP - Đang cấp phát tạm thời (Quá hạn)
-- Thiết bị 7: PC HP - Sắp hết hạn bảo hành (cảnh báo)
-- Thiết bị 8: Laptop Macbook - Đang cấp phát cho NV đã nghỉ việc (để test tính năng thu hồi khi nghỉ việc)
INSERT INTO `devices` (`id`, `device_code`, `type`, `brand`, `model`, `serial_number`, `import_date`, `warranty_expiry`, `condition_state`, `status`, `current_employee_id`, `current_department_id`, `is_temporary`, `expected_return_date`, `notes`) VALUES
(1, 'LT-DELL-001', 'Laptop', 'Dell', 'Latitude 5420', 'SN-DELL-98213', '2024-01-10', '2027-01-10', 'new', 'in_stock', NULL, NULL, 0, NULL, 'Máy mới 100% nguyên seal trong kho'),
(2, 'LT-LENOVO-002', 'Laptop', 'Lenovo', 'ThinkPad T14 Gen 2', 'SN-THINK-77412', '2023-06-15', '2026-06-15', 'used', 'allocated', 1, 1, 0, NULL, 'Cấp cho Nam IT làm việc'),
(3, 'PC-DELL-001', 'PC', 'Dell', 'Optiplex 7080 Tower', 'SN-OPT-55120', '2023-03-20', '2026-03-20', 'used', 'allocated', 3, 2, 0, NULL, 'Cấp cho phòng Kế toán'),
(4, 'PC-ASUS-002', 'PC', 'Asus', 'ExpertCenter D7 Tower', 'SN-ASUS-44211', '2024-02-01', '2027-02-01', 'new', 'in_stock', NULL, NULL, 0, NULL, 'Máy cây văn phòng cấu hình cao'),
(5, 'LT-ASUS-003', 'Laptop', 'Asus', 'ZenBook 14 UX425', 'SN-ASUS-11984', '2023-05-10', '2025-05-10', 'broken', 'maintenance', NULL, NULL, 0, NULL, 'Bị lỗi màn hình chập chờn, đang gửi hãng bảo dưỡng'),
(6, 'LT-HP-004', 'Laptop', 'HP', 'EliteBook 840 G8', 'SN-HP-33290', '2023-08-10', '2026-08-10', 'used', 'allocated', 5, 4, 1, '2024-03-01', 'Cấp tạm thời đi công tác (Đã quá hạn trả)'),
(7, 'PC-HP-003', 'PC', 'HP', 'ProDesk 400 G7', 'SN-HP-88771', '2023-11-01', '2026-11-05', 'used', 'in_stock', NULL, NULL, 0, NULL, 'Sắp hết hạn bảo hành trong vòng 30 ngày tới'),
(8, 'LT-APPLE-005', 'Laptop', 'Apple', 'MacBook Pro 14 M1 Pro', 'SN-MAC-66442', '2023-04-12', '2026-04-12', 'used', 'allocated', 6, 4, 0, NULL, 'Nguyễn Mai Anh đang giữ nhưng nhân viên này đã thôi việc');

-- 5. Cấu hình chi tiết thiết bị (DEVICE_SPECS)
INSERT INTO `device_specs` (`id`, `device_id`, `cpu`, `mainboard`, `ram_gb`, `ram_type`, `ssd_capacity`, `hdd_capacity`, `gpu`, `screen_size`, `notes`) VALUES
(1, 1, 'Intel Core i5-1135G7', NULL, 16, 'DDR4 3200MHz', '512GB NVMe M.2', 'Không có', 'Intel Iris Xe Graphics', '14.0 inch FHD IPS', 'Bàn phím có đèn LED, Pin 4 cell 63Wh'),
(2, 2, 'AMD Ryzen 7 PRO 5850U', NULL, 32, 'DDR4 3200MHz', '1TB NVMe M.2', 'Không có', 'AMD Radeon Graphics', '14.0 inch 400nits Low Power', 'Fingerprint, ThinkShutter webcam'),
(3, 3, 'Intel Core i7-10700', 'Dell Proprietary Q470', 16, 'DDR4 2933MHz', '256GB NVMe', '1TB 7200RPM', 'NVIDIA GeForce GT 730 2GB', NULL, 'Nguồn 260W 80 Plus Bronze'),
(4, 4, 'Intel Core i5-12400', 'ASUS B660M-Plus', 16, 'DDR4 3200MHz', '512GB PCIe Gen 4', 'Không có', 'Intel UHD Graphics 730', NULL, 'Vỏ case văn phòng thoáng mát'),
(5, 5, 'Intel Core i7-1165G7', NULL, 16, 'LPDDR4X 4266MHz', '512GB NVMe M.2', 'Không có', 'Intel Iris Xe Graphics', '14.0 inch FHD sRGB 100%', 'Vỏ nhôm nguyên khối siêu mỏng nhẹ'),
(6, 6, 'Intel Core i5-1145G7', NULL, 16, 'DDR4 3200MHz', '256GB NVMe M.2', 'Không có', 'Intel Iris Xe Graphics', '14.0 inch Anti-glare FHD', 'Chuẩn độ bền quân đội'),
(7, 7, 'Intel Core i3-10100', 'HP Intel H470', 8, 'DDR4 2666MHz', '256GB NVMe M.2', '500GB 7200RPM', 'Intel UHD Graphics 630', NULL, 'Dùng cho văn phòng cơ bản'),
(8, 8, 'Apple M1 Pro 8-Core CPU', NULL, 16, 'Unified Memory', '512GB SSD Apple Fast', 'Không có', '14-Core GPU', '14.2 inch Liquid Retina XDR', 'Củ sạc MagSafe 3 67W, cáp dù');

-- 6. Phiếu mẫu (TICKETS)
INSERT INTO `tickets` (`id`, `ticket_code`, `ticket_type`, `ticket_date`, `created_by`, `from_employee_id`, `from_department_id`, `to_employee_id`, `to_department_id`, `is_temporary`, `expected_return_date`, `status`, `reason`, `notes`) VALUES
(1, 'PX-20230616-001', 'allocation', '2023-06-16', 1, NULL, NULL, 1, 1, 0, NULL, 'completed', 'Cấp phát máy tính xách tay làm việc cho nhân viên IT mới', 'Thiết bị hoạt động tốt, đầy đủ tem niêm phong'),
(2, 'PX-20230321-002', 'allocation', '2023-03-21', 2, NULL, NULL, 3, 2, 0, NULL, 'completed', 'Cấp PC cho Kế toán xử lý phần mềm hóa đơn MISA', 'Máy hoạt động ổn định'),
(3, 'PX-20240215-003', 'allocation', '2024-02-15', 1, NULL, NULL, 5, 4, 1, '2024-03-01', 'completed', 'Cấp tạm thời phục vụ dự án công tác khách hàng miền Trung', 'Hẹn trả lại kho sau khi xong đợt công tác'),
(4, 'PX-20230415-004', 'allocation', '2023-04-15', 2, NULL, NULL, 6, 4, 0, NULL, 'completed', 'Cấp máy cho nhân viên kinh doanh', 'Cần thu hồi do nhân viên này đã nghỉ việc');

-- 7. Chi tiết phiếu mẫu (TICKET_DETAILS)
INSERT INTO `ticket_details` (`id`, `ticket_id`, `device_id`, `accessories`, `returned_condition`, `notes`) VALUES
(1, 1, 2, 'Chuột không dây Logitech, Củ sạc Type-C 65W, Balo chống sốc', NULL, 'Bàn giao đầy đủ phụ kiện theo máy'),
(2, 2, 3, 'Màn hình Dell 24 inch, Bàn phím Dell USB, Chuột quang, Dây nguồn, Cáp DisplayPort', NULL, 'Trọn bộ máy cây văn phòng'),
(3, 3, 6, 'Củ sạc HP 65W, Chuột có dây, Túi chống sốc', NULL, 'Cấp mượn tạm'),
(4, 4, 8, 'Củ sạc Apple 67W, Cáp MagSafe 3, Túi đựng laptop', NULL, 'Đang chờ thu hồi');

-- 8. Lịch sử mẫu (DEVICE_HISTORY)
INSERT INTO `device_history` (`id`, `device_id`, `ticket_id`, `action_type`, `action_date`, `performed_by`, `from_employee_id`, `to_employee_id`, `from_department_id`, `to_department_id`, `description`) VALUES
(1, 1, NULL, 'import', '2024-01-10 09:30:00', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới Laptop Dell Latitude 5420'),
(2, 2, NULL, 'import', '2023-06-15 14:00:00', 2, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới ThinkPad T14 Gen 2'),
(3, 2, 1, 'allocate', '2023-06-16 10:00:00', 1, NULL, 1, NULL, 1, 'Cấp phát cho Trần Hữu Nam (Phòng Công Nghệ Thông Tin) kèm phụ kiện: Chuột, Sạc, Balo'),
(4, 3, NULL, 'import', '2023-03-20 08:30:00', 1, NULL, NULL, NULL, NULL, 'Nhập kho máy bộ PC Dell Optiplex 7080'),
(5, 3, 2, 'allocate', '2023-03-21 09:15:00', 2, NULL, 3, NULL, 2, 'Cấp phát cho Phạm Minh Trang (Phòng Kế Toán) kèm Màn hình 24inch, phím chuột'),
(6, 5, NULL, 'maintenance_start', '2024-03-10 11:00:00', 1, NULL, NULL, NULL, NULL, 'Chuyển trạng thái sang Bảo trì: Lỗi màn hình chập chờn gửi trung tâm bảo hành Asus'),
(7, 6, 3, 'allocate', '2024-02-15 15:30:00', 1, NULL, 5, NULL, 4, 'Cấp phát tạm thời cho Đỗ Văn Thành (Kinh doanh) hạn trả 01/03/2024'),
(8, 8, 4, 'allocate', '2023-04-15 10:00:00', 2, NULL, 6, NULL, 4, 'Cấp phát thiết bị cho Nguyễn Mai Anh (Phòng Kinh Doanh)');

-- 9. Dữ liệu bảo trì mẫu (MAINTENANCES)
INSERT INTO `maintenances` (`id`, `device_id`, `maintenance_type`, `start_date`, `end_date`, `cost`, `provider`, `description`, `status`, `created_by`) VALUES
(1, 5, 'repair', '2024-03-10', NULL, 1200000.00, 'Trung tâm bảo hành Asus Việt Nam', 'Thay thế cáp màn hình và kiểm tra pin laptop', 'in_progress', 1),
(2, 3, 'upgrade', '2023-10-05', '2023-10-06', 850000.00, 'Công ty Tin học Phúc Anh', 'Nâng cấp thêm thanh RAM 8GB DDR4 (từ 8GB lên 16GB)', 'completed', 2);
