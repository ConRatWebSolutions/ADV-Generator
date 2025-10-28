<?php
/**
 * CSRF Protection for DSGVO ADV Project
 * Implements CSRF token generation and validation
 */

class CSRFProtection {
    private static $tokenName = 'csrf_token';
    private static $sessionKey = 'csrf_tokens';
    
    /**
     * Generate a new CSRF token
     * @return string CSRF token
     */
    public static function generateToken() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $token = bin2hex(random_bytes(32));
        $timestamp = time();
        
        // Store token with timestamp
        if (!isset($_SESSION[self::$sessionKey])) {
            $_SESSION[self::$sessionKey] = [];
        }
        
        $_SESSION[self::$sessionKey][$token] = $timestamp;
        
        // Clean old tokens (older than 1 hour)
        self::cleanOldTokens();
        
        return $token;
    }
    
    /**
     * Validate CSRF token
     * @param string $token Token to validate
     * @return bool True if valid, false otherwise
     */
    public static function validateToken($token) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (empty($token) || !isset($_SESSION[self::$sessionKey])) {
            return false;
        }
        
        if (!isset($_SESSION[self::$sessionKey][$token])) {
            return false;
        }
        
        $timestamp = $_SESSION[self::$sessionKey][$token];
        $currentTime = time();
        
        // Token expires after 1 hour
        if (($currentTime - $timestamp) > 3600) {
            unset($_SESSION[self::$sessionKey][$token]);
            return false;
        }
        
        return true;
    }
    
    /**
     * Get CSRF token for form
     * @return string CSRF token
     */
    public static function getToken() {
        return self::generateToken();
    }
    
    /**
     * Get CSRF token name
     * @return string Token name
     */
    public static function getTokenName() {
        return self::$tokenName;
    }
    
    /**
     * Clean old tokens from session
     */
    private static function cleanOldTokens() {
        if (!isset($_SESSION[self::$sessionKey])) {
            return;
        }
        
        $currentTime = time();
        foreach ($_SESSION[self::$sessionKey] as $token => $timestamp) {
            if (($currentTime - $timestamp) > 3600) {
                unset($_SESSION[self::$sessionKey][$token]);
            }
        }
    }
    
    /**
     * Verify CSRF token from request
     * @param array $data Request data
     * @return bool True if valid, false otherwise
     */
    public static function verifyRequest($data) {
        if (!isset($data[self::$tokenName])) {
            return false;
        }
        
        return self::validateToken($data[self::$tokenName]);
    }
    
    /**
     * Get CSRF token HTML input
     * @return string HTML input element
     */
    public static function getTokenInput() {
        $token = self::getToken();
        return '<input type="hidden" name="' . self::$tokenName . '" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }
    
    /**
     * Check if request is AJAX and validate token
     * @return bool True if valid AJAX request, false otherwise
     */
    public static function validateAjaxRequest() {
        // Check if it's an AJAX request
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
            return false;
        }
        
        // Get JSON input
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return false;
        }
        
        return self::verifyRequest($data);
    }
    
    /**
     * Log CSRF token validation attempts
     * @param bool $success Whether validation was successful
     * @param string $ip Client IP address
     */
    public static function logValidation($success, $ip = null) {
        if ($ip === null) {
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        }
        
        $level = $success ? 'info' : 'warning';
        $message = $success ? 'CSRF token validation successful' : 'CSRF token validation failed';
        
        // Log to database if available
        if (class_exists('DatabaseOperations')) {
            DatabaseOperations::logOperation($level, $message, [
                'ip_address' => $ip,
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
                'timestamp' => date('Y-m-d H:i:s')
            ]);
        }
    }
    
    /**
     * Generate CSRF token for JavaScript
     * @return array Token data for JavaScript
     */
    public static function getTokenForJS() {
        $token = self::getToken();
        return [
            'name' => self::$tokenName,
            'value' => $token,
            'timestamp' => time()
        ];
    }
}
