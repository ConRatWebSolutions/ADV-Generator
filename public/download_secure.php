<?php
declare(strict_types=1);

$token = isset($_GET['token']) ? trim($_GET['token']) : '';

if (!$token || !preg_match('/^[a-f0-9]{64}$/', $token)) {
    http_response_code(400);
    exit('Ungültige Anfrage.');
}

require_once __DIR__ . '/../config/environment.php';
require_once __DIR__ . '/../config/database.php';

try {
    $config = EnvironmentConfig::loadConfig();
    $dbConfig = new DatabaseConfig($config);
    $pdo = $dbConfig->getConnection();

    $sql = "SELECT dt.id, dt.expires_at, dt.used, a.pdf_pfad
            FROM download_tokens dt
            JOIN auftragsverarbeitungsvereinbarungen a ON a.id = dt.vereinbarung_id
            WHERE dt.token = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$token]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        http_response_code(404);
        exit('Link nicht gefunden.');
    }

    if (strtotime($row['expires_at']) < time()) {
        http_response_code(410);
        exit('Dieser Download-Link ist abgelaufen.');
    }

    $pdfPfad = $row['pdf_pfad'];
    // pdf_pfad kann absoluter Pfad oder relativer Pfad sein
    $filepath = realpath(str_starts_with($pdfPfad, '/') ? $pdfPfad : __DIR__ . '/../' . $pdfPfad);
    $allowed = realpath(__DIR__ . '/../storage/pdfs');

    if (!$filepath || !str_starts_with($filepath, $allowed) || !is_file($filepath)) {
        http_response_code(404);
        exit('Datei nicht gefunden.');
    }

    $filename = basename($filepath);
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . filesize($filepath));
    header('Cache-Control: no-store');
    readfile($filepath);

} catch (Throwable $e) {
    http_response_code(500);
    exit('Fehler beim Verarbeiten der Anfrage.');
}
