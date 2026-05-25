<!-- modal-add-offense.php -->
<script>
function openAddOffenseModal() {
    window.selectedOffenseEmployeeName = null;

    const content = `
        <style>
            .modal-add-offense * { margin: 0; box-sizing: border-box; }
            .modal-add-offense { font-family: 'Inter', sans-serif; max-width: 600px; width: 100%; }
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
            textarea { resize: vertical; min-height: 100px; }
            .modal-buttons { display: flex; justify-content: flex-end; gap: 12px; margin-top: 28px; padding-top: 20px; border-top: 1px solid #f1f5f9; }
            .btn-save { background: #ef4444; color: white; border: none; padding: 10px 22px; border-radius: 24px; font-size: 0.85rem; font-weight: 500; cursor: pointer; display: flex; align-items: center; gap: 6px; box-shadow: 0 4px 8px rgba(239, 68, 68, 0.2); }
            .btn-save:hover { background: #dc2626; transform: translateY(-1px); }
            .btn-cancel { background: white; color: #475569; border: 1px solid #e2e8f0; padding: 10px 22px; border-radius: 24px; cursor: pointer; display: flex; align-items: center; gap: 6px; }
            .severity-indicator { display: flex; gap: 8px; margin-top: 8px; }
            .severity-option { flex: 1; padding: 8px; border: 1px solid #e2e8f0; border-radius: 12px; text-align: center; cursor: pointer; transition: all 0.2s; }
            .severity-option.active { border-color: #ef4444; background: #fef2f2; }
            .severity-option.minor { border-color: #10b981; }
            .severity-option.minor.active { background: #ecfdf5; }
            .severity-option.moderate { border-color: #4f46e5; }
            .severity-option.moderate.active { background: #eef2ff; }
            .severity-option.major { border-color: #f59e0b; }
            .severity-option.major.active { background: #fffbeb; }
            .severity-option.critical { border-color: #ef4444; }
            .severity-option.critical.active { background: #fef2f2; }
            #severityDescription { margin-top: 8px; padding: 8px 12px; background: #f8fafc; border-radius: 8px; font-weight: 500; }
            .modal-footer-note { font-size: 0.75rem; color: #94a3b8; margin-top: 16px; text-align: right; }
            
            /* Search select styles just like modal-add-correction.php */
            .search-select-container {
                position: relative;
            }
            .search-select-container input[type="text"] {
                width: 100%;
                padding-right: 40px;
            }
            .employee-search-results {
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                z-index: 1000;
                background: white;
                max-height: 200px;
                overflow-y: auto;
                border: 1px solid #e2e8f0;
                border-radius: 12px;
                margin-top: 4px;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            }
            .employee-search-item {
                padding: 10px 12px;
                cursor: pointer;
                border-bottom: 1px solid #f1f5f9;
                transition: background 0.2s;
            }
            .employee-search-item:hover {
                background: #f8fafc;
            }
            .employee-search-item:last-child {
                border-bottom: none;
            }
        </style>
        <div class="modal-add-offense">
            <form id="addOffenseForm" onsubmit="saveNewOffense(event)">
                <div class="form-grid">
                    <div class="section-title"><i class="fas fa-user"></i> Employee Information</div>
                    
                    <div class="form-group full-width" style="position: relative;">
                        <label>Employee <span class="required-star">*</span></label>
                        <div class="search-select-container">
                            <input type="text" id="newOffenseEmployeeSearch" placeholder="Click to select or type to search employee..." autocomplete="off" onfocus="showNewOffenseEmployeeDropdown()" oninput="filterNewOffenseEmployees(this.value)" onblur="hideNewOffenseEmployeeDropdown()" required>
                            <input type="hidden" id="newOffenseEmployee" name="employeeId">
                            <span style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); pointer-events: none; color: #94a3b8;">
                                <i class="fas fa-chevron-down"></i>
                            </span>
                        </div>
                        <div id="newOffenseEmployeeSearchResults" class="employee-search-results" style="display: none;"></div>
                    </div>
                    <input type="hidden" id="newOffenseDepartment" value="">
                    
                    <div class="section-title"><i class="fas fa-gavel"></i> Offense Details</div>
                    <div class="form-group">
                        <label>Offense Type <span class="required-star">*</span></label>
                        <select id="newOffenseType" required>
                            <option value="">Select Type</option>
                            <option value="Attendance">Attendance</option>
                            <option value="Misconduct">Misconduct</option>
                            <option value="Policy Violation">Policy Violation</option>
                            <option value="Safety">Safety</option>
                            <option value="Harassment">Harassment</option>
                            <option value="Performance">Performance</option>
                            <option value="Theft">Theft</option>
                            <option value="Insubordination">Insubordination</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Offense Date <span class="required-star">*</span></label>
                        <input type="date" id="newOffenseDate" required>
                    </div>
                    <div class="form-group full-width">
                        <label>Severity Level <span class="required-star">*</span></label>
                        <input type="hidden" id="newOffenseSeverity" value="Moderate">
                        <div class="severity-indicator">
                            <div class="severity-option minor" onclick="setNewOffenseSeverity('Minor')" data-severity="Minor">
                                <i class="fas fa-circle" style="color: #10b981;"></i><br>Minor
                            </div>
                            <div class="severity-option moderate active" onclick="setNewOffenseSeverity('Moderate')" data-severity="Moderate">
                                <i class="fas fa-circle" style="color: #4f46e5;"></i><br>Moderate
                            </div>
                            <div class="severity-option major" onclick="setNewOffenseSeverity('Major')" data-severity="Major">
                                <i class="fas fa-circle" style="color: #f59e0b;"></i><br>Major
                            </div>
                            <div class="severity-option critical" onclick="setNewOffenseSeverity('Critical')" data-severity="Critical">
                                <i class="fas fa-circle" style="color: #ef4444;"></i><br>Critical
                            </div>
                        </div>
                        <div id="severityDescription">Moderate - Requires formal documentation and follow-up</div>
                    </div>
                    <div class="form-group">
                        <label>Status <span class="required-star">*</span></label>
                        <select id="newOffenseStatus" required>
                            <option value="Pending Review">Pending Review</option>
                            <option value="Under Investigation">Under Investigation</option>
                            <option value="Action Taken">Action Taken</option>
                            <option value="Closed">Closed</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Reported By <span class="required-star">*</span></label>
                        <input type="text" id="newReportedBy" required value="Current User" placeholder="Reporter name">
                    </div>
                    <div class="form-group full-width">
                        <label>Offense Description <span class="required-star">*</span></label>
                        <textarea id="newOffenseDescription" required placeholder="Provide detailed description of the offense, including circumstances, witnesses, and any immediate actions taken..."></textarea>
                    </div>
                    <div class="form-group full-width">
                        <label>Action Taken (if any)</label>
                        <textarea id="newActionTaken" placeholder="Describe any actions taken or recommended..."></textarea>
                    </div>
                </div>
                <div class="modal-footer-note"><span class="required-star">*</span> Required fields</div>
                <div class="modal-buttons">
                    <button type="button" class="btn-cancel" onclick="closeAddOffenseModal()"><i class="fas fa-times"></i> Cancel</button>
                    <button type="submit" class="btn-save"><i class="fas fa-save"></i> Record Offense</button>
                </div>
            </form>
        </div>
    `;
    openModal('Record New Offense', content);
    document.getElementById('newOffenseDate').value = new Date().toISOString().split('T')[0];
}

