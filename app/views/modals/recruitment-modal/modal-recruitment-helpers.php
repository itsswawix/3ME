<!-- modal-recruitment-helpers.php -->
<script>
function escapeHtml(str) { 
    if (!str) return ''; 
    return str.replace(/[&<>]/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;' })[m] || m); 
}

function showToast(message, type = 'info') {
    if (typeof window.parentShowToast === 'function') { 
        window.parentShowToast(message, type); 
        return; 
    }
    const toast = document.createElement('div');
    toast.style.cssText = `
        position: fixed; 
        bottom: 24px; 
        right: 24px; 
        background: ${type === 'success' ? '#10b981' : type === 'warning' ? '#f59e0b' : '#1e293b'}; 
        color: white; 
        padding: 12px 20px; 
        border-radius: 12px; 
        font-size: 13px; 
        z-index: 10000; 
        animation: slideIn 0.3s ease; 
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    `;
    toast.textContent = message; 
    document.body.appendChild(toast);
    setTimeout(() => { 
        toast.style.opacity = '0'; 
        setTimeout(() => toast.remove(), 300); 
    }, 3000);
}

// Add animation style if not already present
if (!document.querySelector('#toast-animation-style')) {
    const style = document.createElement('style');
    style.id = 'toast-animation-style';
    style.textContent = `
        @keyframes slideIn { 
            from { transform: translateX(100%); opacity: 0; } 
            to { transform: translateX(0); opacity: 1; } 
        }
    `;
    document.head.appendChild(style);
}

// Helper function to generate calendar days (used across modals)
function generateFullCalendarDays() {
    const date = new Date();
    const year = date.getFullYear();
    const month = date.getMonth();
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const today = date.getDate();
    
    let daysHtml = '';
    
    for (let i = 0; i < firstDay; i++) {
        daysHtml += `<div class="day-cell" style="opacity: 0.3;"></div>`;
    }
    
    for (let i = 1; i <= daysInMonth; i++) {
        const isToday = (i === today);
        daysHtml += `<div class="day-cell ${isToday ? 'today' : ''}" data-day="${i}">${i}</div>`;
    }
    
    return daysHtml;
}

// Quick accept offer function
function acceptOffer(offerId) {
    const offer = window.offers.find(o => o.id === offerId);
    if (!offer) {
        showToast('Offer not found', 'warning');
        return;
    }
    
    if (!confirm(`Accept offer for ${offer.applicantName}?\n\nThis will:\n• Update offer status to "Accepted"\n• Update applicant status to "Hired"\n• Create an onboarding record\n• Redirect to Onboarding page`)) {
        return;
    }
    
    // Update offer status to Accepted
    const offerData = {
        id: offerId,
        applicant_id: offer.applicantId,
        position: offer.position,
        salary_offer: offer.salaryOffer,
        contract_terms: offer.contractTerms || '',
        hire_date: new Date(offer.hireDate).toISOString().split('T')[0],
        offer_status: 'Accepted',
        employee_id: offer.employeeId || null
    };
    
    fetch('../../api/recruitment/offers.php', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(offerData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Offer accepted! Redirecting to onboarding...', 'success');
            
            // Redirect to onboarding page
            setTimeout(() => {
                window.location.href = `onboarding.php?highlight=${data.employee_id || data.onboarding_id}&from=recruitment&newOffer=${offerId}`;
            }, 1500);
        } else {
            showToast(data.message || 'Error accepting offer', 'warning');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error accepting offer', 'warning');
    });
}

// Quick decline offer function
function declineOffer(offerId) {
    const offer = window.offers.find(o => o.id === offerId);
    if (!offer) {
        showToast('Offer not found', 'warning');
        return;
    }
    
    if (!confirm(`Decline offer for ${offer.applicantName}?`)) {
        return;
    }
    
    // Update offer status to Declined
    const offerData = {
        id: offerId,
        applicant_id: offer.applicantId,
        position: offer.position,
        salary_offer: offer.salaryOffer,
        contract_terms: offer.contractTerms || '',
        hire_date: new Date(offer.hireDate).toISOString().split('T')[0],
        offer_status: 'Declined',
        employee_id: offer.employeeId || null
    };
    
    fetch('../../api/recruitment/offers.php', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(offerData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Offer declined', 'success');
            
            // Update local data
            const index = window.offers.findIndex(o => o.id === offerId);
            if (index !== -1) {
                window.offers[index].offerStatus = 'Declined';
                if (typeof renderOfferTable === 'function') {
                    renderOfferTable(window.offers);
                }
            }
        } else {
            showToast(data.message || 'Error declining offer', 'warning');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error declining offer', 'warning');
    });
}
</script>