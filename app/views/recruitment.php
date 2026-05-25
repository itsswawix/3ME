<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
/**
 * recruitment.php
 * Recruitment and Applicant Tracking System - Rebuilt from scratch
 */

$pageTitle = "Recruitment & Applicants";
$activeMenu = "Recruitment";

// Include database connection
require_once '../../config/database.php';

// Initialize database connection
$database = new Database();
$conn = $database->getConnection();

// Create recruitment tables if they don't exist
try {
    // Job Requisitions Table
    $conn->exec("CREATE TABLE IF NOT EXISTS job_requisitions (
        id VARCHAR(50) PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        department VARCHAR(255),
        position_level VARCHAR(100),
        employment_type ENUM('Full-time', 'Part-time', 'Contract', 'Internship') DEFAULT 'Full-time',
        salary_min DECIMAL(15,2),
        salary_max DECIMAL(15,2),
        description TEXT,
        requirements TEXT,
        status ENUM('Open', 'Closed', 'On Hold') DEFAULT 'Open',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_req_status (status)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // Applicants Table
    $conn->exec("CREATE TABLE IF NOT EXISTS applicants (
        id VARCHAR(50) PRIMARY KEY,
        requisition_id VARCHAR(50) DEFAULT NULL,
        job_id VARCHAR(50) DEFAULT NULL,
        company_id VARCHAR(50) DEFAULT NULL,
        department_id VARCHAR(50) DEFAULT NULL,
        firstname VARCHAR(100) NOT NULL,
        middlename VARCHAR(100),
        surname VARCHAR(100) NOT NULL,
        email VARCHAR(255) NOT NULL,
        contact_number VARCHAR(50),
        resume_filename VARCHAR(255),
        application_status ENUM('Applied', 'Under Review', 'Interview Scheduled', 'Rejected', 'Hired') DEFAULT 'Applied',
        application_date DATE NOT NULL,
        interview_date DATETIME NULL,
        interview_type ENUM('Virtual', 'Phone', 'In-Person') NULL,
        interview_location TEXT NULL,
        notes TEXT,
        avatar VARCHAR(10),
        color VARCHAR(50),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_app_status (application_status),
        INDEX idx_app_date (application_date),
        INDEX idx_app_requisition (requisition_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // Job Offers Table
    $conn->exec("CREATE TABLE IF NOT EXISTS job_offers (
        id VARCHAR(50) PRIMARY KEY,
        applicant_id VARCHAR(50) NOT NULL,
        position VARCHAR(255) NOT NULL,
        salary_offer DECIMAL(15,2) NOT NULL,
        contract_terms TEXT,
        hire_date DATE NOT NULL,
        offer_status ENUM('Pending', 'Accepted', 'Declined', 'Expired') DEFAULT 'Pending',
        employee_id VARCHAR(50) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (applicant_id) REFERENCES applicants(id) ON DELETE CASCADE,
        INDEX idx_offer_status (offer_status),
        INDEX idx_offer_date (hire_date)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    // Onboarding Records Table
    $conn->exec("CREATE TABLE IF NOT EXISTS onboarding_records (
        id VARCHAR(50) PRIMARY KEY,
        employee_id VARCHAR(50) NOT NULL,
        employee_name VARCHAR(255) NOT NULL,
        employee_email VARCHAR(255) NOT NULL,
        job_id VARCHAR(50) DEFAULT NULL,
        department_id VARCHAR(50) DEFAULT NULL,
        company_id VARCHAR(50) DEFAULT NULL,
        start_date DATE NOT NULL,
        progress ENUM('Not Started', 'In Progress', 'Completed') DEFAULT 'Not Started',
        completion_date DATE NULL,
        tasks JSON,
        notes TEXT,
        avatar VARCHAR(10),
        color VARCHAR(50),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_onb_progress (progress),
        INDEX idx_onb_start_date (start_date),
        INDEX idx_onb_company (company_id),
        INDEX idx_onb_department (department_id),
        INDEX idx_onb_job (job_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
} catch (Exception $e) {
    error_log("Error creating recruitment tables: " . $e->getMessage());
}

// Fetch data for the page
try {
    // Get job requisitions
    $requisitionsQuery = "SELECT * FROM job_requisitions WHERE status = 'Open' ORDER BY created_at DESC";
    $requisitionsStmt = $conn->prepare($requisitionsQuery);
    $requisitionsStmt->execute();
    $requisitionsData = $requisitionsStmt->fetchAll();
    
    // Get offers with applicant details — subquery to get profile_photo from employees
    $offersQuery = "SELECT jo.*, a.firstname, a.surname, a.avatar, a.color,
                           CONCAT(a.firstname, ' ', a.surname) as applicant_name,
                           COALESCE(
                             (SELECT e.profile_photo FROM employees e 
                              WHERE (e.email = a.email OR (e.firstname = a.firstname AND e.surname = a.surname))
                              AND e.profile_photo IS NOT NULL AND e.profile_photo != ''
                              LIMIT 1),
                             NULLIF(a.profile_photo, '')
                           ) as profile_photo
                    FROM job_offers jo 
                    LEFT JOIN applicants a ON jo.applicant_id = a.id
                    ORDER BY jo.created_at DESC";
    $offersStmt = $conn->prepare($offersQuery);
    $offersStmt->execute();
    $offersData = $offersStmt->fetchAll();
    
} catch (Exception $e) {
    error_log("Error fetching recruitment data: " . $e->getMessage());
    $requisitionsData = [];
    $offersData = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600&display=swap" rel="stylesheet">
    
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
        }

        .main-content {
            flex: 1;
            padding: 20px 24px;
            overflow-y: auto;
            max-height: 100vh;
        }

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

        .btn {
            padding: 8px 16px;
            border-radius: 20px;
            border: none;
            font-weight: 500;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
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

        .btn-success {
            background: #10b981;
            color: white;
        }

        .btn-success:hover {
            background: #059669;
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

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
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

        .header-actions {
            display: flex;
            gap: 10px;
        }

        /* Stats */
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

        /* Table */
        .table-card {
            background: rgba(255,255,255,0.85);
            backdrop-filter: blur(8px);
            border-radius: 24px;
            padding: 20px;
            box-shadow: 0 8px 20px -8px rgba(0,0,0,0.05);
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

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 12px 8px;
            font-weight: 600;
            color: #475569;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #e2e8f0;
        }

        td {
            padding: 12px 8px;
            border-bottom: 1px solid #f1f5f9;
            color: #1e293b;
            font-size: 13px;
        }

        tbody tr:hover {
            background: rgba(79, 70, 229, 0.03);
        }

        .badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 500;
            display: inline-block;
        }

        .badge-success { background: #dcfce7; color: #16a34a; }
        .badge-warning { background: #fef3c7; color: #b45309; }
        .badge-info { background: #dbeafe; color: #2563eb; }
        .badge-danger { background: #fee2e2; color: #dc2626; }
        .badge-secondary { background: #f1f5f9; color: #64748b; }

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

        .applicant-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .applicant-avatar {
            width: 36px;
            height: 36px;
            border-radius: 12px;
            object-fit: cover;
            flex-shrink: 0;
        }

        .applicant-info h4 {
            font-weight: 600;
            font-size: 13px;
            margin-bottom: 2px;
        }

        .applicant-info p {
            font-size: 11px;
            color: #64748b;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
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
            color: #475569;
            margin-bottom: 8px;
        }

        .empty-state p {
            font-size: 13px;
            color: #94a3b8;
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

        @media (max-width: 768px) {
            .main-content {
                padding: 14px;
            }
            table {
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
    
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-user-graduate"></i> Recruitment & Applicant Tracking</h1>
        </div>

        <!-- Tabs -->
        <div class="tabs-container">
            <div class="tabs">
                <button class="tab active" onclick="window.switchTab('applicants')">
                    <i class="fas fa-user-graduate"></i> Applicant Tracking
                </button>
                <button class="tab" onclick="window.switchTab('offers')">
                    <i class="fas fa-file-signature"></i> Hiring & Offer Management
                </button>
            </div>
        </div>

        <!-- Applicant Tracking Tab -->
        <div id="applicantsTab" class="tab-content active">
            <!-- Search & Filter Bar -->
            <div class="filter-bar">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search applicants by name, email, or job applied..." id="applicantSearchInput" value="">
                </div>
                <select class="filter-select" id="statusFilterApplicant">
                    <option>All Statuses</option>
                    <option>Applied</option>
                    <option>Under Review</option>
                    <option>Interview Scheduled</option>
                    <option>Rejected</option>
                    <option>Hired</option>
                </select>
            </div>

            <div class="stats-mini">
                <div class="stat-mini-card">
                    <i class="fas fa-users"></i>
                    <span id="totalApplicants">0</span> <small>Total Applicants</small>
                </div>
                <div class="stat-mini-card">
                    <i class="fas fa-clock"></i>
                    <span id="underReviewCount">0</span> <small>Under Review</small>
                </div>
                <div class="stat-mini-card">
                    <i class="fas fa-calendar-check"></i>
                    <span id="interviewCount">0</span> <small>Interview</small>
                </div>
                <div class="stat-mini-card">
                    <i class="fas fa-user-check"></i>
                    <span id="hiredCount">0</span> <small>Hired</small>
                </div>
                <div class="header-actions">
                    <button class="btn btn-primary" onclick="window.openAddApplicantModal()">
                        <i class="fas fa-plus"></i> Add Applicant
                    </button>
                </div>
            </div>

            <div class="table-card">
                <h3><i class="fas fa-list-ul"></i> Applicant Tracking System</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Applicant Name</th>
                            <th>Job Applied</th>
                            <th>Status</th>
                            <th>Application Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="applicantTableBody">
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <i class="fas fa-user-graduate"></i>
                                    <h4>No Applicants Yet</h4>
                                    <p>Click "Add Applicant" to start tracking candidates</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="pagination">
                    <div class="pagination-info" id="applicantPaginationInfo">
                        Showing 0 of 0 applicants
                    </div>
                    <div class="pagination-controls" id="applicantPaginationControls">
                    </div>
                </div>
            </div>
        </div>

        <!-- Hiring & Offer Management Tab -->
        <div id="offersTab" class="tab-content">
            <!-- Search & Filter Bar -->
            <div class="filter-bar">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search offers by candidate name or position..." id="offerSearchInput" value="">
                </div>
                <select class="filter-select" id="statusFilterOffer">
                    <option>All Statuses</option>
                    <option>Pending</option>
                    <option>Accepted</option>
                    <option>Declined</option>
                    <option>Expired</option>
                </select>
            </div>

            <div class="stats-mini">
                <div class="stat-mini-card">
                    <i class="fas fa-file-signature"></i>
                    <span id="totalOffers">0</span> <small>Total Offers</small>
                </div>
                <div class="stat-mini-card">
                    <i class="fas fa-hourglass-half"></i>
                    <span id="pendingOffers">0</span> <small>Pending</small>
                </div>
                <div class="stat-mini-card">
                    <i class="fas fa-check-circle"></i>
                    <span id="acceptedOffers">0</span> <small>Accepted</small>
                </div>
                <div class="header-actions">
                    <button class="btn btn-success" onclick="window.openAddOfferModal()">
                        <i class="fas fa-plus"></i> Create Offer
                    </button>
                </div>
            </div>

            <div class="table-card">
                <h3><i class="fas fa-file-signature"></i> Hiring and Offer Management</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Applicant</th>
                            <th>Position</th>
                            <th>Salary Offer</th>
                            <th>Hire Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="offerTableBody">
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <i class="fas fa-file-signature"></i>
                                    <h4>No Job Offers</h4>
                                    <p>Create offers for qualified applicants</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="pagination">
                    <div class="pagination-info" id="offerPaginationInfo">
                        Showing 0 of 0 offers
                    </div>
                    <div class="pagination-controls" id="offerPaginationControls">
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Include Modal Components -->
<?php include 'modals/modal-wrapper.php'; ?>
<?php 
$GLOBALS['requisitionsData'] = $requisitionsData;
include 'modals/recruitment-modal/modal-add-applicant.php'; 
?>
<?php include 'modals/recruitment-modal/modal-edit-applicant.php'; ?>
<?php include 'modals/recruitment-modal/modal-view-applicant.php'; ?>
<?php include 'modals/recruitment-modal/modal-add-offer.php'; ?>
<?php include 'modals/recruitment-modal/modal-edit-offer.php'; ?>
<?php include 'modals/recruitment-modal/modal-view-offer.php'; ?>
<?php include 'modals/recruitment-modal/modal-recruitment-helpers.php'; ?>
<script>
// Initialize data
window.applicants = [];
let currentApplicantPage = 1;
let currentOfferPage = 1;
let itemsPerPage = 8;

// Search and filter terms
window.applicantSearchTerm = '';
window.applicantStatusFilter = 'All Statuses';
window.offerSearchTerm = '';
window.offerStatusFilter = 'All Statuses';

window.offers = <?php echo json_encode(array_map(function($offer) {
    return [
        'id' => $offer['id'],
        'applicantId' => $offer['applicant_id'],
        'applicantName' => $offer['applicant_name'] ?? 'Unknown',
        'position' => $offer['position'],
        'salaryOffer' => (float)$offer['salary_offer'],
        'hireDate' => date('M d, Y', strtotime($offer['hire_date'])),
        'offerStatus' => $offer['offer_status'],
        'employeeId' => $offer['employee_id'],
        'avatar' => $offer['avatar'] ?? 'NA',
        'color' => $offer['color'] ?? 'linear-gradient(145deg, #6366f1, #a78bfa)',
        'profilePhoto' => $offer['profile_photo'] ?? null
    ];
}, $offersData)); ?>;

// Tab switching
window.switchTab = function(tab) {
    console.log('Switching to tab:', tab);
    document.querySelectorAll('.tab').forEach(btn => btn.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
    
    if (tab === 'applicants') {
        document.querySelectorAll('.tab')[0].classList.add('active');
        document.getElementById('applicantsTab').classList.add('active');
        renderApplicantTable();
    } else if (tab === 'offers') {
        document.querySelectorAll('.tab')[1].classList.add('active');
        document.getElementById('offersTab').classList.add('active');
        renderOfferTable();
    }
};

// Load applicants from API
async function loadApplicants() {
    try {
        const apiUrl = window.location.origin + window.location.pathname.replace('app/views/recruitment.php', 'api/recruitment/applicants.php');
        const response = await fetch(apiUrl);
        const result = await response.json();
        
        if (result.success && result.data) {
            window.applicants = result.data.map(app => ({
                id: app.id,
                requisitionTitle: app.job || 'Unknown Position',
                firstname: app.firstname,
                middlename: app.middlename || '',
                surname: app.surname,
                suffix: app.suffix || '',
                email: app.email,
                applicationStatus: app.application_status,
                applicationDate: new Date(app.application_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }),
                profilePhoto: app.profile_photo || null
            }));
            renderApplicantTable();
        }
    } catch (error) {
        console.error('Error loading applicants:', error);
    }
}

// Render applicant table with pagination
function renderApplicantTable() {
    const tbody = document.getElementById('applicantTableBody');
    const allApplicants = window.applicants || [];
    
    // Apply search and status filters
    let filteredApplicants = allApplicants;
    if (window.applicantSearchTerm) {
        const term = window.applicantSearchTerm.toLowerCase().trim();
        filteredApplicants = filteredApplicants.filter(app => {
            const fullName = `${app.firstname} ${app.middlename ? app.middlename + ' ' : ''}${app.surname}`.toLowerCase();
            return fullName.includes(term) ||
                   app.email.toLowerCase().includes(term) ||
                   app.requisitionTitle.toLowerCase().includes(term);
        });
    }
    if (window.applicantStatusFilter && window.applicantStatusFilter !== 'All Statuses') {
        filteredApplicants = filteredApplicants.filter(app => app.applicationStatus === window.applicantStatusFilter);
    }
    
    if (filteredApplicants.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="5">
                    <div class="empty-state">
                        <i class="fas fa-user-graduate"></i>
                        <h4>No Applicants Found</h4>
                        <p>No applicants match your search/filter criteria</p>
                    </div>
                </td>
            </tr>
        `;
        document.getElementById('applicantPaginationInfo').textContent = 'Showing 0 of 0 applicants';
        document.getElementById('applicantPaginationControls').innerHTML = '';
        updateApplicantStats();
        return;
    }
    
    const start = (currentApplicantPage - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    const paginatedData = filteredApplicants.slice(start, end);
    
    tbody.innerHTML = paginatedData.map(app => {
        const statusClass = {
            'Applied': 'badge-info',
            'Under Review': 'badge-warning',
            'Interview Scheduled': 'badge-secondary',
            'Rejected': 'badge-danger',
            'Hired': 'badge-success'
        }[app.applicationStatus] || 'badge-secondary';
        
        const fullName = `${app.firstname} ${app.middlename ? app.middlename[0] + '. ' : ''}${app.surname}${app.suffix ? ' ' + app.suffix : ''}`;
        
        return `
            <tr>
                <td>
                    <div class="applicant-cell">
                        <img src="${app.profilePhoto || '/3ME/assets/images/default-avatar.png'}" class="applicant-avatar" />
                        <div class="applicant-info">
                            <h4>${fullName}</h4>
                            <p>${app.email}</p>
                        </div>
                    </div>
                </td>
                <td>${app.requisitionTitle}</td>
                <td><span class="badge ${statusClass}">${app.applicationStatus}</span></td>
                <td>${app.applicationDate}</td>
                <td class="action-icons">
                    <i class="fas fa-eye" onclick="window.viewApplicant('${app.id}')" title="View"></i>
                    <i class="fas fa-edit" onclick="window.editApplicant('${app.id}')" title="Edit"></i>
                </td>
            </tr>
        `;
    }).join('');
    
    // Pagination info & controls
    const totalPages = Math.ceil(filteredApplicants.length / itemsPerPage);
    document.getElementById('applicantPaginationInfo').textContent = `Showing ${start + 1}-${Math.min(end, filteredApplicants.length)} of ${filteredApplicants.length} applicants`;
    
    const paginationContainer = document.getElementById('applicantPaginationControls');
    let paginationHtml = '';
    paginationHtml += `<div class="page-btn" onclick="changeApplicantPage(${currentApplicantPage - 1})" ${currentApplicantPage === 1 ? 'style="opacity:0.5; pointer-events:none;"' : ''}><i class="fas fa-chevron-left"></i></div>`;
    for (let i = 1; i <= Math.min(totalPages, 5); i++) {
        paginationHtml += `<div class="page-btn ${currentApplicantPage === i ? 'active' : ''}" onclick="changeApplicantPage(${i})">${i}</div>`;
    }
    if (totalPages > 5) paginationHtml += `<div class="page-btn">...</div>`;
    paginationHtml += `<div class="page-btn" onclick="changeApplicantPage(${currentApplicantPage + 1})" ${currentApplicantPage === totalPages || totalPages === 0 ? 'style="opacity:0.5; pointer-events:none;"' : ''}><i class="fas fa-chevron-right"></i></div>`;
    paginationContainer.innerHTML = paginationHtml;
    
    updateApplicantStats();
}

function changeApplicantPage(page) {
    const allApplicants = window.applicants || [];
    let filteredApplicants = allApplicants;
    if (window.applicantSearchTerm) {
        const term = window.applicantSearchTerm.toLowerCase().trim();
        filteredApplicants = filteredApplicants.filter(app => {
            const fullName = `${app.firstname} ${app.middlename ? app.middlename + ' ' : ''}${app.surname}`.toLowerCase();
            return fullName.includes(term) ||
                   app.email.toLowerCase().includes(term) ||
                   app.requisitionTitle.toLowerCase().includes(term);
        });
    }
    if (window.applicantStatusFilter && window.applicantStatusFilter !== 'All Statuses') {
        filteredApplicants = filteredApplicants.filter(app => app.applicationStatus === window.applicantStatusFilter);
    }
    const totalPages = Math.ceil(filteredApplicants.length / itemsPerPage);
    if (page < 1 || page > totalPages) return;
    currentApplicantPage = page;
    renderApplicantTable();
}

// Render offer table with pagination
function renderOfferTable() {
    const tbody = document.getElementById('offerTableBody');
    const allOffers = window.offers || [];
    
    // Apply search and status filters
    let filteredOffers = allOffers;
    if (window.offerSearchTerm) {
        const term = window.offerSearchTerm.toLowerCase().trim();
        filteredOffers = filteredOffers.filter(off => {
            return off.applicantName.toLowerCase().includes(term) ||
                   off.position.toLowerCase().includes(term);
        });
    }
    if (window.offerStatusFilter && window.offerStatusFilter !== 'All Statuses') {
        filteredOffers = filteredOffers.filter(off => off.offerStatus === window.offerStatusFilter);
    }
    
    if (filteredOffers.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6">
                    <div class="empty-state">
                        <i class="fas fa-file-signature"></i>
                        <h4>No Offers Found</h4>
                        <p>No job offers match your search/filter criteria</p>
                    </div>
                </td>
            </tr>
        `;
        document.getElementById('offerPaginationInfo').textContent = 'Showing 0 of 0 offers';
        document.getElementById('offerPaginationControls').innerHTML = '';
        updateOfferStats();
        return;
    }
    
    const start = (currentOfferPage - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    const paginatedData = filteredOffers.slice(start, end);
    
    tbody.innerHTML = paginatedData.map(offer => {
        const statusClass = {
            'Pending': 'badge-warning',
            'Accepted': 'badge-success',
            'Declined': 'badge-danger',
            'Expired': 'badge-secondary'
        }[offer.offerStatus] || 'badge-secondary';
        
        return `
            <tr>
                <td>
                    <div class="applicant-cell">
                        <img src="${offer.profilePhoto || '/3ME/assets/images/default-avatar.png'}" class="applicant-avatar" />
                        <div class="applicant-info">
                            <h4>${offer.applicantName}</h4>
                        </div>
                    </div>
                </td>
                <td>${offer.position}</td>
                <td>₱${offer.salaryOffer.toLocaleString()}</td>
                <td>${offer.hireDate}</td>
                <td><span class="badge ${statusClass}">${offer.offerStatus}</span></td>
                <td class="action-icons">
                    <i class="fas fa-eye" onclick="window.viewOffer('${offer.id}')" title="View"></i>
                    <i class="fas fa-edit" onclick="window.editOffer('${offer.id}')" title="Edit"></i>
                </td>
            </tr>
        `;
    }).join('');
    
    // Pagination info & controls
    const totalPages = Math.ceil(filteredOffers.length / itemsPerPage);
    document.getElementById('offerPaginationInfo').textContent = `Showing ${start + 1}-${Math.min(end, filteredOffers.length)} of ${filteredOffers.length} offers`;
    
    const paginationContainer = document.getElementById('offerPaginationControls');
    let paginationHtml = '';
    paginationHtml += `<div class="page-btn" onclick="changeOfferPage(${currentOfferPage - 1})" ${currentOfferPage === 1 ? 'style="opacity:0.5; pointer-events:none;"' : ''}><i class="fas fa-chevron-left"></i></div>`;
    for (let i = 1; i <= Math.min(totalPages, 5); i++) {
        paginationHtml += `<div class="page-btn ${currentOfferPage === i ? 'active' : ''}" onclick="changeOfferPage(${i})">${i}</div>`;
    }
    if (totalPages > 5) paginationHtml += `<div class="page-btn">...</div>`;
    paginationHtml += `<div class="page-btn" onclick="changeOfferPage(${currentOfferPage + 1})" ${currentOfferPage === totalPages || totalPages === 0 ? 'style="opacity:0.5; pointer-events:none;"' : ''}><i class="fas fa-chevron-right"></i></div>`;
    paginationContainer.innerHTML = paginationHtml;
    
    updateOfferStats();
}

function changeOfferPage(page) {
    const allOffers = window.offers || [];
    let filteredOffers = allOffers;
    if (window.offerSearchTerm) {
        const term = window.offerSearchTerm.toLowerCase().trim();
        filteredOffers = filteredOffers.filter(off => {
            return off.applicantName.toLowerCase().includes(term) ||
                   off.position.toLowerCase().includes(term);
        });
    }
    if (window.offerStatusFilter && window.offerStatusFilter !== 'All Statuses') {
        filteredOffers = filteredOffers.filter(off => off.offerStatus === window.offerStatusFilter);
    }
    const totalPages = Math.ceil(filteredOffers.length / itemsPerPage);
    if (page < 1 || page > totalPages) return;
    currentOfferPage = page;
    renderOfferTable();
}

function updateApplicantStats() {
    const total = window.applicants.length;
    const underReview = window.applicants.filter(a => a.applicationStatus === 'Under Review').length;
    const interview = window.applicants.filter(a => a.applicationStatus === 'Interview Scheduled').length;
    const hired = window.applicants.filter(a => a.applicationStatus === 'Hired').length;
    
    document.getElementById('totalApplicants').textContent = total;
    document.getElementById('underReviewCount').textContent = underReview;
    document.getElementById('interviewCount').textContent = interview;
    document.getElementById('hiredCount').textContent = hired;
}

function updateOfferStats() {
    const total = window.offers.length;
    const pending = window.offers.filter(o => o.offerStatus === 'Pending').length;
    const accepted = window.offers.filter(o => o.offerStatus === 'Accepted').length;
    
    document.getElementById('totalOffers').textContent = total;
    document.getElementById('pendingOffers').textContent = pending;
    document.getElementById('acceptedOffers').textContent = accepted;
}

// Register Search and Filter Event Listeners
document.addEventListener('DOMContentLoaded', () => {
    const appSearch = document.getElementById('applicantSearchInput');
    const appStatus = document.getElementById('statusFilterApplicant');
    const offSearch = document.getElementById('offerSearchInput');
    const offStatus = document.getElementById('statusFilterOffer');

    if (appSearch) {
        appSearch.addEventListener('keyup', (e) => {
            window.applicantSearchTerm = e.target.value;
            currentApplicantPage = 1;
            renderApplicantTable();
        });
    }

    if (appStatus) {
        appStatus.addEventListener('change', (e) => {
            window.applicantStatusFilter = e.target.value;
            currentApplicantPage = 1;
            renderApplicantTable();
        });
    }

    if (offSearch) {
        offSearch.addEventListener('keyup', (e) => {
            window.offerSearchTerm = e.target.value;
            currentOfferPage = 1;
            renderOfferTable();
        });
    }

    if (offStatus) {
        offStatus.addEventListener('change', (e) => {
            window.offerStatusFilter = e.target.value;
            currentOfferPage = 1;
            renderOfferTable();
        });
    }
});

// Toast notification
window.showToast = function(message, type = 'info') {
    const toast = document.createElement('div');
    toast.style.cssText = `
        position: fixed; bottom: 24px; right: 24px; 
        background: ${type === 'success' ? '#10b981' : type === 'warning' ? '#f59e0b' : '#1e293b'}; 
        color: white; padding: 12px 20px; border-radius: 12px; 
        font-size: 13px; z-index: 10000; 
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    `;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
};

// Create offer for applicant
window.createOfferForApplicant = function(applicantId) {
    const applicant = window.applicants.find(a => a.id === applicantId);
    if (!applicant) {
        window.showToast('Applicant not found', 'warning');
        return;
    }
    
    // Check if applicant already has an offer
    const existingOffer = window.offers.find(o => o.applicantId === applicantId);
    if (existingOffer) {
        const confirmCreate = confirm(`${applicant.firstname} ${applicant.surname} already has an offer (${existingOffer.offerStatus}). Create another offer?`);
        if (!confirmCreate) return;
    }
    
    // Open add offer modal with pre-filled applicant data
    if (typeof window.openAddOfferModal === 'function') {
        window.openAddOfferModal(applicant);
    } else {
        window.showToast('Offer modal not available', 'warning');
    }
};

// Expose functions globally
window.renderApplicantTable = renderApplicantTable;
window.renderOfferTable = renderOfferTable;

// Initialize
console.log('🚀 Recruitment page initializing...');
loadApplicants();
renderOfferTable();

// Wait for modal functions to be defined
setTimeout(() => {
    console.log('✅ Checking modal functions:');
    console.log('  openAddApplicantModal:', typeof openAddApplicantModal);
    console.log('  openAddOfferModal:', typeof openAddOfferModal);
    console.log('  viewApplicant:', typeof viewApplicant);
    console.log('  editApplicant:', typeof editApplicant);
    console.log('  viewOffer:', typeof viewOffer);
    console.log('  editOffer:', typeof editOffer);
    
    // Expose modal functions
    if (typeof openAddApplicantModal !== 'undefined') window.openAddApplicantModal = openAddApplicantModal;
    if (typeof openAddOfferModal !== 'undefined') window.openAddOfferModal = openAddOfferModal;
    if (typeof viewApplicant !== 'undefined') window.viewApplicant = viewApplicant;
    if (typeof editApplicant !== 'undefined') window.editApplicant = editApplicant;
    if (typeof viewOffer !== 'undefined') window.viewOffer = viewOffer;
    if (typeof editOffer !== 'undefined') window.editOffer = editOffer;
    
    console.log('✅ Recruitment page ready!');
}, 500);
</script>

</body>
</html>
