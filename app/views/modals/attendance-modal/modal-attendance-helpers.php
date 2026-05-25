<!-- modal-attendance-helpers.php - Helper functions for attendance modals -->
<script>
// Helper function to format time for input (HH:MM)
function formatTimeForInput(timeString) {
    if (!timeString) return '';
    
    // Handle different time formats
    if (timeString.includes(':')) {
        const parts = timeString.split(':');
        return `${parts[0].padStart(2, '0')}:${parts[1].padStart(2, '0')}`;
    }
    
    return timeString;
}

// Helper function to format date for input (YYYY-MM-DD)
function formatDateForInput(dateString) {
    if (!dateString) return '';
    
    let date;
    if (dateString.includes('/')) {
        date = new Date(dateString);
    } else if (dateString.includes('-')) {
        date = new Date(dateString);
    } else {
        date = new Date(dateString);
    }
    
    if (isNaN(date.getTime())) return '';
    
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    
    return `${year}-${month}-${day}`;
}

// Helper function to format date for display (Jan 15, 2024)
function formatDateForDisplay(dateString) {
    if (!dateString) return '';
    
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return dateString;
    
    return date.toLocaleDateString('en-US', { 
        month: 'short', 
        day: 'numeric', 
        year: 'numeric' 
    });
}

// Helper function to calculate duration between two times
function calculateDuration(startTime, endTime) {
    if (!startTime || !endTime) return '0h 0m';
    
    const [startHour, startMin] = startTime.split(':').map(Number);
    const [endHour, endMin] = endTime.split(':').map(Number);
    
    let totalMinutes = (endHour * 60 + endMin) - (startHour * 60 + startMin);
    
    // Handle overnight shifts
    if (totalMinutes < 0) {
        totalMinutes += 24 * 60;
    }
    
    const hours = Math.floor(totalMinutes / 60);
    const minutes = totalMinutes % 60;
    
    return `${hours}h ${minutes}m`;
}

// Helper function to validate time format
function isValidTime(timeString) {
    const timeRegex = /^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/;
    return timeRegex.test(timeString);
}

// Helper function to show validation errors
function showValidationErrors(errors) {
    if (errors.length === 0) return;
    
    const errorHtml = `
        <div style="
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 12px;
            padding: 14px 16px;
            margin-bottom: 16px;
        ">
            <div style="
                display: flex;
                align-items: flex-start;
                gap: 10px;
            ">
                <i class="fas fa-exclamation-circle" style="
                    color: #ef4444;
                    font-size: 18px;
                    margin-top: 2px;
                "></i>
                <div style="flex: 1;">
                    <div style="
                        font-weight: 600;
                        color: #991b1b;
                        margin-bottom: 6px;
                        font-size: 13px;
                    ">Please fix the following errors:</div>
                    <ul style="
                        margin: 0;
                        padding-left: 20px;
                        color: #dc2626;
                        font-size: 12px;
                        line-height: 1.6;
                    ">
                        ${errors.map(err => `<li>${err}</li>`).join('')}
                    </ul>
                </div>
            </div>
        </div>
    `;
    
    const modalBody = document.getElementById('modalBody');
    const existingError = modalBody.querySelector('.validation-errors');
    if (existingError) {
        existingError.remove();
    }
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'validation-errors';
    errorDiv.innerHTML = errorHtml;
    modalBody.insertBefore(errorDiv, modalBody.firstChild);
    
    modalBody.scrollTop = 0;
    showToast('Please fix the validation errors', 'error');
}

// Toast notification function
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    const bgColor = type === 'success' ? '#10b981' : 
                    type === 'warning' ? '#f59e0b' : 
                    type === 'error' ? '#ef4444' : 
                    type === 'info' ? '#3b82f6' : '#1e293b';
    
    toast.style.cssText = `
        position: fixed; 
        bottom: 24px; 
        right: 24px; 
        background: ${bgColor}; 
        color: white; 
        padding: 12px 20px; 
        border-radius: 12px; 
        font-size: 13px; 
        z-index: 10001; 
        animation: slideIn 0.3s ease; 
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        display: flex;
        align-items: center;
        gap: 8px;
    `;
    
    const icons = {
        'success': 'fa-check-circle',
        'error': 'fa-times-circle',
        'warning': 'fa-exclamation-triangle',
        'info': 'fa-info-circle'
    };
    
    const icon = icons[type] || 'fa-info-circle';
    toast.innerHTML = `<i class="fas ${icon}"></i><span>${message}</span>`;
    
    document.body.appendChild(toast);
    
    setTimeout(() => { 
        toast.style.opacity = '0'; 
        toast.style.transition = 'opacity 0.3s'; 
        setTimeout(() => toast.remove(), 300); 
    }, 3000);
}

