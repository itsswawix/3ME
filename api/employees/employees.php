<?php
/**
 * Employees API Endpoint
 * Handles CRUD operations for employees
 */

// Prevent any output before JSON response
ob_start();

// Enable error reporting but don't display errors
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Custom error handler
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    error_log("PHP Error [$errno]: $errstr in $errfile on line $errline");
    if (in_array($errno, [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR])) {
        ob_clean();
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => ['message' => 'A system error occurred.', 'code' => 'SYSTEM_ERROR']
        ]);
        exit;
    }
    return false;
});

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../../config/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $method = $_SERVER['REQUEST_METHOD'];
    $input = json_decode(file_get_contents('php://input'), true);
    
    switch ($method) {
        case 'GET':
            // Check if this is a request for departments
            if (isset($_GET['action']) && $_GET['action'] === 'getDepartments') {
                handleGetDepartments($conn);
            } else {
                handleGetEmployees($conn);
            }
            break;
        case 'POST':
            handleCreateEmployee($conn, $input);
            break;
        case 'PUT':
            handleUpdateEmployee($conn, $input);
            break;
        case 'DELETE':
            handleDeleteEmployee($conn);
            break;
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}

function handleGetEmployees($conn) {
    try {
        // First check if employees table exists
        $checkTable = $conn->query("SHOW TABLES LIKE 'employees'");
        if ($checkTable->rowCount() === 0) {
            if (ob_get_level() > 0) ob_clean();
            echo json_encode(['success' => true, 'data' => [], 'message' => 'Employees table not found']);
            return;
        }
        
        // JOIN with companies and departments to get names
        $query = "SELECT 
            e.*,
            c.name as company,
            d.name as department
        FROM employees e
        LEFT JOIN companies c ON e.company_id = c.id
        LEFT JOIN departments d ON e.department_id = d.id
        ORDER BY e.created_at DESC";
        
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $employees = $stmt->fetchAll();
        
        // Format the response to match what the frontend expects
        $formattedEmployees = array_map(function($emp) {
            return [
                'id' => $emp['id'],
                'employee_id' => $emp['id'], // Use id as employee_id
                'firstname' => $emp['firstname'] ?? '',
                'middlename' => $emp['middlename'] ?? '',
                'surname' => $emp['surname'] ?? '',
                'suffix' => $emp['suffix'] ?? '',
                'email' => $emp['email'] ?? '',
                'phone' => $emp['contact_number'] ?? '', // Map contact_number to phone
                'position' => $emp['job'] ?? '', // Use job field as position
                'job' => $emp['job'] ?? '', // Use job field
                'job_id' => $emp['job_id'] ?? null, // Include job_id foreign key
                'department' => $emp['department'] ?? '', // From JOIN
                'department_id' => $emp['department_id'] ?? null, // Include department_id foreign key
                'company' => $emp['company'] ?? '', // From JOIN
                'company_id' => $emp['company_id'] ?? null, // Include company_id foreign key
                'status' => $emp['employment_status'] ?? 'Active', // Map employment_status to status
                'join_date' => $emp['hire_date'] ?? date('Y-m-d'), // Map hire_date to join_date
                'salary' => $emp['salary'] ?? 0,
                'address' => $emp['address'] ?? '',
                'emergency_contact_name' => $emp['emergency_contact_name'] ?? '',
                'emergency_contact_phone' => $emp['emergency_contact_phone'] ?? '',
                'emergency_contact_relation' => $emp['emergency_contact_relation'] ?? '',
                'sss' => $emp['sss_number'] ?? '',
                'philhealth' => $emp['philhealth_number'] ?? '',
                'pagibig' => $emp['pagibig_number'] ?? '',
                'tin' => $emp['tin_number'] ?? '',
                'avatar' => $emp['avatar'] ?? '',
                'color' => $emp['color'] ?? '',
                'profile_photo' => $emp['profile_photo'] ?? null,
                'profile_photo_filename' => $emp['profile_photo_filename'] ?? null
            ];
        }, $employees);
        
        if (ob_get_level() > 0) ob_clean();
        echo json_encode(['success' => true, 'data' => $formattedEmployees]);
    } catch (Exception $e) {
        if (ob_get_level() > 0) ob_clean();
        http_response_code(500);
        error_log('Error in handleGetEmployees: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error fetching employees: ' . $e->getMessage()]);
    }
}

