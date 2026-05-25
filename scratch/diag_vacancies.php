<?php
chdir(__DIR__ . '/..');
require_once 'config/database.php';
$db = (new Database())->getConnection();

$jobs = $db->query('SELECT id, title, vacancies FROM jobs ORDER BY title')->fetchAll(PDO::FETCH_ASSOC);
echo "Job Vacancy Summary:\n";
foreach ($jobs as $j) {
    $s = $db->prepare("SELECT COUNT(*) as c FROM employees WHERE job_id = ? AND employment_status = 'Active'");
    $s->execute([$j['id']]);
    $emp = (int)$s->fetch(PDO::FETCH_ASSOC)['c'];
    $avail = max(0, (int)$j['vacancies'] - $emp);
    echo "  {$j['title']}: total={$j['vacancies']} | employed={$emp} | available={$avail}\n";
}
?>
