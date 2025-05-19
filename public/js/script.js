/**
 * FoodieHub Custom JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        var alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            var bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
    
    // Quantity input handlers
    var quantityInputs = document.querySelectorAll('.quantity-input');
    
    quantityInputs.forEach(function(input) {
        var minusBtn = input.parentElement.querySelector('.quantity-minus');
        var plusBtn = input.parentElement.querySelector('.quantity-plus');
        
        if (minusBtn && plusBtn) {
            minusBtn.addEventListener('click', function() {
                var value = parseInt(input.value);
                if (value > 1) {
                    input.value = value - 1;
                    // Trigger change event
                    var event = new Event('change');
                    input.dispatchEvent(event);
                }
            });
            
            plusBtn.addEventListener('click', function() {
                var value = parseInt(input.value);
                input.value = value + 1;
                // Trigger change event
                var event = new Event('change');
                input.dispatchEvent(event);
            });
        }
    });
    
    // Cart quantity change handler
    var cartQuantityForms = document.querySelectorAll('.cart-quantity-form');
    
    cartQuantityForms.forEach(function(form) {
        var input = form.querySelector('.quantity-input');
        
        if (input) {
            input.addEventListener('change', function() {
                form.submit();
            });
        }
    });
    
    // Product filter form
    var filterForm = document.getElementById('product-filter-form');
    var filterInputs = document.querySelectorAll('.filter-input');
    
    if (filterForm && filterInputs.length > 0) {
        filterInputs.forEach(function(input) {
            input.addEventListener('change', function() {
                filterForm.submit();
            });
        });
    }
    
    // Star rating input
    var ratingInputs = document.querySelectorAll('.rating-input');
    
    ratingInputs.forEach(function(input) {
        var stars = input.querySelectorAll('.rating-star');
        var ratingValue = input.querySelector('input[type="hidden"]');
        
        stars.forEach(function(star, index) {
            // Hover effect
            star.addEventListener('mouseenter', function() {
                // Fill stars up to current
                for (var i = 0; i <= index; i++) {
                    stars[i].classList.remove('far');
                    stars[i].classList.add('fas');
                }
                // Empty stars after current
                for (var i = index + 1; i < stars.length; i++) {
                    stars[i].classList.remove('fas');
                    stars[i].classList.add('far');
                }
            });
            
            // Click to set rating
            star.addEventListener('click', function() {
                ratingValue.value = index + 1;
                
                // Fill stars up to selected
                for (var i = 0; i <= index; i++) {
                    stars[i].classList.remove('far');
                    stars[i].classList.add('fas');
                }
                // Empty stars after selected
                for (var i = index + 1; i < stars.length; i++) {
                    stars[i].classList.remove('fas');
                    stars[i].classList.add('far');
                }
            });
        });
        
        // Reset on mouse leave if no rating selected
        input.addEventListener('mouseleave', function() {
            var rating = parseInt(ratingValue.value);
            
            if (rating > 0) {
                // Fill stars up to rating
                for (var i = 0; i < rating; i++) {
                    stars[i].classList.remove('far');
                    stars[i].classList.add('fas');
                }
                // Empty stars after rating
                for (var i = rating; i < stars.length; i++) {
                    stars[i].classList.remove('fas');
                    stars[i].classList.add('far');
                }
            } else {
                // Empty all stars
                stars.forEach(function(star) {
                    star.classList.remove('fas');
                    star.classList.add('far');
                });
            }
        });
    });
    
    // Image preview for file inputs
    var imageInputs = document.querySelectorAll('.image-input');
    
    imageInputs.forEach(function(input) {
        var preview = document.getElementById(input.dataset.preview);
        
        if (preview) {
            input.addEventListener('change', function() {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    }
                    
                    reader.readAsDataURL(input.files[0]);
                }
            });
        }
    });
});
