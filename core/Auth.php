<?php
/**
 * File: core/Auth.php
 * Quản lý xác thực người dùng và phân quyền (RBAC)
 */

class Auth {
    /**
     * Khởi tạo session nếu chưa có
     */
    private static function initSession(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Kiểm tra người dùng đã đăng nhập chưa
     */
    public static function check(): bool {
        self::initSession();
        return !empty($_SESSION['user_id']);
    }

    /**
     * Lấy ID người dùng đang đăng nhập
     */
    public static function id(): ?int {
        self::initSession();
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Lấy thông tin user hiện tại
     */
    public static function user(): ?array {
        self::initSession();
        return $_SESSION['user'] ?? null;
    }

    /**
     * Lấy role của user hiện tại ('admin' hoặc 'warehouse')
     */
    public static function role(): ?string {
        self::initSession();
        return $_SESSION['user']['role'] ?? null;
    }

    /**
     * Kiểm tra có phải Admin không
     */
    public static function isAdmin(): bool {
        return self::role() === 'admin';
    }

    /**
     * Kiểm tra có phải Nhân viên kho không
     */
    public static function isWarehouse(): bool {
        return self::role() === 'warehouse';
    }

    /**
     * Thiết lập session đăng nhập
     */
    public static function login(array $user): void {
        self::initSession();
        session_regenerate_id(true); // Chống Session Fixation
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['user'] = [
            'id' => (int)$user['id'],
            'username' => $user['username'],
            'fullname' => $user['fullname'],
            'email' => $user['email'] ?? '',
            'role' => $user['role']
        ];
    }

    /**
     * Đăng xuất hệ thống
     */
    public static function logout(): void {
        self::initSession();
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }

    /**
     * Bắt buộc phải đăng nhập mới được truy cập
     */
    public static function requireLogin(): void {
        if (!self::check()) {
            redirect('login', 'Vui lòng đăng nhập để tiếp tục truy cập!', 'warning');
        }
    }

    /**
     * Bắt buộc phải là quyền Admin
     */
    public static function requireAdmin(): void {
        self::requireLogin();
        if (!self::isAdmin()) {
            redirect('dashboard', 'Bạn không có quyền thực hiện thao tác này (Chỉ dành cho Admin)!', 'danger');
        }
    }
}
