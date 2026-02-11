$(document).ready(function () {
    // Load cart from localStorage
    let cart = {};
    if (localStorage.getItem("cart") != null) {
        cart = JSON.parse(localStorage.getItem("cart"));
    }
    
    // Display cart on page load
    displayCart(cart);
    updateCartSummary(cart);

    // Handle form submission
    $('#orderForm').on('submit', function(e) {
        e.preventDefault();
        
        // Validate form
        if (!this.checkValidity()) {
            this.classList.add('was-validated');
            return;
        }

        // Check if cart is empty
        if (Object.keys(cart).length === 0) {
            showAlert('error', 'Your cart is empty. Please add items before placing an order.');
            return;
        }

        // Gather form data
        const orderData = {
            name: $('#guestName').val().trim(),
            email: $('#guestEmail').val().trim(),
            phone: $('#guestPhone').val().trim(),
            address: $('#guestAddress').val().trim(),
            state: $('#guestState').val().trim(),
            city: $('#guestCity').val().trim(),
            special_instructions: $('#specialInstructions').val().trim(),
            cart: cart
        };

        // Validate form data
        const validation = validateOrderData(orderData);
        if (!validation.valid) {
            showAlert('error', validation.message);
            return;
        }

        // Disable submit button and show loading state
        const $submitBtn = $('#submitBtn');
        const $submitBtnText = $('#submitBtnText');
        const $submitBtnLoader = $('#submitBtnLoader');
        
        $submitBtn.prop('disabled', true);
        $submitBtnText.hide();
        $submitBtnLoader.show();

        // Send order to backend
        submitOrder(orderData, function(response) {
            $submitBtn.prop('disabled', false);
            $submitBtnText.show();
            $submitBtnLoader.hide();

            if (response.success) {
                // Show success message
                showOrderConfirmation(response);
                
                // Clear cart
                localStorage.removeItem('cart');
                cart = {};
                
                // Reset form
                document.getElementById('orderForm').reset();
                document.getElementById('orderForm').classList.remove('was-validated');
                
                // Redirect to menu after 3 seconds
                setTimeout(function() {
                    window.location.href = 'menu.html';
                }, 3000);
            } else {
                showAlert('error', response.message || 'Failed to place order. Please try again.');
            }
        });
    });
});

/**
 * Display cart items on the order page
 */
function displayCart(cart) {
    const $itemsContainer = $('#items');
    const $emptyMessage = $('#emptyCartMessage');
    
    if (Object.keys(cart).length === 0) {
        $itemsContainer.html('');
        $emptyMessage.show();
        return;
    }
    
    $emptyMessage.hide();
    
    let cartHtml = '<div class="cart-items-list">';
    let itemIndex = 1;
    
    for (let itemId in cart) {
        const item = cart[itemId];
        const itemTotal = (item.price * item.quantity).toLocaleString('en-IN');
        const itemPrice = item.price.toLocaleString('en-IN');
        
        cartHtml += `
            <div class="cart-item">
                <div class="item-number">${itemIndex}</div>
                <div class="item-details">
                    <div class="item-name">${escapeHtml(item.name)}</div>
                    <div class="item-specs">
                        <span class="item-qty">Qty: ${item.quantity}</span>
                        <span class="item-unit-price">₹${itemPrice}</span>
                    </div>
                </div>
                <div class="item-total">₹${itemTotal}</div>
            </div>
        `;
        itemIndex++;
    }
    
    cartHtml += '</div>';
    $itemsContainer.html(cartHtml);
}

/**
 * Update cart summary (total items and total price)
 */
function updateCartSummary(cart) {
    let totalItems = 0;
    let totalPrice = 0;
    
    for (let itemId in cart) {
        const item = cart[itemId];
        totalItems += item.quantity;
        totalPrice += (item.price * item.quantity);
    }
    
    $('#totalItems').text(totalItems);
    $('#totalPrice').text('₹' + totalPrice.toLocaleString('en-IN'));
}

/**
 * Validate order data before submission
 */
function validateOrderData(data) {
    // Check name
    if (!data.name || data.name.length < 2) {
        return { valid: false, message: 'Please enter a valid name (at least 2 characters).' };
    }
    
    // Check email
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(data.email)) {
        return { valid: false, message: 'Please enter a valid email address.' };
    }
    
    // Check phone
    const phoneRegex = /^\d{10}$/;
    if (!phoneRegex.test(data.phone)) {
        return { valid: false, message: 'Phone number must be exactly 10 digits.' };
    }
    
    // Check address
    if (!data.address || data.address.length < 5) {
        return { valid: false, message: 'Please enter a valid address (at least 5 characters).' };
    }
    
    // Check state
    if (!data.state || data.state === '-- Select your State --') {
        return { valid: false, message: 'Please select a state.' };
    }
    
    // Check city
    if (!data.city || data.city.length < 2) {
        return { valid: false, message: 'Please enter a valid city name.' };
    }
    
    // Check cart
    if (Object.keys(data.cart).length === 0) {
        return { valid: false, message: 'Your cart is empty. Please add items.' };
    }
    
    return { valid: true };
}

/**
 * Submit order to backend
 */
function submitOrder(orderData, callback) {
    $.ajax({
        url: 'checkout.php',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(orderData),
        dataType: 'json',
        timeout: 10000,
        success: function(response) {
            callback(response);
        },
        error: function(xhr, status, error) {
            let message = 'An error occurred while processing your order.';
            
            if (xhr.status === 400) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    message = response.message || message;
                } catch (e) {
                    message = 'Invalid form data. Please check all fields.';
                }
            } else if (xhr.status === 500) {
                message = 'Server error. Please try again later.';
            } else if (status === 'timeout') {
                message = 'Request timeout. Please try again.';
            }
            
            callback({
                success: false,
                message: message
            });
        }
    });
}

/**
 * Show alert message
 */
function showAlert(type, message) {
    const alertClasses = {
        'success': 'alert-success',
        'error': 'alert-danger',
        'warning': 'alert-warning',
        'info': 'alert-info'
    };
    
    const alertClass = alertClasses[type] || 'alert-info';
    const iconMap = {
        'success': '✓',
        'error': '✗',
        'warning': '⚠',
        'info': 'ℹ'
    };
    const icon = iconMap[type] || '';
    
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <strong>${icon}</strong> ${escapeHtml(message)}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    const $alertContainer = $('#alertContainer');
    $alertContainer.html(alertHtml);
    $alertContainer.scrollIntoView({ behavior: 'smooth' });
}

/**
 * Show order confirmation
 */
function showOrderConfirmation(response) {
    $('#confirmOrderNumber').text(response.order_number);
    $('#confirmTotalAmount').text('₹' + response.total_amount.toLocaleString('en-IN'));
    $('#confirmEmail').text($('#guestEmail').val());
    
    $('#orderConfirmation').show().scrollIntoView({ behavior: 'smooth' });
}

/**
 * Legacy function - kept for backward compatibility
 */
function listcart(cart) {
    displayCart(cart);
    updateCartSummary(cart);
}

/**
 * Escape HTML to prevent XSS
 */
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}
