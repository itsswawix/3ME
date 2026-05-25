<?php
/**
 * Delete Profile Photo API
 * Handles deletion of employee profile photos and clears the DB fields
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if (!in_array($_SERVER['REQUEST_METHOD'], ['POST', 'DELETE'])) {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed. Use POST or DELETE.']);
    exit();
}

require_once '../../config/database.php';

try {
    $input = file_get_contents('php://input');
    $data  = json_decode($input, true);

    if (!$data) {
        throw new Exception('Invalid JSON data');
    }

    if (!isset($data['employeeId']) && !isset($data['filename'])) {
        throw new Exception('Missing required field: employeeId or filename');
    }

    $uploadDir = __DIR__ . '/../../uploads/profile_photos/';

    // Delete by employee ID
    if (isset($data['employeeId'])) {
        $employeeId = $data['employeeId'];
        $pattern    = $uploadDir . 'profile_' . $employeeId . '_*.*';
        $files      = glob($pattern);

        $deletedCount = 0;
        foreach ($files as $file) {
            if (file_exists($file) && unlink($file)) {
                $deletedCount++;
            }
        }

        // Clear DB fields
        try {
            $database = new Database();
            $conn     = $database->getConnection();
            $stmt     = $conn->prepare(
                "UPDATE employees SET profile_photo = NULL, profile_photo_filename = NULL, updated_at = CURRENT_TIMESTAMP WHERE id = ?"
            );
            $stmt->execute([$employeeId]);
        } catch (Exception $dbEx) {
            error_log('DB update failed in delete_profile_photo: ' . $dbEx->getMessage());
        }

        echo json_encode([
            'success'      => true,
            'message'      => "Deleted {$deletedCount} profile photo(s)",
            'deletedCount' => $deletedCount
        ]);

    } else if (isset($data['filename'])) {
        $filename = basename($data['filename']);
        $filepath = $uploadDir . $filename;

        if (file_exists($filepath)) {
            unlink($filepath);
        }

        // Try to clear the DB row that references this filename
        try {
            $database = new Database();
            $conn     = $database->getConnection();
            $stmt     = $conn->prepare(
                "UPDATE employees SET profile_photo = NULL, profile_photo_filename = NULL, updated_at = CURRENT_TIMESTAMP WHERE profile_photo_filename = ?"
            );
            $stmt->execute([$filename]);
        } catch (Exception $dbEx) {
            error_log('DB update failed in delete_profile_photo: ' . $dbEx->getMessage());
        }

        echo json_encode([
            'success'  => true,
            'message'  => 'Profile photo deleted successfully',
            'filename' => $filename
        ]);
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
