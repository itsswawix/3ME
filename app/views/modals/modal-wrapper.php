<!-- modal-wrapper.php - Main modal container -->
<style>
  .modal-overlay {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.2);
    display: none;
    align-items: stretch;
    justify-content: flex-end;
    z-index: 999;
  }

  .modal-panel {
    width: 460px;
    max-width: 95vw;
    background: #ffffff;
    display: flex;
    flex-direction: column;
    box-shadow: -4px 0 18px rgba(0,0,0,0.08);
    border-left: 1px solid #e5e5e2;
    border-radius: 0;
    margin: 0;
    height: 100vh;
    overflow-y: auto;
  }

  .modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 22px;
    border-bottom: 1px solid #ebebea;
    background: #ffffff;
    position: sticky;
    top: 0;
  }

  .modal-header h2 {
    font-size: 18px;
    font-weight: 600;
    color: #1a1a18;
    margin: 0;
  }

  .modal-close {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #9a9a96;
    padding: 0 4px;
  }

  .modal-close:hover { color: #1a1a18; }

  .modal-content {
    padding: 18px 22px;
    flex: 1;
  }

  /* Form Styles */
  .form-group {
    margin-bottom: 18px;
  }

  .form-group label {
    display: block;
    font-weight: 500;
    color: #1e293b;
    margin-bottom: 6px;
    font-size: 13px;
  }

  .required-star {
    color: #ef4444;
    margin-left: 2px;
  }

  .form-group input,
  .form-group select,
  .form-group textarea {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    font-size: 13px;
    outline: none;
    transition: border-color 0.2s;
    font-family: 'Inter', sans-serif;
  }

  .form-group textarea {
    resize: vertical;
    min-height: 70px;
  }

  .form-group input:focus,
  .form-group select:focus,
  .form-group textarea:focus {
    border-color: #4f46e5;
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
  }

  .form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
  }

  .modal-footer {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
    margin-top: 24px;
    padding-top: 18px;
    border-top: 1px solid #ebebea;
  }

  .btn {
    padding: 8px 16px;
    border-radius: 20px;
    border: none;
    font-weight: 500;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .btn-primary {
    background: #4f46e5;
    color: white;
  }

  .btn-primary:hover {
    background: #4338ca;
    transform: translateY(-1px);
  }

  .btn-secondary {
    background: white;
    color: #1e293b;
    border: 1px solid #e2e8f0;
  }

  .btn-secondary:hover {
    background: #f8fafc;
  }

  .btn-success {
    background: #10b981;
    color: white;
  }

  .btn-success:hover {
    background: #059669;
    transform: translateY(-1px);
  }

  .btn-info {
    background: #0ea5e9;
    color: white;
  }

  .btn-info:hover {
    background: #0284c7;
    transform: translateY(-1px);
  }

  .btn-warning {
    background: #f59e0b;
    color: white;
  }

  .btn-warning:hover {
    background: #d97706;
    transform: translateY(-1px);
  }

  .btn-danger {
    background: #ef4444;
    color: white;
  }

  .btn-danger:hover {
    background: #dc2626;
    transform: translateY(-1px);
  }

  .employee-detail p {
    margin-bottom: 12px;
    display: flex;
    align-items: center;
  }

  .employee-detail i {
    width: 24px;
    color: #4f46e5;
  }

  /* Confirmation Dialog */
  .confirmation-overlay {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.5);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
  }

  .confirmation-dialog {
    background: white;
    border-radius: 16px;
    padding: 24px;
    max-width: 420px;
    width: 90%;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    animation: confirmSlideIn 0.2s ease;
  }

  @keyframes confirmSlideIn {
    from { transform: scale(0.9); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
  }

  .confirmation-icon {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: #fef2f2;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 16px;
  }

  .confirmation-icon i {
    font-size: 28px;
    color: #ef4444;
  }

  .confirmation-title {
    font-size: 18px;
    font-weight: 600;
    color: #1a1a18;
    text-align: center;
    margin-bottom: 8px;
  }

  .confirmation-message {
    font-size: 14px;
    color: #64748b;
    text-align: center;
    margin-bottom: 24px;
    line-height: 1.5;
  }

  .confirmation-buttons {
    display: flex;
    gap: 12px;
    justify-content: center;
  }

  .btn-confirm-cancel {
    padding: 10px 24px;
    border-radius: 24px;
    border: 1px solid #e2e8f0;
    background: white;
    color: #475569;
    font-weight: 500;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.2s;
  }

  .btn-confirm-cancel:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
  }

  .btn-confirm-yes {
    padding: 10px 24px;
    border-radius: 24px;
    border: none;
    background: #ef4444;
    color: white;
    font-weight: 500;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.2s;
  }

  .btn-confirm-yes:hover {
    background: #dc2626;
    transform: translateY(-1px);
  }
