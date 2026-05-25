<?php
require_once dirname(__DIR__) . '/config/database.php';
try {
    $db = new Database();
    $conn = $db->getConnection();
    echo "Connected successfully to database.\n";
    
    // Check tables
    $tables = $conn->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables in database:\n" . implode("\n", $tables) . "\n\n";
    
    if (in_array('employees', $tables)) {
        $employees = $conn->query("SELECT id, firstname, surname, email, department_id, company_id FROM employees LIMIT 10")->fetchAll();
        echo "Employees count: " . count($employees) . "\n";
        foreach ($employees as $emp) {
            echo " - {$emp['id']}: {$emp['firstname']} {$emp['surname']} ({$emp['email']})\n";
        }
    } else {
        echo "No employees table found!\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
