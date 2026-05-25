<!-- modal-view-onboard.php -->
<script>
function viewOnboard(id) {
    const record = window.onboardRecords.find(r => r.id === id);
    if (!record) return;
    
    const progressConfig = {
        'Not Started': { bg: '#f1f5f9', color: '#64748b', icon: 'fa-clock' },
        'In Progress': { bg: '#fef3c7', color: '#b45309', icon: 'fa-hourglass-half' },
        'Completed': { bg: '#dcfce7', color: '#16a34a', icon: 'fa-check-circle' }
    }[record.progress] || { bg: '#f1f5f9', color: '#64748b', icon: 'fa-circle' };
    
    const completedTasks = record.tasks ? record.tasks.filter(t => t.completed).length : 0;
    const totalTasks = record.tasks ? record.tasks.length : 0;
    const progressPercentage = totalTasks > 0 ? Math.round((completedTasks / totalTasks) * 100) : 0;
    
    const content = `
        <style>
            .modal-view-onboard * { margin: 0; box-sizing: border-box; }
            .modal-view-onboard { font-family: 'Inter', sans-serif; max-width: 650px; width: 100%; }
            .view-header { display: flex; align-items: flex-start; gap: 16px; margin-bottom: 24px; padding-bottom: 20px; border-bottom: 1px solid #eef2ff; }
            .onboard-avatar-large { width: 64px; height: 64px; border-radius: 20px; object-fit: cover; flex-shrink: 0; }
            .onboard-info-large h3 { font-size: 1.2rem; font-weight: 600; color: #0f172a; margin-bottom: 4px; }
            .onboard-meta { font-size: 0.8rem; color: #64748b; display: flex; gap: 8px; flex-wrap: wrap; }
            .status-badge-view { padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 500; background: ${progressConfig.bg}; color: ${progressConfig.color}; display: inline-flex; align-items: center; gap: 4px; margin-top: 8px; }
            .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px 24px; margin-bottom: 24px; }
            .full-width { grid-column: span 2; }
            .detail-label { font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 6px; display: flex; align-items: center; gap: 6px; }
            .detail-label i { color: #4f46e5; width: 16px; }
            .detail-value { font-size: 0.95rem; font-weight: 500; color: #1e293b; }
            
            /* Progress Section */
            .progress-section { background: linear-gradient(145deg, #f8f4ff, #ffffff); border: 1.5px solid #e9d5ff; border-radius: 20px; padding: 20px; margin-bottom: 24px; }
            .progress-header { display: flex; align-items: center; gap: 10px; margin-bottom: 16px; }
            .progress-header i { font-size: 24px; color: #4f46e5; }
            .progress-header h4 { font-size: 1rem; font-weight: 600; color: #6b21a8; }
            .progress-bar-large { width: 100%; height: 12px; background: #e2e8f0; border-radius: 6px; overflow: hidden; margin-bottom: 12px; }
            .progress-fill-large { height: 100%; background: linear-gradient(90deg, #4f46e5, #7c3aed); transition: width 0.3s ease; border-radius: 6px; }
            .progress-stats { display: flex; justify-content: space-between; align-items: center; }
            .progress-percentage { font-size: 1.2rem; font-weight: 700; color: #4f46e5; }
            .progress-fraction { font-size: 0.9rem; color: #64748b; }
            
            /* Tasks Section */
            .tasks-section { margin-top: 20px; }
            .tasks-header { display: flex; align-items: center; gap: 8px; margin-bottom: 12px; color: #6b21a8; }
            .task-list { max-height: 200px; overflow-y: auto; }
            .task-item-view { display: flex; align-items: center; gap: 10px; padding: 8px 12px; background: white; border-radius: 12px; margin-bottom: 6px; border: 1px solid #f3e8ff; }
            .task-icon { width: 16px; height: 16px; display: flex; align-items: center; justify-content: center; }
            .task-icon.completed { color: #16a34a; }
            .task-icon.pending { color: #94a3b8; }
            .task-text-view { flex: 1; font-size: 0.85rem; color: #1e293b; }
            .task-text-view.completed { text-decoration: line-through; color: #64748b; }
            
            .modal-buttons { display: flex; justify-content: flex-end; gap: 12px; margin-top: 28px; padding-top: 20px; border-top: 1px solid #f1f5f9; flex-wrap: wrap; }
            .btn-primary { background: #4f46e5; color: white; border: none; padding: 10px 22px; border-radius: 24px; cursor: pointer; display: flex; align-items: center; gap: 6px; }
            .btn-secondary { background: white; color: #475569; border: 1px solid #e2e8f0; padding: 10px 22px; border-radius: 24px; cursor: pointer; }
            .btn-success { background: #10b981; color: white; border: none; padding: 10px 22px; border-radius: 24px; cursor: pointer; display: flex; align-items: center; gap: 6px; }
        </style>
        <div class="modal-view-onboard">
            <div class="view-header">
                <img src="${record.profilePhoto || '/3ME/assets/images/default-avatar.png'}" class="onboard-avatar-large" />
                <div class="onboard-info-large">
                    <h3>${escapeHtml(record.employeeName)}</h3>
                    <div class="onboard-meta">
                        <span><i class="fas fa-id-card"></i> ${record.employeeId}</span>
                        <span><i class="fas fa-briefcase"></i> ${escapeHtml(record.position)}</span>
                        <span><i class="fas fa-building"></i> ${escapeHtml(record.department)}</span>
                    </div>
                    <span class="status-badge-view"><i class="fas ${progressConfig.icon}"></i> ${record.progress}</span>
                </div>
            </div>
            
            <!-- Progress Section -->
            <div class="progress-section">
                <div class="progress-header">
                    <i class="fas fa-chart-line"></i>
                    <h4>Onboarding Progress</h4>
                </div>
                
                <div class="progress-bar-large">
                    <div class="progress-fill-large" style="width: ${progressPercentage}%;"></div>
                </div>
                
                <div class="progress-stats">
                    <span class="progress-percentage">${progressPercentage}%</span>
                    <span class="progress-fraction">${completedTasks} of ${totalTasks} tasks completed</span>
                </div>
                
                <!-- Tasks List -->
                <div class="tasks-section">
                    <div class="tasks-header">
                        <i class="fas fa-tasks"></i>
                        <span style="font-weight: 600;">Onboarding Tasks</span>
                    </div>
                    
                    <div class="task-list">
                        ${record.tasks && record.tasks.length > 0 ? record.tasks.map(task => `
                            <div class="task-item-view">
                                <div class="task-icon ${task.completed ? 'completed' : 'pending'}">
                                    <i class="fas fa-${task.completed ? 'check-circle' : 'circle'}"></i>
                                </div>
                                <span class="task-text-view ${task.completed ? 'completed' : ''}">${escapeHtml(task.text)}</span>
                            </div>
                        `).join('') : '<p style="color: #64748b; text-align: center; padding: 20px;">No tasks defined yet.</p>'}
                    </div>
                </div>
            </div>
            
            <div class="detail-grid">
                <div class="detail-item">
                    <div class="detail-label"><i class="fas fa-envelope"></i> Email</div>
                    <div class="detail-value">${escapeHtml(record.employeeEmail)}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label"><i class="fas fa-calendar-plus"></i> Start Date</div>
                    <div class="detail-value">${record.startDate}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label"><i class="fas fa-building"></i> Company</div>
                    <div class="detail-value">${escapeHtml(record.company)}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label"><i class="fas fa-calendar-check"></i> Completion Date</div>
                    <div class="detail-value">${record.completionDate || '—'}</div>
                </div>
                
                ${record.notes ? `
                <div class="detail-item full-width">
                    <div class="detail-label"><i class="fas fa-sticky-note"></i> Notes</div>
                    <div class="detail-value" style="background: #f8fafc; padding: 12px; border-radius: 12px; white-space: pre-wrap;">${escapeHtml(record.notes)}</div>
                </div>
                ` : ''}
            </div>
            
            <div class="modal-buttons">
                <button type="button" class="btn-secondary" onclick="closeModal()">
                    <i class="fas fa-times"></i> Close
                </button>
                <button type="button" class="btn-primary" onclick="closeModal(); editOnboard('${id}');">
                    <i class="fas fa-edit"></i> Edit
                </button>
                ${record.progress !== 'Completed' ? `
                    <button type="button" class="btn-success" onclick="markOnboardingComplete('${id}'); closeModal();">
                        <i class="fas fa-check-circle"></i> Mark Complete
                    </button>
                ` : ''}
            </div>
        </div>
    `;
    
    openModal('Onboarding Details', content);
}

