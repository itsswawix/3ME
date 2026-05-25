<?php
require_once __DIR__ . '/../config/database.php';
try {
    $database = new Database();
    $db = $database->getConnection();
    
    echo "--- IMPORT_HISTORY SCHEMA ---\n";
    $stmt = $db->query("DESCRIBE import_history");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
    }
    
    echo "\n--- IMPORT_DATA SCHEMA ---\n";
    $stmt = $db->query("DESCRIBE import_data");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
