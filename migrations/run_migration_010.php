<?php
/**
 * Run Migration 010 - Add file storage columns to import_history
 */

require_once __DIR__ . '/../config/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    echo "<h2>Running Migration 010: Add file storage to import_history</h2>";
    
    // Check if columns already exist
    $stmt = $conn->query("SHOW COLUMNS FROM import_history LIKE 'file_path'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color: orange;'>⚠️ Column 'file_path' already exists. Skipping migration.</p>";
    } else {
        // Run migration SQL
        $sql = file_get_contents(__DIR__ . '/010_add_file_storage_to_imports.sql');
        
        // Split by semicolons and execute each statement
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        
        foreach ($statements as $statement) {
            if (!empty($statement) && stripos($statement, 'SELECT') !== 0) {
                $conn->exec($statement);
                echo "<p style='color: green;'>✅ Executed: " . htmlspecialchars(substr($statement, 0, 80)) . "...</p>";
            }
        }
        
        echo "<p style='color: green; font-weight: bold;'>✅ Migration 010 completed successfully!</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Migration failed: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
