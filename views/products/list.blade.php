@extends("layouts.main")

@section("title", $title)

@section("content")
<div class="row">
    <!-- Filters Sidebar -->
    <div class="col-lg-3 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Filters</h5>
            </div>
            <div class="card-body">
                <form id="product-filter-form" action="/products" method="GET">
                    @if($search)
                        <input type="hidden" name="search" value="{{ $search }}">
                    @endif

                    <div class="mb-3">
                        <label class="form-label fw-bold">Categories</label>
                        @foreach($categories as $category)
                            <div class="form-check">
                                <input class="form-check-input filter-input" type="radio" name="category"
                                    id="category-{{ $category['id'] }}" value="{{ $category['id'] }}"
                                    {{ isset($filters['category_id']) && $filters['category_id'] == $category['id'] ? 'checked' : '' }}>
                                <label class="form-check-label" for="category-{{ $category['id'] }}">
                                    {{ $category['name'] }}
                                </label>
                            </div>
                        @endforeach
                        <div class="form-check">
                            <input class="form-check-input filter-input" type="radio" name="category"
                                id="category-all" value=""
                                {{ !isset($filters['category_id']) || !$filters['category_id'] ? 'checked' : '' }}>
                            <label class="form-check-label" for="category-all">
                                All Categories
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Price Range</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="number" class="form-control form-control-sm filter-input" name="min_price"
                                    placeholder="Min" value="{{ $filters['min_price'] ?? '' }}">
                            </div>
                            <div class="col-6">
                                <input type="number" class="form-control form-control-sm filter-input" name="max_price"
                                    placeholder="Max" value="{{ $filters['max_price'] ?? '' }}">
                            </div>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="col-lg-9">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">{{ $title }}</h2>

            @if($search)
                <div>
                    <span class="text-muted">Search results for: </span>
                    <span class="fw-bold">{{ $search }}</span>
                </div>
            @endif
        </div>

        @if(count($products) > 0)
            <div class="row">
                @foreach($products as $product)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card product-card h-100">
                            @if(isset($product['image']) && $product['image'])
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
                                    <div class="product-price">${{ number_format($product['price'], 2) }}</div>
                                    <div class="rating">
                                        @php
                                            $rating = isset($product['avg_rating']) ? round($product['avg_rating']) : 0;
                                        @endphp
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $rating)
                                                <i class="fas fa-star"></i>
                                            @else
                                                <i class="far fa-star"></i>
                                            @endif
                                        @endfor
                                    </div>
                                </div>
                                <p class="card-text">{{ isset($product['description']) ? \Illuminate\Support\Str::limit($product['description'], 60) : '' }}</p>
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
        @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i> No products found. Try adjusting your filters or search criteria.
            </div>
        @endif
    </div>
</div>
@endsection
