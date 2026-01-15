<?php

/**
 * PDF Configuration for DSGVO ADV Project
 * TCPDF integration for generating DSGVO agreements
 */

// Load Composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// TCPDF-Konstanten definieren, falls nicht vorhanden (werden normalerweise von TCPDF selbst definiert)
if (!defined('PDF_PAGE_ORIENTATION')) {
    define('PDF_PAGE_ORIENTATION', 'P'); // Portrait
}
if (!defined('PDF_UNIT')) {
    define('PDF_UNIT', 'mm');
}
if (!defined('PDF_PAGE_FORMAT')) {
    define('PDF_PAGE_FORMAT', 'A4');
}

// TCPDF wird direkt über den Namespace verwendet

/**
 * Custom TCPDF class with header image support
 */
class CustomTCPDF extends \TCPDF
{
    private ?string $headerImagePath = null;

    public function setHeaderImagePath(string $path): void
    {
        $this->headerImagePath = $path;
    }

    // Override Header() to add image on every page
    public function Header(): void
    {
        // Versuche zuerst PNG, dann JPG
        $headerImagePath = $this->headerImagePath;

        // Wenn PNG und keine Image-Support, versuche JPG
        if ($headerImagePath && file_exists($headerImagePath)) {
            $extension = strtolower(pathinfo($headerImagePath, PATHINFO_EXTENSION));
            $hasImageSupport = extension_loaded('gd') || extension_loaded('imagick');

            // PNG mit Alpha-Kanal benötigt GD/Imagick
            if ($extension === 'png' && !$hasImageSupport) {
                // Versuche JPG-Version
                $jpgPath = str_replace('.png', '.jpg', $headerImagePath);
                if (file_exists($jpgPath)) {
                    $headerImagePath = $jpgPath;
                } else {
                    // Keine JPG-Version gefunden, überspringe Header-Bild
                    return;
                }
            }
        }

        if ($headerImagePath && file_exists($headerImagePath)) {
            try {
                $pageWidth = $this->getPageWidth();
                $imageInfo = getimagesize($headerImagePath);
                if ($imageInfo !== false) {
                    $imageWidth = $imageInfo[0];
                    $imageHeight = $imageInfo[1];
                    $displayHeight = ($pageWidth / $imageWidth) * $imageHeight;
                    $this->SetXY(0, 0);
                    $this->Image($headerImagePath, 0, 0, $pageWidth, $displayHeight, '', '', '', false, 300, '', false, false, 0);
                }
            } catch (Exception $e) {
                // Fehler beim Laden des Bildes ignorieren, damit PDF trotzdem erstellt wird
                error_log("Header image error: " . $e->getMessage());
            }
        }
    }

    // Override Footer() to add company address on every page
    public function Footer(): void
    {
        // Get page dimensions and margins
        $pageWidth = $this->getPageWidth();
        $pageHeight = $this->getPageHeight();
        $margin = $this->getMargins();

        // Footer height
        $footerHeight = 15;
        $footerY = $pageHeight - $footerHeight;

        // Draw line above footer in #7f91aa over full width (no margins)
        $this->SetDrawColor(127, 145, 170); // RGB for #7f91aa
        $this->SetLineWidth(0.5);
        $this->Line(0, $footerY, $pageWidth, $footerY);

        // Draw gray background (#dfdfdf) over full width (no margins)
        $this->SetFillColor(223, 223, 223); // RGB for #dfdfdf
        $this->Rect(0, $footerY, $pageWidth, $footerHeight, 'F');

        // Position for text (centered vertically in footer, with margins)
        $textY = $footerY + ($footerHeight / 2) - 2;
        $this->SetY($textY);
        $this->SetX($margin['left']);

        // Set font for footer
        $this->SetFont('helvetica', '', 8);

        // Set text color to black
        $this->SetTextColor(0, 0, 0);

        // Add company address on the left (respecting left margin)
        $leftText = "ConRat WebSolutions GmbH - Gartenstr. 4 - 37281 Wanfried";
        $this->Cell(0, 4, $leftText, 0, 0, 'L');

        // Add URL on the right (respecting right margin)
        $rightText = "https://somesolutions.de";
        $this->SetXY($margin['left'], $textY);
        $this->Cell($pageWidth - $margin['left'] - $margin['right'], 4, $rightText, 0, 0, 'R');
    }
}