</style>

<div class="modal-overlay" id="modalOverlay">
  <div class="modal-panel">
    <div class="modal-header">
      <h2 id="modalTitle">Modal</h2>
      <button class="modal-close" onclick="attemptCloseModal()">&times;</button>
    </div>
    <div class="modal-content" id="modalBody"></div>
  </div>
</div>

<!-- Confirmation Dialog -->
<div class="confirmation-overlay" id="confirmationOverlay">
  <div class="confirmation-dialog">
    <div class="confirmation-icon">
      <i class="fas fa-exclamation-triangle"></i>
    </div>
    <div class="confirmation-title" id="confirmTitle">Unsaved Changes</div>
    <div class="confirmation-message" id="confirmMessage">
      Are you sure you want to exit without finishing? Your changes will be lost.
    </div>
    <div class="confirmation-buttons">
      <button class="btn-confirm-cancel" onclick="cancelConfirmation()">
        <i class="fas fa-arrow-left"></i> Continue Editing
      </button>
      <button class="btn-confirm-yes" onclick="confirmAction()">
        <i class="fas fa-times"></i> Exit Anyway
      </button>
    </div>
  </div>
</div>

<script>
  // Modal state tracking
  let modalHasChanges = false;
  let confirmCallback = null;

  function openModal(title, content) {
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('modalBody').innerHTML = content;
    document.getElementById('modalOverlay').style.display = 'flex';
    modalHasChanges = false; // Reset on open
    
    // Track changes in form inputs
    setTimeout(() => {
      const modalBody = document.getElementById('modalBody');
      const inputs = modalBody.querySelectorAll('input, select, textarea');
      
      inputs.forEach(input => {
        input.addEventListener('input', () => {
          modalHasChanges = true;
        });
        input.addEventListener('change', () => {
          modalHasChanges = true;
        });
      });
    }, 100);
  }

  function closeModal(force = false) {
    if (force) {
      modalHasChanges = false;
    }
    document.getElementById('modalOverlay').style.display = 'none';
    modalHasChanges = false;
  }

  function attemptCloseModal() {
    if (modalHasChanges) {
      showConfirmation(
        'Unsaved Changes',
        'Are you sure you want to exit without finishing? Your changes will be lost.',
        () => {
          closeModal(true);
        }
      );
    } else {
      closeModal();
    }
  }

  function showConfirmation(title, message, onConfirm) {
    document.getElementById('confirmTitle').textContent = title;
    document.getElementById('confirmMessage').textContent = message;
    confirmCallback = onConfirm;
    document.getElementById('confirmationOverlay').style.display = 'flex';
  }

  function cancelConfirmation() {
    document.getElementById('confirmationOverlay').style.display = 'none';
    confirmCallback = null;
  }

  function confirmAction() {
    document.getElementById('confirmationOverlay').style.display = 'none';
    if (confirmCallback) {
      confirmCallback();
      confirmCallback = null;
    }
  }

  // Mark modal as saved (call this after successful save)
  function markModalAsSaved() {
    modalHasChanges = false;
  }

  // Close on overlay click
  document.getElementById('modalOverlay').addEventListener('click', function(e) {
    if (e.target === this) attemptCloseModal();
  });

  // Close on escape key
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      const confirmOverlay = document.getElementById('confirmationOverlay');
      if (confirmOverlay.style.display === 'flex') {
        cancelConfirmation();
      } else {
        attemptCloseModal();
      }
    }
  });

  // Close confirmation on overlay click
  document.getElementById('confirmationOverlay').addEventListener('click', function(e) {
    if (e.target === this) cancelConfirmation();
  });
</script>