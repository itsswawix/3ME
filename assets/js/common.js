/**
 * Common JavaScript Utilities
 * 
 * Features:
 * - Toast notifications
 * - Loading indicators
 * - Form validation
 * - Session timeout warning
 * - Notification polling
 */

// ============================================================================
// TOAST NOTIFICATIONS
// ============================================================================

/**
 * Show toast notification
 * 
 * @param {string} message Message to display
 * @param {string} type Type: success, error, warning, info
 * @param {number} duration Duration in milliseconds
 */
function showToast(message, type = 'info', duration = 3000) {
    // Remove existing toasts
    const existingToast = document.querySelector('.toast');
    if (existingToast) {
        existingToast.remove();
    }
    
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
        <div class="toast-content">
            <span class="toast-icon">${getToastIcon(type)}</span>
            <span class="toast-message">${message}</span>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    // Show toast with animation
    setTimeout(() => toast.classList.add('show'), 10);
    
    // Hide and remove toast
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, duration);
}

/**
 * Get icon for toast type
 * 
 * @param {string} type Toast type
 * @returns {string} Icon character
 */
function getToastIcon(type) {
    const icons = {
        success: '✓',
        error: '✗',
        warning: '⚠',
        info: 'ℹ'
    };
    return icons[type] || icons.info;
}

// ============================================================================
// LOADING INDICATORS
// ============================================================================

/**
 * Show loading indicator on button
 * 
 * @param {HTMLElement} element Button element
 */
function showLoading(element) {
    if (!element) return;
    
    element.disabled = true;
    element.dataset.originalText = element.textContent;
    element.innerHTML = '<span class="spinner"></span> Loading...';
}

/**
 * Hide loading indicator on button
 * 
 * @param {HTMLElement} element Button element
 */
function hideLoading(element) {
    if (!element) return;
    
    element.disabled = false;
    element.textContent = element.dataset.originalText || 'Submit';
}

/**
 * Show page loading overlay
 */
function showPageLoading() {
    let overlay = document.getElementById('page-loading-overlay');
    
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.id = 'page-loading-overlay';
        overlay.className = 'page-loading-overlay';
        overlay.innerHTML = '<div class="spinner-large"></div>';
        document.body.appendChild(overlay);
    }
    
    overlay.style.display = 'flex';
}

/**
 * Hide page loading overlay
 */
function hidePageLoading() {
    const overlay = document.getElementById('page-loading-overlay');
    if (overlay) {
        overlay.style.display = 'none';
    }
}

// ============================================================================
// FORM VALIDATION
// ============================================================================

/**
 * Validate form
 * 
 * @param {HTMLFormElement} formElement Form element
 * @returns {boolean} True if valid
 */
function validateForm(formElement) {
    if (!formElement) return false;
    
    const inputs = formElement.querySelectorAll('[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.classList.add('is-invalid');
            isValid = false;
        } else {
            input.classList.remove('is-invalid');
        }
    });
    
    return isValid;
}

/**
 * Validate email format
 * 
 * @param {string} email Email address
 * @returns {boolean} True if valid
 */
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

/**
 * Validate phone number
 * 
 * @param {string} phone Phone number
 * @returns {boolean} True if valid
 */
function validatePhone(phone) {
    const re = /^[\d\s\-\(\)]+$/;
    return re.test(phone) && phone.replace(/\D/g, '').length >= 10;
}

/**
 * Add real-time validation to input
 * 
 * @param {HTMLInputElement} input Input element
 */
function addInputValidation(input) {
    input.addEventListener('blur', function() {
        if (this.hasAttribute('required') && !this.value.trim()) {
            this.classList.add('is-invalid');
        } else if (this.type === 'email' && this.value && !validateEmail(this.value)) {
            this.classList.add('is-invalid');
        } else if (this.type === 'tel' && this.value && !validatePhone(this.value)) {
            this.classList.add('is-invalid');
        } else {
            this.classList.remove('is-invalid');
        }
    });
    
    input.addEventListener('input', function() {
        if (this.classList.contains('is-invalid') && this.value.trim()) {
            this.classList.remove('is-invalid');
        }
    });
}

