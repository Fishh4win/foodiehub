@extends('layouts.admin')

@section('title', $title)

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Manage Orders</h5>
        <div>
            <a href="#" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="fas fa-filter me-1"></i> Filter
            </a>
        </div>
    </div>
    <div class="card-body">
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link active" href="#all-orders" data-bs-toggle="tab">All Orders</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#pending" data-bs-toggle="tab">Pending</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#preparing" data-bs-toggle="tab">Preparing</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#out-for-delivery" data-bs-toggle="tab">Out for Delivery</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#delivered" data-bs-toggle="tab">Delivered</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#cancelled" data-bs-toggle="tab">Cancelled</a>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="all-orders">
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
                            @foreach($orders as $order)
                                <tr>
                                    <td>#{{ $order['id'] }}</td>
                                    <td>{{ $order['customer_name'] }}</td>
                                    <td>{{ $order['vendor_business_name'] }}</td>
                                    <td>₱{{ number_format($order['total_price'], 2) }}</td>
                                    <td>{{ date('M d, Y', strtotime($order['created_at'])) }}</td>
                                    <td>
                                        <span class="status-badge status-{{ $order['status'] }}">
                                            {{ ucfirst(str_replace('_', ' ', $order['status'])) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="/admin/orders/{{ $order['id'] }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="pending">
                <!-- Similar table for pending orders -->
            </div>

            <div class="tab-pane fade" id="preparing">
                <!-- Similar table for preparing orders -->
            </div>

            <div class="tab-pane fade" id="out-for-delivery">
                <!-- Similar table for out for delivery orders -->
            </div>

            <div class="tab-pane fade" id="delivered">
                <!-- Similar table for delivered orders -->
            </div>

            <div class="tab-pane fade" id="cancelled">
                <!-- Similar table for cancelled orders -->
            </div>
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filter Orders</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="/admin/orders" method="GET">
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="status-all" name="status[]" value="all" checked>
                            <label class="form-check-label" for="status-all">All</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="status-pending" name="status[]" value="pending">
                            <label class="form-check-label" for="status-pending">Pending</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="status-preparing" name="status[]" value="preparing">
                            <label class="form-check-label" for="status-preparing">Preparing</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="status-out_for_delivery" name="status[]" value="out_for_delivery">
                            <label class="form-check-label" for="status-out_for_delivery">Out for Delivery</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="status-delivered" name="status[]" value="delivered">
                            <label class="form-check-label" for="status-delivered">Delivered</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="status-cancelled" name="status[]" value="cancelled">
                            <label class="form-check-label" for="status-cancelled">Cancelled</label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="vendor" class="form-label">Vendor</label>
                        <select class="form-select" id="vendor" name="vendor">
                            <option value="">All Vendors</option>
                            <!-- Vendor options would be populated here -->
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="date-range" class="form-label">Date Range</label>
                        <select class="form-select" id="date-range" name="date_range">
                            <option value="all">All Time</option>
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                            <option value="year">This Year</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
