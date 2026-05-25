<!-- modal-leave-helpers.php -->
<script>
// Shared helper functions for leave modals
function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>]/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;' })[m] || m);
}

function showToast(message, type = 'info') {
    if (typeof window.parentShowToast === 'function') { window.parentShowToast(message, type); return; }
    const toast = document.createElement('div');
    toast.style.cssText = `position: fixed; bottom: 24px; right: 24px; background: ${type === 'success' ? '#10b981' : type === 'warning' ? '#f59e0b' : '#1e293b'}; color: white; padding: 12px 20px; border-radius: 12px; font-size: 13px; z-index: 10000; animation: slideIn 0.3s ease; box-shadow: 0 4px 12px rgba(0,0,0,0.15);`;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, 3000);
}
</script>