<!-- modal-add-roster.php -->
<script>
async function openAddRosterModal() {
    // Load companies from database
    const companies = await window.loadCompaniesFromDB();
    
    const companyOptions = companies.map(c => 
        `<option value="${c.id}">${escapeHtml(c.name)}</option>`
    ).join('');
    
    const content = `
        <style>
            .time-preview {
                background: #f8fafc;
                border: 1px solid #e2e8f0;
                border-radius: 8px;
                padding: 12px;
                margin-top: 12px;
                text-align: center;
            }
            .time-preview-label {
                font-size: 11px;
                color: #64748b;
                margin-bottom: 4px;
            }
            .time-preview-value {
                font-size: 16px;
                font-weight: 600;
                color: #4f46e5;
            }
        </style>
        <form id="addRosterForm" onsubmit="handleAddRoster(event)">
            <div class="form-group">
                <label>Shift Name <span class="required-star">*</span></label>
                <input type="text" id="shiftName" name="shiftName" placeholder="e.g., Morning Shift, Night Shift" required>
            </div>
            
            <div class="form-group">
                <label>Company <span class="required-star">*</span></label>
                <select id="companyId" name="companyId" required>
                    <option value="">Select Company</option>
                    ${companyOptions}
                </select>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Start Time <span class="required-star">*</span></label>
                    <input type="time" id="startTime" name="startTime" required onchange="updateDurationPreview()">
                </div>
                <div class="form-group">
                    <label>End Time <span class="required-star">*</span></label>
                    <input type="time" id="endTime" name="endTime" required onchange="updateDurationPreview()">
                </div>
            </div>
            
            <div class="time-preview" id="durationPreview" style="display: none;">
                <div class="time-preview-label">Shift Duration</div>
                <div class="time-preview-value" id="durationValue">0h 0m</div>
            </div>
            
            <div class="form-group">
                <label>Break Duration (minutes)</label>
                <input type="number" id="breakDuration" name="breakDuration" placeholder="e.g., 60" min="0" value="60">
            </div>
            
            <div class="form-group">
                <label>Overtime Rule <span class="required-star">*</span></label>
                <select id="overtimeRule" name="overtimeRule" required>
                    <option value="">Select Overtime Rule</option>
                    <option value="After 8 hours - 1.25x rate">After 8 hours - 1.25x rate</option>
                    <option value="After 9 hours - 1.5x rate">After 9 hours - 1.5x rate</option>
                    <option value="After 10 hours - 2x rate">After 10 hours - 2x rate</option>
                    <option value="Weekend - 1.5x rate">Weekend - 1.5x rate</option>
                    <option value="Holiday - 2x rate">Holiday - 2x rate</option>
                    <option value="No overtime allowed">No overtime allowed</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Late Grace Period (minutes)</label>
                <input type="number" id="lateGracePeriod" name="lateGracePeriod" placeholder="e.g., 15" min="0" value="15">
            </div>
            
            <div class="form-group">
                <label>Effective Date <span class="required-star">*</span></label>
                <input type="date" id="effectiveDate" name="effectiveDate" required>
            </div>
            
            <div class="form-group">
                <label>Notes</label>
                <textarea id="notes" name="notes" placeholder="Additional notes about this roster..." rows="3"></textarea>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Create Roster
                </button>
            </div>
        </form>
    `;
    
    openModal('Create New Roster', content);
    
    // Set default effective date to today
    setTimeout(() => {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('effectiveDate').value = today;
    }, 100);
}

function updateDurationPreview() {
    const startTime = document.getElementById('startTime').value;
    const endTime = document.getElementById('endTime').value;
    const preview = document.getElementById('durationPreview');
    const durationValue = document.getElementById('durationValue');
    
    if (startTime && endTime) {
        const duration = calculateDuration(startTime, endTime);
        durationValue.textContent = duration;
        preview.style.display = 'block';
    } else {
        preview.style.display = 'none';
    }
}

function handleAddRoster(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const errors = [];
    
    // Validation
    if (!formData.get('shiftName').trim()) {
        errors.push('Shift name is required');
    }
    if (!formData.get('companyId')) {
        errors.push('Company is required');
    }
    if (!formData.get('startTime')) {
        errors.push('Start time is required');
    }
    if (!formData.get('endTime')) {
        errors.push('End time is required');
    }
    if (!formData.get('overtimeRule')) {
        errors.push('Overtime rule is required');
    }
    if (!formData.get('effectiveDate')) {
        errors.push('Effective date is required');
    }
    
    if (errors.length > 0) {
        showValidationErrors(errors);
        return;
    }
    
    // Prepare data for API
    const rosterData = {
        shiftName: formData.get('shiftName'),
        companyId: formData.get('companyId'),
        startTime: formData.get('startTime'),
        endTime: formData.get('endTime'),
        breakDuration: parseInt(formData.get('breakDuration')) || 0,
        overtimeRule: formData.get('overtimeRule'),
        lateGracePeriod: parseInt(formData.get('lateGracePeriod')) || 0,
        effectiveDate: formData.get('effectiveDate'),
        notes: formData.get('notes') || '',
        createdBy: 'Current User'
    };
    
    // Send to API
    fetch('/3ME/api/attendance/rosters.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(rosterData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload rosters from API
            loadAttendanceData();
            closeModal(true);
            showToast('Roster created successfully!', 'success');
        } else {
            showToast(data.message || 'Error creating roster', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error creating roster', 'error');
    });
}

// Make function globally available
window.openAddRosterModal = openAddRosterModal;
window.updateDurationPreview = updateDurationPreview;
window.handleAddRoster = handleAddRoster;
</script>
