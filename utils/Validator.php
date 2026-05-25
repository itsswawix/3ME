<?php
/**
 * Validator - Comprehensive data validation
 * 
 * Features:
 * - Rule-based validation
 * - Multiple validation rules per field
 * - Database validation (unique, exists)
 * - Custom error messages
 * - Easy to use and extend
 */

require_once __DIR__ . '/DatabaseUtils.php';

class Validator {
    private $data = [];
    private $rules = [];
    private $errors = [];
    private $db = null;
    
    /**
     * Constructor
     * 
     * @param array $data Data to validate
     */
    public function __construct(array $data) {
        $this->data = $data;
        $this->db = new DatabaseUtils();
    }
    
    /**
     * Set validation rules
     * 
     * @param array $rules Validation rules
     * @return self
     */
    public function rules(array $rules): self {
        $this->rules = $rules;
        return $this;
    }
    
    /**
     * Execute validation
     * 
     * @return bool True if valid
     */
    public function validate(): bool {
        $this->errors = [];
        
        foreach ($this->rules as $field => $ruleString) {
            $rules = explode('|', $ruleString);
            $value = $this->data[$field] ?? null;
            
            foreach ($rules as $rule) {
                $this->validateRule($field, $value, $rule);
            }
        }
        
        return empty($this->errors);
    }
    
    /**
     * Validate single rule
     * 
     * @param string $field Field name
     * @param mixed $value Field value
     * @param string $rule Rule string
     */
    private function validateRule(string $field, $value, string $rule): void {
        // Parse rule and parameters
        $parts = explode(':', $rule);
        $ruleName = $parts[0];
        $params = isset($parts[1]) ? explode(',', $parts[1]) : [];
        
        // Skip validation if field is optional and empty
        if ($ruleName !== 'required' && empty($value)) {
            return;
        }
        
        // Execute validation
        $method = 'validate' . ucfirst($ruleName);
        
        if (method_exists($this, $method)) {
            $isValid = $this->$method($value, ...$params);
            
            if (!$isValid) {
                $this->addError($field, $this->getErrorMessage($field, $ruleName, $params));
            }
        }
    }
    
    /**
     * Add error message
     * 
     * @param string $field Field name
     * @param string $message Error message
     */
    private function addError(string $field, string $message): void {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $message;
    }
    
    /**
     * Get error message for rule
     * 
     * @param string $field Field name
     * @param string $rule Rule name
     * @param array $params Rule parameters
     * @return string Error message
     */
    private function getErrorMessage(string $field, string $rule, array $params): string {
        $fieldName = ucfirst(str_replace('_', ' ', $field));
        
        $messages = [
            'required' => "$fieldName is required",
            'email' => "$fieldName must be a valid email address",
            'phone' => "$fieldName must be a valid phone number",
            'numeric' => "$fieldName must be a number",
            'integer' => "$fieldName must be an integer",
            'min' => "$fieldName must be at least {$params[0]}",
            'max' => "$fieldName must not exceed {$params[0]}",
            'minLength' => "$fieldName must be at least {$params[0]} characters",
            'maxLength' => "$fieldName must not exceed {$params[0]} characters",
            'in' => "$fieldName must be one of: " . implode(', ', $params),
            'unique' => "$fieldName already exists",
            'exists' => "$fieldName does not exist",
            'date' => "$fieldName must be a valid date",
            'dateFormat' => "$fieldName must be in format {$params[0]}",
            'after' => "$fieldName must be after {$params[0]}",
            'before' => "$fieldName must be before {$params[0]}",
            'alpha' => "$fieldName must contain only letters",
            'alphaNum' => "$fieldName must contain only letters and numbers",
            'url' => "$fieldName must be a valid URL"
        ];
        
        return $messages[$rule] ?? "$fieldName is invalid";
    }
    
    /**
     * Get validation errors
     * 
     * @return array Errors
     */
    public function getErrors(): array {
        return $this->errors;
    }
    
    /**
     * Get first error message
     * 
     * @return string|null First error
     */
    public function getFirstError(): ?string {
        if (empty($this->errors)) {
            return null;
        }
        
        $firstField = array_key_first($this->errors);
        return $this->errors[$firstField][0] ?? null;
    }
    
