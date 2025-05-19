<?php
namespace App\Core;

/**
 * Session Class
 * 
 * Handles user sessions and flash messages
 */
class Session {
    /**
     * Start the session
     */
    public static function start() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Set a session variable
     */
    public static function set($key, $value) {
        self::start();
        $_SESSION[$key] = $value;
    }
    
    /**
     * Get a session variable
     */
    public static function get($key, $default = null) {
        self::start();
        return $_SESSION[$key] ?? $default;
    }
    
    /**
     * Check if a session variable exists
     */
    public static function has($key) {
        self::start();
        return isset($_SESSION[$key]);
    }
    
    /**
     * Remove a session variable
     */
    public static function remove($key) {
        self::start();
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }
    
    /**
     * Clear all session variables
     */
    public static function clear() {
        self::start();
        session_unset();
    }
    
    /**
     * Destroy the session
     */
    public static function destroy() {
        self::start();
        session_unset();
        session_destroy();
    }
    
    /**
     * Set a flash message
     */
    public static function setFlash($key, $message) {
        self::start();
        $_SESSION['flash'][$key] = $message;
    }
    
    /**
     * Get a flash message and remove it
     */
    public static function getFlash($key, $default = null) {
        self::start();
        $message = $_SESSION['flash'][$key] ?? $default;
        if (isset($_SESSION['flash'][$key])) {
            unset($_SESSION['flash'][$key]);
        }
        return $message;
    }
    
    /**
     * Check if a flash message exists
     */
    public static function hasFlash($key) {
        self::start();
        return isset($_SESSION['flash'][$key]);
    }
    
    /**
     * Set user data in session
     */
    public static function setUser($user) {
        self::set('user', $user);
        self::set('isLoggedIn', true);
    }
    
    /**
     * Get logged in user
     */
    public static function getUser() {
        return self::get('user');
    }
    
    /**
     * Check if user is logged in
     */
    public static function isLoggedIn() {
        return self::get('isLoggedIn', false);
    }
    
    /**
     * Check if user has a specific role
     */
    public static function hasRole($role) {
        $user = self::getUser();
        return $user && $user['role'] === $role;
    }
    
    /**
     * Logout user
     */
    public static function logout() {
        self::remove('user');
        self::remove('isLoggedIn');
    }
    
    /**
     * Regenerate session ID
     */
    public static function regenerate() {
        self::start();
        session_regenerate_id(true);
    }
}
