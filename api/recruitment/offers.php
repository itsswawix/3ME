<?php
/**
 * Job Offers API Endpoint
 * Handles CRUD operations for job offers
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
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
            handleGetOffers($conn);
            break;
        case 'POST':
            handleCreateOffer($conn, $input);
            break;
        case 'PUT':
            handleUpdateOffer($conn, $input);
            break;
        case 'DELETE':
            handleDeleteOffer($conn);
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

function handleGetOffers($conn) {
    try {
        $query = "SELECT jo.*, a.firstname, a.middlename, a.surname, a.suffix, a.email, a.avatar, a.color,
                         CONCAT(a.firstname, ' ', 
                                CASE WHEN a.middlename IS NOT NULL AND a.middlename != '' 
                                     THEN CONCAT(LEFT(a.middlename, 1), '. ') 
                                     ELSE '' END,
                                a.surname,
                                CASE WHEN a.suffix IS NOT NULL AND a.suffix != '' 
                                     THEN CONCAT(' ', a.suffix) 
                                     ELSE '' END) as applicant_name
                  FROM job_offers jo 
                  LEFT JOIN applicants a ON jo.applicant_id = a.id 
                  ORDER BY jo.created_at DESC";
        
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $offers = $stmt->fetchAll();
        
        echo json_encode(['success' => true, 'data' => $offers]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error fetching offers: ' . $e->getMessage()]);
    }
}

function handleCreateOffer($conn, $input) {
    try {
        // Validate required fields
        $required = ['applicant_id', 'position', 'salary_offer', 'hire_date', 'offer_status'];
        foreach ($required as $field) {
            if (!isset($input[$field]) || $input[$field] === '') {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => "Missing required field: $field"]);
                return;
            }
        }
        
        // Generate unique ID
        $id = 'OFFER-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        // Generate employee ID if offer is accepted
        $employeeId = null;
        if ($input['offer_status'] === 'Accepted') {
            $employeeId = 'EMP-' . date('Y') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
        }
        
        $conn->beginTransaction();
        
        try {
            $query = "INSERT INTO job_offers (
                id, applicant_id, position, salary_offer, contract_terms,
                hire_date, offer_status, employee_id
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $conn->prepare($query);
            $stmt->execute([
                $id,
                $input['applicant_id'],
                $input['position'],
                $input['salary_offer'],
                $input['contract_terms'] ?? '',
                $input['hire_date'],
                $input['offer_status'],
                $employeeId
            ]);
            
            // If offer is accepted, update applicant status to Hired and create onboarding record
            if ($input['offer_status'] === 'Accepted') {
                $updateApplicant = "UPDATE applicants SET application_status = 'Hired' WHERE id = ?";
                $stmt2 = $conn->prepare($updateApplicant);
                $stmt2->execute([$input['applicant_id']]);
                
                // Create onboarding record
                $onboardingId = createOnboardingFromOffer($conn, $input, $employeeId);
            }
            
            $conn->commit();
            
            $response = [
                'success' => true, 
                'id' => $id, 
                'employee_id' => $employeeId,
                'message' => 'Job offer created successfully'
            ];
            
            if (isset($onboardingId)) {
                $response['onboarding_id'] = $onboardingId;
                $response['message'] .= ' and onboarding record created';
            }
            
            echo json_encode($response);
            
        } catch (Exception $e) {
            $conn->rollback();
            throw $e;
        }
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error creating offer: ' . $e->getMessage()]);
    }
}

function handleUpdateOffer($conn, $input) {
    try {
        if (empty($input['id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing offer ID']);
            return;
        }
        
        $conn->beginTransaction();
        
        try {
            // Get current offer status
            $currentQuery = "SELECT offer_status, applicant_id FROM job_offers WHERE id = ?";
            $currentStmt = $conn->prepare($currentQuery);
            $currentStmt->execute([$input['id']]);
            $currentOffer = $currentStmt->fetch();
            
            // Generate employee ID if status changed to Accepted
            $employeeId = $input['employee_id'] ?? null;
            if ($input['offer_status'] === 'Accepted' && $currentOffer['offer_status'] !== 'Accepted' && !$employeeId) {
                $employeeId = 'EMP-' . date('Y') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
            }
            
            $query = "UPDATE job_offers SET 
                        position = ?, salary_offer = ?, contract_terms = ?, hire_date = ?,
                        offer_status = ?, employee_id = ?, updated_at = CURRENT_TIMESTAMP
                      WHERE id = ?";
            
            $stmt = $conn->prepare($query);
            $stmt->execute([
                $input['position'],
                $input['salary_offer'],
                $input['contract_terms'] ?? '',
                $input['hire_date'],
                $input['offer_status'],
                $employeeId,
                $input['id']
            ]);
            
            // Update applicant status based on offer status
            if ($input['offer_status'] === 'Accepted' && $currentOffer['offer_status'] !== 'Accepted') {
                // Newly accepted offer
                $updateApplicant = "UPDATE applicants SET application_status = 'Hired' WHERE id = ?";
                $stmt2 = $conn->prepare($updateApplicant);
                $stmt2->execute([$currentOffer['applicant_id']]);
                
                // Create onboarding record if not already created
                $checkOnboarding = "SELECT id FROM onboarding_records WHERE employee_id = ?";
                $checkStmt = $conn->prepare($checkOnboarding);
                $checkStmt->execute([$employeeId]);
                if (!$checkStmt->fetch()) {
                    $onboardingId = createOnboardingFromOffer($conn, $input, $employeeId, $currentOffer['applicant_id']);
                }
            } elseif ($input['offer_status'] === 'Declined' && $currentOffer['offer_status'] === 'Accepted') {
                // If offer was accepted but now declined, revert applicant status
                $updateApplicant = "UPDATE applicants SET application_status = 'Under Review' WHERE id = ?";
                $stmt2 = $conn->prepare($updateApplicant);
                $stmt2->execute([$currentOffer['applicant_id']]);
            }
            
            $conn->commit();
            
            $response = [
                'success' => true, 
                'employee_id' => $employeeId,
                'message' => 'Job offer updated successfully'
            ];
            
            if (isset($onboardingId)) {
                $response['onboarding_id'] = $onboardingId;
                $response['message'] .= ' and onboarding record created';
            }
            
            echo json_encode($response);
            
        } catch (Exception $e) {
            $conn->rollback();
            throw $e;
        }
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error updating offer: ' . $e->getMessage()]);
    }
}

function handleDeleteOffer($conn) {
    try {
        $id = $_GET['id'] ?? '';
        if (empty($id)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing offer ID']);
            return;
        }
        
        $conn->beginTransaction();
        
        try {
            // Get applicant ID before deleting
            $getApplicantQuery = "SELECT applicant_id FROM job_offers WHERE id = ?";
            $getApplicantStmt = $conn->prepare($getApplicantQuery);
            $getApplicantStmt->execute([$id]);
            $offer = $getApplicantStmt->fetch();
            
            // Delete the offer
            $query = "DELETE FROM job_offers WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$id]);
            
            // Revert applicant status if needed
            if ($offer) {
                $updateApplicant = "UPDATE applicants SET application_status = 'Under Review' WHERE id = ?";
                $stmt2 = $conn->prepare($updateApplicant);
                $stmt2->execute([$offer['applicant_id']]);
            }
            
            $conn->commit();
            
            echo json_encode(['success' => true, 'message' => 'Job offer deleted successfully']);
            
        } catch (Exception $e) {
            $conn->rollback();
            throw $e;
        }
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error deleting offer: ' . $e->getMessage()]);
    }
}

/**
 * Create onboarding record from accepted job offer
 */
