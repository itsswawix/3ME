<!-- poffsz-design.php - Poffsz Design System -->
<!-- Include this file on any page to get the complete Poffsz look and feel -->

<link rel="preconnect" href="https://fonts.googleapis.com" />
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet" />

<style>
  /* ============================================
     POFFSZ DESIGN SYSTEM v1.0
     ============================================ */
  
  *, *::before, *::after { 
    box-sizing: border-box; 
    margin: 0; 
    padding: 0; 
  }

  :root {
    /* Colors */
    --poffsz-bg: #f0efe8;
    --poffsz-surface: #ffffff;
    --poffsz-surface-2: #f7f7f4;
    --poffsz-border: #e5e5e2;
    --poffsz-border-light: #ebebea;
    --poffsz-text: #1a1a18;
    --poffsz-text-2: #666662;
    --poffsz-text-3: #9a9a96;
    --poffsz-accent: #5a52d5;
    --poffsz-accent-bg: #eeedfe;
    --poffsz-success: #0f6e56;
    --poffsz-success-bg: #e1f5ee;
    --poffsz-warning: #854f0b;
    --poffsz-warning-bg: #faeeda;
    --poffsz-info: #185fa5;
    --poffsz-info-bg: #e6f1fb;
    
    /* Spacing */
    --poffsz-space-xs: 4px;
    --poffsz-space-sm: 8px;
    --poffsz-space-md: 12px;
    --poffsz-space-lg: 16px;
    --poffsz-space-xl: 20px;
    --poffsz-space-2xl: 24px;
    --poffsz-space-3xl: 32px;
    
    /* Border Radius */
    --poffsz-radius-sm: 6px;
    --poffsz-radius-md: 8px;
    --poffsz-radius-lg: 10px;
    --poffsz-radius-xl: 12px;
    --poffsz-radius-full: 9999px;
    
    /* Shadows */
    --poffsz-shadow-sm: 0 1px 2px rgba(0,0,0,0.05);
    --poffsz-shadow-md: 0 4px 6px rgba(0,0,0,0.07);
    --poffsz-shadow-lg: 0 10px 15px rgba(0,0,0,0.1);
    --poffsz-shadow-modal: -4px 0 18px rgba(0,0,0,0.08);
    
    /* Typography */
    --poffsz-font-family: 'DM Sans', sans-serif;
    --poffsz-font-size-xs: 11px;
    --poffsz-font-size-sm: 12px;
    --poffsz-font-size-base: 14px;
    --poffsz-font-size-lg: 16px;
    --poffsz-font-size-xl: 18px;
    --poffsz-font-size-2xl: 22px;
    --poffsz-font-size-3xl: 28px;
    
    /* Layout */
    --poffsz-sidebar-width: 210px;
  }

  body {
    font-family: var(--poffsz-font-family);
    background: var(--poffsz-bg);
    color: var(--poffsz-text);
    font-size: var(--poffsz-font-size-base);
    line-height: 1.5;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
  }

  /* ============================================
     TYPOGRAPHY
     ============================================ */
  
  h1, h2, h3, h4, h5, h6 {
    font-weight: 600;
    color: var(--poffsz-text);
    line-height: 1.3;
  }

  h1 { font-size: var(--poffsz-font-size-2xl); }
  h2 { font-size: var(--poffsz-font-size-xl); }
  h3 { font-size: var(--poffsz-font-size-lg); }
  h4 { font-size: var(--poffsz-font-size-base); }

  .poffsz-text-secondary { color: var(--poffsz-text-2); }
  .poffsz-text-tertiary { color: var(--poffsz-text-3); }
  .poffsz-text-accent { color: var(--poffsz-accent); }

  .poffsz-font-light { font-weight: 400; }
  .poffsz-font-medium { font-weight: 500; }
  .poffsz-font-semibold { font-weight: 600; }
  .poffsz-font-bold { font-weight: 700; }

  .poffsz-text-xs { font-size: var(--poffsz-font-size-xs); }
  .poffsz-text-sm { font-size: var(--poffsz-font-size-sm); }
  .poffsz-text-base { font-size: var(--poffsz-font-size-base); }
  .poffsz-text-lg { font-size: var(--poffsz-font-size-lg); }

  .poffsz-uppercase { 
    text-transform: uppercase; 
    letter-spacing: 0.05em;
  }

  /* ============================================
     LAYOUT COMPONENTS
     ============================================ */

  /* Cards */
  .poffsz-card {
    background: var(--poffsz-surface);
    border: 1px solid var(--poffsz-border);
    border-radius: var(--poffsz-radius-lg);
    overflow: hidden;
  }

  .poffsz-card-header {
    padding: var(--poffsz-space-md) var(--poffsz-space-lg);
    border-bottom: 1px solid var(--poffsz-border-light);
    font-weight: 600;
  }

  .poffsz-card-body {
    padding: var(--poffsz-space-lg);
  }

  .poffsz-card-footer {
    padding: var(--poffsz-space-md) var(--poffsz-space-lg);
    border-top: 1px solid var(--poffsz-border-light);
  }

  /* Sidebar */
  .poffsz-sidebar {
    width: var(--poffsz-sidebar-width);
    background: var(--poffsz-surface);
    border-right: 1px solid var(--poffsz-border);
    padding: var(--poffsz-space-xl) var(--poffsz-space-md);
    display: flex;
    flex-direction: column;
    gap: 6px;
    position: fixed;
    top: 0; bottom: 0; left: 0;
    overflow-y: auto;
  }

  .poffsz-main {
    margin-left: var(--poffsz-sidebar-width);
    flex: 1;
    padding: var(--poffsz-space-3xl);
  }

  /* ============================================
     BUTTONS
     ============================================ */

  .poffsz-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: var(--poffsz-space-xs);
    padding: 6px 14px;
    border-radius: var(--poffsz-radius-md);
    font-size: var(--poffsz-font-size-sm);
    font-weight: 500;
    font-family: inherit;
    cursor: pointer;
    border: 1px solid var(--poffsz-border);
    background: var(--poffsz-surface);
    color: var(--poffsz-text-2);
    transition: all 0.15s;
    text-decoration: none;
    line-height: 1.5;
  }

  .poffsz-btn:hover {
    background: var(--poffsz-surface-2);
    border-color: #c5c5c0;
    color: var(--poffsz-text);
  }

  .poffsz-btn:active {
    transform: scale(0.98);
  }

  .poffsz-btn-primary {
    background: var(--poffsz-accent);
    color: white;
    border-color: transparent;
  }

  .poffsz-btn-primary:hover {
    background: #4840c2;
    color: white;
  }

  .poffsz-btn-ghost {
    background: var(--poffsz-surface-2);
    color: var(--poffsz-text-2);
  }

  .poffsz-btn-ghost:hover {
    background: var(--poffsz-border);
  }

  .poffsz-btn-sm {
    padding: 4px 10px;
    font-size: var(--poffsz-font-size-xs);
  }

  .poffsz-btn-lg {
    padding: 10px 20px;
    font-size: var(--poffsz-font-size-base);
  }

  .poffsz-btn-block {
    width: 100%;
  }

  /* ============================================
     STATUS BADGES
     ============================================ */

  .poffsz-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 9px;
    border-radius: var(--poffsz-radius-full);
    font-size: var(--poffsz-font-size-sm);
    font-weight: 500;
  }

  .poffsz-badge-success {
    background: var(--poffsz-success-bg);
    color: var(--poffsz-success);
  }

  .poffsz-badge-warning {
    background: var(--poffsz-warning-bg);
    color: var(--poffsz-warning);
  }

  .poffsz-badge-info {
    background: var(--poffsz-info-bg);
    color: var(--poffsz-info);
  }

  .poffsz-badge-accent {
    background: var(--poffsz-accent-bg);
    color: var(--poffsz-accent);
  }

  .poffsz-badge-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: currentColor;
    display: inline-block;
  }

  /* ============================================
     FORMS
     ============================================ */

  .poffsz-form-group {
    margin-bottom: 14px;
  }

  .poffsz-label {
    display: block;
    font-size: var(--poffsz-font-size-sm);
    font-weight: 500;
    color: #888880;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    margin-bottom: 5px;
  }

  .poffsz-input,
  .poffsz-select,
  .poffsz-textarea {
    width: 100%;
    padding: 9px 12px;
    border: 1px solid #ddddd9;
    border-radius: var(--poffsz-radius-md);
    font-size: var(--poffsz-font-size-base);
    font-family: inherit;
    color: var(--poffsz-text);
    background: #fafaf8;
    transition: border-color 0.15s, box-shadow 0.15s;
    outline: none;
  }

  .poffsz-textarea {
    resize: vertical;
    min-height: 80px;
  }

  .poffsz-input:focus,
  .poffsz-select:focus,
  .poffsz-textarea:focus {
    border-color: var(--poffsz-accent);
    box-shadow: 0 0 0 3px rgba(90,82,213,0.12);
    background: white;
  }

  .poffsz-form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: var(--poffsz-space-md);
  }

  /* ============================================
     TABLES
     ============================================ */

  .poffsz-table {
    width: 100%;
    border-collapse: collapse;
  }

  .poffsz-table thead th {
    padding: 10px 16px;
    text-align: left;
    font-size: var(--poffsz-font-size-xs);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--poffsz-text-3);
    border-bottom: 1px solid var(--poffsz-border-light);
    background: var(--poffsz-surface-2);
  }

  .poffsz-table tbody tr {
    border-bottom: 1px solid var(--poffsz-border-light);
    transition: background 0.12s;
  }

  .poffsz-table tbody tr:hover {
    background: #fafaf8;
  }

  .poffsz-table td {
    padding: 11px 16px;
    font-size: var(--poffsz-font-size-base);
    color: var(--poffsz-text-2);
  }

  /* ============================================
     MODAL (Right Side)
     ============================================ */

  .poffsz-modal-overlay {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.2);
    display: none;
    align-items: stretch;
    justify-content: flex-end;
    z-index: 999;
  }

  .poffsz-modal-panel {
    width: 460px;
    max-width: 95vw;
    background: var(--poffsz-surface);
    display: flex;
    flex-direction: column;
    box-shadow: var(--poffsz-shadow-modal);
    border-left: 1px solid var(--poffsz-border);
    border-radius: 0;
    margin: 0;
    height: 100vh;
    overflow-y: auto;
  }

  .poffsz-modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 22px;
    border-bottom: 1px solid var(--poffsz-border-light);
    background: var(--poffsz-surface);
    position: sticky;
    top: 0;
    z-index: 2;
  }

  .poffsz-modal-title {
    font-size: var(--poffsz-font-size-xl);
    font-weight: 600;
    color: var(--poffsz-text);
  }

  .poffsz-modal-close {
    background: none;
    border: none;
    font-size: 24px;
    line-height: 1;
    cursor: pointer;
    color: var(--poffsz-text-3);
    padding: 0 4px;
    transition: color 0.12s;
  }

  .poffsz-modal-close:hover {
    color: var(--poffsz-text);
  }

  .poffsz-modal-content {
    padding: 18px 22px 28px;
    flex: 1;
  }

  /* ============================================
     UTILITIES
     ============================================ */

  .poffsz-flex { display: flex; }
  .poffsz-flex-col { flex-direction: column; }
  .poffsz-items-center { align-items: center; }
  .poffsz-justify-between { justify-content: space-between; }
  .poffsz-justify-end { justify-content: flex-end; }
  .poffsz-gap-sm { gap: var(--poffsz-space-sm); }
  .poffsz-gap-md { gap: var(--poffsz-space-md); }
  .poffsz-gap-lg { gap: var(--poffsz-space-lg); }
  
  .poffsz-flex-1 { flex: 1; }
  .poffsz-w-full { width: 100%; }
  .poffsz-h-full { height: 100%; }
  
  .poffsz-p-sm { padding: var(--poffsz-space-sm); }
  .poffsz-p-md { padding: var(--poffsz-space-md); }
  .poffsz-p-lg { padding: var(--poffsz-space-lg); }
  .poffsz-p-xl { padding: var(--poffsz-space-xl); }
  
  .poffsz-mb-sm { margin-bottom: var(--poffsz-space-sm); }
  .poffsz-mb-md { margin-bottom: var(--poffsz-space-md); }
  .poffsz-mb-lg { margin-bottom: var(--poffsz-space-lg); }
  .poffsz-mb-xl { margin-bottom: var(--poffsz-space-xl); }
  
  .poffsz-mt-auto { margin-top: auto; }
  
  .poffsz-border { border: 1px solid var(--poffsz-border); }
  .poffsz-border-bottom { border-bottom: 1px solid var(--poffsz-border-light); }
  .poffsz-border-top { border-top: 1px solid var(--poffsz-border-light); }
  
  .poffsz-rounded-sm { border-radius: var(--poffsz-radius-sm); }
  .poffsz-rounded-md { border-radius: var(--poffsz-radius-md); }
  .poffsz-rounded-lg { border-radius: var(--poffsz-radius-lg); }
  .poffsz-rounded-full { border-radius: var(--poffsz-radius-full); }
  
  .poffsz-bg-surface { background: var(--poffsz-surface); }
  .poffsz-bg-surface-2 { background: var(--poffsz-surface-2); }
  .poffsz-bg-accent { background: var(--poffsz-accent); }
  
  .poffsz-cursor-pointer { cursor: pointer; }
  
  /* ============================================
     ICONS (SVG Sprite)
     ============================================ */

  .poffsz-icon {
    width: 16px;
    height: 16px;
    display: inline-block;
    vertical-align: middle;
    flex-shrink: 0;
  }

  .poffsz-icon-sm { width: 14px; height: 14px; }
  .poffsz-icon-lg { width: 20px; height: 20px; }
  .poffsz-icon-xl { width: 24px; height: 24px; }

  /* Brand Icon */
  .poffsz-brand-icon {
    width: 32px;
    height: 32px;
    background: var(--poffsz-accent);
    border-radius: var(--poffsz-radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 15px;
  }

  /* Avatar */
  .poffsz-avatar {
    width: 36px;
    height: 36px;
    border-radius: 0;
    background: var(--poffsz-accent-bg);
    color: var(--poffsz-accent);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    font-weight: 600;
    flex-shrink: 0;
  }

  .poffsz-avatar-rounded {
    border-radius: 50%;
  }

  /* Workspace Icon */
  .poffsz-workspace-icon {
    width: 26px;
    height: 26px;
    background: #c0dd97;
    border-radius: var(--poffsz-radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 700;
    color: #3b6d11;
  }
</style>

<!-- Icon Library -->
<svg style="display: none;">
  <defs>
    <!-- Dashboard -->
    <symbol id="icon-dashboard" viewBox="0 0 15 15">
      <rect x="1" y="1" width="5.5" height="5.5" rx="1" stroke="currentColor" stroke-width="1.3" fill="none"/>
      <rect x="8.5" y="1" width="5.5" height="5.5" rx="1" stroke="currentColor" stroke-width="1.3" fill="none"/>
      <rect x="1" y="8.5" width="5.5" height="5.5" rx="1" stroke="currentColor" stroke-width="1.3" fill="none"/>
      <rect x="8.5" y="8.5" width="5.5" height="5.5" rx="1" stroke="currentColor" stroke-width="1.3" fill="none"/>
    </symbol>
    
    <!-- Products -->
    <symbol id="icon-products" viewBox="0 0 15 15">
      <rect x="1.5" y="1.5" width="12" height="12" rx="2" stroke="currentColor" stroke-width="1.3" fill="none"/>
      <path d="M5 7.5h5M7.5 5v5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
    </symbol>
    
    <!-- Orders -->
    <symbol id="icon-orders" viewBox="0 0 15 15">
      <path d="M2 2h11l-1 8H3L2 2z" stroke="currentColor" stroke-width="1.3" stroke-linejoin="round" fill="none"/>
      <circle cx="5.5" cy="13" r="1" fill="currentColor"/>
      <circle cx="10.5" cy="13" r="1" fill="currentColor"/>
    </symbol>
    
    <!-- Messages -->
    <symbol id="icon-messages" viewBox="0 0 15 15">
      <path d="M1.5 3.5h12M1.5 7.5h12M1.5 11.5h7.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" fill="none"/>
    </symbol>
    
    <!-- Store -->
    <symbol id="icon-store" viewBox="0 0 15 15">
      <path d="M2 12V5l5.5-3 5.5 3v7" stroke="currentColor" stroke-width="1.3" stroke-linejoin="round" fill="none"/>
      <rect x="5" y="8" width="2.5" height="4" rx="0.5" fill="currentColor" opacity="0.4"/>
      <rect x="7.5" y="8" width="2.5" height="4" rx="0.5" fill="currentColor" opacity="0.4"/>
    </symbol>
    
    <!-- Analytics -->
    <symbol id="icon-analytics" viewBox="0 0 15 15">
      <circle cx="7.5" cy="7.5" r="5.5" stroke="currentColor" stroke-width="1.3" fill="none"/>
      <path d="M4 7.5h7M7.5 4v7" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
    </symbol>
    
    <!-- Add -->
    <symbol id="icon-add" viewBox="0 0 13 13">
      <path d="M6.5 1v11M1 6.5h11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
    </symbol>
    
    <!-- Chevron Down -->
    <symbol id="icon-chevron-down" viewBox="0 0 14 14">
      <path d="M3 5l4 4 4-4" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" fill="none"/>
    </symbol>
    
    <!-- Location -->
    <symbol id="icon-location" viewBox="0 0 16 16">
      <path d="M8 1.5C5.5 1.5 3.5 3.5 3.5 6c0 3.5 4.5 8.5 4.5 8.5S12.5 9.5 12.5 6c0-2.5-2-4.5-4.5-4.5z" stroke="currentColor" stroke-width="1.3" fill="none"/>
      <circle cx="8" cy="6" r="1.5" stroke="currentColor" stroke-width="1.3" fill="none"/>
    </symbol>
    
    <!-- Calendar -->
    <symbol id="icon-calendar" viewBox="0 0 16 16">
      <rect x="1.5" y="3" width="13" height="11" rx="1.5" stroke="currentColor" stroke-width="1.3" fill="none"/>
      <path d="M5 3V1.5M11 3V1.5M1.5 7h13" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" fill="none"/>
    </symbol>
    
    <!-- Truck -->
    <symbol id="icon-truck" viewBox="0 0 16 16">
      <path d="M2 10l2-6h7l2 4H2zM10 10h3M4 10v3M12 10v3" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
      <circle cx="5.5" cy="13" r="1" fill="currentColor"/>
      <circle cx="10.5" cy="13" r="1" fill="currentColor"/>
    </symbol>
  </defs>
</svg>

<script>
  // Helper function to use icons
  function poffszIcon(iconName, className = 'poffsz-icon') {
    return `<svg class="${className}" viewBox="0 0 15 15" fill="none">
      <use href="#icon-${iconName}"></use>
    </svg>`;
  }

  // Modal functions
  function poffszOpenModal(title, content) {
    const modal = document.querySelector('.poffsz-modal-overlay');
    const titleEl = modal.querySelector('.poffsz-modal-title');
    const contentEl = modal.querySelector('.poffsz-modal-content');
    
    if (!modal || !titleEl || !contentEl) {
      console.warn('Modal elements not found. Make sure to include the modal HTML.');
      return;
    }
    
    titleEl.textContent = title;
    contentEl.innerHTML = content;
    modal.style.display = 'flex';
  }

  function poffszCloseModal() {
    const modal = document.querySelector('.poffsz-modal-overlay');
    if (modal) modal.style.display = 'none';
  }

  // Initialize modal on page load
  document.addEventListener('DOMContentLoaded', function() {
    const modal = document.querySelector('.poffsz-modal-overlay');
    if (modal) {
      // Close on overlay click
      modal.addEventListener('click', function(e) {
        if (e.target === this) poffszCloseModal();
      });
      
      // Close on escape
      document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.style.display === 'flex') {
          poffszCloseModal();
        }
      });
      
      // Close button
      const closeBtn = modal.querySelector('.poffsz-modal-close');
      if (closeBtn) {
        closeBtn.addEventListener('click', poffszCloseModal);
      }
    }
  });
</script>

<!-- Modal HTML Template -->
<div class="poffsz-modal-overlay">
  <div class="poffsz-modal-panel">
    <div class="poffsz-modal-header">
      <h2 class="poffsz-modal-title">Modal Title</h2>
      <button class="poffsz-modal-close">&times;</button>
    </div>
    <div class="poffsz-modal-content">
      <!-- Content goes here -->
    </div>
  </div>
</div>


