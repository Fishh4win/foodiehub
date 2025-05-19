<?php
namespace App\Core;

use App\Models\User;

/**
 * Auth Class
 * 
 * Handles user authentication and authorization
 */
class Auth {
    /**
     * Attempt to authenticate a user
     */
    public static function attempt($email, $password) {
        $userModel = new User();
        $user = $userModel->authenticate($email, $password);
        
        if ($user) {
            // Remove password from session
            unset($user['password']);
            
            // Set user in session
            Session::setUser($user);
            Session::regenerate();
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Check if user is logged in
     */
    public static function check() {
        return Session::isLoggedIn();
    }
    
    /**
     * Get the authenticated user
     */
    public static function user() {
        return Session::getUser();
    }
    
    /**
     * Get the authenticated user's ID
     */
    public static function id() {
        $user = self::user();
        return $user ? $user['id'] : null;
    }
    
    /**
     * Check if the authenticated user has a specific role
     */
    public static function hasRole($role) {
        return Session::hasRole($role);
    }
    
    /**
     * Logout the authenticated user
     */
    public static function logout() {
        Session::logout();
    }
    
    /**
     * Require authentication
     * 
     * Redirects to login page if not authenticated
     */
    public static function requireAuth() {
        if (!self::check()) {
            Session::setFlash('error', 'Please login to access this page');
            header('Location: /login');
            exit;
        }
    }
    
    /**
     * Require a specific role
     * 
     * Redirects to home page if not authorized
     */
    public static function requireRole($role) {
        self::requireAuth();
        
        if (!self::hasRole($role)) {
            Session::setFlash('error', 'You are not authorized to access this page');
            header('Location: /');
            exit;
        }
    }
}
