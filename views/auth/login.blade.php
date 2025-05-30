@extends('layouts.main')

@section('title', $title)

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Login</h4>
            </div>
            <div class="card-body p-4">
                <form action="/login" method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Login</button>
                    </div>
                </form>
            </div>
            <div class="card-footer bg-light">
                <div class="text-center">
                    <p class="mb-0">Don't have an account? <a href="/register">Register</a></p>
                    <p class="mb-0 mt-2">Want to become a vendor? <a href="/vendor/register">Register as Vendor</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
