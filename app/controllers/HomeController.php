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
}
