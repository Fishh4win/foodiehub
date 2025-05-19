<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Session;
use App\Core\Auth;
use App\Models\Cart;
use App\Models\Product;

class CartController {
    private $view;
    private $cartModel;
    
    public function __construct() {
        $this->view = new View();
        $this->cartModel = new Cart();
        Session::start();
    }
    
    /**
     * Show cart contents
     */
    public function index() {
        // Check if user is logged in
        Auth::requireAuth();
        
        // Get cart items with product details
        $cartItems = $this->cartModel->getUserCartWithProducts(Auth::id());
        
        // Get cart total
        $cartTotal = $this->cartModel->getCartTotal(Auth::id());
        
        $this->view->render("cart.index", [
            "title" => "Your Cart",
            "cartItems" => $cartItems,
            "cartTotal" => $cartTotal
        ]);
    }
    
    /**
     * Add item to cart
     */
    public function add() {
        // Check if user is logged in
        Auth::requireAuth();
        
        $productId = $_POST['product_id'] ?? null;
        $quantity = $_POST['quantity'] ?? 1;
        
        if (!$productId) {
            Session::setFlash('error', 'Invalid product');
            header('Location: /products');
            exit;
        }
        
        // Check if product exists and is available
        $productModel = new Product();
        $product = $productModel->find($productId);
        
        if (!$product || !$product['is_available']) {
            Session::setFlash('error', 'Product is not available');
            header('Location: /products');
            exit;
        }
        
        // Add to cart
        $this->cartModel->addItem(Auth::id(), $productId, $quantity);
        
        Session::setFlash('success', 'Product added to cart');
        
        // Redirect back to previous page or cart
        $redirect = $_POST['redirect'] ?? '/cart';
        header("Location: {$redirect}");
        exit;
    }
    
    /**
     * Update cart item quantity
     */
    public function update() {
        // Check if user is logged in
        Auth::requireAuth();
        
        $cartId = $_POST['cart_id'] ?? null;
        $quantity = $_POST['quantity'] ?? 1;
        
        if (!$cartId) {
            Session::setFlash('error', 'Invalid cart item');
            header('Location: /cart');
            exit;
        }
        
        // Update quantity
        $this->cartModel->updateQuantity($cartId, $quantity);
        
        Session::setFlash('success', 'Cart updated');
        header('Location: /cart');
        exit;
    }
    
    /**
     * Remove item from cart
     */
    public function remove() {
        // Check if user is logged in
        Auth::requireAuth();
        
        $cartId = $_POST['cart_id'] ?? null;
        
        if (!$cartId) {
            Session::setFlash('error', 'Invalid cart item');
            header('Location: /cart');
            exit;
        }
        
        // Remove item
        $this->cartModel->removeItem($cartId);
        
        Session::setFlash('success', 'Item removed from cart');
        header('Location: /cart');
        exit;
    }
    
    /**
     * Clear cart
     */
    public function clear() {
        // Check if user is logged in
        Auth::requireAuth();
        
        // Clear cart
        $this->cartModel->clearCart(Auth::id());
        
        Session::setFlash('success', 'Cart cleared');
        header('Location: /cart');
        exit;
    }
    
    /**
     * Proceed to checkout
     */
    public function checkout() {
        // Check if user is logged in
        Auth::requireAuth();
        
        // Get cart items grouped by vendor
        $cartItemsByVendor = $this->cartModel->getCartItemsByVendor(Auth::id());
        
        // Get cart total
        $cartTotal = $this->cartModel->getCartTotal(Auth::id());
        
        // Check if cart is empty
        if (empty($cartItemsByVendor)) {
            Session::setFlash('error', 'Your cart is empty');
            header('Location: /cart');
            exit;
        }
        
        $this->view->render("cart.checkout", [
            "title" => "Checkout",
            "cartItemsByVendor" => $cartItemsByVendor,
            "cartTotal" => $cartTotal
        ]);
    }
}
