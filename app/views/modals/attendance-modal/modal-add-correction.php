<!-- modal-add-correction.php -->
<script>
async function openAddCorrectionModal() {
    window.selectedEmployeeName = null;
    
    const content = `
        <style>
            .search-select-container {
                position: relative;
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
        <form id="addCorrectionForm" onsubmit="handleAddCorrection(event)">
            <div class="form-group" style="position: relative;">
                <label>Employee <span class="required-star">*</span></label>
                <div class="search-select-container">
                    <input type="text" id="employeeSearch" placeholder="Click to select or type to search employee..." autocomplete="off" onfocus="showEmployeeDropdown()" oninput="filterEmployees(this.value)" onblur="hideEmployeeDropdown()" required>
                    <input type="hidden" id="selectedEmployeeId" name="employeeId">
                    <span style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); pointer-events: none; color: #94a3b8;">
                        <i class="fas fa-chevron-down"></i>
                    </span>
                </div>
                <div id="employeeSearchResults" class="employee-search-results" style="display: none;"></div>
            </div>
            
            <div class="form-group">
                <label>Correction Type <span class="required-star">*</span></label>
                <select id="correctionType" name="correctionType" required>
                    <option value="">Select Type</option>
                    <option value="Late">Late Arrival</option>
                    <option value="Early Departure">Early Departure</option>
                    <option value="Missed Entry">Missed Clock In/Out</option>
                    <option value="Overtime Discrepancy">Overtime Discrepancy</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Original Date <span class="required-star">*</span></label>
                <input type="date" id="originalDate" name="originalDate" required>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Corrected Time In</label>
                    <input type="time" id="timeIn" name="timeIn">
                </div>
                <div class="form-group">
                    <label>Corrected Time Out</label>
                    <input type="time" id="timeOut" name="timeOut">
                </div>
            </div>
            
            <div class="form-group">
                <label>Reason for Correction <span class="required-star">*</span></label>
                <textarea id="reason" name="reason" placeholder="Explain why this correction is needed..." rows="4" required></textarea>
            </div>
            
            <div class="form-group">
                <label>Supporting Documents</label>
                <input type="file" id="documents" name="documents" multiple accept=".pdf,.jpg,.jpeg,.png">
                <small style="color: #64748b; font-size: 11px; display: block; margin-top: 4px;">
                    <i class="fas fa-info-circle"></i> Optional: Upload medical certificates, leave forms, etc.
                </small>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="submit" class="btn btn-warning">
                    <i class="fas fa-paper-plane"></i> Submit Request
                </button>
            </div>
        </form>
    `;
    
    openModal('Request Attendance Correction', content);
}

// Cache for employees data
let employeesCache = null;

async function loadEmployeesFromDB() {
    if (employeesCache) return employeesCache;
    
    try {
        const response = await fetch('/3ME/api/employees/employees.php');
        const result = await response.json();
        if (result.success) {
            employeesCache = result.data || [];
            return employeesCache;
        }
    } catch (error) {
        console.error('Error loading employees:', error);
    }
    return [];
}

async function showEmployeeDropdown() {
    const resultsDiv = document.getElementById('employeeSearchResults');
    const employees = await loadEmployeesFromDB();
    
    // Sort employees alphabetically
    const sortedEmployees = [...employees].sort((a, b) => {
        const nameA = `${a.surname || ''}, ${a.firstname || ''}`.toLowerCase();
        const nameB = `${b.surname || ''}, ${b.firstname || ''}`.toLowerCase();
        return nameA.localeCompare(nameB);
    });

    renderDropdownItems(sortedEmployees);
    resultsDiv.style.display = 'block';
}

async function filterEmployees(query) {
    const resultsDiv = document.getElementById('employeeSearchResults');
    const employees = await loadEmployeesFromDB();
    
    // Filter employees based on search query
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

    renderDropdownItems(sortedFiltered);
    resultsDiv.style.display = 'block';
    
    // Reset selected employee if query is manually edited
    if (window.selectedEmployeeName && query !== window.selectedEmployeeName) {
        document.getElementById('selectedEmployeeId').value = '';
        window.selectedEmployeeName = null;
    }
}

