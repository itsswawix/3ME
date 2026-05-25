<?php
/**
 * Run Migration 003: Add ID columns to applicants table
 * This script adds company_id, department_id, job_id to applicants table
 * and populates them based on existing text values
 */

require_once '../config/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    echo "Starting Migration 003...\n\n";
    
    // Step 1: Add columns to applicants table
    echo "Step 1: Adding ID columns to applicants table...\n";
    
    $alterQueries = [
        "ALTER TABLE applicants ADD COLUMN IF NOT EXISTS company_id INT NULL COMMENT 'Foreign key to companies table' AFTER company",
        "ALTER TABLE applicants ADD COLUMN IF NOT EXISTS department_id INT NULL COMMENT 'Foreign key to departments table' AFTER department",
        "ALTER TABLE applicants ADD COLUMN IF NOT EXISTS job_id INT NULL COMMENT 'Foreign key to jobs table' AFTER position_title"
    ];
    
    foreach ($alterQueries as $query) {
        try {
            $conn->exec($query);
            echo "  ✓ Executed: " . substr($query, 0, 80) . "...\n";
        } catch (PDOException $e) {
            // Column might already exist, that's okay
            if (strpos($e->getMessage(), 'Duplicate column') !== false) {
                echo "  ℹ Column already exists, skipping...\n";
            } else {
                throw $e;
            }
        }
    }
    
    // Step 2: Add indexes
    echo "\nStep 2: Adding indexes...\n";
    
    $indexQueries = [
        "ALTER TABLE applicants ADD INDEX IF NOT EXISTS idx_company_id (company_id)",
        "ALTER TABLE applicants ADD INDEX IF NOT EXISTS idx_department_id (department_id)",
        "ALTER TABLE applicants ADD INDEX IF NOT EXISTS idx_job_id (job_id)"
    ];
    
    foreach ($indexQueries as $query) {
        try {
            $conn->exec($query);
            echo "  ✓ Index added\n";
        } catch (PDOException $e) {
            // Index might already exist
            if (strpos($e->getMessage(), 'Duplicate key') !== false) {
                echo "  ℹ Index already exists, skipping...\n";
            } else {
                throw $e;
            }
        }
    }
    
    // Step 3: Populate ID columns based on text values
    echo "\nStep 3: Populating ID columns from text values...\n";
    
    // Get all applicants
    $stmt = $conn->query("SELECT id, company, department, position_title FROM applicants");
    $applicants = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "  Found " . count($applicants) . " applicants to update\n";
    
    $updated = 0;
    $skipped = 0;
    
    foreach ($applicants as $applicant) {
        $companyId = null;
        $departmentId = null;
        $jobId = null;
        
        // Look up company_id
        if (!empty($applicant['company'])) {
            $companyStmt = $conn->prepare("SELECT id FROM companies WHERE name = ? LIMIT 1");
            $companyStmt->execute([$applicant['company']]);
            $companyRow = $companyStmt->fetch(PDO::FETCH_ASSOC);
            if ($companyRow) {
                $companyId = $companyRow['id'];
            }
        }
        
        // Look up department_id (must match company)
        if (!empty($applicant['department']) && $companyId) {
            $deptStmt = $conn->prepare("SELECT id FROM departments WHERE name = ? AND company_id = ? LIMIT 1");
            $deptStmt->execute([$applicant['department'], $companyId]);
            $deptRow = $deptStmt->fetch(PDO::FETCH_ASSOC);
            if ($deptRow) {
                $departmentId = $deptRow['id'];
            }
        }
        
        // Look up job_id
        if (!empty($applicant['position_title'])) {
            $jobStmt = $conn->prepare("SELECT id FROM jobs WHERE title = ? LIMIT 1");
            $jobStmt->execute([$applicant['position_title']]);
            $jobRow = $jobStmt->fetch(PDO::FETCH_ASSOC);
            if ($jobRow) {
                $jobId = $jobRow['id'];
            }
        }
        
        // Update applicant with IDs
        if ($companyId || $departmentId || $jobId) {
            $updateStmt = $conn->prepare("
                UPDATE applicants 
                SET company_id = ?, department_id = ?, job_id = ? 
                WHERE id = ?
            ");
            $updateStmt->execute([$companyId, $departmentId, $jobId, $applicant['id']]);
            $updated++;
            
            echo "  ✓ Updated applicant {$applicant['id']}: ";
            echo "company_id=" . ($companyId ?: 'NULL') . ", ";
            echo "department_id=" . ($departmentId ?: 'NULL') . ", ";
            echo "job_id=" . ($jobId ?: 'NULL') . "\n";
        } else {
            $skipped++;
            echo "  ⚠ Skipped applicant {$applicant['id']}: No matching IDs found\n";
        }
    }
    
    echo "\n✅ Migration 003 completed successfully!\n";
    echo "  - Updated: $updated applicants\n";
    echo "  - Skipped: $skipped applicants (no matching IDs)\n";
    echo "\nNext steps:\n";
    echo "1. Verify the data looks correct\n";
    echo "2. Test creating new job offers\n";
    echo "3. Check that onboarding records are created with proper IDs\n";
    
} catch (Exception $e) {
    echo "\n❌ Migration failed: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
?>
