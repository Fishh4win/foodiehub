<?php
namespace App\Models;

use App\Core\Model;

class Order extends Model {
    protected $table = 'orders';
    
    /**
     * Create a new order with items
     */
    public function createWithItems($orderData, $items) {
        // Start transaction
        $this->db->query('START TRANSACTION');
        
        try {
            // Create order
            $orderId = $this->create($orderData);
            
            // Create order items
            $orderItemModel = new OrderItem();
            foreach ($items as $item) {
                $item['order_id'] = $orderId;
                $orderItemModel->create($item);
            }
            
            // Commit transaction
            $this->db->query('COMMIT');
            
            return $orderId;
        } catch (\Exception $e) {
            // Rollback transaction on error
            $this->db->query('ROLLBACK');
            throw $e;
        }
    }
    
    /**
     * Get orders by customer ID
     */
    public function getByCustomerId($customerId) {
        return $this->findBy('customer_id', $customerId);
    }
    
    /**
     * Get orders by vendor ID
     */
    public function getByVendorId($vendorId) {
        return $this->findBy('vendor_id', $vendorId);
    }
    
    /**
     * Get orders by status
     */
    public function getByStatus($status) {
        return $this->findBy('status', $status);
    }
    
    /**
     * Get order with items
     */
    public function getWithItems($orderId) {
        // Get order
        $order = $this->find($orderId);
        
        if (!$order) {
            return null;
        }
        
        // Get order items
        $orderItemModel = new OrderItem();
        $items = $orderItemModel->getByOrderId($orderId);
        
        $order['items'] = $items;
        
        return $order;
    }
    
    /**
     * Get order with customer and vendor details
     */
    public function getWithDetails($orderId) {
        $sql = "SELECT o.*, 
                c.name as customer_name, c.email as customer_email, 
                v.business_name as vendor_business_name 
                FROM orders o 
                JOIN users c ON o.customer_id = c.id 
                JOIN vendors v ON o.vendor_id = v.id 
                WHERE o.id = ?";
        
        $order = $this->fetch($sql, [$orderId]);
        
        if (!$order) {
            return null;
        }
        
        // Get order items with product details
        $sql = "SELECT oi.*, p.name as product_name, p.image as product_image 
                FROM order_items oi 
                JOIN products p ON oi.product_id = p.id 
                WHERE oi.order_id = ?";
        
        $items = $this->fetchAll($sql, [$orderId]);
        
        $order['items'] = $items;
        
        return $order;
    }
    
    /**
     * Update order status
     */
    public function updateStatus($orderId, $status) {
        return $this->update($orderId, ['status' => $status]);
    }
    
    /**
     * Get recent orders
     */
    public function getRecent($limit = 10) {
        $sql = "SELECT o.*, 
                c.name as customer_name, 
                v.business_name as vendor_business_name 
                FROM orders o 
                JOIN users c ON o.customer_id = c.id 
                JOIN vendors v ON o.vendor_id = v.id 
                ORDER BY o.created_at DESC 
                LIMIT ?";
        
        return $this->fetchAll($sql, [$limit]);
    }
    
    /**
     * Get sales statistics for a vendor
     */
    public function getVendorStats($vendorId) {
        $sql = "SELECT 
                COUNT(*) as total_orders,
                SUM(total_price) as total_sales,
                COUNT(CASE WHEN status = 'delivered' THEN 1 END) as completed_orders,
                COUNT(CASE WHEN status = 'cancelled' THEN 1 END) as cancelled_orders
                FROM orders 
                WHERE vendor_id = ?";
        
        return $this->fetch($sql, [$vendorId]);
    }
}
