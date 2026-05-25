<?php
/**
 * Run Migration 008: Create jobs table
 */

require_once '../config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    echo "Starting Migration 008: Create jobs table...\n";
    
    // Read and execute the migration SQL
    $sql = file_get_contents(__DIR__ . '/008_create_jobs_table.sql');
    
    // Execute the migration
    $db->exec($sql);
    
    echo "✅ Migration 008 completed successfully!\n";
    echo "Jobs table has been created.\n";
    
} catch (Exception $e) {
    echo "❌ Migration 008 failed: " . $e->getMessage() . "\n";
    exit(1);
}
?>
