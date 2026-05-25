<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
/**
 * onboard-requisition.php
 * Onboarding & Exit Management - Clean interface with sidebar and modal components
 * Updated with right-side slide-out modals matching attendance design
 */

$pageTitle = "Onboarding & Exit";
$activeMenu = "Onboarding";
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
            position: relative;
        }

        .tab-btn.active {
            background: white;
            color: #4f46e5;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .tab-btn:hover:not(.active) {
            color: #1e293b;
        }

        /* Notification badge */
        .notification-badge {
            background: #ef4444;
            color: white;
            font-size: 10px;
            font-weight: 600;
            min-width: 18px;
            height: 18px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 5px;
            margin-left: 6px;
            line-height: 1;
            box-shadow: 0 2px 4px rgba(239, 68, 68, 0.3);
        }

        .notification-badge.hidden {
            display: none;
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

        .onboard-table, .exit-table, .accepted-offers-table {
            width: 100%;
            border-collapse: collapse;
        }

        .onboard-table th, .exit-table th, .accepted-offers-table th {
            text-align: left;
            padding: 12px 8px;
            font-weight: 600;
            color: #475569;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #e2e8f0;
        }

        .onboard-table td, .exit-table td, .accepted-offers-table td {
            padding: 12px 8px;
            border-bottom: 1px solid #f1f5f9;
            color: #1e293b;
            font-size: 13px;
        }

        .onboard-table tbody tr:hover, .exit-table tbody tr:hover, .accepted-offers-table tbody tr:hover {
            background: rgba(79, 70, 229, 0.03);
        }

        .employee-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .employee-avatar {
            width: 36px;
            height: 36px;
            border-radius: 12px;
            object-fit: cover;
            flex-shrink: 0;
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

        /* Progress indicator */
        .progress-indicator {
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 500;
        }

        .progress-fraction {
            color: #1e293b;
            font-weight: 600;
        }

        .progress-label {
            color: #64748b;
            font-size: 11px;
        }

        /* Checklist preview */
        .checklist-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            max-width: 250px;
        }

        .checklist-item {
            background: #f1f5f9;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 10px;
            color: #475569;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .checklist-item.completed {
            background: #dcfce7;
            color: #16a34a;
            text-decoration: line-through;
        }

        .checklist-item i {
            font-size: 8px;
        }

        /* Salary display */
        .salary-display {
            font-weight: 600;
            color: #10b981;
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

        /* Tab content */
        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .row-number {
            color: #94a3b8;
            font-weight: 500;
            width: 30px;
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #64748b;
        }

        .empty-state i {
            font-size: 48px;
            color: #cbd5e1;
            margin-bottom: 16px;
        }

        .empty-state h4 {
            font-size: 16px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 8px;
        }

        .empty-state p {
            font-size: 13px;
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 14px;
            }
            .onboard-table, .exit-table, .accepted-offers-table {
                display: block;
                overflow-x: auto;
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

    <!-- MAIN CONTENT - ONBOARDING & EXIT MANAGEMENT -->
    <main class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <h1><i class="fas fa-user-plus"></i> Onboarding & Exit Management</h1>
        </div>

        <!-- Tabs -->
        <div class="tabs-container">
            <button class="tab-btn active" onclick="switchTab('onboarding')"><i class="fas fa-user-plus"></i> Onboarding</button>
            <button class="tab-btn" onclick="switchTab('exit')"><i class="fas fa-sign-out-alt"></i> Exit Management</button>
        </div>

        <!-- Onboarding Tab -->
        <div id="onboardingTab" class="tab-content active">
            <!-- Filter Bar -->
            <div class="filter-bar">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search by employee name..." id="onboardSearchInput">
                </div>
                <select class="filter-select" id="progressFilter">
                    <option value="">All Progress</option>
                    <option value="Not Started">Not Started</option>
                    <option value="In Progress">In Progress</option>
                </select>
                <button class="btn btn-secondary" onclick="toggleCompletedRecords()" id="toggleCompletedBtn" title="Completed records are hidden by default. Click to show them.">
                    <i class="fas fa-eye"></i> Show Completed
                </button>
            </div>

            <!-- Stats -->
            <div class="stats-mini">
                <div class="stat-mini-card">
                    <i class="fas fa-users"></i>
                    <span id="totalOnboarding">0</span> <small>Active Onboarding</small>
                </div>
                <div class="stat-mini-card">
                    <i class="fas fa-hourglass-half"></i>
                    <span id="inProgressOnboarding">0</span> <small>In Progress</small>
                </div>
                <div class="stat-mini-card">
                    <i class="fas fa-check-circle"></i>
                    <span id="completedOnboarding">0</span> <small>Completed (This Month)</small>
                </div>
                <div class="stat-mini-card">
                    <i class="fas fa-calendar-check"></i>
                    <span id="startingThisWeek">0</span> <small>Starting This Week</small>
                </div>
                <div class="stat-mini-card" id="hiddenRecordsIndicator" style="display: none; background: #fef3c7; border-color: #fbbf24;">
                    <i class="fas fa-eye-slash" style="color: #f59e0b;"></i>
                    <span id="hiddenRecordsCount" style="color: #b45309;">0</span> <small style="color: #b45309;">Completed</small>
                </div>
                <!-- <div class="header-actions">
                    <button class="btn btn-primary" onclick="openAddOnboardModal()"><i class="fas fa-plus"></i> Manual Onboarding</button>
                </div> -->
            </div>

            <!-- Onboarding Table -->
            <div class="table-card">
                <h3><i class="fas fa-list-check"></i> Onboard Requisitions</h3>
                <table class="onboard-table">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Job</th>
                            <th>Tasks Checklist</th>
                            <th>Progress</th>
                            <th>Completion Date</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="onboardTableBody">
                        <!-- Onboarding rows will be populated here -->
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="pagination">
                    <div class="pagination-info" id="onboardPaginationInfo">
                        Showing 0 of 0 onboarding records
                    </div>
                    <div class="pagination-controls" id="onboardPaginationControls">
                        <!-- Pagination buttons -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Exit Management Tab -->
        <div id="exitTab" class="tab-content">
            <!-- Filter Bar -->
            <div class="filter-bar">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search by employee name..." id="exitSearchInput">
                </div>
                <select class="filter-select" id="exitStatusFilter">
                    <option value="">All Status</option>
                    <option value="Pending">Pending Clearance</option>
                    <option value="Cleared">Cleared</option>
                    <option value="Archived">Archived</option>
                </select>
            </div>

            <!-- Stats -->
            <div class="stats-mini">
                <div class="stat-mini-card">
                    <i class="fas fa-sign-out-alt"></i>
                    <span id="totalExits">0</span> <small>Total Exits</small>
                </div>
                <div class="stat-mini-card">
                    <i class="fas fa-clock"></i>
                    <span id="pendingExits">0</span> <small>Pending</small>
                </div>
                <div class="stat-mini-card">
                    <i class="fas fa-check-double"></i>
                    <span id="clearedExits">0</span> <small>Cleared</small>
                </div>
                <div class="stat-mini-card">
                    <i class="fas fa-calendar-alt"></i>
                    <span id="exitsThisMonth">0</span> <small>This Month</small>
                </div>
                <div class="header-actions">
                    <button class="btn btn-danger" onclick="openAddExitModal()"><i class="fas fa-plus"></i> Process Exit</button>
                </div>
            </div>

            <!-- Exit Table -->
            <div class="table-card">
                <h3><i class="fas fa-door-open"></i> Exit and Attendance Records</h3>
                <table class="exit-table">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Job</th>
                            <th>Last Working Day</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Clearance Approved By</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="exitTableBody">
                        <!-- Exit rows will be populated here -->
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="pagination">
                    <div class="pagination-info" id="exitPaginationInfo">
                        Showing 0 of 0 exit records
                    </div>
                    <div class="pagination-controls" id="exitPaginationControls">
                        <!-- Pagination buttons -->
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Include Modal Components -->
<?php include 'modals/modal-wrapper.php'; ?>
<?php include 'modals/onboarding-modal/modal-add-onboard.php'; ?>
<?php include 'modals/onboarding-modal/modal-edit-onboard.php'; ?>
<?php include 'modals/onboarding-modal/modal-view-onboard.php'; ?>
<?php include 'modals/onboarding-modal/modal-add-exit.php'; ?>
<?php include 'modals/onboarding-modal/modal-edit-exit.php'; ?>
<?php include 'modals/onboarding-modal/modal-view-exit.php'; ?>
<?php include 'modals/onboarding-modal/modal-onboarding-helpers.php'; ?>

<script>
    // Initialize data arrays with server data
    window.onboardRecords = [];
    window.exitRecords = [];

    // Load data from server
    async function loadOnboardingData() {
        try {
            // Load onboarding records
            console.log('🔄 Loading onboarding data from API...');
            const onboardResponse = await fetch('../../api/onboarding/records.php');
            console.log('📡 Onboard API response status:', onboardResponse.status);
            console.log('📡 Onboard API response URL:', onboardResponse.url);
            
            if (onboardResponse.ok) {
                const onboardResult = await onboardResponse.json();
                console.log('📥 Onboarding API response:', onboardResult);
                
                if (onboardResult.success && Array.isArray(onboardResult.data)) {
                    console.log('✅ Processing', onboardResult.data.length, 'onboarding records...');
                    
                    // Log raw data to see what we're getting
                    console.log('📋 Raw onboarding data from API:', onboardResult.data);
                    
                    window.onboardRecords = onboardResult.data.map(record => {
                        console.log('🔄 Processing record:', record.id, record);
                        return {
                            id: record.id,
                            employeeId: record.employee_id,
                            employeeName: record.employee_name,
                            employeeEmail: record.employee_email,
                            position: record.job || record.position || 'Unknown Position', // Support both 'job' and 'position' fields
                            department: record.department,
                            company: record.company,
                            job_id: record.job_id,           // Preserve job_id foreign key
                            department_id: record.department_id, // Preserve department_id foreign key
                            company_id: record.company_id,      // Preserve company_id foreign key
                            startDate: record.start_date,
                            progress: record.progress,
                            completionDate: record.completion_date ? formatDateForDisplay(record.completion_date) : null,
                            tasks: record.tasks || [],
                            notes: record.notes || '',
                            avatar: record.avatar || 'NA',
                            color: record.color || 'linear-gradient(145deg, #6366f1, #a78bfa)',
                            profilePhoto: record.profile_photo || null
                        };
                    });
                    console.log('✅ Processed onboarding records:', window.onboardRecords);
                } else {
                    console.warn('⚠️ Invalid onboarding API response format:', onboardResult);
                    window.onboardRecords = [];
                }
            } else {
                console.error('❌ Failed to fetch onboarding records:', onboardResponse.status);
                const errorText = await onboardResponse.text();
                console.error('❌ Error response:', errorText);
                window.onboardRecords = [];
            }

            // Load exit records
            console.log('🔄 Loading exit data from API...');
            const exitResponse = await fetch('../../api/onboarding/exits.php');
            console.log('📡 Exit API response status:', exitResponse.status);
            console.log('📡 Exit API response URL:', exitResponse.url);
            
            if (exitResponse.ok) {
                const exitResult = await exitResponse.json();
                console.log('📥 Exit API response:', exitResult);
                
                if (exitResult.success && Array.isArray(exitResult.data)) {
                    window.exitRecords = exitResult.data.map(record => ({
                        id: record.id,
                        employeeId: record.employee_id,
                        employeeName: record.employee_name,
                        employeeEmail: record.employee_email,
                        position: record.position,
                        department: record.department,
                        company: record.company,
                        lastWorkingDay: formatDateForDisplay(record.last_working_day),
                        reason: record.reason,
                        status: record.status,
                        clearanceApprovedBy: record.clearance_approved_by,
                        resignationLetter: record.resignation_letter,
                        notes: record.notes || '',
                        avatar: record.avatar || 'NA',
                        color: record.color || 'linear-gradient(145deg, #6366f1, #a78bfa)',
                        profilePhoto: record.profile_photo || null
                    }));
                } else {
                    console.warn('⚠️ Invalid exit API response format:', exitResult);
                    window.exitRecords = [];
                }
            } else {
                console.error('❌ Failed to fetch exit records:', exitResponse.status);
                window.exitRecords = [];
            }

            console.log('✅ Onboarding data loaded from server:', {
                onboardRecords: window.onboardRecords.length,
                exitRecords: window.exitRecords.length
            });
        } catch (error) {
            console.error('💥 Error loading onboarding data:', error);
            console.log('⚠️ Falling back to sample data due to error');
            // Fallback to sample data
            loadSampleOnboardingData();
        }
    }
    
    // Helper function to get initials from name
    function getInitials(name) {
        if (!name || name === 'Unknown Applicant') return 'NA';
        const parts = name.split(' ');
        if (parts.length >= 2) {
            return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
        }
        return name.substring(0, 2).toUpperCase();
    }
    
    // Helper function to format dates for display
    function formatDateForDisplay(dateString) {
        if (!dateString) return null;
        try {
            const date = new Date(dateString);
            if (isNaN(date.getTime())) return dateString;
            return date.toLocaleDateString('en-US', { 
                month: 'short', 
                day: '2-digit', 
                year: 'numeric' 
            });
        } catch (e) {
            return dateString;
        }
    }

    // Fallback sample data
    function loadSampleOnboardingData() {
        console.log('⚠️ Loading sample onboarding data as fallback');
        window.onboardRecords = [
            {
                id: 'ONB-2024-001',
                employeeId: 'EMP-2024-001',
                employeeName: 'Robert James Johnson',
                employeeEmail: 'robert.johnson@novacore.com',
                position: 'Marketing Specialist',
                department: 'Marketing',
                company: 'NovaCore Technologies',
                startDate: '2024-05-01',
                progress: 'In Progress',
                completionDate: null,
                tasks: [
                    { text: 'Complete employment forms', completed: true },
                    { text: 'IT equipment setup', completed: true },
                    { text: 'Office tour and introductions', completed: true },
                    { text: 'HR orientation session', completed: false },
                    { text: 'Department training', completed: false },
                    { text: 'System access setup', completed: false }
                ],
                notes: 'New hire from recruitment process',
                avatar: 'RJ',
                color: 'linear-gradient(145deg, #ef4444, #f87171)'
            }
        ];

        window.exitRecords = [
            {
                id: 'EXIT-2024-001',
                employeeId: 'EMP-2023-015',
                employeeName: 'Amanda Rodriguez',
                employeeEmail: 'amanda.rodriguez@novacore.com',
                position: 'Marketing Coordinator',
                department: 'Marketing',
                company: 'NovaCore Technologies',
                lastWorkingDay: 'Apr 15, 2024',
                reason: 'Resignation - Better opportunity',
                status: 'Cleared',
                clearanceApprovedBy: 'Monica White',
                resignationLetter: 'resignation_letter_amanda.pdf',
                notes: 'Smooth transition, all assets returned',
                avatar: 'AR',
                color: 'linear-gradient(145deg, #ec4899, #f472b6)'
            }
        ];
    }

    // Pagination variables
    let currentOnboardPage = 1;
    let currentExitPage = 1;
    let itemsPerPage = 8;
    let filteredOnboard = [];
    let filteredExit = [];

    // Initialize filtered arrays after data is loaded
    function initializeFilteredArrays() {
        filteredOnboard = [...window.onboardRecords];
        filteredExit = [...window.exitRecords];
        console.log('🔄 Initialized filtered arrays:', {
            onboard: filteredOnboard.length,
            exit: filteredExit.length
        });
    }

    // Update stats
    function updateOnboardStats() {
        // Only count non-completed records as "active onboarding"
        const activeRecords = window.onboardRecords.filter(r => r.progress !== 'Completed');
        const total = activeRecords.length;
        const inProgress = activeRecords.filter(r => r.progress === 'In Progress').length;
        
        // Count completed this month for reference
        const thisMonth = new Date().getMonth();
        const thisYear = new Date().getFullYear();
        const completed = window.onboardRecords.filter(r => {
            if (r.progress !== 'Completed' || !r.completionDate) return false;
            const completionDate = new Date(r.completionDate);
            return completionDate.getMonth() === thisMonth && completionDate.getFullYear() === thisYear;
        }).length;
        
        // Count all completed records (not just this month)
        const allCompleted = window.onboardRecords.filter(r => r.progress === 'Completed').length;
        
        const startingThisWeek = 2; // This would need actual calculation based on start dates
        
        document.getElementById('totalOnboarding').innerText = total;
        document.getElementById('inProgressOnboarding').innerText = inProgress;
        document.getElementById('completedOnboarding').innerText = completed;
        document.getElementById('startingThisWeek').innerText = startingThisWeek;
        
        // Show/hide the hidden records indicator
        const hiddenIndicator = document.getElementById('hiddenRecordsIndicator');
        const hiddenCount = document.getElementById('hiddenRecordsCount');
        if (!showCompletedRecords && allCompleted > 0) {
            hiddenIndicator.style.display = 'flex';
            hiddenCount.innerText = allCompleted;
        } else {
            hiddenIndicator.style.display = 'none';
        }
    }

    function updateExitStats() {
        const total = window.exitRecords.length;
        const pending = window.exitRecords.filter(r => r.status === 'Pending').length;
        const cleared = window.exitRecords.filter(r => r.status === 'Cleared').length;
        const thisMonth = 2;
        
        document.getElementById('totalExits').innerText = total;
        document.getElementById('pendingExits').innerText = pending;
        document.getElementById('clearedExits').innerText = cleared;
        document.getElementById('exitsThisMonth').innerText = thisMonth;
    }

    // Calculate task completion
    function calculateProgress(tasks) {
        if (!tasks || tasks.length === 0) return { completed: 0, total: 0 };
        const completed = tasks.filter(t => t.completed).length;
        return { completed, total: tasks.length };
    }

    // Render onboarding table
    function renderOnboardTable(data) {
        console.log('🎨 Rendering onboarding table with', data.length, 'records:', data);
        
        // Use the data passed in (already filtered by applyOnboardFilters)
        filteredOnboard = data;
        console.log('📋 Displaying', filteredOnboard.length, 'onboarding records');
        
        updateOnboardStats();
        
        const start = (currentOnboardPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const paginatedData = filteredOnboard.slice(start, end);
        
        console.log('📄 Paginated data (page', currentOnboardPage, '):', paginatedData);
        
        const tbody = document.getElementById('onboardTableBody');
        
        // Check if we have no data
        if (paginatedData.length === 0) {
            console.warn('⚠️ No data to display in onboarding table');
            tbody.innerHTML = `
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <i class="fas fa-user-plus"></i>
                            <h4>No Onboarding Records</h4>
                            <p>Accepted offers will appear here</p>
                        </div>
                    </td>
                </tr>
            `;
            return;
        }
        
        tbody.innerHTML = paginatedData.map((record) => {
            const progressClass = {
                'Not Started': 'badge-secondary',
                'In Progress': 'badge-warning',
                'Completed': 'badge-success'
            }[record.progress] || 'badge-secondary';
            
            const progress = calculateProgress(record.tasks);
            
            const tasksPreview = record.tasks.slice(0, 3).map(t => 
                `<span class="checklist-item ${t.completed ? 'completed' : ''}">
                    <i class="fas fa-${t.completed ? 'check-circle' : 'circle'}"></i> ${escapeHtml(t.text.substring(0, 20))}${t.text.length > 20 ? '...' : ''}
                </span>`
            ).join('');
            
            return `
                <tr data-employee-id="${record.employeeId || record.id}" data-record-id="${record.id}">
                    <td>
                        <div class="employee-cell">
                            <img src="${record.profilePhoto || '/3ME/assets/images/default-avatar.png'}" class="employee-avatar" />
                            <div class="employee-info">
                                <h4>${escapeHtml(record.employeeName)}</h4>
                                <p>${escapeHtml(record.employeeEmail)}</p>
                                ${record.employeeId ? `<code style="font-size: 10px; color: #64748b;">${record.employeeId}</code>` : ''}
                            </div>
                        </div>
                    </td>
                    <td>${escapeHtml(record.position)}<br><small style="color: #64748b;">${record.department}</small></td>
                    <td>
                        <div class="checklist-preview">
                            ${progress.total === 0 ? 
                                `<span class="checklist-item" style="background: #fef3c7; color: #b45309; border: 1px dashed #f59e0b;">
                                    <i class="fas fa-exclamation-triangle"></i> No tasks assigned
                                </span>
                                <button class="btn" style="padding: 2px 8px; font-size: 10px; margin-left: 8px; background: #4f46e5; color: white; border-radius: 12px;" onclick="assignDefaultTasks('${record.employeeId || record.id}')">
                                    <i class="fas fa-plus"></i> Assign Tasks
                                </button>` :
                                `${tasksPreview}
                                ${progress.total > 3 ? `<span class="checklist-item">+${progress.total - 3} more</span>` : ''}`
                            }
                        </div>
                    </td>
                    <td>
                        <div class="progress-indicator">
                            <span class="progress-fraction">${progress.completed}/${progress.total}</span>
                            <span class="badge ${progressClass}">${record.progress}</span>
                            ${record.progress === 'Not Started' && record.notes && record.notes.includes('job offer') ? 
                                `<span class="badge badge-info" style="margin-left: 8px; font-size: 10px;">
                                    <i class="fas fa-star"></i> New
                                </span>` : ''}
                        </div>
                    </td>
                    <td>${record.completionDate || '—'}</td>
                    <td class="action-icons">
                        <i class="fas fa-eye" onclick="viewOnboard('${record.id}')" title="View"></i>
                        <i class="fas fa-edit" onclick="editOnboard('${record.id}')" title="Edit"></i>
                    </td>
                </tr>
            `;
        }).join('');
        
        const totalPages = Math.ceil(filteredOnboard.length / itemsPerPage);
        document.getElementById('onboardPaginationInfo').textContent = `Showing ${start + 1}-${Math.min(end, filteredOnboard.length)} of ${filteredOnboard.length} records`;
        
        const paginationContainer = document.getElementById('onboardPaginationControls');
        let paginationHtml = '';
        paginationHtml += `<div class="page-btn" onclick="changeOnboardPage(${currentOnboardPage - 1})" ${currentOnboardPage === 1 ? 'style="opacity:0.5; pointer-events:none;"' : ''}><i class="fas fa-chevron-left"></i></div>`;
        for (let i = 1; i <= Math.min(totalPages, 5); i++) {
            paginationHtml += `<div class="page-btn ${currentOnboardPage === i ? 'active' : ''}" onclick="changeOnboardPage(${i})">${i}</div>`;
        }
        if (totalPages > 5) {
            paginationHtml += `<div class="page-btn">...</div>`;
        }
        paginationHtml += `<div class="page-btn" onclick="changeOnboardPage(${currentOnboardPage + 1})" ${currentOnboardPage === totalPages || totalPages === 0 ? 'style="opacity:0.5; pointer-events:none;"' : ''}><i class="fas fa-chevron-right"></i></div>`;
        paginationContainer.innerHTML = paginationHtml;
    }

    // Render exit table
    function renderExitTable(data) {
        filteredExit = data;
        updateExitStats();
        
        const start = (currentExitPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const paginatedData = filteredExit.slice(start, end);
        
        const tbody = document.getElementById('exitTableBody');
        tbody.innerHTML = paginatedData.map((record) => {
            const statusClass = {
                'Pending': 'badge-warning',
                'Cleared': 'badge-success',
                'Archived': 'badge-secondary'
            }[record.status] || 'badge-secondary';
            
            const reasonPreview = record.reason.length > 30 ? record.reason.substring(0, 30) + '...' : record.reason;
            
            return `
                <tr>
                    <td>
                        <div class="employee-cell">
                            <img src="${record.profilePhoto || '/3ME/assets/images/default-avatar.png'}" class="employee-avatar" />
                            <div class="employee-info">
                                <h4>${escapeHtml(record.employeeName)}</h4>
                                <p>${escapeHtml(record.employeeEmail)}</p>
                            </div>
                        </div>
                    </td>
                    <td>${escapeHtml(record.position)}<br><small style="color: #64748b;">${record.department}</small></td>
                    <td>${record.lastWorkingDay}</td>
                    <td title="${escapeHtml(record.reason)}">${escapeHtml(reasonPreview)}</td>
                    <td><span class="badge ${statusClass}">${record.status}</span></td>
                    <td>${record.clearanceApprovedBy || '—'}</td>
                    <td class="action-icons">
                        <i class="fas fa-eye" onclick="viewExit('${record.id}')" title="View"></i>
                        <i class="fas fa-edit" onclick="editExit('${record.id}')" title="Edit"></i>
                        ${record.resignationLetter ? 
                            `<i class="fas fa-file-pdf" onclick="viewResignationLetter('${record.id}')" title="View Resignation Letter"></i>` : ''}
                        ${record.status === 'Pending' ? 
                            `<i class="fas fa-check-double" onclick="approveClearance('${record.id}')" title="Approve Clearance" style="color: #10b981;"></i>` : ''}
                    </td>
                </tr>
            `;
        }).join('');
        
        const totalPages = Math.ceil(filteredExit.length / itemsPerPage);
        document.getElementById('exitPaginationInfo').textContent = `Showing ${start + 1}-${Math.min(end, filteredExit.length)} of ${filteredExit.length} records`;
        
        const paginationContainer = document.getElementById('exitPaginationControls');
        let paginationHtml = '';
        paginationHtml += `<div class="page-btn" onclick="changeExitPage(${currentExitPage - 1})" ${currentExitPage === 1 ? 'style="opacity:0.5; pointer-events:none;"' : ''}><i class="fas fa-chevron-left"></i></div>`;
        for (let i = 1; i <= Math.min(totalPages, 5); i++) {
            paginationHtml += `<div class="page-btn ${currentExitPage === i ? 'active' : ''}" onclick="changeExitPage(${i})">${i}</div>`;
        }
        if (totalPages > 5) {
            paginationHtml += `<div class="page-btn">...</div>`;
        }
        paginationHtml += `<div class="page-btn" onclick="changeExitPage(${currentExitPage + 1})" ${currentExitPage === totalPages || totalPages === 0 ? 'style="opacity:0.5; pointer-events:none;"' : ''}><i class="fas fa-chevron-right"></i></div>`;
        paginationContainer.innerHTML = paginationHtml;
    }

    // Pagination functions
    function changeOnboardPage(page) {
        const totalPages = Math.ceil(filteredOnboard.length / itemsPerPage);
        if (page < 1 || page > totalPages) return;
        currentOnboardPage = page;
        renderOnboardTable(filteredOnboard);
    }

    function changeExitPage(page) {
        const totalPages = Math.ceil(filteredExit.length / itemsPerPage);
        if (page < 1 || page > totalPages) return;
        currentExitPage = page;
        renderExitTable(filteredExit);
    }

    // Tab switching
    function switchTab(tab) {
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
        
        if (tab === 'onboarding') {
            document.querySelectorAll('.tab-btn')[0].classList.add('active');
            document.getElementById('onboardingTab').classList.add('active');
            renderOnboardTable(window.onboardRecords);
        } else {
            document.querySelectorAll('.tab-btn')[1].classList.add('active');
            document.getElementById('exitTab').classList.add('active');
            renderExitTable(window.exitRecords);
        }
    }

    // Filter functions
    let showCompletedRecords = false;
    
    function toggleCompletedRecords() {
        showCompletedRecords = !showCompletedRecords;
        const btn = document.getElementById('toggleCompletedBtn');
        
        if (showCompletedRecords) {
            btn.innerHTML = '<i class="fas fa-eye-slash"></i> Hide Completed';
            btn.style.background = '#4f46e5';
            btn.style.color = 'white';
        } else {
            btn.innerHTML = '<i class="fas fa-eye"></i> Show Completed';
            btn.style.background = 'white';
            btn.style.color = '#1e293b';
            
            // Show a brief message that completed records are now hidden
            const completedCount = window.onboardRecords.filter(r => r.progress === 'Completed').length;
            if (completedCount > 0) {
                showToast(`${completedCount} completed record${completedCount > 1 ? 's' : ''} hidden`, 'info');
            }
        }
        
        applyOnboardFilters();
    }
    
    function applyOnboardFilters() {
        const searchTerm = document.getElementById('onboardSearchInput').value.toLowerCase();
        const progressValue = document.getElementById('progressFilter').value;
        
        // Start with filtering based on completed toggle
        let filtered = showCompletedRecords 
            ? window.onboardRecords 
            : window.onboardRecords.filter(record => record.progress !== 'Completed');
        
        // Apply search filter
        filtered = filtered.filter(record => {
            const matchesSearch = record.employeeName.toLowerCase().includes(searchTerm) ||
                                 record.position.toLowerCase().includes(searchTerm) ||
                                 record.department.toLowerCase().includes(searchTerm);
            return matchesSearch;
        });
        
        // Apply progress filter
        if (progressValue) {
            filtered = filtered.filter(record => record.progress === progressValue);
        }
        
        currentOnboardPage = 1;
        renderOnboardTable(filtered);
    }

    function applyExitFilters() {
        const searchTerm = document.getElementById('exitSearchInput').value.toLowerCase();
        const statusValue = document.getElementById('exitStatusFilter').value;
        
        let filtered = window.exitRecords.filter(record => {
            const matchesSearch = record.employeeName.toLowerCase().includes(searchTerm) ||
                                 record.position.toLowerCase().includes(searchTerm) ||
                                 record.department.toLowerCase().includes(searchTerm);
            const matchesStatus = !statusValue || record.status === statusValue;
            return matchesSearch && matchesStatus;
        });
        
        currentExitPage = 1;
        renderExitTable(filtered);
    }

    // Helper functions
    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }

    function manageTasks(id) {
        editOnboard(id);
    }

    async function markComplete(id) {
        const record = window.onboardRecords.find(r => r.id === id);
        if (!record) return;
        
        console.log('🔍 Checking onboarding record for completion:', {
            id: record.id,
            name: record.employeeName,
            company_id: record.company_id,
            department_id: record.department_id,
            job_id: record.job_id,
            company: record.company,
            department: record.department,
            position: record.position
        });
        
        // Check if IDs are missing or null
        if (!record.company_id || !record.department_id) {
            console.error('❌ Validation failed - missing IDs');
            showToast('⚠️ Cannot complete onboarding: Company ID and Department ID are required', 'error');
            
            // Suggest editing the record
            setTimeout(() => {
                const message = `This employee needs valid company and department assignments before completing onboarding.\n\nCurrent assignments:\n• Company ID: "${record.company_id || '(empty)'}"\n• Department ID: "${record.department_id || '(empty)'}"\n\nWould you like to edit the record now to assign proper values?`;
                if (confirm(message)) {
                    editOnboard(id);
                }
            }, 500);
            return;
        }
        
        console.log('✅ Validation passed - proceeding with completion');
        
        // Update onboarding record
        record.progress = 'Completed';
        record.tasks.forEach(t => t.completed = true);
        record.completionDate = new Date().toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' });
        
        // Show loading message
        showToast(`Completing onboarding for ${record.employeeName}...`, 'info');
        
        try {
            console.log('📝 Marking onboarding as complete and creating employee record...');
            console.log('📋 Onboarding record details:', {
                id: record.id,
                name: record.employeeName,
                email: record.employeeEmail,
                company_id: record.company_id,
                department_id: record.department_id,
                job_id: record.job_id
            });
            
            // Update the onboarding record in the database with IDs
            // The API will automatically create the employee record when progress is set to "Completed"
            const updateResponse = await fetch('../../api/onboarding/records.php', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    id: record.id,
                    employee_name: record.employeeName,
                    employee_email: record.employeeEmail,
                    job_id: record.job_id,           // Pass job_id foreign key
                    department_id: record.department_id, // Pass department_id foreign key
                    company_id: record.company_id,      // Pass company_id foreign key
                    start_date: record.startDate,
                    progress: 'Completed',
                    completion_date: new Date().toISOString().split('T')[0],
                    tasks: record.tasks,
                    notes: record.notes
                })
            });
            
            const updateResult = await updateResponse.json();
            
            // Check if the API explicitly reported employee creation failure
            if (!updateResult.success) {
                console.error('❌ API returned error:', updateResult.message);
                throw new Error(updateResult.message || 'Failed to complete onboarding');
            }
            
            // Check if employee was created (API returns employee_created flag)
            if (updateResult.employee_created === false) {
                console.error('❌ Employee was not created');
                throw new Error(updateResult.message || 'Employee creation failed');
            }
            
            console.log('✅ Onboarding completed and employee created successfully');
            console.log('✅ Employee ID:', updateResult.employee_id);
            
            // Update applicant status to "Hired" in recruitment system
            try {
                console.log('📝 Updating applicant status to Hired in recruitment system...');
                const applicantsResponse = await fetch('../../api/recruitment/applicants.php');
                const applicantsResult = await applicantsResponse.json();
                
                if (applicantsResult.success && Array.isArray(applicantsResult.data)) {
                    // Find applicant by email
                    const applicant = applicantsResult.data.find(a => a.email === record.employeeEmail);
                    
                    if (applicant) {
                        console.log('✅ Found applicant in recruitment system:', applicant.id);
                        
                        // Update applicant status to Hired
                        const updateApplicantResponse = await fetch('../../api/recruitment/applicants.php', {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                id: applicant.id,
                                firstname: applicant.firstname,
                                middlename: applicant.middlename || '',
                                surname: applicant.surname,
                                email: applicant.email,
                                contact_number: applicant.contact_number || '',
                                application_status: 'Hired',
                                interview_date: applicant.interview_date || null,
                                interview_type: applicant.interview_type || null,
                                interview_location: applicant.interview_location || null,
                                notes: applicant.notes || ''
                            })
                        });
                        
                        const updateApplicantResult = await updateApplicantResponse.json();
                        if (updateApplicantResult.success) {
                            console.log('✅ Applicant status updated to Hired in recruitment system');
                        } else {
                            console.warn('⚠️ Failed to update applicant status:', updateApplicantResult.message);
                        }
                    } else {
                        console.log('ℹ️ No matching applicant found in recruitment system (may have been manually added)');
                    }
                }
            } catch (error) {
                console.error('❌ Error updating applicant status:', error);
                // Don't fail the whole operation if this fails
            }
            
            // Reload onboarding data from server to get fresh data
            await loadOnboardingData();
            
            // Apply filters to hide completed records (default behavior)
            applyOnboardFilters();
            
            // Show success message with clear explanation
            showToast(`✅ ${record.employeeName} onboarding completed! Employee added to ${record.company} - ${record.department}`, 'success');
            
            // Show info about completed records being hidden
            setTimeout(() => {
                showToast('ℹ️ Completed records are now hidden. Click "Show Completed" to view them.', 'info');
            }, 2000);
            
            setTimeout(() => {
                if (confirm(`Onboarding completed successfully!\n\n${record.employeeName} has been added to:\n• Company: ${record.company}\n• Department: ${record.department}\n\nThe record has been moved to "Completed" status (hidden by default).\n\nWould you like to view the employee in the Employee Hub?`)) {
                    // Store navigation parameters in sessionStorage
                    sessionStorage.setItem('navigateToCompany', record.company);
                    sessionStorage.setItem('navigateToDepartment', record.department);
                    sessionStorage.setItem('highlightEmployee', record.employeeEmail);
                    sessionStorage.setItem('forceNavigation', 'true'); // Force navigation even if no employees found initially
                    
                    console.log('🚀 Navigating to employee page with parameters:', {
                        company: record.company,
                        department: record.department,
                        email: record.employeeEmail
                    });
                    
                    // Redirect to employee page
                    window.location.href = 'employee.php';
                }
            }, 3500);
            
        } catch (error) {
            console.error('❌ Error completing onboarding:', error);
            showToast(`❌ Error: ${error.message}`, 'error');
            
            // Reload data to ensure UI is in sync
            await loadOnboardingData();
            applyOnboardFilters();
            
            // Provide more helpful error message
            setTimeout(() => {
                alert(`❌ Error completing onboarding:\n\n${error.message}\n\nPossible causes:\n• Company or department doesn't exist in the system\n• Database connection issue\n• Missing required information (company_id, department_id)\n\nPlease check the employee details and try again, or contact your system administrator.`);
            }, 500);
        }
    }

    function approveClearance(id) {
        const record = window.exitRecords.find(r => r.id === id);
        if (record) {
            record.status = 'Cleared';
            record.clearanceApprovedBy = 'Monica White';
            renderExitTable(window.exitRecords);
            showToast(`Exit clearance approved!`, 'success');
        }
    }

    function viewResignationLetter(id) {
        showToast('Opening resignation letter...', 'info');
    }

    function exportData() {
        showToast('Exporting data...', 'info');
    }

    function showToast(message, type = 'info') {
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

    // Remove the complex recruitment welcome banner styles since we're using inline styles now
    // Keep only the essential animations
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    `;
    document.head.appendChild(style);

    // Initialize
    (async function() {
        // Make functions globally accessible
        window.toggleCompletedRecords = toggleCompletedRecords;
        window.markComplete = markComplete;
        
        // Debug helper function
        window.checkOnboardingData = function() {
            console.log('📊 Onboarding Records Summary:');
            console.log('Total records:', window.onboardRecords.length);
            console.table(window.onboardRecords.map(r => ({
                ID: r.id,
                Name: r.employeeName,
                Company: r.company || '(empty)',
                Department: r.department || '(empty)',
                Position: r.position || '(empty)',
                Progress: r.progress
            })));
            
            // Check for placeholder values
            const placeholderValues = ['To be assigned', 'TBA', 'Not assigned', 'Pending', ''];
            const recordsWithPlaceholders = window.onboardRecords.filter(r => {
                const hasCompanyPlaceholder = !r.company || placeholderValues.some(val => 
                    r.company.toLowerCase().includes(val.toLowerCase())
                );
                const hasDeptPlaceholder = !r.department || placeholderValues.some(val => 
                    r.department.toLowerCase().includes(val.toLowerCase())
                );
                return hasCompanyPlaceholder || hasDeptPlaceholder;
            });
            
            if (recordsWithPlaceholders.length > 0) {
                console.warn('⚠️ Records with placeholder values:', recordsWithPlaceholders.length);
                console.table(recordsWithPlaceholders.map(r => ({
                    ID: r.id,
                    Name: r.employeeName,
                    Company: r.company || '(empty)',
                    Department: r.department || '(empty)'
                })));
            } else {
                console.log('✅ All records have valid company and department assignments');
            }
        };
        
        console.log('💡 Debug helper available: Run checkOnboardingData() in console to see all records');
        
        // Set active menu
        const navItems = document.querySelectorAll('.nav-item');
        navItems.forEach(item => {
            item.classList.remove('active');
            if (item.getAttribute('data-title') === 'Onboarding') {
                item.classList.add('active');
            }
        });

        // Load data from server
        await loadOnboardingData();

        // Initialize filtered arrays after data is loaded
        initializeFilteredArrays();

        // Render tables
        console.log('🎨 Initial render - onboard records:', window.onboardRecords.length, 'exit records:', window.exitRecords.length);
        
        // Filter out completed records for initial render (but keep Not Started and In Progress)
        const activeOnboardRecords = window.onboardRecords.filter(r => r.progress !== 'Completed');
        console.log('📋 Active onboarding records (excluding Completed):', activeOnboardRecords.length);
        console.log('📋 Active records:', activeOnboardRecords.map(r => ({ id: r.id, name: r.employeeName, progress: r.progress })));
        renderOnboardTable(activeOnboardRecords);
        renderExitTable(window.exitRecords);

        // Switch to appropriate tab if specified in URL
        const urlParams = new URLSearchParams(window.location.search);
        const tabParam = urlParams.get('tab');
        const highlightParam = urlParams.get('highlight');
        const fromParam = urlParams.get('from');
        const newOfferParam = urlParams.get('newOffer');
        
        if (tabParam === 'exit') {
            switchTab('exit');
        } else {
            switchTab('onboarding');
        }

        // Handle highlighting and welcome message for new onboarding records from recruitment
        if (highlightParam && fromParam === 'recruitment') {
            // Force show all records including "Not Started" when coming from recruitment
            showCompletedRecords = false; // Keep completed hidden
            
            // Show simple welcome message for new onboarding record
            showRecruitmentWelcome(highlightParam, newOfferParam);
            
            // Highlight the new onboarding record after a short delay
            setTimeout(() => {
                highlightOnboardingRecord(highlightParam);
            }, 500);
        }

        // Setup filter listeners
        document.getElementById('onboardSearchInput').addEventListener('keyup', applyOnboardFilters);
        document.getElementById('progressFilter').addEventListener('change', applyOnboardFilters);
        
        document.getElementById('exitSearchInput').addEventListener('keyup', applyExitFilters);
        document.getElementById('exitStatusFilter').addEventListener('change', applyExitFilters);

        console.log('✅ Onboarding & Exit Management loaded');
    })();

    // Function to highlight a specific onboarding record (simplified)
    function highlightOnboardingRecord(employeeId) {
        console.log('🎯 Highlighting onboarding record for employee ID:', employeeId);
        
        // Find the row with the matching employee ID in the onboard table
        const rows = document.querySelectorAll('.onboard-table tbody tr');
        let found = false;
        
        rows.forEach(row => {
            // Check if this row contains the employee we're looking for
            const rowEmployeeId = row.getAttribute('data-employee-id');
            const employeeIdCode = row.querySelector('code');
            
            if (rowEmployeeId === employeeId || (employeeIdCode && employeeIdCode.textContent === employeeId)) {
                found = true;
                highlightRow(row, employeeId);
            }
        });
        
        if (!found) {
            console.log('⚠️ Employee not found in onboarding table, will check again after data loads');
            // Try again after a delay in case data is still loading
            setTimeout(() => {
                const retryRows = document.querySelectorAll('.onboard-table tbody tr');
                retryRows.forEach(row => {
                    const rowEmployeeId = row.getAttribute('data-employee-id');
                    const employeeIdCode = row.querySelector('code');
                    
                    if (rowEmployeeId === employeeId || (employeeIdCode && employeeIdCode.textContent === employeeId)) {
                        highlightRow(row, employeeId);
                    }
                });
            }, 2000);
        }
    }
    
    // Helper function to apply simple highlight effect to a row
    function highlightRow(row, employeeId) {
        console.log('✨ Applying simple highlight effect to row for:', employeeId);
        
        // Add simple highlight effect
        row.style.background = '#fef3c7';
        row.style.border = '1px solid #f59e0b';
        row.style.borderRadius = '4px';
        
        // Scroll into view
        row.scrollIntoView({ behavior: 'smooth', block: 'center' });
        
        // Remove highlight after 5 seconds
        setTimeout(() => {
            row.style.background = '';
            row.style.border = '';
            row.style.borderRadius = '';
        }, 5000);
    }

    // Function to show simple welcome message for new onboarding record from recruitment
    function showRecruitmentWelcome(employeeId, offerId) {
        console.log('🎉 Showing recruitment welcome for:', employeeId, offerId);
        
        // Create simple welcome banner
        const welcomeBanner = document.createElement('div');
        welcomeBanner.id = 'recruitmentWelcome';
        welcomeBanner.style.cssText = `
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: 1px solid #059669;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        `;
        welcomeBanner.innerHTML = `
            <div style="display: flex; align-items: center; gap: 12px;">
                <i class="fas fa-check-circle" style="font-size: 24px;"></i>
                <div>
                    <div style="font-weight: 600; margin-bottom: 4px;">✅ Offer Accepted Successfully!</div>
                    <div style="font-size: 12px; opacity: 0.9;">New employee <strong>${employeeId}</strong> has been added to the onboarding list below.</div>
                </div>
            </div>
            <button onclick="hideRecruitmentWelcome()" style="background: rgba(255,255,255,0.2); border: none; color: white; cursor: pointer; font-size: 18px; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        // Insert banner at the top of the main content
        const mainContent = document.querySelector('.main-content');
        const pageHeader = document.querySelector('.page-header');
        mainContent.insertBefore(welcomeBanner, pageHeader.nextSibling);
        
        // Auto-hide after 10 seconds
        setTimeout(() => {
            hideRecruitmentWelcome();
        }, 10000);
    }

    // Function to assign default onboarding tasks to a new employee
    function assignDefaultTasks(employeeId) {
        console.log('📋 Assigning default tasks to employee:', employeeId);
        
        // Find the onboarding record
        const record = window.onboardRecords.find(r => r.employeeId === employeeId);
        if (!record) {
            console.error('❌ Onboarding record not found for employee:', employeeId);
            showToast('Employee record not found', 'error');
            return;
        }
        
        // Default onboarding tasks
        const defaultTasks = [
            { text: 'Complete employment forms and documentation', completed: false },
            { text: 'IT equipment setup and system access', completed: false },
            { text: 'Office tour and workspace assignment', completed: false },
            { text: 'HR orientation and company policies', completed: false },
            { text: 'Department introduction and team meeting', completed: false },
            { text: 'Job-specific training and procedures', completed: false },
            { text: 'Benefits enrollment and setup', completed: false },
            { text: 'Security badge and access card setup', completed: false }
        ];
        
        // Update the record with default tasks
        record.tasks = defaultTasks;
        record.progress = 'In Progress';
        record.notes = record.notes + ' - Default onboarding tasks assigned automatically.';
        
        // Update the UI
        renderOnboardTable(window.onboardRecords);
        
        // Show success message
        showToast(`Default onboarding tasks assigned to ${record.employeeName}!`, 'success');
        
        // Highlight the updated record
        setTimeout(() => {
            highlightOnboardingRecord(employeeId);
        }, 500);
        
        console.log('✅ Default tasks assigned successfully');
    }

    // Legacy function name for compatibility
    function highlightAcceptedOffer(employeeId) {
        highlightOnboardingRecord(employeeId);
    }

    // Hide recruitment welcome banner
    function hideRecruitmentWelcome() {
        const welcomeBanner = document.getElementById('recruitmentWelcome');
        if (welcomeBanner) {
            welcomeBanner.style.display = 'none';
        }
    }
    
    // Debug function to test API connectivity
    async function debugAcceptedOffers() {
        console.log('🐛 DEBUG: Testing API connectivity...');
        
        try {
            // Test the offers API
            const apiUrl = '../../api/recruitment/offers.php';
            console.log('🌐 Testing API URL:', apiUrl);
            
            const response = await fetch(apiUrl);
            console.log('📡 Response status:', response.status);
            console.log('📡 Response headers:', Object.fromEntries(response.headers.entries()));
            
            if (response.ok) {
                const data = await response.json();
                console.log('📥 API Response:', data);
                
                if (data.success && Array.isArray(data.data)) {
                    const acceptedOffers = data.data.filter(offer => offer.offer_status === 'Accepted');
                    console.log('✅ Total offers:', data.data.length);
                    console.log('✅ Accepted offers:', acceptedOffers.length);
                    console.log('📋 Accepted offers data:', acceptedOffers);
                    
                    showToast(`API Test: Found ${acceptedOffers.length} accepted offers out of ${data.data.length} total`, 'info');
                } else {
                    console.error('❌ Invalid API response format');
                    showToast('API Test: Invalid response format', 'warning');
                }
            } else {
                const errorText = await response.text();
                console.error('❌ API Error:', errorText);
                showToast(`API Test: HTTP ${response.status} - ${errorText}`, 'warning');
            }
        } catch (error) {
            console.error('💥 Debug error:', error);
            showToast(`API Test: Error - ${error.message}`, 'warning');
        }
    }
</script>

</body>
</html>