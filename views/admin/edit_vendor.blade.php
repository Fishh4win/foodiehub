@extends('layouts.admin')

@section('title', $title)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Edit Vendor</h1>
    <a href="/admin/vendors" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i> Back to Vendors
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Vendor Information</h5>
            </div>
            <div class="card-body">
                <form action="/admin/vendors/edit" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="vendor_id" value="{{ $vendor['id'] }}">
                    <input type="hidden" name="user_id" value="{{ $vendor['user_id'] }}">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="business_name" class="form-label">Business Name</label>
                            <input type="text" class="form-control" id="business_name" name="business_name" value="{{ $vendor['business_name'] }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" class="form-control" id="location" name="location" value="{{ $vendor['location'] }}">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Business Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4">{{ $vendor['description'] }}</textarea>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="logo" class="form-label">Logo</label>
                            <input type="file" class="form-control" id="logo" name="logo">
                            <small class="text-muted">Leave empty to keep current logo</small>
                            
                            @if(isset($vendor['logo']) && $vendor['logo'])
                                <div class="mt-2">
                                    <p class="mb-1">Current Logo:</p>
                                    <img src="/{{ $vendor['logo'] }}" class="img-thumbnail" style="max-height: 100px;" alt="{{ $vendor['business_name'] }}">
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label for="banner" class="form-label">Banner</label>
                            <input type="file" class="form-control" id="banner" name="banner">
                            <small class="text-muted">Leave empty to keep current banner</small>
                            
                            @if(isset($vendor['banner']) && $vendor['banner'])
                                <div class="mt-2">
                                    <p class="mb-1">Current Banner:</p>
                                    <img src="/{{ $vendor['banner'] }}" class="img-thumbnail" style="max-height: 100px;" alt="{{ $vendor['business_name'] }} Banner">
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label d-block">Status</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_approved" name="is_approved" {{ $vendor['is_approved'] ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_approved">Approved</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label d-block">Featured</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" {{ $vendor['is_featured'] ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_featured">Featured on homepage</label>
                            </div>
                        </div>
                    </div>
                    
                    <h5 class="mt-4 mb-3">Owner Information</h5>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Owner Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ $vendor['name'] }}">
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ $vendor['email'] }}">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="{{ $vendor['phone'] }}">
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="/admin/vendors" class="btn btn-secondary me-md-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="/admin/vendors/{{ $vendor['id'] }}/products" class="btn btn-info text-white">
                        <i class="fas fa-hamburger me-2"></i> View Products
                    </a>
                    
                    @if(!$vendor['is_approved'])
                        <form action="/admin/vendors/approve" method="POST">
                            <input type="hidden" name="vendor_id" value="{{ $vendor['id'] }}">
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-check me-2"></i> Approve Vendor
                            </button>
                        </form>
                    @else
                        <form action="/admin/vendors/disapprove" method="POST">
                            <input type="hidden" name="vendor_id" value="{{ $vendor['id'] }}">
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-times me-2"></i> Disapprove Vendor
                            </button>
                        </form>
                    @endif
                    
                    @if(!$vendor['is_featured'])
                        <form action="/admin/vendors/feature" method="POST">
                            <input type="hidden" name="vendor_id" value="{{ $vendor['id'] }}">
                            <button type="submit" class="btn btn-warning w-100">
                                <i class="fas fa-star me-2"></i> Feature Vendor
                            </button>
                        </form>
                    @else
                        <form action="/admin/vendors/unfeature" method="POST">
                            <input type="hidden" name="vendor_id" value="{{ $vendor['id'] }}">
                            <button type="submit" class="btn btn-secondary w-100">
                                <i class="far fa-star me-2"></i> Unfeature Vendor
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Vendor Statistics</h5>
            </div>
            <div class="card-body">
                <p class="mb-2"><strong>Joined:</strong> {{ date('M d, Y', strtotime($vendor['created_at'] ?? 'now')) }}</p>
                <p class="mb-2"><strong>Status:</strong> 
                    @if($vendor['is_approved'])
                        <span class="badge bg-success">Approved</span>
                    @else
                        <span class="badge bg-warning">Pending</span>
                    @endif
                </p>
                <p class="mb-0"><strong>Featured:</strong> 
                    @if($vendor['is_featured'])
                        <span class="badge bg-primary">Yes</span>
                    @else
                        <span class="badge bg-secondary">No</span>
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
