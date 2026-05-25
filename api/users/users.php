<?php
/**
 * Unified Users API Endpoint
 * Handles CRUD operations for users
 */

// Prevent any output before JSON response
ob_start();

// Enable error reporting but don't display errors
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Custom error handler
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    error_log("PHP Error [$errno]: $errstr in $errfile on line $errline");
    if (in_array($errno, [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR])) {
        if (ob_get_level() > 0) ob_clean();
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'A system error occurred: ' . $errstr
        ]);
        exit;
    }
    return false;
});

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Check authentication
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized. Please log in first.']);
    exit;
}

// Check admin permissions - Only Admin or HR Officer can manage users
if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['Admin', 'HR Officer'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Insufficient permissions. Admin or HR Officer role required.']);
    exit;
}

require_once '../../config/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $method = $_SERVER['REQUEST_METHOD'];
    $input = json_decode(file_get_contents('php://input'), true);
    
    switch ($method) {
        case 'GET':
            handleGetUsers($conn);
            break;
        case 'POST':
            handleCreateUser($conn, $input);
            break;
        case 'PUT':
            handleUpdateUser($conn, $input);
            break;
        case 'DELETE':
            handleDeleteUser($conn);
            break;
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
    }
    
} catch (Exception $e) {
    if (ob_get_level() > 0) ob_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}

function handleGetUsers($conn) {
    try {
        // Double check users table exists
        $checkTable = $conn->query("SHOW TABLES LIKE 'users'");
        if ($checkTable->rowCount() === 0) {
            if (ob_get_level() > 0) ob_clean();
            echo json_encode(['success' => true, 'data' => [], 'message' => 'Users table not found']);
            return;
        }
        
        $query = "SELECT id, name, email, role, contact_number, department, status, avatar, color, created_at 
                  FROM users 
                  ORDER BY created_at DESC";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $users = $stmt->fetchAll();
        
        // Calculate initials for avatar display if needed
        $formattedUsers = array_map(function($user) {
            $initials = '';
            if (!empty($user['name'])) {
                $parts = explode(' ', $user['name']);
                foreach ($parts as $p) {
                    if (!empty($p)) $initials .= strtoupper($p[0]);
                }
                if (strlen($initials) > 2) $initials = substr($initials, 0, 2);
            }
            if (empty($initials)) $initials = 'US';
            
            return [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role'],
                'contact_number' => $user['contact_number'] ?? '',
                'department' => $user['department'] ?? '',
                'status' => $user['status'] ?? 'Active',
                'avatar' => $user['avatar'] ? $user['avatar'] : $initials,
                'color' => $user['color'] ? $user['color'] : 'linear-gradient(145deg, #4f46e5, #7c3aed)',
                'created_at' => date('M d, Y', strtotime($user['created_at']))
            ];
        }, $users);
        
        if (ob_get_level() > 0) ob_clean();
        echo json_encode(['success' => true, 'data' => $formattedUsers]);
    } catch (Exception $e) {
        if (ob_get_level() > 0) ob_clean();
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error fetching users: ' . $e->getMessage()]);
    }
}

