<?php
/**
 * Modal for editing exit records
 */
?>

<script>
function editExit(exitId) {
    const exit = window.exitRecords.find(r => r.id === exitId);
    if (!exit) {
        showToast('Exit record not found', 'error');
        return;
    }

    const content = `
        <form id="editExitForm" onsubmit="updateExit(event, '${exitId}')">
            <!-- Employee Summary -->
            <div class="employee-summary-card">
                <div class="summary-avatar" style="background: ${exit.color};">${exit.avatar}</div>
                <div class="summary-info">
                    <h4>${exit.employeeName}</h4>
                    <p>${exit.position} • ${exit.department}</p>
                </div>
            </div>

            <div class="section-title-sm">
                <i class="fas fa-calendar"></i> Exit Details
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Last Working Day <span class="required-star">*</span></label>
                    <input type="date" id="editLastWorkingDay" value="${exit.lastWorkingDay}" required>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select id="editExitStatus">
                        <option value="Pending" ${exit.status === 'Pending' ? 'selected' : ''}>Pending</option>
                        <option value="Cleared" ${exit.status === 'Cleared' ? 'selected' : ''}>Cleared</option>
                        <option value="Archived" ${exit.status === 'Archived' ? 'selected' : ''}>Archived</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Reason for Leaving <span class="required-star">*</span></label>
                <textarea id="editExitReason" required>${exit.reason}</textarea>
            </div>

            <div class="form-group">
                <label>Clearance Approved By</label>
                <input type="text" id="editClearanceApprovedBy" value="${exit.clearanceApprovedBy || ''}" placeholder="Enter approver name">
            </div>

            <div class="form-group">
                <label>Notes</label>
                <textarea id="editExitNotes" placeholder="Additional notes...">${exit.notes || ''}</textarea>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Exit Record
                </button>
            </div>
        </form>
    `;

    openModal('Edit Exit Record', content);
}

function updateExit(event, exitId) {
    event.preventDefault();
    
    const exit = window.exitRecords.find(r => r.id === exitId);
    if (!exit) return;

    // Update exit record
    exit.lastWorkingDay = document.getElementById('editLastWorkingDay').value;
    exit.status = document.getElementById('editExitStatus').value;
    exit.reason = document.getElementById('editExitReason').value;
    exit.clearanceApprovedBy = document.getElementById('editClearanceApprovedBy').value;
    exit.notes = document.getElementById('editExitNotes').value;

    closeModal();
    showToast('Exit record updated successfully!', 'success');
    
    // Re-render the table
    if (typeof renderExitTable === 'function') {
        renderExitTable(window.exitRecords);
    }
}
</script>