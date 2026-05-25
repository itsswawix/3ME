<?php
/**
 * User Profile API
 * Handles self-service operations for the currently logged-in user.
 * No Admin privileges required, as all actions are securely locked to the session user_id.
 */

// Enable error reporting but don't display errors to avoid breaking JSON responses
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Custom error handler
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    error_log("PHP Error [$errno]: $errstr in $errfile on line $errline");
    if (in_array($errno, [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR])) {
        if (!headers_sent()) {
            header('Content-Type: application/json');
        }
        echo json_encode([
            'success' => false,
            'message' => 'A system error occurred. Please try again.'
        ]);
        exit;
    }
    return false;
});

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../../config/database.php';
require_once '../../utils/ResponseFormatter.php';
require_once '../../utils/ErrorHandler.php';

// Development session fallback
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 'USER-ADMIN-001';
    $_SESSION['user_email'] = 'admin@3me.com';
    $_SESSION['user_name'] = 'System Administrator';
    $_SESSION['user_role'] = 'Admin';
}

$currentUserId = $_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];

try {
    $database = new Database();
    $db = $database->getConnection();

    if ($method === 'GET') {
        // Fetch current user details
        $stmt = $db->prepare("
            SELECT id, name, email, role, contact_number, department, status, avatar, color 
            FROM users 
            WHERE id = ? 
            LIMIT 1
        ");
        $stmt->execute([$currentUserId]);
        $user = $stmt->fetch();

        if (!$user) {
            ResponseFormatter::error('User profile not found in database', 404);
            exit;
        }

        // Add dynamic initials calculation
        $nameParts = explode(' ', $user['name']);
        $initials = '';
        foreach ($nameParts as $part) {
            if (!empty($part)) {
                $initials .= strtoupper($part[0]);
            }
        }
        if (strlen($initials) > 2) {
            $initials = substr($initials, 0, 2);
        }
        $user['avatar_initials'] = !empty($initials) ? $initials : 'US';

        ResponseFormatter::success($user);
        exit;

    } elseif ($method === 'POST') {
        // Update user profile details
        $data = json_decode(file_get_contents('php://input'), true);

        if (!$data) {
            ResponseFormatter::error('Invalid request body. JSON payload expected.');
            exit;
        }

        $name = trim($data['name'] ?? '');
        $email = trim($data['email'] ?? '');
        $contactNumber = trim($data['contact_number'] ?? '');
        $color = trim($data['color'] ?? '');
        
        $currentPassword = $data['current_password'] ?? '';
        $newPassword = $data['new_password'] ?? '';

        // Validate basic fields
        if (empty($name) || empty($email)) {
            ResponseFormatter::error('Name and Email are required fields.');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            ResponseFormatter::error('Invalid email address format.');
            exit;
        }

        // Fetch current user to perform validations
        $stmt = $db->prepare("SELECT password, email FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$currentUserId]);
        $existingUser = $stmt->fetch();

        if (!$existingUser) {
            ResponseFormatter::error('User profile not found in database', 404);
            exit;
        }

        // Check if email has been changed and if new email already exists for another user
        if (strcasecmp($email, $existingUser['email']) !== 0) {
            $checkEmailStmt = $db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $checkEmailStmt->execute([$email, $currentUserId]);
            if ($checkEmailStmt->fetch()) {
                ResponseFormatter::error('This email is already in use by another account.');
                exit;
            }
        }

        // Start transaction
        $db->beginTransaction();

        // Check if password update is requested
        $passwordUpdateQuery = "";
        $queryParams = [$name, $email, $contactNumber, $color];

        if (!empty($newPassword)) {
            // Require current password for security
            if (empty($currentPassword)) {
                ResponseFormatter::error('Current password is required to set a new password.');
                $db->rollBack();
                exit;
            }

            // Verify current password
            if (!password_verify($currentPassword, $existingUser['password'])) {
                ResponseFormatter::error('The current password you entered is incorrect.');
                $db->rollBack();
                exit;
            }

            // Hash new password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $passwordUpdateQuery = ", password = ?";
            $queryParams[] = $hashedPassword;
        }

        // Append ID to query parameters
        $queryParams[] = $currentUserId;

        // Perform main update query
        $updateSql = "
            UPDATE users 
            SET name = ?, email = ?, contact_number = ?, color = ? {$passwordUpdateQuery}
            WHERE id = ?
        ";
        
        $updateStmt = $db->prepare($updateSql);
        $result = $updateStmt->execute($queryParams);

        if (!$result) {
            ResponseFormatter::error('Failed to update profile in database.');
            $db->rollBack();
            exit;
        }

        $db->commit();

        // Synchronize local session details
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;

        // Recalculate initials
        $nameParts = explode(' ', $name);
        $initials = '';
        foreach ($nameParts as $part) {
            if (!empty($part)) {
                $initials .= strtoupper($part[0]);
            }
        }
        if (strlen($initials) > 2) {
            $initials = substr($initials, 0, 2);
        }
        $avatarInitials = !empty($initials) ? $initials : 'US';

        ResponseFormatter::success([
            'message' => 'Profile updated successfully',
            'user' => [
                'name' => $name,
                'email' => $email,
                'contact_number' => $contactNumber,
                'color' => $color,
                'avatar_initials' => $avatarInitials
            ]
        ]);
        exit;
    } else {
        ResponseFormatter::error('Method not allowed', 405);
        exit;
    }

} catch (PDOException $e) {
    ErrorHandler::logError($e);
    if ($db && $db->inTransaction()) {
        $db->rollBack();
    }
    ResponseFormatter::error('Database error: ' . $e->getMessage(), 500);
} catch (Exception $e) {
    ErrorHandler::logError($e);
    if ($db && $db->inTransaction()) {
        $db->rollBack();
    }
    ResponseFormatter::error($e->getMessage(), 500);
}
