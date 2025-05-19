<?php
namespace App\Core;

/**
 * Helpers Class
 * 
 * A collection of static helper methods for common tasks
 */
class Helpers {
    /**
     * Format a price with currency symbol
     * 
     * @param float $price The price to format
     * @param string $currency The currency symbol (default: $)
     * @param int $decimals The number of decimal places (default: 2)
     * @return string Formatted price
     */
    public static function formatPrice($price, $currency = '$', $decimals = 2) {
        return $currency . number_format($price, $decimals);
    }
    
    /**
     * Format a date
     * 
     * @param string $date The date string
     * @param string $format The format (default: 'M d, Y')
     * @return string Formatted date
     */
    public static function formatDate($date, $format = 'M d, Y') {
        return date($format, strtotime($date));
    }
    
    /**
     * Format a datetime
     * 
     * @param string $datetime The datetime string
     * @param string $format The format (default: 'M d, Y h:i A')
     * @return string Formatted datetime
     */
    public static function formatDateTime($datetime, $format = 'M d, Y h:i A') {
        return date($format, strtotime($datetime));
    }
    
    /**
     * Limit a string to a certain number of characters
     * 
     * @param string $string The string to limit
     * @param int $limit The character limit
     * @param string $append String to append if truncated (default: '...')
     * @return string Limited string
     */
    public static function limitString($string, $limit, $append = '...') {
        if (strlen($string) <= $limit) {
            return $string;
        }
        
        return substr($string, 0, $limit) . $append;
    }
    
    /**
     * Convert a string to slug format
     * 
     * @param string $string The string to convert
     * @return string Slug
     */
    public static function slugify($string) {
        // Replace non-alphanumeric characters with hyphens
        $slug = preg_replace('/[^a-z0-9]+/i', '-', strtolower(trim($string)));
        // Remove duplicate hyphens
        $slug = preg_replace('/-+/', '-', $slug);
        // Remove leading/trailing hyphens
        return trim($slug, '-');
    }
    
    /**
     * Generate a random string
     * 
     * @param int $length The length of the string (default: 10)
     * @param string $characters The characters to use (default: alphanumeric)
     * @return string Random string
     */
    public static function randomString($length = 10, $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') {
        $charactersLength = strlen($characters);
        $randomString = '';
        
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        
        return $randomString;
    }
    
    /**
     * Check if a string starts with a specific substring
     * 
     * @param string $haystack The string to check
     * @param string $needle The substring to search for
     * @return bool True if the string starts with the substring
     */
    public static function startsWith($haystack, $needle) {
        return substr($haystack, 0, strlen($needle)) === $needle;
    }
    
    /**
     * Check if a string ends with a specific substring
     * 
     * @param string $haystack The string to check
     * @param string $needle The substring to search for
     * @return bool True if the string ends with the substring
     */
    public static function endsWith($haystack, $needle) {
        return substr($haystack, -strlen($needle)) === $needle;
    }
    
    /**
     * Get the current URL
     * 
     * @param bool $withQueryString Include query string (default: true)
     * @return string Current URL
     */
    public static function currentUrl($withQueryString = true) {
        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        
        if (!$withQueryString) {
            $url = strtok($url, '?');
        }
        
        return $url;
    }
    
    /**
     * Get the base URL
     * 
     * @return string Base URL
     */
    public static function baseUrl() {
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
    }
    
    /**
     * Check if the current request is AJAX
     * 
     * @return bool True if the request is AJAX
     */
    public static function isAjax() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
    
    /**
     * Get the client's IP address
     * 
     * @return string IP address
     */
    public static function getIpAddress() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        
        return $ip;
    }
    
    /**
     * Sanitize user input
     * 
     * @param string $input The input to sanitize
     * @return string Sanitized input
     */
    public static function sanitize($input) {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Generate a CSRF token
     * 
     * @return string CSRF token
     */
    public static function generateCsrfToken() {
        if (!Session::has('csrf_token')) {
            Session::set('csrf_token', bin2hex(random_bytes(32)));
        }
        
        return Session::get('csrf_token');
    }
    
    /**
     * Verify CSRF token
     * 
     * @param string $token The token to verify
     * @return bool True if the token is valid
     */
    public static function verifyCsrfToken($token) {
        return Session::has('csrf_token') && hash_equals(Session::get('csrf_token'), $token);
    }
    
    /**
     * Redirect to a URL
     * 
     * @param string $url The URL to redirect to
     * @param int $statusCode The HTTP status code (default: 302)
     */
    public static function redirect($url, $statusCode = 302) {
        header('Location: ' . $url, true, $statusCode);
        exit;
    }
    
    /**
     * Get file extension
     * 
     * @param string $filename The filename
     * @return string File extension
     */
    public static function getFileExtension($filename) {
        return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    }
    
    /**
     * Check if file is an image
     * 
     * @param string $filename The filename
     * @return bool True if the file is an image
     */
    public static function isImage($filename) {
        $ext = self::getFileExtension($filename);
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
        
        return in_array($ext, $imageExtensions);
    }
    
    /**
     * Format file size
     * 
     * @param int $bytes The file size in bytes
     * @param int $precision The number of decimal places (default: 2)
     * @return string Formatted file size
     */
    public static function formatFileSize($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
    
    /**
     * Get time ago string
     * 
     * @param string $datetime The datetime string
     * @return string Time ago string
     */
    public static function timeAgo($datetime) {
        $time = strtotime($datetime);
        $now = time();
        $diff = $now - $time;
        
        if ($diff < 60) {
            return 'Just now';
        } elseif ($diff < 3600) {
            $minutes = floor($diff / 60);
            return $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
        } elseif ($diff < 604800) {
            $days = floor($diff / 86400);
            return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
        } elseif ($diff < 2592000) {
            $weeks = floor($diff / 604800);
            return $weeks . ' week' . ($weeks > 1 ? 's' : '') . ' ago';
        } elseif ($diff < 31536000) {
            $months = floor($diff / 2592000);
            return $months . ' month' . ($months > 1 ? 's' : '') . ' ago';
        } else {
            $years = floor($diff / 31536000);
            return $years . ' year' . ($years > 1 ? 's' : '') . ' ago';
        }
    }
}
