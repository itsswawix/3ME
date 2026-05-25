<!-- modal-view-org.php -->
<script>
// Company view modal
function openViewCompanyModal(company) {
    const content = `
        <style>
            .modal-view-org * { margin: 0; box-sizing: border-box; }
            .modal-view-org { font-family: 'Inter', sans-serif; max-width: 550px; width: 100%; }
            .view-header { display: flex; align-items: flex-start; gap: 16px; margin-bottom: 24px; padding-bottom: 20px; border-bottom: 1px solid #eef2ff; }
            .company-icon-large { width: 64px; height: 64px; border-radius: 20px; background: linear-gradient(145deg, #4f46e5, #7c3aed); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.8rem; }
            .info-large h3 { font-size: 1.2rem; font-weight: 600; color: #0f172a; margin-bottom: 4px; }
            .meta { font-size: 0.8rem; color: #64748b; display: flex; gap: 8px; flex-wrap: wrap; }
            .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px 24px; margin-bottom: 24px; }
            .detail-label { font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 6px; display: flex; align-items: center; gap: 6px; }
            .detail-label i { color: #4f46e5; width: 16px; }
            .detail-value { font-size: 0.95rem; font-weight: 500; color: #1e293b; }
            .modal-buttons { display: flex; justify-content: flex-end; gap: 12px; margin-top: 28px; padding-top: 20px; border-top: 1px solid #f1f5f9; }
            .btn-primary { background: #4f46e5; color: white; border: none; padding: 10px 22px; border-radius: 24px; cursor: pointer; display: flex; align-items: center; gap: 6px; }
            .btn-secondary { background: white; color: #475569; border: 1px solid #e2e8f0; padding: 10px 22px; border-radius: 24px; cursor: pointer; }
        </style>
        <div class="modal-view-org">
            <div class="view-header">
                <div class="company-icon-large"><i class="fas fa-building"></i></div>
                <div class="info-large">
                    <h3>${escapeHtml(company.name)}</h3>
                    <div class="meta">
                        <span><i class="fas fa-tag"></i> ${escapeHtml(company.code)}</span>
                        <span><i class="fas fa-fingerprint"></i> ${company.id}</span>
                    </div>
                    <div style="margin-top: 8px;"><span class="badge badge-success">${company.status}</span></div>
                </div>
            </div>
            <div class="detail-grid">
                <div class="detail-item"><div class="detail-label"><i class="fas fa-users"></i> Employees</div><div class="detail-value">${company.employeeCount || 0}</div></div>
                <div class="detail-item"><div class="detail-label"><i class="fas fa-calendar"></i> Status</div><div class="detail-value">${company.status}</div></div>
            </div>
            <div class="modal-buttons">
                <button type="button" class="btn-secondary" onclick="closeModal()"><i class="fas fa-times"></i> Close</button>
                <button type="button" class="btn-primary" onclick="closeModal(); editCompany('${company.id}');"><i class="fas fa-edit"></i> Edit</button>
            </div>
        </div>
    `;
    openModal('Company Details', content);
}

// Department view modal
function openViewDepartmentModal(dept) {
    const content = `
        <style>
            .modal-view-org * { margin: 0; box-sizing: border-box; }
            .modal-view-org { font-family: 'Inter', sans-serif; max-width: 550px; width: 100%; }
            .view-header { display: flex; align-items: flex-start; gap: 16px; margin-bottom: 24px; padding-bottom: 20px; border-bottom: 1px solid #eef2ff; }
            .company-icon-large { width: 64px; height: 64px; border-radius: 20px; background: linear-gradient(145deg, #7c3aed, #9333ea); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.8rem; }
            .info-large h3 { font-size: 1.2rem; font-weight: 600; color: #0f172a; margin-bottom: 4px; }
            .meta { font-size: 0.8rem; color: #64748b; display: flex; gap: 8px; flex-wrap: wrap; }
            .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px 24px; margin-bottom: 24px; }
            .detail-label { font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 6px; display: flex; align-items: center; gap: 6px; }
            .detail-label i { color: #4f46e5; width: 16px; }
            .detail-value { font-size: 0.95rem; font-weight: 500; color: #1e293b; }
            .modal-buttons { display: flex; justify-content: flex-end; gap: 12px; margin-top: 28px; padding-top: 20px; border-top: 1px solid #f1f5f9; }
            .btn-primary { background: #4f46e5; color: white; border: none; padding: 10px 22px; border-radius: 24px; cursor: pointer; display: flex; align-items: center; gap: 6px; }
            .btn-secondary { background: white; color: #475569; border: 1px solid #e2e8f0; padding: 10px 22px; border-radius: 24px; cursor: pointer; }
        </style>
        <div class="modal-view-org">
            <div class="view-header">
                <div class="company-icon-large"><i class="fas fa-sitemap"></i></div>
                <div class="info-large">
                    <h3>${escapeHtml(dept.name)}</h3>
                    <div class="meta">
                        <span><i class="fas fa-tag"></i> ${escapeHtml(dept.code)}</span>
                        <span><i class="fas fa-building"></i> ${escapeHtml(dept.companyName || '')}</span>
                    </div>
                    <div style="margin-top: 8px;"><span class="badge badge-success">${dept.status}</span></div>
                </div>
            </div>
            <div class="detail-grid">
                <div class="detail-item"><div class="detail-label"><i class="fas fa-user-tie"></i> Department Head</div><div class="detail-value">${escapeHtml(dept.head || '—')}</div></div>
                <div class="detail-item"><div class="detail-label"><i class="fas fa-users"></i> Employees</div><div class="detail-value">${dept.employeeCount || 0}</div></div>
            </div>
            <div class="modal-buttons">
                <button type="button" class="btn-secondary" onclick="closeModal()"><i class="fas fa-times"></i> Close</button>
                <button type="button" class="btn-primary" onclick="closeModal(); editDepartment('${dept.id}');"><i class="fas fa-edit"></i> Edit</button>
            </div>
        </div>
    `;
    openModal('Department Details', content);
}

