@extends('layouts.main')

@section('title', $title)

@section('content')
<div class="container py-5">
    <div class="row">
        <!-- Profile Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    @if(isset($user['profile_image']) && $user['profile_image'])
                        <img src="/{{ $user['profile_image'] }}" class="rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover;" alt="{{ $user['name'] }}">
                    @else
                        <div class="bg-light rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 120px; height: 120px;">
                            <i class="fas fa-user fa-4x text-secondary"></i>
                        </div>
                    @endif
                    <h5 class="mb-1">{{ $user['name'] }}</h5>
                    <p class="text-muted mb-3">{{ $user['email'] }}</p>
                    <div class="d-grid">
                        <a href="/orders" class="btn btn-outline-primary">My Orders</a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Profile Content -->
        <div class="col-lg-9">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Profile Information</h5>
                </div>
                <div class="card-body">
                    <form action="/profile/update" method="POST" enctype="multipart/form-data">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ $user['name'] }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ $user['email'] }}" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="text" class="form-control" id="phone" name="phone" value="{{ $user['phone'] ?? '' }}">
                            </div>
                            <div class="col-md-6">
                                <label for="profile_image" class="form-label">Profile Image</label>
                                <input type="file" class="form-control" id="profile_image" name="profile_image">
                                <small class="text-muted">Upload a new profile image (optional)</small>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Delivery Address</label>
                            <textarea class="form-control" id="address" name="address" rows="3">{{ $user['address'] ?? '' }}</textarea>
                        </div>
                        <hr>
                        <h6 class="mb-3">Change Password (optional)</h6>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="current_password" class="form-label">Current Password</label>
                                <input type="password" class="form-control" id="current_password" name="current_password">
                            </div>
                            <div class="col-md-4">
                                <label for="new_password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="new_password" name="new_password">
                            </div>
                            <div class="col-md-4">
                                <label for="confirm_password" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                            </div>
                        </div>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Recent Orders -->
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Orders</h5>
                    <a href="/orders" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    @if(count($orders) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Total</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                        <tr>
                                            <td>#{{ $order['id'] }}</td>
                                            <td>{{ date('M d, Y', strtotime($order['created_at'])) }}</td>
                                            <td>
                                                @if($order['status'] === 'pending')
                                                    <span class="badge bg-warning text-dark">Pending</span>
                                                @elseif($order['status'] === 'preparing')
                                                    <span class="badge bg-info">Preparing</span>
                                                @elseif($order['status'] === 'out_for_delivery')
                                                    <span class="badge bg-primary">Out for Delivery</span>
                                                @elseif($order['status'] === 'delivered')
                                                    <span class="badge bg-success">Delivered</span>
                                                @elseif($order['status'] === 'cancelled')
                                                    <span class="badge bg-danger">Cancelled</span>
                                                @endif
                                            </td>
                                            <td>${{ number_format($order['total_price'], 2) }}</td>
                                            <td>
                                                <a href="/orders/{{ $order['id'] }}" class="btn btn-sm btn-outline-secondary">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                            <h6>No orders yet</h6>
                            <p class="text-muted">You haven't placed any orders yet.</p>
                            <a href="/products" class="btn btn-primary mt-2">Browse Food</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
