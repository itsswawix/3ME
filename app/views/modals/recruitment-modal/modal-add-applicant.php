<!-- modal-add-applicant.php -->
<script>
// Fetch companies, departments, and jobs data on page load
document.addEventListener('DOMContentLoaded', function() {
    fetchCompaniesData();
    fetchDepartmentsData();
    fetchJobsData();
});

function fetchCompaniesData() {
    fetch('../../api/settings/settings_api.php?action=list_companies')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.companies = data.data || [];
                console.log('✅ Companies loaded:', window.companies.length);
                
                // Try to enrich jobs if all data is now available
                enrichJobsData();
            } else {
                console.error('❌ Failed to load companies:', data.message);
                window.companies = [];
            }
        })
        .catch(error => {
            console.error('❌ Error fetching companies:', error);
            window.companies = [];
        });
}

function fetchDepartmentsData() {
    fetch('../../api/settings/settings_api.php?action=list_departments')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.departments = data.data || [];
                console.log('✅ Departments loaded:', window.departments.length);
                
                // Try to enrich jobs if all data is now available
                enrichJobsData();
            } else {
                console.error('❌ Failed to load departments:', data.message);
                window.departments = [];
            }
        })
        .catch(error => {
            console.error('❌ Error fetching departments:', error);
            window.departments = [];
        });
}

function fetchJobsData() {
    fetch('../../api/settings/settings_api.php?action=list_jobs')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Transform the data to match expected format
                window.jobs = (data.data || []).map(job => ({
                    id: job.id,
                    title: job.jobTitle,
                    level: job.level,
                    departments_id: job.departmentId,
                    department_name: '', // Will be populated when needed
                    companies_id: '', // Will be populated when needed
                    company_name: '', // Will be populated when needed
                    status: job.status,
                    vacancies: job.vacancies || 0,
                    availableVacancies: job.availableVacancies ?? job.vacancies ?? 0,
                    employedCount: job.employedCount || 0
                }));
                
                console.log('✅ Jobs loaded:', window.jobs.length);
                console.log('📋 Sample job data:', window.jobs[0]);
                
                // Enrich jobs with department and company information
                // Wait for departments and companies to be loaded
                enrichJobsData();
            } else {
                console.error('❌ Failed to load jobs:', data.message);
                window.jobs = [];
            }
        })
        .catch(error => {
            console.error('❌ Error fetching jobs:', error);
            window.jobs = [];
        });
}

function enrichJobsData() {
    if (!window.jobs || !window.departments || !window.companies) {
        console.log('⏳ Waiting for all data to load before enriching jobs...');
        console.log('  Jobs loaded:', !!window.jobs);
        console.log('  Departments loaded:', !!window.departments);
        console.log('  Companies loaded:', !!window.companies);
        return;
    }
    
    console.log('🔄 Enriching jobs with department and company data...');
    console.log('  Total jobs:', window.jobs.length);
    console.log('  Total departments:', window.departments.length);
    console.log('  Total companies:', window.companies.length);
    
    window.jobs = window.jobs.map(job => {
        // Find the department for this job
        const department = window.departments.find(d => d.id === job.departments_id);
        
        if (department) {
            job.department_name = department.name;
            job.companies_id = department.companyId;
            
            console.log(`  Job "${job.title}" → Department "${department.name}" (ID: ${department.id})`);
            
            // Find the company for this department
            const company = window.companies.find(c => c.id === department.companyId);
            if (company) {
                job.company_name = company.name;
                console.log(`    → Company "${company.name}" (ID: ${company.id})`);
            } else {
                console.warn(`    ⚠️ Company not found for department's companyId: ${department.companyId}`);
            }
        } else {
            console.warn(`  ⚠️ Department not found for job "${job.title}" (departments_id: ${job.departments_id})`);
        }
        
        return job;
    });
    
    // Update job dropdown if modal is open
    const jobSelect = document.getElementById('newJobsId');
    if (jobSelect) {
        populateJobDropdown(jobSelect);
    }
    
    console.log('✅ Jobs enriched with department and company data');
    console.log('📋 Sample enriched job:', window.jobs[0]);
}

