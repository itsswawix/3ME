<!-- modal-add-exit.php -->
<script>
function openAddExitModal() {
    const content = `
        <style>
            .modal-add-exit * { margin: 0; box-sizing: border-box; }
            .modal-add-exit { font-family: 'Inter', sans-serif; max-width: 600px; width: 100%; }
            .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px 24px; }
            .full-width { grid-column: span 2; }
            .form-group { display: flex; flex-direction: column; margin-bottom: 4px; }
            .form-group label { font-size: 0.85rem; font-weight: 500; margin-bottom: 6px; color: #475569; letter-spacing: 0.3px; }
            .form-group input, .form-group select, .form-group textarea { padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 16px; font-size: 0.9rem; background: #ffffff; font-family: 'Inter', sans-serif; transition: all 0.2s ease; }
            .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: #ef4444; box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1); }
            .section-title { grid-column: span 2; font-size: 1rem; font-weight: 600; margin: 20px 0 8px; padding-bottom: 8px; border-bottom: 1.5px solid #e2e8f0; color: #0f172a; display: flex; align-items: center; gap: 8px; }
            .section-title i { color: #ef4444; font-size: 0.9rem; width: 20px; }
            .section-title:first-of-type { margin-top: 0; }
            .required-star { color: #ef4444; margin-left: 2px; }
            textarea { resize: vertical; min-height: 80px; }
            .modal-footer-note { font-size: 0.75rem; color: #94a3b8; margin-top: 16px; text-align: right; }
            .modal-buttons { display: flex; justify-content: flex-end; gap: 12px; margin-top: 28px; padding-top: 20px; border-top: 1px solid #f1f5f9; }
            .btn-save { background: #ef4444; color: white; border: none; padding: 10px 22px; border-radius: 24px; font-size: 0.85rem; font-weight: 500; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 6px; box-shadow: 0 4px 8px rgba(239, 68, 68, 0.2); }
            .btn-save:hover { background: #dc2626; transform: translateY(-1px); box-shadow: 0 6px 12px rgba(239, 68, 68, 0.25); }
            .btn-cancel { background: white; color: #475569; border: 1px solid #e2e8f0; padding: 10px 22px; border-radius: 24px; font-size: 0.85rem; font-weight: 500; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 6px; }
            .btn-cancel:hover { background: #f8fafc; border-color: #cbd5e1; }
            .employee-selector { background: #f8fafc; padding: 12px; border-radius: 16px; margin-bottom: 16px; }
            .employee-selector label { font-size: 0.85rem; font-weight: 500; margin-bottom: 8px; color: #475569; display: block; }
            .employee-selector select { width: 100%; padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 0.9rem; }
        </style>
        <div class="modal-add-exit">
            <div class="employee-selector">
                <label>Select Employee to Process Exit <span class="required-star">*</span></label>
                <select id="exitEmployeeSelect" onchange="populateEmployeeData()" required>
                    <option value="">Choose an employee...</option>
                    ${window.employees ? window.employees.filter(e => e.status === 'Active').map(emp => 
                        `<option value="${emp.id}" data-name="${escapeHtml(emp.name)}" data-email="${escapeHtml(emp.email)}" data-position="${escapeHtml(emp.position)}" data-department="${escapeHtml(emp.department)}" data-company="${escapeHtml(emp.company)}" data-employee-id="${escapeHtml(emp.employeeId || emp.employee_id)}">${escapeHtml(emp.name)} - ${escapeHtml(emp.position)}</option>`
                    ).join('') : '<option value="">No active employees found</option>'}
                </select>
            </div>
            
            <form id="addExitForm" onsubmit="event.preventDefault(); saveNewExit();">
                <div class="form-grid">
                    <div class="section-title"><i class="fas fa-user"></i> Employee Information</div>
                    <div class="form-group"><label>Employee ID</label><input type="text" id="newExitEmployeeId" readonly style="background: #f8fafc;"></div>
                    <div class="form-group"><label>Employee Name</label><input type="text" id="newExitEmployeeName" readonly style="background: #f8fafc;"></div>
                    <div class="form-group full-width"><label>Email</label><input type="email" id="newExitEmployeeEmail" readonly style="background: #f8fafc;"></div>
                    
                    <div class="section-title"><i class="fas fa-briefcase"></i> Position Details</div>
                    <div class="form-group"><label>Position</label><input type="text" id="newExitPosition" readonly style="background: #f8fafc;"></div>
                    <div class="form-group"><label>Department</label><input type="text" id="newExitDepartment" readonly style="background: #f8fafc;"></div>
                    <div class="form-group full-width"><label>Company</label><input type="text" id="newExitCompany" readonly style="background: #f8fafc;"></div>
                    
                    <div class="section-title"><i class="fas fa-calendar-times"></i> Exit Details</div>
                    <div class="form-group"><label>Last Working Day <span class="required-star">*</span></label><input type="date" id="newLastWorkingDay" required></div>
                    <div class="form-group">
                        <label>Status</label>
                        <select id="newExitStatus">
                            <option value="Pending">Pending Clearance</option>
                            <option value="Cleared">Cleared</option>
                            <option value="Archived">Archived</option>
                        </select>
                    </div>
                    
                    <div class="form-group full-width"><label>Reason for Leaving <span class="required-star">*</span></label><textarea id="newExitReason" required placeholder="Please provide the reason for leaving..."></textarea></div>
                    
                    <div class="section-title"><i class="fas fa-sticky-note"></i> Additional Information</div>
                    <div class="form-group"><label>Clearance Approved By</label><input type="text" id="newClearanceApprovedBy" placeholder="Approver name (if applicable)"></div>
                    <div class="form-group"><label>Resignation Letter</label><input type="text" id="newResignationLetter" placeholder="File name or reference"></div>
                    <div class="form-group full-width"><label>Notes</label><textarea id="newExitNotes" placeholder="Additional notes or comments..."></textarea></div>
                </div>
                <div class="modal-footer-note"><span class="required-star">*</span> Required fields</div>
                
                <div class="modal-buttons">
                    <button type="button" class="btn-cancel" onclick="closeAddExitModal()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i> Process Exit
                    </button>
                </div>
            </form>
        </div>
    `;
    
    openModal('Process Employee Exit', content);
    
    // Set today's date as default
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('newLastWorkingDay').value = today;
}

