<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
/**
 * employee.php
 * Employee Hub - Hierarchical drill-down: Company > Department > Employee
 */

$pageTitle = "Employee";
$activeMenu = "Employee Hub";
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

        .btn-danger {
            background: #ef4444;
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        /* Tabs */
        .tabs-container {
            margin-bottom: 20px;
        }

        .tabs {
            display: flex;
            gap: 4px;
            background: rgba(255,255,255,0.5);
            padding: 4px;
            border-radius: 30px;
            width: fit-content;
            backdrop-filter: blur(4px);
        }

        .tab {
            padding: 8px 20px;
            border-radius: 26px;
            font-weight: 500;
            font-size: 13px;
            color: #64748b;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            background: transparent;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .tab i {
            font-size: 13px;
        }

        .tab:hover {
            color: #1e293b;
            background: rgba(255,255,255,0.6);
        }

        .tab.active {
            background: white;
            color: #4f46e5;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
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
            align-items: center;
            flex-wrap: wrap;
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

        .table-header h3 i {
            color: #4f46e5;
            margin-right: 6px;
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

        .employee-table {
            width: 100%;
            border-collapse: collapse;
        }

        .employee-table th {
            text-align: left;
            padding: 12px 8px;
            font-weight: 600;
            color: #475569;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #e2e8f0;
        }

        .employee-table td {
            padding: 12px 8px;
            border-bottom: 1px solid #f1f5f9;
            color: #1e293b;
            font-size: 13px;
        }

        .employee-table tbody tr:hover {
            background: rgba(79, 70, 229, 0.03);
        }

        .clickable-row {
            cursor: pointer;
        }

        .emp-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .emp-avatar-sm {
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

        .emp-info h4 {
            font-weight: 600;
            font-size: 13px;
            margin-bottom: 2px;
        }

        .emp-info p {
            font-size: 11px;
            color: #64748b;
        }

        .company-cell, .department-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .company-icon, .department-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: white;
        }

        .company-info h4, .department-info h4 {
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 2px;
        }

        .company-info p, .department-info p {
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

        .badge-secondary {
            background: #f1f5f9;
            color: #64748b;
        }

        .badge-danger {
            background: #fee2e2;
            color: #dc2626;
        }

        .badge-purple {
            background: #f3e8ff;
            color: #9333ea;
        }

        .badge-orange {
            background: #ffedd5;
            color: #ea580c;
        }

        .badge-teal {
            background: #ccfbf1;
            color: #0d9488;
        }

        .action-icons {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .action-icons i {
            color: #94a3b8;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 14px;
            padding: 4px;
            border-radius: 6px;
        }

        .action-icons i:hover {
            transform: scale(1.1);
        }

        .action-icons .fa-eye:hover {
            color: #3b82f6;
            background: #eff6ff;
        }

        .action-icons .fa-edit:hover {
            color: #4f46e5;
            background: #eef2ff;
        }

        .action-icons .fa-envelope:hover {
            color: #10b981;
            background: #ecfdf5;
        }

        .action-icons .fa-user-slash:hover {
            color: #ef4444;
            background: #fef2f2;
        }

        .action-icons .fa-rotate-left:hover {
            color: #10b981;
            background: #ecfdf5;
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

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #64748b;
        }

        .empty-state i {
            font-size: 40px;
            color: #cbd5e1;
            margin-bottom: 16px;
        }

        .empty-state h4 {
            font-size: 15px;
            font-weight: 500;
            margin-bottom: 8px;
            color: #1e293b;
        }

        /* Termination Modal Specific Styles */
        .termination-form .form-group {
            margin-bottom: 18px;
        }

        .termination-form .form-group label {
            display: block;
            font-weight: 500;
            color: #1e293b;
            margin-bottom: 6px;
            font-size: 13px;
        }

        .termination-form .required-star {
            color: #ef4444;
            margin-left: 2px;
        }

        .radio-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .radio-option {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 12px 14px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s;
            background: white;
        }

        .radio-option:hover {
            border-color: #4f46e5;
            background: #f8fafc;
        }

        .radio-option.selected {
            border-color: #4f46e5;
            background: #eef2ff;
        }

        .radio-option input[type="radio"] {
            margin-top: 2px;
            accent-color: #4f46e5;
            width: 16px;
            height: 16px;
            cursor: pointer;
        }

        .radio-content {
            flex: 1;
        }

        .radio-title {
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 2px;
            font-size: 13px;
        }

        .radio-desc {
            font-size: 11px;
            color: #64748b;
        }

        .other-input-wrapper {
            margin-top: 8px;
            display: none;
        }

        .other-input-wrapper.show {
            display: block;
        }

        .other-input-wrapper input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            font-size: 13px;
            outline: none;
            transition: border-color 0.2s;
        }

        .other-input-wrapper input:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .employee-summary-card {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 16px;
            background: #f8fafc;
            border-radius: 14px;
            margin-bottom: 20px;
            border: 1px solid #e2e8f0;
        }

        .summary-avatar {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 18px;
            color: white;
        }

        .summary-info h4 {
            font-size: 15px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 2px;
        }

        .summary-info p {
            font-size: 12px;
            color: #64748b;
        }

        .modal-footer {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 24px;
            padding-top: 18px;
            border-top: 1px solid #ebebea;
        }

        .section-title-sm {
            font-size: 13px;
            font-weight: 600;
            margin: 16px 0 12px;
            padding-bottom: 6px;
            border-bottom: 1px solid #e2e8f0;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .section-title-sm i {
            color: #4f46e5;
            width: 18px;
        }

        .section-title-sm:first-of-type {
            margin-top: 0;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
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

        /* Additional styles for view employee modal */
        .profile-header {
            display: flex;
            align-items: center;
            gap: 24px;
            margin-bottom: 24px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .profile-avatar-lg {
            width: 100px;
            height: 100px;
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 36px;
            color: white;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }
        
        .profile-info h3 {
            font-size: 20px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 4px;
        }
        
        .profile-info .position {
            color: #64748b;
            font-size: 14px;
            margin-bottom: 8px;
        }
        
        .detail-section {
            margin-bottom: 24px;
        }
        
        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px 20px;
        }
        
        .detail-item {
            display: flex;
            flex-direction: column;
        }
        
        .detail-label {
            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        
        .detail-value {
            font-size: 14px;
            color: #1e293b;
            font-weight: 500;
        }

        .btn-info {
            background: #0ea5e9;
            color: white;
        }

        .btn-info:hover {
            background: #0284c7;
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 14px;
            }
            .employee-table {
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

    <!-- MAIN CONTENT - EMPLOYEE HUB -->
    <main class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <h1>Employee Management</h1>
            <!-- Debug buttons - Remove after testing -->
            <div style="display: flex; gap: 8px;">
                <button class="btn btn-secondary" onclick="console.log('Functions check:', typeof viewEmployee, typeof editEmployee, typeof openModal); alert('viewEmployee: ' + typeof viewEmployee + ', editEmployee: ' + typeof editEmployee + ', openModal: ' + typeof openModal);" style="font-size: 10px; padding: 4px 8px;">
                    🔍 Check Functions
                </button>
                <button class="btn btn-secondary" onclick="if(window.employees && window.employees.length > 0) { console.log('Calling viewEmployee with ID:', window.employees[0].id); viewEmployee(window.employees[0].id); } else { alert('No employees loaded'); }" style="font-size: 10px; padding: 4px 8px;">
                    🔍 Test View
                </button>
                <button class="btn btn-secondary" onclick="if(window.employees && window.employees.length > 0) { console.log('Calling editEmployee with ID:', window.employees[0].id); editEmployee(window.employees[0].id); } else { alert('No employees loaded'); }" style="font-size: 10px; padding: 4px 8px;">
                    ✏️ Test Edit
                </button>
            </div>
        </div>

        <!-- Tabs -->
        <div class="tabs-container">
            <div class="tabs">
                <button class="tab active" onclick="switchTab('active')">
                    <i class="fas fa-user-check"></i> Active Employees
                </button>
                <button class="tab" onclick="switchTab('terminated')">
                    <i class="fas fa-user-slash"></i> Terminated Employees
                </button>
            </div>
        </div>

        <!-- Active Employees View -->
        <div id="activeEmployeesView">
            <div id="activeEmployeeLevelContent"></div>
        </div>

        <!-- Terminated Employees View -->
        <div id="terminatedEmployeesView" style="display: none;">
            <div id="terminatedEmployeeLevelContent"></div>
        </div>
    </main>
</div>

<?php 
// Include modal components
include 'modals/modal-wrapper.php';
include 'modals/employee-modal/modal-employee-helpers.php';
include 'modals/employee-modal/modal-view-employee-new.php';
include 'modals/employee-modal/modal-edit-employee-new.php';
include 'modals/employee-modal/modal-add-employee.php';
?>

<!-- Debug Script - Check if modal functions are loaded -->
<script>
// Wait for DOM to be ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔍 DOM Loaded - Checking modal functions availability:');
    console.log('  typeof viewEmployee:', typeof viewEmployee);
    console.log('  typeof editEmployee:', typeof editEmployee);
    console.log('  typeof openAddEmployeeModal:', typeof openAddEmployeeModal);
    console.log('  typeof openModal:', typeof openModal);
    console.log('  typeof closeModal:', typeof closeModal);

    // If functions are not defined, log an error
    if (typeof viewEmployee === 'undefined') {
        console.error('❌ viewEmployee function is NOT defined!');
    } else {
        console.log('✅ viewEmployee is available');
    }
    if (typeof editEmployee === 'undefined') {
        console.error('❌ editEmployee function is NOT defined!');
    } else {
        console.log('✅ editEmployee is available');
    }
    if (typeof openAddEmployeeModal === 'undefined') {
        console.error('❌ openAddEmployeeModal function is NOT defined!');
    } else {
        console.log('✅ openAddEmployeeModal is available');
    }

    // Test if we can access window.employees
    setTimeout(() => {
        console.log('🔍 Checking data availability after 2 seconds:');
        console.log('  window.employees:', window.employees ? window.employees.length + ' employees' : 'undefined');
        console.log('  window.employeeCompanies:', window.employeeCompanies ? window.employeeCompanies.length + ' companies' : 'undefined');
        console.log('  window.employeeDepartments:', window.employeeDepartments ? window.employeeDepartments.length + ' departments' : 'undefined');
        console.log('  window.employeeJobs:', window.employeeJobs ? window.employeeJobs.length + ' jobs' : 'undefined');
        
        // Try to manually call viewEmployee if data is loaded
        if (window.employees && window.employees.length > 0) {
            console.log('✅ Data is loaded. You can now test the modals.');
            console.log('  First employee ID:', window.employees[0].id);
        }
    }, 2000);
});
</script>

<!-- Termination Modal Script -->
<script>
    // Currently selected employee for termination
    let selectedTerminationEmployee = null;

    // Open termination modal
    function openTerminateEmployeeModal(employeeId) {
        const emp = window.employees.find(e => e.id === employeeId);
        if (!emp) return;
        
        selectedTerminationEmployee = emp;
        
        // Set today's date as default
        const today = new Date().toISOString().split('T')[0];
        
        const content = `
            <div class="termination-form">
                <!-- Employee Summary -->
                <div class="employee-summary-card">
                    <img src="${emp.profilePhoto || '/3ME/assets/images/default-avatar.png'}" style="width: 52px; height: 52px; border-radius: 14px; object-fit: cover;" />
                    <div class="summary-info">
                        <h4>${emp.name}</h4>
                        <p>${emp.position} • ${emp.department}</p>
                    </div>
                </div>
                
                <form id="terminationForm" onsubmit="confirmTermination(event)">
                    <!-- Termination Reason -->
                    <div class="section-title-sm">
                        <i class="fas fa-exclamation-triangle"></i> Termination Reason
                    </div>
                    
                    <div class="form-group">
                        <label>Select Reason <span class="required-star">*</span></label>
                        <div class="radio-group" id="terminationReasonGroup">
                            <label class="radio-option" onclick="selectRadioOption(this, 'abandonment')">
                                <input type="radio" name="terminationReason" value="abandonment" required>
                                <div class="radio-content">
                                    <div class="radio-title">Abandonment of Work</div>
                                    <div class="radio-desc">Employee has been absent without notice for consecutive days</div>
                                </div>
                            </label>
                            
                            <label class="radio-option" onclick="selectRadioOption(this, 'just-cause')">
                                <input type="radio" name="terminationReason" value="just-cause">
                                <div class="radio-content">
                                    <div class="radio-title">Termination for Just Causes (Dismissal)</div>
                                    <div class="radio-desc">Serious misconduct, willful disobedience, gross neglect, fraud, etc.</div>
                                </div>
                            </label>
                            
                            <label class="radio-option" onclick="selectRadioOption(this, 'authorized-cause')">
                                <input type="radio" name="terminationReason" value="authorized-cause">
                                <div class="radio-content">
                                    <div class="radio-title">Termination for Authorized Causes</div>
                                    <div class="radio-desc">Retrenchment, redundancy, closure, or disease</div>
                                </div>
                            </label>
                            
                            <label class="radio-option" onclick="selectRadioOption(this, 'disease')">
                                <input type="radio" name="terminationReason" value="disease">
                                <div class="radio-content">
                                    <div class="radio-title">Termination for Disease</div>
                                    <div class="radio-desc">Employee is unable to continue work due to health condition</div>
                                </div>
                            </label>
                            
                            <label class="radio-option" onclick="selectRadioOption(this, 'end-contract')">
                                <input type="radio" name="terminationReason" value="end-contract">
                                <div class="radio-content">
                                    <div class="radio-title">End of Contract</div>
                                    <div class="radio-desc">Probationary period ended or fixed-term contract expired</div>
                                </div>
                            </label>
                            
                            <label class="radio-option" onclick="selectRadioOption(this, 'other')">
                                <input type="radio" name="terminationReason" value="other">
                                <div class="radio-content">
                                    <div class="radio-title">Other (Please Specify)</div>
                                    <div class="radio-desc">Any other reason not listed above</div>
                                </div>
                            </label>
                        </div>
                        
                        <div class="other-input-wrapper" id="otherReasonWrapper">
                            <input type="text" id="otherReasonInput" placeholder="Please specify the termination reason...">
                        </div>
                    </div>
                    
                    <!-- Termination Details -->
                    <div class="section-title-sm">
                        <i class="fas fa-calendar"></i> Termination Details
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Termination Date <span class="required-star">*</span></label>
                            <input type="date" id="terminationDate" value="${today}" required>
                        </div>
                        <div class="form-group">
                            <label>Last Working Day</label>
                            <input type="date" id="lastWorkingDay" value="${today}">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Remarks / Notes</label>
                        <textarea id="terminationRemarks" placeholder="Additional notes or comments..."></textarea>
                    </div>
                    
                    <!-- Modal Footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="closeModal()">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-user-slash"></i> Confirm Termination
                        </button>
                    </div>
                </form>
            </div>
        `;
        
        openModal('Terminate Employment', content);
    }

    // Select radio option with visual feedback
    function selectRadioOption(element, value) {
        document.querySelectorAll('.radio-option').forEach(opt => {
            opt.classList.remove('selected');
        });
        element.classList.add('selected');
        element.querySelector('input[type="radio"]').checked = true;
        
        const otherWrapper = document.getElementById('otherReasonWrapper');
        if (value === 'other') {
            otherWrapper.classList.add('show');
            document.getElementById('otherReasonInput').setAttribute('required', 'required');
        } else {
            otherWrapper.classList.remove('show');
            document.getElementById('otherReasonInput').removeAttribute('required');
        }
    }

    // Confirm termination
    function confirmTermination(event) {
        event.preventDefault();
        
        if (!selectedTerminationEmployee) {
            closeModal();
            return;
        }
        
        const selectedRadio = document.querySelector('input[name="terminationReason"]:checked');
        if (!selectedRadio) {
            showToast('Please select a termination reason', 'error');
            return;
        }
        
        const reasonCode = selectedRadio.value;
        let reasonDisplay = getReasonDisplayName(reasonCode);
        let otherReasonSpecified = null;
        
        if (reasonCode === 'other') {
            otherReasonSpecified = document.getElementById('otherReasonInput').value.trim();
            if (!otherReasonSpecified) {
                showToast('Please specify the termination reason', 'error');
                return;
            }
            reasonDisplay = `Other: ${otherReasonSpecified}`;
        }
        
        const terminationDate = document.getElementById('terminationDate').value;
        if (!terminationDate) {
            showToast('Please select termination date', 'error');
            return;
        }
        
        const lastWorkingDay = document.getElementById('lastWorkingDay').value || terminationDate;
        const remarks = document.getElementById('terminationRemarks').value;
        
        const empIndex = window.employees.findIndex(e => e.id === selectedTerminationEmployee.id);
        if (empIndex === -1) {
            closeModal();
            return;
        }
        
        const terminatedEmployee = window.employees.splice(empIndex, 1)[0];
        
        terminatedEmployee.terminationReason = reasonDisplay;
        terminatedEmployee.terminationReasonCode = reasonCode;
        terminatedEmployee.terminationDate = formatDate(terminationDate);
        terminatedEmployee.lastWorkingDay = formatDate(lastWorkingDay);
        terminatedEmployee.terminationRemarks = remarks;
        terminatedEmployee.terminatedAt = new Date().toISOString();
        if (otherReasonSpecified) {
            terminatedEmployee.otherReasonSpecified = otherReasonSpecified;
        }
        
        window.terminatedEmployees.push(terminatedEmployee);
        
        closeModal();
        showToast(`${terminatedEmployee.name} has been terminated`, 'success');
        
        // Update UI
        if (currentLevel === 'company') {
            renderCompanyLevel();
        } else if (currentLevel === 'department') {
            renderDepartmentLevel();
        } else if (currentLevel === 'employee') {
            renderEmployeeLevel();
        }
        
        setTimeout(() => {
            if (confirm('Employee terminated successfully. Would you like to view the terminated employees list?')) {
                const tabs = document.querySelectorAll('.tab');
                if (tabs[1]) {
                    tabs[1].click();
                }
            }
        }, 300);
    }

    function getReasonDisplayName(reasonCode) {
        const names = {
            'abandonment': 'Abandonment of Work',
            'just-cause': 'Just Causes (Dismissal)',
            'authorized-cause': 'Authorized Causes',
            'disease': 'Disease',
            'end-contract': 'End of Contract',
            'other': 'Other'
        };
        return names[reasonCode] || reasonCode;
    }

    window.openTerminateEmployeeModal = openTerminateEmployeeModal;
    window.selectRadioOption = selectRadioOption;
    window.confirmTermination = confirmTermination;
</script>

<script>
    // ---------- GLOBAL STATE ----------
    window.employees = [];
    window.terminatedEmployees = [];
    window.companies = [];
    window.departments = [];

    // Load companies from database
    async function loadCompanies() {
        try {
            const response = await fetch('../../api/settings/settings_api.php?action=list_companies');
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const result = await response.json();
            console.log('Companies API response:', result);
            
            if (result.success && Array.isArray(result.data)) {
                window.companies = result.data;
                console.log('✅ Companies loaded from database:', window.companies.length);
            } else {
                console.error('Failed to load companies:', result);
                window.companies = [];
            }
        } catch (error) {
            console.error('Error loading companies:', error);
            window.companies = [];
        }
    }

    // Load departments from database
    async function loadDepartments() {
        try {
            const response = await fetch('../../api/settings/settings_api.php?action=list_departments');
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const result = await response.json();
            console.log('Departments API response:', result);
            
            if (result.success && Array.isArray(result.data)) {
                window.departments = result.data;
                console.log('✅ Departments loaded from database:', window.departments.length);
            } else {
                console.error('Failed to load departments:', result);
                window.departments = [];
            }
        } catch (error) {
            console.error('Error loading departments:', error);
            window.departments = [];
        }
    }

    // Load jobs from database
    async function loadJobs() {
        try {
            const response = await fetch('../../api/settings/settings_api.php?action=list_jobs');
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const result = await response.json();
            console.log('Jobs API response:', result);
            
            if (result.success && Array.isArray(result.data)) {
                window.employeeJobs = result.data;
                console.log('✅ Jobs loaded from database:', window.employeeJobs.length);
            } else {
                console.error('Failed to load jobs:', result);
                window.employeeJobs = [];
            }
        } catch (error) {
            console.error('Error loading jobs:', error);
            window.employeeJobs = [];
        }
    }

    // Load data from server
    async function loadEmployeeData() {
        try {
            const response = await fetch('../../api/employees/employees.php');
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const text = await response.text();
            console.log('Raw API response:', text.substring(0, 500));
            
            let result;
            try {
                result = JSON.parse(text);
            } catch (e) {
                console.error('JSON parse error:', e);
                console.error('Response text:', text);
                throw new Error('Invalid JSON response from API');
            }
            
            if (result.success) {
                const employeeData = result.data || [];
                console.log('Employee data from API:', employeeData);
                
                window.employees = employeeData.map(emp => ({
                    id: emp.id,
                    employeeId: emp.employee_id,
                    firstname: emp.firstname,
                    middlename: emp.middlename || '',
                    surname: emp.surname,
                    suffix: emp.suffix || '',
                    name: `${emp.firstname} ${emp.middlename ? emp.middlename + ' ' : ''}${emp.surname}${emp.suffix ? ' ' + emp.suffix : ''}`,
                    email: emp.email,
                    phone: emp.phone || '',
                    position: emp.position || emp.job,
                    job: emp.job || emp.position,
                    job_id: emp.job_id,           // Preserve job_id foreign key
                    department: emp.department,
                    department_id: emp.department_id, // Preserve department_id foreign key
                    company: emp.company,
                    company_id: emp.company_id,      // Preserve company_id foreign key
                    status: emp.status,
                    joinDate: formatDate(emp.join_date),
                    salary: parseFloat(emp.salary) || 0,
                    address: emp.address || '',
                    emergencyContactName: emp.emergency_contact_name || '',
                    emergencyContactPhone: emp.emergency_contact_phone || '',
                    emergencyContactRelation: emp.emergency_contact_relation || '',
                    level: emp.level || '',
                    type: emp.type || '',
                    startDate: emp.start_date ? formatDate(emp.start_date) : '',
                    endDate: emp.end_date ? formatDate(emp.end_date) : '',
                    duration: emp.duration || '',
                    sss: emp.sss || '',
                    philhealth: emp.philhealth || '',
                    pagibig: emp.pagibig || '',
                    tin: emp.tin || '',
                    remarks: emp.remarks || '',
                    blocklist: emp.blocklist || false,
                    avatar: emp.avatar || generateAvatar(`${emp.firstname} ${emp.surname}`),
                    color: emp.color || generateColor(`${emp.firstname} ${emp.surname}`),
                    profilePhoto: emp.profile_photo || null,
                    profilePhotoFilename: emp.profile_photo_filename || null,
                    isIncomplete: false // Will be set below
                }));
                
                // Check for incomplete profiles
                window.employees.forEach(emp => {
                    emp.isIncomplete = isEmployeeProfileIncomplete(emp);
                });
                
                console.log('✅ Employee data loaded from server:', window.employees.length, 'employees');
                const incompleteCount = window.employees.filter(e => e.isIncomplete).length;
                if (incompleteCount > 0) {
                    console.log(`⚠️ ${incompleteCount} employee(s) have incomplete profiles`);
                }
                
                if (window.employees.length === 0) {
                    console.log('ℹ️ No employees found in database');
                }
            } else {
                console.error('API returned error:', result);
                window.employees = [];
                if (result.message) {
                    console.error('Error message:', result.message);
                    showToast('Failed to load employee data: ' + result.message, 'error');
                }
            }
        } catch (error) {
            console.error('Error loading employee data:', error);
            window.employees = [];
            // Only show toast for actual connection errors
            showToast('Error connecting to server: ' + error.message, 'error');
        }
    }
    
    // Helper function to generate avatar initials
    function generateAvatar(name) {
        const parts = name.split(' ').filter(p => p);
        if (parts.length >= 2) {
            return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
        }
        return parts[0] ? parts[0].substring(0, 2).toUpperCase() : 'NA';
    }
    
    // Helper function to generate color
    function generateColor(name) {
        const colors = [
            'linear-gradient(145deg, #4f46e5, #7c3aed)',
            'linear-gradient(145deg, #ef4444, #f87171)',
            'linear-gradient(145deg, #10b981, #34d399)',
            'linear-gradient(145deg, #f59e0b, #fbbf24)',
            'linear-gradient(145deg, #8b5cf6, #a78bfa)',
            'linear-gradient(145deg, #06b6d4, #67e8f9)',
            'linear-gradient(145deg, #ec4899, #f472b6)',
            'linear-gradient(145deg, #14b8a6, #5eead4)'
        ];
        const index = name.length % colors.length;
        return colors[index];
    }
    
    // Helper function to check if employee profile is incomplete
    function isEmployeeProfileIncomplete(emp) {
        // Check required fields from onboarding
        const requiredFields = [
            'phone', 'address', 'emergencyContactName', 'emergencyContactPhone',
            'sss', 'philhealth', 'pagibig', 'tin'
        ];
        
        for (const field of requiredFields) {
            if (!emp[field] || emp[field].trim() === '') {
                return true;
            }
        }
        
        return false;
    }

    // Make loadEmployeeData globally available so modals can call it
    window.loadEmployeeData = loadEmployeeData;


    // Navigation state
    let currentLevel = 'company'; // 'company', 'department', 'employee'
    let currentTab = 'active';
    let selectedCompany = null;
    let selectedDepartment = null;

    // Pagination variables
    let currentCompanyPage = 1;
    let currentDepartmentPage = 1;
    let currentEmployeePage = 1;
    let currentTerminatedPage = 1;
    let itemsPerPage = 8;
    let filteredCompanies = [];
    let filteredDepartments = [];
    let filteredEmployees = [];
    let filteredTerminatedEmployees = [];

    // Search terms
    let companySearchTerm = '';
    let departmentSearchTerm = '';
    let employeeSearchTerm = '';
    let terminatedSearchTerm = '';

    // ---------- HELPER FUNCTIONS ----------
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    }

    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>]/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;' })[m] || m);
    }

    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        const bgColor = type === 'success' ? '#1e293b' : '#ef4444';
        toast.style.cssText = `
            position: fixed; bottom: 24px; right: 24px;
            background: ${bgColor}; color: white;
            padding: 12px 20px; border-radius: 12px;
            font-size: 13px; z-index: 10001;
            animation: slideIn 0.3s ease;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        `;
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transition = 'opacity 0.3s';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // ---------- STATS FUNCTIONS ----------
    function getActiveStats(data) {
        const total = data.length;
        const active = data.filter(e => e.status === 'Active').length;
        const remote = data.filter(e => e.status === 'Remote').length;
        const onLeave = data.filter(e => e.status === 'On Leave').length;
        const probation = data.filter(e => e.status === 'Probation').length;
        return { total, active, remote, onLeave, probation };
    }

    function getTerminatedStats() {
        const total = window.terminatedEmployees.length;
        const thisMonth = new Date().getMonth();
        const thisYear = new Date().getFullYear();
        const thisMonthCount = window.terminatedEmployees.filter(emp => {
            const termDate = new Date(emp.terminatedAt);
            return termDate.getMonth() === thisMonth && termDate.getFullYear() === thisYear;
        }).length;
        return { total, thisMonthCount };
    }

    function getReasonBadgeClass(reasonCode) {
        const classes = {
            'abandonment': 'badge-danger',
            'just-cause': 'badge-danger',
            'authorized-cause': 'badge-warning',
            'disease': 'badge-purple',
            'end-contract': 'badge-teal',
            'other': 'badge-secondary'
        };
        return classes[reasonCode] || 'badge-secondary';
    }

    // ---------- NAVIGATION FUNCTIONS ----------
    function navigateToLevel(level, company = null, department = null) {
        currentLevel = level;
        selectedCompany = company;
        selectedDepartment = department;
        
        // Reset pagination
        currentCompanyPage = 1;
        currentDepartmentPage = 1;
        currentEmployeePage = 1;
        
        // Reset search terms when navigating
        companySearchTerm = '';
        departmentSearchTerm = '';
        employeeSearchTerm = '';
        
        if (currentTab === 'active') {
            if (level === 'company') {
                renderCompanyLevel();
            } else if (level === 'department') {
                renderDepartmentLevel();
            } else if (level === 'employee') {
                renderEmployeeLevel();
            }
        } else {
            renderTerminatedLevel();
        }
    }

    function navigateBack() {
        if (currentLevel === 'department') {
            navigateToLevel('company');
        } else if (currentLevel === 'employee') {
            navigateToLevel('department', selectedCompany, null);
        }
    }

    // ---------- RENDER COMPANY LEVEL ----------
    function renderCompanyLevel() {
        // Use companies from database instead of deriving from employees
        const companyData = window.companies.map(company => {
            const emps = window.employees.filter(e => e.company === company.name);
            const stats = getActiveStats(emps);
            
            // Get unique departments for this company from database
            const companyDepts = window.departments.filter(d => d.companyId === company.id);
            
            return {
                id: company.id,
                name: company.name,
                employeeCount: emps.length,
                activeCount: stats.active,
                departments: companyDepts.length
            };
        });

        // Apply search filter
        filteredCompanies = companyData.filter(c => 
            c.name.toLowerCase().includes(companySearchTerm.toLowerCase())
        );

        const stats = getActiveStats(window.employees);
        const start = (currentCompanyPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const paginatedData = filteredCompanies.slice(start, end);

        const html = `
            <div class="filter-bar">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search companies..." id="companySearchInput" value="${escapeHtml(companySearchTerm)}">
                </div>
            </div>

            <div class="stats-mini">
                <div class="stat-mini-card">
                    <i class="fas fa-building"></i>
                    <span>${window.companies.length}</span> <small>Companies</small>
                </div>
                <div class="stat-mini-card">
                    <i class="fas fa-users"></i>
                    <span>${stats.total}</span> <small>Total Employees</small>
                </div>
                <div class="stat-mini-card">
                    <i class="fas fa-user-check"></i>
                    <span>${stats.active}</span> <small>Active</small>
                </div>
                <div class="stat-mini-card">
                    <i class="fas fa-umbrella-beach"></i>
                    <span>${stats.onLeave}</span> <small>On Leave</small>
                </div>
                <div class="header-actions">
                    <button class="btn btn-primary" onclick="openAddEmployeeModal()"><i class="fas fa-plus"></i> Add Employee</button>
                </div>
            </div>

            <div class="table-card">
                <div class="table-header">
                    <h3>Company List</h3>
                </div>
                <table class="employee-table">
                    <thead>
                        <tr>
                            <th>Company</th>
                            <th>Departments</th>
                            <th>Total Employees</th>
                            <th>Active Employees</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${paginatedData.length > 0 ? paginatedData.map(company => {
                            const colors = ['#4f46e5', '#0ea5e9', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#14b8a6'];
                            const colorIndex = company.name.length % colors.length;
                            return `
                                <tr class="clickable-row" onclick="navigateToLevel('department', '${escapeHtml(company.name)}')">
                                    <td>
                                        <div class="company-cell">
                                            <div class="company-icon" style="background: ${colors[colorIndex]};">
                                                <i class="fas fa-building"></i>
                                            </div>
                                            <div class="company-info">
                                                <h4>${escapeHtml(company.name)}</h4>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge badge-info">${company.departments} department${company.departments !== 1 ? 's' : ''}</span></td>
                                    <td><span class="badge badge-secondary">${company.employeeCount} employee${company.employeeCount !== 1 ? 's' : ''}</span></td>
                                    <td><span class="badge badge-success">${company.activeCount} active</span></td>
                                    <td class="action-icons" onclick="event.stopPropagation()">
                                        <i class="fas fa-edit" onclick="editCompanyInSettings('${escapeHtml(company.name)}')" title="Edit Company"></i>
                                    </td>
                                </tr>
                            `;
                        }).join('') : `
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        <i class="fas fa-building"></i>
                                        <h4>No Companies Found</h4>
                                        <p>No companies match your search or no companies exist in the database</p>
                                    </div>
                                </td>
                            </tr>
                        `}
                    </tbody>
                </table>

                <div class="pagination">
                    <div class="pagination-info" id="companyPaginationInfo">
                        ${filteredCompanies.length > 0 ? `Showing ${start + 1}-${Math.min(end, filteredCompanies.length)} of ${filteredCompanies.length} companies` : 'No companies'}
                    </div>
                    <div class="pagination-controls" id="companyPaginationControls"></div>
                </div>
            </div>
        `;

        document.getElementById('activeEmployeeLevelContent').innerHTML = html;
        renderCompanyPagination();
        
        const searchInput = document.getElementById('companySearchInput');
        if (searchInput) {
            searchInput.addEventListener('keyup', (e) => {
                companySearchTerm = e.target.value;
                currentCompanyPage = 1;
                renderCompanyLevel();
            });
        }
    }

    function renderCompanyPagination() {
        const totalPages = Math.ceil(filteredCompanies.length / itemsPerPage);
        const container = document.getElementById('companyPaginationControls');
        let html = `<div class="page-btn" onclick="changeCompanyPage(${currentCompanyPage - 1})" ${currentCompanyPage === 1 ? 'style="opacity:0.5; pointer-events:none;"' : ''}><i class="fas fa-chevron-left"></i></div>`;
        for (let i = 1; i <= Math.min(totalPages, 5); i++) {
            html += `<div class="page-btn ${currentCompanyPage === i ? 'active' : ''}" onclick="changeCompanyPage(${i})">${i}</div>`;
        }
        if (totalPages > 5) html += `<div class="page-btn">...</div>`;
        if (totalPages > 5) html += `<div class="page-btn" onclick="changeCompanyPage(${totalPages})">${totalPages}</div>`;
        html += `<div class="page-btn" onclick="changeCompanyPage(${currentCompanyPage + 1})" ${currentCompanyPage === totalPages || totalPages === 0 ? 'style="opacity:0.5; pointer-events:none;"' : ''}><i class="fas fa-chevron-right"></i></div>`;
        container.innerHTML = html;
    }

    // ---------- RENDER DEPARTMENT LEVEL ----------
    function renderDepartmentLevel() {
        // Find the selected company in the database
        const selectedCompanyObj = window.companies.find(c => c.name === selectedCompany);
        
        if (!selectedCompanyObj) {
            console.error('Company not found:', selectedCompany);
            return;
        }
        
        // Get departments for this company from database
        const companyDepartments = window.departments.filter(d => d.companyId === selectedCompanyObj.id);
        
        const departmentData = companyDepartments.map(dept => {
            const emps = window.employees.filter(e => e.department === dept.name && e.company === selectedCompany);
            const stats = getActiveStats(emps);
            return {
                id: dept.id,
                name: dept.name,
                employeeCount: emps.length,
                activeCount: stats.active,
                remoteCount: stats.remote,
                onLeaveCount: stats.onLeave
            };
        });

        // Apply search filter
        filteredDepartments = departmentData.filter(d => 
            d.name.toLowerCase().includes(departmentSearchTerm.toLowerCase())
        );

        const companyEmployees = window.employees.filter(e => e.company === selectedCompany);
        const stats = getActiveStats(companyEmployees);
        const start = (currentDepartmentPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const paginatedData = filteredDepartments.slice(start, end);

        const html = `
            <div class="filter-bar">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search departments..." id="departmentSearchInput" value="${escapeHtml(departmentSearchTerm)}">
                </div>
            </div>

            <div class="stats-mini">
                <div class="stat-mini-card">
                    <i class="fas fa-building"></i>
                    <span>${escapeHtml(selectedCompany)}</span>
                </div>
                <div class="stat-mini-card">
                    <i class="fas fa-layer-group"></i>
                    <span>${companyDepartments.length}</span> <small>Departments</small>
                </div>
                <div class="stat-mini-card">
                    <i class="fas fa-users"></i>
                    <span>${stats.total}</span> <small>Employees</small>
                </div>
                <div class="stat-mini-card">
                    <i class="fas fa-user-check"></i>
                    <span>${stats.active}</span> <small>Active</small>
                </div>
                <div class="header-actions">
                    <button class="btn btn-primary" onclick="openAddEmployeeModal()"><i class="fas fa-plus"></i> Add Employee</button>
                </div>
            </div>

            <div class="table-card">
                <div class="table-header">
                    <h3>
                        <span class="breadcrumb-link-header" onclick="navigateToLevel('company')">Company List</span>
                        <i class="fas fa-chevron-right breadcrumb-separator-header"></i>
                        <span class="breadcrumb-current-header">${escapeHtml(selectedCompany)}</span>
                    </h3>
                </div>
                <table class="employee-table">
                    <thead>
                        <tr>
                            <th>Department</th>
                            <th>Total Employees</th>
                            <th>Active</th>
                            <th>Remote</th>
                            <th>On Leave</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${paginatedData.length > 0 ? paginatedData.map(dept => {
                            const colors = ['#4f46e5', '#0ea5e9', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#14b8a6'];
                            const colorIndex = dept.name.length % colors.length;
                            return `
                                <tr class="clickable-row" onclick="navigateToLevel('employee', '${escapeHtml(selectedCompany)}', '${escapeHtml(dept.name)}')">
                                    <td>
                                        <div class="department-cell">
                                            <div class="department-icon" style="background: ${colors[colorIndex]};">
                                                <i class="fas fa-users"></i>
                                            </div>
                                            <div class="department-info">
                                                <h4>${escapeHtml(dept.name)}</h4>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge badge-secondary">${dept.employeeCount} employee${dept.employeeCount !== 1 ? 's' : ''}</span></td>
                                    <td><span class="badge badge-success">${dept.activeCount}</span></td>
                                    <td><span class="badge badge-info">${dept.remoteCount}</span></td>
                                    <td><span class="badge badge-warning">${dept.onLeaveCount}</span></td>
                                    <td class="action-icons" onclick="event.stopPropagation()">
                                        <i class="fas fa-edit" onclick="editDepartmentInSettings('${escapeHtml(selectedCompany)}', '${escapeHtml(dept.name)}')" title="Edit Department"></i>
                                    </td>
                                </tr>
                            `;
                        }).join('') : `
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <i class="fas fa-layer-group"></i>
                                        <h4>No Departments Found</h4>
                                        <p>No departments match your search or no departments exist for this company</p>
                                    </div>
                                </td>
                            </tr>
                        `}
                    </tbody>
                </table>

                <div class="pagination">
                    <div class="pagination-info" id="departmentPaginationInfo">
                        ${filteredDepartments.length > 0 ? `Showing ${start + 1}-${Math.min(end, filteredDepartments.length)} of ${filteredDepartments.length} departments` : 'No departments'}
                    </div>
                    <div class="pagination-controls" id="departmentPaginationControls"></div>
                </div>
            </div>
        `;

        document.getElementById('activeEmployeeLevelContent').innerHTML = html;
        renderDepartmentPagination();
        
        const searchInput = document.getElementById('departmentSearchInput');
        if (searchInput) {
            searchInput.addEventListener('keyup', (e) => {
                departmentSearchTerm = e.target.value;
                currentDepartmentPage = 1;
                renderDepartmentLevel();
            });
        }
    }

    function renderDepartmentPagination() {
        const totalPages = Math.ceil(filteredDepartments.length / itemsPerPage);
        const container = document.getElementById('departmentPaginationControls');
        let html = `<div class="page-btn" onclick="changeDepartmentPage(${currentDepartmentPage - 1})" ${currentDepartmentPage === 1 ? 'style="opacity:0.5; pointer-events:none;"' : ''}><i class="fas fa-chevron-left"></i></div>`;
        for (let i = 1; i <= Math.min(totalPages, 5); i++) {
            html += `<div class="page-btn ${currentDepartmentPage === i ? 'active' : ''}" onclick="changeDepartmentPage(${i})">${i}</div>`;
        }
        if (totalPages > 5) html += `<div class="page-btn">...</div>`;
        if (totalPages > 5) html += `<div class="page-btn" onclick="changeDepartmentPage(${totalPages})">${totalPages}</div>`;
        html += `<div class="page-btn" onclick="changeDepartmentPage(${currentDepartmentPage + 1})" ${currentDepartmentPage === totalPages || totalPages === 0 ? 'style="opacity:0.5; pointer-events:none;"' : ''}><i class="fas fa-chevron-right"></i></div>`;
        container.innerHTML = html;
    }

    // ---------- RENDER EMPLOYEE LEVEL ----------
    function renderEmployeeLevel() {
        console.log('🔍 renderEmployeeLevel called with:');
        console.log('  selectedCompany:', selectedCompany);
        console.log('  selectedDepartment:', selectedDepartment);
        console.log('  Total employees:', window.employees.length);
        
        let employees = window.employees.filter(e => 
            e.company === selectedCompany && e.department === selectedDepartment
        );
        
        console.log('  Filtered employees:', employees.length);
        if (employees.length === 0) {
            console.warn('⚠️ No employees found for this company/department combination');
            console.log('  Available companies in employees:', [...new Set(window.employees.map(e => e.company))]);
            console.log('  Available departments in employees:', [...new Set(window.employees.map(e => e.department))]);
        }

        // Apply search filter
        filteredEmployees = employees.filter(emp => 
            emp.name.toLowerCase().includes(employeeSearchTerm.toLowerCase()) ||
            emp.email.toLowerCase().includes(employeeSearchTerm.toLowerCase()) ||
            emp.position.toLowerCase().includes(employeeSearchTerm.toLowerCase())
        );

        const stats = getActiveStats(filteredEmployees);
        const start = (currentEmployeePage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const paginatedData = filteredEmployees.slice(start, end);

        const html = `
            <div class="filter-bar">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search employees by name, email, or position..." id="employeeSearchInput" value="${escapeHtml(employeeSearchTerm)}">
                </div>
                <select class="filter-select" id="statusFilterEmployee">
                    <option>All Status</option>
                    <option>Active</option>
                    <option>On Leave</option>
                    <option>Remote</option>
                    <option>Probation</option>
                </select>
            </div>

            <div class="stats-mini">
                <div class="stat-mini-card">
                    <i class="fas fa-users"></i>
                    <span>${stats.total}</span> <small>Total</small>
                </div>
                <div class="stat-mini-card">
                    <i class="fas fa-user-check"></i>
                    <span>${stats.active}</span> <small>Active</small>
                </div>
                <div class="stat-mini-card">
                    <i class="fas fa-umbrella-beach"></i>
                    <span>${stats.onLeave}</span> <small>On Leave</small>
                </div>
                <div class="stat-mini-card">
                    <i class="fas fa-laptop-house"></i>
                    <span>${stats.remote}</span> <small>Remote</small>
                </div>
                <div class="header-actions">
                    <button class="btn btn-primary" onclick="openAddEmployeeModal()"><i class="fas fa-plus"></i> Add Employee</button>
                </div>
            </div>

            <div class="table-card">
                <div class="table-header">
                    <h3>
                        <span class="breadcrumb-link-header" onclick="navigateToLevel('company')">Company List</span>
                        <i class="fas fa-chevron-right breadcrumb-separator-header"></i>
                        <span class="breadcrumb-link-header" onclick="navigateToLevel('department', '${escapeHtml(selectedCompany)}')">${escapeHtml(selectedCompany)}</span>
                        <i class="fas fa-chevron-right breadcrumb-separator-header"></i>
                        <span class="breadcrumb-current-header">${escapeHtml(selectedDepartment)}</span>
                    </h3>
                </div>
                <table class="employee-table">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Position</th>
                            <th>Status</th>
                            <th>Join Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${paginatedData.length > 0 ? paginatedData.map(emp => {
                            const statusClass = {
                                'Active': 'badge-success',
                                'Remote': 'badge-info',
                                'On Leave': 'badge-warning',
                                'Probation': 'badge-warning'
                            }[emp.status] || 'badge-secondary';
                            
                            return `
                                <tr>
                                    <td>
                                        <div class="emp-cell">
                                            <img src="${emp.profilePhoto || '/3ME/assets/images/default-avatar.png'}" class="emp-avatar-sm" style="object-fit: cover;" />
                                            <div class="emp-info">
                                                <h4>${escapeHtml(emp.name)}</h4>
                                                <p>${escapeHtml(emp.email)}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>${escapeHtml(emp.position)}</td>
                                    <td><span class="badge ${statusClass}">${emp.status}</span></td>
                                    <td>${emp.joinDate}</td>
                                    <td class="action-icons">
                                        ${emp.isIncomplete ? '<i class="fas fa-exclamation-triangle" style="color: #f59e0b; margin-right: 4px;" title="Incomplete Profile - Click Edit to Complete"></i>' : ''}
                                        <i class="fas fa-eye" data-employee-id="${emp.id}" data-action="view" title="View"></i>
                                        <i class="fas fa-edit" data-employee-id="${emp.id}" data-action="edit" title="Edit"></i>
                                        <i class="fas fa-user-slash" data-employee-id="${emp.id}" data-action="terminate" title="Terminate Employment"></i>
                                    </td>
                                </tr>
                            `;
                        }).join('') : `
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        <i class="fas fa-users"></i>
                                        <h4>No Employees Found</h4>
                                        <p>No employees match your search criteria</p>
                                    </div>
                                </td>
                            </tr>
                        `}
                    </tbody>
                </table>

                <div class="pagination">
                    <div class="pagination-info" id="employeePaginationInfo">
                        ${filteredEmployees.length > 0 ? `Showing ${start + 1}-${Math.min(end, filteredEmployees.length)} of ${filteredEmployees.length} employees` : 'No employees'}
                    </div>
                    <div class="pagination-controls" id="employeePaginationControls"></div>
                </div>
            </div>
        `;

        document.getElementById('activeEmployeeLevelContent').innerHTML = html;
        renderEmployeePagination();
        
        const searchInput = document.getElementById('employeeSearchInput');
        if (searchInput) {
            searchInput.addEventListener('keyup', (e) => {
                employeeSearchTerm = e.target.value;
                currentEmployeePage = 1;
                renderEmployeeLevel();
            });
        }
        
        const statusFilter = document.getElementById('statusFilterEmployee');
        if (statusFilter) {
            statusFilter.addEventListener('change', (e) => {
                const status = e.target.value;
                let baseEmployees = window.employees.filter(emp => 
                    emp.company === selectedCompany && emp.department === selectedDepartment
                );
                
                if (status === 'All Status') {
                    filteredEmployees = baseEmployees.filter(emp => 
                        emp.name.toLowerCase().includes(employeeSearchTerm.toLowerCase()) ||
                        emp.email.toLowerCase().includes(employeeSearchTerm.toLowerCase()) ||
                        emp.position.toLowerCase().includes(employeeSearchTerm.toLowerCase())
                    );
                } else {
                    filteredEmployees = baseEmployees.filter(emp => 
                        emp.status === status &&
                        (emp.name.toLowerCase().includes(employeeSearchTerm.toLowerCase()) ||
                         emp.email.toLowerCase().includes(employeeSearchTerm.toLowerCase()) ||
                         emp.position.toLowerCase().includes(employeeSearchTerm.toLowerCase()))
                    );
                }
                currentEmployeePage = 1;
                renderEmployeeLevel();
            });
        }
    }

    function renderEmployeePagination() {
        const totalPages = Math.ceil(filteredEmployees.length / itemsPerPage);
        const container = document.getElementById('employeePaginationControls');
        let html = `<div class="page-btn" onclick="changeEmployeePage(${currentEmployeePage - 1})" ${currentEmployeePage === 1 ? 'style="opacity:0.5; pointer-events:none;"' : ''}><i class="fas fa-chevron-left"></i></div>`;
        for (let i = 1; i <= Math.min(totalPages, 5); i++) {
            html += `<div class="page-btn ${currentEmployeePage === i ? 'active' : ''}" onclick="changeEmployeePage(${i})">${i}</div>`;
        }
        if (totalPages > 5) html += `<div class="page-btn">...</div>`;
        if (totalPages > 5) html += `<div class="page-btn" onclick="changeEmployeePage(${totalPages})">${totalPages}</div>`;
        html += `<div class="page-btn" onclick="changeEmployeePage(${currentEmployeePage + 1})" ${currentEmployeePage === totalPages || totalPages === 0 ? 'style="opacity:0.5; pointer-events:none;"' : ''}><i class="fas fa-chevron-right"></i></div>`;
        container.innerHTML = html;
        
        // Add event delegation for action icons
        setupActionIconHandlers();
    }
    
    // Setup event delegation for action icons (using a single global handler)
    let actionIconHandlerAttached = false;
    
    function setupActionIconHandlers() {
        // Only attach the handler once globally
        if (actionIconHandlerAttached) return;
        
        // Use event delegation on the document body to catch all clicks
        document.body.addEventListener('click', function(e) {
            const icon = e.target.closest('i[data-action]');
            if (!icon) return;
            
            // Only handle if it's within an action-icons container
            if (!icon.closest('.action-icons')) return;
            
            e.stopPropagation();
            e.preventDefault();
            
            const action = icon.getAttribute('data-action');
            const employeeId = icon.getAttribute('data-employee-id');
            const employeeEmail = icon.getAttribute('data-employee-email');
            
            console.log('🔍 Action icon clicked:', action);
            console.log('  Employee ID from icon:', employeeId, typeof employeeId);
            console.log('  Available employee IDs:', window.employees ? window.employees.map(e => e.id) : 'no employees');
            
            // Find the employee to verify
            if (employeeId) {
                const emp = window.employees.find(e => String(e.id) === String(employeeId));
                console.log('  Employee found?', emp ? 'YES - ' + emp.name : 'NO');
            }
            
            switch(action) {
                case 'view':
                    console.log('📖 Calling viewEmployee with ID:', employeeId);
                    if (typeof viewEmployee === 'function') {
                        viewEmployee(employeeId);
                    } else {
                        console.error('❌ viewEmployee function not found');
                        showToast('View function not available', 'error');
                    }
                    break;
                case 'edit':
                    console.log('✏️ Calling editEmployee with ID:', employeeId);
                    if (typeof editEmployee === 'function') {
                        editEmployee(employeeId);
                    } else {
                        console.error('❌ editEmployee function not found');
                        showToast('Edit function not available', 'error');
                    }
                    break;
                case 'terminate':
                    if (typeof openTerminateEmployeeModal === 'function') {
                        openTerminateEmployeeModal(employeeId);
                    } else {
                        console.error('❌ openTerminateEmployeeModal function not found');
                        showToast('Terminate function not available', 'error');
                    }
                    break;
            }
        });
        
        actionIconHandlerAttached = true;
        console.log('✅ Action icon handler attached globally');
    }

    // ---------- RENDER TERMINATED LEVEL ----------
    function renderTerminatedLevel() {
        let terminated = window.terminatedEmployees;
        
        // Apply search filter
        filteredTerminatedEmployees = terminated.filter(emp => 
            emp.name.toLowerCase().includes(terminatedSearchTerm.toLowerCase()) ||
            emp.email.toLowerCase().includes(terminatedSearchTerm.toLowerCase()) ||
            emp.department.toLowerCase().includes(terminatedSearchTerm.toLowerCase())
        );

        const stats = getTerminatedStats();
        const start = (currentTerminatedPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const paginatedData = filteredTerminatedEmployees.slice(start, end);

        const html = `
            <div class="filter-bar">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search terminated employees..." id="terminatedSearchInput" value="${escapeHtml(terminatedSearchTerm)}">
                </div>
            </div>

            <div class="stats-mini">
                <div class="stat-mini-card">
                    <i class="fas fa-user-slash"></i>
                    <span>${stats.total}</span> <small>Total Terminated</small>
                </div>
                <div class="stat-mini-card">
                    <i class="fas fa-calendar-xmark"></i>
                    <span>${stats.thisMonthCount}</span> <small>This Month</small>
                </div>
            </div>

            <div class="table-card">
                <div class="table-header">
                    <h3><i class="fas fa-user-slash"></i> Terminated Employees</h3>
                </div>
                <table class="employee-table">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Position</th>
                            <th>Department</th>
                            <th>Company</th>
                            <th>Termination Reason</th>
                            <th>Termination Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${paginatedData.length > 0 ? paginatedData.map(emp => {
                            const badgeClass = getReasonBadgeClass(emp.terminationReasonCode);
                            const displayReason = emp.terminationReasonCode === 'other' && emp.otherReasonSpecified 
                                ? `Other: ${emp.otherReasonSpecified}`
                                : getReasonDisplayName(emp.terminationReasonCode);
                            
                            return `
                                <tr>
                                    <td>
                                        <div class="emp-cell">
                                            <img src="${emp.profilePhoto || '/3ME/assets/images/default-avatar.png'}" class="emp-avatar-sm" style="object-fit: cover;" />
                                            <div class="emp-info">
                                                <h4>${escapeHtml(emp.name)}</h4>
                                                <p>${escapeHtml(emp.email)}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>${escapeHtml(emp.position)}</td>
                                    <td>${escapeHtml(emp.department)}</td>
                                    <td>${escapeHtml(emp.company)}</td>
                                    <td><span class="badge ${badgeClass}">${displayReason}</span></td>
                                    <td>${emp.terminationDate}</td>
                                    <td class="action-icons">
                                        <i class="fas fa-eye" onclick="viewTerminatedEmployee(${emp.id})" title="View Details"></i>
                                        <i class="fas fa-rotate-left" onclick="reinstateEmployee(${emp.id})" title="Reinstate"></i>
                                    </td>
                                </tr>
                            `;
                        }).join('') : `
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <i class="fas fa-user-slash"></i>
                                        <h4>No Terminated Employees</h4>
                                        <p>Terminated employees will appear here</p>
                                    </div>
                                </td>
                            </tr>
                        `}
                    </tbody>
                </table>

                <div class="pagination">
                    <div class="pagination-info" id="terminatedPaginationInfo">
                        ${filteredTerminatedEmployees.length > 0 ? `Showing ${start + 1}-${Math.min(end, filteredTerminatedEmployees.length)} of ${filteredTerminatedEmployees.length} terminated employees` : 'No terminated employees'}
                    </div>
                    <div class="pagination-controls" id="terminatedPaginationControls"></div>
                </div>
            </div>
        `;

        document.getElementById('terminatedEmployeeLevelContent').innerHTML = html;
        renderTerminatedPagination();
        
        const searchInput = document.getElementById('terminatedSearchInput');
        if (searchInput) {
            searchInput.addEventListener('keyup', (e) => {
                terminatedSearchTerm = e.target.value;
                currentTerminatedPage = 1;
                renderTerminatedLevel();
            });
        }
    }

    function renderTerminatedPagination() {
        const totalPages = Math.ceil(filteredTerminatedEmployees.length / itemsPerPage);
        const container = document.getElementById('terminatedPaginationControls');
        let html = `<div class="page-btn" onclick="changeTerminatedPage(${currentTerminatedPage - 1})" ${currentTerminatedPage === 1 ? 'style="opacity:0.5; pointer-events:none;"' : ''}><i class="fas fa-chevron-left"></i></div>`;
        for (let i = 1; i <= Math.min(totalPages, 5); i++) {
            html += `<div class="page-btn ${currentTerminatedPage === i ? 'active' : ''}" onclick="changeTerminatedPage(${i})">${i}</div>`;
        }
        if (totalPages > 5) html += `<div class="page-btn">...</div>`;
        if (totalPages > 5) html += `<div class="page-btn" onclick="changeTerminatedPage(${totalPages})">${totalPages}</div>`;
        html += `<div class="page-btn" onclick="changeTerminatedPage(${currentTerminatedPage + 1})" ${currentTerminatedPage === totalPages || totalPages === 0 ? 'style="opacity:0.5; pointer-events:none;"' : ''}><i class="fas fa-chevron-right"></i></div>`;
        container.innerHTML = html;
    }

    // ---------- PAGE CHANGE FUNCTIONS ----------
    function changeCompanyPage(page) {
        if (page >= 1 && page <= Math.ceil(filteredCompanies.length / itemsPerPage)) {
            currentCompanyPage = page;
            renderCompanyLevel();
        }
    }

    function changeDepartmentPage(page) {
        if (page >= 1 && page <= Math.ceil(filteredDepartments.length / itemsPerPage)) {
            currentDepartmentPage = page;
            renderDepartmentLevel();
        }
    }

    function changeEmployeePage(page) {
        if (page >= 1 && page <= Math.ceil(filteredEmployees.length / itemsPerPage)) {
            currentEmployeePage = page;
            renderEmployeeLevel();
        }
    }

    function changeTerminatedPage(page) {
        if (page >= 1 && page <= Math.ceil(filteredTerminatedEmployees.length / itemsPerPage)) {
            currentTerminatedPage = page;
            renderTerminatedLevel();
        }
    }

    // ---------- TAB SWITCHING ----------
    function switchTab(tab) {
        currentTab = tab;
        
        document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        event.target.closest('.tab').classList.add('active');
        
        document.getElementById('activeEmployeesView').style.display = tab === 'active' ? 'block' : 'none';
        document.getElementById('terminatedEmployeesView').style.display = tab === 'terminated' ? 'block' : 'none';
        
        if (tab === 'active') {
            if (currentLevel === 'company') {
                renderCompanyLevel();
            } else if (currentLevel === 'department') {
                renderDepartmentLevel();
            } else if (currentLevel === 'employee') {
                renderEmployeeLevel();
            }
        } else {
            renderTerminatedLevel();
        }
    }

    // ---------- EMPLOYEE ACTIONS ----------
    function viewTerminatedEmployee(employeeId) {
        const emp = window.terminatedEmployees.find(e => e.id === employeeId);
        if (!emp) return;
        
        const displayReason = emp.terminationReasonCode === 'other' && emp.otherReasonSpecified 
            ? `Other: ${emp.otherReasonSpecified}`
            : getReasonDisplayName(emp.terminationReasonCode);
        
        const content = `
            <div class="employee-profile">
                <div class="profile-header">
                    <div class="profile-avatar-lg" style="background: ${emp.color};">${emp.avatar}</div>
                    <div class="profile-info">
                        <h3>${escapeHtml(emp.name)}</h3>
                        <div class="position">${escapeHtml(emp.position)}</div>
                        <span class="badge ${getReasonBadgeClass(emp.terminationReasonCode)}">${displayReason}</span>
                    </div>
                </div>
                
                <div class="detail-section">
                    <div class="section-title-sm">
                        <i class="fas fa-info-circle"></i> Termination Details
                    </div>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <span class="detail-label">Termination Reason</span>
                            <span class="detail-value">${displayReason}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Termination Date</span>
                            <span class="detail-value">${emp.terminationDate}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Last Working Day</span>
                            <span class="detail-value">${emp.lastWorkingDay || emp.terminationDate}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Remarks</span>
                            <span class="detail-value">${emp.terminationRemarks || 'None'}</span>
                        </div>
                    </div>
                </div>
                
                <div class="detail-section">
                    <div class="section-title-sm">
                        <i class="fas fa-briefcase"></i> Employment Details
                    </div>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <span class="detail-label">Company</span>
                            <span class="detail-value">${escapeHtml(emp.company)}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Department</span>
                            <span class="detail-value">${escapeHtml(emp.department)}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Join Date</span>
                            <span class="detail-value">${emp.joinDate}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Email</span>
                            <span class="detail-value">${escapeHtml(emp.email)}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Phone</span>
                            <span class="detail-value">${emp.phone || 'Not provided'}</span>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        if (typeof openModal === 'function') {
            openModal('Terminated Employee Details', content);
        }
    }

    function reinstateEmployee(employeeId) {
        const empIndex = window.terminatedEmployees.findIndex(e => e.id === employeeId);
        if (empIndex === -1) return;
        
        if (confirm('Are you sure you want to reinstate this employee? They will be moved back to active employees.')) {
            const emp = window.terminatedEmployees[empIndex];
            
            delete emp.terminationReason;
            delete emp.terminationReasonCode;
            delete emp.terminationDate;
            delete emp.lastWorkingDay;
            delete emp.terminationRemarks;
            delete emp.terminatedAt;
            delete emp.otherReasonSpecified;
            
            emp.status = 'Active';
            
            window.employees.push(emp);
            window.terminatedEmployees.splice(empIndex, 1);
            
            showToast(`${emp.name} has been reinstated`, 'success');
            
            renderTerminatedLevel();
            
            if (currentTab === 'active') {
                if (currentLevel === 'company') {
                    renderCompanyLevel();
                } else if (currentLevel === 'department') {
                    renderDepartmentLevel();
                } else if (currentLevel === 'employee') {
                    renderEmployeeLevel();
                }
            }
        }
    }

    function emailEmployee(email) {
        window.location.href = `mailto:${email}`;
    }

    // Redirect to settings page for editing company/department
    function editCompanyInSettings(companyName) {
        // Store the company name in sessionStorage to navigate to it
        sessionStorage.setItem('navigateToCompany', companyName);
        sessionStorage.setItem('navigateToTab', 'org');
        window.location.href = 'settings.php';
    }

    function editDepartmentInSettings(companyName, departmentName) {
        // Store both company and department names
        sessionStorage.setItem('navigateToCompany', companyName);
        sessionStorage.setItem('navigateToDepartment', departmentName);
        sessionStorage.setItem('navigateToTab', 'org');
        window.location.href = 'settings.php';
    }

    // Add animation style
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        .profile-header {
            display: flex;
            align-items: center;
            gap: 24px;
            margin-bottom: 24px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e2e8f0;
        }
        .profile-avatar-lg {
            width: 100px;
            height: 100px;
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 36px;
            color: white;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }
        .profile-info h3 {
            font-size: 20px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 4px;
        }
        .profile-info .position {
            color: #64748b;
            font-size: 14px;
            margin-bottom: 8px;
        }
        .detail-section {
            margin-bottom: 24px;
        }
        .section-title-sm {
            font-size: 14px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .section-title-sm i {
            color: #4f46e5;
            width: 20px;
        }
        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px 20px;
        }
        .detail-item {
            display: flex;
            flex-direction: column;
        }
        .detail-label {
            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .detail-value {
            font-size: 14px;
            color: #1e293b;
            font-weight: 500;
        }
    `;
    document.head.appendChild(style);

    // Initialize
    (async function() {
        window.renderCompanyLevel = renderCompanyLevel;
        window.renderDepartmentLevel = renderDepartmentLevel;
        window.renderEmployeeLevel = renderEmployeeLevel;
        window.renderTerminatedLevel = renderTerminatedLevel;
        window.navigateToLevel = navigateToLevel;
        window.navigateBack = navigateBack;
        window.switchTab = switchTab;
        window.changeCompanyPage = changeCompanyPage;
        window.changeDepartmentPage = changeDepartmentPage;
        window.changeEmployeePage = changeEmployeePage;
        window.changeTerminatedPage = changeTerminatedPage;
        window.viewTerminatedEmployee = viewTerminatedEmployee;
        window.reinstateEmployee = reinstateEmployee;
        window.emailEmployee = emailEmployee;
        window.editCompanyInSettings = editCompanyInSettings;
        window.editDepartmentInSettings = editDepartmentInSettings;
        window.showToast = showToast;

        // Load all data from server
        console.log('🔄 Loading data from server...');
        await Promise.all([
            loadCompanies(),
            loadDepartments(),
            loadJobs(),
            loadEmployeeData()
        ]);
        
        // Make data available for modals
        window.employeeCompanies = window.companies;
        window.employeeDepartments = window.departments;
        // window.employeeJobs is already set by loadJobs()
        
        console.log('✅ All data loaded:', {
            companies: window.companies.length,
            departments: window.departments.length,
            jobs: window.employeeJobs.length,
            employees: window.employees.length
        });

        // Check if we need to navigate to a specific company/department from onboarding
        const navCompany = sessionStorage.getItem('navigateToCompany');
        const navDepartment = sessionStorage.getItem('navigateToDepartment');
        const highlightEmployee = sessionStorage.getItem('highlightEmployee');
        const forceNavigation = sessionStorage.getItem('forceNavigation');
        
        console.log('🔍 Checking for navigation parameters...');
        console.log('  navCompany:', navCompany);
        console.log('  navDepartment:', navDepartment);
        console.log('  highlightEmployee:', highlightEmployee);
        console.log('  forceNavigation:', forceNavigation);
        
        if (navCompany && navDepartment) {
            console.log('📍 Navigation requested from onboarding:');
            console.log('  Company:', navCompany);
            console.log('  Department:', navDepartment);
            console.log('  Employee:', highlightEmployee);
            console.log('  Force Navigation:', forceNavigation);
            
            const showIncompleteWarning = sessionStorage.getItem('showIncompleteWarning');
            
            // Debug: Check if company exists
            const companyExists = window.companies.find(c => c.name === navCompany);
            console.log('  Company exists in database?', companyExists ? 'YES' : 'NO');
            if (companyExists) {
                console.log('  Company ID:', companyExists.id);
                console.log('  Company name from DB:', companyExists.name);
            } else {
                console.error('  ❌ Company not found! Available companies:', window.companies.map(c => c.name));
            }
            
            // Debug: Check if department exists
            const deptExists = window.departments.find(d => d.name === navDepartment);
            console.log('  Department exists in database?', deptExists ? 'YES' : 'NO');
            if (deptExists) {
                console.log('  Department ID:', deptExists.id);
                console.log('  Department name from DB:', deptExists.name);
                console.log('  Department company_id:', deptExists.companyId);
            } else {
                console.error('  ❌ Department not found! Available departments:', window.departments.map(d => d.name));
            }
            
            // Debug: Check if any employees match
            console.log('  Searching for employees with:');
            console.log('    company === "' + navCompany + '"');
            console.log('    department === "' + navDepartment + '"');
            
            const matchingEmployees = window.employees.filter(e => {
                const companyMatch = e.company === navCompany;
                const deptMatch = e.department === navDepartment;
                console.log('    Employee:', e.name, '| Company match:', companyMatch, '| Dept match:', deptMatch);
                return companyMatch && deptMatch;
            });
            
            console.log('  Matching employees:', matchingEmployees.length);
            if (matchingEmployees.length > 0) {
                console.log('  Sample employee:', matchingEmployees[0]);
            } else {
                console.warn('⚠️ No employees found with this company/department combination');
                console.log('  All unique companies in employees:', [...new Set(window.employees.map(e => e.company))]);
                console.log('  All unique departments in employees:', [...new Set(window.employees.map(e => e.department))]);
            }
            
            // Clear the session storage
            sessionStorage.removeItem('navigateToCompany');
            sessionStorage.removeItem('navigateToDepartment');
            sessionStorage.removeItem('highlightEmployee');
            sessionStorage.removeItem('forceNavigation');
            sessionStorage.removeItem('showIncompleteWarning');
            
            // Navigate to employee level if forced OR if we have matching employees
            if (forceNavigation === 'true' || matchingEmployees.length > 0) {
                console.log('✅ Navigating to employee level');
                navigateToLevel('employee', navCompany, navDepartment);
                
                // Highlight the employee if specified
                if (highlightEmployee) {
                    setTimeout(() => {
                        highlightEmployeeInList(highlightEmployee);
                        
                        // Show incomplete profile warning
                        if (showIncompleteWarning === 'true') {
                            showIncompleteProfileBanner(highlightEmployee);
                        }
                    }, 500);
                }
                
                // Show success message
                if (matchingEmployees.length > 0) {
                    showToast(`✅ Showing ${matchingEmployees.length} employee${matchingEmployees.length > 1 ? 's' : ''} in ${navCompany} - ${navDepartment}`, 'success');
                } else {
                    showToast(`⚠️ Navigated to ${navCompany} - ${navDepartment}. Employee data may still be loading...`, 'info');
                    // Reload after a short delay to get fresh data
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                }
            } else {
                console.warn('⚠️ Cannot navigate - no employees found and navigation not forced. Showing company level instead.');
                showToast(`Employee onboarded to ${navCompany} - ${navDepartment}`, 'success');
                renderCompanyLevel();
                renderTerminatedLevel();
            }
        } else {
            console.log('📍 No navigation parameters found, showing company level');
            // Normal initialization
            renderCompanyLevel();
            renderTerminatedLevel();
        }

        console.log('✅ Employee Hub loaded with drill-down navigation');
    })();

    // Function to highlight a specific employee in the list
    function highlightEmployeeInList(employeeIdentifier) {
        console.log('🎯 Highlighting employee:', employeeIdentifier);
        
        // Find all employee rows
        const rows = document.querySelectorAll('.employee-table tbody tr');
        let found = false;
        
        rows.forEach(row => {
            const nameElement = row.querySelector('.emp-info h4');
            const emailElement = row.querySelector('.emp-info p');
            
            if (nameElement && emailElement) {
                const name = nameElement.textContent.trim();
                const email = emailElement.textContent.trim();
                
                // Check if this row matches the employee we're looking for
                if (name.includes(employeeIdentifier) || email.includes(employeeIdentifier) || employeeIdentifier.includes(name)) {
                    found = true;
                    
                    // Apply highlight effect
                    row.style.background = 'linear-gradient(90deg, #dbeafe 0%, #eff6ff 100%)';
                    row.style.border = '2px solid #3b82f6';
                    row.style.borderRadius = '8px';
                    row.style.boxShadow = '0 4px 12px rgba(59, 130, 246, 0.2)';
                    
                    // Scroll into view
                    row.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    
                    // Show welcome message
                    showToast(`✅ ${name} has been successfully onboarded!`, 'success');
                    
                    // Remove highlight after 5 seconds
                    setTimeout(() => {
                        row.style.background = '';
                        row.style.border = '';
                        row.style.borderRadius = '';
                        row.style.boxShadow = '';
                    }, 5000);
                }
            }
        });
        
        if (!found) {
            console.log('⚠️ Employee not found in current view');
        }
    }
    
    // Function to show incomplete profile banner
    function showIncompleteProfileBanner(employeeName) {
        const banner = document.createElement('div');
        banner.style.cssText = `
            position: fixed;
            top: 80px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
            padding: 16px 24px;
            border-radius: 16px;
            font-size: 14px;
            z-index: 9999;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            gap: 12px;
            max-width: 600px;
            animation: slideDown 0.4s ease;
            border: 2px solid #fbbf24;
        `;
        
        banner.innerHTML = `
            <i class="fas fa-exclamation-triangle" style="font-size: 20px; color: #f59e0b;"></i>
            <div style="flex: 1;">
                <div style="font-weight: 600; margin-bottom: 4px;">Incomplete Employee Profile</div>
                <div style="font-size: 12px; opacity: 0.9;">
                    ${employeeName} needs to complete their profile. Click the <strong>Edit</strong> button to add missing details.
                </div>
            </div>
            <button onclick="this.parentElement.remove()" style="
                background: white;
                border: none;
                width: 28px;
                height: 28px;
                border-radius: 50%;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #92400e;
                font-size: 16px;
                transition: all 0.2s;
            " onmouseover="this.style.background='#fef3c7'" onmouseout="this.style.background='white'">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        document.body.appendChild(banner);
        
        // Auto-remove after 10 seconds
        setTimeout(() => {
            banner.style.opacity = '0';
            banner.style.transition = 'opacity 0.3s';
            setTimeout(() => banner.remove(), 300);
        }, 10000);
    }
    
    // Add animation style for banner
    const bannerStyle = document.createElement('style');
    bannerStyle.textContent = `
        @keyframes slideDown {
            from {
                transform: translateX(-50%) translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateX(-50%) translateY(0);
                opacity: 1;
            }
        }
    `;
    document.head.appendChild(bannerStyle);
    
    // Make the function globally accessible
    window.highlightEmployeeInList = highlightEmployeeInList;
    window.showIncompleteProfileBanner = showIncompleteProfileBanner;
</script>

</body>
</html>