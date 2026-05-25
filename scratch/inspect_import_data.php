<?php
require_once __DIR__ . '/../config/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    echo "=== IMPORT HISTORY ===\n";
    $stmt = $conn->query("SELECT * FROM import_history ORDER BY created_at DESC LIMIT 10");
    $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($history as $row) {
        print_r($row);
    }
    
    echo "\n=== IMPORT DATA ROWS COUNT ===\n";
    $stmt = $conn->query("SELECT import_id, COUNT(*) as count FROM import_data GROUP BY import_id");
    $counts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($counts as $c) {
        print_r($c);
    }
    
    echo "\n=== SAMPLE IMPORT DATA (LAST 10 ROWS) ===\n";
    $stmt = $conn->query("SELECT * FROM import_data ORDER BY id DESC LIMIT 10");
    $dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($dataRows as $row) {
        print_r($row);
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