// Position view modal
function openViewPositionModal(pos) {
    const levelClass = { 'Director': 'badge-purple', 'Manager': 'badge-info', 'Senior': 'badge-success', 'Mid-Level': 'badge-warning', 'Junior': 'badge-secondary', 'Entry': 'badge-secondary' }[pos.level] || 'badge-secondary';
    
    const salaryRange = (pos.salaryMin || pos.salaryMax) 
        ? `₱${pos.salaryMin ? parseFloat(pos.salaryMin).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '--'} - ₱${pos.salaryMax ? parseFloat(pos.salaryMax).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '--'}`
        : 'Not specified';
        
    const content = `
        <style>
            .modal-view-org * { margin: 0; box-sizing: border-box; }
            .modal-view-org { font-family: 'Inter', sans-serif; max-width: 550px; width: 100%; }
            .view-header { display: flex; align-items: flex-start; gap: 16px; margin-bottom: 24px; padding-bottom: 20px; border-bottom: 1px solid #eef2ff; }
            .company-icon-large { width: 64px; height: 64px; border-radius: 20px; background: linear-gradient(145deg, #0891b2, #06b6d4); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.8rem; }
            .info-large h3 { font-size: 1.2rem; font-weight: 600; color: #0f172a; margin-bottom: 4px; }
            .meta { font-size: 0.8rem; color: #64748b; display: flex; gap: 8px; flex-wrap: wrap; }
            .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px 24px; margin-bottom: 24px; }
            .detail-label { font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 6px; display: flex; align-items: center; gap: 6px; }
            .detail-label i { color: #4f46e5; width: 16px; }
            .detail-value { font-size: 0.95rem; font-weight: 500; color: #1e293b; }
            .modal-buttons { display: flex; justify-content: flex-end; gap: 12px; margin-top: 28px; padding-top: 20px; border-top: 1px solid #f1f5f9; }
            .btn-primary { background: #4f46e5; color: white; border: none; padding: 10px 22px; border-radius: 24px; cursor: pointer; display: flex; align-items: center; gap: 6px; }
            .btn-secondary { background: white; color: #475569; border: 1px solid #e2e8f0; padding: 10px 22px; border-radius: 24px; cursor: pointer; }
        </style>
        <div class="modal-view-org">
            <div class="view-header">
                <div class="company-icon-large"><i class="fas fa-briefcase"></i></div>
                <div class="info-large">
                    <h3>${escapeHtml(pos.jobTitle || pos.title)}</h3>
                    <div class="meta">
                        <span><i class="fas fa-fingerprint"></i> ${pos.id}</span>
                    </div>
                    <div style="margin-top: 8px;">
                        <span class="badge ${levelClass}">${pos.level}</span>
                        <span class="badge badge-success" style="margin-left: 8px;">${pos.status}</span>
                    </div>
                </div>
            </div>
            <div class="detail-grid">
                <div class="detail-item"><div class="detail-label"><i class="fas fa-arrow-up"></i> Reports To</div><div class="detail-value">${escapeHtml(pos.reportsTo || '—')}</div></div>
                <div class="detail-item"><div class="detail-label"><i class="fas fa-users"></i> Vacancies</div><div class="detail-value">${pos.vacancies || 0} open</div></div>
                <div class="detail-item" style="grid-column: span 2;"><div class="detail-label"><i class="fas fa-dollar-sign"></i> Salary Offer</div><div class="detail-value" style="font-weight: 600; color: #4f46e5;">${salaryRange}</div></div>
            </div>
            <div class="modal-buttons">
                <button type="button" class="btn-secondary" onclick="closeModal()"><i class="fas fa-times"></i> Close</button>
                <button type="button" class="btn-primary" onclick="closeModal(); editPosition('${pos.id}');"><i class="fas fa-edit"></i> Edit</button>
            </div>
        </div>
    `;
    openModal('Position Details', content);
}
</script>
