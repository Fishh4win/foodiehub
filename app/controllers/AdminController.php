<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Session;
use App\Core\Auth;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Product;
use App\Models\Order;
use App\Models\Category;

class AdminController {
    private $view;

    public function __construct() {
        $this->view = new View();
        Session::start();
    }

    /**
     * Show admin dashboard
     */
    public function dashboard() {
        // Check if user is an admin
        Auth::requireRole('admin');

        // Get stats
        $userModel = new User();
        $customerCount = count($userModel->getByRole('customer'));
        $vendorCount = count($userModel->getByRole('vendor'));

        $productModel = new Product();
        $productCount = $productModel->count();

        $orderModel = new Order();
        $orderCount = $orderModel->count();

        // Get recent orders
        $recentOrders = $orderModel->getRecent(5);

        $this->view->render("admin.dashboard", [
            "title" => "Admin Dashboard",
            "customerCount" => $customerCount,
            "vendorCount" => $vendorCount,
            "productCount" => $productCount,
            "orderCount" => $orderCount,
            "recentOrders" => $recentOrders
        ]);
    }

    /**
     * Show users list
     */
    public function users() {
        // Check if user is an admin
        Auth::requireRole('admin');

        // Get filter parameters
        $roles = $_GET['role'] ?? [];
        $dateRange = $_GET['date_range'] ?? 'all';

        // Get users
        $userModel = new User();

        if (in_array('customer', $roles) && !in_array('vendor', $roles) && !in_array('admin', $roles)) {
            $users = $userModel->getByRole('customer');
        } elseif (in_array('vendor', $roles) && !in_array('customer', $roles) && !in_array('admin', $roles)) {
            $users = $userModel->getByRole('vendor');
        } elseif (in_array('admin', $roles) && !in_array('customer', $roles) && !in_array('vendor', $roles)) {
            $users = $userModel->getByRole('admin');
        } else {
            $users = $userModel->all();
        }

        $this->view->render("admin.users", [
            "title" => "Manage Users",
            "users" => $users
        ]);
    }

    /**
     * Add a new user
     */
    public function addUser() {
        // Check if user is an admin
        Auth::requireRole('admin');

        // Get form data
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'customer';

        if (empty($name) || empty($email) || empty($password)) {
            Session::setFlash('error', 'Please fill in all required fields');
            header('Location: /admin/users');
            exit;
        }

        // Check if email already exists
        $userModel = new User();
        if ($userModel->emailExists($email)) {
            Session::setFlash('error', 'Email already exists');
            header('Location: /admin/users');
            exit;
        }

        // Register user
        $userId = $userModel->register([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'role' => $role
        ]);

        if ($userId) {
            // If user is a vendor, create vendor profile
            if ($role === 'vendor') {
                $vendorModel = new Vendor();
                $vendorModel->createVendor($userId, [
                    'business_name' => $name . "'s Business",
                    'location' => 'Not specified',
                    'description' => 'No description'
                ]);
            }

            Session::setFlash('success', 'User added successfully');
        } else {
            Session::setFlash('error', 'Failed to add user');
        }

        header('Location: /admin/users');
        exit;
    }

