<?php
/**
 * administration.php
 * Administration & System Settings - Clean interface with sidebar and right-side modal components
 */

$pageTitle = "Administration";
$activeMenu = "Administration";

// Ensure session is started and check login
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// TEMPORARY: Create a default session for development if none exists
if (!isset($_SESSION['user_id'])) {
    // Create a temporary admin session for development
    $_SESSION['user_id'] = 'USER-ADMIN-001';
    $_SESSION['user_email'] = 'admin@3me.com';
    $_SESSION['user_name'] = 'System Administrator';
    $_SESSION['user_role'] = 'Admin';
    
    // Log this for debugging
    error_log("Created temporary admin session for development");
}

// Original redirect code (commented out for development)
// if (!isset($_SESSION['user_id'])) {
//     header('Location: ../../login.php');
//     exit;
// }

// Debug: Log session info (remove in production)
// error_log("Session user_role: " . ($_SESSION['user_role'] ?? 'not set'));
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
        .tabs-container { display: flex; gap: 4px; margin-bottom: 20px; background: rgba(255,255,255,0.5); padding: 4px; border-radius: 24px; width: fit-content; flex-wrap: wrap; }
        .tab-btn { padding: 8px 20px; border-radius: 20px; border: none; background: transparent; font-size: 13px; font-weight: 500; color: #64748b; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 6px; }
        .tab-btn.active { background: white; color: #4f46e5; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
        .tab-btn:hover:not(.active) { color: #1e293b; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .master-sub-tab { display: none; }
        .master-sub-tab.active { display: block; }
        .filter-bar { display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; }
        .search-box { flex: 1; min-width: 240px; position: relative; }
        .search-box i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 13px; }
        .search-box input { width: 100%; padding: 10px 14px 10px 40px; border-radius: 24px; border: 1px solid #e2e8f0; background: rgba(255,255,255,0.9); font-size: 13px; outline: none; transition: all 0.2s; }
        .search-box input:focus { border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); }
        .filter-select { padding: 10px 16px; border-radius: 24px; border: 1px solid #e2e8f0; background: rgba(255,255,255,0.9); font-size: 13px; color: #1e293b; cursor: pointer; outline: none; }
        .stats-mini { display: flex; gap: 16px; margin-bottom: 20px; flex-wrap: wrap; align-items: center; }
        .stat-mini-card { background: rgba(255,255,255,0.9); padding: 10px 18px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.6); display: flex; align-items: center; gap: 12px; }
        .stat-mini-card span { font-weight: 600; color: #0f172a; }
        .stat-mini-card small { color: #64748b; margin-left: 6px; }
        .table-card { background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); border-radius: 24px; padding: 20px; box-shadow: 0 8px 20px -8px rgba(0,0,0,0.05); border: 1px solid rgba(255,255,255,0.7); }
        .table-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
        .table-header h3 { font-size: 16px; font-weight: 600; color: #0f172a; }
        .breadcrumb-link-header { color: #4f46e5; cursor: pointer; font-weight: 500; }
        .breadcrumb-link-header:hover { color: #4338ca; text-decoration: underline; }
        .breadcrumb-separator-header { color: #cbd5e1; font-size: 10px; margin: 0 4px; }
        .breadcrumb-current-header { color: #0f172a; font-weight: 600; }
        .master-table, .user-table, .company-table, .department-table, .position-table { width: 100%; border-collapse: collapse; }
        .master-table th, .user-table th, .company-table th, .department-table th, .position-table th { text-align: left; padding: 12px 8px; font-weight: 600; color: #475569; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #e2e8f0; }
        .master-table td, .user-table td, .company-table td, .department-table td, .position-table td { padding: 12px 8px; border-bottom: 1px solid #f1f5f9; color: #1e293b; font-size: 13px; }
        .master-table tbody tr:hover, .user-table tbody tr:hover, .company-table tbody tr:hover, .department-table tbody tr:hover, .position-table tbody tr:hover { background: rgba(79, 70, 229, 0.03); }
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 500; display: inline-block; }
        .badge-success { background: #dcfce7; color: #16a34a; }
        .badge-warning { background: #fef3c7; color: #b45309; }
        .badge-info { background: #dbeafe; color: #2563eb; }
        .badge-danger { background: #fee2e2; color: #dc2626; }
        .badge-secondary { background: #f1f5f9; color: #64748b; }
        .badge-purple { background: #f3e8ff; color: #9333ea; }
        .action-icons i { color: #94a3b8; margin: 0 4px; cursor: pointer; transition: color 0.2s; font-size: 14px; }
        .action-icons i:hover { color: #4f46e5; }
        .user-cell { display: flex; align-items: center; gap: 10px; }
        .user-avatar { width: 36px; height: 36px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 13px; color: white; }
        .user-info h4 { font-weight: 600; font-size: 13px; margin-bottom: 2px; }
        .user-info p { font-size: 11px; color: #64748b; }
        .company-cell { display: flex; align-items: center; gap: 10px; }
        .company-info h4 { font-weight: 600; font-size: 13px; margin-bottom: 2px; }
        .clickable-row { cursor: pointer; }
        .type-badge { display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; background: #f1f5f9; border-radius: 20px; font-size: 11px; color: #475569; }
        .type-badge i { color: #4f46e5; font-size: 10px; }
        .status-toggle { width: 40px; height: 20px; background: #e2e8f0; border-radius: 10px; position: relative; cursor: pointer; transition: background 0.2s; display: inline-block; }
        .status-toggle.active { background: #10b981; }
        .status-toggle .toggle-dot { width: 16px; height: 16px; background: white; border-radius: 50%; position: absolute; top: 2px; left: 2px; transition: left 0.2s; box-shadow: 0 1px 3px rgba(0,0,0,0.2); }
        .status-toggle.active .toggle-dot { left: 22px; }
        .pagination { display: flex; justify-content: space-between; align-items: center; margin-top: 20px; }
        .pagination-info { color: #64748b; font-size: 12px; }
        .pagination-controls { display: flex; gap: 6px; }
        .page-btn { width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 10px; background: white; border: 1px solid #e2e8f0; color: #1e293b; cursor: pointer; transition: all 0.2s; font-size: 12px; }
        .page-btn:hover { background: #4f46e5; color: white; border-color: #4f46e5; }
        .page-btn.active { background: #4f46e5; color: white; border-color: #4f46e5; }
        .back-button { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; background: white; border: 1px solid #e2e8f0; border-radius: 20px; color: #475569; font-size: 12px; cursor: pointer; margin-bottom: 16px; transition: all 0.2s; }
        .back-button:hover { background: #f8fafc; border-color: #cbd5e1; }
        @media (max-width: 768px) { .main-content { padding: 14px; } .master-table, .user-table, .company-table, .department-table, .position-table { display: block; overflow-x: auto; } }
    </style>
</head>
<body>
<div class="app-layout">
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <div class="page-header">
            <h1> Administration & System Settings</h1>
        </div>

        <div class="tabs-container">
            <button class="tab-btn active" onclick="switchTab('users')"><i class="fas fa-users-cog"></i> User Management</button>
        </div>

        <!-- User Management Tab -->
        <div id="usersTab" class="tab-content active">
            <div class="filter-bar">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search by name, email, or role..." id="userSearchInput">
                </div>
                <select class="filter-select" id="roleFilter">
                    <option value="">All Roles</option>
                    <option value="Admin">Admin</option>
                    <option value="HR Manager">HR Manager</option>
                    <option value="Manager">Manager</option>
                    <option value="Employee">Employee</option>
                </select>
                <select class="filter-select" id="userStatusFilter">
                    <option value="">All Status</option>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                    <option value="Locked">Locked</option>
                </select>
            </div>

            <div class="stats-mini">
                <div class="stat-mini-card"><i class="fas fa-users"></i><span id="totalUsers">0</span> <small>Total Users</small></div>
                <div class="stat-mini-card"><i class="fas fa-user-check"></i><span id="activeUsers">0</span> <small>Active</small></div>
                <div class="stat-mini-card"><i class="fas fa-user-lock"></i><span id="lockedUsers">0</span> <small>Locked</small></div>
                <div class="stat-mini-card"><i class="fas fa-user-cog"></i><span id="adminUsers">0</span> <small>Admins</small></div>
                <div class="header-actions"><button class="btn btn-primary" onclick="openAddUserModal()"><i class="fas fa-plus"></i> Add User</button></div>
            </div>

            <div class="table-card">
                <div class="table-header"><h3> User Management</h3></div>
                <table class="user-table">
                    <thead><tr><th>User</th><th>Role</th><th>Contact</th><th>Status</th><th></th></tr></thead>
                    <tbody id="userTableBody"></tbody>
                </table>
                <div class="pagination"><div class="pagination-info" id="userPaginationInfo">Showing 0 of 0 users</div><div class="pagination-controls" id="userPaginationControls"></div></div>
            </div>
        </div>
    </main>
</div>

<?php include 'modals/modal-wrapper.php'; ?>
<?php include 'modals/settings-modal/modal-add-user.php'; ?>
<?php include 'modals/settings-modal/modal-edit-user.php'; ?>
<?php include 'modals/settings-modal/modal-view-user.php'; ?>
<?php include 'modals/settings-modal/modal-settings-helpers.php'; ?>

<script>
    // Initialize empty data arrays
    window.users = [];
    window.companies = [];
    window.departments = [];
    window.positions = [];
    window.masterData = [];

    let currentOrgLevel = 'companies', selectedCompanyId = null, selectedDepartmentId = null, selectedCompanyName = '', selectedDepartmentName = '';
    let currentUserPage = 1, currentCompanyPage = 1, currentDepartmentPage = 1, currentPositionPage = 1, currentMasterPage = 1;
    let itemsPerPage = 5;
    let filteredUsers = [...window.users], filteredCompanies = [...window.companies], filteredDepartments = [], filteredPositions = [], filteredMaster = [...window.masterData];
    let currentMasterSubTab = 'general'; // Track current master data sub-tab

    function escapeHtml(str) { if (!str) return ''; return str.replace(/[&<>]/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;' })[m] || m); }

    function updateUserStats() {
        document.getElementById('totalUsers').innerText = window.users.length;
        document.getElementById('activeUsers').innerText = window.users.filter(u => u.status === 'Active').length;
        document.getElementById('lockedUsers').innerText = window.users.filter(u => u.status === 'Locked').length;
        document.getElementById('adminUsers').innerText = window.users.filter(u => u.role === 'Admin').length;
    }

    function updateMasterStats() {
        document.getElementById('totalMasterRecords').innerText = window.masterData.length;
        document.getElementById('activeMasterRecords').innerText = window.masterData.filter(m => m.isActive).length;
        document.getElementById('dataTypesCount').innerText = [...new Set(window.masterData.map(m => m.dataType))].length;
        
        // Update organizational stats
        document.getElementById('totalCompanies').innerText = window.companies.length;
        document.getElementById('totalDepartmentsInCompanies').innerText = window.departments.length;
        document.getElementById('totalDepartments').innerText = window.departments.length;
        document.getElementById('totalPositionsInDepartments').innerText = window.positions.length;
        document.getElementById('totalPositions').innerText = window.positions.length;
        document.getElementById('totalVacancies').innerText = window.positions.reduce((sum, p) => sum + (p.vacancies || 0), 0);
    }

    function renderUserTable(data) {
        filteredUsers = data; updateUserStats();
        const start = (currentUserPage - 1) * itemsPerPage, end = start + itemsPerPage;
        const paginatedData = filteredUsers.slice(start, end);
        document.getElementById('userTableBody').innerHTML = paginatedData.map(user => {
            const statusClass = { 'Active': 'badge-success', 'Inactive': 'badge-secondary', 'Locked': 'badge-danger' }[user.status] || 'badge-secondary';
            const roleClass = { 'Admin': 'badge-purple', 'HR Manager': 'badge-info', 'Manager': 'badge-warning', 'Employee': 'badge-secondary' }[user.role] || 'badge-secondary';
            return `<tr><td><div class="user-cell"><div class="user-avatar" style="background: ${user.color};">${user.avatar}</div><div class="user-info"><h4>${escapeHtml(user.name)}</h4><p>${escapeHtml(user.email)}</p></div></div></td><td><span class="badge ${roleClass}">${user.role}</span></td><td><div><i class="fas fa-phone" style="color: #94a3b8; font-size: 10px;"></i> ${user.contactNumber || '—'}</div><small>${user.department || ''}</small></td><td><span class="badge ${statusClass}">${user.status}</span></td><td class="action-icons"><i class="fas fa-eye" onclick="viewUser('${user.id}')" title="View"></i><i class="fas fa-edit" onclick="editUser('${user.id}')" title="Edit"></i></td></tr>`;
        }).join('');
        document.getElementById('userPaginationInfo').textContent = `Showing ${start + 1}-${Math.min(end, filteredUsers.length)} of ${filteredUsers.length} users`;
        renderPagination('user', currentUserPage, Math.ceil(filteredUsers.length / itemsPerPage));
    }

    function renderMasterTable(data) {
        filteredMaster = data; updateMasterStats();
        const start = (currentMasterPage - 1) * itemsPerPage, end = start + itemsPerPage;
        const paginatedData = filteredMaster.slice(start, end);
        document.getElementById('masterTableBody').innerHTML = paginatedData.map(master => {
            const typeIcon = { 'Departments': 'building', 'Job Titles': 'briefcase', 'Employment Types': 'clock', 'Leave Types': 'umbrella-beach' }[master.dataType] || 'tag';
            return `<tr><td><span class="type-badge"><i class="fas fa-${typeIcon}"></i>${escapeHtml(master.dataType)}</span></td><td><strong>${escapeHtml(master.value)}</strong></td><td>${escapeHtml(master.description) || '—'}</td><td><div style="display: flex; align-items: center; gap: 8px;"><div class="status-toggle ${master.isActive ? 'active' : ''}" onclick="toggleMasterDataStatus('${master.id}')"><div class="toggle-dot"></div></div><span class="badge ${master.isActive ? 'badge-success' : 'badge-secondary'}">${master.isActive ? 'Active' : 'Inactive'}</span></div></td><td class="action-icons"><i class="fas fa-eye" onclick="viewMasterData('${master.id}')" title="View"></i><i class="fas fa-edit" onclick="editMasterData('${master.id}')" title="Edit"></i></td></tr>`;
        }).join('');
        document.getElementById('masterPaginationInfo').textContent = `Showing ${start + 1}-${Math.min(end, filteredMaster.length)} of ${filteredMaster.length} records`;
        renderPagination('master', currentMasterPage, Math.ceil(filteredMaster.length / itemsPerPage));
    }

    function renderPagination(type, currentPage, totalPages) {
        const container = document.getElementById(type + 'PaginationControls');
        let html = `<div class="page-btn" onclick="change${type.charAt(0).toUpperCase() + type.slice(1)}Page(${currentPage - 1})" ${currentPage === 1 ? 'style="opacity:0.5; pointer-events:none;"' : ''}><i class="fas fa-chevron-left"></i></div>`;
        for (let i = 1; i <= Math.min(totalPages, 5); i++) html += `<div class="page-btn ${currentPage === i ? 'active' : ''}" onclick="change${type.charAt(0).toUpperCase() + type.slice(1)}Page(${i})">${i}</div>`;
        if (totalPages > 5) html += `<div class="page-btn">...</div>`;
        html += `<div class="page-btn" onclick="change${type.charAt(0).toUpperCase() + type.slice(1)}Page(${currentPage + 1})" ${currentPage === totalPages || totalPages === 0 ? 'style="opacity:0.5; pointer-events:none;"' : ''}><i class="fas fa-chevron-right"></i></div>`;
        container.innerHTML = html;
    }

    function changeUserPage(page) { if (page >= 1 && page <= Math.ceil(filteredUsers.length / itemsPerPage)) { currentUserPage = page; renderUserTable(filteredUsers); } }
    function changeMasterPage(page) { if (page >= 1 && page <= Math.ceil(filteredMaster.length / itemsPerPage)) { currentMasterPage = page; renderMasterTable(filteredMaster); } }
    function changeCompanyPage(page) { if (page >= 1 && page <= Math.ceil(filteredCompanies.length / itemsPerPage)) { currentCompanyPage = page; renderCompanyTable(filteredCompanies); } }
    function changeDepartmentPage(page) { if (page >= 1 && page <= Math.ceil(filteredDepartments.length / itemsPerPage)) { currentDepartmentPage = page; renderDepartmentTable(filteredDepartments); } }
    function changePositionPage(page) { if (page >= 1 && page <= Math.ceil(filteredPositions.length / itemsPerPage)) { currentPositionPage = page; renderPositionTable(filteredPositions); } }

    // Switch between master data sub-tabs
    function switchMasterSubTab(subTab) {
        currentMasterSubTab = subTab;
        
        // Update sub-tab buttons
        document.querySelectorAll('#masterTab .tabs-container .tab-btn').forEach(btn => btn.classList.remove('active'));
        event.target.classList.add('active');
        
        // Hide all sub-tabs
        document.querySelectorAll('.master-sub-tab').forEach(tab => tab.classList.remove('active'));
        
        // Show selected sub-tab
        if (subTab === 'general') {
            document.getElementById('generalMasterSubTab').classList.add('active');
            renderMasterTable(filteredMaster);
        } else if (subTab === 'companies') {
            document.getElementById('companiesMasterSubTab').classList.add('active');
            renderCompanyTable(window.companies);
        } else if (subTab === 'departments') {
            document.getElementById('departmentsMasterSubTab').classList.add('active');
            populateDepartmentCompanyFilter();
            renderDepartmentTable(window.departments);
        } else if (subTab === 'positions') {
            document.getElementById('positionsMasterSubTab').classList.add('active');
            populatePositionDepartmentFilter();
            renderPositionTable(window.positions);
        }
    }

    // Render companies table
    function renderCompanyTable(data) {
        filteredCompanies = data;
        updateMasterStats();
        
        const start = (currentCompanyPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const paginatedData = filteredCompanies.slice(start, end);
        
        document.getElementById('companyTableBody').innerHTML = paginatedData.map(company => `
            <tr>
                <td>
                    <div class="company-cell">
                        <div class="company-info">
                            <h4>${escapeHtml(company.name)}</h4>
                        </div>
                    </div>
                </td>
                <td><strong>${company.employeeCount || 0}</strong></td>
                <td><span class="badge badge-success">${company.status || 'Active'}</span></td>
                <td class="action-icons">
                    <i class="fas fa-eye" onclick="viewCompany('${company.id}')" title="View"></i>
                    <i class="fas fa-edit" onclick="editCompany('${company.id}')" title="Edit"></i>
                </td>
            </tr>
        `).join('');
        
        document.getElementById('companyPaginationInfo').textContent = `Showing ${start + 1}-${Math.min(end, filteredCompanies.length)} of ${filteredCompanies.length} companies`;
        renderPagination('company', currentCompanyPage, Math.ceil(filteredCompanies.length / itemsPerPage));
    }

    // Render departments table
    function renderDepartmentTable(data) {
        filteredDepartments = data;
        updateMasterStats();
        
        const start = (currentDepartmentPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const paginatedData = filteredDepartments.slice(start, end);
        
        document.getElementById('departmentTableBody').innerHTML = paginatedData.map(dept => {
            const company = window.companies.find(c => c.id === dept.companyId);
            return `
                <tr>
                    <td>
                        <div class="company-cell">
                            <div class="company-info">
                                <h4>${escapeHtml(dept.name)}</h4>
                            </div>
                        </div>
                    </td>
                    <td>${escapeHtml(company ? company.name : 'N/A')}</td>
                    <td><span class="badge badge-purple">${dept.code || 'N/A'}</span></td>
                    <td>${escapeHtml(dept.head || 'N/A')}</td>
                    <td><strong>${dept.employeeCount || 0}</strong></td>
                    <td><span class="badge badge-success">${dept.status || 'Active'}</span></td>
                    <td class="action-icons">
                        <i class="fas fa-eye" onclick="viewDepartment('${dept.id}')" title="View"></i>
                        <i class="fas fa-edit" onclick="editDepartment('${dept.id}')" title="Edit"></i>
                    </td>
                </tr>
            `;
        }).join('');
        
        document.getElementById('departmentPaginationInfo').textContent = `Showing ${start + 1}-${Math.min(end, filteredDepartments.length)} of ${filteredDepartments.length} departments`;
        renderPagination('department', currentDepartmentPage, Math.ceil(filteredDepartments.length / itemsPerPage));
    }

    // Render positions table
    function renderPositionTable(data) {
        filteredPositions = data;
        updateMasterStats();
        
        const start = (currentPositionPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const paginatedData = filteredPositions.slice(start, end);
        
        document.getElementById('positionTableBody').innerHTML = paginatedData.map(position => {
            const dept = window.departments.find(d => d.id === position.departmentId);
            const levelClass = { 'Director': 'badge-purple', 'Manager': 'badge-info', 'Senior': 'badge-success', 'Mid-Level': 'badge-warning', 'Junior': 'badge-secondary', 'Entry': 'badge-secondary' }[position.level] || 'badge-secondary';
            
            return `
                <tr>
                    <td>
                        <div class="company-cell">
                            <div class="company-info">
                                <h4>${escapeHtml(position.jobTitle)}</h4>
                            </div>
                        </div>
                    </td>
                    <td>${escapeHtml(dept ? dept.name : 'N/A')}</td>
                    <td><span class="badge ${levelClass}">${position.level || 'N/A'}</span></td>
                    <td>${escapeHtml(position.reportsTo || 'N/A')}</td>
                    <td><strong>${position.vacancies || 0}</strong> open</td>
                    <td><span class="badge badge-success">${position.status || 'Active'}</span></td>
                    <td class="action-icons">
                        <i class="fas fa-eye" onclick="viewPosition('${position.id}')" title="View"></i>
                        <i class="fas fa-edit" onclick="editPosition('${position.id}')" title="Edit"></i>
                    </td>
                </tr>
            `;
        }).join('');
        
        document.getElementById('positionPaginationInfo').textContent = `Showing ${start + 1}-${Math.min(end, filteredPositions.length)} of ${filteredPositions.length} positions`;
        renderPagination('position', currentPositionPage, Math.ceil(filteredPositions.length / itemsPerPage));
    }

    // Populate department company filter
    function populateDepartmentCompanyFilter() {
        const filter = document.getElementById('departmentCompanyFilter');
        filter.innerHTML = '<option value="">All Companies</option>' + 
            window.companies.map(c => `<option value="${c.id}">${escapeHtml(c.name)}</option>`).join('');
        
        filter.addEventListener('change', function() {
            const companyId = this.value;
            filteredDepartments = companyId ? window.departments.filter(d => d.companyId === companyId) : window.departments;
            currentDepartmentPage = 1;
            renderDepartmentTable(filteredDepartments);
        });
    }

    // Populate position department filter
    function populatePositionDepartmentFilter() {
        const filter = document.getElementById('positionDepartmentFilter');
        filter.innerHTML = '<option value="">All Departments</option>' + 
            window.departments.map(d => `<option value="${d.id}">${escapeHtml(d.name)}</option>`).join('');
        
        filter.addEventListener('change', function() {
            const deptId = this.value;
            filteredPositions = deptId ? window.positions.filter(p => p.departmentId === deptId) : window.positions;
            currentPositionPage = 1;
            renderPositionTable(filteredPositions);
        });
    }

    // Apply filters for companies
    function applyCompanyFilters() {
        const term = document.getElementById('companySearchInput').value.toLowerCase();
        filteredCompanies = window.companies.filter(c => c.name.toLowerCase().includes(term));
        currentCompanyPage = 1;
        renderCompanyTable(filteredCompanies);
    }

    // Apply filters for departments
    function applyDepartmentFilters() {
        const term = document.getElementById('departmentSearchInput').value.toLowerCase();
        const companyId = document.getElementById('departmentCompanyFilter').value;
        
        filteredDepartments = window.departments.filter(d => {
            const matchesSearch = d.name.toLowerCase().includes(term) || (d.code && d.code.toLowerCase().includes(term));
            const matchesCompany = !companyId || d.companyId === companyId;
            return matchesSearch && matchesCompany;
        });
        
        currentDepartmentPage = 1;
        renderDepartmentTable(filteredDepartments);
    }

    // Apply filters for positions
    function applyPositionFilters() {
        const term = document.getElementById('positionSearchInput').value.toLowerCase();
        const deptId = document.getElementById('positionDepartmentFilter').value;
        
        filteredPositions = window.positions.filter(p => {
            const matchesSearch = p.jobTitle.toLowerCase().includes(term);
            const matchesDept = !deptId || p.departmentId === deptId;
            return matchesSearch && matchesDept;
        });
        
        currentPositionPage = 1;
        renderPositionTable(filteredPositions);
    }

    // Modal functions are defined in modal-add-org.php, modal-edit-org.php, modal-view-org.php
    
    function toggleMasterDataStatus(id) { 
        const master = window.masterData.find(m => m.id === id); 
        if (master) { 
            master.isActive = !master.isActive; 
            renderMasterTable(window.masterData); 
            showToast(`Master data ${master.isActive ? 'activated' : 'deactivated'}.`, 'success'); 
        } 
    }
    
    function exportSettings() { showToast('Exporting configuration...', 'info'); }

    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.style.cssText = `position: fixed; bottom: 24px; right: 24px; background: ${type === 'success' ? '#10b981' : type === 'warning' ? '#f59e0b' : '#1e293b'}; color: white; padding: 12px 20px; border-radius: 12px; font-size: 13px; z-index: 10000; animation: slideIn 0.3s ease; box-shadow: 0 4px 12px rgba(0,0,0,0.15);`;
        toast.textContent = message; document.body.appendChild(toast);
        setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, 3000);
    }

    function switchTab(tab) {
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
        
        if (tab === 'users') { 
            document.querySelectorAll('.tabs-container')[0].querySelectorAll('.tab-btn')[0].classList.add('active'); 
            document.getElementById('usersTab').classList.add('active'); 
            renderUserTable(filteredUsers); 
        }
        else if (tab === 'master') { 
            document.querySelectorAll('.tabs-container')[0].querySelectorAll('.tab-btn')[1].classList.add('active'); 
            document.getElementById('masterTab').classList.add('active'); 
            
            // Show the current sub-tab or default to general
            switchMasterSubTab(currentMasterSubTab || 'general');
        }
    }

    function applyUserFilters() {
        const term = document.getElementById('userSearchInput').value.toLowerCase(), role = document.getElementById('roleFilter').value, status = document.getElementById('userStatusFilter').value;
        filteredUsers = window.users.filter(u => (u.name.toLowerCase().includes(term) || u.email.toLowerCase().includes(term)) && (!role || u.role === role) && (!status || u.status === status));
        currentUserPage = 1; renderUserTable(filteredUsers);
    }

    function applyMasterFilters() {
        const term = document.getElementById('masterSearchInput').value.toLowerCase(), type = document.getElementById('dataTypeFilter').value, active = document.getElementById('activeFilter').value;
        filteredMaster = window.masterData.filter(m => (m.value.toLowerCase().includes(term) || m.dataType.toLowerCase().includes(term)) && (!type || m.dataType === type) && (active === '' || (active === 'active' && m.isActive) || (active === 'inactive' && !m.isActive)));
        currentMasterPage = 1; renderMasterTable(filteredMaster);
    }

    const style = document.createElement('style');
    style.textContent = `@keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }`;
    document.head.appendChild(style);

    // API Functions
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

    async function loadUsers() {
        try {
            const result = await fetchAPI('list_users');
            window.users = result.data.map(u => ({
                id: u.id,
                employeeId: u.employee_id,
                name: u.name,
                email: u.email,
                role: u.role,
                contactNumber: u.contact_number,
                department: u.department,
                status: u.status,
                lastLogin: u.last_login,
                avatar: u.avatar,
                color: u.color
            }));
            renderUserTable(window.users);
        } catch (error) {
            console.error('Failed to load users:', error);
        }
    }

    async function loadCompanies() {
        try {
            const result = await fetchAPI('list_companies');
            window.companies = result.data;
            renderCompanyLevel();
        } catch (error) {
            console.error('Failed to load companies:', error);
        }
    }

    async function loadDepartments() {
        try {
            const result = await fetchAPI('list_departments');
            window.departments = result.data;
        } catch (error) {
            console.error('Failed to load departments:', error);
        }
    }

    async function loadPositions() {
        try {
            const result = await fetchAPI('list_positions');
            window.positions = result.data;
        } catch (error) {
            console.error('Failed to load positions:', error);
        }
    }

    async function loadMasterData() {
        try {
            const result = await fetchAPI('list_master_data');
            window.masterData = result.data;
            renderMasterTable(window.masterData);
        } catch (error) {
            console.error('Failed to load master data:', error);
        }
    }

    async function viewUser(id) {
        try {
            const result = await fetchAPI(`get_user&id=${id}`);
            const user = result.data;
            // Open modal with user data
            if (typeof openViewUserModal === 'function') {
                openViewUserModal(user);
            } else {
                showToast('View User: ' + user.name, 'info');
            }
        } catch (error) {
            console.error('Failed to load user:', error);
        }
    }

    async function editUser(id) {
        try {
            const result = await fetchAPI(`get_user&id=${id}`);
            const user = result.data;
            // Open modal with user data
            if (typeof openEditUserModal === 'function') {
                openEditUserModal(user);
            } else {
                showToast('Edit User: ' + user.name, 'info');
            }
        } catch (error) {
            console.error('Failed to load user:', error);
        }
    }

    async function viewCompany(id) {
        try {
            const result = await fetchAPI(`get_company&id=${id}`);
            const company = result.data;
            if (typeof openViewCompanyModal === 'function') {
                openViewCompanyModal(company);
            } else {
                showToast('View Company: ' + company.name, 'info');
            }
        } catch (error) {
            console.error('Failed to load company:', error);
        }
    }

    async function editCompany(id) {
        try {
            const result = await fetchAPI(`get_company&id=${id}`);
            const company = result.data;
            if (typeof openEditCompanyModal === 'function') {
                openEditCompanyModal(company);
            } else {
                showToast('Edit Company: ' + company.name, 'info');
            }
        } catch (error) {
            console.error('Failed to load company:', error);
        }
    }

    async function viewDepartment(id) {
        try {
            const result = await fetchAPI(`get_department&id=${id}`);
            const dept = result.data;
            if (typeof openViewDepartmentModal === 'function') {
                openViewDepartmentModal(dept);
            } else {
                showToast('View Department: ' + dept.name, 'info');
            }
        } catch (error) {
            console.error('Failed to load department:', error);
        }
    }

    async function editDepartment(id) {
        try {
            const result = await fetchAPI(`get_department&id=${id}`);
            const dept = result.data;
            if (typeof openEditDepartmentModal === 'function') {
                openEditDepartmentModal(dept);
            } else {
                showToast('Edit Department: ' + dept.name, 'info');
            }
        } catch (error) {
            console.error('Failed to load department:', error);
        }
    }

    async function viewPosition(id) {
        try {
            const result = await fetchAPI(`get_position&id=${id}`);
            const pos = result.data;
            if (typeof openViewPositionModal === 'function') {
                openViewPositionModal(pos);
            } else {
                showToast('View Position: ' + pos.title, 'info');
            }
        } catch (error) {
            console.error('Failed to load position:', error);
        }
    }

    async function editPosition(id) {
        try {
            const result = await fetchAPI(`get_position&id=${id}`);
            const pos = result.data;
            if (typeof openEditPositionModal === 'function') {
                openEditPositionModal(pos);
            } else {
                showToast('Edit Position: ' + pos.title, 'info');
            }
        } catch (error) {
            console.error('Failed to load position:', error);
        }
    }

    function viewMasterData(id) {
        const master = window.masterData.find(m => m.id === id);
        if (master && typeof openViewMasterDataModal === 'function') {
            openViewMasterDataModal(master);
        } else {
            showToast('View Master Data: ' + master.value, 'info');
        }
    }

    function editMasterData(id) {
        const master = window.masterData.find(m => m.id === id);
        if (master && typeof openEditMasterDataModal === 'function') {
            openEditMasterDataModal(master);
        } else {
            showToast('Edit Master Data: ' + master.value, 'info');
        }
    }

    (function() {
        document.querySelectorAll('.nav-item').forEach(item => { item.classList.remove('active'); if (item.getAttribute('data-title') === 'Administration') item.classList.add('active'); });
        
        // Load all data
        loadUsers();
        loadCompanies();
        loadDepartments();
        loadPositions();
        loadMasterData();
        
        document.getElementById('userSearchInput').addEventListener('keyup', applyUserFilters);
        document.getElementById('roleFilter').addEventListener('change', applyUserFilters);
        document.getElementById('userStatusFilter').addEventListener('change', applyUserFilters);
        document.getElementById('masterSearchInput').addEventListener('keyup', applyMasterFilters);
        document.getElementById('dataTypeFilter').addEventListener('change', applyMasterFilters);
        document.getElementById('activeFilter').addEventListener('change', applyMasterFilters);
        
        // Add event listeners for organizational structure filters (will be attached when sub-tabs are shown)
        setTimeout(() => {
            const companySearch = document.getElementById('companySearchInput');
            const deptSearch = document.getElementById('departmentSearchInput');
            const posSearch = document.getElementById('positionSearchInput');
            
            if (companySearch) companySearch.addEventListener('keyup', applyCompanyFilters);
            if (deptSearch) deptSearch.addEventListener('keyup', applyDepartmentFilters);
            if (posSearch) posSearch.addEventListener('keyup', applyPositionFilters);
        }, 100);
        
        // Check if we need to navigate to a specific tab/level from employee page
        const navigateToTab = sessionStorage.getItem('navigateToTab');
        const navigateToCompany = sessionStorage.getItem('navigateToCompany');
        const navigateToDepartment = sessionStorage.getItem('navigateToDepartment');
        
        if (navigateToTab === 'org') {
            // Clear the session storage
            sessionStorage.removeItem('navigateToTab');
            
            // Wait for data to load, then navigate
            setTimeout(() => {
                // Switch to master data tab
                switchTab('master');
                
                if (navigateToDepartment) {
                    // Navigate to departments sub-tab
                    currentMasterSubTab = 'departments';
                    switchMasterSubTab('departments');
                    
                    // Optionally open edit modal for the department
                    setTimeout(() => {
                        const dept = window.departments.find(d => d.name === navigateToDepartment);
                        if (dept && typeof editDepartment === 'function') {
                            editDepartment(dept.id);
                        }
                    }, 300);
                    
                    sessionStorage.removeItem('navigateToCompany');
                    sessionStorage.removeItem('navigateToDepartment');
                } else if (navigateToCompany) {
                    // Navigate to companies sub-tab
                    currentMasterSubTab = 'companies';
                    switchMasterSubTab('companies');
                    
                    setTimeout(() => {
                        const company = window.companies.find(c => c.name === navigateToCompany);
                        if (company && typeof editCompany === 'function') {
                            editCompany(company.id);
                        }
                    }, 300);
                    sessionStorage.removeItem('navigateToCompany');
                }
            }, 500);
        }
        
        console.log('✅ Administration loaded');
    })();
</script>
</body>
</html>