<?php
/**
 * Leave Balances API Endpoint
 * Handles querying and self-initializing employee leave balances
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
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
    
    if ($method === 'GET') {
        handleGetLeaveBalances($conn);
    } else {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}

function handleGetLeaveBalances($conn) {
    try {
        // Step 1: Automatically self-initialize leave balances for all employees and all leave types
        autoInitializeLeaveBalances($conn);
        
        // Step 2: Query all leave balances joined with employee and department details
        $query = "SELECT 
            b.*,
            e.firstname,
            e.middlename,
            e.surname,
            e.avatar,
            e.color,
            e.profile_photo,
            d.name as department_name,
            t.name as leave_type_name
        FROM leave_balances b
        INNER JOIN employees e ON b.employee_id = e.id
        LEFT JOIN departments d ON e.department_id = d.id
        INNER JOIN leave_types t ON b.leave_type_id = t.id
        ORDER BY e.surname ASC, e.firstname ASC, t.name ASC";
        
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $balances = $stmt->fetchAll();
        
        $formatted = array_map(function($bal) {
            $employeeName = trim(($bal['firstname'] ?? '') . ' ' . 
                                 ($bal['middlename'] ? $bal['middlename'] . ' ' : '') . 
                                 ($bal['surname'] ?? ''));
                                 
            return [
                'id' => $bal['id'],
                'employeeId' => $bal['employee_id'],
                'employeeName' => $employeeName ?: 'Unknown Employee',
                'department' => $bal['department_name'] ?? 'General',
                'avatar' => $bal['avatar'] ?? (strlen($employeeName) >= 2 ? strtoupper(substr($employeeName, 0, 2)) : 'EM'),
                'color' => $bal['color'] ?? 'linear-gradient(145deg, #4f46e5, #7c3aed)',
                'profilePhoto' => $bal['profile_photo'] ?? null,
                'leaveTypeId' => $bal['leave_type_id'],
                'leaveType' => $bal['leave_type_name'] ?? 'General Leave',
                'accrued' => (float)$bal['accrued'],
                'used' => (float)$bal['used'],
                'balance' => (float)$bal['balance'],
                'lastAccrual' => $bal['last_accrual_date'] ? date('M d, Y', strtotime($bal['last_accrual_date'])) : date('M d, Y', strtotime($bal['created_at']))
            ];
        }, $balances);
        
        echo json_encode(['success' => true, 'data' => $formatted]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error fetching leave balances: ' . $e->getMessage()]);
    }
}

function autoInitializeLeaveBalances($conn) {
    try {
        // 1. Get all employees
        $empStmt = $conn->query("SELECT id FROM employees");
        $employees = $empStmt->fetchAll(PDO::FETCH_COLUMN);
        
        // 2. Get all leave types
        $typeStmt = $conn->query("SELECT id, credits FROM leave_types");
        $leaveTypes = $typeStmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($employees) || empty($leaveTypes)) {
            return;
        }
        
        // 3. For each combination, ensure a balance row exists
        $conn->beginTransaction();
        
        // Get existing combinations to check quickly
        $existStmt = $conn->query("SELECT CONCAT(employee_id, '_', leave_type_id) FROM leave_balances");
        $existing = $existStmt->fetchAll(PDO::FETCH_COLUMN);
        $existingSet = array_flip($existing);
        
        $insertQuery = "INSERT INTO leave_balances (id, employee_id, leave_type_id, accrued, used, balance, last_accrual_date) VALUES (?, ?, ?, ?, 0.00, ?, CURRENT_DATE)";
        $insertStmt = $conn->prepare($insertQuery);
        
        foreach ($employees as $empId) {
            foreach ($leaveTypes as $type) {
                $typeId = $type['id'];
                $credits = (float)$type['credits'];
                $key = $empId . '_' . $typeId;
                
                if (!isset($existingSet[$key])) {
                    $id = 'BAL-' . str_replace('.', '', uniqid('', true));
                    $insertStmt->execute([
                        $id,
                        $empId,
                        $typeId,
                        $credits,
                        $credits
                    ]);
                }
            }
        }
        
        $conn->commit();
    } catch (Exception $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        // Log error, but don't fail completely
        error_log("Error in autoInitializeLeaveBalances: " . $e->getMessage());
    }
}
?>
