<?php
/**
 * Settings API - Handles all administration and settings operations
 */

// Prevent any output before JSON response
ob_start();

// Enable error reporting but don't display errors (to avoid breaking JSON responses)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Custom error handler to catch all errors
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    // Log the error
    error_log("PHP Error [$errno]: $errstr in $errfile on line $errline");
    
    // For fatal errors, send JSON error response
    if (in_array($errno, [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR])) {
        ob_clean();
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => [
                'message' => 'A system error occurred. Please try again.',
                'code' => 'SYSTEM_ERROR'
            ],
            'timestamp' => date('c')
        ]);
        exit;
    }
    
    return false; // Let PHP handle the error normally
});

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../../config/database.php';
require_once '../../utils/ResponseFormatter.php';
require_once '../../utils/ErrorHandler.php';

// TEMPORARY: Create default session for development
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 'USER-ADMIN-001';
    $_SESSION['user_email'] = 'admin@3me.com';
    $_SESSION['user_name'] = 'System Administrator';
    $_SESSION['user_role'] = 'Admin';
}

// Check authentication (commented out for development)
// if (!isset($_SESSION['user_id'])) {
//     ResponseFormatter::error('Unauthorized', 401);
//     exit;
// }

// Check admin permissions
function checkAdminPermission() {
    if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['Admin', 'HR Manager'])) {
        ResponseFormatter::error('Insufficient permissions. Admin or HR Manager role required.', 'FORBIDDEN', 403);
        exit;
    }
}

