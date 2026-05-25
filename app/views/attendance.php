<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
/**
 * time-attendance.php
 * Time and Attendance Management - Clean interface with sidebar and modal components
 * Updated with drill-down navigation for import preview and online spreadsheet viewer
 */

$pageTitle = "Time & Attendance";
$activeMenu = "Time & Attendance";
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
    <!-- SheetJS for Excel/CSV parsing -->
    <script src="https://cdn.sheetjs.com/xlsx-0.20.1/package/dist/xlsx.full.min.js"></script>
    
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

        /* Import Action Card - inline with stats */
        .import-action-card {
            background: rgba(255,255,255,0.95);
            padding: 8px 16px;
            border-radius: 24px;
            border: 1px solid rgba(255,255,255,0.6);
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
        }

        .import-action-card:hover {
            background: white;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.1);
            border-color: #4f46e5;
        }

        .import-action-card i {
            color: #4f46e5;
            font-size: 18px;
        }

        .import-action-card .import-text {
            font-weight: 500;
            color: #0f172a;
        }

        .import-action-card .import-hint {
            font-size: 11px;
            color: #64748b;
        }

        /* Selected file info inline */
        .selected-file-info-inline {
            display: flex;
            align-items: center;
            gap: 12px;
            background: #f8fafc;
            padding: 8px 16px;
            border-radius: 24px;
            border: 1px solid #e2e8f0;
        }

        .selected-file-info-inline i {
            color: #4f46e5;
        }

        .selected-file-info-inline .file-details {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .selected-file-info-inline .file-name {
            font-weight: 500;
            color: #0f172a;
        }

        .selected-file-info-inline .file-size {
            color: #64748b;
            font-size: 11px;
        }

        .selected-file-info-inline .btn-icon {
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 4px 8px;
            transition: color 0.2s;
        }

        .selected-file-info-inline .btn-icon:hover {
            color: #ef4444;
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

        .import-table, .roster-table, .correction-table, .preview-data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .import-table th, .roster-table th, .correction-table th, .preview-data-table th {
            text-align: left;
            padding: 12px 8px;
            font-weight: 600;
            color: #475569;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #e2e8f0;
        }

        .import-table td, .roster-table td, .correction-table td, .preview-data-table td {
            padding: 12px 8px;
            border-bottom: 1px solid #f1f5f9;
            color: #1e293b;
            font-size: 13px;
        }

        .import-table tbody tr:hover, .roster-table tbody tr:hover, .correction-table tbody tr:hover, .preview-data-table tbody tr:hover {
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

        /* Progress bar for import stats */
        .import-stats {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .progress-bar {
            width: 80px;
            height: 6px;
            background: #e2e8f0;
            border-radius: 3px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: #10b981;
            border-radius: 3px;
        }

        .progress-fill.warning {
            background: #f59e0b;
        }

        .progress-fill.danger {
            background: #ef4444;
        }

        /* Shift display */
        .shift-badge {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            background: #f1f5f9;
            border-radius: 20px;
            font-size: 12px;
        }

        .shift-badge i {
            color: #4f46e5;
            font-size: 12px;
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

        /* Back button */
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

        /* Spreadsheet Viewer Styles */
        .spreadsheet-viewer {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            margin-bottom: 20px;
        }

        .spreadsheet-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 16px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }

        .spreadsheet-tabs {
            display: flex;
            gap: 4px;
        }

        .sheet-tab {
            padding: 8px 16px;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 8px 8px 0 0;
            cursor: pointer;
            font-size: 12px;
            font-weight: 500;
            color: #64748b;
            transition: all 0.2s;
        }

        .sheet-tab.active {
            background: #4f46e5;
            color: white;
            border-color: #4f46e5;
        }

        .sheet-tab:hover:not(.active) {
            background: #f1f5f9;
        }

        .spreadsheet-actions {
            display: flex;
            gap: 8px;
        }

        .spreadsheet-container {
            overflow-x: auto;
            max-height: 500px;
            overflow-y: auto;
            position: relative;
        }

        .spreadsheet-table {
            border-collapse: collapse;
            width: max-content;
            min-width: 100%;
            font-size: 12px;
        }

        .spreadsheet-table th {
            background: #f1f5f9;
            color: #1e293b;
            font-weight: 600;
            padding: 10px 16px;
            text-align: left;
            border-right: 1px solid #e2e8f0;
            border-bottom: 2px solid #cbd5e1;
            white-space: nowrap;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .spreadsheet-table td {
            padding: 8px 16px;
            border-right: 1px solid #f1f5f9;
            border-bottom: 1px solid #f1f5f9;
            white-space: nowrap;
        }

        .spreadsheet-table tr:hover td {
            background: #f8fafc;
        }

        .spreadsheet-table .row-number {
            background: #f1f5f9;
            color: #64748b;
            text-align: center;
            font-weight: 500;
            position: sticky;
            left: 0;
            z-index: 5;
        }

        .spreadsheet-table th:first-child {
            position: sticky;
            left: 0;
            z-index: 11;
            background: #e2e8f0;
        }

        .spreadsheet-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 16px;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            font-size: 12px;
            color: #64748b;
        }

        .sheet-info {
            display: flex;
            gap: 20px;
        }

        .zoom-control {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .zoom-control button {
            padding: 4px 8px;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            cursor: pointer;
            color: #64748b;
        }

        .zoom-control button:hover {
            background: #f1f5f9;
        }

        /* Summary Tooltip in Viewer Header */
        .summary-tooltip-container {
            position: relative;
            display: inline-flex;
            align-items: center;
            cursor: pointer;
            margin-left: 8px;
        }

        .summary-tooltip-icon {
            color: #4f46e5;
            font-size: 16px;
            transition: color 0.2s ease, transform 0.2s ease;
        }

        .summary-tooltip-container:hover .summary-tooltip-icon {
            color: #6366f1;
            transform: scale(1.2);
        }

        .summary-tooltip-content {
            visibility: hidden;
            opacity: 0;
            position: absolute;
            top: calc(100% + 10px);
            left: 50%;
            transform: translateX(-50%) translateY(-8px);
            width: 260px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 12px;
            padding: 14px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            z-index: 10001;
            transition: opacity 0.2s cubic-bezier(0.4, 0, 0.2, 1), transform 0.2s cubic-bezier(0.4, 0, 0.2, 1), visibility 0.2s ease;
            font-family: inherit;
        }

        /* Tooltip arrow */
        .summary-tooltip-content::before {
            content: '';
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            border-width: 6px;
            border-style: solid;
            border-color: transparent transparent rgba(255, 255, 255, 0.95) transparent;
        }

        .summary-tooltip-container:hover .summary-tooltip-content {
            visibility: visible;
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }

        /* Styling inside tooltip */
        .tooltip-title {
            font-size: 12px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 6px;
        }
        
        .tooltip-title i {
            color: #4f46e5;
        }

        .tooltip-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 11px;
            padding: 5px 0;
            border-bottom: 1px solid #f8fafc;
        }
        
        .tooltip-row:last-child {
            border-bottom: none;
        }

        .tooltip-label {
            color: #64748b;
            font-weight: 500;
        }

        .tooltip-value {
            color: #0f172a;
            font-weight: 600;
        }

        .column-selector {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 12px;
            padding: 12px;
            background: white;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }

        .column-checkbox {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 4px 8px;
            background: #f8fafc;
            border-radius: 6px;
            cursor: pointer;
        }

        .column-checkbox input {
            cursor: pointer;
        }

        .column-checkbox:hover {
            background: #e2e8f0;
        }

        .mapping-section {
            margin-top: 20px;
            padding: 16px;
            background: white;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
        }

        .mapping-title {
            font-weight: 600;
            margin-bottom: 12px;
            color: #0f172a;
        }

        .mapping-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 12px;
        }

        .mapping-field {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .mapping-field label {
            font-weight: 500;
            color: #475569;
        }

        .mapping-field select {
            padding: 8px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 12px;
            background: white;
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 14px;
            }
            .import-table, .roster-table, .correction-table, .preview-data-table {
                display: block;
                overflow-x: auto;
            }
            .stats-mini {
                gap: 8px;
            }
            .spreadsheet-container {
                max-height: 400px;
            }
        }

        /* Loading overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255,255,255,0.9);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .loading-spinner {
            text-align: center;
        }

        .loading-spinner i {
            font-size: 40px;
            color: #4f46e5;
            animation: spin 1s linear infinite;
        }

        .loading-spinner p {
            margin-top: 12px;
            color: #475569;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
<div class="app-layout">
    
    <?php 
    // Include the sidebar component
    include 'sidebar.php'; 
    ?>

    <!-- MAIN CONTENT - TIME & ATTENDANCE -->
    <main class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <h1><i class="fas fa-clock"></i> Time & Attendance</h1>
        </div>

        <!-- Tabs -->
        <div class="tabs-container">
            <button class="tab-btn active" onclick="switchTab('import')"><i class="fas fa-upload"></i> Time Capture Import</button>
            <button class="tab-btn" onclick="switchTab('roster')"><i class="fas fa-calendar-alt"></i> Attendance Rules & Rostering</button>
            <button class="tab-btn" onclick="switchTab('correction')"><i class="fas fa-pen"></i> Exceptions & Corrections</button>
            <button class="tab-btn" onclick="switchTab('storage')"><i class="fas fa-hdd"></i> Uploaded File Storage</button>
        </div>

        <!-- Time Capture Import Tab -->
        <div id="importTab" class="tab-content active">
            <div id="importLevelContent">
                <!-- Content will be populated dynamically -->
            </div>
        </div>

        <!-- Attendance Rules & Rostering Tab -->
        <div id="rosterTab" class="tab-content">
            <!-- Filter Bar -->
            <div class="filter-bar">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search by shift name or company..." id="rosterSearchInput">
                </div>
                <select class="filter-select" id="companyFilter">
                    <option value="">All Companies</option>
                    <!-- Dynamically populated from database -->
                </select>
            </div>

            <!-- Stats -->
            <div class="stats-mini">
                <div class="stat-mini-card">
                    <i class="fas fa-calendar"></i>
                    <span id="totalRosters">0</span> <small>Active Rosters</small>
                </div>
                <div class="stat-mini-card">
                    <i class="fas fa-building"></i>
                    <span id="uniqueCompanies">0</span> <small>Companies</small>
                </div>
                <div class="stat-mini-card">
                    <i class="fas fa-users"></i>
                    <span>247</span> <small>Employees Covered</small>
                </div>
                
                <div class="header-actions">
                    <button class="btn btn-primary" onclick="openAddRosterModal()"><i class="fas fa-plus"></i> New Roster</button>
                </div>
            </div>

            <!-- Roster Table -->
            <div class="table-card">
                <div class="table-header">
                    <h3><i class="fas fa-list-ul"></i> Shift Rosters & Attendance Rules</h3>
                </div>
                <table class="roster-table">
                    <thead>
                        <tr>
                            <th>Shift Name</th>
                            <th>Company</th>
                            <th>Time</th>
                            <th>Overtime Rule</th>
                            <th>Effective Date</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="rosterTableBody">
                        <!-- Roster rows will be populated here -->
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="pagination">
                    <div class="pagination-info" id="rosterPaginationInfo">
                        Showing 0 of 0 rosters
                    </div>
                    <div class="pagination-controls" id="rosterPaginationControls">
                        <!-- Pagination buttons -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Exceptions & Corrections Tab -->
        <div id="correctionTab" class="tab-content">
            <!-- Filter Bar -->
            <div class="filter-bar">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search by employee name..." id="correctionSearchInput">
                </div>
                <select class="filter-select" id="correctionTypeFilter">
                    <option value="">All Types</option>
                    <option value="Late">Late</option>
                    <option value="Early Departure">Early Departure</option>
                    <option value="Missed Entry">Missed Entry</option>
                    <option value="Overtime Discrepancy">Overtime Discrepancy</option>
                </select>
                <select class="filter-select" id="correctionStatusFilter">
                    <option value="">All Status</option>
                    <option value="Pending">Pending</option>
                    <option value="Approved">Approved</option>
                    <option value="Rejected">Rejected</option>
                </select>
            </div>

            <!-- Stats -->
            <div class="stats-mini">
                <div class="stat-mini-card">
                    <i class="fas fa-pen"></i>
                    <span id="totalCorrections">0</span> <small>Total Requests</small>
                </div>
                <div class="stat-mini-card">
                    <i class="fas fa-hourglass-half"></i>
                    <span id="pendingCorrections">0</span> <small>Pending</small>
                </div>
                <div class="stat-mini-card">
                    <i class="fas fa-check-circle"></i>
                    <span id="approvedCorrections">0</span> <small>Approved</small>
                </div>
                <div class="stat-mini-card">
                    <i class="fas fa-times-circle"></i>
                    <span id="rejectedCorrections">0</span> <small>Rejected</small>
                </div>
                <div class="header-actions">
                    <button class="btn btn-warning" onclick="openAddCorrectionModal()"><i class="fas fa-plus"></i> New Correction</button>
                </div>
            </div>

            <!-- Correction Table -->
            <div class="table-card">
                <div class="table-header">
                    <h3><i class="fas fa-exclamation-triangle"></i> Exception & Correction Requests</h3>
                </div>
                <table class="correction-table">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Type</th>
                            <th>Original Date</th>
                            <th>Corrected Time</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="correctionTableBody">
                        <!-- Correction rows will be populated here -->
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="pagination">
                    <div class="pagination-info" id="correctionPaginationInfo">
                        Showing 0 of 0 corrections
                    </div>
                    <div class="pagination-controls" id="correctionPaginationControls">
                        <!-- Pagination buttons -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Uploaded File Storage Tab -->
        <div id="storageTab" class="tab-content">
            <!-- Filter Bar -->
            <div class="filter-bar">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search by file name or uploaded by..." id="storageSearchInput">
                </div>
                <select class="filter-select" id="storageTypeFilter">
                    <option value="">All File Types</option>
                    <option value="CSV">CSV</option>
                    <option value="Excel">Excel</option>
                    <option value="Biometric Export">Biometric Export</option>
                </select>
            </div>

            <!-- Stats -->
            <div class="stats-mini">
                <div class="stat-mini-card">
                    <i class="fas fa-file"></i>
                    <span id="totalStorageFiles">0</span> <small>Stored Files</small>
                </div>
                <div class="stat-mini-card">
                    <i class="fas fa-database"></i>
                    <span id="totalStorageSize">0 KB</span> <small>Total Size</small>
                </div>
                <div class="header-actions">
                    <button class="btn btn-primary" onclick="document.getElementById('fileInput').click()"><i class="fas fa-plus"></i> Upload New File</button>
                </div>
            </div>

            <!-- Storage Table -->
            <div class="table-card">
                <div class="table-header">
                    <h3><i class="fas fa-server"></i> Server File Storage</h3>
                </div>
                <table class="roster-table">
                    <thead>
                        <tr>
                            <th>File Name</th>
                            <th>File Type</th>
                            <th>Uploaded At</th>
                            <th>Uploaded By</th>
                            <th>File Size</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="storageTableBody">
                        <!-- Storage rows will be populated here -->
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="pagination">
                    <div class="pagination-info" id="storagePaginationInfo">
                        Showing 0 of 0 files
                    </div>
                    <div class="pagination-controls" id="storagePaginationControls">
                        <!-- Pagination buttons -->
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Hidden file input -->
<input type="file" id="fileInput" accept=".csv,.xlsx,.xls,.dat" style="display: none;">

<!-- Spreadsheet Viewer Modal -->
<div id="spreadsheetViewerModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 10000; justify-content: center; align-items: center; padding: 20px;">
    <div style="background: white; border-radius: 20px; width: 95%; max-width: 1400px; max-height: 90vh; overflow: hidden; display: flex; flex-direction: column;">
        <div style="padding: 20px 24px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
            <h2 style="font-size: 18px; font-weight: 600; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-file-excel" style="color: #10b981;"></i> 
                <span id="viewerFileName">File Viewer</span>
                <span class="summary-tooltip-container" id="summaryTooltipContainer" style="display: none;">
                    <i class="fas fa-info-circle summary-tooltip-icon"></i>
                    <div class="summary-tooltip-content" id="summaryTooltipContent">
                        <!-- Populated dynamically -->
                    </div>
                </span>
            </h2>
            <button onclick="closeSpreadsheetViewer()" style="background: none; border: none; font-size: 20px; cursor: pointer; color: #94a3b8;"><i class="fas fa-times"></i></button>
        </div>
        <div id="spreadsheetViewerContent" style="flex: 1; overflow: auto; padding: 20px;">
            <!-- Content will be populated dynamically -->
        </div>
        <div id="spreadsheetViewerFooter" style="padding: 16px 24px; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 12px;">
            <button id="spreadsheetCancelBtn" class="btn btn-secondary" onclick="closeSpreadsheetViewer()"><i class="fas fa-times"></i> Cancel</button>
            <button id="spreadsheetImportBtn" class="btn btn-primary" onclick="confirmImportFromViewer()"><i class="fas fa-check"></i> Import Data</button>
        </div>
    </div>
</div>

<!-- Include Modal Components -->
<?php include 'modals/modal-wrapper.php'; ?>
<?php include 'modals/attendance-modal/modal-add-roster.php'; ?>
<?php include 'modals/attendance-modal/modal-edit-roster.php'; ?>
<?php include 'modals/attendance-modal/modal-view-roster.php'; ?>
<?php include 'modals/attendance-modal/modal-add-correction.php'; ?>
<?php include 'modals/attendance-modal/modal-edit-correction.php'; ?>
<?php include 'modals/attendance-modal/modal-view-correction.php'; ?>
<?php include 'modals/attendance-modal/modal-attendance-helpers.php'; ?>

<script>
    // ==================== DATA INITIALIZATION ====================
    
    // Initialize data arrays
    window.importHistory = [];
    window.importPreviewData = {};
    window.rosters = [];
    window.corrections = [];
    
    // API base URL
    const API_BASE = '/3ME/api/attendance';
    
    // Load data from API
    async function loadAttendanceData() {
        try {
            // Load imports
            const importsResponse = await fetch(`${API_BASE}/imports.php`);
            const importsData = await importsResponse.json();
            if (importsData.success) {
                window.importHistory = importsData.data;
            }
            
            // Load rosters
            const rostersResponse = await fetch(`${API_BASE}/rosters.php`);
            const rostersData = await rostersResponse.json();
            if (rostersData.success) {
                window.rosters = rostersData.data;
            }
            
            // Load corrections
            const correctionsResponse = await fetch(`${API_BASE}/corrections.php`);
            const correctionsData = await correctionsResponse.json();
            if (correctionsData.success) {
                window.corrections = correctionsData.data;
            }
            
            // Refresh all tables
            filteredImports = [...window.importHistory];
            filteredRosters = [...window.rosters];
            filteredCorrections = [...window.corrections];
            
            renderImportHistoryLevel();
            renderRosterTable(filteredRosters);
            renderCorrectionTable(filteredCorrections);
            
        } catch (error) {
            console.error('Error loading attendance data:', error);
            showToast('Error loading data from server', 'error');
        }
    }
    
    // Load import preview data
    async function loadImportPreview(importId) {
        try {
            const response = await fetch(`${API_BASE}/imports.php?action=preview&import_id=${importId}`);
            const data = await response.json();
            if (data.success) {
                window.importPreviewData[importId] = {
                    headers: data.headers,
                    rows: data.rows
                };
                return window.importPreviewData[importId];
            }
        } catch (error) {
            console.error('Error loading import preview:', error);
        }
        return { headers: [], rows: [] };
    }
    
    // Generate default preview data for imports without stored data
    function generateDefaultPreviewData(importId) {
        const imp = window.importHistory.find(i => i.id === importId);
        if (!imp) return { headers: [], rows: [] };
        
        return {
            headers: ['Employee ID', 'Name', 'Date', 'Time In', 'Time Out', 'Total Hours', 'Status'],
            rows: []
        };
    }

    // ==================== STATE VARIABLES ====================
    let currentImportLevel = 'history'; // 'history' or 'preview' or 'spreadsheet'
    let selectedImportId = null;
    let selectedImportName = '';
    let currentPreviewPage = 1;
    let previewItemsPerPage = 10;
    let selectedFile = null;
    let spreadsheetData = null;
    let currentSheetIndex = 0;
    let columnVisibility = [];
    let zoomLevel = 1;

    let currentImportPage = 1;
    let currentRosterPage = 1;
    let currentCorrectionPage = 1;
    let currentStoragePage = 1;
    let itemsPerPage = 8;
    let filteredImports = [...window.importHistory];
    let filteredRosters = [...window.rosters];
    let filteredCorrections = [...window.corrections];
    let filteredStorage = [];

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

    function showLoading() {
        const overlay = document.createElement('div');
        overlay.id = 'loadingOverlay';
        overlay.className = 'loading-overlay';
        overlay.innerHTML = `
            <div class="loading-spinner">
                <i class="fas fa-spinner"></i>
                <p>Processing file...</p>
            </div>
        `;
        document.body.appendChild(overlay);
    }

    function hideLoading() {
        const overlay = document.getElementById('loadingOverlay');
        if (overlay) overlay.remove();
    }

    // ==================== SPREADSHEET VIEWER FUNCTIONS ====================
    
    function openSpreadsheetViewer(file) {
        showLoading();
        
        const reader = new FileReader();
        reader.onload = function(e) {
            try {
                const data = new Uint8Array(e.target.result);
                const workbook = XLSX.read(data, { type: 'array' });
                
                 spreadsheetData = {
                    workbook: workbook,
                    sheets: workbook.SheetNames,
                    currentSheet: workbook.SheetNames[0],
                    data: [],
                    fileSize: file.size,
                    fileType: file.name.split('.').pop().toUpperCase()
                };
                
                // Convert first sheet to JSON
                const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
                const jsonData = XLSX.utils.sheet_to_json(firstSheet, { header: 1 });
                spreadsheetData.data = jsonData;
                spreadsheetData.headers = jsonData[0] || [];
                spreadsheetData.rows = jsonData.slice(1) || [];
                
                // Initialize column visibility
                columnVisibility = new Array(spreadsheetData.headers.length).fill(true);
                
                // Update file name in viewer
                document.getElementById('viewerFileName').textContent = file.name;
                
                // Reset buttons for new import flow
                const importBtn = document.getElementById('spreadsheetImportBtn');
                if (importBtn) {
                    importBtn.style.display = 'inline-flex';
                }
                const cancelBtn = document.getElementById('spreadsheetCancelBtn');
                if (cancelBtn) {
                    cancelBtn.innerHTML = '<i class="fas fa-times"></i> Cancel';
                }
                
                // Render spreadsheet viewer
                renderSpreadsheetViewer();
                
                // Show modal
                document.getElementById('spreadsheetViewerModal').style.display = 'flex';
                
            } catch (error) {
                console.error('Error parsing file:', error);
                showToast('Error parsing file: ' + error.message, 'error');
            } finally {
                hideLoading();
            }
        };
        
        reader.onerror = function() {
            hideLoading();
            showToast('Error reading file', 'error');
        };
        
        reader.readAsArrayBuffer(file);
    }

     function closeSpreadsheetViewer() {
        document.getElementById('spreadsheetViewerModal').style.display = 'none';
        const tooltipContainer = document.getElementById('summaryTooltipContainer');
        if (tooltipContainer) {
            tooltipContainer.style.display = 'none';
        }
        spreadsheetData = null;
    }

    async function openSpreadsheetViewerForExisting(importId, fileName) {
        showLoading();
        try {
            let fileParsedSuccessfully = false;
            
            // Try to fetch original raw file first
            try {
                const response = await fetch(`${API_BASE}/upload.php?import_id=${importId}`);
                if (response.ok) {
                    // Check if it returned JSON error or the actual file binary
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        const errResult = await response.json();
                        console.warn('File download returned JSON error:', errResult);
                    } else {
                        const buffer = await response.arrayBuffer();
                        const data = new Uint8Array(buffer);
                        
                        // Parse with SheetJS
                        const workbook = XLSX.read(data, { type: 'array' });
                        
                        const imp = window.importHistory.find(i => i.id === importId);
                        
                        spreadsheetData = {
                            workbook: workbook,
                            sheets: workbook.SheetNames,
                            currentSheet: workbook.SheetNames[0],
                            data: [],
                            fileSize: imp ? imp.fileSize : buffer.byteLength,
                            fileType: imp ? imp.fileType : fileName.split('.').pop().toUpperCase()
                        };
                        
                        // Convert first sheet to JSON
                        const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
                        const jsonData = XLSX.utils.sheet_to_json(firstSheet, { header: 1 });
                        spreadsheetData.data = jsonData;
                        spreadsheetData.headers = jsonData[0] || [];
                        spreadsheetData.rows = jsonData.slice(1) || [];
                        
                        fileParsedSuccessfully = true;
                    }
                }
            } catch (fetchErr) {
                console.warn('Failed to fetch raw file, falling back to DB preview:', fetchErr);
            }
            
            // Fallback: If raw file was deleted or fetch failed, load database preview data
            if (!fileParsedSuccessfully) {
                let previewData = window.importPreviewData[importId];
                if (!previewData) {
                    previewData = await loadImportPreview(importId);
                }
                
                if (!previewData) {
                    previewData = { headers: [], rows: [] };
                }
                
                // Fallback if headers are empty to ensure viewer always displays
                if (!previewData.headers || previewData.headers.length === 0) {
                    previewData.headers = ['Employee ID', 'Name', 'Date', 'Time In', 'Time Out', 'Total Hours', 'Status', 'Remarks'];
                    previewData.rows = [];
                }
                
                const imp = window.importHistory.find(i => i.id === importId);
                spreadsheetData = {
                    workbook: null,
                    sheets: ['Imported Data (Database)'],
                    currentSheet: 'Imported Data (Database)',
                    data: [previewData.headers, ...previewData.rows],
                    headers: previewData.headers,
                    rows: previewData.rows,
                    fileSize: imp ? imp.fileSize : 0,
                    fileType: imp ? imp.fileType : 'CSV'
                };
            }
            
            // Initialize column visibility
            columnVisibility = new Array(spreadsheetData.headers.length).fill(true);
            
            // Update file name in viewer
            document.getElementById('viewerFileName').textContent = fileName;
            
            // Hide the "Import Data" button since it is already imported
            const importBtn = document.getElementById('spreadsheetImportBtn');
            if (importBtn) {
                importBtn.style.display = 'none';
            }
            const cancelBtn = document.getElementById('spreadsheetCancelBtn');
            if (cancelBtn) {
                cancelBtn.innerHTML = '<i class="fas fa-times"></i> Close';
            }
            
            // Render spreadsheet viewer
            renderSpreadsheetViewer();
            
            // Show modal
            document.getElementById('spreadsheetViewerModal').style.display = 'flex';
            
        } catch (error) {
            console.error('Error loading import details:', error);
            showToast('Error loading import details', 'error');
        } finally {
            hideLoading();
        }
    }

    function renderSpreadsheetViewer() {
        if (!spreadsheetData) return;
        
        const container = document.getElementById('spreadsheetViewerContent');
        const headers = spreadsheetData.headers;
        const rows = spreadsheetData.rows;
        const sheets = spreadsheetData.sheets;
        const currentSheet = spreadsheetData.currentSheet;
        
        // Calculate summary stats
        const totalRows = rows.length;
        const totalColumns = headers.length;
        const nonEmptyRows = rows.filter(row => row.some(cell => cell !== null && cell !== undefined && cell !== '')).length;
        
        // File Size & Type
        let fileSizeStr = 'N/A';
        if (spreadsheetData.fileSize) {
            const kb = spreadsheetData.fileSize / 1024;
            if (kb >= 1024) {
                fileSizeStr = (kb / 1024).toFixed(2) + ' MB';
            } else {
                fileSizeStr = kb.toFixed(2) + ' KB';
            }
        }
        const fileTypeStr = spreadsheetData.fileType || 'N/A';
        
        // Populate tooltip at the top
        const tooltipContent = document.getElementById('summaryTooltipContent');
        if (tooltipContent) {
            tooltipContent.innerHTML = `
                <div class="tooltip-title">
                    <i class="fas fa-chart-bar"></i> File Summary
                </div>
                <div class="tooltip-row">
                    <span class="tooltip-label">Total Records:</span>
                    <span class="tooltip-value">${totalRows.toLocaleString()}</span>
                </div>
                <div class="tooltip-row">
                    <span class="tooltip-label">Columns:</span>
                    <span class="tooltip-value">${totalColumns}</span>
                </div>
                <div class="tooltip-row">
                    <span class="tooltip-label">Non-Empty Rows:</span>
                    <span class="tooltip-value">${nonEmptyRows.toLocaleString()}</span>
                </div>
                <div class="tooltip-row">
                    <span class="tooltip-label">File Size:</span>
                    <span class="tooltip-value">${fileSizeStr}</span>
                </div>
                <div class="tooltip-row">
                    <span class="tooltip-label">File Type:</span>
                    <span class="tooltip-value">${fileTypeStr}</span>
                </div>
            `;
            
            const tooltipContainer = document.getElementById('summaryTooltipContainer');
            if (tooltipContainer) {
                tooltipContainer.style.display = 'inline-flex';
            }
        }
        
        let html = `
            <div class="spreadsheet-viewer">
                <!-- Sheet Tabs -->
                <div class="spreadsheet-toolbar">
                    <div class="spreadsheet-tabs">
                        ${sheets.map((sheet, index) => `
                            <div class="sheet-tab ${sheet === currentSheet ? 'active' : ''}" onclick="switchSheet(${index})">
                                ${escapeHtml(sheet)}
                            </div>
                        `).join('')}
                    </div>
                    <div class="spreadsheet-actions">
                        <button class="btn btn-secondary btn-sm" onclick="toggleColumnSelector()">
                            <i class="fas fa-columns"></i> Columns
                        </button>
                        <button class="btn btn-secondary btn-sm" onclick="exportCurrentSheet()">
                            <i class="fas fa-download"></i> Export
                        </button>
                    </div>
                </div>
                
                <!-- Column Visibility Selector -->
                <div id="columnSelector" class="column-selector" style="display: none;">
                    ${headers.map((header, index) => `
                        <label class="column-checkbox">
                            <input type="checkbox" ${columnVisibility[index] ? 'checked' : ''} onchange="toggleColumn(${index})">
                            <span>${escapeHtml(String(header || `Column ${index + 1}`))}</span>
                        </label>
                    `).join('')}
                </div>
                
                <!-- Spreadsheet Table -->
                <div class="spreadsheet-container" id="spreadsheetContainer">
                    <table class="spreadsheet-table">
                        <thead>
                            <tr>
                                <th style="width: 50px;">#</th>
                                ${headers.map((header, index) => 
                                    columnVisibility[index] ? `<th>${escapeHtml(String(header || `Column ${index + 1}`))}</th>` : ''
                                ).join('')}
                            </tr>
                        </thead>
                        <tbody>
                            ${rows.map((row, rowIndex) => `
                                <tr>
                                    <td class="row-number">${rowIndex + 1}</td>
                                    ${row.map((cell, colIndex) => 
                                        columnVisibility[colIndex] ? `<td>${escapeHtml(String(cell !== null && cell !== undefined ? cell : ''))}</td>` : ''
                                    ).join('')}
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
                
                <!-- Footer with Stats -->
                <div class="spreadsheet-footer">
                    <div class="sheet-info">
                        <span><i class="fas fa-table"></i> ${totalRows} rows × ${totalColumns} columns</span>
                        <span><i class="fas fa-check-circle" style="color: #10b981;"></i> ${nonEmptyRows} non-empty rows</span>
                    </div>
                    <div class="zoom-control">
                        <button onclick="zoomOut()"><i class="fas fa-search-minus"></i></button>
                        <span>${Math.round(zoomLevel * 100)}%</span>
                        <button onclick="zoomIn()"><i class="fas fa-search-plus"></i></button>
                    </div>
                </div>
            </div>
        `;
        
        container.innerHTML = html;
    }

    function switchSheet(index) {
        if (!spreadsheetData) return;
        
        const workbook = spreadsheetData.workbook;
        const sheetName = spreadsheetData.sheets[index];
        const sheet = workbook.Sheets[sheetName];
        const jsonData = XLSX.utils.sheet_to_json(sheet, { header: 1 });
        
        spreadsheetData.currentSheet = sheetName;
        spreadsheetData.data = jsonData;
        spreadsheetData.headers = jsonData[0] || [];
        spreadsheetData.rows = jsonData.slice(1) || [];
        
        // Reset column visibility for new sheet
        columnVisibility = new Array(spreadsheetData.headers.length).fill(true);
        
        renderSpreadsheetViewer();
    }

    function toggleColumnSelector() {
        const selector = document.getElementById('columnSelector');
        if (selector) {
            selector.style.display = selector.style.display === 'none' ? 'flex' : 'none';
        }
    }

    function toggleColumn(index) {
        columnVisibility[index] = !columnVisibility[index];
        renderSpreadsheetViewer();
    }

    function zoomIn() {
        zoomLevel = Math.min(zoomLevel + 0.1, 2);
        const container = document.getElementById('spreadsheetContainer');
        if (container) {
            container.style.fontSize = (12 * zoomLevel) + 'px';
        }
    }

    function zoomOut() {
        zoomLevel = Math.max(zoomLevel - 0.1, 0.5);
        const container = document.getElementById('spreadsheetContainer');
        if (container) {
            container.style.fontSize = (12 * zoomLevel) + 'px';
        }
    }

    function exportCurrentSheet() {
        if (!spreadsheetData) return;
        
        // Create a new workbook with just the current sheet
        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.aoa_to_sheet([spreadsheetData.headers, ...spreadsheetData.rows]);
        XLSX.utils.book_append_sheet(wb, ws, spreadsheetData.currentSheet);
        
        // Generate filename
        const baseName = selectedFile ? selectedFile.name.replace(/\.[^/.]+$/, "") : "export";
        const fileName = `${baseName}_${spreadsheetData.currentSheet}.xlsx`;
        
        // Download
        XLSX.writeFile(wb, fileName);
        showToast('Sheet exported successfully!', 'success');
    }

    function confirmImportFromViewer() {
        if (!spreadsheetData) return;
        
        // Get field mappings
        const mappings = {
            employeeId: document.getElementById('mapEmployeeId')?.value,
            employeeName: document.getElementById('mapEmployeeName')?.value,
            date: document.getElementById('mapDate')?.value,
            timeIn: document.getElementById('mapTimeIn')?.value,
            timeOut: document.getElementById('mapTimeOut')?.value,
            status: document.getElementById('mapStatus')?.value
        };
        
        // Store spreadsheet data before closing viewer
        const dataToImport = {
            headers: [...spreadsheetData.headers],
            rows: [...spreadsheetData.rows],
            currentSheet: spreadsheetData.currentSheet
        };
        
        closeSpreadsheetViewer();
        
        // Process the import with stored data
        processImportWithMappings(mappings, dataToImport);
    }

    async function processImportWithMappings(mappings, dataToImport) {
        showToast('Uploading file to server...', 'info');
        
        try {
            // Step 1: Upload the actual file to the server for storage
            const formData = new FormData();
            formData.append('file', selectedFile);
            formData.append('importedBy', 'Current User');
            
            const uploadResponse = await fetch(`${API_BASE}/upload.php`, {
                method: 'POST',
                body: formData
            });
            const uploadResult = await uploadResponse.json();
            
            if (!uploadResult.success) {
                showToast(uploadResult.message || 'File upload failed', 'error');
                return;
            }
            
            showToast('File uploaded! Processing data...', 'info');
            
            // Map columns fuzzy matching
            let employeeIdIdx = -1;
            let employeeNameIdx = -1;
            let dateIdx = -1;
            let timeInIdx = -1;
            let timeOutIdx = -1;
            let totalHoursIdx = -1;
            let statusIdx = -1;
            let remarksIdx = -1;
            
            dataToImport.headers.forEach((header, index) => {
                const h = String(header).toLowerCase().replace(/[^a-z0-9]/g, '');
                
                // Match Employee ID
                if (['employeeid', 'empid', 'employee_id', 'emp_id', 'id', 'employeeno', 'employeenumber', 'empno', 'staffid'].includes(h)) {
                    employeeIdIdx = index;
                }
                // Match Employee Name
                else if (['employeename', 'employee_name', 'name', 'empname', 'fullname', 'full_name', 'staffname'].includes(h)) {
                    employeeNameIdx = index;
                }
                // Match Date
                else if (['date', 'attendancedate', 'logdate', 'workdate'].includes(h)) {
                    dateIdx = index;
                }
                // Match Time In
                else if (['timein', 'time_in', 'clockin', 'in', 'checkin', 'time_in_am', 'timeinam', 'amin', 'in_time', 'intime'].includes(h)) {
                    timeInIdx = index;
                }
                // Match Time Out
                else if (['timeout', 'time_out', 'clockout', 'out', 'checkout', 'time_out_pm', 'timeoutpm', 'pmout', 'out_time', 'outtime'].includes(h)) {
                    timeOutIdx = index;
                }
                // Match Total Hours
                else if (['totalhours', 'total_hours', 'hours', 'workhours', 'workedhours', 'hrs', 'totalhrs', 'total_hrs'].includes(h)) {
                    totalHoursIdx = index;
                }
                // Match Status
                else if (['status', 'attendancestatus', 'type', 'remarkstatus'].includes(h)) {
                    statusIdx = index;
                }
                // Match Remarks
                else if (['remarks', 'remark', 'notes', 'note', 'comment', 'comments'].includes(h)) {
                    remarksIdx = index;
                }
            });
                        const numCols = dataToImport.headers.length;
            
            // Fuzzy fallback if not matched exactly
            if (employeeIdIdx === -1) {
                employeeIdIdx = dataToImport.headers.findIndex(h => {
                    const s = String(h).toLowerCase();
                    return s.includes('id') || s.includes('no') || s.includes('number');
                });
                if (employeeIdIdx === -1) employeeIdIdx = 0; // fallback to first column
            }
            if (employeeNameIdx === -1) {
                employeeNameIdx = dataToImport.headers.findIndex(h => {
                    const s = String(h).toLowerCase();
                    return s.includes('name') || s.includes('employee') || s.includes('staff');
                });
                if (employeeNameIdx === -1) employeeNameIdx = Math.min(1, numCols - 1); // fallback to second column
            }
            if (dateIdx === -1) {
                dateIdx = dataToImport.headers.findIndex(h => String(h).toLowerCase().includes('date') || String(h).toLowerCase().includes('day'));
                if (dateIdx === -1) dateIdx = numCols > 2 ? 2 : -1; // fallback to third column if exists
            }
            if (timeInIdx === -1) {
                timeInIdx = dataToImport.headers.findIndex(h => {
                    const s = String(h).toLowerCase();
                    return s.includes('in') || s.includes('start') || s.includes('entry');
                });
                if (timeInIdx === -1) timeInIdx = numCols > 3 ? 3 : -1;
            }
            if (timeOutIdx === -1) {
                timeOutIdx = dataToImport.headers.findIndex(h => {
                    const s = String(h).toLowerCase();
                    return s.includes('out') || s.includes('end') || s.includes('exit');
                });
                if (timeOutIdx === -1) timeOutIdx = numCols > 4 ? 4 : -1;
            }
            if (totalHoursIdx === -1) {
                totalHoursIdx = dataToImport.headers.findIndex(h => {
                    const s = String(h).toLowerCase();
                    return s.includes('hour') || s.includes('time') || s.includes('duration') || s.includes('work');
                });
                if (totalHoursIdx === -1) totalHoursIdx = numCols > 5 ? 5 : -1;
            }
            if (statusIdx === -1) {
                statusIdx = dataToImport.headers.findIndex(h => String(h).toLowerCase().includes('status') || String(h).toLowerCase().includes('type'));
                if (statusIdx === -1) statusIdx = numCols > 6 ? 6 : -1;
            }
            if (remarksIdx === -1) {
                remarksIdx = dataToImport.headers.findIndex(h => String(h).toLowerCase().includes('remark') || String(h).toLowerCase().includes('note') || String(h).toLowerCase().includes('comment'));
                if (remarksIdx === -1) remarksIdx = numCols > 7 ? 7 : -1;
            }

            const mappedRows = dataToImport.rows.map(row => {
                return {
                    employee_id: employeeIdIdx !== -1 && employeeIdIdx < row.length && row[employeeIdIdx] !== undefined && row[employeeIdIdx] !== null ? String(row[employeeIdIdx]) : '',
                    employee_name: employeeNameIdx !== -1 && employeeNameIdx < row.length && row[employeeNameIdx] !== undefined && row[employeeNameIdx] !== null ? String(row[employeeNameIdx]) : '',
                    date: dateIdx !== -1 && dateIdx < row.length && row[dateIdx] !== undefined && row[dateIdx] !== null ? String(row[dateIdx]) : '',
                    time_in: timeInIdx !== -1 && timeInIdx < row.length && row[timeInIdx] !== undefined && row[timeInIdx] !== null ? String(row[timeInIdx]) : '',
                    time_out: timeOutIdx !== -1 && timeOutIdx < row.length && row[timeOutIdx] !== undefined && row[timeOutIdx] !== null ? String(row[timeOutIdx]) : '',
                    total_hours: totalHoursIdx !== -1 && totalHoursIdx < row.length && row[totalHoursIdx] !== undefined && row[totalHoursIdx] !== null ? String(row[totalHoursIdx]) : '',
                    status: statusIdx !== -1 && statusIdx < row.length && row[statusIdx] !== undefined && row[statusIdx] !== null ? String(row[statusIdx]) : '',
                    remarks: remarksIdx !== -1 && remarksIdx < row.length && row[remarksIdx] !== undefined && row[remarksIdx] !== null ? String(row[remarksIdx]) : ''
                };
            });
            
            // Filter out completely empty mapped rows prior to calling POST
            const cleanedMappedRows = mappedRows.filter(row => {
                return Object.values(row).some(val => val !== null && val.trim() !== '');
            });
            
            // Step 2: Send the parsed data to the imports API for record keeping
            const importData = {
                importId: uploadResult.importId,
                fileName: selectedFile.name,
                fileType: uploadResult.fileType,
                importedBy: 'Current User',
                data: cleanedMappedRows
            };
            
            // Post the parsed import data to the imports API to store in the database
            const importResponse = await fetch(`${API_BASE}/imports.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(importData)
            });
            const importResult = await importResponse.json();
            
            if (!importResult.success) {
                showToast(importResult.message || 'Error processing import data', 'error');
                return;
            }
            
            // Create the local import record for UI display
            const newImport = {
                id: uploadResult.importId,
                fileName: selectedFile.name,
                fileType: uploadResult.fileType,
                importDate: new Date().toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' }) + 
                           ' ' + new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }),
                importedBy: 'Current User',
                totalRecords: importResult.totalRecords || cleanedMappedRows.length,
                successful: importResult.successful || cleanedMappedRows.length,
                failed: importResult.failed || 0,
                status: importResult.status || 'Success'
            };
            
            // Store preview data
            window.importPreviewData[newImport.id] = {
                headers: dataToImport.headers,
                rows: dataToImport.rows
            };
            
            window.importHistory.unshift(newImport);
            filteredImports = [...window.importHistory];
            
            clearFileSelection();
            showToast(`Import completed! ${newImport.totalRecords} records imported. File stored on server.`, 'success');
            renderImportHistoryLevel();;
            
        } catch (error) {
            console.error('Import error:', error);
            showToast('Error during import: ' + error.message, 'error');
        }
    }

    // ==================== IMPORT NAVIGATION ====================
    function navigateToImportLevel(level) {
        if (level === 'history') {
            currentImportLevel = 'history';
            selectedImportId = null;
            selectedImportName = '';
            renderImportHistoryLevel();
        }
    }

    function navigateToPreviewData(importId, importName) {
        openSpreadsheetViewerForExisting(importId, importName);
    }

    // ==================== RENDER FUNCTIONS ====================
    
    // Render Import History Level (Main Import View)
    function renderImportHistoryLevel() {
        const container = document.getElementById('importLevelContent');
        
        // Build stats row with inline import action
        const total = window.importHistory.length;
        const successful = window.importHistory.filter(i => i.status === 'Success').length;
        const partial = window.importHistory.filter(i => i.status === 'Partial').length;
        const failed = window.importHistory.filter(i => i.status === 'Failed').length;
        const totalRecords = window.importHistory.reduce((sum, i) => sum + i.totalRecords, 0);
        
        let statsHtml = `
            <div class="stats-mini">
                <div class="stat-mini-card">
                    <i class="fas fa-upload"></i>
                    <span>${total}</span> <small>Total Imports</small>
                </div>
                <div class="stat-mini-card">
                    <i class="fas fa-check-circle"></i>
                    <span>${successful}</span> <small>Successful</small>
                </div>
                <div class="stat-mini-card">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>${partial}</span> <small>Partial</small>
                </div>
                <div class="stat-mini-card">
                    <i class="fas fa-times-circle"></i>
                    <span>${failed}</span> <small>Failed</small>
                </div>
                <div class="stat-mini-card">
                    <i class="fas fa-database"></i>
                    <span>${totalRecords.toLocaleString()}</span> <small>Records Imported</small>
                </div>
        `;
        
        // Add import action based on whether a file is selected
        if (selectedFile) {
            statsHtml += `
                <div class="selected-file-info-inline">
                    <i class="fas fa-file"></i>
                    <div class="file-details">
                        <span class="file-name">${escapeHtml(selectedFile.name)}</span>
                        <span class="file-size">(${(selectedFile.size / 1024).toFixed(2)} KB)</span>
                    </div>
                    <button class="btn btn-primary btn-sm" onclick="processSelectedFile()" style="padding: 6px 12px;">
                        <i class="fas fa-eye"></i> Preview & Import
                    </button>
                    <button class="btn-icon" onclick="clearFileSelection()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
        } else {
            statsHtml += `
                <div class="import-action-card" onclick="document.getElementById('fileInput').click()">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <div>
                        <div class="import-text">Import Attendance Data</div>
                        <div class="import-hint">CSV, Excel, or Biometric files</div>
                    </div>
                </div>
            `;
        }
        
        statsHtml += `</div>`;
        
        let html = `
            <!-- Filter Bar -->
            <div class="filter-bar">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search by file name or imported by..." id="importSearchInput" value="">
                </div>
                <select class="filter-select" id="importStatusFilter">
                    <option value="">All Status</option>
                    <option value="Success">Success</option>
                    <option value="Partial">Partial</option>
                    <option value="Failed">Failed</option>
                </select>
                <select class="filter-select" id="importTypeFilter">
                    <option value="">All File Types</option>
                    <option value="CSV">CSV</option>
                    <option value="Excel">Excel</option>
                    <option value="Biometric Export">Biometric Export</option>
                </select>
            </div>

            ${statsHtml}

            <!-- Import History Table -->
            <div class="table-card">
                <div class="table-header">
                    <h3><i class="fas fa-history"></i> Import History</h3>
                </div>
                <table class="import-table">
                    <thead>
                        <tr>
                            <th>File Name</th>
                            <th>Type</th>
                            <th>Import Date</th>
                            <th>Imported By</th>
                            <th>Records</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="importTableBody">
                        <!-- Import rows will be populated here -->
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="pagination">
                    <div class="pagination-info" id="importPaginationInfo">
                        Showing 0 of 0 imports
                    </div>
                    <div class="pagination-controls" id="importPaginationControls">
                        <!-- Pagination buttons -->
                    </div>
                </div>
            </div>
        `;
        
        container.innerHTML = html;
        
        // Render the table
        renderImportTable(filteredImports);
        
        // Setup filter listeners
        document.getElementById('importSearchInput').addEventListener('keyup', applyImportFilters);
        document.getElementById('importStatusFilter').addEventListener('change', applyImportFilters);
        document.getElementById('importTypeFilter').addEventListener('change', applyImportFilters);
    }

    // Render Preview Data Level (Drill-down view)
    function renderPreviewDataLevel() {
        const container = document.getElementById('importLevelContent');
        const imp = window.importHistory.find(i => i.id === selectedImportId);
        
        // Get preview data - load from API if not cached
        let previewData = window.importPreviewData[selectedImportId];
        if (!previewData) {
            // Load from API
            loadImportPreview(selectedImportId).then(data => {
                renderPreviewDataLevel(); // Re-render after loading
            });
            // Show loading state
            container.innerHTML = '<div style="text-align: center; padding: 40px;"><i class="fas fa-spinner fa-spin" style="font-size: 32px; color: #4f46e5;"></i><p style="margin-top: 16px; color: #64748b;">Loading preview data...</p></div>';
            return;
        }
        
        const start = (currentPreviewPage - 1) * previewItemsPerPage;
        const end = start + previewItemsPerPage;
        const paginatedRows = previewData.rows.slice(start, end);
        const totalPages = Math.ceil(previewData.rows.length / previewItemsPerPage);
        
        let html = `

            <!-- Stats -->
            <div class="stats-mini">
                <div class="stat-mini-card">
                    <i class="fas fa-file"></i>
                    <span>${escapeHtml(selectedImportName)}</span> <small>File</small>
                </div>
                <div class="stat-mini-card">
                    <i class="fas fa-database"></i>
                    <span>${previewData.rows.length}</span> <small>Total Records</small>
                </div>
                <div class="stat-mini-card">
                    <i class="fas fa-check-circle" style="color: #10b981;"></i>
                    <span>${imp ? imp.successful : previewData.rows.length}</span> <small>Successful</small>
                </div>
                <div class="stat-mini-card">
                    <i class="fas fa-times-circle" style="color: #ef4444;"></i>
                    <span>${imp ? imp.failed : 0}</span> <small>Failed</small>
                </div>
                <div class="header-actions">
                    <button class="btn btn-secondary" onclick="exportPreviewData('${selectedImportId}')"><i class="fas fa-download"></i> Export</button>
                    <button class="btn btn-primary" onclick="navigateToImportLevel('history')"><i class="fas fa-arrow-left"></i> Back</button>
                </div>
            </div>

            <!-- Preview Data Table -->
            <div class="table-card">
                <div class="table-header">
                    <h3>
                        <span class="breadcrumb-link-header" onclick="navigateToImportLevel('history')">Import History</span>
                        <i class="fas fa-chevron-right breadcrumb-separator-header"></i>
                        <span class="breadcrumb-current-header">${escapeHtml(selectedImportName)}</span>
                    </h3>
                </div>
                <div style="overflow-x: auto;">
                    <table class="preview-data-table">
                        <thead>
                            <tr>
                                ${previewData.headers.map(h => `<th>${escapeHtml(h)}</th>`).join('')}
                            </tr>
                        </thead>
                        <tbody id="previewTableBody">
                            ${paginatedRows.map(row => `
                                <tr>
                                    ${row.map(cell => `<td>${escapeHtml(String(cell))}</td>`).join('')}
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="pagination">
                    <div class="pagination-info" id="previewPaginationInfo">
                        Showing ${start + 1}-${Math.min(end, previewData.rows.length)} of ${previewData.rows.length} records
                    </div>
                    <div class="pagination-controls" id="previewPaginationControls">
                        ${renderPreviewPagination(currentPreviewPage, totalPages)}
                    </div>
                </div>
            </div>
        `;
        
        container.innerHTML = html;
    }

    function renderPreviewPagination(currentPage, totalPages) {
        let html = '';
        html += `<div class="page-btn" onclick="changePreviewPage(${currentPage - 1})" ${currentPage === 1 ? 'style="opacity:0.5; pointer-events:none;"' : ''}><i class="fas fa-chevron-left"></i></div>`;
        for (let i = 1; i <= Math.min(totalPages, 5); i++) {
            html += `<div class="page-btn ${currentPage === i ? 'active' : ''}" onclick="changePreviewPage(${i})">${i}</div>`;
        }
        if (totalPages > 5) {
            html += `<div class="page-btn">...</div>`;
        }
        html += `<div class="page-btn" onclick="changePreviewPage(${currentPage + 1})" ${currentPage === totalPages || totalPages === 0 ? 'style="opacity:0.5; pointer-events:none;"' : ''}><i class="fas fa-chevron-right"></i></div>`;
        return html;
    }

    function changePreviewPage(page) {
        const previewData = window.importPreviewData[selectedImportId] || generateDefaultPreviewData(selectedImportId);
        const totalPages = Math.ceil(previewData.rows.length / previewItemsPerPage);
        if (page < 1 || page > totalPages) return;
        currentPreviewPage = page;
        renderPreviewDataLevel();
    }

    // Render import table
    function renderImportTable(data) {
        filteredImports = data;
        
        const start = (currentImportPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const paginatedData = filteredImports.slice(start, end);
        
        const tbody = document.getElementById('importTableBody');
        if (tbody) {
            tbody.innerHTML = paginatedData.map((imp) => {
                const statusClass = {
                    'Success': 'badge-success',
                    'Partial': 'badge-warning',
                    'Failed': 'badge-danger'
                }[imp.status] || 'badge-secondary';
                
                const successRate = imp.totalRecords > 0 ? (imp.successful / imp.totalRecords) * 100 : 0;
                const progressClass = imp.status === 'Success' ? '' : (imp.status === 'Partial' ? 'warning' : 'danger');
                
                return `
                    <tr class="clickable-row" onclick="navigateToPreviewData('${imp.id}', '${escapeHtml(imp.fileName)}')">
                        <td>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <i class="fas fa-file-${imp.fileType === 'CSV' ? 'csv' : (imp.fileType === 'Excel' ? 'excel' : 'file')}" style="color: #4f46e5;"></i>
                                ${escapeHtml(imp.fileName)}
                            </div>
                        </td>
                        <td>${imp.fileType}</td>
                        <td>${imp.importDate}</td>
                        <td>${imp.importedBy}</td>
                        <td>
                            <div class="import-stats">
                                <span style="color: #10b981;">${imp.successful}</span> / 
                                <span style="color: #ef4444;">${imp.failed}</span> / 
                                <span>${imp.totalRecords}</span>
                                <div class="progress-bar">
                                    <div class="progress-fill ${progressClass}" style="width: ${successRate}%;"></div>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge ${statusClass}">${imp.status}</span></td>
                        <td class="action-icons" onclick="event.stopPropagation()">
                            <i class="fas fa-eye" onclick="viewImportDetails('${imp.id}')" title="View Details"></i>
                            <i class="fas fa-download" onclick="downloadImportLog('${imp.id}')" title="Download Log"></i>
                            ${imp.status === 'Failed' || imp.status === 'Partial' ? 
                                `<i class="fas fa-exclamation-circle" onclick="viewErrors('${imp.id}')" title="View Errors" style="color: #ef4444;"></i>` : ''}
                            <i class="fas fa-trash" onclick="deleteImportRecord('${imp.id}')" title="Delete Import" style="color: #ef4444;"></i>
                        </td>
                    </tr>
                `;
            }).join('');
        }
        
        const totalPages = Math.ceil(filteredImports.length / itemsPerPage);
        const infoEl = document.getElementById('importPaginationInfo');
        if (infoEl) {
            infoEl.textContent = `Showing ${start + 1}-${Math.min(end, filteredImports.length)} of ${filteredImports.length} imports`;
        }
        
        const paginationContainer = document.getElementById('importPaginationControls');
        if (paginationContainer) {
            let paginationHtml = '';
            paginationHtml += `<div class="page-btn" onclick="changeImportPage(${currentImportPage - 1})" ${currentImportPage === 1 ? 'style="opacity:0.5; pointer-events:none;"' : ''}><i class="fas fa-chevron-left"></i></div>`;
            for (let i = 1; i <= Math.min(totalPages, 5); i++) {
                paginationHtml += `<div class="page-btn ${currentImportPage === i ? 'active' : ''}" onclick="changeImportPage(${i})">${i}</div>`;
            }
            if (totalPages > 5) {
                paginationHtml += `<div class="page-btn">...</div>`;
            }
            paginationHtml += `<div class="page-btn" onclick="changeImportPage(${currentImportPage + 1})" ${currentImportPage === totalPages || totalPages === 0 ? 'style="opacity:0.5; pointer-events:none;"' : ''}><i class="fas fa-chevron-right"></i></div>`;
            paginationContainer.innerHTML = paginationHtml;
        }
    }

    function updateRosterStats() {
        document.getElementById('totalRosters').innerText = window.rosters.length;
        const uniqueCompanies = [...new Set(window.rosters.map(r => r.companyId))];
        document.getElementById('uniqueCompanies').innerText = uniqueCompanies.length;
    }

    function updateCorrectionStats() {
        const total = window.corrections.length;
        const pending = window.corrections.filter(c => c.status === 'Pending').length;
        const approved = window.corrections.filter(c => c.status === 'Approved').length;
        const rejected = window.corrections.filter(c => c.status === 'Rejected').length;
        
        document.getElementById('totalCorrections').innerText = total;
        document.getElementById('pendingCorrections').innerText = pending;
        document.getElementById('approvedCorrections').innerText = approved;
        document.getElementById('rejectedCorrections').innerText = rejected;
    }

    // Render roster table
    async function renderRosterTable(data) {
        filteredRosters = data;
        updateRosterStats();
        
        // Load companies to map IDs to names
        const companies = await window.loadCompaniesFromDB();
        const companyMap = {};
        companies.forEach(c => {
            companyMap[c.id] = c.name;
        });
        
        const start = (currentRosterPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const paginatedData = filteredRosters.slice(start, end);
        
        const tbody = document.getElementById('rosterTableBody');
        tbody.innerHTML = paginatedData.map((roster) => {
            const companyName = companyMap[roster.companyId] || roster.company || roster.companyId;
            
            return `
                <tr>
                    <td>
                        <div class="shift-badge">
                            <i class="fas fa-clock"></i>
                            ${escapeHtml(roster.shiftName)}
                        </div>
                    </td>
                    <td>${escapeHtml(companyName)}</td>
                    <td>
                        <span style="font-weight: 500;">${roster.startTime}</span>
                        <span style="color: #94a3b8;"> - </span>
                        <span style="font-weight: 500;">${roster.endTime}</span>
                    </td>
                    <td title="${escapeHtml(roster.overtimeRule)}">${escapeHtml(roster.overtimeRule.substring(0, 30))}${roster.overtimeRule.length > 30 ? '...' : ''}</td>
                    <td>${roster.effectiveDate}</td>
                    <td class="action-icons">
                        <i class="fas fa-eye" onclick="viewRoster('${roster.id}')" title="View"></i>
                        <i class="fas fa-edit" onclick="editRoster('${roster.id}')" title="Edit"></i>
                        <i class="fas fa-copy" onclick="duplicateRoster('${roster.id}')" title="Duplicate"></i>
                        <i class="fas fa-users" onclick="assignEmployees('${roster.id}')" title="Assign Employees"></i>
                    </td>
                </tr>
            `;
        }).join('');
        
        const totalPages = Math.ceil(filteredRosters.length / itemsPerPage);
        document.getElementById('rosterPaginationInfo').textContent = `Showing ${start + 1}-${Math.min(end, filteredRosters.length)} of ${filteredRosters.length} rosters`;
        
        const paginationContainer = document.getElementById('rosterPaginationControls');
        let paginationHtml = '';
        paginationHtml += `<div class="page-btn" onclick="changeRosterPage(${currentRosterPage - 1})" ${currentRosterPage === 1 ? 'style="opacity:0.5; pointer-events:none;"' : ''}><i class="fas fa-chevron-left"></i></div>`;
        for (let i = 1; i <= Math.min(totalPages, 5); i++) {
            paginationHtml += `<div class="page-btn ${currentRosterPage === i ? 'active' : ''}" onclick="changeRosterPage(${i})">${i}</div>`;
        }
        if (totalPages > 5) {
            paginationHtml += `<div class="page-btn">...</div>`;
        }
        paginationHtml += `<div class="page-btn" onclick="changeRosterPage(${currentRosterPage + 1})" ${currentRosterPage === totalPages || totalPages === 0 ? 'style="opacity:0.5; pointer-events:none;"' : ''}><i class="fas fa-chevron-right"></i></div>`;
        paginationContainer.innerHTML = paginationHtml;
    }

    // Render correction table
    function renderCorrectionTable(data) {
        filteredCorrections = data;
        updateCorrectionStats();
        
        const start = (currentCorrectionPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const paginatedData = filteredCorrections.slice(start, end);
        
        const tbody = document.getElementById('correctionTableBody');
        tbody.innerHTML = paginatedData.map((corr) => {
            const statusClass = {
                'Pending': 'badge-warning',
                'Approved': 'badge-success',
                'Rejected': 'badge-danger'
            }[corr.status] || 'badge-secondary';
            
            const typeClass = {
                'Late': 'badge-warning',
                'Early Departure': 'badge-info',
                'Missed Entry': 'badge-purple',
                'Overtime Discrepancy': 'badge-secondary'
            }[corr.type] || 'badge-secondary';
            
            const reasonPreview = corr.reason.length > 25 ? corr.reason.substring(0, 25) + '...' : corr.reason;
            
            return `
                <tr>
                    <td>
                        <div class="employee-cell">
                            <img src="${corr.profilePhoto || '/3ME/assets/images/default-avatar.png'}" class="employee-avatar" style="object-fit: cover;" />
                            <div class="employee-info">
                                <h4>${escapeHtml(corr.employeeName)}</h4>
                                <p>${escapeHtml(corr.employeeEmail)}</p>
                            </div>
                        </div>
                    </td>
                    <td><span class="badge ${typeClass}">${corr.type}</span></td>
                    <td>${corr.originalDate}</td>
                    <td>
                        <span style="color: #10b981;">${corr.timeIn || '—'}</span> - 
                        <span style="color: #ef4444;">${corr.timeOut || '—'}</span>
                    </td>
                    <td title="${escapeHtml(corr.reason)}">${escapeHtml(reasonPreview)}</td>
                    <td><span class="badge ${statusClass}">${corr.status}</span></td>
                    <td class="action-icons">
                        <i class="fas fa-eye" onclick="viewCorrection('${corr.id}')" title="View"></i>
                        <i class="fas fa-edit" onclick="editCorrection('${corr.id}')" title="Edit"></i>
                        ${corr.status === 'Pending' ? `
                            <i class="fas fa-check-circle" onclick="approveCorrection('${corr.id}')" title="Approve" style="color: #10b981;"></i>
                            <i class="fas fa-times-circle" onclick="rejectCorrection('${corr.id}')" title="Reject" style="color: #ef4444;"></i>
                        ` : ''}
                    </td>
                </tr>
            `;
        }).join('');
        
        const totalPages = Math.ceil(filteredCorrections.length / itemsPerPage);
        document.getElementById('correctionPaginationInfo').textContent = `Showing ${start + 1}-${Math.min(end, filteredCorrections.length)} of ${filteredCorrections.length} corrections`;
        
        const paginationContainer = document.getElementById('correctionPaginationControls');
        let paginationHtml = '';
        paginationHtml += `<div class="page-btn" onclick="changeCorrectionPage(${currentCorrectionPage - 1})" ${currentCorrectionPage === 1 ? 'style="opacity:0.5; pointer-events:none;"' : ''}><i class="fas fa-chevron-left"></i></div>`;
        for (let i = 1; i <= Math.min(totalPages, 5); i++) {
            paginationHtml += `<div class="page-btn ${currentCorrectionPage === i ? 'active' : ''}" onclick="changeCorrectionPage(${i})">${i}</div>`;
        }
        if (totalPages > 5) {
            paginationHtml += `<div class="page-btn">...</div>`;
        }
        paginationHtml += `<div class="page-btn" onclick="changeCorrectionPage(${currentCorrectionPage + 1})" ${currentCorrectionPage === totalPages || totalPages === 0 ? 'style="opacity:0.5; pointer-events:none;"' : ''}><i class="fas fa-chevron-right"></i></div>`;
        paginationContainer.innerHTML = paginationHtml;
    }

    // Pagination functions
    function changeImportPage(page) {
        const totalPages = Math.ceil(filteredImports.length / itemsPerPage);
        if (page < 1 || page > totalPages) return;
        currentImportPage = page;
        renderImportTable(filteredImports);
    }

    function changeRosterPage(page) {
        const totalPages = Math.ceil(filteredRosters.length / itemsPerPage);
        if (page < 1 || page > totalPages) return;
        currentRosterPage = page;
        renderRosterTable(filteredRosters);
    }

    function changeCorrectionPage(page) {
        const totalPages = Math.ceil(filteredCorrections.length / itemsPerPage);
        if (page < 1 || page > totalPages) return;
        currentCorrectionPage = page;
        renderCorrectionTable(filteredCorrections);
    }

    // Tab switching
    function switchTab(tab) {
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
        
        if (tab === 'import') {
            document.querySelectorAll('.tab-btn')[0].classList.add('active');
            document.getElementById('importTab').classList.add('active');
            if (currentImportLevel === 'history') {
                renderImportHistoryLevel();
            } else {
                renderPreviewDataLevel();
            }
        } else if (tab === 'roster') {
            document.querySelectorAll('.tab-btn')[1].classList.add('active');
            document.getElementById('rosterTab').classList.add('active');
            renderRosterTable(filteredRosters);
        } else if (tab === 'correction') {
            document.querySelectorAll('.tab-btn')[2].classList.add('active');
            document.getElementById('correctionTab').classList.add('active');
            renderCorrectionTable(filteredCorrections);
        } else if (tab === 'storage') {
            document.querySelectorAll('.tab-btn')[3].classList.add('active');
            document.getElementById('storageTab').classList.add('active');
            loadStorageFiles();
        }
    }

    // ==================== UPLOADED FILE STORAGE FUNCTIONS ====================
    function formatFileSize(bytes) {
        if (!bytes || bytes === 0) return '0 KB';
        const kb = bytes / 1024;
        if (kb >= 1024) {
            return (kb / 1024).toFixed(2) + ' MB';
        }
        return kb.toFixed(2) + ' KB';
    }

    function loadStorageFiles() {
        filteredStorage = [...window.importHistory];
        
        // Calculate stats
        const totalFiles = filteredStorage.length;
        const totalSizeBytes = filteredStorage.reduce((sum, item) => sum + (item.fileSize || 0), 0);
        
        const totalFilesEl = document.getElementById('totalStorageFiles');
        if (totalFilesEl) totalFilesEl.textContent = totalFiles;
        
        const totalSizeEl = document.getElementById('totalStorageSize');
        if (totalSizeEl) totalSizeEl.textContent = formatFileSize(totalSizeBytes);
        
        currentStoragePage = 1;
        applyStorageFilters();
    }

    function applyStorageFilters() {
        const searchTerm = document.getElementById('storageSearchInput').value.toLowerCase();
        const typeValue = document.getElementById('storageTypeFilter').value;
        
        filteredStorage = window.importHistory.filter(imp => {
            const name = (imp.originalName || imp.fileName || '').toLowerCase();
            const uploadedBy = (imp.importedBy || '').toLowerCase();
            const matchesSearch = name.includes(searchTerm) || uploadedBy.includes(searchTerm);
            
            let matchesType = true;
            if (typeValue) {
                if (typeValue === 'CSV') {
                    matchesType = imp.fileType.toUpperCase() === 'CSV';
                } else if (typeValue === 'Excel') {
                    matchesType = ['EXCEL', 'XLSX', 'XLS'].includes(imp.fileType.toUpperCase());
                } else if (typeValue === 'Biometric Export') {
                    matchesType = ['DAT', 'TXT', 'BIOMETRIC EXPORT'].includes(imp.fileType.toUpperCase());
                }
            }
            return matchesSearch && matchesType;
        });
        
        currentStoragePage = 1;
        renderStorageTable();
    }

    function renderStorageTable() {
        const start = (currentStoragePage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const paginatedData = filteredStorage.slice(start, end);
        
        const tbody = document.getElementById('storageTableBody');
        if (!tbody) return;
        
        if (paginatedData.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" style="text-align: center; color: #64748b; padding: 40px 20px;">
                        <i class="fas fa-folder-open" style="font-size: 28px; margin-bottom: 12px; display: block; color: #94a3b8; opacity: 0.6;"></i>
                        <span style="font-weight: 500;">No stored files found</span>
                        <p style="font-size: 11px; color: #94a3b8; margin-top: 4px;">Upload files or adjust your filters to see results.</p>
                    </td>
                </tr>
            `;
            const info = document.getElementById('storagePaginationInfo');
            if (info) info.textContent = `Showing 0 of 0 files`;
            const controls = document.getElementById('storagePaginationControls');
            if (controls) controls.innerHTML = '';
            return;
        }
        
        tbody.innerHTML = paginatedData.map(file => {
            const displayName = file.originalName || file.fileName;
            const sizeStr = formatFileSize(file.fileSize);
            const typeBadge = {
                'CSV': '<span class="badge badge-success">CSV</span>',
                'XLSX': '<span class="badge badge-primary">Excel</span>',
                'XLS': '<span class="badge badge-primary">Excel</span>',
                'EXCEL': '<span class="badge badge-primary">Excel</span>'
            }[file.fileType.toUpperCase()] || `<span class="badge badge-secondary">${file.fileType}</span>`;
            
            const uploadedAt = file.importedAt || file.createdAt || 'N/A';
            
            return `
                <tr>
                    <td style="font-weight: 500; color: #0f172a;">${escapeHtml(displayName)}</td>
                    <td>${typeBadge}</td>
                    <td>${escapeHtml(uploadedAt)}</td>
                    <td>${escapeHtml(file.importedBy)}</td>
                    <td>${sizeStr}</td>
                    <td class="action-icons">
                       <i class="fas fa-eye" onclick="previewStorageFile('${file.id}', '${escapeHtml(displayName)}')" title="Preview" style="color: #4f46e5; cursor: pointer; margin-right: 8px;"></i>
                       <i class="fas fa-download" onclick="downloadOriginalFile('${file.id}')" title="Download" style="color: #10b981; cursor: pointer; margin-right: 8px;"></i>
                       <i class="fas fa-trash" onclick="deleteStorageFile('${file.id}')" title="Delete" style="color: #ef4444; cursor: pointer;"></i>
                    </td>
                </tr>
            `;
        }).join('');
        
        const totalPages = Math.ceil(filteredStorage.length / itemsPerPage);
        const info = document.getElementById('storagePaginationInfo');
        if (info) {
            info.textContent = `Showing ${start + 1}-${Math.min(end, filteredStorage.length)} of ${filteredStorage.length} files`;
        }
        
        const paginationContainer = document.getElementById('storagePaginationControls');
        if (paginationContainer) {
            let paginationHtml = '';
            paginationHtml += `<div class="page-btn" onclick="changeStoragePage(${currentStoragePage - 1})" ${currentStoragePage === 1 ? 'style="opacity:0.5; pointer-events:none;"' : ''}><i class="fas fa-chevron-left"></i></div>`;
            for (let i = 1; i <= Math.min(totalPages, 5); i++) {
                paginationHtml += `<div class="page-btn ${currentStoragePage === i ? 'active' : ''}" onclick="changeStoragePage(${i})">${i}</div>`;
            }
            if (totalPages > 5) {
                paginationHtml += `<div class="page-btn">...</div>`;
            }
            paginationHtml += `<div class="page-btn" onclick="changeStoragePage(${currentStoragePage + 1})" ${currentStoragePage === totalPages || totalPages === 0 ? 'style="opacity:0.5; pointer-events:none;"' : ''}><i class="fas fa-chevron-right"></i></div>`;
            paginationContainer.innerHTML = paginationHtml;
        }
    }

    function changeStoragePage(page) {
        const totalPages = Math.ceil(filteredStorage.length / itemsPerPage);
        if (page < 1 || page > totalPages) return;
        currentStoragePage = page;
        renderStorageTable();
    }
    
    function previewStorageFile(importId, fileName) {
        openSpreadsheetViewerForExisting(importId, fileName);
    }
    
    function downloadOriginalFile(importId) {
        window.location.href = `${API_BASE}/upload.php?import_id=${importId}`;
    }
    
    async function deleteStorageFile(importId) {
        await deleteImportRecord(importId);
        loadStorageFiles();
    }

    // Apply filters
    function applyImportFilters() {
        const searchTerm = document.getElementById('importSearchInput').value.toLowerCase();
        const statusValue = document.getElementById('importStatusFilter').value;
        const typeValue = document.getElementById('importTypeFilter').value;
        
        let filtered = window.importHistory.filter(imp => {
            const matchesSearch = imp.fileName.toLowerCase().includes(searchTerm) ||
                                 imp.importedBy.toLowerCase().includes(searchTerm);
            const matchesStatus = !statusValue || imp.status === statusValue;
            const matchesType = !typeValue || imp.fileType === typeValue;
            return matchesSearch && matchesStatus && matchesType;
        });
        
        currentImportPage = 1;
        renderImportTable(filtered);
    }

    function applyRosterFilters() {
        const searchTerm = document.getElementById('rosterSearchInput').value.toLowerCase();
        const companyValue = document.getElementById('companyFilter').value;
        
        let filtered = window.rosters.filter(roster => {
            const companyName = (roster.company || roster.companyId || '').toLowerCase();
            const matchesSearch = roster.shiftName.toLowerCase().includes(searchTerm) ||
                                 companyName.includes(searchTerm);
            const matchesCompany = !companyValue || roster.companyId === companyValue;
            return matchesSearch && matchesCompany;
        });
        
        currentRosterPage = 1;
        renderRosterTable(filtered);
    }

    function applyCorrectionFilters() {
        const searchTerm = document.getElementById('correctionSearchInput').value.toLowerCase();
        const typeValue = document.getElementById('correctionTypeFilter').value;
        const statusValue = document.getElementById('correctionStatusFilter').value;
        
        let filtered = window.corrections.filter(corr => {
            const matchesSearch = corr.employeeName.toLowerCase().includes(searchTerm) ||
                                 corr.employeeEmail.toLowerCase().includes(searchTerm);
            const matchesType = !typeValue || corr.type === typeValue;
            const matchesStatus = !statusValue || corr.status === statusValue;
            return matchesSearch && matchesType && matchesStatus;
        });
        
        currentCorrectionPage = 1;
        renderCorrectionTable(filtered);
    }

    // File upload handlers
    function handleFileSelect(event) {
        const file = event.target.files[0];
        if (file) {
            selectedFile = file;
            renderImportHistoryLevel();
            processSelectedFile();
        }
    }

    function processSelectedFile() {
        if (!selectedFile) return;
        
        // Open spreadsheet viewer for preview
        openSpreadsheetViewer(selectedFile);
    }

    function clearFileSelection() {
        document.getElementById('fileInput').value = '';
        selectedFile = null;
        renderImportHistoryLevel();
    }

    function processImport() {
        if (!selectedFile) return;
        processSelectedFile();
    }

    async function deleteImportRecord(id) {
        if (!confirm('Are you sure you want to delete this import history record? This will also remove all imported data associated with it.')) {
            return;
        }
        try {
            const response = await fetch(`${API_BASE}/imports.php?id=${id}`, {
                method: 'DELETE'
            });
            const result = await response.json();
            if (result.success) {
                showToast('Import history record deleted successfully', 'success');
                // Remove from local array
                window.importHistory = window.importHistory.filter(imp => imp.id !== id);
                filteredImports = [...window.importHistory];
                renderImportHistoryLevel();
            } else {
                showToast(result.message || 'Failed to delete record', 'error');
            }
        } catch (error) {
            console.error('Error deleting import record:', error);
            showToast('Error deleting record', 'error');
        }
    }

    // ==================== ACTION STUB FUNCTIONS ====================
    // These delegate to modal functions defined in the included modal PHP files.
    // viewImportDetails, downloadImportLog, viewErrors, exportPreviewData → modal-attendance-helpers.php
    // viewRoster, editRoster, duplicateRoster, assignEmployees → modal-view/edit-roster.php & helpers
    // viewCorrection, editCorrection, approveCorrection, rejectCorrection → modal-view/edit-correction.php
    // All of the above are declared in the included modal files, so we just expose them globally below.

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

    // ==================== GLOBAL FUNCTION EXPORTS ====================
    // Core navigation & rendering
    window.navigateToImportLevel = navigateToImportLevel;
    window.navigateToPreviewData = navigateToPreviewData;
    window.changePreviewPage = changePreviewPage;
    window.changeImportPage = changeImportPage;
    window.changeRosterPage = changeRosterPage;
    window.changeCorrectionPage = changeCorrectionPage;
    window.switchTab = switchTab;
    // File import
    window.handleFileSelect = handleFileSelect;
    window.clearFileSelection = clearFileSelection;
    window.processImport = processImport;
    window.processSelectedFile = processSelectedFile;
    window.deleteImportRecord = deleteImportRecord;
    window.openSpreadsheetViewerForExisting = openSpreadsheetViewerForExisting;
    window.previewStorageFile = previewStorageFile;
    window.downloadOriginalFile = downloadOriginalFile;
    window.deleteStorageFile = deleteStorageFile;
    window.changeStoragePage = changeStoragePage;
    // Spreadsheet viewer
    window.closeSpreadsheetViewer = closeSpreadsheetViewer;
    window.confirmImportFromViewer = confirmImportFromViewer;
    window.switchSheet = switchSheet;
    window.toggleColumnSelector = toggleColumnSelector;
    window.toggleColumn = toggleColumn;
    window.zoomIn = zoomIn;
    window.zoomOut = zoomOut;
    window.exportCurrentSheet = exportCurrentSheet;
    // Correction in-table quick actions (also defined in modal-view-correction.php)
    window.approveCorrection = function(id) {
        const corr = window.corrections.find(c => c.id === id);
        if (corr) {
            corr.status = 'Approved';
            corr.approvedBy = 'Current User';
            renderCorrectionTable(window.corrections);
            showToast('Correction approved!', 'success');
        }
    };
    window.rejectCorrection = function(id) {
        const corr = window.corrections.find(c => c.id === id);
        if (corr) {
            corr.status = 'Rejected';
            renderCorrectionTable(window.corrections);
            showToast('Correction rejected.', 'warning');
        }
    };

    // ==================== LOAD COMPANIES FOR FILTERS ====================
    window._companiesCache = null;
    
    async function loadCompaniesFromDB() {
        if (window._companiesCache) return window._companiesCache;
        try {
            const response = await fetch('/3ME/api/settings/settings_api.php?action=list_companies');
            const result = await response.json();
            if (result.success) {
                window._companiesCache = result.data || [];
                return window._companiesCache;
            }
        } catch (error) {
            console.error('Error loading companies:', error);
        }
        return [];
    }
    
    async function populateCompanyFilter() {
        const companies = await loadCompaniesFromDB();
        const select = document.getElementById('companyFilter');
        if (!select) return;
        
        // Keep the "All Companies" option
        select.innerHTML = '<option value="">All Companies</option>';
        companies.forEach(company => {
            const option = document.createElement('option');
            option.value = company.id;
            option.textContent = company.name;
            select.appendChild(option);
        });
    }
    
    // Make available globally for modals
    window.loadCompaniesFromDB = loadCompaniesFromDB;
    window.populateCompanyFilter = populateCompanyFilter;

    // Initialize
    (async function() {
        // Set active menu
        const navItems = document.querySelectorAll('.nav-item');
        navItems.forEach(item => {
            item.classList.remove('active');
            if (item.getAttribute('data-title') === 'Time & Attendance') {
                item.classList.add('active');
            }
        });

        // Setup file input listener
        document.getElementById('fileInput').addEventListener('change', handleFileSelect);

        // Load companies for filter dropdown
        await populateCompanyFilter();

        // Load data from API
        loadAttendanceData();

        // Setup roster filter listeners
        document.getElementById('rosterSearchInput').addEventListener('keyup', applyRosterFilters);
        document.getElementById('companyFilter').addEventListener('change', applyRosterFilters);
        
        // Setup correction filter listeners
        document.getElementById('correctionSearchInput').addEventListener('keyup', applyCorrectionFilters);
        document.getElementById('correctionTypeFilter').addEventListener('change', applyCorrectionFilters);
        document.getElementById('correctionStatusFilter').addEventListener('change', applyCorrectionFilters);

        // Setup storage filter listeners
        const storageSearchInput = document.getElementById('storageSearchInput');
        if (storageSearchInput) {
            storageSearchInput.addEventListener('keyup', applyStorageFilters);
        }
        const storageTypeFilter = document.getElementById('storageTypeFilter');
        if (storageTypeFilter) {
            storageTypeFilter.addEventListener('change', applyStorageFilters);
        }

        console.log('✅ Time & Attendance loaded with database connection');
    })();
</script>

</body>
</html>