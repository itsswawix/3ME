<!-- modal-edit-employee-new.php -->
<script>
function editEmployee(employeeId) {
    console.log('✏️ editEmployee called with ID:', employeeId, 'Type:', typeof employeeId);
    
    if (!window.employees || window.employees.length === 0) {
        showToast('Employee data is still loading. Please wait...', 'warning');
        return;
    }
    
    if (!window.employeeCompanies || !window.employeeDepartments || !window.employeeJobs) {
        showToast('Required data is still loading. Please wait...', 'warning');
        return;
    }
    
    // Convert to string for comparison to handle all cases
    const searchId = String(employeeId);
    
    // Find employee with flexible ID matching
    let employee = window.employees.find(emp => String(emp.id) === searchId);
    
    if (!employee) {
        const numId = parseInt(employeeId);
        if (!isNaN(numId)) {
            employee = window.employees.find(emp => parseInt(emp.id) === numId);
        }
    }
    
    if (!employee) {
        console.error('❌ Employee not found with ID:', employeeId);
        showToast('Employee not found', 'warning');
        return;
    }
    
    console.log('✅ Employee found:', employee.name);

    const isIncomplete = employee.isIncomplete || false;

    const content = `
        <style>
            .modal-edit-employee * { margin: 0; box-sizing: border-box; }
            .modal-edit-employee { font-family: 'Inter', sans-serif; max-width: 700px; width: 100%; }
            
            .profile-picture-section {
                display: flex;
                align-items: center;
                gap: 20px;
                padding: 20px;
                background: #f8fafc;
                border-radius: 16px;
                margin-bottom: 20px;
                border: 2px dashed #e2e8f0;
            }
            
            .profile-picture-preview {
                position: relative;
            }
            
            .profile-avatar-placeholder {
                width: 120px;
                height: 120px;
                border-radius: 20px;
                background: linear-gradient(145deg, #cbd5e1, #94a3b8);
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 48px;
            }
            
            .profile-picture-actions {
                display: flex;
                flex-direction: column;
                gap: 10px;
                flex: 1;
            }
            
            .btn-profile-action {
                padding: 10px 18px;
                border-radius: 20px;
                border: 1px solid #e2e8f0;
                background: white;
                color: #475569;
                font-weight: 500;
                font-size: 13px;
                cursor: pointer;
                transition: all 0.2s;
                display: flex;
                align-items: center;
                gap: 8px;
                justify-content: center;
            }
            
            .btn-profile-action:hover {
                background: #f8fafc;
                border-color: #4f46e5;
                color: #4f46e5;
                transform: translateY(-1px);
            }
            
            .btn-profile-action i {
                font-size: 14px;
            }
            
            .btn-profile-action.btn-remove {
                border-color: #fee2e2;
                color: #ef4444;
            }
            
            .btn-profile-action.btn-remove:hover {
                background: #fef2f2;
                border-color: #ef4444;
            }
            
            .section-title-sm {
                grid-column: span 2;
                font-size: 1rem;
                font-weight: 600;
                color: #0f172a;
                margin: 20px 0 8px 0;
                padding-bottom: 8px;
                border-bottom: 1.5px solid #e2e8f0;
                display: flex;
                align-items: center;
                gap: 8px;
            }
            .section-title-sm:first-of-type {
                margin-top: 0;
            }
            .section-title-sm i {
                color: #4f46e5;
                font-size: 14px;
            }
            .employee-preview {
                display: flex;
                align-items: center;
                gap: 12px;
                margin-bottom: 20px;
                padding: 12px;
                background: #f8fafc;
                border-radius: 16px;
            }
            .employee-avatar-small {
                width: 40px;
                height: 40px;
                border-radius: 12px;
                background: ${employee.color};
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-weight: 600;
                font-size: 1rem;
            }
            .warning-banner {
                background: #fef3c7;
                color: #b45309;
                padding: 10px 14px;
                border-radius: 12px;
                font-size: 12px;
                font-weight: 500;
                display: flex;
                align-items: center;
                gap: 8px;
                margin-bottom: 16px;
                border: 1px solid #fbbf24;
            }
            .form-grid { 
                display: grid; 
                grid-template-columns: 1fr 1fr; 
                gap: 16px 24px; 
            }
            .full-width { 
                grid-column: span 2; 
            }
            .form-group { 
                display: flex; 
                flex-direction: column; 
                margin-bottom: 4px; 
            }
            .form-group label { 
                font-size: 0.85rem; 
                font-weight: 500; 
                margin-bottom: 6px; 
                color: #475569; 
            }
            .form-group input, .form-group select, .form-group textarea { 
                padding: 10px 14px; 
                border: 1px solid #e2e8f0; 
                border-radius: 16px; 
                font-size: 0.9rem; 
                background: #ffffff; 
                font-family: inherit;
            }
            .form-group input:focus, .form-group select:focus, .form-group textarea:focus { 
                outline: none; 
                border-color: #4f46e5; 
                box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); 
            }
            .required-star { 
                color: #ef4444; 
            }
            textarea { 
                resize: vertical; 
                min-height: 80px; 
            }

        </style>
        <div class="modal-edit-employee">
            <div class="employee-preview">
                <img src="${employee.profilePhoto || '/3ME/assets/images/default-avatar.png'}" class="employee-avatar-small" style="object-fit: cover;" />
                <div>
                    <h4 style="font-weight:600; font-size: 14px; margin-bottom: 2px;">${escapeHtml(employee.name)}</h4>
                    <p style="color:#64748b; font-size:12px; margin: 0;">${escapeHtml(employee.employeeId)} • ${escapeHtml(employee.position)}</p>
                </div>
            </div>
            
            
            
            <form id="editEmployeeForm" onsubmit="event.preventDefault(); updateEmployeeData('${employee.id}');">
                <!-- Profile Picture Section -->
                <div class="profile-picture-section" style="display: flex; align-items: center; gap: 20px; padding: 20px; background: #f8fafc; border-radius: 16px; margin-bottom: 20px; border: 2px dashed #e2e8f0;">
                    <div class="profile-picture-preview" style="position: relative;">
                        ${employee.profilePhoto ? `
                            <img id="profileImagePreview" src="${employee.profilePhoto}" style="width: 120px; height: 120px; border-radius: 20px; object-fit: cover; box-shadow: 0 4px 12px rgba(0,0,0,0.1);" />
                            <div class="profile-avatar-placeholder" id="profileAvatarPreview" style="display: none; width: 120px; height: 120px; border-radius: 20px; background: linear-gradient(145deg, #cbd5e1, #94a3b8); align-items: center; justify-content: center; color: white; font-size: 48px;">
                                <i class="fas fa-user"></i>
                            </div>
                        ` : `
                            <div class="profile-avatar-placeholder" id="profileAvatarPreview" style="width: 120px; height: 120px; border-radius: 20px; background: linear-gradient(145deg, #cbd5e1, #94a3b8); display: flex; align-items: center; justify-content: center; color: white; font-size: 48px;">
                                <i class="fas fa-user"></i>
                            </div>
                            <img id="profileImagePreview" style="display: none; width: 120px; height: 120px; border-radius: 20px; object-fit: cover; box-shadow: 0 4px 12px rgba(0,0,0,0.1);" />
                        `}
                    </div>
                    <div class="profile-picture-actions" style="display: flex; flex-direction: column; gap: 10px; flex: 1;">
                        <button type="button" class="btn-profile-action" onclick="openWebcamCapture()" style="padding: 10px 18px; border-radius: 20px; border: 1px solid #e2e8f0; background: white; color: #475569; font-weight: 500; font-size: 13px; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 8px; justify-content: center;">
                            <i class="fas fa-camera"></i> Take Photo
                        </button>
                        <button type="button" class="btn-profile-action" onclick="document.getElementById('profilePhotoUpload').click()" style="padding: 10px 18px; border-radius: 20px; border: 1px solid #e2e8f0; background: white; color: #475569; font-weight: 500; font-size: 13px; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 8px; justify-content: center;">
                            <i class="fas fa-upload"></i> Upload Photo
                        </button>
                        <button type="button" class="btn-profile-action btn-remove" onclick="removeProfilePhoto()" id="removePhotoBtn" style="display: ${employee.profilePhoto ? 'flex' : 'none'}; padding: 10px 18px; border-radius: 20px; border: 1px solid #fee2e2; background: white; color: #ef4444; font-weight: 500; font-size: 13px; cursor: pointer; transition: all 0.2s; align-items: center; gap: 8px; justify-content: center;">
                            <i class="fas fa-trash"></i> Remove
                        </button>
                        <input type="file" id="profilePhotoUpload" accept="image/*" style="display: none;" onchange="handlePhotoUpload(event)">
                    </div>
                </div>
                
                <div class="form-grid">
                    <div class="section-title-sm">
                        <i class="fas fa-user"></i> Personal Information
                    </div>
                    
                    <div class="form-group">
                        <label>First Name <span class="required-star">*</span></label>
                        <input type="text" id="editEmpFirstname" value="${escapeHtml(employee.firstname)}" required placeholder="First name">
                    </div>
                    <div class="form-group">
                        <label>Middle Name</label>
                        <input type="text" id="editEmpMiddlename" value="${escapeHtml(employee.middlename || '')}" placeholder="Middle name">
                    </div>

                    <div class="form-group">
                        <label>Last Name <span class="required-star">*</span></label>
                        <input type="text" id="editEmpSurname" value="${escapeHtml(employee.surname)}" required placeholder="Last name">
                    </div>
                    <div class="form-group">
                        <label>Suffix</label>
                        <input type="text" id="editEmpSuffix" value="${escapeHtml(employee.suffix || '')}" placeholder="Jr., Sr., III, etc.">
                    </div>

                    <div class="form-group">
                        <label>Email <span class="required-star">*</span></label>
                        <input type="email" id="editEmpEmail" value="${escapeHtml(employee.email)}" required placeholder="employee@company.com">
                    </div>
                    <div class="form-group">
                        <label>Phone ${isIncomplete ? '<span class="required-star">*</span>' : ''}</label>
                        <div style="display: flex; align-items: center; border: 1px solid #e2e8f0; border-radius: 16px; background: #ffffff; overflow: hidden;">
                            <span style="padding: 10px 0 10px 14px; color: #64748b; font-size: 0.9rem; font-weight: 500;">+63</span>
                            <input type="tel" id="editEmpPhone" value="${escapeHtml(employee.phone || '').replace('+63', '').trim()}" placeholder="XXX XXX XXXX" maxlength="12" style="border: none; padding: 10px 14px 10px 8px; flex: 1; font-size: 0.9rem;" ${isIncomplete ? 'required' : ''} oninput="formatPhoneNumber(this)">
                        </div>
                    </div>

                    <div class="form-group full-width">
                        <label>Address ${isIncomplete ? '<span class="required-star">*</span>' : ''}</label>
                        <textarea id="editEmpAddress" placeholder="Complete address" ${isIncomplete ? 'required' : ''}>${escapeHtml(employee.address || '')}</textarea>
                    </div>
                    
                    <div class="section-title-sm">
                        <i class="fas fa-briefcase"></i> Employment Details
                    </div>
                    
                    <div class="form-group">
                        <label>Company <span class="required-star">*</span></label>
                        <select id="editEmpCompany" required onchange="loadEditDepartments()">
                            <option value="">Select Company</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Department <span class="required-star">*</span></label>
                        <select id="editEmpDepartment" required onchange="loadEditJobs()">
                            <option value="">Select Company First</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Position <span class="required-star">*</span></label>
                        <select id="editEmpPosition" required>
                            <option value="">Select Department First</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select id="editEmpStatus">
                            <option value="Active" ${employee.status === 'Active' ? 'selected' : ''}>Active</option>
                            <option value="Probation" ${employee.status === 'Probation' ? 'selected' : ''}>Probation</option>
                            <option value="On Leave" ${employee.status === 'On Leave' ? 'selected' : ''}>On Leave</option>
                            <option value="Remote" ${employee.status === 'Remote' ? 'selected' : ''}>Remote</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Join Date <span class="required-star">*</span></label>
                        <input type="date" id="editEmpJoinDate" value="${formatDateForInput(employee.joinDate)}" required>
                    </div>
                    <div class="form-group">
                        <label>Salary</label>
                        <input type="number" id="editEmpSalary" value="${employee.salary || ''}" placeholder="50000" step="0.01">
                    </div>
                    
                    <div class="section-title-sm">
                        <i class="fas fa-phone"></i> Emergency Contact
                    </div>
                    
                    <div class="form-group">
                        <label>Emergency Contact Name ${isIncomplete ? '<span class="required-star">*</span>' : ''}</label>
                        <input type="text" id="editEmpEmergencyName" value="${escapeHtml(employee.emergencyContactName || '')}" placeholder="Contact person name" ${isIncomplete ? 'required' : ''}>
                    </div>
                    <div class="form-group">
                        <label>Emergency Contact Phone ${isIncomplete ? '<span class="required-star">*</span>' : ''}</label>
                        <div style="display: flex; align-items: center; border: 1px solid #e2e8f0; border-radius: 16px; background: #ffffff; overflow: hidden;">
                            <span style="padding: 10px 0 10px 14px; color: #64748b; font-size: 0.9rem; font-weight: 500;">+63</span>
                            <input type="tel" id="editEmpEmergencyPhone" value="${escapeHtml(employee.emergencyContactPhone || '').replace('+63', '').trim()}" placeholder="XXX XXX XXXX" maxlength="12" style="border: none; padding: 10px 14px 10px 8px; flex: 1; font-size: 0.9rem;" ${isIncomplete ? 'required' : ''} oninput="formatPhoneNumber(this)">
                        </div>
                    </div>

                    <div class="form-group full-width">
                        <label>Relationship</label>
                        <input type="text" id="editEmpEmergencyRelation" value="${escapeHtml(employee.emergencyContactRelation || '')}" placeholder="e.g., Spouse, Parent, Sibling">
                    </div>
                    
                    <div class="section-title-sm">
                        <i class="fas fa-id-card"></i> Government IDs
                    </div>

                    <div class="form-group">
                        <label>SSS Number ${isIncomplete ? '<span class="required-star">*</span>' : ''}</label>
                        <input type="text" id="editEmpSSS" value="${escapeHtml(employee.sss || '')}" placeholder="XX-XXXXXXX-X" maxlength="12" ${isIncomplete ? 'required' : ''} oninput="formatSSS(this)">
                    </div>
                    <div class="form-group">
                        <label>PhilHealth Number ${isIncomplete ? '<span class="required-star">*</span>' : ''}</label>
                        <input type="text" id="editEmpPhilHealth" value="${escapeHtml(employee.philhealth || '')}" placeholder="XX-XXXXXXXXX-X" maxlength="14" ${isIncomplete ? 'required' : ''} oninput="formatPhilHealth(this)">
                    </div>

                    <div class="form-group">
                        <label>Pag-IBIG Number ${isIncomplete ? '<span class="required-star">*</span>' : ''}</label>
                        <input type="text" id="editEmpPagibig" value="${escapeHtml(employee.pagibig || '')}" placeholder="XXXX-XXXX-XXXX" maxlength="14" ${isIncomplete ? 'required' : ''} oninput="formatPagibig(this)">
                    </div>
                    <div class="form-group">
                        <label>TIN ${isIncomplete ? '<span class="required-star">*</span>' : ''}</label>
                        <input type="text" id="editEmpTIN" value="${escapeHtml(employee.tin || '')}" placeholder="XXX-XXX-XXX-XXX" maxlength="15" ${isIncomplete ? 'required' : ''} oninput="formatTIN(this)">
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> ${isIncomplete ? 'Complete Profile' : 'Save Changes'}
                    </button>
                </div>
            </form>
        </div>
    `;

    openModal(isIncomplete ? 'Complete Employee Profile' : 'Edit Employee', content);
    
    // Store current profile photo for editing
    window.currentProfilePhoto = employee.profilePhoto || null;
    
    // Setup dropdowns with current values
    setTimeout(() => setupEditDropdowns(employee), 100);
}

