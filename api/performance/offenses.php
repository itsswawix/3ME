<?php
/**
 * Performance Offenses API Endpoint
 * Handles CRUD operations for employee disciplinary records
 */

// Prevent any output before JSON response
ob_start();

// Enable error reporting but don't display errors
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Custom error handler to return JSON format errors
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    error_log("PHP Error [$errno]: $errstr in $errfile on line $errline");
    if (in_array($errno, [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR])) {
        ob_clean();
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'A system error occurred.',
            'error' => ['message' => $errstr, 'code' => 'SYSTEM_ERROR']
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
            handleGetOffenses($conn);
            break;
        case 'POST':
            handleCreateOffense($conn, $input);
            break;
        case 'PUT':
            handleUpdateOffense($conn, $input);
            break;
        case 'DELETE':
            handleDeleteOffense($conn);
            break;
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
    }
    
} catch (Exception $e) {
    ob_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}

function handleGetOffenses($conn) {
    try {
        // Read offenses with employee details
        $query = "SELECT 
            dr.*,
            e.firstname as emp_firstname,
            e.surname as emp_surname,
            e.email as emp_email,
            e.avatar as emp_avatar,
            e.color as emp_color,
            d.name as department
        FROM disciplinary_records dr
        LEFT JOIN employees e ON dr.employee_id = e.id
        LEFT JOIN departments d ON e.department_id = d.id
        ORDER BY dr.date DESC, dr.created_at DESC";
        
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $records = $stmt->fetchAll();
        
        $offenses = [];
        foreach ($records as $r) {
            $firstName = $r['emp_firstname'] ?? '';
            $surname = $r['emp_surname'] ?? '';
            $fullName = trim($firstName . ' ' . $surname);
            if (empty($fullName)) {
                $fullName = 'Unknown Employee';
            }
            
            // Generate initials for avatar if empty
            $avatar = $r['emp_avatar'] ?? '';
            if (empty($avatar) && !empty($fullName)) {
                $avatar = strtoupper(implode('', array_map(function($n) { return $n[0] ?? ''; }, explode(' ', $fullName))));
                if (strlen($avatar) > 2) $avatar = substr($avatar, 0, 2);
            }
            if (empty($avatar)) $avatar = 'EM';
            
            $offenses[] = [
                'id' => $r['id'],
                'employeeId' => $r['employee_id'],
                'employeeName' => $fullName,
                'employeeEmail' => $r['emp_email'] ?? 'no-email@company.com',
                'department' => $r['department'] ?? 'General',
                'offenseType' => $r['offense_type'],
                'severity' => $r['severity'],
                'severityScore' => intval($r['severity_score']),
                'date' => $r['date'],
                'status' => $r['status'],
                'reportedBy' => $r['reported_by'],
                'avatar' => $avatar,
                'color' => $r['emp_color'] ?? '#64748b',
                'description' => $r['description'],
                'actionTaken' => $r['action_taken'] ?? ''
            ];
        }
        
        // Calculate statistics
        // 1. Total unique employees with offenses
        $uniqueEmpQuery = "SELECT COUNT(DISTINCT employee_id) FROM disciplinary_records";
        $uniqueEmpStmt = $conn->prepare($uniqueEmpQuery);
        $uniqueEmpStmt->execute();
        $totalEmployees = intval($uniqueEmpStmt->fetchColumn());
        
        // 2. Total offenses count
        $totalOffenses = count($offenses);
        
        // 3. Pending/Under Investigation count
        $pendingQuery = "SELECT COUNT(*) FROM disciplinary_records WHERE status IN ('Pending Review', 'Under Investigation')";
        $pendingStmt = $conn->prepare($pendingQuery);
        $pendingStmt->execute();
        $pendingCount = intval($pendingStmt->fetchColumn());
        
        // 4. This month offenses count
        $currentMonthStart = date('Y-m-01');
        $currentMonthEnd = date('Y-m-t');
        $monthQuery = "SELECT COUNT(*) FROM disciplinary_records WHERE date BETWEEN ? AND ?";
        $monthStmt = $conn->prepare($monthQuery);
        $monthStmt->execute([$currentMonthStart, $currentMonthEnd]);
        $monthCount = intval($monthStmt->fetchColumn());
        
        ob_clean();
        echo json_encode([
            'success' => true,
            'offenses' => $offenses,
            'stats' => [
                'totalEmployeesCount' => $totalEmployees,
                'totalOffensesCount' => $totalOffenses,
                'pendingCount' => $pendingCount,
                'monthCount' => $monthCount
            ]
        ]);
        
    } catch (Exception $e) {
        ob_clean();
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error fetching offenses: ' . $e->getMessage()]);
    }
}

