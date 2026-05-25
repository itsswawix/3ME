<!-- modal-add-requisition.php -->
<script>
function openAddRequisitionModal() {
    const content = `
        <style>
            .modal-add-requisition * { margin: 0; box-sizing: border-box; }
            .modal-add-requisition { font-family: 'Inter', sans-serif; max-width: 600px; width: 100%; }
            .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px 24px; }
            .full-width { grid-column: span 2; }
            .form-group { display: flex; flex-direction: column; margin-bottom: 4px; }
            .form-group label { font-size: 0.85rem; font-weight: 500; margin-bottom: 6px; color: #475569; letter-spacing: 0.3px; }
            .form-group input, .form-group select, .form-group textarea { 
                padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 16px;
                font-size: 0.9rem; background: #ffffff; font-family: 'Inter', sans-serif;
                transition: all 0.2s ease; box-shadow: 0 1px 2px rgba(0,0,0,0.02);
            }
            .form-group input:focus, .form-group select:focus, .form-group textarea:focus { 
                outline: none; border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
            }
            .section-title { 
                grid-column: span 2; font-size: 1rem; font-weight: 600; margin: 20px 0 8px;
                padding-bottom: 8px; border-bottom: 1.5px solid #e2e8f0; color: #0f172a;
                display: flex; align-items: center; gap: 8px;
            }
            .section-title i { color: #4f46e5; font-size: 0.9rem; width: 20px; }
            .section-title:first-of-type { margin-top: 0; }
            .required-star { color: #ef4444; margin-left: 2px; }
            textarea { resize: vertical; min-height: 80px; }
            .modal-footer-note { font-size: 0.75rem; color: #94a3b8; margin-top: 16px; text-align: right; }
            .salary-note { font-size: 0.7rem; color: #64748b; margin-top: 4px; }
            .modal-buttons { 
                display: flex; 
                justify-content: flex-end; 
                gap: 12px; 
                margin-top: 28px; 
                padding-top: 20px; 
                border-top: 1px solid #f1f5f9; 
            }
            .btn-save { 
                background: #4f46e5; 
                color: white; 
                border: none; 
                padding: 10px 22px; 
                border-radius: 24px; 
                font-size: 0.85rem; 
                font-weight: 500; 
                cursor: pointer; 
                transition: all 0.2s; 
                display: flex;
                align-items: center;
                gap: 6px;
                box-shadow: 0 4px 8px rgba(79, 70, 229, 0.2);
            }
            .btn-save:hover { 
                background: #4338ca; 
                transform: translateY(-1px);
                box-shadow: 0 6px 12px rgba(79, 70, 229, 0.25);
            }
            .btn-cancel { 
                background: white; 
                color: #475569; 
                border: 1px solid #e2e8f0; 
                padding: 10px 22px; 
                border-radius: 24px; 
                font-size: 0.85rem; 
                font-weight: 500; 
                cursor: pointer; 
                transition: all 0.2s; 
                display: flex;
                align-items: center;
                gap: 6px;
            }
            .btn-cancel:hover { 
                background: #f8fafc; 
                border-color: #cbd5e1;
            }
        </style>
        <div class="modal-add-requisition">
            <form id="addRequisitionForm" onsubmit="event.preventDefault(); saveNewRequisition();">
                <div class="form-grid">
                    <div class="section-title"><i class="fas fa-briefcase"></i> Position Information</div>
                    
                    <div class="form-group full-width">
                        <label>Job Title <span class="required-star">*</span></label>
                        <input type="text" id="newJobTitle" required placeholder="e.g., Senior Software Engineer">
                    </div>
                    
                    <div class="form-group">
                        <label>Department <span class="required-star">*</span></label>
                        <select id="newDepartment" required>
                            <option value="">Select Department</option>
                            <option value="Engineering">Engineering</option>
                            <option value="Product">Product</option>
                            <option value="Marketing">Marketing</option>
                            <option value="Sales">Sales</option>
                            <option value="HR">Human Resources</option>
                            <option value="Finance">Finance</option>
                            <option value="IT">Information Technology</option>
                            <option value="Operations">Operations</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Company <span class="required-star">*</span></label>
                        <input type="text" id="newCompany" required value="NovaCore Solutions Inc.">
                    </div>
                    
                    <div class="section-title"><i class="fas fa-info-circle"></i> Employment Details</div>
                    
                    <div class="form-group">
                        <label>Employment Type <span class="required-star">*</span></label>
                        <select id="newEmploymentType" required>
                            <option value="">Select Type</option>
                            <option value="Full-time">Full-time</option>
                            <option value="Part-time">Part-time</option>
                            <option value="Contract">Contract</option>
                            <option value="Internship">Internship</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Position Level <span class="required-star">*</span></label>
                        <select id="newPositionLevel" required>
                            <option value="">Select Level</option>
                            <option value="Entry Level">Entry Level</option>
                            <option value="Junior">Junior</option>
                            <option value="Mid-Level">Mid-Level</option>
                            <option value="Senior">Senior</option>
                            <option value="Lead">Lead</option>
                            <option value="Manager">Manager</option>
                            <option value="Director">Director</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Vacancies <span class="required-star">*</span></label>
                        <input type="number" id="newVacancies" required min="1" value="1">
                    </div>
                    
                    <div class="form-group">
                        <label>Requested Start Date</label>
                        <input type="date" id="newStartDate">
                    </div>
                    
                    <div class="section-title"><i class="fas fa-dollar-sign"></i> Compensation</div>
                    
                    <div class="form-group">
                        <label>Salary Range Min (₱)</label>
                        <input type="number" id="newSalaryMin" step="0.01" placeholder="Minimum salary">
                        <div class="salary-note">Monthly gross salary</div>
                    </div>
                    
                    <div class="form-group">
                        <label>Salary Range Max (₱)</label>
                        <input type="number" id="newSalaryMax" step="0.01" placeholder="Maximum salary">
                        <div class="salary-note">Monthly gross salary</div>
                    </div>
                    
                    <div class="form-group full-width">
                        <label>Budget Code</label>
                        <input type="text" id="newBudgetCode" placeholder="e.g., BUD-ENG-2024">
                    </div>
                    
                    <div class="section-title"><i class="fas fa-file-alt"></i> Job Description & Requirements</div>
                    
                    <div class="form-group full-width">
                        <label>Required Skills <span class="required-star">*</span></label>
                        <textarea id="newRequiredSkills" required placeholder="List required skills, separated by commas (e.g., JavaScript, React, Node.js, AWS)"></textarea>
                    </div>
                    
                    <div class="form-group full-width">
                        <label>Job Description <span class="required-star">*</span></label>
                        <textarea id="newJobDescription" required rows="4" placeholder="Detailed job description, responsibilities and qualifications..."></textarea>
                    </div>
                    
                    <div class="section-title"><i class="fas fa-check-circle"></i> Approval</div>
                    
                    <div class="form-group">
                        <label>Approver</label>
                        <input type="text" id="newApprover" placeholder="Approver name" value="Maria Santos">
                    </div>
                    
                    <div class="form-group">
                        <label>Status <span class="required-star">*</span></label>
                        <select id="newStatus" required>
                            <option value="Draft">Draft</option>
                            <option value="Pending" selected>Pending Approval</option>
                            <option value="Approved">Approved</option>
                        </select>
                    </div>
                </div>
                
                
                <div class="modal-buttons">
                    <button type="button" class="btn-cancel" onclick="closeAddRequisitionModal()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i> Create Requisition
                    </button>
                </div>
            </form>
        </div>
    `;
    
    openModal('New Job Requisition', content);
    
    // Set default start date to 30 days from now
    const today = new Date();
    today.setDate(today.getDate() + 30);
    document.getElementById('newStartDate').value = today.toISOString().split('T')[0];
}

