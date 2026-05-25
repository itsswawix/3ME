<!-- modal-edit-roster.php -->
<script>
async function editRoster(rosterId) {
    const roster = window.rosters.find(r => r.id === rosterId);
    
    if (!roster) {
        showToast('Roster not found', 'error');
        return;
    }
    
    // Load companies from database
    const companies = await window.loadCompaniesFromDB();
    
    const companyOptions = companies.map(c => 
        `<option value="${c.id}" ${roster.companyId === c.id ? 'selected' : ''}>${escapeHtml(c.name)}</option>`
    ).join('');
    
    const content = `
        <style>
            .time-preview {
                background: #f8fafc;
                border: 1px solid #e2e8f0;
                border-radius: 8px;
                padding: 12px;
                margin-top: 12px;
                text-align: center;
            }
            .time-preview-label {
                font-size: 11px;
                color: #64748b;
                margin-bottom: 4px;
            }
            .time-preview-value {
                font-size: 16px;
                font-weight: 600;
                color: #4f46e5;
            }
        </style>
        <form id="editRosterForm" onsubmit="handleEditRoster(event, '${roster.id}')">
            <div class="form-group">
                <label>Shift Name <span class="required-star">*</span></label>
                <input type="text" id="shiftName" name="shiftName" value="${escapeHtml(roster.shiftName)}" required>
            </div>
            
            <div class="form-group">
                <label>Company <span class="required-star">*</span></label>
                <select id="companyId" name="companyId" required>
                    <option value="">Select Company</option>
                    ${companyOptions}
                </select>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Start Time <span class="required-star">*</span></label>
                    <input type="time" id="startTime" name="startTime" value="${roster.startTime}" required onchange="updateDurationPreviewEdit()">
                </div>
                <div class="form-group">
                    <label>End Time <span class="required-star">*</span></label>
                    <input type="time" id="endTime" name="endTime" value="${roster.endTime}" required onchange="updateDurationPreviewEdit()">
                </div>
            </div>
            
            <div class="time-preview" id="durationPreviewEdit">
                <div class="time-preview-label">Shift Duration</div>
                <div class="time-preview-value" id="durationValueEdit">${calculateDuration(roster.startTime, roster.endTime)}</div>
            </div>
            
            <div class="form-group">
                <label>Break Duration (minutes)</label>
                <input type="number" id="breakDuration" name="breakDuration" value="${roster.breakDuration || 60}" min="0">
            </div>
            
            <div class="form-group">
                <label>Overtime Rule <span class="required-star">*</span></label>
                <select id="overtimeRule" name="overtimeRule" required>
                    <option value="">Select Overtime Rule</option>
                    <option value="After 8 hours - 1.25x rate" ${roster.overtimeRule === 'After 8 hours - 1.25x rate' ? 'selected' : ''}>After 8 hours - 1.25x rate</option>
                    <option value="After 9 hours - 1.5x rate" ${roster.overtimeRule === 'After 9 hours - 1.5x rate' ? 'selected' : ''}>After 9 hours - 1.5x rate</option>
                    <option value="After 10 hours - 2x rate" ${roster.overtimeRule === 'After 10 hours - 2x rate' ? 'selected' : ''}>After 10 hours - 2x rate</option>
                    <option value="Weekend - 1.5x rate" ${roster.overtimeRule === 'Weekend - 1.5x rate' ? 'selected' : ''}>Weekend - 1.5x rate</option>
                    <option value="Holiday - 2x rate" ${roster.overtimeRule === 'Holiday - 2x rate' ? 'selected' : ''}>Holiday - 2x rate</option>
                    <option value="No overtime allowed" ${roster.overtimeRule === 'No overtime allowed' ? 'selected' : ''}>No overtime allowed</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Late Grace Period (minutes)</label>
                <input type="number" id="lateGracePeriod" name="lateGracePeriod" value="${roster.lateGracePeriod || 15}" min="0">
            </div>
            
            <div class="form-group">
                <label>Effective Date <span class="required-star">*</span></label>
                <input type="date" id="effectiveDate" name="effectiveDate" value="${formatDateForInput(roster.effectiveDate)}" required>
            </div>
            
            <div class="form-group">
                <label>Notes</label>
                <textarea id="notes" name="notes" rows="3">${escapeHtml(roster.notes || '')}</textarea>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="button" class="btn btn-danger" onclick="confirmDeleteRoster('${roster.id}')">
                    <i class="fas fa-trash"></i> Delete
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>
        </form>
    `;
    
    openModal('Edit Roster', content);
}

