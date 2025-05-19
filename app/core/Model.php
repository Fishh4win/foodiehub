<?php
namespace App\Core;

/**
 * Base Model Class
 *
 * All models will extend this class to inherit database functionality
 */
abstract class Model {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';

    /**
     * Constructor
     */
    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Find a record by ID
     */
    public function find($id) {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ? LIMIT 1";
        return $this->db->fetch($sql, [$id]);
    }

    /**
     * Get all records
     */
    public function all() {
        $sql = "SELECT * FROM {$this->table}";
        return $this->db->fetchAll($sql);
    }

    /**
     * Find records by a specific field or conditions
     *
     * @param string|array $field Field name or associative array of conditions
     * @param mixed $value Value to match (ignored if $field is an array)
     * @return array The found records
     */
    public function findBy($field, $value = null) {
        if (is_array($field)) {
            // Handle associative array of conditions
            $conditions = [];
            $values = [];

            foreach ($field as $key => $val) {
                $conditions[] = "{$key} = ?";
                $values[] = $val;
            }

            $whereClause = implode(' AND ', $conditions);
            $sql = "SELECT * FROM {$this->table} WHERE {$whereClause}";
            return $this->db->fetchAll($sql, $values);
        } else {
            // Handle single field condition
            $sql = "SELECT * FROM {$this->table} WHERE {$field} = ?";
            return $this->db->fetchAll($sql, [$value]);
        }
    }

    /**
     * Find one record by a specific field or conditions
     *
     * @param string|array $field Field name or associative array of conditions
     * @param mixed $value Value to match (ignored if $field is an array)
     * @return array|null The found record or null
     */
    public function findOneBy($field, $value = null) {
        if (is_array($field)) {
            // Handle associative array of conditions
            $conditions = [];
            $values = [];

            foreach ($field as $key => $val) {
                $conditions[] = "{$key} = ?";
                $values[] = $val;
            }

            $whereClause = implode(' AND ', $conditions);
            $sql = "SELECT * FROM {$this->table} WHERE {$whereClause} LIMIT 1";
            return $this->db->fetch($sql, $values);
        } else {
            // Handle single field condition
            $sql = "SELECT * FROM {$this->table} WHERE {$field} = ? LIMIT 1";
            return $this->db->fetch($sql, [$value]);
        }
    }

    /**
     * Create a new record
     */
    public function create($data) {
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update a record
     */
    public function update($id, $data) {
        return $this->db->update(
            $this->table,
            $data,
            "{$this->primaryKey} = ?",
            [$id]
        );
    }

    /**
     * Delete a record
     */
    public function delete($id) {
        return $this->db->delete(
            $this->table,
            "{$this->primaryKey} = ?",
            [$id]
        );
    }

    /**
     * Count records
     */
    public function count() {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        $result = $this->db->fetch($sql);
        return $result['count'] ?? 0;
    }

    /**
     * Custom query
     */
    public function query($sql, $params = []) {
        return $this->db->query($sql, $params);
    }

    /**
     * Fetch all with custom query
     */
    public function fetchAll($sql, $params = []) {
        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Fetch one with custom query
     */
    public function fetch($sql, $params = []) {
        return $this->db->fetch($sql, $params);
    }
}
