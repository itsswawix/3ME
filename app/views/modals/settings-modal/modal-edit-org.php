<!-- modal-edit-org.php -->
<script>
// Edit Company Modal
function openEditCompanyModal(company) {
    const content = `
        <style>
            .modal-edit-org * { margin: 0; box-sizing: border-box; }
            .modal-edit-org { font-family: 'Inter', sans-serif; max-width: 550px; width: 100%; }
            .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px 24px; }
            .full-width { grid-column: span 2; }
            .form-group { display: flex; flex-direction: column; margin-bottom: 4px; }
            .form-group label { font-size: 0.85rem; font-weight: 500; margin-bottom: 6px; color: #475569; }
            .form-group input, .form-group select, .form-group textarea { padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 16px; font-size: 0.9rem; background: #ffffff; }
            .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); }
            .form-group textarea { resize: vertical; min-height: 60px; }
            .section-title { grid-column: span 2; font-size: 1rem; font-weight: 600; margin: 20px 0 8px; padding-bottom: 8px; border-bottom: 1.5px solid #e2e8f0; color: #0f172a; display: flex; align-items: center; gap: 8px; }
            .section-title i { color: #4f46e5; }
            .required-star { color: #ef4444; }
            .modal-buttons { display: flex; justify-content: flex-end; gap: 12px; margin-top: 28px; padding-top: 20px; border-top: 1px solid #f1f5f9; }
            .btn-save { background: #4f46e5; color: white; border: none; padding: 10px 22px; border-radius: 24px; cursor: pointer; display: flex; align-items: center; gap: 6px; }
            .btn-cancel { background: white; color: #475569; border: 1px solid #e2e8f0; padding: 10px 22px; border-radius: 24px; cursor: pointer; }
            .org-id-badge { background: linear-gradient(145deg, #f8fafc, #f1f5f9); padding: 8px 14px; border-radius: 12px; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
        </style>
        <div class="modal-edit-org">
            <div class="org-id-badge"><i class="fas fa-hashtag"></i><span>Editing Company:</span><strong>${escapeHtml(company.id)}</strong></div>
            <form id="editCompanyForm" onsubmit="updateCompany(event, '${company.id}')">
                <div class="form-grid">
                    <div class="section-title"><i class="fas fa-building"></i> Company Information</div>
                    <div class="form-group full-width"><label>Company Name <span class="required-star">*</span></label><input type="text" id="editCompanyName" required value="${escapeHtml(company.name)}"></div>
                    <div class="form-group full-width"><label>Address</label><textarea id="editCompanyAddress">${escapeHtml(company.address || '')}</textarea></div>
                    <div class="form-group"><label>Contact Number</label><input type="tel" id="editCompanyPhone" value="${escapeHtml(company.phone || '')}"></div>
                    <div class="form-group"><label>Email</label><input type="email" id="editCompanyEmail" value="${escapeHtml(company.email || '')}"></div>
                    <div class="form-group full-width"><label>Status</label><select id="editCompanyStatus">${['Active','Inactive'].map(s => `<option value="${s}" ${company.status === s ? 'selected' : ''}>${s}</option>`).join('')}</select></div>
                </div>
                <div class="modal-buttons">
                    <button type="button" class="btn-cancel" onclick="closeModal()"><i class="fas fa-times"></i> Cancel</button>
                    <button type="submit" class="btn-save"><i class="fas fa-save"></i> Save Changes</button>
                </div>
            </form>
        </div>
    `;
    openModal('Edit Company', content);
}