function createOnboardingFromOffer($conn, $offerData, $employeeId, $applicantId = null) {
    try {
        // Get applicant details
        if (!$applicantId) {
            $applicantId = $offerData['applicant_id'];
        }
        
        $applicantQuery = "SELECT a.*, c.name as company_name, d.name as department_name, j.title as job
                          FROM applicants a
                          LEFT JOIN companies c ON a.company_id = c.id
                          LEFT JOIN departments d ON a.department_id = d.id
                          LEFT JOIN jobs j ON a.job_id = j.id
                          WHERE a.id = ?";
        $applicantStmt = $conn->prepare($applicantQuery);
        $applicantStmt->execute([$applicantId]);
        $applicant = $applicantStmt->fetch();
        
        if (!$applicant) {
            error_log("Applicant not found for onboarding creation: " . $applicantId);
            return null;
        }
        
        // Generate onboarding ID
        $onboardingId = 'ONB-' . date('Y') . '-' . str_pad(rand(1, 9999), 3, '0', STR_PAD_LEFT);
        
        // Build employee name with suffix
        $employeeName = trim($applicant['firstname'] . ' ' . 
                            ($applicant['middlename'] ? $applicant['middlename'] . ' ' : '') . 
                            $applicant['surname'] .
                            ($applicant['suffix'] ? ' ' . $applicant['suffix'] : ''));
        
        // Default onboarding tasks
        $defaultTasks = [
            ['text' => 'Complete employment forms', 'completed' => false],
            ['text' => 'IT equipment setup', 'completed' => false],
            ['text' => 'Office tour and introductions', 'completed' => false],
            ['text' => 'HR orientation session', 'completed' => false],
            ['text' => 'Department training', 'completed' => false],
            ['text' => 'System access setup', 'completed' => false]
        ];
        
        // Use IDs from applicant record
        $companyId = $applicant['company_id'];
        $departmentId = $applicant['department_id'];
        $jobId = $applicant['job_id'];
        
        // Create onboarding record with IDs
        $insertQuery = "INSERT INTO onboarding_records (
            id, employee_id, employee_name, employee_email, job_id, department_id, company_id,
            start_date, progress, tasks, notes, avatar, color
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->execute([
            $onboardingId,
            $employeeId,
            $employeeName,
            $applicant['email'],
            $jobId,
            $departmentId,
            $companyId,
            $offerData['hire_date'],
            'Not Started',
            json_encode($defaultTasks),
            'Automatically created from accepted job offer',
            $applicant['avatar'] ?? strtoupper(substr($employeeName, 0, 2)),
            $applicant['color'] ?? 'linear-gradient(145deg, #6366f1, #a78bfa)'
        ]);
        
        error_log("Onboarding record created: " . $onboardingId . " for employee: " . $employeeId . " with company_id: " . $companyId . ", department_id: " . $departmentId . ", job_id: " . $jobId);
        
        return $onboardingId;
        
    } catch (Exception $e) {
        error_log("Error creating onboarding from offer: " . $e->getMessage());
        return null;
    }
}
?>