class PDFConfig
{
    private CustomTCPDF $pdf;
    private array $config;

    public function __construct()
    {
        try {
            $this->loadConfig();
            $this->initializePDF();
        } catch (Throwable $e) {
            error_log("PDFConfig constructor error: " . $e->getMessage() . " | File: " . $e->getFile() . ":" . $e->getLine());
            throw $e;
        }
    }

    /**
     * Load PDF configuration
     */
    private function loadConfig(): void
    {
        $this->config = [
            'title' => 'DSGVO-Auftragsverarbeitungsvereinbarung',
            'author' => 'DSGVO ADV System',
            'subject' => 'Auftragsverarbeitungsvereinbarung nach DSGVO',
            'keywords' => 'DSGVO, Auftragsverarbeitung, Datenschutz, Vereinbarung',
            'creator' => 'DSGVO ADV System',
            'font_family' => 'helvetica',
            'font_size' => 10,
            'margin_top' => 20,
            'margin_bottom' => 20,
            'margin_left' => 15,
            'margin_right' => 15
        ];
    }

    /**
     * Initialize TCPDF with configuration
     */
    private function initializePDF(): void
    {
        // Prüfe ob TCPDF-Klasse existiert
        if (!class_exists('TCPDF')) {
            throw new Exception('TCPDF-Klasse nicht gefunden. Bitte Composer-Abhängigkeiten installieren (composer install).');
        }

        // Create custom TCPDF instance with header image support
        try {
            $this->pdf = new CustomTCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        } catch (Throwable $e) {
            error_log("CustomTCPDF creation failed: " . $e->getMessage());
            throw new Exception("Fehler beim Erstellen der PDF-Instanz: " . $e->getMessage());
        }

        // Document information
        $this->pdf->SetCreator($this->config['creator']);
        $this->pdf->SetAuthor($this->config['author']);
        $this->pdf->SetTitle($this->config['title']);
        $this->pdf->SetSubject($this->config['subject']);
        $this->pdf->SetKeywords($this->config['keywords']);

        // Header and footer - enable header to show image on every page
        $this->pdf->setPrintHeader(true);
        $this->pdf->setPrintFooter(true);

        // WICHTIG: Header-Image-Pfad MUSS VOR AddPage() gesetzt werden!
        // Versuche zuerst PNG, dann JPG
        $headerImagePath = __DIR__ . '/../templates/header.png';

        // Prüfe ob GD oder Imagick verfügbar ist
        $hasImageSupport = extension_loaded('gd') || extension_loaded('imagick');

        // Wenn PNG und keine Image-Support, versuche JPG
        if (!$hasImageSupport && file_exists($headerImagePath)) {
            $jpgPath = str_replace('.png', '.jpg', $headerImagePath);
            if (file_exists($jpgPath)) {
                $headerImagePath = $jpgPath;
            }
        }

        // Set header image path VOR AddPage(), damit es auch auf der ersten Seite erscheint
        if (file_exists($headerImagePath)) {
            $this->pdf->setHeaderImagePath($headerImagePath);
        }

        // Setze erstmal Standard-Margins, werden nach AddPage() angepasst
        $this->pdf->SetMargins($this->config['margin_left'], $this->config['margin_top'], $this->config['margin_right']);
        $this->pdf->SetHeaderMargin(0);
        $this->pdf->SetFooterMargin(10);

        // Set auto page breaks
        $this->pdf->SetAutoPageBreak(TRUE, $this->config['margin_bottom']);

        // WICHTIG: getPageWidth() funktioniert nur NACH AddPage()!
        // Daher erst AddPage() aufrufen, dann Header-Höhe berechnen und Margins anpassen
        $this->pdf->AddPage();

        // Calculate header height to set top margin correctly
        $headerHeight = 0;

        if (file_exists($headerImagePath)) {
            $pageWidth = $this->pdf->getPageWidth();
            $imageInfo = getimagesize($headerImagePath);
            if ($imageInfo !== false) {
                $imageWidth = $imageInfo[0];
                $imageHeight = $imageInfo[1];
                $headerHeight = ($pageWidth / $imageWidth) * $imageHeight;
            }
        }

        // Set top margin to header height + spacing so content starts below the image
        // Wenn kein Header-Bild, verwende Standard-Margin
        $topMargin = $headerHeight > 0 ? $headerHeight + 15 : $this->config['margin_top'];
        $this->pdf->SetMargins($this->config['margin_left'], $topMargin, $this->config['margin_right']);

        // Y-Position zurücksetzen, damit Content unter dem Header-Bild beginnt
        $this->pdf->SetY($topMargin);

        // Set font
        $this->pdf->SetFont($this->config['font_family'], '', $this->config['font_size']);
    }