function handleGetDepartments($conn) {
    try {
        $companyId = $_GET['company_id'] ?? '';
        
        if (empty($companyId)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Company ID is required']);
            return;
        }
        
        $query = "SELECT * FROM departments WHERE company_id = ? AND status = 'Active' ORDER BY name";
        $stmt = $conn->prepare($query);
        $stmt->execute([$companyId]);
        $departments = $stmt->fetchAll();
        
        echo json_encode(['success' => true, 'departments' => $departments]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error fetching departments: ' . $e->getMessage()]);
    }
}

function handleCreateEmployee($conn, $input) {
    try {
        // Validate required fields
        $required = ['firstname', 'surname', 'email', 'job', 'department', 'company', 'join_date'];
        foreach ($required as $field) {
            if (empty($input[$field])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => "Missing required field: $field"]);
                return;
            }
        }
        
        // Use provided IDs if available, otherwise look up by name
        $companyId = $input['company_id'] ?? null;
        if (!$companyId) {
            // Fallback: look up by name
            $companyQuery = "SELECT id FROM companies WHERE name = ? LIMIT 1";
            $companyStmt = $conn->prepare($companyQuery);
            $companyStmt->execute([$input['company']]);
            $companyRow = $companyStmt->fetch();
            $companyId = $companyRow ? $companyRow['id'] : null;
            
            if (!$companyId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Company not found: ' . $input['company']]);
                return;
            }
        }
        
        // Use provided department_id if available, otherwise look up by name
        $departmentId = $input['department_id'] ?? null;
        if (!$departmentId) {
            // Fallback: look up by name and company_id
            $deptQuery = "SELECT id FROM departments WHERE name = ? AND company_id = ? LIMIT 1";
            $deptStmt = $conn->prepare($deptQuery);
            $deptStmt->execute([$input['department'], $companyId]);
            $deptRow = $deptStmt->fetch();
            $departmentId = $deptRow ? $deptRow['id'] : null;
            
            if (!$departmentId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Department not found: ' . $input['department'] . ' in company: ' . $input['company']]);
                return;
            }
        }
        
        // Use provided job_id if available, otherwise look up by title (optional)
        $jobId = $input['job_id'] ?? null;
        if (!$jobId && !empty($input['job'])) {
            // Fallback: look up by title
            $jobQuery = "SELECT id FROM jobs WHERE title = ? LIMIT 1";
            $jobStmt = $conn->prepare($jobQuery);
            $jobStmt->execute([$input['job']]);
            $jobRow = $jobStmt->fetch();
            $jobId = $jobRow ? $jobRow['id'] : null;
        }
        
        // Generate unique employee ID
        $employeeId = 'EMP-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        // Check if ID already exists and regenerate if needed
        $checkId = $conn->prepare("SELECT id FROM employees WHERE id = ?");
        $checkId->execute([$employeeId]);
        while ($checkId->fetch()) {
            $employeeId = 'EMP-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $checkId->execute([$employeeId]);
        }
        
        // Generate avatar and color
        $avatar = strtoupper(substr($input['firstname'], 0, 1) . substr($input['surname'], 0, 1));
        $colors = [
            'linear-gradient(145deg, #4f46e5, #7c3aed)',
            'linear-gradient(145deg, #ef4444, #f87171)',
            'linear-gradient(145deg, #10b981, #34d399)',
            'linear-gradient(145deg, #f59e0b, #fbbf24)',
            'linear-gradient(145deg, #8b5cf6, #a78bfa)',
            'linear-gradient(145deg, #06b6d4, #67e8f9)',
            'linear-gradient(145deg, #ec4899, #f472b6)',
            'linear-gradient(145deg, #14b8a6, #5eead4)'
        ];
        $color = $colors[array_rand($colors)];
        
        // Ensure profile_photo columns exist
        $conn->exec("ALTER TABLE employees
            ADD COLUMN IF NOT EXISTS profile_photo VARCHAR(500) DEFAULT NULL,
            ADD COLUMN IF NOT EXISTS profile_photo_filename VARCHAR(255) DEFAULT NULL,
            ADD COLUMN IF NOT EXISTS emergency_contact_name VARCHAR(255) DEFAULT NULL,
            ADD COLUMN IF NOT EXISTS emergency_contact_phone VARCHAR(20) DEFAULT NULL,
            ADD COLUMN IF NOT EXISTS emergency_contact_relation VARCHAR(100) DEFAULT NULL,
            ADD COLUMN IF NOT EXISTS sss_number VARCHAR(20) DEFAULT NULL,
            ADD COLUMN IF NOT EXISTS philhealth_number VARCHAR(20) DEFAULT NULL,
            ADD COLUMN IF NOT EXISTS pagibig_number VARCHAR(20) DEFAULT NULL,
            ADD COLUMN IF NOT EXISTS tin_number VARCHAR(20) DEFAULT NULL");
        
        // Insert using the correct schema with foreign keys
        $query = "INSERT INTO employees (
            id,
            firstname, middlename, surname, suffix, email, contact_number,
            company_id, department_id, job_id, job, employment_status,
            hire_date, salary, avatar, color, address,
            emergency_contact_name, emergency_contact_phone, emergency_contact_relation,
            sss_number, philhealth_number, pagibig_number, tin_number,
            profile_photo, profile_photo_filename, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)";
        
        $stmt = $conn->prepare($query);
        $stmt->execute([
            $employeeId,
            $input['firstname'],
            $input['middlename'] ?? '',
            $input['surname'],
            $input['suffix'] ?? '',
            $input['email'],
            $input['phone'] ?? '',
            $companyId,
            $departmentId,
            $jobId,
            $input['job'], // Store job title as fallback
            $input['status'] ?? 'Active',
            $input['join_date'],
            $input['salary'] ?? 0,
            $avatar,
            $color,
            $input['address'] ?? '',
            $input['emergency_contact_name'] ?? '',
            $input['emergency_contact_phone'] ?? '',
            $input['emergency_contact_relation'] ?? '',
            $input['sss_number'] ?? '',
            $input['philhealth_number'] ?? '',
            $input['pagibig_number'] ?? '',
            $input['tin_number'] ?? '',
            $input['profile_photo'] ?? null,
            $input['profile_photo_filename'] ?? null
        ]);
        
        // Verify the insert was successful
        if (!$employeeId) {
            error_log('Employee insert failed - no ID generated');
            throw new Exception('Failed to insert employee record - no ID generated');
        }
        
        // Verify the employee exists in the database
        $verifyQuery = "SELECT id, firstname, surname, email FROM employees WHERE id = ?";
        $verifyStmt = $conn->prepare($verifyQuery);
        $verifyStmt->execute([$employeeId]);
        $verifiedEmployee = $verifyStmt->fetch();
        
        if (!$verifiedEmployee) {
            error_log('Employee verification failed - record not found after insert');
            throw new Exception('Employee record was not found after insertion');
        }
        
        error_log('Employee created successfully: ID=' . $employeeId . ', Email=' . $input['email']);
        
        if (ob_get_level() > 0) ob_clean();
        echo json_encode([
            'success' => true, 
            'employee_id' => $employeeId,
            'id' => $employeeId,
            'message' => 'Employee created successfully',
            'verified' => true
        ]);
        
    } catch (Exception $e) {
        if (ob_get_level() > 0) ob_clean();
        http_response_code(500);
        error_log('Error creating employee: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error creating employee: ' . $e->getMessage()]);
    }
}

