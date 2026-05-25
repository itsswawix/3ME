<?php
/**
 * Helper functions for onboarding modals
 */
?>

<script>
// Helper function to generate unique IDs
function generateOnboardingId() {
    return 'ONB-' + new Date().getFullYear() + '-' + String(Math.floor(Math.random() * 1000)).padStart(3, '0');
}

function generateExitId() {
    return 'EXIT-' + new Date().getFullYear() + '-' + String(Math.floor(Math.random() * 1000)).padStart(3, '0');
}

// Helper function to generate employee avatar and color
function generateEmployeeAvatar(name) {
    const names = name.split(' ');
    const initials = names.length >= 2 ? names[0][0] + names[names.length-1][0] : names[0][0] + (names[0][1] || '');
    
    const colors = [
        'linear-gradient(145deg, #4f46e5, #7c3aed)',
        'linear-gradient(145deg, #0ea5e9, #3b82f6)',
        'linear-gradient(145deg, #10b981, #059669)',
        'linear-gradient(145deg, #f59e0b, #d97706)',
        'linear-gradient(145deg, #ef4444, #dc2626)',
        'linear-gradient(145deg, #8b5cf6, #a78bfa)',
        'linear-gradient(145deg, #ec4899, #f472b6)',
        'linear-gradient(145deg, #14b8a6, #0d9488)'
    ];
    
    const colorIndex = name.length % colors.length;
    
    return {
        avatar: initials.toUpperCase(),
        color: colors[colorIndex]
    };
}

// Helper function to get default onboarding tasks
function getDefaultOnboardingTasks() {
    return [
        { text: 'Complete employment forms', completed: false },
        { text: 'IT equipment setup', completed: false },
        { text: 'Office tour and introductions', completed: false },
        { text: 'HR orientation session', completed: false },
        { text: 'Department training', completed: false },
        { text: 'System access setup', completed: false },
        { text: 'Benefits enrollment', completed: false },
        { text: 'Emergency contact information', completed: false }
    ];
}

// Helper function to format date for display
function formatDateForDisplay(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
        month: 'short', 
        day: 'numeric', 
        year: 'numeric' 
    });
}

// Helper function to format date for input
function formatDateForInput(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toISOString().split('T')[0];
}

// Helper function to calculate progress percentage
function calculateProgressPercentage(tasks) {
    if (!tasks || tasks.length === 0) return 0;
    const completed = tasks.filter(t => t.completed).length;
    return Math.round((completed / tasks.length) * 100);
}

// Helper function to get progress status based on percentage
function getProgressStatus(percentage) {
    if (percentage === 0) return 'Not Started';
    if (percentage === 100) return 'Completed';
    return 'In Progress';
}

// Helper function to validate email
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Helper function to validate required fields
function validateOnboardingForm(formData) {
    const errors = [];
    
    if (!formData.employeeName || formData.employeeName.trim() === '') {
        errors.push('Employee name is required');
    }
    
    if (!formData.employeeEmail || formData.employeeEmail.trim() === '') {
        errors.push('Employee email is required');
    } else if (!isValidEmail(formData.employeeEmail)) {
        errors.push('Please enter a valid email address');
    }
    
    if (!formData.position || formData.position.trim() === '') {
        errors.push('Position is required');
    }
    
    if (!formData.department || formData.department.trim() === '') {
        errors.push('Department is required');
    }
    
    if (!formData.startDate) {
        errors.push('Start date is required');
    }
    
    return errors;
}

// Helper function to validate exit form
function validateExitForm(formData) {
    const errors = [];
    
    if (!formData.employeeName || formData.employeeName.trim() === '') {
        errors.push('Employee name is required');
    }
    
    if (!formData.lastWorkingDay) {
        errors.push('Last working day is required');
    }
    
    if (!formData.reason || formData.reason.trim() === '') {
        errors.push('Reason for leaving is required');
    }
    
    return errors;
}

