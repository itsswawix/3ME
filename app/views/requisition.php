<?php
// Ensure session is started and check login
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * requisition.php (Company/Department/Job Management)
 * Organizational Structure Management - Hierarchical drill-down: Company > Department > Job
 * Moved from Master Data to Requisition module
 */

$pageTitle = "Job Requisitions";
$activeMenu = "Recruitment";

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
        .stats-mini { display: flex; gap: 16px; margin-bottom: 20px; flex-wrap: wrap; align-items: center; }
        .stat-mini-card { background: rgba(255,255,255,0.9); padding: 10px 18px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.6); display: flex; align-items: center; gap: 12px; }
        .stat-mini-card i { color: #4f46e5; font-size: 16px; }
        .stat-mini-card span { font-weight: 600; color: #0f172a; }
        .stat-mini-card small { color: #64748b; margin-left: 6px; }
        .table-card { background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); border-radius: 24px; padding: 20px; box-shadow: 0 8px 20px -8px rgba(0,0,0,0.05); border: 1px solid rgba(255,255,255,0.7); }
        .table-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
        .table-header h3 { font-size: 16px; font-weight: 600; color: #0f172a; display: flex; align-items: center; gap: 8px; }
        .breadcrumb-link-header { color: #4f46e5; cursor: pointer; font-weight: 500; }
        .breadcrumb-link-header:hover { color: #4338ca; text-decoration: underline; }
        .breadcrumb-separator-header { color: #cbd5e1; font-size: 10px; margin: 0 4px; }
        .breadcrumb-current-header { color: #0f172a; font-weight: 600; }
        .org-table { width: 100%; border-collapse: collapse; }
        .org-table th { text-align: left; padding: 12px 8px; font-weight: 600; color: #475569; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #e2e8f0; }
        .org-table td { padding: 12px 8px; border-bottom: 1px solid #f1f5f9; color: #1e293b; font-size: 13px; }
        .org-table tbody tr:hover { background: rgba(79, 70, 229, 0.03); }
        .clickable-row { cursor: pointer; }
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 500; display: inline-block; }
        .badge-success { background: #dcfce7; color: #16a34a; }
        .badge-info { background: #dbeafe; color: #2563eb; }
        .badge-secondary { background: #f1f5f9; color: #64748b; }
        .badge-purple { background: #f3e8ff; color: #9333ea; }
        .badge-warning { background: #fef3c7; color: #b45309; }
        .action-icons i { color: #94a3b8; margin: 0 4px; cursor: pointer; transition: color 0.2s; font-size: 14px; }
        .action-icons i:hover { color: #4f46e5; }
        .company-cell, .department-cell { display: flex; align-items: center; gap: 10px; }
        .company-icon, .department-icon { width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 18px; color: white; }
        .company-info h4, .department-info h4 { font-weight: 600; font-size: 14px; margin-bottom: 2px; }
        .pagination { display: flex; justify-content: space-between; align-items: center; margin-top: 20px; }
        .pagination-info { color: #64748b; font-size: 12px; }
        .pagination-controls { display: flex; gap: 6px; }
        .page-btn { width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 10px; background: white; border: 1px solid #e2e8f0; color: #1e293b; cursor: pointer; transition: all 0.2s; font-size: 12px; }
        .page-btn:hover { background: #4f46e5; color: white; border-color: #4f46e5; }
        .page-btn.active { background: #4f46e5; color: white; border-color: #4f46e5; }
        .empty-state { text-align: center; padding: 40px; color: #64748b; }
        .empty-state i { font-size: 40px; color: #cbd5e1; margin-bottom: 16px; }
        .empty-state h4 { font-size: 15px; font-weight: 500; margin-bottom: 8px; color: #1e293b; }
        @media (max-width: 768px) { .main-content { padding: 14px; } .org-table { display: block; overflow-x: auto; } }
    </style>
</head>
<body>
<div class="app-layout">
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-sitemap"></i> Organizational Structure</h1>
        </div>

        <!-- Organizational Structure Content -->
        <div id="orgLevelContent"></div>
    </main>
</div>

<?php include 'modals/modal-wrapper.php'; ?>
<?php include 'modals/masterdata-modal/modal-masterdata-helpers.php'; ?>
<?php include 'modals/masterdata-modal/modal-add-org.php'; ?>
<?php include 'modals/masterdata-modal/modal-edit-org.php'; ?>
<?php include 'modals/masterdata-modal/modal-view-org.php'; ?>

<script>
    // Initialize data arrays
    window.companies = [];
    window.departments = [];
    window.jobs = [];

    // Navigation state for hierarchical drill-down
    let currentLevel = 'company'; // 'company', 'department', 'job'
    let selectedCompany = null;
    let selectedDepartment = null;

    // Pagination variables
    let currentCompanyPage = 1;
    let currentDepartmentPage = 1;
    let currentJobPage = 1;
    let itemsPerPage = 10;
    
    // Filtered data arrays
    let filteredCompanies = [];
    let filteredDepartments = [];
    let filteredJobs = [];

    // Search terms
    let companySearchTerm = '';
    let departmentSearchTerm = '';
    let jobSearchTerm = '';

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

    // ---------- NAVIGATION FUNCTIONS ----------
    function navigateToLevel(level, company = null, department = null) {
        currentLevel = level;
        selectedCompany = company;
        selectedDepartment = department;
        
        // Reset pagination
        currentCompanyPage = 1;
        currentDepartmentPage = 1;
        currentJobPage = 1;
        
        // Reset search terms when navigating
        companySearchTerm = '';
        departmentSearchTerm = '';
        jobSearchTerm = '';
        
        if (level === 'company') {
            renderCompanyLevel();
        } else if (level === 'department') {
            renderDepartmentLevel();
        } else if (level === 'job') {
            renderJobLevel();
        }
    }

    function navigateBack() {
        if (currentLevel === 'department') {
            navigateToLevel('company');
        } else if (currentLevel === 'job') {
            navigateToLevel('department', selectedCompany, null);
        }
    }

    // ---------- RENDER COMPANY LEVEL ----------
    function renderCompanyLevel() {
        const companyData = window.companies.map(company => {
            const companyDepts = window.departments.filter(d => d.companyId === company.id);
            return {
                id: company.id,
                name: company.name,
                departments: companyDepts.length,
                status: company.status || 'Active'
            };
        });

        // Apply search filter
        filteredCompanies = companyData.filter(c => 
            c.name.toLowerCase().includes(companySearchTerm.toLowerCase())
        );

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
                    <i class="fas fa-sitemap"></i>
                    <span>${window.departments.length}</span> <small>Total Departments</small>
                </div>
                <div class="stat-mini-card">
                    <i class="fas fa-briefcase"></i>
                    <span>${window.jobs.length}</span> <small>Total Jobs</small>
                </div>
                <div class="header-actions">
                    <button class="btn btn-primary" onclick="window.openAddOrgModal('company')"><i class="fas fa-plus"></i> Add Company</button>
                </div>
            </div>

            <div class="table-card">
                <div class="table-header">
                    <h3><i class="fas fa-building"></i> Company List</h3>
                </div>
                <table class="org-table">
                    <thead>
                        <tr>
                            <th>Company</th>
                            <th>Departments</th>
                            <th>Status</th>
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
                                    <td><span class="badge badge-success">${company.status}</span></td>
                                    <td class="action-icons" onclick="event.stopPropagation()">
                                        <i class="fas fa-eye" onclick="viewCompany('${company.id}')" title="View"></i>
                                        <i class="fas fa-edit" onclick="editCompany('${company.id}')" title="Edit"></i>
                                    </td>
                                </tr>
                            `;
                        }).join('') : `
                            <tr>
                                <td colspan="4">
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

        document.getElementById('orgLevelContent').innerHTML = html;
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
        const selectedCompanyObj = window.companies.find(c => c.name === selectedCompany);
        
        if (!selectedCompanyObj) {
            console.error('Company not found:', selectedCompany);
            return;
        }
        
        const companyDepartments = window.departments.filter(d => d.companyId === selectedCompanyObj.id);
        
        const departmentData = companyDepartments.map(dept => {
            const deptJobs = window.jobs.filter(p => p.departmentId === dept.id);
            return {
                id: dept.id,
                name: dept.name,
                code: dept.code || 'N/A',
                head: dept.head || 'N/A',
                jobs: deptJobs.length,
                status: dept.status || 'Active'
            };
        });

        // Apply search filter
        filteredDepartments = departmentData.filter(d => 
            d.name.toLowerCase().includes(departmentSearchTerm.toLowerCase())
        );

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
                    <i class="fas fa-sitemap"></i>
                    <span>${companyDepartments.length}</span> <small>Departments</small>
                </div>
                <div class="stat-mini-card">
                    <i class="fas fa-briefcase"></i>
                    <span>${window.jobs.filter(p => {
                        const dept = window.departments.find(d => d.id === p.departmentId);
                        return dept && dept.companyId === selectedCompanyObj.id;
                    }).length}</span> <small>Total Jobs</small>
                </div>
                <div class="header-actions">
                    <button class="btn btn-primary" onclick="window.openAddOrgModal('department', '${selectedCompanyObj.id}')"><i class="fas fa-plus"></i> Add Department</button>
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
                <table class="org-table">
                    <thead>
                        <tr>
                            <th>Department</th>
                            <th>Department Head</th>
                            <th>Jobs</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${paginatedData.length > 0 ? paginatedData.map(dept => {
                            const colors = ['#4f46e5', '#0ea5e9', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#14b8a6'];
                            const colorIndex = dept.name.length % colors.length;
                            return `
                                <tr class="clickable-row" onclick="navigateToLevel('job', '${escapeHtml(selectedCompany)}', '${escapeHtml(dept.name)}')">
                                    <td>
                                        <div class="department-cell">
                                            <div class="department-icon" style="background: ${colors[colorIndex]};">
                                                <i class="fas fa-sitemap"></i>
                                            </div>
                                            <div class="department-info">
                                                <h4>${escapeHtml(dept.name)}</h4>
                                            </div>
                                        </div>
                                    </td>
                                    <td>${escapeHtml(dept.head)}</td>
                                    <td><span class="badge badge-info">${dept.jobs} job${dept.jobs !== 1 ? 's' : ''}</span></td>
                                    <td><span class="badge badge-success">${dept.status}</span></td>
                                    <td class="action-icons" onclick="event.stopPropagation()">
                                        <i class="fas fa-eye" onclick="viewDepartment('${dept.id}')" title="View"></i>
                                        <i class="fas fa-edit" onclick="editDepartment('${dept.id}')" title="Edit"></i>
                                    </td>
                                </tr>
                            `;
                        }).join('') : `
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        <i class="fas fa-sitemap"></i>
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

        document.getElementById('orgLevelContent').innerHTML = html;
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

    // ---------- RENDER JOB LEVEL ----------
    function renderJobLevel() {
        const selectedDepartmentObj = window.departments.find(d => d.name === selectedDepartment);
        
        if (!selectedDepartmentObj) {
            console.error('Department not found:', selectedDepartment);
            return;
        }
        
        const departmentJobs = window.jobs.filter(p => p.departmentId === selectedDepartmentObj.id);
        
        const jobData = departmentJobs.map(job => {
            return {
                id: job.id,
                jobTitle: job.jobTitle || job.title || 'N/A',
                level: job.level || 'N/A',
                reportsTo: job.reportsTo || 'N/A',
                vacancies: job.vacancies || 0,
                availableVacancies: job.availableVacancies ?? job.vacancies ?? 0,
                employedCount: job.employedCount || 0,
                salaryMin: job.salaryMin || job.salary_min || null,
                salaryMax: job.salaryMax || job.salary_max || null,
                status: job.status || 'Active'
            };
        });

        // Apply search filter
        filteredJobs = jobData.filter(p => 
            p.jobTitle.toLowerCase().includes(jobSearchTerm.toLowerCase())
        );

        const start = (currentJobPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const paginatedData = filteredJobs.slice(start, end);

        const html = `
            <div class="filter-bar">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search jobs..." id="jobSearchInput" value="${escapeHtml(jobSearchTerm)}">
                </div>
            </div>

            <div class="stats-mini">
                <div class="stat-mini-card">
                    <i class="fas fa-sitemap"></i>
                    <span>${escapeHtml(selectedDepartment)}</span>
                </div>
                <div class="stat-mini-card">
                    <i class="fas fa-briefcase"></i>
                    <span>${departmentJobs.length}</span> <small>Jobs</small>
                </div>
                <div class="stat-mini-card">
                    <i class="fas fa-users"></i>
                    <span>${departmentJobs.reduce((sum, p) => sum + (p.availableVacancies ?? p.vacancies ?? 0), 0)}</span> <small>Available Vacancies</small>
                </div>
                <div class="header-actions">
                    <button class="btn btn-primary" onclick="window.openAddOrgModal('position', '${selectedDepartmentObj.id}', '${escapeHtml(selectedDepartment)}')"><i class="fas fa-plus"></i> Add Job</button>
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
                <table class="org-table">
                    <thead>
                        <tr>
                            <th>Job Title</th>
                            <th>Level</th>
                            <th>Salary Range</th>
                            <th>Vacancies</th>
                            <th>Available</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${paginatedData.length > 0 ? paginatedData.map(job => {
                            const levelClass = { 'Director': 'badge-purple', 'Manager': 'badge-info', 'Senior': 'badge-success', 'Mid-Level': 'badge-warning', 'Junior': 'badge-secondary', 'Entry': 'badge-secondary' }[job.level] || 'badge-secondary';
                            const salaryRange = (job.salaryMin || job.salaryMax) 
                                ? `₱${job.salaryMin ? parseFloat(job.salaryMin).toLocaleString() : '--'} - ₱${job.salaryMax ? parseFloat(job.salaryMax).toLocaleString() : '--'}`
                                : 'Not specified';
                            return `
                                <tr>
                                    <td>
                                        <div class="company-cell">
                                            <div class="company-info">
                                                <h4>${escapeHtml(job.jobTitle)}</h4>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge ${levelClass}">${job.level}</span></td>
                                    <td><span style="font-weight: 500; color: #0f172a;">${salaryRange}</span></td>
                                    <td>
                                        <span style="font-weight:600; color:#0f172a;">${job.vacancies}</span>
                                    </td>
                                    <td>${(() => {
                                        const avail = job.availableVacancies;
                                        if (avail <= 0) return `<span class="badge badge-danger" title="${job.employedCount} employed, all slots filled">0 — Full</span>`;
                                        return `<span class="badge badge-success" title="${job.employedCount} employed">${avail} open</span>`;
                                    })()}</td>
                                    <td><span class="badge badge-success">${job.status}</span></td>
                                    <td class="action-icons">
                                        <i class="fas fa-eye" onclick="viewJob('${job.id}')" title="View"></i>
                                        <i class="fas fa-edit" onclick="editJob('${job.id}')" title="Edit"></i>
                                    </td>
                                </tr>
                            `;
                        }).join('') : `
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <i class="fas fa-briefcase"></i>
                                        <h4>No Jobs Found</h4>
                                        <p>No jobs match your search or no jobs exist for this department</p>
                                    </div>
                                </td>
                            </tr>
                        `}
                    </tbody>
                </table>

                <div class="pagination">
                    <div class="pagination-info" id="jobPaginationInfo">
                        ${filteredJobs.length > 0 ? `Showing ${start + 1}-${Math.min(end, filteredJobs.length)} of ${filteredJobs.length} jobs` : 'No jobs'}
                    </div>
                    <div class="pagination-controls" id="jobPaginationControls"></div>
                </div>
            </div>
        `;

        document.getElementById('orgLevelContent').innerHTML = html;
        renderJobPagination();
        
        const searchInput = document.getElementById('jobSearchInput');
        if (searchInput) {
            searchInput.addEventListener('keyup', (e) => {
                jobSearchTerm = e.target.value;
                currentJobPage = 1;
                renderJobLevel();
            });
        }
    }

    function renderJobPagination() {
        const totalPages = Math.ceil(filteredJobs.length / itemsPerPage);
        const container = document.getElementById('jobPaginationControls');
        let html = `<div class="page-btn" onclick="changeJobPage(${currentJobPage - 1})" ${currentJobPage === 1 ? 'style="opacity:0.5; pointer-events:none;"' : ''}><i class="fas fa-chevron-left"></i></div>`;
        for (let i = 1; i <= Math.min(totalPages, 5); i++) {
            html += `<div class="page-btn ${currentJobPage === i ? 'active' : ''}" onclick="changeJobPage(${i})">${i}</div>`;
        }
        if (totalPages > 5) html += `<div class="page-btn">...</div>`;
        if (totalPages > 5) html += `<div class="page-btn" onclick="changeJobPage(${totalPages})">${totalPages}</div>`;
        html += `<div class="page-btn" onclick="changeJobPage(${currentJobPage + 1})" ${currentJobPage === totalPages || totalPages === 0 ? 'style="opacity:0.5; pointer-events:none;"' : ''}><i class="fas fa-chevron-right"></i></div>`;
        container.innerHTML = html;
    }
    
    // Alias for compatibility with modals
    function renderPositionLevel() {
        return renderJobLevel();
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

    function changeJobPage(page) {
        if (page >= 1 && page <= Math.ceil(filteredJobs.length / itemsPerPage)) {
            currentJobPage = page;
            renderJobLevel();
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

    async function loadCompanies() {
        try {
            const result = await fetchAPI('list_companies');
            window.companies = result.data || [];
            if (currentLevel === 'company') renderCompanyLevel();
        } catch (error) {
            console.error('Failed to load companies:', error);
        }
    }

    async function loadDepartments() {
        try {
            const result = await fetchAPI('list_departments');
            window.departments = result.data || [];
            if (currentLevel === 'department') renderDepartmentLevel();
        } catch (error) {
            console.error('Failed to load departments:', error);
        }
    }

    async function loadJobs() {
        try {
            const result = await fetchAPI('list_jobs');
            window.jobs = result.data || [];
            if (currentLevel === 'job') renderJobLevel();
        } catch (error) {
            console.error('Failed to load jobs:', error);
        }
    }
    
    // Alias for compatibility with modals
    async function loadPositions() {
        return await loadJobs();
    }

    // ---------- VIEW/EDIT FUNCTIONS ----------
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

    async function viewJob(id) {
        try {
            const result = await fetchAPI(`get_job&id=${id}`);
            const job = result.data;
            if (typeof openViewPositionModal === 'function') {
                openViewPositionModal(job);
            } else {
                showToast('View Job: ' + (job.jobTitle || job.title), 'info');
            }
        } catch (error) {
            console.error('Failed to load job:', error);
        }
    }

    async function editCompany(id) {
        try {
            const result = await fetchAPI(`get_company&id=${id}`);
            const company = result.data;
            if (typeof openEditCompanyModal === 'function') {
                openEditCompanyModal(company);
            } else {
                showToast('Edit Company modal not available', 'warning');
            }
        } catch (error) {
            console.error('Failed to load company:', error);
        }
    }
    
    async function editDepartment(id) {
        try {
            const result = await fetchAPI(`get_department&id=${id}`);
            const dept = result.data;
            if (typeof openEditDepartmentModal === 'function') {
                openEditDepartmentModal(dept);
            } else {
                showToast('Edit Department modal not available', 'warning');
            }
        } catch (error) {
            console.error('Failed to load department:', error);
        }
    }
    
    async function editJob(id) {
        try {
            const result = await fetchAPI(`get_job&id=${id}`);
            const job = result.data;
            if (typeof openEditPositionModal === 'function') {
                openEditPositionModal(job);
            } else {
                showToast('Edit Job modal not available', 'warning');
            }
        } catch (error) {
            console.error('Failed to load job:', error);
        }
    }
    
    // Alias for compatibility
    async function editPosition(id) {
        return await editJob(id);
    }
    
    async function viewPosition(id) {
        return await viewJob(id);
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
            if (item.getAttribute('data-title') === 'Recruitment') {
                item.classList.add('active');
            }
        });
        
        // Load all data
        await Promise.all([
            loadCompanies(),
            loadDepartments(),
            loadJobs()
        ]);
        
        // Start at company level
        renderCompanyLevel();

        console.log('✅ Organizational Structure loaded in Requisition module');
    })();
</script>

</body>
</html>