// ============================================================================
// SESSION MANAGEMENT
// ============================================================================

let sessionWarningShown = false;

/**
 * Check session timeout
 */
function checkSessionTimeout() {
    api.get('/auth/check_session.php')
        .then(data => {
            const remaining = data.data?.remaining || 0;
            
            // Show warning if less than 5 minutes remaining
            if (remaining < 300 && remaining > 0 && !sessionWarningShown) {
                sessionWarningShown = true;
                
                if (confirm('Your session will expire in 5 minutes. Do you want to extend it?')) {
                    api.post('/auth/refresh_session.php')
                        .then(() => {
                            showToast('Session extended successfully', 'success');
                            sessionWarningShown = false;
                        })
                        .catch(err => {
                            console.error('Failed to refresh session:', err);
                        });
                }
            }
        })
        .catch(err => {
            console.error('Session check failed:', err);
        });
}

// Check session every minute
setInterval(checkSessionTimeout, 60000);

// ============================================================================
// NOTIFICATION POLLING
// ============================================================================

/**
 * Poll for new notifications
 */
function pollNotifications() {
    api.get('/notifications/unread_count.php')
        .then(data => {
            const count = data.data?.count || 0;
            const badge = document.querySelector('.notification-badge');
            
            if (badge) {
                badge.textContent = count;
                badge.style.display = count > 0 ? 'inline-block' : 'none';
            }
        })
        .catch(err => {
            console.error('Notification poll failed:', err);
        });
}

// Poll notifications every 30 seconds
setInterval(pollNotifications, 30000);

// Initial poll on page load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', pollNotifications);
} else {
    pollNotifications();
}

// ============================================================================
// UTILITY FUNCTIONS
// ============================================================================

/**
 * Format date to YYYY-MM-DD
 * 
 * @param {Date} date Date object
 * @returns {string} Formatted date
 */
function formatDate(date) {
    const d = new Date(date);
    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

/**
 * Format currency
 * 
 * @param {number} amount Amount
 * @returns {string} Formatted currency
 */
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
}

/**
 * Debounce function
 * 
 * @param {Function} func Function to debounce
 * @param {number} wait Wait time in milliseconds
 * @returns {Function} Debounced function
 */
function debounce(func, wait = 300) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Confirm action with custom message
 * 
 * @param {string} message Confirmation message
 * @returns {boolean} True if confirmed
 */
function confirmAction(message = 'Are you sure?') {
    return confirm(message);
}

/**
 * Copy text to clipboard
 * 
 * @param {string} text Text to copy
 */
function copyToClipboard(text) {
    navigator.clipboard.writeText(text)
        .then(() => {
            showToast('Copied to clipboard', 'success');
        })
        .catch(err => {
            console.error('Failed to copy:', err);
            showToast('Failed to copy to clipboard', 'error');
        });
}

// ============================================================================
// INITIALIZATION
// ============================================================================

/**
 * Initialize common functionality on page load
 */
document.addEventListener('DOMContentLoaded', function() {
    // Add validation to all forms
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(this)) {
                e.preventDefault();
                showToast('Please fill in all required fields', 'error');
            }
        });
    });
    
    // Add real-time validation to inputs
    document.querySelectorAll('input[required], input[type="email"], input[type="tel"]').forEach(input => {
        addInputValidation(input);
    });
    
    // Add confirmation to delete buttons
    document.querySelectorAll('[data-confirm]').forEach(button => {
        button.addEventListener('click', function(e) {
            const message = this.dataset.confirm || 'Are you sure?';
            if (!confirmAction(message)) {
                e.preventDefault();
                e.stopPropagation();
            }
        });
    });
});
