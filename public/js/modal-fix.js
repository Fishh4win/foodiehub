/**
 * FoodieHub Modal Fix
 * This script fixes issues with modal flickering and refreshing
 */

document.addEventListener('DOMContentLoaded', function() {
    // Store modal state to prevent flickering
    const modalState = {};
    
    // Get all modals on the page
    const modals = document.querySelectorAll('.modal');
    
    // Initialize Bootstrap modals properly
    modals.forEach(modalElement => {
        const modalId = modalElement.id;
        if (!modalId) return;
        
        // Create modal instance if it doesn't exist
        if (!window.bootstrap) {
            console.error('Bootstrap JavaScript is not loaded');
            return;
        }
        
        const modalInstance = new bootstrap.Modal(modalElement);
        
        // Store modal instance and state
        modalState[modalId] = {
            instance: modalInstance,
            isShown: false,
            content: modalElement.innerHTML
        };
        
        // Handle modal events to prevent flickering
        modalElement.addEventListener('show.bs.modal', function(event) {
            // Prevent default behavior if needed
            if (modalState[modalId].isShown) {
                event.preventDefault();
                return;
            }
            
            // Update state
            modalState[modalId].isShown = true;
        });
        
        modalElement.addEventListener('hidden.bs.modal', function() {
            // Update state
            modalState[modalId].isShown = false;
            
            // Restore original content if it was changed
            if (modalElement.innerHTML !== modalState[modalId].content) {
                modalElement.innerHTML = modalState[modalId].content;
            }
        });
    });
    
    // Fix for dynamic modals (like those in loops with IDs containing item IDs)
    const modalTriggers = document.querySelectorAll('[data-bs-toggle="modal"]');
    
    modalTriggers.forEach(trigger => {
        const targetSelector = trigger.getAttribute('data-bs-target');
        if (!targetSelector) return;
        
        const modalElement = document.querySelector(targetSelector);
        if (!modalElement) return;
        
        // Store original content for this modal
        const modalId = modalElement.id;
        if (!modalId) return;
        
        if (!modalState[modalId]) {
            const modalInstance = new bootstrap.Modal(modalElement);
            modalState[modalId] = {
                instance: modalInstance,
                isShown: false,
                content: modalElement.innerHTML
            };
        }
        
        // Replace default click behavior to prevent flickering
        trigger.addEventListener('click', function(event) {
            event.preventDefault();
            
            // Only show if not already shown
            if (!modalState[modalId].isShown) {
                modalState[modalId].instance.show();
            }
        });
    });
    
    // Fix for modals with dynamic content loaded via AJAX
    document.addEventListener('click', function(event) {
        // Check if the clicked element has a data attribute for loading content
        const dynamicContentTrigger = event.target.closest('[data-load-content]');
        
        if (dynamicContentTrigger) {
            const targetModalSelector = dynamicContentTrigger.getAttribute('data-bs-target');
            const contentUrl = dynamicContentTrigger.getAttribute('data-load-content');
            
            if (targetModalSelector && contentUrl) {
                event.preventDefault();
                
                const modalElement = document.querySelector(targetModalSelector);
                if (!modalElement) return;
                
                const modalId = modalElement.id;
                if (!modalId || !modalState[modalId]) return;
                
                // Show loading indicator in modal
                const modalBody = modalElement.querySelector('.modal-body');
                if (modalBody) {
                    modalBody.innerHTML = '<div class="text-center p-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-3">Loading content...</p></div>';
                }
                
                // Show modal
                modalState[modalId].instance.show();
                
                // Load content via AJAX (simplified example)
                fetch(contentUrl)
                    .then(response => response.text())
                    .then(html => {
                        if (modalBody && modalState[modalId].isShown) {
                            modalBody.innerHTML = html;
                        }
                    })
                    .catch(error => {
                        if (modalBody && modalState[modalId].isShown) {
                            modalBody.innerHTML = '<div class="alert alert-danger">Error loading content</div>';
                        }
                        console.error('Error loading modal content:', error);
                    });
            }
        }
    });
    
    // Fix for update-status-btn in vendor orders
    const updateStatusBtns = document.querySelectorAll('.update-status-btn');
    
    updateStatusBtns.forEach(button => {
        button.addEventListener('click', function() {
            const orderId = this.getAttribute('data-order-id');
            const status = this.getAttribute('data-status');
            
            const orderIdInput = document.getElementById('order_id');
            const statusInput = document.getElementById('status');
            
            if (orderIdInput && statusInput) {
                orderIdInput.value = orderId;
                statusInput.value = status;
            }
        });
    });
});
