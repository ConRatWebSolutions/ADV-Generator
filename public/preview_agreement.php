<?php

/**
 * Preview Agreement Endpoint
 * Returns the HTML preview of the agreement based on form data
 */

header('Content-Type: application/json');

try {
    // Only allow POST requests
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Nur POST erlaubt');
    }

    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Ungültige JSON-Daten');
    }

    // Validate required fields
    $required = ['vorname', 'name', 'email', 'firma', 'ansprechpartner', 'anschrift', 'plz', 'ort'];
    foreach ($required as $field) {
        if (empty($input[$field])) {
            throw new Exception("Feld '$field' ist erforderlich.");
        }
    }

    // Validate email
    if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Ungültige E-Mail-Adresse');
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

    // Return success with HTML
    echo json_encode([
        'success' => true,
        'html' => $html
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
