<?php
/**
 * Modal for adding new employees
 */
?>

<script>
// Load jobs, departments, and companies data on page load
document.addEventListener('DOMContentLoaded', function() {
    if (!window.employeeJobsLoaded) {
        fetchEmployeeJobsData();
        fetchEmployeeDepartmentsData();
        fetchEmployeeCompaniesData();
        window.employeeJobsLoaded = true;
    }
});

function fetchEmployeeJobsData() {
    fetch('../../api/settings/settings_api.php?action=list_jobs')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.employeeJobs = (data.data || []).map(job => ({
                    id: job.id,
                    title: job.jobTitle,
                    level: job.level,
                    department_id: job.departmentId,
                    status: job.status
                }));
                console.log('✅ Employee jobs loaded:', window.employeeJobs.length);
            } else {
                console.error('❌ Failed to load jobs:', data.message);
                window.employeeJobs = [];
            }
        })
        .catch(error => {
            console.error('❌ Error fetching jobs:', error);
            window.employeeJobs = [];
        });
}

function fetchEmployeeDepartmentsData() {
    fetch('../../api/settings/settings_api.php?action=list_departments')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.employeeDepartments = data.data || [];
                console.log('✅ Employee departments loaded:', window.employeeDepartments.length);
            } else {
                console.error('❌ Failed to load departments:', data.message);
                window.employeeDepartments = [];
            }
        })
        .catch(error => {
            console.error('❌ Error fetching departments:', error);
            window.employeeDepartments = [];
        });
}

function fetchEmployeeCompaniesData() {
    fetch('../../api/settings/settings_api.php?action=list_companies')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.employeeCompanies = data.data || [];
                console.log('✅ Employee companies loaded:', window.employeeCompanies.length);
            } else {
                console.error('❌ Failed to load companies:', data.message);
                window.employeeCompanies = [];
            }
        })
        .catch(error => {
            console.error('❌ Error fetching companies:', error);
            window.employeeCompanies = [];
        });
}

