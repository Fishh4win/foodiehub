<?php
namespace App\Models;

use App\Core\Model;

class Review extends Model {
    protected $table = 'reviews';
    
    /**
     * Get reviews by product ID
     */
    public function getByProductId($productId) {
        return $this->findBy('product_id', $productId);
    }
    
    /**
     * Get reviews by user ID
     */
    public function getByUserId($userId) {
        return $this->findBy('user_id', $userId);
    }
    
    /**
     * Get reviews with user details
     */
    public function getWithUserDetails($productId) {
        $sql = "SELECT r.*, u.name as user_name, u.profile_image 
                FROM reviews r 
                JOIN users u ON r.user_id = u.id 
                WHERE r.product_id = ? 
                ORDER BY r.created_at DESC";
        
        return $this->fetchAll($sql, [$productId]);
    }
    
    /**
     * Get average rating for a product
     */
    public function getAverageRating($productId) {
        $sql = "SELECT AVG(rating) as avg_rating, COUNT(*) as review_count 
                FROM reviews 
                WHERE product_id = ?";
        
        return $this->fetch($sql, [$productId]);
    }
    
    /**
     * Check if user has already reviewed a product
     */
    public function hasUserReviewed($userId, $productId) {
        $sql = "SELECT COUNT(*) as count 
                FROM reviews 
                WHERE user_id = ? AND product_id = ?";
        
        $result = $this->fetch($sql, [$userId, $productId]);
        return $result['count'] > 0;
    }
}