function setupEditDropdowns(employee) {
    const companySelect = document.getElementById('editEmpCompany');
    if (companySelect && window.employeeCompanies) {
        companySelect.innerHTML = '<option value="">Select Company</option>';
        window.employeeCompanies.forEach(company => {
            const option = document.createElement('option');
            option.value = company.id;
            option.textContent = company.name;
            if (employee.company_id && company.id == employee.company_id) {
                option.selected = true;
            }
            companySelect.appendChild(option);
        });
        
        if (employee.company_id) {
            setTimeout(() => loadEditDepartments(employee), 100);
        }
    }
}

function loadEditDepartments(employee = null) {
    const companySelect = document.getElementById('editEmpCompany');
    const departmentSelect = document.getElementById('editEmpDepartment');
    const positionSelect = document.getElementById('editEmpPosition');
    
    const companyId = companySelect.value;
    
    if (positionSelect) {
        positionSelect.innerHTML = '<option value="">Select Department First</option>';
    }
    
    if (!companyId) {
        departmentSelect.innerHTML = '<option value="">Select Company First</option>';
        return;
    }
    
    if (window.employeeDepartments) {
        const companyDepts = window.employeeDepartments.filter(d => d.companyId == companyId);
        departmentSelect.innerHTML = '<option value="">Select Department</option>';
        companyDepts.forEach(dept => {
            const option = document.createElement('option');
            option.value = dept.id;
            option.textContent = dept.name;
            if (employee && employee.department_id && dept.id == employee.department_id) {
                option.selected = true;
            }
            departmentSelect.appendChild(option);
        });
        
        if (employee && employee.department_id) {
            setTimeout(() => loadEditJobs(employee), 100);
        }
    }
}

