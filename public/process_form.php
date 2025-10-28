<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(60);

header('Content-Type: application/json');

try {
    // Einfache Validierung
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Nur POST erlaubt');
    }
    
    // JSON-Daten lesen
    $input = json_decode(file_get_contents('php://input'), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Ungültige JSON-Daten');
    }
    
    // Erforderliche Felder prüfen
    $required = ['vorname', 'name', 'email', 'firma', 'ansprechpartner', 'anschrift', 'plz', 'ort'];
    foreach ($required as $field) {
        if (empty($input[$field])) {
            throw new Exception("Feld '$field' ist erforderlich");
        }
    }
    
    // E-Mail validieren
    if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Ungültige E-Mail-Adresse');
    }
    
    // Vereinbarungs-ID generieren
    $agreementId = 'ADV-' . date('Ymd') . '-' . substr(md5(uniqid()), 0, 8);
    
    // Datenbankverbindung
    require_once __DIR__ . '/../config/database.php';
    $pdo = DatabaseConfig::getConnection();
    
    // In PostgreSQL-Datenbank speichern (angepasst an Schema)
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
        $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'erstellt',
        date('Y-m-d H:i:s')
    ]);
    
    // ID der eingefügten Zeile holen
    $id = $pdo->lastInsertId();
    
    // PDF generieren
    require_once __DIR__ . '/../config/pdf_config.php';
    $pdfConfig = new PDFConfig();
    $pdfData = array_merge($input, ['agreement_id' => $agreementId, 'id' => $id]);
    $pdfPath = $pdfConfig->generateAgreementPDF($pdfData);
    
    // PDF-Pfad in Datenbank aktualisieren
    $updateSql = "UPDATE auftragsverarbeitungsvereinbarungen SET pdf_pfad = ? WHERE id = ?";
    $updateStmt = $pdo->prepare($updateSql);
    $updateStmt->execute([$pdfPath, $id]);
    
    // E-Mail senden
    require_once __DIR__ . '/../config/email_config.php';
    $emailConfig = new EmailConfig();
    $pdfData['pdfPath'] = $pdfPath; // Füge den korrekten PDF-Pfad hinzu
    $emailConfig->sendAgreementEmails($pdfData);
    
    // Status auf 'versendet' aktualisieren
    $statusSql = "UPDATE auftragsverarbeitungsvereinbarungen SET status = 'versendet' WHERE id = ?";
    $statusStmt = $pdo->prepare($statusSql);
    $statusStmt->execute([$id]);
    
    // Erfolg
    echo json_encode([
        'success' => true,
        'message' => 'Vereinbarung erfolgreich erstellt und per E-Mail versendet!',
        'agreement_id' => $agreementId,
        'id' => $id,
        'pdf_path' => $pdfPath
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}