function openAddEmployeeModal() {
    console.log('🔍 openAddEmployeeModal called');
    console.log('  Available data:', {
        jobs: window.employeeJobs?.length || 0,
        departments: window.employeeDepartments?.length || 0,
        companies: window.employeeCompanies?.length || 0
    });
    
    const content = `
        <form id="addEmployeeForm" onsubmit="addEmployee(event)">
            
            <div class="section-title-sm">
                <i class="fas fa-user"></i> Personal Information
            </div>
            
            <!-- Profile Picture Section -->
            <div class="profile-picture-section" style="display: flex; align-items: center; gap: 20px; padding: 20px; background: #f8fafc; border-radius: 16px; margin-bottom: 20px; border: 2px dashed #e2e8f0;">
                <div class="profile-picture-preview" style="position: relative;">
                    <div class="profile-avatar-placeholder" id="profileAvatarPreview" style="width: 120px; height: 120px; border-radius: 20px; background: linear-gradient(145deg, #cbd5e1, #94a3b8); display: flex; align-items: center; justify-content: center; color: white; font-size: 48px;">
                        <i class="fas fa-user"></i>
                    </div>
                    <img id="profileImagePreview" style="display: none; width: 120px; height: 120px; border-radius: 20px; object-fit: cover; box-shadow: 0 4px 12px rgba(0,0,0,0.1);" />
                </div>
                <div class="profile-picture-actions" style="display: flex; flex-direction: column; gap: 10px; flex: 1;">
                    <button type="button" class="btn-profile-action" onclick="openWebcamCapture()" style="padding: 10px 18px; border-radius: 20px; border: 1px solid #e2e8f0; background: white; color: #475569; font-weight: 500; font-size: 13px; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 8px; justify-content: center;">
                        <i class="fas fa-camera"></i> Take Photo
                    </button>
                    <button type="button" class="btn-profile-action" onclick="document.getElementById('profilePhotoUpload').click()" style="padding: 10px 18px; border-radius: 20px; border: 1px solid #e2e8f0; background: white; color: #475569; font-weight: 500; font-size: 13px; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 8px; justify-content: center;">
                        <i class="fas fa-upload"></i> Upload Photo
                    </button>
                    <button type="button" class="btn-profile-action btn-remove" onclick="removeProfilePhoto()" id="removePhotoBtn" style="display: none; padding: 10px 18px; border-radius: 20px; border: 1px solid #fee2e2; background: white; color: #ef4444; font-weight: 500; font-size: 13px; cursor: pointer; transition: all 0.2s; align-items: center; gap: 8px; justify-content: center;">
                        <i class="fas fa-trash"></i> Remove
                    </button>
                    <input type="file" id="profilePhotoUpload" accept="image/*" style="display: none;" onchange="handlePhotoUpload(event)">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>First Name <span class="required-star">*</span></label>
                    <input type="text" id="empFirstname" required placeholder="Enter first name">
                </div>
                <div class="form-group">
                    <label>Middle Name</label>
                    <input type="text" id="empMiddlename" placeholder="Enter middle name">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Last Name <span class="required-star">*</span></label>
                    <input type="text" id="empSurname" required placeholder="Enter last name">
                </div>
                <div class="form-group">
                    <label>Suffix</label>
                    <input type="text" id="empSuffix" placeholder="Jr., Sr., III, etc.">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Email <span class="required-star">*</span></label>
                    <input type="email" id="empEmail" required placeholder="employee@company.com">
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="tel" id="empPhone" placeholder="+63 XXX XXX XXXX">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Employee ID <span class="required-star">*</span></label>
                    <input type="text" id="empEmployeeId" required placeholder="EMP-2024-XXX">
                </div>
                <div class="form-group"></div>
            </div>

            <div class="section-title-sm">
                <i class="fas fa-briefcase"></i> Employment Details
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Position <span class="required-star">*</span></label>
                    <select id="empPosition" required onchange="loadEmpDepartmentsByJob(this.value)">
                        <option value="">Select Position</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Department <span class="required-star">*</span></label>
                    <select id="empDepartment" required onchange="loadEmpCompaniesByDepartment()">
                        <option value="">Select Position First</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Company <span class="required-star">*</span></label>
                    <select id="empCompany" required>
                        <option value="">Select Department First</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select id="empStatus">
                        <option value="Active">Active</option>
                        <option value="Probation">Probation</option>
                        <option value="On Leave">On Leave</option>
                        <option value="Remote">Remote</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Join Date <span class="required-star">*</span></label>
                    <input type="date" id="empJoinDate" required>
                </div>
                <div class="form-group">
                    <label>Salary</label>
                    <input type="number" id="empSalary" placeholder="50000" step="0.01">
                </div>
            </div>

            <div class="section-title-sm">
                <i class="fas fa-home"></i> Additional Information
            </div>

            <div class="form-group">
                <label>Address</label>
                <textarea id="empAddress" placeholder="Complete address"></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Emergency Contact Name</label>
                    <input type="text" id="empEmergencyName" placeholder="Contact person name">
                </div>
                <div class="form-group">
                    <label>Emergency Contact Phone</label>
                    <input type="tel" id="empEmergencyPhone" placeholder="+63 XXX XXX XXXX">
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Employee
                </button>
            </div>
        </form>
    `;

    openModal('Add New Employee', content);
    
    // Setup dropdowns
    setupEmployeeFormDropdowns();
    
    // Set default values
    document.getElementById('empJoinDate').value = new Date().toISOString().split('T')[0];
    document.getElementById('empEmployeeId').value = generateEmployeeId();
}

function setupEmployeeFormDropdowns() {
    // Populate job dropdown from API
    const positionSelect = document.getElementById('empPosition');
    if (positionSelect && window.employeeJobs && window.employeeJobs.length > 0) {
        positionSelect.innerHTML = '<option value="">Select Position</option>';
        window.employeeJobs.forEach(job => {
            const option = document.createElement('option');
            option.value = job.id;
            option.textContent = job.title + (job.level ? ` (${job.level})` : '');
            option.setAttribute('data-department-id', job.department_id || '');
            option.setAttribute('data-title', job.title);
            positionSelect.appendChild(option);
        });
        console.log('✅ Position dropdown populated with', window.employeeJobs.length, 'jobs');
    } else if (positionSelect) {
        positionSelect.innerHTML = '<option value="">No jobs available - Create in Settings</option>';
    }
}