function renderDropdownItems(employeesList) {
    const resultsDiv = document.getElementById('employeeSearchResults');
    
    if (employeesList.length === 0) {
        resultsDiv.innerHTML = '<div style="padding: 12px; text-align: center; color: #64748b; font-size: 12px;">No employees found</div>';
        return;
    }
    
    resultsDiv.innerHTML = employeesList.map(emp => {
        const fullName = `${emp.firstname} ${emp.middlename ? emp.middlename + ' ' : ''}${emp.surname}`;
        return `
            <div class="employee-search-item" onmousedown="selectEmployeeForCorrection('${emp.id}', '${escapeHtml(fullName)}')">
                <div style="font-weight: 500; color: #1e293b; font-size: 13px;">${escapeHtml(fullName)}</div>
                <div style="font-size: 11px; color: #64748b;">${escapeHtml(emp.email || 'No email')}</div>
            </div>
        `;
    }).join('');
}

function selectEmployeeForCorrection(id, name) {
    document.getElementById('selectedEmployeeId').value = id;
    document.getElementById('employeeSearch').value = name;
    
    window.selectedEmployeeName = name;
    
    const resultsDiv = document.getElementById('employeeSearchResults');
    if (resultsDiv) {
        resultsDiv.style.display = 'none';
    }
}

function hideEmployeeDropdown() {
    // Hide dropdown after a short delay to allow onmousedown selection to trigger first
    setTimeout(() => {
        const resultsDiv = document.getElementById('employeeSearchResults');
        if (resultsDiv) {
            resultsDiv.style.display = 'none';
        }
        
        const selectedId = document.getElementById('selectedEmployeeId').value;
        const searchInput = document.getElementById('employeeSearch');
        if (!selectedId) {
            searchInput.value = '';
        } else if (window.selectedEmployeeName) {
            searchInput.value = window.selectedEmployeeName;
        }
    }, 200);
}

function handleAddCorrection(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const errors = [];
    
    // Validation
    if (!formData.get('employeeId')) {
        errors.push('Please select a valid employee from the dropdown list');
    }
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
        employeeId: formData.get('employeeId'),
        type: formData.get('correctionType'),
        originalDate: formData.get('originalDate'),
        timeIn: formData.get('timeIn') || null,
        timeOut: formData.get('timeOut') || null,
        reason: formData.get('reason'),
        status: 'Pending',
        requestedBy: 'Current User'
    };
    
    // Send to API
    fetch('/3ME/api/attendance/corrections.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(correctionData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload corrections from API
            loadAttendanceData();
            closeModal(true);
            showToast('Correction request submitted successfully!', 'success');
        } else {
            showToast(data.message || 'Error creating correction', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error creating correction', 'error');
    });
}

function generateEmployeeAvatar(fullName) {
    const parts = fullName.split(' ').filter(p => p);
    let avatar;
    
    if (parts.length >= 2) {
        avatar = (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
    } else {
        avatar = parts[0] ? parts[0].substring(0, 2).toUpperCase() : 'NA';
    }
    
    const colors = [
        'linear-gradient(145deg, #4f46e5, #7c3aed)',
        'linear-gradient(145deg, #ef4444, #f87171)',
        'linear-gradient(145deg, #10b981, #34d399)',
        'linear-gradient(145deg, #f59e0b, #fbbf24)',
        'linear-gradient(145deg, #8b5cf6, #a78bfa)',
        'linear-gradient(145deg, #06b6d4, #67e8f9)',
        'linear-gradient(145deg, #ec4899, #f472b6)',
        'linear-gradient(145deg, #14b8a6, #5eead4)'
    ];
    
    const colorIndex = fullName.length % colors.length;
    const color = colors[colorIndex];
    
    return { avatar, color };
}

// Make functions globally available
window.openAddCorrectionModal = openAddCorrectionModal;
window.loadEmployeesFromDB = loadEmployeesFromDB;
window.showEmployeeDropdown = showEmployeeDropdown;
window.filterEmployees = filterEmployees;
window.selectEmployeeForCorrection = selectEmployeeForCorrection;
window.hideEmployeeDropdown = hideEmployeeDropdown;
window.handleAddCorrection = handleAddCorrection;
</script>
