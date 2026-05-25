<?php
require_once __DIR__ . '/../config/database.php';
try {
    $database = new Database();
    $conn = $database->getConnection();
    $stmt = $conn->query("SELECT id, firstname, surname FROM employees LIMIT 5");
    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($employees);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