function loadEmpDepartmentsByJob(jobId) {
    const departmentSelect = document.getElementById('empDepartment');
    const companySelect = document.getElementById('empCompany');
    
    // Reset dependent fields
    if (companySelect) {
        companySelect.innerHTML = '<option value="">Select Department First</option>';
    }
    
    if (!jobId) {
        if (departmentSelect) {
            departmentSelect.innerHTML = '<option value="">Select Position First</option>';
        }
        return;
    }
    
    // Get the selected job's department
    const jobSelect = document.getElementById('empPosition');
    const selectedOption = jobSelect.options[jobSelect.selectedIndex];
    const departmentId = selectedOption.getAttribute('data-department-id');
    
    if (departmentId && window.employeeDepartments) {
        const department = window.employeeDepartments.find(d => d.id === departmentId);
        if (department) {
            departmentSelect.innerHTML = `<option value="${department.id}" selected>${department.name}</option>`;
            console.log('✅ Department auto-populated:', department.name);
            
            // Auto-load companies
            loadEmpCompaniesByDepartment();
        } else {
            departmentSelect.innerHTML = '<option value="">Department not found</option>';
        }
    } else {
        departmentSelect.innerHTML = '<option value="">No department for this job</option>';
    }
}

function loadEmpCompaniesByDepartment() {
    const departmentSelect = document.getElementById('empDepartment');
    const companySelect = document.getElementById('empCompany');
    
    const departmentId = departmentSelect.value;
    if (!departmentId) {
        companySelect.innerHTML = '<option value="">Select Department First</option>';
        return;
    }
    
    if (window.employeeDepartments && window.employeeCompanies) {
        const department = window.employeeDepartments.find(d => d.id === departmentId);
        if (department && department.companyId) {
            const company = window.employeeCompanies.find(c => c.id === department.companyId);
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

async function addEmployee(event) {
    event.preventDefault();
    
    // Get form data
    const jobId = document.getElementById('empPosition').value;
    const departmentId = document.getElementById('empDepartment').value;
    const companyId = document.getElementById('empCompany').value;
    
    // Get display names
    const jobSelect = document.getElementById('empPosition');
    const departmentSelect = document.getElementById('empDepartment');
    const companySelect = document.getElementById('empCompany');
    
    const position = jobSelect.options[jobSelect.selectedIndex]?.getAttribute('data-title') || jobSelect.options[jobSelect.selectedIndex]?.text || '';
    const department = departmentSelect.options[departmentSelect.selectedIndex]?.text || '';
    const company = companySelect.options[companySelect.selectedIndex]?.text || '';
    
    const formData = {
        firstname: document.getElementById('empFirstname').value.trim(),
        middlename: document.getElementById('empMiddlename').value.trim(),
        surname: document.getElementById('empSurname').value.trim(),
        suffix: document.getElementById('empSuffix').value.trim(),
        email: document.getElementById('empEmail').value.trim(),
        phone: document.getElementById('empPhone').value.trim(),
        employeeId: document.getElementById('empEmployeeId').value.trim(),
        job_id: jobId,
        department_id: departmentId,
        company_id: companyId,
        position: position,
        department: department,
        company: company,
        status: document.getElementById('empStatus').value,
        joinDate: document.getElementById('empJoinDate').value,
        salary: parseFloat(document.getElementById('empSalary').value) || 0,
        address: document.getElementById('empAddress').value.trim(),
        emergencyContactName: document.getElementById('empEmergencyName').value.trim(),
        emergencyContactPhone: document.getElementById('empEmergencyPhone').value.trim()
    };
    
    // Validate form
    const errors = validateEmployeeForm(formData);
    if (errors.length > 0) {
        showValidationErrors(errors);
        return;
    }
    
    console.log('📤 Creating employee with IDs:');
    console.log('  Job ID:', formData.job_id, '| Position:', formData.position);
    console.log('  Department ID:', formData.department_id, '| Name:', formData.department);
    console.log('  Company ID:', formData.company_id, '| Name:', formData.company);
    
    try {
        // ---- Step 1: Save employee to the database via API ----
        const apiPayload = {
            firstname: formData.firstname,
            middlename: formData.middlename,
            surname: formData.surname,
            suffix: formData.suffix,
            email: formData.email,
            phone: formData.phone,
            job: formData.position,
            job_id: formData.job_id,
            department: formData.department,
            department_id: formData.department_id,
            company: formData.company,
            company_id: formData.company_id,
            status: formData.status,
            join_date: formData.joinDate,
            salary: formData.salary,
            address: formData.address
        };

        const createResponse = await fetch('../../api/employees/employees.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(apiPayload)
        });
        const createResult = await createResponse.json();

        if (!createResult.success) {
            throw new Error(createResult.message || 'Failed to create employee');
        }

        const savedEmployeeId = createResult.employee_id || createResult.id;
        console.log('✅ Employee saved to DB with ID:', savedEmployeeId);

        // ---- Step 2: Upload profile photo if one was captured/selected ----
        let photoUrl = null;
        let photoFilename = null;

        if (window.currentProfilePhoto) {
            showToast('Uploading profile photo...', 'info');
            try {
                // upload_profile_photo.php also updates the DB row
                const photoData = await uploadProfilePhotoToServer(window.currentProfilePhoto, savedEmployeeId);
                photoUrl = photoData.url;
                photoFilename = photoData.filename;
                console.log('✅ Profile photo uploaded:', photoUrl);
            } catch (photoErr) {
                console.error('Photo upload failed:', photoErr);
                showToast('Employee saved, but photo upload failed', 'warning');
            }
        }

        // Clear the temporary profile photo
        window.currentProfilePhoto = null;

        // ---- Step 3: Reload employee list from DB so everything is in sync ----
        if (typeof loadEmployeeData === 'function') {
            await loadEmployeeData();
        }

        closeModal();
        showToast(photoUrl ? 'Employee added successfully with profile photo!' : 'Employee added successfully!', 'success');
        refreshCurrentView();

    } catch (error) {
        console.error('❌ Error creating employee:', error);
        showToast('Error: ' + error.message, 'error');
    }
}

// Helper function to refresh current view
function refreshCurrentView() {
    if (typeof renderCompanyLevel === 'function' && currentLevel === 'company') {
        renderCompanyLevel();
    } else if (typeof renderDepartmentLevel === 'function' && currentLevel === 'department') {
        renderDepartmentLevel();
    } else if (typeof renderEmployeeLevel === 'function' && currentLevel === 'employee') {
        renderEmployeeLevel();
    }
}

function generateEmployeeId() {
    const year = new Date().getFullYear();
    const randomNum = String(Math.floor(Math.random() * 1000)).padStart(3, '0');
    return `EMP-${year}-${randomNum}`;
}

function validateEmployeeForm(formData) {
    const errors = [];
    
    if (!formData.firstname) errors.push('First name is required');
    if (!formData.surname) errors.push('Last name is required');
    if (!formData.email) errors.push('Email is required');
    else if (!isValidEmail(formData.email)) errors.push('Please enter a valid email address');
    if (!formData.employeeId) errors.push('Employee ID is required');
    if (!formData.job_id) errors.push('Position is required');
    if (!formData.department_id) errors.push('Department is required');
    if (!formData.company_id) errors.push('Company is required');
    if (!formData.joinDate) errors.push('Join date is required');
    
    // Check if employee ID already exists
    const existingEmployee = window.employees.find(emp => emp.employeeId === formData.employeeId);
    if (existingEmployee) {
        errors.push('Employee ID already exists');
    }
    
    // Check if email already exists
    const existingEmail = window.employees.find(emp => emp.email === formData.email);
    if (existingEmail) {
        errors.push('Email address already exists');
    }
    
    return errors;
}

// Make functions globally available
window.openAddEmployeeModal = openAddEmployeeModal;
window.addEmployee = addEmployee;
window.setupEmployeeFormDropdowns = setupEmployeeFormDropdowns;
window.loadEmpDepartmentsByJob = loadEmpDepartmentsByJob;
window.loadEmpCompaniesByDepartment = loadEmpCompaniesByDepartment;
window.generateEmployeeId = generateEmployeeId;
window.validateEmployeeForm = validateEmployeeForm;

// ========== PROFILE PICTURE FUNCTIONALITY ==========

// Global variable to store current profile photo
window.currentProfilePhoto = null;

// Open webcam capture as a separate overlay (not replacing the main modal)
function openWebcamCapture() {
    // Create or get the webcam overlay
    let overlay = document.getElementById('webcamOverlay');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.id = 'webcamOverlay';
        document.body.appendChild(overlay);
    }
    
    overlay.innerHTML = `
        <style>
            #webcamOverlay {
                position: fixed;
                top: 0; left: 0; right: 0; bottom: 0;
                background: rgba(0,0,0,0.6);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 10000;
            }
            .webcam-panel {
                background: white;
                border-radius: 24px;
                padding: 24px;
                max-width: 520px;
                width: 90%;
                box-shadow: 0 20px 60px rgba(0,0,0,0.3);
                animation: webcamSlideIn 0.25s ease;
            }
            @keyframes webcamSlideIn {
                from { transform: scale(0.9); opacity: 0; }
                to { transform: scale(1); opacity: 1; }
            }
            .webcam-panel-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 16px;
            }
            .webcam-panel-header h3 {
                font-size: 16px;
                font-weight: 600;
                color: #1e293b;
                margin: 0;
                display: flex;
                align-items: center;
                gap: 8px;
            }
            .webcam-panel-header h3 i {
                color: #4f46e5;
            }
            .webcam-panel-close {
                background: none;
                border: none;
                font-size: 20px;
                cursor: pointer;
                color: #9a9a96;
                padding: 4px;
            }
            .webcam-panel-close:hover { color: #1a1a18; }
            .webcam-container {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 16px;
            }
            .webcam-video-wrapper {
                position: relative;
                width: 100%;
                max-width: 480px;
                background: #000;
                border-radius: 16px;
                overflow: hidden;
            }
            #webcamVideo {
                width: 100%;
                height: auto;
                display: block;
                transform: scaleX(-1);
                -webkit-transform: scaleX(-1);
            }
            #webcamCanvas {
                display: none;
            }
            #capturedPhotoPreview {
                width: 100%;
                height: auto;
                display: none;
                border-radius: 16px;
            }
            .webcam-controls {
                display: flex;
                gap: 12px;
                justify-content: center;
                flex-wrap: wrap;
            }
            .btn-webcam {
                padding: 12px 24px;
                border-radius: 24px;
                border: none;
                font-weight: 500;
                font-size: 14px;
                cursor: pointer;
                transition: all 0.2s;
                display: flex;
                align-items: center;
                gap: 8px;
            }
            .btn-webcam-capture {
                background: #4f46e5;
                color: white;
            }
            .btn-webcam-capture:hover {
                background: #4338ca;
            }
            .btn-webcam-confirm {
                background: #10b981;
                color: white;
            }
            .btn-webcam-confirm:hover {
                background: #059669;
            }
            .btn-webcam-retake {
                background: #f59e0b;
                color: white;
            }
            .btn-webcam-retake:hover {
                background: #d97706;
            }
            .btn-webcam-cancel {
                background: #ef4444;
                color: white;
            }
            .btn-webcam-cancel:hover {
                background: #dc2626;
            }
            .webcam-error {
                color: #ef4444;
                text-align: center;
                padding: 16px;
                background: #fee2e2;
                border-radius: 12px;
                margin-top: 12px;
            }
            .preview-message {
                text-align: center;
                color: #475569;
                font-size: 14px;
                font-weight: 500;
                padding: 12px;
                background: #f8fafc;
                border-radius: 12px;
                border: 1px solid #e2e8f0;
            }
            .preview-message i {
                color: #10b981;
                margin-right: 6px;
            }
        </style>
        <div class="webcam-panel">
            <div class="webcam-panel-header">
                <h3><i class="fas fa-camera"></i> Take Profile Photo</h3>
                <button class="webcam-panel-close" onclick="closeWebcam()">&times;</button>
            </div>
            <div class="webcam-container">
                <div class="webcam-video-wrapper">
                    <video id="webcamVideo" autoplay playsinline></video>
                    <canvas id="webcamCanvas"></canvas>
                    <img id="capturedPhotoPreview" />
                </div>
                
                <!-- Initial capture controls -->
                <div class="webcam-controls" id="captureControls">
                    <button type="button" class="btn-webcam btn-webcam-capture" onclick="capturePhoto()">
                        <i class="fas fa-camera"></i> Capture Photo
                    </button>
                    <button type="button" class="btn-webcam btn-webcam-cancel" onclick="closeWebcam()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </div>
                
                <!-- Preview confirmation controls (hidden initially) -->
                <div class="webcam-controls" id="confirmControls" style="display: none;">
                    <div class="preview-message">
                        <i class="fas fa-check-circle"></i> Photo captured! Review and confirm to use this photo.
                    </div>
                </div>
                <div class="webcam-controls" id="confirmButtons" style="display: none;">
                    <button type="button" class="btn-webcam btn-webcam-confirm" onclick="confirmCapturedPhoto()">
                        <i class="fas fa-check"></i> Use This Photo
                    </button>
                    <button type="button" class="btn-webcam btn-webcam-retake" onclick="retakePhoto()">
                        <i class="fas fa-redo"></i> Retake
                    </button>
                    <button type="button" class="btn-webcam btn-webcam-cancel" onclick="closeWebcam()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </div>
                
                <div id="webcamError"></div>
            </div>
        </div>
    `;
    
    // Start webcam
    setTimeout(() => startWebcam(), 100);
}

// Start webcam stream
async function startWebcam() {
    try {
        const video = document.getElementById('webcamVideo');
        const stream = await navigator.mediaDevices.getUserMedia({ 
            video: { 
                width: { ideal: 1280 },
                height: { ideal: 720 },
                facingMode: 'user'
            } 
        });
        video.srcObject = stream;
        window.currentWebcamStream = stream;
    } catch (error) {
        console.error('Error accessing webcam:', error);
        const errorDiv = document.getElementById('webcamError');
        if (errorDiv) {
            errorDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Unable to access webcam. Please check permissions.';
            errorDiv.className = 'webcam-error';
        }
    }
}

// Capture photo from webcam
function capturePhoto() {
    const video = document.getElementById('webcamVideo');
    const canvas = document.getElementById('webcamCanvas');
    const preview = document.getElementById('capturedPhotoPreview');
    
    if (!video || !canvas || !preview) return;
    
    // Set canvas dimensions to match video
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    
    // Draw video frame to canvas (mirrored to match the video element's CSS transform)
    const ctx = canvas.getContext('2d');
    ctx.save();
    ctx.translate(canvas.width, 0);
    ctx.scale(-1, 1);
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
    ctx.restore();
    
    // Convert to base64
    const photoData = canvas.toDataURL('image/jpeg', 0.9);
    
    // Store temporarily
    window.tempCapturedPhoto = photoData;
    
    // Show preview
    preview.src = photoData;
    preview.style.display = 'block';
    video.style.display = 'none';
    
    // Switch controls
    document.getElementById('captureControls').style.display = 'none';
    document.getElementById('confirmControls').style.display = 'flex';
    document.getElementById('confirmButtons').style.display = 'flex';
}

// Confirm and use the captured photo
async function confirmCapturedPhoto() {
    if (!window.tempCapturedPhoto) {
        showToast('No photo captured', 'error');
        return;
    }
    
    // Store photo temporarily (will be uploaded when form is submitted)
    window.currentProfilePhoto = window.tempCapturedPhoto;
    
    // Clean up temp
    window.tempCapturedPhoto = null;
    
    // Close webcam overlay first (so the form elements are accessible again)
    closeWebcam();
    
    // Update preview in the form (now the form elements should exist)
    updateProfilePhotoPreview(window.currentProfilePhoto);
    
    showToast('Photo confirmed and ready to use!', 'success');
}

// Upload profile photo to server
async function uploadProfilePhotoToServer(photoData, employeeId) {
    try {
        const response = await fetch('../../api/employees/upload_profile_photo.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                photo: photoData,
                employeeId: employeeId
            })
        });
        
        const result = await response.json();
        
        if (!result.success) {
            throw new Error(result.message || 'Failed to upload photo');
        }
        
        return result.data;
    } catch (error) {
        console.error('Error uploading photo:', error);
        throw error;
    }
}

