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
     * Show form to edit a vendor
     */
    public function showEditVendor($id) {
        // Check if user is an admin
        Auth::requireRole('admin');

        // Get vendor with details
        $vendorModel = new Vendor();
        $vendor = $vendorModel->getWithUserDetails($id);

        if (!$vendor) {
            Session::setFlash('error', 'Vendor not found');
            header('Location: /admin/vendors');
            exit;
        }

        $this->view->render("admin.edit_vendor", [
            "title" => "Edit Vendor",
            "vendor" => $vendor
        ]);
    }

    /**
     * Process editing a vendor
     */
    public function editVendor() {
        // Check if user is an admin
        Auth::requireRole('admin');

        // Get form data
        $vendorId = $_POST['vendor_id'] ?? null;
        $userId = $_POST['user_id'] ?? null;
        $businessName = $_POST['business_name'] ?? '';
        $description = $_POST['description'] ?? '';
        $location = $_POST['location'] ?? '';
        $isApproved = isset($_POST['is_approved']) ? true : false;
        $isFeatured = isset($_POST['is_featured']) ? true : false;

        if (!$vendorId || !$userId || empty($businessName)) {
            Session::setFlash('error', 'Please fill in all required fields');
            header("Location: /admin/vendors/edit/{$vendorId}");
            exit;
        }

        // Update vendor data
        $vendorModel = new Vendor();
        $vendorData = [
            'business_name' => $businessName,
            'description' => $description,
            'location' => $location,
            'is_approved' => $isApproved,
            'is_featured' => $isFeatured
        ];

        // Handle logo upload
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/vendors/';

            // Create directory if it doesn't exist
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileName = time() . '_' . basename($_FILES['logo']['name']);
            $uploadFile = $uploadDir . $fileName;

            // Check if file is an image
            $imageInfo = getimagesize($_FILES['logo']['tmp_name']);
            if ($imageInfo === false) {
                Session::setFlash('error', 'Uploaded file is not an image');
                header("Location: /admin/vendors/edit/{$vendorId}");
                exit;
            }

            // Move uploaded file
            if (move_uploaded_file($_FILES['logo']['tmp_name'], $uploadFile)) {
                $vendorData['logo'] = $uploadFile;
            } else {
                Session::setFlash('error', 'Failed to upload logo');
                header("Location: /admin/vendors/edit/{$vendorId}");
                exit;
            }
        }

        // Handle banner upload
        if (isset($_FILES['banner']) && $_FILES['banner']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/vendors/';

            // Create directory if it doesn't exist
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileName = time() . '_banner_' . basename($_FILES['banner']['name']);
            $uploadFile = $uploadDir . $fileName;

            // Check if file is an image
            $imageInfo = getimagesize($_FILES['banner']['tmp_name']);
            if ($imageInfo === false) {
                Session::setFlash('error', 'Uploaded file is not an image');
                header("Location: /admin/vendors/edit/{$vendorId}");
                exit;
            }

            // Move uploaded file
            if (move_uploaded_file($_FILES['banner']['tmp_name'], $uploadFile)) {
                $vendorData['banner'] = $uploadFile;
            } else {
                Session::setFlash('error', 'Failed to upload banner');
                header("Location: /admin/vendors/edit/{$vendorId}");
                exit;
            }
        }

        // Update vendor
        $result = $vendorModel->update($vendorId, $vendorData);

        // Update user data if provided
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $phone = $_POST['phone'] ?? '';

        if (!empty($name) || !empty($email) || !empty($phone)) {
            $userModel = new User();
            $userData = [];

            if (!empty($name)) {
                $userData['name'] = $name;
            }

            if (!empty($email)) {
                // Check if email already exists for another user
                $existingUser = $userModel->getByEmail($email);
                if ($existingUser && $existingUser['id'] != $userId) {
                    Session::setFlash('error', 'Email already exists');
                    header("Location: /admin/vendors/edit/{$vendorId}");
                    exit;
                }

                $userData['email'] = $email;
            }

            if (!empty($phone)) {
                $userData['phone'] = $phone;
            }

            if (!empty($userData)) {
                $userModel->update($userId, $userData);
            }
        }

        if ($result) {
            Session::setFlash('success', 'Vendor updated successfully');
            header('Location: /admin/vendors');
            exit;
        } else {
            Session::setFlash('error', 'Failed to update vendor');
            header("Location: /admin/vendors/edit/{$vendorId}");
            exit;
        }
    }

    /**
     * Show vendor products
     */
    public function vendorProducts($id) {
        // Check if user is an admin
        Auth::requireRole('admin');

        // Get vendor with details
        $vendorModel = new Vendor();
        $vendor = $vendorModel->getWithUserDetails($id);

        if (!$vendor) {
            Session::setFlash('error', 'Vendor not found');
            header('Location: /admin/vendors');
            exit;
        }

        // Get vendor products
        $productModel = new Product();
        $products = $productModel->getByVendorId($vendor['id']);

        // Get categories for filter
        $categoryModel = new Category();
        $categories = $categoryModel->all();

        $this->view->render("admin.vendor_products", [
            "title" => "Vendor Products",
            "vendor" => $vendor,
            "products" => $products,
            "categories" => $categories
        ]);
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

        // Get filter parameters
        $statuses = $_GET['status'] ?? [];
        $vendorId = $_GET['vendor'] ?? null;
        $dateRange = $_GET['date_range'] ?? 'all';

        // Build filters array
        $filters = [
            'status' => $statuses,
            'vendor' => $vendorId,
            'date_range' => $dateRange
        ];

        // Get orders based on filters
        $orderModel = new Order();

        if (empty($filters['status']) && empty($filters['vendor']) && $filters['date_range'] === 'all') {
            // No filters applied, get recent orders
            $orders = $orderModel->getRecent(100);
        } else {
            // Apply filters
            $orders = $orderModel->filter($filters);
        }

        // Get vendors for filter dropdown
        $vendorModel = new Vendor();
        $vendors = $vendorModel->getAllWithUserDetails();

        $this->view->render("admin.orders", [
            "title" => "Manage Orders",
            "orders" => $orders,
            "vendors" => $vendors,
            "filters" => $filters
        ]);
    }

    /**
     * Show all products
     */
    public function products() {
        // Check if user is an admin
        Auth::requireRole('admin');

        // Get filter parameters
        $categoryId = $_GET['category'] ?? null;
        $vendorId = $_GET['vendor'] ?? null;
        $search = $_GET['search'] ?? null;

        // Get categories for filter
        $categoryModel = new Category();
        $categories = $categoryModel->all();

        // Get vendors for filter
        $vendorModel = new Vendor();
        $vendors = $vendorModel->getAllWithUserDetails();

        // Build filters array
        $filters = [
            'category_id' => $categoryId,
            'vendor_id' => $vendorId
        ];

        // Get products based on filters
        $productModel = new Product();
        if ($search) {
            $products = $productModel->search($search);
        } else {
            $products = $productModel->filter($filters);
        }

        $this->view->render("admin.products", [
            "title" => "Manage Products",
            "products" => $products,
            "categories" => $categories,
            "vendors" => $vendors,
            "filters" => [
                "category" => $categoryId,
                "vendor" => $vendorId,
                "search" => $search
            ]
        ]);
    }

    /**
     * Show form to edit a product
     */
    public function showEditProduct($id) {
        // Check if user is an admin
        Auth::requireRole('admin');

        // Get product
        $productModel = new Product();
        $product = $productModel->getWithVendorDetails($id);

        if (!$product) {
            Session::setFlash('error', 'Product not found');
            header('Location: /admin/products');
            exit;
        }

        // Get categories
        $categoryModel = new Category();
        $categories = $categoryModel->all();

        $this->view->render("admin.edit_product", [
            "title" => "Edit Product",
            "product" => $product,
            "categories" => $categories
        ]);
    }

    /**
     * Process editing a product
     */
    public function editProduct() {
        // Check if user is an admin
        Auth::requireRole('admin');

        // Get form data
        $productId = $_POST['product_id'] ?? null;
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $price = $_POST['price'] ?? '';
        $categoryId = $_POST['category_id'] ?? null;
        $preparationTime = $_POST['preparation_time'] ?? null;
        $isAvailable = isset($_POST['is_available']) ? true : false;

        if (!$productId || empty($name) || empty($price)) {
            Session::setFlash('error', 'Please fill in all required fields');
            header("Location: /admin/products/edit/{$productId}");
            exit;
        }

        // Get product to check vendor
        $productModel = new Product();
        $product = $productModel->find($productId);

        if (!$product) {
            Session::setFlash('error', 'Product not found');
            header('Location: /admin/products');
            exit;
        }

        // Handle image upload
        $image = $product['image']; // Keep existing image by default

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/products/';

            // Create directory if it doesn't exist
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileName = time() . '_' . basename($_FILES['image']['name']);
            $uploadFile = $uploadDir . $fileName;

            // Check if file is an image
            $imageInfo = getimagesize($_FILES['image']['tmp_name']);
            if ($imageInfo === false) {
                Session::setFlash('error', 'Uploaded file is not an image');
                header("Location: /admin/products/edit/{$productId}");
                exit;
            }

            // Move uploaded file
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                $image = $uploadFile;
            } else {
                Session::setFlash('error', 'Failed to upload image');
                header("Location: /admin/products/edit/{$productId}");
                exit;
            }
        }

        // Update product
        $result = $productModel->update($productId, [
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'image' => $image,
            'category_id' => $categoryId,
            'preparation_time' => $preparationTime,
            'is_available' => $isAvailable
        ]);

        if ($result) {
            Session::setFlash('success', 'Product updated successfully');
            header('Location: /admin/products');
            exit;
        } else {
            Session::setFlash('error', 'Failed to update product');
            header("Location: /admin/products/edit/{$productId}");
            exit;
        }
    }

    /**
     * Delete a product
     */
    public function deleteProduct() {
        // Check if user is an admin
        Auth::requireRole('admin');

        // Get product ID
        $productId = $_POST['product_id'] ?? null;

        if (!$productId) {
            Session::setFlash('error', 'Invalid product');
            header('Location: /admin/products');
            exit;
        }

        // Delete product
        $productModel = new Product();
        $result = $productModel->delete($productId);

        if ($result) {
            Session::setFlash('success', 'Product deleted successfully');
        } else {
            Session::setFlash('error', 'Failed to delete product');
        }

        header('Location: /admin/products');
        exit;
    }

    /**
     * Toggle product availability
     */
    public function toggleProductAvailability() {
        // Check if user is an admin
        Auth::requireRole('admin');

        // Get product ID
        $productId = $_POST['product_id'] ?? null;

        if (!$productId) {
            Session::setFlash('error', 'Invalid product');
            header('Location: /admin/products');
            exit;
        }

        // Get product
        $productModel = new Product();
        $product = $productModel->find($productId);

        if (!$product) {
            Session::setFlash('error', 'Product not found');
            header('Location: /admin/products');
            exit;
        }

        // Toggle availability
        $isAvailable = !$product['is_available'];
        $result = $productModel->update($productId, ['is_available' => $isAvailable]);

        if ($result) {
            $status = $isAvailable ? 'available' : 'unavailable';
            Session::setFlash('success', "Product marked as {$status}");
        } else {
            Session::setFlash('error', 'Failed to update product availability');
        }

        header('Location: /admin/products');
        exit;
    }
}
