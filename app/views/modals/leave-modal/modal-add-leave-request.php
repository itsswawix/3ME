<!-- modal-add-leave-request.php -->
<script>
function openAddLeaveRequestModal() {
    window.selectedLeaveEmployeeName = null;
    
    const content = `
        <style>
            .search-select-container { position: relative; }
            .employee-search-results { position: absolute; top: 100%; left: 0; right: 0; z-index: 1000; background: white; max-height: 200px; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 12px; margin-top: 4px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); }
            .employee-search-item { padding: 10px 12px; cursor: pointer; border-bottom: 1px solid #f1f5f9; transition: background 0.2s; }
            .employee-search-item:hover { background: #f8fafc; }
            .employee-search-item:last-child { border-bottom: none; }
        </style>
        
        <form id="addLeaveRequestForm" onsubmit="saveNewLeaveRequest(event)">
            <!-- Section: Employee Information -->
            <h3 style="font-size: 14px; font-weight: 600; color: #1e293b; margin: 0 0 16px; display: flex; align-items: center; gap: 8px; border-bottom: 1px solid #f1f5f9; padding-bottom: 6px;">
                <i class="fas fa-user" style="color: #4f46e5;"></i> Employee Information
            </h3>
            
            <div class="form-group" style="position: relative;">
                <label>Employee <span class="required-star">*</span></label>
                <div class="search-select-container">
                    <input type="text" id="newRequestEmployeeSearch" placeholder="Click to select or type to search employee..." autocomplete="off" onfocus="showLeaveEmployeeDropdown()" oninput="filterLeaveEmployees(this.value)" onblur="hideLeaveEmployeeDropdown()" required>
                    <input type="hidden" id="newRequestEmployee" name="employeeId">
                </div>
                <div id="newRequestEmployeeResults" class="employee-search-results" style="display: none;"></div>
            </div>
            
            <!-- Section: Leave Details -->
            <h3 style="font-size: 14px; font-weight: 600; color: #1e293b; margin: 20px 0 16px; display: flex; align-items: center; gap: 8px; border-bottom: 1px solid #f1f5f9; padding-bottom: 6px;">
                <i class="fas fa-calendar" style="color: #4f46e5;"></i> Leave Details
            </h3>
            
            <div class="form-group">
                <label>Leave Type <span class="required-star">*</span></label>
                <select id="newRequestLeaveType" required onchange="updateNewBalanceDisplay()">
                    <option value="">Select Leave Type</option>
                    ${window.leaveTypes.map(t => `<option value="${t.id}" data-credits="${t.credits}">${t.name}</option>`).join('')}
                </select>
            </div>
            
            <div id="newBalanceDisplay" style="display: none; background: #f0fdf4; border-radius: 12px; padding: 12px 14px; margin-bottom: 18px; align-items: center; justify-content: space-between; border: 1px solid #bbf7d0;">
                <span style="color: #166534; font-weight: 500; font-size: 13px;"><i class="fas fa-info-circle"></i> Available Balance:</span>
                <span id="newBalanceValue" style="font-weight: 700; color: #166534; font-size: 14px;">15.0 days</span>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Start Date <span class="required-star">*</span></label>
                    <input type="date" id="newStartDate" required onchange="calculateNewDuration()">
                </div>
                <div class="form-group">
                    <label>End Date <span class="required-star">*</span></label>
                    <input type="date" id="newEndDate" required onchange="calculateNewDuration()">
                </div>
            </div>
            
            <div class="form-group">
                <label>Duration (Days)</label>
                <input type="text" id="newDuration" readonly style="background: #f8fafc;">
            </div>
            
            <div class="form-group">
                <label>Reason <span class="required-star">*</span></label>
                <textarea id="newRequestReason" required placeholder="Please provide reason for leave request..." rows="3"></textarea>
            </div>
            
            <div style="font-size: 11px; color: #94a3b8; margin-top: 12px; text-align: right;">
                <span class="required-star">*</span> Required fields
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Submit Request
                </button>
            </div>
        </form>
    `;
    openModal('New Leave Request', content);
    
    // Set default dates to today
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('newStartDate').value = today;
    document.getElementById('newEndDate').value = today;
    calculateNewDuration();
    loadLeaveEmployeesFromDB();
}

