<?php
/**
 * File: config/database.php
 * Quản lý kết nối cơ sở dữ liệu MySQL bằng PDO (Singleton Pattern)
 */

class Database {
    private static ?PDO $instance = null;

    /**
     * Khởi tạo kết nối PDO duy nhất
     * @return PDO
     */
    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Ném Exception khi có lỗi truy vấn
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Mặc định trả về mảng kết hợp
                PDO::ATTR_EMULATE_PREPARES   => false,                  // Sử dụng Prepared Statements thực sự
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
            ];

            try {
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
                self::$instance->exec("SET NAMES " . DB_CHARSET);
            } catch (PDOException $e) {
                // Hiển thị thông báo lỗi thân thiện khi chưa import database hoặc sai kết nối
                die('<div style="font-family: Arial; padding: 20px; background: #fff3cd; color: #856404; border: 1px solid #ffeeba; border-radius: 5px; margin: 20px;">
                    <h3>Lỗi kết nối Cơ sở dữ liệu:</h3>
                    <p>' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</p>
                    <p>Vui lòng kiểm tra lại cấu hình trong <code>config/config.php</code> và đảm bảo bạn đã import file <code>database.sql</code> vào MySQL trên XAMPP.</p>
                </div>');
            }
        }
        return self::$instance;
    }

    // Chặn khởi tạo đối tượng từ bên ngoài
    private function __construct() {}
    private function __clone() {}
}
