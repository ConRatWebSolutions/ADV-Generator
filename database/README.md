# Datenbank-Setup für DSGVO ADV Projekt

## 📋 Übersicht

Dieses Verzeichnis enthält die SQL-Schemas für beide Datenbanktypen:
- **MySQL/MariaDB** (lokal mit DDEV)
- **PostgreSQL** (Live-Server)

## 🚀 Schnellstart

### Lokale Entwicklung (DDEV + MariaDB)
```bash
# Datenbank initialisieren
ddev init-db
```

### Live-Server (PostgreSQL)
```bash
# Datenbank auf Live-Server initialisieren
ddev init-db-live
```

## 📊 Tabellen-Übersicht

### 1. `auftragsverarbeitungsvereinbarungen`
**Haupttabelle für die Formulardaten**
- `id` - Eindeutige ID (AUTO_INCREMENT/SERIAL)
- `name` - Nachname
- `vorname` - Vorname
- `anschrift` - Straße und Hausnummer
- `firma` - Firmenname
- `email` - E-Mail-Adresse
- `plz` - Postleitzahl
- `ort` - Ort
- `ansprechpartner` - Ansprechpartner
- `ip_adresse` - IP-Adresse des Absenders
- `status` - Status (erstellt/versendet/fehler)
- `pdf_pfad` - Pfad zur generierten PDF
- `fehler_nachricht` - Fehlermeldung bei Problemen
- `erstellt_am` - Erstellungszeitpunkt
- `aktualisiert_am` - Letzte Aktualisierung

### 2. `email_logs`
**E-Mail-Versand-Protokoll**
- `id` - Eindeutige ID
- `vereinbarung_id` - Referenz zur Vereinbarung
- `empfaenger_email` - E-Mail-Adresse des Empfängers
- `empfaenger_typ` - Typ (kunde/admin)
- `status` - Versandstatus (versendet/fehler)
- `fehler_nachricht` - Fehlermeldung
- `versendet_am` - Versandzeitpunkt

### 3. `system_logs`
**System-Ereignisse und Debugging**
- `id` - Eindeutige ID
- `log_level` - Log-Level (debug/info/warning/error/critical)
- `nachricht` - Log-Nachricht
- `erstellt_am` - Zeitstempel

### 4. `csrf_tokens`
**CSRF-Schutz**
- `id` - Eindeutige ID
- `token` - CSRF-Token
- `ip_adresse` - IP-Adresse
- `erstellt_am` - Erstellungszeitpunkt
- `expires_at` - Ablaufzeitpunkt

### 5. `rate_limits`
**Rate Limiting**
- `id` - Eindeutige ID
- `ip_adresse` - IP-Adresse
- `request_count` - Anzahl der Anfragen
- `first_request` - Erste Anfrage
- `last_request` - Letzte Anfrage
- `blocked_until` - Blockiert bis

## 🔧 Manuelle Installation

### MySQL/MariaDB
```bash
mysql -h db -u root -proot db < database/schema_mysql.sql
```

### PostgreSQL
```bash
psql -h your-host -p 5432 -U your-user -d your-database < database/schema_postgresql.sql
```

## 🔍 Datenbank testen

### Lokal (DDEV)
```bash
# Verbindung testen
ddev mysql

# Tabellen anzeigen
SHOW TABLES;

# Daten anzeigen
SELECT * FROM auftragsverarbeitungsvereinbarungen;
```

### Live-Server (PostgreSQL)
```bash
# Verbindung testen
psql -h 194.164.63.96 -p 5432 -U advsomesolutions -d adv

# Tabellen anzeigen
\dt

# Daten anzeigen
SELECT * FROM auftragsverarbeitungsvereinbarungen;
```

## 🛠️ Wartung

### Backup erstellen
```bash
# MySQL/MariaDB
mysqldump -h db -u root -proot db > backup.sql

# PostgreSQL
pg_dump -h your-host -U your-user your-database > backup.sql
```

### Logs bereinigen
```sql
-- Alte System-Logs löschen (älter als 30 Tage)
DELETE FROM system_logs WHERE erstellt_am < NOW() - INTERVAL 30 DAY;

-- Alte CSRF-Token löschen (abgelaufen)
DELETE FROM csrf_tokens WHERE expires_at < NOW();
```

## 🔒 Sicherheit

- Alle Tabellen verwenden **Prepared Statements**
- **Fremdschlüssel** für Datenintegrität
- **Indizes** für Performance
- **Zeitstempel** für Audit-Trail
- **IP-Tracking** für Sicherheit
