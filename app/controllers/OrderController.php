<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Session;
use App\Core\Auth;
use App\Models\Order;
use App\Models\Cart;
use App\Models\Product;
use App\Models\User;

class OrderController {
    private $view;
    private $orderModel;

    public function __construct() {
        $this->view = new View();
        $this->orderModel = new Order();
        Session::start();
    }

    /**
     * Place a new order
     */
    public function place() {
        // Check if user is logged in
        Auth::requireAuth();

        // Get form data
        $vendorId = $_POST['vendor_id'] ?? null;
        $paymentMethod = $_POST['payment_method'] ?? 'cash_on_delivery';
        $deliveryAddress = $_POST['delivery_address'] ?? '';
        $notes = $_POST['notes'] ?? '';

        if (!$vendorId || empty($deliveryAddress)) {
            Session::setFlash('error', 'Please fill in all required fields');
            header('Location: /cart/checkout');
            exit;
        }

        // Get cart items for this vendor
        $cartModel = new Cart();
        $cartItems = $cartModel->getUserCartWithProducts(Auth::id());

        // Filter items by vendor
        $vendorItems = array_filter($cartItems, function($item) use ($vendorId) {
            return $item['vendor_id'] == $vendorId;
        });

        if (empty($vendorItems)) {
            Session::setFlash('error', 'No items found for this vendor');
            header('Location: /cart/checkout');
            exit;
        }

        // Calculate total price
        $totalPrice = 0;
        $orderItems = [];

        foreach ($vendorItems as $item) {
            $totalPrice += $item['price'] * $item['quantity'];

            $orderItems[] = [
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price']
            ];
        }

        // Create order data
        $orderData = [
            'customer_id' => Auth::id(),
            'vendor_id' => $vendorId,
            'total_price' => $totalPrice,
            'status' => 'pending',
            'payment_method' => $paymentMethod,
            'payment_status' => 'pending',
            'delivery_address' => $deliveryAddress,
            'notes' => $notes
        ];

        // Create order with items
        try {
            $orderId = $this->orderModel->createWithItems($orderData, $orderItems);

            // Remove ordered items from cart
            foreach ($vendorItems as $item) {
                $cartModel->removeItem($item['cart_id']);
            }

            Session::setFlash('success', 'Order placed successfully');
            header("Location: /orders/{$orderId}");
            exit;
        } catch (\Exception $e) {
            Session::setFlash('error', 'Failed to place order: ' . $e->getMessage());
            header('Location: /cart/checkout');
            exit;
        }
    }

    /**
     * List user's orders
     */
    public function list() {
        // Check if user is logged in
        Auth::requireAuth();

        // Get user's orders
        $orders = $this->orderModel->getByCustomerId(Auth::id());

        $this->view->render("orders.list", [
            "title" => "Your Orders",
            "orders" => $orders
        ]);
    }

    /**
     * Show order details
     */
    public function details($id) {
        // Check if user is logged in
        Auth::requireAuth();

        // Get order with details
        $order = $this->orderModel->getWithDetails($id);

        if (!$order) {
            Session::setFlash('error', 'Order not found');
            header('Location: /orders');
            exit;
        }

        // Check if user is authorized to view this order
        if (Auth::hasRole('customer') && $order['customer_id'] != Auth::id()) {
            Session::setFlash('error', 'You are not authorized to view this order');
            header('Location: /orders');
            exit;
        }

        if (Auth::hasRole('vendor')) {
            // Get vendor details for the current user
            $userModel = new User();
            $vendor = $userModel->getVendorWithDetails(Auth::id());

            if (!$vendor || $order['vendor_id'] != $vendor['id']) {
                Session::setFlash('error', 'You are not authorized to view this order');
                header('Location: /vendor/orders');
                exit;
            }
        }

        $this->view->render("orders.details", [
            "title" => "Order #{$id}",
            "order" => $order
        ]);
    }

    /**
     * Cancel an order
     */
    public function cancel() {
        // Check if user is logged in
        Auth::requireAuth();

        $orderId = $_POST['order_id'] ?? null;

        if (!$orderId) {
            Session::setFlash('error', 'Invalid order');
            header('Location: /orders');
            exit;
        }

        // Get order
        $order = $this->orderModel->find($orderId);

        if (!$order) {
            Session::setFlash('error', 'Order not found');
            header('Location: /orders');
            exit;
        }

        // Check if user is authorized to cancel this order
        if (Auth::hasRole('customer') && $order['customer_id'] != Auth::id()) {
            Session::setFlash('error', 'You are not authorized to cancel this order');
            header('Location: /orders');
            exit;
        }

        // Check if order can be cancelled
        if ($order['status'] != 'pending' && $order['status'] != 'preparing') {
            Session::setFlash('error', 'This order cannot be cancelled');
            header("Location: /orders/{$orderId}");
            exit;
        }

        // Cancel order
        $this->orderModel->updateStatus($orderId, 'cancelled');

        Session::setFlash('success', 'Order cancelled successfully');
        header("Location: /orders/{$orderId}");
        exit;
    }
}
