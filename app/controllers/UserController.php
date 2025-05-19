<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Auth;
use App\Core\Session;
use App\Models\User;
use App\Models\Order;

class UserController {
    private $view;
    private $userModel;
    
    public function __construct() {
        $this->view = new View();
        $this->userModel = new User();
        Session::start();
    }
    
    /**
     * Show user profile
     */
    public function profile() {
        // Check if user is logged in
        Auth::requireAuth();
        
        // Get user data
        $user = Auth::user();
        
        // Get user's orders
        $orderModel = new Order();
        $orders = $orderModel->getByCustomerId($user['id']);
        
        $this->view->render('user.profile', [
            'title' => 'My Profile',
            'user' => $user,
            'orders' => $orders
        ]);
    }
    
    /**
     * Update user profile
     */
    public function updateProfile() {
        // Check if user is logged in
        Auth::requireAuth();
        
        // Get user data
        $user = Auth::user();
        
        // Get form data
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $address = $_POST['address'] ?? '';
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        if (empty($name) || empty($email)) {
            Session::setFlash('error', 'Name and email are required');
            header('Location: /profile');
            exit;
        }
        
        // Check if email already exists for another user
        $existingUser = $this->userModel->getByEmail($email);
        if ($existingUser && $existingUser['id'] != $user['id']) {
            Session::setFlash('error', 'Email already exists');
            header('Location: /profile');
            exit;
        }
        
        // Update user data
        $userData = [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'address' => $address
        ];
        
        // Handle password change if requested
        if (!empty($currentPassword) && !empty($newPassword)) {
            // Verify current password
            if (!password_verify($currentPassword, $user['password'])) {
                Session::setFlash('error', 'Current password is incorrect');
                header('Location: /profile');
                exit;
            }
            
            // Verify password confirmation
            if ($newPassword !== $confirmPassword) {
                Session::setFlash('error', 'New passwords do not match');
                header('Location: /profile');
                exit;
            }
            
            // Update password
            $userData['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }
        
        // Handle profile image upload
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/profiles/';
            
            // Create directory if it doesn't exist
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $fileName = time() . '_' . basename($_FILES['profile_image']['name']);
            $uploadFile = $uploadDir . $fileName;
            
            // Check if file is an image
            $imageInfo = getimagesize($_FILES['profile_image']['tmp_name']);
            if ($imageInfo === false) {
                Session::setFlash('error', 'Uploaded file is not an image');
                header('Location: /profile');
                exit;
            }
            
            // Move uploaded file
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $uploadFile)) {
                $userData['profile_image'] = $uploadFile;
            } else {
                Session::setFlash('error', 'Failed to upload profile image');
                header('Location: /profile');
                exit;
            }
        }
        
        // Update user
        $result = $this->userModel->update($user['id'], $userData);
        
        if ($result) {
            // Update session user data
            $updatedUser = $this->userModel->find($user['id']);
            unset($updatedUser['password']); // Remove password from session
            Session::setUser($updatedUser);
            
            Session::setFlash('success', 'Profile updated successfully');
        } else {
            Session::setFlash('error', 'Failed to update profile');
        }
        
        header('Location: /profile');
        exit;
    }
}
