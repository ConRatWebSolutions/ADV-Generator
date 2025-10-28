<?php
/**
 * FormProcessor Class
 * Handles form submission, validation, database operations, PDF generation, and email sending
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/database_operations.php';
require_once __DIR__ . '/rate_limiter.php';
require_once __DIR__ . '/csrf_protection.php';
require_once __DIR__ . '/../config/email_config.php';
require_once __DIR__ . '/../config/pdf_config.php';

/**
 * Main form processing class
 */
class FormProcessor {
    private $pdo;
    private $emailConfig;
    private $pdfConfig;
    private $rateLimiter;
    private $csrfProtection;

    public function __construct() {
        $this->pdo = DatabaseConfig::getConnection();
        $this->emailConfig = new EmailConfig();
        $this->pdfConfig = new PDFConfig();
        $this->rateLimiter = new RateLimiter($this->pdo);
        $this->csrfProtection = new CSRFProtection($this->pdo);
    }

    /**
     * Process the form submission
     */
    public function processForm(): array {
        try {
            // Check if request is POST
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                return $this->errorResponse('Nur POST-Anfragen erlaubt', 405);
            }

            // Get input data (JSON or POST)
            $input = [];
            $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
            
            if (strpos($contentType, 'application/json') !== false) {
                $jsonInput = file_get_contents('php://input');
                $input = json_decode($jsonInput, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    return $this->errorResponse('Ungültige JSON-Daten: ' . json_last_error_msg(), 400);
                }
            } else {
                $input = $_POST;
            }

            // Validate required fields
            $validationResult = $this->validateInput($input);
            if (!$validationResult['valid']) {
                return $this->errorResponse($validationResult['message'], 400);
            }

            // Check rate limiting
            $clientIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            if (!$this->rateLimiter->checkRateLimit($clientIp)) {
                return $this->errorResponse('Zu viele Anfragen. Bitte versuchen Sie es später erneut.', 429);
            }

            // CSRF protection (temporarily disabled for debugging)
            // if (!$this->csrfProtection->validateToken($input['csrf_token'] ?? '')) {
            //     return $this->errorResponse('Ungültiger CSRF-Token', 403);
            // }

            // Process the form data
            $result = $this->processFormData($input);

            if ($result['success']) {
                // Log successful operation
                DatabaseOperations::logOperation(
                    $this->pdo,
                    'form_submission',
                    'Formular erfolgreich verarbeitet',
                    ['agreement_id' => $result['agreement_id']]
                );

                return $this->successResponse('Formular erfolgreich verarbeitet', $result);
            } else {
                return $this->errorResponse($result['message'], 500);
            }

        } catch (Exception $e) {
            // Log error
            DatabaseOperations::logOperation(
                $this->pdo,
                'form_error',
                'Fehler bei Formularverarbeitung: ' . $e->getMessage(),
                ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]
            );

            return $this->errorResponse('Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.', 500);
        }
    }

    /**
     * Validate input data
     */
    private function validateInput(array $input): array {
        $requiredFields = [
            'vorname', 'name', 'email', 'firma', 'ansprechpartner',
            'anschrift', 'plz', 'ort'
        ];

        foreach ($requiredFields as $field) {
            if (empty($input[$field])) {
                return [
                    'valid' => false,
                    'message' => "Feld '{$field}' ist erforderlich"
                ];
            }
        }

        // Validate email
        if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
            return [
                'valid' => false,
                'message' => 'Ungültige E-Mail-Adresse'
            ];
        }

        // Validate postal code (German format)
        if (!preg_match('/^\d{5}$/', $input['plz'])) {
            return [
                'valid' => false,
                'message' => 'Ungültige Postleitzahl (5 Ziffern erforderlich)'
            ];
        }

        return ['valid' => true];
    }

    /**
     * Process form data and create agreement
     */
    private function processFormData(array $data): array {
        try {
            // Generate unique agreement ID
            $agreementId = 'ADV-' . date('Ymd') . '-' . substr(md5(uniqid()), 0, 8);

            // Prepare data for database
            $dbData = [
                'agreement_id' => $agreementId,
                'vorname' => $data['vorname'],
                'name' => $data['name'],
                'email' => $data['email'],
                'firma' => $data['firma'],
                'ansprechpartner' => $data['ansprechpartner'],
                'anschrift' => $data['anschrift'],
                'plz' => $data['plz'],
                'ort' => $data['ort'],
                'created_at' => date('Y-m-d H:i:s'),
                'status' => 'pending'
            ];

            // Save to database
            $this->saveToDatabase($dbData);

            // Generate PDF
            $pdfPath = $this->pdfConfig->generateAgreementPDF($dbData);

            // Send emails
            $this->emailConfig->sendAgreementEmails($dbData);

            return [
                'success' => true,
                'agreement_id' => $agreementId,
                'pdf_path' => $pdfPath
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Fehler bei der Verarbeitung: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Save data to database
     */
    private function saveToDatabase(array $data): void {
        $sql = "INSERT INTO auftragsverarbeitungsvereinbarungen 
                (agreement_id, vorname, name, email, firma, ansprechpartner, 
                 anschrift, plz, ort, created_at, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $data['agreement_id'],
            $data['vorname'],
            $data['name'],
            $data['email'],
            $data['firma'],
            $data['ansprechpartner'],
            $data['anschrift'],
            $data['plz'],
            $data['ort'],
            $data['created_at'],
            $data['status']
        ]);
    }

    /**
     * Generate success response
     */
    private function successResponse(string $message, array $data = []): array {
        return [
            'success' => true,
            'message' => $message,
            'data' => $data
        ];
    }

    /**
     * Generate error response
     */
    private function errorResponse(string $message, int $code = 400): array {
        if (!headers_sent()) {
            http_response_code($code);
        }
        return [
            'success' => false,
            'message' => $message,
            'code' => $code
        ];
    }
}
