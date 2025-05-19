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

    /* User modal specific styles */
    .user-modal .modal-body {
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
        <h5 class="mb-0">All Users</h5>
        <div>
            <a href="#" class="btn btn-sm btn-outline-primary me-2" data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="fas fa-filter me-1"></i> Filter
            </a>
            <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="fas fa-plus me-1"></i> Add User
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Joined</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>{{ $user['id'] }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if(isset($user['profile_image']) && $user['profile_image'])
                                        <img src="/{{ $user['profile_image'] }}" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;" alt="{{ $user['name'] }}">
                                    @else
                                        <div class="bg-light rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="fas fa-user text-secondary"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="mb-0 fw-bold">{{ $user['name'] }}</p>
                                        @if($user['role'] === 'vendor')
                                            <small class="text-muted">
                                                <i class="fas fa-store me-1"></i> Vendor
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>{{ $user['email'] }}</td>
                            <td>
                                <span class="badge bg-{{ $user['role'] === 'admin' ? 'danger' : ($user['role'] === 'vendor' ? 'success' : 'primary') }}">
                                    {{ ucfirst($user['role']) }}
                                </span>
                            </td>
                            <td>{{ date('M d, Y', strtotime($user['created_at'])) }}</td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="status-{{ $user['id'] }}" checked>
                                </div>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user['id'] }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteUserModal{{ $user['id'] }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>

                                <!-- Edit User Modal -->
                                <div class="modal fade" id="editUserModal{{ $user['id'] }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit User</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="/admin/users/edit" method="POST">
                                                    <input type="hidden" name="user_id" value="{{ $user['id'] }}">

                                                    <div class="mb-3">
                                                        <label for="name{{ $user['id'] }}" class="form-label">Name</label>
                                                        <input type="text" class="form-control" id="name{{ $user['id'] }}" name="name" value="{{ $user['name'] }}" required>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="email{{ $user['id'] }}" class="form-label">Email</label>
                                                        <input type="email" class="form-control" id="email{{ $user['id'] }}" name="email" value="{{ $user['email'] }}" required>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="role{{ $user['id'] }}" class="form-label">Role</label>
                                                        <select class="form-select" id="role{{ $user['id'] }}" name="role" required>
                                                            <option value="customer" {{ $user['role'] === 'customer' ? 'selected' : '' }}>Customer</option>
                                                            <option value="vendor" {{ $user['role'] === 'vendor' ? 'selected' : '' }}>Vendor</option>
                                                            <option value="admin" {{ $user['role'] === 'admin' ? 'selected' : '' }}>Admin</option>
                                                        </select>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="password{{ $user['id'] }}" class="form-label">New Password (leave blank to keep current)</label>
                                                        <input type="password" class="form-control" id="password{{ $user['id'] }}" name="password">
                                                    </div>

                                                    <div class="d-flex justify-content-end">
                                                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Delete User Modal -->
                                <div class="modal fade" id="deleteUserModal{{ $user['id'] }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Delete User</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Are you sure you want to delete the user <strong>{{ $user['name'] }}</strong>?</p>
                                                <p class="text-danger">This action cannot be undone.</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <form action="/admin/users/delete" method="POST">
                                                    <input type="hidden" name="user_id" value="{{ $user['id'] }}">
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

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="/admin/users/add" method="POST">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="customer">Customer</option>
                            <option value="vendor">Vendor</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filter Users</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="/admin/users" method="GET">
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="role-all" name="role[]" value="all" checked>
                            <label class="form-check-label" for="role-all">All</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="role-customer" name="role[]" value="customer">
                            <label class="form-check-label" for="role-customer">Customer</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="role-vendor" name="role[]" value="vendor">
                            <label class="form-check-label" for="role-vendor">Vendor</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="role-admin" name="role[]" value="admin">
                            <label class="form-check-label" for="role-admin">Admin</label>
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
        const userModals = {};

        // Process edit user modals
        document.querySelectorAll('[id^="editUserModal"]').forEach(modalElement => {
            const modalId = modalElement.id;

            // Detach modal from DOM and reattach to body to prevent flickering
            const modalContent = modalElement.innerHTML;
            const modalParent = modalElement.parentNode;
            modalParent.removeChild(modalElement);

            // Create a new modal element
            const newModal = document.createElement('div');
            newModal.id = modalId;
            newModal.className = modalElement.className + ' user-modal';
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
            userModals[modalId] = {
                instance: modalInstance,
                isShown: false,
                element: newModal
            };

            // Prevent default behavior and handle modal events
            newModal.addEventListener('show.bs.modal', function(event) {
                // Prevent default behavior if needed
                if (userModals[modalId].isShown) {
                    event.preventDefault();
                    return;
                }

                // Update state
                userModals[modalId].isShown = true;
            });

            newModal.addEventListener('hidden.bs.modal', function() {
                // Update state
                userModals[modalId].isShown = false;
            });
        });

        // Process delete user modals
        document.querySelectorAll('[id^="deleteUserModal"]').forEach(modalElement => {
            const modalId = modalElement.id;

            // Detach modal from DOM and reattach to body to prevent flickering
            const modalContent = modalElement.innerHTML;
            const modalParent = modalElement.parentNode;
            modalParent.removeChild(modalElement);

            // Create a new modal element
            const newModal = document.createElement('div');
            newModal.id = modalId;
            newModal.className = modalElement.className + ' user-modal';
            newModal.setAttribute('tabindex', '-1');
            newModal.setAttribute('aria-hidden', 'true');
            newModal.innerHTML = modalContent;

            // Append to body instead of being nested in table
            document.body.appendChild(newModal);

            // Initialize Bootstrap modal
            const modalInstance = new bootstrap.Modal(newModal, {
                backdrop: 'static',
                keyboard: true
            });

            // Store modal instance and state
            userModals[modalId] = {
                instance: modalInstance,
                isShown: false,
                element: newModal
            };

            // Prevent default behavior and handle modal events
            newModal.addEventListener('show.bs.modal', function(event) {
                if (userModals[modalId].isShown) {
                    event.preventDefault();
                    return;
                }

                userModals[modalId].isShown = true;
            });

            newModal.addEventListener('hidden.bs.modal', function() {
                userModals[modalId].isShown = false;
            });
        });

        // Process add user and filter modals
        ['addUserModal', 'filterModal'].forEach(modalId => {
            const modalElement = document.getElementById(modalId);
            if (modalElement) {
                // Initialize Bootstrap modal
                const modalInstance = new bootstrap.Modal(modalElement, {
                    backdrop: 'static',
                    keyboard: true
                });

                // Store modal instance and state
                userModals[modalId] = {
                    instance: modalInstance,
                    isShown: false,
                    element: modalElement
                };

                // Prevent default behavior and handle modal events
                modalElement.addEventListener('show.bs.modal', function(event) {
                    if (userModals[modalId].isShown) {
                        event.preventDefault();
                        return;
                    }

                    userModals[modalId].isShown = true;
                });

                modalElement.addEventListener('hidden.bs.modal', function() {
                    userModals[modalId].isShown = false;
                });
            }
        });

        // Handle modal trigger clicks for edit and delete modals
        document.querySelectorAll('[data-bs-toggle="modal"]').forEach(trigger => {
            const targetId = trigger.getAttribute('data-bs-target').substring(1); // Remove the # character

            if (targetId.startsWith('editUserModal') || targetId.startsWith('deleteUserModal') ||
                targetId === 'addUserModal' || targetId === 'filterModal') {

                trigger.addEventListener('click', function(event) {
                    event.preventDefault();

                    if (userModals[targetId]) {
                        // Only show if not already shown
                        if (!userModals[targetId].isShown) {
                            userModals[targetId].instance.show();
                        }
                    }
                });
            }
        });
    });
</script>
@endsection