    /**
     * Generate DSGVO agreement PDF
     * @param array $userData
     * @return string PDF file path
     */
    public function generateAgreementPDF(array $userData): string
    {
        try {
            // Generate filename
            $filename = $this->generateFilename($userData);
            $filepath = $this->getPDFPath($filename);

            // Generate PDF content
            $this->addHeader();
            $this->addAgreementContent($userData);
            $this->addAnlagen($userData);
            $this->addFooter();

            // Output PDF
            $this->pdf->Output($filepath, 'F');

            return $filepath;
        } catch (Throwable $e) {
            $errorMsg = "PDF generation error: " . $e->getMessage() . " | File: " . $e->getFile() . " | Line: " . $e->getLine();
            error_log($errorMsg);
            error_log("Stack trace: " . $e->getTraceAsString());
            throw new Exception("Failed to generate PDF: " . $e->getMessage());
        }
    }

    /**
     * Add header to PDF with title (only on first page)
     * Note: Header image is added automatically via Header() method on every page
     * Top margin is already set to header height + spacing, so content starts below the image
     */
    private function addHeader(): void
    {
        // Top margin is already set correctly, so we're already positioned below the image
        // Just add the title
        $this->pdf->SetFont($this->config['font_family'], 'B', 16);
        $this->pdf->Cell(0, 10, 'Vereinbarung zur Auftragsverarbeitung nach Art. 28 DSGVO', 0, 1, 'C');
        $this->pdf->Ln(5);
    }

    /**
     * Remove leading whitespace from HTML content
     * @param string $html
     * @return string
     */
    private function cleanHtmlWhitespace(string $html): string
    {
        // Entferne führende Leerzeichen/Zeilenumbrüche vor Tags
        $html = preg_replace('/^\s+/m', '', $html);
        // Entferne Leerzeichen direkt vor öffnenden Tags
        $html = preg_replace('/\s+</', '<', $html);
        // Entferne Leerzeichen direkt nach schließenden Tags
        $html = preg_replace('/>\s+/', '>', $html);
        // Entferne mehrfache Leerzeichen
        $html = preg_replace('/\s{2,}/', ' ', $html);
        return trim($html);
    }

    /**
     * Add agreement content to PDF using AgreementTemplate
     */
    private function addAgreementContent(array $userData): void
    {
        // Include the AgreementTemplate class
        require_once __DIR__ . '/../templates/agreement_template.php';

        // Get the agreement text from the template
        $agreementText = AgreementTemplate::generateAgreementText($userData);

        // Entferne führende Leerzeichen
        $agreementText = $this->cleanHtmlWhitespace($agreementText);

        // Add spacing between list items by modifying HTML structure
        // TCPDF doesn't reliably support CSS margins, so we add <br> tags
        $htmlContent = preg_replace('/(<\/li>)/i', '$1<br>', $agreementText);

        // Set font for the content
        $this->pdf->SetFont($this->config['font_family'], '', 10);

        // Add the text as HTML content to PDF (TCPDF supports basic HTML)
        $this->pdf->writeHTML($htmlContent, true, false, true, false, '');
    }

