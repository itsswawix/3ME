<!-- modal-edit-correction.php -->
<script>
function editCorrection(correctionId) {
    const correction = window.corrections.find(c => c.id === correctionId);
    
    if (!correction) {
        showToast('Correction not found', 'error');
        return;
    }
    
    const content = `
        <form id="editCorrectionForm" onsubmit="handleEditCorrection(event, '${correction.id}')">
            <div class="form-group">
                <label>Employee</label>
                <div style="background: #f8fafc; padding: 10px 12px; border-radius: 8px; border: 1px solid #e2e8f0;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <img src="${correction.profilePhoto || '/3ME/assets/images/default-avatar.png'}" style="width: 36px; height: 36px; border-radius: 10px; object-fit: cover; flex-shrink: 0;" />
                        <div>
                            <div style="font-weight: 500; color: #1e293b; font-size: 13px;">${escapeHtml(correction.employeeName)}</div>
                            <div style="font-size: 11px; color: #64748b;">${escapeHtml(correction.employeeEmail)}</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label>Correction Type <span class="required-star">*</span></label>
                <select id="correctionType" name="correctionType" required>
                    <option value="">Select Type</option>
                    <option value="Late" ${correction.type === 'Late' ? 'selected' : ''}>Late Arrival</option>
                    <option value="Early Departure" ${correction.type === 'Early Departure' ? 'selected' : ''}>Early Departure</option>
                    <option value="Missed Entry" ${correction.type === 'Missed Entry' ? 'selected' : ''}>Missed Clock In/Out</option>
                    <option value="Overtime Discrepancy" ${correction.type === 'Overtime Discrepancy' ? 'selected' : ''}>Overtime Discrepancy</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Original Date <span class="required-star">*</span></label>
                <input type="date" id="originalDate" name="originalDate" value="${formatDateForInput(correction.originalDate)}" required>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Corrected Time In</label>
                    <input type="time" id="timeIn" name="timeIn" value="${correction.timeIn || ''}">
                </div>
                <div class="form-group">
                    <label>Corrected Time Out</label>
                    <input type="time" id="timeOut" name="timeOut" value="${correction.timeOut || ''}">
                </div>
            </div>
            
            <div class="form-group">
                <label>Reason for Correction <span class="required-star">*</span></label>
                <textarea id="reason" name="reason" rows="4" required>${escapeHtml(correction.reason)}</textarea>
            </div>
            
            <div class="form-group">
                <label>Status</label>
                <select id="status" name="status">
                    <option value="Pending" ${correction.status === 'Pending' ? 'selected' : ''}>Pending</option>
                    <option value="Approved" ${correction.status === 'Approved' ? 'selected' : ''}>Approved</option>
                    <option value="Rejected" ${correction.status === 'Rejected' ? 'selected' : ''}>Rejected</option>
                </select>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="button" class="btn btn-danger" onclick="confirmDeleteCorrection('${correction.id}')">
                    <i class="fas fa-trash"></i> Delete
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>
        </form>
    `;
    
    openModal('Edit Correction Request', content);
}

function handleEditCorrection(event, correctionId) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const errors = [];
    
    // Validation
    if (!formData.get('correctionType')) {
        errors.push('Correction type is required');
    }
    if (!formData.get('originalDate')) {
        errors.push('Original date is required');
    }
    if (!formData.get('reason').trim()) {
        errors.push('Reason for correction is required');
    }
    
    if (errors.length > 0) {
        showValidationErrors(errors);
        return;
    }
    
    // Prepare data for API
    const correctionData = {
        id: correctionId,
        type: formData.get('correctionType'),
        originalDate: formData.get('originalDate'),
        timeIn: formData.get('timeIn') || null,
        timeOut: formData.get('timeOut') || null,
        reason: formData.get('reason'),
        status: formData.get('status'),
        approvedBy: 'Current User'
    };
    
    // Send to API
    fetch('/3ME/api/attendance/corrections.php', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(correctionData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload corrections from API
            loadAttendanceData();
            closeModal(true);
            showToast('Correction updated successfully!', 'success');
        } else {
            showToast(data.message || 'Error updating correction', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error updating correction', 'error');
    });
}

function confirmDeleteCorrection(correctionId) {
    showConfirmation(
        'Delete Correction',
        'Are you sure you want to delete this correction request? This action cannot be undone.',
        () => {
            deleteCorrection(correctionId);
        }
    );
}

function deleteCorrection(correctionId) {
    // Send DELETE request to API
    fetch(`/3ME/api/attendance/corrections.php?id=${correctionId}`, {
        method: 'DELETE'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload corrections from API
            loadAttendanceData();
            closeModal(true);
            showToast('Correction deleted successfully!', 'success');
        } else {
            showToast(data.message || 'Error deleting correction', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error deleting correction', 'error');
    });
}

// Make functions globally available
window.editCorrection = editCorrection;
window.handleEditCorrection = handleEditCorrection;
window.confirmDeleteCorrection = confirmDeleteCorrection;
window.deleteCorrection = deleteCorrection;
</script>
