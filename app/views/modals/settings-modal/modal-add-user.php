<!-- modal-add-user.php -->
<div id="addUserModalContent" style="display: none;">
    <form onsubmit="handleAddUser(event)">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div style="grid-column: span 2;"><label>Full Name *</label><input type="text" name="firstname" required style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 12px;"></div>
            <div><label>Email *</label><input type="email" name="email" required style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 12px;"></div>
            <div><label>Password *</label><input type="password" name="password" required style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 12px;"></div>
            <div><label>Role *</label><select name="role" required style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 12px;"><option value="">Select Role</option><option value="Admin">Admin</option><option value="HR Manager">HR Manager</option><option value="Manager">Manager</option></select></div>
            <div style="grid-column: span 2;"><label>Contact Number</label><input type="tel" name="contact_number" style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 12px;"></div>
        </div>
        <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
            <button type="button" onclick="closeModal()" style="padding: 10px 20px; border: 1px solid #e2e8f0; border-radius: 20px; background: white; cursor: pointer;">Cancel</button>
            <button type="submit" style="padding: 10px 20px; border: none; border-radius: 20px; background: #4f46e5; color: white; cursor: pointer;">Create User</button>
        </div>
    </form>
</div>

<script>
function openAddUserModal() {
    const content = `
        <style>
            .modal-add-user * { margin: 0; box-sizing: border-box; }
            .modal-add-user { font-family: 'Inter', sans-serif; max-width: 550px; width: 100%; }
            .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px 24px; }
            .full-width { grid-column: span 2; }
            .form-group { display: flex; flex-direction: column; margin-bottom: 4px; }
            .form-group label { font-size: 0.85rem; font-weight: 500; margin-bottom: 6px; color: #475569; letter-spacing: 0.3px; }
            .form-group input, .form-group select { padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 16px; font-size: 0.9rem; background: #ffffff; font-family: 'Inter', sans-serif; transition: all 0.2s ease; }
            .form-group input:focus, .form-group select:focus { outline: none; border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); }
            .section-title { grid-column: span 2; font-size: 1rem; font-weight: 600; margin: 20px 0 8px; padding-bottom: 8px; border-bottom: 1.5px solid #e2e8f0; color: #0f172a; display: flex; align-items: center; gap: 8px; }
            .section-title i { color: #4f46e5; font-size: 0.9rem; width: 20px; }
            .section-title:first-of-type { margin-top: 0; }
            .required-star { color: #ef4444; margin-left: 2px; }
            .modal-buttons { display: flex; justify-content: flex-end; gap: 12px; margin-top: 28px; padding-top: 20px; border-top: 1px solid #f1f5f9; }
            .btn-save { background: #4f46e5; color: white; border: none; padding: 10px 22px; border-radius: 24px; font-size: 0.85rem; font-weight: 500; cursor: pointer; display: flex; align-items: center; gap: 6px; box-shadow: 0 4px 8px rgba(79, 70, 229, 0.2); }
            .btn-save:hover { background: #4338ca; transform: translateY(-1px); }
            .btn-cancel { background: white; color: #475569; border: 1px solid #e2e8f0; padding: 10px 22px; border-radius: 24px; cursor: pointer; display: flex; align-items: center; gap: 6px; }
            .photo-upload { display: flex; align-items: center; gap: 16px; }
            .photo-preview { width: 60px; height: 60px; border-radius: 16px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-size: 24px; color: #94a3b8; overflow: hidden; }
            .photo-preview img { width: 100%; height: 100%; object-fit: cover; }
            .modal-footer-note { font-size: 0.75rem; color: #94a3b8; margin-top: 16px; text-align: right; }
        </style>
        <div class="modal-add-user">
            <form id="addUserForm" onsubmit="saveNewUser(event)">
                <div class="form-grid">
                    <div class="section-title"><i class="fas fa-user"></i> Profile Information</div>
                    <div class="form-group full-width">
                        <label>Profile Photo</label>
                        <div class="photo-upload">
                            <div class="photo-preview" id="newPhotoPreview"><i class="fas fa-user"></i></div>
                            <div><input type="file" id="newProfilePhoto" class="form-control" accept="image/*" onchange="previewNewPhoto(event)"><small style="color: #64748b;">JPG, PNG or GIF. Max 2MB.</small></div>
                        </div>
                    </div>
                    <div class="form-group"><label>First Name <span class="required-star">*</span></label><input type="text" id="newUserFirstname" required placeholder="e.g., John"></div>
                    <div class="form-group"><label>Middle Name</label><input type="text" id="newUserMiddlename" placeholder="e.g., Smith"></div>
                    <div class="form-group"><label>Surname <span class="required-star">*</span></label><input type="text" id="newUserSurname" required placeholder="e.g., Doe"></div>
                    <div class="form-group"><label>Email <span class="required-star">*</span></label><input type="email" id="newUserEmail" required placeholder="john.doe@novacore.com"></div>
                    <div class="form-group"><label>Contact Number</label><input type="tel" id="newUserContact" placeholder="+63 XXX XXX XXXX"></div>
                    <div class="section-title"><i class="fas fa-shield"></i> Access & Permissions</div>
                    <div class="form-group"><label>Password <span class="required-star">*</span></label><input type="password" id="newUserPassword" required placeholder="••••••••"></div>
                    <div class="form-group"><label>Role <span class="required-star">*</span></label><select id="newUserRole" required><option value="">Select Role</option><option value="Admin">Admin</option><option value="HR Manager">HR Manager</option><option value="Manager">Manager</option><option value="Employee">Employee</option></select></div>
                </div>
                <div class="modal-footer-note"><span class="required-star">*</span> Required fields</div>
                <div class="modal-buttons">
                    <button type="button" class="btn-cancel" onclick="closeAddUserModal()"><i class="fas fa-times"></i> Cancel</button>
                    <button type="submit" class="btn-save" id="saveUserBtn"><i class="fas fa-save"></i> Create User</button>
                </div>
            </form>
        </div>
    `;
    openModal('Add New User', content);
}

