<!-- modal-user-helpers.php -->
<script>
function escapeHtml(str) { 
    if (!str) return ''; 
    return str.replace(/[&<>"]/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' })[m] || m); 
}

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.style.cssText = `
        position: fixed; 
        bottom: 24px; 
        right: 24px; 
        background: ${type === 'success' ? '#10b981' : type === 'warning' ? '#f59e0b' : type === 'error' ? '#ef4444' : '#1e293b'}; 
        color: white; 
        padding: 12px 20px; 
        border-radius: 12px; 
        font-size: 13px; 
        z-index: 10000; 
        animation: slideIn 0.3s ease; 
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    `;
    toast.textContent = message; 
    document.body.appendChild(toast);
    setTimeout(() => { 
        toast.style.opacity = '0'; 
        setTimeout(() => toast.remove(), 300); 
    }, 3000);
}

// Add animation style if not already present
if (!document.querySelector('#toast-animation-style')) {
    const style = document.createElement('style');
    style.id = 'toast-animation-style';
    style.textContent = `
        @keyframes slideIn { 
            from { transform: translateX(100%); opacity: 0; } 
            to { transform: translateX(0); opacity: 1; } 
        }
    `;
    document.head.appendChild(style);
}

// Quick status toggling function
async function toggleUserStatus(userId) {
    const user = window.users.find(u => u.id === userId);
    if (!user) {
        showToast('User not found', 'warning');
        return;
    }
    
    // Check lockout protection
    if (userId === window.currentLoggedInUserId) {
        showToast('You cannot suspend or deactivate your own account.', 'warning');
        return;
    }
    
    const newStatus = user.status === 'Active' ? 'Inactive' : 'Active';
    
    if (!confirm(`Are you sure you want to change the status of ${user.name} to "${newStatus}"?`)) {
        return;
    }
    
    // Split name for updating
    const nameParts = user.name.split(' ');
    const firstname = nameParts[0] || '';
    const surname = nameParts.slice(1).join(' ') || 'User';
    
    const updateData = {
        id: userId,
        firstname: firstname,
        surname: surname,
        email: user.email,
        role: user.role,
        department: user.department,
        contact_number: user.contact_number,
        status: newStatus
    };
    
    try {
        const response = await fetch('../../api/users/users.php', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(updateData)
        });
        const result = await response.json();
        
        if (result.success) {
            showToast(`User status updated to "${newStatus}"!`, 'success');
            user.status = newStatus;
            
            if (typeof renderUserTable === 'function') {
                renderUserTable();
            }
        } else {
            showToast(result.message || 'Error updating status', 'warning');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('Error updating status', 'error');
    }
}

// Quick delete user function
async function deleteUser(userId) {
    const user = window.users.find(u => u.id === userId);
    if (!user) {
        showToast('User not found', 'warning');
        return;
    }
    
    // Lockout protection
    if (userId === window.currentLoggedInUserId) {
        showToast('Lockout Protection: You cannot delete your own active account!', 'error');
        return;
    }
    
    if (!confirm(`⚠️ DANGER: Are you sure you want to permanently delete the user "${user.name}" (ID: ${userId})?\n\nThis action CANNOT be undone.`)) {
        return;
    }
    
    try {
        const response = await fetch(`../../api/users/users.php?id=${encodeURIComponent(userId)}`, {
            method: 'DELETE'
        });
        const result = await response.json();
        
        if (result.success) {
            showToast('User permanently deleted!', 'success');
            
            // Remove locally
            window.users = window.users.filter(u => u.id !== userId);
            
            if (typeof renderUserTable === 'function') {
                renderUserTable();
            }
        } else {
            showToast(result.message || 'Error deleting user', 'warning');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('Error deleting user', 'error');
    }
}
</script>
