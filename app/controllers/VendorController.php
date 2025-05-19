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

class VendorController {
    private $view;
    
    public function __construct() {
        $this->view = new View();
        Session::start();
    }
    
    /**
     * Show vendor dashboard
     */
    public function dashboard() {
        // Check if user is a vendor
        Auth::requireRole('vendor');
        
        // Get vendor details
        $userModel = new User();
        $vendor = $userModel->getVendorWithDetails(Auth::id());
        
        // Get vendor stats
        $orderModel = new Order();
        $stats = $orderModel->getVendorStats($vendor['id']);
        
        // Get recent orders
        $recentOrders = $orderModel->getByVendorId($vendor['id']);
        $recentOrders = array_slice($recentOrders, 0, 5);
        
        $this->view->render("vendor.dashboard", [
            "title" => "Vendor Dashboard",
            "vendor" => $vendor,
            "stats" => $stats,
            "recentOrders" => $recentOrders
        ]);
    }
    
    /**
     * Show vendor products
     */
    public function products() {
        // Check if user is a vendor
        Auth::requireRole('vendor');
        
        // Get vendor details
        $userModel = new User();
        $vendor = $userModel->getVendorWithDetails(Auth::id());
        
        // Get vendor products
        $productModel = new Product();
        $products = $productModel->getByVendorId($vendor['id']);
        
        $this->view->render("vendor.products", [
            "title" => "Your Products",
            "vendor" => $vendor,
            "products" => $products
        ]);
    }
    
    /**
     * Show form to add a new product
     */
    public function showAddProduct() {
        // Check if user is a vendor
        Auth::requireRole('vendor');
        
        // Get categories
        $categoryModel = new Category();
        $categories = $categoryModel->all();
        
        $this->view->render("vendor.add_product", [
            "title" => "Add New Product",
            "categories" => $categories
        ]);
    }
    
