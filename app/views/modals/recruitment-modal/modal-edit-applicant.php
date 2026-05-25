<!-- modal-edit-applicant.php -->
<script>
function editApplicant(id) {
    const app = window.applicants.find(a => a.id === id);
    if (!app) return;
    
    let appDateValue = '';
    try { appDateValue = new Date(app.applicationDate).toISOString().split('T')[0]; } catch(e) {}
    
    const interviewDetails = app.interviewDetails || null;
    const hasInterview = interviewDetails !== null;
    
    let rawContact = app.contactNumber || '';
    // Strip leading +63 or 63 and spaces
    rawContact = rawContact.replace(/^(\+?63\s*)/, '');
    let contactDigits = rawContact.replace(/\D/g, '').substring(0, 10);
    let formattedContact = '';
    if (contactDigits.length > 0) {
        formattedContact = contactDigits.substring(0, 3);
    }
    if (contactDigits.length > 3) {
        formattedContact += ' ' + contactDigits.substring(3, 6);
    }
    if (contactDigits.length > 6) {
        formattedContact += ' ' + contactDigits.substring(6, 10);
    }
    
    const content = `
        <style>
            .modal-edit-applicant * { margin: 0; box-sizing: border-box; }
            .modal-edit-applicant { font-family: 'Inter', sans-serif; max-width: 600px; width: 100%; }
            .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px 24px; }
            .full-width { grid-column: span 2; }
            .form-group { display: flex; flex-direction: column; margin-bottom: 4px; }
            .form-group label { font-size: 0.85rem; font-weight: 500; margin-bottom: 6px; color: #475569; }
            .form-group input, .form-group select { padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 16px; font-size: 0.9rem; background: #ffffff; }
            .form-group input:focus, .form-group select:focus { outline: none; border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); }
            .section-title { grid-column: span 2; font-size: 1rem; font-weight: 600; margin: 20px 0 8px; padding-bottom: 8px; border-bottom: 1.5px solid #e2e8f0; color: #0f172a; display: flex; align-items: center; gap: 8px; }
            .section-title i { color: #4f46e5; }
            .required-star { color: #ef4444; }
            .employee-preview { display: flex; align-items: center; gap: 12px; margin-bottom: 20px; padding: 12px; background: #f8fafc; border-radius: 16px; }
            .employee-avatar-small { width: 40px; height: 40px; border-radius: 12px; background: ${app.color}; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; }
            .modal-buttons { display: flex; justify-content: flex-end; gap: 12px; margin-top: 28px; padding-top: 20px; border-top: 1px solid #f1f5f9; }
            .btn-save { background: #4f46e5; color: white; border: none; padding: 10px 22px; border-radius: 24px; font-size: 0.85rem; font-weight: 500; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 6px; box-shadow: 0 4px 8px rgba(79, 70, 229, 0.2); }
            .btn-save:hover { background: #4338ca; transform: translateY(-1px); }
            .btn-cancel { background: white; color: #475569; border: 1px solid #e2e8f0; padding: 10px 22px; border-radius: 24px; font-size: 0.85rem; font-weight: 500; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 6px; }
            .btn-cancel:hover { background: #f8fafc; border-color: #cbd5e1; }
            .interview-quick-schedule { background: #f8f4ff; padding: 14px 16px; border-radius: 16px; margin-top: 8px; border: 1px solid #e9d5ff; }
            .interview-quick-schedule p { display: flex; align-items: center; gap: 8px; margin-bottom: 10px; color: #6b21a8; }
            .btn-schedule { background: #9333ea; color: white; border: none; padding: 8px 16px; border-radius: 20px; font-size: 0.8rem; cursor: pointer; }
            .btn-schedule:hover { background: #7e22ce; }
            .interview-details-display { background: white; padding: 12px; border-radius: 12px; margin-bottom: 10px; border: 1px solid #e9d5ff; }
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
        <div class="modal-edit-applicant">
            
            <div class="employee-preview">
                <div class="employee-avatar-small">${app.avatar}</div>
                <div>
                    <h4 style="font-weight:600;">${escapeHtml(app.firstname)} ${escapeHtml(app.surname)}</h4>
                    <p style="color:#64748b; font-size:0.75rem;">${escapeHtml(app.email)}</p>
                </div>
            </div>
            <form id="editApplicantForm" onsubmit="event.preventDefault(); updateApplicant('${id}');">
                <div class="form-grid">
                    <div class="section-title"><i class="fas fa-user"></i> Personal Information</div>
                    <div class="form-group"><label>Surname <span class="required-star">*</span></label><input type="text" id="editSurname" required value="${escapeHtml(app.surname)}"></div>
                    <div class="form-group"><label>First Name <span class="required-star">*</span></label><input type="text" id="editFirstname" required value="${escapeHtml(app.firstname)}"></div>
                    <div class="form-group"><label>Middle Name</label><input type="text" id="editMiddlename" value="${escapeHtml(app.middlename || '')}"></div>
                    <div class="form-group"><label>Suffix</label><input type="text" id="editSuffix" value="${escapeHtml(app.suffix || '')}" placeholder="Jr., Sr., III, etc."></div>
                    
                    <div class="section-title"><i class="fas fa-briefcase"></i> Position & Assignment</div>
                    <div class="form-group full-width">
                        <label>Position <span class="required-star">*</span></label>
                        <input type="text" id="editPosition" required value="${escapeHtml(app.position || '')}" readonly style="background: #f8fafc; cursor: not-allowed;">
                        <small style="color: #64748b; font-size: 0.75rem; margin-top: 4px;">Position cannot be changed after application</small>
                        ${app.jobId ? `<div id="editJobVacancyBadge" style="margin-top:6px; padding:5px 10px; border-radius:20px; font-size:0.75rem; font-weight:600; display:inline-flex; align-items:center; background:#f1f5f9; color:#64748b;"><i class="fas fa-spinner fa-spin" style="margin-right:4px;"></i> Checking vacancies...</div>` : ''}
                    </div>
                    <div class="form-group">
                        <label>Department <span class="required-star">*</span></label>
                        <input type="text" id="editDepartment" required value="${escapeHtml(app.department || '')}" readonly style="background: #f8fafc; cursor: not-allowed;">
                        <small style="color: #64748b; font-size: 0.75rem; margin-top: 4px;">Auto-assigned with position</small>
                    </div>
                    <div class="form-group">
                        <label>Company <span class="required-star">*</span></label>
                        <input type="text" id="editCompanyDisplay" required value="${escapeHtml(app.company || '')}" readonly style="background: #f8fafc; cursor: not-allowed;">
                        <small style="color: #64748b; font-size: 0.75rem; margin-top: 4px;">Auto-assigned with position</small>
                    </div>
                    
                    <div class="section-title"><i class="fas fa-address-book"></i> Contact Information</div>
                    <div class="form-group">
                        <label>Contact Number <span class="required-star">*</span></label>
                        <div class="phone-input-wrapper">
                            <span style="padding: 10px 14px; background: #f1f5f9; color: #64748b; font-size: 0.9rem; border-right: 1px solid #e2e8f0; font-weight: 500;">+63</span>
                            <input type="tel" id="editContactNumber" required placeholder="XXX XXX XXXX" maxlength="12" oninput="formatMobileNumber(this)" value="${escapeHtml(formattedContact)}">
                        </div>
                        <small style="color: #64748b; font-size: 0.75rem; margin-top: 4px;">Enter 10 digit mobile number (e.g., 917 123 4567)</small>
                    </div>
                    <div class="form-group"><label>Email <span class="required-star">*</span></label><input type="email" id="editEmail" required value="${escapeHtml(app.email)}"></div>
                    
                    <div class="section-title"><i class="fas fa-info-circle"></i> Status</div>
                    <div class="form-group">
                        <label>Application Status <span class="required-star">*</span></label>
                        <select id="editApplicationStatus" required onchange="toggleInterviewSection(this.value, '${id}')">
                            ${['Applied','Under Review','Interview Scheduled','Rejected','Hired'].map(s => `<option value="${s}" ${app.applicationStatus === s ? 'selected' : ''}>${s}</option>`).join('')}
                        </select>
                    </div>
                    <div class="form-group"><label>Application Date <span class="required-star">*</span></label><input type="date" id="editApplicationDate" required value="${appDateValue}"></div>
                    
                    <!-- Interview Section -->
                    <div id="interviewSection" class="full-width" style="display: ${app.applicationStatus === 'Interview Scheduled' ? 'block' : 'none'};">
                        <div class="interview-quick-schedule">
                            <p><i class="fas fa-calendar-check" style="color: #9333ea;"></i> <strong>Interview Details</strong></p>
                            
                            ${hasInterview ? `
                            <div class="interview-details-display">
                                <div style="margin-bottom: 8px; font-size: 0.85rem;">
                                    <div><i class="fas fa-calendar"></i> <strong>${interviewDetails.interviewDate}</strong> at <strong>${interviewDetails.interviewTime}</strong></div>
                                    <div style="margin-top: 4px;"><i class="fas fa-${interviewDetails.interviewType === 'Virtual' ? 'video' : (interviewDetails.interviewType === 'Phone' ? 'phone' : 'building')}"></i> ${interviewDetails.interviewType} Interview</div>
                                    ${interviewDetails.location ? `<div style="margin-top: 4px; color: #64748b;"><i class="fas fa-map-pin"></i> ${escapeHtml(interviewDetails.location)}</div>` : ''}
                                </div>
                                <span class="badge badge-success" style="font-size: 0.7rem;"><i class="fas fa-check-circle"></i> Scheduled</span>
                            </div>
                            ` : `
                            <div style="margin-bottom: 10px; padding: 10px; background: #fff; border-radius: 10px;">
                                <span style="color: #64748b;"><i class="fas fa-clock"></i> No interview scheduled yet</span>
                            </div>
                            `}
                            
                            <button type="button" class="btn-schedule" onclick="closeModal(); showFullCalendar('${id}');">
                                <i class="fas fa-calendar-${hasInterview ? 'edit' : 'plus'}"></i> ${hasInterview ? 'Update Interview' : 'Schedule Interview'}
                            </button>
                            <button type="button" class="btn-schedule" style="background: #10b981; margin-left: 8px;" onclick="callApplicant('${id}', '${escapeHtml(app.contactNumber)}');">
                                <i class="fas fa-phone"></i> Contact
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="modal-buttons">
                    <button type="button" class="btn-cancel" onclick="closeModal()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    `;
    
    openModal('Edit Applicant', content);
    
    // Load vacancy info for the applicant's job
    if (app.jobId) {
        setTimeout(() => {
            const badge = document.getElementById('editJobVacancyBadge');
            if (!badge) return;
            
            // First check from window.jobs cache
            const cachedJob = (window.jobs || []).find(j => j.id === app.jobId);
            if (cachedJob) {
                const available = cachedJob.availableVacancies ?? cachedJob.vacancies ?? 0;
                const total = cachedJob.vacancies || 0;
                if (available <= 0) {
                    badge.style.background = '#fee2e2'; badge.style.color = '#dc2626';
                    badge.innerHTML = '<i class="fas fa-ban" style="margin-right:4px;"></i> No Vacancies Available';
                } else {
                    badge.style.background = '#dcfce7'; badge.style.color = '#16a34a';
                    badge.innerHTML = `<i class="fas fa-users" style="margin-right:4px;"></i> ${available} of ${total} vacancies available`;
                }
                return;
            }
            
            // Fallback: fetch from API
            fetch(`../../api/settings/settings_api.php?action=get_job&id=${app.jobId}`)
                .then(r => r.json())
                .then(data => {
                    if (data.success && data.data) {
                        const jd = data.data;
                        const available = jd.availableVacancies ?? jd.vacancies ?? 0;
                        const total = jd.vacancies || 0;
                        if (!badge) return;
                        if (available <= 0) {
                            badge.style.background = '#fee2e2'; badge.style.color = '#dc2626';
                            badge.innerHTML = '<i class="fas fa-ban" style="margin-right:4px;"></i> No Vacancies Available';
                        } else {
                            badge.style.background = '#dcfce7'; badge.style.color = '#16a34a';
                            badge.innerHTML = `<i class="fas fa-users" style="margin-right:4px;"></i> ${available} of ${total} vacancies available`;
                        }
                    } else {
                        if (badge) { badge.style.display = 'none'; }
                    }
                })
                .catch(() => { if (badge) badge.style.display = 'none'; });
        }, 100);
    }
}

