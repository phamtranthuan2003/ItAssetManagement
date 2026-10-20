<?php
/**
 * File: core/Model.php
 * Lớp Model cơ sở, cung cấp các phương thức truy vấn CSDL an toàn qua PDO
 */

require_once __DIR__ . '/../config/database.php';

abstract class Model {
    protected PDO $db;
    protected string $table = '';

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Thực thi câu lệnh SQL với Prepared Statement
     */
    protected function query(string $sql, array $params = []): PDOStatement {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Lấy một dòng kết quả
     */
    protected function fetch(string $sql, array $params = []): ?array {
        $stmt = $this->query($sql, $params);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Lấy danh sách kết quả
     */
    protected function fetchAll(string $sql, array $params = []): array {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    /**
     * Đếm số bản ghi
     */
    protected function count(string $sql, array $params = []): int {
        $stmt = $this->query($sql, $params);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Lấy ID vừa chèn vào CSDL
     */
    public function lastInsertId(): int {
        return (int)$this->db->lastInsertId();
    }

    /**
     * Bắt đầu một Database Transaction
     */
    public function beginTransaction(): bool {
        return $this->db->beginTransaction();
    }

    /**
     * Cam kết hoàn tất Transaction
     */
    public function commit(): bool {
        return $this->db->commit();
    }

    /**
     * Rollback hủy bỏ Transaction khi có lỗi
     */
    public function rollBack(): bool {
        return $this->db->rollBack();
    }
}