try {
    $database = new Database();
    $db = $database->getConnection();
    
    $action = $_GET['action'] ?? '';
    $method = $_SERVER['REQUEST_METHOD'];
    
    switch ($action) {
        // ==================== USER MANAGEMENT ====================
        case 'list_users':
            checkAdminPermission();
            $stmt = $db->query("SELECT * FROM users ORDER BY created_at DESC");
            $users = $stmt->fetchAll();
            
            // Format user data
            $formattedUsers = array_map(function($user) {
                $nameParts = explode(' ', $user['name']);
                $initials = '';
                foreach ($nameParts as $part) {
                    if (!empty($part)) {
                        $initials .= strtoupper($part[0]);
                    }
                }
                if (strlen($initials) > 2) {
                    $initials = substr($initials, 0, 2);
                }
                
                $colors = ['#4f46e5', '#7c3aed', '#db2777', '#dc2626', '#ea580c', '#16a34a', '#0891b2'];
                $color = $colors[array_sum(str_split(ord($user['id']))) % count($colors)];
                
                return [
                    'id' => $user['id'],
                    'employee_id' => $user['employee_id'] ?? null,
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'role' => $user['role'],
                    'contact_number' => $user['contact_number'] ?? '',
                    'department' => $user['department'] ?? '',
                    'status' => $user['status'] ?? 'Active',
                    'last_login' => $user['last_login'] ?? null,
                    'avatar' => $initials,
                    'color' => $color
                ];
            }, $users);
            
            ResponseFormatter::success($formattedUsers);
            break;
            
        case 'get_user':
            checkAdminPermission();
            $id = $_GET['id'] ?? '';
            if (empty($id)) {
                ResponseFormatter::error('User ID is required');
                exit;
            }
            
            $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$id]);
            $user = $stmt->fetch();
            
            if (!$user) {
                ResponseFormatter::error('User not found');
                exit;
            }
            
            ResponseFormatter::success($user);
            break;
            
        case 'create_user':
            checkAdminPermission();
            $data = json_decode(file_get_contents('php://input'), true);
            
            $firstname = $data['firstname'] ?? '';
            $middlename = $data['middlename'] ?? '';
            $surname = $data['surname'] ?? '';
            $email = $data['email'] ?? '';
            $password = $data['password'] ?? '';
            $role = $data['role'] ?? '';
            $contact = $data['contact_number'] ?? '';
            
            if (empty($firstname) || empty($surname) || empty($email) || empty($password) || empty($role)) {
                ResponseFormatter::error('Missing required fields');
                exit;
            }
            
            // Check if email already exists
            $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                ResponseFormatter::error('Email already exists');
                exit;
            }
            
            // Generate user ID
            $stmt = $db->query("SELECT COUNT(*) as count FROM users");
            $count = $stmt->fetch()['count'];
            $userId = 'USER-' . str_pad($count + 1, 6, '0', STR_PAD_LEFT);
            
            // Build full name
            $fullName = trim($firstname . ' ' . $middlename . ' ' . $surname);
            
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert user
            $stmt = $db->prepare("INSERT INTO users (id, name, email, password, role, contact_number, status, created_at) VALUES (?, ?, ?, ?, ?, ?, 'Active', NOW())");
            $stmt->execute([$userId, $fullName, $email, $hashedPassword, $role, $contact]);
            
            ResponseFormatter::success(['id' => $userId, 'message' => 'User created successfully']);
            break;
            
        case 'update_user':
            checkAdminPermission();
            $data = json_decode(file_get_contents('php://input'), true);
            
            $id = $data['id'] ?? '';
            $name = $data['name'] ?? '';
            $email = $data['email'] ?? '';
            $role = $data['role'] ?? '';
            $contact = $data['contact_number'] ?? '';
            $status = $data['status'] ?? 'Active';
            
            if (empty($id) || empty($name) || empty($email) || empty($role)) {
                ResponseFormatter::error('Missing required fields');
                exit;
            }
            
            // Check if user exists
            $stmt = $db->prepare("SELECT id FROM users WHERE id = ?");
            $stmt->execute([$id]);
            if (!$stmt->fetch()) {
                ResponseFormatter::error('User not found');
                exit;
            }
            
            // Update user
            if (isset($data['password']) && !empty($data['password'])) {
                $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
                $stmt = $db->prepare("UPDATE users SET name = ?, email = ?, password = ?, role = ?, contact_number = ?, status = ?, updated_at = NOW() WHERE id = ?");
                $stmt->execute([$name, $email, $hashedPassword, $role, $contact, $status, $id]);
            } else {
                $stmt = $db->prepare("UPDATE users SET name = ?, email = ?, role = ?, contact_number = ?, status = ?, updated_at = NOW() WHERE id = ?");
                $stmt->execute([$name, $email, $role, $contact, $status, $id]);
            }
            
            ResponseFormatter::success(['message' => 'User updated successfully']);
            break;
            
        // ==================== COMPANY MANAGEMENT ====================
        case 'list_companies':
            $stmt = $db->query("SELECT * FROM companies ORDER BY name");
            $companies = $stmt->fetchAll();
            
            // Get employee counts
            $formattedCompanies = array_map(function($company) use ($db) {
                $stmt = $db->prepare("SELECT COUNT(*) as count FROM employees WHERE company_id = ?");
                $stmt->execute([$company['id']]);
                $count = $stmt->fetch()['count'];
                
                return [
                    'id' => $company['id'],
                    'name' => $company['name'],
                    'employeeCount' => $count,
                    'status' => $company['status'] ?? 'Active'
                ];
            }, $companies);
            
            ResponseFormatter::success($formattedCompanies);
            break;
            
        case 'get_company':
            $id = $_GET['id'] ?? '';
            $stmt = $db->prepare("SELECT * FROM companies WHERE id = ?");
            $stmt->execute([$id]);
            $company = $stmt->fetch();
            
            if (!$company) {
                ResponseFormatter::error('Company not found');
                exit;
            }
            
            // Map contact_number to phone for frontend consistency
            if (isset($company['contact_number'])) {
                $company['phone'] = $company['contact_number'];
            }
            
            ResponseFormatter::success($company);
            break;
            
        case 'create_company':
            checkAdminPermission();
            $data = json_decode(file_get_contents('php://input'), true);
            
            $name = $data['name'] ?? '';
            $address = $data['address'] ?? '';
            $contactNumber = $data['phone'] ?? '';
            $email = $data['email'] ?? '';
            $status = $data['status'] ?? 'Active';
            
            if (empty($name)) {
                ResponseFormatter::error('Company name is required');
                exit;
            }
            
            // Generate company ID
            $stmt = $db->query("SELECT COUNT(*) as count FROM companies");
            $count = $stmt->fetch()['count'];
            $companyId = 'COMP-' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);
            
            // Insert company (note: column is contact_number, not phone)
            $stmt = $db->prepare("INSERT INTO companies (id, name, address, contact_number, email, status, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$companyId, $name, $address, $contactNumber, $email, $status]);
            
            ResponseFormatter::success(['id' => $companyId, 'message' => 'Company created successfully']);
            break;
            
        case 'update_company':
            checkAdminPermission();
            $data = json_decode(file_get_contents('php://input'), true);
            
            $id = $data['id'] ?? '';
            $name = $data['name'] ?? '';
            $address = $data['address'] ?? '';
            $contactNumber = $data['phone'] ?? '';
            $email = $data['email'] ?? '';
            $status = $data['status'] ?? 'Active';
            
            if (empty($id) || empty($name)) {
                ResponseFormatter::error('Missing required fields');
                exit;
            }
            
            // Check if company exists
            $stmt = $db->prepare("SELECT id FROM companies WHERE id = ?");
            $stmt->execute([$id]);
            if (!$stmt->fetch()) {
                ResponseFormatter::error('Company not found');
                exit;
            }
            
            // Update company (note: column is contact_number, not phone)
            $stmt = $db->prepare("UPDATE companies SET name = ?, address = ?, contact_number = ?, email = ?, status = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$name, $address, $contactNumber, $email, $status, $id]);
            
            ResponseFormatter::success(['message' => 'Company updated successfully']);
            break;
            
        // ==================== DEPARTMENT MANAGEMENT ====================
        case 'list_departments':
            $stmt = $db->query("SELECT d.*, c.name as company_name FROM departments d LEFT JOIN companies c ON d.company_id = c.id ORDER BY d.name");
            $departments = $stmt->fetchAll();
            
            $formattedDepartments = array_map(function($dept) use ($db) {
                $stmt = $db->prepare("SELECT COUNT(*) as count FROM employees WHERE department_id = ?");
                $stmt->execute([$dept['id']]);
                $count = $stmt->fetch()['count'];
                
                return [
                    'id' => $dept['id'],
                    'companyId' => $dept['company_id'],
                    'companyName' => $dept['company_name'],
                    'name' => $dept['name'],
                    'code' => $dept['code'],
                    'head' => $dept['head'] ?? '—',
                    'employeeCount' => $count,
                    'status' => $dept['status'] ?? 'Active'
                ];
            }, $departments);
            
            ResponseFormatter::success($formattedDepartments);
            break;
            
        case 'get_department':
            $id = $_GET['id'] ?? '';
            $stmt = $db->prepare("SELECT * FROM departments WHERE id = ?");
            $stmt->execute([$id]);
            $dept = $stmt->fetch();
            
            if (!$dept) {
                ResponseFormatter::error('Department not found');
                exit;
            }
            
            ResponseFormatter::success($dept);
            break;
            
        case 'create_department':
            checkAdminPermission();
            $data = json_decode(file_get_contents('php://input'), true);
            
            $companyId = $data['company_id'] ?? '';
            $name = $data['name'] ?? '';
            $code = $data['code'] ?? '';
            $head = $data['head'] ?? '';
            $status = $data['status'] ?? 'Active';
            
            if (empty($companyId) || empty($name) || empty($code)) {
                ResponseFormatter::error('Company ID, name, and code are required');
                exit;
            }
            
            // Check if company exists
            $stmt = $db->prepare("SELECT id FROM companies WHERE id = ?");
            $stmt->execute([$companyId]);
            if (!$stmt->fetch()) {
                ResponseFormatter::error('Company not found');
                exit;
            }
            
            // Generate department ID
            $stmt = $db->query("SELECT COUNT(*) as count FROM departments");
            $count = $stmt->fetch()['count'];
            $deptId = 'DEPT-' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);
            
            // Insert department
            $stmt = $db->prepare("INSERT INTO departments (id, company_id, name, code, head, status, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$deptId, $companyId, $name, $code, $head, $status]);
            
            ResponseFormatter::success(['id' => $deptId, 'message' => 'Department created successfully']);
            break;
            
        case 'update_department':
            checkAdminPermission();
            $data = json_decode(file_get_contents('php://input'), true);
            
            $id = $data['id'] ?? '';
            $name = $data['name'] ?? '';
            $code = $data['code'] ?? '';
            $head = $data['head'] ?? '';
            $status = $data['status'] ?? 'Active';
            
            if (empty($id) || empty($name) || empty($code)) {
                ResponseFormatter::error('Missing required fields');
                exit;
            }
            
            // Check if department exists
            $stmt = $db->prepare("SELECT id FROM departments WHERE id = ?");
            $stmt->execute([$id]);
            if (!$stmt->fetch()) {
                ResponseFormatter::error('Department not found');
                exit;
            }
            
            // Update department
            $stmt = $db->prepare("UPDATE departments SET name = ?, code = ?, head = ?, status = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$name, $code, $head, $status, $id]);
            
            ResponseFormatter::success(['message' => 'Department updated successfully']);
            break;
            
        // ==================== JOBS MANAGEMENT ====================
        case 'list_jobs':
            $stmt = $db->query("SELECT * FROM jobs ORDER BY title");
            $jobs = $stmt->fetchAll();
            
            $formattedJobs = array_map(function($job) use ($db) {
                $totalVacancies = (int)($job['vacancies'] ?? 0);
                
                // Count active employees assigned to this job (authoritative source)
                $empStmt = $db->prepare("SELECT COUNT(*) as cnt FROM employees WHERE job_id = ? AND employment_status = 'Active'");
                $empStmt->execute([$job['id']]);
                $employedCount = (int)($empStmt->fetch()['cnt'] ?? 0);
                
                $availableVacancies = max(0, $totalVacancies - $employedCount);
                
                return [
                    'id' => $job['id'],
                    'departmentId' => $job['department_id'],
                    'jobTitle' => $job['title'],
                    'level' => $job['level'] ?? 'Mid-Level',
                    'reportsTo' => $job['reports_to'] ?? '—',
                    'vacancies' => $totalVacancies,
                    'availableVacancies' => $availableVacancies,
                    'employedCount' => $employedCount,
                    'salaryMin' => $job['salary_min'] ?? null,
                    'salaryMax' => $job['salary_max'] ?? null,
                    'status' => $job['status'] ?? 'Active'
                ];
            }, $jobs);
            
            ResponseFormatter::success($formattedJobs);
            break;
            
        case 'get_job':
            $id = $_GET['id'] ?? '';
            $stmt = $db->prepare("SELECT * FROM jobs WHERE id = ?");
            $stmt->execute([$id]);
            $job = $stmt->fetch();
            
            if (!$job) {
                ResponseFormatter::error('Job not found');
                exit;
            }
            
            // Map keys for camelCase and front-end compatibility
            $job['jobTitle'] = $job['title'];
            $job['reportsTo'] = $job['reports_to'] ?? '';
            $job['salaryMin'] = $job['salary_min'] ?? null;
            $job['salaryMax'] = $job['salary_max'] ?? null;
            $job['vacancies'] = (int)($job['vacancies'] ?? 0);
            
            // Calculate available vacancies using employees table (authoritative source)
            $empStmt2 = $db->prepare("SELECT COUNT(*) as cnt FROM employees WHERE job_id = ? AND employment_status = 'Active'");
            $empStmt2->execute([$id]);
            $employedCount2 = (int)($empStmt2->fetch()['cnt'] ?? 0);
            
            $job['availableVacancies'] = max(0, $job['vacancies'] - $employedCount2);
            $job['employedCount'] = $employedCount2;
            
            ResponseFormatter::success($job);
            break;
            
        case 'create_job':
            checkAdminPermission();
            $data = json_decode(file_get_contents('php://input'), true);
            
            $departmentId = $data['department_id'] ?? '';
            $title = $data['title'] ?? '';
            $level = $data['level'] ?? '';
            $reportsTo = $data['reportsTo'] ?? $data['reports_to'] ?? '';
            $vacancies = $data['vacancies'] ?? 0;
            $salaryMin = $data['salaryMin'] ?? $data['salary_min'] ?? null;
            $salaryMax = $data['salaryMax'] ?? $data['salary_max'] ?? null;
            $status = $data['status'] ?? 'Active';
            
            if (empty($departmentId) || empty($title) || empty($level)) {
                ResponseFormatter::error('Department ID, title, and level are required');
                exit;
            }
            
            // Check if department exists
            $stmt = $db->prepare("SELECT id FROM departments WHERE id = ?");
            $stmt->execute([$departmentId]);
            if (!$stmt->fetch()) {
                ResponseFormatter::error('Department not found');
                exit;
            }
            
            // Generate job ID
            $stmt = $db->query("SELECT COUNT(*) as count FROM jobs");
            $count = $stmt->fetch()['count'];
            $jobId = 'JOB-' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);
            
            // Insert job
            $stmt = $db->prepare("INSERT INTO jobs (id, department_id, title, level, reports_to, vacancies, salary_min, salary_max, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$jobId, $departmentId, $title, $level, $reportsTo, (int)$vacancies, $salaryMin, $salaryMax, $status]);
            
            ResponseFormatter::success(['id' => $jobId, 'message' => 'Job created successfully']);
            break;
            
        case 'update_position':
        case 'update_job':
            checkAdminPermission();
            $data = json_decode(file_get_contents('php://input'), true);
            
            $id = $data['id'] ?? '';
            $title = $data['title'] ?? '';
            $level = $data['level'] ?? '';
            $reportsTo = $data['reportsTo'] ?? $data['reports_to'] ?? '';
            $vacancies = $data['vacancies'] ?? 0;
            $salaryMin = $data['salaryMin'] ?? $data['salary_min'] ?? null;
            $salaryMax = $data['salaryMax'] ?? $data['salary_max'] ?? null;
            $status = $data['status'] ?? 'Active';
            
            if (empty($id) || empty($title) || empty($level)) {
                ResponseFormatter::error('Missing required fields');
                exit;
            }
            
            // Check if job exists
            $stmt = $db->prepare("SELECT id FROM jobs WHERE id = ?");
            $stmt->execute([$id]);
            if (!$stmt->fetch()) {
                ResponseFormatter::error('Job not found');
                exit;
            }
            
            // Update job
            $stmt = $db->prepare("UPDATE jobs SET title = ?, level = ?, reports_to = ?, vacancies = ?, salary_min = ?, salary_max = ?, status = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$title, $level, $reportsTo, (int)$vacancies, $salaryMin, $salaryMax, $status, $id]);
            
            ResponseFormatter::success(['message' => 'Job updated successfully']);
            break;
            
        // ==================== MASTER DATA MANAGEMENT ====================
        case 'list_master_data':
            $stmt = $db->query("SELECT * FROM master_data ORDER BY data_type, value");
            $masterData = $stmt->fetchAll();
            
            $formattedData = array_map(function($item) {
                return [
                    'id' => $item['id'],
                    'dataType' => $item['data_type'],
                    'value' => $item['value'],
                    'description' => $item['description'] ?? '',
                    'isActive' => (bool)$item['is_active']
                ];
            }, $masterData);
            
            ResponseFormatter::success($formattedData);
            break;
            
        case 'get_master_data':
            $id = $_GET['id'] ?? '';
            $stmt = $db->prepare("SELECT * FROM master_data WHERE id = ?");
            $stmt->execute([$id]);
            $data = $stmt->fetch();
            
            if (!$data) {
                ResponseFormatter::error('Master data not found');
                exit;
            }
            
            ResponseFormatter::success($data);
            break;
            
        case 'create_master_data':
            checkAdminPermission();
            $data = json_decode(file_get_contents('php://input'), true);
            
            $dataType = $data['data_type'] ?? '';
            $value = $data['value'] ?? '';
            $description = $data['description'] ?? '';
            
            if (empty($dataType) || empty($value)) {
                ResponseFormatter::error('Data type and value are required');
                exit;
            }
            
            // Generate ID
            $stmt = $db->query("SELECT COUNT(*) as count FROM master_data");
            $count = $stmt->fetch()['count'];
            $id = 'MD-' . str_pad($count + 1, 6, '0', STR_PAD_LEFT);
            
            $stmt = $db->prepare("INSERT INTO master_data (id, data_type, value, description, is_active, created_at) VALUES (?, ?, ?, ?, 1, NOW())");
            $stmt->execute([$id, $dataType, $value, $description]);
            
            ResponseFormatter::success(['id' => $id, 'message' => 'Master data created successfully']);
            break;
            
        case 'update_master_data':
            checkAdminPermission();
            $data = json_decode(file_get_contents('php://input'), true);
            
            $id = $data['id'] ?? '';
            $dataType = $data['data_type'] ?? '';
            $value = $data['value'] ?? '';
            $description = $data['description'] ?? '';
            $isActive = $data['is_active'] ?? true;
            
            if (empty($id) || empty($dataType) || empty($value)) {
                ResponseFormatter::error('Missing required fields');
                exit;
            }
            
            $stmt = $db->prepare("UPDATE master_data SET data_type = ?, value = ?, description = ?, is_active = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$dataType, $value, $description, $isActive ? 1 : 0, $id]);
            
            ResponseFormatter::success(['message' => 'Master data updated successfully']);
            break;
            
        default:
            ResponseFormatter::error('Invalid action', 'INVALID_ACTION', 400);
            break;
    }
    
} catch (Exception $e) {
    ErrorHandler::log($e->getMessage(), 'error', [
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);
    ResponseFormatter::error($e->getMessage(), 'SERVER_ERROR', 500);
}
?>
