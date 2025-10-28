<?php
/**
 * Database Configuration for DSGVO ADV Project
 * MariaDB 10.4 compatible
 * Unterstützt automatische Umgebungserkennung (DDEV vs. Remote-Server)
 */

require_once __DIR__ . '/environment.php';

class DatabaseConfig {
    
    private static $connection = null;
    
    /**
     * Get database connection
     * @return PDO
     * @throws PDOException
     */
    public static function getConnection(): PDO {
        if (self::$connection === null) {
            try {
                // Lade Umgebungskonfiguration
                $config = EnvironmentConfig::loadConfig();
                
                // Erstelle DSN basierend auf Datenbanktyp
                $dsn = self::buildDsn($config);
                
                // PDO-Optionen basierend auf Datenbanktyp
                $options = self::getDbOptions($config['db_type']);
                
                self::$connection = new PDO(
                    $dsn,
                    $config['db_user'],
                    $config['db_password'],
                    $options
                );
                
                // Log successful connection mit Umgebungsinfo
                $env = EnvironmentConfig::getEnvironment();
                $dbType = $config['db_type'];
                self::logConnection('info', "Database connection established successfully (Environment: {$env}, DB: {$dbType})");
                
            } catch (PDOException $e) {
                $env = EnvironmentConfig::getEnvironment();
                $dbType = $config['db_type'] ?? 'unknown';
                self::logConnection('error', "Database connection failed (Environment: {$env}, DB: {$dbType}): " . $e->getMessage());
                throw new PDOException('Database connection failed: ' . $e->getMessage());
            }
        }
        
        return self::$connection;
    }
    
    /**
     * Erstelle DSN basierend auf Datenbanktyp
     * @param array $config
     * @return string
     */
    private static function buildDsn(array $config): string {
        $dbType = $config['db_type'] ?? 'mysql';
        
        switch ($dbType) {
            case 'postgresql':
            case 'postgres':
                return sprintf(
                    'pgsql:host=%s;port=%d;dbname=%s',
                    $config['db_host'],
                    $config['db_port'],
                    $config['db_name']
                );
                
            case 'mysql':
            default:
                return sprintf(
                    'mysql:host=%s;port=%d;dbname=%s;charset=%s',
                    $config['db_host'],
                    (int)$config['db_port'],
                    $config['db_name'],
                    $config['db_charset']
                );
        }
    }
    
    /**
     * Hole PDO-Optionen basierend auf Datenbanktyp
     * @param string $dbType
     * @return array
     */
    private static function getDbOptions(string $dbType): array {
        $baseOptions = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ];
        
        switch ($dbType) {
            case 'postgresql':
            case 'postgres':
                return $baseOptions;
                
            case 'mysql':
            default:
                $mysqlOptions = $baseOptions;
                $mysqlOptions[PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci";
                return $mysqlOptions;
        }
    }
    
    /**
     * Test database connection
     * @return bool
     */
    public static function testConnection(): bool {
        try {
            $pdo = self::getConnection();
            $stmt = $pdo->query('SELECT 1');
            return $stmt !== false;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Get database statistics
     * @return array
     */
    public static function getStats(): array {
        try {
            $pdo = self::getConnection();
            
            // Count total agreements using prepared statement
            $stmt = $pdo->prepare('SELECT COUNT(*) as total FROM auftragsverarbeitungsvereinbarungen');
            $stmt->execute();
            $total = $stmt->fetch()['total'];
            
            // Count by status using prepared statement
            $stmt = $pdo->prepare('SELECT status, COUNT(*) as count FROM auftragsverarbeitungsvereinbarungen GROUP BY status');
            $stmt->execute();
            $statusCounts = $stmt->fetchAll();
            
            // Count emails sent today using prepared statement
            $stmt = $pdo->prepare('SELECT COUNT(*) as emails_today FROM email_logs WHERE DATE(versendet_am) = CURDATE()');
            $stmt->execute();
            $emailsToday = $stmt->fetch()['emails_today'];
            
            return [
                'total_agreements' => $total,
                'status_counts' => $statusCounts,
                'emails_today' => $emailsToday,
                'connection_status' => 'connected'
            ];
            
        } catch (Exception $e) {
            return [
                'error' => $e->getMessage(),
                'connection_status' => 'error'
            ];
        }
    }
    
    /**
     * Log database events
     * @param string $level
     * @param string $message
     */
    private static function logConnection(string $level, string $message): void {
        try {
            $pdo = self::getConnection();
            $stmt = $pdo->prepare('INSERT INTO system_logs (log_level, nachricht) VALUES (?, ?)');
            $stmt->execute([$level, $message]);
        } catch (Exception $e) {
            // Fallback to file logging if database logging fails
            error_log("Database Log Error: " . $e->getMessage());
        }
    }
    
    /**
     * Get current database configuration (without sensitive data)
     * @return array
     */
    public static function getConfigInfo(): array {
        $config = EnvironmentConfig::loadConfig();
        
        return [
            'environment' => $config['environment'],
            'db_type' => $config['db_type'],
            'db_host' => $config['db_host'],
            'db_port' => $config['db_port'],
            'db_name' => $config['db_name'],
            'db_charset' => $config['db_charset'],
            'debug_mode' => EnvironmentConfig::isDebug(),
            'is_ddev' => EnvironmentConfig::getEnvironment() === 'local'
        ];
    }
    
    /**
     * Close database connection
     */
    public static function closeConnection(): void {
        self::$connection = null;
    }
}
