<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
/**
 * performance.php
 * Performance Management - Employee Disciplinary Tracker with sidebar and modal components
 * Following the attendance.php design pattern
 */

$pageTitle = "Performance Management";
$activeMenu = "Performance & Reports";
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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: radial-gradient(circle at 20% 30%, #eef2ff, #e0e7ff);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            font-size: 13px;
        }

        .app-layout {
            display: flex;
            min-height: 100vh;
            position: relative;
        }

        /* Main content */
        .main-content {
            flex: 1;
            padding: 20px 24px;
            overflow-y: auto;
            max-height: 100vh;
        }

        /* Header */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .page-header h1 {
            font-size: 22px;
            font-weight: 600;
            color: #0f172a;
        }

        .page-header h1 i {
            color: #ef4444;
            margin-right: 8px;
        }

        .header-actions {
            display: flex;
            gap: 10px;
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
            background: #ef4444;
            color: white;
        }

        .btn-primary:hover {
            background: #dc2626;
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
        }

        .btn-warning {
            background: #f59e0b;
            color: white;
        }

        .btn-warning:hover {
            background: #d97706;
        }

        .btn-danger {
            background: #ef4444;
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        /* Tabs */
        .tabs-container {
            display: flex;
            gap: 4px;
            margin-bottom: 20px;
            background: rgba(255,255,255,0.5);
            padding: 4px;
            border-radius: 24px;
            width: fit-content;
            flex-wrap: wrap;
        }

        .tab-btn {
            padding: 8px 20px;
            border-radius: 20px;
            border: none;
            background: transparent;
            font-size: 13px;
            font-weight: 500;
            color: #64748b;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .tab-btn.active {
            background: white;
            color: #ef4444;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .tab-btn:hover:not(.active) {
            color: #1e293b;
        }

        /* Search and filter bar */
        .filter-bar {
            display: flex;
            gap: 12px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .search-box {
            flex: 1;
            min-width: 240px;
            position: relative;
        }

        .search-box i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 13px;
        }

        .search-box input {
            width: 100%;
            padding: 10px 14px 10px 40px;
            border-radius: 24px;
            border: 1px solid #e2e8f0;
            background: rgba(255,255,255,0.9);
            font-size: 13px;
            outline: none;
            transition: all 0.2s;
        }

        .search-box input:focus {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }

        .filter-select {
            padding: 10px 16px;
            border-radius: 24px;
            border: 1px solid #e2e8f0;
            background: rgba(255,255,255,0.9);
            font-size: 13px;
            color: #1e293b;
            cursor: pointer;
            outline: none;
        }

        /* Stats summary */
        .stats-mini {
            display: flex;
            gap: 16px;
            margin-bottom: 20px;
            flex-wrap: wrap;
            align-items: center;
        }

        .stat-mini-card {
            background: rgba(255,255,255,0.9);
            padding: 10px 18px;
            border-radius: 20px;
            border: 1px solid rgba(255,255,255,0.6);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .stat-mini-card i {
            color: #ef4444;
            font-size: 16px;
        }

        .stat-mini-card span {
            font-weight: 600;
            color: #0f172a;
        }

        .stat-mini-card small {
            color: #64748b;
            margin-left: 6px;
        }

        /* Table card */
        .table-card {
            background: rgba(255,255,255,0.85);
            backdrop-filter: blur(8px);
            border-radius: 24px;
            padding: 20px;
            box-shadow: 0 8px 20px -8px rgba(0,0,0,0.05);
            border: 1px solid rgba(255,255,255,0.7);
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .table-header h3 {
            font-size: 16px;
            font-weight: 600;
            color: #0f172a;
        }

        .offense-table {
            width: 100%;
            border-collapse: collapse;
        }

        .offense-table th {
            text-align: left;
            padding: 12px 8px;
            font-weight: 600;
            color: #475569;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #e2e8f0;
        }

        .offense-table td {
            padding: 12px 8px;
            border-bottom: 1px solid #f1f5f9;
            color: #1e293b;
            font-size: 13px;
        }

        .offense-table tbody tr:hover {
            background: rgba(239, 68, 68, 0.03);
        }

        .badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 500;
            display: inline-block;
        }

        .badge-success {
            background: #dcfce7;
            color: #16a34a;
        }

        .badge-warning {
            background: #fef3c7;
            color: #b45309;
        }

        .badge-info {
            background: #dbeafe;
            color: #2563eb;
        }

        .badge-danger {
            background: #fee2e2;
            color: #dc2626;
        }

        .badge-secondary {
            background: #f1f5f9;
            color: #64748b;
        }

        .badge-purple {
            background: #f3e8ff;
            color: #9333ea;
        }

        .action-icons i {
            color: #94a3b8;
            margin: 0 4px;
            cursor: pointer;
            transition: color 0.2s;
            font-size: 14px;
        }

        .action-icons i:hover {
            color: #ef4444;
        }

        /* Employee cell */
        .employee-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .employee-avatar {
            width: 36px;
            height: 36px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 13px;
            color: white;
        }

        .employee-info h4 {
            font-weight: 600;
            font-size: 13px;
            margin-bottom: 2px;
        }

        .employee-info p {
            font-size: 11px;
            color: #64748b;
        }

        /* Tab content */
        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
        }

        .pagination-info {
            color: #64748b;
            font-size: 12px;
        }

        .pagination-controls {
            display: flex;
            gap: 6px;
        }

        .page-btn {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background: white;
            border: 1px solid #e2e8f0;
            color: #1e293b;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 12px;
        }

        .page-btn:hover {
            background: #ef4444;
            color: white;
            border-color: #ef4444;
        }

        .page-btn.active {
            background: #ef4444;
            color: white;
            border-color: #ef4444;
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 14px;
            }
            .offense-table {
                display: block;
                overflow-x: auto;
            }
            .stats-mini {
                gap: 8px;
            }
        }
    </style>
