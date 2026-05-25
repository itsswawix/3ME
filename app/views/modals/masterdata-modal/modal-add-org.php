<!-- modal-add-org.php -->
<script>
window.openAddOrgModal = function(type, parentId, parentName) {
    type = type || 'company';
    if (type === 'company') {
        openAddCompanyModal();
    } else if (type === 'department') {
        openAddDepartmentModal(parentId);
    } else if (type === 'position') {
        openAddPositionModal(parentId, parentName);
    }
}

window.openAddCompanyModal = function() {
    var content = '<div class="modal-add-org">' +
        '<form id="addCompanyForm" onsubmit="saveNewCompany(event); return false;">' +
            '<div style="margin-bottom: 20px;">' +
                '<h3 style="font-size: 16px; font-weight: 600; margin-bottom: 16px; color: #0f172a; display: flex; align-items: center; gap: 8px;"><i class="fas fa-building"></i> Company Information</h3>' +
                '<div style="margin-bottom: 16px;">' +
                    '<label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 6px; color: #475569;">Company Name <span style="color: #ef4444;">*</span></label>' +
                    '<input type="text" id="newCompanyName" required placeholder="e.g., 3ME Corporation" style="width: 100%; padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 13px;">' +
                '</div>' +
                '<div style="margin-bottom: 16px;">' +
                    '<label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 6px; color: #475569;">Address</label>' +
                    '<textarea id="newCompanyAddress" placeholder="Company address" rows="2" style="width: 100%; padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 13px; resize: vertical;"></textarea>' +
                '</div>' +
                '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">' +
                    '<div>' +
                        '<label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 6px; color: #475569;">Contact Number</label>' +
                        '<input type="tel" id="newCompanyPhone" placeholder="e.g., +63 XXX XXX XXXX" style="width: 100%; padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 13px;">' +
                    '</div>' +
                    '<div>' +
                        '<label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 6px; color: #475569;">Email</label>' +
                        '<input type="email" id="newCompanyEmail" placeholder="e.g., info@company.com" style="width: 100%; padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 13px;">' +
                    '</div>' +
                '</div>' +
                '<div>' +
                    '<label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 6px; color: #475569;">Status</label>' +
                    '<select id="newCompanyStatus" style="width: 100%; padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 13px;">' +
                        '<option value="Active">Active</option>' +
                        '<option value="Inactive">Inactive</option>' +
                    '</select>' +
                '</div>' +
            '</div>' +
            '<div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; padding-top: 20px; border-top: 1px solid #f1f5f9;">' +
                '<button type="button" onclick="closeModal()" style="background: white; color: #475569; border: 1px solid #e2e8f0; padding: 10px 22px; border-radius: 24px; cursor: pointer; display: flex; align-items: center; gap: 6px;"><i class="fas fa-times"></i> Cancel</button>' +
                '<button type="submit" style="background: #4f46e5; color: white; border: none; padding: 10px 22px; border-radius: 24px; cursor: pointer; display: flex; align-items: center; gap: 6px;"><i class="fas fa-save"></i> Save Company</button>' +
            '</div>' +
        '</form>' +
    '</div>';
    
    openModal('Add Company', content);
}

window.openAddDepartmentModal = function(companyId) {
    var content = '<div class="modal-add-org">' +
        '<form id="addDepartmentForm" onsubmit="saveNewDepartment(event, \'' + companyId + '\'); return false;">' +
            '<div style="margin-bottom: 20px;">' +
                '<h3 style="font-size: 16px; font-weight: 600; margin-bottom: 16px; color: #0f172a; display: flex; align-items: center; gap: 8px;"><i class="fas fa-sitemap"></i> Department Information</h3>' +
                '<div style="margin-bottom: 16px;">' +
                    '<label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 6px; color: #475569;">Department Name <span style="color: #ef4444;">*</span></label>' +
                    '<input type="text" id="newDeptName" required placeholder="e.g., Engineering" style="width: 100%; padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 13px;">' +
                '</div>' +
                '<div style="margin-bottom: 16px;">' +
                    '<label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 6px; color: #475569;">Department Head</label>' +
                    '<input type="text" id="newDeptHead" placeholder="e.g., John Doe" style="width: 100%; padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 13px;">' +
                '</div>' +
                '<div>' +
                    '<label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 6px; color: #475569;">Status</label>' +
                    '<select id="newDeptStatus" style="width: 100%; padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 13px;">' +
                        '<option value="Active">Active</option>' +
                        '<option value="Inactive">Inactive</option>' +
                    '</select>' +
                '</div>' +
            '</div>' +
            '<div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; padding-top: 20px; border-top: 1px solid #f1f5f9;">' +
                '<button type="button" onclick="closeModal()" style="background: white; color: #475569; border: 1px solid #e2e8f0; padding: 10px 22px; border-radius: 24px; cursor: pointer; display: flex; align-items: center; gap: 6px;"><i class="fas fa-times"></i> Cancel</button>' +
                '<button type="submit" style="background: #4f46e5; color: white; border: none; padding: 10px 22px; border-radius: 24px; cursor: pointer; display: flex; align-items: center; gap: 6px;"><i class="fas fa-save"></i> Save Department</button>' +
            '</div>' +
        '</form>' +
    '</div>';
    
    openModal('Add Department', content);
}

