<?php
/**
 * Import History API Endpoint
 * Handles operations for attendance import history
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../../config/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $method = $_SERVER['REQUEST_METHOD'];
    $input = json_decode(file_get_contents('php://input'), true);
    
    switch ($method) {
        case 'GET':
            if (isset($_GET['action']) && $_GET['action'] === 'preview') {
                handleGetImportPreview($conn);
            } else {
                handleGetImports($conn);
            }
            break;
        case 'POST':
            handleCreateImport($conn, $input);
            break;
        case 'DELETE':
            handleDeleteImport($conn);
            break;
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}

function handleGetImports($conn) {
    try {
        $query = "SELECT * FROM import_history ORDER BY import_date DESC";
        
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $imports = $stmt->fetchAll();
        
        // Format the response
        $formattedImports = array_map(function($imp) {
            return [
                'id' => $imp['id'],
                'fileName' => $imp['file_name'],
                'fileType' => $imp['file_type'],
                'importDate' => date('M d, Y h:i A', strtotime($imp['import_date'])),
                'importedBy' => $imp['imported_by'],
                'totalRecords' => (int)$imp['total_records'],
                'successful' => (int)$imp['successful'],
                'failed' => (int)$imp['failed'],
                'status' => $imp['status'],
                'filePath' => $imp['file_path'] ?? null,
                'fileSize' => isset($imp['file_size']) ? (int)$imp['file_size'] : 0
            ];
        }, $imports);
        
        echo json_encode(['success' => true, 'data' => $formattedImports]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error fetching imports: ' . $e->getMessage()]);
    }
}

function handleGetImportPreview($conn) {
    try {
        $importId = $_GET['import_id'] ?? '';
        if (empty($importId)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing import ID']);
            return;
        }
        
        $query = "SELECT * FROM import_data WHERE import_id = ? ORDER BY date, employee_name";
        
        $stmt = $conn->prepare($query);
        $stmt->execute([$importId]);
        $data = $stmt->fetchAll();
        
        // Format as headers and rows
        if (count($data) > 0) {
            $headers = ['Employee ID', 'Name', 'Date', 'Time In', 'Time Out', 'Total Hours', 'Status', 'Remarks'];
            $rows = array_map(function($row) {
                return [
                    $row['employee_id'] ?? '',
                    $row['employee_name'] ?? '',
                    $row['date'] ?? '',
                    $row['time_in'] ? substr($row['time_in'], 0, 5) : '',
                    $row['time_out'] ? substr($row['time_out'], 0, 5) : '',
                    $row['total_hours'] ?? '',
                    $row['status'] ?? '',
                    $row['remarks'] ?? ''
                ];
            }, $data);
            
            echo json_encode(['success' => true, 'headers' => $headers, 'rows' => $rows]);
        } else {
            echo json_encode(['success' => true, 'headers' => [], 'rows' => []]);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error fetching preview: ' . $e->getMessage()]);
    }
}

function formatSqlDate($dateStr) {
    if (empty($dateStr)) return null;
    $dateStr = trim($dateStr);
    
    // Check if it's already in YYYY-MM-DD format
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateStr)) {
        return $dateStr;
    }
    
    // Try list of common date formats
    $formats = [
        'Y-m-d',
        'm/d/Y',
        'd/m/Y',
        'd-m-Y',
        'Y/m/d',
        'Y.m.d',
        'd.m.Y',
        'm-d-Y',
        'M j, Y',
        'j M Y',
        'd M Y',
        'Y-m-d H:i:s',
        'Y-m-d H:i',
        'm/d/Y h:i A',
        'd/m/Y h:i A',
        'm/d/Y H:i',
        'd/m/Y H:i'
    ];
    
    foreach ($formats as $format) {
        $d = DateTime::createFromFormat($format, $dateStr);
        if ($d) {
            $errors = DateTime::getLastErrors();
            if ($errors === false || ($errors['warning_count'] == 0 && $errors['error_count'] == 0)) {
                return $d->format('Y-m-d');
            }
        }
    }
    
    // Fallback: try createFromFormat with loose matching or DateTime constructor
    try {
        $d = new DateTime($dateStr);
        return $d->format('Y-m-d');
    } catch (Exception $e) {
        // Continue
    }

    // Try Excel serial date format (if it's a number)
    if (is_numeric($dateStr)) {
        // Excel serial dates start from Jan 1, 1900
        $unixTime = ($dateStr - 25569) * 86400;
        return date('Y-m-d', $unixTime);
    }
    
    return null;
}

function formatSqlTime($timeStr) {
    if (empty($timeStr)) return null;
    $timeStr = trim($timeStr);
    
    // Check if it's already in HH:MM:SS or HH:MM format
    if (preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $timeStr)) {
        return $timeStr;
    }
    
    // Try list of common time formats
    $formats = [
        'H:i:s',
        'H:i',
        'h:i:s A',
        'h:i A',
        'g:i:s A',
        'g:i A',
        'h:i:s a',
        'h:i a',
        'g:i:s a',
        'g:i a',
        'H:i:s.u'
    ];
    
    foreach ($formats as $format) {
        $t = DateTime::createFromFormat($format, $timeStr);
        if ($t) {
            $errors = DateTime::getLastErrors();
            if ($errors === false || ($errors['warning_count'] == 0 && $errors['error_count'] == 0)) {
                return $t->format('H:i:s');
            }
        }
    }
    
    // Try to parse using strtotime
    $time = strtotime($timeStr);
    if ($time !== false) {
        return date('H:i:s', $time);
    }
    
    // Try Excel serial time format (if it's a decimal number between 0 and 1)
    if (is_numeric($timeStr) && $timeStr >= 0 && $timeStr < 1) {
        $seconds = round($timeStr * 86400);
        $hours = floor($seconds / 3600);
        $seconds -= $hours * 3600;
        $minutes = floor($seconds / 60);
        $seconds -= $minutes * 60;
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }
    
    return null;
}

function formatSqlDecimal($val) {
    if ($val === null || $val === '') return null;
    $val = trim($val);
    
    // Strip non-numeric characters except dots and minus
    $cleaned = preg_replace('/[^0-9.-]/', '', $val);
    if (is_numeric($cleaned)) {
        return (float)$cleaned;
    }
    return null;
}

function handleCreateImport($conn, $input) {
    try {
        // Validate required fields
        $required = ['fileName', 'fileType', 'importedBy', 'data'];
        foreach ($required as $field) {
            if (empty($input[$field])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => "Missing required field: $field"]);
                return;
            }
        }
        
        // Use existing ID if passed (e.g. from file upload storage flow), otherwise generate a new one
        $id = $input['importId'] ?? '';
        if (empty($id)) {
            $id = 'IMP-' . date('Y') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
        }
        
        // Start transaction
        $conn->beginTransaction();
        
        try {
            // Filter out completely empty rows first
            $filteredData = [];
            if (isset($input['data']) && is_array($input['data'])) {
                foreach ($input['data'] as $row) {
                    $isEmpty = true;
                    if (is_array($row)) {
                        foreach ($row as $val) {
                            if ($val !== null && trim((string)$val) !== '') {
                                $isEmpty = false;
                                break;
                            }
                        }
                    }
                    if (!$isEmpty) {
                        $filteredData[] = $row;
                    }
                }
            }
            
            // Count records
            $totalRecords = count($filteredData);
            $successful = 0;
            $failed = 0;
            
            // Check if import history record already exists (e.g., inserted during upload.php step)
            $checkQuery = "SELECT id FROM import_history WHERE id = ?";
            $checkStmt = $conn->prepare($checkQuery);
            $checkStmt->execute([$id]);
            $exists = $checkStmt->fetch();
            
            if (!$exists) {
                // Insert import history
                $historyQuery = "INSERT INTO import_history (
                    id, file_name, file_type, imported_by, total_records, successful, failed, status
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                
                $historyStmt = $conn->prepare($historyQuery);
                $historyStmt->execute([
                    $id,
                    $input['fileName'],
                    $input['fileType'],
                    $input['importedBy'],
                    $totalRecords,
                    $totalRecords, // Will update after processing
                    0,
                    'Success'
                ]);
            } else {
                // Update import history with total records count initially
                $updateHistoryQuery = "UPDATE import_history SET total_records = ?, status = 'Success' WHERE id = ?";
                $updateHistoryStmt = $conn->prepare($updateHistoryQuery);
                $updateHistoryStmt->execute([$totalRecords, $id]);
            }
            
            // Fetch all existing employees to map IDs and heal names
            $employeeIds = [];
            $employeeNamesMap = []; // lowercase name => id
            try {
                $empStmt = $conn->prepare("SELECT id, firstname, middlename, surname FROM employees");
                $empStmt->execute();
                while ($r = $empStmt->fetch()) {
                    $employeeIds[$r['id']] = true;
                    
                    // Cache by various name combinations for healing
                    $fname = trim($r['firstname']);
                    $sname = trim($r['surname']);
                    $mname = !empty($r['middlename']) ? trim($r['middlename']) : '';
                    
                    $firstLast = strtolower($fname . ' ' . $sname);
                    $firstMidLast = strtolower(trim($fname . ' ' . $mname . ' ' . $sname));
                    
                    $employeeNamesMap[$firstLast] = $r['id'];
                    $employeeNamesMap[$firstMidLast] = $r['id'];
                }
            } catch (Exception $e) {
                error_log("Failed to fetch employee database mapping: " . $e->getMessage());
            }

            // Insert import data
            $dataQuery = "INSERT INTO import_data (
                import_id, employee_id, employee_name, date, time_in, time_out, total_hours, status, remarks
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $dataStmt = $conn->prepare($dataQuery);
            
            foreach ($filteredData as $row) {
                try {
                    // 1. Employee ID healing & mapping
                    $empId = !empty($row['employee_id']) ? trim($row['employee_id']) : null;
                    $empName = !empty($row['employee_name']) ? trim($row['employee_name']) : '';
                    
                    // If ID is provided but does not exist in the database:
                    if ($empId !== null && !isset($employeeIds[$empId])) {
                        // Check if we can heal by employee name lookup!
                        $lookupName = strtolower($empName);
                        if (!empty($lookupName) && isset($employeeNamesMap[$lookupName])) {
                            $empId = $employeeNamesMap[$lookupName];
                        } else {
                            // No matching employee name found, set employee_id to NULL to prevent foreign key constraint failure
                            $empId = null;
                        }
                    }
                    // If ID is empty but name is provided, try lookup:
                    else if ($empId === null && !empty($empName)) {
                        $lookupName = strtolower($empName);
                        if (isset($employeeNamesMap[$lookupName])) {
                            $empId = $employeeNamesMap[$lookupName];
                        }
                    }
                    
                    // 2. Date conversion
                    $formattedDate = isset($row['date']) ? formatSqlDate($row['date']) : null;
                    
                    // 3. Time conversion
                    $formattedTimeIn = isset($row['time_in']) ? formatSqlTime($row['time_in']) : null;
                    $formattedTimeOut = isset($row['time_out']) ? formatSqlTime($row['time_out']) : null;
                    
                    // 4. Decimal conversion
                    $formattedTotalHours = isset($row['total_hours']) ? formatSqlDecimal($row['total_hours']) : null;

                    $dataStmt->execute([
                        $id,
                        $empId,
                        $empName,
                        $formattedDate,
                        $formattedTimeIn,
                        $formattedTimeOut,
                        $formattedTotalHours,
                        $row['status'] ?? '',
                        $row['remarks'] ?? ''
                    ]);
                    $successful++;
                } catch (Exception $e) {
                    $failed++;
                    error_log("Failed to insert row: " . $e->getMessage() . " | Row: " . json_encode($row));
                }
            }
            
            // Update import history with actual counts
            $status = $failed === 0 ? 'Success' : ($successful === 0 ? 'Failed' : 'Partial');
            $updateQuery = "UPDATE import_history SET successful = ?, failed = ?, status = ? WHERE id = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->execute([$successful, $failed, $status, $id]);
            
            $conn->commit();
            
            echo json_encode([
                'success' => true,
                'id' => $id,
                'message' => 'Import completed successfully',
                'totalRecords' => $totalRecords,
                'successful' => $successful,
                'failed' => $failed,
                'status' => $status
            ]);
            
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error creating import: ' . $e->getMessage()]);
    }
}

function handleDeleteImport($conn) {
    try {
        $id = $_GET['id'] ?? '';
        if (empty($id)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing import ID']);
            return;
        }
        
        // Delete import history (cascade will delete import_data)
        $query = "DELETE FROM import_history WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$id]);
        
        echo json_encode(['success' => true, 'message' => 'Import deleted successfully']);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error deleting import: ' . $e->getMessage()]);
    }
}
?>
