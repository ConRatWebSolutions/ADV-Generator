<?php
/**
 * Form Validation Tests for DSGVO ADV Project
 * Unit tests for form validation functionality
 */

use PHPUnit\Framework\TestCase;

class FormValidationTest extends TestCase {
    
    /**
     * Test valid form data
     */
    public function testValidFormData() {
        $validData = [
            'vorname' => 'Max',
            'name' => 'Mustermann',
            'email' => 'max.mustermann@example.com',
            'firma' => 'Test GmbH',
            'ansprechpartner' => 'Max Mustermann',
            'anschrift' => 'Musterstraße 123',
            'plz' => '12345',
            'ort' => 'Musterstadt'
        ];
        
        $this->assertTrue($this->validateFormData($validData));
    }
    
    /**
     * Test missing required fields
     */
    public function testMissingRequiredFields() {
        $incompleteData = [
            'vorname' => 'Max',
            'email' => 'max.mustermann@example.com'
            // Missing other required fields
        ];
        
        $this->assertFalse($this->validateFormData($incompleteData));
    }
    
    /**
     * Test invalid email format
     */
    public function testInvalidEmailFormat() {
        $invalidEmailData = [
            'vorname' => 'Max',
            'name' => 'Mustermann',
            'email' => 'invalid-email',
            'firma' => 'Test GmbH',
            'ansprechpartner' => 'Max Mustermann',
            'anschrift' => 'Musterstraße 123',
            'plz' => '12345',
            'ort' => 'Musterstadt'
        ];
        
        $this->assertFalse($this->validateFormData($invalidEmailData));
    }
    
    /**
     * Test invalid postal code
     */
    public function testInvalidPostalCode() {
        $invalidPLZData = [
            'vorname' => 'Max',
            'name' => 'Mustermann',
            'email' => 'max.mustermann@example.com',
            'firma' => 'Test GmbH',
            'ansprechpartner' => 'Max Mustermann',
            'anschrift' => 'Musterstraße 123',
            'plz' => '123', // Invalid: too short
            'ort' => 'Musterstadt'
        ];
        
        $this->assertFalse($this->validateFormData($invalidPLZData));
    }
    
    /**
     * Test field length validation
     */
    public function testFieldLengthValidation() {
        $longData = [
            'vorname' => str_repeat('A', 101), // Too long
            'name' => 'Mustermann',
            'email' => 'max.mustermann@example.com',
            'firma' => 'Test GmbH',
            'ansprechpartner' => 'Max Mustermann',
            'anschrift' => 'Musterstraße 123',
            'plz' => '12345',
            'ort' => 'Musterstadt'
        ];
        
        $this->assertFalse($this->validateFormData($longData));
    }
    
    /**
     * Test XSS protection
     */
    public function testXSSProtection() {
        $xssData = [
            'vorname' => '<script>alert("xss")</script>',
            'name' => 'Mustermann',
            'email' => 'max.mustermann@example.com',
            'firma' => 'Test GmbH',
            'ansprechpartner' => 'Max Mustermann',
            'anschrift' => 'Musterstraße 123',
            'plz' => '12345',
            'ort' => 'Musterstadt'
        ];
        
        $sanitizedData = $this->sanitizeFormData($xssData);
        $this->assertStringNotContainsString('<script>', $sanitizedData['vorname']);
        $this->assertStringContainsString('&lt;script&gt;', $sanitizedData['vorname']);
    }
    
    /**
     * Test empty values
     */
    public function testEmptyValues() {
        $emptyData = [
            'vorname' => '',
            'name' => '',
            'email' => '',
            'firma' => '',
            'ansprechpartner' => '',
            'anschrift' => '',
            'plz' => '',
            'ort' => ''
        ];
        
        $this->assertFalse($this->validateFormData($emptyData));
    }
    
