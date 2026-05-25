<?php
/**
 * SessionManager - Centralized session management with timeout and security
 * 
 * Features:
 * - Automatic session timeout (30 minutes)
 * - Session validation
 * - Secure cookie settings
 * - User data management
 * - Session refresh
 */
class SessionManager {
    private const SESSION_TIMEOUT = 1800; // 30 minutes in seconds
    private const WARNING_THRESHOLD = 300; // 5 minutes warning
    
    /**
     * Start session with secure settings
     */
    public static function start(): void {
        if (session_status() === PHP_SESSION_NONE) {
            // Set secure session settings
            ini_set('session.cookie_httponly', 1);
            ini_set('session.use_only_cookies', 1);
            ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS
            ini_set('session.cookie_samesite', 'Lax');
            
            session_start();
            
            // Initialize session if new
            if (!isset($_SESSION['initialized'])) {
                $_SESSION['initialized'] = true;
                $_SESSION['created_at'] = time();
            }
            
            // Check if session is valid
            if (!self::isValid()) {
                self::destroy();
            }
        }
    }
    
    /**
     * Check if session is valid
     * 
     * @return bool True if session is valid
     */
    public static function isValid(): bool {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            return false;
        }
        
        // Check session timeout
        $lastActivity = $_SESSION['last_activity'] ?? 0;
        if (time() - $lastActivity > self::SESSION_TIMEOUT) {
            return false;
        }
        
        // Update last activity time
        $_SESSION['last_activity'] = time();
        return true;
    }
    
    /**
     * Set session value
     * 
     * @param string $key Session key
     * @param mixed $value Value to store
     */
    public static function set(string $key, $value): void {
        self::start();
        $_SESSION[$key] = $value;
    }
    
    /**
     * Get session value
     * 
     * @param string $key Session key
     * @return mixed Session value or null
     */
    public static function get(string $key) {
        self::start();
        return $_SESSION[$key] ?? null;
    }
    
    /**
     * Get current user ID
     * 
     * @return string|null User ID or null
     */
    public static function getUserId(): ?string {
        return self::get('user_id');
    }
    
    /**
     * Get current user role
     * 
     * @return string|null User role or null
     */
    public static function getUserRole(): ?string {
        return self::get('user_role');
    }
    
    /**
     * Get current user name
     * 
     * @return string|null User name or null
     */
    public static function getUserName(): ?string {
        return self::get('user_name');
    }
    
    /**
     * Get current user email
     * 
     * @return string|null User email or null
     */
    public static function getUserEmail(): ?string {
        return self::get('user_email');
    }
    
    /**
     * Destroy session completely
     */
    public static function destroy(): void {
        self::start();
        $_SESSION = [];
        
        // Delete session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        session_destroy();
    }
    
    /**
     * Refresh session timeout
     */
    public static function refresh(): void {
        self::start();
        $_SESSION['last_activity'] = time();
    }
    
    /**
     * Get time remaining until session expires (in seconds)
     * 
     * @return int Seconds remaining
     */
    public static function getTimeRemaining(): int {
        self::start();
        $lastActivity = $_SESSION['last_activity'] ?? time();
        $remaining = self::SESSION_TIMEOUT - (time() - $lastActivity);
        return max(0, $remaining);
    }
    
    /**
     * Check if session warning should be shown
     * 
     * @return bool True if warning should be shown
     */
    public static function shouldShowWarning(): bool {
        $remaining = self::getTimeRemaining();
        return $remaining > 0 && $remaining <= self::WARNING_THRESHOLD;
    }
    
    /**
     * Require authentication - redirect to login if not authenticated
     * 
     * @param string $redirectUrl URL to redirect to after login
     */
    public static function requireAuth(string $redirectUrl = '/app/views/login.php'): void {
        self::start();
        
        if (!self::isValid()) {
            // Store current URL for redirect after login
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? '';
            
            header('Location: ' . $redirectUrl);
            exit;
        }
    }
    
    /**
     * Login user and create session
     * 
     * @param string $userId User ID
     * @param string $userRole User role
     * @param string $userName User name
     * @param string $userEmail User email
     */
    public static function login(string $userId, string $userRole, string $userName, string $userEmail): void {
        self::start();
        
        // Regenerate session ID to prevent session fixation
        session_regenerate_id(true);
        
        // Set session data
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_role'] = $userRole;
        $_SESSION['user_name'] = $userName;
        $_SESSION['user_email'] = $userEmail;
        $_SESSION['last_activity'] = time();
        $_SESSION['login_time'] = time();
        $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }
    
    /**
     * Logout user and destroy session
     */
    public static function logout(): void {
        self::destroy();
    }
    
    /**
     * Check if user has specific role
     * 
     * @param string $role Role to check
     * @return bool True if user has role
     */
    public static function hasRole(string $role): bool {
        return self::getUserRole() === $role;
    }
    
    /**
     * Check if user has any of the specified roles
     * 
     * @param array $roles Roles to check
     * @return bool True if user has any of the roles
     */
    public static function hasAnyRole(array $roles): bool {
        $userRole = self::getUserRole();
        return in_array($userRole, $roles);
    }
    
    /**
     * Get session metadata
     * 
     * @return array Session metadata
     */
    public static function getMetadata(): array {
        self::start();
        
        return [
            'user_id' => self::getUserId(),
            'user_role' => self::getUserRole(),
            'user_name' => self::getUserName(),
            'user_email' => self::getUserEmail(),
            'login_time' => $_SESSION['login_time'] ?? null,
            'last_activity' => $_SESSION['last_activity'] ?? null,
            'time_remaining' => self::getTimeRemaining(),
            'ip_address' => $_SESSION['ip_address'] ?? null
        ];
    }
}
?>
