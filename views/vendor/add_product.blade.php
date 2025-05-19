@extends('layouts.main')

@section('title', $title)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Add New Product</h1>
    <a href="/vendor/products" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i> Back to Products
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0">Product Information</h5>
    </div>
    <div class="card-body">
        <form action="/vendor/products/add" method="POST" enctype="multipart/form-data">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="col-md-6">
                    <label for="price" class="form-label">Price (₱) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="category_id" class="form-label">Category (Optional)</label>
                    <div class="input-group">
                        <select class="form-select" id="category_id" name="category_id">
                            <option value="">-- Select Category (Optional) --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category['id'] }}">{{ $category['name'] }}</option>
                            @endforeach
                        </select>
                        <a href="/vendor/categories/add" class="btn btn-outline-secondary">
                            <i class="fas fa-plus"></i>
                        </a>
                    </div>
                    <div class="form-text">If your category is not listed, click the + button to add a new one.</div>
                </div>
                <div class="col-md-6">
                    <label for="preparation_time" class="form-label">Preparation Time (minutes)</label>
                    <input type="number" class="form-control" id="preparation_time" name="preparation_time" min="1">
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="4"></textarea>
            </div>

            <div class="mb-4">
                <label for="image" class="form-label">Product Image</label>
                <input type="file" class="form-control image-input" id="image" name="image" accept="image/*" data-preview="image-preview">
                <div class="form-text">Recommended size: 500x500 pixels, max 2MB</div>
                <div class="mt-2">
                    <img id="image-preview" class="rounded border" style="max-height: 200px; display: none;" alt="Product preview">
                </div>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="/vendor/products" class="btn btn-secondary me-md-2">Cancel</a>
                <button type="submit" class="btn btn-primary">Add Product</button>
            </div>
        </form>
    </div>
</div>
@endsection
