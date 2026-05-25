<?php
/**
 * ResponseFormatter - Standardize all API responses
 * 
 * Features:
 * - Consistent JSON response format
 * - HTTP status code management
 * - Success and error responses
 * - Pagination support
 */
class ResponseFormatter {
    
    /**
     * Send success response
     * 
     * @param mixed $data Data to return
     * @param string|null $message Success message
     * @param int $httpCode HTTP status code (default: 200)
     */
    public static function success($data = null, string $message = null, int $httpCode = 200): void {
        // Clean any previous output
        if (ob_get_level() > 0) {
            ob_clean();
        }
        
        http_response_code($httpCode);
        header('Content-Type: application/json');
        
        $response = [
            'success' => true,
            'timestamp' => date('c')
        ];
        
        if ($data !== null) {
            $response['data'] = $data;
        }
        
        if ($message !== null) {
            $response['message'] = $message;
        }
        
        echo json_encode($response, JSON_PRETTY_PRINT);
        exit;
    }
    
    /**
     * Send error response
     * 
     * @param string $message Error message
     * @param string|null $code Error code
     * @param int $httpCode HTTP status code (default: 400)
     */
    public static function error(string $message, string $code = null, int $httpCode = 400): void {
        // Clean any previous output
        if (ob_get_level() > 0) {
            ob_clean();
        }
        
        http_response_code($httpCode);
        header('Content-Type: application/json');
        
        $response = [
            'success' => false,
            'error' => [
                'message' => $message
            ],
            'timestamp' => date('c')
        ];
        
        if ($code !== null) {
            $response['error']['code'] = $code;
        }
        
        echo json_encode($response, JSON_PRETTY_PRINT);
        exit;
    }
    
    /**
     * Send validation error response
     * 
     * @param array $errors Validation errors
     * @param int $httpCode HTTP status code (default: 422)
     */
    public static function validationError(array $errors, int $httpCode = 422): void {
        http_response_code($httpCode);
        header('Content-Type: application/json');
        
        echo json_encode([
            'success' => false,
            'error' => [
                'message' => 'Validation failed',
                'details' => $errors
            ],
            'timestamp' => date('c')
        ], JSON_PRETTY_PRINT);
        exit;
    }
    
    /**
     * Send unauthorized response (401)
     * 
     * @param string $message Error message
     */
    public static function unauthorized(string $message = 'Unauthorized'): void {
        self::error($message, 'UNAUTHORIZED', 401);
    }
    
    /**
     * Send forbidden response (403)
     * 
     * @param string $message Error message
     */
    public static function forbidden(string $message = 'Forbidden'): void {
        self::error($message, 'FORBIDDEN', 403);
    }
    
    /**
     * Send not found response (404)
     * 
     * @param string $message Error message
     */
    public static function notFound(string $message = 'Resource not found'): void {
        self::error($message, 'NOT_FOUND', 404);
    }
    
    /**
     * Send server error response (500)
     * 
     * @param string $message Error message
     * @param string|null $code Error code
     */
    public static function serverError(string $message = 'Internal server error', string $code = null): void {
        self::error($message, $code, 500);
    }
    
    /**
     * Send paginated response
     * 
     * @param array $data Data array
     * @param int $total Total number of records
     * @param int $page Current page
     * @param int $perPage Records per page
     * @param string|null $message Success message
     */
    public static function paginated(array $data, int $total, int $page, int $perPage, string $message = null): void {
        http_response_code(200);
        header('Content-Type: application/json');
        
        $totalPages = ceil($total / $perPage);
        
        $response = [
            'success' => true,
            'data' => $data,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'per_page' => $perPage,
                'total_pages' => $totalPages,
                'has_next' => $page < $totalPages,
                'has_prev' => $page > 1
            ],
            'timestamp' => date('c')
        ];
        
        if ($message !== null) {
            $response['message'] = $message;
        }
        
        echo json_encode($response, JSON_PRETTY_PRINT);
        exit;
    }
    
    /**
     * Send method not allowed response (405)
     * 
     * @param array $allowedMethods Allowed HTTP methods
     */
    public static function methodNotAllowed(array $allowedMethods = []): void {
        http_response_code(405);
        
        if (!empty($allowedMethods)) {
            header('Allow: ' . implode(', ', $allowedMethods));
        }
        
        header('Content-Type: application/json');
        
        echo json_encode([
            'success' => false,
            'error' => [
                'message' => 'Method not allowed',
                'code' => 'METHOD_NOT_ALLOWED',
                'allowed_methods' => $allowedMethods
            ],
            'timestamp' => date('c')
        ], JSON_PRETTY_PRINT);
        exit;
    }
    
    /**
     * Send created response (201)
     * 
     * @param mixed $data Created resource data
     * @param string|null $message Success message
     */
    public static function created($data = null, string $message = 'Resource created successfully'): void {
        self::success($data, $message, 201);
    }
    
    /**
     * Send no content response (204)
     */
    public static function noContent(): void {
        http_response_code(204);
        exit;
    }
}
?>
