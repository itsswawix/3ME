<!-- modal-edit-offense.php -->
<script>
function editOffense(id) {
    const off = window.offenses.find(o => o.id === id);
    if (!off) return;
    
    const content = `
        <style>
            .modal-edit-offense * { margin: 0; box-sizing: border-box; }
            .modal-edit-offense { font-family: 'Inter', sans-serif; max-width: 600px; width: 100%; }
            .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px 24px; }
            .full-width { grid-column: span 2; }
            .form-group { display: flex; flex-direction: column; margin-bottom: 4px; }
            .form-group label { font-size: 0.85rem; font-weight: 500; margin-bottom: 6px; color: #475569; }
            .form-group input, .form-group select, .form-group textarea { padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 16px; font-size: 0.9rem; background: #ffffff; }
            .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: #ef4444; box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1); }
            .section-title { grid-column: span 2; font-size: 1rem; font-weight: 600; margin: 20px 0 8px; padding-bottom: 8px; border-bottom: 1.5px solid #e2e8f0; color: #0f172a; display: flex; align-items: center; gap: 8px; }
            .section-title i { color: #ef4444; }
            .required-star { color: #ef4444; }
            textarea { resize: vertical; min-height: 100px; }
            .modal-buttons { display: flex; justify-content: flex-end; gap: 12px; margin-top: 28px; padding-top: 20px; border-top: 1px solid #f1f5f9; }
            .btn-save { background: #ef4444; color: white; border: none; padding: 10px 22px; border-radius: 24px; cursor: pointer; display: flex; align-items: center; gap: 6px; }
            .btn-save:hover { background: #dc2626; }
            .btn-cancel { background: white; color: #475569; border: 1px solid #e2e8f0; padding: 10px 22px; border-radius: 24px; cursor: pointer; }
            .severity-indicator { display: flex; gap: 8px; margin-top: 8px; }
            .severity-option { flex: 1; padding: 8px; border: 1px solid #e2e8f0; border-radius: 12px; text-align: center; cursor: pointer; transition: all 0.2s; }
            .severity-option.active { border-color: #ef4444; background: #fef2f2; }
            .severity-option.minor { border-color: #10b981; }
            .severity-option.minor.active { background: #ecfdf5; }
            .severity-option.moderate { border-color: #4f46e5; }
            .severity-option.moderate.active { background: #eef2ff; }
            .severity-option.major { border-color: #f59e0b; }
            .severity-option.major.active { background: #fffbeb; }
            .severity-option.critical { border-color: #ef4444; }
            .severity-option.critical.active { background: #fef2f2; }
            #editSeverityDescription { margin-top: 8px; padding: 8px 12px; background: #f8fafc; border-radius: 8px; font-weight: 500; }
            .offense-id-badge { background: linear-gradient(145deg, #f8fafc, #f1f5f9); padding: 8px 14px; border-radius: 12px; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
            .employee-preview { display: flex; align-items: center; gap: 12px; margin-bottom: 20px; padding: 12px; background: #f8fafc; border-radius: 16px; }
            .employee-avatar-small { width: 40px; height: 40px; border-radius: 12px; background: ${off.color}; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; }
        </style>
        <div class="modal-edit-offense">
            <div class="offense-id-badge">
                <i class="fas fa-hashtag" style="color: #ef4444;"></i>
                <span style="font-weight: 600;">${off.id}</span>
                <span style="margin-left: auto; font-size: 0.75rem; color: #64748b;">Created: ${off.date}</span>
            </div>
            <div class="employee-preview">
                <div class="employee-avatar-small">${off.avatar}</div>
                <div><h4 style="font-weight:600;">${escapeHtml(off.employeeName)}</h4><p style="color:#64748b; font-size:0.75rem;">${off.employeeId || 'EMP'} • ${off.department}</p></div>
            </div>
            <form id="editOffenseForm" onsubmit="updateOffense(event, '${id}')">
                <div class="form-grid">
                    <div class="section-title"><i class="fas fa-gavel"></i> Offense Details</div>
                    <div class="form-group">
                        <label>Offense Type <span class="required-star">*</span></label>
                        <select id="editOffenseType" required>
                            <option value="Attendance" ${off.offenseType === 'Attendance' ? 'selected' : ''}>Attendance</option>
                            <option value="Misconduct" ${off.offenseType === 'Misconduct' ? 'selected' : ''}>Misconduct</option>
                            <option value="Policy Violation" ${off.offenseType === 'Policy Violation' ? 'selected' : ''}>Policy Violation</option>
                            <option value="Safety" ${off.offenseType === 'Safety' ? 'selected' : ''}>Safety</option>
                            <option value="Harassment" ${off.offenseType === 'Harassment' ? 'selected' : ''}>Harassment</option>
                            <option value="Performance" ${off.offenseType === 'Performance' ? 'selected' : ''}>Performance</option>
                            <option value="Theft" ${off.offenseType === 'Theft' ? 'selected' : ''}>Theft</option>
                            <option value="Insubordination" ${off.offenseType === 'Insubordination' ? 'selected' : ''}>Insubordination</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Offense Date <span class="required-star">*</span></label>
                        <input type="date" id="editOffenseDate" required value="${off.date}">
                    </div>
                    <div class="form-group full-width">
                        <label>Severity Level <span class="required-star">*</span></label>
                        <input type="hidden" id="editOffenseSeverity" value="${off.severity}">
                        <div class="severity-indicator">
                            <div class="severity-option minor ${off.severity === 'Minor' ? 'active' : ''}" onclick="setEditOffenseSeverity('Minor')" data-severity="Minor">
                                <i class="fas fa-circle" style="color: #10b981;"></i><br>Minor
                            </div>
                            <div class="severity-option moderate ${off.severity === 'Moderate' ? 'active' : ''}" onclick="setEditOffenseSeverity('Moderate')" data-severity="Moderate">
                                <i class="fas fa-circle" style="color: #4f46e5;"></i><br>Moderate
                            </div>
                            <div class="severity-option major ${off.severity === 'Major' ? 'active' : ''}" onclick="setEditOffenseSeverity('Major')" data-severity="Major">
                                <i class="fas fa-circle" style="color: #f59e0b;"></i><br>Major
                            </div>
                            <div class="severity-option critical ${off.severity === 'Critical' ? 'active' : ''}" onclick="setEditOffenseSeverity('Critical')" data-severity="Critical">
                                <i class="fas fa-circle" style="color: #ef4444;"></i><br>Critical
                            </div>
                        </div>
                        <div id="editSeverityDescription"></div>
                    </div>
                    <div class="form-group">
                        <label>Status <span class="required-star">*</span></label>
                        <select id="editOffenseStatus" required>
                            <option value="Pending Review" ${off.status === 'Pending Review' ? 'selected' : ''}>Pending Review</option>
                            <option value="Under Investigation" ${off.status === 'Under Investigation' ? 'selected' : ''}>Under Investigation</option>
                            <option value="Action Taken" ${off.status === 'Action Taken' ? 'selected' : ''}>Action Taken</option>
                            <option value="Closed" ${off.status === 'Closed' ? 'selected' : ''}>Closed</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Reported By <span class="required-star">*</span></label>
                        <input type="text" id="editReportedBy" required value="${escapeHtml(off.reportedBy)}">
                    </div>
                    <div class="form-group full-width">
                        <label>Offense Description <span class="required-star">*</span></label>
                        <textarea id="editOffenseDescription" required>${escapeHtml(off.description)}</textarea>
                    </div>
                    <div class="form-group full-width">
                        <label>Action Taken</label>
                        <textarea id="editActionTaken">${escapeHtml(off.actionTaken || '')}</textarea>
                    </div>
                </div>
                <div class="modal-buttons">
                    <button type="button" class="btn-cancel" onclick="closeModal()"><i class="fas fa-times"></i> Cancel</button>
                    <button type="submit" class="btn-save"><i class="fas fa-save"></i> Save Changes</button>
                </div>
            </form>
        </div>
    `;
    openModal('Edit Offense Record', content);
    updateEditSeverityDescription();
}