function markOnboardingComplete(id) {
    const record = window.onboardRecords.find(r => r.id === id);
    if (!record) return;
    
    if (confirm(`Mark onboarding as complete for ${record.employeeName}?`)) {
        // Update record
        record.progress = 'Completed';
        record.completionDate = new Date().toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' });
        
        // Mark all tasks as completed
        if (record.tasks) {
            record.tasks.forEach(task => task.completed = true);
        }
        
        // Update API
        const updateData = {
            id: id,
            employee_name: record.employeeName,
            employee_email: record.employeeEmail,
            position: record.position,
            department: record.department,
            company: record.company,
            start_date: convertDateForAPI(record.startDate),
            progress: 'Completed',
            completion_date: new Date().toISOString().split('T')[0],
            tasks: record.tasks || [],
            notes: record.notes || ''
        };
        
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
                showToast(`${record.employeeName} onboarding completed! Employee record created.`, 'success');
                
                // Update UI if render functions exist
                if (typeof renderOnboardTable === 'function') {
                    renderOnboardTable(window.onboardRecords);
                }
                if (typeof renderAcceptedOffersTable === 'function') {
                    renderAcceptedOffersTable(window.acceptedOffers);
                }
                
                // Navigate to employee page after a short delay
                setTimeout(() => {
                    // Store navigation parameters
                    sessionStorage.setItem('navigateToCompany', record.company);
                    sessionStorage.setItem('navigateToDepartment', record.department);
                    sessionStorage.setItem('highlightEmployee', record.employeeName);
                    
                    // Redirect to employee page
                    window.location.href = 'employee.php';
                }, 1500);
            } else {
                showToast(data.message || 'Error updating record', 'warning');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error updating record', 'warning');
        });
    }
}

function convertDateForAPI(dateStr) {
    if (!dateStr) return new Date().toISOString().split('T')[0];
    try {
        const date = new Date(dateStr);
        return date.toISOString().split('T')[0];
    } catch (e) {
        return new Date().toISOString().split('T')[0];
    }
}

function emailEmployee(email) {
    if (email) {
        window.location.href = `mailto:${email}`;
    } else {
        showToast('No email available', 'warning');
    }
}

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
</script>