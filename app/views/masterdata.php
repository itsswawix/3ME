<?php
// Ensure session is started and check login
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * masterdata.php (General Master Data Only)
 * Master Data Management - General configuration data only
 * Company/Department/Job management moved to Requisition module
 */

$pageTitle = "Master Data";
$activeMenu = "Master Data";

// TEMPORARY: Create a default session for development if none exists
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 'USER-ADMIN-001';
    $_SESSION['user_email'] = 'admin@3me.com';
    $_SESSION['user_name'] = 'System Administrator';
    $_SESSION['user_role'] = 'Admin';
    error_log("Created temporary admin session for development");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title><?php echo $pageTitle; ?></title>
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&family=Outfit:wght@400;500;600&display=swap" rel="stylesheet">
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: radial-gradient(circle at 20% 30%, #eef2ff, #e0e7ff); font-family: 'Inter', sans-serif; min-height: 100vh; font-size: 13px; }
        .app-layout { display: flex; min-height: 100vh; position: relative; }
        .main-content { flex: 1; padding: 20px 24px; overflow-y: auto; max-height: 100vh; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .page-header h1 { font-size: 22px; font-weight: 600; color: #0f172a; }
        .page-header h1 i { color: #4f46e5; margin-right: 8px; }
        .header-actions { display: flex; gap: 10px; }
        .btn { padding: 8px 16px; border-radius: 20px; border: none; font-weight: 500; font-size: 12px; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 6px; }
        .btn-primary { background: #4f46e5; color: white; }
        .btn-primary:hover { background: #4338ca; transform: translateY(-1px); }
        .btn-secondary { background: white; color: #1e293b; border: 1px solid #e2e8f0; }
        .btn-secondary:hover { background: #f8fafc; }
        .filter-bar { display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; }
        .search-box { flex: 1; min-width: 240px; position: relative; }
        .search-box i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 13px; }
        .search-box input { width: 100%; padding: 10px 14px 10px 40px; border-radius: 24px; border: 1px solid #e2e8f0; background: rgba(255,255,255,0.9); font-size: 13px; outline: none; transition: all 0.2s; }
        .search-box input:focus { border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); }
        .filter-select { padding: 10px 16px; border-radius: 24px; border: 1px solid #e2e8f0; background: rgba(255,255,255,0.9); font-size: 13px; color: #1e293b; cursor: pointer; outline: none; }
        .stats-mini { display: flex; gap: 16px; margin-bottom: 20px; flex-wrap: wrap; align-items: center; }
        .stat-mini-card { background: rgba(255,255,255,0.9); padding: 10px 18px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.6); display: flex; align-items: center; gap: 12px; }
        .stat-mini-card i { color: #4f46e5; font-size: 16px; }
        .stat-mini-card span { font-weight: 600; color: #0f172a; }
        .stat-mini-card small { color: #64748b; margin-left: 6px; }
        .table-card { background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); border-radius: 24px; padding: 20px; box-shadow: 0 8px 20px -8px rgba(0,0,0,0.05); border: 1px solid rgba(255,255,255,0.7); }
        .table-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
        .table-header h3 { font-size: 16px; font-weight: 600; color: #0f172a; display: flex; align-items: center; gap: 8px; }
        .master-table { width: 100%; border-collapse: collapse; }
        .master-table th { text-align: left; padding: 12px 8px; font-weight: 600; color: #475569; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #e2e8f0; }
        .master-table td { padding: 12px 8px; border-bottom: 1px solid #f1f5f9; color: #1e293b; font-size: 13px; }
        .master-table tbody tr:hover { background: rgba(79, 70, 229, 0.03); }
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 500; display: inline-block; }
        .badge-success { background: #dcfce7; color: #16a34a; }
        .badge-secondary { background: #f1f5f9; color: #64748b; }
        .type-badge { display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; background: #f1f5f9; border-radius: 20px; font-size: 11px; color: #475569; }
        .type-badge i { color: #4f46e5; font-size: 10px; }
        .status-toggle { width: 40px; height: 20px; background: #e2e8f0; border-radius: 10px; position: relative; cursor: pointer; transition: background 0.2s; display: inline-block; }
        .status-toggle.active { background: #10b981; }
        .status-toggle .toggle-dot { width: 16px; height: 16px; background: white; border-radius: 50%; position: absolute; top: 2px; left: 2px; transition: left 0.2s; box-shadow: 0 1px 3px rgba(0,0,0,0.2); }
        .status-toggle.active .toggle-dot { left: 22px; }
        .action-icons i { color: #94a3b8; margin: 0 4px; cursor: pointer; transition: color 0.2s; font-size: 14px; }
        .action-icons i:hover { color: #4f46e5; }
        .pagination { display: flex; justify-content: space-between; align-items: center; margin-top: 20px; }
        .pagination-info { color: #64748b; font-size: 12px; }
        .pagination-controls { display: flex; gap: 6px; }
        .page-btn { width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 10px; background: white; border: 1px solid #e2e8f0; color: #1e293b; cursor: pointer; transition: all 0.2s; font-size: 12px; }
        .page-btn:hover { background: #4f46e5; color: white; border-color: #4f46e5; }
        .page-btn.active { background: #4f46e5; color: white; border-color: #4f46e5; }
        .empty-state { text-align: center; padding: 40px; color: #64748b; }
        .empty-state i { font-size: 40px; color: #cbd5e1; margin-bottom: 16px; }
        .empty-state h4 { font-size: 15px; font-weight: 500; margin-bottom: 8px; color: #1e293b; }
        .info-card { background: linear-gradient(135deg, #dbeafe, #eff6ff); border: 1px solid #93c5fd; border-radius: 16px; padding: 16px; margin-bottom: 20px; }
        .info-card h4 { font-size: 0.95rem; font-weight: 600; color: #1e40af; margin-bottom: 8px; display: flex; align-items: center; gap: 8px; }
        .info-card p { font-size: 0.85rem; color: #1e40af; line-height: 1.5; }
        @media (max-width: 768px) { .main-content { padding: 14px; } .master-table { display: block; overflow-x: auto; } }
    </style>
</head>
<body>
<div class="app-layout">
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-database"></i> Master Data Management</h1>
        </div>

        <!-- Info Card -->
        <div class="info-card">
            <h4><i class="fas fa-info-circle"></i> Note</h4>
            <p>Company, Department, and Job management has been moved to the <strong>Job Requisitions</strong> module under Recruitment. Use this page to manage general configuration data like employment types, leave types, and other system-wide settings.</p>
        </div>

        <!-- General Master Data Content -->
        <div id="generalContent"></div>
    </main>
</div>

<?php include 'modals/modal-wrapper.php'; ?>
<?php include 'modals/masterdata-modal/modal-masterdata-helpers.php'; ?>
<?php include 'modals/masterdata-modal/modal-add-master.php'; ?>
<?php include 'modals/masterdata-modal/modal-edit-master.php'; ?>
<?php include 'modals/masterdata-modal/modal-view-master.php'; ?>

<script>
    // Initialize data array
    window.masterData = [];

    // Pagination variables
    let currentMasterPage = 1;
    let itemsPerPage = 10;
    let filteredMaster = [];
    let masterSearchTerm = '';

    // ---------- HELPER FUNCTIONS ----------
    function escapeHtml(str) { 
        if (!str) return ''; 
        return str.replace(/[&<>]/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;' })[m] || m); 
    }

    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        const bgColor = type === 'success' ? '#10b981' : type === 'warning' ? '#f59e0b' : '#1e293b';
        toast.style.cssText = `position: fixed; bottom: 24px; right: 24px; background: ${bgColor}; color: white; padding: 12px 20px; border-radius: 12px; font-size: 13px; z-index: 10000; animation: slideIn 0.3s ease; box-shadow: 0 4px 12px rgba(0,0,0,0.15);`;
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, 3000);
    }

    // ---------- RENDER GENERAL DATA TAB ----------
    function renderGeneralDataTab() {
        // Apply search filter
        const term = masterSearchTerm.toLowerCase();
        filteredMaster = window.masterData.filter(m => 
            m.value.toLowerCase().includes(term) || m.dataType.toLowerCase().includes(term)
        );

        const start = (currentMasterPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const paginatedData = filteredMaster.slice(start, end);

        const html = `
            <div class="filter-bar">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search by value or data type..." id="masterSearchInput" value="${escapeHtml(masterSearchTerm)}">
                </div>
                <select class="filter-select" id="dataTypeFilter">
                    <option value="">All Data Types</option>
                    <option value="Departments">Departments</option>
                    <option value="Job Titles">Job Titles</option>
                    <option value="Employment Types">Employment Types</option>
                    <option value="Leave Types">Leave Types</option>
                </select>
                <select class="filter-select" id="activeFilter">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>

            <div class="stats-mini">
                <div class="stat-mini-card">
                    <i class="fas fa-database"></i>
                    <span>${window.masterData.length}</span> <small>Total Records</small>
                </div>
                <div class="stat-mini-card">
                    <i class="fas fa-check-circle"></i>
                    <span>${window.masterData.filter(m => m.isActive).length}</span> <small>Active</small>
                </div>
                <div class="stat-mini-card">
                    <i class="fas fa-tags"></i>
                    <span>${[...new Set(window.masterData.map(m => m.dataType))].length}</span> <small>Data Types</small>
                </div>
                <div class="header-actions">
                    <button class="btn btn-primary" onclick="window.openAddMasterModal()"><i class="fas fa-plus"></i> Add Master Data</button>
                </div>
            </div>

            <div class="table-card">
                <div class="table-header">
                    <h3><i class="fas fa-database"></i> Master Data Configuration</h3>
                </div>
                <table class="master-table">
                    <thead>
                        <tr>
                            <th>Data Type</th>
                            <th>Value</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${paginatedData.length > 0 ? paginatedData.map(master => {
                            const typeIcon = { 'Departments': 'building', 'Job Titles': 'briefcase', 'Employment Types': 'clock', 'Leave Types': 'umbrella-beach' }[master.dataType] || 'tag';
                            return `
                                <tr>
                                    <td><span class="type-badge"><i class="fas fa-${typeIcon}"></i>${escapeHtml(master.dataType)}</span></td>
                                    <td><strong>${escapeHtml(master.value)}</strong></td>
                                    <td>${escapeHtml(master.description) || '—'}</td>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <div class="status-toggle ${master.isActive ? 'active' : ''}" onclick="toggleMasterDataStatus('${master.id}')">
                                                <div class="toggle-dot"></div>
                                            </div>
                                            <span class="badge ${master.isActive ? 'badge-success' : 'badge-secondary'}">${master.isActive ? 'Active' : 'Inactive'}</span>
                                        </div>
                                    </td>
                                    <td class="action-icons">
                                        <i class="fas fa-eye" onclick="viewMasterData('${master.id}')" title="View"></i>
                                        <i class="fas fa-edit" onclick="editMasterData('${master.id}')" title="Edit"></i>
                                    </td>
                                </tr>
                            `;
                        }).join('') : `
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        <i class="fas fa-database"></i>
                                        <h4>No Master Data Found</h4>
                                        <p>No master data records match your search</p>
                                    </div>
                                </td>
                            </tr>
                        `}
                    </tbody>
                </table>

                <div class="pagination">
                    <div class="pagination-info" id="masterPaginationInfo">
                        ${filteredMaster.length > 0 ? `Showing ${start + 1}-${Math.min(end, filteredMaster.length)} of ${filteredMaster.length} records` : 'No records'}
                    </div>
                    <div class="pagination-controls" id="masterPaginationControls"></div>
                </div>
            </div>
        `;

        document.getElementById('generalContent').innerHTML = html;
        renderMasterPagination();
        
        const searchInput = document.getElementById('masterSearchInput');
        if (searchInput) {
            searchInput.addEventListener('keyup', (e) => {
                masterSearchTerm = e.target.value;
                currentMasterPage = 1;
                renderGeneralDataTab();
            });
        }

        const dataTypeFilter = document.getElementById('dataTypeFilter');
        if (dataTypeFilter) {
            dataTypeFilter.addEventListener('change', applyMasterFilters);
        }

        const activeFilter = document.getElementById('activeFilter');
        if (activeFilter) {
            activeFilter.addEventListener('change', applyMasterFilters);
        }
    }

    function renderMasterPagination() {
        const totalPages = Math.ceil(filteredMaster.length / itemsPerPage);
        const container = document.getElementById('masterPaginationControls');
        let html = `<div class="page-btn" onclick="changeMasterPage(${currentMasterPage - 1})" ${currentMasterPage === 1 ? 'style="opacity:0.5; pointer-events:none;"' : ''}><i class="fas fa-chevron-left"></i></div>`;
        for (let i = 1; i <= Math.min(totalPages, 5); i++) {
            html += `<div class="page-btn ${currentMasterPage === i ? 'active' : ''}" onclick="changeMasterPage(${i})">${i}</div>`;
        }
        if (totalPages > 5) html += `<div class="page-btn">...</div>`;
        if (totalPages > 5) html += `<div class="page-btn" onclick="changeMasterPage(${totalPages})">${totalPages}</div>`;
        html += `<div class="page-btn" onclick="changeMasterPage(${currentMasterPage + 1})" ${currentMasterPage === totalPages || totalPages === 0 ? 'style="opacity:0.5; pointer-events:none;"' : ''}><i class="fas fa-chevron-right"></i></div>`;
        container.innerHTML = html;
    }

    function applyMasterFilters() {
        const term = document.getElementById('masterSearchInput').value.toLowerCase();
        const type = document.getElementById('dataTypeFilter').value;
        const active = document.getElementById('activeFilter').value;
        
        filteredMaster = window.masterData.filter(m => {
            const matchesSearch = m.value.toLowerCase().includes(term) || m.dataType.toLowerCase().includes(term);
            const matchesType = !type || m.dataType === type;
            const matchesActive = active === '' || (active === 'active' && m.isActive) || (active === 'inactive' && !m.isActive);
            return matchesSearch && matchesType && matchesActive;
        });
        
        currentMasterPage = 1;
        renderGeneralDataTab();
    }

    function changeMasterPage(page) {
        if (page >= 1 && page <= Math.ceil(filteredMaster.length / itemsPerPage)) {
            currentMasterPage = page;
            renderGeneralDataTab();
        }
    }

    // ---------- API FUNCTIONS ----------
    async function fetchAPI(action, method = 'GET', data = null) {
        const url = `../../api/settings/settings_api.php?action=${action}`;
        const options = {
            method: method,
            headers: { 'Content-Type': 'application/json' }
        };
        if (data && method !== 'GET') options.body = JSON.stringify(data);
        
        try {
            const response = await fetch(url, options);
            const result = await response.json();
            if (!result.success) throw new Error(result.message);
            return result;
        } catch (error) {
            showToast(error.message || 'An error occurred', 'warning');
            throw error;
        }
    }

    async function loadMasterData() {
        try {
            const result = await fetchAPI('list_master_data');
            window.masterData = result.data || [];
            renderGeneralDataTab();
        } catch (error) {
            console.error('Failed to load master data:', error);
        }
    }

    // ---------- VIEW/EDIT FUNCTIONS ----------
    function viewMasterData(id) {
        const master = window.masterData.find(m => m.id === id);
        if (master && typeof window.openViewMasterModal === 'function') {
            window.openViewMasterModal(master);
        } else {
            showToast('View Master Data: ' + master.value, 'info');
        }
    }

    function editMasterData(id) {
        const master = window.masterData.find(m => m.id === id);
        if (master && typeof window.openEditMasterModal === 'function') {
            window.openEditMasterModal(master);
        } else {
            showToast('Edit Master Data: ' + master.value, 'info');
        }
    }

    function toggleMasterDataStatus(id) { 
        const master = window.masterData.find(m => m.id === id); 
        if (master) { 
            master.isActive = !master.isActive; 
            renderGeneralDataTab(); 
            showToast(`Master data ${master.isActive ? 'activated' : 'deactivated'}.`, 'success'); 
        } 
    }

    // Animation styles
    const style = document.createElement('style');
    style.textContent = `@keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }`;
    document.head.appendChild(style);

    // ---------- INITIALIZE ----------
    (async function() {
        // Set active menu
        const navItems = document.querySelectorAll('.nav-item');
        navItems.forEach(item => {
            item.classList.remove('active');
            const title = item.getAttribute('data-title');
            if (title === 'Master Data') {
                item.classList.add('active');
            }
        });
        
        // Load master data
        await loadMasterData();

        console.log('✅ Master Data loaded (General data only)');
    })();
</script>

</body>
</html>
