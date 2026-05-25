<?php
require_once __DIR__ . '/../config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    echo "--- TESTING CREATE_JOB API FUNCTIONALITY ---\n";
    // Setup clean test job
    $db->exec("DELETE FROM jobs WHERE id = 'JOB-TEST-999'");
    
    // Simulating create_job payload
    $createPayload = [
        'department_id' => 'DEPT-001',
        'title' => 'Test Software Engineer II',
        'level' => 'Senior',
        'reportsTo' => 'VP of Engineering',
        'vacancies' => 3,
        'salaryMin' => 85000.50,
        'salaryMax' => 120000.75,
        'status' => 'Active'
    ];
    
    // Generate manual count and ID to match settings_api logic
    $jobId = 'JOB-TEST-999';
    $stmt = $db->prepare("INSERT INTO jobs (id, department_id, title, level, reports_to, vacancies, salary_min, salary_max, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->execute([
        $jobId,
        $createPayload['department_id'],
        $createPayload['title'],
        $createPayload['level'],
        $createPayload['reportsTo'],
        $createPayload['vacancies'],
        $createPayload['salaryMin'],
        $createPayload['salaryMax'],
        $createPayload['status']
    ]);
    echo "✔ Job created successfully in database with ID: $jobId\n";
    
    echo "\n--- TESTING GET_JOB MAPPING ---\n";
    $stmt = $db->prepare("SELECT * FROM jobs WHERE id = ?");
    $stmt->execute([$jobId]);
    $job = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Simulating mapping done in settings_api.php
    $job['jobTitle'] = $job['title'];
    $job['reportsTo'] = $job['reports_to'] ?? '';
    $job['salaryMin'] = $job['salary_min'] ?? null;
    $job['salaryMax'] = $job['salary_max'] ?? null;
    $job['vacancies'] = (int)($job['vacancies'] ?? 0);
    
    echo "Mapped jobTitle: " . $job['jobTitle'] . "\n";
    echo "Mapped reportsTo: " . $job['reportsTo'] . "\n";
    echo "Mapped vacancies: " . $job['vacancies'] . "\n";
    echo "Mapped salaryMin: " . $job['salaryMin'] . "\n";
    echo "Mapped salaryMax: " . $job['salaryMax'] . "\n";
    
    if (
        $job['jobTitle'] === 'Test Software Engineer II' &&
        $job['reportsTo'] === 'VP of Engineering' &&
        $job['vacancies'] === 3 &&
        (float)$job['salaryMin'] === 85000.50 &&
        (float)$job['salaryMax'] === 120000.75
    ) {
        echo "✔ All fields match successfully!\n";
    } else {
        echo "❌ Mismatch in fields!\n";
    }
    
    echo "\n--- TESTING UPDATE_JOB FUNCTIONALITY ---\n";
    $updatePayload = [
        'id' => $jobId,
        'title' => 'Updated Engineer II',
        'level' => 'Senior',
        'reportsTo' => 'Director of Engineering',
        'vacancies' => 5,
        'salaryMin' => 90000.00,
        'salaryMax' => 130000.00,
        'status' => 'Active'
    ];
    
    // Simulate settings_api.php update_job SQL execution
    $stmt = $db->prepare("UPDATE jobs SET title = ?, level = ?, reports_to = ?, vacancies = ?, salary_min = ?, salary_max = ?, status = ?, updated_at = NOW() WHERE id = ?");
    $stmt->execute([
        $updatePayload['title'],
        $updatePayload['level'],
        $updatePayload['reportsTo'],
        (int)$updatePayload['vacancies'],
        $updatePayload['salaryMin'],
        $updatePayload['salaryMax'],
        $updatePayload['status'],
        $updatePayload['id']
    ]);
    
    // Fetch and check again
    $stmt = $db->prepare("SELECT * FROM jobs WHERE id = ?");
    $stmt->execute([$jobId]);
    $updatedJob = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "Updated title: " . $updatedJob['title'] . "\n";
    echo "Updated reports_to: " . $updatedJob['reports_to'] . "\n";
    echo "Updated vacancies: " . $updatedJob['vacancies'] . "\n";
    echo "Updated salary_min: " . $updatedJob['salary_min'] . "\n";
    echo "Updated salary_max: " . $updatedJob['salary_max'] . "\n";
    
    if (
        $updatedJob['title'] === 'Updated Engineer II' &&
        $updatedJob['reports_to'] === 'Director of Engineering' &&
        (int)$updatedJob['vacancies'] === 5 &&
        (float)$updatedJob['salary_min'] === 90000.00 &&
        (float)$updatedJob['salary_max'] === 130000.00
    ) {
        echo "✔ All updated fields match successfully!\n";
    } else {
        echo "❌ Mismatch in updated fields!\n";
    }
    
    // Cleanup test data
    $db->exec("DELETE FROM jobs WHERE id = 'JOB-TEST-999'");
    echo "\n✔ Test job cleaned up successfully.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
