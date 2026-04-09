<?php

/**
 * Environment Configuration Handler
 * Automatische Erkennung der Umgebung (DDEV vs. Remote-Server)
 */

class EnvironmentConfig
{
    private static $config = null;

    /**
     * Lade Umgebungskonfiguration
     * @return array
     */
    public static function loadConfig(): array
    {
        if (self::$config === null) {
            // Prüfe auf DDEV-Umgebung
            if (self::isDdevEnvironment()) {
                self::$config = self::loadDdevConfig();
            } else {
                // Lade Produktionskonfiguration
                self::$config = self::loadProductionConfig();
            }
        }

        return self::$config;
    }

    /**
     * Prüfe ob DDEV-Umgebung erkannt wird
     * @return bool
     */
    private static function isDdevEnvironment(): bool
    {
        // DDEV-spezifische Erkennungsmerkmale
        return (
            // DDEV_HOSTNAME ist gesetzt
            getenv('DDEV_HOSTNAME') !== false ||
            // DDEV_PROJECT ist gesetzt
            getenv('DDEV_PROJECT') !== false ||
            // Prüfe auf .ddev Verzeichnis
            file_exists(__DIR__ . '/../.ddev') ||
            // Prüfe auf DDEV_HOSTNAME in $_SERVER
            (isset($_SERVER['DDEV_HOSTNAME']) && !empty($_SERVER['DDEV_HOSTNAME']))
        );
    }

    /**
     * Lade DDEV-spezifische Konfiguration
     * @return array
     */
    private static function loadDdevConfig(): array
    {
        return [
            'environment' => 'local',
            'db_type' => 'mysql',
            'db_host' => 'db',
            'db_port' => 3306,
            'db_name' => 'db',
            'db_user' => 'db',
            'db_password' => 'db',
            'db_charset' => 'utf8mb4',
            'debug' => true,
            'log_level' => 'debug',
            // E-Mail-Konfiguration für DDEV (Mailpit)
            'mail_host' => 'localhost:1025',
            'mail_port' => '',
            'mail_username' => '',
            'mail_password' => '',
            'mail_encryption' => '',
            'mail_from_address' => 'noreply@adv-somesolutions.ddev.site',
            'mail_from_name' => 'ADV-Somesolutions (DDEV)',
            'admin_email' => 'mlehmann@conrat.de'
        ];
    }

    /**
     * Lade Produktionskonfiguration
     * @return array
     */
    private static function loadProductionConfig(): array
    {
        // Versuche .env Datei zu laden
        $envFile = __DIR__ . '/../.env.live';
        if (file_exists($envFile)) {
            return self::parseEnvFile($envFile);
        }

        // Fallback auf Umgebungsvariablen (sollte .env.live verwenden)
        return [
            'environment' => getenv('ENVIRONMENT') ?: 'production',
            'db_type' => getenv('DB_TYPE') ?: 'postgresql',
            'db_host' => getenv('DB_HOST') ?: 'localhost',
            'db_port' => (int)(getenv('DB_PORT') ?: 5432),
            'db_name' => getenv('DB_NAME') ?: 'production_db',
            'db_user' => getenv('DB_USER') ?: 'production_user',
            'db_password' => getenv('DB_PASSWORD') ?: '',
            'db_charset' => getenv('DB_CHARSET') ?: 'utf8',
            'debug' => false,
            'log_level' => 'error',
            // E-Mail-Konfiguration für Produktion
            'mail_host' => getenv('MAIL_HOST') ?: 'smtp.example.com',
            'mail_port' => (int)(getenv('MAIL_PORT') ?: 587),
            'mail_username' => getenv('MAIL_USERNAME') ?: '',
            'mail_password' => getenv('MAIL_PASSWORD') ?: '',
            'mail_encryption' => getenv('MAIL_ENCRYPTION') ?: 'tls',
            'mail_from_address' => getenv('MAIL_FROM_ADDRESS') ?: 'noreply@adv-somesolutions.de',
            'mail_from_name' => getenv('MAIL_FROM_NAME') ?: 'ADV-Somesolutions',
            'admin_email' => getenv('ADMIN_EMAIL') ?: 'mlehmann@conrat.de'
        ];
    }

    /**
     * Parse .env Datei
     * @param string $filePath
     * @return array
     */
    private static function parseEnvFile(string $filePath): array
    {
        $config = [];
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            // Ignoriere Kommentare
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // Parse KEY=VALUE Paare
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);

                // Entferne Anführungszeichen falls vorhanden
                if ((substr($value, 0, 1) === '"' && substr($value, -1) === '"') ||
                    (substr($value, 0, 1) === "'" && substr($value, -1) === "'")
                ) {
                    $value = substr($value, 1, -1);
                }

                $key = strtolower($key);

                // Konvertiere spezielle Werte
                if ($key === 'db_port') {
                    $config[$key] = (int)$value;
                } else {
                    $config[$key] = $value;
                }
            }
        }

        return $config;
    }

    /**
     * Hole spezifischen Konfigurationswert
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        $config = self::loadConfig();
        return $config[$key] ?? $default;
    }

    /**
     * Prüfe ob Debug-Modus aktiv ist
     * @return bool
     */
    public static function isDebug(): bool
    {
        return (bool)self::get('debug', false);
    }

    /**
     * Hole aktuelle Umgebung
     * @return string
     */
    public static function getEnvironment(): string
    {
        return self::get('environment', 'production');
    }
}
