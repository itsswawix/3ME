<!-- modal-view-user.php -->
<script>
function viewUser(id) {
    const user = window.users.find(u => u.id === id);
    if (!user) return;
    
    const statusConfig = {
        'Active': { bg: '#dcfce7', color: '#16a34a', icon: 'fa-check-circle' },
        'Inactive': { bg: '#fee2e2', color: '#dc2626', icon: 'fa-times-circle' },
        'Suspended': { bg: '#fef3c7', color: '#d97706', icon: 'fa-ban' },
        'Pending': { bg: '#f1f5f9', color: '#64748b', icon: 'fa-hourglass-half' }
    }[user.status] || { bg: '#f1f5f9', color: '#64748b', icon: 'fa-circle' };
    
    const content = `
        <style>
            .modal-view-user * { margin: 0; box-sizing: border-box; }
            .modal-view-user { font-family: 'Inter', sans-serif; width: 100%; max-width: 500px; }
            .view-header { display: flex; align-items: center; gap: 16px; margin-bottom: 24px; padding-bottom: 20px; border-bottom: 1px solid #eef2ff; }
            .user-avatar-large { width: 68px; height: 68px; border-radius: 20px; background: ${user.color}; display: flex; align-items: center; justify-content: center; color: white; font-size: 22px; font-weight: 600; box-shadow: 0 8px 16px rgba(0,0,0,0.06); }
            .user-info-large h3 { font-size: 1.15rem; font-weight: 600; color: #0f172a; margin-bottom: 4px; }
            .user-meta { font-size: 0.8rem; color: #64748b; display: flex; gap: 8px; flex-wrap: wrap; }
            .status-badge-view { padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 500; background: ${statusConfig.bg}; color: ${statusConfig.color}; display: inline-flex; align-items: center; gap: 4px; margin-top: 8px; }
            .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px 24px; margin-bottom: 24px; }
            .detail-label { font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 6px; display: flex; align-items: center; gap: 6px; }
            .detail-label i { color: #4f46e5; width: 16px; text-align: center; }
            .detail-value { font-size: 0.95rem; font-weight: 500; color: #1e293b; word-break: break-all; }
            .modal-buttons { display: flex; justify-content: flex-end; gap: 12px; margin-top: 28px; padding-top: 20px; border-top: 1px solid #f1f5f9; }
            .btn-primary { background: #4f46e5; color: white; border: none; padding: 10px 22px; border-radius: 24px; cursor: pointer; display: flex; align-items: center; gap: 6px; font-size: 0.85rem; transition: all 0.2s; box-shadow: 0 4px 8px rgba(79, 70, 229, 0.15); }
            .btn-primary:hover { background: #4338ca; transform: translateY(-1px); }
            .btn-secondary { background: white; color: #475569; border: 1px solid #e2e8f0; padding: 10px 22px; border-radius: 24px; cursor: pointer; font-size: 0.85rem; transition: all 0.2s; }
            .btn-secondary:hover { background: #f8fafc; border-color: #cbd5e1; }
        </style>
        <div class="modal-view-user">
            <div class="view-header">
                <div class="user-avatar-large">${user.avatar}</div>
                <div class="user-info-large">
                    <h3>${escapeHtml(user.name)}</h3>
                    <div class="user-meta">
                        <span><i class="fas fa-hashtag"></i> ${user.id}</span>
                        <span>•</span>
                        <span><i class="fas fa-user-tag"></i> ${escapeHtml(user.role)}</span>
                    </div>
                    <span class="status-badge-view"><i class="fas ${statusConfig.icon}"></i> ${user.status}</span>
                </div>
            </div>
            
            <div class="detail-grid">
                <div class="detail-item">
                    <div class="detail-label"><i class="fas fa-envelope"></i> Email Address</div>
                    <div class="detail-value">${escapeHtml(user.email)}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label"><i class="fas fa-phone"></i> Contact Number</div>
                    <div class="detail-value">${escapeHtml(user.contact_number) || '—'}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label"><i class="fas fa-building"></i> Department</div>
                    <div class="detail-value">${escapeHtml(user.department) || '—'}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label"><i class="fas fa-calendar-alt"></i> Created At</div>
                    <div class="detail-value">${user.created_at}</div>
                </div>
            </div>
            
            <div class="modal-buttons">
                <button type="button" class="btn-secondary" onclick="closeModal()">
                    <i class="fas fa-times"></i> Close
                </button>
                <button type="button" class="btn-primary" onclick="closeModal(); editUser('${id}');">
                    <i class="fas fa-user-edit"></i> Edit User
                </button>
            </div>
        </div>
    `;
    openModal('User Details', content);
}
</script>
