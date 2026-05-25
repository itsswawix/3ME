<!-- modal-view-applicant.php -->
<script>
function viewApplicant(id) {
    const app = window.applicants.find(a => a.id === id);
    if (!app) return;
    const statusConfig = {
        'Applied': { bg: '#dbeafe', color: '#2563eb', icon: 'fa-paper-plane' },
        'Under Review': { bg: '#fef3c7', color: '#b45309', icon: 'fa-search' },
        'Interview Scheduled': { bg: '#f3e8ff', color: '#9333ea', icon: 'fa-calendar-check' },
        'Rejected': { bg: '#fee2e2', color: '#dc2626', icon: 'fa-times-circle' },
        'Hired': { bg: '#dcfce7', color: '#16a34a', icon: 'fa-check-circle' }
    }[app.applicationStatus] || { bg: '#f1f5f9', color: '#64748b', icon: 'fa-circle' };
    const fullName = `${app.firstname} ${app.middlename ? app.middlename[0] + '. ' : ''}${app.surname}${app.suffix ? ' ' + app.suffix : ''}`;
    
    // Get interview details if status is Interview Scheduled
    const interviewDetails = app.interviewDetails || null;
    const hasInterview = interviewDetails !== null;
    
    const content = `
        <!-- Applicant Header -->
        <div style="display: flex; align-items: flex-start; gap: 16px; margin-bottom: 24px; padding-bottom: 20px; border-bottom: 1px solid #eef2ff;">
            <img src="${app.profilePhoto || '/3ME/assets/images/default-avatar.png'}" style="width: 64px; height: 64px; border-radius: 20px; object-fit: cover; flex-shrink: 0;" />
            <div style="flex: 1;">
                <h3 style="margin: 0; font-size: 16px; font-weight: 600; color: #0f172a;">${escapeHtml(fullName)}</h3>
                <div style="font-size: 12px; color: #64748b; margin-top: 4px; display: flex; gap: 12px; flex-wrap: wrap;">
                    <span><i class="fas fa-id-card" style="color: #4f46e5; width: 14px;"></i> ${app.id}</span>
                    <span><i class="fas fa-briefcase" style="color: #4f46e5; width: 14px;"></i> ${escapeHtml(app.requisitionTitle || app.requisitionId)}</span>
                </div>
                <div style="margin-top: 8px;">
                    <span style="padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 500; background: ${statusConfig.bg}; color: ${statusConfig.color}; display: inline-flex; align-items: center; gap: 4px;">
                        <i class="fas ${statusConfig.icon}"></i> ${app.applicationStatus}
                    </span>
                    ${app.applicationStatus === 'Interview Scheduled' ? `
                        <span style="padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 500; display: inline-flex; align-items: center; gap: 4px; margin-left: 8px; background: ${hasInterview ? '#dcfce7' : '#fef3c7'}; color: ${hasInterview ? '#16a34a' : '#b45309'};">
                            <i class="fas fa-${hasInterview ? 'check-circle' : 'clock'}"></i>
                            ${hasInterview ? 'Interview Scheduled' : 'Interview Not Scheduled'}
                        </span>
                    ` : ''}
                </div>
            </div>
        </div>

        <!-- Detail Box: Position, Department, etc. -->
        <h3 style="font-size: 14px; font-weight: 600; color: #1e293b; margin: 0 0 16px; display: flex; align-items: center; gap: 8px; border-bottom: 1px solid #f1f5f9; padding-bottom: 6px;">
            <i class="fas fa-user-circle" style="color: #4f46e5;"></i> Applicant Information
        </h3>
        
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; margin-bottom: 24px;">
            <div style="background: #f8fafc; padding: 12px 14px; border-radius: 16px; border: 1px solid #e2e8f0;">
                <div style="font-size: 9px; font-weight: 600; text-transform: uppercase; color: #64748b; margin-bottom: 4px;"><i class="fas fa-briefcase" style="color: #4f46e5;"></i> Position</div>
                <div style="font-size: 13px; font-weight: 600; color: #0f172a;">${escapeHtml(app.position || app.requisitionTitle)}</div>
            </div>
            <div style="background: #f8fafc; padding: 12px 14px; border-radius: 16px; border: 1px solid #e2e8f0;">
                <div style="font-size: 9px; font-weight: 600; text-transform: uppercase; color: #64748b; margin-bottom: 4px;"><i class="fas fa-layer-group" style="color: #4f46e5;"></i> Department</div>
                <div style="font-size: 13px; font-weight: 600; color: #0f172a;">${escapeHtml(app.department || 'Not assigned')}</div>
            </div>
            <div style="background: #f8fafc; padding: 12px 14px; border-radius: 16px; border: 1px solid #e2e8f0;">
                <div style="font-size: 9px; font-weight: 600; text-transform: uppercase; color: #64748b; margin-bottom: 4px;"><i class="fas fa-envelope" style="color: #4f46e5;"></i> Email</div>
                <div style="font-size: 13px; font-weight: 600; color: #0f172a; word-break: break-all;">${escapeHtml(app.email)}</div>
            </div>
            <div style="background: #f8fafc; padding: 12px 14px; border-radius: 16px; border: 1px solid #e2e8f0;">
                <div style="font-size: 9px; font-weight: 600; text-transform: uppercase; color: #64748b; margin-bottom: 4px;"><i class="fas fa-phone" style="color: #4f46e5;"></i> Contact</div>
                <div style="font-size: 13px; font-weight: 600; color: #0f172a;">${escapeHtml(app.contactNumber)}</div>
            </div>
            <div style="background: #f8fafc; padding: 12px 14px; border-radius: 16px; border: 1px solid #e2e8f0;">
                <div style="font-size: 9px; font-weight: 600; text-transform: uppercase; color: #64748b; margin-bottom: 4px;"><i class="fas fa-calendar" style="color: #4f46e5;"></i> Applied Date</div>
                <div style="font-size: 13px; font-weight: 600; color: #0f172a;">${app.applicationDate}</div>
            </div>
            <div style="background: #f0fdf4; padding: 12px 14px; border-radius: 16px; border: 1px solid #bbf7d0; cursor: pointer; transition: all 0.2s;" onclick="viewResume('${app.id}')" onmouseover="this.style.background='#dcfce7'" onmouseout="this.style.background='#f0fdf4'">
                <div style="font-size: 9px; font-weight: 600; text-transform: uppercase; color: #166534; margin-bottom: 4px;"><i class="fas fa-file-pdf" style="color: #10b981;"></i> Resume</div>
                <div style="font-size: 13px; font-weight: 600; color: #166534; display: flex; align-items: center; gap: 4px;"><i class="fas fa-download"></i> ${escapeHtml(app.resumeName)}</div>
            </div>
        </div>

        ${app.applicationStatus === 'Interview Scheduled' ? `
        <h3 style="font-size: 14px; font-weight: 600; color: #1e293b; margin: 24px 0 16px; display: flex; align-items: center; gap: 8px; border-bottom: 1px solid #f1f5f9; padding-bottom: 6px;">
            <i class="fas fa-calendar-check" style="color: #9333ea;"></i> Interview Details
        </h3>
        
        <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 24px;">
            <div style="display: flex; flex-direction: column; padding: 14px; background: #faf5ff; border: 1px solid #e9d5ff; border-radius: 16px; gap: 10px;">
                ${hasInterview ? `
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; font-size: 14px; background: #9333ea; flex-shrink: 0;">
                            <i class="fas ${interviewDetails.interviewType === 'Virtual' ? 'fa-video' : (interviewDetails.interviewType === 'Phone' ? 'fa-phone' : 'fa-building')}"></i>
                        </div>
                        <div>
                            <h4 style="margin: 0; font-size: 13px; font-weight: 600; color: #0f172a;">${interviewDetails.interviewType} Interview</h4>
                            <p style="margin: 2px 0 0; font-size: 11px; color: #6b21a8;">${interviewDetails.interviewDate} at ${interviewDetails.interviewTime}</p>
                        </div>
                    </div>
                </div>
                ${interviewDetails.location ? `
                <div style="margin-top: 4px; padding-top: 8px; border-top: 1px dashed #e9d5ff; font-size: 12px; color: #6b21a8; display: flex; align-items: center; gap: 6px;">
                    <i class="fas fa-map-pin"></i> ${escapeHtml(interviewDetails.location)}
                </div>
                ` : ''}
                ` : `
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #9333ea; font-size: 16px; background: #f3e8ff; flex-shrink: 0;">
                        <i class="fas fa-calendar-plus"></i>
                    </div>
                    <div>
                        <h4 style="margin: 0; font-size: 13px; font-weight: 600; color: #0f172a;">No interview scheduled</h4>
                        <p style="margin: 2px 0 0; font-size: 11px; color: #6b21a8;">Please set an interview schedule.</p>
                    </div>
                </div>
                `}
                <div style="margin-top: 8px;">
                    <button type="button" class="btn btn-primary" style="background: #9333ea; border-color: #9333ea; font-size: 11px; padding: 6px 12px;" onclick="closeModal(); showFullCalendar('${app.id}')">
                        <i class="fas fa-calendar-${hasInterview ? 'edit' : 'plus'}"></i> ${hasInterview ? 'Update Schedule' : 'Schedule Interview'}
                    </button>
                </div>
            </div>
            
        </div>
        ` : ''}

        <!-- Action Buttons -->
        <div class="modal-footer" style="flex-wrap: wrap; margin-top: 10px;">
            <button type="button" class="btn btn-secondary" onclick="closeModal()">
                <i class="fas fa-times"></i> Close
            </button>
            <button type="button" class="btn btn-info" onclick="closeModal(); editApplicant('${id}');">
                <i class="fas fa-edit"></i> Edit
            </button>
            ${app.applicationStatus !== 'Hired' && app.applicationStatus !== 'Rejected' ? `
            <button type="button" class="btn btn-success" onclick="closeModal(); createOfferFromApplicant('${id}');">
                <i class="fas fa-file-signature"></i> Create Offer
            </button>
            ` : ''}
        </div>
    `;
    openModal('Applicant Details', content);
}