function handleUpdateEmployee($conn, $input) {
    try {
        if (empty($input['id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing employee ID']);
            return;
        }
        
        // Use provided IDs if available, otherwise look up by name
        $companyId = $input['company_id'] ?? null;
        if (!$companyId) {
            // Fallback: look up by name
            $companyQuery = "SELECT id FROM companies WHERE name = ? LIMIT 1";
            $companyStmt = $conn->prepare($companyQuery);
            $companyStmt->execute([$input['company']]);
            $companyRow = $companyStmt->fetch();
            $companyId = $companyRow ? $companyRow['id'] : null;
            
            if (!$companyId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Company not found: ' . $input['company']]);
                return;
            }
        }
        
        // Use provided department_id if available, otherwise look up by name
        $departmentId = $input['department_id'] ?? null;
        if (!$departmentId) {
            // Fallback: look up by name and company_id
            $deptQuery = "SELECT id FROM departments WHERE name = ? AND company_id = ? LIMIT 1";
            $deptStmt = $conn->prepare($deptQuery);
            $deptStmt->execute([$input['department'], $companyId]);
            $deptRow = $deptStmt->fetch();
            $departmentId = $deptRow ? $deptRow['id'] : null;
            
            if (!$departmentId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Department not found: ' . $input['department']]);
                return;
            }
        }
        
        // Use provided job_id if available, otherwise look up by title (optional)
        $jobId = $input['job_id'] ?? null;
        if (!$jobId && !empty($input['job'])) {
            // Fallback: look up by title
            $jobQuery = "SELECT id FROM jobs WHERE title = ? LIMIT 1";
            $jobStmt = $conn->prepare($jobQuery);
            $jobStmt->execute([$input['job']]);
            $jobRow = $jobStmt->fetch();
            $jobId = $jobRow ? $jobRow['id'] : null;
        }
        
        // Ensure all required columns exist in the employees table
        try {
            $conn->exec("ALTER TABLE employees
                ADD COLUMN IF NOT EXISTS emergency_contact_name VARCHAR(255) DEFAULT NULL,
                ADD COLUMN IF NOT EXISTS emergency_contact_phone VARCHAR(20) DEFAULT NULL,
                ADD COLUMN IF NOT EXISTS emergency_contact_relation VARCHAR(100) DEFAULT NULL,
                ADD COLUMN IF NOT EXISTS sss_number VARCHAR(20) DEFAULT NULL,
                ADD COLUMN IF NOT EXISTS philhealth_number VARCHAR(20) DEFAULT NULL,
                ADD COLUMN IF NOT EXISTS pagibig_number VARCHAR(20) DEFAULT NULL,
                ADD COLUMN IF NOT EXISTS tin_number VARCHAR(20) DEFAULT NULL");
        } catch (Exception $alterException) {
            error_log('Note: Could not add columns (may already exist): ' . $alterException->getMessage());
        }
        
        $query = "UPDATE employees SET 
                    firstname = ?, middlename = ?, surname = ?, suffix = ?, email = ?, contact_number = ?,
                    job = ?, job_id = ?, department_id = ?, company_id = ?, 
                    employment_status = ?, hire_date = ?, salary = ?, address = ?,
                    emergency_contact_name = ?, emergency_contact_phone = ?, emergency_contact_relation = ?,
                    sss_number = ?, philhealth_number = ?, pagibig_number = ?, tin_number = ?,
                    profile_photo = ?, profile_photo_filename = ?,
                    updated_at = CURRENT_TIMESTAMP
                  WHERE id = ?";
        
        $stmt = $conn->prepare($query);
        $stmt->execute([
            $input['firstname'],
            $input['middlename'] ?? '',
            $input['surname'],
            $input['suffix'] ?? '',
            $input['email'],
            $input['phone'] ?? '',
            $input['job'],
            $jobId,
            $departmentId,
            $companyId,
            $input['status'] ?? 'Active',
            $input['join_date'] ?? date('Y-m-d'),
            $input['salary'] ?? 0,
            $input['address'] ?? '',
            $input['emergency_contact_name'] ?? '',
            $input['emergency_contact_phone'] ?? '',
            $input['emergency_contact_relation'] ?? '',
            $input['sss_number'] ?? '',
            $input['philhealth_number'] ?? '',
            $input['pagibig_number'] ?? '',
            $input['tin_number'] ?? '',
            $input['profile_photo'] ?? null,
            $input['profile_photo_filename'] ?? null,
            $input['id']
        ]);
        
        if (ob_get_level() > 0) ob_clean();
        echo json_encode(['success' => true, 'message' => 'Employee updated successfully']);
        
    } catch (Exception $e) {
        if (ob_get_level() > 0) ob_clean();
        http_response_code(500);
        error_log('Error updating employee: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error updating employee: ' . $e->getMessage()]);
    }
}

function handleDeleteEmployee($conn) {
    try {
        $id = $_GET['id'] ?? '';
        if (empty($id)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing employee ID']);
            return;
        }
        
        $query = "DELETE FROM employees WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$id]);
        
        echo json_encode(['success' => true, 'message' => 'Employee deleted successfully']);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error deleting employee: ' . $e->getMessage()]);
    }
}
?>