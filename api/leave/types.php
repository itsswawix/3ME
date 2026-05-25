<?php
/**
 * Leave Types API Endpoint
 * Handles CRUD operations for leave types & policies
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
            handleGetLeaveTypes($conn);
            break;
        case 'POST':
            handleCreateLeaveType($conn, $input);
            break;
        case 'PUT':
            handleUpdateLeaveType($conn, $input);
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

function handleGetLeaveTypes($conn) {
    try {
        $id = $_GET['id'] ?? '';
        if (!empty($id)) {
            $query = "SELECT * FROM leave_types WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$id]);
            $type = $stmt->fetch();
            if ($type) {
                echo json_encode([
                    'success' => true,
                    'data' => [
                        'id' => $type['id'],
                        'name' => $type['name'],
                        'credits' => (float)$type['credits'],
                        'maxDuration' => (int)$type['max_duration'],
                        'eligibilityRule' => $type['eligibility_rule'] ?? ''
                    ]
                ]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Leave type not found']);
            }
        } else {
            $query = "SELECT * FROM leave_types ORDER BY name ASC";
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $types = $stmt->fetchAll();
            
            $formatted = array_map(function($t) {
                return [
                    'id' => $t['id'],
                    'name' => $t['name'],
                    'credits' => (float)$t['credits'],
                    'maxDuration' => (int)$t['max_duration'],
                    'eligibilityRule' => $t['eligibility_rule'] ?? ''
                ];
            }, $types);
            
            echo json_encode(['success' => true, 'data' => $formatted]);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error fetching leave types: ' . $e->getMessage()]);
    }
}

function handleCreateLeaveType($conn, $input) {
    try {
        if (empty($input['name']) || !isset($input['credits'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing required fields: name and credits are required']);
            return;
        }
        
        // Generate unique ID
        $id = 'LVT-' . str_replace('.', '', uniqid('', true));
        
        $query = "INSERT INTO leave_types (id, name, credits, max_duration, eligibility_rule) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->execute([
            $id,
            trim($input['name']),
            (float)$input['credits'],
            (int)($input['maxDuration'] ?? 30),
            $input['eligibilityRule'] ?? null
        ]);
        
        echo json_encode([
            'success' => true,
            'id' => $id,
            'message' => 'Leave type created successfully'
        ]);
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { // Unique violation
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'A leave type with this name already exists.']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error creating leave type: ' . $e->getMessage()]);
    }
}

function handleUpdateLeaveType($conn, $input) {
    try {
        if (empty($input['id']) || empty($input['name']) || !isset($input['credits'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            return;
        }
        
        $query = "UPDATE leave_types SET name = ?, credits = ?, max_duration = ?, eligibility_rule = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([
            trim($input['name']),
            (float)$input['credits'],
            (int)($input['maxDuration'] ?? 30),
            $input['eligibilityRule'] ?? null,
            $input['id']
        ]);
        
        echo json_encode(['success' => true, 'message' => 'Leave type updated successfully']);
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { // Unique violation
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'A leave type with this name already exists.']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error updating leave type: ' . $e->getMessage()]);
    }
}
?>
