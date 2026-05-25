<!-- modal-add-onboard.php -->
<script>
// Load jobs, departments, and companies data on page load
document.addEventListener('DOMContentLoaded', function() {
    if (!window.onboardingJobsLoaded) {
        fetchOnboardingJobsData();
        fetchOnboardingDepartmentsData();
        fetchOnboardingCompaniesData();
        window.onboardingJobsLoaded = true;
    }
});

function fetchOnboardingJobsData() {
    fetch('../../api/settings/settings_api.php?action=list_jobs')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.onboardingJobs = (data.data || []).map(job => ({
                    id: job.id,
                    title: job.jobTitle,
                    level: job.level,
                    department_id: job.departmentId,
                    status: job.status
                }));
                console.log('✅ Onboarding jobs loaded:', window.onboardingJobs.length);
            } else {
                console.error('❌ Failed to load jobs:', data.message);
                window.onboardingJobs = [];
            }
        })
        .catch(error => {
            console.error('❌ Error fetching jobs:', error);
            window.onboardingJobs = [];
        });
}

function fetchOnboardingDepartmentsData() {
    fetch('../../api/settings/settings_api.php?action=list_departments')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.onboardingDepartments = data.data || [];
                console.log('✅ Onboarding departments loaded:', window.onboardingDepartments.length);
            } else {
                console.error('❌ Failed to load departments:', data.message);
                window.onboardingDepartments = [];
            }
        })
        .catch(error => {
            console.error('❌ Error fetching departments:', error);
            window.onboardingDepartments = [];
        });
}

function fetchOnboardingCompaniesData() {
    fetch('../../api/settings/settings_api.php?action=list_companies')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.onboardingCompanies = data.data || [];
                console.log('✅ Onboarding companies loaded:', window.onboardingCompanies.length);
            } else {
                console.error('❌ Failed to load companies:', data.message);
                window.onboardingCompanies = [];
            }
        })
        .catch(error => {
            console.error('❌ Error fetching companies:', error);
            window.onboardingCompanies = [];
        });
}

