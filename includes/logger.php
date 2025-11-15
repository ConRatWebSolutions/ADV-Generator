<?php

/**
 * Centralized Logging System
 * Schreibt Logs in den logs/ Ordner
 */

class Logger
{

    private static string $logDir;

    /**
     * Initialisiere Logger
     */
    private static function init(): void
    {
        if (!isset(self::$logDir)) {
            self::$logDir = __DIR__ . '/../logs/';

            // Erstelle logs/ Ordner falls nicht vorhanden
            if (!is_dir(self::$logDir)) {
                mkdir(self::$logDir, 0755, true);
            }
        }
    }

    /**
     * Hole User-Informationen für Logs
     */
    private static function getUserInfo(): string
    {
        $user = 'unknown';

        // Versuche E-Mail aus verschiedenen Quellen zu holen
        if (isset($_POST['email'])) {
            $user = $_POST['email'];
        } elseif (isset($_GET['email'])) {
            $user = $_GET['email'];
        } elseif (isset($_SESSION) && isset($_SESSION['email'])) {
            $user = $_SESSION['email'];
        } else {
            // Versuche aus JSON-Input zu lesen (für process_form.php)
            $input = file_get_contents('php://input');
            if (!empty($input)) {
                $jsonData = json_decode($input, true);
                if (json_last_error() === JSON_ERROR_NONE && isset($jsonData['email'])) {
                    $user = $jsonData['email'];
                }
            }
        }

        // IP-Adresse hinzufügen
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

        return "User: {$user} | IP: {$ip}";
    }

    /**
     * Schreibe in E-Mail-Log
     * @param string $message
     * @param string $level (info, success, error)
     */
    public static function logMail(string $message, string $level = 'info'): void
    {
        self::init();
        $logFile = self::$logDir . 'mailsend.log';
        $timestamp = date('Y-m-d H:i:s');
        $userInfo = self::getUserInfo();
        $logEntry = "[{$timestamp}] [{$level}] {$userInfo} | {$message}" . PHP_EOL;
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }

    /**
     * Schreibe in Error-Log
     * @param string $message
     * @param string|null $file
     * @param int|null $line
     */
    public static function logError(string $message, ?string $file = null, ?int $line = null): void
    {
        self::init();
        $logFile = self::$logDir . 'error.log';
        $timestamp = date('Y-m-d H:i:s');
        $userInfo = self::getUserInfo();

        $location = '';
        if ($file !== null) {
            $location = " | File: " . basename($file);
            if ($line !== null) {
                $location .= ":" . $line;
            }
        }

        $logEntry = "[{$timestamp}] [ERROR] {$userInfo}{$location} | {$message}" . PHP_EOL;
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }

    /**
     * Schreibe in Access-Log
     * @param string $message
     * @param string $method
     */
    public static function logAccess(string $message, string $method = 'GET'): void
    {
        self::init();
        $logFile = self::$logDir . 'access.log';
        $timestamp = date('Y-m-d H:i:s');
        $userInfo = self::getUserInfo();
        $requestUri = $_SERVER['REQUEST_URI'] ?? 'unknown';
        $logEntry = "[{$timestamp}] [{$method}] {$userInfo} | URI: {$requestUri} | {$message}" . PHP_EOL;
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }

    /**
     * Allgemeine Log-Funktion (für Kompatibilität mit error_log)
     * @param string $message
     * @param int $type
     */
    public static function log(string $message, int $type = 0): void
    {
        self::logError($message);
    }
}
