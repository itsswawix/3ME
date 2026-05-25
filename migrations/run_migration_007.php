<?php
/**
 * Run Migration 007: Add suffix to personal details
 * 
 * This script adds the suffix field to all tables with personal information
 */

require_once __DIR__ . '/../config/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    echo "Starting Migration 007: Add suffix to personal details...\n\n";
    
    // Read the SQL file
    $sqlFile = __DIR__ . '/007_add_suffix_to_personal_details.sql';
    $sql = file_get_contents($sqlFile);
    
    // Remove comments and split into individual statements
    $lines = explode("\n", $sql);
    $cleanedLines = array_filter($lines, function($line) {
        $line = trim($line);
        return !empty($line) && !preg_match('/^--/', $line);
    });
    $cleanedSql = implode("\n", $cleanedLines);
    
    // Split into individual statements
    $statements = array_filter(
        array_map('trim', explode(';', $cleanedSql)),
        function($stmt) {
            return !empty($stmt) && stripos($stmt, 'ALTER TABLE') !== false;
        }
    );
    
    $successCount = 0;
    $errorCount = 0;
    
    foreach ($statements as $statement) {
        try {
            // Extract table name for better logging
            preg_match('/ALTER TABLE\s+(\w+)/i', $statement, $matches);
            $tableName = $matches[1] ?? 'unknown';
            
            $conn->exec($statement . ';');
            echo "✅ Successfully added suffix column to table: $tableName\n";
            $successCount++;
        } catch (PDOException $e) {
            // Check if column already exists
            if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
                echo "ℹ️  Suffix column already exists in table: $tableName (skipping)\n";
            } else {
                echo "❌ Error adding suffix to table $tableName: " . $e->getMessage() . "\n";
                $errorCount++;
            }
        }
    }
    
    echo "\n";
    echo "========================================\n";
    echo "Migration 007 Complete!\n";
    echo "========================================\n";
    echo "✅ Successful: $successCount\n";
    echo "❌ Errors: $errorCount\n";
    echo "\n";
    echo "The suffix field has been added to:\n";
    echo "  - applicants table\n";
    echo "  - employees table\n";
    echo "  - onboarding_records table\n";
    echo "  - users table\n";
    echo "\n";
    echo "You can now use the suffix field in forms and displays.\n";
    
} catch (Exception $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
