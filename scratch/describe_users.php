<?php
require_once __DIR__ . '/../config/database.php';
try {
    $db = (new Database())->getConnection();
    $columns = $db->query('DESCRIBE users')->fetchAll();
    echo json_encode($columns, JSON_PRETTY_PRINT) . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