// Employee cache for leave modal
let leaveEmployeesCache = null;

async function loadLeaveEmployeesFromDB() {
    if (leaveEmployeesCache) return leaveEmployeesCache;
    
    try {
        const response = await fetch('../../api/employees/employees.php');
        const result = await response.json();
        if (result.success) {
            leaveEmployeesCache = result.data || [];
            return leaveEmployeesCache;
        }
    } catch (error) {
        console.error('Error loading employees:', error);
    }
    return [];
}

async function showLeaveEmployeeDropdown() {
    const resultsDiv = document.getElementById('newRequestEmployeeResults');
    const employees = await loadLeaveEmployeesFromDB();
    
    const sortedEmployees = [...employees].sort((a, b) => {
        const nameA = `${a.surname || ''}, ${a.firstname || ''}`.toLowerCase();
        const nameB = `${b.surname || ''}, ${b.firstname || ''}`.toLowerCase();
        return nameA.localeCompare(nameB);
    });

    renderLeaveEmployeeDropdown(sortedEmployees);
    resultsDiv.style.display = 'block';
}

async function filterLeaveEmployees(query) {
    const resultsDiv = document.getElementById('newRequestEmployeeResults');
    const employees = await loadLeaveEmployeesFromDB();
    
    const filtered = employees.filter(emp => {
        const fullName = `${emp.firstname} ${emp.middlename || ''} ${emp.surname}`.toLowerCase();
        const email = (emp.email || '').toLowerCase();
        const searchTerm = query.toLowerCase();
        
        return fullName.includes(searchTerm) || email.includes(searchTerm);
    });
    
    const sortedFiltered = filtered.sort((a, b) => {
        const nameA = `${a.surname || ''}, ${a.firstname || ''}`.toLowerCase();
        const nameB = `${b.surname || ''}, ${b.firstname || ''}`.toLowerCase();
        return nameA.localeCompare(nameB);
    });

    renderLeaveEmployeeDropdown(sortedFiltered);
    resultsDiv.style.display = 'block';
    
    if (window.selectedLeaveEmployeeName && query !== window.selectedLeaveEmployeeName) {
        document.getElementById('newRequestEmployee').value = '';
        window.selectedLeaveEmployeeName = null;
    }
}

function renderLeaveEmployeeDropdown(employeesList) {
    const resultsDiv = document.getElementById('newRequestEmployeeResults');
    
    if (employeesList.length === 0) {
        resultsDiv.innerHTML = '<div style="padding: 12px; text-align: center; color: #64748b; font-size: 12px;">No employees found</div>';
        return;
    }
    
    resultsDiv.innerHTML = employeesList.map(emp => {
        const fullName = `${emp.firstname} ${emp.middlename ? emp.middlename + ' ' : ''}${emp.surname}`;
        const position = emp.position || emp.job || 'No Position';
        return `
            <div class="employee-search-item" onmousedown="selectLeaveEmployee('${emp.employee_id}', '${escapeHtmlForLeave(fullName)}', '${escapeHtmlForLeave(emp.email || '')}', '${escapeHtmlForLeave(position)}', '${escapeHtmlForLeave(emp.department || '')}')">
                <div style="font-weight: 500; color: #1e293b; font-size: 13px;">${escapeHtmlForLeave(fullName)}</div>
                <div style="font-size: 11px; color: #64748b;">${escapeHtmlForLeave(position)} - ${escapeHtmlForLeave(emp.email || 'No email')}</div>
            </div>
        `;
    }).join('');
}

