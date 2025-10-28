<?php
/**
 * CSRF Token Endpoint for DSGVO ADV Project
 * Provides CSRF tokens for AJAX requests
 */

// Set content type for JSON response
header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Start session for CSRF protection
session_start();

// Include CSRF protection
require_once __DIR__ . '/../includes/csrf_protection.php';

// Handle CORS for development
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');
}

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    }
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    }
    exit(0);
}

// Only allow GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode([
        'success' => false,
        'message' => 'Only GET requests are allowed'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    // Generate CSRF token
    $token = CSRFProtection::getToken();
    
    // Return token
    echo json_encode([
        'success' => true,
        'token' => $token,
        'timestamp' => time()
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    // Log error
    error_log('CSRF token generation failed: ' . $e->getMessage());
    
    // Return error response
    echo json_encode([
        'success' => false,
        'message' => 'Failed to generate CSRF token'
    ], JSON_UNESCAPED_UNICODE);
}
