<?php
/**
 * Mobile Device Tests for DSGVO ADV Project
 * Tests for mobile device compatibility and responsive design
 */

use PHPUnit\Framework\TestCase;

class MobileDeviceTest extends TestCase {
    
    private $baseUrl;
    private $mobileDevices = [
        'iphone_se' => [
            'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.1 Mobile/15E148 Safari/604.1',
            'viewport' => ['width' => 375, 'height' => 667],
            'device_pixel_ratio' => 2
        ],
        'iphone_12' => [
            'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.1 Mobile/15E148 Safari/604.1',
            'viewport' => ['width' => 390, 'height' => 844],
            'device_pixel_ratio' => 3
        ],
        'iphone_14_plus' => [
            'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.1 Mobile/15E148 Safari/604.1',
            'viewport' => ['width' => 428, 'height' => 926],
            'device_pixel_ratio' => 3
        ],
        'samsung_galaxy_s21' => [
            'user_agent' => 'Mozilla/5.0 (Linux; Android 13; SM-G991B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Mobile Safari/537.36',
            'viewport' => ['width' => 384, 'height' => 854],
            'device_pixel_ratio' => 2.75
        ],
        'samsung_galaxy_tab' => [
            'user_agent' => 'Mozilla/5.0 (Linux; Android 13; SM-T970) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'viewport' => ['width' => 800, 'height' => 1280],
            'device_pixel_ratio' => 1.5
        ],
        'ipad' => [
            'user_agent' => 'Mozilla/5.0 (iPad; CPU OS 17_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.1 Mobile/15E148 Safari/604.1',
            'viewport' => ['width' => 768, 'height' => 1024],
            'device_pixel_ratio' => 2
        ],
        'ipad_pro' => [
            'user_agent' => 'Mozilla/5.0 (iPad; CPU OS 17_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.1 Mobile/15E148 Safari/604.1',
            'viewport' => ['width' => 1024, 'height' => 1366],
            'device_pixel_ratio' => 2
        ]
    ];
    
    protected function setUp(): void {
        $this->baseUrl = 'http://localhost:8080'; // DDEV URL
    }
    
    /**
     * Test mobile device compatibility
     */
    public function testMobileDeviceCompatibility() {
        foreach ($this->mobileDevices as $deviceName => $deviceConfig) {
            $this->testDeviceCompatibility($deviceName, $deviceConfig);
        }
    }
    
    /**
     * Test responsive design on mobile devices
     */
    public function testResponsiveDesignOnMobileDevices() {
        foreach ($this->mobileDevices as $deviceName => $deviceConfig) {
            $this->testResponsiveDesign($deviceName, $deviceConfig);
        }
    }
    
    /**
     * Test touch interactions on mobile devices
     */
    public function testTouchInteractionsOnMobileDevices() {
        foreach ($this->mobileDevices as $deviceName => $deviceConfig) {
            $this->testTouchInteractions($deviceName, $deviceConfig);
        }
    }
    
    /**
     * Test mobile form functionality
     */
    public function testMobileFormFunctionality() {
        foreach ($this->mobileDevices as $deviceName => $deviceConfig) {
            $this->testFormOnDevice($deviceName, $deviceConfig);
        }
    }
    
    /**
     * Test mobile performance
     */
    public function testMobilePerformance() {
        foreach ($this->mobileDevices as $deviceName => $deviceConfig) {
            $this->testPerformanceOnDevice($deviceName, $deviceConfig);
        }
    }
    
    /**
     * Test mobile accessibility
     */
    public function testMobileAccessibility() {
        foreach ($this->mobileDevices as $deviceName => $deviceConfig) {
            $this->testAccessibilityOnDevice($deviceName, $deviceConfig);
        }
    }
    
    /**
     * Test mobile viewport handling
     */
    public function testMobileViewportHandling() {
        foreach ($this->mobileDevices as $deviceName => $deviceConfig) {
            $this->testViewportOnDevice($deviceName, $deviceConfig);
        }
    }
    
