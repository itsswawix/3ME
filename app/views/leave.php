<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
/**
 * leave.php
 * Leave and Absence Management - Clean interface with sidebar and modal components
 * Updated with drill-down navigation for employee leave history
 */

$pageTitle = "Leave Management";
$activeMenu = "Leave";
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
            color: #4f46e5;
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

        .btn-info {
            background: #0ea5e9;
            color: white;
        }

        .btn-info:hover {
            background: #0284c7;
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
            color: #4f46e5;
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
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
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
            color: #4f46e5;
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
        .table-card h3 {
            font-size: 16px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
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

        .breadcrumb-link-header {
            color: #4f46e5;
            cursor: pointer;
            font-weight: 500;
        }

        .breadcrumb-link-header:hover {
            color: #4338ca;
            text-decoration: underline;
        }

        .breadcrumb-separator-header {
            color: #cbd5e1;
            font-size: 10px;
            margin: 0 4px;
        }

        .breadcrumb-current-header {
            color: #0f172a;
            font-weight: 600;
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            color: #475569;
            font-size: 12px;
            cursor: pointer;
            margin-bottom: 16px;
            transition: all 0.2s;
        }

        .back-button:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }

        .leave-type-table, .leave-request-table, .balance-table, .history-table {
            width: 100%;
            border-collapse: collapse;
        }

        .leave-type-table th, .leave-request-table th, .balance-table th, .history-table th {
            text-align: left;
            padding: 12px 8px;
            font-weight: 600;
            color: #475569;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #e2e8f0;
        }

        .leave-type-table td, .leave-request-table td, .balance-table td, .history-table td {
            padding: 12px 8px;
            border-bottom: 1px solid #f1f5f9;
            color: #1e293b;
            font-size: 13px;
        }

        .leave-type-table tbody tr:hover, .leave-request-table tbody tr:hover, .balance-table tbody tr:hover, .history-table tbody tr:hover {
            background: rgba(79, 70, 229, 0.03);
        }

        .clickable-row {
            cursor: pointer;
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
            color: #4f46e5;
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

        /* Employee summary card */
        .employee-summary-card {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 20px;
            background: #f8fafc;
            border-radius: 20px;
            margin-bottom: 24px;
            border: 1px solid #e2e8f0;
        }

        .employee-avatar-lg {
            width: 64px;
            height: 64px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 24px;
            color: white;
        }

        .employee-details-lg h3 {
            font-size: 18px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 4px;
        }

        .employee-details-lg p {
            color: #64748b;
            font-size: 13px;
        }

        .employee-stats {
            margin-left: auto;
            display: flex;
            gap: 24px;
        }

        .employee-stat {
            text-align: center;
        }

        .employee-stat .label {
            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .employee-stat .value {
            font-size: 24px;
            font-weight: 700;
            color: #0f172a;
        }

        /* Leave type icon */
        .leave-type-icon {
            width: 36px;
            height: 36px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .leave-type-info h4 {
            font-weight: 600;
            font-size: 13px;
            margin-bottom: 2px;
        }

        .leave-type-info p {
            font-size: 11px;
            color: #64748b;
        }

        /* Balance bar */
        .balance-bar-container {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .balance-bar {
            width: 100px;
            height: 6px;
            background: #e2e8f0;
            border-radius: 3px;
            overflow: hidden;
        }

        .balance-fill {
            height: 100%;
            background: #4f46e5;
            border-radius: 3px;
        }

        .balance-fill.warning {
            background: #f59e0b;
        }

        .balance-fill.danger {
            background: #ef4444;
        }

        .balance-fill.success {
            background: #10b981;
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
            background: #4f46e5;
            color: white;
            border-color: #4f46e5;
        }

        .page-btn.active {
            background: #4f46e5;
            color: white;
            border-color: #4f46e5;
        }

        .row-number {
            color: #94a3b8;
            font-weight: 500;
            width: 30px;
        }

        /* Credits display */
        .credits-display {
            font-weight: 600;
            color: #0f172a;
        }

        .credits-label {
            color: #64748b;
            font-size: 11px;
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 14px;
            }
            .leave-type-table, .leave-request-table, .balance-table, .history-table {
                display: block;
                overflow-x: auto;
            }
            .employee-summary-card {
                flex-wrap: wrap;
            }
            .employee-stats {
                margin-left: 0;
                width: 100%;
                justify-content: space-around;
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

    <!-- MAIN CONTENT - LEAVE MANAGEMENT -->
    <main class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <h1><i class="fas fa-calendar-alt"></i> Leave & Absence Management</h1>
        </div>

        <!-- Tabs -->
        <div class="tabs-container">
            <button class="tab-btn active" onclick="switchTab('requests')"><i class="fas fa-clipboard-list"></i> Leave Requests</button>
            <button class="tab-btn" onclick="switchTab('balances')"><i class="fas fa-balance-scale"></i> Leave Balances</button>
            <button class="tab-btn" onclick="switchTab('types')"><i class="fas fa-cog"></i> Leave Types & Policies</button>
        </div>

        <!-- Leave Requests Tab -->
        <div id="requestsTab" class="tab-content active">
            <div id="requestsContent">
                <!-- Content will be populated dynamically -->
            </div>
        </div>

        <!-- Leave Balances Tab -->
        <div id="balancesTab" class="tab-content">
            <div id="balancesLevelContent">
                <!-- Content will be populated dynamically -->
            </div>
        </div>

        <!-- Leave Types & Policies Tab -->
        <div id="typesTab" class="tab-content">
            <!-- Filter Bar -->
            <div class="filter-bar">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search by leave name..." id="typeSearchInput">
                </div>
            </div>

            <!-- Stats -->
            <div class="stats-mini">
                <div class="stat-mini-card">
                    <i class="fas fa-tags"></i>
                    <span id="totalLeaveTypes">0</span> <small>Leave Types</small>
                </div>
                <div class="stat-mini-card">
                    <i class="fas fa-check-circle"></i>
                    <span id="activePolicies">0</span> <small>Active Policies</small>
                </div>
                <div class="header-actions">
                    <button class="btn btn-primary" onclick="openAddLeaveTypeModal()"><i class="fas fa-plus"></i> New Leave Type</button>
                </div>
            </div>

            <!-- Leave Types Table -->
            <div class="table-card">
                <h3><i class="fas fa-tags"></i> Leave Types & Policies</h3>
                <table class="leave-type-table">
                    <thead>
                        <tr>
                            <th>Leave Type</th>
                            <th>Default Credits</th>
                            <th>Max Duration</th>
                            <th>Eligibility Rule</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="typeTableBody">
                        <!-- Type rows will be populated here -->
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="pagination">
                    <div class="pagination-info" id="typePaginationInfo">
                        Showing 0 of 0 leave types
                    </div>
                    <div class="pagination-controls" id="typePaginationControls">
                        <!-- Pagination buttons -->
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Include Modal Components -->
<?php include 'modals/modal-wrapper.php'; ?>
<?php include 'modals/leave-modal/modal-wrapper.php'; ?>

<script>
    // Initialize empty data arrays
    window.leaveTypes = [];
    window.leaveRequests = [];
    window.leaveBalances = [];
    window.employeeLeaveHistory = {};

    // Load leave data dynamically from database APIs
    async function loadLeaveDataFromDB() {
        try {
            // Fetch Leave Types
            const typesRes = await fetch('../../api/leave/types.php');
            const typesData = await typesRes.json();
            if (typesData.success) {
                window.leaveTypes = typesData.data || [];
            }
            
            // Fetch Leave Requests
            const reqsRes = await fetch('../../api/leave/requests.php');
            const reqsData = await reqsRes.json();
            if (reqsData.success) {
                window.leaveRequests = reqsData.data || [];
                
                // Group requests by employee to form employeeLeaveHistory
                window.employeeLeaveHistory = {};
                window.leaveRequests.forEach(req => {
                    if (!window.employeeLeaveHistory[req.employeeId]) {
                        window.employeeLeaveHistory[req.employeeId] = [];
                    }
                    window.employeeLeaveHistory[req.employeeId].push({
                        id: req.id,
                        employeeId: req.employeeId,
                        leaveType: req.leaveType,
                        startDate: req.startDate,
                        endDate: req.endDate,
                        days: req.duration,
                        status: req.status,
                        approvedBy: req.approvedBy,
                        requestDate: req.requestDate
                    });
                });
            }
            
            // Fetch Leave Balances
            const balRes = await fetch('../../api/leave/balances.php');
            const balData = await balRes.json();
            if (balData.success) {
                window.leaveBalances = balData.data || [];
            }
            
            // Reset filters to reflect new data
            filteredRequests = [...window.leaveRequests];
            filteredBalances = [...window.leaveBalances];
            filteredTypes = [...window.leaveTypes];
            
            // Render UI
            renderRequestsLevel();
            if (currentBalancesLevel === 'balances') {
                renderBalancesLevel();
            } else {
                renderEmployeeHistoryLevel();
            }
            renderLeaveTypeTable(window.leaveTypes);
            
        } catch (error) {
            console.error('Error loading leave data:', error);
            showToast('Failed to load leave data from database', 'warning');
        }
    }

    // Generate history for employees
    function getEmployeeLeaveHistory(employeeId) {
        if (window.employeeLeaveHistory[employeeId]) {
            return window.employeeLeaveHistory[employeeId];
        }
        return [];
    }

    // State variables
    let currentBalancesLevel = 'balances';
    let selectedEmployeeId = null;
    let selectedEmployeeName = '';
    let selectedEmployeeData = null;
    let currentHistoryPage = 1;
    let historyItemsPerPage = 8;

    let currentRequestPage = 1;
    let currentBalancePage = 1;
    let currentTypePage = 1;
    let itemsPerPage = 8;
    let filteredRequests = [...window.leaveRequests];
    let filteredBalances = [...window.leaveBalances];
    let filteredTypes = [...window.leaveTypes];

    // Update stats
    function updateRequestStats() {
        const total = window.leaveRequests.length;
        const pending = window.leaveRequests.filter(r => r.status === 'Pending').length;
        const approved = window.leaveRequests.filter(r => r.status === 'Approved').length;
        const rejected = window.leaveRequests.filter(r => r.status === 'Rejected').length;
        
        // Calculate on leave today dynamically
        const todayStr = new Date().toISOString().split('T')[0];
        const onLeaveToday = window.leaveRequests.filter(r => {
            if (r.status !== 'Approved') return false;
            try {
                const start = new Date(r.startDateRaw || r.startDate);
                const end = new Date(r.endDateRaw || r.endDate);
                const today = new Date(todayStr);
                return today >= start && today <= end;
            } catch(e) {
                return false;
            }
        }).length;
        
        document.getElementById('totalRequests').innerText = total;
        document.getElementById('pendingRequests').innerText = pending;
        document.getElementById('approvedRequests').innerText = approved;
        document.getElementById('rejectedRequests').innerText = rejected;
        document.getElementById('onLeaveToday').innerText = onLeaveToday;
    }

    function updateBalanceStats() {
        const uniqueEmployees = [...new Set(window.leaveBalances.map(b => b.employeeId))];
        const totalAccrued = window.leaveBalances.reduce((sum, b) => sum + b.accrued, 0);
        const totalUsed = window.leaveBalances.reduce((sum, b) => sum + b.used, 0);
        const totalBalance = window.leaveBalances.reduce((sum, b) => sum + b.balance, 0);
        
        document.getElementById('totalEmployees').innerText = uniqueEmployees.length;
        document.getElementById('totalAccrued').innerText = totalAccrued.toFixed(0);
        document.getElementById('totalUsed').innerText = totalUsed.toFixed(0);
        document.getElementById('totalBalance').innerText = totalBalance.toFixed(0);
    }

    function updateTypeStats() {
        document.getElementById('totalLeaveTypes').innerText = window.leaveTypes.length;
        document.getElementById('activePolicies').innerText = window.leaveTypes.length;
    }

    // Navigation functions
    function navigateToBalancesLevel(level) {
        if (level === 'balances') {
            currentBalancesLevel = 'balances';
            selectedEmployeeId = null;
            selectedEmployeeName = '';
            selectedEmployeeData = null;
            renderBalancesLevel();
        }
    }

    function navigateToEmployeeHistory(employeeId, employeeName, employeeData) {
        currentBalancesLevel = 'history';
        selectedEmployeeId = employeeId;
        selectedEmployeeName = employeeName;
        selectedEmployeeData = employeeData;
        currentHistoryPage = 1;
        renderEmployeeHistoryLevel();
    }

    // Render Requests Level
    function renderRequestsLevel() {
        const container = document.getElementById('requestsContent');
        
        let html = `
            <!-- Filter Bar -->
            <div class="filter-bar">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search by employee name..." id="requestSearchInput" value="">
                </div>
                <select class="filter-select" id="requestStatusFilter">
                    <option value="">All Status</option>
                    <option value="Pending">Pending</option>
                    <option value="Approved">Approved</option>
                    <option value="Rejected">Rejected</option>
                    <option value="Cancelled">Cancelled</option>
                </select>
                <select class="filter-select" id="leaveTypeFilter">
                    <option value="">All Leave Types</option>
                    ${window.leaveTypes.map(t => `<option value="${escapeHtml(t.name)}">${escapeHtml(t.name)}</option>`).join('')}
                </select>
            </div>

            <!-- Stats -->
            <div class="stats-mini">
                <div class="stat-mini-card">
                    <i class="fas fa-calendar"></i>
                    <span id="totalRequests">0</span> <small>Total Requests</small>
                </div>
                <div class="stat-mini-card">
                    <i class="fas fa-hourglass-half"></i>
                    <span id="pendingRequests">0</span> <small>Pending</small>
                </div>
                <div class="stat-mini-card">
                    <i class="fas fa-check-circle"></i>
                    <span id="approvedRequests">0</span> <small>Approved</small>
                </div>
                <div class="stat-mini-card">
                    <i class="fas fa-times-circle"></i>
                    <span id="rejectedRequests">0</span> <small>Rejected</small>
                </div>
                <div class="stat-mini-card">
                    <i class="fas fa-users"></i>
                    <span id="onLeaveToday">0</span> <small>On Leave Today</small>
                </div>
                <div class="header-actions">
                    <button class="btn btn-primary" onclick="openAddLeaveRequestModal()"><i class="fas fa-plus"></i> Request Leave</button>
                </div>
            </div>

            <!-- Leave Requests Table -->
            <div class="table-card">
                <h3><i class="fas fa-list-ul"></i> Leave Requests & Approvals</h3>
                <table class="leave-request-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Employee</th>
                            <th>Leave Type</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th>Approved By</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="requestTableBody">
                        <!-- Request rows will be populated here -->
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="pagination">
                    <div class="pagination-info" id="requestPaginationInfo">
                        Showing 0 of 0 requests
                    </div>
                    <div class="pagination-controls" id="requestPaginationControls">
                        <!-- Pagination buttons -->
                    </div>
                </div>
            </div>
        `;
        
        container.innerHTML = html;
        renderLeaveRequestTable(filteredRequests);
        
        document.getElementById('requestSearchInput').addEventListener('keyup', applyRequestFilters);
        document.getElementById('requestStatusFilter').addEventListener('change', applyRequestFilters);
        document.getElementById('leaveTypeFilter').addEventListener('change', applyRequestFilters);
    }

    // Render Balances Level (Main Balances View)
    function renderBalancesLevel() {
        const container = document.getElementById('balancesLevelContent');
        
        let html = `
            <!-- Filter Bar -->
            <div class="filter-bar">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search by employee name..." id="balanceSearchInput" value="">
                </div>
                <select class="filter-select" id="balanceDepartmentFilter">
                    <option value="">All Departments</option>
                    <option value="Engineering">Engineering</option>
                    <option value="Product">Product</option>
                    <option value="Marketing">Marketing</option>
                    <option value="Sales">Sales</option>
                    <option value="HR">Human Resources</option>
                    <option value="Finance">Finance</option>
                </select>
            </div>

            <!-- Stats -->
            <div class="stats-mini">
                <div class="stat-mini-card">
                    <i class="fas fa-users"></i>
                    <span id="totalEmployees">0</span> <small>Employees</small>
                </div>
                <div class="stat-mini-card">
                    <i class="fas fa-calendar-plus"></i>
                    <span id="totalAccrued">0</span> <small>Total Accrued (Days)</small>
                </div>
                <div class="stat-mini-card">
                    <i class="fas fa-calendar-minus"></i>
                    <span id="totalUsed">0</span> <small>Total Used (Days)</small>
                </div>
                <div class="stat-mini-card">
                    <i class="fas fa-calendar-check"></i>
                    <span id="totalBalance">0</span> <small>Available Balance</small>
                </div>
            </div>

            <!-- Leave Balances Table -->
            <div class="table-card">
                <h3><i class="fas fa-chart-bar"></i> Leave Accrual & Balances</h3>
                <table class="balance-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Employee</th>
                            <th>Department</th>
                            <th>Accrued</th>
                            <th>Used</th>
                            <th>Balance</th>
                            <th>Last Accrual</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="balanceTableBody">
                        <!-- Balance rows will be populated here -->
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="pagination">
                    <div class="pagination-info" id="balancePaginationInfo">
                        Showing 0 of 0 balances
                    </div>
                    <div class="pagination-controls" id="balancePaginationControls">
                        <!-- Pagination buttons -->
                    </div>
                </div>
            </div>
        `;
        
        container.innerHTML = html;
        renderBalanceTable(filteredBalances);
        
        document.getElementById('balanceSearchInput').addEventListener('keyup', applyBalanceFilters);
        document.getElementById('balanceDepartmentFilter').addEventListener('change', applyBalanceFilters);
    }

    // Render Employee History Level (Drill-down view)
    function renderEmployeeHistoryLevel() {
        const container = document.getElementById('balancesLevelContent');
        const historyData = getEmployeeLeaveHistory(selectedEmployeeId);
        const employeeBalances = window.leaveBalances.filter(b => b.employeeId === selectedEmployeeId);
        const totalUsed = employeeBalances.reduce((sum, b) => sum + b.used, 0);
        const totalBalance = employeeBalances.reduce((sum, b) => sum + b.balance, 0);
        
        const start = (currentHistoryPage - 1) * historyItemsPerPage;
        const end = start + historyItemsPerPage;
        const paginatedData = historyData.slice(start, end);
        const totalPages = Math.ceil(historyData.length / historyItemsPerPage);
        
        let html = `
            <!-- Back Button -->

            <!-- Employee Summary -->
            <div class="employee-summary-card">
                <div class="employee-avatar-lg" style="background: ${selectedEmployeeData.color};">${selectedEmployeeData.avatar}</div>
                <div class="employee-details-lg">
                    <h3>${escapeHtml(selectedEmployeeName)}</h3>
                    <p>${escapeHtml(selectedEmployeeData.department)} • ${selectedEmployeeId}</p>
                </div>
                <div class="employee-stats">
                    <div class="employee-stat">
                        <div class="label">Total Used</div>
                        <div class="value" style="color: #ef4444;">${totalUsed.toFixed(1)}</div>
                        <small>days</small>
                    </div>
                    <div class="employee-stat">
                        <div class="label">Available</div>
                        <div class="value" style="color: #10b981;">${totalBalance.toFixed(1)}</div>
                        <small>days</small>
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="stats-mini">
                <div class="stat-mini-card">
                    <i class="fas fa-history"></i>
                    <span>${historyData.length}</span> <small>Total Requests</small>
                </div>
                <div class="stat-mini-card">
                    <i class="fas fa-check-circle"></i>
                    <span>${historyData.filter(h => h.status === 'Approved').length}</span> <small>Approved</small>
                </div>
                <div class="stat-mini-card">
                    <i class="fas fa-hourglass-half"></i>
                    <span>${historyData.filter(h => h.status === 'Pending').length}</span> <small>Pending</small>
                </div>
                <div class="header-actions">
                    <button class="btn btn-secondary" onclick="exportEmployeeHistory('${selectedEmployeeId}')"><i class="fas fa-download"></i> Export</button>
                </div>
            </div>

            <!-- History Table -->
            <div class="table-card">
                <div class="table-header">
                    <h3>
                        <span class="breadcrumb-link-header" onclick="navigateToBalancesLevel('balances')">Leave Balances</span>
                        <i class="fas fa-chevron-right breadcrumb-separator-header"></i>
                        <span class="breadcrumb-current-header">${escapeHtml(selectedEmployeeName)}</span>
                    </h3>
                </div>
                <table class="history-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Leave Type</th>
                            <th>Date Range</th>
                            <th>Days</th>
                            <th>Request Date</th>
                            <th>Status</th>
                            <th>Approved By</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="historyTableBody">
                        ${paginatedData.map((history, index) => {
                            const statusClass = {
                                'Pending': 'badge-warning',
                                'Approved': 'badge-success',
                                'Rejected': 'badge-danger',
                                'Cancelled': 'badge-secondary'
                            }[history.status] || 'badge-secondary';
                            
                            return `
                                <tr>
                                    <td class="row-number">${start + index + 1}</td>
                                    <td>
                                        <span class="badge ${history.leaveType === 'Vacation Leave' ? 'badge-success' : 'badge-info'}">${escapeHtml(history.leaveType)}</span>
                                    </td>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 6px;">
                                            <span>${history.startDate}</span>
                                            <i class="fas fa-arrow-right" style="color: #94a3b8; font-size: 10px;"></i>
                                            <span>${history.endDate}</span>
                                        </div>
                                    </td>
                                    <td><strong>${history.days}</strong> day${history.days > 1 ? 's' : ''}</td>
                                    <td>${history.requestDate}</td>
                                    <td><span class="badge ${statusClass}">${history.status}</span></td>
                                    <td>${history.approvedBy || '—'}</td>
                                    <td class="action-icons">
                                        <i class="fas fa-eye" onclick="viewLeaveRequest('${history.id}')" title="View Details"></i>
                                    </td>
                                </tr>
                            `;
                        }).join('')}
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="pagination">
                    <div class="pagination-info" id="historyPaginationInfo">
                        Showing ${start + 1}-${Math.min(end, historyData.length)} of ${historyData.length} requests
                    </div>
                    <div class="pagination-controls" id="historyPaginationControls">
                        ${renderHistoryPagination(currentHistoryPage, totalPages)}
                    </div>
                </div>
            </div>
        `;
        
        container.innerHTML = html;
    }

    function renderHistoryPagination(currentPage, totalPages) {
        let html = '';
        html += `<div class="page-btn" onclick="changeHistoryPage(${currentPage - 1})" ${currentPage === 1 ? 'style="opacity:0.5; pointer-events:none;"' : ''}><i class="fas fa-chevron-left"></i></div>`;
        for (let i = 1; i <= Math.min(totalPages, 5); i++) {
            html += `<div class="page-btn ${currentPage === i ? 'active' : ''}" onclick="changeHistoryPage(${i})">${i}</div>`;
        }
        if (totalPages > 5) html += `<div class="page-btn">...</div>`;
        html += `<div class="page-btn" onclick="changeHistoryPage(${currentPage + 1})" ${currentPage === totalPages || totalPages === 0 ? 'style="opacity:0.5; pointer-events:none;"' : ''}><i class="fas fa-chevron-right"></i></div>`;
        return html;
    }

    function changeHistoryPage(page) {
        const historyData = getEmployeeLeaveHistory(selectedEmployeeId);
        const totalPages = Math.ceil(historyData.length / historyItemsPerPage);
        if (page < 1 || page > totalPages) return;
        currentHistoryPage = page;
        renderEmployeeHistoryLevel();
    }

    // Render leave types table
    function renderLeaveTypeTable(data) {
        filteredTypes = data;
        updateTypeStats();
        
        const start = (currentTypePage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const paginatedData = filteredTypes.slice(start, end);
        
        const tbody = document.getElementById('typeTableBody');
        tbody.innerHTML = paginatedData.map((type) => {
            const iconColor = type.name === 'Vacation Leave' ? '#10b981' : 
                            (type.name === 'Sick Leave' ? '#ef4444' : 
                            (type.name === 'Emergency Leave' ? '#f59e0b' : '#4f46e5'));
            
            return `
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div class="leave-type-icon" style="background: ${iconColor};">
                                <i class="fas fa-${getLeaveIcon(type.name)}"></i>
                            </div>
                            <div class="leave-type-info">
                                <h4>${escapeHtml(type.name)}</h4>
                            </div>
                        </div>
                    </td>
                    <td><span class="credits-display">${type.credits}</span> <span class="credits-label">days/year</span></td>
                    <td>${type.maxDuration} days</td>
                    <td title="${escapeHtml(type.eligibilityRule)}">${escapeHtml(type.eligibilityRule.substring(0, 40))}${type.eligibilityRule.length > 40 ? '...' : ''}</td>
                    <td class="action-icons">
                        <i class="fas fa-eye" onclick="viewLeaveType('${type.id}')" title="View"></i>
                        <i class="fas fa-edit" onclick="editLeaveType('${type.id}')" title="Edit"></i>
                        <i class="fas fa-copy" onclick="duplicateLeaveType('${type.id}')" title="Duplicate"></i>
                    </td>
                </tr>
            `;
        }).join('');
        
        const totalPages = Math.ceil(filteredTypes.length / itemsPerPage);
        document.getElementById('typePaginationInfo').textContent = `Showing ${start + 1}-${Math.min(end, filteredTypes.length)} of ${filteredTypes.length} leave types`;
        renderPagination('type', currentTypePage, totalPages);
    }

    // Render leave requests table - REMOVED REASON AND DATE RANGE COLUMNS
    function renderLeaveRequestTable(data) {
        filteredRequests = data;
        updateRequestStats();
        
        const start = (currentRequestPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const paginatedData = filteredRequests.slice(start, end);
        
        const tbody = document.getElementById('requestTableBody');
        tbody.innerHTML = paginatedData.map((req, index) => {
            const statusClass = {
                'Pending': 'badge-warning',
                'Approved': 'badge-success',
                'Rejected': 'badge-danger',
                'Cancelled': 'badge-secondary'
            }[req.status] || 'badge-secondary';
            
            return `
                <tr>
                    <td class="row-number">${start + index + 1}</td>
                    <td>
                        <div class="employee-cell">
                            <img src="${req.profilePhoto || '/3ME/assets/images/default-avatar.png'}" class="employee-avatar" style="object-fit: cover;" />
                            <div class="employee-info">
                                <h4>${escapeHtml(req.employeeName)}</h4>
                                <p>${req.employeeId}</p>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-info">${escapeHtml(req.leaveType)}</span>
                    </td>
                    <td><strong>${req.duration}</strong> day${req.duration > 1 ? 's' : ''}</td>
                    <td><span class="badge ${statusClass}">${req.status}</span></td>
                    <td>${req.approvedBy || '—'}</td>
                    <td class="action-icons">
                        <i class="fas fa-eye" onclick="viewLeaveRequest('${req.id}')" title="View Details"></i>
                        <i class="fas fa-edit" onclick="editLeaveRequest('${req.id}')" title="Edit"></i>
                        ${req.status === 'Pending' ? `
                            <i class="fas fa-check-circle" onclick="approveRequest('${req.id}')" title="Approve" style="color: #10b981;"></i>
                            <i class="fas fa-times-circle" onclick="rejectRequest('${req.id}')" title="Reject" style="color: #ef4444;"></i>
                        ` : ''}
                    </td>
                </tr>
            `;
        }).join('');
        
        const totalPages = Math.ceil(filteredRequests.length / itemsPerPage);
        document.getElementById('requestPaginationInfo').textContent = `Showing ${start + 1}-${Math.min(end, filteredRequests.length)} of ${filteredRequests.length} requests`;
        renderPagination('request', currentRequestPage, totalPages);
    }

    // Render leave balances table
    function renderBalanceTable(data) {
        filteredBalances = data;
        updateBalanceStats();
        
        // Group by employee for display
        const employeeMap = new Map();
        filteredBalances.forEach(bal => {
            if (!employeeMap.has(bal.employeeId)) {
                employeeMap.set(bal.employeeId, {
                    employeeId: bal.employeeId,
                    employeeName: bal.employeeName,
                    department: bal.department,
                    avatar: bal.avatar,
                    color: bal.color,
                    profilePhoto: bal.profilePhoto || null,
                    balances: []
                });
            }
            employeeMap.get(bal.employeeId).balances.push(bal);
        });
        
        const employees = Array.from(employeeMap.values());
        
        const start = (currentBalancePage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const paginatedData = employees.slice(start, end);
        
        const tbody = document.getElementById('balanceTableBody');
        tbody.innerHTML = paginatedData.map((emp, index) => {
            const firstBal = emp.balances[0];
            const totalUsed = emp.balances.reduce((sum, b) => sum + b.used, 0);
            const totalBalance = emp.balances.reduce((sum, b) => sum + b.balance, 0);
            const totalAccrued = emp.balances.reduce((sum, b) => sum + b.accrued, 0);
            const usagePercent = totalAccrued > 0 ? (totalUsed / totalAccrued) * 100 : 0;
            let barClass = 'success';
            if (usagePercent > 75) barClass = 'danger';
            else if (usagePercent > 50) barClass = 'warning';
            
            // Create leave types preview
            const leaveTypesPreview = emp.balances.map(b => 
                `<span class="badge ${b.leaveType === 'Vacation Leave' ? 'badge-success' : 'badge-info'}" style="margin-right: 4px; margin-bottom: 4px;">${escapeHtml(b.leaveType)}</span>`
            ).join('');
            
            // Create a serializable employee data object for the onclick handler
            const empDataForHandler = {
                employeeId: emp.employeeId,
                employeeName: emp.employeeName,
                department: emp.department,
                avatar: emp.avatar,
                color: emp.color
            };
            const empDataStr = JSON.stringify(empDataForHandler).replace(/'/g, "\\'").replace(/"/g, '&quot;');
            
            return `
                <tr class="clickable-row" onclick="viewBalanceDetails('${emp.employeeId}')">
                    <td class="row-number" onclick="event.stopPropagation()">${start + index + 1}</td>
                    <td>
                        <div class="employee-cell">
                            <img src="${emp.profilePhoto || '/3ME/assets/images/default-avatar.png'}" class="employee-avatar" style="object-fit: cover;" />
                            <div class="employee-info">
                                <h4>${escapeHtml(emp.employeeName)}</h4>
                                <p>${emp.employeeId}</p>
                            </div>
                        </div>
                    </td>
                    <td>${escapeHtml(emp.department)}</td>
                    <td><span class="credits-display">${totalAccrued.toFixed(1)}</span></td>
                    <td><span style="color: #ef4444;">${totalUsed.toFixed(1)}</span></td>
                    <td>
                        <div class="balance-bar-container">
                            <span style="font-weight: 600; color: ${totalBalance === 0 ? '#ef4444' : '#0f172a'};">${totalBalance.toFixed(1)}</span>
                            <div class="balance-bar">
                                <div class="balance-fill ${barClass}" style="width: ${usagePercent}%;"></div>
                            </div>
                        </div>
                    </td>
                    <td onclick="event.stopPropagation()">${firstBal.lastAccrual}</td>
                    <td class="action-icons" onclick="event.stopPropagation()">
                        <i class="fas fa-eye" onclick="viewBalanceDetails('${emp.employeeId}')" title="View Balance Details"></i>
                    </td>
                </tr>
            `;
        }).join('');
        
        const totalPages = Math.ceil(employees.length / itemsPerPage);
        document.getElementById('balancePaginationInfo').textContent = `Showing ${start + 1}-${Math.min(end, employees.length)} of ${employees.length} employees`;
        renderPagination('balance', currentBalancePage, totalPages);
    }

    // Pagination helper
    function renderPagination(type, currentPage, totalPages) {
        const container = document.getElementById(type + 'PaginationControls');
        if (!container) return;
        let html = '';
        html += `<div class="page-btn" onclick="change${type.charAt(0).toUpperCase() + type.slice(1)}Page(${currentPage - 1})" ${currentPage === 1 ? 'style="opacity:0.5; pointer-events:none;"' : ''}><i class="fas fa-chevron-left"></i></div>`;
        for (let i = 1; i <= Math.min(totalPages, 5); i++) {
            html += `<div class="page-btn ${currentPage === i ? 'active' : ''}" onclick="change${type.charAt(0).toUpperCase() + type.slice(1)}Page(${i})">${i}</div>`;
        }
        if (totalPages > 5) html += `<div class="page-btn">...</div>`;
        html += `<div class="page-btn" onclick="change${type.charAt(0).toUpperCase() + type.slice(1)}Page(${currentPage + 1})" ${currentPage === totalPages || totalPages === 0 ? 'style="opacity:0.5; pointer-events:none;"' : ''}><i class="fas fa-chevron-right"></i></div>`;
        container.innerHTML = html;
    }

    // Page change functions
    function changeRequestPage(page) { if (page >= 1 && page <= Math.ceil(filteredRequests.length / itemsPerPage)) { currentRequestPage = page; renderLeaveRequestTable(filteredRequests); } }
    function changeBalancePage(page) { 
        const employeeMap = new Map();
        filteredBalances.forEach(bal => {
            if (!employeeMap.has(bal.employeeId)) {
                employeeMap.set(bal.employeeId, bal);
            }
        });
        const employees = Array.from(employeeMap.values());
        if (page >= 1 && page <= Math.ceil(employees.length / itemsPerPage)) { currentBalancePage = page; renderBalanceTable(filteredBalances); } 
    }
    function changeTypePage(page) { if (page >= 1 && page <= Math.ceil(filteredTypes.length / itemsPerPage)) { currentTypePage = page; renderLeaveTypeTable(filteredTypes); } }

    // Tab switching
    function switchTab(tab) {
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
        
        if (tab === 'requests') {
            document.querySelectorAll('.tab-btn')[0].classList.add('active');
            document.getElementById('requestsTab').classList.add('active');
            renderRequestsLevel();
        } else if (tab === 'balances') {
            document.querySelectorAll('.tab-btn')[1].classList.add('active');
            document.getElementById('balancesTab').classList.add('active');
            if (currentBalancesLevel === 'balances') {
                renderBalancesLevel();
            } else {
                renderEmployeeHistoryLevel();
            }
        } else {
            document.querySelectorAll('.tab-btn')[2].classList.add('active');
            document.getElementById('typesTab').classList.add('active');
            renderLeaveTypeTable(filteredTypes);
        }
    }

    // Filter functions
    function applyRequestFilters() {
        const searchTerm = document.getElementById('requestSearchInput').value.toLowerCase();
        const statusValue = document.getElementById('requestStatusFilter').value;
        const typeValue = document.getElementById('leaveTypeFilter').value;
        
        let filtered = window.leaveRequests.filter(req => {
            const matchesSearch = req.employeeName.toLowerCase().includes(searchTerm) || req.leaveType.toLowerCase().includes(searchTerm);
            const matchesStatus = !statusValue || req.status === statusValue;
            const matchesType = !typeValue || req.leaveType === typeValue;
            return matchesSearch && matchesStatus && matchesType;
        });
        
        currentRequestPage = 1;
        renderLeaveRequestTable(filtered);
    }

    function applyBalanceFilters() {
        const searchTerm = document.getElementById('balanceSearchInput').value.toLowerCase();
        const deptValue = document.getElementById('balanceDepartmentFilter').value;
        
        let filtered = window.leaveBalances.filter(bal => {
            const matchesSearch = bal.employeeName.toLowerCase().includes(searchTerm);
            const matchesDept = !deptValue || bal.department === deptValue;
            return matchesSearch && matchesDept;
        });
        
        currentBalancePage = 1;
        renderBalanceTable(filtered);
    }

    function applyTypeFilters() {
        const searchTerm = document.getElementById('typeSearchInput').value.toLowerCase();
        let filtered = window.leaveTypes.filter(type => type.name.toLowerCase().includes(searchTerm));
        currentTypePage = 1;
        renderLeaveTypeTable(filtered);
    }

    // Helper functions
    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>]/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;' })[m] || m);
    }

    function getLeaveIcon(leaveName) {
        const icons = { 'Vacation Leave': 'umbrella-beach', 'Sick Leave': 'hospital', 'Emergency Leave': 'exclamation-triangle', 'Maternity Leave': 'baby', 'Paternity Leave': 'baby-carriage', 'Bereavement Leave': 'heart-broken', 'Unpaid Leave': 'money-bill-wave' };
        return icons[leaveName] || 'calendar';
    }

    async function approveRequest(id) {
        try {
            const res = await fetch('../../api/leave/requests.php', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id, status: 'Approved', approvedBy: 'Monica White' })
            });
            const data = await res.json();
            if (data.success) {
                showToast('Leave request approved!', 'success');
                await loadLeaveDataFromDB();
            } else {
                showToast(data.message || 'Failed to approve request', 'warning');
            }
        } catch (error) {
            console.error('Error approving request:', error);
            showToast('Failed to connect to database API', 'warning');
        }
    }

    async function rejectRequest(id) {
        try {
            const res = await fetch('../../api/leave/requests.php', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id, status: 'Rejected' })
            });
            const data = await res.json();
            if (data.success) {
                showToast('Leave request rejected.', 'warning');
                await loadLeaveDataFromDB();
            } else {
                showToast(data.message || 'Failed to reject request', 'warning');
            }
        } catch (error) {
            console.error('Error rejecting request:', error);
            showToast('Failed to connect to database API', 'warning');
        }
    }

    function duplicateLeaveType(id) { 
        const type = window.leaveTypes.find(t => t.id === id);
        if (type) {
            showToast('Duplicating leave type: ' + type.name, 'info');
        }
    }

    function exportEmployeeHistory(employeeId) {
        showToast('Exporting leave history...', 'info');
    }

    function exportData() { showToast('Exporting leave management report...', 'info'); }

    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.style.cssText = `position: fixed; bottom: 24px; right: 24px; background: ${type === 'success' ? '#10b981' : type === 'warning' ? '#f59e0b' : '#1e293b'}; color: white; padding: 12px 20px; border-radius: 12px; font-size: 13px; z-index: 10000; animation: slideIn 0.3s ease; box-shadow: 0 4px 12px rgba(0,0,0,0.15);`;
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, 3000);
    }

    // Make functions globally available
    window.navigateToBalancesLevel = navigateToBalancesLevel;
    window.navigateToEmployeeHistory = navigateToEmployeeHistory;
    window.changeHistoryPage = changeHistoryPage;
    window.exportEmployeeHistory = exportEmployeeHistory;

    // Add animation style
    const style = document.createElement('style');
    style.textContent = `@keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }`;
    document.head.appendChild(style);

    // Initialize
    (function() {
        const navItems = document.querySelectorAll('.nav-item');
        navItems.forEach(item => { item.classList.remove('active'); if (item.getAttribute('data-title') === 'Leave') item.classList.add('active'); });

        loadLeaveDataFromDB();

        document.getElementById('typeSearchInput').addEventListener('keyup', applyTypeFilters);

        console.log('✅ Leave Management loaded with drill-down navigation');
    })();
</script>

</body>
</html>