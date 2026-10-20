<?php
/**
 * File: core/Helper.php
 * Các hàm tiện ích hỗ trợ toàn hệ thống: bảo mật, định dạng, sinh URL, CSRF...
 */

/**
 * Chống tấn công XSS (Cross-Site Scripting)
 */
function e($value): string {
    if ($value === null) {
        return '';
    }
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

/**
 * Sinh đường dẫn nội bộ (URL) chuẩn xác
 */
function url(string $path = ''): string {
    $path = ltrim($path, '/');
    $base = BASE_PATH;
    return ($base === '' ? '' : $base) . '/' . $path;
}

/**
 * Đường dẫn tĩnh tới thư mục assets (CSS, JS, hình ảnh)
 */
function asset(string $path = ''): string {
    $path = ltrim($path, '/');
    return url('assets/' . $path);
}

/**
 * Chuyển hướng trang và kèm thông báo flash (nếu có)
 */
function redirect(string $path, ?string $flashMessage = null, string $type = 'success'): void {
    if ($flashMessage !== null) {
        setFlash($type, $flashMessage);
    }
    $targetUrl = rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
    header("Location: " . $targetUrl);
    exit;
}

/**
 * Thiết lập thông báo flash session
 */
function setFlash(string $type, string $message): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['flash'] = [
        'type' => $type, // success, danger, warning, info
        'message' => $message
    ];
}

/**
 * Lấy và xóa thông báo flash session sau khi hiển thị
 */
function getFlash(): ?array {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Định dạng ngày tháng d/m/Y
 */
function formatDate(?string $date, string $format = 'd/m/Y'): string {
    if (empty($date) || $date === '0000-00-00') {
        return '—';
    }
    return date($format, strtotime($date));
}

/**
 * Định dạng ngày giờ d/m/Y H:i
 */
function formatDateTime(?string $datetime, string $format = 'd/m/Y H:i'): string {
    if (empty($datetime) || $datetime === '0000-00-00 00:00:00') {
        return '—';
    }
    return date($format, strtotime($datetime));
}

/**
 * Định dạng tiền tệ VND
 */
function formatMoney($amount): string {
    return number_format((float)$amount, 0, ',', '.') . ' VNĐ';
}

/**
 * Hiển thị Badge trạng thái thiết bị
 */
function renderStatusBadge(string $status): string {
    switch ($status) {
        case 'in_stock':
            return '<span class="badge bg-success"><i class="bi bi-box-seam me-1"></i>Trong kho</span>';
        case 'allocated':
            return '<span class="badge bg-primary"><i class="bi bi-person-check me-1"></i>Đang cấp phát</span>';
        case 'maintenance':
            return '<span class="badge bg-warning text-dark"><i class="bi bi-tools me-1"></i>Bảo trì</span>';
        case 'liquidated':
            return '<span class="badge bg-secondary"><i class="bi bi-archive me-1"></i>Thanh lý</span>';
        default:
            return '<span class="badge bg-light text-dark">' . e($status) . '</span>';
    }
}

/**
 * Hiển thị Badge tình trạng thiết bị
 */
function renderConditionBadge(string $condition): string {
    switch ($condition) {
        case 'new':
            return '<span class="badge bg-info text-dark">Mới 100%</span>';
        case 'used':
            return '<span class="badge bg-light text-dark border">Đang dùng tốt</span>';
        case 'broken':
            return '<span class="badge bg-danger">Hỏng hóc</span>';
        case 'under_warranty':
            return '<span class="badge bg-warning text-dark">Đang bảo hành</span>';
        default:
            return '<span class="badge bg-secondary">' . e($condition) . '</span>';
    }
}

/**
 * Hiển thị Badge loại phiếu
 */
function renderTicketTypeBadge(string $type): string {
    switch ($type) {
        case 'allocation':
            return '<span class="badge bg-primary"><i class="bi bi-box-arrow-right me-1"></i>Cấp phát</span>';
        case 'transfer':
            return '<span class="badge bg-info text-dark"><i class="bi bi-arrow-left-right me-1"></i>Chuyển giao</span>';
        case 'revocation':
            return '<span class="badge bg-danger"><i class="bi bi-box-arrow-in-left me-1"></i>Thu hồi</span>';
        default:
            return '<span class="badge bg-secondary">' . e($type) . '</span>';
    }
}

/**
 * Hiển thị Badge trạng thái phiếu
 */
function renderTicketStatusBadge(string $status): string {
    switch ($status) {
        case 'draft':
            return '<span class="badge bg-secondary">Nháp</span>';
        case 'pending':
            return '<span class="badge bg-warning text-dark">Chờ duyệt</span>';
        case 'completed':
            return '<span class="badge bg-success">Hoàn thành</span>';
        case 'cancelled':
            return '<span class="badge bg-danger">Hủy bỏ</span>';
        default:
            return '<span class="badge bg-secondary">' . e($status) . '</span>';
    }
}

/**
 * Khởi tạo CSRF Token nếu chưa có
 */
function csrf_token(): string {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Trả về input ẩn CSRF cho form
 */
function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

/**
 * Kiểm tra tính hợp lệ của CSRF Token
 */
function verify_csrf(): bool {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $token = $_POST['csrf_token'] ?? '';
    return !empty($token) && hash_equals($_SESSION['csrf_token'] ?? '', $token);
}
