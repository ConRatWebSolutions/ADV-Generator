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

    // Load template
    require_once __DIR__ . '/../templates/agreement_template.php';

    // Generate agreement text
    $agreementText = AgreementTemplate::generateAgreementText($input);

    // Wrap in container with title
    $html = '<div class="agreement-preview">';
    $html .= '<h1>Vereinbarung zur Auftragsverarbeitung nach Art. 28 DSGVO</h1>';
    $html .= $agreementText;
    $html .= '</div>';

    // Return success with HTML (json_encode will properly escape the HTML)
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
