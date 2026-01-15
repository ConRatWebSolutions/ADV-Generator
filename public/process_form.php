<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
set_time_limit(60);

// Logger laden - MUSS als erstes geladen werden
require_once __DIR__ . '/../includes/logger.php';

// Sofort loggen, dass Request angekommen ist
$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN';
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$timestamp = date('Y-m-d H:i:s');
$logDir = __DIR__ . '/../logs/';
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}
file_put_contents($logDir . 'access.log', "[{$timestamp}] [{$requestMethod}] User: unknown | IP: {$ip} | URI: " . ($_SERVER['REQUEST_URI'] ?? 'unknown') . " | Request gestartet" . PHP_EOL, FILE_APPEND | LOCK_EX);

header('Content-Type: application/json');

try {
    if ($requestMethod !== 'POST') {
        file_put_contents($logDir . 'error.log', "[{$timestamp}] [ERROR] User: unknown | IP: {$ip} | Ungültige Request-Methode: {$requestMethod}" . PHP_EOL, FILE_APPEND | LOCK_EX);
        throw new Exception('Nur POST erlaubt');
    }

    // JSON-Daten lesen
    $rawInput = file_get_contents('php://input');
    file_put_contents($logDir . 'access.log', "[{$timestamp}] [POST] User: unknown | IP: {$ip} | JSON-Input empfangen (Länge: " . strlen($rawInput) . " Bytes)" . PHP_EOL, FILE_APPEND | LOCK_EX);

    if (empty($rawInput)) {
        file_put_contents($logDir . 'error.log', "[{$timestamp}] [ERROR] User: unknown | IP: {$ip} | Keine JSON-Daten empfangen" . PHP_EOL, FILE_APPEND | LOCK_EX);
        throw new Exception('Keine Daten empfangen');
    }

    $input = json_decode($rawInput, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        $jsonError = json_last_error_msg();
        file_put_contents($logDir . 'error.log', "[{$timestamp}] [ERROR] User: unknown | IP: {$ip} | JSON-Parse-Fehler: {$jsonError}" . PHP_EOL, FILE_APPEND | LOCK_EX);
        throw new Exception('Ungültige JSON-Daten: ' . $jsonError);
    }

    $userEmail = $input['email'] ?? 'unknown';
    file_put_contents($logDir . 'access.log', "[{$timestamp}] [POST] User: {$userEmail} | IP: {$ip} | JSON erfolgreich dekodiert" . PHP_EOL, FILE_APPEND | LOCK_EX);

    // Erforderliche Felder prüfen
    $required = ['dienstleistung', 'vorname', 'name', 'email', 'firma', 'ansprechpartner', 'anschrift', 'plz', 'ort'];
    $missingFields = [];
    foreach ($required as $field) {
        if (empty($input[$field])) {
            $missingFields[] = $field;
        }
    }

    if (!empty($missingFields)) {
        file_put_contents($logDir . 'error.log', "[{$timestamp}] [ERROR] User: {$userEmail} | IP: {$ip} | Fehlende Felder: " . implode(', ', $missingFields) . PHP_EOL, FILE_APPEND | LOCK_EX);
        throw new Exception("Fehlende Felder: " . implode(', ', $missingFields));
    }

    // E-Mail validieren
    if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
        file_put_contents($logDir . 'error.log', "[{$timestamp}] [ERROR] User: {$userEmail} | IP: {$ip} | Ungültige E-Mail-Adresse" . PHP_EOL, FILE_APPEND | LOCK_EX);
        throw new Exception('Ungültige E-Mail-Adresse');
    }

    // Vereinbarungs-ID generieren
    $agreementId = 'ADV-' . date('Ymd') . '-' . substr(md5(uniqid()), 0, 8);
    file_put_contents($logDir . 'access.log', "[{$timestamp}] [POST] User: {$userEmail} | IP: {$ip} | Vereinbarungs-ID: {$agreementId}" . PHP_EOL, FILE_APPEND | LOCK_EX);

    // Environment-Config und Datenbankverbindung laden
    require_once __DIR__ . '/../config/environment.php';
    require_once __DIR__ . '/../config/database.php';

    file_put_contents($logDir . 'access.log', "[{$timestamp}] [POST] User: {$userEmail} | IP: {$ip} | Verbinde mit Datenbank..." . PHP_EOL, FILE_APPEND | LOCK_EX);
    try {
        $pdo = DatabaseConfig::getConnection();
        file_put_contents($logDir . 'access.log', "[{$timestamp}] [POST] User: {$userEmail} | IP: {$ip} | Datenbankverbindung erfolgreich" . PHP_EOL, FILE_APPEND | LOCK_EX);
    } catch (Exception $dbException) {
        file_put_contents($logDir . 'error.log', "[{$timestamp}] [ERROR] User: {$userEmail} | IP: {$ip} | Database connection failed: " . $dbException->getMessage() . PHP_EOL, FILE_APPEND | LOCK_EX);
        throw new Exception("Datenbankverbindung fehlgeschlagen: " . $dbException->getMessage());
    }

    // In Datenbank speichern
    $config = EnvironmentConfig::loadConfig();
    $dbType = $config['db_type'] ?? 'mysql';
    file_put_contents($logDir . 'access.log', "[{$timestamp}] [POST] User: {$userEmail} | IP: {$ip} | Datenbanktyp: {$dbType}" . PHP_EOL, FILE_APPEND | LOCK_EX);

    try {
        if ($dbType === 'postgresql' || $dbType === 'postgres') {
            $sql = "INSERT INTO auftragsverarbeitungsvereinbarungen 
                    (name, vorname, email, firma, ansprechpartner, 
                     anschrift, plz, ort, ip_adresse, status, erstellt_am) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    RETURNING id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $input['name'],
                $input['vorname'],
                $input['email'],
                $input['firma'],
                $input['ansprechpartner'],
                $input['anschrift'],
                $input['plz'],
                $input['ort'],
                $ip,
                'erstellt',
                date('Y-m-d H:i:s')
            ]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $id = $result['id'] ?? null;
        } else {
            $sql = "INSERT INTO auftragsverarbeitungsvereinbarungen 
                    (name, vorname, email, firma, ansprechpartner, 
                     anschrift, plz, ort, ip_adresse, status, erstellt_am) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $input['name'],
                $input['vorname'],
                $input['email'],
                $input['firma'],
                $input['ansprechpartner'],
                $input['anschrift'],
                $input['plz'],
                $input['ort'],
                $ip,
                'erstellt',
                date('Y-m-d H:i:s')
            ]);
            $id = $pdo->lastInsertId();
        }

        if (!$id) {
            file_put_contents($logDir . 'error.log', "[{$timestamp}] [ERROR] User: {$userEmail} | IP: {$ip} | Konnte keine ID nach INSERT erhalten" . PHP_EOL, FILE_APPEND | LOCK_EX);
            throw new Exception("Konnte keine ID nach INSERT erhalten");
        }
        file_put_contents($logDir . 'access.log', "[{$timestamp}] [POST] User: {$userEmail} | IP: {$ip} | Datenbank-INSERT erfolgreich. ID: {$id}" . PHP_EOL, FILE_APPEND | LOCK_EX);
    } catch (PDOException $pdoException) {
        file_put_contents($logDir . 'error.log', "[{$timestamp}] [ERROR] User: {$userEmail} | IP: {$ip} | Database INSERT failed: " . $pdoException->getMessage() . PHP_EOL, FILE_APPEND | LOCK_EX);
        throw new Exception("Datenbankfehler beim Speichern: " . $pdoException->getMessage());
    }

    // PDF generieren - mit Timeout-Schutz
    $pdfPath = '';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logDir . 'access.log', "[{$timestamp}] [POST] User: {$userEmail} | IP: {$ip} | Starte PDF-Generierung..." . PHP_EOL, FILE_APPEND | LOCK_EX);

    // Timeout erhöhen für PDF-Generierung
    $oldTimeout = ini_get('max_execution_time');
    set_time_limit(120); // 2 Minuten für PDF-Generierung

    try {
        require_once __DIR__ . '/../config/pdf_config.php';

        if (!class_exists('TCPDF')) {
            throw new Exception('TCPDF-Klasse nicht gefunden. Bitte Composer-Abhängigkeiten installieren.');
        }

        $pdfConfig = new PDFConfig();
        $pdfData = array_merge($input, ['agreement_id' => $agreementId, 'id' => $id]);

        // PDF-Generierung mit explizitem Logging
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($logDir . 'access.log', "[{$timestamp}] [POST] User: {$userEmail} | IP: {$ip} | Rufe generateAgreementPDF auf..." . PHP_EOL, FILE_APPEND | LOCK_EX);

        $pdfPath = $pdfConfig->generateAgreementPDF($pdfData);

        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($logDir . 'access.log', "[{$timestamp}] [POST] User: {$userEmail} | IP: {$ip} | PDF erfolgreich generiert: {$pdfPath}" . PHP_EOL, FILE_APPEND | LOCK_EX);

        $updateSql = "UPDATE auftragsverarbeitungsvereinbarungen SET pdf_pfad = ? WHERE id = ?";
        $updateStmt = $pdo->prepare($updateSql);
        $updateStmt->execute([$pdfPath, $id]);

        // Timeout zurücksetzen
        set_time_limit($oldTimeout);
    } catch (Throwable $pdfException) {
        set_time_limit($oldTimeout);
        $timestamp = date('Y-m-d H:i:s');
        $errorMsg = $pdfException->getMessage();
        $errorFile = basename($pdfException->getFile());
        $errorLine = $pdfException->getLine();

        file_put_contents($logDir . 'error.log', "[{$timestamp}] [ERROR] User: {$userEmail} | IP: {$ip} | PDF generation failed: {$errorMsg} | File: {$errorFile}:{$errorLine}" . PHP_EOL, FILE_APPEND | LOCK_EX);

        $updateSql = "UPDATE auftragsverarbeitungsvereinbarungen SET fehler_nachricht = ? WHERE id = ?";
        $updateStmt = $pdo->prepare($updateSql);
        $updateStmt->execute(['PDF-Generierung fehlgeschlagen: ' . $errorMsg, $id]);
    }

    // E-Mail senden
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logDir . 'access.log', "[{$timestamp}] [POST] User: {$userEmail} | IP: {$ip} | Starte E-Mail-Versand..." . PHP_EOL, FILE_APPEND | LOCK_EX);
    try {
        require_once __DIR__ . '/../config/email_config.php';
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($logDir . 'access.log', "[{$timestamp}] [POST] User: {$userEmail} | IP: {$ip} | EmailConfig geladen, erstelle Instanz..." . PHP_EOL, FILE_APPEND | LOCK_EX);
        $emailConfig = new EmailConfig();
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($logDir . 'access.log', "[{$timestamp}] [POST] User: {$userEmail} | IP: {$ip} | EmailConfig-Instanz erstellt, rufe sendAgreementEmails auf..." . PHP_EOL, FILE_APPEND | LOCK_EX);
        $pdfData = array_merge($input, ['agreement_id' => $agreementId, 'id' => $id, 'pdfPath' => $pdfPath]);
        $emailResult = $emailConfig->sendAgreementEmails($pdfData);
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($logDir . 'access.log', "[{$timestamp}] [POST] User: {$userEmail} | IP: {$ip} | sendAgreementEmails zurückgekehrt" . PHP_EOL, FILE_APPEND | LOCK_EX);

        if (!$emailResult['success']) {
            file_put_contents($logDir . 'mailsend.log', "[{$timestamp}] [error] User: {$userEmail} | IP: {$ip} | Email sending failed: " . json_encode($emailResult) . PHP_EOL, FILE_APPEND | LOCK_EX);
            $statusSql = "UPDATE auftragsverarbeitungsvereinbarungen SET status = 'fehler', fehler_nachricht = ? WHERE id = ?";
            $statusStmt = $pdo->prepare($statusSql);
            $errorMsg = 'E-Mail-Versand fehlgeschlagen: ' . ($emailResult['error'] ?? 'Unbekannter Fehler');
            $statusStmt->execute([$errorMsg, $id]);
        } else {
            file_put_contents($logDir . 'mailsend.log', "[{$timestamp}] [success] User: {$userEmail} | IP: {$ip} | Emails sent successfully for agreement ID: {$id}" . PHP_EOL, FILE_APPEND | LOCK_EX);
            $statusSql = "UPDATE auftragsverarbeitungsvereinbarungen SET status = 'versendet' WHERE id = ?";
            $statusStmt = $pdo->prepare($statusSql);
            $statusStmt->execute([$id]);
        }
    } catch (Exception $emailException) {
        file_put_contents($logDir . 'mailsend.log', "[{$timestamp}] [error] User: {$userEmail} | IP: {$ip} | Email sending exception: " . $emailException->getMessage() . PHP_EOL, FILE_APPEND | LOCK_EX);
        $statusSql = "UPDATE auftragsverarbeitungsvereinbarungen SET status = 'fehler', fehler_nachricht = ? WHERE id = ?";
        $statusStmt = $pdo->prepare($statusSql);
        $statusStmt->execute(['E-Mail-Exception: ' . $emailException->getMessage(), $id]);
    }

    // Erfolg
    file_put_contents($logDir . 'access.log', "[{$timestamp}] [POST] User: {$userEmail} | IP: {$ip} | Formular erfolgreich verarbeitet - Agreement ID: {$agreementId}, DB ID: {$id}" . PHP_EOL, FILE_APPEND | LOCK_EX);

    echo json_encode([
        'success' => true,
        'message' => 'Vereinbarung erfolgreich erstellt und per E-Mail versendet!',
        'agreement_id' => $agreementId,
        'id' => $id,
        'pdf_path' => $pdfPath
    ]);
} catch (Exception $e) {
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $logDir = __DIR__ . '/../logs/';
    file_put_contents($logDir . 'error.log', "[{$timestamp}] [ERROR] User: unknown | IP: {$ip} | File: " . basename($e->getFile()) . ":" . $e->getLine() . " | " . $e->getMessage() . PHP_EOL, FILE_APPEND | LOCK_EX);
    file_put_contents($logDir . 'error.log', "[{$timestamp}] [ERROR] User: unknown | IP: {$ip} | Stack trace: " . $e->getTraceAsString() . PHP_EOL, FILE_APPEND | LOCK_EX);

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} catch (Error $e) {
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $logDir = __DIR__ . '/../logs/';
    file_put_contents($logDir . 'error.log', "[{$timestamp}] [ERROR] User: unknown | IP: {$ip} | Fatal Error: " . $e->getMessage() . " | File: " . basename($e->getFile()) . ":" . $e->getLine() . PHP_EOL, FILE_APPEND | LOCK_EX);

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Ein kritischer Fehler ist aufgetreten: ' . $e->getMessage()
    ]);
}
