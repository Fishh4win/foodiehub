<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Session;
use App\Models\Product;
use App\Models\Category;
use App\Models\Review;
use App\Models\Cart;
use App\Core\Auth;

class ProductController {
    private $view;
    private $productModel;
    private $categoryModel;

    public function __construct() {
        $this->view = new View();
        $this->productModel = new Product();
        $this->categoryModel = new Category();
        Session::start();
    }

    /**
     * List all products with optional filtering
     */
    public function list() {
        // Get filter parameters
        $categoryId = $_GET['category'] ?? null;
        $minPrice = $_GET['min_price'] ?? null;
        $maxPrice = $_GET['max_price'] ?? null;
        $vendorId = $_GET['vendor'] ?? null;
        $search = $_GET['search'] ?? null;

        // Build filters array
        $filters = [
            'category_id' => $categoryId,
            'min_price' => $minPrice,
            'max_price' => $maxPrice,
            'vendor_id' => $vendorId,
            'is_available' => true
        ];

        // Get products based on filters
        if ($search) {
            $products = $this->productModel->search($search);
        } else {
            $products = $this->productModel->filter($filters);
        }

        // Get all categories for filter sidebar
        $categories = $this->categoryModel->all();

        $this->view->render("products.list", [
            "title" => "Browse Food",
            "products" => $products,
            "categories" => $categories,
            "filters" => $filters,
            "search" => $search
        ]);
    }

    /**
     * Show product details
     */
    public function details($id) {
        // Get product with vendor details
        $product = $this->productModel->getWithVendorDetails($id);

        if (!$product) {
            Session::setFlash('error', 'Product not found');
            header('Location: /products');
            exit;
        }

        // Get reviews
        $reviewModel = new Review();
        $reviews = $reviewModel->getWithUserDetails($id);
        $ratingData = $reviewModel->getAverageRating($id);

        // Check if user has already reviewed this product
        $userHasReviewed = false;
        if (Auth::check()) {
            $userHasReviewed = $reviewModel->hasUserReviewed(Auth::id(), $id);
        }

        // Get related products from same category
        $relatedProducts = [];
        if ($product['category_id']) {
            $relatedProducts = $this->productModel->getByCategoryId($product['category_id']);

            // Remove current product from related products
            $relatedProducts = array_filter($relatedProducts, function($item) use ($id) {
                return $item['id'] != $id;
            });

            // Limit to 4 related products
            $relatedProducts = array_slice($relatedProducts, 0, 4);
        }

        $this->view->render("products.details", [
            "title" => $product['name'],
            "product" => $product,
            "reviews" => $reviews,
            "ratingData" => $ratingData,
            "userHasReviewed" => $userHasReviewed,
            "relatedProducts" => $relatedProducts
        ]);
    }

    /**
     * Add product to cart
     */
    public function addToCart() {
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
        $product = $this->productModel->find($productId);
        if (!$product || !$product['is_available']) {
            Session::setFlash('error', 'Product is not available');
            header('Location: /products');
            exit;
        }

        // Add to cart
        $cartModel = new Cart();
        $cartModel->addItem(Auth::id(), $productId, $quantity);

        Session::setFlash('success', 'Product added to cart');
        header('Location: /cart');
        exit;
    }

    /**
     * Submit product review
     */
    public function submitReview() {
        // Check if user is logged in
        Auth::requireAuth();

        $productId = $_POST['product_id'] ?? null;
        $rating = $_POST['rating'] ?? null;
        $comment = $_POST['comment'] ?? '';

        if (!$productId || !$rating || $rating < 1 || $rating > 5) {
            Session::setFlash('error', 'Invalid review data');
            header("Location: /products/{$productId}");
            exit;
        }

        // Check if product exists
        $product = $this->productModel->find($productId);
        if (!$product) {
            Session::setFlash('error', 'Product not found');
            header('Location: /products');
            exit;
        }

        // Check if user has already reviewed this product
        $reviewModel = new Review();
        if ($reviewModel->hasUserReviewed(Auth::id(), $productId)) {
            Session::setFlash('error', 'You have already reviewed this product');
            header("Location: /products/{$productId}");
            exit;
        }

        // Create review
        $reviewModel->create([
            'user_id' => Auth::id(),
            'product_id' => $productId,
            'rating' => $rating,
            'comment' => $comment
        ]);

        Session::setFlash('success', 'Review submitted successfully');
        header("Location: /products/{$productId}");
        exit;
    }

    /**
     * Search products
     */
    public function search() {
        $keyword = $_GET['q'] ?? '';

        if (empty($keyword)) {
            header('Location: /products');
            exit;
        }

        $products = $this->productModel->search($keyword);
        $categories = $this->categoryModel->all();

        $this->view->render("products.list", [
            "title" => "Search Results: {$keyword}",
            "products" => $products,
            "categories" => $categories,
            "search" => $keyword
        ]);
    }
}
