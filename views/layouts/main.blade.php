<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield("title", "FoodieHub") - Food Marketplace</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="/css/style.css">

    @yield('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="fas fa-utensils me-2"></i>FoodieHub
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/products">Browse Food</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/contact">Contact</a>
                    </li>
                </ul>

                <!-- Search Form -->
                <form class="d-flex me-2" action="/search" method="GET">
                    <input class="form-control me-2" type="search" name="q" placeholder="Search food...">
                    <button class="btn btn-light" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </form>

                <ul class="navbar-nav">
                    @if(\App\Core\Auth::check())
                        @if(\App\Core\Auth::hasRole('customer'))
                            <li class="nav-item">
                                <a class="nav-link" href="/cart">
                                    <i class="fas fa-shopping-cart"></i> Cart
                                </a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-user"></i> {{ \App\Core\Auth::user()['name'] }}
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="/orders">My Orders</a></li>
                                    <li><a class="dropdown-item" href="/profile">Profile</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="/logout">Logout</a></li>
                                </ul>
                            </li>
                        @elseif(\App\Core\Auth::hasRole('vendor'))
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-store"></i> Vendor
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="/vendor/dashboard">Dashboard</a></li>
                                    <li><a class="dropdown-item" href="/vendor/products">Products</a></li>
                                    <li><a class="dropdown-item" href="/vendor/orders">Orders</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="/logout">Logout</a></li>
                                </ul>
                            </li>
                        @elseif(\App\Core\Auth::hasRole('admin'))
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-user-shield"></i> Admin
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="/admin/dashboard">Dashboard</a></li>
                                    <li><a class="dropdown-item" href="/admin/users">Users</a></li>
                                    <li><a class="dropdown-item" href="/admin/vendors">Vendors</a></li>
                                    <li><a class="dropdown-item" href="/admin/categories">Categories</a></li>
                                    <li><a class="dropdown-item" href="/admin/orders">Orders</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="/logout">Logout</a></li>
                                </ul>
                            </li>
                        @endif
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="/login">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/register">Register</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/vendor/register">Become a Vendor</a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <div class="container mt-3">
        @if(\App\Core\Session::hasFlash('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ \App\Core\Session::getFlash('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(\App\Core\Session::hasFlash('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ \App\Core\Session::getFlash('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    <!-- Main Content -->
    <main class="container py-4">
        @yield("content")
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>FoodieHub</h5>
                    <p>Your favorite food marketplace platform connecting local food vendors with hungry customers.</p>
                </div>
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="/" class="text-white">Home</a></li>
                        <li><a href="/products" class="text-white">Browse Food</a></li>
                        <li><a href="/about" class="text-white">About Us</a></li>
                        <li><a href="/contact" class="text-white">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Contact Us</h5>
                    <address>
                        <i class="fas fa-map-marker-alt"></i> 123 Food Street, Cuisine City<br>
                        <i class="fas fa-phone"></i> (123) 456-7890<br>
                        <i class="fas fa-envelope"></i> info@foodiehub.com
                    </address>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <p>&copy; {{ date('Y') }} FoodieHub. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JS -->
    <script src="/js/script.js"></script>

    @yield('scripts')
</body>
</html>