// Export preview data function
function exportPreviewData(importId) {
    const previewData = window.importPreviewData[importId] || generateDefaultPreviewData(importId);
    
    if (!previewData || !previewData.headers || !previewData.rows) {
        showToast('No data available to export', 'warning');
        return;
    }
    
    // Create CSV content
    const csvContent = [
        previewData.headers.join(','),
        ...previewData.rows.map(row => row.map(cell => `"${String(cell).replace(/"/g, '""')}"`).join(','))
    ].join('\n');
    
    // Create download link
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    
    link.setAttribute('href', url);
    link.setAttribute('download', `import_${importId}_${new Date().getTime()}.csv`);
    link.style.visibility = 'hidden';
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    showToast('Data exported successfully!', 'success');
}

// View import details function
function viewImportDetails(importId) {
    const imp = window.importHistory.find(i => i.id === importId);
    if (!imp) {
        showToast('Import not found', 'error');
        return;
    }
    
    const statusConfig = {
        'Success': { bg: '#dcfce7', color: '#16a34a', icon: 'fa-check-circle' },
        'Partial': { bg: '#fef3c7', color: '#b45309', icon: 'fa-exclamation-triangle' },
        'Failed': { bg: '#fee2e2', color: '#dc2626', icon: 'fa-times-circle' }
    }[imp.status] || { bg: '#f1f5f9', color: '#64748b', icon: 'fa-circle' };
    
    const successRate = imp.totalRecords > 0 ? ((imp.successful / imp.totalRecords) * 100).toFixed(1) : 0;
    
    const content = `
        <style>
            .import-detail-section {
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
            }
            .detail-value {
                font-size: 13px;
                font-weight: 500;
                color: #1e293b;
            }
            .status-badge-large {
                padding: 8px 16px;
                border-radius: 20px;
                font-size: 13px;
                font-weight: 500;
                background: ${statusConfig.bg};
                color: ${statusConfig.color};
                display: inline-flex;
                align-items: center;
                gap: 6px;
            }
            .progress-bar-large {
                width: 100%;
                height: 12px;
                background: #e2e8f0;
                border-radius: 6px;
                overflow: hidden;
                margin-top: 8px;
            }
            .progress-fill-large {
                height: 100%;
                background: ${statusConfig.color};
                border-radius: 6px;
                transition: width 0.3s ease;
            }
            .stat-card {
                background: #f8fafc;
                border: 1px solid #e2e8f0;
                border-radius: 12px;
                padding: 16px;
                text-align: center;
            }
            .stat-value {
                font-size: 24px;
                font-weight: 700;
                color: #1e293b;
                margin-bottom: 4px;
            }
            .stat-label {
                font-size: 11px;
                color: #64748b;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
        </style>
        <div class="modal-view-import">
            <div class="import-detail-section">
                <div class="detail-grid">
                    <div class="detail-item detail-item-full">
                        <div class="detail-label">Status</div>
                        <div class="detail-value">
                            <span class="status-badge-large">
                                <i class="fas ${statusConfig.icon}"></i> ${imp.status}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="import-detail-section">
                <div class="section-title-view"><i class="fas fa-chart-bar"></i> Import Statistics</div>
                <div class="detail-grid">
                    <div class="stat-card">
                        <div class="stat-value" style="color: #10b981;">${imp.successful}</div>
                        <div class="stat-label">Successful</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value" style="color: #ef4444;">${imp.failed}</div>
                        <div class="stat-label">Failed</div>
                    </div>
                    <div class="stat-card" style="grid-column: span 2;">
                        <div class="stat-value">${imp.totalRecords}</div>
                        <div class="stat-label">Total Records</div>
                        <div class="progress-bar-large">
                            <div class="progress-fill-large" style="width: ${successRate}%;"></div>
                        </div>
                        <div style="margin-top: 8px; font-size: 12px; color: #64748b;">
                            ${successRate}% Success Rate
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="import-detail-section">
                <div class="section-title-view"><i class="fas fa-info-circle"></i> Import Details</div>
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">File Name</div>
                        <div class="detail-value">${escapeHtml(imp.fileName)}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">File Type</div>
                        <div class="detail-value">${imp.fileType}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Import Date</div>
                        <div class="detail-value">${imp.importDate}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Imported By</div>
                        <div class="detail-value">${imp.importedBy}</div>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">
                    <i class="fas fa-times"></i> Close
                </button>
                <button type="button" class="btn btn-info" onclick="closeModal(); setTimeout(() => navigateToPreviewData('${imp.id}', '${escapeHtml(imp.fileName)}'), 100)">
                    <i class="fas fa-eye"></i> View Data
                </button>
                <button type="button" class="btn btn-primary" onclick="downloadImportLog('${imp.id}')">
                    <i class="fas fa-download"></i> Download Log
                </button>
            </div>
        </div>
    `;
    
    openModal('Import Details', content);
}