window.openAddPositionModal = function(departmentId, departmentName) {
    var deptNameEscaped = (departmentName || '').replace(/'/g, "\\'");
    var content = '<div class="modal-add-org">' +
        '<div style="background: #f1f5f9; padding: 8px 14px; border-radius: 12px; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; font-size: 13px; color: #64748b;">' +
            '<i class="fas fa-sitemap"></i> Department: <strong>' + escapeHtml(departmentName || '') + '</strong>' +
        '</div>' +
        '<form id="addPositionForm" onsubmit="saveNewPosition(event, \'' + departmentId + '\'); return false;">' +
            '<div style="margin-bottom: 20px;">' +
                '<h3 style="font-size: 16px; font-weight: 600; margin-bottom: 16px; color: #0f172a; display: flex; align-items: center; gap: 8px;"><i class="fas fa-briefcase"></i> Position Information</h3>' +
                '<div style="margin-bottom: 16px;">' +
                    '<label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 6px; color: #475569;">Job Title <span style="color: #ef4444;">*</span></label>' +
                    '<input type="text" id="newPosTitle" required placeholder="e.g., Senior Software Engineer" style="width: 100%; padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 13px;">' +
                '</div>' +
                '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">' +
                    '<div>' +
                        '<label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 6px; color: #475569;">Level <span style="color: #ef4444;">*</span></label>' +
                        '<select id="newPosLevel" required style="width: 100%; padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 13px;">' +
                            '<option value="">Select Level</option>' +
                            '<option value="Director">Director</option>' +
                            '<option value="Manager">Manager</option>' +
                            '<option value="Senior">Senior</option>' +
                            '<option value="Mid-Level">Mid-Level</option>' +
                            '<option value="Junior">Junior</option>' +
                            '<option value="Entry">Entry</option>' +
                        '</select>' +
                    '</div>' +
                    '<div>' +
                        '<label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 6px; color: #475569;">Vacancies</label>' +
                        '<input type="number" id="newPosVacancies" min="0" value="0" placeholder="0" style="width: 100%; padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 13px;">' +
                    '</div>' +
                '</div>' +
                '<div style="margin-bottom: 16px;">' +
                    '<label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 6px; color: #475569;">Reports To</label>' +
                    '<input type="text" id="newPosReportsTo" placeholder="e.g., Engineering Manager" style="width: 100%; padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 13px;">' +
                '</div>' +
                '<h3 style="font-size: 16px; font-weight: 600; margin: 20px 0 16px; padding-top: 20px; border-top: 1px solid #e2e8f0; color: #0f172a; display: flex; align-items: center; gap: 8px;"><i class="fas fa-dollar-sign"></i> Salary Offer</h3>' +
                '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">' +
                    '<div>' +
                        '<label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 6px; color: #475569;">Minimum Salary (₱)</label>' +
                        '<input type="number" id="newPosSalaryMin" min="0" step="0.01" placeholder="0.00" style="width: 100%; padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 13px;">' +
                        '<small style="color: #64748b; font-size: 11px; margin-top: 4px; display: block;">Monthly gross salary</small>' +
                    '</div>' +
                    '<div>' +
                        '<label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 6px; color: #475569;">Maximum Salary (₱)</label>' +
                        '<input type="number" id="newPosSalaryMax" min="0" step="0.01" placeholder="0.00" style="width: 100%; padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 13px;">' +
                        '<small style="color: #64748b; font-size: 11px; margin-top: 4px; display: block;">Monthly gross salary</small>' +
                    '</div>' +
                '</div>' +
                '<div style="margin-bottom: 16px;">' +
                    '<label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 6px; color: #475569;">Status</label>' +
                    '<select id="newPosStatus" style="width: 100%; padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 13px;">' +
                        '<option value="Active">Active</option>' +
                        '<option value="Inactive">Inactive</option>' +
                    '</select>' +
                '</div>' +
            '</div>' +
            '<div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; padding-top: 20px; border-top: 1px solid #f1f5f9;">' +
                '<button type="button" onclick="closeModal()" style="background: white; color: #475569; border: 1px solid #e2e8f0; padding: 10px 22px; border-radius: 24px; cursor: pointer; display: flex; align-items: center; gap: 6px;"><i class="fas fa-times"></i> Cancel</button>' +
                '<button type="submit" style="background: #4f46e5; color: white; border: none; padding: 10px 22px; border-radius: 24px; cursor: pointer; display: flex; align-items: center; gap: 6px;"><i class="fas fa-save"></i> Save Position</button>' +
            '</div>' +
        '</form>' +
    '</div>';
    
    openModal('Add Position', content);
}

function saveNewCompany(event) {
    event.preventDefault();
    var name = document.getElementById('newCompanyName').value.trim();
    var address = document.getElementById('newCompanyAddress').value.trim();
    var phone = document.getElementById('newCompanyPhone').value.trim();
    var email = document.getElementById('newCompanyEmail').value.trim();
    var status = document.getElementById('newCompanyStatus').value;
    
    if (!name) { 
        showToast('Please fill all required fields.', 'warning'); 
        return; 
    }
    
    // Validate email if provided
    if (email && !email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
        showToast('Please enter a valid email address.', 'warning');
        return;
    }
    
    var data = {
        name: name,
        address: address,
        phone: phone,
        email: email,
        status: status
    };
    
    // Save to database via API
    saveCompanyToDatabase(data);
}

async function saveCompanyToDatabase(data) {
    try {
        const response = await fetch('../../api/settings/settings_api.php?action=create_company', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            credentials: 'include',
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showToast('Company created successfully!', 'success');
            if (typeof markModalAsSaved === 'function') markModalAsSaved();
            closeModal(true);
            // Reload companies
            if (typeof loadCompanies === 'function') {
                await loadCompanies();
                await loadDepartments();
                renderCompanyLevel();
            }
        } else {
            showToast(result.message || 'Failed to create company', 'warning');
        }
    } catch (error) {
        console.error('Error creating company:', error);
        showToast('Failed to create company: ' + error.message, 'warning');
    }
}

function saveNewDepartment(event, companyId) {
    event.preventDefault();
    var name = document.getElementById('newDeptName').value.trim();
    var head = document.getElementById('newDeptHead').value.trim();
    var status = document.getElementById('newDeptStatus').value;
    
    if (!name) { 
        showToast('Please fill all required fields.', 'warning'); 
        return; 
    }
    
    var data = {
        company_id: companyId,
        name: name,
        head: head,
        status: status
    };
    
    saveDepartmentToDatabase(data);
}

async function saveDepartmentToDatabase(data) {
    try {
        const response = await fetch('../../api/settings/settings_api.php?action=create_department', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            credentials: 'include',
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showToast('Department created successfully!', 'success');
            if (typeof markModalAsSaved === 'function') markModalAsSaved();
            closeModal(true);
            // Reload departments
            if (typeof loadDepartments === 'function') {
                await loadDepartments();
                await loadJobs();
                renderDepartmentLevel();
            }
        } else {
            showToast(result.message || 'Failed to create department', 'warning');
        }
    } catch (error) {
        console.error('Error creating department:', error);
        showToast('Failed to create department: ' + error.message, 'warning');
    }
}

function saveNewPosition(event, departmentId) {
    event.preventDefault();
    var title = document.getElementById('newPosTitle').value.trim();
    var level = document.getElementById('newPosLevel').value;
    var vacancies = document.getElementById('newPosVacancies').value;
    var reportsTo = document.getElementById('newPosReportsTo').value.trim();
    var salaryMin = document.getElementById('newPosSalaryMin').value;
    var salaryMax = document.getElementById('newPosSalaryMax').value;
    var status = document.getElementById('newPosStatus').value;
    
    if (!title || !level) { 
        showToast('Please fill all required fields.', 'warning'); 
        return; 
    }
    
    // Validate salary range
    if (salaryMin && salaryMax && parseFloat(salaryMin) > parseFloat(salaryMax)) {
        showToast('Minimum salary cannot be greater than maximum salary.', 'warning');
        return;
    }
    
    var data = {
        department_id: departmentId,
        title: title,
        level: level,
        vacancies: vacancies ? parseInt(vacancies) : 0,
        reportsTo: reportsTo,
        salaryMin: salaryMin ? parseFloat(salaryMin) : null,
        salaryMax: salaryMax ? parseFloat(salaryMax) : null,
        status: status
    };
    
    savePositionToDatabase(data);
}

async function savePositionToDatabase(data) {
    try {
        const response = await fetch('../../api/settings/settings_api.php?action=create_job', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            credentials: 'include',
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showToast('Job created successfully!', 'success');
            if (typeof markModalAsSaved === 'function') markModalAsSaved();
            closeModal(true);
            // Reload jobs
            if (typeof loadJobs === 'function') {
                await loadJobs();
                renderJobLevel();
            }
        } else {
            showToast(result.message || 'Failed to create job', 'warning');
        }
    } catch (error) {
        console.error('Error creating job:', error);
        showToast('Failed to create job: ' + error.message, 'warning');
    }
}
</script>
