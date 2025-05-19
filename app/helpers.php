<?php
/**
 * Global helper functions for the application
 */

use App\Core\Helpers;
use App\Core\Session;
use App\Core\Auth;

/**
 * Format a price with currency symbol
 *
 * @param float $price The price to format
 * @param string $currency The currency symbol (default: $)
 * @param int $decimals The number of decimal places (default: 2)
 * @return string Formatted price
 */
function format_price($price, $currency = '₱', $decimals = 2) {
    return Helpers::formatPrice($price, $currency, $decimals);
}

/**
 * Format a date
 *
 * @param string $date The date string
 * @param string $format The format (default: 'M d, Y')
 * @return string Formatted date
 */
function format_date($date, $format = 'M d, Y') {
    return Helpers::formatDate($date, $format);
}

/**
 * Format a datetime
 *
 * @param string $datetime The datetime string
 * @param string $format The format (default: 'M d, Y h:i A')
 * @return string Formatted datetime
 */
function format_datetime($datetime, $format = 'M d, Y h:i A') {
    return Helpers::formatDateTime($datetime, $format);
}

/**
 * Limit a string to a certain number of characters
 *
 * @param string $string The string to limit
 * @param int $limit The character limit
 * @param string $append String to append if truncated (default: '...')
 * @return string Limited string
 */
function limit_string($string, $limit, $append = '...') {
    return Helpers::limitString($string, $limit, $append);
}

/**
 * Convert a string to slug format
 *
 * @param string $string The string to convert
 * @return string Slug
 */
function slugify($string) {
    return Helpers::slugify($string);
}

/**
 * Get the current URL
 *
 * @param bool $withQueryString Include query string (default: true)
 * @return string Current URL
 */
function current_url($withQueryString = true) {
    return Helpers::currentUrl($withQueryString);
}

/**
 * Get the base URL
 *
 * @return string Base URL
 */
function base_url() {
    return Helpers::baseUrl();
}

/**
 * Sanitize user input
 *
 * @param string $input The input to sanitize
 * @return string Sanitized input
 */
function sanitize($input) {
    return Helpers::sanitize($input);
}

/**
 * Generate a CSRF token
 *
 * @return string CSRF token
 */
function csrf_token() {
    return Helpers::generateCsrfToken();
}

/**
 * Generate a CSRF token field
 *
 * @return string CSRF token field HTML
 */
function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

/**
 * Check if the current request is AJAX
 *
 * @return bool True if the request is AJAX
 */
function is_ajax() {
    return Helpers::isAjax();
}

/**
 * Get time ago string
 *
 * @param string $datetime The datetime string
 * @return string Time ago string
 */
function time_ago($datetime) {
    return Helpers::timeAgo($datetime);
}

/**
 * Check if user is logged in
 *
 * @return bool True if user is logged in
 */
function is_logged_in() {
    return Auth::check();
}

/**
 * Get the authenticated user
 *
 * @return array|null User data
 */
function current_user() {
    return Auth::user();
}

/**
 * Check if the authenticated user has a specific role
 *
 * @param string $role Role to check
 * @return bool True if user has the role
 */
function has_role($role) {
    return Auth::hasRole($role);
}

/**
 * Get a flash message
 *
 * @param string $key Flash message key
 * @param mixed $default Default value if not found
 * @return mixed Flash message
 */
function flash($key, $default = null) {
    return Session::getFlash($key, $default);
}

/**
 * Check if a flash message exists
 *
 * @param string $key Flash message key
 * @return bool True if flash message exists
 */
function has_flash($key) {
    return Session::hasFlash($key);
}

/**
 * Get a session variable
 *
 * @param string $key Session key
 * @param mixed $default Default value if not found
 * @return mixed Session value
 */
function session($key, $default = null) {
    return Session::get($key, $default);
}

/**
 * Check if a session variable exists
 *
 * @param string $key Session key
 * @return bool True if session variable exists
 */
function has_session($key) {
    return Session::has($key);
}

/**
 * Get the current request method
 *
 * @return string Request method
 */
function request_method() {
    return $_SERVER['REQUEST_METHOD'];
}

/**
 * Check if the current request method is POST
 *
 * @return bool True if request method is POST
 */