function openAddApplicantModal() {
    const content = `
        <style>
            .modal-add-applicant * { margin: 0; box-sizing: border-box; }
            .modal-add-applicant { font-family: 'Inter', sans-serif; max-width: 600px; width: 100%; }
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
            .modal-footer-note { font-size: 0.75rem; color: #94a3b8; margin-top: 16px; text-align: right; }
            .file-upload-wrapper input[type="file"]::-webkit-file-upload-button { background: white; border: 1px solid #e2e8f0; border-radius: 20px; padding: 8px 16px; font-family: 'Inter', sans-serif; cursor: pointer; }
            .modal-buttons { display: flex; justify-content: flex-end; gap: 12px; margin-top: 28px; padding-top: 20px; border-top: 1px solid #f1f5f9; }
            .btn-save { background: #4f46e5; color: white; border: none; padding: 10px 22px; border-radius: 24px; font-size: 0.85rem; font-weight: 500; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 6px; box-shadow: 0 4px 8px rgba(79, 70, 229, 0.2); }
            .btn-save:hover { background: #4338ca; transform: translateY(-1px); box-shadow: 0 6px 12px rgba(79, 70, 229, 0.25); }
            .btn-cancel { background: white; color: #475569; border: 1px solid #e2e8f0; padding: 10px 22px; border-radius: 24px; font-size: 0.85rem; font-weight: 500; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 6px; }
            .btn-cancel:hover { background: #f8fafc; border-color: #cbd5e1; }
            .interview-section { background: #f8f4ff; padding: 16px; border-radius: 16px; margin-top: 16px; border: 1px solid #e9d5ff; }
            .interview-header { display: flex; align-items: center; gap: 8px; margin-bottom: 12px; color: #6b21a8; }
            .interview-status { display: flex; align-items: center; gap: 8px; margin-top: 8px; }
            .btn-schedule-interview { background: #9333ea; color: white; border: none; padding: 8px 16px; border-radius: 20px; font-size: 0.8rem; cursor: pointer; margin-top: 8px; }
            .btn-schedule-interview:hover { background: #7e22ce; }
            
            /* Custom Searchable Dropdown matching modal-add-correction.php */
            .search-select-container {
                position: relative;
            }
            .job-search-results {
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
            .job-search-item {
                padding: 10px 12px;
                cursor: pointer;
                border-bottom: 1px solid #f1f5f9;
                transition: background 0.2s;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .job-search-item:hover:not(.disabled) {
                background: #f8fafc;
            }
            .job-search-item.disabled {
                opacity: 0.6;
                background: #fff5f5;
                cursor: not-allowed;
            }
            .job-search-item:last-child {
                border-bottom: none;
            }
            .phone-input-wrapper {
                display: flex;
                align-items: center;
                gap: 0;
                border: 1px solid #e2e8f0;
                border-radius: 16px;
                overflow: hidden;
                background: #ffffff;
                transition: all 0.2s ease;
            }
            .phone-input-wrapper:focus-within {
                border-color: #4f46e5;
                box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
            }
            .phone-input-wrapper input {
                border: none !important;
                border-radius: 0 !important;
                flex: 1;
                outline: none !important;
                box-shadow: none !important;
            }
        </style>
        <div class="modal-add-applicant">
            <form id="addApplicantForm" onsubmit="event.preventDefault(); saveNewApplicant();">
                <div class="form-grid">
                    <div class="section-title"><i class="fas fa-user"></i> Personal Information</div>
                    <div class="form-group"><label>Surname <span class="required-star">*</span></label><input type="text" id="newSurname" required placeholder="Last name"></div>
                    <div class="form-group"><label>First Name <span class="required-star">*</span></label><input type="text" id="newFirstname" required placeholder="First name"></div>
                    <div class="form-group"><label>Middle Name</label><input type="text" id="newMiddlename" placeholder="Middle name"></div>
                    <div class="form-group"><label>Suffix</label><input type="text" id="newSuffix" placeholder="Jr., Sr., III, etc."></div>
                    
                    <div class="section-title"><i class="fas fa-briefcase"></i> Position & Assignment</div>
                    <div class="form-group full-width" style="position: relative;">
                        <label>Job Position <span class="required-star">*</span></label>
                        <select id="newJobsId" required onchange="loadDepartmentsByJob(this.value)" style="display: none;">
                            <option value="">Select Position</option>
                            <!-- Options will be populated by JavaScript -->
                        </select>
                        
                        <!-- Premium Custom Searchable Select (Follows attendance-modal design) -->
                        <div class="search-select-container">
                            <input type="text" id="jobSearch" placeholder="Click to select or type to search position..." autocomplete="off" onfocus="showJobDropdown()" oninput="filterJobs(this.value)" onblur="hideJobDropdown()" required>
                            <span style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); pointer-events: none; color: #94a3b8;">
                                <i class="fas fa-chevron-down"></i>
                            </span>
                        </div>
                        <div id="jobSearchResults" class="job-search-results" style="display: none;"></div>
                        
                        <div id="jobVacancyBadge" style="display:none; margin-top:6px; padding:6px 12px; border-radius:20px; font-size:0.78rem; font-weight:600; align-items:center; width:fit-content;"></div>
                    </div>
                    <div class="form-group"><label>Department <span class="required-star">*</span></label>
                        <select id="newDepartmentsId" required onchange="loadCompaniesByDepartmentAndJob()">
                            <option value="">Select Position First</option>
                        </select>
                        <small style="color: #64748b; font-size: 0.75rem; margin-top: 4px;">Departments that have the selected position</small>
                    </div>
                    <div class="form-group"><label>Company <span class="required-star">*</span></label>
                        <select id="newCompaniesId" required>
                            <option value="">Select Position & Department First</option>
                        </select>
                        <small style="color: #64748b; font-size: 0.75rem; margin-top: 4px;">Companies that have both the department and position</small>
                    </div>
                    
                    <div class="section-title"><i class="fas fa-address-book"></i> Contact Information</div>
                    <div class="form-group">
                        <label>Contact Number <span class="required-star">*</span></label>
                        <div class="phone-input-wrapper">
                            <span style="padding: 10px 14px; background: #f1f5f9; color: #64748b; font-size: 0.9rem; border-right: 1px solid #e2e8f0; font-weight: 500;">+63</span>
                            <input type="tel" id="newContactNumber" required placeholder="XXX XXX XXXX" maxlength="12" oninput="formatMobileNumber(this)">
                        </div>
                        <small style="color: #64748b; font-size: 0.75rem; margin-top: 4px;">Enter (e.g., 917 123 4567)</small>
                    </div>
                    <div class="form-group"><label>Email <span class="required-star">*</span></label><input type="email" id="newEmail" required placeholder="applicant@email.com"></div>
                    
                    <div class="section-title"><i class="fas fa-file"></i> Documents</div>
                    <div class="form-group full-width">
                        <label>Resume/CV <span class="required-star">*</span></label>
                        <div class="file-upload-wrapper"><input type="file" id="newResume" accept=".pdf,.doc,.docx" required></div>
                        <small style="color: #64748b;">Upload resume (PDF, DOC, DOCX)</small>
                    </div>
                    
                    <div class="section-title"><i class="fas fa-info-circle"></i> Status</div>
                    <div class="form-group">
                        <label>Application Status <span class="required-star">*</span></label>
                        <select id="newApplicationStatus" required onchange="toggleNewInterviewSection(this.value)">
                            <option value="Applied">Applied</option>
                            <option value="Under Review">Under Review</option>
                            <option value="Interview Scheduled">Interview Scheduled</option>
                            <option value="Rejected">Rejected</option>
                            <option value="Hired">Hired</option>
                        </select>
                    </div>
                    <div class="form-group"><label>Application Date <span class="required-star">*</span></label><input type="date" id="newApplicationDate" required></div>
                    
                    <!-- Interview Scheduling Section (shown when status is Interview Scheduled) -->
                    <div id="newInterviewSection" class="full-width" style="display: none;">
                        <div class="interview-section">
                            <div class="interview-header">
                                <i class="fas fa-calendar-check"></i>
                                <strong>Interview Scheduling</strong>
                            </div>
                            <div id="newInterviewStatus" class="interview-status">
                                <span style="color: #64748b;"><i class="fas fa-clock"></i> No interview scheduled yet</span>
                            </div>
                            <button type="button" class="btn-schedule-interview" onclick="scheduleInterviewForNewApplicant()">
                                <i class="fas fa-calendar-plus"></i> Schedule Interview
                            </button>
                            <input type="hidden" id="newInterviewDetails" value="">
                        </div>
                    </div>
                </div>
                <div class="modal-footer-note"><span class="required-star">*</span> Required fields</div>
                
                <div class="modal-buttons">
                    <button type="button" class="btn-cancel" onclick="closeAddApplicantModal()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn-save" id="addApplicantSaveBtn">
                        <i class="fas fa-save"></i> Save Applicant
                    </button>
                </div>
            </form>
        </div>
    `;
    
    openModal('Add New Applicant', content);
    document.getElementById('newApplicationDate').value = new Date().toISOString().split('T')[0];
    
    // Populate job dropdown from window.jobs
    const jobSelect = document.getElementById('newJobsId');
    
    console.log('🔍 openAddApplicantModal called');
    console.log('  window.jobs exists?', typeof window.jobs !== 'undefined');
    console.log('  window.jobs value:', window.jobs);
    console.log('  jobSelect element:', jobSelect);
    
    populateJobDropdown(jobSelect);
    
    // Store temporary interview details
    window.tempNewInterviewDetails = null;
}

