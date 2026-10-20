<?php
/**
 * File: config/config.php
 * Cấu hình hệ thống và kết nối cơ sở dữ liệu
 */

// Thiết lập múi giờ Việt Nam
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Cấu hình Database
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'warehouse_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Tên ứng dụng
define('APP_NAME', 'Hệ Thống Quản Lý Thiết Bị Kho (PC/Laptop)');
define('APP_VERSION', '1.0.0');

// Tự động nhận diện Base URL và Base Path để chạy mượt mà trên mọi môi trường XAMPP
$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$scriptDir = str_replace('\\', '/', dirname($scriptName));
$basePath = rtrim($scriptDir, '/\\');

// Nếu ở thư mục gốc (root) thì base path rỗng, nếu ở thư mục con thì lấy đúng đường dẫn con
define('BASE_PATH', ($basePath === '' || $basePath === '/' || $basePath === '\\') ? '' : $basePath);

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || ($_SERVER['SERVER_PORT'] ?? 80) == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
define('BASE_URL', rtrim($protocol . $host . BASE_PATH, '/'));
