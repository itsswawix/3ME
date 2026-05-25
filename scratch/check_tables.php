<?php
require_once dirname(__DIR__) . '/config/database.php';
try {
    $db = new Database();
    $conn = $db->getConnection();
    echo "Connected successfully.\n\n";
    
    $tables = $conn->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Testing each table:\n";
    foreach ($tables as $table) {
        try {
            $stmt = $conn->query("SELECT 1 FROM `$table` LIMIT 1");
            $stmt->execute();
            echo "✔ Table '$table' is OK\n";
        } catch (PDOException $e) {
            echo "❌ Table '$table' error: " . $e->getMessage() . "\n";
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