function loadEditJobs(employee = null) {
    const departmentSelect = document.getElementById('editEmpDepartment');
    const positionSelect = document.getElementById('editEmpPosition');
    
    const departmentId = departmentSelect.value;
    if (!departmentId) {
        positionSelect.innerHTML = '<option value="">Select Department First</option>';
        return;
    }
    
    if (window.employeeJobs) {
        const deptJobs = window.employeeJobs.filter(j => j.department_id == departmentId);
        positionSelect.innerHTML = '<option value="">Select Position</option>';
        deptJobs.forEach(job => {
            const option = document.createElement('option');
            option.value = job.id;
            option.textContent = job.title || job.jobTitle;
            option.setAttribute('data-title', job.title || job.jobTitle);
            if (employee && employee.job_id && job.id == employee.job_id) {
                option.selected = true;
            }
            positionSelect.appendChild(option);
        });
    }
}

function updateEmployeeData(employeeId) {
    const employee = window.employees.find(emp => emp.id == employeeId);
    if (!employee) {
        showToast('Employee not found', 'warning');
        return;
    }
    
    // Get form data
    const jobId = document.getElementById('editEmpPosition').value;
    const departmentId = document.getElementById('editEmpDepartment').value;
    const companyId = document.getElementById('editEmpCompany').value;
    
    // Get display names
    const jobSelect = document.getElementById('editEmpPosition');
    const departmentSelect = document.getElementById('editEmpDepartment');
    const companySelect = document.getElementById('editEmpCompany');
    
    const position = jobSelect.options[jobSelect.selectedIndex]?.getAttribute('data-title') || jobSelect.options[jobSelect.selectedIndex]?.text || '';
    const department = departmentSelect.options[departmentSelect.selectedIndex]?.text || '';
    const company = companySelect.options[companySelect.selectedIndex]?.text || '';
    
    // Get phone numbers and add +63 prefix
    const phoneValue = document.getElementById('editEmpPhone').value.replace(/\s/g, '');
    const emergencyPhoneValue = document.getElementById('editEmpEmergencyPhone').value.replace(/\s/g, '');
    
    // Update employee object
    employee.firstname = document.getElementById('editEmpFirstname').value.trim();
    employee.middlename = document.getElementById('editEmpMiddlename').value.trim();
    employee.surname = document.getElementById('editEmpSurname').value.trim();
    employee.suffix = document.getElementById('editEmpSuffix').value.trim();
    employee.name = `${employee.firstname} ${employee.middlename ? employee.middlename + ' ' : ''}${employee.surname}${employee.suffix ? ' ' + employee.suffix : ''}`;
    employee.email = document.getElementById('editEmpEmail').value.trim();
    employee.phone = phoneValue ? `+63${phoneValue}` : '';
    employee.address = document.getElementById('editEmpAddress').value.trim();
    
    employee.job_id = jobId;
    employee.department_id = departmentId;
    employee.company_id = companyId;
    employee.position = position;
    employee.job = position;
    employee.department = department;
    employee.company = company;
    employee.status = document.getElementById('editEmpStatus').value;
    employee.joinDate = formatDateForDisplay(document.getElementById('editEmpJoinDate').value);
    employee.salary = parseFloat(document.getElementById('editEmpSalary').value) || 0;
    
    employee.emergencyContactName = document.getElementById('editEmpEmergencyName').value.trim();
    employee.emergencyContactPhone = emergencyPhoneValue ? `+63${emergencyPhoneValue}` : '';
    employee.emergencyContactRelation = document.getElementById('editEmpEmergencyRelation').value.trim();
    
    employee.sss = document.getElementById('editEmpSSS').value.trim();
    employee.philhealth = document.getElementById('editEmpPhilHealth').value.trim();
    employee.pagibig = document.getElementById('editEmpPagibig').value.trim();
    employee.tin = document.getElementById('editEmpTIN').value.trim();
    
    // Handle profile photo update
    if (window.currentProfilePhoto !== undefined && window.currentProfilePhoto !== employee.profilePhoto) {
        // If there's a new photo to upload
        if (window.currentProfilePhoto && window.currentProfilePhoto.startsWith('data:image')) {
            showToast('Uploading profile photo...', 'info');
            
            uploadProfilePhotoToServer(window.currentProfilePhoto, employee.employeeId)
                .then(photoData => {
                    // Delete old photo if exists
                    if (employee.profilePhotoFilename) {
                        deleteProfilePhotoFromServer(null, employee.profilePhotoFilename).catch(err => {
                            console.error('Failed to delete old photo:', err);
                        });
                    }
                    
                    employee.profilePhoto = photoData.url;
                    employee.profilePhotoFilename = photoData.filename;
                    
                    finishEmployeeUpdate(employee);
                })
                .catch(error => {
                    console.error('Photo upload failed:', error);
                    showToast('Employee updated, but photo upload failed', 'warning');
                    finishEmployeeUpdate(employee);
                });
            
            return; // Wait for async upload
        }
        // If photo was removed
        else if (window.currentProfilePhoto === null && employee.profilePhoto) {
            if (employee.profilePhotoFilename) {
                deleteProfilePhotoFromServer(null, employee.profilePhotoFilename).catch(err => {
                    console.error('Failed to delete photo:', err);
                });
            }
            employee.profilePhoto = null;
            employee.profilePhotoFilename = null;
        }
    }
    
    finishEmployeeUpdate(employee);
}