function is_post() {
    return request_method() === 'POST';
}

/**
 * Check if the current request method is GET
 *
 * @return bool True if request method is GET
 */
function is_get() {
    return request_method() === 'GET';
}

/**
 * Get a request variable
 *
 * @param string $key Request key
 * @param mixed $default Default value if not found
 * @return mixed Request value
 */
function request($key, $default = null) {
    if (isset($_POST[$key])) {
        return $_POST[$key];
    }

    if (isset($_GET[$key])) {
        return $_GET[$key];
    }

    return $default;
}

/**
 * Check if a request variable exists
 *
 * @param string $key Request key
 * @return bool True if request variable exists
 */
function has_request($key) {
    return isset($_POST[$key]) || isset($_GET[$key]);
}

/**
 * Redirect to a URL
 *
 * @param string $url The URL to redirect to
 * @param int $statusCode The HTTP status code (default: 302)
 */
function redirect($url, $statusCode = 302) {
    Helpers::redirect($url, $statusCode);
}

/**
 * Get the client's IP address
 *
 * @return string IP address
 */
function get_ip_address() {
    return Helpers::getIpAddress();
}

/**
 * Format file size
 *
 * @param int $bytes The file size in bytes
 * @param int $precision The number of decimal places (default: 2)
 * @return string Formatted file size
 */
function format_file_size($bytes, $precision = 2) {
    return Helpers::formatFileSize($bytes, $precision);
}

/**
 * Check if the current URL matches a pattern
 *
 * @param string $pattern URL pattern
 * @return bool True if URL matches pattern
 */
function url_is($pattern) {
    $currentUrl = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    // Convert pattern to regex
    $pattern = str_replace('*', '.*', $pattern);
    $pattern = '#^' . $pattern . '$#';

    return preg_match($pattern, $currentUrl);
}

/**
 * Get the active class if the current URL matches a pattern
 *
 * @param string $pattern URL pattern
 * @param string $class Active class (default: 'active')
 * @return string Active class or empty string
 */
function active_class($pattern, $class = 'active') {
    return url_is($pattern) ? $class : '';
}

/**
 * Get the asset URL
 *
 * @param string $path Asset path
 * @return string Asset URL
 */
function asset($path) {
    return base_url() . '/' . ltrim($path, '/');
}

/**
 * Get the public path
 *
 * @param string $path Path relative to public directory
 * @return string Full path
 */
function public_path($path = '') {
    return __DIR__ . '/../public/' . ltrim($path, '/');
}

/**
 * Get the storage path
 *
 * @param string $path Path relative to storage directory
 * @return string Full path
 */
function storage_path($path = '') {
    return __DIR__ . '/../storage/' . ltrim($path, '/');
}

/**
 * Get the app path
 *
 * @param string $path Path relative to app directory
 * @return string Full path
 */
function app_path($path = '') {
    return __DIR__ . '/' . ltrim($path, '/');
}

/**
 * Get the root path
 *
 * @param string $path Path relative to root directory
 * @return string Full path
 */
function root_path($path = '') {
    return __DIR__ . '/../' . ltrim($path, '/');
}

/**
 * Generate a random string
 *
 * @param int $length The length of the string (default: 10)
 * @param string $characters The characters to use (default: alphanumeric)
 * @return string Random string
 */
function random_string($length = 10, $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') {
    return Helpers::randomString($length, $characters);
}

/**
 * Check if a string starts with a specific substring
 *
 * @param string $haystack The string to check
 * @param string $needle The substring to search for
 * @return bool True if the string starts with the substring
 */
function starts_with($haystack, $needle) {
    return Helpers::startsWith($haystack, $needle);
}

/**
 * Check if a string ends with a specific substring
 *
 * @param string $haystack The string to check
 * @param string $needle The substring to search for
 * @return bool True if the string ends with the substring
 */
function ends_with($haystack, $needle) {
    return Helpers::endsWith($haystack, $needle);
}

/**
 * Check if the current URL path matches the given path
 *
 * @param string $path The path to check
 * @return bool True if the current URL path matches
 */
function is_current_url($path) {
    $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    return $currentPath === $path;
}
