<!-- modal-view-requisition.php -->
<script>
function viewRequisition(id) {
    const req = window.requisitions.find(r => r.id === id);
    if (!req) return;
    
    const statusConfig = {
        'Draft': { bg: '#f1f5f9', color: '#64748b', icon: 'fa-pencil' },
        'Pending': { bg: '#fef3c7', color: '#b45309', icon: 'fa-clock' },
        'Approved': { bg: '#dcfce7', color: '#16a34a', icon: 'fa-check-circle' },
        'Rejected': { bg: '#fee2e2', color: '#dc2626', icon: 'fa-times-circle' },
        'Filled': { bg: '#dbeafe', color: '#2563eb', icon: 'fa-user-check' },
        'Cancelled': { bg: '#f1f5f9', color: '#64748b', icon: 'fa-ban' }
    }[req.status] || { bg: '#f1f5f9', color: '#64748b', icon: 'fa-circle' };
    
    const skills = req.requiredSkills.split(',').map(s => s.trim());
    const fillPercentage = ((req.filledPositions || 0) / req.vacancies) * 100;
    
    const content = `
        <style>
            .modal-view-requisition * { margin: 0; box-sizing: border-box; }
            .modal-view-requisition { font-family: 'Inter', sans-serif; max-width: 600px; width: 100%; }
            .view-header { 
                display: flex; align-items: flex-start; gap: 16px; margin-bottom: 24px;
                padding-bottom: 20px; border-bottom: 1px solid #eef2ff;
            }
            .req-icon-large { 
                width: 64px; height: 64px; border-radius: 20px;
                background: linear-gradient(145deg, #4f46e5, #7c3aed);
                display: flex; align-items: center; justify-content: center;
                color: white; font-size: 1.8rem;
            }
            .req-info-large h3 { font-size: 1.2rem; font-weight: 600; color: #0f172a; margin-bottom: 4px; }
            .req-meta { font-size: 0.8rem; color: #64748b; display: flex; gap: 8px; flex-wrap: wrap; }
            .status-badge-view { 
                padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 500;
                background: ${statusConfig.bg}; color: ${statusConfig.color};
                display: inline-flex; align-items: center; gap: 4px; margin-top: 8px;
            }
            .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px 24px; margin-bottom: 24px; }
            .detail-item { display: flex; flex-direction: column; }
            .detail-label { 
                font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;
                color: #94a3b8; margin-bottom: 6px; display: flex; align-items: center; gap: 6px;
            }
            .detail-label i { color: #4f46e5; width: 16px; }
            .detail-value { font-size: 0.95rem; font-weight: 500; color: #1e293b; }
            .progress-container-view { display: flex; align-items: center; gap: 12px; }
            .progress-bar-view { width: 100px; height: 6px; background: #e2e8f0; border-radius: 3px; overflow: hidden; }
            .progress-fill-view { height: 100%; background: #4f46e5; border-radius: 3px; }
            .skills-container { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 8px; }
            .skill-tag-view { 
                background: #f1f5f9; padding: 6px 14px; border-radius: 20px;
                font-size: 0.8rem; color: #1e293b;
            }
            .description-section { margin-top: 20px; padding-top: 20px; border-top: 1px solid #eef2ff; }
            .description-section h4 { 
                font-size: 0.9rem; font-weight: 600; color: #0f172a; margin-bottom: 12px;
                display: flex; align-items: center; gap: 8px;
            }
            .description-content { 
                background: #f8fafc; padding: 16px; border-radius: 16px;
                font-size: 0.9rem; line-height: 1.6; color: #334155;
            }
        </style>
        <div class="modal-view-requisition">
            <div class="view-header">
                <div class="req-icon-large"><i class="fas fa-briefcase"></i></div>
                <div class="req-info-large">
                    <h3>${escapeHtml(req.jobTitle)}</h3>
                    <div class="req-meta">
                        <span><i class="fas fa-building"></i> ${escapeHtml(req.department)}</span>
                    </div>
                    <span class="status-badge-view">
                        <i class="fas ${statusConfig.icon}"></i> ${req.status}
                    </span>
                </div>
            </div>
            
            <div class="detail-grid">
                <div class="detail-item">
                    <div class="detail-label"> Company</div>
                    <div class="detail-value">${escapeHtml(req.company)}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label"> Employment Type</div>
                    <div class="detail-value">${escapeHtml(req.employmentType)}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label"> Position Level</div>
                    <div class="detail-value">${escapeHtml(req.positionLevel)}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label"> Salary Range</div>
                    <div class="detail-value">
                        ₱${req.salaryRangeMin ? req.salaryRangeMin.toLocaleString() : '--'} - 
                        ₱${req.salaryRangeMax ? req.salaryRangeMax.toLocaleString() : '--'}
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-label"> Vacancies</div>
                    <div class="detail-value">
                        <div class="progress-container-view">
                            <span>${req.filledPositions || 0}/${req.vacancies} filled</span>
                            <div class="progress-bar-view">
                                <div class="progress-fill-view" style="width: ${fillPercentage}%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-label"> Requested Start</div>
                    <div class="detail-value">${req.requestedStartDate || 'Not specified'}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label"> Budget Code</div>
                    <div class="detail-value">${req.budgetCode || 'N/A'}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label"> Submission Date</div>
                    <div class="detail-value">${req.submissionDate}</div>
                </div>
            </div>
            
            <div style="margin-bottom: 20px;">
                <div class="detail-label"><i class="fas fa-code"></i> Required Skills</div>
                <div class="skills-container">
                    ${skills.map(s => `<span class="skill-tag-view">${escapeHtml(s)}</span>`).join('')}
                </div>
            </div>
            
            <div class="description-section">
                <h4><i class="fas fa-file-alt"></i> Job Description</h4>
                <div class="description-content">${escapeHtml(req.jobDescription)}</div>
            </div>
        </div>
    `;
    
    const footer = `
        <button type="button" class="btn btn-secondary" onclick="closeModal()"><i class="fas fa-times"></i> Close</button>
        <button type="button" class="btn btn-primary" onclick="closeModal(); editRequisition('${id}');">
            <i class="fas fa-edit"></i> Edit
        </button>
        ${req.status === 'Pending' ? `
            <button type="button" class="btn btn-success" onclick="approveRequisition('${id}'); closeModal();">
                <i class="fas fa-check"></i> Approve
            </button>
        ` : ''}
        ${req.status === 'Approved' ? `
            <button type="button" class="btn btn-primary" onclick="postRequisition('${id}'); closeModal();" style="background: #f59e0b;">
                <i class="fas fa-bullhorn"></i> Post Job
            </button>
        ` : ''}
    `;
    
    openModal('Requisition Details', content, footer);
}
</script>