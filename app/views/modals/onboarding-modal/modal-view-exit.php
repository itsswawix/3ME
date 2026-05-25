<?php
/**
 * Modal for viewing exit record details
 */
?>

<script>
function viewExit(exitId) {
    const exit = window.exitRecords.find(r => r.id === exitId);
    if (!exit) {
        showToast('Exit record not found', 'error');
        return;
    }

    const content = `
        <div class="exit-profile">
            <!-- Employee Header -->
            <div class="profile-header">
                <div class="profile-avatar-lg" style="background: ${exit.color};">${exit.avatar}</div>
                <div class="profile-info">
                    <h3>${escapeHtml(exit.employeeName)}</h3>
                    <div class="position">${escapeHtml(exit.position)}</div>
                    <span class="badge ${exit.status === 'Cleared' ? 'badge-success' : exit.status === 'Pending' ? 'badge-warning' : 'badge-secondary'}">${exit.status}</span>
                </div>
            </div>

            <!-- Exit Details -->
            <div class="detail-section">
                <div class="section-title-sm">
                    <i class="fas fa-door-open"></i> Exit Information
                </div>
                <div class="detail-grid">
                    <div class="detail-item">
                        <span class="detail-label">Last Working Day</span>
                        <span class="detail-value">${exit.lastWorkingDay}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Exit Status</span>
                        <span class="detail-value">
                            <span class="badge ${exit.status === 'Cleared' ? 'badge-success' : exit.status === 'Pending' ? 'badge-warning' : 'badge-secondary'}">${exit.status}</span>
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Clearance Approved By</span>
                        <span class="detail-value">${exit.clearanceApprovedBy || 'Not yet approved'}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Employee ID</span>
                        <span class="detail-value" style="font-family: monospace;">${exit.employeeId}</span>
                    </div>
                </div>
            </div>

            <!-- Employment Details -->
            <div class="detail-section">
                <div class="section-title-sm">
                    <i class="fas fa-briefcase"></i> Employment Details
                </div>
                <div class="detail-grid">
                    <div class="detail-item">
                        <span class="detail-label">Company</span>
                        <span class="detail-value">${escapeHtml(exit.company)}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Department</span>
                        <span class="detail-value">${escapeHtml(exit.department)}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Email</span>
                        <span class="detail-value">${escapeHtml(exit.employeeEmail)}</span>
                    </div>
                </div>
            </div>

            <!-- Reason for Leaving -->
            <div class="detail-section">
                <div class="section-title-sm">
                    <i class="fas fa-comment-alt"></i> Reason for Leaving
                </div>
                <div class="reason-text">
                    ${escapeHtml(exit.reason)}
                </div>
            </div>

            ${exit.notes ? `
            <!-- Additional Notes -->
            <div class="detail-section">
                <div class="section-title-sm">
                    <i class="fas fa-sticky-note"></i> Additional Notes
                </div>
                <div class="notes-text">
                    ${escapeHtml(exit.notes)}
                </div>
            </div>
            ` : ''}

            ${exit.resignationLetter ? `
            <!-- Documents -->
            <div class="detail-section">
                <div class="section-title-sm">
                    <i class="fas fa-file-alt"></i> Documents
                </div>
                <div class="document-item">
                    <i class="fas fa-file-pdf"></i>
                    <span>Resignation Letter</span>
                    <button class="btn btn-secondary btn-sm" onclick="viewResignationLetter('${exit.id}')">
                        <i class="fas fa-eye"></i> View
                    </button>
                </div>
            </div>
            ` : ''}

            <!-- Action Buttons -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">
                    <i class="fas fa-times"></i> Close
                </button>
                <button type="button" class="btn btn-primary" onclick="closeModal(); editExit('${exit.id}')">
                    <i class="fas fa-edit"></i> Edit Record
                </button>
                ${exit.status === 'Pending' ? `
                <button type="button" class="btn btn-success" onclick="closeModal(); approveClearance('${exit.id}')">
                    <i class="fas fa-check-double"></i> Approve Clearance
                </button>
                ` : ''}
            </div>
        </div>

        <style>
            .reason-text, .notes-text {
                background: #f8fafc;
                padding: 16px;
                border-radius: 12px;
                border: 1px solid #e2e8f0;
                color: #1e293b;
                line-height: 1.6;
                font-size: 14px;
            }
            .document-item {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 12px 16px;
                background: #f8fafc;
                border-radius: 12px;
                border: 1px solid #e2e8f0;
            }
            .document-item i {
                color: #ef4444;
                font-size: 18px;
            }
            .document-item span {
                flex: 1;
                font-weight: 500;
                color: #1e293b;
            }
            .btn-sm {
                padding: 6px 12px;
                font-size: 12px;
            }
        </style>
    `;

    openModal('Exit Record Details', content);
}
</script>