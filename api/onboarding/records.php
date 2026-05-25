<?php
/**
 * Onboarding Records API Endpoint
 * Handles CRUD operations for onboarding records
 */

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
            handleGetRecords($conn);
            break;
        case 'POST':
            handleCreateRecord($conn, $input);
            break;
        case 'PUT':
            handleUpdateRecord($conn, $input);
            break;
        case 'DELETE':
            handleDeleteRecord($conn);
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

function handleGetRecords($conn) {
    try {
        // JOIN with employees, companies, departments, jobs to get full details
        $query = "SELECT 
            onr.*,
            e.firstname,
            e.middlename,
            e.surname,
            e.email as emp_email,
            e.profile_photo as emp_profile_photo,
            c.name as company_name,
            d.name as department_name,
            j.title as job_title
        FROM onboarding_records onr
        LEFT JOIN employees e ON onr.employee_id = e.id
        LEFT JOIN companies c ON onr.company_id = c.id
        LEFT JOIN departments d ON onr.department_id = d.id
        LEFT JOIN jobs j ON onr.job_id = j.id
        ORDER BY onr.created_at DESC";
        
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $records = $stmt->fetchAll();
        
        // Format records with proper field names
        $formattedRecords = [];
        foreach ($records as $record) {
            // Parse JSON tasks
            $tasks = json_decode($record['tasks'], true) ?: [];
            
            // Build employee name
            $employeeName = trim(($record['firstname'] ?? '') . ' ' . 
                                 ($record['middlename'] ? $record['middlename'] . ' ' : '') . 
                                 ($record['surname'] ?? ''));
            if (empty($employeeName)) {
                $employeeName = $record['employee_name'] ?? 'Unknown Employee';
            }
            
            $formattedRecords[] = [
                'id' => $record['id'],
                'employee_id' => $record['employee_id'],
                'employee_name' => $employeeName,
                'employee_email' => $record['emp_email'] ?? $record['employee_email'] ?? '',
                'job' => $record['job_title'] ?? 'Unknown Position',
                'position' => $record['job_title'] ?? 'Unknown Position',
                'department' => $record['department_name'] ?? 'Not assigned',
                'company' => $record['company_name'] ?? 'Not assigned',
                'job_id' => $record['job_id'],
                'department_id' => $record['department_id'],
                'company_id' => $record['company_id'],
                'start_date' => $record['start_date'],
                'progress' => $record['progress'],
                'completion_date' => $record['completion_date'],
                'tasks' => $tasks,
                'notes' => $record['notes'] ?? '',
                'avatar' => $record['avatar'] ?? strtoupper(substr($employeeName, 0, 2)),
                'color' => $record['color'] ?? 'linear-gradient(145deg, #6366f1, #a78bfa)',
                'profile_photo' => $record['emp_profile_photo'] ?? null,
                'created_at' => $record['created_at'],
                'updated_at' => $record['updated_at']
            ];
        }
        
        echo json_encode(['success' => true, 'data' => $formattedRecords]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error fetching records: ' . $e->getMessage()]);
    }
}

function handleCreateRecord($conn, $input) {
    try {
        // Validate required fields - now using IDs instead of text fields
        $required = ['employee_id', 'employee_name', 'employee_email', 'job_id', 'department_id', 'company_id', 'start_date'];
        foreach ($required as $field) {
            if (empty($input[$field])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => "Missing required field: $field"]);
                return;
            }
        }
        
        // Generate unique ID
        $id = 'ONB-' . date('Y') . '-' . str_pad(rand(1, 9999), 3, '0', STR_PAD_LEFT);
        
        // Default tasks for new onboarding
        $defaultTasks = [
            ['text' => 'Complete employment forms', 'completed' => false],
            ['text' => 'IT equipment setup', 'completed' => false],
            ['text' => 'Office tour and introductions', 'completed' => false],
            ['text' => 'HR orientation session', 'completed' => false],
            ['text' => 'Department training', 'completed' => false],
            ['text' => 'System access setup', 'completed' => false]
        ];
        
        $tasks = isset($input['tasks']) ? $input['tasks'] : $defaultTasks;
        
        $query = "INSERT INTO onboarding_records (
            id, employee_id, employee_name, employee_email, job_id, department_id, company_id,
            start_date, progress, tasks, notes, avatar, color
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($query);
        $stmt->execute([
            $id,
            $input['employee_id'],
            $input['employee_name'],
            $input['employee_email'],
            $input['job_id'] ?? null,
            $input['department_id'] ?? null,
            $input['company_id'] ?? null,
            $input['start_date'],
            $input['progress'] ?? 'Not Started',
            json_encode($tasks),
            $input['notes'] ?? '',
            $input['avatar'] ?? strtoupper(substr($input['employee_name'], 0, 2)),
            $input['color'] ?? 'linear-gradient(145deg, #6366f1, #a78bfa)'
        ]);
        
        echo json_encode(['success' => true, 'id' => $id, 'message' => 'Onboarding record created successfully']);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error creating record: ' . $e->getMessage()]);
    }
}

