<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield("title", "Admin Dashboard") - FoodieHub Admin</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/admin.css">

    @yield('styles')
</head>
<body>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div class="bg-dark text-white" id="sidebar-wrapper">
            <div class="sidebar-heading p-3 border-bottom">
                <i class="fas fa-utensils me-2"></i>FoodieHub Admin
            </div>
            <div class="list-group list-group-flush">
                <a href="/admin/dashboard" class="list-group-item list-group-item-action bg-transparent text-white {{ is_current_url('/admin/dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                </a>
                <a href="/admin/users" class="list-group-item list-group-item-action bg-transparent text-white {{ is_current_url('/admin/users') ? 'active' : '' }}">
                    <i class="fas fa-users me-2"></i>Users
                </a>
                <a href="/admin/vendors" class="list-group-item list-group-item-action bg-transparent text-white {{ is_current_url('/admin/vendors') ? 'active' : '' }}">
                    <i class="fas fa-store me-2"></i>Vendors
                </a>
                <a href="/admin/categories" class="list-group-item list-group-item-action bg-transparent text-white {{ is_current_url('/admin/categories') ? 'active' : '' }}">
                    <i class="fas fa-tags me-2"></i>Categories
                </a>
                <a href="/admin/products" class="list-group-item list-group-item-action bg-transparent text-white {{ is_current_url('/admin/products') ? 'active' : '' }}">
                    <i class="fas fa-hamburger me-2"></i>Products
                </a>
                <a href="/admin/orders" class="list-group-item list-group-item-action bg-transparent text-white {{ is_current_url('/admin/orders') ? 'active' : '' }}">
                    <i class="fas fa-shopping-cart me-2"></i>Orders
                </a>
                <a href="/" class="list-group-item list-group-item-action bg-transparent text-white">
                    <i class="fas fa-home me-2"></i>Back to Site
                </a>
                <a href="/logout" class="list-group-item list-group-item-action bg-transparent text-danger">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </a>
            </div>
        </div>

        <!-- Page Content -->
        <div id="page-content-wrapper" class="bg-light">
            <nav class="navbar navbar-expand-lg navbar-light bg-white py-3 px-4 shadow-sm">
                <div class="d-flex align-items-center">
                    <i class="fas fa-bars me-3 fs-4" id="menu-toggle"></i>
                    <h4 class="mb-0">@yield("title", "Admin Dashboard")</h4>
                </div>

                <div class="ms-auto">
                    <div class="dropdown">
                        <a class="dropdown-toggle d-flex align-items-center text-decoration-none" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
                                <i class="fas fa-user-shield text-white"></i>
                            </div>
                            <div>
                                <span class="d-none d-sm-inline">{{ \App\Core\Auth::user()['name'] }}</span>
                                <small class="d-block text-muted">Administrator</small>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuLink">
                            <li><a class="dropdown-item" href="/logout"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- Flash Messages -->
            <div class="container-fluid px-4 mt-3">
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
            <div class="container-fluid px-4 py-3">
                @yield("content")
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JS -->
    <script src="/js/script.js"></script>
    <script src="/js/admin.js"></script>

    <script>
        // Toggle sidebar
        document.getElementById("menu-toggle").addEventListener("click", function(e) {
            e.preventDefault();
            document.getElementById("wrapper").classList.toggle("toggled");
        });
    </script>

    @yield('scripts')
</body>
</html>
