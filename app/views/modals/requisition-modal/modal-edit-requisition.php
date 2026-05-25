<!-- modal-edit-requisition.php -->
<script>
function editRequisition(id) {
    const req = window.requisitions.find(r => r.id === id);
    if (!req) return;
    
    let startDateValue = '';
    if (req.requestedStartDate) {
        startDateValue = req.requestedStartDate;
    }
    
    const content = `
        <style>
            .modal-edit-requisition * { margin: 0; box-sizing: border-box; }
            .modal-edit-requisition { font-family: 'Inter', sans-serif; max-width: 600px; width: 100%; }
            .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px 24px; }
            .full-width { grid-column: span 2; }
            .form-group { display: flex; flex-direction: column; margin-bottom: 4px; }
            .form-group label { font-size: 0.85rem; font-weight: 500; margin-bottom: 6px; color: #475569; }
            .form-group input, .form-group select, .form-group textarea { 
                padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 16px;
                font-size: 0.9rem; background: #ffffff; font-family: 'Inter', sans-serif;
            }
            .form-group input:focus, .form-group select:focus, .form-group textarea:focus { 
                outline: none; border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
            }
            .section-title { 
                grid-column: span 2; font-size: 1rem; font-weight: 600; margin: 20px 0 8px;
                padding-bottom: 8px; border-bottom: 1.5px solid #e2e8f0; color: #0f172a;
                display: flex; align-items: center; gap: 8px;
            }
            .section-title i { color: #4f46e5; }
            .required-star { color: #ef4444; }
            textarea { resize: vertical; min-height: 80px; }
            .req-id-badge { 
                background: linear-gradient(145deg, #f8fafc, #f1f5f9); padding: 8px 14px;
                border-radius: 12px; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;
            }
            .req-id-badge i { color: #4f46e5; }
            .req-id-badge strong { color: #0f172a; font-weight: 600; }
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
        <div class="modal-edit-requisition">
            <div class="req-id-badge">
                <i class="fas fa-hashtag"></i>
                <span>Editing Requisition:</span>
                <strong>${escapeHtml(req.id)}</strong>
            </div>
            <form id="editRequisitionForm" onsubmit="event.preventDefault(); updateRequisition('${id}');">
                <div class="form-grid">
                    <div class="section-title"><i class="fas fa-briefcase"></i> Position Information</div>
                    
                    <div class="form-group full-width">
                        <label>Job Title <span class="required-star">*</span></label>
                        <input type="text" id="editJobTitle" required value="${escapeHtml(req.jobTitle)}">
                    </div>
                    
                    <div class="form-group">
                        <label>Department <span class="required-star">*</span></label>
                        <select id="editDepartment" required>
                            ${['Engineering','Product','Marketing','Sales','HR','Finance','IT','Operations'].map(d => 
                                `<option value="${d}" ${req.department === d ? 'selected' : ''}>${d}</option>`
                            ).join('')}
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Company <span class="required-star">*</span></label>
                        <input type="text" id="editCompany" required value="${escapeHtml(req.company)}">
                    </div>
                    
                    <div class="section-title"><i class="fas fa-info-circle"></i> Employment Details</div>
                    
                    <div class="form-group">
                        <label>Employment Type <span class="required-star">*</span></label>
                        <select id="editEmploymentType" required>
                            ${['Full-time','Part-time','Contract','Internship'].map(t => 
                                `<option value="${t}" ${req.employmentType === t ? 'selected' : ''}>${t}</option>`
                            ).join('')}
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Position Level <span class="required-star">*</span></label>
                        <select id="editPositionLevel" required>
                            ${['Entry Level','Junior','Mid-Level','Senior','Lead','Manager','Director'].map(l => 
                                `<option value="${l}" ${req.positionLevel === l ? 'selected' : ''}>${l}</option>`
                            ).join('')}
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Vacancies <span class="required-star">*</span></label>
                        <input type="number" id="editVacancies" required min="1" value="${req.vacancies}">
                    </div>
                    
                    <div class="form-group">
                        <label>Filled Positions</label>
                        <input type="number" id="editFilledPositions" min="0" value="${req.filledPositions || 0}">
                    </div>
                    
                    <div class="form-group">
                        <label>Requested Start Date</label>
                        <input type="date" id="editStartDate" value="${startDateValue}">
                    </div>
                    
                    <div class="section-title"><i class="fas fa-dollar-sign"></i> Compensation</div>
                    
                    <div class="form-group">
                        <label>Salary Range Min (₱)</label>
                        <input type="number" id="editSalaryMin" step="0.01" value="${req.salaryRangeMin || ''}">
                        <div class="salary-note">Monthly gross salary</div>
                    </div>
                    
                    <div class="form-group">
                        <label>Salary Range Max (₱)</label>
                        <input type="number" id="editSalaryMax" step="0.01" value="${req.salaryRangeMax || ''}">
                        <div class="salary-note">Monthly gross salary</div>
                    </div>
                    
                    <div class="form-group full-width">
                        <label>Budget Code</label>
                        <input type="text" id="editBudgetCode" value="${escapeHtml(req.budgetCode || '')}">
                    </div>
                    
                    <div class="section-title"><i class="fas fa-file-alt"></i> Job Description & Requirements</div>
                    
                    <div class="form-group full-width">
                        <label>Required Skills <span class="required-star">*</span></label>
                        <textarea id="editRequiredSkills" required>${escapeHtml(req.requiredSkills)}</textarea>
                    </div>
                    
                    <div class="form-group full-width">
                        <label>Job Description <span class="required-star">*</span></label>
                        <textarea id="editJobDescription" required rows="4">${escapeHtml(req.jobDescription)}</textarea>
                    </div>
                    
                    <div class="section-title"><i class="fas fa-check-circle"></i> Approval</div>
                    
                    <div class="form-group">
                        <label>Approver</label>
                        <input type="text" id="editApprover" value="${escapeHtml(req.approver || '')}">
                    </div>
                    
                    <div class="form-group">
                        <label>Status <span class="required-star">*</span></label>
                        <select id="editStatus" required>
                            ${['Draft','Pending','Approved','Rejected','Filled','Cancelled'].map(s => 
                                `<option value="${s}" ${req.status === s ? 'selected' : ''}>${s === 'Pending' ? 'Pending Approval' : s}</option>`
                            ).join('')}
                        </select>
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
    
    openModal('Edit Job Requisition', content);
}

function updateRequisition(id) {
    const index = window.requisitions.findIndex(r => r.id === id);
    if (index === -1) return;
    
    const jobTitle = document.getElementById('editJobTitle')?.value;
    const department = document.getElementById('editDepartment')?.value;
    const requiredSkills = document.getElementById('editRequiredSkills')?.value;
    const jobDescription = document.getElementById('editJobDescription')?.value;
    
    if (!jobTitle || !department || !requiredSkills || !jobDescription) {
        showToast('Please fill in all required fields', 'warning');
        return;
    }
    
    const updatedReq = {
        ...window.requisitions[index],
        jobTitle: jobTitle,
        department: department,
        requiredSkills: requiredSkills,
        vacancies: parseInt(document.getElementById('editVacancies')?.value || '1'),
        filledPositions: parseInt(document.getElementById('editFilledPositions')?.value) || 0,
        jobDescription: jobDescription,
        budgetCode: document.getElementById('editBudgetCode')?.value || '',
        positionLevel: document.getElementById('editPositionLevel')?.value || '',
        company: document.getElementById('editCompany')?.value || 'NovaCore Solutions Inc.',
        employmentType: document.getElementById('editEmploymentType')?.value || 'Full-time',
        salaryRangeMin: parseFloat(document.getElementById('editSalaryMin')?.value) || null,
        salaryRangeMax: parseFloat(document.getElementById('editSalaryMax')?.value) || null,
        requestedStartDate: document.getElementById('editStartDate')?.value || '',
        status: document.getElementById('editStatus')?.value || 'Pending',
        approver: document.getElementById('editApprover')?.value || ''
    };
    
    window.requisitions[index] = updatedReq;
    
    if (typeof renderRequisitionTable === 'function') {
        renderRequisitionTable(window.requisitions);
    }
    
    if (typeof closeModal === 'function') {
        closeModal();
    }
    
    showToast('Requisition updated successfully!', 'success');
}
</script>