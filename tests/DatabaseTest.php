<?php
/**
 * Database Tests for DSGVO ADV Project
 * Unit tests for database operations
 */

use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase {
    
    private $pdo;
    private $testDbName = 'dsgvo_adv_test';
    
    protected function setUp(): void {
        // Create test database connection
        $this->pdo = new PDO(
            'mysql:host=localhost;dbname=' . $this->testDbName,
            'root',
            '',
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
        
        // Create test tables
        $this->createTestTables();
    }
    
    protected function tearDown(): void {
        // Clean up test data
        $this->cleanupTestData();
    }
    
    /**
     * Test database connection
     */
    public function testDatabaseConnection() {
        $this->assertInstanceOf(PDO::class, $this->pdo);
        $this->assertNotFalse($this->pdo->query('SELECT 1'));
    }
    
    /**
     * Test agreement insertion
     */
    public function testInsertAgreement() {
        $agreementData = [
            'name' => 'Mustermann',
            'vorname' => 'Max',
            'anschrift' => 'Musterstraße 123',
            'firma' => 'Test GmbH',
            'email' => 'max.mustermann@example.com',
            'plz' => '12345',
            'ort' => 'Musterstadt',
            'ansprechpartner' => 'Max Mustermann',
            'ip_adresse' => '127.0.0.1'
        ];
        
        $stmt = $this->pdo->prepare("
            INSERT INTO auftragsverarbeitungsvereinbarungen 
            (name, vorname, anschrift, firma, email, plz, ort, ansprechpartner, ip_adresse) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $result = $stmt->execute([
            $agreementData['name'],
            $agreementData['vorname'],
            $agreementData['anschrift'],
            $agreementData['firma'],
            $agreementData['email'],
            $agreementData['plz'],
            $agreementData['ort'],
            $agreementData['ansprechpartner'],
            $agreementData['ip_adresse']
        ]);
        
        $this->assertTrue($result);
        $this->assertGreaterThan(0, $this->pdo->lastInsertId());
    }
    
    /**
     * Test agreement retrieval
     */
    public function testGetAgreement() {
        // Insert test agreement
        $agreementId = $this->insertTestAgreement();
        
        $stmt = $this->pdo->prepare("SELECT * FROM auftragsverarbeitungsvereinbarungen WHERE id = ?");
        $stmt->execute([$agreementId]);
        $agreement = $stmt->fetch();
        
        $this->assertNotFalse($agreement);
        $this->assertEquals($agreementId, $agreement['id']);
        $this->assertEquals('Test GmbH', $agreement['firma']);
    }
    
    /**
     * Test agreement update
     */
    public function testUpdateAgreement() {
        $agreementId = $this->insertTestAgreement();
        
        $stmt = $this->pdo->prepare("
            UPDATE auftragsverarbeitungsvereinbarungen 
            SET status = ?, pdf_pfad = ? 
            WHERE id = ?
        ");
        
        $result = $stmt->execute(['versendet', '/path/to/test.pdf', $agreementId]);
        $this->assertTrue($result);
        
        // Verify update
        $stmt = $this->pdo->prepare("SELECT status, pdf_pfad FROM auftragsverarbeitungsvereinbarungen WHERE id = ?");
        $stmt->execute([$agreementId]);
        $agreement = $stmt->fetch();
        
        $this->assertEquals('versendet', $agreement['status']);
        $this->assertEquals('/path/to/test.pdf', $agreement['pdf_pfad']);
    }
    
    /**
     * Test email log insertion
     */
    public function testInsertEmailLog() {
        $agreementId = $this->insertTestAgreement();
        
        $emailLogData = [
            'agreement_id' => $agreementId,
            'empfaenger' => 'test@example.com',
            'betreff' => 'Test Email',
            'status' => 'versendet',
            'fehler_nachricht' => null
        ];
        
        $stmt = $this->pdo->prepare("
            INSERT INTO email_logs 
            (agreement_id, empfaenger, betreff, status, fehler_nachricht) 
            VALUES (?, ?, ?, ?, ?)
        ");
        
        $result = $stmt->execute([
            $emailLogData['agreement_id'],
            $emailLogData['empfaenger'],
            $emailLogData['betreff'],
            $emailLogData['status'],
            $emailLogData['fehler_nachricht']
        ]);
        
        $this->assertTrue($result);
    }
    
    /**
     * Test system log insertion
     */
    public function testInsertSystemLog() {
        $logData = [
            'log_level' => 'info',
            'nachricht' => 'Test log message',
            'kontext' => json_encode(['test' => 'data'])
        ];
        
        $stmt = $this->pdo->prepare("
            INSERT INTO system_logs (log_level, nachricht, kontext) 
            VALUES (?, ?, ?)
        ");
        
        $result = $stmt->execute([
            $logData['log_level'],
            $logData['nachricht'],
            $logData['kontext']
        ]);
        
        $this->assertTrue($result);
    }
    
    /**
     * Test rate limiting insertion
     */
    public function testInsertRateLimit() {
        $rateLimitData = [
            'identifier' => '127.0.0.1',
            'type' => 'request',
            'block_until' => null
        ];
        
        $stmt = $this->pdo->prepare("
            INSERT INTO rate_limits (identifier, type, block_until) 
            VALUES (?, ?, ?)
        ");
        
        $result = $stmt->execute([
            $rateLimitData['identifier'],
            $rateLimitData['type'],
            $rateLimitData['block_until']
        ]);
        
        $this->assertTrue($result);
    }
    
    /**
     * Test prepared statements security
     */
    public function testPreparedStatementsSecurity() {
        $maliciousInput = "'; DROP TABLE auftragsverarbeitungsvereinbarungen; --";
        
        $stmt = $this->pdo->prepare("SELECT * FROM auftragsverarbeitungsvereinbarungen WHERE firma = ?");
        $stmt->execute([$maliciousInput]);
        
        // Table should still exist
        $result = $this->pdo->query("SHOW TABLES LIKE 'auftragsverarbeitungsvereinbarungen'");
        $this->assertNotFalse($result->fetch());
    }
    
    /**
     * Test transaction rollback
     */
    public function testTransactionRollback() {
        $this->pdo->beginTransaction();
        
        try {
            // Insert test data
            $stmt = $this->pdo->prepare("
                INSERT INTO auftragsverarbeitungsvereinbarungen 
                (name, vorname, anschrift, firma, email, plz, ort, ansprechpartner, ip_adresse) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute(['Test', 'User', 'Test St. 1', 'Test Corp', 'test@example.com', '12345', 'Test City', 'Test User', '127.0.0.1']);
            
            // Force error
            $this->pdo->exec("INVALID SQL");
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
        }
        
        // Verify no data was inserted
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM auftragsverarbeitungsvereinbarungen WHERE firma = ?");
        $stmt->execute(['Test Corp']);
        $count = $stmt->fetchColumn();
        
        $this->assertEquals(0, $count);
    }
    
    /**
     * Test database constraints
     */
    public function testDatabaseConstraints() {
        // Test NOT NULL constraint
        $this->expectException(PDOException::class);
        
        $stmt = $this->pdo->prepare("
            INSERT INTO auftragsverarbeitungsvereinbarungen 
            (name, vorname, anschrift, firma, email, plz, ort, ansprechpartner, ip_adresse) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([null, 'Max', 'Test St. 1', 'Test Corp', 'test@example.com', '12345', 'Test City', 'Test User', '127.0.0.1']);
    }
    
    /**
     * Test UTF-8 encoding
     */
    public function testUTF8Encoding() {
        $unicodeData = [
            'name' => 'Müller',
            'vorname' => 'Jürgen',
            'anschrift' => 'Straße 123',
            'firma' => 'Müller & Söhne GmbH',
            'email' => 'jürgen.müller@example.com',
            'plz' => '12345',
            'ort' => 'München',
            'ansprechpartner' => 'Jürgen Müller',
            'ip_adresse' => '127.0.0.1'
        ];
        
        $stmt = $this->pdo->prepare("
            INSERT INTO auftragsverarbeitungsvereinbarungen 
            (name, vorname, anschrift, firma, email, plz, ort, ansprechpartner, ip_adresse) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $result = $stmt->execute([
            $unicodeData['name'],
            $unicodeData['vorname'],
            $unicodeData['anschrift'],
            $unicodeData['firma'],
            $unicodeData['email'],
            $unicodeData['plz'],
            $unicodeData['ort'],
            $unicodeData['ansprechpartner'],
            $unicodeData['ip_adresse']
        ]);
        
        $this->assertTrue($result);
        
        // Verify data integrity
        $stmt = $this->pdo->prepare("SELECT * FROM auftragsverarbeitungsvereinbarungen WHERE firma = ?");
        $stmt->execute([$unicodeData['firma']]);
        $result = $stmt->fetch();
        
        $this->assertEquals($unicodeData['name'], $result['name']);
        $this->assertEquals($unicodeData['vorname'], $result['vorname']);
    }
    
    /**
     * Insert test agreement
     */
    private function insertTestAgreement() {
        $stmt = $this->pdo->prepare("
            INSERT INTO auftragsverarbeitungsvereinbarungen 
            (name, vorname, anschrift, firma, email, plz, ort, ansprechpartner, ip_adresse) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            'Mustermann',
            'Max',
            'Musterstraße 123',
            'Test GmbH',
            'max.mustermann@example.com',
            '12345',
            'Musterstadt',
            'Max Mustermann',
            '127.0.0.1'
        ]);
        
        return $this->pdo->lastInsertId();
    }
    
    /**
     * Create test tables
     */
    private function createTestTables() {
        $sql = "
            CREATE TABLE IF NOT EXISTS auftragsverarbeitungsvereinbarungen (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                vorname VARCHAR(255) NOT NULL,
                anschrift TEXT NOT NULL,
                firma VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL,
                plz VARCHAR(10) NOT NULL,
                ort VARCHAR(255) NOT NULL,
                ansprechpartner VARCHAR(255) NOT NULL,
                ip_adresse VARCHAR(45) NOT NULL,
                erstellt_am TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                pdf_pfad VARCHAR(500) NULL,
                status ENUM('erstellt', 'versendet', 'fehler') DEFAULT 'erstellt',
                fehler_nachricht TEXT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            
            CREATE TABLE IF NOT EXISTS email_logs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                agreement_id INT NOT NULL,
                empfaenger VARCHAR(255) NOT NULL,
                betreff VARCHAR(500) NOT NULL,
                status ENUM('versendet', 'fehler') NOT NULL,
                fehler_nachricht TEXT NULL,
                versendet_am TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            
            CREATE TABLE IF NOT EXISTS system_logs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                log_level ENUM('debug', 'info', 'warning', 'error') NOT NULL,
                nachricht TEXT NOT NULL,
                kontext JSON NULL,
                erstellt_am TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            
            CREATE TABLE IF NOT EXISTS rate_limits (
                id INT AUTO_INCREMENT PRIMARY KEY,
                identifier VARCHAR(255) NOT NULL,
                type ENUM('request', 'block') NOT NULL,
                block_until TIMESTAMP NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        
        $this->pdo->exec($sql);
    }
    
    /**
     * Clean up test data
     */
    private function cleanupTestData() {
        $tables = ['auftragsverarbeitungsvereinbarungen', 'email_logs', 'system_logs', 'rate_limits'];
        
        foreach ($tables as $table) {
            $this->pdo->exec("DELETE FROM $table");
        }
    }
}
