<?php
declare(strict_types=1);

// Alle bisherige Ausgabe verwerfen damit keine Warnings das PDF korrumpieren
ob_start();

require_once __DIR__ . '/../config/pdf_config.php';

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
