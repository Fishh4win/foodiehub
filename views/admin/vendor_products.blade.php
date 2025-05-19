@extends('layouts.admin')

@section('title', $title)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">{{ $vendor['business_name'] }}'s Products</h1>
    <div>
        <a href="/admin/vendors/edit/{{ $vendor['id'] }}" class="btn btn-outline-primary me-2">
            <i class="fas fa-edit me-1"></i> Edit Vendor
        </a>
        <a href="/admin/vendors" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Vendors
        </a>
    </div>
</div>

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
                    <p class="mb-1"><strong>Owner:</strong> {{ $vendor['name'] }}</p>
                    <p class="mb-1"><strong>Email:</strong> {{ $vendor['email'] }}</p>
                    <p class="mb-1"><strong>Phone:</strong> {{ $vendor['phone'] ?? 'Not provided' }}</p>
                </div>
                
                <div class="mb-3">
                    <p class="mb-1"><strong>Status:</strong> 
                        @if($vendor['is_approved'])
                            <span class="badge bg-success">Approved</span>
                        @else
                            <span class="badge bg-warning">Pending</span>
                        @endif
                    </p>
                    <p class="mb-1"><strong>Featured:</strong> 
                        @if($vendor['is_featured'])
                            <span class="badge bg-primary">Yes</span>
                        @else
                            <span class="badge bg-secondary">No</span>
                        @endif
                    </p>
                </div>
                
                <div class="d-grid gap-2">
                    <a href="/admin/vendors/edit/{{ $vendor['id'] }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i> Edit Vendor
                    </a>
                    <a href="/products?vendor={{ $vendor['id'] }}" class="btn btn-outline-info" target="_blank">
                        <i class="fas fa-external-link-alt me-2"></i> View in Store
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Products ({{ count($products) }})</h5>
                <a href="/admin/products?vendor={{ $vendor['id'] }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-filter me-1"></i> Filter in All Products
                </a>
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
                                        <td>
                                            @foreach($categories as $category)
                                                @if($category['id'] == $product['category_id'])
                                                    {{ $category['name'] }}
                                                @endif
                                            @endforeach
                                        </td>
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
                                                <a href="/admin/products/edit/{{ $product['id'] }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="/admin/products/toggle-availability" method="POST" class="d-inline">
                                                    <input type="hidden" name="product_id" value="{{ $product['id'] }}">
                                                    <button type="submit" class="btn btn-sm btn-outline-warning">
                                                        @if(isset($product['is_available']) && $product['is_available'])
                                                            <i class="fas fa-ban"></i>
                                                        @else
                                                            <i class="fas fa-check"></i>
                                                        @endif
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i> This vendor has no products yet.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
