<?php
namespace App\Core;

/**
 * Notification Class
 * 
 * Handles in-app notifications
 */
class Notification {
    /**
     * @var string Notifications table name
     */
    private $table = 'notifications';
    
    /**
     * @var Database Database instance
     */
    private $db;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Create a notification
     * 
     * @param int $userId User ID
     * @param string $type Notification type
     * @param string $message Notification message
     * @param array $data Additional data (optional)
     * @param string $link Notification link (optional)
     * @return int Notification ID
     */
    public function create($userId, $type, $message, $data = [], $link = '') {
        $notification = [
            'user_id' => $userId,
            'type' => $type,
            'message' => $message,
            'data' => json_encode($data),
            'link' => $link,
            'is_read' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->db->insert($this->table, $notification);
    }
    
    /**
     * Get notifications for a user
     * 
     * @param int $userId User ID
     * @param int $limit Limit (default: 10)
     * @param int $offset Offset (default: 0)
     * @param bool $unreadOnly Get only unread notifications (default: false)
     * @return array Notifications
     */
    public function getForUser($userId, $limit = 10, $offset = 0, $unreadOnly = false) {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = ?";
        
        if ($unreadOnly) {
            $sql .= " AND is_read = 0";
        }
        
        $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
        
        return $this->db->fetchAll($sql, [$userId, $limit, $offset]);
    }
    
    /**
     * Get notification by ID
     * 
     * @param int $id Notification ID
     * @return array|null Notification
     */
    public function getById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ? LIMIT 1";
        return $this->db->fetch($sql, [$id]);
    }
    
    /**
     * Mark notification as read
     * 
     * @param int $id Notification ID
     * @return bool True if successful
     */
    public function markAsRead($id) {
        return $this->db->update($this->table, ['is_read' => 1], 'id = ?', [$id]);
    }
    
    /**
     * Mark all notifications as read for a user
     * 
     * @param int $userId User ID
     * @return bool True if successful
     */
    public function markAllAsRead($userId) {
        return $this->db->update($this->table, ['is_read' => 1], 'user_id = ? AND is_read = 0', [$userId]);
    }
    
    /**
     * Delete notification
     * 
     * @param int $id Notification ID
     * @return bool True if successful
     */
    public function delete($id) {
        return $this->db->delete($this->table, 'id = ?', [$id]);
    }
    
    /**
     * Delete all notifications for a user
     * 
     * @param int $userId User ID
     * @return bool True if successful
     */
    public function deleteAllForUser($userId) {
        return $this->db->delete($this->table, 'user_id = ?', [$userId]);
    }
    
    /**
     * Count unread notifications for a user
     * 
     * @param int $userId User ID
     * @return int Number of unread notifications
     */
    public function countUnread($userId) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE user_id = ? AND is_read = 0";
        $result = $this->db->fetch($sql, [$userId]);
        return $result['count'] ?? 0;
    }
    
    /**
     * Create a notification for order status change
     * 
     * @param int $orderId Order ID
     * @param string $status New status
     * @return int Notification ID
     */
    public function createOrderStatusNotification($orderId, $status) {
        // Get order details
        $sql = "SELECT o.*, c.id as customer_id, v.id as vendor_id, v.business_name 
                FROM orders o 
                JOIN users c ON o.customer_id = c.id 
                JOIN vendors v ON o.vendor_id = v.id 
                WHERE o.id = ?";
        
        $order = $this->db->fetch($sql, [$orderId]);
        
        if (!$order) {
            return false;
        }
        
        // Create notification for customer
        $message = '';
        $type = 'order_status';
        $link = "/orders/{$orderId}";
        
        switch ($status) {
            case 'pending':
                $message = "Your order #{$orderId} has been placed and is pending.";
                break;
            case 'preparing':
                $message = "Your order #{$orderId} is now being prepared by {$order['business_name']}.";
                break;
            case 'out_for_delivery':
                $message = "Your order #{$orderId} is out for delivery.";
                break;
            case 'delivered':
                $message = "Your order #{$orderId} has been delivered. Enjoy your meal!";
                break;
            case 'cancelled':
                $message = "Your order #{$orderId} has been cancelled.";
                break;
        }
        
        $data = [
            'order_id' => $orderId,
            'status' => $status
        ];
        
        return $this->create($order['customer_id'], $type, $message, $data, $link);
    }
    
    /**
     * Create a notification for new order
     * 
     * @param int $orderId Order ID
     * @return int Notification ID
     */
    public function createNewOrderNotification($orderId) {
        // Get order details
        $sql = "SELECT o.*, c.name as customer_name, v.user_id as vendor_user_id 
                FROM orders o 
                JOIN users c ON o.customer_id = c.id 
                JOIN vendors v ON o.vendor_id = v.id 
                WHERE o.id = ?";
        
        $order = $this->db->fetch($sql, [$orderId]);
        
        if (!$order) {
            return false;
        }
        
        // Create notification for vendor
        $message = "New order #{$orderId} received from {$order['customer_name']}.";
        $type = 'new_order';
        $link = "/vendor/orders/{$orderId}";
        
        $data = [
            'order_id' => $orderId,
            'customer_name' => $order['customer_name'],
            'total_price' => $order['total_price']
        ];
        
        return $this->create($order['vendor_user_id'], $type, $message, $data, $link);
    }
    
    /**
     * Create a notification for new review
     * 
     * @param int $reviewId Review ID
     * @return int Notification ID
     */
    public function createNewReviewNotification($reviewId) {
        // Get review details
        $sql = "SELECT r.*, u.name as user_name, p.name as product_name, p.vendor_id, v.user_id as vendor_user_id 
                FROM reviews r 
                JOIN users u ON r.user_id = u.id 
                JOIN products p ON r.product_id = p.id 
                JOIN vendors v ON p.vendor_id = v.id 
                WHERE r.id = ?";
        
        $review = $this->db->fetch($sql, [$reviewId]);
        
        if (!$review) {
            return false;
        }
        
        // Create notification for vendor
        $message = "{$review['user_name']} left a {$review['rating']}-star review on {$review['product_name']}.";
        $type = 'new_review';
        $link = "/products/{$review['product_id']}";
        
        $data = [
            'review_id' => $reviewId,
            'product_id' => $review['product_id'],
            'rating' => $review['rating'],
            'user_name' => $review['user_name']
        ];
        
        return $this->create($review['vendor_user_id'], $type, $message, $data, $link);
    }
}
