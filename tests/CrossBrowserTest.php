<?php
/**
 * Cross-Browser Tests for DSGVO ADV Project
 * Tests for browser compatibility (Chrome, Firefox, Safari, Edge)
 */

use PHPUnit\Framework\TestCase;

class CrossBrowserTest extends TestCase {
    
    private $baseUrl;
    private $browsers = [
        'chrome' => 'Chrome/120.0.0.0',
        'firefox' => 'Firefox/121.0',
        'safari' => 'Safari/537.36',
        'edge' => 'Edg/120.0.0.0'
    ];
    
    protected function setUp(): void {
        $this->baseUrl = 'http://localhost:8080'; // DDEV URL
    }
    
    /**
     * Test form functionality across browsers
     */
    public function testFormFunctionalityAcrossBrowsers() {
        foreach ($this->browsers as $browserName => $userAgent) {
            $this->testFormInBrowser($browserName, $userAgent);
        }
    }
    
    /**
     * Test CSS compatibility across browsers
     */
    public function testCSSCompatibilityAcrossBrowsers() {
        foreach ($this->browsers as $browserName => $userAgent) {
            $this->testCSSInBrowser($browserName, $userAgent);
        }
    }
    
    /**
     * Test JavaScript functionality across browsers
     */
    public function testJavaScriptFunctionalityAcrossBrowsers() {
        foreach ($this->browsers as $browserName => $userAgent) {
            $this->testJavaScriptInBrowser($browserName, $userAgent);
        }
    }
    
    /**
     * Test responsive design across browsers
     */
    public function testResponsiveDesignAcrossBrowsers() {
        $viewportSizes = [
            'mobile' => ['width' => 375, 'height' => 667],
            'tablet' => ['width' => 768, 'height' => 1024],
            'desktop' => ['width' => 1920, 'height' => 1080]
        ];
        
        foreach ($this->browsers as $browserName => $userAgent) {
            foreach ($viewportSizes as $deviceName => $viewport) {
                $this->testResponsiveInBrowser($browserName, $userAgent, $deviceName, $viewport);
            }
        }
    }
    
    /**
     * Test form validation across browsers
     */
    public function testFormValidationAcrossBrowsers() {
        $testCases = [
            'empty_fields' => [
                'vorname' => '',
                'name' => '',
                'email' => '',
                'firma' => '',
                'ansprechpartner' => '',
                'anschrift' => '',
                'plz' => '',
                'ort' => ''
            ],
            'invalid_email' => [
                'vorname' => 'Max',
                'name' => 'Mustermann',
                'email' => 'invalid-email',
                'firma' => 'Test GmbH',
                'ansprechpartner' => 'Max Mustermann',
                'anschrift' => 'Musterstraße 123',
                'plz' => '12345',
                'ort' => 'Musterstadt'
            ],
            'invalid_plz' => [
                'vorname' => 'Max',
                'name' => 'Mustermann',
                'email' => 'test@example.com',
                'firma' => 'Test GmbH',
                'ansprechpartner' => 'Max Mustermann',
                'anschrift' => 'Musterstraße 123',
                'plz' => '123',
                'ort' => 'Musterstadt'
            ]
        ];
        
        foreach ($this->browsers as $browserName => $userAgent) {
            foreach ($testCases as $testName => $testData) {
                $this->testValidationInBrowser($browserName, $userAgent, $testName, $testData);
            }
        }
    }
    
    /**
     * Test accessibility across browsers
     */
    public function testAccessibilityAcrossBrowsers() {
        foreach ($this->browsers as $browserName => $userAgent) {
            $this->testAccessibilityInBrowser($browserName, $userAgent);
        }
    }
    