// Download import log function
function downloadImportLog(importId) {
    const imp = window.importHistory.find(i => i.id === importId);
    if (!imp) {
        showToast('Import not found', 'error');
        return;
    }
    
    const logContent = `
Import Log Report
==================
Import ID: ${imp.id}
File Name: ${imp.fileName}
File Type: ${imp.fileType}
Import Date: ${imp.importDate}
Imported By: ${imp.importedBy}
Status: ${imp.status}

Statistics:
-----------
Total Records: ${imp.totalRecords}
Successful: ${imp.successful}
Failed: ${imp.failed}
Success Rate: ${imp.totalRecords > 0 ? ((imp.successful / imp.totalRecords) * 100).toFixed(2) : 0}%

Generated: ${new Date().toLocaleString()}
    `.trim();
    
    const blob = new Blob([logContent], { type: 'text/plain;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    
    link.setAttribute('href', url);
    link.setAttribute('download', `import_log_${imp.id}.txt`);
    link.style.visibility = 'hidden';
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    showToast('Import log downloaded!', 'success');
}

// View errors function
function viewErrors(importId) {
    const imp = window.importHistory.find(i => i.id === importId);
    if (!imp) {
        showToast('Import not found', 'error');
        return;
    }
    
    // Generate mock error data
    const errors = [];
    for (let i = 0; i < imp.failed; i++) {
        errors.push({
            row: Math.floor(Math.random() * imp.totalRecords) + 1,
            field: ['Employee ID', 'Time In', 'Time Out', 'Date'][Math.floor(Math.random() * 4)],
            error: ['Invalid format', 'Missing required field', 'Duplicate entry', 'Invalid date'][Math.floor(Math.random() * 4)]
        });
    }
    
    const content = `
        <style>
            .error-list {
                max-height: 400px;
                overflow-y: auto;
            }
            .error-item {
                background: #fef2f2;
                border: 1px solid #fecaca;
                border-radius: 8px;
                padding: 12px;
                margin-bottom: 8px;
            }
            .error-row {
                font-weight: 600;
                color: #991b1b;
                margin-bottom: 4px;
                font-size: 12px;
            }
            .error-details {
                font-size: 12px;
                color: #dc2626;
            }
        </style>
        <div class="modal-view-errors">
            <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 14px; margin-bottom: 16px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-exclamation-triangle" style="color: #ef4444; font-size: 20px;"></i>
                    <div>
                        <div style="font-weight: 600; color: #991b1b; margin-bottom: 4px;">
                            ${imp.failed} Error${imp.failed !== 1 ? 's' : ''} Found
                        </div>
                        <div style="font-size: 12px; color: #dc2626;">
                            The following records could not be imported
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="error-list">
                ${errors.map(err => `
                    <div class="error-item">
                        <div class="error-row">
                            <i class="fas fa-times-circle"></i> Row ${err.row}
                        </div>
                        <div class="error-details">
                            <strong>${err.field}:</strong> ${err.error}
                        </div>
                    </div>
                `).join('')}
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">
                    <i class="fas fa-times"></i> Close
                </button>
                <button type="button" class="btn btn-primary" onclick="downloadErrorReport('${imp.id}')">
                    <i class="fas fa-download"></i> Download Error Report
                </button>
            </div>
        </div>
    `;
    
    openModal('Import Errors', content);
}

// Download error report function
function downloadErrorReport(importId) {
    showToast('Error report downloaded!', 'success');
    closeModal();
}

// Escape HTML helper
function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>]/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;' })[m] || m);
}

// Make functions globally available
window.formatTimeForInput = formatTimeForInput;
window.formatDateForInput = formatDateForInput;
window.formatDateForDisplay = formatDateForDisplay;
window.calculateDuration = calculateDuration;
window.isValidTime = isValidTime;
window.showValidationErrors = showValidationErrors;
window.showToast = showToast;
window.exportPreviewData = exportPreviewData;
window.viewImportDetails = viewImportDetails;
window.downloadImportLog = downloadImportLog;
window.viewErrors = viewErrors;
window.downloadErrorReport = downloadErrorReport;
</script>
