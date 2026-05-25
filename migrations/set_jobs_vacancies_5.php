<?php
/**
 * Migration: Fix job vacancies based on actual employee count
 * Sets vacancies to max(5, current_employee_count + 1) so there's always at least 1 available
 * Run once to correct vacancy counts.
 */

chdir(__DIR__ . '/..');
require_once 'config/database.php';
$database = new Database();
$conn = $database->getConnection();

echo "=== Vacancy Fix Migration ===\n\n";

$jobs = $conn->query('SELECT id, title, vacancies FROM jobs ORDER BY title')->fetchAll(PDO::FETCH_ASSOC);

foreach ($jobs as $j) {
    // Count active employees for this job
    $empStmt = $conn->prepare("SELECT COUNT(*) as cnt FROM employees WHERE job_id = ? AND employment_status = 'Active'");
    $empStmt->execute([$j['id']]);
    $empCount = (int)($empStmt->fetch(PDO::FETCH_ASSOC)['cnt'] ?? 0);

    // Ensure vacancies is at least 5 and at least empCount+1 (so there's 1 slot open)
    $newVacancies = max(5, $empCount + 1, (int)$j['vacancies']);

    // If current vacancies < empCount (fully filled or over), bump to empCount + 1
    if ((int)$j['vacancies'] < $empCount) {
        $newVacancies = $empCount + 1;
    }

    $updateStmt = $conn->prepare("UPDATE jobs SET vacancies = ? WHERE id = ?");
    $updateStmt->execute([$newVacancies, $j['id']]);

    $avail = $newVacancies - $empCount;
    echo "  [{$j['id']}] {$j['title']}\n";
    echo "    Employees: {$empCount}  |  Vacancies: {$j['vacancies']} → {$newVacancies}  |  Available: {$avail}\n\n";
}

echo "Done! All vacancy counts corrected.\n";
?>