async function finishEmployeeUpdate(employee) {
    // Check if profile is now complete
    employee.isIncomplete = isEmployeeProfileIncomplete(employee);
    
    // Update avatar if name changed
    const avatarData = generateEmployeeAvatar(employee.name);
    employee.avatar = avatarData.avatar;
    
    // Clear temporary profile photo
    window.currentProfilePhoto = null;
    
    // ---- Persist to database via API ----
    try {
        const apiPayload = {
            id: employee.id,
            firstname: employee.firstname,
            middlename: employee.middlename,
            surname: employee.surname,
            suffix: employee.suffix,
            email: employee.email,
            phone: employee.phone,
            job: employee.position || employee.job,
            job_id: employee.job_id,
            department: employee.department,
            department_id: employee.department_id,
            company: employee.company,
            company_id: employee.company_id,
            status: employee.status,
            join_date: document.getElementById('editEmpJoinDate').value, // Send raw date value
            salary: employee.salary,
            address: employee.address,
            emergency_contact_name: employee.emergencyContactName,
            emergency_contact_phone: employee.emergencyContactPhone,
            emergency_contact_relation: employee.emergencyContactRelation,
            sss_number: employee.sss,
            philhealth_number: employee.philhealth,
            pagibig_number: employee.pagibig,
            tin_number: employee.tin,
            profile_photo: employee.profilePhoto || null,
            profile_photo_filename: employee.profilePhotoFilename || null
        };

        const response = await fetch('../../api/employees/employees.php', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(apiPayload)
        });
        const result = await response.json();

        if (!result.success) {
            console.error('Failed to save employee to DB:', result.message);
            showToast('Warning: Changes may not be saved to database', 'warning');
        } else {
            console.log('✅ Employee updated in DB:', employee.id);
        }
    } catch (err) {
        console.error('Error saving employee to DB:', err);
    }

    // Reload employee list from server to stay in sync
    if (typeof loadEmployeeData === 'function') {
        await loadEmployeeData();
    }

    closeModal();
    
    if (employee.isIncomplete) {
        showToast('Employee updated! Some fields are still incomplete.', 'warning');
    } else {
        showToast('Employee profile updated successfully! ✅', 'success');
    }
    
    // Refresh the current view
    if (typeof renderCompanyLevel === 'function' && currentLevel === 'company') {
        renderCompanyLevel();
    } else if (typeof renderDepartmentLevel === 'function' && currentLevel === 'department') {
        renderDepartmentLevel();
    } else if (typeof renderEmployeeLevel === 'function' && currentLevel === 'employee') {
        renderEmployeeLevel();
    }
}