    // ========================================================================
    // VALIDATION RULES
    // ========================================================================
    
    /**
     * Validate required field
     */
    private function validateRequired($value): bool {
        if (is_null($value)) {
            return false;
        }
        if (is_string($value) && trim($value) === '') {
            return false;
        }
        return true;
    }
    
    /**
     * Validate email format
     */
    private function validateEmail($value): bool {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Validate phone number
     */
    private function validatePhone($value): bool {
        // Remove all non-digit characters
        $digits = preg_replace('/\D/', '', $value);
        // Check if it has at least 10 digits
        return strlen($digits) >= 10;
    }
    
    /**
     * Validate numeric value
     */
    private function validateNumeric($value): bool {
        return is_numeric($value);
    }
    
    /**
     * Validate integer value
     */
    private function validateInteger($value): bool {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }
    
    /**
     * Validate minimum value
     */
    private function validateMin($value, $min): bool {
        return $value >= $min;
    }
    
    /**
     * Validate maximum value
     */
    private function validateMax($value, $max): bool {
        return $value <= $max;
    }
    
    /**
     * Validate minimum length
     */
    private function validateMinLength($value, $min): bool {
        return strlen($value) >= $min;
    }
    
    /**
     * Validate maximum length
     */
    private function validateMaxLength($value, $max): bool {
        return strlen($value) <= $max;
    }
    
    /**
     * Validate value is in array
     */
    private function validateIn($value, ...$options): bool {
        return in_array($value, $options);
    }
    
    /**
     * Validate unique in database
     * Format: unique:table,column,exceptId
     */
    private function validateUnique($value, $table, $column, $exceptId = null): bool {
        try {
            $conditions = [$column => $value];
            $result = $this->db->findOne($table, $conditions);
            
            // If no record found, value is unique
            if (!$result) {
                return true;
            }
            
            // If exceptId provided, check if it's the same record
            if ($exceptId && isset($result['id']) && $result['id'] == $exceptId) {
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Validate exists in database
     * Format: exists:table,column
     */
    private function validateExists($value, $table, $column = 'id'): bool {
        try {
            $conditions = [$column => $value];
            $result = $this->db->findOne($table, $conditions);
            return $result !== null;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Validate date
     */
    private function validateDate($value): bool {
        return strtotime($value) !== false;
    }
    
    /**
     * Validate date format
     */
    private function validateDateFormat($value, $format = 'Y-m-d'): bool {
        $d = DateTime::createFromFormat($format, $value);
        return $d && $d->format($format) === $value;
    }
    
    /**
     * Validate date is after another date
     */
    private function validateAfter($value, $afterDate): bool {
        return strtotime($value) > strtotime($afterDate);
    }
    
    /**
     * Validate date is before another date
     */
    private function validateBefore($value, $beforeDate): bool {
        return strtotime($value) < strtotime($beforeDate);
    }
    
    /**
     * Validate alphabetic characters only
     */
    private function validateAlpha($value): bool {
        return preg_match('/^[a-zA-Z]+$/', $value);
    }
    
    /**
     * Validate alphanumeric characters only
     */
    private function validateAlphaNum($value): bool {
        return preg_match('/^[a-zA-Z0-9]+$/', $value);
    }
    
    /**
     * Validate URL
     */
    private function validateUrl($value): bool {
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }
    
    // ========================================================================
    // STATIC HELPER METHODS
    // ========================================================================
    
    /**
     * Quick validation without instantiation
     * 
     * @param array $data Data to validate
     * @param array $rules Validation rules
     * @return array Result with 'valid' and 'errors' keys
     */
    public static function quick(array $data, array $rules): array {
        $validator = new self($data);
        $validator->rules($rules);
        $isValid = $validator->validate();
        
        return [
            'valid' => $isValid,
            'errors' => $validator->getErrors()
        ];
    }
    
    /**
     * Validate single value
     * 
     * @param mixed $value Value to validate
     * @param string $rules Validation rules
     * @return bool True if valid
     */
    public static function value($value, string $rules): bool {
        $validator = new self(['value' => $value]);
        $validator->rules(['value' => $rules]);
        return $validator->validate();
    }
}
?>
