<?php
/**
 * Corrections API Endpoint
 * Handles CRUD operations for attendance corrections
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
            handleGetCorrections($conn);
            break;
        case 'POST':
            handleCreateCorrection($conn, $input);
            break;
        case 'PUT':
            handleUpdateCorrection($conn, $input);
            break;
        case 'DELETE':
            handleDeleteCorrection($conn);
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

function handleGetCorrections($conn) {
    try {
        // JOIN with employees to get employee details
        $query = "SELECT 
            c.*,
            e.firstname,
            e.middlename,
            e.surname,
            e.email,
            e.avatar,
            e.color,
            e.profile_photo
        FROM corrections c
        LEFT JOIN employees e ON c.employee_id = e.id
        ORDER BY c.created_at DESC";
        
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $corrections = $stmt->fetchAll();
        
        // Format the response
        $formattedCorrections = array_map(function($corr) {
            $employeeName = trim(($corr['firstname'] ?? '') . ' ' . 
                                 ($corr['middlename'] ? $corr['middlename'] . ' ' : '') . 
                                 ($corr['surname'] ?? ''));
            
            return [
                'id' => $corr['id'],
                'employeeId' => $corr['employee_id'],
                'employeeName' => $employeeName ?: 'Unknown Employee',
                'employeeEmail' => $corr['email'] ?? '',
                'avatar' => $corr['avatar'] ?? strtoupper(substr($employeeName, 0, 2)),
                'color' => $corr['color'] ?? 'linear-gradient(145deg, #4f46e5, #7c3aed)',
                'profilePhoto' => $corr['profile_photo'] ?? null,
                'type' => $corr['type'],
                'originalDate' => date('M d, Y', strtotime($corr['original_date'])),
                'originalDateRaw' => $corr['original_date'],
                'timeIn' => $corr['time_in'] ? substr($corr['time_in'], 0, 5) : null,
                'timeOut' => $corr['time_out'] ? substr($corr['time_out'], 0, 5) : null,
                'reason' => $corr['reason'],
                'status' => $corr['status'],
                'requestedBy' => $corr['requested_by'] ?? 'System',
                'requestedDate' => date('M d, Y', strtotime($corr['requested_date'])),
                'approvedBy' => $corr['approved_by'],
                'approvedDate' => $corr['approved_date'] ? date('M d, Y', strtotime($corr['approved_date'])) : null
            ];
        }, $corrections);
        
        echo json_encode(['success' => true, 'data' => $formattedCorrections]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error fetching corrections: ' . $e->getMessage()]);
    }
}

function handleCreateCorrection($conn, $input) {
    try {
        // Validate required fields
        $required = ['employeeId', 'type', 'originalDate', 'reason'];
        foreach ($required as $field) {
            if (empty($input[$field])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => "Missing required field: $field"]);
                return;
            }
        }
        
        // Verify that the employee exists in the database
        $empCheckQuery = "SELECT COUNT(*) FROM employees WHERE id = ?";
        $empCheckStmt = $conn->prepare($empCheckQuery);
        $empCheckStmt->execute([$input['employeeId']]);
        if ($empCheckStmt->fetchColumn() == 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'The selected employee does not exist in the database.']);
            return;
        }
        
        // Generate unique ID
        $id = 'COR-' . time();
        
        $query = "INSERT INTO corrections (
            id, employee_id, type, original_date, time_in, time_out,
            reason, status, requested_by
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($query);
        $stmt->execute([
            $id,
            $input['employeeId'],
            $input['type'],
            $input['originalDate'],
            $input['timeIn'] ?? null,
            $input['timeOut'] ?? null,
            $input['reason'],
            $input['status'] ?? 'Pending',
            $input['requestedBy'] ?? 'Current User'
        ]);
        
        echo json_encode(['success' => true, 'id' => $id, 'message' => 'Correction created successfully']);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error creating correction: ' . $e->getMessage()]);
    }
}

function handleUpdateCorrection($conn, $input) {
    try {
        if (empty($input['id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing correction ID']);
            return;
        }
        
        // Get old status to check if it changed
        $checkQuery = "SELECT status FROM corrections WHERE id = ?";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->execute([$input['id']]);
        $oldRecord = $checkStmt->fetch();
        $oldStatus = $oldRecord['status'] ?? '';
        $newStatus = $input['status'] ?? 'Pending';
        
        // Prepare update query
        $query = "UPDATE corrections SET 
                    type = ?, original_date = ?, time_in = ?, time_out = ?,
                    reason = ?, status = ?, updated_at = CURRENT_TIMESTAMP";
        
        $params = [
            $input['type'],
            $input['originalDate'],
            $input['timeIn'] ?? null,
            $input['timeOut'] ?? null,
            $input['reason'],
            $newStatus
        ];
        
        // If status changed to Approved or Rejected, set approval info
        if ($oldStatus !== $newStatus && ($newStatus === 'Approved' || $newStatus === 'Rejected')) {
            $query .= ", approved_by = ?, approved_date = CURRENT_TIMESTAMP";
            $params[] = $input['approvedBy'] ?? 'Current User';
        }
        
        $query .= " WHERE id = ?";
        $params[] = $input['id'];
        
        $stmt = $conn->prepare($query);
        $stmt->execute($params);
        
        echo json_encode(['success' => true, 'message' => 'Correction updated successfully']);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error updating correction: ' . $e->getMessage()]);
    }
}

function handleDeleteCorrection($conn) {
    try {
        $id = $_GET['id'] ?? '';
        if (empty($id)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing correction ID']);
            return;
        }
        
        $query = "DELETE FROM corrections WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$id]);
        
        echo json_encode(['success' => true, 'message' => 'Correction deleted successfully']);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error deleting correction: ' . $e->getMessage()]);
    }
}
?>