    /**
     * Edit a user
     */
    public function editUser() {
        // Check if user is an admin
        Auth::requireRole('admin');

        // Get form data
        $userId = $_POST['user_id'] ?? null;
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $role = $_POST['role'] ?? '';
        $password = $_POST['password'] ?? '';

        if (!$userId || empty($name) || empty($email) || empty($role)) {
            Session::setFlash('error', 'Please fill in all required fields');
            header('Location: /admin/users');
            exit;
        }

        // Get user
        $userModel = new User();
        $user = $userModel->find($userId);

        if (!$user) {
            Session::setFlash('error', 'User not found');
            header('Location: /admin/users');
            exit;
        }

        // Check if email already exists for another user
        $existingUser = $userModel->getByEmail($email);
        if ($existingUser && $existingUser['id'] != $userId) {
            Session::setFlash('error', 'Email already exists');
            header('Location: /admin/users');
            exit;
        }

        // Update user data
        $userData = [
            'name' => $name,
            'email' => $email,
            'role' => $role
        ];

        // Update password if provided
        if (!empty($password)) {
            $userData['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $result = $userModel->update($userId, $userData);

        if ($result) {
            // If user role changed to vendor, create vendor profile if it doesn't exist
            if ($role === 'vendor' && $user['role'] !== 'vendor') {
                $vendorModel = new Vendor();
                $vendor = $vendorModel->getByUserId($userId);

                if (!$vendor) {
                    $vendorModel->createVendor($userId, [
                        'business_name' => $name . "'s Business",
                        'location' => 'Not specified',
                        'description' => 'No description'
                    ]);
                }
            }

            Session::setFlash('success', 'User updated successfully');
        } else {
            Session::setFlash('error', 'Failed to update user');
        }

        header('Location: /admin/users');
        exit;
    }

    /**
     * Delete a user
     */
    public function deleteUser() {
        // Check if user is an admin
        Auth::requireRole('admin');

        // Get user ID
        $userId = $_POST['user_id'] ?? null;

        if (!$userId) {
            Session::setFlash('error', 'Invalid user');
            header('Location: /admin/users');
            exit;
        }

        // Get user
        $userModel = new User();
        $user = $userModel->find($userId);

        if (!$user) {
            Session::setFlash('error', 'User not found');
            header('Location: /admin/users');
            exit;
        }

        // Prevent deleting the current admin
        if ($user['id'] === Auth::id()) {
            Session::setFlash('error', 'You cannot delete your own account');
            header('Location: /admin/users');
            exit;
        }

        // Delete user
        $result = $userModel->delete($userId);

        if ($result) {
            Session::setFlash('success', 'User deleted successfully');
        } else {
            Session::setFlash('error', 'Failed to delete user');
        }

        header('Location: /admin/users');
        exit;
    }

    /**
     * Show vendors list
     */
    public function vendors() {
        // Check if user is an admin
        Auth::requireRole('admin');

        // Get vendors with details
        $vendorModel = new Vendor();
        $vendors = $vendorModel->getAllWithUserDetails();

        $this->view->render("admin.vendors", [
            "title" => "Manage Vendors",
            "vendors" => $vendors
        ]);
    }

    /**
     * Approve a vendor
     */
    public function approveVendor() {
        // Check if user is an admin
        Auth::requireRole('admin');

        // Get vendor ID
        $vendorId = $_POST['vendor_id'] ?? null;

        if (!$vendorId) {
            Session::setFlash('error', 'Invalid vendor');
            header('Location: /admin/vendors');
            exit;
        }

        // Approve vendor
        $vendorModel = new Vendor();
        $result = $vendorModel->approve($vendorId);

        if ($result) {
            Session::setFlash('success', 'Vendor approved successfully');
        } else {
            Session::setFlash('error', 'Failed to approve vendor');
        }

        header('Location: /admin/vendors');
        exit;
    }

    /**
     * Disapprove a vendor
     */
    public function disapproveVendor() {
        // Check if user is an admin
        Auth::requireRole('admin');

        // Get vendor ID
        $vendorId = $_POST['vendor_id'] ?? null;

        if (!$vendorId) {
            Session::setFlash('error', 'Invalid vendor');
            header('Location: /admin/vendors');
            exit;
        }

        // Disapprove vendor
        $vendorModel = new Vendor();
        $result = $vendorModel->disapprove($vendorId);

        if ($result) {
            Session::setFlash('success', 'Vendor disapproved successfully');
        } else {
            Session::setFlash('error', 'Failed to disapprove vendor');
        }

        header('Location: /admin/vendors');
        exit;
    }

    /**
     * Feature a vendor
     */
    public function featureVendor() {
        // Check if user is an admin
        Auth::requireRole('admin');

        // Get vendor ID
        $vendorId = $_POST['vendor_id'] ?? null;

        if (!$vendorId) {
            Session::setFlash('error', 'Invalid vendor');
            header('Location: /admin/vendors');
            exit;
        }

        // Feature vendor
        $vendorModel = new Vendor();
        $result = $vendorModel->feature($vendorId);

        if ($result) {
            Session::setFlash('success', 'Vendor featured successfully');
        } else {
            Session::setFlash('error', 'Failed to feature vendor');
        }

        header('Location: /admin/vendors');
        exit;
    }

    /**
     * Unfeature a vendor
     */
    public function unfeatureVendor() {
        // Check if user is an admin
        Auth::requireRole('admin');

        // Get vendor ID
        $vendorId = $_POST['vendor_id'] ?? null;

        if (!$vendorId) {
            Session::setFlash('error', 'Invalid vendor');
            header('Location: /admin/vendors');
            exit;
        }

        // Unfeature vendor
        $vendorModel = new Vendor();
        $result = $vendorModel->unfeature($vendorId);

        if ($result) {
            Session::setFlash('success', 'Vendor unfeatured successfully');
        } else {
            Session::setFlash('error', 'Failed to unfeature vendor');
        }

        header('Location: /admin/vendors');
        exit;
    }

    /**
     * Show categories list
     */
    public function categories() {
        // Check if user is an admin
        Auth::requireRole('admin');

        // Get categories with product counts
        $categoryModel = new Category();
        $categories = $categoryModel->getAllWithProductCounts();

        $this->view->render("admin.categories", [
            "title" => "Manage Categories",
            "categories" => $categories
        ]);
    }

    /**
     * Show form to add a new category
     */
    public function showAddCategory() {
        // Check if user is an admin
        Auth::requireRole('admin');

        $this->view->render("admin.add_category", [
            "title" => "Add New Category"
        ]);
    }

    /**
     * Process adding a new category
     */
    public function addCategory() {
        // Check if user is an admin
        Auth::requireRole('admin');

        // Get form data
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';

        if (empty($name)) {
            Session::setFlash('error', 'Please enter a category name');
            header('Location: /admin/categories/add');
            exit;
        }

        // Check if category already exists
        $categoryModel = new Category();
        $existingCategory = $categoryModel->getByName($name);

        if ($existingCategory) {
            Session::setFlash('error', 'Category already exists');
            header('Location: /admin/categories/add');
            exit;
        }

        // Handle image upload
        $image = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/uploads/categories/';

            // Create directory if it doesn't exist
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
            $uploadFile = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                $image = 'uploads/categories/' . $fileName;
            }
        }

        // Create category
        $categoryId = $categoryModel->create([
            'name' => $name,
            'description' => $description,
            'image' => $image
        ]);

        if ($categoryId) {
            Session::setFlash('success', 'Category added successfully');
            header('Location: /admin/categories');
            exit;
        } else {
            Session::setFlash('error', 'Failed to add category');
            header('Location: /admin/categories/add');
            exit;
        }
    }

