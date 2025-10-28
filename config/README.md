# Datenbankkonfiguration

Diese Konfiguration unterstützt automatische Umgebungserkennung für lokale DDEV-Entwicklung und Remote-Server.

## Automatische Umgebungserkennung

Das System erkennt automatisch die Umgebung basierend auf:

### DDEV (Lokale Entwicklung)
- `DDEV_HOSTNAME` Umgebungsvariable ist gesetzt
- `DDEV_PROJECT` Umgebungsvariable ist gesetzt
- `.ddev` Verzeichnis existiert
- `$_SERVER['DDEV_HOSTNAME']` ist gesetzt

### Remote-Server (Produktion)
- Keine DDEV-Umgebungsvariablen erkannt
- Lädt Konfiguration aus `.env.live` oder Umgebungsvariablen

## Konfigurationsdateien

### .env.live
Erstellen Sie eine `.env.live` Datei im Projektroot mit Ihren Remote-Server-Daten:

```env
# Produktionsumgebung (Remote-Server)
ENVIRONMENT=production
DB_HOST=your-remote-db-host.com
DB_PORT=3306
DB_NAME=your_production_db
DB_USER=your_production_user
DB_PASSWORD=your_secure_production_password
DB_CHARSET=utf8mb4
```

### DDEV-Konfiguration
Für DDEV wird automatisch folgende Konfiguration verwendet:
- Host: `db`
- Port: `3306`
- Datenbank: `db`
- Benutzer: `db`
- Passwort: `db`

## Verwendung

```php
// Datenbankverbindung herstellen
$pdo = DatabaseConfig::getConnection();

// Konfigurationsinfo abrufen
$config = DatabaseConfig::getConfigInfo();
echo "Aktuelle Umgebung: " . $config['environment'];

// Umgebungsinfo direkt abrufen
$environment = EnvironmentConfig::getEnvironment();
$isDebug = EnvironmentConfig::isDebug();
```

## Sicherheit

- Die `.env.live` Datei sollte niemals in Git committet werden
- Fügen Sie `.env.live` zu Ihrer `.gitignore` hinzu
- Verwenden Sie starke Passwörter für Produktionsumgebungen
- Sensitive Daten werden in Logs nicht ausgegeben

## Debugging

In der lokalen DDEV-Umgebung:
- Debug-Modus ist aktiviert
- Detaillierte Logs werden erstellt
- Fehler werden vollständig angezeigt

In der Produktionsumgebung:
- Debug-Modus ist deaktiviert
- Nur Fehler-Logs werden erstellt
- Fehler werden sicher behandelt
