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

// Kein Cross-Origin-Zugriff noetig: Formular und Endpoint laufen auf derselben
// Domain. Ein reflektierter Origin-Header kombiniert mit
// Allow-Credentials:true wuerde fremden Seiten erlauben, mit den Cookies
// des Opfers gueltige CSRF-Tokens abzuholen und so den CSRF-Schutz aushebeln.

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