function formatDateForInput(dateStr) {
    if (!dateStr) return '';
    try {
        const date = new Date(dateStr);
        return date.toISOString().split('T')[0];
    } catch (e) {
        return '';
    }
}

function formatDateForDisplay(dateStr) {
    if (!dateStr) return '';
    try {
        const date = new Date(dateStr);
        return date.toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' });
    } catch (e) {
        return dateStr;
    }
}

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>]/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;' })[m] || m);
}

// Input formatting functions
function formatPhoneNumber(input) {
    // Remove all non-digit characters
    let value = input.value.replace(/\D/g, '');
    
    // If the user types "09", automatically remove the leading "0" (since +63 is prefixed)
    if (value.startsWith('09')) {
        value = value.substring(1);
    }
    
    // Limit to 10 digits
    if (value.length > 10) {
        value = value.substring(0, 10);
    }
    
    // Format: XXX XXX XXXX
    if (value.length > 6) {
        value = value.substring(0, 3) + ' ' + value.substring(3, 6) + ' ' + value.substring(6);
    } else if (value.length > 3) {
        value = value.substring(0, 3) + ' ' + value.substring(3);
    }
    
    input.value = value;
}

function formatSSS(input) {
    // Remove all non-digit characters
    let value = input.value.replace(/\D/g, '');
    
    // Limit to 10 digits (2-7-1)
    if (value.length > 10) {
        value = value.substring(0, 10);
    }
    
    // Format: XX-XXXXXXX-X
    if (value.length > 9) {
        value = value.substring(0, 2) + '-' + value.substring(2, 9) + '-' + value.substring(9);
    } else if (value.length > 2) {
        value = value.substring(0, 2) + '-' + value.substring(2);
    }
    
    input.value = value;
}