</head>
<body>
<div class="app-layout">
    
    <?php 
    // Include the sidebar component
    include 'sidebar.php'; 
    ?>

    <!-- MAIN CONTENT - PERFORMANCE MANAGEMENT -->
    <main class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <h1><i class="fas fa-balance-scale"></i> Performance Management</h1>
        </div>

        <!-- Tabs -->
        <div class="tabs-container">
            <button class="tab-btn active" onclick="switchTab('offenses')"><i class="fas fa-gavel"></i> Offense Records</button>
            <button class="tab-btn" onclick="switchTab('reports')"><i class="fas fa-file-alt"></i> Disciplinary Reports</button>
        </div>

        <!-- Offense Records Tab -->
        <div id="offensesTab" class="tab-content active">
            <!-- Filter Bar -->
            <div class="filter-bar">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search by employee, department, offense type..." id="offenseSearchInput">
                </div>
                <select class="filter-select" id="severityFilter">
                    <option value="">All Severities</option>
                    <option value="Minor">Minor</option>
                    <option value="Moderate">Moderate</option>
                    <option value="Major">Major</option>
                    <option value="Critical">Critical</option>
                </select>
                <select class="filter-select" id="statusFilter">
                    <option value="">All Statuses</option>
                    <option value="Pending Review">Pending Review</option>
                    <option value="Under Investigation">Under Investigation</option>
                    <option value="Action Taken">Action Taken</option>
                    <option value="Closed">Closed</option>
                </select>
                <select class="filter-select" id="deptFilter">
                    <option value="">All Departments</option>
                    <option value="Engineering">Engineering</option>
                    <option value="Product">Product</option>
                    <option value="Marketing">Marketing</option>
                    <option value="Sales">Sales</option>
                    <option value="HR">HR</option>
                    <option value="Finance">Finance</option>
                </select>
            </div>

            <!-- Stats -->
            <div class="stats-mini">
                <div class="stat-mini-card">
                    <i class="fas fa-users"></i>
                    <span id="totalEmployeesCount">0</span> <small>Employees with offenses</small>
                </div>
                <div class="stat-mini-card">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span id="totalOffensesCount">0</span> <small>Total offenses</small>
                </div>
                <div class="stat-mini-card">
                    <i class="fas fa-hourglass-half"></i>
                    <span id="pendingCount">0</span> <small>Pending / Investigation</small>
                </div>
                <div class="stat-mini-card">
                    <i class="fas fa-calendar-week"></i>
                    <span id="monthCount">0</span> <small>This month</small>
                </div>
                
                <div class="header-actions">
                    <button class="btn btn-primary" onclick="openAddOffenseModal()"><i class="fas fa-plus"></i> Record Offense</button>
                    <button class="btn btn-secondary" onclick="exportOffenseReport()"><i class="fas fa-file-excel"></i> Export CSV</button>
                </div>
            </div>

            <!-- Offense Table -->
            <div class="table-card">
                <div class="table-header">
                    <h3><i class="fas fa-clipboard-list"></i> Offense Registry</h3>
                </div>
                <table class="offense-table">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Department</th>
                            <th>Offense Type</th>
                            <th>Severity</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Reported By</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="offenseTableBody">
                        <!-- Offense rows will be populated here -->
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="pagination">
                    <div class="pagination-info" id="offensePaginationInfo">
                        Showing 0 of 0 offenses
                    </div>
                    <div class="pagination-controls" id="offensePaginationControls">
                        <!-- Pagination buttons -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Disciplinary Reports Tab -->
        <div id="reportsTab" class="tab-content">
            <div class="table-card">
                <div class="table-header">
                    <h3><i class="fas fa-file-alt"></i> Disciplinary Reports</h3>
                </div>
                <p style="text-align: center; padding: 40px; color: #64748b;">
                    <i class="fas fa-file-contract" style="font-size: 48px; margin-bottom: 16px; display: block;"></i>
                    Disciplinary reports generation coming soon...
                </p>
            </div>
        </div>
    </main>
