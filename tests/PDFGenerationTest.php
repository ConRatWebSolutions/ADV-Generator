<?php
/**
 * PDF Generation Tests for DSGVO ADV Project
 * Unit tests for PDF generation functionality
 */

use PHPUnit\Framework\TestCase;
use TCPDF;

class PDFGenerationTest extends TestCase {
    
    private $pdf;
    private $testData;
    private $outputDir;
    
    protected function setUp(): void {
        $this->outputDir = sys_get_temp_dir() . '/pdf_tests/';
        if (!is_dir($this->outputDir)) {
            mkdir($this->outputDir, 0755, true);
        }
        
        $this->testData = [
            'firma' => 'Test GmbH',
            'ansprechpartner' => 'Max Mustermann',
            'email' => 'max.mustermann@example.com',
            'anschrift' => 'Musterstraße 123',
            'plz' => '12345',
            'ort' => 'Musterstadt',
            'vorname' => 'Max',
            'name' => 'Mustermann'
        ];
        
        $this->pdf = new TCPDF();
        $this->pdf->SetCreator('DSGVO ADV System');
        $this->pdf->SetAuthor('Conrat GmbH');
        $this->pdf->SetTitle('Auftragsverarbeitungsvereinbarung');
        $this->pdf->SetSubject('DSGVO-konforme Auftragsverarbeitungsvereinbarung');
    }
    
