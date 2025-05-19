@extends('layouts.main')

@section('title', $title)

@section('content')
<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card dashboard-card bg-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Orders</div>
                        <div class="card-value">{{ $stats['total_orders'] ?? 0 }}</div>
                    </div>
                    <div class="icon text-primary">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0">
                <a href="/vendor/orders" class="text-primary">View Details <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card dashboard-card bg-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Products</div>
                        <div class="card-value">{{ $stats['total_products'] ?? 0 }}</div>
                    </div>
                    <div class="icon text-success">
                        <i class="fas fa-hamburger"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0">
                <a href="/vendor/products" class="text-success">View Details <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card dashboard-card bg-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Revenue</div>
                        <div class="card-value">₱{{ number_format($stats['total_revenue'] ?? 0, 2) }}</div>
                    </div>
                    <div class="icon text-info">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0">
                <a href="/vendor/orders" class="text-info">View Details <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card dashboard-card bg-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Orders</div>
                        <div class="card-value">{{ $stats['pending_orders'] ?? 0 }}</div>
                    </div>
                    <div class="icon text-warning">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0">
                <a href="/vendor/orders" class="text-warning">View Details <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
        </div>
    </div>
</div>

<!-- Vendor Information -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white">
                <h5 class="mb-0">Vendor Information</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    @if(isset($vendor['logo']) && $vendor['logo'])
                        <img src="/{{ $vendor['logo'] }}" class="img-fluid rounded mb-3" style="max-height: 100px;" alt="{{ $vendor['business_name'] }}">
                    @else
                        <div class="bg-light rounded d-flex align-items-center justify-content-center mb-3" style="height: 100px;">
                            <i class="fas fa-store fa-3x text-secondary"></i>
                        </div>
                    @endif
                    <h5>{{ $vendor['business_name'] }}</h5>
                    <p class="text-muted">{{ $vendor['location'] }}</p>
                </div>
                <div class="mb-3">
                    <p class="mb-1"><strong>Status:</strong>
                        @if($vendor['is_approved'])
                            <span class="badge bg-success">Approved</span>
                        @else
                            <span class="badge bg-warning">Pending Approval</span>
                        @endif
                    </p>
                    <p class="mb-1"><strong>Featured:</strong>
                        @if($vendor['is_featured'])
                            <span class="badge bg-primary">Featured</span>
                        @else
                            <span class="badge bg-secondary">Not Featured</span>
                        @endif
                    </p>
                    <p class="mb-1"><strong>Contact:</strong> {{ $vendor['phone'] ?? 'Not specified' }}</p>
                    <p class="mb-1"><strong>Email:</strong> {{ $vendor['email'] ?? 'Not specified' }}</p>
                </div>
                <a href="/profile" class="btn btn-outline-primary btn-sm w-100">Edit Profile</a>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Recent Orders</h5>
            </div>
            <div class="card-body">
                @if(count($recentOrders) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Total</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentOrders as $order)
                                    <tr>
                                        <td>#{{ $order['id'] }}</td>
                                        <td>{{ $order['customer_name'] }}</td>
                                        <td>₱{{ number_format($order['total_price'], 2) }}</td>
                                        <td>{{ date('M d, Y', strtotime($order['created_at'])) }}</td>
                                        <td>
                                            <span class="status-badge status-{{ $order['status'] }}">
                                                {{ ucfirst(str_replace('_', ' ', $order['status'])) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="/vendor/orders/{{ $order['id'] }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted mb-0">No recent orders found.</p>
                @endif
            </div>
            <div class="card-footer bg-white">
                <a href="/vendor/orders" class="btn btn-primary btn-sm">View All Orders</a>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="/vendor/products/add" class="btn btn-primary w-100 py-3">
                            <i class="fas fa-plus-circle me-2"></i> Add Product
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="/vendor/products" class="btn btn-success w-100 py-3">
                            <i class="fas fa-hamburger me-2"></i> Manage Products
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="/vendor/categories" class="btn btn-secondary w-100 py-3">
                            <i class="fas fa-tags me-2"></i> Manage Categories
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="/vendor/orders" class="btn btn-info w-100 py-3 text-white">
                            <i class="fas fa-truck me-2"></i> Manage Orders
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
