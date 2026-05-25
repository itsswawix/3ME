<!-- modal-edit-user.php -->
<div id="editUserModalContent" style="display: none;">
    <form onsubmit="handleEditUser(event)">
        <input type="hidden" id="editUserId" name="id">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div style="grid-column: span 2;"><label>Full Name *</label><input type="text" id="editUserName" name="name" required style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 12px;"></div>
            <div><label>Email *</label><input type="email" id="editUserEmail" name="email" required style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 12px;"></div>
            <div><label>Contact</label><input type="tel" id="editUserContact" name="contact_number" style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 12px;"></div>
            <div><label>Role *</label><select id="editUserRole" name="role" required style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 12px;"><option value="Admin">Admin</option><option value="HR Manager">HR Manager</option><option value="Manager">Manager</option><option value="Employee">Employee</option></select></div>
            <div style="grid-column: span 2;"><label>Status *</label><select id="editUserStatus" name="status" required style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 12px;"><option value="Active">Active</option><option value="Inactive">Inactive</option><option value="Locked">Locked</option></select></div>
        </div>
        <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
            <button type="button" onclick="closeModal()" style="padding: 10px 20px; border: 1px solid #e2e8f0; border-radius: 20px; background: white; cursor: pointer;">Cancel</button>
            <button type="submit" style="padding: 10px 20px; border: none; border-radius: 20px; background: #4f46e5; color: white; cursor: pointer;">Update User</button>
        </div>
    </form>
</div>

<div id="viewUserModalContent" style="display: none;">
    <div style="padding: 20px;">
        <div style="margin-bottom: 16px;"><strong>Name:</strong> <span id="viewUserName"></span></div>
        <div style="margin-bottom: 16px;"><strong>Email:</strong> <span id="viewUserEmail"></span></div>
        <div style="margin-bottom: 16px;"><strong>Role:</strong> <span id="viewUserRole"></span></div>
        <div style="margin-bottom: 16px;"><strong>Contact:</strong> <span id="viewUserContact"></span></div>
        <div style="margin-bottom: 16px;"><strong>Status:</strong> <span id="viewUserStatus"></span></div>
        <div style="margin-bottom: 16px;"><strong>Last Login:</strong> <span id="viewUserLastLogin"></span></div>
        <div style="display: flex; justify-content: flex-end; margin-top: 24px;">
            <button onclick="closeModal()" style="padding: 10px 20px; border: 1px solid #e2e8f0; border-radius: 20px; background: white; cursor: pointer;">Close</button>
        </div>
    </div>
</div>

<script>
function editUser(id) {
    const user = window.users.find(u => u.id === id);
    if (!user) return;
    
    const content = `
        <style>
            .modal-edit-user * { margin: 0; box-sizing: border-box; }
            .modal-edit-user { font-family: 'Inter', sans-serif; max-width: 550px; width: 100%; }
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
            .btn-save:hover { background: #4338ca; }
            .btn-cancel { background: white; color: #475569; border: 1px solid #e2e8f0; padding: 10px 22px; border-radius: 24px; cursor: pointer; }
            .user-id-badge { background: linear-gradient(145deg, #f8fafc, #f1f5f9); padding: 8px 14px; border-radius: 12px; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
            .employee-preview { display: flex; align-items: center; gap: 12px; margin-bottom: 20px; padding: 12px; background: #f8fafc; border-radius: 16px; }
            .employee-avatar-small { width: 40px; height: 40px; border-radius: 12px; background: ${user.color}; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; }
            .photo-upload { display: flex; align-items: center; gap: 16px; }
            .photo-preview { width: 60px; height: 60px; border-radius: 16px; background: ${user.color}; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 20px; overflow: hidden; }
        </style>
        <div class="modal-edit-user">
            <div class="employee-preview">
                <div class="employee-avatar-small">${user.avatar}</div>
                <div><h4 style="font-weight:600;">${escapeHtml(user.name)}</h4><p style="color:#64748b; font-size:0.75rem;">${user.email}</p></div>
            </div>
            <form id="editUserForm" onsubmit="updateUser(event, '${id}')">
                <div class="form-grid">
                    <div class="section-title"><i class="fas fa-user"></i> Profile Information</div>
                    <div class="form-group full-width">
                        <label>Profile Photo</label>
                        <div class="photo-upload">
                            <div class="photo-preview" id="editPhotoPreview">${user.avatar}</div>
                            <div><input type="file" id="editProfilePhoto" class="form-control" accept="image/*" onchange="previewEditPhoto(event)"><small>JPG, PNG or GIF.</small></div>
                        </div>
                    </div>
                    <div class="form-group full-width"><label>Full Name <span class="required-star">*</span></label><input type="text" id="editUserName" required value="${escapeHtml(user.name)}"></div>
                    <div class="form-group full-width"><label>Email <span class="required-star">*</span></label><input type="email" id="editUserEmail" required value="${escapeHtml(user.email)}"></div>
                    <div class="form-group full-width"><label>Contact Number</label><input type="tel" id="editUserContact" value="${escapeHtml(user.contactNumber || '')}"></div>
                    <div class="section-title"><i class="fas fa-shield"></i> Access & Permissions</div>
                    <div class="form-group"><label>Role <span class="required-star">*</span></label><select id="editUserRole" required>${['Admin','HR Manager','Manager','Employee'].map(r => `<option value="${r}" ${user.role === r ? 'selected' : ''}>${r}</option>`).join('')}</select></div>
                    <div class="form-group"><label>Status <span class="required-star">*</span></label><select id="editUserStatus" required>${['Active','Inactive','Locked'].map(s => `<option value="${s}" ${user.status === s ? 'selected' : ''}>${s}</option>`).join('')}</select></div>
                    <div class="form-group full-width"><label>Password</label><input type="password" id="editUserPassword" placeholder="Leave blank to keep current"></div>
                </div>
                <div class="modal-buttons">
                    <button type="button" class="btn-cancel" onclick="closeModal()"><i class="fas fa-times"></i> Cancel</button>
                    <button type="submit" class="btn-save"><i class="fas fa-save"></i> Save Changes</button>
                </div>
            </form>
        </div>
    `;
    openModal('Edit User', content);
}

function previewEditPhoto(event) { 
    const file = event.target.files[0]; 
    if (file) { 
        const reader = new FileReader(); 
        reader.onload = e => document.getElementById('editPhotoPreview').innerHTML = `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;" alt="Preview">`; 
        reader.readAsDataURL(file); 
    } 
}

async function updateUser(event, id) {
    event.preventDefault();
    
    const password = document.getElementById('editUserPassword').value;
    
    const data = {
        id: id,
        name: document.getElementById('editUserName').value,
        email: document.getElementById('editUserEmail').value,
        role: document.getElementById('editUserRole').value,
        contact_number: document.getElementById('editUserContact').value,
        status: document.getElementById('editUserStatus').value
    };
    
    if (password) {
        data.password = password;
    }
    
    try {
        const response = await fetch('../../api/settings/settings_api.php?action=update_user', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            credentials: 'include',
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        if (result.success) {
            showToast('User updated successfully!', 'success');
            if (typeof markModalAsSaved === 'function') markModalAsSaved();
            closeModal(true);
            if (typeof loadUsers === 'function') loadUsers();
        } else {
            showToast(result.message, 'warning');
        }
    } catch (error) {
        showToast('Failed to update user: ' + error.message, 'warning');
    }
}
</script>