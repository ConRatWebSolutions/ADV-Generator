# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a PHP application for generating DSGVO (GDPR) Auftragsverarbeitungsvereinbarungen (data processing agreements). Users fill out a form, the system saves their data to a database, generates a PDF agreement, and emails it to them and the admin.

## Development Environment

The project runs on **DDEV** (PHP 8.2, MariaDB 10.11, nginx-fpm). The document root is `public/`.

```bash
ddev start                  # Start local environment
ddev stop                   # Stop local environment
ddev ssh                    # SSH into web container
ddev composer install       # Install PHP dependencies
ddev launch                 # Open https://ADV-somesolutions.ddev.site in browser
```

Email testing in local dev uses **Mailpit** (accessible at `https://ADV-somesolutions.ddev.site:8026`).

## Architecture

### Request Flow
1. User fills out `public/index.html` (static form with CSRF protection)
2. Form submits JSON via fetch to `public/process_form.php`
3. `process_form.php` orchestrates: DB insert → PDF generation → Email send

### Configuration Layer (`config/`)
- `environment.php` — `EnvironmentConfig` class: auto-detects DDEV vs. production by checking `DDEV_HOSTNAME`/`DDEV_PROJECT` env vars or `.ddev` directory. Local uses MySQL/MariaDB; production reads from `.env.live` and defaults to PostgreSQL.
- `database.php` — `DatabaseConfig` singleton: builds DSN from `EnvironmentConfig`, supports both MySQL and PostgreSQL. SQL in `process_form.php` branches on `db_type` because PostgreSQL uses `RETURNING id` while MySQL uses `lastInsertId()`.
- `pdf_config.php` — `PDFConfig` + `CustomTCPDF` classes: wraps TCPDF, adds a header image (`templates/header.png`) and footer on every page, outputs PDFs to `storage/pdfs/`.
- `email_config.php` — `EmailConfig` class: uses PHPMailer, reads SMTP settings from `EnvironmentConfig`.

### Template Layer (`templates/`)
- `agreement_template.php` — `AgreementTemplate::generateAgreementText()`: returns HTML string with user data interpolated, representing the main agreement body (§§ 1–10).
- `anlage1.php` through `anlage4.php` — each exports a class (`Anlage1`–`Anlage4`) with a static `getContent()` method returning HTML. All four appendices are appended as separate PDF pages.
- There is a single shared `anlage1.php`; the old per-service anlage files were deleted and replaced with it.

### Support Layer (`includes/`)
- `logger.php`, `csrf_protection.php`, `rate_limiter.php`, `database_operations.php`, `form_processor.php`

### Logging
Logs are written to `logs/` (access.log, error.log, mailsend.log) using direct `file_put_contents` calls in `process_form.php`.

### Dependencies (via Composer)
- `tecnickcom/tcpdf` — PDF generation
- `phpmailer/phpmailer` — Email sending

## Production Deployment

Production uses `.env.live` (not committed) with PostgreSQL. The `EnvironmentConfig::loadProductionConfig()` parses this file. All keys are lowercase (e.g. `db_host`, `mail_password`).

PDFs are stored in `storage/pdfs/` — ensure this directory is writable on the server.