function populateJobDropdown(jobSelect) {
    if (!jobSelect) return;
    if (window.jobs && window.jobs.length > 0) {
        // Clear existing options except the first one
        jobSelect.innerHTML = '<option value="">Select Position</option>';
        
        // Add job options
        window.jobs.forEach(job => {
            const available = job.availableVacancies ?? job.vacancies ?? 0;
            const option = document.createElement('option');
            option.value = job.id;
            const vacancyLabel = available > 0 ? ` [${available} ${available === 1 ? 'vacancy' : 'vacancies'}]` : ' [No vacancy]';
            option.textContent = job.title + (job.level ? ` (${job.level})` : '') + vacancyLabel;
            
            // Set data attributes for cascading
            option.setAttribute('data-title', job.title);
            option.setAttribute('data-departments-id', job.departments_id || '');
            option.setAttribute('data-department-name', job.department_name || '');
            option.setAttribute('data-companies-id', job.companies_id || '');
            option.setAttribute('data-company-name', job.company_name || '');
            option.setAttribute('data-available-vacancies', available);
            option.setAttribute('data-total-vacancies', job.vacancies || 0);
            
            // Disable if no vacancies
            if (available <= 0) {
                option.disabled = true;
                option.style.color = '#ef4444';
            }
            
            jobSelect.appendChild(option);
        });
        
        console.log('✅ Job dropdown populated with', window.jobs.length, 'jobs');
        
        // Clear search text if no job is selected
        const searchInput = document.getElementById('jobSearch');
        if (searchInput && !jobSelect.value) {
            searchInput.value = '';
            window.selectedJobName = null;
        }
    } else {
        console.error('❌ No jobs available! You need to create jobs in Settings first.');
        jobSelect.innerHTML = '<option value="">No jobs available - Create in Settings</option>';
    }
}

function toggleNewInterviewSection(status) {
    const section = document.getElementById('newInterviewSection');
    if (section) {
        section.style.display = status === 'Interview Scheduled' ? 'block' : 'none';
    }
}

