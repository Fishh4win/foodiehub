@extends('layouts.main')

@section('title', $title)

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Manage Categories</h5>
        <div>
            <a href="/vendor/categories/add" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Add Category
            </a>
            <a href="/vendor/products" class="btn btn-outline-secondary ms-2">
                <i class="fas fa-hamburger me-1"></i> Back to Products
            </a>
        </div>
    </div>
    <div class="card-body">
        @if(count($categories) > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Products</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $category)
                            <tr>
                                <td>{{ $category['id'] }}</td>
                                <td>
                                    @if(isset($category['image']) && $category['image'])
                                        <img src="/{{ $category['image'] }}" class="rounded" style="width: 50px; height: 50px; object-fit: cover;" alt="{{ $category['name'] }}">
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                            <i class="fas fa-utensils text-secondary"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $category['name'] }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($category['description'] ?? '', 50) }}</td>
                                <td>{{ $category['product_count'] ?? 0 }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="/vendor/categories/edit/{{ $category['id'] }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="/products?category={{ $category['id'] }}" class="btn btn-sm btn-outline-info" target="_blank">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info mb-0">
                <i class="fas fa-info-circle me-2"></i> No categories found.
                <a href="/vendor/categories/add" class="alert-link">Add your first category</a>.
            </div>
        @endif
    </div>
</div>

<div class="card shadow-sm mt-4">
    <div class="card-header bg-white">
        <h5 class="mb-0">About Categories</h5>
    </div>
    <div class="card-body">
        <p>Categories help organize your products and make it easier for customers to find what they're looking for. Here are some tips:</p>
        <ul>
            <li>Use clear, descriptive names for your categories</li>
            <li>Add an image that represents the category</li>
            <li>Keep the number of categories manageable</li>
            <li>Make sure all your products are assigned to appropriate categories</li>
        </ul>
    </div>
</div>
@endsection
