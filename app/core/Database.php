<?php
namespace App\Core;

use PDO;

class Database {
    protected $pdo;
    private static $instance = null;

    /**
     * Database constructor - private to prevent direct instantiation
     */
    private function __construct() {
        $config = require_once __DIR__ . '/../config/database.php';

        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";

        try {
            $this->pdo = new PDO($dsn, $config['username'], $config['password'], $config['options']);
        } catch (\PDOException $e) {
            throw new \Exception("Database connection failed: " . $e->getMessage());
        }
    }

    /**
     * Get database instance (Singleton pattern)
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Execute a query with parameters
     */
    public function query($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Fetch a single record
     */
    public function fetch($sql, $params = []) {
        return $this->query($sql, $params)->fetch();
    }

    /**
     * Fetch all records
     */
    public function fetchAll($sql, $params = []) {
        return $this->query($sql, $params)->fetchAll();
    }

    /**
     * Insert a record and return the ID
     */
    public function insert($table, $data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";

        $this->query($sql, array_values($data));
        return $this->pdo->lastInsertId();
    }

    /**
     * Update records
     */
    public function update($table, $data, $where, $whereParams = []) {
        $setParts = [];
        foreach (array_keys($data) as $column) {
            $setParts[] = "{$column} = ?";
        }

        $setClause = implode(', ', $setParts);
        $sql = "UPDATE {$table} SET {$setClause} WHERE {$where}";

        $params = array_merge(array_values($data), $whereParams);
        return $this->query($sql, $params)->rowCount();
    }

    /**
     * Delete records
     */
    public function delete($table, $where, $params = []) {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        return $this->query($sql, $params)->rowCount();
    }
}
