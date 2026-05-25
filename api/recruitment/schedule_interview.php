<?php
/**
 * Schedule Interview API
 * Updates the interview details and application status for an applicant
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../../config/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $input = json_decode(file_get_contents('php://input'), true);
    if (empty($input['id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Missing applicant ID']);
        return;
    }

    $query = "UPDATE applicants SET 
                application_status = :application_status,
                interview_date = :interview_date,
                interview_type = :interview_type,
                interview_location = :interview_location,
                updated_at = CURRENT_TIMESTAMP
              WHERE id = :id";
    
    $stmt = $conn->prepare($query);
    $stmt->execute([
        ':id' => $input['id'],
        ':application_status' => $input['application_status'],
        ':interview_date' => $input['interview_date'],
        ':interview_type' => $input['interview_type'],
        ':interview_location' => $input['interview_location']
    ]);
    
    echo json_encode(['success' => true, 'message' => 'Interview scheduled successfully']);
} catch (Exception $e) {
    error_log('Schedule Interview Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
