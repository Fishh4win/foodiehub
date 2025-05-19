<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/helpers.php';

use App\Core\Router;
use App\Core\Session;

// Start session
Session::start();

$router = new Router;

// Home routes
$router->get("/", "HomeController@index");
$router->get("/about", "HomeController@about");
$router->get("/contact", "HomeController@contact");
$router->post("/contact/send", "HomeController@sendContactForm");

// Auth routes
$router->get("/login", "AuthController@showLogin");
$router->post("/login", "AuthController@login");
$router->get("/register", "AuthController@showRegister");
$router->post("/register", "AuthController@registerCustomer");
$router->get("/vendor/register", "AuthController@showVendorRegister");
$router->post("/vendor/register", "AuthController@registerVendor");
$router->get("/logout", "AuthController@logout");

// Product routes
$router->get("/products", "ProductController@list");
$router->get("/products/{id}", "ProductController@details");
$router->post("/products/review", "ProductController@submitReview");
$router->get("/search", "ProductController@search");

// Cart routes
$router->get("/cart", "CartController@index");
$router->post("/cart/add", "CartController@add");
$router->post("/cart/update", "CartController@update");
$router->post("/cart/remove", "CartController@remove");
$router->post("/cart/clear", "CartController@clear");
$router->get("/cart/checkout", "CartController@checkout");

// User routes
$router->get("/profile", "UserController@profile");
$router->post("/profile/update", "UserController@updateProfile");

// Order routes
$router->post("/orders/place", "OrderController@place");
$router->get("/orders", "OrderController@list");
$router->get("/orders/{id}", "OrderController@details");
$router->post("/orders/cancel", "OrderController@cancel");

// Vendor routes
$router->get("/vendor/dashboard", "VendorController@dashboard");

// Vendor Product Management
$router->get("/vendor/products", "VendorController@products");
$router->get("/vendor/products/add", "VendorController@showAddProduct");
$router->post("/vendor/products/add", "VendorController@addProduct");
$router->get("/vendor/products/edit/{id}", "VendorController@showEditProduct");
$router->post("/vendor/products/edit", "VendorController@editProduct");
$router->post("/vendor/products/delete", "VendorController@deleteProduct");
$router->post("/vendor/products/toggle-availability", "VendorController@toggleProductAvailability");

// Vendor Category Management
$router->get("/vendor/categories", "VendorController@categories");
$router->get("/vendor/categories/add", "VendorController@showAddCategory");
$router->post("/vendor/categories/add", "VendorController@addCategory");
$router->get("/vendor/categories/edit/{id}", "VendorController@showEditCategory");
$router->post("/vendor/categories/edit", "VendorController@editCategory");

// Vendor Order Management
$router->get("/vendor/orders", "VendorController@orders");
$router->get("/vendor/orders/{id}", "OrderController@details");
$router->post("/vendor/orders/update-status", "VendorController@updateOrderStatus");

// Admin routes
$router->get("/admin/dashboard", "AdminController@dashboard");

// Admin User Management
$router->get("/admin/users", "AdminController@users");
$router->post("/admin/users/add", "AdminController@addUser");
$router->post("/admin/users/edit", "AdminController@editUser");
$router->post("/admin/users/delete", "AdminController@deleteUser");

// Admin Vendor Management
$router->get("/admin/vendors", "AdminController@vendors");
$router->get("/admin/vendors/edit/{id}", "AdminController@showEditVendor");
$router->post("/admin/vendors/edit", "AdminController@editVendor");
$router->get("/admin/vendors/{id}/products", "AdminController@vendorProducts");
$router->post("/admin/vendors/approve", "AdminController@approveVendor");
$router->post("/admin/vendors/disapprove", "AdminController@disapproveVendor");
$router->post("/admin/vendors/feature", "AdminController@featureVendor");
$router->post("/admin/vendors/unfeature", "AdminController@unfeatureVendor");

// Admin Category Management
$router->get("/admin/categories", "AdminController@categories");
$router->get("/admin/categories/add", "AdminController@showAddCategory");
$router->post("/admin/categories/add", "AdminController@addCategory");
$router->get("/admin/categories/edit/{id}", "AdminController@showEditCategory");
$router->post("/admin/categories/edit", "AdminController@editCategory");
$router->post("/admin/categories/delete", "AdminController@deleteCategory");

// Admin Product Management
$router->get("/admin/products", "AdminController@products");
$router->get("/admin/products/edit/{id}", "AdminController@showEditProduct");
$router->post("/admin/products/edit", "AdminController@editProduct");
$router->post("/admin/products/delete", "AdminController@deleteProduct");
$router->post("/admin/products/toggle-availability", "AdminController@toggleProductAvailability");

// Admin Order Management
$router->get("/admin/orders", "AdminController@orders");
$router->get("/admin/orders/{id}", "OrderController@details");

// Dispatch the request
$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