// Edit Department Modal
function openEditDepartmentModal(dept) {
    const content = `
        <style>
            .modal-edit-org * { margin: 0; box-sizing: border-box; }
            .modal-edit-org { font-family: 'Inter', sans-serif; max-width: 550px; width: 100%; }
            .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px 24px; }
            .full-width { grid-column: span 2; }
            .form-group { display: flex; flex-direction: column; margin-bottom: 4px; }
            .form-group label { font-size: 0.85rem; font-weight: 500; margin-bottom: 6px; color: #475569; }
            .form-group input, .form-group select { padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 16px; font-size: 0.9rem; background: #ffffff; }
            .form-group input:focus, .form-group select:focus { outline: none; border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); }
            .section-title { grid-column: span 2; font-size: 1rem; font-weight: 600; margin: 20px 0 8px; padding-bottom: 8px; border-bottom: 1.5px solid #e2e8f0; color: #0f172a; display: flex; align-items: center; gap: 8px; }
            .section-title i { color: #4f46e5; }
            .required-star { color: #ef4444; }
            .modal-buttons { display: flex; justify-content: flex-end; gap: 12px; margin-top: 28px; padding-top: 20px; border-top: 1px solid #f1f5f9; }
            .btn-save { background: #4f46e5; color: white; border: none; padding: 10px 22px; border-radius: 24px; cursor: pointer; display: flex; align-items: center; gap: 6px; }
            .btn-cancel { background: white; color: #475569; border: 1px solid #e2e8f0; padding: 10px 22px; border-radius: 24px; cursor: pointer; }
            .org-id-badge { background: linear-gradient(145deg, #f8fafc, #f1f5f9); padding: 8px 14px; border-radius: 12px; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
        </style>
        <div class="modal-edit-org">
            <div class="org-id-badge"><i class="fas fa-hashtag"></i><span>Editing Department:</span><strong>${escapeHtml(dept.id)}</strong></div>
            <form id="editDepartmentForm" onsubmit="updateDepartment(event, '${dept.id}')">
                <div class="form-grid">
                    <div class="section-title"><i class="fas fa-sitemap"></i> Department Information</div>
                    <div class="form-group full-width"><label>Department Name <span class="required-star">*</span></label><input type="text" id="editDeptName" required value="${escapeHtml(dept.name)}"></div>
                    <div class="form-group"><label>Department Code <span class="required-star">*</span></label><input type="text" id="editDeptCode" required value="${escapeHtml(dept.code)}"></div>
                    <div class="form-group"><label>Department Head</label><input type="text" id="editDeptHead" value="${escapeHtml(dept.head || '')}"></div>
                    <div class="form-group full-width"><label>Status</label><select id="editDeptStatus">${['Active','Inactive'].map(s => `<option value="${s}" ${dept.status === s ? 'selected' : ''}>${s}</option>`).join('')}</select></div>
                </div>
                <div class="modal-buttons">
                    <button type="button" class="btn-cancel" onclick="closeModal()"><i class="fas fa-times"></i> Cancel</button>
                    <button type="submit" class="btn-save"><i class="fas fa-save"></i> Save Changes</button>
                </div>
            </form>
        </div>
    `;
    openModal('Edit Department', content);
}

// Edit Position Modal
function openEditPositionModal(pos) {
    const content = `
        <style>
            .modal-edit-org * { margin: 0; box-sizing: border-box; }
            .modal-edit-org { font-family: 'Inter', sans-serif; max-width: 550px; width: 100%; }
            .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px 24px; }
            .full-width { grid-column: span 2; }
            .form-group { display: flex; flex-direction: column; margin-bottom: 4px; }
            .form-group label { font-size: 0.85rem; font-weight: 500; margin-bottom: 6px; color: #475569; }
            .form-group input, .form-group select { padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 16px; font-size: 0.9rem; background: #ffffff; }
            .form-group input:focus, .form-group select:focus { outline: none; border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); }
            .section-title { grid-column: span 2; font-size: 1rem; font-weight: 600; margin: 20px 0 8px; padding-bottom: 8px; border-bottom: 1.5px solid #e2e8f0; color: #0f172a; display: flex; align-items: center; gap: 8px; }
            .section-title i { color: #4f46e5; }
            .required-star { color: #ef4444; }
            .modal-buttons { display: flex; justify-content: flex-end; gap: 12px; margin-top: 28px; padding-top: 20px; border-top: 1px solid #f1f5f9; }
            .btn-save { background: #4f46e5; color: white; border: none; padding: 10px 22px; border-radius: 24px; cursor: pointer; display: flex; align-items: center; gap: 6px; }
            .btn-cancel { background: white; color: #475569; border: 1px solid #e2e8f0; padding: 10px 22px; border-radius: 24px; cursor: pointer; }
            .org-id-badge { background: linear-gradient(145deg, #f8fafc, #f1f5f9); padding: 8px 14px; border-radius: 12px; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
        </style>
        <div class="modal-edit-org">
            <div class="org-id-badge"><i class="fas fa-hashtag"></i><span>Editing Position:</span><strong>${escapeHtml(pos.id)}</strong></div>
            <form id="editPositionForm" onsubmit="updatePosition(event, '${pos.id}')">
                <div class="form-grid">
                    <div class="section-title"><i class="fas fa-briefcase"></i> Position Information</div>
                    <div class="form-group full-width"><label>Job Title <span class="required-star">*</span></label><input type="text" id="editPosTitle" required value="${escapeHtml(pos.title || pos.jobTitle)}"></div>
                    <div class="form-group"><label>Level <span class="required-star">*</span></label><select id="editPosLevel" required>${['Director','Manager','Senior','Mid-Level','Junior','Entry'].map(l => `<option value="${l}" ${pos.level === l ? 'selected' : ''}>${l}</option>`).join('')}</select></div>
                    <div class="form-group"><label>Status</label><select id="editPosStatus">${['Active','Inactive'].map(s => `<option value="${s}" ${pos.status === s ? 'selected' : ''}>${s}</option>`).join('')}</select></div>
                </div>
                <div class="modal-buttons">
                    <button type="button" class="btn-cancel" onclick="closeModal()"><i class="fas fa-times"></i> Cancel</button>
                    <button type="submit" class="btn-save"><i class="fas fa-save"></i> Save Changes</button>
                </div>
            </form>
        </div>
    `;
    openModal('Edit Position', content);
}

