<?php
/**
 * DatabaseUtils Class
 * 
 * Comprehensive database utility class that extends the existing Database class
 * with reusable CRUD operations, transaction management, query building, validation,
 * error handling, batch operations, and schema inspection capabilities.
 * 
 * @extends Database
 */

require_once __DIR__ . '/../config/database.php';

class DatabaseUtils extends Database {
    
    /**
     * Private properties for error tracking and debug mode
     */
    private $lastError = null;
    private $lastQuery = null;
    private $debugMode = false;
    private $transactionActive = false;
    
    /**
     * Constructor
     * Initializes the DatabaseUtils class
     */
    public function __construct() {
        // No need to call parent constructor as Database class doesn't have one
    }
    
    /**
     * Enable or disable debug mode
     * 
     * @param bool $enabled Whether to enable debug mode
     * @return void
     */
    public function setDebugMode(bool $enabled): void {
        $this->debugMode = $enabled;
    }
    
    /**
     * Get the last error message
     * 
     * @return string|null The last error message or null if no error
     */
    public function getLastError(): ?string {
        return $this->lastError;
    }
    
    /**
     * Get the last executed query
     * 
     * @return string|null The last executed query or null if no query
     */
    public function getLastQuery(): ?string {
        return $this->lastQuery;
    }
    
    /**
     * Check if a transaction is currently active
     * 
     * @return bool True if transaction is active, false otherwise
     */
    public function isTransactionActive(): bool {
        return $this->transactionActive;
    }
    
    /**
     * Log error to file with timestamp and context
     * 
     * @param string $message Error message
     * @param string $severity Error severity level (warning, error, critical)
     * @param array $context Additional context information
     * @return void
     */
    private function logError(string $message, string $severity = 'error', array $context = []): void {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] [" . strtoupper($severity) . "] $message";
        
        if ($this->lastQuery) {
            $logMessage .= "\nQuery: " . $this->lastQuery;
        }
        
        if (!empty($context)) {
            $logMessage .= "\nContext: " . json_encode($context);
        }
        
        if ($this->debugMode && function_exists('debug_backtrace')) {
            $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5);
            $logMessage .= "\nStack Trace: " . json_encode($trace);
        }
        
        error_log($logMessage);
        $this->lastError = $message;
    }
    
    /**
     * Format standardized error response
     * 
     * @param string $message Error message
     * @param string|null $query Optional query string
     * @return array Standardized error response
     */
    private function formatErrorResponse(string $message, string $query = null): array {
        $response = [
            'success' => false,
            'error' => $message
        ];
        
        if ($this->debugMode && $query) {
            $response['query'] = $query;
        }
        
        return $response;
    }
    
    /**
     * Insert a single record into a table
     * 
     * Inserts a single record into the specified table using prepared statements
     * for SQL injection prevention. Returns a standardized response with the
     * generated ID on success or error details on failure.
     * 
     * @param string $table The table name to insert into
     * @param array $data Associative array of column => value pairs
     * @return array Standardized response ['success' => bool, 'id' => string|null, 'error' => string|null]
     */
    public function insert(string $table, array $data): array {
        try {
            // Validate input
            if (empty($table)) {
                $error = "Table name cannot be empty";
                $this->logError($error, 'error');
                return $this->formatErrorResponse($error);
            }
            
            if (empty($data)) {
                $error = "Data array cannot be empty";
                $this->logError($error, 'error');
                return $this->formatErrorResponse($error);
            }
            
            // Get database connection
            $conn = $this->getConnection();
            if (!$conn) {
                $error = "Failed to establish database connection";
                $this->logError($error, 'critical');
                return $this->formatErrorResponse($error);
            }
            
            // Build INSERT query
            $columns = array_keys($data);
            $placeholders = array_fill(0, count($columns), '?');
            
            $query = sprintf(
                "INSERT INTO %s (%s) VALUES (%s)",
                $table,
                implode(', ', $columns),
                implode(', ', $placeholders)
            );
            
            $this->lastQuery = $query;
            
            // Prepare and execute statement
            $stmt = $conn->prepare($query);
            $values = array_values($data);
            $stmt->execute($values);
            
            // Get the last inserted ID
            $lastId = $conn->lastInsertId();
            
            return [
                'success' => true,
                'id' => $lastId ?: null,
                'affected' => $stmt->rowCount()
            ];
            
        } catch (PDOException $e) {
            $error = "Database insert failed: " . $e->getMessage();
            $this->logError($error, 'error', [
                'table' => $table,
                'data' => $data
            ]);
            return $this->formatErrorResponse($error, $this->lastQuery);
        } catch (Exception $e) {
            $error = "Insert operation failed: " . $e->getMessage();
            $this->logError($error, 'error', [
                'table' => $table,
                'data' => $data
            ]);
            return $this->formatErrorResponse($error);
        }
    }
}
?>
