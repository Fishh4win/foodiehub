@extends('layouts.main')

@section('title', $title)

@section('content')
<div class="row">
    <!-- Product Details -->
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
            <div class="row g-0">
                <div class="col-md-5">
                    @if($product['image'])
                        <img src="/{{ $product['image'] }}" class="img-fluid rounded-start h-100" style="object-fit: cover;" alt="{{ $product['name'] }}">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center h-100" style="min-height: 300px;">
                            <i class="fas fa-hamburger fa-5x text-secondary"></i>
                        </div>
                    @endif
                </div>
                <div class="col-md-7">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h2 class="card-title">{{ $product['name'] }}</h2>
                                <p class="text-muted mb-2">{{ $product['category_name'] ?? 'Uncategorized' }}</p>
                            </div>
                            <div class="product-price fs-3">₱{{ number_format($product['price'], 2) }}</div>
                        </div>

                        <div class="mb-3">
                            <div class="rating">
                                @php
                                    $rating = isset($ratingData['avg_rating']) ? round($ratingData['avg_rating']) : 0;
                                @endphp
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $rating)
                                        <i class="fas fa-star"></i>
                                    @else
                                        <i class="far fa-star"></i>
                                    @endif
                                @endfor
                                <span class="rating-count">({{ $ratingData['review_count'] ?? 0 }} reviews)</span>
                            </div>
                        </div>

                        <p class="card-text">{{ $product['description'] }}</p>

                        @if(isset($product['preparation_time']) && $product['preparation_time'])
                            <p class="card-text">
                                <i class="fas fa-clock me-1"></i> Preparation time: {{ $product['preparation_time'] }} minutes
                            </p>
                        @endif

                        <div class="d-flex align-items-center mt-4">
                            <a href="/products?vendor={{ $product['vendor_id'] }}" class="text-decoration-none">
                                <div class="d-flex align-items-center">
                                    @if(isset($product['logo']) && $product['logo'])
                                        <img src="/{{ $product['logo'] }}" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;" alt="{{ $product['business_name'] }}">
                                    @else
                                        <div class="bg-light rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="fas fa-store text-secondary"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="mb-0 fw-bold">{{ $product['business_name'] }}</p>
                                        <p class="mb-0 small text-muted">{{ $product['vendor_name'] }}</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white">
                @if(\App\Core\Auth::check() && \App\Core\Auth::hasRole('customer'))
                    <form action="/cart/add" method="POST">
                        <input type="hidden" name="product_id" value="{{ $product['id'] }}">
                        <input type="hidden" name="redirect" value="/products/{{ $product['id'] }}">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <button type="button" class="btn btn-outline-secondary quantity-minus">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <input type="number" class="form-control text-center quantity-input" name="quantity" value="1" min="1">
                                    <button type="button" class="btn btn-outline-secondary quantity-plus">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-cart-plus me-2"></i> Add to Cart
                                </button>
                            </div>
                        </div>
                    </form>
                @elseif(!\App\Core\Auth::check())
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i> Please <a href="/login" class="alert-link">login</a> to add this item to your cart.
                    </div>
                @endif
            </div>
        </div>

        <!-- Reviews Section -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h4 class="mb-0">Customer Reviews</h4>
            </div>
            <div class="card-body">
                @if(count($reviews) > 0)
                    @foreach($reviews as $review)
                        <div class="mb-4 pb-4 border-bottom">
                            <div class="d-flex align-items-center mb-2">
                                @if(isset($review['profile_image']) && $review['profile_image'])
                                    <img src="/{{ $review['profile_image'] }}" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;" alt="{{ $review['user_name'] }}">
                                @else
                                    <div class="bg-light rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="fas fa-user text-secondary"></i>
                                    </div>
                                @endif
                                <div>
                                    <p class="mb-0 fw-bold">{{ $review['user_name'] }}</p>
                                    <p class="mb-0 small text-muted">{{ date('M d, Y', strtotime($review['created_at'])) }}</p>
                                </div>
                            </div>
                            <div class="rating mb-2">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $review['rating'])
                                        <i class="fas fa-star"></i>
                                    @else
                                        <i class="far fa-star"></i>
                                    @endif
                                @endfor
                            </div>
                            <p class="mb-0">{{ $review['comment'] }}</p>
                        </div>
                    @endforeach
                @else
                    <p class="text-muted">No reviews yet. Be the first to review this product!</p>
                @endif

                @if(\App\Core\Auth::check() && \App\Core\Auth::hasRole('customer') && !$userHasReviewed)
                    <div class="mt-4">
                        <h5>Write a Review</h5>
                        <form action="/products/review" method="POST">
                            <input type="hidden" name="product_id" value="{{ $product['id'] }}">
                            <div class="mb-3">
                                <label class="form-label">Rating</label>
                                <div class="rating-input mb-2">
                                    <input type="hidden" name="rating" value="0">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="far fa-star rating-star fs-3 me-1" style="cursor: pointer;"></i>
                                    @endfor
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="comment" class="form-label">Comment</label>
                                <textarea class="form-control" id="comment" name="comment" rows="3" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit Review</button>
                        </form>
                    </div>
                @elseif(\App\Core\Auth::check() && \App\Core\Auth::hasRole('customer') && $userHasReviewed)
                    <div class="alert alert-info mt-4">
                        <i class="fas fa-info-circle me-2"></i> You have already reviewed this product.
                    </div>
                @elseif(!\App\Core\Auth::check())
                    <div class="alert alert-info mt-4">
                        <i class="fas fa-info-circle me-2"></i> Please <a href="/login" class="alert-link">login</a> to write a review.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Related Products -->
        @if(count($relatedProducts) > 0)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h4 class="mb-0">Related Products</h4>
                </div>
                <div class="card-body">
                    @foreach($relatedProducts as $relatedProduct)
                        <div class="d-flex mb-3 pb-3 border-bottom">
                            @if(isset($relatedProduct['image']) && $relatedProduct['image'])
                                <img src="/{{ $relatedProduct['image'] }}" class="rounded me-3" style="width: 80px; height: 80px; object-fit: cover;" alt="{{ $relatedProduct['name'] }}">
                            @else
                                <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                    <i class="fas fa-hamburger text-secondary"></i>
                                </div>
                            @endif
                            <div>
                                <h6 class="mb-1">{{ $relatedProduct['name'] }}</h6>
                                <div class="product-price mb-2">₱{{ number_format($relatedProduct['price'], 2) }}</div>
                                <a href="/products/{{ $relatedProduct['id'] }}" class="btn btn-sm btn-outline-primary">View</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Quantity buttons
        const minusBtn = document.querySelector('.quantity-minus');
        const plusBtn = document.querySelector('.quantity-plus');
        const quantityInput = document.querySelector('.quantity-input');

        if (minusBtn && plusBtn && quantityInput) {
            minusBtn.addEventListener('click', function() {
                let value = parseInt(quantityInput.value);
                if (value > 1) {
                    quantityInput.value = value - 1;
                }
            });

            plusBtn.addEventListener('click', function() {
                let value = parseInt(quantityInput.value);
                quantityInput.value = value + 1;
            });
        }

        // Rating stars
        const ratingStars = document.querySelectorAll('.rating-star');
        const ratingInput = document.querySelector('input[name="rating"]');

        if (ratingStars.length && ratingInput) {
            ratingStars.forEach((star, index) => {
                star.addEventListener('click', function() {
                    const rating = index + 1;
                    ratingInput.value = rating;

                    // Update stars
                    ratingStars.forEach((s, i) => {
                        if (i < rating) {
                            s.classList.remove('far');
                            s.classList.add('fas');
                        } else {
                            s.classList.remove('fas');
                            s.classList.add('far');
                        }
                    });
                });
            });
        }
    });
</script>
@endsection
