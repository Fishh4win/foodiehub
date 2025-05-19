@extends('layouts.admin')

@section('title', $title)

@section('styles')
<style>
    /* Modal fix styles */
    .modal {
        will-change: transform;
        backface-visibility: hidden;
        transform: translateZ(0);
    }

    .modal-backdrop {
        will-change: opacity;
        backface-visibility: hidden;
    }

    .modal.fade .modal-dialog {
        transition: transform 0.2s ease-out !important;
    }

    .modal-content {
        border: none;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    /* Vendor modal specific styles */
    #viewVendorModal .modal-body {
        padding: 1.5rem;
    }

    /* Fix for modal animation */
    @keyframes modalFadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Apply animation to modal content */
    .modal.show .modal-content {
        animation: modalFadeIn 0.2s ease-out;
    }

    /* Fix for modal open body padding */
    body.modal-open-fix {
        padding-right: 0 !important;
    }

    /* Fix for modal scrollbar */
    .modal-open {
        overflow: hidden;
        padding-right: 0 !important;
    }
</style>
@endsection

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Manage Vendors</h5>
        <div>
            <a href="#" class="btn btn-sm btn-outline-primary me-2" data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="fas fa-filter me-1"></i> Filter
            </a>
        </div>
    </div>
    <div class="card-body">
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link active" href="#all-vendors" data-bs-toggle="tab">All Vendors</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#pending-approval" data-bs-toggle="tab">Pending Approval</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#approved" data-bs-toggle="tab">Approved</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#featured" data-bs-toggle="tab">Featured</a>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="all-vendors">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Vendor</th>
                                <th>Contact</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vendors as $vendor)
                                <tr>
                                    <td>{{ $vendor['id'] }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if(isset($vendor['logo']) && $vendor['logo'])
                                                <img src="/{{ $vendor['logo'] }}" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;" alt="{{ $vendor['business_name'] }}">
                                            @else
                                                <div class="bg-light rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                    <i class="fas fa-store text-secondary"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <p class="mb-0 fw-bold">{{ $vendor['business_name'] }}</p>
                                                <small class="text-muted">{{ $vendor['name'] }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="mb-0">{{ $vendor['email'] }}</p>
                                        <small class="text-muted">{{ $vendor['phone'] ?? 'No phone' }}</small>
                                    </td>
                                    <td>{{ $vendor['location'] }}</td>
                                    <td>
                                        @if($vendor['is_approved'])
                                            <span class="badge badge-approved">Approved</span>
                                        @else
                                            <span class="badge badge-pending">Pending</span>
                                        @endif

                                        @if($vendor['is_featured'])
                                            <span class="badge badge-featured">Featured</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="#" class="btn btn-sm btn-outline-primary vendor-modal-trigger" data-bs-toggle="modal" data-bs-target="#viewVendorModal{{ $vendor['id'] }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="/admin/vendors/edit/{{ $vendor['id'] }}" class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip" title="Edit Vendor">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="/admin/vendors/{{ $vendor['id'] }}/products" class="btn btn-sm btn-outline-dark" data-bs-toggle="tooltip" title="View Products">
                                                <i class="fas fa-hamburger"></i>
                                            </a>

                                            @if(!$vendor['is_approved'])
                                                <form action="/admin/vendors/approve" method="POST">
                                                    <input type="hidden" name="vendor_id" value="{{ $vendor['id'] }}">
                                                    <button type="submit" class="btn btn-sm btn-outline-success" data-bs-toggle="tooltip" title="Approve Vendor">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <form action="/admin/vendors/disapprove" method="POST">
                                                    <input type="hidden" name="vendor_id" value="{{ $vendor['id'] }}">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" title="Disapprove Vendor">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            @if(!$vendor['is_featured'])
                                                <form action="/admin/vendors/feature" method="POST">
                                                    <input type="hidden" name="vendor_id" value="{{ $vendor['id'] }}">
                                                    <button type="submit" class="btn btn-sm btn-outline-warning" data-bs-toggle="tooltip" title="Feature Vendor">
                                                        <i class="fas fa-star"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <form action="/admin/vendors/unfeature" method="POST">
                                                    <input type="hidden" name="vendor_id" value="{{ $vendor['id'] }}">
                                                    <button type="submit" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="Unfeature Vendor">
                                                        <i class="far fa-star"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>

                                        <!-- View Vendor Modal -->
                                        <div class="modal fade" id="viewVendorModal{{ $vendor['id'] }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Vendor Details</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-md-4 text-center mb-4 mb-md-0">
                                                                @if(isset($vendor['logo']) && $vendor['logo'])
                                                                    <img src="/{{ $vendor['logo'] }}" class="img-fluid rounded mb-3" style="max-height: 200px;" alt="{{ $vendor['business_name'] }}">
                                                                @else
                                                                    <div class="bg-light rounded d-flex align-items-center justify-content-center mb-3" style="height: 200px;">
                                                                        <i class="fas fa-store fa-4x text-secondary"></i>
                                                                    </div>
                                                                @endif

                                                                <div class="d-grid gap-2">
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
                                                            <div class="col-md-8">
                                                                <h4>{{ $vendor['business_name'] }}</h4>
                                                                <p class="text-muted">{{ $vendor['description'] }}</p>

                                                                <div class="row mb-3">
                                                                    <div class="col-md-6">
                                                                        <h6>Owner Information</h6>
                                                                        <p class="mb-1"><strong>Name:</strong> {{ $vendor['name'] }}</p>
                                                                        <p class="mb-1"><strong>Email:</strong> {{ $vendor['email'] }}</p>
                                                                        <p class="mb-1"><strong>Phone:</strong> {{ $vendor['phone'] ?? 'Not provided' }}</p>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <h6>Business Information</h6>
                                                                        <p class="mb-1"><strong>Location:</strong> {{ $vendor['location'] }}</p>
                                                                        <p class="mb-1"><strong>Status:</strong>
                                                                            @if($vendor['is_approved'])
                                                                                <span class="badge badge-approved">Approved</span>
                                                                            @else
                                                                                <span class="badge badge-pending">Pending</span>
                                                                            @endif
                                                                        </p>
                                                                        <p class="mb-1"><strong>Featured:</strong>
                                                                            @if($vendor['is_featured'])
                                                                                <span class="badge badge-featured">Yes</span>
                                                                            @else
                                                                                <span class="badge bg-secondary">No</span>
                                                                            @endif
                                                                        </p>
                                                                    </div>
                                                                </div>

                                                                <div class="d-flex justify-content-between mb-3">
                                                                    <a href="/admin/vendors/{{ $vendor['id'] }}/products" class="btn btn-outline-primary">
                                                                        <i class="fas fa-hamburger me-2"></i> Manage Products
                                                                    </a>
                                                                    <a href="/admin/orders?vendor={{ $vendor['id'] }}" class="btn btn-outline-info">
                                                                        <i class="fas fa-shopping-cart me-2"></i> View Orders
                                                                    </a>
                                                                </div>
                                                                <div class="d-grid">
                                                                    <a href="/admin/vendors/edit/{{ $vendor['id'] }}" class="btn btn-primary">
                                                                        <i class="fas fa-edit me-2"></i> Edit Vendor
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
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

            <div class="tab-pane fade" id="pending-approval">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Vendor</th>
                                <th>Contact</th>
                                <th>Location</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vendors as $vendor)
                                @if(!$vendor['is_approved'])
                                    <tr>
                                        <td>{{ $vendor['id'] }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if(isset($vendor['logo']) && $vendor['logo'])
                                                    <img src="/{{ $vendor['logo'] }}" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;" alt="{{ $vendor['business_name'] }}">
                                                @else
                                                    <div class="bg-light rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <i class="fas fa-store text-secondary"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <p class="mb-0 fw-bold">{{ $vendor['business_name'] }}</p>
                                                    <small class="text-muted">{{ $vendor['name'] }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="mb-0">{{ $vendor['email'] }}</p>
                                            <small class="text-muted">{{ $vendor['phone'] ?? 'No phone' }}</small>
                                        </td>
                                        <td>{{ $vendor['location'] }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="#" class="btn btn-sm btn-outline-primary vendor-modal-trigger" data-bs-toggle="modal" data-bs-target="#viewVendorModal{{ $vendor['id'] }}">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="/admin/vendors/edit/{{ $vendor['id'] }}" class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip" title="Edit Vendor">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="/admin/vendors/{{ $vendor['id'] }}/products" class="btn btn-sm btn-outline-dark" data-bs-toggle="tooltip" title="View Products">
                                                    <i class="fas fa-hamburger"></i>
                                                </a>
                                                <form action="/admin/vendors/approve" method="POST">
                                                    <input type="hidden" name="vendor_id" value="{{ $vendor['id'] }}">
                                                    <button type="submit" class="btn btn-sm btn-outline-success" data-bs-toggle="tooltip" title="Approve Vendor">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="approved">
                <!-- Similar table for approved vendors -->
            </div>

            <div class="tab-pane fade" id="featured">
                <!-- Similar table for featured vendors -->
            </div>
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filter Vendors</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="/admin/vendors" method="GET">
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="status-all" name="status[]" value="all" checked>
                            <label class="form-check-label" for="status-all">All</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="status-approved" name="status[]" value="approved">
                            <label class="form-check-label" for="status-approved">Approved</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="status-pending" name="status[]" value="pending">
                            <label class="form-check-label" for="status-pending">Pending</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="status-featured" name="status[]" value="featured">
                            <label class="form-check-label" for="status-featured">Featured</label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="date-range" class="form-label">Date Range</label>
                        <select class="form-select" id="date-range" name="date_range">
                            <option value="all">All Time</option>
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                            <option value="year">This Year</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fix for modal flickering
        document.body.classList.add('modal-open-fix');

        // Store modal instances to prevent flickering
        const vendorModals = {};

        // Initialize all vendor modals
        document.querySelectorAll('[id^="viewVendorModal"]').forEach(modalElement => {
            const modalId = modalElement.id;

            // Detach modal from DOM and reattach to body to prevent flickering
            const modalContent = modalElement.innerHTML;
            const modalParent = modalElement.parentNode;
            modalParent.removeChild(modalElement);

            // Create a new modal element
            const newModal = document.createElement('div');
            newModal.id = modalId;
            newModal.className = modalElement.className;
            newModal.setAttribute('tabindex', '-1');
            newModal.setAttribute('aria-hidden', 'true');
            newModal.innerHTML = modalContent;

            // Append to body instead of being nested in table
            document.body.appendChild(newModal);

            // Initialize Bootstrap modal
            const modalInstance = new bootstrap.Modal(newModal, {
                backdrop: 'static',  // Prevent closing when clicking outside
                keyboard: true       // Allow ESC key to close
            });

            // Store modal instance and state
            vendorModals[modalId] = {
                instance: modalInstance,
                isShown: false,
                element: newModal
            };

            // Prevent default behavior and handle modal events
            newModal.addEventListener('show.bs.modal', function(event) {
                // Prevent default behavior if needed
                if (vendorModals[modalId].isShown) {
                    event.preventDefault();
                    return;
                }

                // Update state
                vendorModals[modalId].isShown = true;
            });

            newModal.addEventListener('hidden.bs.modal', function() {
                // Update state
                vendorModals[modalId].isShown = false;
            });
        });

        // Handle modal trigger clicks
        document.querySelectorAll('.vendor-modal-trigger').forEach(trigger => {
            const targetId = trigger.getAttribute('data-bs-target').substring(1); // Remove the # character

            trigger.addEventListener('click', function(event) {
                event.preventDefault();

                if (vendorModals[targetId]) {
                    // Only show if not already shown
                    if (!vendorModals[targetId].isShown) {
                        vendorModals[targetId].instance.show();
                    }
                }
            });
        });
    });
</script>
@endsection
