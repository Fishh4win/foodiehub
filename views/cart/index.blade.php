@extends('layouts.main')

@section('title', $title)

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h4 class="mb-0">Your Cart</h4>
            </div>
            <div class="card-body">
                @if(count($cartItems) > 0)
                    @foreach($cartItems as $item)
                        <div class="cart-item">
                            <div class="row align-items-center">
                                <div class="col-md-2 col-4">
                                    @if(isset($item['image']) && $item['image'])
                                        <img src="/{{ $item['image'] }}" class="cart-item-img img-fluid" alt="{{ $item['name'] }}">
                                    @else
                                        <div class="cart-item-img bg-light d-flex align-items-center justify-content-center">
                                            <i class="fas fa-hamburger text-secondary"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-4 col-8">
                                    <h5 class="mb-1">{{ $item['name'] }}</h5>
                                    <p class="text-muted mb-0">{{ $item['business_name'] }}</p>
                                </div>
                                <div class="col-md-2 col-4 mt-3 mt-md-0">
                                    <div class="product-price">₱{{ number_format($item['price'], 2) }}</div>
                                </div>
                                <div class="col-md-2 col-4 mt-3 mt-md-0">
                                    <form action="/cart/update" method="POST" class="cart-quantity-form">
                                        <input type="hidden" name="cart_id" value="{{ $item['cart_id'] }}">
                                        <div class="input-group input-group-sm">
                                            <button type="button" class="btn btn-outline-secondary quantity-minus">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <input type="number" class="form-control text-center quantity-input" name="quantity" value="{{ $item['quantity'] }}" min="1">
                                            <button type="button" class="btn btn-outline-secondary quantity-plus">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-md-2 col-4 mt-3 mt-md-0 text-end">
                                    <div class="mb-2">₱{{ number_format($item['price'] * $item['quantity'], 2) }}</div>
                                    <form action="/cart/remove" method="POST" class="d-inline">
                                        <input type="hidden" name="cart_id" value="{{ $item['cart_id'] }}">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
                        <h5>Your cart is empty</h5>
                        <p class="text-muted">Add some delicious food to your cart!</p>
                        <a href="/products" class="btn btn-primary mt-3">Browse Food</a>
                    </div>
                @endif
            </div>
            @if(count($cartItems) > 0)
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <form action="/cart/clear" method="POST">
                            <button type="submit" class="btn btn-outline-danger">
                                <i class="fas fa-trash me-2"></i> Clear Cart
                            </button>
                        </form>
                        <a href="/products" class="btn btn-outline-primary">
                            <i class="fas fa-plus me-2"></i> Add More Items
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h4 class="mb-0">Order Summary</h4>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal:</span>
                    <span>₱{{ number_format($cartTotal, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Delivery Fee:</span>
                    <span>₱0.00</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-3">
                    <span class="fw-bold">Total:</span>
                    <span class="cart-total">₱{{ number_format($cartTotal, 2) }}</span>
                </div>

                @if(count($cartItems) > 0)
                    <div class="d-grid">
                        <a href="/cart/checkout" class="btn btn-primary">
                            <i class="fas fa-shopping-bag me-2"></i> Proceed to Checkout
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
