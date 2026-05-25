<!-- modal-view-master.php -->
<script>
function openViewMasterDataModal(master) {
    const typeIcon = { 
        'Departments': 'building', 
        'Job Titles': 'briefcase', 
        'Employment Types': 'clock', 
        'Leave Types': 'umbrella-beach' 
    }[master.dataType] || 'tag';
    
    const content = `
        <style>
            .modal-view-master * { margin: 0; box-sizing: border-box; }
            .modal-view-master { font-family: 'Inter', sans-serif; max-width: 550px; width: 100%; }
            .view-header { display: flex; align-items: flex-start; gap: 16px; margin-bottom: 24px; padding-bottom: 20px; border-bottom: 1px solid #eef2ff; }
            .master-icon { width: 64px; height: 64px; border-radius: 20px; background: linear-gradient(135deg, #4f46e5, #7c3aed); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.8rem; }
            .master-info-large h3 { font-size: 1.2rem; font-weight: 600; color: #0f172a; margin-bottom: 4px; }
            .master-meta { font-size: 0.8rem; color: #64748b; display: flex; gap: 8px; flex-wrap: wrap; }
            .detail-grid { display: grid; grid-template-columns: 1fr; gap: 20px; margin-bottom: 24px; }
            .detail-item { padding: 16px; background: #f8fafc; border-radius: 12px; }
            .detail-label { font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 6px; display: flex; align-items: center; gap: 6px; }
            .detail-label i { color: #4f46e5; width: 16px; }
            .detail-value { font-size: 0.95rem; font-weight: 500; color: #1e293b; }
            .modal-buttons { display: flex; justify-content: flex-end; gap: 12px; margin-top: 28px; padding-top: 20px; border-top: 1px solid #f1f5f9; }
            .btn-primary { background: #4f46e5; color: white; border: none; padding: 10px 22px; border-radius: 24px; cursor: pointer; display: flex; align-items: center; gap: 6px; }
            .btn-secondary { background: white; color: #475569; border: 1px solid #e2e8f0; padding: 10px 22px; border-radius: 24px; cursor: pointer; }
            .status-badge { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 500; }
            .status-active { background: #dcfce7; color: #16a34a; }
            .status-inactive { background: #f1f5f9; color: #64748b; }
        </style>
        <div class="modal-view-master">
            <div class="view-header">
                <div class="master-icon"><i class="fas fa-${typeIcon}"></i></div>
                <div class="master-info-large">
                    <h3>${escapeHtml(master.value)}</h3>
                    <div class="master-meta">
                        <span><i class="fas fa-tag"></i> ${escapeHtml(master.dataType)}</span>
                        <span><i class="fas fa-fingerprint"></i> ${master.id}</span>
                    </div>
                    <div style="margin-top: 8px;">
                        <span class="status-badge ${master.isActive ? 'status-active' : 'status-inactive'}">
                            <i class="fas fa-${master.isActive ? 'check-circle' : 'times-circle'}"></i>
                            ${master.isActive ? 'Active' : 'Inactive'}
                        </span>
                    </div>
                </div>
            </div>
            <div class="detail-grid">
                <div class="detail-item">
                    <div class="detail-label"><i class="fas fa-align-left"></i> Description</div>
                    <div class="detail-value">${escapeHtml(master.description) || 'No description provided'}</div>
                </div>
            </div>
            <div class="modal-buttons">
                <button type="button" class="btn-secondary" onclick="closeModal()"><i class="fas fa-times"></i> Close</button>
                <button type="button" class="btn-primary" onclick="closeModal(); editMasterData('${master.id}');"><i class="fas fa-edit"></i> Edit</button>
            </div>
        </div>
    `;
    openModal('Master Data Details', content);
}
</script>
