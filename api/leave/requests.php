<?php
/**
 * Leave Requests API Endpoint
 * Handles CRUD operations for leave requests & approvals
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS');
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
            handleGetLeaveRequests($conn);
            break;
        case 'POST':
            handleCreateLeaveRequest($conn, $input);
            break;
        case 'PUT':
            handleUpdateLeaveRequest($conn, $input);
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

function handleGetLeaveRequests($conn) {
    try {
        $id = $_GET['id'] ?? '';
        
        // Base query with employee and leave type details
        $query = "SELECT 
            r.*,
            e.firstname,
            e.middlename,
            e.surname,
            e.email,
            e.avatar,
            e.color,
            e.profile_photo,
            d.name as department,
            t.name as leave_type_name
        FROM leave_requests r
        LEFT JOIN employees e ON r.employee_id = e.id
        LEFT JOIN departments d ON e.department_id = d.id
        LEFT JOIN leave_types t ON r.leave_type_id = t.id";
        
        if (!empty($id)) {
            $query .= " WHERE r.id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$id]);
            $req = $stmt->fetch();
            
            if ($req) {
                $employeeName = trim(($req['firstname'] ?? '') . ' ' . 
                                     ($req['middlename'] ? $req['middlename'] . ' ' : '') . 
                                     ($req['surname'] ?? ''));
                                     
                echo json_encode([
                    'success' => true,
                    'data' => [
                        'id' => $req['id'],
                        'employeeId' => $req['employee_id'],
                        'employeeName' => $employeeName ?: 'Unknown Employee',
                        'employeeEmail' => $req['email'] ?? '',
                        'avatar' => $req['avatar'] ?? strtoupper(substr($employeeName, 0, 2)),
                        'color' => $req['color'] ?? 'linear-gradient(145deg, #4f46e5, #7c3aed)',
                        'profilePhoto' => $req['profile_photo'] ?? null,
                        'department' => $req['department'] ?? 'General',
                        'leaveTypeId' => $req['leave_type_id'],
                        'leaveType' => $req['leave_type_name'] ?? 'General Leave',
                        'startDate' => date('M d, Y', strtotime($req['start_date'])),
                        'endDate' => date('M d, Y', strtotime($req['end_date'])),
                        'startDateRaw' => $req['start_date'],
                        'endDateRaw' => $req['end_date'],
                        'duration' => (int)$req['duration'],
                        'reason' => $req['reason'],
                        'status' => $req['status'],
                        'approvedBy' => $req['approved_by'],
                        'approvedDate' => $req['approved_date'] ? date('M d, Y', strtotime($req['approved_date'])) : null,
                        'requestDate' => date('M d, Y', strtotime($req['created_at']))
                    ]
                ]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Leave request not found']);
            }
        } else {
            // Sort by creation date
            $query .= " ORDER BY r.created_at DESC";
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $requests = $stmt->fetchAll();
            
            $formatted = array_map(function($req) {
                $employeeName = trim(($req['firstname'] ?? '') . ' ' . 
                                     ($req['middlename'] ? $req['middlename'] . ' ' : '') . 
                                     ($req['surname'] ?? ''));
                
                return [
                    'id' => $req['id'],
                    'employeeId' => $req['employee_id'],
                    'employeeName' => $employeeName ?: 'Unknown Employee',
                    'employeeEmail' => $req['email'] ?? '',
                    'avatar' => $req['avatar'] ?? (strlen($employeeName) >= 2 ? strtoupper(substr($employeeName, 0, 2)) : 'EM'),
                    'color' => $req['color'] ?? 'linear-gradient(145deg, #4f46e5, #7c3aed)',
                    'profilePhoto' => $req['profile_photo'] ?? null,
                    'department' => $req['department'] ?? 'General',
                    'leaveTypeId' => $req['leave_type_id'],
                    'leaveType' => $req['leave_type_name'] ?? 'General Leave',
                    'startDate' => date('M d, Y', strtotime($req['start_date'])),
                    'endDate' => date('M d, Y', strtotime($req['end_date'])),
                    'startDateRaw' => $req['start_date'],
                    'endDateRaw' => $req['end_date'],
                    'duration' => (int)$req['duration'],
                    'reason' => $req['reason'],
                    'status' => $req['status'],
                    'approvedBy' => $req['approved_by'],
                    'approvedDate' => $req['approved_date'] ? date('M d, Y', strtotime($req['approved_date'])) : null,
                    'requestDate' => date('M d, Y', strtotime($req['created_at']))
                ];
            }, $requests);
            
            echo json_encode(['success' => true, 'data' => $formatted]);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error fetching requests: ' . $e->getMessage()]);
    }
}

function handleCreateLeaveRequest($conn, $input) {
    try {
        $required = ['employeeId', 'leaveTypeId', 'startDate', 'endDate', 'duration', 'reason'];
        foreach ($required as $field) {
            if (empty($input[$field])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => "Missing required field: $field"]);
                return;
            }
        }
        
        // Generate unique ID
        $id = 'LRQ-' . time() . '-' . rand(100, 999);
        
        $query = "INSERT INTO leave_requests (id, employee_id, leave_type_id, start_date, end_date, duration, reason, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')";
        $stmt = $conn->prepare($query);
        $stmt->execute([
            $id,
            $input['employeeId'],
            $input['leaveTypeId'],
            $input['startDate'],
            $input['endDate'],
            (int)$input['duration'],
            trim($input['reason'])
        ]);
        
        // Dynamic balance recalculation
        recountUsedLeaves($conn, $input['employeeId'], $input['leaveTypeId']);
        
        echo json_encode([
            'success' => true,
            'id' => $id,
            'message' => 'Leave request submitted successfully'
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error creating leave request: ' . $e->getMessage()]);
    }
}

function handleUpdateLeaveRequest($conn, $input) {
    try {
        if (empty($input['id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing leave request ID']);
            return;
        }
        
        // Get old record to identify changes in status, employee, or type
        $checkQuery = "SELECT employee_id, leave_type_id, status FROM leave_requests WHERE id = ?";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->execute([$input['id']]);
        $oldRecord = $checkStmt->fetch();
        
        if (!$oldRecord) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Leave request not found']);
            return;
        }
        
        $employeeId = $oldRecord['employee_id'];
        $leaveTypeId = $oldRecord['leave_type_id'];
        $oldStatus = $oldRecord['status'];
        $newStatus = $input['status'] ?? $oldStatus;
        
        // Build the update query dynamically
        $query = "UPDATE leave_requests SET 
                    leave_type_id = ?, 
                    start_date = ?, 
                    end_date = ?, 
                    duration = ?, 
                    reason = ?, 
                    status = ?";
                    
        $params = [
            $input['leaveTypeId'] ?? $leaveTypeId,
            $input['startDate'] ?? null,
            $input['endDate'] ?? null,
            (int)($input['duration'] ?? 1),
            $input['reason'] ?? '',
            $newStatus
        ];
        
        // If status changed to Approved/Rejected, log approved_by & approved_date
        if ($oldStatus !== $newStatus && ($newStatus === 'Approved' || $newStatus === 'Rejected')) {
            $query .= ", approved_by = ?, approved_date = CURRENT_TIMESTAMP";
            $params[] = $input['approvedBy'] ?? 'Monica White';
        } else if ($newStatus === 'Pending') {
            $query .= ", approved_by = NULL, approved_date = NULL";
        } else if (isset($input['approvedBy'])) {
            $query .= ", approved_by = ?";
            $params[] = $input['approvedBy'];
        }
        
        $query .= " WHERE id = ?";
        $params[] = $input['id'];
        
        $stmt = $conn->prepare($query);
        $stmt->execute($params);
        
        // Re-calculate both old employee/type (if changed) and new employee/type
        recountUsedLeaves($conn, $employeeId, $leaveTypeId);
        if (($input['leaveTypeId'] ?? $leaveTypeId) !== $leaveTypeId) {
            recountUsedLeaves($conn, $employeeId, $input['leaveTypeId']);
        }
        
        echo json_encode(['success' => true, 'message' => 'Leave request updated successfully']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error updating leave request: ' . $e->getMessage()]);
    }
}

function recountUsedLeaves($conn, $employeeId, $leaveTypeId) {
    // 1. Get sum of approved leaves
    $sumQuery = "SELECT SUM(duration) FROM leave_requests WHERE employee_id = ? AND leave_type_id = ? AND status = 'Approved'";
    $sumStmt = $conn->prepare($sumQuery);
    $sumStmt->execute([$employeeId, $leaveTypeId]);
    $usedSum = (float)($sumStmt->fetchColumn() ?: 0.0);
    
    // 2. Ensure balance record exists
    $checkQuery = "SELECT id, accrued FROM leave_balances WHERE employee_id = ? AND leave_type_id = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->execute([$employeeId, $leaveTypeId]);
    $balanceRecord = $checkStmt->fetch();
    
    if ($balanceRecord) {
        $accrued = (float)$balanceRecord['accrued'];
        $newBalance = $accrued - $usedSum;
        
        $updateQuery = "UPDATE leave_balances SET used = ?, balance = ? WHERE id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->execute([$usedSum, $newBalance, $balanceRecord['id']]);
    } else {
        // If no balance record, we fetch default credits from leave_types and create it
        $typeQuery = "SELECT credits FROM leave_types WHERE id = ?";
        $typeStmt = $conn->prepare($typeQuery);
        $typeStmt->execute([$leaveTypeId]);
        $defaultCredits = (float)($typeStmt->fetchColumn() ?: 15.0);
        
        $balId = 'BAL-' . str_replace('.', '', uniqid('', true));
        $newBalance = $defaultCredits - $usedSum;
        
        $insertQuery = "INSERT INTO leave_balances (id, employee_id, leave_type_id, accrued, used, balance) VALUES (?, ?, ?, ?, ?, ?)";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->execute([$balId, $employeeId, $leaveTypeId, $defaultCredits, $usedSum, $newBalance]);
    }
}
?>
