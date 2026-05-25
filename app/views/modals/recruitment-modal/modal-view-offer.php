<!-- modal-view-offer.php -->
<script>
function viewOffer(id) {
    const offer = window.offers.find(o => o.id === id);
    if (!offer) return;
    const statusConfig = {
        'Pending': { bg: '#fef3c7', color: '#b45309', icon: 'fa-clock' },
        'Accepted': { bg: '#dcfce7', color: '#16a34a', icon: 'fa-check-circle' },
        'Declined': { bg: '#fee2e2', color: '#dc2626', icon: 'fa-times-circle' },
        'Expired': { bg: '#f1f5f9', color: '#64748b', icon: 'fa-calendar-times' }
    }[offer.offerStatus] || { bg: '#f1f5f9', color: '#64748b', icon: 'fa-circle' };
    
    const content = `
        <style>
            .modal-view-offer * { margin: 0; box-sizing: border-box; }
            .modal-view-offer { font-family: 'Inter', sans-serif; max-width: 550px; width: 100%; }
            .view-header { display: flex; align-items: flex-start; gap: 16px; margin-bottom: 24px; padding-bottom: 20px; border-bottom: 1px solid #eef2ff; }
            .offer-icon-large { width: 64px; height: 64px; border-radius: 20px; background: linear-gradient(145deg, #10b981, #34d399); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.8rem; }
            .offer-info-large h3 { font-size: 1.2rem; font-weight: 600; color: #0f172a; margin-bottom: 4px; }
            .offer-meta { font-size: 0.8rem; color: #64748b; display: flex; gap: 8px; flex-wrap: wrap; }
            .status-badge-view { padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 500; background: ${statusConfig.bg}; color: ${statusConfig.color}; display: inline-flex; align-items: center; gap: 4px; margin-top: 8px; }
            .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px 24px; margin-bottom: 24px; }
            .detail-label { font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 6px; display: flex; align-items: center; gap: 6px; }
            .detail-label i { color: #10b981; width: 16px; }
            .detail-value { font-size: 0.95rem; font-weight: 500; color: #1e293b; }
            .salary-large { font-size: 1rem; font-weight: 700; color: #10b981; }
            .contract-section { margin-top: 20px; padding-top: 20px; border-top: 1px solid #eef2ff; }
            .contract-section h4 { font-size: 0.9rem; font-weight: 600; color: #0f172a; margin-bottom: 12px; display: flex; align-items: center; gap: 8px; }
            .contract-content { background: #f8fafc; padding: 16px; border-radius: 16px; font-size: 0.9rem; line-height: 1.6; color: #334155; }
            .modal-buttons { display: flex; justify-content: flex-end; gap: 12px; margin-top: 28px; padding-top: 20px; border-top: 1px solid #f1f5f9; flex-wrap: wrap; }
            .btn-primary { background: #10b981; color: white; border: none; padding: 10px 22px; border-radius: 24px; cursor: pointer; display: flex; align-items: center; gap: 6px; }
            .btn-secondary { background: white; color: #475569; border: 1px solid #e2e8f0; padding: 10px 22px; border-radius: 24px; cursor: pointer; }
        </style>
        <div class="modal-view-offer">
            <div class="view-header">
                <div class="offer-icon-large"><i class="fas fa-file-signature"></i></div>
                <div class="offer-info-large">
                    <h3>${escapeHtml(offer.applicantName)}</h3>
                    <div class="offer-meta"><span><i class="fas fa-hashtag"></i> ${offer.id}</span><span><i class="fas fa-user"></i> ${offer.applicantId}</span></div>
                    <span class="status-badge-view"><i class="fas ${statusConfig.icon}"></i> ${offer.offerStatus}</span>
                </div>
            </div>
            <div class="detail-grid">
                <div class="detail-item"><div class="detail-label"> Position</div><div class="detail-value">${escapeHtml(offer.position)}</div></div>
                <div class="detail-item"><div class="detail-label"> Hire Date</div><div class="detail-value">${offer.hireDate}</div></div>
                <div class="detail-item full-width"><div class="detail-label"> Salary Offer</div><div class="detail-value salary-large">₱${offer.salaryOffer.toLocaleString()}</div></div>
                <div class="detail-item"><div class="detail-label"> Employee ID</div><div class="detail-value">${offer.employeeId || '—'}</div></div>
            </div>
            <div class="contract-section">
                <h4><i class="fas fa-file-contract"></i> Contract Terms</h4>
                <div class="contract-content">${escapeHtml(offer.contractTerms)}</div>
            </div>
            <div class="modal-buttons">
                <button type="button" class="btn-secondary" onclick="closeModal()"><i class="fas fa-times"></i> Close</button>
                <button type="button" class="btn-primary" onclick="closeModal(); editOffer('${id}');"><i class="fas fa-edit"></i> Edit</button>
                ${offer.offerStatus === 'Pending' ? `<button type="button" class="btn-primary" style="background: #4f46e5;" onclick="acceptOffer('${id}'); closeModal();"><i class="fas fa-check"></i> Accept</button>` : ''}
                ${offer.offerStatus === 'Pending' ? `<button type="button" class="btn-secondary" onclick="declineOffer('${id}'); closeModal();"><i class="fas fa-times"></i> Decline</button>` : ''}
            </div>
        </div>
    `;
    openModal('Offer Details', content);
}
</script>