function handleCreateUser($conn, $input) {
    try {
        $firstname = trim($input['firstname'] ?? '');
        $surname = trim($input['surname'] ?? '');
        $email = trim($input['email'] ?? '');
        $password = $input['password'] ?? '';
        $role = trim($input['role'] ?? '');
        $department = trim($input['department'] ?? '');
        $contact = trim($input['contact_number'] ?? '');
        
        if (empty($firstname) || empty($surname) || empty($email) || empty($password) || empty($role)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing required fields. First name, surname, email, password, and role are required.']);
            return;
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
            return;
        }
        
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Email address is already registered.']);
            return;
        }
        
        // Generate User ID (USER-000001, etc.)
        $stmt = $conn->query("SELECT id FROM users ORDER BY created_at DESC LIMIT 1");
        $lastUser = $stmt->fetch();
        $nextNum = 1;
        if ($lastUser && preg_match('/USER-(\d+)/', $lastUser['id'], $matches)) {
            $nextNum = intval($matches[1]) + 1;
        }
        $userId = 'USER-' . str_pad($nextNum, 6, '0', STR_PAD_LEFT);
        
        // Compile full name
        $name = trim($firstname . ' ' . $surname);
        
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Set initials & dynamic color gradient
        $avatar = strtoupper(substr($firstname, 0, 1) . substr($surname, 0, 1));
        $colors = [
            'linear-gradient(145deg, #4f46e5, #7c3aed)', // indigo/violet
            'linear-gradient(145deg, #10b981, #34d399)', // emerald
            'linear-gradient(145deg, #f59e0b, #fbbf24)', // amber
            'linear-gradient(145deg, #ef4444, #f87171)', // red
            'linear-gradient(145deg, #3b82f6, #60a5fa)', // blue
            'linear-gradient(145deg, #ec4899, #f472b6)', // pink
            'linear-gradient(145deg, #8b5cf6, #a78bfa)', // purple
            'linear-gradient(145deg, #06b6d4, #67e8f9)'  // cyan
        ];
        $color = $colors[array_rand($colors)];
        
        // Insert user
        $query = "INSERT INTO users (id, name, email, password, role, contact_number, department, status, avatar, color, created_at)
                  VALUES (?, ?, ?, ?, ?, ?, ?, 'Active', ?, ?, CURRENT_TIMESTAMP)";
        $stmt = $conn->prepare($query);
        $stmt->execute([
            $userId,
            $name,
            $email,
            $hashedPassword,
            $role,
            $contact,
            $department,
            $avatar,
            $color
        ]);
        
        if (ob_get_level() > 0) ob_clean();
        echo json_encode([
            'success' => true,
            'id' => $userId,
            'message' => 'User created successfully!'
        ]);
        
    } catch (Exception $e) {
        if (ob_get_level() > 0) ob_clean();
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error creating user: ' . $e->getMessage()]);
    }
}

function handleUpdateUser($conn, $input) {
    try {
        $id = trim($input['id'] ?? '');
        $firstname = trim($input['firstname'] ?? '');
        $surname = trim($input['surname'] ?? '');
        $email = trim($input['email'] ?? '');
        $role = trim($input['role'] ?? '');
        $department = trim($input['department'] ?? '');
        $contact = trim($input['contact_number'] ?? '');
        $status = trim($input['status'] ?? 'Active');
        $password = $input['password'] ?? '';
        
        if (empty($id) || empty($firstname) || empty($surname) || empty($email) || empty($role)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing required fields. First name, surname, email, and role are required.']);
            return;
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
            return;
        }
        
        // Check if email already exists for another user
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $id]);
        if ($stmt->fetch()) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Email address is already in use by another user.']);
            return;
        }
        
        $name = trim($firstname . ' ' . $surname);
        $avatar = strtoupper(substr($firstname, 0, 1) . substr($surname, 0, 1));
        
        // Prevent disabling or changing role of self to avoid lockout
        if ($id === $_SESSION['user_id']) {
            if ($status !== 'Active') {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'You cannot suspend or deactivate your own account.']);
                return;
            }
            if ($role !== $_SESSION['user_role'] && $_SESSION['user_role'] === 'Admin') {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'You cannot change your own admin role.']);
                return;
            }
        }
        
        // Build base parameters
        $params = [$name, $email, $role, $contact, $department, $status, $avatar];
        $passwordQuery = "";
        
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $passwordQuery = ", password = ?";
            $params[] = $hashedPassword;
        }
        
        $params[] = $id;
        
        $query = "UPDATE users SET 
                    name = ?, email = ?, role = ?, contact_number = ?, department = ?, status = ?, avatar = ? {$passwordQuery}
                  WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute($params);
        
        // If updating self, synchronize session details
        if ($id === $_SESSION['user_id']) {
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_role'] = $role;
        }
        
        if (ob_get_level() > 0) ob_clean();
        echo json_encode([
            'success' => true,
            'message' => 'User updated successfully!'
        ]);
        
    } catch (Exception $e) {
        if (ob_get_level() > 0) ob_clean();
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error updating user: ' . $e->getMessage()]);
    }
}

function handleDeleteUser($conn) {
    try {
        $id = trim($_GET['id'] ?? '');
        if (empty($id)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing user ID.']);
            return;
        }
        
        // Lockout protection: Cannot delete self
        if ($id === $_SESSION['user_id']) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Lockout Protection: You cannot delete your own logged-in account!']);
            return;
        }
        
        $query = "DELETE FROM users WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$id]);
        
        if (ob_get_level() > 0) ob_clean();
        echo json_encode([
            'success' => true,
            'message' => 'User deleted successfully!'
        ]);
        
    } catch (Exception $e) {
        if (ob_get_level() > 0) ob_clean();
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error deleting user: ' . $e->getMessage()]);
    }
}
?>
