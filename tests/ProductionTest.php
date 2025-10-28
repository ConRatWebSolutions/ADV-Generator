<?php
/**
 * Production Environment Tests for DSGVO ADV Project
 * Tests for production server deployment and functionality
 */

use PHPUnit\Framework\TestCase;

class ProductionTest extends TestCase {
    
    private $baseUrl;
    private $productionConfig;
    
    protected function setUp(): void {
        $this->baseUrl = 'https://your-domain.com'; // Production URL
        $this->productionConfig = require_once __DIR__ . '/../config/production_config.php';
    }
    
    /**
     * Test production environment setup
     */
    public function testProductionEnvironmentSetup() {
        // Test production configuration
        $this->assertTrue(ProductionConfig::isProductionMode(), "Production mode not enabled");
        $this->assertFalse(ProductionConfig::isDebugEnabled(), "Debug mode should be disabled in production");
        $this->assertEquals('error', ProductionConfig::getLogLevel(), "Log level not set to error");
        $this->assertFalse(ProductionConfig::isEmailTestMode(), "Email test mode should be disabled in production");
        $this->assertFalse(ProductionConfig::isPDFTestMode(), "PDF test mode should be disabled in production");
    }
    
    /**
     * Test production server connectivity
     */
    public function testProductionServerConnectivity() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'User-Agent: ProductionTest/1.0'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $totalTime = curl_getinfo($ch, CURLINFO_TOTAL_TIME);
        curl_close($ch);
        
