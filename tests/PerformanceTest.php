<?php
/**
 * Performance Tests for DSGVO ADV Project
 * Tests for performance optimization and monitoring
 */

use PHPUnit\Framework\TestCase;

class PerformanceTest extends TestCase {
    
    private $baseUrl;
    private $performanceThresholds = [
        'page_load_time' => 2.0, // seconds
        'form_submission_time' => 3.0, // seconds
        'pdf_generation_time' => 5.0, // seconds
        'email_sending_time' => 10.0, // seconds
        'database_query_time' => 0.1, // seconds
        'memory_usage' => 64 * 1024 * 1024, // 64MB
        'cpu_usage' => 80.0 // percentage
    ];
    
    protected function setUp(): void {
        $this->baseUrl = 'http://localhost:8080'; // DDEV URL
    }
    
    /**
     * Test page load performance
     */
    public function testPageLoadPerformance() {
        $loadTimes = [];
        
        // Test multiple page loads
        for ($i = 0; $i < 10; $i++) {
            $startTime = microtime(true);
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->baseUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language: de-DE,de;q=0.9,en;q=0.8',
                'Accept-Encoding: gzip, deflate'
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $totalTime = curl_getinfo($ch, CURLINFO_TOTAL_TIME);
            curl_close($ch);
            
            $endTime = microtime(true);
            $loadTime = $endTime - $startTime;
            
            $this->assertEquals(200, $httpCode, "Page load failed on attempt " . ($i + 1));
            $this->assertLessThan($this->performanceThresholds['page_load_time'], $loadTime, 
                "Page load too slow on attempt " . ($i + 1) . ": {$loadTime}s");
            
            $loadTimes[] = $loadTime;
        }
        
        // Calculate average load time
        $averageLoadTime = array_sum($loadTimes) / count($loadTimes);
        $this->assertLessThan($this->performanceThresholds['page_load_time'], $averageLoadTime, 
            "Average page load time too slow: {$averageLoadTime}s");
    }
    
    /**
     * Test form submission performance
     */
    public function testFormSubmissionPerformance() {
        $submissionTimes = [];
        
        // Test multiple form submissions
        for ($i = 0; $i < 5; $i++) {
            $startTime = microtime(true);
            
            // Get CSRF token
            $csrfToken = $this->getCSRFToken();
            
            // Submit form
            $response = $this->submitForm([
                'vorname' => 'Test' . $i,
                'name' => 'User' . $i,
                'email' => 'test' . $i . '@example.com',
                'firma' => 'Test Company ' . $i,
                'ansprechpartner' => 'Test User ' . $i,
                'anschrift' => 'Test Street ' . $i,
                'plz' => '12345',
                'ort' => 'Test City',
                'csrf_token' => $csrfToken
            ]);
            
            $endTime = microtime(true);
            $submissionTime = $endTime - $startTime;
            
            $this->assertLessThan($this->performanceThresholds['form_submission_time'], $submissionTime, 
                "Form submission too slow on attempt " . ($i + 1) . ": {$submissionTime}s");
            
            $submissionTimes[] = $submissionTime;
        }
        
        // Calculate average submission time
        $averageSubmissionTime = array_sum($submissionTimes) / count($submissionTimes);
        $this->assertLessThan($this->performanceThresholds['form_submission_time'], $averageSubmissionTime, 
            "Average form submission time too slow: {$averageSubmissionTime}s");
    }
    
    /**
     * Test PDF generation performance
     */
    public function testPDFGenerationPerformance() {
        $generationTimes = [];
        
        // Test multiple PDF generations
        for ($i = 0; $i < 3; $i++) {
            $startTime = microtime(true);
            
            // Generate PDF
            $pdfPath = $this->generatePDF([
                'vorname' => 'Test' . $i,
                'name' => 'User' . $i,
                'email' => 'test' . $i . '@example.com',
                'firma' => 'Test Company ' . $i,
                'ansprechpartner' => 'Test User ' . $i,
                'anschrift' => 'Test Street ' . $i,
                'plz' => '12345',
                'ort' => 'Test City'
            ]);
            
            $endTime = microtime(true);
            $generationTime = $endTime - $startTime;
            
            $this->assertLessThan($this->performanceThresholds['pdf_generation_time'], $generationTime, 
                "PDF generation too slow on attempt " . ($i + 1) . ": {$generationTime}s");
            
            $generationTimes[] = $generationTime;
            
            // Clean up
            if (file_exists($pdfPath)) {
                unlink($pdfPath);
            }
        }
        
        // Calculate average generation time
        $averageGenerationTime = array_sum($generationTimes) / count($generationTimes);
        $this->assertLessThan($this->performanceThresholds['pdf_generation_time'], $averageGenerationTime, 
            "Average PDF generation time too slow: {$averageGenerationTime}s");
    }
    
