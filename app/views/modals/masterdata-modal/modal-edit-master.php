<!-- modal-edit-master.php -->
<script>
function openEditMasterDataModal(master) {
    const content = `
        <style>
            .modal-edit-master * { margin: 0; box-sizing: border-box; }
            .modal-edit-master { font-family: 'Inter', sans-serif; max-width: 550px; width: 100%; }
            .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px 24px; }
            .full-width { grid-column: span 2; }
            .form-group { display: flex; flex-direction: column; margin-bottom: 4px; }
            .form-group label { font-size: 0.85rem; font-weight: 500; margin-bottom: 6px; color: #475569; }
            .form-group input, .form-group select, .form-group textarea { padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 16px; font-size: 0.9rem; background: #ffffff; font-family: 'Inter', sans-serif; }
            .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); }
            .section-title { grid-column: span 2; font-size: 1rem; font-weight: 600; margin: 20px 0 8px; padding-bottom: 8px; border-bottom: 1.5px solid #e2e8f0; color: #0f172a; display: flex; align-items: center; gap: 8px; }
            .section-title i { color: #4f46e5; }
            .required-star { color: #ef4444; }
            textarea { resize: vertical; min-height: 80px; }
            .modal-buttons { display: flex; justify-content: flex-end; gap: 12px; margin-top: 28px; padding-top: 20px; border-top: 1px solid #f1f5f9; }
            .btn-save { background: #4f46e5; color: white; border: none; padding: 10px 22px; border-radius: 24px; cursor: pointer; display: flex; align-items: center; gap: 6px; }
            .btn-cancel { background: white; color: #475569; border: 1px solid #e2e8f0; padding: 10px 22px; border-radius: 24px; cursor: pointer; }
            .status-toggle { width: 40px; height: 20px; background: #e2e8f0; border-radius: 10px; position: relative; cursor: pointer; transition: background 0.2s; display: inline-block; }
            .status-toggle.active { background: #10b981; }
            .status-toggle .toggle-dot { width: 16px; height: 16px; background: white; border-radius: 50%; position: absolute; top: 2px; left: 2px; transition: left 0.2s; box-shadow: 0 1px 3px rgba(0,0,0,0.2); }
            .status-toggle.active .toggle-dot { left: 22px; }
            .master-id-badge { background: linear-gradient(145deg, #f8fafc, #f1f5f9); padding: 8px 14px; border-radius: 12px; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; font-size: 0.85rem; color: #64748b; }
        </style>
        <div class="modal-edit-master">
            <div class="master-id-badge"><i class="fas fa-fingerprint"></i> ${master.id}</div>
            <form id="editMasterForm" onsubmit="updateMasterData(event, '${master.id}')">
                <div class="form-grid">
                    <div class="section-title"><i class="fas fa-database"></i> Master Data Information</div>
                    <div class="form-group full-width">
                        <label>Data Type <span class="required-star">*</span></label>
                        <select id="editMasterDataType" required>
                            <option value="">Select Data Type</option>
                            ${['Departments', 'Job Titles', 'Employment Types', 'Leave Types', 'Performance Periods'].map(type => 
                                `<option value="${type}" ${master.dataType === type ? 'selected' : ''}>${type}</option>`
                            ).join('')}
                        </select>
                    </div>
                    <div class="form-group full-width">
                        <label>Value <span class="required-star">*</span></label>
                        <input type="text" id="editMasterValue" required value="${escapeHtml(master.value)}">
                    </div>
                    <div class="form-group full-width">
                        <label>Description</label>
                        <textarea id="editMasterDescription" rows="3">${escapeHtml(master.description || '')}</textarea>
                    </div>
                    <div class="form-group full-width">
                        <label>Status</label>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div class="status-toggle ${master.isActive ? 'active' : ''}" id="editMasterStatusToggle" onclick="toggleEditMasterStatus()">
                                <div class="toggle-dot"></div>
                            </div>
                            <span id="editMasterStatusLabel">${master.isActive ? 'Active' : 'Inactive'}</span>
                        </div>
                        <input type="hidden" id="editMasterIsActive" value="${master.isActive}">
                    </div>
                </div>
                <div class="modal-buttons">
                    <button type="button" class="btn-cancel" onclick="closeModal()"><i class="fas fa-times"></i> Cancel</button>
                    <button type="submit" class="btn-save"><i class="fas fa-save"></i> Save Changes</button>
                </div>
            </form>
        </div>
    `;
    openModal('Edit Master Data', content);
}

function toggleEditMasterStatus() {
    const toggle = document.getElementById('editMasterStatusToggle');
    const label = document.getElementById('editMasterStatusLabel');
    const input = document.getElementById('editMasterIsActive');
    toggle.classList.toggle('active');
    const isActive = toggle.classList.contains('active');
    label.textContent = isActive ? 'Active' : 'Inactive';
    input.value = isActive;
}

async function updateMasterData(event, id) {
    event.preventDefault();
    
    const dataType = document.getElementById('editMasterDataType').value;
    const value = document.getElementById('editMasterValue').value.trim();
    const description = document.getElementById('editMasterDescription').value.trim();
    const isActive = document.getElementById('editMasterIsActive').value === 'true';
    
    if (!dataType || !value) {
        showToast('Please fill all required fields.', 'warning');
        return;
    }
    
    const data = {
        id: id,
        data_type: dataType,
        value: value,
        description: description,
        is_active: isActive
    };
    
    try {
        const response = await fetch('../../api/settings/settings_api.php?action=update_master_data', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            credentials: 'include',
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showToast('Master data updated successfully!', 'success');
            if (typeof markModalAsSaved === 'function') markModalAsSaved();
            closeModal(true);
            if (typeof loadMasterData === 'function') loadMasterData();
        } else {
            showToast(result.message || 'Failed to update master data', 'warning');
        }
    } catch (error) {
        console.error('Error updating master data:', error);
        showToast('Failed to update master data: ' + error.message, 'warning');
    }
}
</script>