// Cache for employees data in offense modal
let newOffenseEmployeesCache = null;

async function loadNewOffenseEmployeesFromDB() {
    if (newOffenseEmployeesCache) return newOffenseEmployeesCache;
    
    try {
        const response = await fetch('/3ME/api/employees/employees.php');
        const result = await response.json();
        if (result.success) {
            newOffenseEmployeesCache = result.data || [];
            return newOffenseEmployeesCache;
        }
    } catch (error) {
        console.error('Error loading employees for offense modal:', error);
    }
    // Fallback to window.employees if fetch fails
    return window.employees || [];
}

async function showNewOffenseEmployeeDropdown() {
    const resultsDiv = document.getElementById('newOffenseEmployeeSearchResults');
    const employees = await loadNewOffenseEmployeesFromDB();
    
    // Sort employees alphabetically
    const sortedEmployees = [...employees].sort((a, b) => {
        const nameA = `${a.surname || ''}, ${a.firstname || ''}`.toLowerCase();
        const nameB = `${b.surname || ''}, ${b.firstname || ''}`.toLowerCase();
        return nameA.localeCompare(nameB);
    });

    renderNewOffenseDropdownItems(sortedEmployees);
    resultsDiv.style.display = 'block';
}

async function filterNewOffenseEmployees(query) {
    const resultsDiv = document.getElementById('newOffenseEmployeeSearchResults');
    const employees = await loadNewOffenseEmployeesFromDB();
    
    // Filter employees based on search query
    const filtered = employees.filter(emp => {
        const fullName = `${emp.firstname || ''} ${emp.middlename || ''} ${emp.surname || ''}`.toLowerCase();
        const email = (emp.email || '').toLowerCase();
        const dept = (emp.department || '').toLowerCase();
        const job = (emp.job || emp.position || '').toLowerCase();
        const searchTerm = query.toLowerCase();
        
        return fullName.includes(searchTerm) || email.includes(searchTerm) || dept.includes(searchTerm) || job.includes(searchTerm);
    });
    
    const sortedFiltered = filtered.sort((a, b) => {
        const nameA = `${a.surname || ''}, ${a.firstname || ''}`.toLowerCase();
        const nameB = `${b.surname || ''}, ${b.firstname || ''}`.toLowerCase();
        return nameA.localeCompare(nameB);
    });

    renderNewOffenseDropdownItems(sortedFiltered);
    resultsDiv.style.display = 'block';
    
    // Reset selected employee if query is manually edited
    if (window.selectedOffenseEmployeeName && query !== window.selectedOffenseEmployeeName) {
        document.getElementById('newOffenseEmployee').value = '';
        document.getElementById('newOffenseDepartment').value = '';
        window.selectedOffenseEmployeeName = null;
    }
}