function openAddOnboardModal() {
    const content = `
        <style>
            .modal-add-onboard * { margin: 0; box-sizing: border-box; }
            .modal-add-onboard { font-family: 'Inter', sans-serif; max-width: 600px; width: 100%; }
            .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px 24px; }
            .full-width { grid-column: span 2; }
            .form-group { display: flex; flex-direction: column; margin-bottom: 4px; }
            .form-group label { font-size: 0.85rem; font-weight: 500; margin-bottom: 6px; color: #475569; letter-spacing: 0.3px; }
            .form-group input, .form-group select, .form-group textarea { padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 16px; font-size: 0.9rem; background: #ffffff; font-family: 'Inter', sans-serif; transition: all 0.2s ease; }
            .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); }
            .section-title { grid-column: span 2; font-size: 1rem; font-weight: 600; margin: 20px 0 8px; padding-bottom: 8px; border-bottom: 1.5px solid #e2e8f0; color: #0f172a; display: flex; align-items: center; gap: 8px; }
            .section-title i { color: #4f46e5; font-size: 0.9rem; width: 20px; }
            .section-title:first-of-type { margin-top: 0; }
            .required-star { color: #ef4444; margin-left: 2px; }
            textarea { resize: vertical; min-height: 80px; }
            .modal-footer-note { font-size: 0.75rem; color: #94a3b8; margin-top: 16px; text-align: right; }
            .modal-buttons { display: flex; justify-content: flex-end; gap: 12px; margin-top: 28px; padding-top: 20px; border-top: 1px solid #f1f5f9; }
            .btn-save { background: #4f46e5; color: white; border: none; padding: 10px 22px; border-radius: 24px; font-size: 0.85rem; font-weight: 500; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 6px; box-shadow: 0 4px 8px rgba(79, 70, 229, 0.2); }
            .btn-save:hover { background: #4338ca; transform: translateY(-1px); box-shadow: 0 6px 12px rgba(79, 70, 229, 0.25); }
            .btn-cancel { background: white; color: #475569; border: 1px solid #e2e8f0; padding: 10px 22px; border-radius: 24px; font-size: 0.85rem; font-weight: 500; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 6px; }
            .btn-cancel:hover { background: #f8fafc; border-color: #cbd5e1; }
            .employee-preview { display: flex; align-items: center; gap: 12px; margin-bottom: 20px; padding: 12px; background: #f8fafc; border-radius: 16px; }
            .employee-avatar-small { width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; }
        </style>
        <div class="modal-add-onboard">
            <form id="addOnboardForm" onsubmit="event.preventDefault(); saveNewOnboard();">
                <div class="form-grid">
                    <div class="section-title"><i class="fas fa-user"></i> Employee Information</div>
                    <div class="form-group"><label>Employee ID <span class="required-star">*</span></label><input type="text" id="newEmployeeId" required placeholder="EMP-2024-XXX"></div>
                    <div class="form-group"><label>First Name <span class="required-star">*</span></label><input type="text" id="newEmployeeFirstName" required placeholder="First name"></div>
                    <div class="form-group"><label>Middle Name</label><input type="text" id="newEmployeeMiddleName" placeholder="Middle name"></div>
                    <div class="form-group"><label>Last Name <span class="required-star">*</span></label><input type="text" id="newEmployeeLastName" required placeholder="Last name"></div>
                    <div class="form-group"><label>Suffix</label><input type="text" id="newEmployeeSuffix" placeholder="Jr., Sr., III, etc."></div>
                    <div class="form-group full-width"><label>Email <span class="required-star">*</span></label><input type="email" id="newEmployeeEmail" required placeholder="employee@company.com"></div>
                    
                    <div class="section-title"><i class="fas fa-briefcase"></i> Position Details</div>
                    <div class="form-group full-width">
                        <label>Job Position <span class="required-star">*</span></label>
                        <select id="newJobId" required onchange="loadOnboardDepartmentsByJob(this.value)">
                            <option value="">Select Position</option>
                        </select>
                    </div>
                    <div class="form-group"><label>Department <span class="required-star">*</span></label>
                        <select id="newDepartmentId" required onchange="loadOnboardCompaniesByDepartment()">
                            <option value="">Select Position First</option>
                        </select>
                    </div>
                    <div class="form-group"><label>Company <span class="required-star">*</span></label>
                        <select id="newCompanyId" required>
                            <option value="">Select Department First</option>
                        </select>
                    </div>
                    
                    <div class="section-title"><i class="fas fa-calendar"></i> Onboarding Schedule</div>
                    <div class="form-group"><label>Start Date <span class="required-star">*</span></label><input type="date" id="newStartDate" required></div>
                    <div class="form-group">
                        <label>Progress Status</label>
                        <select id="newProgress">
                            <option value="Not Started">Not Started</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Completed">Completed</option>
                        </select>
                    </div>
                    
                    <div class="section-title"><i class="fas fa-sticky-note"></i> Additional Information</div>
                    <div class="form-group full-width"><label>Notes</label><textarea id="newNotes" placeholder="Additional notes or special instructions..."></textarea></div>
                </div>
                <div class="modal-footer-note"><span class="required-star">*</span> Required fields</div>
                
                <div class="modal-buttons">
                    <button type="button" class="btn-cancel" onclick="closeAddOnboardModal()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i> Create Onboarding
                    </button>
                </div>
            </form>
        </div>
    `;
    
    openModal('Create Onboarding Record', content);
    
    // Set today's date as default
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('newStartDate').value = today;
    
    // Populate job dropdown
    populateOnboardJobDropdown();
}

