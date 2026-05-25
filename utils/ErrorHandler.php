<?php
/**
 * ErrorHandler - Unified error handling with logging
 * 
 * Features:
 * - User-friendly error messages
 * - Detailed error logging
 * - Error code generation
 * - Database error handling
 * - Validation error formatting
 */
class ErrorHandler {
    private static $logFile = __DIR__ . '/../logs/errors.log';
    private static $adminEmail = 'admin@3me.com';
    
    /**
     * Handle any error or exception
     * 
     * @param \Throwable $error Error or exception
     * @param array $context Additional context
     * @return array Error response array
     */
    public static function handle(\Throwable $error, array $context = []): array {
        $code = self::generateErrorCode($context['module'] ?? 'SYSTEM');
        $message = self::getUserMessage($error);
        
        // Log error with full details
        self::log($error->getMessage(), 'error', array_merge($context, [
            'file' => $error->getFile(),
            'line' => $error->getLine(),
            'trace' => $error->getTraceAsString(),
            'error_code' => $code
        ]));
        
        // Send admin notification for critical errors
        if (isset($context['critical']) && $context['critical']) {
            self::notifyAdmin($error->getMessage(), $context);
        }
        
        return [
            'success' => false,
            'message' => $message,
            'code' => $code
        ];
    }
    
    /**
     * Handle database errors specifically
     * 
     * @param \PDOException $error PDO exception
     * @param array $context Additional context
     * @return array Error response array
     */
    public static function handleDatabaseError(\PDOException $error, array $context = []): array {
        $context['module'] = $context['module'] ?? 'DATABASE';
        $context['error_type'] = 'database';
        
        return self::handle($error, $context);
    }
    
    /**
     * Handle validation errors
     * 
     * @param array $errors Validation errors
     * @return array Error response array
     */
    public static function handleValidationError(array $errors): array {
        self::log('Validation failed', 'warning', ['errors' => $errors]);
        
        return [
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $errors
        ];
    }
    
    /**
     * Handle session errors
     * 
     * @return array Error response array
     */
    public static function handleSessionError(): array {
        self::log('Session expired or invalid', 'info');
        
        return [
            'success' => false,
            'message' => 'Your session has expired. Please log in again.',
            'code' => 'SESSION_EXPIRED'
        ];
    }
    
    /**
     * Log error to file
     * 
     * @param string $message Error message
     * @param string $level Log level (debug, info, warning, error, critical)
     * @param array $context Additional context
     */
    public static function log(string $message, string $level = 'error', array $context = []): void {
        // Create logs directory if it doesn't exist
        $logDir = dirname(self::$logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? json_encode($context, JSON_PRETTY_PRINT) : '';
        
        $logMessage = sprintf(
            "[%s] [%s] %s\n%s\n%s\n",
            $timestamp,
            strtoupper($level),
            $message,
            $contextStr,
            str_repeat('-', 80)
        );
        
        error_log($logMessage, 3, self::$logFile);
        
        // Also log to PHP error log for critical errors
        if ($level === 'critical') {
            error_log("CRITICAL ERROR: $message");
        }
    }
    
    /**
     * Send email notification to admin
     * 
     * @param string $message Error message
     * @param array $context Additional context
     */
    public static function notifyAdmin(string $message, array $context = []): void {
        $subject = '3ME HR System - Critical Error';
        $body = "A critical error occurred in the 3ME HR Management System:\n\n";
        $body .= "Error: $message\n\n";
        $body .= "Context:\n" . print_r($context, true) . "\n\n";
        $body .= "Time: " . date('Y-m-d H:i:s') . "\n";
        $body .= "Server: " . ($_SERVER['SERVER_NAME'] ?? 'unknown') . "\n";
        
        // In production, use proper email sending
        // For now, just log it
        self::log("Admin notification: $message", 'critical', $context);
        
        // Uncomment to enable email notifications
        // mail(self::$adminEmail, $subject, $body);
    }
    
    /**
     * Convert technical error to user-friendly message
     * 
     * @param \Throwable $error Error or exception
     * @return string User-friendly message
     */
    public static function getUserMessage(\Throwable $error): string {
        $message = $error->getMessage();
        
        // Database errors
        if (strpos($message, 'Duplicate entry') !== false) {
            if (strpos($message, 'email') !== false) {
                return 'This email address is already registered in the system.';
            }
            if (strpos($message, 'employee_number') !== false) {
                return 'This employee number already exists.';
            }
            return 'This record already exists in the system.';
        }
        
        if (strpos($message, 'foreign key constraint') !== false || 
            strpos($message, 'Cannot delete or update a parent row') !== false) {
            return 'Cannot delete this record because it is being used elsewhere in the system.';
        }
        
        if (strpos($message, 'Connection refused') !== false || 
            strpos($message, 'SQLSTATE[HY000]') !== false) {
            return 'Unable to connect to the database. Please try again later or contact support.';
        }
        
        if (strpos($message, 'Unknown column') !== false) {
            return 'A system error occurred. Please contact support.';
        }
        
        // File errors
        if (strpos($message, 'file') !== false || strpos($message, 'upload') !== false) {
            return 'There was a problem with the file upload. Please try again.';
        }
        
        // Permission errors
        if (strpos($message, 'permission') !== false || strpos($message, 'denied') !== false) {
            return 'You do not have permission to perform this action.';
        }
        
        // Network errors
        if (strpos($message, 'timeout') !== false || strpos($message, 'timed out') !== false) {
            return 'The operation timed out. Please try again.';
        }
        
        // Default user-friendly message
        return 'An error occurred while processing your request. Please try again or contact support if the problem persists.';
    }
    
    /**
     * Generate unique error code
     * 
     * @param string $module Module name
     * @return string Error code
     */
    private static function generateErrorCode(string $module): string {
        return 'ERR-' . strtoupper($module) . '-' . date('YmdHis') . '-' . substr(uniqid(), -4);
    }
    
    /**
     * Get last error from log file
     * 
     * @param int $lines Number of lines to retrieve
     * @return string Last error lines
     */
    public static function getLastError(int $lines = 10): string {
        if (!file_exists(self::$logFile)) {
            return 'No errors logged';
        }
        
        $file = file(self::$logFile);
        $lastLines = array_slice($file, -$lines);
        
        return implode('', $lastLines);
    }
    
    /**
     * Clear error log file
     */
    public static function clearLog(): void {
        if (file_exists(self::$logFile)) {
            file_put_contents(self::$logFile, '');
        }
    }
}
?>
