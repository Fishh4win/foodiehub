@extends('layouts.main')

@section('title', $title)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Add New Category</h1>
    <a href="/vendor/categories" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i> Back to Categories
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0">Category Information</h5>
    </div>
    <div class="card-body">
        <form action="/vendor/categories/add" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="name" class="form-label">Category Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                <div class="form-text">Provide a brief description of this category</div>
            </div>
            
            <div class="mb-4">
                <label for="image" class="form-label">Category Image</label>
                <input type="file" class="form-control image-input" id="image" name="image" accept="image/*" data-preview="image-preview">
                <div class="form-text">Recommended size: 500x500 pixels, max 2MB</div>
                <div class="mt-2">
                    <img id="image-preview" class="rounded border" style="max-height: 200px; display: none;" alt="Category preview">
                </div>
            </div>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="/vendor/categories" class="btn btn-secondary me-md-2">Cancel</a>
                <button type="submit" class="btn btn-primary">Add Category</button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm mt-4">
    <div class="card-header bg-white">
        <h5 class="mb-0">Tips for Creating Categories</h5>
    </div>
    <div class="card-body">
        <ul>
            <li><strong>Be specific:</strong> Use clear, descriptive names that customers will understand</li>
            <li><strong>Be consistent:</strong> Use a similar naming convention for all categories</li>
            <li><strong>Use images:</strong> A good category image helps customers quickly identify what's in the category</li>
            <li><strong>Keep it simple:</strong> Don't create too many categories, as it can overwhelm customers</li>
        </ul>
    </div>
</div>
@endsection