    /**
     * Add Anlagen (appendices) to PDF as separate pages
     */
    private function addAnlagen(array $userData = []): void
    {
        // Determine which Anlage 1 to load based on service
        $dienstleistung = $userData['dienstleistung'] ?? 'webhosting';

        // Map service to Anlage 1 class and file
        $anlage1Map = [
            'webhosting' => ['file' => 'anlage1-webhosting.php', 'class' => 'Anlage1Webhosting'],
            'chatbot4you' => ['file' => 'anlage1-chatbot4you.php', 'class' => 'Anlage1Chatbot4you'],
            'conrat-ai' => ['file' => 'anlage1-conrat-ai.php', 'class' => 'Anlage1ConratAi'],
            'adventskalender' => ['file' => 'anlage1-adventskalender.php', 'class' => 'Anlage1Adventskalender']
        ];

        // Default to webhosting if service not found
        $anlage1Config = $anlage1Map[$dienstleistung] ?? $anlage1Map['webhosting'];

        // Load Anlagen classes
        require_once __DIR__ . '/../templates/' . $anlage1Config['file'];
        require_once __DIR__ . '/../templates/anlage2.php';
        require_once __DIR__ . '/../templates/anlage3.php';
        require_once __DIR__ . '/../templates/anlage4.php';

        // Add each Anlage as a new page
        $anlagen = [
            ['class' => $anlage1Config['class'], 'title' => 'Anlage 1 - Gegenstand der Verarbeitung'],
            ['class' => 'Anlage2', 'title' => 'Anlage 2 - Weisungsberechtigte Personen und Datenschutzbeauftragter'],
            ['class' => 'Anlage3', 'title' => 'Anlage 3 - Unterauftragnehmer'],
            ['class' => 'Anlage4', 'title' => 'Anlage 4 - Technische und organisatorische Maßnahmen']
        ];

        foreach ($anlagen as $anlage) {
            // Add new page for each Anlage
            // Header image will be added automatically via Header() method
            // Header() method already sets Y position below the image
            $this->pdf->AddPage();

            // Optional: Add a title for the Anlage
            $this->pdf->SetFont($this->config['font_family'], 'B', 14);
            $this->pdf->Cell(0, 10, $anlage['title'], 0, 1, 'L');
            $this->pdf->Ln(5);

            // Get content from the Anlage class
            $content = call_user_func([$anlage['class'], 'getContent']);

            // Entferne führende Leerzeichen
            $content = $this->cleanHtmlWhitespace($content);

            // Add spacing between list items by modifying HTML structure
            // TCPDF doesn't reliably support CSS margins, so we add <br> tags
            $htmlContent = preg_replace('/(<\/li>)/i', '$1<br>', $content);

            // Set font for the content
            $this->pdf->SetFont($this->config['font_family'], '', 10);

            // Add the content as HTML
            $this->pdf->writeHTML($htmlContent, true, false, true, false, '');

            // Add date note at the end of each Anlage
            $this->pdf->Ln(10);
            $this->pdf->SetFont($this->config['font_family'], 'I', 9);
            $currentDate = date('d.m.Y');
            $this->pdf->Cell(0, 5, 'Erstellt am: ' . $currentDate, 0, 1, 'L');
        }
    }

    /**
     * Add footer to PDF
     */
    private function addFooter(): void
    {
        # $this->pdf->SetY(-15);
        #  $this->pdf->SetFont($this->config['font_family'], 'I', 8);
        # $this->pdf->Cell(0, 10, 'Erstellt am ' . date('d.m.Y H:i') . ' | DSGVO ADV System', 0, 0, 'C');
    }

    /**
     * Generate filename for PDF
     */
    private function generateFilename(array $userData): string
    {
        $company = preg_replace('/[^a-zA-Z0-9_-]/', '_', $userData['firma']);
        $date = date('Y-m-d_H-i-s');
        return "Auftragsverarbeitungsvereinbarung_{$company}_{$date}.pdf";
    }

    /**
     * Get PDF storage path
     */
    private function getPDFPath(string $filename): string
    {
        $pdfDir = __DIR__ . '/../storage/pdfs';
        if (!is_dir($pdfDir)) {
            mkdir($pdfDir, 0755, true);
        }
        return $pdfDir . '/' . $filename;
    }

    /**
     * Test PDF generation
     * @return array
     */
    public function testPDFGeneration(): array
    {
        try {
            $testData = [
                'firma' => 'Test GmbH',
                'vorname' => 'Max',
                'name' => 'Mustermann',
                'anschrift' => 'Musterstraße 1',
                'plz' => '12345',
                'ort' => 'Musterstadt',
                'email' => 'test@example.com',
                'ansprechpartner' => 'Max Mustermann'
            ];

            $filepath = $this->generateAgreementPDF($testData);

            return [
                'success' => true,
                'message' => 'PDF generation successful',
                'filepath' => $filepath,
                'filesize' => filesize($filepath)
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'PDF generation failed: ' . $e->getMessage()
            ];
        }
    }
}
