<?php
session_start();
header('Content-Type: application/json');

if (isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => true,
        'authenticated' => true,
        'user' => [
            'id' => $_SESSION['user_id'],
            'email' => $_SESSION['user_email'],
            'name' => $_SESSION['user_name'],
            'role' => $_SESSION['user_role']
        ]
    ]);
} else {
    echo json_encode([
        'success' => true,
        'authenticated' => false
    ]);
}
?>
