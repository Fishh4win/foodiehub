@extends('layouts.main')

@section('title', $title)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Edit Category</h1>
    <a href="/vendor/categories" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i> Back to Categories
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0">Category Information</h5>
    </div>
    <div class="card-body">
        <form action="/vendor/categories/edit" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="category_id" value="{{ $category['id'] }}">
            
            <div class="mb-3">
                <label for="name" class="form-label">Category Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" value="{{ $category['name'] }}" required>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3">{{ $category['description'] }}</textarea>
                <div class="form-text">Provide a brief description of this category</div>
            </div>
            
            <div class="mb-4">
                <label for="image" class="form-label">Category Image</label>
                <input type="file" class="form-control image-input" id="image" name="image" accept="image/*" data-preview="image-preview">
                <div class="form-text">Leave empty to keep the current image</div>
                <div class="mt-2">
                    @if(isset($category['image']) && $category['image'])
                        <img id="image-preview" src="/{{ $category['image'] }}" class="rounded border" style="max-height: 200px;" alt="{{ $category['name'] }}">
                    @else
                        <img id="image-preview" class="rounded border" style="max-height: 200px; display: none;" alt="Category preview">
                    @endif
                </div>
            </div>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="/vendor/categories" class="btn btn-secondary me-md-2">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm mt-4">
    <div class="card-header bg-white">
        <h5 class="mb-0">Products in this Category</h5>
    </div>
    <div class="card-body">
        <a href="/vendor/products?category={{ $category['id'] }}" class="btn btn-outline-primary">
            <i class="fas fa-hamburger me-1"></i> View Products in this Category
        </a>
    </div>
</div>
@endsection