function scheduleInterviewForNewApplicant() {
    // Store current form data
    const tempData = {
        surname: document.getElementById('newSurname')?.value || 'Applicant',
        firstname: document.getElementById('newFirstname')?.value || 'New',
        requisitionTitle: document.getElementById('newRequisitionId')?.selectedOptions[0]?.text.split(' - ')[1] || 'Position',
        contactNumber: document.getElementById('newContactNumber')?.value || '',
        email: document.getElementById('newEmail')?.value || ''
    };
    
    const content = `
        <style>
            .calendar-modal * { margin: 0; box-sizing: border-box; }
            .calendar-modal { font-family: 'Inter', sans-serif; max-width: 500px; width: 100%; }
            .calendar-full { background: white; border-radius: 20px; padding: 20px; }
            .calendar-full-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
            .weekdays { display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px; margin-bottom: 8px; }
            .weekday { text-align: center; font-size: 0.7rem; font-weight: 600; color: #64748b; padding: 8px 0; }
            .days-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px; }
            .day-cell { text-align: center; padding: 10px 0; border-radius: 12px; cursor: pointer; transition: all 0.2s; font-size: 0.85rem; }
            .day-cell:hover { background: #f3e8ff; }
            .day-cell.selected { background: #9333ea; color: white; }
            .day-cell.today { border: 2px solid #9333ea; }
            .time-selector { margin-top: 24px; }
            .time-selector label { display: block; font-size: 0.8rem; font-weight: 600; color: #475569; margin-bottom: 8px; }
            .time-selector select { width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 0.9rem; margin-bottom: 12px; }
            .interview-type-selector { margin-top: 16px; }
            .type-options { display: flex; gap: 10px; margin-top: 8px; }
            .type-option { flex: 1; padding: 12px; border: 1.5px solid #e2e8f0; border-radius: 12px; text-align: center; cursor: pointer; transition: all 0.2s; }
            .type-option.selected { border-color: #9333ea; background: #f8f4ff; }
            .type-option i { font-size: 20px; margin-bottom: 6px; display: block; color: #9333ea; }
            .modal-buttons { display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; }
            .btn-save-schedule { background: #9333ea; color: white; border: none; padding: 12px 24px; border-radius: 24px; font-size: 0.9rem; font-weight: 500; cursor: pointer; }
            .btn-cancel-schedule { background: white; color: #475569; border: 1px solid #e2e8f0; padding: 12px 24px; border-radius: 24px; font-size: 0.9rem; cursor: pointer; }
        </style>
        <div class="calendar-modal">
            <div class="calendar-full">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid #f1f5f9;">
                    <div style="width: 40px; height: 40px; border-radius: 12px; background: linear-gradient(145deg, #6366f1, #8b5cf6); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600;">${(tempData.firstname[0] || '') + (tempData.surname[0] || '')}</div>
                    <div>
                        <div style="font-weight: 600;">${escapeHtml(tempData.firstname)} ${escapeHtml(tempData.surname)}</div>
                        <div style="font-size: 0.75rem; color: #64748b;">${escapeHtml(tempData.requisitionTitle)}</div>
                    </div>
                </div>
                
                <div class="weekdays">
                    <div class="weekday">Su</div><div class="weekday">Mo</div><div class="weekday">Tu</div>
                    <div class="weekday">We</div><div class="weekday">Th</div><div class="weekday">Fr</div><div class="weekday">Sa</div>
                </div>
                <div class="days-grid" id="calendarDaysGrid">
                    ${generateFullCalendarDays()}
                </div>
                
                <div class="time-selector">
                    <label><i class="fas fa-clock"></i> Interview Time</label>
                    <select id="interviewTimeSelect">
                        <option value="09:00">09:00 AM</option>
                        <option value="09:30">09:30 AM</option>
                        <option value="10:00" selected>10:00 AM</option>
                        <option value="10:30">10:30 AM</option>
                        <option value="11:00">11:00 AM</option>
                        <option value="11:30">11:30 AM</option>
                        <option value="13:00">01:00 PM</option>
                        <option value="13:30">01:30 PM</option>
                        <option value="14:00">02:00 PM</option>
                        <option value="14:30">02:30 PM</option>
                        <option value="15:00">03:00 PM</option>
                        <option value="15:30">03:30 PM</option>
                        <option value="16:00">04:00 PM</option>
                        <option value="16:30">04:30 PM</option>
                    </select>
                </div>
                
                <div class="interview-type-selector">
                    <label><i class="fas fa-video"></i> Interview Type</label>
                    <div class="type-options">
                        <div class="type-option selected" data-type="Virtual" onclick="selectInterviewType(this)">
                            <i class="fas fa-video"></i> Virtual
                        </div>
                        <div class="type-option" data-type="Phone" onclick="selectInterviewType(this)">
                            <i class="fas fa-phone"></i> Phone
                        </div>
                        <div class="type-option" data-type="In-Person" onclick="selectInterviewType(this)">
                            <i class="fas fa-building"></i> In-Person
                        </div>
                    </div>
                    <input type="text" id="interviewLocation" placeholder="Meeting link, phone number, or address" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 0.9rem; margin-top: 12px;">
                </div>
                
                <div class="modal-buttons">
                    <button type="button" class="btn-cancel-schedule" onclick="closeModal(); openAddApplicantModal();">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="button" class="btn-save-schedule" onclick="saveTempInterviewSchedule()">
                        <i class="fas fa-check"></i> Save Schedule
                    </button>
                </div>
            </div>
        </div>
    `;
    
    openModal('Schedule Interview', content);
    
    setTimeout(() => {
        document.querySelectorAll('.day-cell').forEach(day => {
            day.addEventListener('click', function() {
                document.querySelectorAll('.day-cell').forEach(d => d.classList.remove('selected'));
                this.classList.add('selected');
            });
        });
    }, 100);
}

