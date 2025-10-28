<?php
/**
 * PDF Configuration for DSGVO ADV Project
 * TCPDF integration for generating DSGVO agreements
 */

// Load Composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// TCPDF wird direkt über den Namespace verwendet

class PDFConfig {
    private \TCPDF $pdf;
    private array $config;
    
    public function __construct() {
        $this->loadConfig();
        $this->initializePDF();
    }
    
    /**
     * Load PDF configuration
     */
    private function loadConfig(): void {
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
    private function initializePDF(): void {
        $this->pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // Document information
        $this->pdf->SetCreator($this->config['creator']);
        $this->pdf->SetAuthor($this->config['author']);
        $this->pdf->SetTitle($this->config['title']);
        $this->pdf->SetSubject($this->config['subject']);
        $this->pdf->SetKeywords($this->config['keywords']);
        
        // Header and footer
        $this->pdf->setPrintHeader(false);
        $this->pdf->setPrintFooter(true);
        
        // Set margins
        $this->pdf->SetMargins($this->config['margin_left'], $this->config['margin_top'], $this->config['margin_right']);
        $this->pdf->SetHeaderMargin(5);
        $this->pdf->SetFooterMargin(10);
        
        // Set auto page breaks
        $this->pdf->SetAutoPageBreak(TRUE, $this->config['margin_bottom']);
        
        // Set font
        $this->pdf->SetFont($this->config['font_family'], '', $this->config['font_size']);
        
        // Add a page
        $this->pdf->AddPage();
    }
    
    /**
     * Generate DSGVO agreement PDF
     * @param array $userData
     * @return string PDF file path
     */
    public function generateAgreementPDF(array $userData): string {
        try {
            // Generate filename
            $filename = $this->generateFilename($userData);
            $filepath = $this->getPDFPath($filename);
            
            // Generate PDF content
            $this->addHeader();
            $this->addAgreementContent($userData);
            $this->addFooter();
            
            // Output PDF
            $this->pdf->Output($filepath, 'F');
            
            return $filepath;
            
        } catch (Exception $e) {
            error_log("PDF generation error: " . $e->getMessage());
            throw new Exception("Failed to generate PDF: " . $e->getMessage());
        }
    }
    
    /**
     * Add header to PDF
     */
    private function addHeader(): void {
        $this->pdf->SetFont($this->config['font_family'], 'B', 16);
        $this->pdf->Cell(0, 10, 'Vereinbarung zur Auftragsverarbeitung nach Art. 28 DSGVO', 0, 1, 'C');
        $this->pdf->Ln(5);
        
     
    }
    
    /**
     * Add agreement content to PDF using AgreementTemplate
     */
    private function addAgreementContent(array $userData): void {
        // Include the AgreementTemplate class
        require_once __DIR__ . '/../templates/agreement_template.php';
        
        // Get the agreement text from the template
        $agreementText = AgreementTemplate::generateAgreementText($userData);
        
        // Set font for the content
        $this->pdf->SetFont($this->config['font_family'], '', 10);
        
        // Add the text as HTML content to PDF (TCPDF supports basic HTML)
        $this->pdf->writeHTML($agreementText, true, false, true, false, '');
    }
    
    /**
     * Add footer to PDF
     */
    private function addFooter(): void {
       # $this->pdf->SetY(-15);
       #  $this->pdf->SetFont($this->config['font_family'], 'I', 8);
       # $this->pdf->Cell(0, 10, 'Erstellt am ' . date('d.m.Y H:i') . ' | DSGVO ADV System', 0, 0, 'C');
    }
    
    /**
     * Generate filename for PDF
     */
    private function generateFilename(array $userData): string {
        $company = preg_replace('/[^a-zA-Z0-9_-]/', '_', $userData['firma']);
        $date = date('Y-m-d_H-i-s');
        return "Auftragsverarbeitungsvereinbarung_{$company}_{$date}.pdf";
    }
    
    /**
     * Get PDF storage path
     */
    private function getPDFPath(string $filename): string {
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
    public function testPDFGeneration(): array {
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