function populateOnboardJobDropdown() {
    const jobSelect = document.getElementById('newJobId');
    if (!jobSelect) return;
    
    if (window.onboardingJobs && window.onboardingJobs.length > 0) {
        jobSelect.innerHTML = '<option value="">Select Position</option>';
        window.onboardingJobs.forEach(job => {
            const option = document.createElement('option');
            option.value = job.id;
            option.textContent = job.title + (job.level ? ` (${job.level})` : '');
            option.setAttribute('data-department-id', job.department_id || '');
            option.setAttribute('data-title', job.title);
            jobSelect.appendChild(option);
        });
        console.log('✅ Job dropdown populated with', window.onboardingJobs.length, 'jobs');
    } else {
        jobSelect.innerHTML = '<option value="">No jobs available - Create in Settings</option>';
        console.warn('⚠️ No jobs available');
    }
}

function loadOnboardDepartmentsByJob(jobId) {
    const departmentSelect = document.getElementById('newDepartmentId');
    const companySelect = document.getElementById('newCompanyId');
    
    // Reset dependent fields
    companySelect.innerHTML = '<option value="">Select Department First</option>';
    
    if (!jobId) {
        departmentSelect.innerHTML = '<option value="">Select Position First</option>';
        return;
    }
    
    // Get the selected job's department
    const jobSelect = document.getElementById('newJobId');
    const selectedOption = jobSelect.options[jobSelect.selectedIndex];
    const departmentId = selectedOption.getAttribute('data-department-id');
    
    if (departmentId && window.onboardingDepartments) {
        const department = window.onboardingDepartments.find(d => d.id === departmentId);
        if (department) {
            departmentSelect.innerHTML = `<option value="${department.id}" selected>${department.name}</option>`;
            console.log('✅ Department auto-populated:', department.name);
            
            // Auto-load companies
            loadOnboardCompaniesByDepartment();
        } else {
            departmentSelect.innerHTML = '<option value="">Department not found</option>';
        }
    } else {
        departmentSelect.innerHTML = '<option value="">No department for this job</option>';
    }
}

function loadOnboardCompaniesByDepartment() {
    const departmentSelect = document.getElementById('newDepartmentId');
    const companySelect = document.getElementById('newCompanyId');
    
    const departmentId = departmentSelect.value;
    if (!departmentId) {
        companySelect.innerHTML = '<option value="">Select Department First</option>';
        return;
    }
    
    if (window.onboardingDepartments && window.onboardingCompanies) {
        const department = window.onboardingDepartments.find(d => d.id === departmentId);
        if (department && department.companyId) {
            const company = window.onboardingCompanies.find(c => c.id === department.companyId);
            if (company) {
                companySelect.innerHTML = `<option value="${company.id}" selected>${company.name}</option>`;
                console.log('✅ Company auto-populated:', company.name);
            } else {
                companySelect.innerHTML = '<option value="">Company not found</option>';
            }
        } else {
            companySelect.innerHTML = '<option value="">No company for this department</option>';
        }
    }
}

