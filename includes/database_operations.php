<?php
/**
 * Secure Database Operations for DSGVO ADV Project
 * All operations use prepared statements for SQL injection protection
 */

require_once __DIR__ . '/../config/database.php';

class DatabaseOperations {
    
    /**
     * Insert new agreement into database
     * @param array $data User form data
     * @return int|false Agreement ID or false on error
     */
    public static function insertAgreement(array $data): int|false {
        try {
            $pdo = DatabaseConfig::getConnection();
            
            $sql = "INSERT INTO auftragsverarbeitungsvereinbarungen 
                    (name, vorname, anschrift, firma, email, plz, ort, ansprechpartner, ip_adresse, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([
                $data['name'],
                $data['vorname'],
                $data['anschrift'],
                $data['firma'],
                $data['email'],
                $data['plz'],
                $data['ort'],
                $data['ansprechpartner'],
                $data['ip_adresse'],
                'erstellt'
            ]);
            
            if ($result) {
                $agreementId = $pdo->lastInsertId();
                self::logOperation('info', "Agreement created with ID: $agreementId");
                return $agreementId;
            }
            
            return false;
            
        } catch (Exception $e) {
            self::logOperation('error', "Failed to insert agreement: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update agreement with PDF path
     * @param int $agreementId
     * @param string $pdfPath
     * @return bool
     */
    public static function updateAgreementPdf(int $agreementId, string $pdfPath): bool {
        try {
            $pdo = DatabaseConfig::getConnection();
            
            $sql = "UPDATE auftragsverarbeitungsvereinbarungen 
                    SET pdf_pfad = ?, status = 'versendet' 
                    WHERE id = ?";
            
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([$pdfPath, $agreementId]);
            
            if ($result) {
                self::logOperation('info', "Agreement $agreementId updated with PDF path");
            }
            
            return $result;
            
        } catch (Exception $e) {
            self::logOperation('error', "Failed to update agreement PDF: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update agreement status
     * @param int $agreementId
     * @param string $status
     * @param string|null $errorMessage
     * @return bool
     */
    public static function updateAgreementStatus(int $agreementId, string $status, ?string $errorMessage = null): bool {
        try {
            $pdo = DatabaseConfig::getConnection();
            
            $sql = "UPDATE auftragsverarbeitungsvereinbarungen 
                    SET status = ?, fehler_nachricht = ? 
                    WHERE id = ?";
            
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([$status, $errorMessage, $agreementId]);
            
            if ($result) {
                self::logOperation('info', "Agreement $agreementId status updated to: $status");
            }
            
            return $result;
            
        } catch (Exception $e) {
            self::logOperation('error', "Failed to update agreement status: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get agreement by ID
     * @param int $agreementId
     * @return array|false
     */
    public static function getAgreementById(int $agreementId): array|false {
        try {
            $pdo = DatabaseConfig::getConnection();
            
            $sql = "SELECT * FROM auftragsverarbeitungsvereinbarungen WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$agreementId]);
            
            return $stmt->fetch();
            
        } catch (Exception $e) {
            self::logOperation('error', "Failed to get agreement: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get agreements by status
     * @param string $status
     * @return array
     */
    public static function getAgreementsByStatus(string $status): array {
        try {
            $pdo = DatabaseConfig::getConnection();
            
            $sql = "SELECT * FROM auftragsverarbeitungsvereinbarungen WHERE status = ? ORDER BY erstellt_am DESC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$status]);
            
            return $stmt->fetchAll();
            
        } catch (Exception $e) {
            self::logOperation('error', "Failed to get agreements by status: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get agreements by date range
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public static function getAgreementsByDateRange(string $startDate, string $endDate): array {
        try {
            $pdo = DatabaseConfig::getConnection();
            
            $sql = "SELECT * FROM auftragsverarbeitungsvereinbarungen 
                    WHERE DATE(erstellt_am) BETWEEN ? AND ? 
                    ORDER BY erstellt_am DESC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$startDate, $endDate]);
            
            return $stmt->fetchAll();
            
        } catch (Exception $e) {
            self::logOperation('error', "Failed to get agreements by date range: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Search agreements by company name
     * @param string $companyName
     * @return array
     */
    public static function searchAgreementsByCompany(string $companyName): array {
        try {
            $pdo = DatabaseConfig::getConnection();
            
            $sql = "SELECT * FROM auftragsverarbeitungsvereinbarungen 
                    WHERE firma LIKE ? 
                    ORDER BY erstellt_am DESC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(["%$companyName%"]);
            
            return $stmt->fetchAll();
            
        } catch (Exception $e) {
            self::logOperation('error', "Failed to search agreements by company: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get email logs for an agreement
     * @param int $agreementId
     * @return array
     */
    public static function getEmailLogsForAgreement(int $agreementId): array {
        try {
            $pdo = DatabaseConfig::getConnection();
            
            $sql = "SELECT * FROM email_logs WHERE vereinbarung_id = ? ORDER BY versendet_am DESC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$agreementId]);
            
            return $stmt->fetchAll();
            
        } catch (Exception $e) {
            self::logOperation('error', "Failed to get email logs: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Insert email log entry
     * @param int $agreementId
     * @param string $email
     * @param string $type
     * @param string $status
     * @param string|null $errorMessage
     * @return bool
     */
    public static function insertEmailLog(int $agreementId, string $email, string $type, string $status, ?string $errorMessage = null): bool {
        try {
            $pdo = DatabaseConfig::getConnection();
            
            $sql = "INSERT INTO email_logs (vereinbarung_id, empfaenger_email, empfaenger_typ, status, fehler_nachricht) 
                    VALUES (?, ?, ?, ?, ?)";
            
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([$agreementId, $email, $type, $status, $errorMessage]);
            
            if ($result) {
                self::logOperation('info', "Email log inserted for agreement $agreementId");
            }
            
            return $result;
            
        } catch (Exception $e) {
            self::logOperation('error', "Failed to insert email log: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get system logs
     * @param int $limit
     * @return array
     */
    public static function getSystemLogs(int $limit = 100): array {
        try {
            $pdo = DatabaseConfig::getConnection();
            
            $sql = "SELECT * FROM system_logs ORDER BY erstellt_am DESC LIMIT ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$limit]);
            
            return $stmt->fetchAll();
            
        } catch (Exception $e) {
            self::logOperation('error', "Failed to get system logs: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Log database operation
     * @param string $level
     * @param string $message
     */
    public static function logOperation(string $level, string $message): void {
        try {
            $pdo = DatabaseConfig::getConnection();
            $stmt = $pdo->prepare("INSERT INTO system_logs (log_level, nachricht) VALUES (?, ?)");
            $stmt->execute([$level, $message]);
        } catch (Exception $e) {
            error_log("Database logging failed: " . $e->getMessage());
        }
    }
}
