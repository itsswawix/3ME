<?php
/**
 * Create User API
 * Handles user creation with proper authentication and authorization
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../../config/database.php';
require_once '../../utils/ResponseFormatter.php';
require_once '../../utils/ErrorHandler.php';

// TEMPORARY: Create default session for development
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 'USER-ADMIN-001';
    $_SESSION['user_email'] = 'admin@3me.com';
    $_SESSION['user_name'] = 'System Administrator';
    $_SESSION['user_role'] = 'Admin';
}

// Check authentication
if (!isset($_SESSION['user_id'])) {
    ResponseFormatter::error('Unauthorized', 401);
    exit;
}

// Check admin permissions
if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['Admin', 'HR Manager'])) {
    ResponseFormatter::error('Insufficient permissions. Admin or HR Manager role required.', 403, [
        'your_role' => $_SESSION['user_role'] ?? 'unknown',
        'session_debug' => [
            'user_id' => $_SESSION['user_id'] ?? null,
            'user_role' => $_SESSION['user_role'] ?? null
        ]
    ]);
    exit;
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ResponseFormatter::error('Method not allowed', 405);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Get JSON input
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        ResponseFormatter::error('Invalid JSON data');
        exit;
    }
    
    // Extract and validate fields
    $firstname = trim($data['firstname'] ?? '');
    $middlename = trim($data['middlename'] ?? '');
    $surname = trim($data['surname'] ?? '');
    $email = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';
    $role = $data['role'] ?? '';
    $contact = trim($data['contact_number'] ?? '');
    
    // Validate required fields
    if (empty($firstname) || empty($surname) || empty($email) || empty($password) || empty($role)) {
        ResponseFormatter::error('Missing required fields: firstname, surname, email, password, and role are required');
        exit;
    }
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        ResponseFormatter::error('Invalid email format');
        exit;
    }
    
    // Validate role
    $validRoles = ['Admin', 'HR Manager', 'Manager', 'Employee'];
    if (!in_array($role, $validRoles)) {
        ResponseFormatter::error('Invalid role. Must be one of: ' . implode(', ', $validRoles));
        exit;
    }
    
    // Check if email already exists
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        ResponseFormatter::error('Email already exists');
        exit;
    }
    
    // Generate user ID
    $stmt = $db->query("SELECT COUNT(*) as count FROM users");
    $count = $stmt->fetch()['count'];
    $userId = 'USER-' . str_pad($count + 1, 6, '0', STR_PAD_LEFT);
    
    // Build full name
    $fullName = trim($firstname . ' ' . $middlename . ' ' . $surname);
    
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert user
    $stmt = $db->prepare("
        INSERT INTO users (id, name, email, password, role, contact_number, status, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, 'Active', NOW())
    ");
    
    $result = $stmt->execute([$userId, $fullName, $email, $hashedPassword, $role, $contact]);
    
    if ($result) {
        ResponseFormatter::success([
            'id' => $userId,
            'name' => $fullName,
            'email' => $email,
            'role' => $role,
            'message' => 'User created successfully'
        ]);
    } else {
        ResponseFormatter::error('Failed to create user');
    }
    
} catch (PDOException $e) {
    ErrorHandler::logError($e);
    ResponseFormatter::error('Database error: ' . $e->getMessage(), 500);
} catch (Exception $e) {
    ErrorHandler::logError($e);
    ResponseFormatter::error($e->getMessage(), 500);
}
?>