function generateFullCalendarDays() {
    const date = new Date();
    const year = date.getFullYear();
    const month = date.getMonth();
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const today = date.getDate();
    
    let daysHtml = '';
    
    for (let i = 0; i < firstDay; i++) {
        daysHtml += `<div class="day-cell" style="opacity: 0.3;"></div>`;
    }
    
    for (let i = 1; i <= daysInMonth; i++) {
        const isToday = (i === today);
        daysHtml += `<div class="day-cell ${isToday ? 'today' : ''}" data-day="${i}">${i}</div>`;
    }
    
    return daysHtml;
}

function selectInterviewType(element) {
    document.querySelectorAll('.type-option').forEach(opt => opt.classList.remove('selected'));
    element.classList.add('selected');
}

function saveTempInterviewSchedule() {
    const selectedDay = document.querySelector('.day-cell.selected')?.dataset.day;
    const timeSelect = document.getElementById('interviewTimeSelect');
    const timeValue = timeSelect?.value;
    const interviewType = document.querySelector('.type-option.selected')?.dataset.type || 'Virtual';
    const location = document.getElementById('interviewLocation')?.value || '';
    
    if (!selectedDay) {
        showToast('Please select a date for the interview', 'warning');
        return;
    }
    
    const timeOptions = {
        '09:00': '9:00 AM', '09:30': '9:30 AM', '10:00': '10:00 AM', '10:30': '10:30 AM',
        '11:00': '11:00 AM', '11:30': '11:30 AM', '13:00': '1:00 PM', '13:30': '1:30 PM',
        '14:00': '2:00 PM', '14:30': '2:30 PM', '15:00': '3:00 PM', '15:30': '3:30 PM',
        '16:00': '4:00 PM', '16:30': '4:30 PM'
    };
    const displayTime = timeOptions[timeValue] || timeValue;
    
    const date = new Date();
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    const interviewDate = `${monthNames[date.getMonth()]} ${selectedDay}, ${date.getFullYear()}`;
    
    window.tempNewInterviewDetails = {
        interviewDate: interviewDate,
        interviewTime: displayTime,
        interviewType: interviewType,
        location: location
    };
    
    closeModal();
    openAddApplicantModal();
    
    // Update the interview status display
    setTimeout(() => {
        const statusDiv = document.getElementById('newInterviewStatus');
        if (statusDiv) {
            statusDiv.innerHTML = `
                <span style="color: #10b981;"><i class="fas fa-check-circle"></i> Interview Scheduled!</span>
                <div style="margin-top: 8px; font-size: 0.85rem; color: #1e293b;">
                    <div><i class="fas fa-calendar"></i> ${interviewDate} at ${displayTime}</div>
                    <div><i class="fas fa-${interviewType === 'Virtual' ? 'video' : (interviewType === 'Phone' ? 'phone' : 'building')}"></i> ${interviewType} Interview</div>
                </div>
            `;
        }
        document.getElementById('newInterviewDetails').value = JSON.stringify(window.tempNewInterviewDetails);
        document.getElementById('newApplicationStatus').value = 'Interview Scheduled';
        document.getElementById('newInterviewSection').style.display = 'block';
    }, 100);
    
    showToast('Interview schedule saved!', 'success');
}

function closeAddApplicantModal() {
    if (typeof closeModal === 'function') {
        closeModal();
    }
}

function loadDepartmentsByJob(jobsId) {
    const departmentSelect = document.getElementById('newDepartmentsId');
    const companySelect = document.getElementById('newCompaniesId');
    const vacancyBadge = document.getElementById('jobVacancyBadge');
    const saveBtn = document.getElementById('addApplicantSaveBtn');
    
    console.log('🔄 loadDepartmentsByJob called with jobsId:', jobsId);
    
    // Reset dependent fields
    companySelect.innerHTML = '<option value="">Select Position & Department First</option>';
    
    if (!jobsId) {
        departmentSelect.innerHTML = '<option value="">Select Position First</option>';
        if (vacancyBadge) vacancyBadge.style.display = 'none';
        if (saveBtn) saveBtn.disabled = false;
        return;
    }
    
    // Get the selected job data
    const jobSelect = document.getElementById('newJobsId');
    const selectedOption = jobSelect.options[jobSelect.selectedIndex];
    
    if (selectedOption) {
        const departmentsId = selectedOption.getAttribute('data-departments-id');
        const departmentName = selectedOption.getAttribute('data-department-name');
        const companiesId = selectedOption.getAttribute('data-companies-id');
        const companyName = selectedOption.getAttribute('data-company-name');
        const availableVacancies = parseInt(selectedOption.getAttribute('data-available-vacancies') || '0', 10);
        const totalVacancies = parseInt(selectedOption.getAttribute('data-total-vacancies') || '0', 10);
        
        console.log('📋 Job selection details:');
        console.log('  Job ID:', jobsId);
        console.log('  Available Vacancies:', availableVacancies, '/', totalVacancies);
        
        // Update vacancy badge
        if (vacancyBadge) {
            vacancyBadge.style.display = 'inline-flex';
            if (availableVacancies <= 0) {
                vacancyBadge.style.background = '#fee2e2';
                vacancyBadge.style.color = '#dc2626';
                vacancyBadge.innerHTML = '<i class="fas fa-ban" style="margin-right:4px;"></i> No Vacancies Available';
                if (saveBtn) { saveBtn.disabled = true; saveBtn.style.opacity = '0.5'; saveBtn.title = 'No vacancies available for this job'; }
                showToast('No vacancies available for this job. Please select a different position.', 'warning');
            } else {
                vacancyBadge.style.background = '#dcfce7';
                vacancyBadge.style.color = '#16a34a';
                vacancyBadge.innerHTML = `<i class="fas fa-check-circle" style="margin-right:4px;"></i> ${availableVacancies} of ${totalVacancies} vacancies available`;
                if (saveBtn) { saveBtn.disabled = false; saveBtn.style.opacity = ''; saveBtn.title = ''; }
            }
        }
        
        if (departmentsId && departmentName) {
            // Auto-populate the department
            departmentSelect.innerHTML = `
                <option value="${escapeHtml(departmentsId)}" selected>
                    ${escapeHtml(departmentName)} ✓
                </option>
            `;
            
            // Add visual feedback
            departmentSelect.style.background = '#dcfce7';
            departmentSelect.style.borderColor = '#10b981';
            setTimeout(() => {
                departmentSelect.style.background = '#ffffff';
                departmentSelect.style.borderColor = '#e2e8f0';
            }, 1500);
            
            console.log('✅ Department auto-populated:', departmentName);
            
            // Automatically trigger company loading
            loadCompaniesByDepartmentAndJob();
        } else {
            departmentSelect.innerHTML = '<option value="">⚠️ No department assigned to this job</option>';
            console.warn('⚠️ No department data found for job:', jobsId);
            console.warn('   This job may not be properly configured in Settings.');
            
            // Show warning to user
            showToast('This job position has no department assigned. Please configure it in Settings.', 'warning');
        }
    }
}

