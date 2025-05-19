<?php
namespace App\Models;

use App\Core\Model;

class User extends Model {
    protected $table = 'users';
    
    /**
     * Register a new user
     */
    public function register($data) {
        // Hash the password
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        
        // Create the user
        return $this->create($data);
    }
    
    /**
     * Authenticate a user
     */
    public function authenticate($email, $password) {
        $user = $this->findOneBy('email', $email);
        
        if (!$user) {
            return false;
        }
        
        if (password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }
    
    /**
     * Get user by email
     */
    public function getByEmail($email) {
        return $this->findOneBy('email', $email);
    }
    
    /**
     * Check if email exists
     */
    public function emailExists($email) {
        $user = $this->getByEmail($email);
        return $user ? true : false;
    }
    
    /**
     * Get users by role
     */
    public function getByRole($role) {
        return $this->findBy('role', $role);
    }
    
    /**
     * Get user with vendor details
     */
    public function getVendorWithDetails($userId) {
        $sql = "SELECT u.*, v.* 
                FROM users u 
                JOIN vendors v ON u.id = v.user_id 
                WHERE u.id = ?";
        
        return $this->fetch($sql, [$userId]);
    }
    
    /**
     * Get all vendors with their details
     */
    public function getAllVendors() {
        $sql = "SELECT u.*, v.* 
                FROM users u 
                JOIN vendors v ON u.id = v.user_id 
                WHERE u.role = 'vendor'";
        
        return $this->fetchAll($sql);
    }
    
    /**
     * Get approved vendors
     */
    public function getApprovedVendors() {
        $sql = "SELECT u.*, v.* 
                FROM users u 
                JOIN vendors v ON u.id = v.user_id 
                WHERE u.role = 'vendor' AND v.is_approved = 1";
        
        return $this->fetchAll($sql);
    }
    
    /**
     * Get featured vendors
     */
    public function getFeaturedVendors() {
        $sql = "SELECT u.*, v.* 
                FROM users u 
                JOIN vendors v ON u.id = v.user_id 
                WHERE u.role = 'vendor' AND v.is_featured = 1";
        
        return $this->fetchAll($sql);
    }
}
