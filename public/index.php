<?php
/**
 * File: public/index.php
 * Front Controller: Tiếp nhận tất cả các request, khởi tạo cấu hình và điều hướng Router
 */

// Bắt đầu session nếu chưa có
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Thiết lập mã hóa UTF-8 cho HTTP Header để hiển thị chuẩn tiếng Việt
header('Content-Type: text/html; charset=UTF-8');

// Nạp các file cấu hình và lớp nền tảng
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/Helper.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Model.php';
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Router.php';

// Khởi tạo bộ định tuyến
$router = new Router();

// ==========================================
// 1. ROUTES XÁC THỰC & TÀI KHOẢN
// ==========================================
$router->get('', 'DashboardController@index');
$router->get('login', 'AuthController@login');
$router->post('login', 'AuthController@handleLogin');
$router->get('logout', 'AuthController@logout');
$router->get('profile', 'AuthController@profile');
$router->post('profile/password', 'AuthController@updatePassword');

// ==========================================
// 2. ROUTES BẢNG ĐIỀU KHIỂN & BÁO CÁO
// ==========================================
$router->get('dashboard', 'DashboardController@index');
$router->get('reports', 'ReportController@index');

// ==========================================
// 3. ROUTES QUẢN LÝ THIẾT BỊ
// ==========================================
$router->get('devices', 'DeviceController@index');
$router->get('devices/create', 'DeviceController@create');
$router->post('devices/store', 'DeviceController@store');
$router->get('devices/export', 'DeviceController@exportCsv');
$router->get('devices/{id}', 'DeviceController@show');
$router->get('devices/{id}/edit', 'DeviceController@edit');
$router->post('devices/{id}/update', 'DeviceController@update');
$router->post('devices/{id}/delete', 'DeviceController@delete');

// ==========================================
// 4. ROUTES PHÒNG BAN
// ==========================================
$router->get('departments', 'DepartmentController@index');
$router->post('departments/store', 'DepartmentController@store');
$router->post('departments/{id}/update', 'DepartmentController@update');
$router->post('departments/{id}/delete', 'DepartmentController@delete');

// ==========================================
// 5. ROUTES NHÂN VIÊN & THU HỒI KHI NGHỈ VIỆC
// ==========================================
$router->get('employees', 'EmployeeController@index');
$router->post('employees/store', 'EmployeeController@store');
$router->get('employees/{id}', 'EmployeeController@show');
$router->post('employees/{id}/update', 'EmployeeController@update');
$router->post('employees/{id}/delete', 'EmployeeController@delete');
$router->post('employees/{id}/bulk-revoke', 'EmployeeController@bulkRevoke');

// ==========================================
// 6. ROUTES CẤP PHÁT / CHUYỂN GIAO / THU HỒI
// ==========================================
$router->get('tickets', 'TicketController@index');
$router->get('tickets/create', 'TicketController@create');
$router->post('tickets/store', 'TicketController@store');
$router->get('tickets/{id}', 'TicketController@show');
$router->get('tickets/{id}/print', 'TicketController@printTicket');

// ==========================================
// 7. ROUTES BẢO TRÌ & NÂNG CẤP LINH KIỆN
// ==========================================
$router->get('maintenance', 'MaintenanceController@index');
$router->get('maintenance/create', 'MaintenanceController@create');
$router->post('maintenance/store', 'MaintenanceController@store');
$router->post('maintenance/{id}/status', 'MaintenanceController@updateStatus');

// ==========================================
// 8. ROUTES QUẢN LÝ TÀI KHOẢN (ADMIN ONLY)
// ==========================================
$router->get('users', 'UserController@index');
$router->post('users/store', 'UserController@store');
$router->post('users/{id}/update', 'UserController@update');
$router->post('users/{id}/delete', 'UserController@delete');

// Tiến hành điều phối request
$router->dispatch();