    /**
     * Test email sending performance
     */
    public function testEmailSendingPerformance() {
        $sendingTimes = [];
        
        // Test multiple email sends
        for ($i = 0; $i < 3; $i++) {
            $startTime = microtime(true);
            
            // Send test email
            $result = $this->sendTestEmail([
                'vorname' => 'Test' . $i,
                'name' => 'User' . $i,
                'email' => 'test' . $i . '@example.com',
                'firma' => 'Test Company ' . $i,
                'ansprechpartner' => 'Test User ' . $i,
                'anschrift' => 'Test Street ' . $i,
                'plz' => '12345',
                'ort' => 'Test City'
            ]);
            
            $endTime = microtime(true);
            $sendingTime = $endTime - $startTime;
            
            $this->assertLessThan($this->performanceThresholds['email_sending_time'], $sendingTime, 
                "Email sending too slow on attempt " . ($i + 1) . ": {$sendingTime}s");
            
            $sendingTimes[] = $sendingTime;
        }
        
        // Calculate average sending time
        $averageSendingTime = array_sum($sendingTimes) / count($sendingTimes);
        $this->assertLessThan($this->performanceThresholds['email_sending_time'], $averageSendingTime, 
            "Average email sending time too slow: {$averageSendingTime}s");
    }
    
    /**
     * Test database query performance
     */
    public function testDatabaseQueryPerformance() {
        $queryTimes = [];
        
        // Test multiple database queries
        for ($i = 0; $i < 10; $i++) {
            $startTime = microtime(true);
            
            // Test database connection
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->baseUrl . '/test-database');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            $endTime = microtime(true);
            $queryTime = $endTime - $startTime;
            
            $this->assertEquals(200, $httpCode, "Database query failed on attempt " . ($i + 1));
            $this->assertLessThan($this->performanceThresholds['database_query_time'], $queryTime, 
                "Database query too slow on attempt " . ($i + 1) . ": {$queryTime}s");
            
            $queryTimes[] = $queryTime;
        }
        
