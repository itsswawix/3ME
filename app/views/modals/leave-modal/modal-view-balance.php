<!-- modal-view-balance.php -->
<script>
function viewBalanceDetails(employeeId) {
    // Get employee balances
    const employeeBalances = window.leaveBalances.filter(b => b.employeeId === employeeId);
    if (employeeBalances.length === 0) return;
    
    const employee = employeeBalances[0];
    const fullName = employee.employeeName;
    const totalAccrued = employeeBalances.reduce((sum, b) => sum + b.accrued, 0);
    const totalUsed = employeeBalances.reduce((sum, b) => sum + b.used, 0);
    const totalBalance = employeeBalances.reduce((sum, b) => sum + b.balance, 0);
    
    const content = `
        <!-- Employee Header -->
        <div style="display: flex; align-items: flex-start; gap: 16px; margin-bottom: 24px; padding-bottom: 20px; border-bottom: 1px solid #eef2ff;">
            <img src="${employee.profilePhoto || '/3ME/assets/images/default-avatar.png'}" style="width: 64px; height: 64px; border-radius: 20px; object-fit: cover; flex-shrink: 0;" />
            <div>
                <h3 style="margin: 0; font-size: 16px; font-weight: 600; color: #0f172a;">${escapeHtml(fullName)}</h3>
                <div style="font-size: 12px; color: #64748b; margin-top: 4px; display: flex; gap: 12px; flex-wrap: wrap;">
                    <span><i class="fas fa-id-card" style="color: #4f46e5; width: 14px;"></i> ${escapeHtml(employee.employeeId)}</span>
                    <span><i class="fas fa-building" style="color: #4f46e5; width: 14px;"></i> ${escapeHtml(employee.department)}</span>
                </div>
            </div>
        </div>
        
        <!-- Summary Cards -->
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 24px;">
            <div style="background: #f8fafc; padding: 12px 8px; border-radius: 16px; text-align: center; border: 1px solid #e2e8f0;">
                <div style="font-size: 9px; font-weight: 600; text-transform: uppercase; color: #64748b; margin-bottom: 6px; display: flex; align-items: center; justify-content: center; gap: 4px;"><i class="fas fa-plus-circle" style="color: #10b981;"></i> Accrued</div>
                <div style="font-size: 20px; font-weight: 700; color: #10b981;">${totalAccrued.toFixed(1)}<span style="font-size: 10px; color: #64748b; font-weight: 400; margin-left: 2px;">d</span></div>
            </div>
            <div style="background: #f8fafc; padding: 12px 8px; border-radius: 16px; text-align: center; border: 1px solid #e2e8f0;">
                <div style="font-size: 9px; font-weight: 600; text-transform: uppercase; color: #64748b; margin-bottom: 6px; display: flex; align-items: center; justify-content: center; gap: 4px;"><i class="fas fa-minus-circle" style="color: #ef4444;"></i> Used</div>
                <div style="font-size: 20px; font-weight: 700; color: #ef4444;">${totalUsed.toFixed(1)}<span style="font-size: 10px; color: #64748b; font-weight: 400; margin-left: 2px;">d</span></div>
            </div>
            <div style="background: #f8fafc; padding: 12px 8px; border-radius: 16px; text-align: center; border: 1px solid #e2e8f0;">
                <div style="font-size: 9px; font-weight: 600; text-transform: uppercase; color: #64748b; margin-bottom: 6px; display: flex; align-items: center; justify-content: center; gap: 4px;"><i class="fas fa-balance-scale" style="color: #4f46e5;"></i> Available</div>
                <div style="font-size: 20px; font-weight: 700; color: #4f46e5;">${totalBalance.toFixed(1)}<span style="font-size: 10px; color: #64748b; font-weight: 400; margin-left: 2px;">d</span></div>
            </div>
        </div>
        
        <!-- Leave Types Breakdown -->
        <h3 style="font-size: 14px; font-weight: 600; color: #1e293b; margin: 24px 0 16px; display: flex; align-items: center; gap: 8px; border-bottom: 1px solid #f1f5f9; padding-bottom: 6px;">
            <i class="fas fa-list-ul" style="color: #4f46e5;"></i> Leave Balance Breakdown
        </h3>
        
        <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 24px;">
            ${employeeBalances.map(bal => {
                const usagePercent = bal.accrued > 0 ? (bal.used / bal.accrued) * 100 : 0;
                let barClass = 'success';
                if (usagePercent > 75) barClass = 'danger';
                else if (usagePercent > 50) barClass = 'warning';
                
                const iconColor = bal.leaveType === 'Vacation Leave' ? '#10b981' : 
                                (bal.leaveType === 'Sick Leave' ? '#ef4444' : 
                                (bal.leaveType === 'Emergency Leave' ? '#f59e0b' : '#4f46e5'));
                
                const icon = bal.leaveType === 'Vacation Leave' ? 'umbrella-beach' : 
                            (bal.leaveType === 'Sick Leave' ? 'hospital' : 
                            (bal.leaveType === 'Emergency Leave' ? 'exclamation-triangle' : 
                            (bal.leaveType === 'Maternity Leave' ? 'baby' :
                            (bal.leaveType === 'Paternity Leave' ? 'baby-carriage' :
                            (bal.leaveType === 'Bereavement Leave' ? 'heart-broken' : 'calendar')))));
                
                return `
                    <div style="display: flex; flex-direction: column; padding: 14px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; gap: 10px;">
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; font-size: 14px; background: ${iconColor}; flex-shrink: 0;">
                                    <i class="fas fa-${icon}"></i>
                                </div>
                                <div>
                                    <h4 style="margin: 0; font-size: 13px; font-weight: 600; color: #0f172a;">${escapeHtml(bal.leaveType)}</h4>
                                    <p style="margin: 2px 0 0; font-size: 10px; color: #64748b;">Last accrual: ${bal.lastAccrual}</p>
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 13px; font-weight: 700; color: #4f46e5;">${bal.balance.toFixed(1)} <span style="font-size: 10px; color: #64748b; font-weight: 400;">days</span></div>
                                <div style="font-size: 10px; color: #94a3b8; margin-top: 2px;">Acc: ${bal.accrued.toFixed(1)} | Used: ${bal.used.toFixed(1)}</div>
                            </div>
                        </div>
                        <div style="width: 100%; height: 6px; background: #e2e8f0; border-radius: 3px; overflow: hidden;">
                            <div style="height: 100%; border-radius: 3px; width: ${usagePercent}%; background: ${barClass === 'danger' ? '#ef4444' : barClass === 'warning' ? '#f59e0b' : '#10b981'};"></div>
                        </div>
                    </div>
                `;
            }).join('')}
        </div>
        
        <!-- Info Note -->
        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 12px; margin-top: 16px; display: flex; align-items: flex-start; gap: 8px;">
            <i class="fas fa-info-circle" style="color: #10b981; font-size: 14px; margin-top: 2px;"></i>
            <p style="margin: 0; font-size: 11px; color: #166534; line-height: 1.4;">
                <strong>Leave Policy Note:</strong> Vacation and sick leaves accrue monthly at 1.25 days per month. 
                Unused vacation leaves may be carried over up to 5 days to the next year. 
                Sick leaves do not carry over.
            </p>
        </div>
        
        <div style="font-size: 11px; color: #64748b; text-align: right; margin-top: 10px;">
            <i class="far fa-clock"></i> Last accrual date: ${employeeBalances[0].lastAccrual}
        </div>
        
        <!-- Action Buttons -->
        <div class="modal-footer" style="flex-wrap: wrap;">
            <button type="button" class="btn btn-secondary" onclick="closeModal()">
                <i class="fas fa-times"></i> Close
            </button>
            <button type="button" class="btn btn-info" onclick="closeModal(); viewAccrualHistory('${employeeBalances[0].id}')">
                <i class="fas fa-history"></i> View History
            </button>
            <button type="button" class="btn btn-primary" onclick="closeModal(); requestLeaveForEmployee('${employeeId}')">
                <i class="fas fa-calendar-plus"></i> Request Leave
            </button>
        </div>
    `;
    
    openModal('Leave Balance Details', content);
}

// Helper function to request leave for specific employee
function requestLeaveForEmployee(employeeId) {
    const employee = window.leaveBalances.find(b => b.employeeId === employeeId);
    if (employee) {
        // Store selected employee for pre-filling the request form
        window.selectedEmployeeForLeave = {
            id: employeeId,
            name: employee.employeeName,
            department: employee.department
        };
        openAddLeaveRequestModal();
    }
}

// Escape HTML function
function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}

// Make function globally available
window.viewBalanceDetails = viewBalanceDetails;
window.requestLeaveForEmployee = requestLeaveForEmployee;
</script>