function populateEmployeeData() {
    const select = document.getElementById('exitEmployeeSelect');
    const selectedOption = select.options[select.selectedIndex];
    
    if (selectedOption.value) {
        document.getElementById('newExitEmployeeId').value = selectedOption.dataset.employeeId || '';
        document.getElementById('newExitEmployeeName').value = selectedOption.dataset.name || '';
        document.getElementById('newExitEmployeeEmail').value = selectedOption.dataset.email || '';
        document.getElementById('newExitPosition').value = selectedOption.dataset.position || '';
        document.getElementById('newExitDepartment').value = selectedOption.dataset.department || '';
        document.getElementById('newExitCompany').value = selectedOption.dataset.company || '';
    } else {
        // Clear all fields
        document.getElementById('newExitEmployeeId').value = '';
        document.getElementById('newExitEmployeeName').value = '';
        document.getElementById('newExitEmployeeEmail').value = '';
        document.getElementById('newExitPosition').value = '';
        document.getElementById('newExitDepartment').value = '';
        document.getElementById('newExitCompany').value = '';
    }
}

function closeAddExitModal() {
    if (typeof closeModal === 'function') {
        closeModal();
    }
}

function saveNewExit() {
    const employeeSelect = document.getElementById('exitEmployeeSelect');
    const selectedEmployeeId = employeeSelect.value;
    
    if (!selectedEmployeeId) {
        showToast('Please select an employee.', 'warning');
        return;
    }
    
    const employeeName = document.getElementById('newExitEmployeeName')?.value.trim();
    const reason = document.getElementById('newExitReason')?.value.trim();
    
    if (!employeeName || !reason) {
        showToast('Please fill all required fields.', 'warning');
        return;
    }
    
    const exitData = {
        employee_id: document.getElementById('newExitEmployeeId')?.value || selectedEmployeeId,
        employee_name: employeeName,
        employee_email: document.getElementById('newExitEmployeeEmail')?.value || '',
        position: document.getElementById('newExitPosition')?.value || '',
        department: document.getElementById('newExitDepartment')?.value || '',
        company: document.getElementById('newExitCompany')?.value || '',
        last_working_day: document.getElementById('newLastWorkingDay')?.value || new Date().toISOString().split('T')[0],
        reason: reason,
        status: document.getElementById('newExitStatus')?.value || 'Pending',
        clearance_approved_by: document.getElementById('newClearanceApprovedBy')?.value || null,
        resignation_letter: document.getElementById('newResignationLetter')?.value || null,
        notes: document.getElementById('newExitNotes')?.value || ''
    };
    
    // Send to API
    fetch('../../api/onboarding/exits.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(exitData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(`Exit record created for ${employeeName}!`, 'success');
            
            // Add to local array for immediate UI update
            const newExit = {
                id: data.id,
                employeeId: exitData.employee_id,
                employeeName: exitData.employee_name,
                employeeEmail: exitData.employee_email,
                position: exitData.position,
                department: exitData.department,
                company: exitData.company,
                lastWorkingDay: new Date(exitData.last_working_day).toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' }),
                reason: exitData.reason,
                status: exitData.status,
                clearanceApprovedBy: exitData.clearance_approved_by,
                resignationLetter: exitData.resignation_letter,
                notes: exitData.notes,
                avatar: employeeName.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2),
                color: 'linear-gradient(145deg, #ef4444, #f87171)'
            };
            
            if (typeof window.exitRecords !== 'undefined') {
                window.exitRecords.unshift(newExit);
                
                // Update UI if render functions exist
                if (typeof renderExitTable === 'function') {
                    renderExitTable(window.exitRecords);
                }
            }
            
            // Update employee status to terminated if needed
            if (window.employees && selectedEmployeeId) {
                const empIndex = window.employees.findIndex(e => e.id == selectedEmployeeId);
                if (empIndex !== -1) {
                    window.employees[empIndex].status = 'Terminated';
                }
            }
            
            if (typeof closeModal === 'function') {
                closeModal();
            }
        } else {
            showToast(data.message || 'Error creating exit record', 'warning');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error creating exit record', 'warning');
    });
}

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>]/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;' })[m] || m);
}

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.style.cssText = `position: fixed; bottom: 24px; right: 24px; background: ${type === 'success' ? '#10b981' : type === 'warning' ? '#f59e0b' : '#1e293b'}; color: white; padding: 12px 20px; border-radius: 12px; font-size: 13px; z-index: 10000; animation: slideIn 0.3s ease; box-shadow: 0 4px 12px rgba(0,0,0,0.15);`;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, 3000);
}
</script>