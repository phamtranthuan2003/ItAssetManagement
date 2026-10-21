<?php
/**
 * File: core/Controller.php
 * Lớp Controller cơ sở hỗ trợ render view, layout, kiểm tra dữ liệu đầu vào và CSRF
 */

abstract class Controller {
    /**
     * Render một view bên trong layout
     * @param string $view Đường dẫn tương đối từ app/views (ví dụ 'devices/index')
     * @param array $data Dữ liệu truyền vào view
     * @param string|null $layout Tên layout trong app/views/layouts/ (mặc định 'main', null nếu không dùng layout)
     */
    protected function render(string $view, array $data = [], ?string $layout = 'main'): void {
        // Trích xuất biến từ mảng dữ liệu để view có thể sử dụng trực tiếp
        extract($data);

        $viewPath = __DIR__ . '/../app/views/' . $view . '.php';

        if (!file_exists($viewPath)) {
            die("Không tìm thấy view: " . htmlspecialchars($viewPath, ENT_QUOTES, 'UTF-8'));
        }

        // Bắt đầu vùng đệm bộ nhớ để lưu nội dung view
        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        // Nếu có layout thì nhúng view vào layout
        if ($layout !== null) {
            $layoutPath = __DIR__ . '/../app/views/layouts/' . $layout . '.php';
            if (file_exists($layoutPath)) {
                require $layoutPath;
            } else {
                echo $content;
            }
        } else {
            echo $content;
        }
    }

    /**
     * Xuất dữ liệu dưới dạng JSON (cho các API/AJAX call)
     */
    protected function json(array $data, int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Kiểm tra phương thức request có phải là POST
     */
    protected function isPost(): bool {
        return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
    }

    /**
     * Kiểm tra phương thức request có phải là GET
     */
    protected function isGet(): bool {
        return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET';
    }

    /**
     * Lấy giá trị từ $_POST có làm sạch khoảng trắng
     */
    protected function post(string $key, $default = null) {
        if (!isset($_POST[$key])) {
            return $default;
        }
        $val = $_POST[$key];
        return is_string($val) ? trim($val) : $val;
    }

    /**
     * Lấy giá trị từ $_GET có làm sạch khoảng trắng
     */
    protected function get(string $key, $default = null) {
        if (!isset($_GET[$key])) {
            return $default;
        }
        $val = $_GET[$key];
        return is_string($val) ? trim($val) : $val;
    }

    protected function returnTo(string $default, string $expectedPath): string {
        $candidate = $this->post('return_to', $this->get('return_to', ''));
        if (!is_string($candidate) || preg_match('/[\r\n]/', $candidate)) {
            return $default;
        }

        $parts = parse_url($candidate);
        if ($parts === false || isset($parts['scheme']) || isset($parts['host']) ||
            isset($parts['user']) || isset($parts['pass']) ||
            trim($parts['path'] ?? '', '/') !== trim($expectedPath, '/')) {
            return $default;
        }

        return $expectedPath . (!empty($parts['query']) ? '?' . $parts['query'] : '');
    }

    /**
     * Kiểm tra CSRF Token trong form POST
     */
    protected function validateCsrf(): void {
        if (!verify_csrf()) {
            redirect('dashboard', 'Lỗi bảo mật CSRF: Phiên làm việc đã hết hạn hoặc form không hợp lệ!', 'danger');
        }
    }
}
