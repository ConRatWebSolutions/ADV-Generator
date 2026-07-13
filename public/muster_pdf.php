<?php
declare(strict_types=1);

// Alle bisherige Ausgabe verwerfen damit keine Warnings das PDF korrumpieren
ob_start();

require_once __DIR__ . '/../config/environment.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/database_operations.php';
require_once __DIR__ . '/../includes/rate_limiter.php';
require_once __DIR__ . '/../config/pdf_config.php';

// Rate-Limiting: verhindert Ressourcen-Abuse durch wiederholte,
// unauthentifizierte PDF-Generierung (eigener Bucket, getrennt vom Formular).
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$rateLimiter = new RateLimiter(10, 3600, 900);
$rateLimitResult = $rateLimiter->checkRateLimit($ip . ':muster');
if (!$rateLimitResult['allowed']) {
    ob_end_clean();
    http_response_code(429);
    exit($rateLimitResult['message'] ?? 'Zu viele Anfragen.');
}

$musterData = [
    'vorname'         => 'Max',
    'name'            => 'Muster',
    'email'           => 'max.muster@muster-gmbh.de',
    'firma'           => 'Muster GmbH',
    'ansprechpartner' => 'Max Muster',
    'anschrift'       => 'Musterstraße 1',
    'plz'             => '12345',
    'ort'             => 'Musterstadt',
    'dienstleistung'  => 'webhosting',
    'agreement_id'    => 'MUSTER',
    'id'              => 0,
];

try {
    $pdfConfig = new PDFConfig();
    $pdfPath = $pdfConfig->generateAgreementPDF($musterData);

    $realPath = realpath($pdfPath) ?: $pdfPath;

    ob_end_clean();

    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="Muster-Auftragsverarbeitungsvereinbarung.pdf"');
    header('Content-Length: ' . filesize($realPath));
    header('Cache-Control: no-store');
    readfile($realPath);
    unlink($realPath);
} catch (Throwable $e) {
    ob_end_clean();
    http_response_code(500);
    exit('PDF konnte nicht erstellt werden: ' . $e->getMessage());
}
