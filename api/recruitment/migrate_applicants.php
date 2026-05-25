<?php
/**
 * Migration: Update applicants table to use foreign key IDs
 * Changes: position_id -> job_id, position_title (drop), company -> company_id, department -> department_id
 */

require_once '../../config/database.php';

$db = new Database();
$conn = $db->getConnection();

echo "=== Applicants Table Migration ===\n\n";

// Check current columns
$stmt = $conn->query('DESCRIBE applicants');
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$columns = array_column($rows, 'Field');
echo "Current columns: " . implode(', ', $columns) . "\n\n";

$hasJobId = in_array('job_id', $columns);
$hasCompanyId = in_array('company_id', $columns);
$hasDepartmentId = in_array('department_id', $columns);
$hasPositionId = in_array('position_id', $columns);
$hasPositionTitle = in_array('position_title', $columns);
$hasCompany = in_array('company', $columns);
$hasDepartment = in_array('department', $columns);

// Step 1: Add new columns if they don't exist
if (!$hasJobId) {
    echo "Adding job_id column...\n";
    $conn->exec("ALTER TABLE applicants ADD COLUMN job_id VARCHAR(50) DEFAULT NULL AFTER requisition_id");
    echo "  Done.\n";
} else {
    echo "job_id column already exists.\n";
}

if (!$hasCompanyId) {
    echo "Adding company_id column...\n";
    $conn->exec("ALTER TABLE applicants ADD COLUMN company_id VARCHAR(50) DEFAULT NULL AFTER job_id");
    echo "  Done.\n";
} else {
    echo "company_id column already exists.\n";
}

if (!$hasDepartmentId) {
    echo "Adding department_id column...\n";
    $conn->exec("ALTER TABLE applicants ADD COLUMN department_id VARCHAR(50) DEFAULT NULL AFTER company_id");
    echo "  Done.\n";
} else {
    echo "department_id column already exists.\n";
}

// Step 2: Migrate data from old columns to new columns
if ($hasPositionId && $hasJobId) {
    echo "\nMigrating position_id -> job_id...\n";
    // Map position_id values to job IDs by matching the job title
    $stmt = $conn->query("SELECT id, position_id, position_title, company, department FROM applicants WHERE job_id IS NULL AND position_id IS NOT NULL");
    $applicants = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($applicants as $app) {
        $jobId = null;
        $companyId = null;
        $departmentId = null;
        
        // Try to find job by position_id directly (it might already be a job ID)
        $jStmt = $conn->prepare("SELECT id FROM jobs WHERE id = ?");
        $jStmt->execute([$app['position_id']]);
        $job = $jStmt->fetch(PDO::FETCH_ASSOC);
        if ($job) {
            $jobId = $job['id'];
        }
        
        // Try to find department by name
        if (!empty($app['department'])) {
            $dStmt = $conn->prepare("SELECT id FROM departments WHERE name = ?");
            $dStmt->execute([$app['department']]);
            $dept = $dStmt->fetch(PDO::FETCH_ASSOC);
            if ($dept) {
                $departmentId = $dept['id'];
            }
        }
        
        // Try to find company by name
        if (!empty($app['company'])) {
            $cStmt = $conn->prepare("SELECT id FROM companies WHERE name = ?");
            $cStmt->execute([$app['company']]);
            $comp = $cStmt->fetch(PDO::FETCH_ASSOC);
            if ($comp) {
                $companyId = $comp['id'];
            }
        }
        
        // Update the record
        $uStmt = $conn->prepare("UPDATE applicants SET job_id = ?, company_id = ?, department_id = ? WHERE id = ?");
        $uStmt->execute([$jobId, $companyId, $departmentId, $app['id']]);
        echo "  Migrated {$app['id']}: job_id={$jobId}, company_id={$companyId}, department_id={$departmentId}\n";
    }
    echo "  Migration complete.\n";
}

// Step 3: Drop old columns
if ($hasPositionId) {
    echo "\nDropping position_id column...\n";
    $conn->exec("ALTER TABLE applicants DROP COLUMN position_id");
    echo "  Done.\n";
}

if ($hasPositionTitle) {
    echo "Dropping position_title column...\n";
    $conn->exec("ALTER TABLE applicants DROP COLUMN position_title");
    echo "  Done.\n";
}

if ($hasCompany) {
    echo "Dropping company column...\n";
    $conn->exec("ALTER TABLE applicants DROP COLUMN company");
    echo "  Done.\n";
}

if ($hasDepartment) {
    echo "Dropping department column...\n";
    $conn->exec("ALTER TABLE applicants DROP COLUMN department");
    echo "  Done.\n";
}

// Step 4: Drop the foreign key constraint on requisition_id if it exists
try {
    $conn->exec("ALTER TABLE applicants DROP FOREIGN KEY applicants_ibfk_1");
    echo "\nDropped foreign key constraint applicants_ibfk_1.\n";
} catch (Exception $e) {
    echo "\nNo foreign key constraint to drop (or already removed).\n";
}

// Step 5: Make requisition_id nullable if it isn't already
try {
    $conn->exec("ALTER TABLE applicants MODIFY COLUMN requisition_id VARCHAR(50) DEFAULT NULL");
    echo "Made requisition_id nullable.\n";
} catch (Exception $e) {
    echo "requisition_id already nullable or error: " . $e->getMessage() . "\n";
}

// Step 6: Update color column width
try {
    $conn->exec("ALTER TABLE applicants MODIFY COLUMN color VARCHAR(50) DEFAULT NULL");
    echo "Updated color column to VARCHAR(50).\n";
} catch (Exception $e) {
    echo "Color column update error: " . $e->getMessage() . "\n";
}

// Verify final structure
echo "\n=== Final Table Structure ===\n";
$stmt = $conn->query('DESCRIBE applicants');
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $row) {
    echo "  {$row['Field']} ({$row['Type']}) {$row['Null']} {$row['Default']}\n";
}

echo "\n=== Migration Complete ===\n";
?>
