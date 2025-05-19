@extends("layouts.main")

@section("title", $title)

@section("content")
    <!-- Hero Section -->
    <div class="hero-section text-center">
        <div class="container">
            <h1>Discover Delicious Food Near You</h1>
            <p class="lead">Order from your favorite local food vendors with just a few clicks!</p>
            <a href="/products" class="btn btn-light btn-lg">Browse Food</a>
            <a href="/vendor/register" class="btn btn-outline-light btn-lg ms-2">Become a Vendor</a>
        </div>
    </div>

    <!-- Categories Section -->
    <section class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Food Categories</h2>
            <a href="/products" class="btn btn-outline-primary">View All</a>
        </div>

        <div class="row">
            @foreach($categories as $category)
                <div class="col-md-3 col-sm-6 mb-4">
                    <a href="/products?category={{ $category['id'] }}" class="text-decoration-none">
                        <div class="card h-100">
                            @if($category['image'])
                                <img src="/{{ $category['image'] }}" class="card-img-top" alt="{{ $category['name'] }}">
                            @else
                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 150px;">
                                    <i class="fas fa-utensils fa-3x text-secondary"></i>
                                </div>
                            @endif
                            <div class="card-body text-center">
                                <h5 class="card-title">{{ $category['name'] }}</h5>
                                <p class="card-text text-muted">{{ $category['product_count'] ?? 0 }} items</p>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Featured Products Section -->
    <section class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Top Rated Food</h2>
            <a href="/products" class="btn btn-outline-primary">View All</a>
        </div>

        <div class="row">
            @foreach($featuredProducts as $product)
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="card product-card h-100">
                        @if($product['image'])
                            <img src="/{{ $product['image'] }}" class="card-img-top" alt="{{ $product['name'] }}">
                        @else
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="fas fa-hamburger fa-3x text-secondary"></i>
                            </div>
                        @endif
                        <div class="card-body">
                            <div class="product-category mb-1">{{ $product['category_name'] ?? 'Uncategorized' }}</div>
                            <h5 class="card-title">{{ $product['name'] }}</h5>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="product-price">₱{{ number_format($product['price'], 2) }}</div>
                                <div class="rating">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= round($product['avg_rating'] ?? 0))
                                            <i class="fas fa-star"></i>
                                        @else
                                            <i class="far fa-star"></i>
                                        @endif
                                    @endfor
                                    <span class="rating-count">({{ $product['review_count'] ?? 0 }})</span>
                                </div>
                            </div>
                            <p class="card-text">{{ \Illuminate\Support\Str::limit($product['description'] ?? '', 60) }}</p>
                        </div>
                        <div class="card-footer bg-white">
                            <div class="d-grid">
                                <a href="/products/{{ $product['id'] }}" class="btn btn-primary">View Details</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Featured Vendors Section -->
    <section class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Featured Vendors</h2>
            <a href="/products" class="btn btn-outline-primary">View All</a>
        </div>

        <div class="row">
            @foreach($featuredVendors as $vendor)
                <div class="col-md-6 mb-4">
                    <div class="card vendor-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                @if($vendor['logo'])
                                    <img src="/{{ $vendor['logo'] }}" class="vendor-logo" alt="{{ $vendor['business_name'] }}">
                                @else
                                    <div class="vendor-logo bg-light d-flex align-items-center justify-content-center">
                                        <i class="fas fa-store fa-2x text-secondary"></i>
                                    </div>
                                @endif
                                <div>
                                    <h5 class="card-title mb-1">{{ $vendor['business_name'] }}</h5>
                                    <p class="card-text text-muted mb-2">{{ $vendor['location'] }}</p>
                                    <a href="/products?vendor={{ $vendor['id'] }}" class="btn btn-sm btn-outline-primary">View Menu</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="mb-5">
        <h2 class="text-center mb-4">How It Works</h2>

        <div class="row text-center">
            <div class="col-md-4 mb-4">
                <div class="p-4">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-search fa-2x"></i>
                    </div>
                    <h4>Browse Food</h4>
                    <p class="text-muted">Explore a wide variety of delicious food from local vendors.</p>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="p-4">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-shopping-cart fa-2x"></i>
                    </div>
                    <h4>Place Order</h4>
                    <p class="text-muted">Add items to your cart and place your order with just a few clicks.</p>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="p-4">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-utensils fa-2x"></i>
                    </div>
                    <h4>Enjoy Food</h4>
                    <p class="text-muted">Receive your food and enjoy a delicious meal from local vendors.</p>
                </div>
            </div>
        </div>
    </section>
@endsection