        $this->assertEquals(200, $httpCode, "Production server not accessible");
        $this->assertLessThan(3.0, $totalTime, "Production server response too slow: {$totalTime}s");
        $this->assertStringContainsString('Auftragsverarbeitungsvereinbarung', $response, "Production page content incorrect");
    }
    
    /**
     * Test production SSL certificate
     */
    public function testProductionSSLCertificate() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_CERTINFO, true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $certInfo = curl_getinfo($ch, CURLINFO_CERTINFO);
        curl_close($ch);
        
        $this->assertEquals(200, $httpCode, "SSL connection failed");
        $this->assertNotEmpty($certInfo, "SSL certificate information not available");
        
        // Check certificate expiry
        if (!empty($certInfo)) {
            $cert = $certInfo[0];
            $this->assertArrayHasKey('Expire date', $cert, "Certificate expiry date not found");
            
            $expiryDate = strtotime($cert['Expire date']);
            $daysUntilExpiry = ($expiryDate - time()) / (24 * 60 * 60);
            $this->assertGreaterThan(30, $daysUntilExpiry, "SSL certificate expires in less than 30 days");
        }
    }
    
    /**
     * Test production security headers
     */
    public function testProductionSecurityHeaders() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        $this->assertEquals(200, $httpCode, "Production server not accessible");
        
        // Check security headers
        $this->assertStringContainsString('Strict-Transport-Security', $response, "HSTS header missing");
        $this->assertStringContainsString('X-Content-Type-Options', $response, "X-Content-Type-Options header missing");
        $this->assertStringContainsString('X-Frame-Options', $response, "X-Frame-Options header missing");
        $this->assertStringContainsString('X-XSS-Protection', $response, "X-XSS-Protection header missing");
        $this->assertStringContainsString('Referrer-Policy', $response, "Referrer-Policy header missing");
    }
    
    /**
     * Test production form functionality
     */
    public function testProductionFormFunctionality() {
        // Get CSRF token
        $csrfToken = $this->getCSRFToken();
        
        // Submit form with production test data
        $response = $this->submitForm([
            'vorname' => 'Production',
            'name' => 'Test',
            'email' => 'production-test@example.com',
            'firma' => 'Production Test Company',
            'ansprechpartner' => 'Production Test User',
            'anschrift' => 'Production Test Street 123',
            'plz' => '12345',
            'ort' => 'Production Test City',
            'csrf_token' => $csrfToken
        ]);
        
        $this->assertTrue($response['success'], "Production form submission failed");
        $this->assertStringContainsString('success', $response['message'], "Production form response incorrect");
    }
    
    /**
     * Test production email functionality
     */
    public function testProductionEmailFunctionality() {
        // Test email sending in production
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl . '/test-email');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        $this->assertEquals(200, $httpCode, "Email test failed");
        
        $data = json_decode($response, true);
        $this->assertTrue($data['success'], "Production email functionality failed");
    }
    
    /**
     * Test production PDF generation
     */
    public function testProductionPDFGeneration() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl . '/test-pdf');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        $this->assertEquals(200, $httpCode, "PDF test failed");
        
        $data = json_decode($response, true);
        $this->assertTrue($data['success'], "Production PDF generation failed");
    }
    
    /**
     * Test production performance
     */
    public function testProductionPerformance() {
        $loadTimes = [];
        
        // Test multiple page loads
        for ($i = 0; $i < 5; $i++) {
            $startTime = microtime(true);
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->baseUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            $endTime = microtime(true);
            $loadTime = $endTime - $startTime;
            
            $this->assertEquals(200, $httpCode, "Page load failed on attempt " . ($i + 1));
            $this->assertLessThan(3.0, $loadTime, "Page load too slow on attempt " . ($i + 1) . ": {$loadTime}s");
            
            $loadTimes[] = $loadTime;
        }
        
        // Calculate average load time
        $averageLoadTime = array_sum($loadTimes) / count($loadTimes);
        $this->assertLessThan(2.0, $averageLoadTime, "Average page load time too slow: {$averageLoadTime}s");
    }
    
    /**
     * Test production error handling
     */
    public function testProductionErrorHandling() {
        // Test 404 error
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl . '/nonexistent-page');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        $this->assertEquals(404, $httpCode, "404 error handling failed");
    }
    
    /**
     * Test production rate limiting
     */
    public function testProductionRateLimiting() {
        $responses = [];
        
        // Test multiple rapid requests
        for ($i = 0; $i < 10; $i++) {
            $response = $this->submitForm([
                'vorname' => 'Rate',
                'name' => 'Test',
                'email' => 'rate-test@example.com',
                'firma' => 'Rate Test Company',
                'ansprechpartner' => 'Rate Test User',
                'anschrift' => 'Rate Test Street',
                'plz' => '12345',
                'ort' => 'Rate Test City',
                'csrf_token' => $this->getCSRFToken()
            ]);
            
            $responses[] = $response;
            
            // Small delay between requests
            usleep(100000); // 0.1 seconds
        }
        
        // Check if rate limiting is working
        $rateLimitedCount = 0;
        foreach ($responses as $response) {
            if (!$response['success'] && strpos($response['message'], 'rate') !== false) {
                $rateLimitedCount++;
            }
        }
        
        $this->assertGreaterThan(0, $rateLimitedCount, "Rate limiting not working");
    }
    
    /**
     * Test production logging
     */
    public function testProductionLogging() {
        // Test logging functionality
        $logger = new ProductionLogger();
        $logger->init();
        
        // Test error logging
        $logger->error('Production test error message', ['test' => 'error']);
        $logger->warning('Production test warning message', ['test' => 'warning']);
        $logger->info('Production test info message', ['test' => 'info']);
        
        // Check if logs were written
        $logs = $logger->getLogs(10);
        $this->assertGreaterThan(0, count($logs), "No logs written");
        
        // Check log content
        $logContent = implode("\n", $logs);
        $this->assertStringContainsString('Production test error message', $logContent, "Error log not found");
        $this->assertStringContainsString('Production test warning message', $logContent, "Warning log not found");
    }
    
    /**
     * Test production monitoring
     */
    public function testProductionMonitoring() {
        // Test application monitoring
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $totalTime = curl_getinfo($ch, CURLINFO_TOTAL_TIME);
        curl_close($ch);
        
        $this->assertEquals(200, $httpCode, "Application monitoring failed");
        $this->assertLessThan(3.0, $totalTime, "Application response time too slow: {$totalTime}s");
    }
    
    /**
     * Test production backup functionality
     */
    public function testProductionBackupFunctionality() {
        // Test if backup directories exist
        $this->assertDirectoryExists('storage/backups', "Backup directory not found");
        
        // Test backup creation
        $backupDir = 'storage/backups/production_test_' . date('Y-m-d_H-i-s');
        mkdir($backupDir, 0755, true);
        
        $this->assertDirectoryExists($backupDir, "Backup directory creation failed");
        
        // Cleanup
        rmdir($backupDir);
    }
    
    /**
     * Helper methods
     */
    
    private function getCSRFToken() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl . '/get_csrf_token.php');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
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
    
    private function submitForm($data) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl . '/process_form.php');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
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
}
