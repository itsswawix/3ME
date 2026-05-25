<!-- modal-edit-user.php -->
<script>
function editUser(id) {
    const user = window.users.find(u => u.id === id);
    if (!user) return;
    
    // Split full name into first name and surname
    const nameParts = user.name.split(' ');
    const firstname = nameParts[0] || '';
    const surname = nameParts.slice(1).join(' ') || '';
    
    const content = `
        <style>
            .modal-edit-user * { margin: 0; box-sizing: border-box; }
            .modal-edit-user { font-family: 'Inter', sans-serif; width: 100%; max-width: 500px; }
            .user-preview { display: flex; align-items: center; gap: 12px; margin-bottom: 20px; padding: 12px; background: #f8fafc; border-radius: 16px; border: 1px solid #f1f5f9; }
            .user-avatar-small { width: 42px; height: 42px; border-radius: 12px; background: ${user.color}; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 14px; box-shadow: 0 4px 8px rgba(0,0,0,0.05); }
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
        <div class="modal-edit-user">
            <div class="user-preview">
                <div class="user-avatar-small">${user.avatar}</div>
                <div>
                    <h4 style="font-weight:600; color:#0f172a;">${escapeHtml(user.name)}</h4>
                    <p style="color:#64748b; font-size:0.75rem;">${user.id} • ${escapeHtml(user.role)}</p>
                </div>
            </div>
            
            <form id="editUserForm" onsubmit="event.preventDefault(); submitEditUser('${id}');">
                <div class="form-grid">
                    <div class="section-title"><i class="fas fa-id-card"></i> Personal Details</div>
                    
                    <div class="form-group">
                        <label>First Name <span class="required-star">*</span></label>
                        <input type="text" id="editFirstname" required value="${escapeHtml(firstname)}">
                    </div>
                    <div class="form-group">
                        <label>Surname <span class="required-star">*</span></label>
                        <input type="text" id="editSurname" required value="${escapeHtml(surname)}">
                    </div>
                    
                    <div class="form-group full-width">
                        <label>Email Address <span class="required-star">*</span></label>
                        <input type="email" id="editEmail" required value="${escapeHtml(user.email)}">
                    </div>
                    
                    <div class="form-group">
                        <label>Contact Number</label>
                        <input type="text" id="editContact" value="${escapeHtml(user.contact_number)}">
                    </div>
                    <div class="form-group">
                        <label>Department</label>
                        <select id="editDepartment">
                            <option value="">Select Department</option>
                            <option value="Human Resources" ${user.department === 'Human Resources' ? 'selected' : ''}>Human Resources</option>
                            <option value="IT Department" ${user.department === 'IT Department' ? 'selected' : ''}>IT Department</option>
                            <option value="Finance" ${user.department === 'Finance' ? 'selected' : ''}>Finance</option>
                            <option value="Operations" ${user.department === 'Operations' ? 'selected' : ''}>Operations</option>
                            <option value="Executive Office" ${user.department === 'Executive Office' ? 'selected' : ''}>Executive Office</option>
                        </select>
                    </div>

                    <div class="section-title"><i class="fas fa-user-lock"></i> Account Settings</div>
                    
                    <div class="form-group">
                        <label>Role <span class="required-star">*</span></label>
                        <select id="editRole" required ${id === window.currentLoggedInUserId ? 'disabled' : ''}>
                            <option value="Admin" ${user.role === 'Admin' ? 'selected' : ''}>Admin</option>
                            <option value="Supervisor" ${user.role === 'Supervisor' ? 'selected' : ''}>Supervisor</option>
                            <option value="Manager" ${user.role === 'Manager' ? 'selected' : ''}>Manager</option>
                            <option value="HR Officer" ${user.role === 'HR Officer' ? 'selected' : ''}>HR Officer</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status <span class="required-star">*</span></label>
                        <select id="editStatus" required ${id === window.currentLoggedInUserId ? 'disabled' : ''}>
                            <option value="Active" ${user.status === 'Active' ? 'selected' : ''}>Active</option>
                            <option value="Inactive" ${user.status === 'Inactive' ? 'selected' : ''}>Inactive</option>
                            <option value="Suspended" ${user.status === 'Suspended' ? 'selected' : ''}>Suspended</option>
                            <option value="Pending" ${user.status === 'Pending' ? 'selected' : ''}>Pending</option>
                        </select>
                    </div>
                    
                    <div class="form-group full-width">
                        <label>Change Password</label>
                        <input type="password" id="editPassword" minlength="6" placeholder="Leave blank to keep current password">
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
    openModal('Edit System User', content);
}

async function submitEditUser(id) {
    const firstname = document.getElementById('editFirstname')?.value.trim();
    const surname = document.getElementById('editSurname')?.value.trim();
    const email = document.getElementById('editEmail')?.value.trim();
    const contact_number = document.getElementById('editContact')?.value.trim();
    const department = document.getElementById('editDepartment')?.value;
    
    // Check if fields are disabled (i.e. self update). If so, we use the original values from user
    const roleSelect = document.getElementById('editRole');
    const role = roleSelect ? roleSelect.value : window.users.find(u => u.id === id).role;
    
    const statusSelect = document.getElementById('editStatus');
    const status = statusSelect ? statusSelect.value : window.users.find(u => u.id === id).status;
    
    const password = document.getElementById('editPassword')?.value;
    
    if (!firstname || !surname || !email || !role || !status) {
        showToast('Please fill out all required fields.', 'warning');
        return;
    }
    
    const userData = {
        id,
        firstname,
        surname,
        email,
        contact_number,
        department,
        role,
        status
    };
    
    if (password && password.trim().length >= 6) {
        userData.password = password;
    }
    
    try {
        const response = await fetch('../../api/users/users.php', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(userData)
        });
        const result = await response.json();
        
        if (result.success) {
            showToast('User details updated successfully!', 'success');
            markModalAsSaved();
            closeModal();
            
            // Reload user list
            if (typeof loadUsers === 'function') {
                loadUsers();
            }
        } else {
            showToast(result.message || 'Error updating user', 'warning');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('Error updating user', 'error');
    }
}
</script>
