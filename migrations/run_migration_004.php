<?php
/**
 * Run Migration 004: Remove text fields from onboarding_records
 * 
 * This migration removes the old text fields (job, position, department, company)
 * from the onboarding_records table since we now use foreign key IDs instead.
 */

require_once '../config/database.php';

echo "=== Migration 004: Remove Text Fields from Onboarding Records ===\n\n";

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    echo "Step 1: Checking current table structure...\n";
    $checkQuery = "SELECT COLUMN_NAME, DATA_TYPE 
                   FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE TABLE_SCHEMA = DATABASE() 
                   AND TABLE_NAME = 'onboarding_records'
                   ORDER BY ORDINAL_POSITION";
    $stmt = $conn->prepare($checkQuery);
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Current columns:\n";
    foreach ($columns as $col) {
        echo "  - {$col['COLUMN_NAME']} ({$col['DATA_TYPE']})\n";
    }
    echo "\n";
    
    // Check which text columns exist
    $textColumns = ['job', 'position', 'department', 'company'];
    $columnsToRemove = [];
    
    foreach ($textColumns as $colName) {
        $found = false;
        foreach ($columns as $col) {
            if ($col['COLUMN_NAME'] === $colName) {
                $found = true;
                break;
            }
        }
        if ($found) {
            $columnsToRemove[] = $colName;
        }
    }
    
    if (empty($columnsToRemove)) {
        echo "✅ No text columns to remove. Migration already applied or not needed.\n";
        exit(0);
    }
    
    echo "Step 2: Found text columns to remove: " . implode(', ', $columnsToRemove) . "\n\n";
    
    // Confirm before proceeding
    echo "⚠️  WARNING: This will permanently remove the following columns:\n";
    foreach ($columnsToRemove as $col) {
        echo "   - $col\n";
    }
    echo "\nThe data will be preserved through foreign key relationships:\n";
    echo "   - job → job_id (references jobs table)\n";
    echo "   - position → job_id (references jobs table)\n";
    echo "   - department → department_id (references departments table)\n";
    echo "   - company → company_id (references companies table)\n";
    echo "\n";
    
    // Check if running in CLI or web
    if (php_sapi_name() === 'cli') {
        echo "Do you want to proceed? (yes/no): ";
        $handle = fopen("php://stdin", "r");
        $line = fgets($handle);
        $confirm = trim(strtolower($line));
        fclose($handle);
        
        if ($confirm !== 'yes' && $confirm !== 'y') {
            echo "Migration cancelled.\n";
            exit(0);
        }
    } else {
        // Running in web, proceed automatically
        echo "Running in web mode, proceeding automatically...\n";
    }
    
    echo "\nStep 3: Removing text columns...\n";
    
    $conn->beginTransaction();
    
    try {
        foreach ($columnsToRemove as $colName) {
            echo "  Dropping column: $colName... ";
            $dropQuery = "ALTER TABLE onboarding_records DROP COLUMN `$colName`";
            $conn->exec($dropQuery);
            echo "✅ Done\n";
        }
        
        $conn->commit();
        echo "\n✅ Migration 004 completed successfully!\n\n";
        
        // Show final structure
        echo "Step 4: Verifying final table structure...\n";
        $stmt = $conn->prepare($checkQuery);
        $stmt->execute();
        $finalColumns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "Final columns:\n";
        foreach ($finalColumns as $col) {
            echo "  - {$col['COLUMN_NAME']} ({$col['DATA_TYPE']})\n";
        }
        echo "\n";
        
        // Verify foreign key columns exist
        echo "Step 5: Verifying foreign key columns...\n";
        $fkColumns = ['job_id', 'company_id', 'department_id'];
        $allPresent = true;
        
        foreach ($fkColumns as $fkCol) {
            $found = false;
            foreach ($finalColumns as $col) {
                if ($col['COLUMN_NAME'] === $fkCol) {
                    $found = true;
                    echo "  ✅ $fkCol exists\n";
                    break;
                }
            }
            if (!$found) {
                echo "  ❌ $fkCol missing!\n";
                $allPresent = false;
            }
        }
        
        if ($allPresent) {
            echo "\n✅ All foreign key columns are present!\n";
            echo "\n=== Migration Complete ===\n";
            echo "The onboarding_records table now uses only foreign key IDs.\n";
            echo "Display names are fetched via JOINs with reference tables.\n";
        } else {
            echo "\n⚠️  Warning: Some foreign key columns are missing!\n";
            echo "You may need to add them manually.\n";
        }
        
    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }
    
} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "Migration failed. No changes were made.\n";
    exit(1);
}
?>