// Open modal with pre-filled employee data (from accepted offers)
function openAddOnboardModalWithEmployee(employeeData) {
    const content = `
        <style>
            .modal-add-onboard * { margin: 0; box-sizing: border-box; }
            .modal-add-onboard { font-family: 'Inter', sans-serif; max-width: 600px; width: 100%; }
            .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px 24px; }
            .full-width { grid-column: span 2; }
            .form-group { display: flex; flex-direction: column; margin-bottom: 4px; }
            .form-group label { font-size: 0.85rem; font-weight: 500; margin-bottom: 6px; color: #475569; letter-spacing: 0.3px; }
            .form-group input, .form-group select, .form-group textarea { padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 16px; font-size: 0.9rem; background: #ffffff; font-family: 'Inter', sans-serif; transition: all 0.2s ease; }
            .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); }
            .section-title { grid-column: span 2; font-size: 1rem; font-weight: 600; margin: 20px 0 8px; padding-bottom: 8px; border-bottom: 1.5px solid #e2e8f0; color: #0f172a; display: flex; align-items: center; gap: 8px; }
            .section-title i { color: #4f46e5; font-size: 0.9rem; width: 20px; }
            .section-title:first-of-type { margin-top: 0; }
            .required-star { color: #ef4444; margin-left: 2px; }
            textarea { resize: vertical; min-height: 80px; }
            .modal-footer-note { font-size: 0.75rem; color: #94a3b8; margin-top: 16px; text-align: right; }
            .modal-buttons { display: flex; justify-content: flex-end; gap: 12px; margin-top: 28px; padding-top: 20px; border-top: 1px solid #f1f5f9; }
            .btn-save { background: #4f46e5; color: white; border: none; padding: 10px 22px; border-radius: 24px; font-size: 0.85rem; font-weight: 500; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 6px; box-shadow: 0 4px 8px rgba(79, 70, 229, 0.2); }
            .btn-save:hover { background: #4338ca; transform: translateY(-1px); box-shadow: 0 6px 12px rgba(79, 70, 229, 0.25); }
            .btn-cancel { background: white; color: #475569; border: 1px solid #e2e8f0; padding: 10px 22px; border-radius: 24px; font-size: 0.85rem; font-weight: 500; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 6px; }
            .btn-cancel:hover { background: #f8fafc; border-color: #cbd5e1; }
            .employee-preview { display: flex; align-items: center; gap: 12px; margin-bottom: 20px; padding: 12px; background: #f8fafc; border-radius: 16px; }
            .employee-avatar-small { width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; }
        </style>
        <div class="modal-add-onboard">
            <div class="employee-preview">
                <div class="employee-avatar-small" style="background: ${employeeData.color};">${employeeData.avatar}</div>
                <div>
                    <h4 style="font-weight:600;">${escapeHtml(employeeData.name)}</h4>
                    <p style="color:#64748b; font-size:0.75rem;">From Accepted Job Offer • ${escapeHtml(employeeData.position)}</p>
                </div>
            </div>
            
            <form id="addOnboardForm" onsubmit="event.preventDefault(); saveNewOnboard();">
                <input type="hidden" id="prefilledJobId" value="${employeeData.job_id || ''}">
                <input type="hidden" id="prefilledDepartmentId" value="${employeeData.department_id || ''}">
                <input type="hidden" id="prefilledCompanyId" value="${employeeData.company_id || ''}">
                
                <div class="form-grid">
                    <div class="section-title"><i class="fas fa-user"></i> Employee Information</div>
                    <div class="form-group"><label>Employee ID <span class="required-star">*</span></label><input type="text" id="newEmployeeId" required value="${escapeHtml(employeeData.employeeId)}" readonly style="background: #f8fafc;"></div>
                    <div class="form-group"><label>Employee Name <span class="required-star">*</span></label><input type="text" id="newEmployeeName" required value="${escapeHtml(employeeData.name)}"></div>
                    <div class="form-group full-width"><label>Email <span class="required-star">*</span></label><input type="email" id="newEmployeeEmail" required value="${escapeHtml(employeeData.email)}"></div>
                    
                    <div class="section-title"><i class="fas fa-briefcase"></i> Position Details</div>
                    <div class="form-group full-width">
                        <label>Job Position <span class="required-star">*</span></label>
                        <select id="newJobId" required onchange="loadOnboardDepartmentsByJob(this.value)">
                            <option value="">Select Position</option>
                        </select>
                    </div>
                    <div class="form-group"><label>Department <span class="required-star">*</span></label>
                        <select id="newDepartmentId" required onchange="loadOnboardCompaniesByDepartment()">
                            <option value="">Select Position First</option>
                        </select>
                    </div>
                    <div class="form-group"><label>Company <span class="required-star">*</span></label>
                        <select id="newCompanyId" required>
                            <option value="">Select Department First</option>
                        </select>
                    </div>
                    
                    <div class="section-title"><i class="fas fa-calendar"></i> Onboarding Schedule</div>
                    <div class="form-group"><label>Start Date <span class="required-star">*</span></label><input type="date" id="newStartDate" required></div>
                    <div class="form-group">
                        <label>Progress Status</label>
                        <select id="newProgress">
                            <option value="Not Started" selected>Not Started</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Completed">Completed</option>
                        </select>
                    </div>
                    
                    <div class="section-title"><i class="fas fa-sticky-note"></i> Additional Information</div>
                    <div class="form-group full-width"><label>Notes</label><textarea id="newNotes" placeholder="Additional notes or special instructions...">New hire from recruitment process - ${escapeHtml(employeeData.name)} accepted job offer.</textarea></div>
                </div>
                <div class="modal-footer-note"><span class="required-star">*</span> Required fields</div>
                
                <div class="modal-buttons">
                    <button type="button" class="btn-cancel" onclick="closeAddOnboardModal()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i> Create Onboarding Checklist
                    </button>
                </div>
            </form>
        </div>
    `;
    
    openModal('Start Employee Onboarding', content);
    
    // Set today's date as default
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('newStartDate').value = today;
    
    // Populate dropdowns and pre-select values
    populateOnboardJobDropdown();
    setTimeout(() => {
        if (employeeData.job_id) {
            document.getElementById('newJobId').value = employeeData.job_id;
            loadOnboardDepartmentsByJob(employeeData.job_id);
            setTimeout(() => {
                if (employeeData.department_id) {
                    document.getElementById('newDepartmentId').value = employeeData.department_id;
                    loadOnboardCompaniesByDepartment();
                    setTimeout(() => {
                        if (employeeData.company_id) {
                            document.getElementById('newCompanyId').value = employeeData.company_id;
                        }
                    }, 100);
                }
            }, 100);
        }
    }, 100);
}

