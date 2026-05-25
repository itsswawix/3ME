<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * users.php
 * System User Management - Redesigned from scratch following recruitment.php design
 */

$pageTitle = "User Management";
$activeMenu = "Users";

// Include database connection
require_once '../../config/database.php';

// Initialize database connection
$database = new Database();
$conn = $database->getConnection();

// Create users table and seed default admin if empty
try {
    $conn->exec("CREATE TABLE IF NOT EXISTS users (
        id VARCHAR(50) PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role VARCHAR(50) NOT NULL,
        contact_number VARCHAR(50) DEFAULT NULL,
        department VARCHAR(255) DEFAULT NULL,
        status VARCHAR(50) DEFAULT 'Active',
        avatar VARCHAR(50) DEFAULT NULL,
        color VARCHAR(50) DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_user_role (role),
        INDEX idx_user_status (status)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // Check if empty and seed a default administrator
    $checkUsers = $conn->query("SELECT COUNT(*) as count FROM users");
    $count = $checkUsers->fetch()['count'];
    
    if ($count == 0) {
        $insertAdmin = $conn->prepare("
            INSERT INTO users (id, name, email, password, role, contact_number, department, status, avatar, color, created_at)
            VALUES ('USER-ADMIN-001', 'System Administrator', 'admin@3me.com', ?, 'Admin', '+63 900 111 2222', 'IT Department', 'Active', 'SA', 'linear-gradient(145deg, #4f46e5, #7c3aed)', NOW())
        ");
        $insertAdmin->execute([password_hash('admin123', PASSWORD_DEFAULT)]);
    }
} catch (Exception $e) {
    error_log("Error creating/seeding users table: " . $e->getMessage());
}

// Fetch all users for initial paint
try {
    $usersQuery = "SELECT id, name, email, role, contact_number, department, status, avatar, color, created_at 
                  FROM users 
                  ORDER BY created_at DESC";
    $usersStmt = $conn->prepare($usersQuery);
    $usersStmt->execute();
    $usersData = $usersStmt->fetchAll();
} catch (Exception $e) {
    error_log("Error fetching users data: " . $e->getMessage());
    $usersData = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - 3ME HR System</title>
    
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
            box-shadow: 0 4px 10px rgba(79, 70, 229, 0.15);
        }

        .btn-primary:hover {
            background: #4338ca;
            transform: translateY(-1px);
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
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
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

        /* Table Card */
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

        .table-card h3 i {
            color: #4f46e5;
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
        .badge-info { background: #dbeafe; color: #2563eb; }
        .badge-warning { background: #fef3c7; color: #b45309; }
        .badge-danger { background: #fee2e2; color: #dc2626; }
        .badge-secondary { background: #f1f5f9; color: #64748b; }

        .action-icons i {
            color: #94a3b8;
            margin: 0 6px;
            cursor: pointer;
            transition: color 0.2s;
            font-size: 14px;
        }

        .action-icons i.fa-eye:hover { color: #2563eb; }
        .action-icons i.fa-edit:hover { color: #4f46e5; }
        .action-icons i.fa-ban:hover { color: #f59e0b; }
        .action-icons i.fa-trash-alt:hover { color: #ef4444; }

        .user-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 13px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .user-info h4 {
            font-weight: 600;
            font-size: 13px;
            color: #0f172a;
            margin-bottom: 2px;
        }

        .user-info p {
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
            <h1><i class="fas fa-users-cog"></i> System User Management</h1>
        </div>

        <!-- Search & Filter Bar -->
        <div class="filter-bar">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search users by name, email, or user ID..." id="userSearchInput">
            </div>
            
            <select class="filter-select" id="roleFilter">
                <option value="">All Roles</option>
                <option value="Admin">Admin</option>
                <option value="Supervisor">Supervisor</option>
                <option value="Manager">Manager</option>
                <option value="HR Officer">HR Officer</option>
            </select>

            <select class="filter-select" id="statusFilter">
                <option value="">All Statuses</option>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
                <option value="Suspended">Suspended</option>
                <option value="Pending">Pending</option>
            </select>
            
            <select class="filter-select" id="deptFilter">
                <option value="">All Departments</option>
                <option value="Human Resources">Human Resources</option>
                <option value="IT Department">IT Department</option>
                <option value="Finance">Finance</option>
                <option value="Operations">Operations</option>
                <option value="Executive Office">Executive Office</option>
            </select>
        </div>

        <!-- Mini Stats Grid -->
        <div class="stats-mini">
            <div class="stat-mini-card">
                <i class="fas fa-users"></i>
                <span id="totalUsersCount">0</span> <small>Total Users</small>
            </div>
            <div class="stat-mini-card">
                <i class="fas fa-user-shield"></i>
                <span id="adminUsersCount">0</span> <small>Admins</small>
            </div>
            <div class="stat-mini-card">
                <i class="fas fa-user-tie"></i>
                <span id="managerUsersCount">0</span> <small>HR Officers</small>
            </div>
            <div class="stat-mini-card">
                <i class="fas fa-circle-check" style="color: #10b981;"></i>
                <span id="activeUsersCount">0</span> <small>Active</small>
            </div>
            
            <div style="flex: 1;"></div>
            
            <div class="header-actions">
                <button class="btn btn-primary" onclick="openAddUserModal()">
                    <i class="fas fa-plus"></i> Add System User
                </button>
            </div>
        </div>

        <!-- Users Table Card -->
        <div class="table-card">
            <h3><i class="fas fa-list-ul"></i> System Users List</h3>
            <table>
                <thead>
                    <tr>
                        <th>User Information</th>
                        <th>User ID</th>
                        <th>Role & Access</th>
                        <th>Department</th>
                        <th>Contact No.</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="userTableBody">
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <i class="fas fa-users-cog"></i>
                                <h4>No Users Found</h4>
                                <p>Load system users or create a new user profile.</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination">
                <div class="pagination-info" id="userPaginationInfo">
                    Showing 0 of 0 users
                </div>
                <div class="pagination-controls" id="userPaginationControls">
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Modal Wrapper -->
<?php include 'modals/modal-wrapper.php'; ?>

<!-- Modular User Modal Files -->
<?php include 'modals/user-modal/modal-add-user.php'; ?>
<?php include 'modals/user-modal/modal-edit-user.php'; ?>
<?php include 'modals/user-modal/modal-view-user.php'; ?>
<?php include 'modals/user-modal/modal-user-helpers.php'; ?>

<script>
// Pass session user details safely to JS
window.currentLoggedInUserId = <?php echo json_encode($_SESSION['user_id'] ?? 'USER-ADMIN-001'); ?>;

// Initialize data list
window.users = <?php echo json_encode(array_map(function($user) {
    $initials = '';
    if (!empty($user['name'])) {
        $parts = explode(' ', $user['name']);
        foreach ($parts as $p) {
            if (!empty($p)) $initials .= strtoupper($p[0]);
        }
        if (strlen($initials) > 2) $initials = substr($initials, 0, 2);
    }
    if (empty($initials)) $initials = 'US';

    return [
        'id' => $user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'role' => $user['role'],
        'contact_number' => $user['contact_number'] ?? '',
        'department' => $user['department'] ?? '',
        'status' => $user['status'] ?? 'Active',
        'avatar' => $user['avatar'] ? $user['avatar'] : $initials,
        'color' => $user['color'] ? $user['color'] : 'linear-gradient(145deg, #4f46e5, #7c3aed)',
        'created_at' => date('M d, Y', strtotime($user['created_at']))
    ];
}, $usersData)); ?>;

let currentUserPage = 1;
const entriesPerPage = 10;

// Search & Filter terms
let userSearchTerm = '';
let userRoleFilter = '';
let userStatusFilter = '';
let userDeptFilter = '';

// Load users list from REST API dynamically
async function loadUsers() {
    try {
        const response = await fetch('../../api/users/users.php');
        const result = await response.json();
        
        if (result.success && result.data) {
            window.users = result.data;
            updateStats();
            renderUserTable();
        }
    } catch (error) {
        console.error('Error fetching users from API:', error);
    }
}

// Update Mini Stats Numbers
function updateStats() {
    const list = window.users || [];
    
    document.getElementById('totalUsersCount').textContent = list.length;
    document.getElementById('adminUsersCount').textContent = list.filter(u => u.role === 'Admin').length;
    document.getElementById('managerUsersCount').textContent = list.filter(u => u.role === 'HR Officer').length;
    document.getElementById('activeUsersCount').textContent = list.filter(u => u.status === 'Active').length;
}

// Render dynamic rows
function renderUserTable() {
    const tbody = document.getElementById('userTableBody');
    if (!tbody) return;
    
    // Filter users list
    let filteredList = window.users.filter(u => {
        // Search text match
        const searchStr = (u.name + ' ' + u.email + ' ' + u.id).toLowerCase();
        const matchesSearch = searchStr.includes(userSearchTerm.toLowerCase());
        
        // Role match
        const matchesRole = !userRoleFilter || u.role === userRoleFilter;
        
        // Status match
        const matchesStatus = !userStatusFilter || u.status === userStatusFilter;
        
        // Dept match
        const matchesDept = !userDeptFilter || u.department === userDeptFilter;
        
        return matchesSearch && matchesRole && matchesStatus && matchesDept;
    });
    
    // Total pages
    const totalItems = filteredList.length;
    const totalPages = Math.ceil(totalItems / entriesPerPage) || 1;
    
    if (currentUserPage > totalPages) currentUserPage = totalPages;
    if (currentUserPage < 1) currentUserPage = 1;
    
    // Slice for current page
    const startIdx = (currentUserPage - 1) * entriesPerPage;
    const pageItems = filteredList.slice(startIdx, startIdx + entriesPerPage);
    
    if (totalItems === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7">
                    <div class="empty-state">
                        <i class="fas fa-users-cog"></i>
                        <h4>No Users Match Filters</h4>
                        <p>Try clearing some filters or searching for something else.</p>
                    </div>
                </td>
            </tr>
        `;
        document.getElementById('userPaginationInfo').textContent = `Showing 0 of 0 users`;
        document.getElementById('userPaginationControls').innerHTML = '';
        return;
    }
    
    // Render rows
    let rowsHtml = '';
    pageItems.forEach(u => {
        const statusClass = {
            'Active': 'badge-success',
            'Inactive': 'badge-danger',
            'Suspended': 'badge-warning',
            'Pending': 'badge-secondary'
        }[u.status] || 'badge-secondary';
        
        const roleClass = {
            'Admin': 'badge-info',
            'HR Officer': 'badge-success',
            'Manager': 'badge-warning',
            'Supervisor': 'badge-info'
        }[u.role] || 'badge-secondary';
        
        rowsHtml += `
            <tr>
                <td>
                    <div class="user-cell">
                        <div class="user-avatar" style="background: ${u.color};">${escapeHtml(u.avatar)}</div>
                        <div class="user-info">
                            <h4>${escapeHtml(u.name)}</h4>
                            <p>${escapeHtml(u.email)}</p>
                        </div>
                    </div>
                </td>
                <td><strong>${u.id}</strong></td>
                <td><span class="badge ${roleClass}">${escapeHtml(u.role)}</span></td>
                <td>${escapeHtml(u.department) || '<span style="color:#94a3b8;">—</span>'}</td>
                <td>${escapeHtml(u.contact_number) || '<span style="color:#94a3b8;">—</span>'}</td>
                <td><span class="badge ${statusClass}">${escapeHtml(u.status)}</span></td>
                <td class="action-icons">
                    <i class="fas fa-eye" title="View details" onclick="viewUser('${u.id}')"></i>
                    <i class="fas fa-edit" title="Edit user" onclick="editUser('${u.id}')"></i>
                    <i class="fas fa-ban" title="Toggle status" onclick="toggleUserStatus('${u.id}')"></i>
                    ${u.id !== window.currentLoggedInUserId ? `<i class="fas fa-trash-alt" title="Delete User" onclick="deleteUser('${u.id}')"></i>` : ''}
                </td>
            </tr>
        `;
    });
    tbody.innerHTML = rowsHtml;
    
    // Update pagination text
    const showingStart = totalItems === 0 ? 0 : startIdx + 1;
    const showingEnd = Math.min(startIdx + entriesPerPage, totalItems);
    document.getElementById('userPaginationInfo').textContent = `Showing ${showingStart} to ${showingEnd} of ${totalItems} users`;
    
    // Pagination Controls
    let controlsHtml = '';
    
    // Prev button
    controlsHtml += `
        <button class="page-btn" ${currentUserPage === 1 ? 'style="opacity:0.5; cursor:not-allowed;" disabled' : ''} onclick="changePage(${currentUserPage - 1})">
            <i class="fas fa-chevron-left"></i>
        </button>
    `;
    
    for (let p = 1; p <= totalPages; p++) {
        if (p === 1 || p === totalPages || (p >= currentUserPage - 1 && p <= currentUserPage + 1)) {
            controlsHtml += `
                <button class="page-btn ${p === currentUserPage ? 'active' : ''}" onclick="changePage(${p})">
                    ${p}
                </button>
            `;
        } else if (p === currentUserPage - 2 || p === currentUserPage + 2) {
            controlsHtml += `<span style="padding: 6px; color: #94a3b8;">...</span>`;
        }
    }
    
    // Next button
    controlsHtml += `
        <button class="page-btn" ${currentUserPage === totalPages ? 'style="opacity:0.5; cursor:not-allowed;" disabled' : ''} onclick="changePage(${currentUserPage + 1})">
            <i class="fas fa-chevron-right"></i>
        </button>
    `;
    
    document.getElementById('userPaginationControls').innerHTML = controlsHtml;
}

function changePage(p) {
    currentUserPage = p;
    renderUserTable();
}

// Event Listeners for Filters
document.getElementById('userSearchInput').addEventListener('input', function(e) {
    userSearchTerm = e.target.value;
    currentUserPage = 1;
    renderUserTable();
});

document.getElementById('roleFilter').addEventListener('change', function(e) {
    userRoleFilter = e.target.value;
    currentUserPage = 1;
    renderUserTable();
});

document.getElementById('statusFilter').addEventListener('change', function(e) {
    userStatusFilter = e.target.value;
    currentUserPage = 1;
    renderUserTable();
});

document.getElementById('deptFilter').addEventListener('change', function(e) {
    userDeptFilter = e.target.value;
    currentUserPage = 1;
    renderUserTable();
});

// Initialization
document.addEventListener('DOMContentLoaded', function() {
    updateStats();
    renderUserTable();
});
</script>
</body>
</html>