    protected function tearDown(): void {
        // Clean up test files
        if (is_dir($this->outputDir)) {
            $files = glob($this->outputDir . '*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            rmdir($this->outputDir);
        }
    }
    
    /**
     * Test PDF creation
     */
    public function testPDFCreation() {
        $this->assertInstanceOf(TCPDF::class, $this->pdf);
        $this->assertEquals('DSGVO ADV System', $this->pdf->getCreator());
        $this->assertEquals('Conrat GmbH', $this->pdf->getAuthor());
        $this->assertEquals('Auftragsverarbeitungsvereinbarung', $this->pdf->getTitle());
    }
    
    /**
     * Test PDF content generation
     */
    public function testPDFContentGeneration() {
        $this->pdf->AddPage();
        
        // Add header
        $this->pdf->SetFont('helvetica', 'B', 16);
        $this->pdf->Cell(0, 10, 'AUFTRAGSVERARBEITUNGSVEREINBARUNG', 0, 1, 'C');
        $this->pdf->Ln(10);
        
        // Add company information
        $this->pdf->SetFont('helvetica', '', 12);
        $this->pdf->Cell(0, 8, 'Firma: ' . $this->testData['firma'], 0, 1);
        $this->pdf->Cell(0, 8, 'Ansprechpartner: ' . $this->testData['ansprechpartner'], 0, 1);
        $this->pdf->Cell(0, 8, 'E-Mail: ' . $this->testData['email'], 0, 1);
        
        $this->assertTrue($this->pdf->getPage() > 0);
    }
    
    /**
     * Test PDF with agreement template
     */
    public function testPDFWithAgreementTemplate() {
        $this->pdf->AddPage();
        
        // Add agreement content
        $agreementText = $this->generateAgreementText($this->testData);
        $this->pdf->SetFont('helvetica', '', 10);
        $this->pdf->writeHTML($agreementText, true, false, true, false, '');
        
        $this->assertTrue($this->pdf->getPage() > 0);
    }
    
    /**
     * Test PDF file output
     */
    public function testPDFFileOutput() {
        $filename = $this->outputDir . 'test_agreement.pdf';
        
        $this->pdf->AddPage();
        $this->pdf->SetFont('helvetica', '', 12);
        $this->pdf->Cell(0, 10, 'Test Agreement Content', 0, 1);
        
        $this->pdf->Output($filename, 'F');
        
        $this->assertTrue(file_exists($filename));
        $this->assertGreaterThan(0, filesize($filename));
        
        // Verify PDF content
        $content = file_get_contents($filename);
        $this->assertStringContainsString('%PDF', $content);
        $this->assertStringContainsString('Test Agreement Content', $content);
    }
    
    /**
     * Test PDF with Unicode characters
     */
    public function testPDFWithUnicode() {
        $unicodeData = [
            'firma' => 'Müller & Söhne GmbH',
            'ansprechpartner' => 'Jürgen Müller',
            'email' => 'jürgen.müller@example.com',
            'anschrift' => 'Straße 123',
            'plz' => '12345',
            'ort' => 'München'
        ];
        
        $this->pdf->AddPage();
        $this->pdf->SetFont('helvetica', '', 12);
        
        $this->pdf->Cell(0, 8, 'Firma: ' . $unicodeData['firma'], 0, 1);
        $this->pdf->Cell(0, 8, 'Ansprechpartner: ' . $unicodeData['ansprechpartner'], 0, 1);
        $this->pdf->Cell(0, 8, 'E-Mail: ' . $unicodeData['email'], 0, 1);
        $this->pdf->Cell(0, 8, 'Adresse: ' . $unicodeData['anschrift'], 0, 1);
        $this->pdf->Cell(0, 8, 'Ort: ' . $unicodeData['ort'], 0, 1);
        
        $filename = $this->outputDir . 'unicode_test.pdf';
        $this->pdf->Output($filename, 'F');
        
        $this->assertTrue(file_exists($filename));
        
        // Verify Unicode content is preserved
        $content = file_get_contents($filename);
        $this->assertStringContainsString('Müller', $content);
        $this->assertStringContainsString('Jürgen', $content);
    }
    
    /**
     * Test PDF metadata
     */
    public function testPDFMetadata() {
        $this->pdf->SetCreator('Test Creator');
        $this->pdf->SetAuthor('Test Author');
        $this->pdf->SetTitle('Test Title');
        $this->pdf->SetSubject('Test Subject');
        $this->pdf->SetKeywords('DSGVO, Auftragsverarbeitung, Test');
        
        $this->assertEquals('Test Creator', $this->pdf->getCreator());
        $this->assertEquals('Test Author', $this->pdf->getAuthor());
        $this->assertEquals('Test Title', $this->pdf->getTitle());
        $this->assertEquals('Test Subject', $this->pdf->getSubject());
        $this->assertEquals('DSGVO, Auftragsverarbeitung, Test', $this->pdf->getKeywords());
    }
    
    /**
     * Test PDF page layout
     */
    public function testPDFPageLayout() {
        $this->pdf->AddPage();
        
        // Test margins
        $this->pdf->SetMargins(20, 20, 20);
        $this->assertEquals(20, $this->pdf->getMargins()['left']);
        $this->assertEquals(20, $this->pdf->getMargins()['top']);
        $this->assertEquals(20, $this->pdf->getMargins()['right']);
        
        // Test page size
        $this->assertEquals('A4', $this->pdf->getPageFormat());
        
        // Test orientation
        $this->assertEquals('P', $this->pdf->getPageOrientation());
    }
    
    /**
     * Test PDF fonts
     */
    public function testPDFFonts() {
        $this->pdf->AddPage();
        
        // Test different fonts
        $fonts = ['helvetica', 'times', 'courier'];
        
        foreach ($fonts as $font) {
            $this->pdf->SetFont($font, '', 12);
            $this->pdf->Cell(0, 8, "Test with $font font", 0, 1);
        }
        
        $this->assertTrue($this->pdf->getPage() > 0);
    }
    
    /**
     * Test PDF with tables
     */
    public function testPDFWithTables() {
        $this->pdf->AddPage();
        $this->pdf->SetFont('helvetica', '', 10);
        
        // Create table
        $html = '
        <table border="1" cellpadding="4">
            <tr>
                <th>Feld</th>
                <th>Wert</th>
            </tr>
            <tr>
                <td>Firma</td>
                <td>' . $this->testData['firma'] . '</td>
            </tr>
            <tr>
                <td>Ansprechpartner</td>
                <td>' . $this->testData['ansprechpartner'] . '</td>
            </tr>
            <tr>
                <td>E-Mail</td>
                <td>' . $this->testData['email'] . '</td>
            </tr>
        </table>
        ';
        
        $this->pdf->writeHTML($html, true, false, true, false, '');
        
        $filename = $this->outputDir . 'table_test.pdf';
        $this->pdf->Output($filename, 'F');
        
        $this->assertTrue(file_exists($filename));
    }
    
    /**
     * Test PDF with signatures
     */
    public function testPDFWithSignatures() {
        $this->pdf->AddPage();
        $this->pdf->SetFont('helvetica', '', 12);
        
        // Add signature fields
        $this->pdf->Cell(0, 8, 'Unterschriften:', 0, 1);
        $this->pdf->Ln(10);
        
        // Auftraggeber signature
        $this->pdf->Cell(80, 8, 'Auftraggeber (Conrat GmbH):', 0, 0);
        $this->pdf->Cell(80, 8, '_________________________', 0, 1);
        $this->pdf->Cell(80, 8, 'Datum: _______________', 0, 1);
        $this->pdf->Ln(10);
        
        // Auftragsverarbeiter signature
        $this->pdf->Cell(80, 8, 'Auftragsverarbeiter (' . $this->testData['firma'] . '):', 0, 0);
        $this->pdf->Cell(80, 8, '_________________________', 0, 1);
        $this->pdf->Cell(80, 8, 'Datum: _______________', 0, 1);
        
        $filename = $this->outputDir . 'signature_test.pdf';
        $this->pdf->Output($filename, 'F');
        
        $this->assertTrue(file_exists($filename));
    }
    
    /**
     * Test PDF error handling
     */
    public function testPDFErrorHandling() {
        $this->expectException(Exception::class);
        
        // Try to use invalid font
        $this->pdf->SetFont('invalid_font', '', 12);
    }
    
    /**
     * Test PDF file size
     */
    public function testPDFFileSize() {
        $this->pdf->AddPage();
        $this->pdf->SetFont('helvetica', '', 12);
        
        // Add substantial content
        for ($i = 0; $i < 50; $i++) {
            $this->pdf->Cell(0, 8, "Line $i: This is test content for PDF generation.", 0, 1);
        }
        
        $filename = $this->outputDir . 'size_test.pdf';
        $this->pdf->Output($filename, 'F');
        
        $this->assertTrue(file_exists($filename));
        $this->assertGreaterThan(1000, filesize($filename)); // Should be at least 1KB
    }
    
    /**
     * Test PDF with images (if any)
     */
    public function testPDFWithImages() {
        $this->pdf->AddPage();
        $this->pdf->SetFont('helvetica', '', 12);
        
        // Add text content
        $this->pdf->Cell(0, 8, 'Test PDF with image support', 0, 1);
        
        // Note: In a real implementation, you might add logo or other images
        // For now, we just test that the PDF can handle image-related operations
        
        $filename = $this->outputDir . 'image_test.pdf';
        $this->pdf->Output($filename, 'F');
        
        $this->assertTrue(file_exists($filename));
    }
    
    /**
     * Generate agreement text for testing
     */
    private function generateAgreementText($data) {
        return "
        <h2>AUFTRAGSVERARBEITUNGSVEREINBARUNG</h2>
        <p><strong>zwischen</strong></p>
        <p>Conrat GmbH<br>
        Musterstraße 123<br>
        12345 Musterstadt</p>
        <p><strong>und</strong></p>
        <p>{$data['firma']}<br>
        {$data['anschrift']}<br>
        {$data['plz']} {$data['ort']}<br>
        E-Mail: {$data['email']}<br>
        Ansprechpartner: {$data['ansprechpartner']}</p>
        <h3>§ 1 Gegenstand der Auftragsverarbeitung</h3>
        <p>Gegenstand dieser Vereinbarung ist die Verarbeitung personenbezogener Daten durch den Auftragsverarbeiter im Auftrag des Auftraggebers.</p>
        ";
    }
}