    /**
     * Process adding a new product
     */
    public function addProduct() {
        // Check if user is a vendor
        Auth::requireRole('vendor');
        
        // Get vendor details
        $userModel = new User();
        $vendor = $userModel->getVendorWithDetails(Auth::id());
        
        // Get form data
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $price = $_POST['price'] ?? '';
        $categoryId = $_POST['category_id'] ?? null;
        $preparationTime = $_POST['preparation_time'] ?? null;
        
        if (empty($name) || empty($price)) {
            Session::setFlash('error', 'Please fill in all required fields');
            header('Location: /vendor/products/add');
            exit;
        }
        
        // Handle image upload
        $image = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/uploads/products/';
            
            // Create directory if it doesn't exist
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
            $uploadFile = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                $image = 'uploads/products/' . $fileName;
            }
        }
        
        // Create product
        $productModel = new Product();
        $productId = $productModel->create([
            'vendor_id' => $vendor['id'],
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'image' => $image,
            'category_id' => $categoryId,
            'preparation_time' => $preparationTime,
            'is_available' => true
        ]);
        
        if ($productId) {
            Session::setFlash('success', 'Product added successfully');
            header('Location: /vendor/products');
            exit;
        } else {
            Session::setFlash('error', 'Failed to add product');
            header('Location: /vendor/products/add');
            exit;
        }
    }
    
    /**
     * Show form to edit a product
     */
    public function showEditProduct($id) {
        // Check if user is a vendor
        Auth::requireRole('vendor');
        
        // Get vendor details
        $userModel = new User();
        $vendor = $userModel->getVendorWithDetails(Auth::id());
        
        // Get product
        $productModel = new Product();
        $product = $productModel->find($id);
        
        if (!$product) {
            Session::setFlash('error', 'Product not found');
            header('Location: /vendor/products');
            exit;
        }
        
        // Check if product belongs to this vendor
        if ($product['vendor_id'] != $vendor['id']) {
            Session::setFlash('error', 'You are not authorized to edit this product');
            header('Location: /vendor/products');
            exit;
        }
        
        // Get categories
        $categoryModel = new Category();
        $categories = $categoryModel->all();
        
        $this->view->render("vendor.edit_product", [
            "title" => "Edit Product",
            "product" => $product,
            "categories" => $categories
        ]);
    }
    
    /**
     * Process editing a product
     */
    public function editProduct() {
        // Check if user is a vendor
        Auth::requireRole('vendor');
        
        // Get vendor details
        $userModel = new User();
        $vendor = $userModel->getVendorWithDetails(Auth::id());
        
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
            header("Location: /vendor/products/edit/{$productId}");
            exit;
        }
        
        // Get product
        $productModel = new Product();
        $product = $productModel->find($productId);
        
        if (!$product) {
            Session::setFlash('error', 'Product not found');
            header('Location: /vendor/products');
            exit;
        }
        
        // Check if product belongs to this vendor
        if ($product['vendor_id'] != $vendor['id']) {
            Session::setFlash('error', 'You are not authorized to edit this product');
            header('Location: /vendor/products');
            exit;
        }
        
        // Handle image upload
        $image = $product['image'];
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/uploads/products/';
            
            // Create directory if it doesn't exist
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
            $uploadFile = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                $image = 'uploads/products/' . $fileName;
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
            header('Location: /vendor/products');
            exit;
        } else {
            Session::setFlash('error', 'Failed to update product');
            header("Location: /vendor/products/edit/{$productId}");
            exit;
        }
    }
    
    /**
     * Delete a product
     */
    public function deleteProduct() {
        // Check if user is a vendor
        Auth::requireRole('vendor');
        
        // Get vendor details
        $userModel = new User();
        $vendor = $userModel->getVendorWithDetails(Auth::id());
        
        // Get product ID
        $productId = $_POST['product_id'] ?? null;
        
        if (!$productId) {
            Session::setFlash('error', 'Invalid product');
            header('Location: /vendor/products');
            exit;
        }
        
        // Get product
        $productModel = new Product();
        $product = $productModel->find($productId);
        
        if (!$product) {
            Session::setFlash('error', 'Product not found');
            header('Location: /vendor/products');
            exit;
        }
        
        // Check if product belongs to this vendor
        if ($product['vendor_id'] != $vendor['id']) {
            Session::setFlash('error', 'You are not authorized to delete this product');
            header('Location: /vendor/products');
            exit;
        }
        
        // Delete product
        $result = $productModel->delete($productId);
        
        if ($result) {
            Session::setFlash('success', 'Product deleted successfully');
        } else {
            Session::setFlash('error', 'Failed to delete product');
        }
        
        header('Location: /vendor/products');
        exit;
    }
    
    /**
     * Show vendor orders
     */
    public function orders() {
        // Check if user is a vendor
        Auth::requireRole('vendor');
        
        // Get vendor details
        $userModel = new User();
        $vendor = $userModel->getVendorWithDetails(Auth::id());
        
        // Get vendor orders
        $orderModel = new Order();
        $orders = $orderModel->getByVendorId($vendor['id']);
        
        $this->view->render("vendor.orders", [
            "title" => "Your Orders",
            "vendor" => $vendor,
            "orders" => $orders
        ]);
    }
    
    /**
     * Update order status
     */
    public function updateOrderStatus() {
        // Check if user is a vendor
        Auth::requireRole('vendor');
        
        // Get vendor details
        $userModel = new User();
        $vendor = $userModel->getVendorWithDetails(Auth::id());
        
        // Get form data
        $orderId = $_POST['order_id'] ?? null;
        $status = $_POST['status'] ?? null;
        
        if (!$orderId || !$status) {
            Session::setFlash('error', 'Invalid order or status');
            header('Location: /vendor/orders');
            exit;
        }
        
        // Get order
        $orderModel = new Order();
        $order = $orderModel->find($orderId);
        
        if (!$order) {
            Session::setFlash('error', 'Order not found');
            header('Location: /vendor/orders');
            exit;
        }
        
        // Check if order belongs to this vendor
        if ($order['vendor_id'] != $vendor['id']) {
            Session::setFlash('error', 'You are not authorized to update this order');
            header('Location: /vendor/orders');
            exit;
        }
        
        // Update order status
        $result = $orderModel->updateStatus($orderId, $status);
        
        if ($result) {
            Session::setFlash('success', 'Order status updated successfully');
        } else {
            Session::setFlash('error', 'Failed to update order status');
        }
        
        header("Location: /vendor/orders/{$orderId}");
        exit;
    }
}