// Helper function to show validation errors
function showValidationErrors(errors) {
    if (errors.length === 0) return;
    
    const errorMessage = errors.length === 1 
        ? errors[0] 
        : 'Please fix the following errors:\n• ' + errors.join('\n• ');
    
    showToast(errorMessage, 'error');
}

// Helper function to get company list
function getCompanyList() {
    return [
        'NovaCore Technologies',
        'TechSolutions Inc',
        'Digital Dynamics Corp',
        'InnovateTech Ltd',
        'FutureSoft Systems'
    ];
}

// Helper function to get department list
function getDepartmentList() {
    return [
        'Engineering',
        'Product',
        'Design',
        'Marketing',
        'Sales',
        'Human Resources',
        'Finance',
        'Operations',
        'Analytics',
        'Customer Success'
    ];
}

// Helper function to get position list
function getPositionList() {
    return [
        'Software Engineer',
        'Senior Software Engineer',
        'Product Manager',
        'UX/UI Designer',
        'Marketing Specialist',
        'Sales Representative',
        'HR Manager',
        'Data Analyst',
        'Project Manager',
        'Customer Success Manager'
    ];
}

// Helper function to escape HTML
function escapeHtml(str) {
    if (!str) return '';
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

// Helper function to show toast notifications
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    const bgColor = {
        'success': '#10b981',
        'error': '#ef4444',
        'warning': '#f59e0b',
        'info': '#1e293b'
    }[type] || '#1e293b';
    
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
        max-width: 400px;
        word-wrap: break-word;
    `;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 0.3s';
        setTimeout(() => toast.remove(), 300);
    }, type === 'error' ? 5000 : 3000);
}

// Helper function to populate select options
function populateSelectOptions(selectId, options, selectedValue = '') {
    const select = document.getElementById(selectId);
    if (!select) return;
    
    select.innerHTML = '';
    
    // Add default option if needed
    if (selectId.includes('company') || selectId.includes('department') || selectId.includes('position')) {
        select.innerHTML = '<option value="">Select...</option>';
    }
    
    options.forEach(option => {
        const optionElement = document.createElement('option');
        optionElement.value = option;
        optionElement.textContent = option;
        if (option === selectedValue) {
            optionElement.selected = true;
        }
        select.appendChild(optionElement);
    });
}

// Helper function to setup form dropdowns
function setupOnboardingFormDropdowns(selectedCompany = '', selectedDepartment = '', selectedPosition = '') {
    populateSelectOptions('onboardCompany', getCompanyList(), selectedCompany);
    populateSelectOptions('onboardDepartment', getDepartmentList(), selectedDepartment);
    populateSelectOptions('onboardPosition', getPositionList(), selectedPosition);
}

function setupExitFormDropdowns(selectedCompany = '', selectedDepartment = '', selectedPosition = '') {
    populateSelectOptions('exitCompany', getCompanyList(), selectedCompany);
    populateSelectOptions('exitDepartment', getDepartmentList(), selectedDepartment);
    populateSelectOptions('exitPosition', getPositionList(), selectedPosition);
}


// Make functions globally available
window.generateOnboardingId = generateOnboardingId;
window.generateExitId = generateExitId;
window.generateEmployeeAvatar = generateEmployeeAvatar;
window.getDefaultOnboardingTasks = getDefaultOnboardingTasks;
window.formatDateForDisplay = formatDateForDisplay;
window.formatDateForInput = formatDateForInput;
window.calculateProgressPercentage = calculateProgressPercentage;
window.getProgressStatus = getProgressStatus;
window.validateOnboardingForm = validateOnboardingForm;
window.validateExitForm = validateExitForm;
window.showValidationErrors = showValidationErrors;
window.setupOnboardingFormDropdowns = setupOnboardingFormDropdowns;
window.setupExitFormDropdowns = setupExitFormDropdowns;
window.escapeHtml = escapeHtml;
</script>