// Delete profile photo from server
async function deleteProfilePhotoFromServer(employeeId, filename = null) {
    try {
        const body = filename ? { filename } : { employeeId };
        
        const response = await fetch('../../api/employees/delete_profile_photo.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(body)
        });
        
        const result = await response.json();
        
        if (!result.success) {
            throw new Error(result.message || 'Failed to delete photo');
        }
        
        return result;
    } catch (error) {
        console.error('Error deleting photo:', error);
        throw error;
    }
}

// Retake photo
function retakePhoto() {
    const video = document.getElementById('webcamVideo');
    const preview = document.getElementById('capturedPhotoPreview');
    
    // Clear temporary photo
    window.tempCapturedPhoto = null;
    
    // Show video again
    if (video && preview) {
        video.style.display = 'block';
        preview.style.display = 'none';
        preview.src = '';
    }
    
    // Switch controls back
    document.getElementById('captureControls').style.display = 'flex';
    document.getElementById('confirmControls').style.display = 'none';
    document.getElementById('confirmButtons').style.display = 'none';
}

// Close webcam overlay and stop stream
function closeWebcam() {
    if (window.currentWebcamStream) {
        window.currentWebcamStream.getTracks().forEach(track => track.stop());
        window.currentWebcamStream = null;
    }
    window.tempCapturedPhoto = null;
    
    // Remove the webcam overlay (NOT the main modal)
    const overlay = document.getElementById('webcamOverlay');
    if (overlay) {
        overlay.remove();
    }
}

