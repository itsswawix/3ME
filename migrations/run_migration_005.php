<?php
/**
 * Run Migration 005: Update employees table schema
 * - Remove employee_number column (use id as employee_number)
 * - Rename position_id to job_id (if exists)
 */

require_once '../config/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    echo "Starting Migration 005...\n\n";
    
    // Read and execute the migration SQL
    $sql = file_get_contents(__DIR__ . '/005_update_employees_schema.sql');
    
    // Execute the migration
    $conn->exec($sql);
    
    echo "✓ Migration 005 completed successfully!\n\n";
    
    // Verify the changes
    echo "Verifying employees table structure:\n";
    $stmt = $conn->query("DESCRIBE employees");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columns as $column) {
        echo "  - {$column['Field']} ({$column['Type']})\n";
    }
    
    echo "\n";
    
    // Check that employee_number is gone
    $hasEmployeeNumber = false;
    $hasJobId = false;
    $hasPositionId = false;
    
    foreach ($columns as $column) {
        if ($column['Field'] === 'employee_number') $hasEmployeeNumber = true;
        if ($column['Field'] === 'job_id') $hasJobId = true;
        if ($column['Field'] === 'position_id') $hasPositionId = true;
    }
    
    echo "Verification:\n";
    echo "  - employee_number removed: " . ($hasEmployeeNumber ? "❌ FAILED" : "✓ SUCCESS") . "\n";
    echo "  - job_id exists: " . ($hasJobId ? "✓ SUCCESS" : "❌ FAILED") . "\n";
    echo "  - position_id removed: " . ($hasPositionId ? "❌ FAILED" : "✓ SUCCESS") . "\n";
    
} catch (Exception $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
?>
