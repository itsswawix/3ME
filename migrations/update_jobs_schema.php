<?php
/**
 * Migration: update_jobs_schema.php
 * Adds reports_to, vacancies, salary_min, and salary_max columns to jobs table if not exists.
 */

require_once __DIR__ . '/../config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    echo "Starting jobs schema update...\n";
    
    function addColumnIfNeeded($db, $table, $column, $definition) {
        $stmt = $db->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
        if ($stmt->rowCount() == 0) {
            $db->exec("ALTER TABLE `$table` ADD `$column` $definition");
            echo "✔ Column `$column` added successfully to `$table`.\n";
        } else {
            echo "ℹ Column `$column` already exists in `$table`.\n";
        }
    }
    
    // Add columns with safe checks
    addColumnIfNeeded($db, 'jobs', 'reports_to', 'VARCHAR(255) NULL AFTER level');
    addColumnIfNeeded($db, 'jobs', 'vacancies', 'INT DEFAULT 0 AFTER reports_to');
    addColumnIfNeeded($db, 'jobs', 'salary_min', 'DECIMAL(15,2) NULL AFTER vacancies');
    addColumnIfNeeded($db, 'jobs', 'salary_max', 'DECIMAL(15,2) NULL AFTER salary_min');
    
    echo "🎉 Jobs table schema updated successfully!\n";
} catch (Exception $e) {
    echo "❌ Error migrating jobs table schema: " . $e->getMessage() . "\n";
    exit(1);
}
