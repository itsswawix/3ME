<?php
/**
 * File Upload API Endpoint
 * Handles attendance file uploads and stores them on the server
 * Accepts: CSV, XLSX, XLS, DAT files
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../../config/database.php';

// Configuration
$UPLOAD_DIR = __DIR__ . '/../../uploads/attendance/';
$ALLOWED_EXTENSIONS = ['csv', 'xlsx', 'xls', 'dat'];
$MAX_FILE_SIZE = 50 * 1024 * 1024; // 50MB

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $method = $_SERVER['REQUEST_METHOD'];
    
    switch ($method) {
        case 'POST':
            handleFileUpload($conn, $UPLOAD_DIR, $ALLOWED_EXTENSIONS, $MAX_FILE_SIZE);
            break;
        case 'GET':
            handleFileDownload($conn, $UPLOAD_DIR);
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

function handleFileUpload($conn, $uploadDir, $allowedExtensions, $maxFileSize) {
    // Ensure upload directory exists
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to create upload directory']);
            return;
        }
    }
    
    // Check if file was uploaded
    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize directive',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE directive',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the upload'
        ];
        
        $errorCode = $_FILES['file']['error'] ?? UPLOAD_ERR_NO_FILE;
        $errorMsg = $errorMessages[$errorCode] ?? 'Unknown upload error';
        
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => $errorMsg]);
        return;
    }
    
    $file = $_FILES['file'];
    $originalName = $file['name'];
    $fileSize = $file['size'];
    $tmpPath = $file['tmp_name'];
    
    // Validate file size
    if ($fileSize > $maxFileSize) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'File size exceeds maximum allowed size (50MB)']);
        return;
    }
    
    // Validate file extension
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    if (!in_array($extension, $allowedExtensions)) {
        http_response_code(400);
        echo json_encode([
            'success' => false, 
            'message' => 'Invalid file type. Allowed: ' . implode(', ', $allowedExtensions)
        ]);
        return;
    }
    
    // Generate unique filename
    $timestamp = date('Ymd_His');
    $uniqueId = substr(uniqid(), -6);
    $safeName = preg_replace('/[^a-zA-Z0-9_\-.]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
    $storedName = "{$timestamp}_{$uniqueId}_{$safeName}.{$extension}";
    $storedPath = $uploadDir . $storedName;
    
    // Move uploaded file
    if (!move_uploaded_file($tmpPath, $storedPath)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to save uploaded file']);
        return;
    }
    
    // Determine file type for import_history
    $fileType = 'CSV';
    if (in_array($extension, ['xlsx', 'xls'])) {
        $fileType = 'Excel';
    } elseif ($extension === 'dat') {
        $fileType = 'Biometric Export';
    }
    
    // Generate import ID
    $importId = 'IMP-' . date('Y') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
    
    // Get the imported_by from POST data or default
    $importedBy = $_POST['importedBy'] ?? 'Current User';
    
    // Store relative path for portability
    $relativePath = 'uploads/attendance/' . $storedName;
    
    // Insert import history record with file info
    try {
        $query = "INSERT INTO import_history (
            id, file_name, file_type, imported_by, total_records, successful, failed, status, file_path, file_size, original_name
        ) VALUES (?, ?, ?, ?, 0, 0, 0, 'Success', ?, ?, ?)";
        
        $stmt = $conn->prepare($query);
        $stmt->execute([
            $importId,
            $originalName,
            $fileType,
            $importedBy,
            $relativePath,
            $fileSize,
            $originalName
        ]);
    } catch (Exception $e) {
        // If the new columns don't exist yet, fall back to basic insert
        if (strpos($e->getMessage(), 'file_path') !== false || strpos($e->getMessage(), 'Unknown column') !== false) {
            $query = "INSERT INTO import_history (
                id, file_name, file_type, imported_by, total_records, successful, failed, status
            ) VALUES (?, ?, ?, ?, 0, 0, 0, 'Success')";
            
            $stmt = $conn->prepare($query);
            $stmt->execute([
                $importId,
                $originalName,
                $fileType,
                $importedBy
            ]);
        } else {
            // Clean up uploaded file on DB error
            @unlink($storedPath);
            throw $e;
        }
    }
    
    echo json_encode([
        'success' => true,
        'importId' => $importId,
        'fileName' => $originalName,
        'fileType' => $fileType,
        'filePath' => $relativePath,
        'fileSize' => $fileSize,
        'message' => 'File uploaded successfully'
    ]);
}

function handleFileDownload($conn, $uploadDir) {
    $importId = $_GET['import_id'] ?? '';
    
    if (empty($importId)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Missing import ID']);
        return;
    }
    
    try {
        $query = "SELECT file_path, original_name, file_name FROM import_history WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$importId]);
        $record = $stmt->fetch();
        
        if (!$record || empty($record['file_path'])) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'File not found']);
            return;
        }
        
        $fullPath = __DIR__ . '/../../' . $record['file_path'];
        
        if (!file_exists($fullPath)) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'File no longer exists on server']);
            return;
        }
        
        $downloadName = $record['original_name'] ?? $record['file_name'];
        
        // Send file for download
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $downloadName . '"');
        header('Content-Length: ' . filesize($fullPath));
        header('Cache-Control: no-cache');
        
        readfile($fullPath);
        exit;
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error retrieving file: ' . $e->getMessage()]);
    }
}
?>
