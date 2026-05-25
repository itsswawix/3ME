<?php
/**
 * Run Migration 011: Create leave tables
 */

require_once dirname(__DIR__) . '/config/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    echo "Starting Migration 011: Create leave tables...\n";
    
    // Read and execute the SQL file
    $sql = file_get_contents(__DIR__ . '/011_create_leave_tables.sql');
    
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
    
    echo "\n✅ Migration 011 completed successfully!\n";
    echo "Leave tables created:\n";
    echo "  - leave_types\n";
    echo "  - leave_requests\n";
    echo "  - leave_balances\n";
    
} catch (Exception $e) {
    echo "\n❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
?>