function toggleInterviewSection(status, applicantId) {
    const section = document.getElementById('interviewSection');
    if (section) {
        section.style.display = status === 'Interview Scheduled' ? 'block' : 'none';
    }
}

async function updateApplicant(id) {
    const index = window.applicants.findIndex(a => a.id === id);
    if (index === -1) return;
    
    const surname = document.getElementById('editSurname')?.value.trim();
    const firstname = document.getElementById('editFirstname')?.value.trim();
    
    if (!surname || !firstname) {
        showToast('Please fill all required fields.', 'warning');
        return;
    }
    
    const newStatus = document.getElementById('editApplicationStatus')?.value || 'Applied';
    
    // Get contact number and validate
    const contactInput = document.getElementById('editContactNumber')?.value || '';
    const contactDigits = contactInput.replace(/\D/g, '');
    
    if (!contactDigits || contactDigits.length !== 10) {
        showToast('Mobile number must be exactly 10 digits (excluding +63).', 'warning');
        return;
    }
    
    const contact_number = '+63 ' + contactDigits;
    
    // Retrieve interview details from local state if they exist
    let interviewDate = null;
    let interviewType = null;
    let interviewLocation = null;
    
    const localApp = window.applicants[index];
    
    // Re-construct the API payload
    const applicantData = {
        id: id,
        firstname: firstname,
        middlename: document.getElementById('editMiddlename')?.value || '',
        surname: surname,
        suffix: document.getElementById('editSuffix')?.value || '',
        email: document.getElementById('editEmail')?.value || '',
        contact_number: contact_number,
        application_status: newStatus,
        application_date: document.getElementById('editApplicationDate')?.value || new Date().toISOString().split('T')[0],
        notes: localApp.notes || ''
    };
    
    // Handle interview date and format for database
    if (newStatus === 'Interview Scheduled' && localApp.interviewDetails) {
        try {
            const details = localApp.interviewDetails;
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
    
    applicantData.interview_date = interviewDate;
    applicantData.interview_type = interviewType;
    applicantData.interview_location = interviewLocation;
    
    try {
        const response = await fetch('../../api/recruitment/applicants.php', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(applicantData)
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast('Applicant updated successfully!', 'success');
            
            // Reload fresh data from API
            if (typeof loadApplicants === 'function') {
                await loadApplicants();
            } else {
                // Fallback local update
                const updatedApplicant = {
                    ...localApp,
                    surname: surname,
                    firstname: firstname,
                    middlename: applicantData.middlename,
                    suffix: applicantData.suffix,
                    contactNumber: contact_number,
                    email: applicantData.email,
                    applicationStatus: newStatus,
                    applicationDate: new Date(applicantData.application_date).toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' }),
                    avatar: (firstname[0] || '') + (surname[0] || '')
                };
                window.applicants[index] = updatedApplicant;
                if (typeof renderApplicantTable === 'function') {
                    renderApplicantTable(window.applicants);
                }
            }
            
            if (typeof closeModal === 'function') {
                closeModal();
            }
            
            // If status changed to Interview Scheduled and no interview scheduled, prompt for scheduling
            if (newStatus === 'Interview Scheduled' && !localApp.interviewDetails) {
                setTimeout(() => {
                    showFullCalendar(id);
                }, 500);
            }
        } else {
            showToast(data.message || 'Error updating applicant', 'warning');
        }
    } catch (error) {
        console.error('Error updating applicant via API:', error);
        showToast('Network error while updating applicant.', 'error');
    }
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