<?php
declare(strict_types=1);

$filename = isset($_GET['file']) ? basename($_GET['file']) : '';

if (!$filename || !preg_match('/^adv_[a-zA-Z0-9_-]+\.pdf$/', $filename)) {
    http_response_code(400);
    exit('Ungültige Anfrage.');
}

$filepath = __DIR__ . '/../storage/pdfs/' . $filename;

if (!file_exists($filepath) || !is_file($filepath)) {
    http_response_code(404);
    exit('Datei nicht gefunden.');
}

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . filesize($filepath));
header('Cache-Control: no-store');
readfile($filepath);
