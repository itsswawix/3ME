<!-- modal-add-offer.php -->
<script>
function openAddOfferModal(prefilledApplicant = null) {
    const content = `
        <style>
            .modal-add-offer * { margin: 0; box-sizing: border-box; }
            .modal-add-offer { font-family: 'Inter', sans-serif; max-width: 600px; width: 100%; }
            .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px 24px; }
            .full-width { grid-column: span 2; }
            .form-group { display: flex; flex-direction: column; margin-bottom: 4px; }
            .form-group label { font-size: 0.85rem; font-weight: 500; margin-bottom: 6px; color: #475569; letter-spacing: 0.3px; }
            .form-group input, .form-group select, .form-group textarea { padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 16px; font-size: 0.9rem; background: #ffffff; font-family: 'Inter', sans-serif; }
            .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: #10b981; box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1); }
            .section-title { grid-column: span 2; font-size: 1rem; font-weight: 600; margin: 20px 0 8px; padding-bottom: 8px; border-bottom: 1.5px solid #e2e8f0; color: #0f172a; display: flex; align-items: center; gap: 8px; }
            .section-title i { color: #10b981; font-size: 0.9rem; width: 20px; }
            .section-title:first-of-type { margin-top: 0; }
            .required-star { color: #ef4444; margin-left: 2px; }
            textarea { resize: vertical; min-height: 80px; }
            .modal-footer-note { font-size: 0.75rem; color: #94a3b8; margin-top: 16px; text-align: right; }
            .modal-buttons { display: flex; justify-content: flex-end; gap: 12px; margin-top: 28px; padding-top: 20px; border-top: 1px solid #f1f5f9; }
            .btn-save { background: #10b981; color: white; border: none; padding: 10px 22px; border-radius: 24px; font-size: 0.85rem; font-weight: 500; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 6px; box-shadow: 0 4px 8px rgba(16, 185, 129, 0.2); }
            .btn-save:hover { background: #059669; transform: translateY(-1px); box-shadow: 0 6px 12px rgba(16, 185, 129, 0.25); }
            .btn-cancel { background: white; color: #475569; border: 1px solid #e2e8f0; padding: 10px 22px; border-radius: 24px; font-size: 0.85rem; font-weight: 500; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 6px; }
            .btn-cancel:hover { background: #f8fafc; border-color: #cbd5e1; }
            .prefilled-badge { background: #dcfce7; color: #16a34a; padding: 4px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: 500; margin-left: 8px; }
        </style>
        <div class="modal-add-offer">
            <form id="addOfferForm" onsubmit="event.preventDefault(); saveNewOffer();">
                <div class="form-grid">
                    <div class="section-title">
                        <i class="fas fa-user"></i> Applicant Information
                        ${prefilledApplicant ? '<span class="prefilled-badge">Pre-selected</span>' : ''}
                    </div>
                    <div class="form-group full-width">
                        <label>Applicant <span class="required-star">*</span></label>
                        <select id="newOfferApplicantId" required ${prefilledApplicant ? 'style="background: #f0fdf4;"' : ''}>
                            <option value="">Select Applicant</option>
                            ${window.applicants.filter(a => a.applicationStatus !== 'Rejected' && a.applicationStatus !== 'Hired').map(a => `<option value="${a.id}" ${prefilledApplicant && prefilledApplicant.id === a.id ? 'selected' : ''}>${a.firstname} ${a.surname} - ${a.requisitionTitle}</option>`).join('')}
                        </select>
                    </div>
                    
                    <div class="section-title"><i class="fas fa-file-signature"></i> Offer Details</div>
                    <div class="form-group full-width"><label>Position</label><input type="text" id="newOfferPosition" readonly style="background: #f8fafc;" value="${prefilledApplicant ? prefilledApplicant.requisitionTitle : ''}"></div>
                    <div class="form-group"><label>Salary Offer (₱) <span class="required-star">*</span></label><input type="number" id="newSalaryOffer" step="0.01" required placeholder="0.00"></div>
                    <div class="form-group"><label>Hire Date <span class="required-star">*</span></label><input type="date" id="newHireDate" required></div>
                    <div class="form-group full-width"><label>Contract Terms <span class="required-star">*</span></label><textarea id="newContractTerms" required placeholder="Full-time position with standard benefits package..."></textarea></div>
                    
                    <div class="section-title"><i class="fas fa-info-circle"></i> Status</div>
                    <div class="form-group">
                        <label>Offer Status <span class="required-star">*</span></label>
                        <select id="newOfferStatus" required onchange="handleOfferStatusChange(this.value)">
                            <option value="Pending">Pending</option>
                            <option value="Accepted">Accepted</option>
                            <option value="Declined">Declined</option>
                        </select>
                    </div>
                    <div id="acceptedOfferNotice" style="display: none; grid-column: span 2; background: linear-gradient(135deg, #dcfce7, #f0fdf4); border: 1px solid #86efac; border-radius: 12px; padding: 12px 16px; font-size: 0.85rem; color: #166534; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-info-circle" style="font-size: 1.2rem; color: #10b981;"></i>
                        <div>
                            <strong>Accepting this offer will:</strong>
                            <ul style="margin: 6px 0 0 0; padding-left: 20px; line-height: 1.6;">
                                <li>Update applicant status to "Hired"</li>
                                <li>Create an onboarding record automatically</li>
                                <li>Redirect you to the Onboarding page</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="modal-buttons">
                    <button type="button" class="btn-cancel" onclick="closeAddOfferModal()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i> Create Offer
                    </button>
                </div>
            </form>
        </div>
    `;
    
    openModal('Create Job Offer', content);
    
    const today = new Date();
    today.setDate(today.getDate() + 14);
    document.getElementById('newHireDate').value = today.toISOString().split('T')[0];
    
    const applicantSelect = document.getElementById('newOfferApplicantId');
    if (applicantSelect) {
        applicantSelect.addEventListener('change', function() {
            const app = window.applicants.find(a => a.id === this.value);
            if (app) {
                document.getElementById('newOfferPosition').value = app.requisitionTitle;
            }
        });
    }
}

