<?php
/**
 * File: core/Auth.php
 * Quản lý xác thực người dùng và phân quyền (RBAC)
 */

class Auth {
    private static ?array $currentUser = null;
    private static ?array $permissionCache = null;

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
     * Lấy thông tin user hiện tại (Tự động đồng bộ với CSDL)
     */
    public static function user(): ?array {
        self::initSession();
        if (empty($_SESSION['user_id'])) {
            return null;
        }

        if (self::$currentUser !== null) {
            return self::$currentUser;
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id, username, fullname, email, role, status FROM users WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => (int)$_SESSION['user_id']]);
        $freshUser = $stmt->fetch();
        if (!$freshUser || (int)$freshUser['status'] !== 1) {
            self::logout();
            return null;
        }

        self::$currentUser = [
            'id'       => (int)$freshUser['id'],
            'username' => $freshUser['username'],
            'fullname' => $freshUser['fullname'],
            'email'    => $freshUser['email'] ?? '',
            'role'     => $freshUser['role']
        ];
        $_SESSION['user'] = self::$currentUser;
        return self::$currentUser;
    }

    /**
     * Lấy role của user hiện tại ('admin' hoặc 'warehouse')
     */
    public static function role(): ?string {
        return self::user()['role'] ?? null;
    }

    /**
     * Kiểm tra có phải Admin không
     */
    public static function isAdmin(): bool {
        return self::id() === 6;
    }

    public static function hasPermission(string $permission): bool {
        if (self::isAdmin()) {
            return true;
        }

        $user = self::user();
        if (!$user) {
            return false;
        }

        if (self::$permissionCache === null) {
            $db = Database::getConnection();
            $stmt = $db->prepare(
                'SELECT p.code
                 FROM role_permissions rp
                 INNER JOIN permissions p ON p.id = rp.permission_id
                 INNER JOIN roles r ON r.id = rp.role_id
                 WHERE r.code = :role'
            );
            $stmt->execute(['role' => $user['role']]);
            self::$permissionCache = $stmt->fetchAll(PDO::FETCH_COLUMN);
        }

        return in_array($permission, self::$permissionCache, true);
    }

    public static function requirePermission(string $permission): void {
        self::requireLogin();
        if (!self::hasPermission($permission)) {
            http_response_code(403);
            echo '<!DOCTYPE html><html lang="vi"><head><meta charset="UTF-8"><title>403 - Không đủ quyền</title></head>'
                . '<body><h1>403 - Không đủ quyền truy cập</h1><p>Tài khoản của bạn chưa được cấp quyền thực hiện thao tác này.</p></body></html>';
            exit;
        }
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
        self::$currentUser = null;
        self::$permissionCache = null;
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
        self::$currentUser = null;
        self::$permissionCache = null;
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
        if (!self::check() || !self::user()) {
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

    public static function landingPath(): string {
        foreach ([
            'dashboard.view' => 'dashboard',
            'devices.view' => 'devices',
            'tickets.view' => 'tickets',
            'maintenance.view' => 'maintenance',
            'departments.view' => 'departments',
            'employees.view' => 'employees',
            'reports.view' => 'reports'
        ] as $permission => $path) {
            if (self::hasPermission($permission)) {
                return $path;
            }
        }
        return 'profile';
    }
}
