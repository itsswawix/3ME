<?php
require_once '../../config/database.php';
$db = new Database();
$conn = $db->getConnection();

echo "=== Jobs ===\n";
$stmt = $conn->query('SELECT id, title, department_id FROM jobs');
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($rows as $r) echo $r['id'] . ' => ' . $r['title'] . ' (dept: ' . $r['department_id'] . ")\n";

echo "\n=== Departments ===\n";
$stmt = $conn->query('SELECT id, name, company_id FROM departments');
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($rows as $r) echo $r['id'] . ' => ' . $r['name'] . ' (company: ' . $r['company_id'] . ")\n";

echo "\n=== Companies ===\n";
$stmt = $conn->query('SELECT id, name FROM companies');
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($rows as $r) echo $r['id'] . ' => ' . $r['name'] . "\n";

echo "\n=== Applicants ===\n";
$stmt = $conn->query('SELECT id, job_id, company_id, department_id FROM applicants');
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($rows as $r) echo $r['id'] . ' => job:' . ($r['job_id'] ?: 'NULL') . ' company:' . ($r['company_id'] ?: 'NULL') . ' dept:' . ($r['department_id'] ?: 'NULL') . "\n";
?>
