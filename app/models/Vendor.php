<?php
namespace App\Models;

use App\Core\Model;

class Vendor extends Model {
    protected $table = 'vendors';
    
    /**
     * Create a new vendor
     */
    public function createVendor($userId, $data) {
        $vendorData = [
            'user_id' => $userId,
            'business_name' => $data['business_name'],
            'description' => $data['description'] ?? null,
            'location' => $data['location'] ?? null,
            'logo' => $data['logo'] ?? null,
            'banner' => $data['banner'] ?? null,
            'is_approved' => false,
            'is_featured' => false
        ];
        
        return $this->create($vendorData);
    }
    
    /**
     * Get vendor by user ID
     */
    public function getByUserId($userId) {
        return $this->findOneBy('user_id', $userId);
    }
    
    /**
     * Approve a vendor
     */
    public function approve($vendorId) {
        return $this->update($vendorId, ['is_approved' => true]);
    }
    
    /**
     * Disapprove a vendor
     */
    public function disapprove($vendorId) {
        return $this->update($vendorId, ['is_approved' => false]);
    }
    
    /**
     * Feature a vendor
     */
    public function feature($vendorId) {
        return $this->update($vendorId, ['is_featured' => true]);
    }
    
    /**
     * Unfeature a vendor
     */
    public function unfeature($vendorId) {
        return $this->update($vendorId, ['is_featured' => false]);
    }
    
    /**
     * Get vendor with user details
     */
    public function getWithUserDetails($vendorId) {
        $sql = "SELECT v.*, u.name, u.email, u.phone, u.address, u.profile_image 
                FROM vendors v 
                JOIN users u ON v.user_id = u.id 
                WHERE v.id = ?";
        
        return $this->fetch($sql, [$vendorId]);
    }
    
    /**
     * Get all vendors with user details
     */
    public function getAllWithUserDetails() {
        $sql = "SELECT v.*, u.name, u.email, u.phone 
                FROM vendors v 
                JOIN users u ON v.user_id = u.id";
        
        return $this->fetchAll($sql);
    }
    
    /**
     * Get approved vendors with user details
     */
    public function getApprovedWithUserDetails() {
        $sql = "SELECT v.*, u.name, u.email, u.phone 
                FROM vendors v 
                JOIN users u ON v.user_id = u.id 
                WHERE v.is_approved = 1";
        
        return $this->fetchAll($sql);
    }
    
    /**
     * Get featured vendors with user details
     */
    public function getFeaturedWithUserDetails() {
        $sql = "SELECT v.*, u.name, u.email, u.phone 
                FROM vendors v 
                JOIN users u ON v.user_id = u.id 
                WHERE v.is_featured = 1";
        
        return $this->fetchAll($sql);
    }
}