// Show full calendar modal for scheduling
function showFullCalendar(applicantId) {
    const app = window.applicants.find(a => a.id === applicantId);
    if (!app) return;
    
    const existingInterview = app.interviewDetails || null;
    
    const content = `
        <style>
            .calendar-modal * { margin: 0; box-sizing: border-box; }
            .calendar-modal { font-family: 'Inter', sans-serif; max-width: 500px; width: 100%; }
            .calendar-full { background: white; border-radius: 20px; padding: 20px; }
            .weekdays { display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px; margin-bottom: 8px; }
            .weekday { text-align: center; font-size: 0.7rem; font-weight: 600; color: #64748b; padding: 8px 0; }
            .days-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px; }
            .day-cell { text-align: center; padding: 10px 0; border-radius: 12px; cursor: pointer; transition: all 0.2s; font-size: 0.85rem; }
            .day-cell:hover { background: #f3e8ff; }
            .day-cell.selected { background: #9333ea; color: white; }
            .day-cell.today { border: 2px solid #9333ea; }
            .time-selector { margin-top: 24px; }
            .time-selector label { display: block; font-size: 0.8rem; font-weight: 600; color: #475569; margin-bottom: 8px; }
            .time-selector select, .time-selector input { width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 0.9rem; margin-bottom: 12px; }
            .interview-type-selector { margin-top: 16px; }
            .type-options { display: flex; gap: 10px; margin-top: 8px; }
            .type-option { flex: 1; padding: 12px; border: 1.5px solid #e2e8f0; border-radius: 12px; text-align: center; cursor: pointer; transition: all 0.2s; }
            .type-option.selected { border-color: #9333ea; background: #f8f4ff; }
            .type-option i { font-size: 20px; margin-bottom: 6px; display: block; color: #9333ea; }
            .contact-preview { margin-top: 20px; padding: 16px; background: #f8fafc; border-radius: 16px; }
            .contact-preview p { display: flex; align-items: center; gap: 8px; margin-bottom: 8px; }
            .contact-preview i { color: #9333ea; width: 20px; }
            .modal-buttons { display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; }
            .btn-save-schedule { background: #9333ea; color: white; border: none; padding: 12px 24px; border-radius: 24px; font-size: 0.9rem; font-weight: 500; cursor: pointer; }
            .btn-save-schedule:hover { background: #7e22ce; }
            .btn-cancel-schedule { background: white; color: #475569; border: 1px solid #e2e8f0; padding: 12px 24px; border-radius: 24px; font-size: 0.9rem; cursor: pointer; }
        </style>
        <div class="calendar-modal">
            <div class="calendar-full">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid #f1f5f9;">
                    <img src="${app.profilePhoto || '/3ME/assets/images/default-avatar.png'}" style="width:40px;height:40px;object-fit:cover;border-radius:12px;flex-shrink:0;" />
                    <div>
                        <div style="font-weight: 600;">${escapeHtml(app.firstname)} ${escapeHtml(app.surname)}</div>
                        <div style="font-size: 0.75rem; color: #64748b;">${escapeHtml(app.requisitionTitle)}</div>
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
                        <div class="type-option ${existingInterview?.interviewType === 'Virtual' || !existingInterview ? 'selected' : ''}" data-type="Virtual" onclick="selectInterviewType(this)">
                            <i class="fas fa-video"></i> Virtual
                        </div>
                        <div class="type-option ${existingInterview?.interviewType === 'Phone' ? 'selected' : ''}" data-type="Phone" onclick="selectInterviewType(this)">
                            <i class="fas fa-phone"></i> Phone
                        </div>
                        <div class="type-option ${existingInterview?.interviewType === 'In-Person' ? 'selected' : ''}" data-type="In-Person" onclick="selectInterviewType(this)">
                            <i class="fas fa-building"></i> In-Person
                        </div>
                    </div>
                    <input type="text" id="interviewLocation" placeholder="Meeting link, phone number, or address" value="${escapeHtml(existingInterview?.location || '')}" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 0.9rem; margin-top: 12px;">
                </div>
                
                <div class="contact-preview">
                    <p><i class="fas fa-phone"></i> <strong>Contact:</strong> ${escapeHtml(app.contactNumber)}</p>
                    <p><i class="fas fa-envelope"></i> <strong>Email:</strong> ${escapeHtml(app.email)}</p>
                </div>
                
                <div class="modal-buttons">
                    <button type="button" class="btn-cancel-schedule" onclick="closeModal(); viewApplicant('${app.id}');">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="button" class="btn-save-schedule" onclick="saveInterviewSchedule('${app.id}')">
                        <i class="fas fa-check"></i> Schedule Interview
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

function selectInterviewType(element) {
    document.querySelectorAll('.type-option').forEach(opt => opt.classList.remove('selected'));
    element.classList.add('selected');
}

function saveInterviewSchedule(applicantId) {
    const app = window.applicants.find(a => a.id === applicantId);
    if (!app) return;
    
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
    
    // Format for MySQL datetime: YYYY-MM-DD HH:MM:00
    const formattedMonth = (date.getMonth() + 1).toString().padStart(2, '0');
    const formattedDay = selectedDay.padStart(2, '0');
    const mysqlDateTime = `${date.getFullYear()}-${formattedMonth}-${formattedDay} ${timeValue}:00`;
    
    const payload = {
        id: app.id,
        application_status: 'Interview Scheduled',
        interview_date: mysqlDateTime,
        interview_type: interviewType,
        interview_location: location
    };

    fetch('/3ME/api/recruitment/schedule_interview.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Save interview details locally
            app.interviewDetails = {
                interviewDate: interviewDate,
                interviewTime: displayTime,
                interviewType: interviewType,
                location: location
            };
            
            app.applicationStatus = 'Interview Scheduled';
            
            // Refresh table
            if (typeof renderApplicantTable === 'function') {
                renderApplicantTable(window.applicants);
            }
            
            closeModal();
            showToast(`Interview scheduled for ${interviewDate} at ${displayTime}`, 'success');
            
            // Refresh view
            setTimeout(() => {
                viewApplicant(applicantId);
            }, 100);
        } else {
            showToast(data.message || 'Failed to schedule interview', 'error');
        }
    })
    .catch(err => {
        console.error('Error scheduling interview:', err);
        showToast('Error scheduling interview', 'error');
    });
}

function callApplicant(applicantId, phoneNumber) {
    if (phoneNumber && phoneNumber !== '+63 XXX XXX XXXX') {
        showToast(`Initiating call to ${phoneNumber}...`, 'info');
    } else {
        showToast('No contact number available', 'warning');
    }
}

function messageApplicant(applicantId, phoneNumber) {
    if (phoneNumber && phoneNumber !== '+63 XXX XXX XXXX') {
        showToast(`Opening SMS to ${phoneNumber}...`, 'info');
    } else {
        showToast('No contact number available', 'warning');
    }
}

function emailApplicant(applicantId, email) {
    if (email) {
        const app = window.applicants.find(a => a.id === applicantId);
        const subject = `Interview Schedule - ${app?.requisitionTitle || 'Job Application'}`;
        showToast(`Opening email to ${email}...`, 'info');
    } else {
        showToast('No email available', 'warning');
    }
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
</script>