<?php
// Simulate frontend fuzzy mapping and post it to api/attendance/imports.php

// Mock server environment to bypass auto-run
$_SERVER['REQUEST_METHOD'] = 'CLI';

require_once __DIR__ . '/../config/database.php';

// We will load and parse the CSV
$csvFile = __DIR__ . '/sample_attendance.csv';
$lines = file($csvFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$headers = str_getcsv(array_shift($lines));
$rows = [];
foreach ($lines as $line) {
    $rows[] = str_getcsv($line);
}

echo "Headers found: " . implode(', ', $headers) . "\n";

// Map columns fuzzy matching (exactly like frontend processImportWithMappings)
$employeeIdIdx = -1;
$employeeNameIdx = -1;
$dateIdx = -1;
$timeInIdx = -1;
$timeOutIdx = -1;
$totalHoursIdx = -1;
$statusIdx = -1;
$remarksIdx = -1;

foreach ($headers as $index => $header) {
    $h = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $header));
    
    // Match Employee ID
    if (in_array($h, ['employeeid', 'empid', 'employeeid', 'empid', 'employee_id', 'emp_id', 'id', 'employeeno', 'employeenumber', 'empno', 'staffid'])) {
        $employeeIdIdx = $index;
    }
    // Match Employee Name
    elseif (in_array($h, ['employeename', 'employee_name', 'name', 'empname', 'fullname', 'full_name', 'staffname'])) {
        $employeeNameIdx = $index;
    }
    // Match Date
    elseif (in_array($h, ['date', 'attendancedate', 'logdate', 'workdate'])) {
        $dateIdx = $index;
    }
    // Match Time In
    elseif (in_array($h, ['timein', 'time_in', 'clockin', 'in', 'checkin', 'time_in_am', 'timeinam', 'amin', 'in_time', 'intime'])) {
        $timeInIdx = $index;
    }
    // Match Time Out
    elseif (in_array($h, ['timeout', 'time_out', 'clockout', 'out', 'checkout', 'time_out_pm', 'timeoutpm', 'pmout', 'out_time', 'outtime'])) {
        $timeOutIdx = $index;
    }
    // Match Total Hours
    elseif (in_array($h, ['totalhours', 'total_hours', 'hours', 'workhours', 'workedhours', 'hrs', 'totalhrs', 'total_hrs'])) {
        $totalHoursIdx = $index;
    }
    // Match Status
    elseif (in_array($h, ['status', 'attendancestatus', 'type', 'remarkstatus'])) {
        $statusIdx = $index;
    }
    // Match Remarks
    elseif (in_array($h, ['remarks', 'remark', 'notes', 'note', 'comment', 'comments'])) {
        $remarksIdx = $index;
    }
}

// Fallback logic
if ($employeeIdIdx === -1) {
    foreach ($headers as $index => $h) {
        $s = strtolower($h);
        if (strpos($s, 'id') !== false || strpos($s, 'no') !== false || strpos($s, 'number') !== false) {
            $employeeIdIdx = $index;
            break;
        }
    }
    if ($employeeIdIdx === -1) $employeeIdIdx = 0;
}
if ($employeeNameIdx === -1) {
    foreach ($headers as $index => $h) {
        $s = strtolower($h);
        if (strpos($s, 'name') !== false || strpos($s, 'employee') !== false || strpos($s, 'staff') !== false) {
            $employeeNameIdx = $index;
            break;
        }
    }
    if ($employeeNameIdx === -1) $employeeNameIdx = 1;
}
if ($dateIdx === -1) {
    foreach ($headers as $index => $h) {
        $s = strtolower($h);
        if (strpos($s, 'date') !== false || strpos($s, 'day') !== false) {
            $dateIdx = $index;
            break;
        }
    }
    if ($dateIdx === -1) $dateIdx = 2;
}
if ($timeInIdx === -1) {
    foreach ($headers as $index => $h) {
        $s = strtolower($h);
        if (strpos($s, 'in') !== false || strpos($s, 'start') !== false || strpos($s, 'entry') !== false) {
            $timeInIdx = $index;
            break;
        }
    }
    if ($timeInIdx === -1) $timeInIdx = 3;
}
if ($timeOutIdx === -1) {
    foreach ($headers as $index => $h) {
        $s = strtolower($h);
        if (strpos($s, 'out') !== false || strpos($s, 'end') !== false || strpos($s, 'exit') !== false) {
            $timeOutIdx = $index;
            break;
        }
    }
    if ($timeOutIdx === -1) $timeOutIdx = 4;
}
if ($totalHoursIdx === -1) {
    foreach ($headers as $index => $h) {
        $s = strtolower($h);
        if (strpos($s, 'hour') !== false || strpos($s, 'time') !== false || strpos($s, 'duration') !== false || strpos($s, 'work') !== false) {
            $totalHoursIdx = $index;
            break;
        }
    }
    if ($totalHoursIdx === -1) $totalHoursIdx = 5;
}
if ($statusIdx === -1) {
    foreach ($headers as $index => $h) {
        $s = strtolower($h);
        if (strpos($s, 'status') !== false || strpos($s, 'type') !== false) {
            $statusIdx = $index;
            break;
        }
    }
    if ($statusIdx === -1) $statusIdx = 6;
}
if ($remarksIdx === -1) {
    foreach ($headers as $index => $h) {
        $s = strtolower($h);
        if (strpos($s, 'remark') !== false || strpos($s, 'note') !== false || strpos($s, 'comment') !== false) {
            $remarksIdx = $index;
            break;
        }
    }
    if ($remarksIdx === -1) $remarksIdx = 7;
}

echo "Mapped Indices:\n";
echo "employeeIdIdx: $employeeIdIdx\n";
echo "employeeNameIdx: $employeeNameIdx\n";
echo "dateIdx: $dateIdx\n";
echo "timeInIdx: $timeInIdx\n";
echo "timeOutIdx: $timeOutIdx\n";
echo "totalHoursIdx: $totalHoursIdx\n";
echo "statusIdx: $statusIdx\n";
echo "remarksIdx: $remarksIdx\n\n";

$mappedRows = [];
foreach ($rows as $row) {
    $mappedRows[] = [
        'employee_id' => isset($row[$employeeIdIdx]) ? (string)$row[$employeeIdIdx] : '',
        'employee_name' => isset($row[$employeeNameIdx]) ? (string)$row[$employeeNameIdx] : '',
        'date' => isset($row[$dateIdx]) ? (string)$row[$dateIdx] : '',
        'time_in' => isset($row[$timeInIdx]) ? (string)$row[$timeInIdx] : '',
        'time_out' => isset($row[$timeOutIdx]) ? (string)$row[$timeOutIdx] : '',
        'total_hours' => isset($row[$totalHoursIdx]) ? (string)$row[$totalHoursIdx] : '',
        'status' => isset($row[$statusIdx]) ? (string)$row[$statusIdx] : '',
        'remarks' => isset($row[$remarksIdx]) ? (string)$row[$remarksIdx] : ''
    ];
}

// Mimic POST request payload
$testInput = [
    'importId' => 'IMP-TEST-' . rand(100, 999),
    'fileName' => 'sample_attendance.csv',
    'fileType' => 'CSV',
    'importedBy' => 'Test Runner',
    'data' => $mappedRows
];

ob_start();
chdir(__DIR__ . '/../api/attendance');
require_once 'imports.php';

$database = new Database();
$conn = $database->getConnection();
handleCreateImport($conn, $testInput);
$output = ob_get_clean();

echo "API Response:\n";
echo $output . "\n";
