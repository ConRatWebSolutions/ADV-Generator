<?php
/**
 * Integration Tests for DSGVO ADV Project
 * End-to-end tests for the complete workflow
 */

use PHPUnit\Framework\TestCase;

class IntegrationTest extends TestCase {
    
    private $baseUrl;
    private $testData;
    private $pdo;
    
    protected function setUp(): void {
        $this->baseUrl = 'http://localhost:8080'; // DDEV URL
        $this->testData = [
            'vorname' => 'Max',
            'name' => 'Mustermann',
            'email' => 'max.mustermann@example.com',
            'firma' => 'Test GmbH',
            'ansprechpartner' => 'Max Mustermann',
            'anschrift' => 'Musterstraße 123',
            'plz' => '12345',
            'ort' => 'Musterstadt'
        ];
        
        // Database connection for verification
        $this->pdo = new PDO(
            'mysql:host=localhost;dbname=dsgvo_adv',
            'db',
            'db',
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
    }
    
    /**
     * Test complete form submission workflow
     */
    public function testCompleteFormSubmissionWorkflow() {
        // Step 1: Get CSRF token
        $csrfToken = $this->getCSRFToken();
        $this->assertNotEmpty($csrfToken);
        
        // Step 2: Submit form with valid data
        $response = $this->submitForm($this->testData, $csrfToken);
        $this->assertTrue($response['success']);
        $this->assertArrayHasKey('agreement_id', $response);
        
        $agreementId = $response['agreement_id'];
        
        // Step 3: Verify database entry
        $agreement = $this->getAgreementFromDatabase($agreementId);
        $this->assertNotFalse($agreement);
        $this->assertEquals($this->testData['firma'], $agreement['firma']);
        $this->assertEquals($this->testData['email'], $agreement['email']);
        
        // Step 4: Verify PDF was generated
        $this->assertNotEmpty($agreement['pdf_pfad']);
        $this->assertTrue(file_exists($agreement['pdf_pfad']));
        
        // Step 5: Verify email logs
        $emailLogs = $this->getEmailLogsForAgreement($agreementId);
        $this->assertCount(2, $emailLogs); // User and admin emails
        
        // Step 6: Verify system logs
        $systemLogs = $this->getSystemLogsForAgreement($agreementId);
        $this->assertGreaterThan(0, count($systemLogs));
        
        // Cleanup
        $this->cleanupTestData($agreementId);
    }
    
    /**
     * Test form validation with invalid data
     */
    public function testFormValidationWithInvalidData() {
        $invalidData = [
            'vorname' => '', // Empty required field
            'name' => 'Mustermann',
            'email' => 'invalid-email', // Invalid email
            'firma' => 'Test GmbH',
            'ansprechpartner' => 'Max Mustermann',
            'anschrift' => 'Musterstraße 123',
            'plz' => '123', // Invalid PLZ
            'ort' => 'Musterstadt'
        ];
        
        $csrfToken = $this->getCSRFToken();
        $response = $this->submitForm($invalidData, $csrfToken);
        
        $this->assertFalse($response['success']);
        $this->assertStringContainsString('erforderlich', $response['message']);
    }
    
    /**
     * Test rate limiting
     */
    public function testRateLimiting() {
        $csrfToken = $this->getCSRFToken();
        
        // Submit multiple requests quickly
        for ($i = 0; $i < 6; $i++) {
            $response = $this->submitForm($this->testData, $csrfToken);
            
            if ($i < 5) {
                // First 5 requests should succeed or fail due to validation
                $this->assertTrue(isset($response['success']));
            } else {
                // 6th request should be rate limited
                $this->assertFalse($response['success']);
                $this->assertStringContainsString('Zu viele Anfragen', $response['message']);
            }
        }
    }
    
    /**
     * Test CSRF protection
     */
    public function testCSRFProtection() {
        // Submit form without CSRF token
        $response = $this->submitForm($this->testData, '');
        
        $this->assertFalse($response['success']);
        $this->assertStringContainsString('Ungültige Anfrage', $response['message']);
    }
    
    /**
     * Test XSS protection
     */
    public function testXSSProtection() {
        $xssData = [
            'vorname' => '<script>alert("xss")</script>Max',
            'name' => 'Mustermann',
            'email' => 'test@example.com',
            'firma' => '<img src=x onerror=alert("xss")>Test GmbH',
            'ansprechpartner' => 'Max Mustermann',
            'anschrift' => 'Musterstraße 123',
            'plz' => '12345',
            'ort' => 'Musterstadt'
        ];
        
        $csrfToken = $this->getCSRFToken();
        $response = $this->submitForm($xssData, $csrfToken);
        
        if ($response['success']) {
            $agreementId = $response['agreement_id'];
            $agreement = $this->getAgreementFromDatabase($agreementId);
            
            // Verify XSS was sanitized
            $this->assertStringNotContainsString('<script>', $agreement['vorname']);
            $this->assertStringNotContainsString('<img', $agreement['firma']);
            $this->assertStringContainsString('&lt;script&gt;', $agreement['vorname']);
            
            $this->cleanupTestData($agreementId);
        }
    }
    
    /**
     * Test database transaction integrity
     */
    public function testDatabaseTransactionIntegrity() {
        // This test would simulate a failure scenario
        // and verify that database remains consistent
        
        $csrfToken = $this->getCSRFToken();
        
        // Count initial records
        $initialCount = $this->getAgreementCount();
        
        // Submit form
        $response = $this->submitForm($this->testData, $csrfToken);
        
        if ($response['success']) {
            $agreementId = $response['agreement_id'];
            
            // Verify record was created
            $newCount = $this->getAgreementCount();
            $this->assertEquals($initialCount + 1, $newCount);
            
            // Verify related records
            $emailLogs = $this->getEmailLogsForAgreement($agreementId);
            $this->assertGreaterThan(0, count($emailLogs));
            
            $this->cleanupTestData($agreementId);
        }
    }
    
    /**
     * Test email delivery (if mailhog is available)
     */
    public function testEmailDelivery() {
        $csrfToken = $this->getCSRFToken();
        $response = $this->submitForm($this->testData, $csrfToken);
        
        if ($response['success']) {
            $agreementId = $response['agreement_id'];
            
            // Wait a moment for email processing
            sleep(2);
            
            // Check email logs
            $emailLogs = $this->getEmailLogsForAgreement($agreementId);
            $this->assertGreaterThan(0, count($emailLogs));
            
            // Verify at least one email was sent successfully
            $successfulEmails = array_filter($emailLogs, function($log) {
                return $log['status'] === 'versendet';
            });
            $this->assertGreaterThan(0, count($successfulEmails));
            
            $this->cleanupTestData($agreementId);
        }
    }
    
    /**
     * Test PDF generation and storage
     */
    public function testPDFGenerationAndStorage() {
        $csrfToken = $this->getCSRFToken();
        $response = $this->submitForm($this->testData, $csrfToken);
        
        if ($response['success']) {
            $agreementId = $response['agreement_id'];
            $agreement = $this->getAgreementFromDatabase($agreementId);
            
            // Verify PDF path is set
            $this->assertNotEmpty($agreement['pdf_pfad']);
            
            // Verify PDF file exists
            $pdfPath = $agreement['pdf_pfad'];
            $this->assertTrue(file_exists($pdfPath));
            
            // Verify PDF content
            $pdfContent = file_get_contents($pdfPath);
            $this->assertStringContainsString('%PDF', $pdfContent);
            $this->assertStringContainsString($this->testData['firma'], $pdfContent);
            $this->assertStringContainsString($this->testData['ansprechpartner'], $pdfContent);
            
            $this->cleanupTestData($agreementId);
        }
    }
    
    /**
     * Test error handling and logging
     */
    public function testErrorHandlingAndLogging() {
        // Submit form with invalid data to trigger errors
        $invalidData = [
            'vorname' => '',
            'name' => '',
            'email' => 'invalid',
            'firma' => '',
            'ansprechpartner' => '',
            'anschrift' => '',
            'plz' => '',
            'ort' => ''
        ];
        
        $csrfToken = $this->getCSRFToken();
        $response = $this->submitForm($invalidData, $csrfToken);
        
        $this->assertFalse($response['success']);
        
        // Check that error was logged
        $systemLogs = $this->getRecentSystemLogs();
        $errorLogs = array_filter($systemLogs, function($log) {
            return $log['log_level'] === 'error';
        });
        
        // Should have error logs for validation failures
        $this->assertGreaterThan(0, count($errorLogs));
    }
    
    /**
     * Get CSRF token
     */
    private function getCSRFToken() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl . '/get_csrf_token.php');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'X-Requested-With: XMLHttpRequest'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200) {
            $data = json_decode($response, true);
            return $data['token'] ?? '';
        }
        
        return '';
    }
    
    /**
     * Submit form data
     */
    private function submitForm($data, $csrfToken) {
        $data['csrf_token'] = $csrfToken;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl . '/process_form.php');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'X-Requested-With: XMLHttpRequest'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200) {
            return json_decode($response, true);
        }
        
        return ['success' => false, 'message' => 'HTTP Error: ' . $httpCode];
    }
    
    /**
     * Get agreement from database
     */
    private function getAgreementFromDatabase($agreementId) {
        $stmt = $this->pdo->prepare("SELECT * FROM auftragsverarbeitungsvereinbarungen WHERE id = ?");
        $stmt->execute([$agreementId]);
        return $stmt->fetch();
    }
    
    /**
     * Get email logs for agreement
     */
    private function getEmailLogsForAgreement($agreementId) {
        $stmt = $this->pdo->prepare("SELECT * FROM email_logs WHERE agreement_id = ?");
        $stmt->execute([$agreementId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get system logs for agreement
     */
    private function getSystemLogsForAgreement($agreementId) {
        $stmt = $this->pdo->prepare("SELECT * FROM system_logs WHERE JSON_EXTRACT(kontext, '$.agreement_id') = ?");
        $stmt->execute([$agreementId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get recent system logs
     */
    private function getRecentSystemLogs() {
        $stmt = $this->pdo->prepare("SELECT * FROM system_logs ORDER BY erstellt_am DESC LIMIT 10");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Get agreement count
     */
    private function getAgreementCount() {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM auftragsverarbeitungsvereinbarungen");
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    
    /**
     * Cleanup test data
     */
    private function cleanupTestData($agreementId) {
        // Delete PDF file if exists
        $stmt = $this->pdo->prepare("SELECT pdf_pfad FROM auftragsverarbeitungsvereinbarungen WHERE id = ?");
        $stmt->execute([$agreementId]);
        $agreement = $stmt->fetch();
        
        if ($agreement && $agreement['pdf_pfad'] && file_exists($agreement['pdf_pfad'])) {
            unlink($agreement['pdf_pfad']);
        }
        
        // Delete database records
        $this->pdo->prepare("DELETE FROM email_logs WHERE agreement_id = ?")->execute([$agreementId]);
        $this->pdo->prepare("DELETE FROM system_logs WHERE JSON_EXTRACT(kontext, '$.agreement_id') = ?")->execute([$agreementId]);
        $this->pdo->prepare("DELETE FROM auftragsverarbeitungsvereinbarungen WHERE id = ?")->execute([$agreementId]);
    }
}
