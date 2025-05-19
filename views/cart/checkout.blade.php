@extends('layouts.main')

@section('title', $title)

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h4 class="mb-0">Checkout</h4>
            </div>
            <div class="card-body">
                @if(count($cartItemsByVendor) > 0)
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> You have items from multiple vendors. Please place separate orders for each vendor.
                    </div>
                    
                    @foreach($cartItemsByVendor as $vendorId => $vendorData)
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">{{ $vendorData['business_name'] }}</h5>
                            </div>
                            <div class="card-body">
                                @foreach($vendorData['items'] as $item)
                                    <div class="d-flex mb-3 pb-3 border-bottom">
                                        <div class="flex-shrink-0">
                                            @if(isset($item['image']) && $item['image'])
                                                <img src="/{{ $item['image'] }}" class="rounded" style="width: 60px; height: 60px; object-fit: cover;" alt="{{ $item['name'] }}">
                                            @else
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                                    <i class="fas fa-hamburger text-secondary"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ms-3 flex-grow-1">
                                            <h6 class="mb-0">{{ $item['name'] }}</h6>
                                            <div class="d-flex justify-content-between align-items-center mt-2">
                                                <div class="text-muted">
                                                    <small>${{ number_format($item['price'], 2) }} x {{ $item['quantity'] }}</small>
                                                </div>
                                                <div class="fw-bold">${{ number_format($item['price'] * $item['quantity'], 2) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                
                                <form action="/orders/place" method="POST">
                                    <input type="hidden" name="vendor_id" value="{{ $vendorId }}">
                                    
                                    <div class="mb-3">
                                        <label for="delivery_address_{{ $vendorId }}" class="form-label">Delivery Address</label>
                                        <textarea class="form-control" id="delivery_address_{{ $vendorId }}" name="delivery_address" rows="3" required></textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="payment_method_{{ $vendorId }}" class="form-label">Payment Method</label>
                                        <select class="form-select" id="payment_method_{{ $vendorId }}" name="payment_method" required>
                                            <option value="cash_on_delivery">Cash on Delivery</option>
                                            <option value="credit_card">Credit Card</option>
                                            <option value="paypal">PayPal</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="notes_{{ $vendorId }}" class="form-label">Order Notes (Optional)</label>
                                        <textarea class="form-control" id="notes_{{ $vendorId }}" name="notes" rows="2"></textarea>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="fw-bold">Total:</span>
                                            @php
                                                $vendorTotal = 0;
                                                foreach($vendorData['items'] as $item) {
                                                    $vendorTotal += $item['price'] * $item['quantity'];
                                                }
                                            @endphp
                                            <span class="fs-5 ms-2">${{ number_format($vendorTotal, 2) }}</span>
                                        </div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-check me-2"></i> Place Order
                                        </button>
                                    </div>
                                </form>
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
                    <span>${{ number_format($cartTotal, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Delivery Fee:</span>
                    <span>$0.00</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-3">
                    <span class="fw-bold">Total:</span>
                    <span class="cart-total">${{ number_format($cartTotal, 2) }}</span>
                </div>
                
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle me-2"></i> Please place separate orders for each vendor.
                </div>
                
                <div class="d-grid mt-3">
                    <a href="/cart" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i> Back to Cart
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
