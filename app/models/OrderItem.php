<?php
namespace App\Models;

use App\Core\Model;

class OrderItem extends Model {
    protected $table = 'order_items';
    
    /**
     * Get items by order ID
     */
    public function getByOrderId($orderId) {
        return $this->findBy('order_id', $orderId);
    }
    
    /**
     * Get items with product details
     */
    public function getWithProductDetails($orderId) {
        $sql = "SELECT oi.*, p.name, p.image 
                FROM order_items oi 
                JOIN products p ON oi.product_id = p.id 
                WHERE oi.order_id = ?";
        
        return $this->fetchAll($sql, [$orderId]);
    }
}
