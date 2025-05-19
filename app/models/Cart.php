<?php
namespace App\Models;

use App\Core\Model;

class Cart extends Model {
    protected $table = 'cart';

    /**
     * Add item to cart
     */
    public function addItem($userId, $productId, $quantity = 1) {
        // Check if item already exists in cart
        $sql = "SELECT * FROM {$this->table} WHERE user_id = ? AND product_id = ? LIMIT 1";
        $existingItem = $this->fetch($sql, [$userId, $productId]);

        if ($existingItem) {
            // Update quantity
            $newQuantity = $existingItem['quantity'] + $quantity;
            return $this->update($existingItem['id'], ['quantity' => $newQuantity]);
        } else {
            // Add new item
            return $this->create([
                'user_id' => $userId,
                'product_id' => $productId,
                'quantity' => $quantity
            ]);
        }
    }

    /**
     * Update cart item quantity
     */
    public function updateQuantity($cartId, $quantity) {
        return $this->update($cartId, ['quantity' => $quantity]);
    }

    /**
     * Remove item from cart
     */
    public function removeItem($cartId) {
        return $this->delete($cartId);
    }

    /**
     * Clear user's cart
     */
    public function clearCart($userId) {
        return $this->db->delete($this->table, 'user_id = ?', [$userId]);
    }

    /**
     * Get user's cart items with product details
     */
    public function getUserCartWithProducts($userId) {
        $sql = "SELECT c.id as cart_id, c.quantity,
                p.id as product_id, p.name, p.price, p.image,
                v.id as vendor_id, v.business_name
                FROM cart c
                JOIN products p ON c.product_id = p.id
                JOIN vendors v ON p.vendor_id = v.id
                WHERE c.user_id = ?";

        return $this->fetchAll($sql, [$userId]);
    }

    /**
     * Get cart total
     */
    public function getCartTotal($userId) {
        $sql = "SELECT SUM(p.price * c.quantity) as total
                FROM cart c
                JOIN products p ON c.product_id = p.id
                WHERE c.user_id = ?";

        $result = $this->fetch($sql, [$userId]);
        return $result['total'] ?? 0;
    }

    /**
     * Get cart item count
     */
    public function getCartItemCount($userId) {
        $sql = "SELECT COUNT(*) as count FROM cart WHERE user_id = ?";
        $result = $this->fetch($sql, [$userId]);
        return $result['count'] ?? 0;
    }

    /**
     * Group cart items by vendor
     */
    public function getCartItemsByVendor($userId) {
        $sql = "SELECT c.id as cart_id, c.quantity,
                p.id as product_id, p.name, p.price, p.image,
                v.id as vendor_id, v.business_name
                FROM cart c
                JOIN products p ON c.product_id = p.id
                JOIN vendors v ON p.vendor_id = v.id
                WHERE c.user_id = ?
                ORDER BY v.id";

        $items = $this->fetchAll($sql, [$userId]);

        $groupedItems = [];
        foreach ($items as $item) {
            $vendorId = $item['vendor_id'];
            if (!isset($groupedItems[$vendorId])) {
                $groupedItems[$vendorId] = [
                    'vendor_id' => $vendorId,
                    'business_name' => $item['business_name'],
                    'items' => []
                ];
            }

            $groupedItems[$vendorId]['items'][] = $item;
        }

        return $groupedItems;
    }
}