function loadCompaniesByDepartmentAndJob() {
    const jobSelect = document.getElementById('newJobsId');
    const departmentSelect = document.getElementById('newDepartmentsId');
    const companySelect = document.getElementById('newCompaniesId');
    
    const jobsId = jobSelect.value;
    const departmentsId = departmentSelect.value;
    
    console.log('🔄 loadCompaniesByDepartmentAndJob called');
    console.log('  Job ID:', jobsId);
    console.log('  Department ID:', departmentsId);
    
    if (!jobsId || !departmentsId) {
        companySelect.innerHTML = '<option value="">Select Position & Department First</option>';
        return;
    }
    
    // Get the selected job data to find the company
    const selectedJobOption = jobSelect.options[jobSelect.selectedIndex];
    
    if (selectedJobOption) {
        const companiesId = selectedJobOption.getAttribute('data-companies-id');
        const companyName = selectedJobOption.getAttribute('data-company-name');
        
        console.log('📋 Company details from job:');
        console.log('  Company ID:', companiesId);
        console.log('  Company Name:', companyName);
        
        if (companiesId && companyName) {
            // Auto-populate the company
            companySelect.innerHTML = `
                <option value="${escapeHtml(companiesId)}" selected>
                    ${escapeHtml(companyName)} ✓
                </option>
            `;
            
            // Add visual feedback
            companySelect.style.background = '#dcfce7';
            companySelect.style.borderColor = '#10b981';
            setTimeout(() => {
                companySelect.style.background = '#ffffff';
                companySelect.style.borderColor = '#e2e8f0';
            }, 1500);
            
            console.log('✅ Company auto-populated:', companyName);
            console.log('📋 Complete assignment:');
            console.log('  Job:', selectedJobOption.getAttribute('data-title'));
            console.log('  Department:', departmentSelect.options[departmentSelect.selectedIndex]?.text);
            console.log('  Company:', companyName);
            
            // Show success message
            showToast(`Auto-assigned: ${companyName} → ${departmentSelect.options[departmentSelect.selectedIndex]?.text}`, 'success');
        } else {
            companySelect.innerHTML = '<option value="">⚠️ No company assigned to this job</option>';
            console.warn('⚠️ No company data found');
            console.warn('   The department for this job may not be properly linked to a company.');
            
            // Show warning to user
            showToast('This job\'s department has no company assigned. Please configure it in Settings.', 'warning');
        }
    }
}

// Legacy function - kept for compatibility but simplified
function loadDepartmentsByCompany(companiesId) {
    // This function is now mainly used as a fallback
    // The main flow is driven by job selection
    console.log('loadDepartmentsByCompany called - using job-driven flow instead');
}

