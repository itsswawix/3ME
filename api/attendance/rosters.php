<?php
/**
 * Rosters API Endpoint
 * Handles CRUD operations for shift rosters
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
            handleGetRosters($conn);
            break;
        case 'POST':
            handleCreateRoster($conn, $input);
            break;
        case 'PUT':
            handleUpdateRoster($conn, $input);
            break;
        case 'DELETE':
            handleDeleteRoster($conn);
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

function handleGetRosters($conn) {
    try {
        // JOIN with companies to get company name
        $query = "SELECT 
            r.*,
            c.name as company_name
        FROM rosters r
        LEFT JOIN companies c ON r.company_id = c.id
        ORDER BY r.created_at DESC";
        
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $rosters = $stmt->fetchAll();
        
        // Format the response
        $formattedRosters = array_map(function($roster) {
            return [
                'id' => $roster['id'],
                'shiftName' => $roster['shift_name'],
                'companyId' => $roster['company_id'],
                'company' => $roster['company_name'] ?? 'Unknown Company',
                'startTime' => substr($roster['start_time'], 0, 5), // HH:MM format
                'endTime' => substr($roster['end_time'], 0, 5),
                'breakDuration' => (int)$roster['break_duration'],
                'overtimeRule' => $roster['overtime_rule'],
                'lateGracePeriod' => (int)$roster['late_grace_period'],
                'effectiveDate' => date('M d, Y', strtotime($roster['effective_date'])),
                'effectiveDateRaw' => $roster['effective_date'],
                'notes' => $roster['notes'] ?? '',
                'createdBy' => $roster['created_by'] ?? 'System',
                'createdDate' => date('M d, Y', strtotime($roster['created_at']))
            ];
        }, $rosters);
        
        echo json_encode(['success' => true, 'data' => $formattedRosters]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error fetching rosters: ' . $e->getMessage()]);
    }
}

function handleCreateRoster($conn, $input) {
    try {
        // Validate required fields
        $required = ['shiftName', 'companyId', 'startTime', 'endTime', 'overtimeRule', 'effectiveDate'];
        foreach ($required as $field) {
            if (empty($input[$field])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => "Missing required field: $field"]);
                return;
            }
        }
        
        // Generate unique ID
        $id = 'RST-' . time();
        
        $query = "INSERT INTO rosters (
            id, shift_name, company_id, start_time, end_time, break_duration,
            overtime_rule, late_grace_period, effective_date, notes, created_by
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($query);
        $stmt->execute([
            $id,
            $input['shiftName'],
            $input['companyId'],
            $input['startTime'],
            $input['endTime'],
            $input['breakDuration'] ?? 0,
            $input['overtimeRule'],
            $input['lateGracePeriod'] ?? 0,
            $input['effectiveDate'],
            $input['notes'] ?? '',
            $input['createdBy'] ?? 'Current User'
        ]);
        
        echo json_encode(['success' => true, 'id' => $id, 'message' => 'Roster created successfully']);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error creating roster: ' . $e->getMessage()]);
    }
}

function handleUpdateRoster($conn, $input) {
    try {
        if (empty($input['id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing roster ID']);
            return;
        }
        
        $query = "UPDATE rosters SET 
                    shift_name = ?, company_id = ?, start_time = ?, end_time = ?,
                    break_duration = ?, overtime_rule = ?, late_grace_period = ?,
                    effective_date = ?, notes = ?, updated_at = CURRENT_TIMESTAMP
                  WHERE id = ?";
        
        $stmt = $conn->prepare($query);
        $stmt->execute([
            $input['shiftName'],
            $input['companyId'],
            $input['startTime'],
            $input['endTime'],
            $input['breakDuration'] ?? 0,
            $input['overtimeRule'],
            $input['lateGracePeriod'] ?? 0,
            $input['effectiveDate'],
            $input['notes'] ?? '',
            $input['id']
        ]);
        
        echo json_encode(['success' => true, 'message' => 'Roster updated successfully']);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error updating roster: ' . $e->getMessage()]);
    }
}

function handleDeleteRoster($conn) {
    try {
        $id = $_GET['id'] ?? '';
        if (empty($id)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing roster ID']);
            return;
        }
        
        $query = "DELETE FROM rosters WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$id]);
        
        echo json_encode(['success' => true, 'message' => 'Roster deleted successfully']);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error deleting roster: ' . $e->getMessage()]);
    }
}
?>
