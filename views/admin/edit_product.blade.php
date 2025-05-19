@extends('layouts.admin')

@section('title', $title)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Edit Product</h1>
    <a href="/admin/products" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i> Back to Products
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0">Product Information</h5>
    </div>
    <div class="card-body">
        <form action="/admin/products/edit" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="product_id" value="{{ $product['id'] }}">
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">Product Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ $product['name'] }}" required>
                </div>
                <div class="col-md-6">
                    <label for="price" class="form-label">Price (₱)</label>
                    <input type="number" class="form-control" id="price" name="price" value="{{ $product['price'] }}" step="0.01" min="0" required>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="category_id" class="form-label">Category</label>
                    <select class="form-select" id="category_id" name="category_id">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category['id'] }}" {{ $product['category_id'] == $category['id'] ? 'selected' : '' }}>
                                {{ $category['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="preparation_time" class="form-label">Preparation Time (minutes)</label>
                    <input type="number" class="form-control" id="preparation_time" name="preparation_time" value="{{ $product['preparation_time'] }}" min="0">
                </div>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="4">{{ $product['description'] }}</textarea>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="image" class="form-label">Product Image</label>
                    <input type="file" class="form-control" id="image" name="image">
                    <small class="text-muted">Leave empty to keep current image</small>
                    
                    @if(isset($product['image']) && $product['image'])
                        <div class="mt-2">
                            <p class="mb-1">Current Image:</p>
                            <img src="/{{ $product['image'] }}" class="img-thumbnail" style="max-height: 150px;" alt="{{ $product['name'] }}">
                        </div>
                    @endif
                </div>
                <div class="col-md-6">
                    <label class="form-label d-block">Availability</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_available" name="is_available" {{ isset($product['is_available']) && $product['is_available'] ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_available">Product is available</label>
                    </div>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Vendor</label>
                    <p class="form-control-plaintext">{{ $product['business_name'] ?? 'Unknown Vendor' }}</p>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Last Updated</label>
                    <p class="form-control-plaintext">{{ isset($product['updated_at']) ? date('M d, Y H:i', strtotime($product['updated_at'])) : 'N/A' }}</p>
                </div>
            </div>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="/admin/products" class="btn btn-secondary me-md-2">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection
