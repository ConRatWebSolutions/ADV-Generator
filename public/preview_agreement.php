<?php

/**
 * Preview Agreement Endpoint
 * Returns the HTML preview of the agreement based on form data
 */

// Disable error display to prevent breaking JSON output
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Set JSON header first
header('Content-Type: application/json; charset=utf-8');

// Function to safely output JSON
function outputJson($data)
{
    $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($json === false) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Fehler beim Erstellen der JSON-Antwort: ' . json_last_error_msg()
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo $json;
    }
    exit;
}

try {
    // Only allow POST requests
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        outputJson([
            'success' => false,
            'message' => 'Nur POST erlaubt'
        ]);
    }

    // Rate-Limiting: verhindert automatisiertes Massen-Abrufen dieses
    // unauthentifizierten Endpunkts (eigener Bucket, getrennt vom Formular).
    require_once __DIR__ . '/../config/environment.php';
    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../includes/database_operations.php';
    require_once __DIR__ . '/../includes/rate_limiter.php';
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $rateLimiter = new RateLimiter(30, 3600, 900);
    $rateLimitResult = $rateLimiter->checkRateLimit($ip . ':preview');
    if (!$rateLimitResult['allowed']) {
        http_response_code(429);
        outputJson([
            'success' => false,
            'message' => $rateLimitResult['message'] ?? 'Zu viele Anfragen.'
        ]);
    }

    // Get JSON input
    $rawInput = file_get_contents('php://input');
    if (empty($rawInput)) {
        outputJson([
            'success' => false,
            'message' => 'Keine Daten empfangen'
        ]);
    }

    $input = json_decode($rawInput, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        outputJson([
            'success' => false,
            'message' => 'Ungültige JSON-Daten: ' . json_last_error_msg()
        ]);
    }

    // Validate required fields
    $required = ['vorname', 'name', 'email', 'firma', 'ansprechpartner', 'anschrift', 'plz', 'ort'];
    $missingFields = [];
    foreach ($required as $field) {
        if (empty($input[$field])) {
            $missingFields[] = $field;
        }
    }

    if (!empty($missingFields)) {
        outputJson([
            'success' => false,
            'message' => 'Fehlende Felder: ' . implode(', ', $missingFields)
        ]);
    }

    // Validate email
    if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
        outputJson([
            'success' => false,
            'message' => 'Ungültige E-Mail-Adresse'
        ]);
    }

    // Load template and generate full agreement HTML (Hauptvertrag + Anlagen 1–4, wie im PDF)
    require_once __DIR__ . '/../templates/agreement_template.php';
    $html = AgreementTemplate::generateFullAgreementHtml($input);

    outputJson([
        'success' => true,
        'html' => $html
    ]);
} catch (Exception $e) {
    error_log("Preview error: " . $e->getMessage());
    http_response_code(500);
    outputJson([
        'success' => false,
        'message' => 'Serverfehler: ' . $e->getMessage()
    ]);
} catch (Error $e) {
    error_log("Preview fatal error: " . $e->getMessage());
    http_response_code(500);
    outputJson([
        'success' => false,
        'message' => 'Kritischer Fehler: ' . $e->getMessage()
    ]);
}