    /**
     * Test mobile keyboard handling
     */
    public function testMobileKeyboardHandling() {
        $keyboardTests = [
            'email_input' => [
                'field' => 'email',
                'input_type' => 'email',
                'expected_keyboard' => 'email'
            ],
            'numeric_input' => [
                'field' => 'plz',
                'input_type' => 'text',
                'pattern' => '[0-9]{5}',
                'expected_keyboard' => 'numeric'
            ],
            'text_input' => [
                'field' => 'vorname',
                'input_type' => 'text',
                'expected_keyboard' => 'text'
            ]
        ];
        
        foreach ($this->mobileDevices as $deviceName => $deviceConfig) {
            foreach ($keyboardTests as $testName => $testConfig) {
                $this->testKeyboardOnDevice($deviceName, $deviceConfig, $testName, $testConfig);
            }
        }
    }
    
    /**
     * Test device-specific features
     */
    public function testDeviceSpecificFeatures() {
        // Test iOS-specific features
        $this->testIOSFeatures();
        
        // Test Android-specific features
        $this->testAndroidFeatures();
        
        // Test tablet-specific features
        $this->testTabletFeatures();
    }
    
    /**
     * Test device compatibility
     */
    private function testDeviceCompatibility($deviceName, $deviceConfig) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $deviceConfig['user_agent']);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language: de-DE,de;q=0.9,en;q=0.8',
            'Accept-Encoding: gzip, deflate'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        $this->assertEquals(200, $httpCode, "Failed to load page on $deviceName");
        $this->assertStringContainsString('Auftragsverarbeitungsvereinbarung', $response, "Page title missing on $deviceName");
        $this->assertStringContainsString('form', $response, "Form element missing on $deviceName");
    }
    
    /**
     * Test responsive design
     */
    private function testResponsiveDesign($deviceName, $deviceConfig) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $deviceConfig['user_agent']);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        // Check for viewport meta tag
        $this->assertStringContainsString('viewport', $response, "Viewport meta tag missing on $deviceName");
        $this->assertStringContainsString('width=device-width', $response, "Viewport width missing on $deviceName");
        $this->assertStringContainsString('initial-scale=1', $response, "Initial scale missing on $deviceName");
        
        // Check for responsive CSS
        $this->assertStringContainsString('@media', $response, "Media queries missing on $deviceName");
        
        // Check for mobile-specific CSS
        $this->assertStringContainsString('mobile', $response, "Mobile CSS missing on $deviceName");
    }
    
    /**
     * Test touch interactions
     */
    private function testTouchInteractions($deviceName, $deviceConfig) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $deviceConfig['user_agent']);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        // Check for touch-friendly elements
        $this->assertStringContainsString('button', $response, "Button elements missing on $deviceName");
        $this->assertStringContainsString('input', $response, "Input elements missing on $deviceName");
        
        // Check for touch-friendly CSS
        $this->assertStringContainsString('touch-action', $response, "Touch action CSS missing on $deviceName");
        $this->assertStringContainsString('user-select', $response, "User select CSS missing on $deviceName");
    }
    
    /**
     * Test form on device
     */
    private function testFormOnDevice($deviceName, $deviceConfig) {
        // Test form loading
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $deviceConfig['user_agent']);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        // Check for form fields
        $formFields = ['vorname', 'name', 'email', 'firma', 'ansprechpartner', 'anschrift', 'plz', 'ort'];
        foreach ($formFields as $field) {
            $this->assertStringContainsString("name=\"$field\"", $response, "Form field $field missing on $deviceName");
        }
        
        // Check for mobile-friendly attributes
        $this->assertStringContainsString('autocomplete', $response, "Autocomplete attributes missing on $deviceName");
        $this->assertStringContainsString('required', $response, "Required attributes missing on $deviceName");
    }
    
    /**
     * Test performance on device
     */
    private function testPerformanceOnDevice($deviceName, $deviceConfig) {
        $startTime = microtime(true);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $deviceConfig['user_agent']);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $totalTime = curl_getinfo($ch, CURLINFO_TOTAL_TIME);
        curl_close($ch);
        
        $endTime = microtime(true);
        $responseTime = $endTime - $startTime;
        
        $this->assertEquals(200, $httpCode, "Failed to load page on $deviceName");
        $this->assertLessThan(2.0, $responseTime, "Page load too slow on $deviceName: {$responseTime}s");
        $this->assertLessThan(1.0, $totalTime, "Network time too slow on $deviceName: {$totalTime}s");
    }
    
    /**
     * Test accessibility on device
     */
    private function testAccessibilityOnDevice($deviceName, $deviceConfig) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $deviceConfig['user_agent']);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        // Check for accessibility features
        $this->assertStringContainsString('lang="de"', $response, "Language attribute missing on $deviceName");
        $this->assertStringContainsString('for=', $response, "Label associations missing on $deviceName");
        $this->assertStringContainsString('aria-', $response, "ARIA attributes missing on $deviceName");
        $this->assertStringContainsString('role=', $response, "Role attributes missing on $deviceName");
    }
    
    /**
     * Test viewport on device
     */
    private function testViewportOnDevice($deviceName, $deviceConfig) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $deviceConfig['user_agent']);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        // Check for viewport meta tag
        $this->assertStringContainsString('viewport', $response, "Viewport meta tag missing on $deviceName");
        
        // Check for device-specific viewport handling
        $viewport = $deviceConfig['viewport'];
        $width = $viewport['width'];
        $height = $viewport['height'];
        
        // Verify responsive design works for this viewport
        $this->assertStringContainsString('@media', $response, "Media queries missing on $deviceName");
    }
    
    /**
     * Test keyboard on device
     */
    private function testKeyboardOnDevice($deviceName, $deviceConfig, $testName, $testConfig) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $deviceConfig['user_agent']);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        $field = $testConfig['field'];
        $inputType = $testConfig['input_type'];
        
        // Check for input type
        $this->assertStringContainsString("type=\"$inputType\"", $response, "Input type missing for $field on $deviceName");
        
        // Check for pattern if specified
        if (isset($testConfig['pattern'])) {
            $this->assertStringContainsString("pattern=\"{$testConfig['pattern']}\"", $response, "Pattern missing for $field on $deviceName");
        }
    }
    
    /**
     * Test iOS-specific features
     */
    private function testIOSFeatures() {
        $iosDevices = array_filter($this->mobileDevices, function($device) {
            return strpos($device['user_agent'], 'iPhone') !== false || strpos($device['user_agent'], 'iPad') !== false;
        });
        
        foreach ($iosDevices as $deviceName => $deviceConfig) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->baseUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, $deviceConfig['user_agent']);
            
            $response = curl_exec($ch);
            curl_close($ch);
            
            // Check for iOS-specific features
            $this->assertStringContainsString('apple-touch-icon', $response, "Apple touch icon missing on $deviceName");
            $this->assertStringContainsString('apple-mobile-web-app', $response, "Apple mobile web app meta missing on $deviceName");
        }
    }
    
    /**
     * Test Android-specific features
     */
    private function testAndroidFeatures() {
        $androidDevices = array_filter($this->mobileDevices, function($device) {
            return strpos($device['user_agent'], 'Android') !== false;
        });
        
        foreach ($androidDevices as $deviceName => $deviceConfig) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->baseUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, $deviceConfig['user_agent']);
            
            $response = curl_exec($ch);
            curl_close($ch);
            
            // Check for Android-specific features
            $this->assertStringContainsString('theme-color', $response, "Theme color missing on $deviceName");
            $this->assertStringContainsString('mobile-web-app-capable', $response, "Mobile web app capable missing on $deviceName");
        }
    }
    
    /**
     * Test tablet-specific features
     */
    private function testTabletFeatures() {
        $tabletDevices = array_filter($this->mobileDevices, function($device) {
            return strpos($device['user_agent'], 'iPad') !== false || 
                   strpos($device['user_agent'], 'Tablet') !== false ||
                   $device['viewport']['width'] >= 768;
        });
        
        foreach ($tabletDevices as $deviceName => $deviceConfig) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->baseUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, $deviceConfig['user_agent']);
            
            $response = curl_exec($ch);
            curl_close($ch);
            
            // Check for tablet-specific features
            $this->assertStringContainsString('tablet', $response, "Tablet CSS missing on $deviceName");
            $this->assertStringContainsString('@media (min-width: 768px)', $response, "Tablet media query missing on $deviceName");
        }
    }
}
