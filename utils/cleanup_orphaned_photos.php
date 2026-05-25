<?php
/**
 * Cleanup Orphaned Profile Photos
 * This script removes profile photos for employees that no longer exist in the system
 * Run this periodically as a maintenance task
 */

require_once __DIR__ . '/../config/database.php';

// Set execution time limit for large cleanups
set_time_limit(300); // 5 minutes

echo "=== Profile Photos Cleanup Utility ===\n\n";

try {
    // Get database connection
    $db = new Database();
    $conn = $db->getConnection();
    
    // Get all employee IDs from database
    $query = "SELECT employee_id FROM employees";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    
    $activeEmployeeIds = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $activeEmployeeIds[] = $row['employee_id'];
    }
    
    echo "Found " . count($activeEmployeeIds) . " active employees in database\n\n";
    
    // Get all profile photos from uploads directory
    $uploadDir = __DIR__ . '/../uploads/profile_photos/';
    $photoFiles = glob($uploadDir . 'profile_*.*');
    
    echo "Found " . count($photoFiles) . " profile photos in storage\n\n";
    
    $orphanedCount = 0;
    $deletedCount = 0;
    $totalSize = 0;
    
    foreach ($photoFiles as $photoFile) {
        $filename = basename($photoFile);
        
        // Extract employee ID from filename (format: profile_{employeeId}_{timestamp}.{ext})
        if (preg_match('/^profile_(.+?)_\d+\.\w+$/', $filename, $matches)) {
            $employeeId = $matches[1];
            
            // Check if employee exists
            if (!in_array($employeeId, $activeEmployeeIds)) {
                $orphanedCount++;
                $fileSize = filesize($photoFile);
                $totalSize += $fileSize;
                
                echo "Orphaned: $filename (Employee ID: $employeeId) - " . formatBytes($fileSize) . "\n";
                
                // Delete the file
                if (unlink($photoFile)) {
                    $deletedCount++;
                    echo "  ✓ Deleted\n";
                } else {
                    echo "  ✗ Failed to delete\n";
                }
            }
        }
    }
    
    echo "\n=== Cleanup Summary ===\n";
    echo "Orphaned photos found: $orphanedCount\n";
    echo "Photos deleted: $deletedCount\n";
    echo "Space freed: " . formatBytes($totalSize) . "\n";
    
    if ($orphanedCount === 0) {
        echo "\n✓ No orphaned photos found. Storage is clean!\n";
    } else if ($deletedCount === $orphanedCount) {
        echo "\n✓ All orphaned photos cleaned up successfully!\n";
    } else {
        echo "\n⚠ Some photos could not be deleted. Check file permissions.\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow];
}

echo "\n=== Cleanup Complete ===\n";
?>