function closeAddUserModal() { if (typeof closeModal === 'function') closeModal(); }

function previewNewPhoto(event) { 
    const file = event.target.files[0]; 
    if (file) { 
        const reader = new FileReader(); 
        reader.onload = e => document.getElementById('newPhotoPreview').innerHTML = `<img src="${e.target.result}" alt="Preview">`; 
        reader.readAsDataURL(file); 
    } 
}

async function saveNewUser(event) {
    event.preventDefault();
    
    const firstname = document.getElementById('newUserFirstname').value.trim();
    const middlename = document.getElementById('newUserMiddlename').value.trim();
    const surname = document.getElementById('newUserSurname').value.trim();
    const email = document.getElementById('newUserEmail').value.trim();
    const password = document.getElementById('newUserPassword').value;
    const role = document.getElementById('newUserRole').value;
    const contact = document.getElementById('newUserContact').value || '';
    
    if (!firstname || !surname || !email || !password || !role) { 
        showToast('Please fill all required fields.', 'warning'); 
        return; 
    }
    
    const saveBtn = document.getElementById('saveUserBtn');
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';
    
    const data = {
        firstname: firstname,
        middlename: middlename,
        surname: surname,
        email: email,
        password: password,
        role: role,
        contact_number: contact
    };
    
    try {
        const response = await fetch('../../api/users/create_user.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            credentials: 'include',
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showToast('User created successfully!', 'success');
            if (typeof markModalAsSaved === 'function') markModalAsSaved();
            closeModal(true);
            if (typeof loadUsers === 'function') loadUsers();
        } else {
            let errorMsg = result.message || 'Failed to create user';
            if (result.session_debug) {
                console.log('Session debug:', result.session_debug);
                errorMsg += ` (Role: ${result.your_role || 'unknown'})`;
            }
            showToast(errorMsg, 'warning');
        }
    } catch (error) {
        console.error('Error creating user:', error);
        showToast('Failed to create user: ' + error.message, 'warning');
    } finally {
        saveBtn.disabled = false;
        saveBtn.innerHTML = '<i class="fas fa-save"></i> Create User';
    }
}
</script>