function handleUpdateRecord($conn, $input) {
    try {
        if (empty($input['id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing record ID']);
            return;
        }
        
        // Get the old progress status before updating
        $checkQuery = "SELECT progress, employee_id FROM onboarding_records WHERE id = ?";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->execute([$input['id']]);
        $oldRecord = $checkStmt->fetch();
        $oldProgress = $oldRecord['progress'] ?? '';
        $employeeId = $oldRecord['employee_id'] ?? null;
        
        $query = "UPDATE onboarding_records SET 
                    employee_name = ?, employee_email = ?, job_id = ?, department_id = ?, company_id = ?,
                    start_date = ?, progress = ?, completion_date = ?, tasks = ?, notes = ?,
                    updated_at = CURRENT_TIMESTAMP
                  WHERE id = ?";
        
        $stmt = $conn->prepare($query);
        $stmt->execute([
            $input['employee_name'],
            $input['employee_email'],
            $input['job_id'] ?? null,
            $input['department_id'] ?? null,
            $input['company_id'] ?? null,
            $input['start_date'],
            $input['progress'],
            $input['completion_date'] ?? null,
            json_encode($input['tasks'] ?? []),
            $input['notes'] ?? '',
            $input['id']
        ]);
        
        // If progress changed to "Completed" and employee doesn't exist yet, create employee record
        if ($input['progress'] === 'Completed' && $oldProgress !== 'Completed') {
            try {
                $result = createEmployeeFromOnboarding($conn, $input, $employeeId);
                
                if (!$result['success']) {
                    // Employee creation failed - return error to frontend
                    http_response_code(500);
                    echo json_encode([
                        'success' => false, 
                        'message' => 'Onboarding updated but employee creation failed: ' . $result['message'],
                        'employee_created' => false
                    ]);
                    return;
                }
                
                // Success - return with employee_id
                echo json_encode([
                    'success' => true, 
                    'message' => 'Onboarding record updated and employee created successfully',
                    'employee_created' => true,
                    'employee_id' => $result['employee_id']
                ]);
                return;
                
            } catch (Exception $e) {
                // Employee creation threw an exception - return error to frontend
                error_log("Exception creating employee from onboarding: " . $e->getMessage());
                http_response_code(500);
                echo json_encode([
                    'success' => false, 
                    'message' => 'Onboarding updated but employee creation failed: ' . $e->getMessage(),
                    'employee_created' => false
                ]);
                return;
            }
        }
        
        echo json_encode(['success' => true, 'message' => 'Onboarding record updated successfully']);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error updating record: ' . $e->getMessage()]);
    }
}

