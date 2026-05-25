<!-- modal-view-correction.php -->
<script>
function viewCorrection(correctionId) {
    const correction = window.corrections.find(c => c.id === correctionId);
    
    if (!correction) {
        showToast('Correction not found', 'error');
        return;
    }
    
    const statusConfig = {
        'Pending': { bg: '#fef3c7', color: '#b45309', icon: 'fa-hourglass-half' },
        'Approved': { bg: '#dcfce7', color: '#16a34a', icon: 'fa-check-circle' },
        'Rejected': { bg: '#fee2e2', color: '#dc2626', icon: 'fa-times-circle' }
    }[correction.status] || { bg: '#f1f5f9', color: '#64748b', icon: 'fa-circle' };
    
    const typeConfig = {
        'Late': { bg: '#fef3c7', color: '#b45309', icon: 'fa-clock' },
        'Early Departure': { bg: '#dbeafe', color: '#2563eb', icon: 'fa-door-open' },
        'Missed Entry': { bg: '#f3e8ff', color: '#9333ea', icon: 'fa-exclamation-triangle' },
        'Overtime Discrepancy': { bg: '#f1f5f9', color: '#64748b', icon: 'fa-business-time' }
    }[correction.type] || { bg: '#f1f5f9', color: '#64748b', icon: 'fa-circle' };
    
    const content = `
        <style>
            .correction-detail-section {
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
                background: ${correction.color};
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
                margin-bottom: 8px;
            }
            .status-badge-view {
                padding: 6px 14px;
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
            .type-badge-view {
                padding: 6px 14px;
                border-radius: 16px;
                font-size: 11px;
                font-weight: 500;
                background: ${typeConfig.bg};
                color: ${typeConfig.color};
                display: inline-flex;
                align-items: center;
                gap: 4px;
                margin-left: 8px;
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
            .time-badge {
                background: #f1f5f9;
                padding: 6px 12px;
                border-radius: 8px;
                font-weight: 600;
                color: #475569;
                font-size: 13px;
                display: inline-block;
            }
            .time-in {
                color: #10b981;
            }
            .time-out {
                color: #ef4444;
            }
            .reason-box {
                background: #f8fafc;
                border: 1px solid #e2e8f0;
                border-radius: 12px;
                padding: 14px;
                margin-top: 8px;
            }
            .reason-text {
                font-size: 13px;
                color: #475569;
                line-height: 1.6;
            }
            .approval-actions {
                display: flex;
                gap: 8px;
                margin-top: 16px;
            }
        </style>
        <div class="modal-view-correction">
            <div class="view-header">
                <img src="${correction.profilePhoto || '/3ME/assets/images/default-avatar.png'}" class="employee-avatar-large" style="background: none; object-fit: cover;" />
                <div class="employee-info-large">
                    <h3>${escapeHtml(correction.employeeName)}</h3>
                    <div class="employee-meta">
                        <i class="fas fa-envelope"></i> ${escapeHtml(correction.employeeEmail)}
                    </div>
                    <div>
                        <span class="status-badge-view">
                            <i class="fas ${statusConfig.icon}"></i> ${correction.status}
                        </span>
                        <span class="type-badge-view">
                            <i class="fas ${typeConfig.icon}"></i> ${correction.type}
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Correction Details -->
            <div class="correction-detail-section">
                <div class="section-title-view"><i class="fas fa-calendar-alt"></i> Correction Details</div>
                
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-calendar"></i> Original Date</div>
                        <div class="detail-value">${correction.originalDate}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-tag"></i> Type</div>
                        <div class="detail-value">${correction.type}</div>
                    </div>
                    ${correction.timeIn || correction.timeOut ? `
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-sign-in-alt"></i> Corrected Time In</div>
                        <div class="detail-value">
                            ${correction.timeIn ? `<span class="time-badge time-in">${correction.timeIn}</span>` : '<span style="color: #94a3b8;">Not specified</span>'}
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-sign-out-alt"></i> Corrected Time Out</div>
                        <div class="detail-value">
                            ${correction.timeOut ? `<span class="time-badge time-out">${correction.timeOut}</span>` : '<span style="color: #94a3b8;">Not specified</span>'}
                        </div>
                    </div>
                    ` : ''}
                </div>
            </div>
            
            <!-- Reason -->
            <div class="correction-detail-section">
                <div class="section-title-view"><i class="fas fa-comment-alt"></i> Reason for Correction</div>
                
                <div class="reason-box">
                    <div class="reason-text">${escapeHtml(correction.reason)}</div>
                </div>
            </div>
            
            <!-- Request Information -->
            <div class="correction-detail-section">
                <div class="section-title-view"><i class="fas fa-info-circle"></i> Request Information</div>
                
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-user"></i> Requested By</div>
                        <div class="detail-value">${correction.requestedBy || 'Unknown'}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-calendar-plus"></i> Request Date</div>
                        <div class="detail-value">${correction.requestedDate || 'N/A'}</div>
                    </div>
                    ${correction.approvedBy ? `
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-user-check"></i> ${correction.status} By</div>
                        <div class="detail-value">${correction.approvedBy}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-calendar-check"></i> ${correction.status} Date</div>
                        <div class="detail-value">${correction.approvedDate || 'N/A'}</div>
                    </div>
                    ` : ''}
                </div>
            </div>
            
            ${correction.status === 'Pending' ? `
            <div class="approval-actions">
                <button type="button" class="btn btn-success" onclick="closeModal(); setTimeout(() => approveCorrection('${correction.id}'), 100)" style="flex: 1;">
                    <i class="fas fa-check-circle"></i> Approve Request
                </button>
                <button type="button" class="btn btn-danger" onclick="closeModal(); setTimeout(() => rejectCorrection('${correction.id}'), 100)" style="flex: 1;">
                    <i class="fas fa-times-circle"></i> Reject Request
                </button>
            </div>
            ` : ''}
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">
                    <i class="fas fa-times"></i> Close
                </button>
                <button type="button" class="btn btn-primary" onclick="closeModal(); setTimeout(() => editCorrection('${correction.id}'), 100)">
                    <i class="fas fa-edit"></i> Edit Request
                </button>
            </div>
        </div>
    `;
    
    openModal('Correction Request Details', content);
}

// Make function globally available
window.viewCorrection = viewCorrection;
</script>