function selectLeaveEmployee(id, name, email, position, department) {
    document.getElementById('newRequestEmployee').value = id;
    document.getElementById('newRequestEmployeeSearch').value = name;
    
    window.selectedLeaveEmployeeName = name;
    window.selectedLeaveEmployeeEmail = email;
    window.selectedLeaveEmployeePosition = position;
    window.selectedLeaveEmployeeDepartment = department;
    
    const resultsDiv = document.getElementById('newRequestEmployeeResults');
    if (resultsDiv) {
        resultsDiv.style.display = 'none';
    }
}

function hideLeaveEmployeeDropdown() {
    setTimeout(() => {
        const resultsDiv = document.getElementById('newRequestEmployeeResults');
        if (resultsDiv) {
            resultsDiv.style.display = 'none';
        }
        
        const selectedId = document.getElementById('newRequestEmployee').value;
        const searchInput = document.getElementById('newRequestEmployeeSearch');
        if (!selectedId) {
            searchInput.value = '';
        } else if (window.selectedLeaveEmployeeName) {
            searchInput.value = window.selectedLeaveEmployeeName;
        }
    }, 200);
}

function escapeHtmlForLeave(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML.replace(/'/g, "\\'").replace(/"/g, '\\"');
}

function updateNewBalanceDisplay() {
    const select = document.getElementById('newRequestLeaveType');
    const selectedOption = select.options[select.selectedIndex];
    const credits = selectedOption?.dataset.credits;
    const display = document.getElementById('newBalanceDisplay');
    const value = document.getElementById('newBalanceValue');
    if (credits) { 
        value.textContent = credits + ' days'; 
        display.style.display = 'flex'; 
    } else { 
        display.style.display = 'none'; 
    }
}

function calculateNewDuration() {
    const start = document.getElementById('newStartDate').value;
    const end = document.getElementById('newEndDate').value;
    if (start && end) {
        const diffDays = Math.ceil((new Date(end) - new Date(start)) / (1000 * 60 * 60 * 24)) + 1;
        document.getElementById('newDuration').value = diffDays + ' day' + (diffDays > 1 ? 's' : '');
    }
}

async function saveNewLeaveRequest(event) {
    event.preventDefault();
    const employeeId = document.getElementById('newRequestEmployee').value;
    const leaveTypeSelect = document.getElementById('newRequestLeaveType');
    const leaveTypeId = leaveTypeSelect.value;
    const startDate = document.getElementById('newStartDate').value;
    const endDate = document.getElementById('newEndDate').value;
    const durationStr = document.getElementById('newDuration').value;
    const duration = parseInt(durationStr);
    const reason = document.getElementById('newRequestReason').value.trim();
    
    if (!employeeId || !leaveTypeId || !startDate || !endDate || !reason) {
        showToast('Please fill all required fields.', 'warning');
        return;
    }
    
    try {
        const response = await fetch('../../api/leave/requests.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                employeeId,
                leaveTypeId,
                startDate,
                endDate,
                duration,
                reason
            })
        });
        const result = await response.json();
        if (result.success) {
            closeModal();
            showToast('Leave request submitted successfully!', 'success');
            if (typeof loadLeaveDataFromDB === 'function') {
                await loadLeaveDataFromDB();
            }
        } else {
            showToast(result.message || 'Failed to submit leave request.', 'warning');
        }
    } catch (error) {
        console.error('Error submitting leave request:', error);
        showToast('Failed to connect to database API.', 'warning');
    }
}

// Make functions globally available
window.openAddLeaveRequestModal = openAddLeaveRequestModal;
window.loadLeaveEmployeesFromDB = loadLeaveEmployeesFromDB;
window.showLeaveEmployeeDropdown = showLeaveEmployeeDropdown;
window.filterLeaveEmployees = filterLeaveEmployees;
window.selectLeaveEmployee = selectLeaveEmployee;
window.hideLeaveEmployeeDropdown = hideLeaveEmployeeDropdown;
window.saveNewLeaveRequest = saveNewLeaveRequest;
</script>