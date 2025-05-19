@extends('layouts.admin')

@section('title', $title)

@section('content')
<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card dashboard-card bg-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Customers</div>
                        <div class="card-value">{{ $customerCount }}</div>
                    </div>
                    <div class="icon text-primary">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0">
                <a href="/admin/users" class="text-primary">View Details <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card dashboard-card bg-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Vendors</div>
                        <div class="card-value">{{ $vendorCount }}</div>
                    </div>
                    <div class="icon text-success">
                        <i class="fas fa-store"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0">
                <a href="/admin/vendors" class="text-success">View Details <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card dashboard-card bg-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Products</div>
                        <div class="card-value">{{ $productCount }}</div>
                    </div>
                    <div class="icon text-info">
                        <i class="fas fa-hamburger"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0">
                <a href="#" class="text-info">View Details <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card dashboard-card bg-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Orders</div>
                        <div class="card-value">{{ $orderCount }}</div>
                    </div>
                    <div class="icon text-warning">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0">
                <a href="/admin/orders" class="text-warning">View Details <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
        </div>
    </div>
</div>

<!-- Recent Orders -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Recent Orders</h5>
                <a href="/admin/orders" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="card-body">
                @if(count($recentOrders) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Vendor</th>
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
                                        <td>{{ $order['vendor_business_name'] }}</td>
                                        <td>${{ number_format($order['total_price'], 2) }}</td>
                                        <td>{{ date('M d, Y', strtotime($order['created_at'])) }}</td>
                                        <td>
                                            <span class="badge status-{{ $order['status'] }}">
                                                {{ ucfirst(str_replace('_', ' ', $order['status'])) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="/admin/orders/{{ $order['id'] }}" class="btn btn-sm btn-primary">
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
                        <a href="/admin/categories/add" class="btn btn-primary w-100 py-3">
                            <i class="fas fa-plus-circle me-2"></i> Add Category
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="/admin/vendors" class="btn btn-success w-100 py-3">
                            <i class="fas fa-check-circle me-2"></i> Approve Vendors
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="/admin/orders" class="btn btn-info w-100 py-3 text-white">
                            <i class="fas fa-truck me-2"></i> Manage Orders
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="/admin/users" class="btn btn-warning w-100 py-3">
                            <i class="fas fa-user-plus me-2"></i> Manage Users
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
