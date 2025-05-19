@extends('layouts.admin')

@section('title', $title)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Edit Category</h5>
            </div>
            <div class="card-body">
                <form action="/admin/categories/edit" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="category_id" value="{{ $category['id'] }}">
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Category Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ $category['name'] }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3">{{ $category['description'] }}</textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="image" class="form-label">Category Image</label>
                        <input type="file" class="form-control image-input" id="image" name="image" data-preview="image-preview">
                        <div class="mt-2">
                            @if(isset($category['image']) && $category['image'])
                                <img src="/{{ $category['image'] }}" id="image-preview" class="img-thumbnail" style="max-height: 200px;">
                            @else
                                <img id="image-preview" class="img-thumbnail" style="max-height: 200px; display: none;">
                            @endif
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end">
                        <a href="/admin/categories" class="btn btn-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