function closeAddOfferModal() {
    if (typeof closeModal === 'function') {
        closeModal();
    }
}

function handleOfferStatusChange(status) {
    const notice = document.getElementById('acceptedOfferNotice');
    if (notice) {
        notice.style.display = status === 'Accepted' ? 'flex' : 'none';
    }
}

function saveNewOffer() {
    const applicantId = document.getElementById('newOfferApplicantId')?.value;
    const app = window.applicants.find(a => a.id === applicantId);
    
    if (!app) { 
        showToast('Please select an applicant.', 'warning'); 
        return; 
    }
    
    const salaryOffer = document.getElementById('newSalaryOffer')?.value;
    if (!salaryOffer || parseFloat(salaryOffer) <= 0) {
        showToast('Please enter a valid salary offer.', 'warning');
        return;
    }
    
    const offerData = {
        applicant_id: applicantId,
        position: document.getElementById('newOfferPosition')?.value || '',
        salary_offer: parseFloat(salaryOffer),
        contract_terms: document.getElementById('newContractTerms')?.value || '',
        hire_date: document.getElementById('newHireDate')?.value || new Date().toISOString().split('T')[0],
        offer_status: document.getElementById('newOfferStatus')?.value || 'Pending',
        employee_id: document.getElementById('newEmployeeId')?.value || null
    };
    
    // Send to API
    fetch('../../api/recruitment/offers.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(offerData)
    })
    .then(response => response.json())
    .then(data => {
        console.log('📥 Create Offer API Response:', data);
        
        if (data.success) {
            let message = `Offer ${data.id} created successfully!`;
            
            // Check if onboarding was created (offer status is Accepted)
            if (data.onboarding_id || offerData.offer_status === 'Accepted') {
                message = `Offer accepted! Redirecting to onboarding...`;
                showToast(message, 'success');
                
                console.log('✅ Offer accepted, redirecting to onboarding page...');
                console.log('   Employee ID:', data.employee_id);
                console.log('   Onboarding ID:', data.onboarding_id);
                
                // Close modal and redirect to onboarding page
                if (typeof closeModal === 'function') {
                    closeModal();
                }
                
                // Redirect to onboarding page after a short delay
                setTimeout(() => {
                    const redirectUrl = `onboarding.php?highlight=${data.employee_id || data.onboarding_id}&from=recruitment&newOffer=${data.id}`;
                    console.log('🔄 Redirecting to:', redirectUrl);
                    window.location.href = redirectUrl;
                }, 1500);
                
                return; // Exit early to prevent further UI updates
            } else {
                showToast(message, 'success');
            }
            
            // Add to local array for immediate UI update
            const newOffer = {
                id: data.id,
                applicantId: offerData.applicant_id,
                applicantName: `${app.firstname} ${app.surname}`,
                position: offerData.position,
                salaryOffer: offerData.salary_offer,
                contractTerms: offerData.contract_terms,
                hireDate: new Date(offerData.hire_date).toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' }),
                offerStatus: offerData.offer_status,
                employeeId: offerData.employee_id,
                avatar: app.avatar,
                color: app.color
            };
            
            window.offers.unshift(newOffer);
            
            // Update applicant status if offer is accepted
            if (offerData.offer_status === 'Accepted') {
                app.applicationStatus = 'Hired';
                if (typeof renderApplicantTable === 'function') {
                    renderApplicantTable(window.applicants);
                }
            }
            
            if (typeof renderOfferTable === 'function') {
                renderOfferTable(window.offers);
            }
            
            if (typeof closeModal === 'function') {
                closeModal();
            }
        } else {
            showToast(data.message || 'Error creating offer', 'warning');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error creating offer', 'warning');
    });
}
</script>