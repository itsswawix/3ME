<!-- modal-view-roster.php -->
<script>
async function viewRoster(rosterId) {
    const roster = window.rosters.find(r => r.id === rosterId);
    
    if (!roster) {
        showToast('Roster not found', 'error');
        return;
    }
    
    // Load companies to get company name
    const companies = await window.loadCompaniesFromDB();
    const company = companies.find(c => c.id === roster.companyId);
    const companyName = company ? company.name : roster.companyId;
    
    const duration = calculateDuration(roster.startTime, roster.endTime);
    
    const content = `
        <style>
            .roster-detail-section {
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
                background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
                color: white;
                padding: 20px;
                border-radius: 12px;
                margin-bottom: 20px;
            }
            .view-header h3 {
                font-size: 18px;
                font-weight: 600;
                margin: 0 0 8px 0;
            }
            .view-header p {
                font-size: 13px;
                opacity: 0.9;
                margin: 0;
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
                font-size: 14px;
                display: inline-block;
            }
            .info-card {
                background: #f8fafc;
                border: 1px solid #e2e8f0;
                border-radius: 12px;
                padding: 14px;
                margin-top: 8px;
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
            .info-card-value {
                font-size: 16px;
                font-weight: 700;
                color: #4f46e5;
            }
        </style>
        <div class="modal-view-roster">
            <div class="view-header">
                <h3><i class="fas fa-clock"></i> ${escapeHtml(roster.shiftName)}</h3>
                <p><i class="fas fa-building"></i> ${escapeHtml(companyName)}</p>
            </div>
            
            <!-- Shift Timing -->
            <div class="roster-detail-section">
                <div class="section-title-view"><i class="fas fa-clock"></i> Shift Timing</div>
                
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-play"></i> Start Time</div>
                        <div class="detail-value">
                            <span class="time-badge">${roster.startTime}</span>
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-stop"></i> End Time</div>
                        <div class="detail-value">
                            <span class="time-badge">${roster.endTime}</span>
                        </div>
                    </div>
                </div>
                
                <div class="info-card">
                    <div class="info-card-title"><i class="fas fa-hourglass-half"></i> Total Duration</div>
                    <div class="info-card-value">${duration}</div>
                </div>
            </div>
            
            <!-- Break & Grace Period -->
            <div class="roster-detail-section">
                <div class="section-title-view"><i class="fas fa-coffee"></i> Break & Grace Period</div>
                
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-coffee"></i> Break Duration</div>
                        <div class="detail-value">${roster.breakDuration || 0} minutes</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-clock"></i> Late Grace Period</div>
                        <div class="detail-value">${roster.lateGracePeriod || 0} minutes</div>
                    </div>
                </div>
            </div>
            
            <!-- Overtime Rule -->
            <div class="roster-detail-section">
                <div class="section-title-view"><i class="fas fa-business-time"></i> Overtime Policy</div>
                
                <div class="info-card">
                    <div class="detail-value">
                        <i class="fas fa-check-circle" style="color: #10b981; margin-right: 6px;"></i>
                        ${escapeHtml(roster.overtimeRule)}
                    </div>
                </div>
            </div>
            
            <!-- Additional Details -->
            <div class="roster-detail-section">
                <div class="section-title-view"><i class="fas fa-info-circle"></i> Additional Details</div>
                
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-calendar-check"></i> Effective Date</div>
                        <div class="detail-value">${roster.effectiveDate}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label"><i class="fas fa-user"></i> Created By</div>
                        <div class="detail-value">${roster.createdBy || 'System'}</div>
                    </div>
                    ${roster.notes ? `
                    <div class="detail-item detail-item-full">
                        <div class="detail-label"><i class="fas fa-sticky-note"></i> Notes</div>
                        <div class="detail-value">${escapeHtml(roster.notes)}</div>
                    </div>
                    ` : ''}
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">
                    <i class="fas fa-times"></i> Close
                </button>
                <button type="button" class="btn btn-info" onclick="closeModal(); setTimeout(() => duplicateRoster('${roster.id}'), 100)">
                    <i class="fas fa-copy"></i> Duplicate
                </button>
                <button type="button" class="btn btn-primary" onclick="closeModal(); setTimeout(() => editRoster('${roster.id}'), 100)">
                    <i class="fas fa-edit"></i> Edit Roster
                </button>
            </div>
        </div>
    `;
    
    openModal('Roster Details', content);
}

// Make function globally available
window.viewRoster = viewRoster;
</script>
