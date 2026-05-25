<!-- modal-edit-leave-type.php -->
<script>
function editLeaveType(id) {
    const type = window.leaveTypes.find(t => t.id === id);
    if (!type) return;
    
    const content = `
        <form id="editLeaveTypeForm" onsubmit="updateLeaveType(event, '${id}')">
            <h3 style="font-size: 14px; font-weight: 600; color: #1e293b; margin: 0 0 16px; display: flex; align-items: center; gap: 8px; border-bottom: 1px solid #f1f5f9; padding-bottom: 6px;">
                <i class="fas fa-tag" style="color: #4f46e5;"></i> Leave Type Information
            </h3>
            
            <div class="form-group">
                <label>Leave Name <span class="required-star">*</span></label>
                <input type="text" id="editLeaveName" maxlength="50" required value="${escapeHtml(type.name)}">
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Default Annual Credits <span class="required-star">*</span></label>
                    <input type="number" id="editLeaveCredits" required step="0.5" min="0" value="${type.credits}">
                </div>
                <div class="form-group">
                    <label>Max Consecutive Days</label>
                    <input type="number" id="editMaxDuration" min="1" value="${type.maxDuration}">
                </div>
            </div>
            
            <div class="form-group">
                <label>Eligibility Rule</label>
                <textarea id="editEligibilityRule">${escapeHtml(type.eligibilityRule)}</textarea>
            </div>
            
            <div style="font-size: 11px; color: #94a3b8; margin-top: 12px; text-align: right;">
                <span class="required-star">*</span> Required fields
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>
        </form>
    `;
    openModal('Edit Leave Type', content);
}

async function updateLeaveType(event, id) {
    event.preventDefault();
    const name = document.getElementById('editLeaveName').value.trim();
    const credits = parseFloat(document.getElementById('editLeaveCredits').value);
    const maxDuration = parseInt(document.getElementById('editMaxDuration').value);
    const eligibilityRule = document.getElementById('editEligibilityRule').value.trim();
    
    if (!name) { showToast('Please enter a leave name.', 'warning'); return; }
    
    try {
        const response = await fetch('../../api/leave/types.php', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id, name, credits, maxDuration, eligibilityRule })
        });
        const result = await response.json();
        if (result.success) {
            closeModal();
            showToast('Leave type updated successfully!', 'success');
            if (typeof loadLeaveDataFromDB === 'function') {
                await loadLeaveDataFromDB();
            }
        } else {
            showToast(result.message || 'Failed to update leave type.', 'warning');
        }
    } catch (error) {
        console.error('Error updating leave type:', error);
        showToast('Failed to connect to database API.', 'warning');
    }
}

function viewLeaveType(id) { editLeaveType(id); }

// Make functions globally available
window.editLeaveType = editLeaveType;
window.updateLeaveType = updateLeaveType;
window.viewLeaveType = viewLeaveType;
</script>