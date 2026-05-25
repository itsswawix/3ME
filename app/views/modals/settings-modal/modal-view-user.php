<!-- modal-view-user.php -->
<script>
function viewUser(id) {
    const user = window.users.find(u => u.id === id);
    if (!user) return;
    const statusClass = { 'Active': 'badge-success', 'Inactive': 'badge-secondary', 'Locked': 'badge-danger' }[user.status] || 'badge-secondary';
    const roleClass = { 'Admin': 'badge-purple', 'HR Manager': 'badge-info', 'Manager': 'badge-warning', 'Employee': 'badge-secondary' }[user.role] || 'badge-secondary';
    
    const content = `
        <style>
            .modal-view-user * { margin: 0; box-sizing: border-box; }
            .modal-view-user { font-family: 'Inter', sans-serif; max-width: 550px; width: 100%; }
            .view-header { display: flex; align-items: flex-start; gap: 16px; margin-bottom: 24px; padding-bottom: 20px; border-bottom: 1px solid #eef2ff; }
            .user-avatar-large { width: 64px; height: 64px; border-radius: 20px; background: ${user.color}; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 1.4rem; }
            .user-info-large h3 { font-size: 1.2rem; font-weight: 600; color: #0f172a; margin-bottom: 4px; }
            .user-meta { font-size: 0.8rem; color: #64748b; display: flex; gap: 8px; flex-wrap: wrap; }
            .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px 24px; margin-bottom: 24px; }
            .detail-label { font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 6px; display: flex; align-items: center; gap: 6px; }
            .detail-label i { color: #4f46e5; width: 16px; }
            .detail-value { font-size: 0.95rem; font-weight: 500; color: #1e293b; }
            .modal-buttons { display: flex; justify-content: flex-end; gap: 12px; margin-top: 28px; padding-top: 20px; border-top: 1px solid #f1f5f9; }
            .btn-primary { background: #4f46e5; color: white; border: none; padding: 10px 22px; border-radius: 24px; cursor: pointer; display: flex; align-items: center; gap: 6px; }
            .btn-secondary { background: white; color: #475569; border: 1px solid #e2e8f0; padding: 10px 22px; border-radius: 24px; cursor: pointer; }
        </style>
        <div class="modal-view-user">
            <div class="view-header">
                <div class="user-avatar-large">${user.avatar}</div>
                <div class="user-info-large">
                    <h3>${escapeHtml(user.name)}</h3>
                    <div class="user-meta"><span><i class="fas fa-id-card"></i> ${user.id}</span><span><i class="fas fa-envelope"></i> ${escapeHtml(user.email)}</span></div>
                    <div style="margin-top: 8px;"><span class="badge ${roleClass}">${user.role}</span> <span class="badge ${statusClass}" style="margin-left: 8px;">${user.status}</span></div>
                </div>
            </div>
            <div class="detail-grid">
                <div class="detail-item"><div class="detail-label"><i class="fas fa-phone"></i> Contact</div><div class="detail-value">${user.contactNumber || '—'}</div></div>
                <div class="detail-item"><div class="detail-label"><i class="fas fa-building"></i> Department</div><div class="detail-value">${user.department || '—'}</div></div>
            </div>
            <div class="modal-buttons">
                <button type="button" class="btn-secondary" onclick="closeModal()"><i class="fas fa-times"></i> Close</button>
                <button type="button" class="btn-primary" onclick="closeModal(); editUser('${id}');"><i class="fas fa-edit"></i> Edit</button>
                <button type="button" class="btn-primary" onclick="resetPassword('${id}');" style="background: #f59e0b;"><i class="fas fa-key"></i> Reset Password</button>
            </div>
        </div>
    `;
    openModal('User Details', content);
}
</script>