function setEditOffenseSeverity(severity) {
    document.getElementById('editOffenseSeverity').value = severity;
    
    document.querySelectorAll('.severity-option').forEach(opt => {
        opt.classList.remove('active');
        if (opt.dataset.severity === severity) opt.classList.add('active');
    });
    
    updateEditSeverityDescription();
}

function updateEditSeverityDescription() {
    const severity = document.getElementById('editOffenseSeverity').value;
    const descriptions = {
        'Minor': 'Minor - Verbal warning, informal documentation',
        'Moderate': 'Moderate - Requires formal documentation and follow-up',
        'Major': 'Major - Written warning, possible suspension',
        'Critical': 'Critical - Immediate action required, possible termination'
    };
    const colors = { 'Minor': '#10b981', 'Moderate': '#4f46e5', 'Major': '#f59e0b', 'Critical': '#ef4444' };
    
    const descEl = document.getElementById('editSeverityDescription');
    descEl.textContent = descriptions[severity];
    descEl.style.color = colors[severity];
    descEl.style.background = `${colors[severity]}10`;
}

function updateOffense(event, id) {
    event.preventDefault();
    
    const severity = document.getElementById('editOffenseSeverity').value;
    
    const payload = {
        id: id,
        offense_type: document.getElementById('editOffenseType').value,
        severity: severity,
        date: document.getElementById('editOffenseDate').value,
        status: document.getElementById('editOffenseStatus').value,
        reported_by: document.getElementById('editReportedBy').value,
        description: document.getElementById('editOffenseDescription').value,
        action_taken: document.getElementById('editActionTaken').value
    };
    
    fetch('/3ME/api/performance/offenses.php', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast('Offense updated successfully!', 'success');
            if (typeof loadOffenses === 'function') loadOffenses();
            closeModal();
        } else {
            showToast(data.message || 'Error updating offense', 'error');
        }
    })
    .catch(err => {
        console.error(err);
        showToast('System error updating offense', 'error');
    });
}
</script>
