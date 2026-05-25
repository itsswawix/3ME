<?php
/**
 * Exit Records API Endpoint
 * Handles CRUD operations for exit records
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
            handleGetExits($conn);
            break;
        case 'POST':
            handleCreateExit($conn, $input);
            break;
        case 'PUT':
            handleUpdateExit($conn, $input);
            break;
        case 'DELETE':
            handleDeleteExit($conn);
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

function handleGetExits($conn) {
    try {
        $query = "SELECT er.*, e.profile_photo as emp_profile_photo
                  FROM exit_records er
                  LEFT JOIN employees e ON er.employee_id = e.id
                  ORDER BY er.created_at DESC";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $exits = $stmt->fetchAll();
        
        // Add profile_photo to each record
        $exits = array_map(function($record) {
            $record['profile_photo'] = $record['emp_profile_photo'] ?? null;
            unset($record['emp_profile_photo']);
            return $record;
        }, $exits);
        
        echo json_encode(['success' => true, 'data' => $exits]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error fetching exits: ' . $e->getMessage()]);
    }
}

function handleCreateExit($conn, $input) {
    try {
        // Validate required fields
        $required = ['employee_id', 'employee_name', 'employee_email', 'job', 'department', 'company', 'last_working_day', 'reason'];
        foreach ($required as $field) {
            if (empty($input[$field])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => "Missing required field: $field"]);
                return;
            }
        }
        
        // Generate unique ID
        $id = 'EXIT-' . date('Y') . '-' . str_pad(rand(1, 9999), 3, '0', STR_PAD_LEFT);
        
        $query = "INSERT INTO exit_records (
            id, employee_id, employee_name, employee_email, position, department, company,
            last_working_day, reason, status, clearance_approved_by, resignation_letter, notes, avatar, color
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($query);
        $stmt->execute([
            $id,
            $input['employee_id'],
            $input['employee_name'],
            $input['employee_email'],
            $input['job'],
            $input['department'],
            $input['company'],
            $input['last_working_day'],
            $input['reason'],
            $input['status'] ?? 'Pending',
            $input['clearance_approved_by'] ?? null,
            $input['resignation_letter'] ?? null,
            $input['notes'] ?? '',
            $input['avatar'] ?? strtoupper(substr($input['employee_name'], 0, 2)),
            $input['color'] ?? 'linear-gradient(145deg, #ef4444, #f87171)'
        ]);
        
        echo json_encode(['success' => true, 'id' => $id, 'message' => 'Exit record created successfully']);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error creating exit: ' . $e->getMessage()]);
    }
}

function handleUpdateExit($conn, $input) {
    try {
        if (empty($input['id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing exit ID']);
            return;
        }
        
        $query = "UPDATE exit_records SET 
                    employee_name = ?, employee_email = ?, job = ?, department = ?, company = ?,
                    last_working_day = ?, reason = ?, status = ?, clearance_approved_by = ?,
                    resignation_letter = ?, notes = ?, updated_at = CURRENT_TIMESTAMP
                  WHERE id = ?";
        
        $stmt = $conn->prepare($query);
        $stmt->execute([
            $input['employee_name'],
            $input['employee_email'],
            $input['job'],
            $input['department'],
            $input['company'],
            $input['last_working_day'],
            $input['reason'],
            $input['status'],
            $input['clearance_approved_by'] ?? null,
            $input['resignation_letter'] ?? null,
            $input['notes'] ?? '',
            $input['id']
        ]);
        
        echo json_encode(['success' => true, 'message' => 'Exit record updated successfully']);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error updating exit: ' . $e->getMessage()]);
    }
}

function handleDeleteExit($conn) {
    try {
        $id = $_GET['id'] ?? '';
        if (empty($id)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing exit ID']);
            return;
        }
        
        $query = "DELETE FROM exit_records WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$id]);
        
        echo json_encode(['success' => true, 'message' => 'Exit record deleted successfully']);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error deleting exit: ' . $e->getMessage()]);
    }
}
?>