function updateDurationPreviewEdit() {
    const startTime = document.getElementById('startTime').value;
    const endTime = document.getElementById('endTime').value;
    const durationValue = document.getElementById('durationValueEdit');
    
    if (startTime && endTime) {
        const duration = calculateDuration(startTime, endTime);
        durationValue.textContent = duration;
    }
}

function handleEditRoster(event, rosterId) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const errors = [];
    
    // Validation
    if (!formData.get('shiftName').trim()) {
        errors.push('Shift name is required');
    }
    if (!formData.get('companyId')) {
        errors.push('Company is required');
    }
    if (!formData.get('startTime')) {
        errors.push('Start time is required');
    }
    if (!formData.get('endTime')) {
        errors.push('End time is required');
    }
    if (!formData.get('overtimeRule')) {
        errors.push('Overtime rule is required');
    }
    if (!formData.get('effectiveDate')) {
        errors.push('Effective date is required');
    }
    
    if (errors.length > 0) {
        showValidationErrors(errors);
        return;
    }
    
    // Prepare data for API
    const rosterData = {
        id: rosterId,
        shiftName: formData.get('shiftName'),
        companyId: formData.get('companyId'),
        startTime: formData.get('startTime'),
        endTime: formData.get('endTime'),
        breakDuration: parseInt(formData.get('breakDuration')) || 0,
        overtimeRule: formData.get('overtimeRule'),
        lateGracePeriod: parseInt(formData.get('lateGracePeriod')) || 0,
        effectiveDate: formData.get('effectiveDate'),
        notes: formData.get('notes') || ''
    };
    
    // Send to API
    fetch('/3ME/api/attendance/rosters.php', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(rosterData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload rosters from API
            loadAttendanceData();
            closeModal(true);
            showToast('Roster updated successfully!', 'success');
        } else {
            showToast(data.message || 'Error updating roster', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error updating roster', 'error');
    });
}

function confirmDeleteRoster(rosterId) {
    showConfirmation(
        'Delete Roster',
        'Are you sure you want to delete this roster? This action cannot be undone.',
        () => {
            deleteRoster(rosterId);
        }
    );
}

function deleteRoster(rosterId) {
    // Send DELETE request to API
    fetch(`/3ME/api/attendance/rosters.php?id=${rosterId}`, {
        method: 'DELETE'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload rosters from API
            loadAttendanceData();
            closeModal(true);
            showToast('Roster deleted successfully!', 'success');
        } else {
            showToast(data.message || 'Error deleting roster', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error deleting roster', 'error');
    });
}

function duplicateRoster(rosterId) {
    const roster = window.rosters.find(r => r.id === rosterId);
    
    if (!roster) {
        showToast('Roster not found', 'error');
        return;
    }
    
    const newRoster = {
        ...roster,
        id: 'RST-' + Date.now(),
        shiftName: roster.shiftName + ' (Copy)',
        createdBy: 'Current User',
        createdDate: new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
    };
    
    window.rosters.unshift(newRoster);
    renderRosterTable(window.rosters);
    showToast('Roster duplicated successfully!', 'success');
}

function assignEmployees(rosterId) {
    const roster = window.rosters.find(r => r.id === rosterId);
    
    if (!roster) {
        showToast('Roster not found', 'error');
        return;
    }
    
    showToast('Employee assignment feature coming soon!', 'info');
}

// Make functions globally available
window.editRoster = editRoster;
window.updateDurationPreviewEdit = updateDurationPreviewEdit;
window.handleEditRoster = handleEditRoster;
window.confirmDeleteRoster = confirmDeleteRoster;
window.deleteRoster = deleteRoster;
window.duplicateRoster = duplicateRoster;
window.assignEmployees = assignEmployees;
</script>
