<?php
/**
 * Applicants API - Clean Version
 * Handles CRUD operations for job applicants
 * Uses job_id, company_id, department_id foreign keys
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
        // Ensure profile_photo column exists on applicants
        try {
            $conn->exec("ALTER TABLE applicants ADD COLUMN IF NOT EXISTS profile_photo VARCHAR(500) DEFAULT NULL");
        } catch (Exception $ex) { /* column may already exist */ }

        // Use a subquery to get profile_photo from the employees table
        // Matches by email OR firstname+surname (same approach as onboarding)
        // This avoids duplicate rows from multiple employee matches
        $query = "SELECT a.*,
                         j.title AS job, j.level AS job_level,
                         d.name AS department_name,
                         c.name AS company_name,
                         (SELECT e.profile_photo FROM employees e 
                          WHERE (e.email = a.email OR (e.firstname = a.firstname AND e.surname = a.surname))
                          AND e.profile_photo IS NOT NULL AND e.profile_photo != ''
                          LIMIT 1) AS emp_profile_photo
                  FROM applicants a
                  LEFT JOIN jobs j ON a.job_id = j.id
                  LEFT JOIN departments d ON a.department_id = d.id
                  LEFT JOIN companies c ON a.company_id = c.id
                  ORDER BY a.created_at DESC";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $applicants = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Prefer employee photo, fall back to applicant's own photo
        foreach ($applicants as &$applicant) {
            $empPhoto = $applicant['emp_profile_photo'] ?? null;
            $ownPhoto = $applicant['profile_photo'] ?? null;
            $applicant['profile_photo'] = (!empty($empPhoto)) ? $empPhoto : ((!empty($ownPhoto)) ? $ownPhoto : null);
            unset($applicant['emp_profile_photo']);
        }
        unset($applicant);
        
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
        $required = ['firstname', 'surname', 'email', 'job_id', 'company_id', 
                     'department_id', 'application_status', 'application_date'];
        
        foreach ($required as $field) {
            if (!isset($input[$field]) || $input[$field] === '') {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => "Missing required field: $field"]);
                return;
            }
        }
        
        // Check vacancy availability for the selected job
        $jobId = $input['job_id'];
        $vacancyStmt = $conn->prepare("SELECT vacancies FROM jobs WHERE id = ?");
        $vacancyStmt->execute([$jobId]);
        $jobRow = $vacancyStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$jobRow) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Job not found.']);
            return;
        }
        
        $totalVacancies = (int)($jobRow['vacancies'] ?? 0);
        
        // Count active employees for this job (authoritative source - matches Employee Management)
        $empStmt = $conn->prepare("SELECT COUNT(*) as cnt FROM employees WHERE job_id = ? AND employment_status = 'Active'");
        $empStmt->execute([$jobId]);
        $employedCount = (int)($empStmt->fetch(PDO::FETCH_ASSOC)['cnt'] ?? 0);
        
        $availableVacancies = max(0, $totalVacancies - $employedCount);
        
        if ($availableVacancies <= 0) {
            http_response_code(400);
            echo json_encode([
                'success' => false, 
                'message' => "No available vacancies for this job position. All {$totalVacancies} slot(s) are filled.",
                'availableVacancies' => 0,
                'totalVacancies' => $totalVacancies
            ]);
            return;
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
        
        // Insert query with foreign key IDs
        $query = "INSERT INTO applicants (
            id, requisition_id, job_id, company_id, department_id,
            firstname, middlename, surname, suffix, email,
            contact_number, resume_filename, 
            application_status, application_date,
            interview_date, interview_type, interview_location,
            notes, avatar, color, profile_photo
        ) VALUES (
            :id, :requisition_id, :job_id, :company_id, :department_id,
            :firstname, :middlename, :surname, :suffix, :email,
            :contact_number, :resume_filename,
            :application_status, :application_date,
            :interview_date, :interview_type, :interview_location,
            :notes, :avatar, :color, :profile_photo
        )";
        
        $stmt = $conn->prepare($query);
        
        $params = [
            ':id' => $id,
            ':requisition_id' => $input['requisition_id'] ?? null,
            ':job_id' => $input['job_id'],
            ':company_id' => $input['company_id'],
            ':department_id' => $input['department_id'],
            ':firstname' => $input['firstname'],
            ':middlename' => $input['middlename'] ?? '',
            ':surname' => $input['surname'],
            ':suffix' => $input['suffix'] ?? '',
            ':email' => $input['email'],
            ':contact_number' => $input['contact_number'] ?? '',
            ':resume_filename' => $input['resume_filename'] ?? 'Resume.pdf',
            ':application_status' => $input['application_status'],
            ':application_date' => $input['application_date'],
            ':interview_date' => $input['interview_date'] ?? null,
            ':interview_type' => $input['interview_type'] ?? null,
            ':interview_location' => $input['interview_location'] ?? null,
            ':notes' => $input['notes'] ?? '',
            ':avatar' => $avatar,
            ':color' => $color,
            ':profile_photo' => $input['profile_photo'] ?? null
        ];
        
        error_log('Executing INSERT with params: ' . json_encode($params));
        
        $result = $stmt->execute($params);
        
        if ($result) {
            error_log('Applicant created successfully: ' . $id);
            echo json_encode([
                'success' => true,
                'id' => $id,
                'message' => 'Applicant created successfully',
                'availableVacancies' => $availableVacancies - 1
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
                    suffix = :suffix,
                    email = :email,
                    contact_number = :contact_number,
                    application_status = :application_status,
                    interview_date = :interview_date,
                    interview_type = :interview_type,
                    interview_location = :interview_location,
                    notes = :notes,
                    profile_photo = :profile_photo,
                    updated_at = CURRENT_TIMESTAMP
                  WHERE id = :id";
        
        $stmt = $conn->prepare($query);
        $stmt->execute([
            ':id' => $input['id'],
            ':firstname' => $input['firstname'],
            ':middlename' => $input['middlename'] ?? '',
            ':surname' => $input['surname'],
            ':suffix' => $input['suffix'] ?? '',
            ':email' => $input['email'],
            ':contact_number' => $input['contact_number'] ?? '',
            ':application_status' => $input['application_status'],
            ':interview_date' => $input['interview_date'] ?? null,
            ':interview_type' => $input['interview_type'] ?? null,
            ':interview_location' => $input['interview_location'] ?? null,
            ':notes' => $input['notes'] ?? '',
            ':profile_photo' => $input['profile_photo'] ?? null
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