function closeAddRequisitionModal() {
    if (typeof closeModal === 'function') {
        closeModal();
    }
}

function saveNewRequisition() {
    const jobTitle = document.getElementById('newJobTitle')?.value.trim();
    const department = document.getElementById('newDepartment')?.value;
    const requiredSkills = document.getElementById('newRequiredSkills')?.value.trim();
    const jobDescription = document.getElementById('newJobDescription')?.value.trim();
    
    if (!jobTitle || !department || !requiredSkills || !jobDescription) {
        showToast('Please fill in all required fields', 'warning');
        return;
    }
    
    const newId = 'REQ-2024-' + String(window.requisitions.length + 1).padStart(3, '0');
    
    const newReq = {
        id: newId,
        jobTitle: jobTitle,
        department: department,
        requiredSkills: requiredSkills,
        vacancies: parseInt(document.getElementById('newVacancies')?.value || '1'),
        filledPositions: 0,
        jobDescription: jobDescription,
        budgetCode: document.getElementById('newBudgetCode')?.value || '',
        positionLevel: document.getElementById('newPositionLevel')?.value || '',
        company: document.getElementById('newCompany')?.value || 'NovaCore Solutions Inc.',
        employmentType: document.getElementById('newEmploymentType')?.value || 'Full-time',
        salaryRangeMin: parseFloat(document.getElementById('newSalaryMin')?.value) || null,
        salaryRangeMax: parseFloat(document.getElementById('newSalaryMax')?.value) || null,
        requestedStartDate: document.getElementById('newStartDate')?.value || '',
        status: document.getElementById('newStatus')?.value || 'Pending',
        submissionDate: new Date().toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' }),
        approver: document.getElementById('newApprover')?.value || ''
    };
    
    window.requisitions.push(newReq);
    
    if (typeof renderRequisitionTable === 'function') {
        renderRequisitionTable(window.requisitions);
    }
    
    if (typeof closeModal === 'function') {
        closeModal();
    }
    
    showToast(`Requisition ${newId} created successfully!`, 'success');
}
</script>