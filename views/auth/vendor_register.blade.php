@extends('layouts.main')

@section('title', $title)

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Register as Vendor</h4>
            </div>
            <div class="card-body p-4">
                <form action="/vendor/register" method="POST" enctype="multipart/form-data">
                    <h5 class="mb-3">Personal Information</h5>
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" id="phone" name="phone" required>
                    </div>
                    
                    <hr class="my-4">
                    
                    <h5 class="mb-3">Business Information</h5>
                    <div class="mb-3">
                        <label for="business_name" class="form-label">Business Name</label>
                        <input type="text" class="form-control" id="business_name" name="business_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="location" class="form-label">Business Location</label>
                        <input type="text" class="form-control" id="location" name="location" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Business Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="logo" class="form-label">Business Logo (Optional)</label>
                            <input type="file" class="form-control image-input" id="logo" name="logo" data-preview="logo-preview">
                            <div class="mt-2">
                                <img id="logo-preview" class="img-thumbnail" style="max-height: 100px; display: none;">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="banner" class="form-label">Business Banner (Optional)</label>
                            <input type="file" class="form-control image-input" id="banner" name="banner" data-preview="banner-preview">
                            <div class="mt-2">
                                <img id="banner-preview" class="img-thumbnail" style="max-height: 100px; display: none;">
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Your vendor account will need to be approved by an administrator before you can start selling.
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Register as Vendor</button>
                    </div>
                </form>
            </div>
            <div class="card-footer bg-light">
                <div class="text-center">
                    <p class="mb-0">Already have an account? <a href="/login">Login</a></p>
                    <p class="mb-0 mt-2">Want to register as a customer? <a href="/register">Register as Customer</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