function createEmployeeFromOnboarding($conn, $onboardingData, $employeeId) {
    // Check if employee already exists
    if ($employeeId) {
        $checkEmp = $conn->prepare("SELECT id FROM employees WHERE id = ?");
        $checkEmp->execute([$employeeId]);
        if ($checkEmp->fetch()) {
            // Employee already exists, skip creation
            error_log("Employee already exists with ID: " . $employeeId);
            return ['success' => true, 'message' => 'Employee already exists', 'employee_id' => $employeeId];
        }
    }
    
    // Use the IDs directly from onboarding data
    $companyId = $onboardingData['company_id'] ?? null;
    $departmentId = $onboardingData['department_id'] ?? null;
    $jobId = $onboardingData['job_id'] ?? null;
    
    if (!$companyId) {
        $errorMsg = "Company ID missing in onboarding data";
        error_log($errorMsg);
        throw new Exception($errorMsg);
    }
    
    if (!$departmentId) {
        $errorMsg = "Department ID missing in onboarding data";
        error_log($errorMsg);
        throw new Exception($errorMsg);
    }
    
    // Get job title for fallback display
    $jobTitle = 'Unknown Position';
    if ($jobId) {
        $jobQuery = "SELECT title FROM jobs WHERE id = ? LIMIT 1";
        $jobStmt = $conn->prepare($jobQuery);
        $jobStmt->execute([$jobId]);
        $jobRow = $jobStmt->fetch();
        if ($jobRow) {
            $jobTitle = $jobRow['title'];
        }
    }
    
    // Parse employee name
    $nameParts = explode(' ', trim($onboardingData['employee_name']));
    $firstname = $nameParts[0] ?? '';
    $surname = end($nameParts);
    $middlename = '';
    if (count($nameParts) > 2) {
        $middlename = implode(' ', array_slice($nameParts, 1, -1));
    }
    
    // Generate unique employee ID
    $newEmployeeId = 'EMP-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    
    // Check if ID already exists and regenerate if needed
    $checkId = $conn->prepare("SELECT id FROM employees WHERE id = ?");
    $checkId->execute([$newEmployeeId]);
    while ($checkId->fetch()) {
        $newEmployeeId = 'EMP-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        $checkId->execute([$newEmployeeId]);
    }
    
    // Generate avatar and color
    $avatar = strtoupper(substr($firstname, 0, 1) . substr($surname, 0, 1));
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
    
    // Create employee record using IDs
    $insertQuery = "INSERT INTO employees (
        id,
        firstname, middlename, surname, email, contact_number,
        company_id, department_id, job_id, job, employment_status,
        hire_date, salary, avatar, color, created_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)";
    
    $insertStmt = $conn->prepare($insertQuery);
    $success = $insertStmt->execute([
        $newEmployeeId,
        $firstname,
        $middlename,
        $surname,
        $onboardingData['employee_email'],
        '', // contact_number - not available in onboarding
        $companyId,
        $departmentId,
        $jobId,
        $jobTitle, // Store job title as fallback
        'Active', // employment_status
        $onboardingData['start_date'],
        0, // salary - default to 0
        $avatar,
        $color
    ]);
    
    if (!$success) {
        $errorMsg = "Failed to insert employee record into database";
        error_log($errorMsg);
        throw new Exception($errorMsg);
    }
    
    // Verify the employee was created
    $verifyQuery = "SELECT id FROM employees WHERE id = ?";
    $verifyStmt = $conn->prepare($verifyQuery);
    $verifyStmt->execute([$newEmployeeId]);
    if (!$verifyStmt->fetch()) {
        $errorMsg = "Employee record not found after insertion";
        error_log($errorMsg);
        throw new Exception($errorMsg);
    }
    
    error_log("Employee created successfully from onboarding with ID: " . $newEmployeeId . ", company_id: " . $companyId . ", department_id: " . $departmentId . ", job_id: " . $jobId);
    
    // Update onboarding record with employee_id
    $updateOnboardingQuery = "UPDATE onboarding_records SET employee_id = ? WHERE id = ?";
    $updateOnboardingStmt = $conn->prepare($updateOnboardingQuery);
    $updateOnboardingStmt->execute([$newEmployeeId, $onboardingData['id']]);
    
    // Decrement job vacancies by 1 (official vacancy consumed on successful onboarding)
    if ($jobId) {
        $decrQuery = "UPDATE jobs SET vacancies = GREATEST(0, vacancies - 1) WHERE id = ?";
        $decrStmt = $conn->prepare($decrQuery);
        $decrStmt->execute([$jobId]);
        error_log("Decremented vacancies for job: " . $jobId);
    }
    
    return ['success' => true, 'message' => 'Employee created successfully', 'employee_id' => $newEmployeeId];
}

function handleDeleteRecord($conn) {
    try {
        $id = $_GET['id'] ?? '';
        if (empty($id)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing record ID']);
            return;
        }
        
        $query = "DELETE FROM onboarding_records WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$id]);
        
        echo json_encode(['success' => true, 'message' => 'Onboarding record deleted successfully']);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error deleting record: ' . $e->getMessage()]);
    }
}
?>