        // Calculate average query time
        $averageQueryTime = array_sum($queryTimes) / count($queryTimes);
        $this->assertLessThan($this->performanceThresholds['database_query_time'], $averageQueryTime, 
            "Average database query time too slow: {$averageQueryTime}s");
    }
    
    /**
     * Test memory usage
     */
    public function testMemoryUsage() {
        $memoryUsages = [];
        
        // Test memory usage during operations
        for ($i = 0; $i < 5; $i++) {
            $startMemory = memory_get_usage();
            
            // Perform memory-intensive operations
            $this->performMemoryIntensiveOperations();
            
            $endMemory = memory_get_usage();
            $memoryUsage = $endMemory - $startMemory;
            
            $this->assertLessThan($this->performanceThresholds['memory_usage'], $memoryUsage, 
                "Memory usage too high on attempt " . ($i + 1) . ": " . $this->formatBytes($memoryUsage));
            
            $memoryUsages[] = $memoryUsage;
        }
        
        // Calculate average memory usage
        $averageMemoryUsage = array_sum($memoryUsages) / count($memoryUsages);
        $this->assertLessThan($this->performanceThresholds['memory_usage'], $averageMemoryUsage, 
            "Average memory usage too high: " . $this->formatBytes($averageMemoryUsage));
    }
    
    /**
     * Test concurrent requests
     */
    public function testConcurrentRequests() {
        $concurrentRequests = 10;
        $startTime = microtime(true);
        
        // Create multiple concurrent requests
        $multiHandle = curl_multi_init();
        $curlHandles = [];
        
        for ($i = 0; $i < $concurrentRequests; $i++) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->baseUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            
            $curlHandles[] = $ch;
            curl_multi_add_handle($multiHandle, $ch);
        }
        
        // Execute all requests
        $running = null;
        do {
            curl_multi_exec($multiHandle, $running);
            curl_multi_select($multiHandle);
        } while ($running > 0);
        
        // Get results
        $successCount = 0;
        foreach ($curlHandles as $ch) {
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($httpCode === 200) {
                $successCount++;
            }
            curl_multi_remove_handle($multiHandle, $ch);
            curl_close($ch);
        }
        
        curl_multi_close($multiHandle);
        
        $endTime = microtime(true);
        $totalTime = $endTime - $startTime;
        
        $this->assertEquals($concurrentRequests, $successCount, "Not all concurrent requests succeeded");
        $this->assertLessThan(10.0, $totalTime, "Concurrent requests took too long: {$totalTime}s");
    }
    
    /**
     * Test resource optimization
     */
    public function testResourceOptimization() {
        // Test CSS minification
        $this->testCSSMinification();
        
        // Test JavaScript minification
        $this->testJavaScriptMinification();
        
        // Test image optimization
        $this->testImageOptimization();
        
        // Test compression
        $this->testCompression();
    }
    
    /**
     * Test CSS minification
     */
    private function testCSSMinification() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl . '/assets/css/style.css');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: text/css,*/*;q=0.1'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $contentLength = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
        curl_close($ch);
        
        $this->assertEquals(200, $httpCode, "CSS file not accessible");
        $this->assertLessThan(100 * 1024, $contentLength, "CSS file too large: " . $this->formatBytes($contentLength));
    }
    
    /**
     * Test JavaScript minification
     */
    private function testJavaScriptMinification() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl . '/assets/js/form.js');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/javascript,*/*;q=0.8'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $contentLength = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
        curl_close($ch);
        
        $this->assertEquals(200, $httpCode, "JavaScript file not accessible");
        $this->assertLessThan(50 * 1024, $contentLength, "JavaScript file too large: " . $this->formatBytes($contentLength));
    }
    
    /**
     * Test image optimization
     */
    private function testImageOptimization() {
        // Check if images exist and are optimized
        $imageFiles = [
            '/assets/images/logo.png',
            '/assets/images/icon.png'
        ];
        
        foreach ($imageFiles as $imageFile) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->baseUrl . $imageFile);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: image/png,image/jpeg,image/gif,*/*;q=0.8'
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $contentLength = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
            curl_close($ch);
            
            if ($httpCode === 200) {
                $this->assertLessThan(100 * 1024, $contentLength, "Image $imageFile too large: " . $this->formatBytes($contentLength));
            }
        }
    }
    
    /**
     * Test compression
     */
    private function testCompression() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept-Encoding: gzip, deflate'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $contentLength = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
        curl_close($ch);
        
        $this->assertEquals(200, $httpCode, "Page not accessible");
        $this->assertLessThan(50 * 1024, $contentLength, "Compressed page too large: " . $this->formatBytes($contentLength));
    }
    
    /**
     * Test caching performance
     */
    public function testCachingPerformance() {
        // Test first request (cache miss)
        $startTime = microtime(true);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        $endTime = microtime(true);
        $firstLoadTime = $endTime - $startTime;
        
        $this->assertEquals(200, $httpCode, "First request failed");
        
        // Test second request (cache hit)
        $startTime = microtime(true);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        $endTime = microtime(true);
        $secondLoadTime = $endTime - $startTime;
        
        $this->assertEquals(200, $httpCode, "Second request failed");
        $this->assertLessThan($firstLoadTime, $secondLoadTime, "Caching not working effectively");
    }
    
    /**
     * Test database performance under load
     */
    public function testDatabasePerformanceUnderLoad() {
        $concurrentQueries = 20;
        $startTime = microtime(true);
        
        // Create multiple concurrent database queries
        $multiHandle = curl_multi_init();
        $curlHandles = [];
        
        for ($i = 0; $i < $concurrentQueries; $i++) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->baseUrl . '/test-database');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            
            $curlHandles[] = $ch;
            curl_multi_add_handle($multiHandle, $ch);
        }
        
        // Execute all queries
        $running = null;
        do {
            curl_multi_exec($multiHandle, $running);
            curl_multi_select($multiHandle);
        } while ($running > 0);
        
        // Get results
        $successCount = 0;
        foreach ($curlHandles as $ch) {
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($httpCode === 200) {
                $successCount++;
            }
            curl_multi_remove_handle($multiHandle, $ch);
            curl_close($ch);
        }
        
        curl_multi_close($multiHandle);
        
        $endTime = microtime(true);
        $totalTime = $endTime - $startTime;
        
        $this->assertEquals($concurrentQueries, $successCount, "Not all database queries succeeded");
        $this->assertLessThan(5.0, $totalTime, "Database queries under load took too long: {$totalTime}s");
    }
    
    /**
     * Helper methods
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
    
    private function submitForm($data) {
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
    
    private function generatePDF($data) {
        // This would call the PDF generation endpoint
        // For now, return a mock path
        return '/tmp/test_' . uniqid() . '.pdf';
    }
    
    private function sendTestEmail($data) {
        // This would call the email sending endpoint
        // For now, return a mock result
        return ['success' => true, 'message' => 'Email sent'];
    }
    
    private function performMemoryIntensiveOperations() {
        // Simulate memory-intensive operations
        $data = [];
        for ($i = 0; $i < 1000; $i++) {
            $data[] = str_repeat('x', 1000);
        }
        return $data;
    }
    
    private function formatBytes($bytes) {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1;
        $bytes /= pow(1024, $pow);
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
