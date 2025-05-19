@extends('layouts.admin')

@section('title', $title)

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Manage Categories</h5>
        <a href="/admin/categories/add" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Add Category
        </a>
    </div>
    <div class="card-body">
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
                                    <a href="/admin/categories/edit/{{ $category['id'] }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteCategoryModal{{ $category['id'] }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                
                                <!-- Delete Category Modal -->
                                <div class="modal fade" id="deleteCategoryModal{{ $category['id'] }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Delete Category</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Are you sure you want to delete the category <strong>{{ $category['name'] }}</strong>?</p>
                                                @if(($category['product_count'] ?? 0) > 0)
                                                    <div class="alert alert-warning">
                                                        <i class="fas fa-exclamation-triangle me-2"></i> This category has {{ $category['product_count'] }} products. Deleting it will remove the category from these products.
                                                    </div>
                                                @endif
                                                <p class="text-danger">This action cannot be undone.</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <form action="/admin/categories/delete" method="POST">
                                                    <input type="hidden" name="category_id" value="{{ $category['id'] }}">
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
    </div>
</div>
@endsection
