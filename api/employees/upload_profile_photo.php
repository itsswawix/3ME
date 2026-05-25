<?php
/**
 * Upload Profile Photo API
 * Handles profile photo uploads for employees and persists URL to DB
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed. Use POST.']);
    exit();
}

require_once '../../config/database.php';

try {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!$data) {
        throw new Exception('Invalid JSON data');
    }

    if (!isset($data['photo']) || !isset($data['employeeId'])) {
        throw new Exception('Missing required fields: photo and employeeId');
    }

    $photoData   = $data['photo'];
    $employeeId  = $data['employeeId'];
    $filename    = $data['filename'] ?? null;

    // Validate base64 image data
    if (!preg_match('/^data:image\/(jpeg|jpg|png|gif|webp);base64,/', $photoData, $matches)) {
        throw new Exception('Invalid image format. Only JPEG, PNG, GIF and WebP are allowed.');
    }

    $imageType = $matches[1];
    if ($imageType === 'jpg') $imageType = 'jpeg';

    // Decode base64
    $rawPhoto = preg_replace('/^data:image\/\w+;base64,/', '', $photoData);
    $rawPhoto = base64_decode($rawPhoto);
    if ($rawPhoto === false) {
        throw new Exception('Failed to decode base64 image data');
    }

    // Ensure upload directory exists
    $uploadDir = __DIR__ . '/../../uploads/profile_photos/';
    if (!file_exists($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            throw new Exception('Failed to create upload directory');
        }
    }

    // Build filename
    if (!$filename) {
        $filename = 'profile_' . $employeeId . '_' . time() . '.' . $imageType;
    }
    $filepath = $uploadDir . $filename;

    // Delete any previous photo for this employee
    $oldFiles = glob($uploadDir . 'profile_' . $employeeId . '_*.*');
    foreach ($oldFiles as $oldFile) {
        if (file_exists($oldFile) && $oldFile !== $filepath) {
            @unlink($oldFile);
        }
    }

    // Save image file
    if (file_put_contents($filepath, $rawPhoto) === false) {
        throw new Exception('Failed to save image file');
    }
    if (!file_exists($filepath)) {
        throw new Exception('Image file was not saved');
    }

    $fileSize = filesize($filepath);
    $photoUrl = '/3ME/uploads/profile_photos/' . $filename;

    // -------------------------------------------------------
    // Persist photo URL in the employees table
    // -------------------------------------------------------
    try {
        $database = new Database();
        $conn     = $database->getConnection();

        // Ensure the columns exist (idempotent)
        $conn->exec("ALTER TABLE employees
            ADD COLUMN IF NOT EXISTS profile_photo VARCHAR(500) DEFAULT NULL,
            ADD COLUMN IF NOT EXISTS profile_photo_filename VARCHAR(255) DEFAULT NULL");

        $stmt = $conn->prepare(
            "UPDATE employees SET profile_photo = ?, profile_photo_filename = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?"
        );
        $stmt->execute([$photoUrl, $filename, $employeeId]);

        error_log("Profile photo saved to DB for employee: $employeeId -> $photoUrl");
    } catch (Exception $dbEx) {
        // Non-fatal: file was saved, just log the DB error
        error_log('DB update failed in upload_profile_photo: ' . $dbEx->getMessage());
    }

    echo json_encode([
        'success' => true,
        'message' => 'Profile photo uploaded successfully',
        'data'    => [
            'filename' => $filename,
            'url'      => $photoUrl,
            'size'     => $fileSize,
            'type'     => $imageType
        ]
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