function handleCreateOffense($conn, $input) {
    try {
        // Validate required fields
        $required = ['employee_id', 'offense_type', 'severity', 'date', 'status', 'reported_by', 'description'];
        foreach ($required as $field) {
            if (empty($input[$field])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => "Missing required field: $field"]);
                return;
            }
        }
        
        // Check if employee exists
        $empCheck = $conn->prepare("SELECT id FROM employees WHERE id = ?");
        $empCheck->execute([$input['employee_id']]);
        if (!$empCheck->fetch()) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => "Employee with ID '{$input['employee_id']}' not found."]);
            return;
        }
        
        // Calculate severity score
        $severity = $input['severity'];
        $severityScore = 1;
        if ($severity === 'Moderate') $severityScore = 2;
        else if ($severity === 'Major') $severityScore = 3;
        else if ($severity === 'Critical') $severityScore = 4;
        
        // Generate high-quality sequential ID: OFF-001, OFF-002, etc.
        $idQuery = "SELECT MAX(CAST(SUBSTRING(id, 5) AS UNSIGNED)) as max_num FROM disciplinary_records WHERE id LIKE 'OFF-%'";
        $idStmt = $conn->prepare($idQuery);
        $idStmt->execute();
        $maxNum = $idStmt->fetchColumn();
        $nextNum = $maxNum ? intval($maxNum) + 1 : 1;
        $newId = 'OFF-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
        
        $query = "INSERT INTO disciplinary_records (
            id, employee_id, offense_type, severity, severity_score, date,
            status, reported_by, description, action_taken
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($query);
        $stmt->execute([
            $newId,
            $input['employee_id'],
            $input['offense_type'],
            $severity,
            $severityScore,
            $input['date'],
            $input['status'],
            $input['reported_by'],
            $input['description'],
            $input['action_taken'] ?? null
        ]);
        
        ob_clean();
        echo json_encode([
            'success' => true,
            'message' => 'Offense recorded successfully',
            'id' => $newId
        ]);
        
    } catch (Exception $e) {
        ob_clean();
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error creating offense: ' . $e->getMessage()]);
    }
}

function handleUpdateOffense($conn, $input) {
    try {
        if (empty($input['id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing offense record ID']);
            return;
        }
        
        $id = $input['id'];
        
        // Check if record exists
        $check = $conn->prepare("SELECT id FROM disciplinary_records WHERE id = ?");
        $check->execute([$id]);
        if (!$check->fetch()) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => "Offense record '{$id}' not found."]);
            return;
        }
        
        // Calculate severity score
        $severity = $input['severity'] ?? 'Minor';
        $severityScore = 1;
        if ($severity === 'Moderate') $severityScore = 2;
        else if ($severity === 'Major') $severityScore = 3;
        else if ($severity === 'Critical') $severityScore = 4;
        
        $query = "UPDATE disciplinary_records SET 
            offense_type = ?, 
            severity = ?, 
            severity_score = ?, 
            date = ?, 
            status = ?, 
            reported_by = ?, 
            description = ?, 
            action_taken = ?,
            updated_at = CURRENT_TIMESTAMP
            WHERE id = ?";
            
        $stmt = $conn->prepare($query);
        $stmt->execute([
            $input['offense_type'],
            $severity,
            $severityScore,
            $input['date'],
            $input['status'],
            $input['reported_by'],
            $input['description'],
            $input['action_taken'] ?? null,
            $id
        ]);
        
        ob_clean();
        echo json_encode([
            'success' => true,
            'message' => 'Offense record updated successfully'
        ]);
        
    } catch (Exception $e) {
        ob_clean();
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error updating offense: ' . $e->getMessage()]);
    }
}

function handleDeleteOffense($conn) {
    try {
        $id = $_GET['id'] ?? '';
        if (empty($id)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing offense record ID']);
            return;
        }
        
        // Check if record exists
        $check = $conn->prepare("SELECT id FROM disciplinary_records WHERE id = ?");
        $check->execute([$id]);
        if (!$check->fetch()) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => "Offense record '{$id}' not found."]);
            return;
        }
        
        $query = "DELETE FROM disciplinary_records WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$id]);
        
        ob_clean();
        echo json_encode([
            'success' => true,
            'message' => 'Offense record deleted successfully'
        ]);
        
    } catch (Exception $e) {
        ob_clean();
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error deleting offense: ' . $e->getMessage()]);
    }
}
?>