async function saveNewApplicant() {
    const surname = document.getElementById('newSurname')?.value.trim();
    const firstname = document.getElementById('newFirstname')?.value.trim();
    const jobSelect = document.getElementById('newJobsId');
    const departmentSelect = document.getElementById('newDepartmentsId');
    const companySelect = document.getElementById('newCompaniesId');
    
    if (!surname || !firstname) { 
        showToast('Please fill all required fields.', 'warning'); 
        return; 
    }
    
    if (!jobSelect || !jobSelect.value) {
        showToast('Please select a job position.', 'warning');
        return;
    }
    
    if (!departmentSelect || !departmentSelect.value) {
        showToast('Please select a department.', 'warning');
        return;
    }
    
    if (!companySelect || !companySelect.value) {
        showToast('Please select a company.', 'warning');
        return;
    }
    
    // Get the job title for display
    const jobTitle = jobSelect.options[jobSelect.selectedIndex]?.getAttribute('data-title') || 'Unknown Position';
    const applicationStatus = document.getElementById('newApplicationStatus')?.value || 'Applied';
    
    // Get interview details if status is Interview Scheduled
    let interviewDate = null;
    let interviewType = null;
    let interviewLocation = null;
    
    if (applicationStatus === 'Interview Scheduled') {
        const detailsJson = document.getElementById('newInterviewDetails')?.value;
        if (detailsJson) {
            try {
                const details = JSON.parse(detailsJson);
                // Convert to database format
                const dateStr = details.interviewDate;
                const timeStr = details.interviewTime;
                // Create a proper datetime string
                interviewDate = new Date(dateStr + ' ' + timeStr).toISOString().slice(0, 19).replace('T', ' ');
                interviewType = details.interviewType;
                interviewLocation = details.location;
            } catch(e) {
                console.error('Error parsing interview details:', e);
            }
        }
        if (!interviewDate && window.tempNewInterviewDetails) {
            const details = window.tempNewInterviewDetails;
            interviewDate = new Date(details.interviewDate + ' ' + details.interviewTime).toISOString().slice(0, 19).replace('T', ' ');
            interviewType = details.interviewType;
            interviewLocation = details.location;
        }
    }
    
    // Get the display names from the selected options (for logging only)
    const companyName = companySelect.options[companySelect.selectedIndex]?.text || '';
    const departmentName = departmentSelect.options[departmentSelect.selectedIndex]?.text || '';
    
    // Get contact number and add +63 prefix for storage (digits only, no spaces)
    const contactInput = document.getElementById('newContactNumber')?.value || '';
    const contactDigits = contactInput.replace(/\D/g, '');
    if (!contactDigits || contactDigits.length !== 10) {
        showToast('Mobile number must be exactly 10 digits (excluding +63).', 'warning');
        return;
    }
    const contact_number = '+63 ' + contactDigits;
    
    const applicantData = {
        requisition_id: document.getElementById('newRequisitionId')?.value || null,
        job_id: jobSelect.value,
        company_id: companySelect.value,
        department_id: departmentSelect.value,
        firstname: firstname,
        middlename: document.getElementById('newMiddlename')?.value || '',
        surname: surname,
        suffix: document.getElementById('newSuffix')?.value || '',
        email: document.getElementById('newEmail')?.value || '',
        contact_number: contact_number,
        application_status: applicationStatus,
        application_date: document.getElementById('newApplicationDate')?.value || new Date().toISOString().split('T')[0],
        resume_filename: document.getElementById('newResume')?.files[0]?.name || 'Resume.pdf',
        interview_date: interviewDate,
        interview_type: interviewType,
        interview_location: interviewLocation
    };
    
    console.log('📤 Sending applicant data to API:');
    console.log('  Job ID:', applicantData.job_id, '| Title:', jobTitle);
    console.log('  Company ID:', applicantData.company_id, '| Name:', companyName);
    console.log('  Department ID:', applicantData.department_id, '| Name:', departmentName);
    console.log('  Full payload:', JSON.stringify(applicantData, null, 2));
    
    // Send to API
    try {
        const response = await fetch('../../api/recruitment/applicants.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(applicantData)
        });
        
        console.log('Response status:', response.status);
        const data = await response.json();
        console.log('Response data:', data);
        
        if (data.success) {
            showToast(`Applicant ${firstname} ${surname} added successfully!`, 'success');
            
            console.log('✅ Applicant saved successfully, reloading list...');
            
            // Close modal first
            if (typeof closeModal === 'function') {
                closeModal();
            }
            
            // Reload applicants from API to get fresh data
            if (typeof loadApplicants === 'function') {
                await loadApplicants();
            } else {
                console.warn('⚠️ loadApplicants function not found, adding to local array');
                
                // Fallback: Add to local array for immediate UI update
                const newApplicant = {
                    id: data.id,
                    requisitionId: applicantData.requisition_id,
                    jobId: applicantData.job_id,
                    companyId: applicantData.company_id,
                    departmentId: applicantData.department_id,
                    requisitionTitle: jobTitle,
                    firstname: applicantData.firstname,
                    middlename: applicantData.middlename,
                    surname: applicantData.surname,
                    email: applicantData.email,
                    company: companyName,
                    department: departmentName,
                    position: jobTitle,
                    contactNumber: applicantData.contact_number,
                    applicationStatus: applicantData.application_status,
                    applicationDate: new Date(applicantData.application_date).toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' }),
                    resumeName: applicantData.resume_filename,
                    avatar: (firstname[0] || '') + (surname[0] || ''),
                    color: 'linear-gradient(145deg, #6366f1, #a78bfa)',
                    interviewDetails: interviewDate ? {
                        interviewDate: new Date(interviewDate).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' }),
                        interviewTime: new Date(interviewDate).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true }),
                        interviewType: interviewType,
                        location: interviewLocation
                    } : null
                };
                
                window.applicants = window.applicants || [];
                window.applicants.unshift(newApplicant);
                
                if (typeof renderApplicantTable === 'function') {
                    renderApplicantTable(window.applicants);
                } else {
                    console.warn('renderApplicantTable function not found, skipping table update');
                }
            }
            
            window.tempNewInterviewDetails = null;
        } else {
            showToast(data.message || 'Error creating applicant', 'warning');
        }
    } catch (error) {
        console.error('Fetch Error:', error);
        console.error('Error stack:', error.stack);
        showToast('Network error or JavaScript exception: ' + error.message, 'error');
    }
}

/* Custom Searchable Dropdown matching modal-add-correction.php design */
window.selectedJobName = null;

