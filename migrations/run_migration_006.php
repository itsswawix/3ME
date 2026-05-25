<?php
/**
 * Run Migration 006: Add job field to employees table
 */

require_once '../config/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    echo "Starting Migration 006...\n\n";
    
    // Check if job column already exists
    $stmt = $conn->query("SHOW COLUMNS FROM employees LIKE 'job'");
    if ($stmt->rowCount() > 0) {
        echo "✓ Job column already exists, skipping migration.\n";
        exit(0);
    }
    
    // Read and execute the migration SQL
    $sql = file_get_contents(__DIR__ . '/006_add_job_field.sql');
    $conn->exec($sql);
    
    echo "✓ Migration 006 completed successfully!\n\n";
    
    // Verify the change
    echo "Verifying job column was added:\n";
    $stmt = $conn->query("SHOW COLUMNS FROM employees LIKE 'job'");
    $column = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($column) {
        echo "  ✓ Job column added: {$column['Field']} ({$column['Type']})\n";
    } else {
        echo "  ❌ Job column not found!\n";
        exit(1);
    }
    
    // Optionally populate job field from jobs table
    echo "\nPopulating job field from jobs table...\n";
    $updateQuery = "UPDATE employees e 
                    INNER JOIN jobs j ON e.job_id = j.id 
                    SET e.job = j.title 
                    WHERE e.job_id IS NOT NULL AND (e.job IS NULL OR e.job = '')";
    $stmt = $conn->prepare($updateQuery);
    $stmt->execute();
    $rowsUpdated = $stmt->rowCount();
    echo "  ✓ Updated {$rowsUpdated} employee records with job titles\n";
    
} catch (Exception $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
?>
