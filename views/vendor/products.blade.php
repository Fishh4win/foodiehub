@extends('layouts.main')

@section('title', $title)

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Your Products</h5>
        <div>
            <a href="/vendor/products/add" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Add Product
            </a>
            <a href="/vendor/categories" class="btn btn-outline-secondary ms-2">
                <i class="fas fa-tags me-1"></i> Manage Categories
            </a>
        </div>
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
                                <td>{{ $product['category_name'] ?? 'Uncategorized' }}</td>
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
                                        <a href="/products/{{ $product['id'] }}" class="btn btn-sm btn-outline-info" target="_blank" data-bs-toggle="tooltip" title="View Product">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="/vendor/products/edit/{{ $product['id'] }}" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Edit Product">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $product['id'] }}" data-bs-toggle="tooltip" title="Delete Product">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <form action="/vendor/products/toggle-availability" method="POST" class="d-inline">
                                            <input type="hidden" name="product_id" value="{{ $product['id'] }}">
                                            <button type="submit" class="btn btn-sm btn-outline-warning" data-bs-toggle="tooltip" title="{{ isset($product['is_available']) && $product['is_available'] ? 'Mark as Unavailable' : 'Mark as Available' }}">
                                                @if(isset($product['is_available']) && $product['is_available'])
                                                    <i class="fas fa-ban"></i>
                                                @else
                                                    <i class="fas fa-check"></i>
                                                @endif
                                            </button>
                                        </form>
                                    </div>

                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteModal{{ $product['id'] }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Confirm Delete</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Are you sure you want to delete <strong>{{ $product['name'] }}</strong>?</p>
                                                    <p class="text-danger"><small>This action cannot be undone.</small></p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <form action="/vendor/products/delete" method="POST">
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
                <i class="fas fa-info-circle me-2"></i> You haven't added any products yet.
                <a href="/vendor/products/add" class="alert-link">Add your first product</a>.
            </div>
        @endif
    </div>
</div>
@endsection
