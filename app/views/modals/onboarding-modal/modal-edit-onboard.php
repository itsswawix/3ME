<!-- modal-edit-onboard.php -->
<script>
function editOnboard(id) {
    const record = window.onboardRecords.find(r => r.id === id);
    if (!record) return;
    
    const content = `
        <style>
            .modal-edit-onboard * { margin: 0; box-sizing: border-box; }
            .modal-edit-onboard { font-family: 'Inter', sans-serif; max-width: 700px; width: 100%; }
            .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px 24px; }
            .full-width { grid-column: span 2; }
            .form-group { display: flex; flex-direction: column; margin-bottom: 4px; }
            .form-group label { font-size: 0.85rem; font-weight: 500; margin-bottom: 6px; color: #475569; }
            .form-group input, .form-group select, .form-group textarea { padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 16px; font-size: 0.9rem; background: #ffffff; }
            .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); }
            .section-title { grid-column: span 2; font-size: 1rem; font-weight: 600; margin: 20px 0 8px; padding-bottom: 8px; border-bottom: 1.5px solid #e2e8f0; color: #0f172a; display: flex; align-items: center; gap: 8px; }
            .section-title i { color: #4f46e5; }
            .required-star { color: #ef4444; }
            textarea { resize: vertical; min-height: 80px; }
            .employee-preview { display: flex; align-items: center; gap: 12px; margin-bottom: 20px; padding: 12px; background: #f8fafc; border-radius: 16px; }
            .employee-avatar-small { width: 40px; height: 40px; border-radius: 12px; background: ${record.color}; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; }
            .modal-buttons { display: flex; justify-content: flex-end; gap: 12px; margin-top: 28px; padding-top: 20px; border-top: 1px solid #f1f5f9; }
            .btn-save { background: #4f46e5; color: white; border: none; padding: 10px 22px; border-radius: 24px; font-size: 0.85rem; font-weight: 500; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 6px; box-shadow: 0 4px 8px rgba(79, 70, 229, 0.2); }
            .btn-save:hover { background: #4338ca; transform: translateY(-1px); }
            .btn-cancel { background: white; color: #475569; border: 1px solid #e2e8f0; padding: 10px 22px; border-radius: 24px; font-size: 0.85rem; font-weight: 500; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 6px; }
            .btn-cancel:hover { background: #f8fafc; border-color: #cbd5e1; }
            
            /* Task Management Styles */
            .tasks-section { background: #f8fafc; padding: 16px; border-radius: 16px; margin-top: 16px; }
            .tasks-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
            .tasks-header h4 { font-size: 0.9rem; font-weight: 600; color: #1e293b; }
            .task-item { display: flex; align-items: center; gap: 10px; padding: 8px 12px; background: white; border-radius: 12px; margin-bottom: 8px; border: 1px solid #e2e8f0; }
            .task-checkbox { width: 18px; height: 18px; accent-color: #4f46e5; cursor: pointer; }
            .task-text { flex: 1; font-size: 0.85rem; color: #1e293b; }
            .task-text.completed { text-decoration: line-through; color: #64748b; }
            .task-delete { color: #ef4444; cursor: pointer; padding: 4px; border-radius: 6px; transition: all 0.2s; }
            .task-delete:hover { background: #fee2e2; }
            .add-task-form { display: flex; gap: 8px; margin-top: 12px; }
            .add-task-input { flex: 1; padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 0.85rem; }
            .add-task-btn { background: #4f46e5; color: white; border: none; padding: 8px 16px; border-radius: 12px; font-size: 0.8rem; cursor: pointer; }
            .progress-indicator { display: flex; align-items: center; gap: 8px; }
            .progress-bar { flex: 1; height: 6px; background: #e2e8f0; border-radius: 3px; overflow: hidden; }
            .progress-fill { height: 100%; background: #4f46e5; transition: width 0.3s ease; }
            .progress-text { font-size: 0.8rem; color: #64748b; font-weight: 500; }
        </style>
        <div class="modal-edit-onboard">
            <div class="employee-preview">
                <div class="employee-avatar-small">${record.avatar}</div>
                <div>
                    <h4 style="font-weight:600;">${escapeHtml(record.employeeName)}</h4>
                    <p style="color:#64748b; font-size:0.75rem;">${escapeHtml(record.employeeEmail)} • ${escapeHtml(record.position)}</p>
                </div>
            </div>
            
            <form id="editOnboardForm" onsubmit="event.preventDefault(); updateOnboard('${id}');">
                <div class="form-grid">
                    <div class="section-title"><i class="fas fa-user"></i> Employee Information</div>
                    <div class="form-group"><label>Employee ID</label><input type="text" id="editEmployeeId" value="${escapeHtml(record.employeeId)}" readonly style="background: #f8fafc;"></div>
                    <div class="form-group"><label>Employee Name <span class="required-star">*</span></label><input type="text" id="editEmployeeName" required value="${escapeHtml(record.employeeName)}"></div>
                    <div class="form-group full-width"><label>Email <span class="required-star">*</span></label><input type="email" id="editEmployeeEmail" required value="${escapeHtml(record.employeeEmail)}"></div>
                    
                    <div class="section-title"><i class="fas fa-briefcase"></i> Position Details</div>
                    <div class="form-group"><label>Position <span class="required-star">*</span></label><input type="text" id="editPosition" required value="${escapeHtml(record.position)}"></div>
                    <div class="form-group"><label>Company <span class="required-star">*</span></label><select id="editCompany" required onchange="loadDepartmentsForEdit(this.value)"></select></div>
                    <div class="form-group"><label>Department <span class="required-star">*</span></label><select id="editDepartment" required></select></div>
                    <div class="form-group full-width" style="background: #fef3c7; padding: 12px; border-radius: 12px; border: 1px solid #fbbf24;">
                        <p style="font-size: 0.8rem; color: #b45309; margin: 0;">
                            <i class="fas fa-info-circle"></i> <strong>Important:</strong> Company, department, and position are assigned from the recruitment system. Changes here will update the employee record when onboarding is completed.
                        </p>
                    </div>
                    
                    <div class="section-title"><i class="fas fa-calendar"></i> Onboarding Schedule</div>
                    <div class="form-group"><label>Start Date <span class="required-star">*</span></label><input type="date" id="editStartDate" required value="${convertDateToInput(record.startDate)}"></div>
                    <div class="form-group">
                        <label>Progress Status</label>
                        <select id="editProgress" onchange="handleProgressChange(this.value)">
                            <option value="Not Started" ${record.progress === 'Not Started' ? 'selected' : ''}>Not Started</option>
                            <option value="In Progress" ${record.progress === 'In Progress' ? 'selected' : ''}>In Progress</option>
                            <option value="Completed" ${record.progress === 'Completed' ? 'selected' : ''}>Completed</option>
                        </select>
                    </div>
                    
                    <div class="form-group full-width" id="completionDateGroup" style="display: ${record.progress === 'Completed' ? 'flex' : 'none'};">
                        <label>Completion Date</label>
                        <input type="date" id="editCompletionDate" value="${record.completionDate ? convertDateToInput(record.completionDate) : ''}">
                    </div>
                    
                    <div class="section-title"><i class="fas fa-sticky-note"></i> Additional Information</div>
                    <div class="form-group full-width"><label>Notes</label><textarea id="editNotes">${escapeHtml(record.notes || '')}</textarea></div>
                </div>
                
                <!-- Tasks Management Section -->
                <div class="tasks-section">
                    <div class="tasks-header">
                        <h4><i class="fas fa-tasks"></i> Onboarding Tasks</h4>
                        <div class="progress-indicator">
                            <div class="progress-bar">
                                <div class="progress-fill" id="taskProgressFill" style="width: ${calculateTaskProgress(record.tasks)}%;"></div>
                            </div>
                            <span class="progress-text" id="taskProgressText">${calculateTaskProgress(record.tasks)}%</span>
                        </div>
                    </div>
                    
                    <div id="tasksList">
                        ${renderTasksList(record.tasks)}
                    </div>
                    
                    <div class="add-task-form">
                        <input type="text" class="add-task-input" id="newTaskInput" placeholder="Add new task...">
                        <button type="button" class="add-task-btn" onclick="addNewTask()">
                            <i class="fas fa-plus"></i> Add
                        </button>
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
    
    openModal('Edit Onboarding Record', content);
    
    // Load companies and departments after modal opens
    setTimeout(() => {
        loadCompaniesForEdit(record.company, record.department);
    }, 100);
}

// Load companies into the edit modal
async function loadCompaniesForEdit(selectedCompany, selectedDepartment) {
    try {
        const response = await fetch('../../api/settings/settings_api.php?action=list_companies');
        const result = await response.json();
        
        const companySelect = document.getElementById('editCompany');
        if (!companySelect) return;
        
        if (result.success && Array.isArray(result.data)) {
            companySelect.innerHTML = '<option value="">Select Company</option>' + 
                result.data.map(company => 
                    `<option value="${escapeHtml(company.name)}" ${company.name === selectedCompany ? 'selected' : ''}>${escapeHtml(company.name)}</option>`
                ).join('');
            
            // Load departments for the selected company
            if (selectedCompany) {
                await loadDepartmentsForEdit(selectedCompany, selectedDepartment);
            }
        }
    } catch (error) {
        console.error('Error loading companies:', error);
    }
}

// Load departments for the selected company
async function loadDepartmentsForEdit(companyName, selectedDepartment) {
    try {
        // First get the company ID
        const companiesResponse = await fetch('../../api/settings/settings_api.php?action=list_companies');
        const companiesResult = await companiesResponse.json();
        
        if (!companiesResult.success) return;
        
        const company = companiesResult.data.find(c => c.name === companyName);
        if (!company) return;
        
        // Now get departments for this company
        const deptResponse = await fetch('../../api/settings/settings_api.php?action=list_departments');
        const deptResult = await deptResponse.json();
        
        const deptSelect = document.getElementById('editDepartment');
        if (!deptSelect) return;
        
        if (deptResult.success && Array.isArray(deptResult.data)) {
            const companyDepts = deptResult.data.filter(d => d.companyId === company.id);
            
            deptSelect.innerHTML = '<option value="">Select Department</option>' + 
                companyDepts.map(dept => 
                    `<option value="${escapeHtml(dept.name)}" ${dept.name === selectedDepartment ? 'selected' : ''}>${escapeHtml(dept.name)}</option>`
                ).join('');
        }
    } catch (error) {
        console.error('Error loading departments:', error);
    }
}

function renderTasksList(tasks) {
    if (!tasks || tasks.length === 0) {
        return '<p style="color: #64748b; font-size: 0.85rem; text-align: center; padding: 20px;">No tasks added yet.</p>';
    }
    
    return tasks.map((task, index) => `
        <div class="task-item">
            <input type="checkbox" class="task-checkbox" ${task.completed ? 'checked' : ''} 
                   onchange="toggleTask(${index}, this.checked)">
            <span class="task-text ${task.completed ? 'completed' : ''}" id="taskText${index}">${escapeHtml(task.text)}</span>
            <i class="fas fa-trash task-delete" onclick="deleteTask(${index})" title="Delete task"></i>
        </div>
    `).join('');
}

function calculateTaskProgress(tasks) {
    if (!tasks || tasks.length === 0) return 0;
    const completed = tasks.filter(t => t.completed).length;
    return Math.round((completed / tasks.length) * 100);
}

function convertDateToInput(dateStr) {
    if (!dateStr) return '';
    try {
        const date = new Date(dateStr);
        return date.toISOString().split('T')[0];
    } catch (e) {
        return '';
    }
}

function handleProgressChange(progress) {
    const completionGroup = document.getElementById('completionDateGroup');
    const completionInput = document.getElementById('editCompletionDate');
    
    if (progress === 'Completed') {
        completionGroup.style.display = 'flex';
        if (!completionInput.value) {
            completionInput.value = new Date().toISOString().split('T')[0];
        }
    } else {
        completionGroup.style.display = 'none';
        completionInput.value = '';
    }
}

let currentTasks = [];

function toggleTask(index, completed) {
    if (currentTasks[index]) {
        currentTasks[index].completed = completed;
        
        // Update visual state
        const taskText = document.getElementById(`taskText${index}`);
        if (taskText) {
            if (completed) {
                taskText.classList.add('completed');
            } else {
                taskText.classList.remove('completed');
            }
        }
        
        // Update progress
        updateTaskProgress();
    }
}

function deleteTask(index) {
    if (confirm('Are you sure you want to delete this task?')) {
        currentTasks.splice(index, 1);
        refreshTasksList();
        updateTaskProgress();
    }
}

function addNewTask() {
    const input = document.getElementById('newTaskInput');
    const taskText = input.value.trim();
    
    if (taskText) {
        currentTasks.push({
            text: taskText,
            completed: false
        });
        
        input.value = '';
        refreshTasksList();
        updateTaskProgress();
    }
}

function refreshTasksList() {
    const container = document.getElementById('tasksList');
    container.innerHTML = renderTasksList(currentTasks);
}

function updateTaskProgress() {
    const progress = calculateTaskProgress(currentTasks);
    const progressFill = document.getElementById('taskProgressFill');
    const progressText = document.getElementById('taskProgressText');
    
    if (progressFill) progressFill.style.width = progress + '%';
    if (progressText) progressText.textContent = progress + '%';
    
    // Auto-update progress status based on task completion
    const progressSelect = document.getElementById('editProgress');
    if (progressSelect && progress === 100 && currentTasks.length > 0) {
        progressSelect.value = 'Completed';
        handleProgressChange('Completed');
    } else if (progressSelect && progress > 0 && progress < 100) {
        if (progressSelect.value === 'Not Started') {
            progressSelect.value = 'In Progress';
        }
    }
}

function updateOnboard(id) {
    const employeeName = document.getElementById('editEmployeeName')?.value.trim();
    const employeeEmail = document.getElementById('editEmployeeEmail')?.value.trim();
    
    if (!employeeName || !employeeEmail) {
        showToast('Please fill all required fields.', 'warning');
        return;
    }
    
    // Check for duplicate email (excluding current record)
    if (window.onboardRecords && window.onboardRecords.some(r => r.id !== id && r.employeeEmail.toLowerCase() === employeeEmail.toLowerCase())) {
        showToast('An onboarding record with this email already exists.', 'warning');
        return;
    }
    
    const progress = document.getElementById('editProgress')?.value || 'Not Started';
    
    // Check if all tasks are completed when trying to mark as completed
    if (progress === 'Completed') {
        const incompleteTasks = currentTasks.filter(t => !t.completed);
        if (incompleteTasks.length > 0) {
            showToast(`Cannot mark as completed. ${incompleteTasks.length} task(s) still incomplete. Please complete all tasks first.`, 'warning');
            return;
        }
        
        if (currentTasks.length === 0) {
            showToast('Cannot mark as completed. No tasks have been assigned. Please add onboarding tasks first.', 'warning');
            return;
        }
    }
    
    const completionDate = progress === 'Completed' ? document.getElementById('editCompletionDate')?.value : null;
    
    // Get the original record to preserve IDs
    const originalRecord = window.onboardRecords.find(r => r.id === id);
    
    const updateData = {
        id: id,
        employee_name: employeeName,
        employee_email: employeeEmail,
        position: document.getElementById('editPosition')?.value.trim() || '',
        job_id: originalRecord?.job_id || null,           // Preserve job_id
        department: document.getElementById('editDepartment')?.value.trim() || '',
        department_id: originalRecord?.department_id || null, // Preserve department_id
        company: document.getElementById('editCompany')?.value.trim() || '',
        company_id: originalRecord?.company_id || null,      // Preserve company_id
        start_date: document.getElementById('editStartDate')?.value || '',
        progress: progress,
        completion_date: completionDate,
        tasks: currentTasks,
        notes: document.getElementById('editNotes')?.value || ''
    };
    
    // Send to API
    fetch('../../api/onboarding/records.php', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(updateData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const wasCompleted = updateData.progress === 'Completed';
            showToast(`Onboarding record updated successfully!${wasCompleted ? ' Employee record created.' : ''}`, 'success');
            
            // Update local array
            const index = window.onboardRecords.findIndex(r => r.id === id);
            if (index !== -1) {
                window.onboardRecords[index] = {
                    ...window.onboardRecords[index],
                    employeeName: updateData.employee_name,
                    employeeEmail: updateData.employee_email,
                    position: updateData.position,
                    department: updateData.department,
                    company: updateData.company,
                    startDate: new Date(updateData.start_date).toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' }),
                    progress: updateData.progress,
                    completionDate: completionDate ? new Date(completionDate).toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' }) : null,
                    tasks: updateData.tasks,
                    notes: updateData.notes
                };
                
                // Update UI if render functions exist
                if (typeof renderOnboardTable === 'function') {
                    renderOnboardTable(window.onboardRecords);
                }
            }
            
            if (typeof closeModal === 'function') {
                closeModal();
            }
            
            // If completed, navigate to employee page
            if (wasCompleted) {
                setTimeout(() => {
                    // Store navigation parameters
                    sessionStorage.setItem('navigateToCompany', updateData.company);
                    sessionStorage.setItem('navigateToDepartment', updateData.department);
                    sessionStorage.setItem('highlightEmployee', updateData.employee_name);
                    
                    // Redirect to employee page
                    window.location.href = 'employee.php';
                }, 1500);
            }
        } else {
            showToast(data.message || 'Error updating onboarding record', 'warning');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error updating onboarding record', 'warning');
    });
}

// Initialize tasks when modal opens
document.addEventListener('DOMContentLoaded', function() {
    // This will be called when the modal content is loaded
    setTimeout(() => {
        const record = window.onboardRecords?.find(r => r.id === window.currentEditingOnboardId);
        if (record && record.tasks) {
            currentTasks = [...record.tasks];
        }
    }, 100);
});

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>]/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;' })[m] || m);
}

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.style.cssText = `position: fixed; bottom: 24px; right: 24px; background: ${type === 'success' ? '#10b981' : type === 'warning' ? '#f59e0b' : '#1e293b'}; color: white; padding: 12px 20px; border-radius: 12px; font-size: 13px; z-index: 10000; animation: slideIn 0.3s ease; box-shadow: 0 4px 12px rgba(0,0,0,0.15);`;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, 3000);
}

// Store current editing ID for task initialization
window.currentEditingOnboardId = null;

// Override editOnboard to store current ID
const originalEditOnboard = editOnboard;
editOnboard = function(id) {
    window.currentEditingOnboardId = id;
    const record = window.onboardRecords.find(r => r.id === id);
    if (record && record.tasks) {
        currentTasks = [...record.tasks];
    }
    originalEditOnboard(id);
};
</script>