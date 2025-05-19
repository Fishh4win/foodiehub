<?php
namespace App\Models;

use App\Core\Model;

class Product extends Model {
    protected $table = 'products';
    
    /**
     * Get products by vendor ID
     */
    public function getByVendorId($vendorId) {
        return $this->findBy('vendor_id', $vendorId);
    }
    
    /**
     * Get products by category ID
     */
    public function getByCategoryId($categoryId) {
        return $this->findBy('category_id', $categoryId);
    }
    
    /**
     * Get available products
     */
    public function getAvailable() {
        return $this->findBy('is_available', true);
    }
    
    /**
     * Get available products by vendor
     */
    public function getAvailableByVendor($vendorId) {
        $sql = "SELECT * FROM {$this->table} WHERE vendor_id = ? AND is_available = 1";
        return $this->fetchAll($sql, [$vendorId]);
    }
    
    /**
     * Get product with vendor details
     */
    public function getWithVendorDetails($productId) {
        $sql = "SELECT p.*, v.business_name, v.logo, u.name as vendor_name 
                FROM products p 
                JOIN vendors v ON p.vendor_id = v.id 
                JOIN users u ON v.user_id = u.id 
                WHERE p.id = ?";
        
        return $this->fetch($sql, [$productId]);
    }
    
    /**
     * Get all products with vendor details
     */
    public function getAllWithVendorDetails() {
        $sql = "SELECT p.*, v.business_name, v.logo, u.name as vendor_name 
                FROM products p 
                JOIN vendors v ON p.vendor_id = v.id 
                JOIN users u ON v.user_id = u.id";
        
        return $this->fetchAll($sql);
    }
    
    /**
     * Search products by name or description
     */
    public function search($keyword) {
        $sql = "SELECT p.*, v.business_name, v.logo 
                FROM products p 
                JOIN vendors v ON p.vendor_id = v.id 
                WHERE p.name LIKE ? OR p.description LIKE ?";
        
        $param = "%{$keyword}%";
        return $this->fetchAll($sql, [$param, $param]);
    }
    
    /**
     * Filter products by multiple criteria
     */
    public function filter($filters) {
        $sql = "SELECT p.*, v.business_name, v.logo 
                FROM products p 
                JOIN vendors v ON p.vendor_id = v.id 
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['category_id'])) {
            $sql .= " AND p.category_id = ?";
            $params[] = $filters['category_id'];
        }
        
        if (!empty($filters['min_price'])) {
            $sql .= " AND p.price >= ?";
            $params[] = $filters['min_price'];
        }
        
        if (!empty($filters['max_price'])) {
            $sql .= " AND p.price <= ?";
            $params[] = $filters['max_price'];
        }
        
        if (!empty($filters['vendor_id'])) {
            $sql .= " AND p.vendor_id = ?";
            $params[] = $filters['vendor_id'];
        }
        
        if (isset($filters['is_available'])) {
            $sql .= " AND p.is_available = ?";
            $params[] = $filters['is_available'];
        }
        
        return $this->fetchAll($sql, $params);
    }
    
    /**
     * Get top-rated products
     */
    public function getTopRated($limit = 10) {
        $sql = "SELECT p.*, AVG(r.rating) as avg_rating, COUNT(r.id) as review_count 
                FROM products p 
                LEFT JOIN reviews r ON p.id = r.product_id 
                WHERE p.is_available = 1 
                GROUP BY p.id 
                HAVING review_count > 0 
                ORDER BY avg_rating DESC 
                LIMIT ?";
        
        return $this->fetchAll($sql, [$limit]);
    }
}