// Handle photo upload
function handlePhotoUpload(event) {
    const file = event.target.files[0];
    if (!file) return;
    
    // Validate file type
    if (!file.type.startsWith('image/')) {
        showToast('Please select a valid image file', 'error');
        return;
    }
    
    // Validate file size (max 5MB)
    if (file.size > 5 * 1024 * 1024) {
        showToast('Image size must be less than 5MB', 'error');
        return;
    }
    
    // Read file as base64
    const reader = new FileReader();
    reader.onload = function(e) {
        const photoData = e.target.result;
        window.currentProfilePhoto = photoData;
        updateProfilePhotoPreview(photoData);
        showToast('Photo uploaded successfully!', 'success');
    };
    reader.readAsDataURL(file);
}

// Update profile photo preview
function updateProfilePhotoPreview(photoData) {
    const placeholder = document.getElementById('profileAvatarPreview');
    const preview = document.getElementById('profileImagePreview');
    const removeBtn = document.getElementById('removePhotoBtn');
    
    if (placeholder && preview) {
        placeholder.style.display = 'none';
        preview.src = photoData;
        preview.style.display = 'block';
        
        if (removeBtn) {
            removeBtn.style.display = 'inline-flex';
        }
    }
}

// Remove profile photo
async function removeProfilePhoto() {
    const employeeId = document.getElementById('empEmployeeId')?.value;
    
    // If we have a stored photo on server, delete it
    if (window.currentProfilePhoto && window.currentProfilePhoto.startsWith('/3ME/uploads/')) {
        if (employeeId) {
            try {
                await deleteProfilePhotoFromServer(employeeId);
                showToast('Photo removed from server', 'success');
            } catch (error) {
                console.error('Failed to delete photo from server:', error);
            }
        }
    }
    
    window.currentProfilePhoto = null;
    
    const placeholder = document.getElementById('profileAvatarPreview');
    const preview = document.getElementById('profileImagePreview');
    const removeBtn = document.getElementById('removePhotoBtn');
    
    if (placeholder && preview) {
        placeholder.style.display = 'flex';
        preview.style.display = 'none';
        preview.src = '';
        
        if (removeBtn) {
            removeBtn.style.display = 'none';
        }
    }
    
    // Clear file input
    const fileInput = document.getElementById('profilePhotoUpload');
    if (fileInput) {
        fileInput.value = '';
    }
    
    showToast('Photo removed', 'success');
}

// Make profile picture functions globally available
window.openWebcamCapture = openWebcamCapture;
window.startWebcam = startWebcam;
window.capturePhoto = capturePhoto;
window.confirmCapturedPhoto = confirmCapturedPhoto;
window.retakePhoto = retakePhoto;
window.closeWebcam = closeWebcam;
window.handlePhotoUpload = handlePhotoUpload;
window.updateProfilePhotoPreview = updateProfilePhotoPreview;
window.removeProfilePhoto = removeProfilePhoto;
window.uploadProfilePhotoToServer = uploadProfilePhotoToServer;
window.deleteProfilePhotoFromServer = deleteProfilePhotoFromServer;
window.refreshCurrentView = refreshCurrentView;
</script>

<!-- Profile Picture Styles -->
<style>
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

#profileImagePreview {
    width: 120px;
    height: 120px;
    border-radius: 20px;
    object-fit: cover;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
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

@media (max-width: 768px) {
    .profile-picture-section {
        flex-direction: column;
        text-align: center;
    }
    
    .profile-picture-actions {
        width: 100%;
    }
}
</style>