function formatPhilHealth(input) {
    // Remove all non-digit characters
    let value = input.value.replace(/\D/g, '');
    
    // Limit to 12 digits (2-9-1)
    if (value.length > 12) {
        value = value.substring(0, 12);
    }
    
    // Format: XX-XXXXXXXXX-X
    if (value.length > 11) {
        value = value.substring(0, 2) + '-' + value.substring(2, 11) + '-' + value.substring(11);
    } else if (value.length > 2) {
        value = value.substring(0, 2) + '-' + value.substring(2);
    }
    
    input.value = value;
}

function formatPagibig(input) {
    // Remove all non-digit characters
    let value = input.value.replace(/\D/g, '');
    
    // Limit to 12 digits (4-4-4)
    if (value.length > 12) {
        value = value.substring(0, 12);
    }
    
    // Format: XXXX-XXXX-XXXX
    if (value.length > 8) {
        value = value.substring(0, 4) + '-' + value.substring(4, 8) + '-' + value.substring(8);
    } else if (value.length > 4) {
        value = value.substring(0, 4) + '-' + value.substring(4);
    }
    
    input.value = value;
}

function formatTIN(input) {
    // Remove all non-digit characters
    let value = input.value.replace(/\D/g, '');
    
    // Limit to 12 digits (3-3-3-3)
    if (value.length > 12) {
        value = value.substring(0, 12);
    }
    
    // Format: XXX-XXX-XXX-XXX
    if (value.length > 9) {
        value = value.substring(0, 3) + '-' + value.substring(3, 6) + '-' + value.substring(6, 9) + '-' + value.substring(9);
    } else if (value.length > 6) {
        value = value.substring(0, 3) + '-' + value.substring(3, 6) + '-' + value.substring(6);
    } else if (value.length > 3) {
        value = value.substring(0, 3) + '-' + value.substring(3);
    }
    
    input.value = value;
}

// Make functions globally available
window.editEmployee = editEmployee;
window.updateEmployeeData = updateEmployeeData;
window.finishEmployeeUpdate = finishEmployeeUpdate;
window.loadEditDepartments = loadEditDepartments;
window.loadEditJobs = loadEditJobs;
window.setupEditDropdowns = setupEditDropdowns;
window.formatDateForInput = formatDateForInput;
window.formatDateForDisplay = formatDateForDisplay;
window.formatPhoneNumber = formatPhoneNumber;
window.formatSSS = formatSSS;
window.formatPhilHealth = formatPhilHealth;
window.formatPagibig = formatPagibig;
window.formatTIN = formatTIN;
</script>