function renderNewOffenseDropdownItems(employeesList) {
    const resultsDiv = document.getElementById('newOffenseEmployeeSearchResults');
    
    if (employeesList.length === 0) {
        resultsDiv.innerHTML = '<div style="padding: 12px; text-align: center; color: #64748b; font-size: 12px;">No employees found</div>';
        return;
    }
    
    resultsDiv.innerHTML = employeesList.map(emp => {
        const fullName = `${emp.firstname} ${emp.middlename ? emp.middlename + ' ' : ''}${emp.surname}`;
        const jobTitle = emp.job || emp.position || '';
        const dept = emp.department || 'General';
        return `
            <div class="employee-search-item" onmousedown="selectEmployeeForNewOffense('${emp.employee_id || emp.id}', '${escapeHtml(fullName)}', '${escapeHtml(dept)}')">
                <div style="font-weight: 500; color: #1e293b; font-size: 13px;">${escapeHtml(fullName)}</div>
                <div style="font-size: 11px; color: #64748b;">${escapeHtml(jobTitle)} &bull; ${escapeHtml(dept)}</div>
            </div>
        `;
    }).join('');
}

function selectEmployeeForNewOffense(id, name, department) {
    document.getElementById('newOffenseEmployee').value = id;
    document.getElementById('newOffenseEmployeeSearch').value = name;
    document.getElementById('newOffenseDepartment').value = department;
    
    window.selectedOffenseEmployeeName = name;
    
    const resultsDiv = document.getElementById('newOffenseEmployeeSearchResults');
    if (resultsDiv) {
        resultsDiv.style.display = 'none';
    }
}

