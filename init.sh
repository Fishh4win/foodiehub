#!/bin/bash

echo "🍽️ Initializing Food Marketplace PHP MVC Project..."

# Create folder structure
echo "📁 Creating folders..."
mkdir -p app/controllers
mkdir -p app/models
mkdir -p app/core
mkdir -p public
mkdir -p views/layouts
mkdir -p views/home
mkdir -p views/products
mkdir -p views/orders
mkdir -p views/auth
mkdir -p views/dashboard
mkdir -p views/errors
mkdir -p cache

# Initialize composer
echo "📦 Installing Blade..."
composer init -n
composer require duncan3dc/blade

# .gitignore
echo "🛡️ Adding .gitignore..."
cat > .gitignore <<EOL
/vendor/
/cache/
/.env
EOL

# Composer autoloading config
cat > composer.json <<EOL
{
    "autoload": {
        "psr-4": {
            "App\\\\": "app/"
        }
    },
    "require": {
        "duncan3dc/blade": "^5.9"
    }
}
EOL

# Router class
cat > app/core/Router.php <<'EOL'
<?php
namespace App\Core;

class Router {
    private $routes = [];

    public function get($uri, $action) {
        $this->routes['GET'][$uri] = $action;
    }

    public function post($uri, $action) {
        $this->routes['POST'][$uri] = $action;
    }

    public function dispatch($method, $uri) {
        $uri = parse_url($uri, PHP_URL_PATH);
        $action = $this->routes[$method][$uri] ?? null;

        if (!$action) {
            http_response_code(404);
            echo (new View)->render("errors.404");
            return;
        }

        [$controller, $method] = explode("@", $action);
        $controller = "App\\Controllers\\$controller";
        (new $controller)->$method();
    }
}
EOL

# View class
cat > app/core/View.php <<'EOL'
<?php
namespace App\Core;

use duncan3dc\Laravel\BladeInstance;

class View {
    private $blade;

    public function __construct() {
        $this->blade = new BladeInstance(__DIR__ . "/../../views", __DIR__ . "/../../cache");
    }

    public function render($view, $data = []) {
        echo $this->blade->render($view, $data);
    }
}
EOL

# Database class (basic)
cat > app/core/Database.php <<'EOL'
<?php
namespace App\Core;

use PDO;

class Database {
    protected $pdo;

    public function __construct() {
        $this->pdo = new PDO("mysql:host=localhost;dbname=food_marketplace", "root", "");
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function query($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}
EOL

# HomeController
cat > app/controllers/HomeController.php <<'EOL'
<?php
namespace App\Controllers;

use App\Core\View;

class HomeController {
    public function index() {
        (new View)->render("home.index", ["title" => "Welcome to FoodieHub"]);
    }
}
EOL

# ProductController
cat > app/controllers/ProductController.php <<'EOL'
<?php
namespace App\Controllers;

use App\Core\View;

class ProductController {
    public function list() {
        // Simulated products
        $products = [
            ["name" => "Veg Burger", "price" => 5],
            ["name" => "Chicken Pizza", "price" => 8]
        ];
        (new View)->render("products.list", ["title" => "Browse Food", "products" => $products]);
    }
}
EOL

# index.php
cat > public/index.php <<'EOL'
<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;

$router = new Router;
$router->get("/", "HomeController@index");
$router->get("/products", "ProductController@list");

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
EOL

# Layout template
cat > views/layouts/main.blade.php <<'EOL'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield("title", "FoodieHub")</title>
</head>
<body>
    <header>
        <h1>FoodieHub</h1>
        <nav>
            <a href="/">Home</a> | 
            <a href="/products">Browse Food</a>
        </nav>
    </header>
    <main>
        @yield("content")
    </main>
</body>
</html>
EOL

# Home page
cat > views/home/index.blade.php <<'EOL'
@extends("layouts.main")

@section("title", $title)

@section("content")
    <h2>Welcome to FoodieHub</h2>
    <p>Order from your favorite local food vendors!</p>
@endsection
EOL

# Product list
cat > views/products/list.blade.php <<'EOL'
@extends("layouts.main")

@section("title", $title)

@section("content")
    <h2>Available Food</h2>
    <ul>
        @foreach($products as $product)
            <li>{{ $product["name"] }} - ${{ $product["price"] }}</li>
        @endforeach
    </ul>
@endsection
EOL

# 404 page
cat > views/errors/404.blade.php <<'EOL'
@extends("layouts.main")

@section("title", "404 Not Found")

@section("content")
    <h2>Page Not Found</h2>
    <p>Sorry, we couldn't find what you're looking for.</p>
@endsection
EOL

# Autoload classes
echo "🔃 Dumping autoload..."
composer dump-autoload

echo "✅ Food Marketplace project initialized!"
echo "👉 Run your app with: php -S localhost:8000 -t public"
