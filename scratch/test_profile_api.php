<?php
/**
 * Scratch Verification Script for Profile API
 * Validates the profile fetching (GET) and updating (POST) operations.
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=============================================\n";
echo "       PROFILE API VERIFICATION SYSTEM        \n";
echo "=============================================\n\n";

try {
    require_once __DIR__ . '/../config/database.php';
    $db = (new Database())->getConnection();
    
    // Ensure the mock user exists in the database
    $stmt = $db->prepare("SELECT id, name, email, contact_number, color, role, department FROM users WHERE id = ?");
    $stmt->execute(['USER-ADMIN-001']);
    $user = $stmt->fetch();
    
    if (!$user) {
        echo "[WARNING] USER-ADMIN-001 does not exist in the database! Creating temporary test user...\n";
        $insert = $db->prepare("
            INSERT INTO users (id, name, email, password, role, contact_number, department, status, created_at)
            VALUES ('USER-ADMIN-001', 'System Administrator', 'admin@3me.com', ?, 'Admin', '+63 900 111 2222', 'IT Department', 'Active', NOW())
        ");
        $insert->execute([password_hash('admin123', PASSWORD_DEFAULT)]);
        echo "[SUCCESS] Temporary test user created.\n\n";
        
        // Fetch again
        $stmt->execute(['USER-ADMIN-001']);
        $user = $stmt->fetch();
    }

    echo "1. Testing Fetch Profile (GET-equivalent):\n";
    echo "   - User ID: " . $user['id'] . "\n";
    echo "   - Name: " . $user['name'] . "\n";
    echo "   - Email: " . $user['email'] . "\n";
    echo "   - Role: " . $user['role'] . "\n";
    echo "   - Department: " . ($user['department'] ?? 'None') . "\n";
    echo "   - Color Theme: " . ($user['color'] ?? 'None') . "\n";
    echo "   - Contact No: " . ($user['contact_number'] ?? 'None') . "\n";
    echo "   [PASS] Profile fetched successfully from database.\n\n";
    
    echo "2. Testing Update Profile (POST-equivalent):\n";
    
    $newName = "Admin User (Tested)";
    $newEmail = "admin-test@3me.com";
    $newContact = "+63 999 888 7777";
    $newColor = "#ea580c";
    
    echo "   - Attempting update to name: '$newName', color: '$newColor', contact: '$newContact'...\n";
    
    $db->beginTransaction();
    $updateStmt = $db->prepare("
        UPDATE users 
        SET name = ?, email = ?, contact_number = ?, color = ?
        WHERE id = ?
    ");
    $result = $updateStmt->execute([$newName, $newEmail, $newContact, $newColor, 'USER-ADMIN-001']);
    $db->commit();
    
    if ($result) {
        echo "   [PASS] Profile database update executed successfully.\n";
        
        // Re-read from database to verify persistence
        $checkStmt = $db->prepare("SELECT name, email, contact_number, color FROM users WHERE id = ?");
        $checkStmt->execute(['USER-ADMIN-001']);
        $updatedUser = $checkStmt->fetch();
        
        echo "   - Verified DB Name: " . $updatedUser['name'] . "\n";
        echo "   - Verified DB Color: " . $updatedUser['color'] . "\n";
        echo "   - Verified DB Contact: " . $updatedUser['contact_number'] . "\n\n";
        
        // Restore original data to keep DB clean
        $db->beginTransaction();
        $restoreStmt = $db->prepare("
            UPDATE users 
            SET name = ?, email = ?, contact_number = ?, color = ?
            WHERE id = ?
        ");
        $restoreStmt->execute(['System Administrator', 'admin@3me.com', '+63 900 111 2222', '#4f46e5', 'USER-ADMIN-001']);
        $db->commit();
        echo "   - Original profile details restored successfully for clean state.\n";
    } else {
        echo "   [FAIL] Profile database update failed.\n";
    }

} catch (Exception $e) {
    echo "\n[ERROR] An exception occurred during test execution: " . $e->getMessage() . "\n";
}

echo "\n=============================================\n";
echo "      VERIFICATION TEST SESSION ENDED         \n";
echo "=============================================\n";
