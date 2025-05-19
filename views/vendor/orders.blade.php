@extends('layouts.main')

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
                                    <td>₱{{ number_format($order['total_price'], 2) }}</td>
                                    <td>{{ date('M d, Y', strtotime($order['created_at'])) }}</td>
                                    <td>
                                        <span class="status-badge status-{{ $order['status'] }}">
                                            {{ ucfirst(str_replace('_', ' ', $order['status'])) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="/vendor/orders/{{ $order['id'] }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        @if($order['status'] == 'pending')
                                            <button type="button" class="btn btn-sm btn-success update-status-btn" 
                                                data-order-id="{{ $order['id'] }}" 
                                                data-status="preparing"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#updateStatusModal">
                                                <i class="fas fa-check"></i> Accept
                                            </button>
                                        @elseif($order['status'] == 'preparing')
                                            <button type="button" class="btn btn-sm btn-info update-status-btn" 
                                                data-order-id="{{ $order['id'] }}" 
                                                data-status="out_for_delivery"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#updateStatusModal">
                                                <i class="fas fa-truck"></i> Out for Delivery
                                            </button>
                                        @elseif($order['status'] == 'out_for_delivery')
                                            <button type="button" class="btn btn-sm btn-success update-status-btn" 
                                                data-order-id="{{ $order['id'] }}" 
                                                data-status="delivered"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#updateStatusModal">
                                                <i class="fas fa-check-circle"></i> Mark Delivered
                                            </button>
                                        @endif
                                        
                                        @if($order['status'] != 'cancelled' && $order['status'] != 'delivered')
                                            <button type="button" class="btn btn-sm btn-danger update-status-btn" 
                                                data-order-id="{{ $order['id'] }}" 
                                                data-status="cancelled"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#updateStatusModal">
                                                <i class="fas fa-times"></i> Cancel
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Other tab panes with filtered orders by status -->
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

<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateStatusModalLabel">Update Order Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/vendor/orders/update-status" method="post">
                <div class="modal-body">
                    <p>Are you sure you want to update the status of this order?</p>
                    <input type="hidden" name="order_id" id="order_id">
                    <input type="hidden" name="status" id="status">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filterModalLabel">Filter Orders</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/vendor/orders" method="get">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="date_from" class="form-label">Date From</label>
                        <input type="date" class="form-control" id="date_from" name="date_from">
                    </div>
                    <div class="mb-3">
                        <label for="date_to" class="form-label">Date To</label>
                        <input type="date" class="form-control" id="date_to" name="date_to">
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">All Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="preparing">Preparing</option>
                            <option value="out_for_delivery">Out for Delivery</option>
                            <option value="delivered">Delivered</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Update the hidden fields in the modal when the update status button is clicked
    document.querySelectorAll('.update-status-btn').forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('order_id').value = this.getAttribute('data-order-id');
            document.getElementById('status').value = this.getAttribute('data-status');
        });
    });
</script>
@endsection