    /**
     * Show form to edit a category
     */
    public function showEditCategory($id) {
        // Check if user is an admin
        Auth::requireRole('admin');

        // Get category
        $categoryModel = new Category();
        $category = $categoryModel->find($id);

        if (!$category) {
            Session::setFlash('error', 'Category not found');
            header('Location: /admin/categories');
            exit;
        }

        $this->view->render("admin.edit_category", [
            "title" => "Edit Category",
            "category" => $category
        ]);
    }

    /**
     * Process editing a category
     */
    public function editCategory() {
        // Check if user is an admin
        Auth::requireRole('admin');

        // Get form data
        $categoryId = $_POST['category_id'] ?? null;
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';

        if (!$categoryId || empty($name)) {
            Session::setFlash('error', 'Please enter a category name');
            header("Location: /admin/categories/edit/{$categoryId}");
            exit;
        }

        // Get category
        $categoryModel = new Category();
        $category = $categoryModel->find($categoryId);

        if (!$category) {
            Session::setFlash('error', 'Category not found');
            header('Location: /admin/categories');
            exit;
        }

        // Check if name already exists for another category
        $existingCategory = $categoryModel->getByName($name);
        if ($existingCategory && $existingCategory['id'] != $categoryId) {
            Session::setFlash('error', 'Category name already exists');
            header("Location: /admin/categories/edit/{$categoryId}");
            exit;
        }

        // Handle image upload
        $image = $category['image'];
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/uploads/categories/';

            // Create directory if it doesn't exist
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
            $uploadFile = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                $image = 'uploads/categories/' . $fileName;
            }
        }

        // Update category
        $result = $categoryModel->update($categoryId, [
            'name' => $name,
            'description' => $description,
            'image' => $image
        ]);

        if ($result) {
            Session::setFlash('success', 'Category updated successfully');
            header('Location: /admin/categories');
            exit;
        } else {
            Session::setFlash('error', 'Failed to update category');
            header("Location: /admin/categories/edit/{$categoryId}");
            exit;
        }
    }

    /**
     * Delete a category
     */
    public function deleteCategory() {
        // Check if user is an admin
        Auth::requireRole('admin');

        // Get category ID
        $categoryId = $_POST['category_id'] ?? null;

        if (!$categoryId) {
            Session::setFlash('error', 'Invalid category');
            header('Location: /admin/categories');
            exit;
        }

        // Delete category
        $categoryModel = new Category();
        $result = $categoryModel->delete($categoryId);

        if ($result) {
            Session::setFlash('success', 'Category deleted successfully');
        } else {
            Session::setFlash('error', 'Failed to delete category');
        }

        header('Location: /admin/categories');
        exit;
    }

    /**
     * Show all orders
     */
    public function orders() {
        // Check if user is an admin
        Auth::requireRole('admin');

        // Get orders
        $orderModel = new Order();
        $orders = $orderModel->getRecent(100);

        $this->view->render("admin.orders", [
            "title" => "Manage Orders",
            "orders" => $orders
        ]);
    }
}
