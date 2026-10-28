<?php
/**
 * File: core/Router.php
 * Bộ định tuyến URL thân thiện (Friendly URL Router)
 */

class Router {
    private array $routes = [];

    /**
     * Đăng ký route GET
     */
    public function get(string $uri, string $action): void {
        $this->addRoute('GET', $uri, $action);
    }

    /**
     * Đăng ký route POST
     */
    public function post(string $uri, string $action): void {
        $this->addRoute('POST', $uri, $action);
    }

    /**
     * Thêm route vào danh sách
     */
    private function addRoute(string $method, string $uri, string $action): void {
        $permission = $this->permissionForRoute($action);
        $this->routes[] = [
            'method' => strtoupper($method),
            'uri'    => trim($uri, '/'),
            'action' => $action,
            'permission' => $permission
        ];
    }

    private function permissionForRoute(string $action): ?string {
        [$controller, $method] = explode('@', $action);
        if ($controller === 'AuthController') {
            return null;
        }

        if ($controller === 'UserController') {
            return 'users.manage';
        }
        if ($controller === 'RoleController') {
            return 'roles.manage';
        }

        $modules = [
            'DashboardController' => 'dashboard',
            'ReportController' => 'reports',
            'DeviceController' => 'devices',
            'DepartmentController' => 'departments',
            'EmployeeController' => 'employees',
            'TicketController' => 'tickets',
            'MaintenanceController' => 'maintenance'
        ];
        $module = $modules[$controller] ?? null;
        if ($module === null) {
            return null;
        }

        $operation = match ($method) {
            'create', 'store' => 'create',
            'edit', 'update', 'updateStatus' => 'update',
            'delete' => 'delete',
            'exportCsv' => 'export',
            'printTicket' => 'print',
            'bulkRevoke' => 'revoke',
            default => 'view'
        };

        return $module . '.' . $operation;
    }

    /**
     * Phân tích URL và chuyển tiếp request đến đúng Controller
     */
    public function dispatch(): void {
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';

        // Tách query string (ví dụ ?page=2&type=PC)
        $uriParts = explode('?', $requestUri, 2);
        $path = $uriParts[0];

        // Loại bỏ BASE_PATH nếu ứng dụng đặt trong thư mục con (ví dụ /warehouse_management/public)
        $base = BASE_PATH;
        if ($base !== '' && strpos($path, $base) === 0) {
            $path = substr($path, strlen($base));
        }

        // Loại bỏ index.php nếu có
        $path = preg_replace('#^/index\.php#', '', $path);
        $path = trim($path, '/');

        // Tìm route khớp
        foreach ($this->routes as $route) {
            if ($route['method'] !== $requestMethod) {
                continue;
            }

            // Chuyển đổi định dạng route {id} sang Regex pattern
            $pattern = preg_replace('#\{([a-zA-Z0-9_]+)\}#', '([^/]+)', $route['uri']);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $path, $matches)) {
                array_shift($matches); // Loại bỏ phần tử match toàn bộ chuỗi

                if ($route['permission'] !== null) {
                    Auth::requirePermission($route['permission']);
                }

                [$controllerName, $methodName] = explode('@', $route['action']);

                $controllerFile = __DIR__ . '/../app/controllers/' . $controllerName . '.php';
                if (!file_exists($controllerFile)) {
                    die("Không tìm thấy Controller: " . htmlspecialchars($controllerName, ENT_QUOTES, 'UTF-8'));
                }

                require_once $controllerFile;

                if (!class_exists($controllerName)) {
                    die("Class $controllerName không tồn tại!");
                }

                $controller = new $controllerName();

                if (!method_exists($controller, $methodName)) {
                    die("Method $methodName không tồn tại trong $controllerName!");
                }

                // Gọi action và truyền tham số (nếu có)
                call_user_func_array([$controller, $methodName], $matches);
                return;
            }
        }

        // Nếu không tìm thấy route khớp -> 404
        $this->handleNotFound();
    }

    /**
     * Trang lỗi 404 thân thiện
     */
    private function handleNotFound(): void {
        http_response_code(404);
        echo '<!DOCTYPE html>
        <html lang="vi">
        <head>
            <meta charset="UTF-8">
            <title>404 - Không tìm thấy trang</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
        </head>
        <body class="bg-light d-flex align-items-center justify-content-center" style="min-height: 100vh;">
            <div class="text-center p-5 bg-white rounded-3 shadow-sm" style="max-width: 500px;">
                <i class="bi bi-exclamation-triangle-fill text-warning display-1"></i>
                <h1 class="mt-3 text-danger fw-bold">404</h1>
                <h4 class="mb-3">Không tìm thấy trang yêu cầu</h4>
                <p class="text-muted">Đường dẫn bạn truy cập không tồn tại hoặc đã được chuyển đi nơi khác.</p>
                <a href="' . url('dashboard') . '" class="btn btn-primary mt-2"><i class="bi bi-house-door me-1"></i> Trở về Trang chủ</a>
            </div>
        </body>
        </html>';
        exit;
    }
}
