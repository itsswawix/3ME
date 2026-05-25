<?php
/**
 * Test Onboarding Flow
 * This script tests the complete flow from applicant to onboarding record
 */

require_once '../config/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    echo "=== Testing Onboarding Flow ===\n\n";
    
    // Step 1: Check if applicants table has ID columns
    echo "Step 1: Checking applicants table structure...\n";
    $stmt = $conn->query("DESCRIBE applicants");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $hasCompanyId = false;
    $hasDepartmentId = false;
    $hasJobId = false;
    
    foreach ($columns as $column) {
        if ($column['Field'] === 'company_id') $hasCompanyId = true;
        if ($column['Field'] === 'department_id') $hasDepartmentId = true;
        if ($column['Field'] === 'job_id') $hasJobId = true;
    }
    
    if ($hasCompanyId && $hasDepartmentId && $hasJobId) {
        echo "  ✓ All ID columns exist in applicants table\n";
    } else {
        echo "  ❌ Missing ID columns:\n";
        if (!$hasCompanyId) echo "    - company_id\n";
        if (!$hasDepartmentId) echo "    - department_id\n";
        if (!$hasJobId) echo "    - job_id\n";
        echo "\n  Please run: php migrations/run_migration_003.php\n";
        exit(1);
    }
    
    // Step 2: Check if onboarding_records table has ID columns
    echo "\nStep 2: Checking onboarding_records table structure...\n";
    $stmt = $conn->query("DESCRIBE onboarding_records");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $hasCompanyId = false;
    $hasDepartmentId = false;
    $hasJobId = false;
    
    foreach ($columns as $column) {
        if ($column['Field'] === 'company_id') $hasCompanyId = true;
        if ($column['Field'] === 'department_id') $hasDepartmentId = true;
        if ($column['Field'] === 'job_id') $hasJobId = true;
    }
    
    if ($hasCompanyId && $hasDepartmentId && $hasJobId) {
        echo "  ✓ All ID columns exist in onboarding_records table\n";
    } else {
        echo "  ⚠ Missing ID columns in onboarding_records:\n";
        if (!$hasCompanyId) echo "    - company_id\n";
        if (!$hasDepartmentId) echo "    - department_id\n";
        if (!$hasJobId) echo "    - job_id\n";
        echo "\n  Note: These should be added automatically when creating records\n";
    }
    
    // Step 3: Check sample applicant data
    echo "\nStep 3: Checking applicant data...\n";
    $stmt = $conn->query("
        SELECT a.id, a.firstname, a.surname, a.email,
               a.company, a.company_id,
               a.department, a.department_id,
               a.position_title, a.job_id,
               c.name as company_name,
               d.name as department_name,
               j.title as job_title
        FROM applicants a
        LEFT JOIN companies c ON a.company_id = c.id
        LEFT JOIN departments d ON a.department_id = d.id
        LEFT JOIN jobs j ON a.job_id = j.id
        LIMIT 5
    ");
    $applicants = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($applicants) === 0) {
        echo "  ⚠ No applicants found in database\n";
    } else {
        echo "  Found " . count($applicants) . " applicants (showing first 5):\n\n";
        
        foreach ($applicants as $applicant) {
            echo "  Applicant: {$applicant['id']} - {$applicant['firstname']} {$applicant['surname']}\n";
            echo "    Email: {$applicant['email']}\n";
            
            // Check company
            if ($applicant['company_id']) {
                if ($applicant['company_name']) {
                    echo "    ✓ Company: {$applicant['company_name']} (ID: {$applicant['company_id']})\n";
                } else {
                    echo "    ⚠ Company ID: {$applicant['company_id']} (NOT FOUND in companies table)\n";
                }
            } else {
                echo "    ❌ Company ID: NULL (text: {$applicant['company']})\n";
            }
            
            // Check department
            if ($applicant['department_id']) {
                if ($applicant['department_name']) {
                    echo "    ✓ Department: {$applicant['department_name']} (ID: {$applicant['department_id']})\n";
                } else {
                    echo "    ⚠ Department ID: {$applicant['department_id']} (NOT FOUND in departments table)\n";
                }
            } else {
                echo "    ❌ Department ID: NULL (text: {$applicant['department']})\n";
            }
            
            // Check job
            if ($applicant['job_id']) {
                if ($applicant['job_title']) {
                    echo "    ✓ Job: {$applicant['job_title']} (ID: {$applicant['job_id']})\n";
                } else {
                    echo "    ⚠ Job ID: {$applicant['job_id']} (NOT FOUND in jobs table)\n";
                }
            } else {
                echo "    ❌ Job ID: NULL (text: {$applicant['position_title']})\n";
            }
            
            echo "\n";
        }
    }
    
    // Step 4: Check onboarding records
    echo "Step 4: Checking onboarding records...\n";
    $stmt = $conn->query("
        SELECT onr.id, onr.employee_id, onr.employee_name,
               onr.company_id, onr.department_id, onr.job_id,
               c.name as company_name,
               d.name as department_name,
               j.title as job_title,
               onr.progress
        FROM onboarding_records onr
        LEFT JOIN companies c ON onr.company_id = c.id
        LEFT JOIN departments d ON onr.department_id = d.id
        LEFT JOIN jobs j ON onr.job_id = j.id
        ORDER BY onr.created_at DESC
        LIMIT 5
    ");
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($records) === 0) {
        echo "  ⚠ No onboarding records found\n";
        echo "  This is normal if you haven't accepted any offers yet\n";
    } else {
        echo "  Found " . count($records) . " onboarding records (showing first 5):\n\n";
        
        $hasIssues = false;
        
        foreach ($records as $record) {
            echo "  Record: {$record['id']} - {$record['employee_name']}\n";
            echo "    Employee ID: {$record['employee_id']}\n";
            echo "    Progress: {$record['progress']}\n";
            
            // Check company
            if ($record['company_id']) {
                if ($record['company_name']) {
                    echo "    ✓ Company: {$record['company_name']} (ID: {$record['company_id']})\n";
                } else {
                    echo "    ❌ Company ID: {$record['company_id']} (NOT FOUND - record won't display!)\n";
                    $hasIssues = true;
                }
            } else {
                echo "    ❌ Company ID: NULL (record won't display!)\n";
                $hasIssues = true;
            }
            
            // Check department
            if ($record['department_id']) {
                if ($record['department_name']) {
                    echo "    ✓ Department: {$record['department_name']} (ID: {$record['department_id']})\n";
                } else {
                    echo "    ❌ Department ID: {$record['department_id']} (NOT FOUND - record won't display!)\n";
                    $hasIssues = true;
                }
            } else {
                echo "    ❌ Department ID: NULL (record won't display!)\n";
                $hasIssues = true;
            }
            
            // Check job
            if ($record['job_id']) {
                if ($record['job_title']) {
                    echo "    ✓ Job: {$record['job_title']} (ID: {$record['job_id']})\n";
                } else {
                    echo "    ❌ Job ID: {$record['job_id']} (NOT FOUND - record won't display!)\n";
                    $hasIssues = true;
                }
            } else {
                echo "    ❌ Job ID: NULL (record won't display!)\n";
                $hasIssues = true;
            }
            
            echo "\n";
        }
        
        if ($hasIssues) {
            echo "  ⚠ ISSUES FOUND: Some onboarding records have NULL or invalid IDs\n";
            echo "  These records will not display in the onboarding list!\n";
            echo "\n  To fix:\n";
            echo "  1. Delete these records from onboarding_records table\n";
            echo "  2. Make sure applicants have valid company_id, department_id, job_id\n";
            echo "  3. Re-accept the job offers to recreate onboarding records\n";
        }
    }
    
    // Step 5: Summary
    echo "\n=== Summary ===\n";
    echo "✓ Database structure is correct\n";
    
    $applicantsWithIds = 0;
    $applicantsWithoutIds = 0;
    
    $stmt = $conn->query("SELECT COUNT(*) as total FROM applicants WHERE company_id IS NOT NULL AND department_id IS NOT NULL AND job_id IS NOT NULL");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $applicantsWithIds = $result['total'];
    
    $stmt = $conn->query("SELECT COUNT(*) as total FROM applicants WHERE company_id IS NULL OR department_id IS NULL OR job_id IS NULL");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $applicantsWithoutIds = $result['total'];
    
    echo "✓ Applicants with complete IDs: $applicantsWithIds\n";
    if ($applicantsWithoutIds > 0) {
        echo "⚠ Applicants with missing IDs: $applicantsWithoutIds\n";
        echo "  Run: php migrations/run_migration_003.php to populate IDs\n";
    }
    
    $stmt = $conn->query("SELECT COUNT(*) as total FROM onboarding_records WHERE company_id IS NOT NULL AND department_id IS NOT NULL AND job_id IS NOT NULL");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $validRecords = $result['total'];
    
    $stmt = $conn->query("SELECT COUNT(*) as total FROM onboarding_records WHERE company_id IS NULL OR department_id IS NULL OR job_id IS NULL");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $invalidRecords = $result['total'];
    
    echo "✓ Valid onboarding records: $validRecords\n";
    if ($invalidRecords > 0) {
        echo "⚠ Invalid onboarding records: $invalidRecords (won't display in list)\n";
        echo "  These need to be deleted and recreated\n";
    }
    
    echo "\n✅ Test completed!\n";
    
    if ($applicantsWithoutIds > 0 || $invalidRecords > 0) {
        echo "\n⚠ Action required: See warnings above\n";
        exit(1);
    } else {
        echo "\n🎉 Everything looks good! The onboarding flow should work correctly.\n";
    }
    
} catch (Exception $e) {
    echo "\n❌ Test failed: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
?>
