<?php
/**
 * Attendance Module Setup Script
 * Run this file to set up the attendance module database tables
 */

echo "<!DOCTYPE html>
<html>
<head>
    <title>Attendance Module Setup</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .success { color: #10b981; }
        .error { color: #ef4444; }
        .warning { color: #f59e0b; }
        .step { background: #f8fafc; padding: 15px; margin: 10px 0; border-radius: 8px; border-left: 4px solid #4f46e5; }
        pre { background: #1e293b; color: #e2e8f0; padding: 15px; border-radius: 8px; overflow-x: auto; }
        .btn { background: #4f46e5; color: white; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; }
        .btn:hover { background: #4338ca; }
    </style>
</head>
<body>
    <h1>🗄️ Attendance Module Setup</h1>
";

require_once 'config/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    echo "<div class='step'>";
    echo "<h2>Step 1: Database Connection</h2>";
    echo "<p class='success'>✅ Connected to database successfully!</p>";
    echo "</div>";
    
    echo "<div class='step'>";
    echo "<h2>Step 2: Creating Attendance Tables</h2>";
    
    // Read and execute the SQL file
    $sql = file_get_contents('migrations/009_create_attendance_tables.sql');
    
    // Split by semicolon and execute each statement
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    $created = [];
    $skipped = [];
    
    foreach ($statements as $statement) {
        if (!empty($statement) && stripos($statement, 'SELECT') !== 0) {
            try {
                $conn->exec($statement);
                // Extract table name from CREATE TABLE statement
                if (preg_match('/CREATE TABLE.*?`?(\w+)`?/i', $statement, $matches)) {
                    $created[] = $matches[1];
                }
            } catch (PDOException $e) {
                if (strpos($e->getMessage(), 'already exists') !== false) {
                    if (preg_match('/CREATE TABLE.*?`?(\w+)`?/i', $statement, $matches)) {
                        $skipped[] = $matches[1];
                    }
                } else {
                    throw $e;
                }
            }
        }
    }
    
    if (count($created) > 0) {
        echo "<p class='success'>✅ Created tables:</p><ul>";
        foreach ($created as $table) {
            echo "<li class='success'>$table</li>";
        }
        echo "</ul>";
    }
    
    if (count($skipped) > 0) {
        echo "<p class='warning'>⚠️ Tables already exist (skipped):</p><ul>";
        foreach ($skipped as $table) {
            echo "<li class='warning'>$table</li>";
        }
        echo "</ul>";
    }
    
    echo "</div>";
    
    echo "<div class='step'>";
    echo "<h2>Step 3: Verify Tables</h2>";
    
    $tables = ['rosters', 'corrections', 'import_history', 'import_data'];
    $verified = [];
    $missing = [];
    
    foreach ($tables as $table) {
        $check = $conn->query("SHOW TABLES LIKE '$table'");
        if ($check->rowCount() > 0) {
            $verified[] = $table;
        } else {
            $missing[] = $table;
        }
    }
    
    if (count($verified) === count($tables)) {
        echo "<p class='success'>✅ All tables verified successfully!</p>";
        echo "<ul>";
        foreach ($verified as $table) {
            echo "<li class='success'>$table</li>";
        }
        echo "</ul>";
    } else {
        echo "<p class='error'>❌ Some tables are missing:</p>";
        echo "<ul>";
        foreach ($missing as $table) {
            echo "<li class='error'>$table</li>";
        }
        echo "</ul>";
    }
    
    echo "</div>";
    
    echo "<div class='step'>";
    echo "<h2>Step 4: Test API Endpoints</h2>";
    echo "<p>Test the API endpoints to ensure they're working:</p>";
    echo "<pre>";
    echo "# Test Rosters API\n";
    echo "curl http://localhost/3ME/api/attendance/rosters.php\n\n";
    echo "# Test Corrections API\n";
    echo "curl http://localhost/3ME/api/attendance/corrections.php\n\n";
    echo "# Test Imports API\n";
    echo "curl http://localhost/3ME/api/attendance/imports.php\n";
    echo "</pre>";
    echo "</div>";
    
    echo "<div class='step'>";
    echo "<h2>✅ Setup Complete!</h2>";
    echo "<p>The attendance module is now ready to use.</p>";
    echo "<p><a href='app/views/attendance.php' class='btn'>Open Attendance Module</a></p>";
    echo "</div>";
    
    echo "<div class='step'>";
    echo "<h2>📚 Documentation</h2>";
    echo "<p>For detailed information, see:</p>";
    echo "<ul>";
    echo "<li><a href='ATTENDANCE_DATABASE_INTEGRATION.md'>Database Integration Guide</a></li>";
    echo "<li><a href='ATTENDANCE_MODALS_SUMMARY.md'>Modals Implementation Summary</a></li>";
    echo "<li><a href='ATTENDANCE_QUICK_REFERENCE.md'>Quick Reference Guide</a></li>";
    echo "</ul>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='step'>";
    echo "<h2 class='error'>❌ Setup Failed</h2>";
    echo "<p class='error'>Error: " . $e->getMessage() . "</p>";
    echo "<p>Please check:</p>";
    echo "<ul>";
    echo "<li>Database connection settings in config/database.php</li>";
    echo "<li>MySQL server is running</li>";
    echo "<li>Database user has CREATE TABLE permissions</li>";
    echo "</ul>";
    echo "</div>";
}

echo "</body></html>";
?>
