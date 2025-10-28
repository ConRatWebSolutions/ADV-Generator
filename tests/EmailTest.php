<?php
/**
 * Email Tests for DSGVO ADV Project
 * Unit tests for email functionality
 */

use PHPUnit\Framework\TestCase;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailTest extends TestCase {
    
    private $mailer;
    private $testEmail = 'test@example.com';
    private $adminEmail = 'admin@example.com';
    
    protected function setUp(): void {
        // Create PHPMailer instance for testing
        $this->mailer = new PHPMailer(true);
        $this->mailer->isSMTP();
        $this->mailer->Host = 'localhost';
        $this->mailer->Port = 1025; // Mailhog port for testing
        $this->mailer->SMTPAuth = false;
        $this->mailer->isHTML(true);
        $this->mailer->CharSet = 'UTF-8';
    }
    
    /**
     * Test email configuration
     */
    public function testEmailConfiguration() {
        $this->assertInstanceOf(PHPMailer::class, $this->mailer);
        $this->assertEquals('localhost', $this->mailer->Host);
        $this->assertEquals(1025, $this->mailer->Port);
        $this->assertTrue($this->mailer->isHTML());
        $this->assertEquals('UTF-8', $this->mailer->CharSet);
    }
    
    /**
     * Test user email creation
     */
    public function testUserEmailCreation() {
        $agreementData = [
            'firma' => 'Test GmbH',
            'ansprechpartner' => 'Max Mustermann',
            'email' => 'max.mustermann@example.com'
        ];
        
        $emailContent = $this->createUserEmail($agreementData);
        
        $this->assertStringContainsString('Auftragsverarbeitungsvereinbarung', $emailContent['subject']);
        $this->assertStringContainsString('Test GmbH', $emailContent['body']);
        $this->assertStringContainsString('Max Mustermann', $emailContent['body']);
        $this->assertStringContainsString('max.mustermann@example.com', $emailContent['body']);
    }
    
    /**
     * Test admin email creation
     */
    public function testAdminEmailCreation() {
        $agreementData = [
            'firma' => 'Test GmbH',
            'ansprechpartner' => 'Max Mustermann',
            'email' => 'max.mustermann@example.com',
            'anschrift' => 'Musterstraße 123',
            'plz' => '12345',
            'ort' => 'Musterstadt'
        ];
        
        $emailContent = $this->createAdminEmail($agreementData);
        
        $this->assertStringContainsString('Neue Auftragsverarbeitungsvereinbarung', $emailContent['subject']);
        $this->assertStringContainsString('Test GmbH', $emailContent['body']);
        $this->assertStringContainsString('Max Mustermann', $emailContent['body']);
        $this->assertStringContainsString('Musterstraße 123', $emailContent['body']);
    }
    
    /**
     * Test email validation
     */
    public function testEmailValidation() {
        $validEmails = [
            'test@example.com',
            'user.name@domain.co.uk',
            'user+tag@example.org',
            'user123@test-domain.com'
        ];
        
        $invalidEmails = [
            'invalid-email',
            '@example.com',
            'test@',
            'test..test@example.com'
        ];
        
        foreach ($validEmails as $email) {
            $this->assertTrue(filter_var($email, FILTER_VALIDATE_EMAIL), "Valid email failed: $email");
        }
        
        foreach ($invalidEmails as $email) {
            $this->assertFalse(filter_var($email, FILTER_VALIDATE_EMAIL), "Invalid email passed: $email");
        }
    }
    
    /**
     * Test email sanitization
     */
    public function testEmailSanitization() {
        $unsafeData = [
            'firma' => '<script>alert("xss")</script>Test GmbH',
            'ansprechpartner' => 'Max & Co.',
            'email' => 'test@example.com'
        ];
        
        $sanitizedData = $this->sanitizeEmailData($unsafeData);
        
        $this->assertStringNotContainsString('<script>', $sanitizedData['firma']);
        $this->assertStringContainsString('&lt;script&gt;', $sanitizedData['firma']);
        $this->assertStringContainsString('&amp;', $sanitizedData['ansprechpartner']);
    }
    
    /**
     * Test email template rendering
     */
    public function testEmailTemplateRendering() {
        $templateData = [
            'company_name' => 'Test GmbH',
            'contact_person' => 'Max Mustermann',
            'email' => 'max.mustermann@example.com',
            'agreement_date' => date('d.m.Y'),
            'admin_email' => 'admin@example.com'
        ];
        
        $userTemplate = $this->renderUserTemplate($templateData);
        $adminTemplate = $this->renderAdminTemplate($templateData);
        
        $this->assertStringContainsString('Test GmbH', $userTemplate);
        $this->assertStringContainsString('Max Mustermann', $userTemplate);
        $this->assertStringContainsString('Test GmbH', $adminTemplate);
        $this->assertStringContainsString('admin@example.com', $adminTemplate);
    }
    
    /**
     * Test email headers
     */
    public function testEmailHeaders() {
        $this->mailer->setFrom('noreply@conrat.de', 'Conrat GmbH');
        $this->mailer->addAddress('test@example.com', 'Test User');
        $this->mailer->Subject = 'Test Subject';
        $this->mailer->Body = 'Test Body';
        
        $this->assertEquals('noreply@conrat.de', $this->mailer->From);
        $this->assertEquals('Conrat GmbH', $this->mailer->FromName);
        $this->assertCount(1, $this->mailer->getAllRecipientAddresses());
        $this->assertEquals('Test Subject', $this->mailer->Subject);
        $this->assertEquals('Test Body', $this->mailer->Body);
    }
    
    /**
     * Test PDF attachment
     */
    public function testPDFAttachment() {
        $testPdfPath = '/tmp/test.pdf';
        
        // Create dummy PDF file
        file_put_contents($testPdfPath, '%PDF-1.4 test content');
        
        $this->mailer->addAttachment($testPdfPath, 'Auftragsverarbeitungsvereinbarung.pdf');
        
        $attachments = $this->mailer->getAttachments();
        $this->assertCount(1, $attachments);
        $this->assertEquals('Auftragsverarbeitungsvereinbarung.pdf', $attachments[0][2]);
        
        // Clean up
        unlink($testPdfPath);
    }
    
    /**
     * Test email encoding
     */
    public function testEmailEncoding() {
        $unicodeData = [
            'firma' => 'Müller & Söhne GmbH',
            'ansprechpartner' => 'Jürgen Müller',
            'email' => 'jürgen.müller@example.com'
        ];
        
        $emailContent = $this->createUserEmail($unicodeData);
        
        $this->assertStringContainsString('Müller & Söhne GmbH', $emailContent['body']);
        $this->assertStringContainsString('Jürgen Müller', $emailContent['body']);
        $this->assertEquals('UTF-8', mb_detect_encoding($emailContent['body']));
    }
    
    /**
     * Test email error handling
     */
    public function testEmailErrorHandling() {
        $invalidMailer = new PHPMailer(true);
        $invalidMailer->isSMTP();
        $invalidMailer->Host = 'invalid-host';
        $invalidMailer->Port = 9999;
        $invalidMailer->SMTPAuth = false;
        
        $this->expectException(Exception::class);
        
        $invalidMailer->setFrom('test@example.com');
        $invalidMailer->addAddress('recipient@example.com');
        $invalidMailer->Subject = 'Test';
        $invalidMailer->Body = 'Test';
        $invalidMailer->send();
    }
    
    /**
     * Test email logging
     */
    public function testEmailLogging() {
        $logData = [
            'agreement_id' => 1,
            'empfaenger' => 'test@example.com',
            'betreff' => 'Test Email',
            'status' => 'versendet',
            'fehler_nachricht' => null
        ];
        
        $logEntry = $this->createEmailLog($logData);
        
        $this->assertEquals(1, $logEntry['agreement_id']);
        $this->assertEquals('test@example.com', $logEntry['empfaenger']);
        $this->assertEquals('Test Email', $logEntry['betreff']);
        $this->assertEquals('versendet', $logEntry['status']);
        $this->assertNull($logEntry['fehler_nachricht']);
    }
    
    /**
     * Test email rate limiting
     */
    public function testEmailRateLimiting() {
        $rateLimiter = $this->createRateLimiter();
        
        // Test normal rate
        for ($i = 0; $i < 5; $i++) {
            $result = $rateLimiter->checkRateLimit('127.0.0.1');
            $this->assertTrue($result['allowed']);
        }
        
        // Test rate limit exceeded
        $result = $rateLimiter->checkRateLimit('127.0.0.1');
        $this->assertFalse($result['allowed']);
    }
    
    /**
     * Create user email content
     */
    private function createUserEmail($data) {
        $subject = 'Ihre DSGVO-Auftragsverarbeitungsvereinbarung';
        
        $body = "
            <h2>Ihre Auftragsverarbeitungsvereinbarung wurde erstellt</h2>
            <p>Sehr geehrte Damen und Herren,</p>
            <p>Ihre Auftragsverarbeitungsvereinbarung für <strong>{$data['firma']}</strong> wurde erfolgreich erstellt.</p>
            <p>Ansprechpartner: {$data['ansprechpartner']}</p>
            <p>E-Mail: {$data['email']}</p>
            <p>Die Vereinbarung finden Sie als PDF-Anhang zu dieser E-Mail.</p>
            <p>Mit freundlichen Grüßen<br>Conrat GmbH</p>
        ";
        
        return [
            'subject' => $subject,
            'body' => $body
        ];
    }
    
    /**
     * Create admin email content
     */
    private function createAdminEmail($data) {
        $subject = 'Neue Auftragsverarbeitungsvereinbarung erstellt';
        
        $body = "
            <h2>Neue Auftragsverarbeitungsvereinbarung</h2>
            <p>Eine neue Auftragsverarbeitungsvereinbarung wurde erstellt:</p>
            <ul>
                <li><strong>Firma:</strong> {$data['firma']}</li>
                <li><strong>Ansprechpartner:</strong> {$data['ansprechpartner']}</li>
                <li><strong>E-Mail:</strong> {$data['email']}</li>
                <li><strong>Adresse:</strong> {$data['anschrift']}, {$data['plz']} {$data['ort']}</li>
            </ul>
            <p>Die Vereinbarung finden Sie als PDF-Anhang zu dieser E-Mail.</p>
        ";
        
        return [
            'subject' => $subject,
            'body' => $body
        ];
    }
    
    /**
     * Sanitize email data
     */
    private function sanitizeEmailData($data) {
        $sanitized = [];
        foreach ($data as $key => $value) {
            $sanitized[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }
        return $sanitized;
    }
    
    /**
     * Render user email template
     */
    private function renderUserTemplate($data) {
        return "
            <h2>Ihre Auftragsverarbeitungsvereinbarung</h2>
            <p>Firma: {$data['company_name']}</p>
            <p>Ansprechpartner: {$data['contact_person']}</p>
            <p>E-Mail: {$data['email']}</p>
            <p>Datum: {$data['agreement_date']}</p>
        ";
    }
    
    /**
     * Render admin email template
     */
    private function renderAdminTemplate($data) {
        return "
            <h2>Neue Vereinbarung</h2>
            <p>Firma: {$data['company_name']}</p>
            <p>Kontakt: {$data['contact_person']}</p>
            <p>Admin: {$data['admin_email']}</p>
        ";
    }
    
    /**
     * Create email log entry
     */
    private function createEmailLog($data) {
        return [
            'agreement_id' => $data['agreement_id'],
            'empfaenger' => $data['empfaenger'],
            'betreff' => $data['betreff'],
            'status' => $data['status'],
            'fehler_nachricht' => $data['fehler_nachricht'],
            'versendet_am' => date('Y-m-d H:i:s')
        ];
    }
    
    /**
     * Create rate limiter for testing
     */
    private function createRateLimiter() {
        return new class {
            private $requests = [];
            
            public function checkRateLimit($identifier) {
                $now = time();
                $this->requests[$identifier] = $this->requests[$identifier] ?? [];
                
                // Remove old requests (older than 1 hour)
                $this->requests[$identifier] = array_filter(
                    $this->requests[$identifier],
                    function($timestamp) use ($now) {
                        return ($now - $timestamp) < 3600;
                    }
                );
                
                if (count($this->requests[$identifier]) >= 5) {
                    return ['allowed' => false, 'reason' => 'rate_limit_exceeded'];
                }
                
                $this->requests[$identifier][] = $now;
                return ['allowed' => true];
            }
        };
    }
}