// Update functions
async function updateCompany(event, id) {
    event.preventDefault();
    
    const name = document.getElementById('editCompanyName').value.trim();
    const address = document.getElementById('editCompanyAddress').value.trim();
    const phone = document.getElementById('editCompanyPhone').value.trim();
    const email = document.getElementById('editCompanyEmail').value.trim();
    const status = document.getElementById('editCompanyStatus').value;
    
    if (!name) {
        showToast('Please fill all required fields.', 'warning');
        return;
    }
    
    // Validate email if provided
    if (email && !email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
        showToast('Please enter a valid email address.', 'warning');
        return;
    }
    
    try {
        const response = await fetch('../../api/settings/settings_api.php?action=update_company', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            credentials: 'include',
            body: JSON.stringify({ id, name, address, phone, email, status })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showToast('Company updated successfully!', 'success');
            if (typeof markModalAsSaved === 'function') markModalAsSaved();
            closeModal(true);
            // Reload companies
            if (typeof loadCompanies === 'function') {
                await loadCompanies();
                await loadDepartments();
                renderCompanyLevel();
            }
        } else {
            showToast(result.message || 'Failed to update company', 'warning');
        }
    } catch (error) {
        console.error('Error updating company:', error);
        showToast('Failed to update company: ' + error.message, 'warning');
    }
}

async function updateDepartment(event, id) {
    event.preventDefault();
    
    const name = document.getElementById('editDeptName').value.trim();
    const code = document.getElementById('editDeptCode').value.trim();
    const head = document.getElementById('editDeptHead').value.trim();
    const status = document.getElementById('editDeptStatus').value;
    
    if (!name || !code) {
        showToast('Please fill all required fields.', 'warning');
        return;
    }
    
    try {
        const response = await fetch('../../api/settings/settings_api.php?action=update_department', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            credentials: 'include',
            body: JSON.stringify({ id, name, code, head, status })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showToast('Department updated successfully!', 'success');
            if (typeof markModalAsSaved === 'function') markModalAsSaved();
            closeModal(true);
            // Reload departments
            if (typeof loadDepartments === 'function') {
                await loadDepartments();
                await loadPositions();
                renderDepartmentLevel();
            }
        } else {
            showToast(result.message || 'Failed to update department', 'warning');
        }
    } catch (error) {
        console.error('Error updating department:', error);
        showToast('Failed to update department: ' + error.message, 'warning');
    }
}

async function updatePosition(event, id) {
    event.preventDefault();
    
    const title = document.getElementById('editPosTitle').value.trim();
    const level = document.getElementById('editPosLevel').value;
    const status = document.getElementById('editPosStatus').value;
    
    if (!title || !level) {
        showToast('Please fill all required fields.', 'warning');
        return;
    }
    
    try {
        const response = await fetch('../../api/settings/settings_api.php?action=update_position', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            credentials: 'include',
            body: JSON.stringify({ id, title, level, status })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showToast('Position updated successfully!', 'success');
            if (typeof markModalAsSaved === 'function') markModalAsSaved();
            closeModal(true);
            // Reload positions
            if (typeof loadPositions === 'function') {
                await loadPositions();
                renderPositionLevel();
            }
        } else {
            showToast(result.message || 'Failed to update position', 'warning');
        }
    } catch (error) {
        console.error('Error updating position:', error);
        showToast('Failed to update position: ' + error.message, 'warning');
    }
}
</script>
