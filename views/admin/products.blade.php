@extends('layouts.admin')

@section('title', $title)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Manage Products</h1>
</div>

<!-- Filters -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0">Filters</h5>
    </div>
    <div class="card-body">
        <form action="/admin/products" method="GET" class="row g-3">
            <div class="col-md-3">
                <label for="category" class="form-label">Category</label>
                <select class="form-select" id="category" name="category">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category['id'] }}" {{ $filters['category'] == $category['id'] ? 'selected' : '' }}>
                            {{ $category['name'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="vendor" class="form-label">Vendor</label>
                <select class="form-select" id="vendor" name="vendor">
                    <option value="">All Vendors</option>
                    @foreach($vendors as $vendor)
                        <option value="{{ $vendor['id'] }}" {{ $filters['vendor'] == $vendor['id'] ? 'selected' : '' }}>
                            {{ $vendor['business_name'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="search" class="form-label">Search</label>
                <input type="text" class="form-control" id="search" name="search" value="{{ $filters['search'] }}" placeholder="Search by name or description">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
            </div>
        </form>
    </div>
</div>

<!-- Products Table -->
<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Products</h5>
    </div>
    <div class="card-body">
        @if(count($products) > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Vendor</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                            <tr>
                                <td>{{ $product['id'] }}</td>
                                <td>
                                    @if(isset($product['image']) && $product['image'])
                                        <img src="/{{ $product['image'] }}" class="rounded" style="width: 50px; height: 50px; object-fit: cover;" alt="{{ $product['name'] }}">
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                            <i class="fas fa-hamburger text-secondary"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $product['name'] }}</td>
                                <td>{{ $product['category_name'] ?? 'Uncategorized' }}</td>
                                <td>{{ $product['business_name'] ?? 'Unknown Vendor' }}</td>
                                <td>₱{{ number_format($product['price'], 2) }}</td>
                                <td>
                                    @if(isset($product['is_available']) && $product['is_available'])
                                        <span class="badge bg-success">Available</span>
                                    @else
                                        <span class="badge bg-danger">Unavailable</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="/admin/products/edit/{{ $product['id'] }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $product['id'] }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <form action="/admin/products/toggle-availability" method="POST" class="d-inline">
                                            <input type="hidden" name="product_id" value="{{ $product['id'] }}">
                                            <button type="submit" class="btn btn-sm btn-warning">
                                                @if(isset($product['is_available']) && $product['is_available'])
                                                    <i class="fas fa-ban"></i>
                                                @else
                                                    <i class="fas fa-check"></i>
                                                @endif
                                            </button>
                                        </form>
                                    </div>
                                    
                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteModal{{ $product['id'] }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $product['id'] }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel{{ $product['id'] }}">Confirm Delete</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Are you sure you want to delete the product "{{ $product['name'] }}"?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <form action="/admin/products/delete" method="POST">
                                                        <input type="hidden" name="product_id" value="{{ $product['id'] }}">
                                                        <button type="submit" class="btn btn-danger">Delete</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info mb-0">
                <i class="fas fa-info-circle me-2"></i> No products found. Try adjusting your filters.
            </div>
        @endif
    </div>
</div>
@endsection
