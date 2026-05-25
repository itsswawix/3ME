<!-- modal-add-leave-type.php -->
<script>
function openAddLeaveTypeModal() {
    const content = `
        <form id="addLeaveTypeForm" onsubmit="saveNewLeaveType(event)">
            <h3 style="font-size: 14px; font-weight: 600; color: #1e293b; margin: 0 0 16px; display: flex; align-items: center; gap: 8px; border-bottom: 1px solid #f1f5f9; padding-bottom: 6px;">
                <i class="fas fa-tag" style="color: #4f46e5;"></i> Leave Type Information
            </h3>
            
            <div class="form-group">
                <label>Leave Name <span class="required-star">*</span></label>
                <input type="text" id="newLeaveName" maxlength="50" required placeholder="e.g., Vacation Leave, Sick Leave">
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Default Annual Credits <span class="required-star">*</span></label>
                    <input type="number" id="newLeaveCredits" required step="0.5" min="0" value="15" placeholder="Days per year">
                </div>
                <div class="form-group">
                    <label>Max Consecutive Days</label>
                    <input type="number" id="newMaxDuration" min="1" value="30" placeholder="Maximum days">
                </div>
            </div>
            
            <div class="form-group">
                <label>Eligibility Rule</label>
                <textarea id="newEligibilityRule" placeholder="e.g., All regular employees after 3 months of service...">All regular employees are eligible after completing 3 months of probationary period.</textarea>
            </div>
            
            <div style="font-size: 11px; color: #94a3b8; margin-top: 12px; text-align: right;">
                <span class="required-star">*</span> Required fields
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Leave Type
                </button>
            </div>
        </form>
    `;
    openModal('New Leave Type', content);
}

async function saveNewLeaveType(event) {
    event.preventDefault();
    const name = document.getElementById('newLeaveName').value.trim();
    const credits = parseFloat(document.getElementById('newLeaveCredits').value);
    const maxDuration = parseInt(document.getElementById('newMaxDuration').value);
    const eligibilityRule = document.getElementById('newEligibilityRule').value.trim();
    
    if (!name) { showToast('Please enter a leave name.', 'warning'); return; }
    
    try {
        const response = await fetch('../../api/leave/types.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ name, credits, maxDuration, eligibilityRule: eligibilityRule || 'Not specified' })
        });
        const result = await response.json();
        if (result.success) {
            closeModal();
            showToast(`Leave type "${name}" added successfully!`, 'success');
            if (typeof loadLeaveDataFromDB === 'function') {
                await loadLeaveDataFromDB();
            }
        } else {
            showToast(result.message || 'Failed to create leave type.', 'warning');
        }
    } catch (error) {
        console.error('Error saving leave type:', error);
        showToast('Failed to connect to database API.', 'warning');
    }
}

// Make functions globally available
window.openAddLeaveTypeModal = openAddLeaveTypeModal;
window.saveNewLeaveType = saveNewLeaveType;
</script>