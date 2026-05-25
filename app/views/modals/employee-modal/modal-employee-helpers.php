<!-- modal-employee-helpers.php - Helper functions for employee modals -->
<script>
// Helper function to format date for input (YYYY-MM-DD)
function formatDateForInput(dateString) {
    if (!dateString) return '';
    
    // Handle different date formats
    let date;
    if (dateString.includes('/')) {
        // Format: "Jan 15, 2024" or "01/15/2024"
        date = new Date(dateString);
    } else if (dateString.includes('-')) {
        // Format: "2024-01-15"
        date = new Date(dateString);
    } else {
        date = new Date(dateString);
    }
    
    if (isNaN(date.getTime())) return '';
    
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    
    return `${year}-${month}-${day}`;
}

// Helper function to format date for display (Jan 15, 2024)
function formatDateForDisplay(dateString) {
    if (!dateString) return '';
    
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return dateString;
    
    return date.toLocaleDateString('en-US', { 
        month: 'short', 
        day: 'numeric', 
        year: 'numeric' 
    });
}

// Helper function to generate employee avatar
function generateEmployeeAvatar(fullName) {
    const parts = fullName.split(' ').filter(p => p);
    let avatar;
    
    if (parts.length >= 2) {
        avatar = (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
    } else {
        avatar = parts[0] ? parts[0].substring(0, 2).toUpperCase() : 'NA';
    }
    
    const colors = [
        'linear-gradient(145deg, #4f46e5, #7c3aed)',
        'linear-gradient(145deg, #ef4444, #f87171)',
        'linear-gradient(145deg, #10b981, #34d399)',
        'linear-gradient(145deg, #f59e0b, #fbbf24)',
        'linear-gradient(145deg, #8b5cf6, #a78bfa)',
        'linear-gradient(145deg, #06b6d4, #67e8f9)',
        'linear-gradient(145deg, #ec4899, #f472b6)',
        'linear-gradient(145deg, #14b8a6, #5eead4)'
    ];
    
    const colorIndex = fullName.length % colors.length;
    const color = colors[colorIndex];
    
    return { avatar, color };
}

// Helper function to validate email
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Helper function to show validation errors
function showValidationErrors(errors) {
    if (errors.length === 0) return;
    
    const errorMessage = errors.join('\n• ');
    const errorHtml = `
        <div style="
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 12px;
            padding: 14px 16px;
            margin-bottom: 16px;
        ">
            <div style="
                display: flex;
                align-items: flex-start;
                gap: 10px;
            ">
                <i class="fas fa-exclamation-circle" style="
                    color: #ef4444;
                    font-size: 18px;
                    margin-top: 2px;
                "></i>
                <div style="flex: 1;">
                    <div style="
                        font-weight: 600;
                        color: #991b1b;
                        margin-bottom: 6px;
                        font-size: 13px;
                    ">Please fix the following errors:</div>
                    <ul style="
                        margin: 0;
                        padding-left: 20px;
                        color: #dc2626;
                        font-size: 12px;
                        line-height: 1.6;
                    ">
                        ${errors.map(err => `<li>${err}</li>`).join('')}
                    </ul>
                </div>
            </div>
        </div>
    `;
    
    // Insert error message at the top of modal content
    const modalBody = document.getElementById('modalBody');
    const existingError = modalBody.querySelector('.validation-errors');
    if (existingError) {
        existingError.remove();
    }
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'validation-errors';
    errorDiv.innerHTML = errorHtml;
    modalBody.insertBefore(errorDiv, modalBody.firstChild);
    
    // Scroll to top to show errors
    modalBody.scrollTop = 0;
    
    // Also show toast
    showToast('Please fix the validation errors', 'error');
}

// Toast notification function
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    const bgColor = type === 'success' ? '#10b981' : 
                    type === 'warning' ? '#f59e0b' : 
                    type === 'error' ? '#ef4444' : 
                    type === 'info' ? '#3b82f6' : '#1e293b';
    
    toast.style.cssText = `
        position: fixed; 
        bottom: 24px; 
        right: 24px; 
        background: ${bgColor}; 
        color: white; 
        padding: 12px 20px; 
        border-radius: 12px; 
        font-size: 13px; 
        z-index: 10001; 
        animation: slideIn 0.3s ease; 
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        display: flex;
        align-items: center;
        gap: 8px;
    `;
    
    // Add icon based on type
    const icons = {
        'success': 'fa-check-circle',
        'error': 'fa-times-circle',
        'warning': 'fa-exclamation-triangle',
        'info': 'fa-info-circle'
    };
    
    const icon = icons[type] || 'fa-info-circle';
    toast.innerHTML = `<i class="fas ${icon}"></i><span>${message}</span>`;
    
    document.body.appendChild(toast);
    
    setTimeout(() => { 
        toast.style.opacity = '0'; 
        toast.style.transition = 'opacity 0.3s'; 
        setTimeout(() => toast.remove(), 300); 
    }, 3000);
}

// Make functions globally available
window.formatDateForInput = formatDateForInput;
window.formatDateForDisplay = formatDateForDisplay;
window.generateEmployeeAvatar = generateEmployeeAvatar;
window.isValidEmail = isValidEmail;
window.showValidationErrors = showValidationErrors;
window.showToast = showToast;
</script>
