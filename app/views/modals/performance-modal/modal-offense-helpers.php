<!-- modal-offense-helpers.php -->
<script>
function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>]/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;' })[m] || m);
}

function getSeverityClass(severity) {
    if (severity === 'Critical') return 'critical';
    if (severity === 'Major') return 'major';
    if (severity === 'Moderate') return 'moderate';
    return 'minor';
}

function getSeverityBadgeClass(severity) {
    if (severity === 'Critical') return 'badge-danger';
    if (severity === 'Major') return 'badge-warning';
    if (severity === 'Moderate') return 'badge-info';
    return 'badge-secondary';
}

function getStatusBadgeClass(status) {
    if (status === 'Pending Review') return 'badge-warning';
    if (status === 'Action Taken') return 'badge-info';
    if (status === 'Closed') return 'badge-secondary';
    return 'badge-purple';
}

function showToast(message, type = 'info') {
    if (typeof window.parentShowToast === 'function') { window.parentShowToast(message, type); return; }
    const toast = document.createElement('div');
    toast.style.cssText = `position: fixed; bottom: 24px; right: 24px; background: ${type === 'success' ? '#10b981' : type === 'warning' ? '#f59e0b' : '#1e293b'}; color: white; padding: 12px 20px; border-radius: 12px; font-size: 13px; z-index: 10000; animation: slideIn 0.3s ease; box-shadow: 0 4px 12px rgba(0,0,0,0.15);`;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, 3000);
}

function generateOffenseReport(id) {
    showToast('Generating offense report...', 'info');
    // In production, this would trigger PDF generation
}
</script>