</div>

<!-- Include Modal Components -->
<?php include 'modals/modal-wrapper.php'; ?>
<?php include 'modals/performance-modal/modal-add-offense.php'; ?>
<?php include 'modals/performance-modal/modal-edit-offense.php'; ?>
<?php include 'modals/performance-modal/modal-view-offense.php'; ?>
<?php include 'modals/performance-modal/modal-offense-helpers.php'; ?>

<script>
    // ==================== DATABASE & API SYNC ====================
    window.offenses = [];
    window.offensesArray = [];
    window.employees = [];

    // ==================== STATE VARIABLES ====================
    let currentOffensePage = 1;
    let itemsPerPage = 8;
    let filteredOffenses = [];

    // ==================== API ACTIONS ====================
    function loadOffenses() {
        fetch('/3ME/api/performance/offenses.php')
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.offenses = data.offenses;
                    window.offensesArray = window.offenses;
                    
                    // Apply filters to reflect search & filter UI
                    applyOffenseFilters();
                    
                    // Update stats
                    document.getElementById('totalEmployeesCount').innerText = data.stats.totalEmployeesCount;
                    document.getElementById('totalOffensesCount').innerText = data.stats.totalOffensesCount;
                    document.getElementById('pendingCount').innerText = data.stats.pendingCount;
                    document.getElementById('monthCount').innerText = data.stats.monthCount;
                }
            })
            .catch(err => {
                console.error(err);
                showToast("Failed to load offenses from database", "error");
            });
    }

    function loadEmployees() {
        fetch('/3ME/api/employees/employees.php')
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.employees = data.data;
                    if (typeof populateEmployeeDropdown === 'function') {
                        populateEmployeeDropdown();
                    }
                }
            })
            .catch(err => {
                console.error(err);
            });
    }

    // Expose loaders globally
    window.loadOffenses = loadOffenses;
    window.loadEmployees = loadEmployees;

    // ==================== HELPER FUNCTIONS ====================
    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }

    function getSeverityClass(sev) {
        const map = { 'Critical': 'badge-danger', 'Major': 'badge-warning', 'Moderate': 'badge-info', 'Minor': 'badge-secondary' };
        return map[sev] || 'badge-secondary';
    }

    function getStatusClass(st) {
        const map = { 'Pending Review': 'badge-warning', 'Under Investigation': 'badge-purple', 'Action Taken': 'badge-info', 'Closed': 'badge-secondary' };
        return map[st] || 'badge-secondary';
    }

    function formatDate(dateStr) {
        if (!dateStr) return '';
        const d = new Date(dateStr);
        return d.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
    }

    function updateStats() {
        const uniqueEmps = new Set(window.offenses.map(o => o.employeeName)).size;
        document.getElementById('totalEmployeesCount').innerText = uniqueEmps;
        document.getElementById('totalOffensesCount').innerText = window.offenses.length;
        const pendingCount = window.offenses.filter(o => o.status === 'Pending Review' || o.status === 'Under Investigation').length;
        document.getElementById('pendingCount').innerText = pendingCount;
        const now = new Date();
        const currentMonth = now.getMonth(), currentYear = now.getFullYear();
        const monthOff = window.offenses.filter(o => { const d = new Date(o.date); return d.getMonth() === currentMonth && d.getFullYear() === currentYear; }).length;
        document.getElementById('monthCount').innerText = monthOff;
    }

    // ==================== RENDER FUNCTIONS ====================
    
    function renderOffenseTable(data) {
        filteredOffenses = data;
        updateStats();
        
        const start = (currentOffensePage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const paginatedData = filteredOffenses.slice(start, end);
        
        const tbody = document.getElementById('offenseTableBody');
        tbody.innerHTML = paginatedData.map((offense) => {
            const avatarLetter = offense.avatar || offense.employeeName.charAt(0).toUpperCase();
            const bgColor = offense.color || '#64748b';
            return `
                <tr>
                    <td>
                        <div class="employee-cell">
                            <div class="employee-avatar" style="background:${bgColor};">${escapeHtml(avatarLetter)}</div>
                            <div class="employee-info">
                                <h4>${escapeHtml(offense.employeeName)}</h4>
                                <p>${escapeHtml(offense.employeeEmail || '')}</p>
                            </div>
                        </div>
                    </td>
                    <td>${escapeHtml(offense.department)}</td>
                    <td>${escapeHtml(offense.offenseType)}</td>
                    <td><span class="badge ${getSeverityClass(offense.severity)}">${offense.severity}</span></td>
                    <td>${formatDate(offense.date)}</td>
                    <td><span class="badge ${getStatusClass(offense.status)}">${offense.status}</span></td>
                    <td>${escapeHtml(offense.reportedBy)}</td>
                    <td class="action-icons">
                        <i class="fas fa-eye" onclick="viewOffenseDetails('${offense.id}')" title="View"></i>
                        <i class="fas fa-edit" onclick="editOffenseById('${offense.id}')" title="Edit"></i>
                        <i class="fas fa-trash-alt" onclick="deleteOffense('${offense.id}')" title="Delete"></i>
                    </td>
                </tr>
            `;
        }).join('');
        
        const totalPages = Math.ceil(filteredOffenses.length / itemsPerPage);
        document.getElementById('offensePaginationInfo').textContent = `Showing ${start + 1}-${Math.min(end, filteredOffenses.length)} of ${filteredOffenses.length} offenses`;
        
        const paginationContainer = document.getElementById('offensePaginationControls');
        let paginationHtml = '';
        paginationHtml += `<div class="page-btn" onclick="changeOffensePage(${currentOffensePage - 1})" ${currentOffensePage === 1 ? 'style="opacity:0.5; pointer-events:none;"' : ''}><i class="fas fa-chevron-left"></i></div>`;
        for (let i = 1; i <= Math.min(totalPages, 5); i++) {
            paginationHtml += `<div class="page-btn ${currentOffensePage === i ? 'active' : ''}" onclick="changeOffensePage(${i})">${i}</div>`;
        }
        if (totalPages > 5) {
            paginationHtml += `<div class="page-btn">...</div>`;
        }
        paginationHtml += `<div class="page-btn" onclick="changeOffensePage(${currentOffensePage + 1})" ${currentOffensePage === totalPages || totalPages === 0 ? 'style="opacity:0.5; pointer-events:none;"' : ''}><i class="fas fa-chevron-right"></i></div>`;
        paginationContainer.innerHTML = paginationHtml;
    }

    // ==================== FILTER FUNCTIONS ====================
    
    function applyOffenseFilters() {
        const searchTerm = document.getElementById('offenseSearchInput').value.toLowerCase();
        const severityValue = document.getElementById('severityFilter').value;
        const statusValue = document.getElementById('statusFilter').value;
        const deptValue = document.getElementById('deptFilter').value;
        
        let filtered = window.offenses.filter(offense => {
            const matchesSearch = offense.employeeName.toLowerCase().includes(searchTerm) ||
                                 offense.department.toLowerCase().includes(searchTerm) ||
                                 offense.offenseType.toLowerCase().includes(searchTerm) ||
                                 (offense.employeeEmail && offense.employeeEmail.toLowerCase().includes(searchTerm));
            const matchesSeverity = !severityValue || offense.severity === severityValue;
            const matchesStatus = !statusValue || offense.status === statusValue;
            const matchesDept = !deptValue || offense.department === deptValue;
            return matchesSearch && matchesSeverity && matchesStatus && matchesDept;
        });
        
        currentOffensePage = 1;
        renderOffenseTable(filtered);
    }

    // ==================== PAGINATION ====================
    
    function changeOffensePage(page) {
        const totalPages = Math.ceil(filteredOffenses.length / itemsPerPage);
        if (page < 1 || page > totalPages) return;
        currentOffensePage = page;
        renderOffenseTable(filteredOffenses);
    }

    // ==================== TAB SWITCHING ====================
    
    function switchTab(tab) {
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
        
        if (tab === 'offenses') {
            document.querySelectorAll('.tab-btn')[0].classList.add('active');
            document.getElementById('offensesTab').classList.add('active');
            renderOffenseTable(filteredOffenses);
        } else {
            document.querySelectorAll('.tab-btn')[1].classList.add('active');
            document.getElementById('reportsTab').classList.add('active');
        }
    }

    // ==================== ACTION FUNCTIONS ====================
    
    function viewOffenseDetails(id) {
        // This function is defined in modal-view-offense.php
        if (typeof viewOffense !== 'undefined') {
            viewOffense(id);
        } else {
            showToast('View offense modal not loaded', 'error');
        }
    }

    function editOffenseById(id) {
        // This function is defined in modal-edit-offense.php
        if (typeof editOffense !== 'undefined') {
            editOffense(id);
        } else {
            showToast('Edit offense modal not loaded', 'error');
        }
    }

    function deleteOffense(id) {
        if (confirm("Permanently delete this offense record?")) {
            fetch(`/3ME/api/performance/offenses.php?id=${id}`, {
                method: 'DELETE'
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast("Offense removed successfully!", "success");
                    loadOffenses();
                } else {
                    showToast(data.message || "Error deleting offense", "error");
                }
            })
            .catch(err => {
                console.error(err);
                showToast("System error deleting offense", "error");
            });
        }
    }

    function exportOffenseReport() {
        let csvRows = [["Employee", "Email", "Department", "Offense Type", "Severity", "Date", "Status", "Reported By"]];
        window.offenses.forEach(o => {
            csvRows.push([o.employeeName, o.employeeEmail, o.department, o.offenseType, o.severity, o.date, o.status, o.reportedBy]);
        });
        const csvContent = csvRows.map(row => row.map(cell => `"${String(cell).replace(/"/g, '""')}"`).join(",")).join("\n");
        const blob = new Blob([csvContent], { type: "text/csv" });
        const link = document.createElement("a");
        link.href = URL.createObjectURL(blob);
        link.download = "offense_report.csv";
        link.click();
        URL.revokeObjectURL(link.href);
        showToast("Exported offenses CSV", "success");
    }

    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.style.cssText = `position: fixed; bottom: 24px; right: 24px; background: ${type === 'success' ? '#10b981' : type === 'warning' ? '#f59e0b' : type === 'error' ? '#ef4444' : '#1e293b'}; color: white; padding: 12px 20px; border-radius: 12px; font-size: 13px; z-index: 10000; animation: slideIn 0.3s ease; box-shadow: 0 4px 12px rgba(0,0,0,0.15);`;
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => { toast.style.opacity = '0'; toast.style.transition = 'opacity 0.3s'; setTimeout(() => toast.remove(), 300); }, 3000);
    }

    // Add animation style
    const style = document.createElement('style');
    style.textContent = `@keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }`;
    document.head.appendChild(style);

    // Make functions globally available
    window.switchTab = switchTab;
    window.changeOffensePage = changeOffensePage;
    window.viewOffenseDetails = viewOffenseDetails;
    window.editOffenseById = editOffenseById;
    window.deleteOffense = deleteOffense;
    window.exportOffenseReport = exportOffenseReport;

    // Initialize
    (function() {
        // Set active menu
        const navItems = document.querySelectorAll('.nav-item');
        navItems.forEach(item => {
            item.classList.remove('active');
            if (item.getAttribute('data-title') === 'Performance & Reports') {
                item.classList.add('active');
            }
        });

        // Load live dynamic data from Database
        loadEmployees();
        loadOffenses();

        // Setup filter listeners
        document.getElementById('offenseSearchInput').addEventListener('keyup', applyOffenseFilters);
        document.getElementById('severityFilter').addEventListener('change', applyOffenseFilters);
        document.getElementById('statusFilter').addEventListener('change', applyOffenseFilters);
        document.getElementById('deptFilter').addEventListener('change', applyOffenseFilters);

        console.log('✅ Performance Management loaded');
    })();
</script>

</body>
</html>