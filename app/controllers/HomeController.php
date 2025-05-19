<?php
namespace App\Controllers;

use App\Core\View;
use App\Core\Session;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;

class HomeController {
    private $view;

    public function __construct() {
        $this->view = new View();
        Session::start();
    }

    /**
     * Display the homepage with featured products and vendors
     */
    public function index() {
        // Get featured products
        $productModel = new Product();
        $featuredProducts = $productModel->getTopRated(8);

        // Get featured vendors
        $userModel = new User();
        $featuredVendors = $userModel->getFeaturedVendors();

        // Get categories
        $categoryModel = new Category();
        $categories = $categoryModel->getAllWithAvailableProductCounts();

        $this->view->render("home.index", [
            "title" => "Welcome to FoodieHub",
            "featuredProducts" => $featuredProducts,
            "featuredVendors" => $featuredVendors,
            "categories" => $categories
        ]);
    }

    /**
     * Display the about page
     */
    public function about() {
        $this->view->render("home.about", [
            "title" => "About FoodieHub"
        ]);
    }

    /**
     * Display the contact page
     */
    public function contact() {
        $this->view->render("home.contact", [
            "title" => "Contact Us"
        ]);
    }

    /**
     * Process contact form submission
     */
    public function sendContactForm() {
        // Get form data
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $subject = $_POST['subject'] ?? '';
        $message = $_POST['message'] ?? '';

        // Validate form data
        if (empty($name) || empty($email) || empty($subject) || empty($message)) {
            Session::setFlash('error', 'Please fill in all required fields');
            header('Location: /contact');
            exit;
        }

        // In a real application, you would send an email here
        // For now, we'll just simulate a successful submission

        Session::setFlash('success', 'Thank you for your message! We will get back to you soon.');
        header('Location: /contact');
        exit;
    }
}
