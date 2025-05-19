/**
 * FoodieHub Admin Dashboard JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Initialize popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
    
    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        var alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            var bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
    
    // Confirmation dialogs
    var confirmForms = document.querySelectorAll('form[data-confirm]');
    confirmForms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            var message = this.getAttribute('data-confirm') || 'Are you sure you want to perform this action?';
            if (!confirm(message)) {
                e.preventDefault();
                return false;
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
    
    // Data tables (if available)
    if (typeof $.fn.DataTable !== 'undefined') {
        $('.data-table').DataTable({
            responsive: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search...",
            }
        });
    }
    
    // Charts (if available)
    if (typeof Chart !== 'undefined') {
        // Sales Chart
        var salesChartCanvas = document.getElementById('salesChart');
        if (salesChartCanvas) {
            var salesChart = new Chart(salesChartCanvas, {
                type: 'line',
                data: {
                    labels: salesChartData.labels,
                    datasets: [{
                        label: 'Sales',
                        data: salesChartData.data,
                        backgroundColor: 'rgba(13, 110, 253, 0.1)',
                        borderColor: '#0d6efd',
                        borderWidth: 2,
                        pointBackgroundColor: '#0d6efd',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
        
        // Orders Chart
        var ordersChartCanvas = document.getElementById('ordersChart');
        if (ordersChartCanvas) {
            var ordersChart = new Chart(ordersChartCanvas, {
                type: 'doughnut',
                data: {
                    labels: ordersChartData.labels,
                    datasets: [{
                        data: ordersChartData.data,
                        backgroundColor: [
                            '#ffc107', // Pending
                            '#0dcaf0', // Preparing
                            '#6f42c1', // Out for delivery
                            '#198754', // Delivered
                            '#dc3545'  // Cancelled
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
    }
});
