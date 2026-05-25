<!-- modal-view-offense.php -->
<script>
function viewOffense(id) {
    const off = window.offenses.find(o => o.id === id);
    if (!off) return;
    
    const severityColors = { 'Minor': '#10b981', 'Moderate': '#4f46e5', 'Major': '#f59e0b', 'Critical': '#ef4444' };
    const statusColors = { 
        'Pending Review': '#f59e0b', 
        'Under Investigation': '#8b5cf6', 
        'Action Taken': '#4f46e5', 
        'Closed': '#64748b' 
    };
    
    const content = `
        <style>
            .modal-view-offense * { margin: 0; box-sizing: border-box; }
            .modal-view-offense { font-family: 'Inter', sans-serif; max-width: 550px; width: 100%; }
            .view-header { display: flex; align-items: flex-start; gap: 16px; margin-bottom: 24px; padding-bottom: 20px; border-bottom: 1px solid #fef2f2; }
            .employee-avatar-large { width: 64px; height: 64px; border-radius: 20px; background: ${off.color}; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 1.4rem; }
            .employee-info-large h3 { font-size: 1.2rem; font-weight: 600; color: #0f172a; margin-bottom: 4px; }
            .employee-meta { font-size: 0.8rem; color: #64748b; display: flex; gap: 8px; flex-wrap: wrap; }
            .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px 24px; margin-bottom: 24px; }
            .detail-label { font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 6px; display: flex; align-items: center; gap: 6px; }
            .detail-label i { color: #ef4444; width: 16px; }
            .detail-value { font-size: 0.95rem; font-weight: 500; color: #1e293b; }
            .offense-badge-large { display: inline-block; padding: 6px 16px; border-radius: 20px; font-weight: 600; font-size: 0.9rem; }
            .summary-section { margin-top: 20px; padding-top: 20px; border-top: 1px solid #fef2f2; }
            .summary-section h4 { font-size: 0.9rem; font-weight: 600; color: #0f172a; margin-bottom: 12px; display: flex; align-items: center; gap: 8px; }
            .summary-content { background: #f8fafc; padding: 16px; border-radius: 16px; font-size: 0.9rem; line-height: 1.6; color: #334155; }
            .modal-buttons { display: flex; justify-content: flex-end; gap: 12px; margin-top: 28px; padding-top: 20px; border-top: 1px solid #f1f5f9; flex-wrap: wrap; }
            .btn-primary { background: #ef4444; color: white; border: none; padding: 10px 22px; border-radius: 24px; cursor: pointer; display: flex; align-items: center; gap: 6px; }
            .btn-secondary { background: white; color: #475569; border: 1px solid #e2e8f0; padding: 10px 22px; border-radius: 24px; cursor: pointer; }
            .btn-warning { background: #f59e0b; color: white; border: none; padding: 10px 22px; border-radius: 24px; cursor: pointer; display: flex; align-items: center; gap: 6px; }
            .status-timeline { display: flex; align-items: center; margin: 16px 0; }
            .timeline-step { flex: 1; text-align: center; }
            .timeline-dot { width: 12px; height: 12px; border-radius: 50%; margin: 0 auto 4px; }
            .timeline-line { flex: 0.5; height: 2px; background: #e2e8f0; }
        </style>
        <div class="modal-view-offense">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                <span class="offense-badge-large" style="background: ${severityColors[off.severity]}20; color: ${severityColors[off.severity]};">
                    <i class="fas fa-gavel"></i> ${off.severity} Offense
                </span>
                <span style="font-family: monospace; color: #64748b;">${off.id}</span>
            </div>
            <div class="view-header">
                <div class="employee-avatar-large">${off.avatar}</div>
                <div class="employee-info-large">
                    <h3>${escapeHtml(off.employeeName)}</h3>
                    <div class="employee-meta"><span><i class="fas fa-id-card"></i> ${off.employeeId || 'EMP'}</span><span><i class="fas fa-envelope"></i> ${off.employeeEmail}</span></div>
                </div>
            </div>
            <div class="detail-grid">
                <div class="detail-item"><div class="detail-label"><i class="fas fa-building"></i> Department</div><div class="detail-value">${escapeHtml(off.department)}</div></div>
                <div class="detail-item"><div class="detail-label"><i class="fas fa-tag"></i> Offense Type</div><div class="detail-value">${escapeHtml(off.offenseType)}</div></div>
                <div class="detail-item"><div class="detail-label"><i class="fas fa-calendar"></i> Offense Date</div><div class="detail-value">${off.date}</div></div>
                <div class="detail-item"><div class="detail-label"><i class="fas fa-user-shield"></i> Reported By</div><div class="detail-value">${escapeHtml(off.reportedBy)}</div></div>
            </div>
            <div style="background: #f8fafc; border-radius: 16px; padding: 16px; margin-bottom: 16px;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                    <span style="font-weight: 600; color: #0f172a;">Current Status</span>
                    <span style="padding: 4px 12px; border-radius: 20px; background: ${statusColors[off.status]}20; color: ${statusColors[off.status]}; font-weight: 500;">${off.status}</span>
                </div>
                <div class="status-timeline">
                    <div class="timeline-step">
                        <div class="timeline-dot" style="background: ${off.status !== 'Pending Review' ? '#10b981' : '#e2e8f0'};"></div>
                        <small>Reported</small>
                    </div>
                    <div class="timeline-line" style="background: ${off.status !== 'Pending Review' ? '#10b981' : '#e2e8f0'};"></div>
                    <div class="timeline-step">
                        <div class="timeline-dot" style="background: ${off.status === 'Action Taken' || off.status === 'Closed' ? '#10b981' : '#e2e8f0'};"></div>
                        <small>Investigation</small>
                    </div>
                    <div class="timeline-line" style="background: ${off.status === 'Action Taken' || off.status === 'Closed' ? '#10b981' : '#e2e8f0'};"></div>
                    <div class="timeline-step">
                        <div class="timeline-dot" style="background: ${off.status === 'Closed' ? '#10b981' : '#e2e8f0'};"></div>
                        <small>Resolved</small>
                    </div>
                </div>
            </div>
            <div class="summary-section">
                <h4><i class="fas fa-file-alt"></i> Offense Description</h4>
                <div class="summary-content">${escapeHtml(off.description) || 'No description provided.'}</div>
            </div>
            ${off.actionTaken ? `
            <div class="summary-section">
                <h4><i class="fas fa-check-circle"></i> Action Taken</h4>
                <div class="summary-content">${escapeHtml(off.actionTaken)}</div>
            </div>
            ` : ''}
            <div class="modal-buttons">
                <button type="button" class="btn-secondary" onclick="closeModal()"><i class="fas fa-times"></i> Close</button>
                <button type="button" class="btn-primary" onclick="closeModal(); editOffense('${id}');"><i class="fas fa-edit"></i> Edit</button>
                <button type="button" class="btn-warning" onclick="generateOffenseReport('${id}');"><i class="fas fa-file-pdf"></i> Generate Report</button>
            </div>
        </div>
    `;
    openModal('Offense Details', content);
}
</script>
