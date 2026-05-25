<!-- modal-edit-offer.php -->
<script>
function handleEditOfferStatusChange(newStatus, oldStatus) {
    const notice = document.getElementById('editAcceptedOfferNotice');
    if (notice) {
        // Show notice only if changing TO Accepted from a different status
        notice.style.display = (newStatus === 'Accepted' && oldStatus !== 'Accepted') ? 'flex' : 'none';
    }
}

function editOffer(id) {
    const offer = window.offers.find(o => o.id === id);
    if (!offer) return;
    
    let hireDateValue = '';
    try { hireDateValue = new Date(offer.hireDate).toISOString().split('T')[0]; } catch(e) {}
    
    const content = `
        <style>
            .modal-edit-offer * { margin: 0; box-sizing: border-box; }
            .modal-edit-offer { font-family: 'Inter', sans-serif; max-width: 600px; width: 100%; }
            .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px 24px; }
            .full-width { grid-column: span 2; }
            .form-group { display: flex; flex-direction: column; margin-bottom: 4px; }
            .form-group label { font-size: 0.85rem; font-weight: 500; margin-bottom: 6px; color: #475569; }
            .form-group input, .form-group select, .form-group textarea { padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 16px; font-size: 0.9rem; background: #ffffff; }
            .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: #10b981; box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1); }
            .section-title { grid-column: span 2; font-size: 1rem; font-weight: 600; margin: 20px 0 8px; padding-bottom: 8px; border-bottom: 1.5px solid #e2e8f0; color: #0f172a; display: flex; align-items: center; gap: 8px; }
            .section-title i { color: #10b981; }
            .required-star { color: #ef4444; }
            textarea { resize: vertical; min-height: 80px; }
            .offer-id-badge { background: linear-gradient(145deg, #f0fdf4, #dcfce7); padding: 8px 14px; border-radius: 12px; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
            .offer-id-badge i { color: #10b981; }
            .offer-id-badge strong { color: #065f46; font-weight: 600; }
            .employee-preview { display: flex; align-items: center; gap: 12px; margin-bottom: 20px; padding: 12px; background: #f8fafc; border-radius: 16px; }
            .employee-avatar-small { width: 40px; height: 40px; border-radius: 12px; background: ${offer.color}; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; }
            .modal-buttons { display: flex; justify-content: flex-end; gap: 12px; margin-top: 28px; padding-top: 20px; border-top: 1px solid #f1f5f9; }
            .btn-save { background: #10b981; color: white; border: none; padding: 10px 22px; border-radius: 24px; font-size: 0.85rem; font-weight: 500; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 6px; box-shadow: 0 4px 8px rgba(16, 185, 129, 0.2); }
            .btn-save:hover { background: #059669; transform: translateY(-1px); }
            .btn-cancel { background: white; color: #475569; border: 1px solid #e2e8f0; padding: 10px 22px; border-radius: 24px; font-size: 0.85rem; font-weight: 500; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 6px; }
            .btn-cancel:hover { background: #f8fafc; border-color: #cbd5e1; }
        </style>
        <div class="modal-edit-offer">
            
            <div class="employee-preview">
                <div class="employee-avatar-small">${offer.avatar}</div>
                <div>
                    <h4 style="font-weight:600;">${escapeHtml(offer.applicantName)}</h4>
                    <p style="color:#64748b; font-size:0.75rem;">${offer.applicantId} • ${escapeHtml(offer.position)}</p>
                </div>
            </div>
            <form id="editOfferForm" onsubmit="event.preventDefault(); updateOffer('${id}');">
                <div class="form-grid">
                    <div class="section-title"><i class="fas fa-file-signature"></i> Offer Details</div>
                    <div class="form-group"><label>Salary Offer (₱) <span class="required-star">*</span></label><input type="number" id="editSalaryOffer" step="0.01" required value="${offer.salaryOffer}"></div>
                    <div class="form-group"><label>Hire Date <span class="required-star">*</span></label><input type="date" id="editHireDate" required value="${hireDateValue}"></div>
                    <div class="form-group full-width"><label>Contract Terms <span class="required-star">*</span></label><textarea id="editContractTerms" required>${escapeHtml(offer.contractTerms)}</textarea></div>
                    
                    <div class="section-title"><i class="fas fa-info-circle"></i> Status</div>
                    <div class="form-group">
                        <label>Offer Status <span class="required-star">*</span></label>
                        <select id="editOfferStatus" required onchange="handleEditOfferStatusChange(this.value, '${offer.offerStatus}')">
                            ${['Pending','Accepted','Declined','Expired'].map(s => `<option value="${s}" ${offer.offerStatus === s ? 'selected' : ''}>${s}${s === 'Accepted' ? '' : ''}</option>`).join('')}
                        </select>
                    </div>
                    
                    <div id="editAcceptedOfferNotice" style="display: ${offer.offerStatus === 'Accepted' ? 'none' : 'none'}; grid-column: span 2; background: linear-gradient(135deg, #dcfce7, #f0fdf4); border: 1px solid #86efac; border-radius: 12px; padding: 12px 16px; font-size: 0.85rem; color: #166534; align-items: center; gap: 10px;">
                        <i class="fas fa-info-circle" style="font-size: 1.2rem; color: #10b981;"></i>
                        <div>
                            <strong>Changing status to "Accepted" will:</strong>
                            <ul style="margin: 6px 0 0 0; padding-left: 20px; line-height: 1.6;">
                                <li>Update applicant status to "Hired"</li>
                                <li>Create an onboarding record automatically</li>
                                <li>Redirect you to the Onboarding page</li>
                            </ul>
                        </div>
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
    
    openModal('Edit Job Offer', content);
}

function updateOffer(id) {
    const index = window.offers.findIndex(o => o.id === id);
    if (index === -1) return;
    
    const salaryOffer = document.getElementById('editSalaryOffer')?.value;
    if (!salaryOffer || parseFloat(salaryOffer) <= 0) {
        showToast('Please enter a valid salary offer.', 'warning');
        return;
    }
    
    const offer = window.offers[index];
    const oldStatus = offer.offerStatus;
    const newStatus = document.getElementById('editOfferStatus')?.value || 'Pending';
    
    const offerData = {
        id: id,
        applicant_id: offer.applicantId,
        position: offer.position,
        salary_offer: parseFloat(salaryOffer),
        contract_terms: document.getElementById('editContractTerms')?.value || '',
        hire_date: document.getElementById('editHireDate')?.value || new Date().toISOString().split('T')[0],
        offer_status: newStatus,
        employee_id: document.getElementById('editEmployeeId')?.value || null
    };
    
    // Send to API
    fetch('../../api/recruitment/offers.php', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(offerData)
    })
    .then(response => response.json())
    .then(data => {
        console.log('📥 Update Offer API Response:', data);
        console.log('   Old Status:', oldStatus, '→ New Status:', newStatus);
        
        if (data.success) {
            let message = 'Offer updated successfully!';
            
            // Check if onboarding was created (status changed to Accepted)
            if ((data.onboarding_id || newStatus === 'Accepted') && oldStatus !== 'Accepted') {
                message = `Offer accepted! Redirecting to onboarding...`;
                showToast(message, 'success');
                
                console.log('✅ Offer status changed to Accepted, redirecting to onboarding page...');
                console.log('   Employee ID:', data.employee_id);
                console.log('   Onboarding ID:', data.onboarding_id);
                
                // Close modal and redirect to onboarding page
                if (typeof closeModal === 'function') {
                    closeModal();
                }
                
                // Redirect to onboarding page after a short delay
                setTimeout(() => {
                    const redirectUrl = `onboarding.php?highlight=${data.employee_id || data.onboarding_id}&from=recruitment&newOffer=${id}`;
                    console.log('🔄 Redirecting to:', redirectUrl);
                    window.location.href = redirectUrl;
                }, 1500);
                
                return; // Exit early to prevent further UI updates
            } else {
                showToast(message, 'success');
            }
            
            // Update local data
            const updatedOffer = {
                ...window.offers[index],
                salaryOffer: offerData.salary_offer,
                contractTerms: offerData.contract_terms,
                hireDate: new Date(offerData.hire_date).toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' }),
                offerStatus: offerData.offer_status,
                employeeId: data.employee_id || offerData.employee_id
            };
            
            window.offers[index] = updatedOffer;
            
            // If offer is accepted, update applicant status
            if (updatedOffer.offerStatus === 'Accepted') {
                const app = window.applicants.find(a => a.id === updatedOffer.applicantId);
                if (app) {
                    app.applicationStatus = 'Hired';
                    if (typeof renderApplicantTable === 'function') {
                        renderApplicantTable(window.applicants);
                    }
                }
            }
            
            if (typeof renderOfferTable === 'function') {
                renderOfferTable(window.offers);
            }
            
            if (typeof closeModal === 'function') {
                closeModal();
            }
        } else {
            showToast(data.message || 'Error updating offer', 'warning');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error updating offer', 'warning');
    });
}
</script>