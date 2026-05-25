<!-- modal-view-employee-new.php -->
<script>
function viewEmployee(employeeId) {
    console.log('👁️ viewEmployee called with ID:', employeeId, 'Type:', typeof employeeId);
    
    if (!window.employees || window.employees.length === 0) {
        showToast('Employee data is still loading. Please wait...', 'warning');
        return;
    }
    
    // Convert to string for comparison to handle all cases
    const searchId = String(employeeId);
    
    // Find employee with flexible ID matching
    let employee = window.employees.find(emp => String(emp.id) === searchId);
    
    if (!employee) {
        const numId = parseInt(employeeId);
        if (!isNaN(numId)) {
            employee = window.employees.find(emp => parseInt(emp.id) === numId);
        }
    }
    
    if (!employee) {
        console.error('❌ Employee not found with ID:', employeeId);
        showToast('Employee not found', 'warning');
        return;
    }
    
    console.log('✅ Employee found:', employee.name);
    
    const statusConfig = {
        'Active': { bg: '#dcfce7', color: '#16a34a', icon: 'fa-check-circle' },
        'Remote': { bg: '#dbeafe', color: '#2563eb', icon: 'fa-laptop-house' },
        'On Leave': { bg: '#fef3c7', color: '#b45309', icon: 'fa-calendar-xmark' },
        'Probation': { bg: '#ffedd5', color: '#ea580c', icon: 'fa-hourglass-half' },
        'Terminated': { bg: '#fee2e2', color: '#dc2626', icon: 'fa-user-slash' }
    }[employee.status] || { bg: '#f1f5f9', color: '#64748b', icon: 'fa-circle' };

    const content = `
        <style>
            .employee-detail-section {
                margin-bottom: 20px;
            }
            .section-title-view {
                font-size: 14px;
                font-weight: 600;
                color: #1e293b;
                margin: 20px 0 12px 0;
                padding-bottom: 8px;
                border-bottom: 1px solid #e2e8f0;
                display: flex;
                align-items: center;
                gap: 8px;
            }
            .section-title-view:first-of-type {
                margin-top: 0;
            }
            .section-title-view i {
                color: #4f46e5;
                font-size: 13px;
            }
            .view-header {
                display: flex;
                align-items: flex-start;
                gap: 14px;
                margin-bottom: 20px;
                padding-bottom: 16px;
                border-bottom: 1px solid #e2e8f0;
            }
            .employee-avatar-large {
                width: 64px;
                height: 64px;
                border-radius: 16px;
                background: ${employee.color};
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-weight: 600;
                font-size: 1.5rem;
                flex-shrink: 0;
            }
            .employee-info-large h3 {
                font-size: 16px;
                font-weight: 600;
                color: #1e293b;
                margin: 0 0 4px 0;
            }
            .employee-meta {
                font-size: 12px;
                color: #64748b;
                display: flex;
                gap: 8px;
                flex-wrap: wrap;
                margin-bottom: 8px;
            }
            .status-badge-view {
                padding: 4px 12px;
                border-radius: 16px;
                font-size: 11px;
                font-weight: 500;
                background: ${statusConfig.bg};
                color: ${statusConfig.color};
                display: inline-flex;
                align-items: center;
                gap: 4px;
                margin-top: 6px;
            }
            .detail-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 16px;
            }
            .detail-item {
                margin-bottom: 0;
            }
            .detail-item-full {
                grid-column: span 2;
            }
            .detail-label {
                font-size: 11px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                color: #94a3b8;
                margin-bottom: 4px;
                display: flex;
                align-items: center;
                gap: 6px;
            }
            .detail-label i {
                color: #4f46e5;
                width: 14px;
            }
            .detail-value {
                font-size: 13px;
                font-weight: 500;
                color: #1e293b;
            }
            .detail-value a {
                color: #4f46e5;
                text-decoration: none;
            }
            .detail-value a:hover {
                text-decoration: underline;
            }
            .id-badge {
                font-family: 'Courier New', monospace;
                background: #f1f5f9;
                padding: 3px 8px;
                border-radius: 6px;
                font-weight: 600;
                color: #475569;
                font-size: 12px;
            }
            .salary-badge {
                font-weight: 600;
                color: #10b981;
                font-size: 14px;
            }
            .info-card {
                background: #f8fafc;
                border: 1px solid #e2e8f0;
                border-radius: 12px;
                padding: 12px;
                margin-bottom: 12px;
            }
            .info-card-title {
                font-size: 12px;
                font-weight: 600;
                color: #475569;
                margin-bottom: 8px;
                display: flex;
                align-items: center;
                gap: 6px;
            }
            .info-card-title i {
                color: #4f46e5;
            }
        </style>
        <div class="modal-view-employee">
            <div class="view-header">
                <img src="${employee.profilePhoto || '/3ME/assets/images/default-avatar.png'}" class="employee-avatar-large" style="object-fit: cover;" />
                <div class="employee-info-large">
                    <h3>${escapeHtml(employee.name)}</h3>
                    <div class="employee-meta">
                        <span><i class="fas fa-id-card"></i> ${employee.employeeId}</span>
                        <span><i class="fas fa-briefcase"></i> ${escapeHtml(employee.position)}</span>
                    </div>
                    <span class="status-badge-view"><i class="fas ${statusConfig.icon}"></i> ${employee.status}</span>
                </div>
            </div>
            
            <!-- Personal Information -->
            <div class="employee-detail-section">
                <div class="section-title-view"><i class="fas fa-user"></i> Personal Information</div>
                
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-id-badge"></i> Employee ID</div>
                        <div class="detail-value"><span class="id-badge">${employee.employeeId}</span></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-user"></i> Full Name</div>
                        <div class="detail-value">${escapeHtml(employee.name)}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-envelope"></i> Email</div>
                        <div class="detail-value"><a href="mailto:${employee.email}">${escapeHtml(employee.email)}</a></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-phone"></i> Phone</div>
                        <div class="detail-value">${escapeHtml(employee.phone || 'Not provided')}</div>
                    </div>
                    ${employee.address ? `
                    <div class="detail-item detail-item-full">
                        <div class="detail-label"><i class="fas fa-map-marker-alt"></i> Address</div>
                        <div class="detail-value">${escapeHtml(employee.address)}</div>
                    </div>
                    ` : ''}
                </div>
            </div>
            
            <!-- Employment Details -->
            <div class="employee-detail-section">
                <div class="section-title-view"><i class="fas fa-briefcase"></i> Employment Details</div>
                
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-city"></i> Company</div>
                        <div class="detail-value">${escapeHtml(employee.company)}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-building"></i> Department</div>
                        <div class="detail-value">${escapeHtml(employee.department)}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-user-tie"></i> Position</div>
                        <div class="detail-value">${escapeHtml(employee.position)}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-calendar-plus"></i> Join Date</div>
                        <div class="detail-value">${employee.joinDate}</div>
                    </div>
                    <div class="detail-item detail-item-full">
                        <div class="detail-label"><i class="fas fa-money-bill-wave"></i> Salary</div>
                        <div class="detail-value">
                            ${employee.salary ? `<span class="salary-badge">₱${employee.salary.toLocaleString()}</span>` : 'Not specified'}
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Emergency Contact -->
            ${employee.emergencyContactName || employee.emergencyContactPhone ? `
            <div class="employee-detail-section">
                <div class="section-title-view"><i class="fas fa-phone-square"></i> Emergency Contact</div>
                
                <div class="info-card">
                    <div class="detail-grid">
                        ${employee.emergencyContactName ? `
                        <div class="detail-item">
                            <div class="detail-label"><i class="fas fa-user"></i> Contact Name</div>
                            <div class="detail-value">${escapeHtml(employee.emergencyContactName)}</div>
                        </div>
                        ` : ''}
                        ${employee.emergencyContactPhone ? `
                        <div class="detail-item">
                            <div class="detail-label"><i class="fas fa-phone"></i> Contact Phone</div>
                            <div class="detail-value">${escapeHtml(employee.emergencyContactPhone)}</div>
                        </div>
                        ` : ''}
                        ${employee.emergencyContactRelation ? `
                        <div class="detail-item detail-item-full">
                            <div class="detail-label"><i class="fas fa-heart"></i> Relationship</div>
                            <div class="detail-value">${escapeHtml(employee.emergencyContactRelation)}</div>
                        </div>
                        ` : ''}
                    </div>
                </div>
            </div>
            ` : ''}
            
            <!-- Government IDs -->
            ${employee.sss || employee.philhealth || employee.pagibig || employee.tin ? `
            <div class="employee-detail-section">
                <div class="section-title-view"><i class="fas fa-id-card"></i> Government IDs</div>
                
                <div class="detail-grid">
                    ${employee.sss ? `
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-shield-alt"></i> SSS Number</div>
                        <div class="detail-value"><span class="id-badge">${escapeHtml(employee.sss)}</span></div>
                    </div>
                    ` : ''}
                    ${employee.philhealth ? `
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-heartbeat"></i> PhilHealth</div>
                        <div class="detail-value"><span class="id-badge">${escapeHtml(employee.philhealth)}</span></div>
                    </div>
                    ` : ''}
                    ${employee.pagibig ? `
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-home"></i> Pag-IBIG</div>
                        <div class="detail-value"><span class="id-badge">${escapeHtml(employee.pagibig)}</span></div>
                    </div>
                    ` : ''}
                    ${employee.tin ? `
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-file-invoice-dollar"></i> TIN</div>
                        <div class="detail-value"><span class="id-badge">${escapeHtml(employee.tin)}</span></div>
                    </div>
                    ` : ''}
                </div>
            </div>
            ` : ''}
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">
                    <i class="fas fa-times"></i> Close
                </button>
                <button type="button" class="btn btn-info" onclick="emailEmployee('${employee.email}')">
                    <i class="fas fa-envelope"></i> Send Email
                </button>
                <button type="button" class="btn btn-primary" onclick="closeModal(); setTimeout(() => editEmployee(${employee.id}), 100)">
                    <i class="fas fa-edit"></i> Edit Employee
                </button>
            </div>
        </div>
    `;

    openModal('Employee Details', content);
}

function emailEmployee(email) {
    if (email) {
        window.location.href = `mailto:${email}`;
    } else {
        if (typeof showToast === 'function') {
            showToast('No email available', 'warning');
        }
    }
}

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>]/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;' })[m] || m);
}

// Make function globally available
window.viewEmployee = viewEmployee;
window.emailEmployee = emailEmployee;
</script>