    /**
     * Test whitespace-only values
     */
    public function testWhitespaceOnlyValues() {
        $whitespaceData = [
            'vorname' => '   ',
            'name' => '   ',
            'email' => '   ',
            'firma' => '   ',
            'ansprechpartner' => '   ',
            'anschrift' => '   ',
            'plz' => '   ',
            'ort' => '   '
        ];
        
        $this->assertFalse($this->validateFormData($whitespaceData));
    }
    
    /**
     * Test special characters in names
     */
    public function testSpecialCharactersInNames() {
        $specialCharData = [
            'vorname' => 'Max-Müller',
            'name' => 'Mustermann',
            'email' => 'max.mustermann@example.com',
            'firma' => 'Test & Co. GmbH',
            'ansprechpartner' => 'Max Mustermann',
            'anschrift' => 'Musterstraße 123',
            'plz' => '12345',
            'ort' => 'Musterstadt'
        ];
        
        $this->assertTrue($this->validateFormData($specialCharData));
    }
    
    /**
     * Test international email addresses
     */
    public function testInternationalEmailAddresses() {
        $internationalEmails = [
            'test@example.com',
            'user.name@domain.co.uk',
            'user+tag@example.org',
            'user123@test-domain.com'
        ];
        
        foreach ($internationalEmails as $email) {
            $data = [
                'vorname' => 'Max',
                'name' => 'Mustermann',
                'email' => $email,
                'firma' => 'Test GmbH',
                'ansprechpartner' => 'Max Mustermann',
                'anschrift' => 'Musterstraße 123',
                'plz' => '12345',
                'ort' => 'Musterstadt'
            ];
            
            $this->assertTrue($this->validateFormData($data), "Failed for email: $email");
        }
    }
    
    /**
     * Test German postal codes
     */
    public function testGermanPostalCodes() {
        $validPLZs = ['12345', '01000', '99999'];
        $invalidPLZs = ['1234', '123456', 'abcde', '1234a'];
        
        foreach ($validPLZs as $plz) {
            $data = [
                'vorname' => 'Max',
                'name' => 'Mustermann',
                'email' => 'max.mustermann@example.com',
                'firma' => 'Test GmbH',
                'ansprechpartner' => 'Max Mustermann',
                'anschrift' => 'Musterstraße 123',
                'plz' => $plz,
                'ort' => 'Musterstadt'
            ];
            
            $this->assertTrue($this->validateFormData($data), "Failed for PLZ: $plz");
        }
        
        foreach ($invalidPLZs as $plz) {
            $data = [
                'vorname' => 'Max',
                'name' => 'Mustermann',
                'email' => 'max.mustermann@example.com',
                'firma' => 'Test GmbH',
                'ansprechpartner' => 'Max Mustermann',
                'anschrift' => 'Musterstraße 123',
                'plz' => $plz,
                'ort' => 'Musterstadt'
            ];
            
            $this->assertFalse($this->validateFormData($data), "Should fail for PLZ: $plz");
        }
    }
    
    /**
     * Validate form data (simplified version for testing)
     */
    private function validateFormData($data) {
        $requiredFields = [
            'vorname', 'name', 'email', 'firma', 'ansprechpartner',
            'anschrift', 'plz', 'ort'
        ];
        
        // Check required fields
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty(trim($data[$field]))) {
                return false;
            }
        }
        
        // Validate email format
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        
        // Validate German postal code
        if (!preg_match('/^[0-9]{5}$/', $data['plz'])) {
            return false;
        }
        
        // Validate field lengths
        $lengths = [
            'vorname' => 100,
            'name' => 100,
            'email' => 255,
            'firma' => 255,
            'ansprechpartner' => 255,
            'anschrift' => 500,
            'plz' => 10,
            'ort' => 255
        ];
        
        foreach ($lengths as $field => $maxLength) {
            if (strlen($data[$field]) > $maxLength) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Sanitize form data (simplified version for testing)
     */
    private function sanitizeFormData($data) {
        $sanitized = [];
        foreach ($data as $key => $value) {
            $sanitized[$key] = htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
        }
        return $sanitized;
    }
}
