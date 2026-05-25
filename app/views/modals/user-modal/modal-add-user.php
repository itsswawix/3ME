<!-- modal-add-user.php -->
<script>
function openAddUserModal() {
    const content = `
        <style>
            .modal-add-user * { margin: 0; box-sizing: border-box; }
            .modal-add-user { font-family: 'Inter', sans-serif; width: 100%; max-width: 500px; }
            .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px 20px; }
            .full-width { grid-column: span 2; }
            .form-group { display: flex; flex-direction: column; }
            .form-group label { font-size: 0.82rem; font-weight: 500; margin-bottom: 6px; color: #475569; }
            .form-group input, .form-group select { padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 14px; font-size: 0.88rem; background: #ffffff; transition: all 0.2s; }
            .form-group input:focus, .form-group select:focus { outline: none; border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); }
            .section-title { grid-column: span 2; font-size: 0.95rem; font-weight: 600; margin: 12px 0 4px; padding-bottom: 6px; border-bottom: 1.5px solid #eef2ff; color: #0f172a; display: flex; align-items: center; gap: 8px; }
            .section-title i { color: #4f46e5; }
            .required-star { color: #ef4444; }
            .modal-buttons { display: flex; justify-content: flex-end; gap: 12px; margin-top: 28px; padding-top: 20px; border-top: 1px solid #f1f5f9; }
            .btn-save { background: #4f46e5; color: white; border: none; padding: 10px 22px; border-radius: 24px; font-size: 0.85rem; font-weight: 500; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 6px; box-shadow: 0 4px 8px rgba(79, 70, 229, 0.2); }
            .btn-save:hover { background: #4338ca; transform: translateY(-1px); }
            .btn-cancel { background: white; color: #475569; border: 1px solid #e2e8f0; padding: 10px 22px; border-radius: 24px; font-size: 0.85rem; font-weight: 500; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 6px; }
            .btn-cancel:hover { background: #f8fafc; border-color: #cbd5e1; }
        </style>
        <div class="modal-add-user">
            <form id="addUserForm" onsubmit="event.preventDefault(); submitAddUser();">
                <div class="form-grid">
                    <div class="section-title"><i class="fas fa-id-card"></i> Personal Information</div>
                    
                    <div class="form-group">
                        <label>First Name <span class="required-star">*</span></label>
                        <input type="text" id="addFirstname" required placeholder="e.g. John">
                    </div>
                    <div class="form-group">
                        <label>Surname <span class="required-star">*</span></label>
                        <input type="text" id="addSurname" required placeholder="e.g. Doe">
                    </div>
                    
                    <div class="form-group full-width">
                        <label>Email Address <span class="required-star">*</span></label>
                        <input type="email" id="addEmail" required placeholder="john.doe@company.com">
                    </div>
                    
                    <div class="form-group full-width">
                        <label>Contact Number</label>
                        <input type="text" id="addContact" placeholder="+63 900 000 0000">
                    </div>

                    <div class="section-title"><i class="fas fa-user-lock"></i> Security & Role</div>
                    
                    <div class="form-group full-width">
                        <label>Role <span class="required-star">*</span></label>
                        <select id="addRole" required>
                            <option value="">Select Role</option>
                            <option value="Admin">Admin</option>
                            <option value="Supervisor">Supervisor</option>
                            <option value="Manager">Manager</option>
                            <option value="HR Officer">HR Officer</option>
                        </select>
                    </div>
                </div>
                
                <div class="modal-buttons">
                    <button type="button" class="btn-cancel" onclick="closeModal()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn-save">
                        <i class="fas fa-user-plus"></i> Create User
                    </button>
                </div>
            </form>
        </div>
    `;
    openModal('Add New System User', content);
}

async function submitAddUser() {
    const firstname = document.getElementById('addFirstname')?.value.trim();
    const surname = document.getElementById('addSurname')?.value.trim();
    const email = document.getElementById('addEmail')?.value.trim();
    const contact_number = document.getElementById('addContact')?.value.trim();
    const role = document.getElementById('addRole')?.value;
    const password = firstname.toLowerCase() + '123';
    const department = '';
    
    if (!firstname || !surname || !email || !role) {
        showToast('Please fill out all required fields.', 'warning');
        return;
    }
    
    const userData = {
        firstname,
        surname,
        email,
        contact_number,
        department,
        role,
        password
    };
    
    try {
        const response = await fetch('../../api/users/users.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(userData)
        });
        const result = await response.json();
        
        if (result.success) {
            showToast('User created successfully!', 'success');
            markModalAsSaved();
            closeModal();
            
            // Reload user list
            if (typeof loadUsers === 'function') {
                loadUsers();
            }
        } else {
            showToast(result.message || 'Error creating user', 'warning');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('Error creating user', 'error');
    }
}
</script>