function closeAddOnboardModal() {
    if (typeof closeModal === 'function') {
        closeModal();
    }
}

function saveNewOnboard() {
    const employeeId = document.getElementById('newEmployeeId')?.value.trim();
    const employeeName = document.getElementById('newEmployeeName')?.value.trim();
    const employeeEmail = document.getElementById('newEmployeeEmail')?.value.trim();
    const jobId = document.getElementById('newJobId')?.value;
    const departmentId = document.getElementById('newDepartmentId')?.value;
    const companyId = document.getElementById('newCompanyId')?.value;
    
    if (!employeeId || !employeeName || !employeeEmail) {
        showToast('Please fill all required fields.', 'warning');
        return;
    }
    
    if (!jobId || !departmentId || !companyId) {
        showToast('Please select job position, department, and company.', 'warning');
        return;
    }
    
    // Check for duplicate email
    if (window.onboardRecords && window.onboardRecords.some(r => r.employeeEmail.toLowerCase() === employeeEmail.toLowerCase())) {
        showToast('An onboarding record with this email already exists.', 'warning');
        return;
    }
    
    // Get display names for UI
    const jobSelect = document.getElementById('newJobId');
    const departmentSelect = document.getElementById('newDepartmentId');
    const companySelect = document.getElementById('newCompanyId');
    
    const position = jobSelect.options[jobSelect.selectedIndex]?.getAttribute('data-title') || jobSelect.options[jobSelect.selectedIndex]?.text || '';
    const department = departmentSelect.options[departmentSelect.selectedIndex]?.text || '';
    const company = companySelect.options[companySelect.selectedIndex]?.text || '';
    
    const onboardData = {
        employee_id: employeeId,
        employee_name: employeeName,
        employee_email: employeeEmail,
        job_id: jobId,
        department_id: departmentId,
        company_id: companyId,
        position: position,
        department: department,
        company: company,
        start_date: document.getElementById('newStartDate')?.value || new Date().toISOString().split('T')[0],
        progress: document.getElementById('newProgress')?.value || 'Not Started',
        notes: document.getElementById('newNotes')?.value || ''
    };
    
    console.log('📤 Sending onboarding data with IDs:');
    console.log('  Job ID:', onboardData.job_id, '| Position:', onboardData.position);
    console.log('  Department ID:', onboardData.department_id, '| Name:', onboardData.department);
    console.log('  Company ID:', onboardData.company_id, '| Name:', onboardData.company);
    
    // Send to API
    fetch('../../api/onboarding/records.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(onboardData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(`Onboarding record created for ${employeeName}!`, 'success');
            
            // Create employee record with incomplete flag
            const employeeData = {
                firstname: employeeName.split(' ')[0] || '',
                middlename: '',
                surname: employeeName.split(' ').slice(1).join(' ') || '',
                suffix: '',
                email: onboardData.employee_email,
                phone: '',
                employeeId: onboardData.employee_id,
                job_id: onboardData.job_id,
                department_id: onboardData.department_id,
                company_id: onboardData.company_id,
                position: onboardData.position,
                department: onboardData.department,
                company: onboardData.company,
                status: 'Probation',
                joinDate: onboardData.start_date,
                salary: 0,
                address: '',
                emergencyContactName: '',
                emergencyContactPhone: '',
                emergencyContactRelation: '',
                level: '',
                type: 'Probationary',
                startDate: onboardData.start_date,
                endDate: '',
                duration: '',
                sss: '',
                philhealth: '',
                pagibig: '',
                tin: '',
                remarks: onboardData.notes,
                blocklist: false,
                isIncomplete: true // Mark as incomplete
            };
            
            // Send employee creation request
            fetch('../../api/employees/employees.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(employeeData)
            })
            .then(response => response.json())
            .then(empData => {
                if (empData.success) {
                    console.log('✅ Employee record created with incomplete flag');
                    
                    // Store navigation data
                    sessionStorage.setItem('navigateToCompany', onboardData.company);
                    sessionStorage.setItem('navigateToDepartment', onboardData.department);
                    sessionStorage.setItem('highlightEmployee', employeeName);
                    sessionStorage.setItem('forceNavigation', 'true');
                    sessionStorage.setItem('showIncompleteWarning', 'true');
                    
                    // Navigate to employee page
                    setTimeout(() => {
                        window.location.href = 'employee.php';
                    }, 1500);
                } else {
                    console.error('Failed to create employee record:', empData.message);
                    showToast('Onboarding created but employee record failed. Please add manually.', 'warning');
                }
            })
            .catch(error => {
                console.error('Error creating employee:', error);
                showToast('Onboarding created but employee record failed. Please add manually.', 'warning');
            });
            
            // Add to local array for immediate UI update
            const newRecord = {
                id: data.id,
                employeeId: onboardData.employee_id,
                employeeName: onboardData.employee_name,
                employeeEmail: onboardData.employee_email,
                position: onboardData.position,
                job_id: onboardData.job_id,
                department: onboardData.department,
                department_id: onboardData.department_id,
                company: onboardData.company,
                company_id: onboardData.company_id,
                startDate: new Date(onboardData.start_date).toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' }),
                progress: onboardData.progress,
                completionDate: null,
                tasks: [
                    { text: 'Complete employment forms', completed: false },
                    { text: 'IT equipment setup', completed: false },
                    { text: 'Office tour and introductions', completed: false },
                    { text: 'HR orientation session', completed: false },
                    { text: 'Department training', completed: false },
                    { text: 'System access setup', completed: false }
                ],
                notes: onboardData.notes,
                avatar: employeeName.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2),
                color: 'linear-gradient(145deg, #6366f1, #a78bfa)'
            };
            
            if (typeof window.onboardRecords !== 'undefined') {
                window.onboardRecords.unshift(newRecord);
                
                // Update UI if render functions exist
                if (typeof renderOnboardTable === 'function') {
                    renderOnboardTable(window.onboardRecords);
                }
                if (typeof renderAcceptedOffersTable === 'function') {
                    renderAcceptedOffersTable(window.acceptedOffers);
                }
            }
            
            if (typeof closeModal === 'function') {
                closeModal();
            }
        } else {
            showToast(data.message || 'Error creating onboarding record', 'warning');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error creating onboarding record', 'warning');
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