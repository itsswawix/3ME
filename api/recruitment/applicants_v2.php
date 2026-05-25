<?php
/**
 * Applicants API v2 - Clean and Simple
 * Handles CRUD operations for job applicants
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../../config/database.php';

// Enable error logging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $method = $_SERVER['REQUEST_METHOD'];
    
    switch ($method) {
        case 'GET':
            handleGet($conn);
            break;
        case 'POST':
            handlePost($conn);
            break;
        case 'PUT':
            handlePut($conn);
            break;
        case 'DELETE':
            handleDelete($conn);
            break;
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    }
    
} catch (Exception $e) {
    error_log('Applicants API Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}

function handleGet($conn) {
    try {
        $query = "SELECT * FROM applicants ORDER BY created_at DESC";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $applicants = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'data' => $applicants,
            'count' => count($applicants)
        ]);
    } catch (Exception $e) {
        error_log('GET Error: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function handlePost($conn) {
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        
        error_log('POST Input: ' . json_encode($input));
        
        // Validate required fields
        $required = ['firstname', 'surname', 'email', 'position_id', 'position_title', 
                     'company', 'department', 'application_status', 'application_date'];
        
        foreach ($required as $field) {
            if (!isset($input[$field]) || $input[$field] === '') {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => "Missing required field: $field"]);
                return;
            }
        }
        
        // Generate ID
        $id = 'APP-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        // Generate avatar and color
        $avatar = strtoupper(substr($input['firstname'], 0, 1) . substr($input['surname'], 0, 1));
        $colors = [
            'linear-gradient(145deg, #6366f1, #a78bfa)',
            'linear-gradient(145deg, #10b981, #34d399)',
            'linear-gradient(145deg, #f59e0b, #fbbf24)',
            'linear-gradient(145deg, #ef4444, #f87171)',
            'linear-gradient(145deg, #8b5cf6, #a78bfa)',
            'linear-gradient(145deg, #06b6d4, #67e8f9)'
        ];
        $color = $colors[array_rand($colors)];
        
        // Insert query
        $query = "INSERT INTO applicants (
            id, requisition_id, position_id, position_title, 
            firstname, middlename, surname, email, 
            company, department, contact_number, 
            resume_filename, application_status, application_date, 
            interview_date, interview_type, interview_location, 
            notes, avatar, color
        ) VALUES (
            :id, :requisition_id, :position_id, :position_title,
            :firstname, :middlename, :surname, :email,
            :company, :department, :contact_number,
            :resume_filename, :application_status, :application_date,
            :interview_date, :interview_type, :interview_location,
            :notes, :avatar, :color
        )";
        
        $stmt = $conn->prepare($query);
        
        $params = [
            ':id' => $id,
            ':requisition_id' => $input['requisition_id'] ?? null,
            ':position_id' => $input['position_id'],
            ':position_title' => $input['position_title'],
            ':firstname' => $input['firstname'],
            ':middlename' => $input['middlename'] ?? '',
            ':surname' => $input['surname'],
            ':email' => $input['email'],
            ':company' => $input['company'],
            ':department' => $input['department'],
            ':contact_number' => $input['contact_number'] ?? '',
            ':resume_filename' => $input['resume_filename'] ?? 'Resume.pdf',
            ':application_status' => $input['application_status'],
            ':application_date' => $input['application_date'],
            ':interview_date' => $input['interview_date'] ?? null,
            ':interview_type' => $input['interview_type'] ?? null,
            ':interview_location' => $input['interview_location'] ?? null,
            ':notes' => $input['notes'] ?? '',
            ':avatar' => $avatar,
            ':color' => $color
        ];
        
        error_log('Executing INSERT with params: ' . json_encode($params));
        
        $result = $stmt->execute($params);
        
        if ($result) {
            error_log('Applicant created successfully: ' . $id);
            echo json_encode([
                'success' => true,
                'id' => $id,
                'message' => 'Applicant created successfully'
            ]);
        } else {
            $errorInfo = $stmt->errorInfo();
            error_log('Insert failed: ' . json_encode($errorInfo));
            throw new Exception('Failed to insert applicant: ' . $errorInfo[2]);
        }
        
    } catch (Exception $e) {
        error_log('POST Error: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function handlePut($conn) {
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (empty($input['id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing applicant ID']);
            return;
        }
        
        $query = "UPDATE applicants SET 
                    firstname = :firstname,
                    middlename = :middlename,
                    surname = :surname,
                    email = :email,
                    contact_number = :contact_number,
                    application_status = :application_status,
                    interview_date = :interview_date,
                    interview_type = :interview_type,
                    interview_location = :interview_location,
                    notes = :notes,
                    updated_at = CURRENT_TIMESTAMP
                  WHERE id = :id";
        
        $stmt = $conn->prepare($query);
        $stmt->execute([
            ':id' => $input['id'],
            ':firstname' => $input['firstname'],
            ':middlename' => $input['middlename'] ?? '',
            ':surname' => $input['surname'],
            ':email' => $input['email'],
            ':contact_number' => $input['contact_number'] ?? '',
            ':application_status' => $input['application_status'],
            ':interview_date' => $input['interview_date'] ?? null,
            ':interview_type' => $input['interview_type'] ?? null,
            ':interview_location' => $input['interview_location'] ?? null,
            ':notes' => $input['notes'] ?? ''
        ]);
        
        echo json_encode(['success' => true, 'message' => 'Applicant updated successfully']);
        
    } catch (Exception $e) {
        error_log('PUT Error: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function handleDelete($conn) {
    try {
        $id = $_GET['id'] ?? '';
        
        if (empty($id)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing applicant ID']);
            return;
        }
        
        $query = "DELETE FROM applicants WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->execute([':id' => $id]);
        
        echo json_encode(['success' => true, 'message' => 'Applicant deleted successfully']);
        
    } catch (Exception $e) {
        error_log('DELETE Error: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>
