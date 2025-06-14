// Main JavaScript file for Event Booking System

$(document).ready(function() {
    // Initialize cart count
    updateCartCount();
    
    // Smooth scrolling for anchor links
    $('a[href^="#"]').on('click', function(event) {
        var target = $(this.getAttribute('href'));
        if (target.length) {
            event.preventDefault();
            $('html, body').stop().animate({
                scrollTop: target.offset().top - 70
            }, 1000);
        }
    });
    
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
    
    // Add to cart functionality
    $(document).on('click', '.add-to-cart', function(e) {
        e.preventDefault();
        
        var eventId = $(this).data('event-id');
        var quantity = $('#quantity').val() || 1;
        var button = $(this);
        var originalText = button.html();
        
        // Show loading state
        button.html('<span class="loading"></span> Adding...');
        button.prop('disabled', true);
        
        $.ajax({
            url: 'ajax/add_to_cart.php',
            method: 'POST',
            data: {
                event_id: eventId,
                quantity: quantity
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    updateCartCount();
                    
                    // Update button temporarily
                    button.html('<i class="fas fa-check me-2"></i>Added!');
                    button.removeClass('btn-primary').addClass('btn-success');
                    
                    setTimeout(function() {
                        button.html(originalText);
                        button.removeClass('btn-success').addClass('btn-primary');
                        button.prop('disabled', false);
                    }, 2000);
                } else {
                    showAlert('danger', response.message);
                    button.html(originalText);
                    button.prop('disabled', false);
                }
            },
            error: function() {
                showAlert('danger', 'An error occurred. Please try again.');
                button.html(originalText);
                button.prop('disabled', false);
            }
        });
    });
    
    // Update cart quantity
    $(document).on('click', '.update-quantity', function(e) {
        e.preventDefault();
        
        var cartId = $(this).data('cart-id');
        var action = $(this).data('action');
        var quantityElement = $('#quantity-' + cartId);
        var currentQuantity = parseInt(quantityElement.text());
        var newQuantity = action === 'increase' ? currentQuantity + 1 : currentQuantity - 1;
        
        if (newQuantity < 1) {
            return;
        }
        
        $.ajax({
            url: 'ajax/update_cart.php',
            method: 'POST',
            data: {
                cart_id: cartId,
                quantity: newQuantity
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    quantityElement.text(newQuantity);
                    $('#subtotal-' + cartId).text('$' + response.subtotal);
                    $('#cart-total').text('$' + response.total);
                    updateCartCount();
                } else {
                    showAlert('danger', response.message);
                }
            },
            error: function() {
                showAlert('danger', 'An error occurred. Please try again.');
            }
        });
    });
    
    // Remove from cart
    $(document).on('click', '.remove-from-cart', function(e) {
        e.preventDefault();
        
        if (!confirm('Are you sure you want to remove this item from your cart?')) {
            return;
        }
        
        var cartId = $(this).data('cart-id');
        var cartItem = $('#cart-item-' + cartId);
        
        $.ajax({
            url: 'ajax/remove_from_cart.php',
            method: 'POST',
            data: {
                cart_id: cartId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    cartItem.fadeOut(function() {
                        $(this).remove();
                        $('#cart-total').text('$' + response.total);
                        updateCartCount();
                        
                        if (response.cart_empty) {
                            location.reload();
                        }
                    });
                    showAlert('success', response.message);
                } else {
                    showAlert('danger', response.message);
                }
            },
            error: function() {
                showAlert('danger', 'An error occurred. Please try again.');
            }
        });
    });
    
    // Form validation
    $('form').on('submit', function(e) {
        var form = $(this);
        var isValid = true;
        
        // Check required fields
        form.find('input[required], select[required], textarea[required]').each(function() {
            var field = $(this);
            if (!field.val().trim()) {
                field.addClass('is-invalid');
                isValid = false;
            } else {
                field.removeClass('is-invalid');
            }
        });
        
        // Email validation
        form.find('input[type="email"]').each(function() {
            var email = $(this);
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (email.val() && !emailRegex.test(email.val())) {
                email.addClass('is-invalid');
                isValid = false;
            }
        });
        
        // Password confirmation
        var password = form.find('input[name="password"]');
        var confirmPassword = form.find('input[name="confirm_password"]');
        if (password.length && confirmPassword.length) {
            if (password.val() !== confirmPassword.val()) {
                confirmPassword.addClass('is-invalid');
                showAlert('danger', 'Passwords do not match.');
                isValid = false;
            }
        }
        
        if (!isValid) {
            e.preventDefault();
            showAlert('danger', 'Please fill in all required fields correctly.');
        }
    });
    
    // Clear form validation on input
    $('input, select, textarea').on('input change', function() {
        $(this).removeClass('is-invalid');
    });
    
    // Search functionality
    $('#search-form').on('submit', function(e) {
        var searchInput = $('#search');
        if (!searchInput.val().trim()) {
            e.preventDefault();
            searchInput.focus();
            showAlert('warning', 'Please enter a search term.');
        }
    });
    
    // Date picker minimum date
    $('input[type="date"]').attr('min', new Date().toISOString().split('T')[0]);
    
    // Tooltip initialization
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Popover initialization
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
});

// Update cart count
function updateCartCount() {
    $.ajax({
        url: 'ajax/get_cart_count.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#cart-count').text(response.count);
                if (response.count > 0) {
                    $('#cart-count').show();
                } else {
                    $('#cart-count').hide();
                }
            }
        }
    });
}

// Show alert message
function showAlert(type, message) {
    var alertHtml = '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' +
                    message +
                    '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                    '</div>';
    
    // Remove existing alerts
    $('.alert').remove();
    
    // Add new alert
    $('main.main-content').prepend('<div class="container mt-3">' + alertHtml + '</div>');
    
    // Auto-hide after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
}

// Format currency
function formatCurrency(amount) {
    return '$' + parseFloat(amount).toFixed(2);
}

// Confirm action
function confirmAction(message) {
    return confirm(message || 'Are you sure you want to perform this action?');
}

// Loading state for buttons
function setButtonLoading(button, loading) {
    if (loading) {
        button.data('original-text', button.html());
        button.html('<span class="loading"></span> Loading...');
        button.prop('disabled', true);
    } else {
        button.html(button.data('original-text'));
        button.prop('disabled', false);
    }
}

// Scroll to top
function scrollToTop() {
    $('html, body').animate({scrollTop: 0}, 'slow');
}

// Add scroll to top button
$(window).scroll(function() {
    if ($(this).scrollTop() > 100) {
        if ($('#scroll-to-top').length === 0) {
            $('body').append('<button id="scroll-to-top" class="btn btn-primary position-fixed" style="bottom: 20px; right: 20px; z-index: 1000; border-radius: 50%; width: 50px; height: 50px;"><i class="fas fa-arrow-up"></i></button>');
        }
        $('#scroll-to-top').fadeIn();
    } else {
        $('#scroll-to-top').fadeOut();
    }
});

$(document).on('click', '#scroll-to-top', function() {
    scrollToTop();
});
