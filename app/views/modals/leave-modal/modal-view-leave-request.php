<!-- modal-view-leave-request.php -->
<script>
function viewLeaveRequest(id) {
    const req = window.leaveRequests.find(r => r.id === id);
    if (!req) return;
    
    const statusConfig = {
        'Pending': { bg: '#fef3c7', color: '#b45309', icon: 'fa-clock' },
        'Approved': { bg: '#dcfce7', color: '#16a34a', icon: 'fa-check-circle' },
        'Rejected': { bg: '#fee2e2', color: '#dc2626', icon: 'fa-times-circle' },
        'Cancelled': { bg: '#f1f5f9', color: '#64748b', icon: 'fa-ban' }
    }[req.status] || { bg: '#f1f5f9', color: '#64748b', icon: 'fa-circle' };
    
    const content = `
        <div style="display: flex; align-items: flex-start; gap: 16px; margin-bottom: 24px; padding-bottom: 20px; border-bottom: 1px solid #eef2ff;">
            <img src="${req.profilePhoto || '/3ME/assets/images/default-avatar.png'}" style="width: 64px; height: 64px; border-radius: 20px; object-fit: cover; flex-shrink: 0;" />
            <div>
                <h3 style="margin: 0; font-size: 16px; font-weight: 600; color: #0f172a;">${escapeHtml(req.employeeName)}</h3>
                <div style="font-size: 12px; color: #64748b; margin-top: 4px; display: flex; gap: 12px; flex-wrap: wrap;">
                    <span><i class="fas fa-id-card" style="color: #4f46e5;"></i> ${req.employeeId}</span>
                    <span><i class="fas fa-envelope" style="color: #4f46e5;"></i> ${req.employeeEmail}</span>
                </div>
                <span style="padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 500; background: ${statusConfig.bg}; color: ${statusConfig.color}; display: inline-flex; align-items: center; gap: 4px; margin-top: 8px;">
                    <i class="fas ${statusConfig.icon}"></i> ${req.status}
                </span>
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px 20px; margin-bottom: 20px;">
            <div>
                <div style="font-size: 10px; font-weight: 600; text-transform: uppercase; color: #94a3b8; margin-bottom: 4px; display: flex; align-items: center; gap: 4px;"><i class="fas fa-tag" style="color: #4f46e5;"></i> Leave Type</div>
                <div style="font-size: 13px; font-weight: 500; color: #1e293b;"><span style="background: #e0e7ff; color: #4338ca; padding: 2px 8px; border-radius: 6px; font-size: 11px; font-weight: 600;">${escapeHtml(req.leaveType)}</span></div>
            </div>
            <div>
                <div style="font-size: 10px; font-weight: 600; text-transform: uppercase; color: #94a3b8; margin-bottom: 4px; display: flex; align-items: center; gap: 4px;"><i class="fas fa-building" style="color: #4f46e5;"></i> Department</div>
                <div style="font-size: 13px; font-weight: 500; color: #1e293b;">${escapeHtml(req.department)}</div>
            </div>
            <div>
                <div style="font-size: 10px; font-weight: 600; text-transform: uppercase; color: #94a3b8; margin-bottom: 4px; display: flex; align-items: center; gap: 4px;"><i class="fas fa-calendar" style="color: #4f46e5;"></i> Date Range</div>
                <div style="font-size: 13px; font-weight: 500; color: #1e293b;">${req.startDate} - ${req.endDate}</div>
            </div>
            <div>
                <div style="font-size: 10px; font-weight: 600; text-transform: uppercase; color: #94a3b8; margin-bottom: 4px; display: flex; align-items: center; gap: 4px;"><i class="fas fa-clock" style="color: #4f46e5;"></i> Duration</div>
                <div style="font-size: 13px; font-weight: 500; color: #1e293b;">${req.duration} day${req.duration > 1 ? 's' : ''}</div>
            </div>
            <div>
                <div style="font-size: 10px; font-weight: 600; text-transform: uppercase; color: #94a3b8; margin-bottom: 4px; display: flex; align-items: center; gap: 4px;"><i class="fas fa-user-check" style="color: #4f46e5;"></i> Approved By</div>
                <div style="font-size: 13px; font-weight: 500; color: #1e293b;">${req.approvedBy || '—'}</div>
            </div>
        </div>
        
        <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid #eef2ff;">
            <h4 style="font-size: 13px; font-weight: 600; color: #0f172a; margin: 0 0 8px; display: flex; align-items: center; gap: 6px;"><i class="fas fa-comment" style="color: #4f46e5;"></i> Reason</h4>
            <div style="background: #f8fafc; padding: 12px; border-radius: 12px; font-size: 13px; line-height: 1.5; color: #334155; border: 1px solid #f1f5f9;">${escapeHtml(req.reason)}</div>
        </div>
        
        <div class="modal-footer" style="flex-wrap: wrap;">
            <button type="button" class="btn btn-secondary" onclick="closeModal()">
                <i class="fas fa-times"></i> Close
            </button>
            <button type="button" class="btn btn-primary" onclick="closeModal(); editLeaveRequest('${id}');">
                <i class="fas fa-edit"></i> Edit
            </button>
            ${req.status === 'Pending' ? `
                <button type="button" class="btn btn-success" onclick="approveRequest('${id}'); closeModal();">
                    <i class="fas fa-check"></i> Approve
                </button>
                <button type="button" class="btn btn-danger" onclick="rejectRequest('${id}'); closeModal();">
                    <i class="fas fa-times"></i> Reject
                </button>
            ` : ''}
        </div>
    `;
    openModal('Leave Request Details', content);
}

// Make function globally available
window.viewLeaveRequest = viewLeaveRequest;
</script>