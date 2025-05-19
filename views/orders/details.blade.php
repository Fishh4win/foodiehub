@extends('layouts.main')

@section('title', $title)

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Order #{{ $order['id'] }}</h4>
                <span class="status-badge status-{{ $order['status'] }}">
                    {{ ucfirst(str_replace('_', ' ', $order['status'])) }}
                </span>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5>Order Information</h5>
                        <p class="mb-1"><strong>Order Date:</strong> {{ date('M d, Y h:i A', strtotime($order['created_at'])) }}</p>
                        <p class="mb-1"><strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $order['payment_method'])) }}</p>
                        <p class="mb-1"><strong>Payment Status:</strong> {{ ucfirst($order['payment_status']) }}</p>
                        <p class="mb-0"><strong>Delivery Address:</strong> {{ $order['delivery_address'] }}</p>
                    </div>
                    <div class="col-md-6">
                        <h5>Vendor Information</h5>
                        <p class="mb-1"><strong>Vendor:</strong> {{ $order['vendor_business_name'] }}</p>
                        <p class="mb-0"><strong>Customer:</strong> {{ $order['customer_name'] }}</p>
                    </div>
                </div>

                @if($order['notes'])
                    <div class="alert alert-info mb-4">
                        <strong>Order Notes:</strong> {{ $order['notes'] }}
                    </div>
                @endif

                <h5>Order Items</h5>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order['items'] as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if(isset($item['product_image']) && $item['product_image'])
                                                <img src="/{{ $item['product_image'] }}" class="rounded me-2" style="width: 50px; height: 50px; object-fit: cover;" alt="{{ $item['product_name'] }}">
                                            @else
                                                <div class="bg-light rounded me-2 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                    <i class="fas fa-hamburger text-secondary"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <h6 class="mb-0">{{ $item['product_name'] }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>₱{{ number_format($item['price'], 2) }}</td>
                                    <td>{{ $item['quantity'] }}</td>
                                    <td class="text-end">₱{{ number_format($item['price'] * $item['quantity'], 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                <td class="text-end"><strong>₱{{ number_format($order['total_price'], 2) }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="/orders" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i> Back to Orders
                    </a>

                    @if(\App\Core\Auth::hasRole('customer') && ($order['status'] == 'pending' || $order['status'] == 'preparing'))
                        <form action="/orders/cancel" method="POST" onsubmit="return confirm('Are you sure you want to cancel this order?');">
                            <input type="hidden" name="order_id" value="{{ $order['id'] }}">
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-times me-2"></i> Cancel Order
                            </button>
                        </form>
                    @elseif(\App\Core\Auth::hasRole('vendor') && $order['status'] != 'cancelled' && $order['status'] != 'delivered')
                        <form action="/vendor/orders/update-status" method="POST">
                            <input type="hidden" name="order_id" value="{{ $order['id'] }}">
                            <div class="input-group">
                                <select class="form-select" name="status" required>
                                    <option value="pending" {{ $order['status'] == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="preparing" {{ $order['status'] == 'preparing' ? 'selected' : '' }}>Preparing</option>
                                    <option value="out_for_delivery" {{ $order['status'] == 'out_for_delivery' ? 'selected' : '' }}>Out for Delivery</option>
                                    <option value="delivered" {{ $order['status'] == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                </select>
                                <button type="submit" class="btn btn-primary">Update Status</button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Order Timeline</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-shopping-cart me-2 text-primary"></i> Order Placed
                        </div>
                        <small>{{ date('M d, Y h:i A', strtotime($order['created_at'])) }}</small>
                    </li>

                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-utensils me-2 {{ $order['status'] == 'preparing' || $order['status'] == 'out_for_delivery' || $order['status'] == 'delivered' ? 'text-primary' : 'text-muted' }}"></i>
                            Preparing
                        </div>
                        <small>{{ $order['status'] == 'preparing' || $order['status'] == 'out_for_delivery' || $order['status'] == 'delivered' ? date('M d, Y h:i A', strtotime($order['updated_at'])) : '-' }}</small>
                    </li>

                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-truck me-2 {{ $order['status'] == 'out_for_delivery' || $order['status'] == 'delivered' ? 'text-primary' : 'text-muted' }}"></i>
                            Out for Delivery
                        </div>
                        <small>{{ $order['status'] == 'out_for_delivery' || $order['status'] == 'delivered' ? date('M d, Y h:i A', strtotime($order['updated_at'])) : '-' }}</small>
                    </li>

                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-check-circle me-2 {{ $order['status'] == 'delivered' ? 'text-primary' : 'text-muted' }}"></i>
                            Delivered
                        </div>
                        <small>{{ $order['status'] == 'delivered' ? date('M d, Y h:i A', strtotime($order['updated_at'])) : '-' }}</small>
                    </li>

                    @if($order['status'] == 'cancelled')
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-times-circle me-2 text-danger"></i>
                                Cancelled
                            </div>
                            <small>{{ date('M d, Y h:i A', strtotime($order['updated_at'])) }}</small>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
