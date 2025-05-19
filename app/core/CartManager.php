<?php
namespace App\Core;

use App\Models\Cart;
use App\Models\Product;

/**
 * CartManager Class
 * 
 * Manages shopping cart operations
 */
class CartManager {
    /**
     * @var Cart Cart model
     */
    private $cartModel;
    
    /**
     * @var Product Product model
     */
    private $productModel;
    
    /**
     * @var int User ID
     */
    private $userId;
    
    /**
     * Constructor
     * 
     * @param int $userId User ID
     */
    public function __construct($userId = null) {
        $this->cartModel = new Cart();
        $this->productModel = new Product();
        $this->userId = $userId ?? Auth::id();
    }
    
    /**
     * Set user ID
     * 
     * @param int $userId User ID
     * @return $this
     */
    public function setUserId($userId) {
        $this->userId = $userId;
        return $this;
    }
    
    /**
     * Add item to cart
     * 
     * @param int $productId Product ID
     * @param int $quantity Quantity (default: 1)
     * @return bool True if successful
     */
    public function addItem($productId, $quantity = 1) {
        // Check if user is logged in
        if (!$this->userId) {
            return false;
        }
        
        // Check if product exists and is available
        $product = $this->productModel->find($productId);
        
        if (!$product || !$product['is_available']) {
            return false;
        }
        
        // Add to cart
        return $this->cartModel->addItem($this->userId, $productId, $quantity);
    }
    
    /**
     * Update item quantity
     * 
     * @param int $cartId Cart item ID
     * @param int $quantity New quantity
     * @return bool True if successful
     */
    public function updateQuantity($cartId, $quantity) {
        // Check if user is logged in
        if (!$this->userId) {
            return false;
        }
        
        // Get cart item
        $sql = "SELECT * FROM cart WHERE id = ? AND user_id = ?";
        $cartItem = $this->cartModel->fetch($sql, [$cartId, $this->userId]);
        
        if (!$cartItem) {
            return false;
        }
        
        // Update quantity
        return $this->cartModel->updateQuantity($cartId, $quantity);
    }
    
    /**
     * Remove item from cart
     * 
     * @param int $cartId Cart item ID
     * @return bool True if successful
     */
    public function removeItem($cartId) {
        // Check if user is logged in
        if (!$this->userId) {
            return false;
        }
        
        // Get cart item
        $sql = "SELECT * FROM cart WHERE id = ? AND user_id = ?";
        $cartItem = $this->cartModel->fetch($sql, [$cartId, $this->userId]);
        
        if (!$cartItem) {
            return false;
        }
        
        // Remove item
        return $this->cartModel->removeItem($cartId);
    }
    
    /**
     * Clear cart
     * 
     * @return bool True if successful
     */
    public function clearCart() {
        // Check if user is logged in
        if (!$this->userId) {
            return false;
        }
        
        // Clear cart
        return $this->cartModel->clearCart($this->userId);
    }
    
    /**
     * Get cart items
     * 
     * @return array Cart items
     */
    public function getItems() {
        // Check if user is logged in
        if (!$this->userId) {
            return [];
        }
        
        // Get cart items
        return $this->cartModel->getUserCartWithProducts($this->userId);
    }
    
    /**
     * Get cart items grouped by vendor
     * 
     * @return array Cart items grouped by vendor
     */
    public function getItemsByVendor() {
        // Check if user is logged in
        if (!$this->userId) {
            return [];
        }
        
        // Get cart items grouped by vendor
        return $this->cartModel->getCartItemsByVendor($this->userId);
    }
    
    /**
     * Get cart total
     * 
     * @return float Cart total
     */
    public function getTotal() {
        // Check if user is logged in
        if (!$this->userId) {
            return 0;
        }
        
        // Get cart total
        return $this->cartModel->getCartTotal($this->userId);
    }
    
    /**
     * Get cart item count
     * 
     * @return int Cart item count
     */
    public function getItemCount() {
        // Check if user is logged in
        if (!$this->userId) {
            return 0;
        }
        
        // Get cart item count
        return $this->cartModel->getCartItemCount($this->userId);
    }
    
    /**
     * Check if cart is empty
     * 
     * @return bool True if cart is empty
     */
    public function isEmpty() {
        return $this->getItemCount() === 0;
    }
    
    /**
     * Check if cart has items from multiple vendors
     * 
     * @return bool True if cart has items from multiple vendors
     */
    public function hasMultipleVendors() {
        // Check if user is logged in
        if (!$this->userId) {
            return false;
        }
        
        // Get cart items
        $items = $this->getItems();
        
        if (empty($items)) {
            return false;
        }
        
        // Get unique vendor IDs
        $vendorIds = [];
        
        foreach ($items as $item) {
            $vendorIds[$item['vendor_id']] = true;
        }
        
        return count($vendorIds) > 1;
    }
    
    /**
     * Get vendor IDs in cart
     * 
     * @return array Vendor IDs
     */
    public function getVendorIds() {
        // Check if user is logged in
        if (!$this->userId) {
            return [];
        }
        
        // Get cart items
        $items = $this->getItems();
        
        if (empty($items)) {
            return [];
        }
        
        // Get unique vendor IDs
        $vendorIds = [];
        
        foreach ($items as $item) {
            $vendorIds[$item['vendor_id']] = $item['vendor_id'];
        }
        
        return array_values($vendorIds);
    }
    
    /**
     * Get cart items by vendor
     * 
     * @param int $vendorId Vendor ID
     * @return array Cart items for vendor
     */
    public function getItemsForVendor($vendorId) {
        // Check if user is logged in
        if (!$this->userId) {
            return [];
        }
        
        // Get cart items
        $items = $this->getItems();
        
        if (empty($items)) {
            return [];
        }
        
        // Filter items by vendor
        $vendorItems = [];
        
        foreach ($items as $item) {
            if ($item['vendor_id'] == $vendorId) {
                $vendorItems[] = $item;
            }
        }
        
        return $vendorItems;
    }
    
    /**
     * Get cart total for vendor
     * 
     * @param int $vendorId Vendor ID
     * @return float Cart total for vendor
     */
    public function getTotalForVendor($vendorId) {
        // Get cart items for vendor
        $items = $this->getItemsForVendor($vendorId);
        
        if (empty($items)) {
            return 0;
        }
        
        // Calculate total
        $total = 0;
        
        foreach ($items as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        
        return $total;
    }
}
