<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Auth;
use App\Core\Session;
use App\Models\User;
use App\Models\Vendor;

class AuthController {
    private $view;
    
    public function __construct() {
        $this->view = new View();
    }
    
    /**
     * Show login form
     */
    public function showLogin() {
        $this->view->render('auth.login', [
            'title' => 'Login'
        ]);
    }
    
    /**
     * Process login
     */
    public function login() {
        // Validate input
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            Session::setFlash('error', 'Please enter both email and password');
            header('Location: /login');
            exit;
        }
        
        // Attempt to authenticate
        if (Auth::attempt($email, $password)) {
            $user = Auth::user();
            
            Session::setFlash('success', 'Welcome back, ' . $user['name']);
            
            // Redirect based on role
            if ($user['role'] === 'admin') {
                header('Location: /admin/dashboard');
            } elseif ($user['role'] === 'vendor') {
                header('Location: /vendor/dashboard');
            } else {
                header('Location: /');
            }
            exit;
        } else {
            Session::setFlash('error', 'Invalid email or password');
            header('Location: /login');
            exit;
        }
    }
    
    /**
     * Show registration form
     */
    public function showRegister() {
        $this->view->render('auth.register', [
            'title' => 'Register'
        ]);
    }
    
    /**
     * Process customer registration
     */
    public function registerCustomer() {
        // Validate input
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        if (empty($name) || empty($email) || empty($password)) {
            Session::setFlash('error', 'Please fill in all required fields');
            header('Location: /register');
            exit;
        }
        
        if ($password !== $confirmPassword) {
            Session::setFlash('error', 'Passwords do not match');
            header('Location: /register');
            exit;
        }
        
        // Check if email already exists
        $userModel = new User();
        if ($userModel->emailExists($email)) {
            Session::setFlash('error', 'Email already exists');
            header('Location: /register');
            exit;
        }
        
        // Register user
        $userId = $userModel->register([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'role' => 'customer'
        ]);
        
        if ($userId) {
            Session::setFlash('success', 'Registration successful. Please login.');
            header('Location: /login');
            exit;
        } else {
            Session::setFlash('error', 'Registration failed');
            header('Location: /register');
            exit;
        }
    }
    
    /**
     * Show vendor registration form
     */
    public function showVendorRegister() {
        $this->view->render('auth.vendor_register', [
            'title' => 'Become a Vendor'
        ]);
    }
    
    /**
     * Process vendor registration
     */
    public function registerVendor() {
        // Validate input
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $businessName = $_POST['business_name'] ?? '';
        $location = $_POST['location'] ?? '';
        $description = $_POST['description'] ?? '';
        
        if (empty($name) || empty($email) || empty($password) || empty($businessName) || empty($location)) {
            Session::setFlash('error', 'Please fill in all required fields');
            header('Location: /vendor/register');
            exit;
        }
        
        if ($password !== $confirmPassword) {
            Session::setFlash('error', 'Passwords do not match');
            header('Location: /vendor/register');
            exit;
        }
        
        // Check if email already exists
        $userModel = new User();
        if ($userModel->emailExists($email)) {
            Session::setFlash('error', 'Email already exists');
            header('Location: /vendor/register');
            exit;
        }
        
        // Start transaction
        $db = \App\Core\Database::getInstance();
        $db->query('START TRANSACTION');
        
        try {
            // Register user
            $userId = $userModel->register([
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'role' => 'vendor'
            ]);
            
            // Create vendor profile
            $vendorModel = new Vendor();
            $vendorModel->createVendor($userId, [
                'business_name' => $businessName,
                'location' => $location,
                'description' => $description
            ]);
            
            // Commit transaction
            $db->query('COMMIT');
            
            Session::setFlash('success', 'Registration successful. Your vendor account is pending approval.');
            header('Location: /login');
            exit;
        } catch (\Exception $e) {
            // Rollback transaction on error
            $db->query('ROLLBACK');
            
            Session::setFlash('error', 'Registration failed: ' . $e->getMessage());
            header('Location: /vendor/register');
            exit;
        }
    }
    
    /**
     * Logout user
     */
    public function logout() {
        Auth::logout();
        Session::setFlash('success', 'You have been logged out');
        header('Location: /');
        exit;
    }
}
