# ADV-Generator – Auftragsverarbeitungsvereinbarung

PHP-Anwendung zur automatisierten Erstellung von DSGVO-konformen Auftragsverarbeitungsvereinbarungen (AVV). Nutzer füllen ein Webformular aus, das System speichert die Daten in einer Datenbank, generiert ein PDF und versendet es per E-Mail.

## Voraussetzungen

- [DDEV](https://ddev.readthedocs.io/en/stable/users/install/) installiert
- Git installiert

## Lokale Entwicklung einrichten

### 1. Projekt klonen

```bash
git clone git@github.com:ConRatWebSolutions/ADV-Generator.git ADV-somesolutions
cd ADV-somesolutions
```

### 2. DDEV-Umgebung starten

```bash
ddev start
```

### 3. Composer-Abhängigkeiten installieren

```bash
ddev composer install
```

### 4. Umgebungsvariablen (Produktion)

Für den Produktivbetrieb wird eine `.env.live`-Datei benötigt (nicht im Repository enthalten). Bei Bedarf beim Entwickler anfragen. Lokal ist keine `.env`-Datei erforderlich – DDEV stellt alle Datenbankverbindungen automatisch bereit.

### 5. Datenbank einrichten

Das Datenbankschema liegt in `database/`:

```bash
# MySQL/MariaDB (lokal via DDEV)
ddev mysql < database/schema_mysql.sql

# PostgreSQL (Produktion)
psql < database/schema_postgresql.sql
```

## Lokale URLs

| Dienst     | URL                                      |
| ---------- | ---------------------------------------- |
| Anwendung  | https://ADV-somesolutions.ddev.site      |
| phpMyAdmin | https://ADV-somesolutions.ddev.site:8037 |
| Mailpit    | https://ADV-somesolutions.ddev.site:8026 |

## Nützliche DDEV-Befehle

```bash
ddev start              # Entwicklungsumgebung starten
ddev stop               # Entwicklungsumgebung stoppen
ddev restart            # Entwicklungsumgebung neu starten
ddev ssh                # SSH-Zugang zum Web-Container
ddev launch             # Browser öffnen
ddev composer <cmd>     # Composer-Befehle ausführen
ddev exec <cmd>         # Beliebige Befehle im Container ausführen
ddev deploy             # Dateien auf den Produktionsserver deployen
```

## Projektstruktur

```
ADV-somesolutions/
├── public/                     # Document Root (nginx)
│   ├── index.html              # Formular (CSRF-geschützt, fetch-basiert)
│   ├── process_form.php        # Hauptverarbeitung: DB → PDF → E-Mail
│   ├── preview_agreement.php   # Vorschau des generierten Vertrags
│   └── get_csrf_token.php      # CSRF-Token-Endpoint
│
├── config/                     # Konfigurationsklassen
│   ├── environment.php         # EnvironmentConfig: DDEV vs. Produktion
│   ├── database.php            # DatabaseConfig: MySQL / PostgreSQL
│   ├── pdf_config.php          # PDFConfig + CustomTCPDF (TCPDF-Wrapper)
│   └── email_config.php        # EmailConfig (PHPMailer)
│
├── templates/                  # Vertragsvorlagen (HTML-Strings)
│   ├── agreement_template.php  # Hauptvertrag §§ 1–10
│   ├── anlage1.php             # Anlage 1: Dienstleistungen & Verarbeitungen
│   ├── anlage2.php             # Anlage 2: Technische und organisatorische Maßnahmen
│   ├── anlage3.php             # Anlage 3: Unterauftragsverarbeiter
│   └── anlage4.php             # Anlage 4: Weitere Regelungen
│
├── includes/                   # Hilfsklassen
│   ├── logger.php
│   ├── csrf_protection.php
│   ├── rate_limiter.php
│   ├── database_operations.php
│   └── form_processor.php
│
├── database/                   # Datenbankschemas
│   ├── schema_mysql.sql
│   └── schema_postgresql.sql
│
├── storage/pdfs/               # Generierte PDFs (schreibbar halten)
├── logs/                       # access.log, error.log, mailsend.log
└── vendor/                     # Composer-Abhängigkeiten (nicht im Repo)
```

## Ablauf der Formularverarbeitung

1. `public/index.html` – Nutzer füllt das AVV-Formular aus
2. Fetch-Request (JSON) an `public/process_form.php`
3. `process_form.php` führt aus:
   - CSRF-Validierung & Rate-Limiting
   - Formulardaten in Datenbank speichern
   - PDF mit TCPDF generieren → `storage/pdfs/`
   - E-Mail mit PHPMailer versenden (an Nutzer + Admin)

## Technologie-Stack

| Komponente   | Technologie                                         |
| ------------ | --------------------------------------------------- |
| Sprache      | PHP 8.2                                             |
| Webserver    | nginx-fpm (DDEV)                                    |
| Datenbank    | MariaDB 10.11 (lokal) / PostgreSQL (Produktion)     |
| PDF          | [TCPDF](https://tcpdf.org/) via `tecnickcom/tcpdf`  |
| E-Mail       | [PHPMailer](https://github.com/PHPMailer/PHPMailer) |
| Dev-Umgebung | [DDEV](https://ddev.readthedocs.io/)                |
| Mail-Testing | Mailpit                                             |

## Deployment

```bash
ddev deploy             # Dateien auf den Produktionsserver übertragen
```

**Voraussetzung:** Der lokale SSH-Public-Key muss auf dem Produktionsserver in `~/.ssh/authorized_keys` hinterlegt sein, damit `ddev deploy` ohne Passwortabfrage funktioniert.

```bash
# Einmalige Einrichtung: SSH-Key auf den Server kopieren
ssh-copy-id benutzer@produktionsserver
```

## Produktion

- Umgebungserkennung erfolgt automatisch anhand der Env-Vars `DDEV_HOSTNAME` / `DDEV_PROJECT`
- Produktivkonfiguration wird aus `.env.live` geladen (Kleinbuchstaben-Keys: `db_host`, `mail_password` etc.)
- PostgreSQL verwendet `RETURNING id` statt `lastInsertId()`
- `storage/pdfs/` muss auf dem Server schreibbar sein
