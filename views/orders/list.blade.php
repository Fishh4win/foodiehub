@extends('layouts.main')

@section('title', $title)

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h4 class="mb-0">Your Orders</h4>
    </div>
    <div class="card-body">
        @if(count($orders) > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Vendor</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td>#{{ $order['id'] }}</td>
                                <td>{{ $order['vendor_business_name'] ?? 'Unknown Vendor' }}</td>
                                <td>{{ date('M d, Y', strtotime($order['created_at'])) }}</td>
                                <td>${{ number_format($order['total_price'], 2) }}</td>
                                <td>
                                    <span class="status-badge status-{{ $order['status'] }}">
                                        {{ ucfirst(str_replace('_', ' ', $order['status'])) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="/orders/{{ $order['id'] }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-shopping-bag fa-4x text-muted mb-3"></i>
                <h5>No orders yet</h5>
                <p class="text-muted">You haven't placed any orders yet.</p>
                <a href="/products" class="btn btn-primary mt-3">Browse Food</a>
            </div>
        @endif
    </div>
</div>
@endsection