    /**
     * Test form in specific browser
     */
    private function testFormInBrowser($browserName, $userAgent) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language: de-DE,de;q=0.9,en;q=0.8',
            'Accept-Encoding: gzip, deflate'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        $this->assertEquals(200, $httpCode, "Failed to load page in $browserName");
        $this->assertStringContainsString('Auftragsverarbeitungsvereinbarung', $response, "Page title missing in $browserName");
        $this->assertStringContainsString('form', $response, "Form element missing in $browserName");
        $this->assertStringContainsString('vorname', $response, "Form field missing in $browserName");
    }
    
    /**
     * Test CSS in specific browser
     */
    private function testCSSInBrowser($browserName, $userAgent) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl . '/assets/css/style.css');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: text/css,*/*;q=0.1'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        $this->assertEquals(200, $httpCode, "Failed to load CSS in $browserName");
        $this->assertStringContainsString('.form-field', $response, "CSS classes missing in $browserName");
        $this->assertStringContainsString('@media', $response, "Media queries missing in $browserName");
    }
    
    /**
     * Test JavaScript in specific browser
     */
    private function testJavaScriptInBrowser($browserName, $userAgent) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl . '/assets/js/form.js');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/javascript,*/*;q=0.8'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        $this->assertEquals(200, $httpCode, "Failed to load JavaScript in $browserName");
        $this->assertStringContainsString('class FormHandler', $response, "JavaScript classes missing in $browserName");
        $this->assertStringContainsString('addEventListener', $response, "Event listeners missing in $browserName");
    }
    
    /**
     * Test responsive design in specific browser
     */
    private function testResponsiveInBrowser($browserName, $userAgent, $deviceName, $viewport) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language: de-DE,de;q=0.9,en;q=0.8'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        $this->assertEquals(200, $httpCode, "Failed to load page in $browserName on $deviceName");
        
        // Check for responsive meta tag
        $this->assertStringContainsString('viewport', $response, "Viewport meta tag missing in $browserName");
        
        // Check for responsive CSS
        $this->assertStringContainsString('@media', $response, "Responsive CSS missing in $browserName");
    }
    
    /**
     * Test validation in specific browser
     */
    private function testValidationInBrowser($browserName, $userAgent, $testName, $testData) {
        // Get CSRF token
        $csrfToken = $this->getCSRFToken($userAgent);
        
        // Submit form
        $response = $this->submitForm($testData, $csrfToken, $userAgent);
        
        // Verify validation works
        $this->assertFalse($response['success'], "Validation should fail for $testName in $browserName");
        $this->assertNotEmpty($response['message'], "Error message missing for $testName in $browserName");
    }
    
    /**
     * Test accessibility in specific browser
     */
    private function testAccessibilityInBrowser($browserName, $userAgent) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        $this->assertEquals(200, $httpCode, "Failed to load page in $browserName");
        
        // Check for accessibility features
        $this->assertStringContainsString('lang="de"', $response, "Language attribute missing in $browserName");
        $this->assertStringContainsString('for=', $response, "Label associations missing in $browserName");
        $this->assertStringContainsString('required', $response, "Required attributes missing in $browserName");
        $this->assertStringContainsString('aria-', $response, "ARIA attributes missing in $browserName");
    }
    
    /**
     * Test browser-specific features
     */
    public function testBrowserSpecificFeatures() {
        // Test Chrome-specific features
        $this->testChromeFeatures();
        
        // Test Firefox-specific features
        $this->testFirefoxFeatures();
        
        // Test Safari-specific features
        $this->testSafariFeatures();
        
        // Test Edge-specific features
        $this->testEdgeFeatures();
    }
    
    /**
     * Test Chrome-specific features
     */
    private function testChromeFeatures() {
        $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        // Chrome should support modern CSS features
        $this->assertStringContainsString('flexbox', $response, "Flexbox support missing for Chrome");
        $this->assertStringContainsString('grid', $response, "CSS Grid support missing for Chrome");
    }
    
    /**
     * Test Firefox-specific features
     */
    private function testFirefoxFeatures() {
        $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:121.0) Gecko/20100101 Firefox/121.0';
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        // Firefox should support modern CSS features
        $this->assertStringContainsString('flexbox', $response, "Flexbox support missing for Firefox");
        $this->assertStringContainsString('grid', $response, "CSS Grid support missing for Firefox");
    }
    
    /**
     * Test Safari-specific features
     */
    private function testSafariFeatures() {
        $userAgent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Version/17.1 Safari/537.36';
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        // Safari should support modern CSS features
        $this->assertStringContainsString('flexbox', $response, "Flexbox support missing for Safari");
        $this->assertStringContainsString('grid', $response, "CSS Grid support missing for Safari");
    }
    
    /**
     * Test Edge-specific features
     */
    private function testEdgeFeatures() {
        $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36 Edg/120.0.0.0';
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        // Edge should support modern CSS features
        $this->assertStringContainsString('flexbox', $response, "Flexbox support missing for Edge");
        $this->assertStringContainsString('grid', $response, "CSS Grid support missing for Edge");
    }
    
    /**
     * Get CSRF token with specific user agent
     */
    private function getCSRFToken($userAgent) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl . '/get_csrf_token.php');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
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
     * Submit form with specific user agent
     */
    private function submitForm($data, $csrfToken, $userAgent) {
        $data['csrf_token'] = $csrfToken;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl . '/process_form.php');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
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
