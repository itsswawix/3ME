<!-- modal-edit-leave-request.php -->
<script>
function editLeaveRequest(id) {
    const req = window.leaveRequests.find(r => r.id === id);
    if (!req) return;
    
    const startDateValue = req.startDateRaw || '';
    const endDateValue = req.endDateRaw || '';
    
    const content = `
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px; padding: 12px; background: #f8fafc; border-radius: 16px; border: 1px solid #e2e8f0;">
            <img src="${req.profilePhoto || '/3ME/assets/images/default-avatar.png'}" style="width: 40px; height: 40px; border-radius: 12px; object-fit: cover; flex-shrink: 0;" />
            <div>
                <h4 style="margin: 0; font-size: 13px; font-weight: 600; color: #0f172a;">${escapeHtml(req.employeeName)}</h4>
                <p style="margin: 2px 0 0; color: #64748b; font-size: 11px; display: flex; gap: 8px;">
                    <span>ID: ${req.employeeId}</span>
                </p>
            </div>
        </div>
        
        <form id="editLeaveRequestForm" onsubmit="updateLeaveRequest(event, '${id}')">
            <h3 style="font-size: 14px; font-weight: 600; color: #1e293b; margin: 0 0 16px; display: flex; align-items: center; gap: 8px; border-bottom: 1px solid #f1f5f9; padding-bottom: 6px;">
                <i class="fas fa-calendar" style="color: #4f46e5;"></i> Leave Details
            </h3>
            
            <div class="form-group">
                <label>Leave Type <span class="required-star">*</span></label>
                <select id="editRequestLeaveType" required>
                    ${window.leaveTypes.map(t => `<option value="${t.id}" ${req.leaveTypeId === t.id ? 'selected' : ''}>${t.name}</option>`).join('')}
                </select>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Start Date <span class="required-star">*</span></label>
                    <input type="date" id="editStartDate" required value="${startDateValue}" onchange="calculateEditDuration()">
                </div>
                <div class="form-group">
                    <label>End Date <span class="required-star">*</span></label>
                    <input type="date" id="editEndDate" required value="${endDateValue}" onchange="calculateEditDuration()">
                </div>
            </div>
            
            <div class="form-group">
                <label>Duration</label>
                <input type="text" id="editDuration" readonly style="background: #f8fafc;" value="${req.duration} day${req.duration > 1 ? 's' : ''}">
            </div>
            
            <div class="form-group">
                <label>Reason <span class="required-star">*</span></label>
                <textarea id="editRequestReason" required rows="3">${escapeHtml(req.reason)}</textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Status <span class="required-star">*</span></label>
                    <select id="editRequestStatus" required>
                        <option value="Pending" ${req.status === 'Pending' ? 'selected' : ''}>Pending</option>
                        <option value="Approved" ${req.status === 'Approved' ? 'selected' : ''}>Approved</option>
                        <option value="Rejected" ${req.status === 'Rejected' ? 'selected' : ''}>Rejected</option>
                        <option value="Cancelled" ${req.status === 'Cancelled' ? 'selected' : ''}>Cancelled</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Approved By</label>
                    <input type="text" id="editApprovedBy" value="${escapeHtml(req.approvedBy || '')}" placeholder="Approver name...">
                </div>
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
    openModal('Edit Leave Request', content);
}

function calculateEditDuration() {
    const start = document.getElementById('editStartDate')?.value;
    const end = document.getElementById('editEndDate')?.value;
    if (start && end) {
        const diffDays = Math.ceil((new Date(end) - new Date(start)) / (1000 * 60 * 60 * 24)) + 1;
        const durationInput = document.getElementById('editDuration');
        if (durationInput) durationInput.value = diffDays + ' day' + (diffDays > 1 ? 's' : '');
    }
}

async function updateLeaveRequest(event, id) {
    event.preventDefault();
    
    const leaveTypeSelect = document.getElementById('editRequestLeaveType');
    const leaveTypeId = leaveTypeSelect.value;
    const startDate = document.getElementById('editStartDate').value;
    const endDate = document.getElementById('editEndDate').value;
    const duration = parseInt(document.getElementById('editDuration').value.split(' ')[0]);
    const reason = document.getElementById('editRequestReason').value.trim();
    const status = document.getElementById('editRequestStatus').value;
    const approvedBy = document.getElementById('editApprovedBy').value.trim() || null;
    
    try {
        const response = await fetch('../../api/leave/requests.php', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                id,
                leaveTypeId,
                startDate,
                endDate,
                duration,
                reason,
                status,
                approvedBy
            })
        });
        const result = await response.json();
        if (result.success) {
            closeModal();
            showToast('Leave request updated successfully!', 'success');
            if (typeof loadLeaveDataFromDB === 'function') {
                await loadLeaveDataFromDB();
            }
        } else {
            showToast(result.message || 'Failed to update leave request.', 'warning');
        }
    } catch (error) {
        console.error('Error updating leave request:', error);
        showToast('Failed to connect to database API.', 'warning');
    }
}

// Make functions globally available
window.editLeaveRequest = editLeaveRequest;
window.calculateEditDuration = calculateEditDuration;
window.updateLeaveRequest = updateLeaveRequest;
</script>