<!-- modal-masterdata-helpers.php -->
<script>
// Utility functions
function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>]/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;' })[m] || m);
}

function showToast(message, type = 'info') {
    if (typeof window.parentShowToast === 'function') { 
        window.parentShowToast(message, type); 
        return; 
    }
    const toast = document.createElement('div');
    toast.style.cssText = `position: fixed; bottom: 24px; right: 24px; background: ${type === 'success' ? '#10b981' : type === 'warning' ? '#f59e0b' : '#1e293b'}; color: white; padding: 12px 20px; border-radius: 12px; font-size: 13px; z-index: 10000; animation: slideIn 0.3s ease; box-shadow: 0 4px 12px rgba(0,0,0,0.15);`;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, 3000);
}

// API helper function with credentials
async function fetchAPI(action, method = 'GET', data = null) {
    const url = `../../api/settings/settings_api.php?action=${action}`;
    const options = {
        method: method,
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include' // Important: Include cookies for session
    };
    if (data && method !== 'GET') options.body = JSON.stringify(data);
    
    try {
        const response = await fetch(url, options);
        const result = await response.json();
        if (!result.success) {
            console.error('API Error:', result);
            throw new Error(result.message || 'An error occurred');
        }
        return result;
    } catch (error) {
        showToast(error.message || 'An error occurred', 'warning');
        throw error;
    }
}

// Check current user permissions
async function checkUserPermissions() {
    try {
        const response = await fetch('../../api/auth/check_session.php', {
            credentials: 'include'
        });
        const result = await response.json();
        console.log('Current session:', result);
        return result;
    } catch (error) {
        console.error('Session check failed:', error);
        return null;
    }
}
</script>   