function hideNewOffenseEmployeeDropdown() {
    // Hide dropdown after a short delay to allow onmousedown selection to trigger first
    setTimeout(() => {
        const resultsDiv = document.getElementById('newOffenseEmployeeSearchResults');
        if (resultsDiv) {
            resultsDiv.style.display = 'none';
        }
        
        const selectedId = document.getElementById('newOffenseEmployee').value;
        const searchInput = document.getElementById('newOffenseEmployeeSearch');
        if (!selectedId) {
            searchInput.value = '';
            document.getElementById('newOffenseDepartment').value = '';
        } else if (window.selectedOffenseEmployeeName) {
            searchInput.value = window.selectedOffenseEmployeeName;
        }
    }, 200);
}

// Fallback empty function to prevent script runtime errors
function populateEmployeeDropdown() {}

function closeAddOffenseModal() { 
    if (typeof attemptCloseModal === 'function') {
        attemptCloseModal();
    } else if (typeof closeModal === 'function') {
        closeModal();
    }
}

function setNewOffenseSeverity(severity) {
    document.getElementById('newOffenseSeverity').value = severity;
    
    // Update UI
    document.querySelectorAll('.severity-option').forEach(opt => {
        opt.classList.remove('active');
        if (opt.dataset.severity === severity) opt.classList.add('active');
    });
    
    const descriptions = {
        'Minor': 'Minor - Verbal warning, informal documentation',
        'Moderate': 'Moderate - Requires formal documentation and follow-up',
        'Major': 'Major - Written warning, possible suspension',
        'Critical': 'Critical - Immediate action required, possible termination'
    };
    const colors = { 'Minor': '#10b981', 'Moderate': '#4f46e5', 'Major': '#f59e0b', 'Critical': '#ef4444' };
    
    const descEl = document.getElementById('severityDescription');
    descEl.textContent = descriptions[severity];
    descEl.style.color = colors[severity];
    descEl.style.background = `${colors[severity]}10`;
}

function saveNewOffense(event) {
    event.preventDefault();
    const employeeSelect = document.getElementById('newOffenseEmployee');
    if (!employeeSelect.value) { showToast('Please select an employee.', 'warning'); return; }
    
    const severity = document.getElementById('newOffenseSeverity').value;
    
    const payload = {
        employee_id: employeeSelect.value,
        offense_type: document.getElementById('newOffenseType').value,
        severity: severity,
        date: document.getElementById('newOffenseDate').value,
        status: document.getElementById('newOffenseStatus').value,
        reported_by: document.getElementById('newReportedBy').value,
        description: document.getElementById('newOffenseDescription').value,
        action_taken: document.getElementById('newActionTaken').value
    };
    
    fetch('/3ME/api/performance/offenses.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast(`Offense recorded successfully!`, 'success');
            if (typeof loadOffenses === 'function') loadOffenses();
            if (typeof markModalAsSaved === 'function') markModalAsSaved();
            closeModal(true);
        } else {
            showToast(data.message || 'Error recording offense', 'error');
        }
    })
    .catch(err => {
        console.error(err);
        showToast('System error recording offense', 'error');
    });
}

// Make functions globally available
window.openAddOffenseModal = openAddOffenseModal;
window.showNewOffenseEmployeeDropdown = showNewOffenseEmployeeDropdown;
window.filterNewOffenseEmployees = filterNewOffenseEmployees;
window.selectEmployeeForNewOffense = selectEmployeeForNewOffense;
window.hideNewOffenseEmployeeDropdown = hideNewOffenseEmployeeDropdown;
window.setNewOffenseSeverity = setNewOffenseSeverity;
window.saveNewOffense = saveNewOffense;
window.loadNewOffenseEmployeesFromDB = loadNewOffenseEmployeesFromDB;
</script>
