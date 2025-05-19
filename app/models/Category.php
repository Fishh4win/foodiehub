<?php
namespace App\Models;

use App\Core\Model;

class Category extends Model {
    protected $table = 'categories';
    
    /**
     * Get category by name
     */
    public function getByName($name) {
        return $this->findOneBy('name', $name);
    }
    
    /**
     * Get categories with product counts
     */
    public function getAllWithProductCounts() {
        $sql = "SELECT c.*, COUNT(p.id) as product_count 
                FROM categories c 
                LEFT JOIN products p ON c.id = p.category_id 
                GROUP BY c.id";
        
        return $this->fetchAll($sql);
    }
    
    /**
     * Get categories with available product counts
     */
    public function getAllWithAvailableProductCounts() {
        $sql = "SELECT c.*, COUNT(p.id) as product_count 
                FROM categories c 
                LEFT JOIN products p ON c.id = p.category_id AND p.is_available = 1 
                GROUP BY c.id";
        
        return $this->fetchAll($sql);
    }
}
