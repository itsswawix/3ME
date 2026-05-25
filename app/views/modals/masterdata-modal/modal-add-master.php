<!-- modal-add-master.php -->
<script>
window.openAddMasterModal = function() {
    openAddMasterDataModal();
}

function openAddMasterDataModal() {
    const content = `
        <style>
            .modal-add-master * { margin: 0; box-sizing: border-box; }
            .modal-add-master { font-family: 'Inter', sans-serif; max-width: 550px; width: 100%; }
            .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px 24px; }
            .full-width { grid-column: span 2; }
            .form-group { display: flex; flex-direction: column; margin-bottom: 4px; }
            .form-group label { font-size: 0.85rem; font-weight: 500; margin-bottom: 6px; color: #475569; letter-spacing: 0.3px; }
            .form-group input, .form-group select, .form-group textarea { padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 16px; font-size: 0.9rem; background: #ffffff; font-family: 'Inter', sans-serif; }
            .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); }
            .section-title { grid-column: span 2; font-size: 1rem; font-weight: 600; margin: 20px 0 8px; padding-bottom: 8px; border-bottom: 1.5px solid #e2e8f0; color: #0f172a; display: flex; align-items: center; gap: 8px; }
            .section-title i { color: #4f46e5; font-size: 0.9rem; width: 20px; }
            .section-title:first-of-type { margin-top: 0; }
            .required-star { color: #ef4444; margin-left: 2px; }
            textarea { resize: vertical; min-height: 80px; }
            .modal-buttons { display: flex; justify-content: flex-end; gap: 12px; margin-top: 28px; padding-top: 20px; border-top: 1px solid #f1f5f9; }
            .btn-save { background: #4f46e5; color: white; border: none; padding: 10px 22px; border-radius: 24px; cursor: pointer; display: flex; align-items: center; gap: 6px; box-shadow: 0 4px 8px rgba(79, 70, 229, 0.2); }
            .btn-cancel { background: white; color: #475569; border: 1px solid #e2e8f0; padding: 10px 22px; border-radius: 24px; cursor: pointer; }
            .status-toggle { width: 40px; height: 20px; background: #e2e8f0; border-radius: 10px; position: relative; cursor: pointer; transition: background 0.2s; display: inline-block; }
            .status-toggle.active { background: #10b981; }
            .status-toggle .toggle-dot { width: 16px; height: 16px; background: white; border-radius: 50%; position: absolute; top: 2px; left: 2px; transition: left 0.2s; box-shadow: 0 1px 3px rgba(0,0,0,0.2); }
            .status-toggle.active .toggle-dot { left: 22px; }
            .modal-footer-note { font-size: 0.75rem; color: #94a3b8; margin-top: 16px; text-align: right; }
        </style>
        <div class="modal-add-master">
            <form id="addMasterForm" onsubmit="saveNewMasterData(event)">
                <div class="form-grid">
                    <div class="section-title"><i class="fas fa-database"></i> Master Data Information</div>
                    <div class="form-group full-width"><label>Data Type <span class="required-star">*</span></label><select id="newMasterDataType" required><option value="">Select Data Type</option><option value="Departments">Departments</option><option value="Job Titles">Job Titles</option><option value="Employment Types">Employment Types</option><option value="Leave Types">Leave Types</option><option value="Performance Periods">Performance Periods</option></select></div>
                    <div class="form-group full-width"><label>Value <span class="required-star">*</span></label><input type="text" id="newMasterValue" required placeholder="e.g., Human Resources, Full-Time"></div>
                    <div class="form-group full-width"><label>Description</label><textarea id="newMasterDescription" rows="3" placeholder="Additional description or notes..."></textarea></div>
                    <div class="form-group full-width"><label>Status</label><div style="display: flex; align-items: center; gap: 12px;"><div class="status-toggle active" id="newMasterStatusToggle" onclick="toggleNewMasterStatus()"><div class="toggle-dot"></div></div><span id="newMasterStatusLabel">Active</span></div><input type="hidden" id="newMasterIsActive" value="true"></div>
                </div>
                <div class="modal-footer-note"><span class="required-star">*</span> Required fields</div>
                <div class="modal-buttons">
                    <button type="button" class="btn-cancel" onclick="closeAddMasterModal()"><i class="fas fa-times"></i> Cancel</button>
                    <button type="submit" class="btn-save"><i class="fas fa-save"></i> Save Master Data</button>
                </div>
            </form>
        </div>
    `;
    openModal('Add Master Data', content);
}

function closeAddMasterModal() { if (typeof closeModal === 'function') closeModal(); }
function toggleNewMasterStatus() {
    const toggle = document.getElementById('newMasterStatusToggle');
    const label = document.getElementById('newMasterStatusLabel');
    const input = document.getElementById('newMasterIsActive');
    toggle.classList.toggle('active');
    label.textContent = toggle.classList.contains('active') ? 'Active' : 'Inactive';
    input.value = toggle.classList.contains('active') ? 'true' : 'false';
}

async function saveNewMasterData(event) {
    event.preventDefault();
    
    const dataType = document.getElementById('newMasterDataType').value;
    const value = document.getElementById('newMasterValue').value.trim();
    const description = document.getElementById('newMasterDescription').value.trim();
    const isActive = document.getElementById('newMasterIsActive').value === 'true';
    
    if (!dataType || !value) { 
        showToast('Please fill all required fields.', 'warning'); 
        return; 
    }
    
    const data = {
        data_type: dataType,
        value: value,
        description: description,
        is_active: isActive
    };
    
    try {
        const response = await fetch('../../api/settings/settings_api.php?action=create_master_data', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            credentials: 'include',
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showToast('Master data created successfully!', 'success');
            if (typeof markModalAsSaved === 'function') markModalAsSaved();
            closeModal(true);
            if (typeof loadMasterData === 'function') loadMasterData();
        } else {
            showToast(result.message || 'Failed to create master data', 'warning');
        }
    } catch (error) {
        console.error('Error creating master data:', error);
        showToast('Failed to create master data: ' + error.message, 'warning');
    }
}
</script>
