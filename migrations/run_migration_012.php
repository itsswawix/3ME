<?php
/**
 * Run Migration 012: Create performance tables
 */

require_once dirname(__DIR__) . '/config/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    echo "Starting Migration 012: Create performance tables...\n";
    
    // Read and execute the SQL file
    $sql = file_get_contents(__DIR__ . '/012_create_performance_tables.sql');
    
    // Split by semicolon and execute each statement
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (!empty($statement) && stripos($statement, 'SELECT') !== 0) {
            try {
                $conn->exec($statement);
                echo "✓ Executed statement successfully\n";
            } catch (PDOException $e) {
                // Check if error is about table already existing
                if (strpos($e->getMessage(), 'already exists') !== false) {
                    echo "⚠ Table already exists, skipping...\n";
                } else {
                    throw $e;
                }
            }
        }
    }
    
    echo "\n✅ Migration 012 completed successfully!\n";
    echo "Performance tables created:\n";
    echo "  - disciplinary_records\n";
    
} catch (Exception $e) {
    echo "\n❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
?>