async function showJobDropdown() {
    const resultsDiv = document.getElementById('jobSearchResults');
    if (!resultsDiv) return;
    
    const jobSelect = document.getElementById('newJobsId');
    if (!jobSelect) return;
    
    // Get all options from the hidden select (excluding the empty "Select Position")
    const options = Array.from(jobSelect.options).filter(opt => opt.value !== '');
    
    if (options.length === 0) {
        resultsDiv.innerHTML = '<div style="padding: 12px; text-align: center; color: #ef4444; font-size: 12px;">No jobs available - Create in Settings</div>';
        resultsDiv.style.display = 'block';
        return;
    }
    
    // Sort options alphabetically by text content
    const sortedOptions = [...options].sort((a, b) => {
        const titleA = (a.textContent || '').toLowerCase();
        const titleB = (b.textContent || '').toLowerCase();
        return titleA.localeCompare(titleB);
    });
    
    renderJobDropdownItems(sortedOptions);
    resultsDiv.style.display = 'block';
}

async function filterJobs(query) {
    const resultsDiv = document.getElementById('jobSearchResults');
    if (!resultsDiv) return;
    
    const jobSelect = document.getElementById('newJobsId');
    if (!jobSelect) return;
    
    const options = Array.from(jobSelect.options).filter(opt => opt.value !== '');
    
    const filtered = options.filter(opt => {
        const text = opt.textContent.toLowerCase();
        const searchTerm = query.toLowerCase();
        return text.includes(searchTerm);
    });
    
    const sortedFiltered = [...filtered].sort((a, b) => {
        const titleA = (a.textContent || '').toLowerCase();
        const titleB = (b.textContent || '').toLowerCase();
        return titleA.localeCompare(titleB);
    });
    
    renderJobDropdownItems(sortedFiltered);
    resultsDiv.style.display = 'block';
    
    // Reset selected job if query is manually edited
    if (window.selectedJobName && query !== window.selectedJobName) {
        jobSelect.value = '';
        window.selectedJobName = null;
        jobSelect.dispatchEvent(new Event('change'));
    }
}

function renderJobDropdownItems(optionsList) {
    const resultsDiv = document.getElementById('jobSearchResults');
    if (!resultsDiv) return;
    
    if (optionsList.length === 0) {
        resultsDiv.innerHTML = '<div style="padding: 12px; text-align: center; color: #64748b; font-size: 12px;">No positions found</div>';
        return;
    }
    
    resultsDiv.innerHTML = optionsList.map(opt => {
        const val = opt.value;
        const text = opt.textContent;
        const available = parseInt(opt.getAttribute('data-available-vacancies') || '0', 10);
        const isDisabled = opt.disabled;
        
        let badgeHtml = '';
        if (available > 0) {
            badgeHtml = `<span style="font-size: 11px; padding: 2px 8px; border-radius: 12px; background: #dcfce7; color: #16a34a; font-weight: 600;">${available} open</span>`;
        } else {
            badgeHtml = `<span style="font-size: 11px; padding: 2px 8px; border-radius: 12px; background: #fee2e2; color: #dc2626; font-weight: 600;">Full</span>`;
        }
        
        // Remove the bracketed vacancy label from display text
        const displayName = text.split(' [')[0];
        
        const mousedownAttr = !isDisabled 
            ? `onmousedown="selectJobForApplicant('${val}', '${escapeHtml(displayName)}')"` 
            : `onmousedown="event.stopPropagation(); showToast('No vacancies available for this job position.', 'warning');"`;
        
        return `
            <div class="job-search-item ${isDisabled ? 'disabled' : ''}" ${mousedownAttr}>
                <div style="font-weight: 500; color: #1e293b; font-size: 13px;">${escapeHtml(displayName)}</div>
                ${badgeHtml}
            </div>
        `;
    }).join('');
}

function selectJobForApplicant(id, title) {
    const jobSelect = document.getElementById('newJobsId');
    const searchInput = document.getElementById('jobSearch');
    
    jobSelect.value = id;
    searchInput.value = title;
    window.selectedJobName = title;
    
    const resultsDiv = document.getElementById('jobSearchResults');
    if (resultsDiv) {
        resultsDiv.style.display = 'none';
    }
    
    // Trigger cascading resets and populators
    jobSelect.dispatchEvent(new Event('change'));
}

// Make functions globally available
window.showJobDropdown = showJobDropdown;
window.filterJobs = filterJobs;
window.selectJobForApplicant = selectJobForApplicant;
window.hideJobDropdown = hideJobDropdown;
window.formatMobileNumber = formatMobileNumber;

function hideJobDropdown() {
    setTimeout(() => {
        const resultsDiv = document.getElementById('jobSearchResults');
        if (resultsDiv) {
            resultsDiv.style.display = 'none';
        }
        
        const selectedId = document.getElementById('newJobsId').value;
        const searchInput = document.getElementById('jobSearch');
        if (!selectedId) {
            searchInput.value = '';
        } else if (window.selectedJobName) {
            searchInput.value = window.selectedJobName;
        }
    }, 200);
}

function formatMobileNumber(input) {
    // Get only digits
    let value = input.value.replace(/\D/g, '');
    
    // If the user types "09", automatically remove the leading "0" (since +63 is prefixed)
    if (value.startsWith('09')) {
        value = value.substring(1);
    }
    
    // Limit to 10 digits
    if (value.length > 10) {
        value = value.substring(0, 10);
    }
    
    // Format with spaces: XXX XXX XXXX
    let formatted = '';
    if (value.length > 0) {
        formatted = value.substring(0, 3);
    }
    if (value.length > 3) {
        formatted += ' ' + value.substring(3, 6);
    }
    if (value.length > 6) {
        formatted += ' ' + value.substring(6, 10);
    }
    
    // Update input value
    input.value = formatted;
}
</script>