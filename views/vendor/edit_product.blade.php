@extends('layouts.main')

@section('title', $title)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Edit Product</h1>
    <a href="/vendor/products" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i> Back to Products
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0">Product Information</h5>
    </div>
    <div class="card-body">
        <form action="/vendor/products/edit" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="product_id" value="{{ $product['id'] }}">

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ $product['name'] }}" required>
                </div>
                <div class="col-md-6">
                    <label for="price" class="form-label">Price (₱) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="price" name="price" value="{{ $product['price'] }}" step="0.01" min="0" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="category_id" class="form-label">Category (Optional)</label>
                    <div class="input-group">
                        <select class="form-select" id="category_id" name="category_id">
                            <option value="">-- Select Category (Optional) --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category['id'] }}" {{ isset($product['category_id']) && $product['category_id'] == $category['id'] ? 'selected' : '' }}>
                                    {{ $category['name'] }}
                                </option>
                            @endforeach
                        </select>
                        <a href="/vendor/categories/add" class="btn btn-outline-secondary">
                            <i class="fas fa-plus"></i>
                        </a>
                    </div>
                    <div class="form-text">You can leave this empty if you don't want to categorize the product.</div>
                </div>
                <div class="col-md-6">
                    <label for="preparation_time" class="form-label">Preparation Time (minutes)</label>
                    <input type="number" class="form-control" id="preparation_time" name="preparation_time" value="{{ $product['preparation_time'] }}" min="1">
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="4">{{ $product['description'] }}</textarea>
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Product Image</label>
                <input type="file" class="form-control image-input" id="image" name="image" accept="image/*" data-preview="image-preview">
                <div class="form-text">Leave empty to keep the current image</div>
                <div class="mt-2">
                    @if(isset($product['image']) && $product['image'])
                        <img id="image-preview" src="/{{ $product['image'] }}" class="rounded border" style="max-height: 200px;" alt="{{ $product['name'] }}">
                    @else
                        <img id="image-preview" class="rounded border" style="max-height: 200px; display: none;" alt="Product preview">
                    @endif
                </div>
            </div>

            <div class="mb-4">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="is_available" name="is_available" {{ isset($product['is_available']) && $product['is_available'] ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_available">Available for ordering</label>
                </div>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="/vendor/products" class="btn btn-secondary me-md-2">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection
