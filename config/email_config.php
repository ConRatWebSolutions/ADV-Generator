<?php

/**
 * Email Configuration for DSGVO ADV Project
 * PHPMailer integration with environment-based settings
 */

// Load Composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../includes/logger.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailConfig
{
    private PHPMailer $mailer;
    private array $config;

    public function __construct()
    {
        $this->loadConfig();
        $this->initializeMailer();
    }

    /**
     * Load email configuration from environment
     */
    private function loadConfig(): void
    {
        // Lade Konfiguration aus EnvironmentConfig
        require_once __DIR__ . '/environment.php';
        $envConfig = EnvironmentConfig::loadConfig();

        $this->config = [
            'host' => $envConfig['mail_host'] ?? 'mailpit',
            'port' => (int)($envConfig['mail_port'] ?? 1025),
            'username' => $envConfig['mail_username'] ?? '',
            'password' => $envConfig['mail_password'] ?? '',
            'encryption' => $envConfig['mail_encryption'] ?? '',
            'from_address' => $envConfig['mail_from_address'] ?? 'noreply@adv-somesolutions.ddev.site',
            'from_name' => $envConfig['mail_from_name'] ?? 'ADV-Somesolutions',
            'admin_email' => $envConfig['admin_email'] ?? 'info@conrat.de'
        ];
    }

    /**
     * Get PHPMailer instance
     */
    public function getMailer(): PHPMailer
    {
        return $this->mailer;
    }

    /**
     * Initialize PHPMailer with configuration
     */
    private function initializeMailer(): void
    {
        $this->mailer = new PHPMailer(true);


        try {
            // Server settings
            $this->mailer->isSMTP();
            $this->mailer->Host = $this->config['host'];
            $this->mailer->SMTPAuth = !empty($this->config['username']);
            $this->mailer->Username = $this->config['username'];
            $this->mailer->Password = $this->config['password'];
            $this->mailer->SMTPSecure = $this->config['encryption'];
            $this->mailer->Port = $this->config['port'];

            // Character encoding
            $this->mailer->CharSet = 'UTF-8';
            $this->mailer->Encoding = 'base64';

            // Default sender
            $this->mailer->setFrom($this->config['from_address'], $this->config['from_name']);
        } catch (Exception $e) {
            Logger::logError("Email configuration error: " . $e->getMessage(), __FILE__, __LINE__);
            throw new Exception("Failed to initialize email configuration");
        }
    }

    /**
     * Send agreement PDF to user
     * @param string $userEmail
     * @param string $userName
     * @param string $pdfPath
     * @param string $companyName
     * @return bool
     */
    public function sendAgreementToUser(string $userEmail, string $userName, string $pdfPath, string $companyName): bool
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();

            // Recipient
            $this->mailer->addAddress($userEmail, $userName);

            // Subject and body
            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Ihre DSGVO-Auftragsverarbeitungsvereinbarung';
            $this->mailer->Body = $this->getUserEmailTemplate($userName, $companyName);
            $this->mailer->AltBody = $this->getUserEmailTextTemplate($userName, $companyName);

            // PDF attachment (nur wenn Datei existiert)
            if (!empty($pdfPath) && file_exists($pdfPath)) {
                $this->mailer->addAttachment($pdfPath, 'Auftragsverarbeitungsvereinbarung.pdf');
            } else {
                Logger::logMail("PDF-Datei nicht gefunden: " . $pdfPath, 'error');
            }

            $result = $this->mailer->send();

            if ($result) {
                $this->logEmailSent($userEmail, 'nutzer', 'erfolgreich');
            }

            return $result;
        } catch (Exception $e) {
            $this->logEmailSent($userEmail, 'nutzer', 'fehler', $e->getMessage());
            Logger::logMail("Failed to send email to user: " . $e->getMessage(), 'error');
            return false;
        }
    }

    /**
     * Send agreement PDF to admin
     * @param string $pdfPath
     * @param array $userData
     * @return bool
     */
    public function sendAgreementToAdmin(string $pdfPath, array $userData): bool
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();

            // Recipient
            $this->mailer->addAddress($this->config['admin_email'], 'Admin');

            // BCC
            $this->mailer->addBCC('mlehmann@conrat.de', 'Martin Lehmann');

            // Subject and body
            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Neue DSGVO-Auftragsverarbeitungsvereinbarung - ' . $userData['firma'];
            $this->mailer->Body = $this->getAdminEmailTemplate($userData);
            $this->mailer->AltBody = $this->getAdminEmailTextTemplate($userData);

            // PDF attachment (nur wenn Datei existiert)
            if (!empty($pdfPath) && file_exists($pdfPath)) {
                $this->mailer->addAttachment($pdfPath, 'Auftragsverarbeitungsvereinbarung.pdf');
            } else {
                Logger::logMail("PDF-Datei nicht gefunden: " . $pdfPath, 'error');
            }

            $result = $this->mailer->send();

            if ($result) {
                $this->logEmailSent($this->config['admin_email'], 'admin', 'erfolgreich');
            }

            return $result;
        } catch (Exception $e) {
            $this->logEmailSent($this->config['admin_email'], 'admin', 'fehler', $e->getMessage());
            Logger::logMail("Failed to send email to admin: " . $e->getMessage(), 'error');
            return false;
        }
    }

    /**
     * Test email configuration
     * @return array
     */
    public function testConfiguration(): array
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($this->config['admin_email'], 'Test');
            $this->mailer->Subject = 'Test Email - DSGVO ADV System';
            $this->mailer->Body = 'This is a test email to verify email configuration.';
            $this->mailer->isHTML(false);

            $result = $this->mailer->send();

            return [
                'success' => $result,
                'message' => $result ? 'Email configuration working' : 'Email sending failed',
                'config' => [
                    'host' => $this->config['host'],
                    'port' => $this->config['port'],
                    'encryption' => $this->config['encryption']
                ]
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Email configuration error: ' . $e->getMessage(),
                'config' => $this->config
            ];
        }
    }

    /**
     * Get user email HTML template
     */
    private function getUserEmailTemplate(string $userName, string $companyName): string
    {
        return "
        <html>
        <head><title>DSGVO-Auftragsverarbeitungsvereinbarung</title></head>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
            <h2>Ihre DSGVO-Auftragsverarbeitungsvereinbarung</h2>
            <p>Sehr geehrte/r {$userName},</p>
            <p>anbei erhalten Sie Ihre DSGVO-konforme Auftragsverarbeitungsvereinbarung für {$companyName}.</p>
            <p>Bitte drucken Sie das Dokument aus, unterschreiben Sie es und senden Sie es an uns zurück.</p>
            <p>Bei Fragen stehen wir Ihnen gerne zur Verfügung.</p>
            <p>Mit freundlichen Grüßen<br>Ihr DSGVO ADV Team</p>
        </body>
        </html>";
    }

    /**
     * Get user email text template
     */
    private function getUserEmailTextTemplate(string $userName, string $companyName): string
    {
        return "Ihre DSGVO-Auftragsverarbeitungsvereinbarung\n\nSehr geehrte/r {$userName},\n\nanbei erhalten Sie Ihre DSGVO-konforme Auftragsverarbeitungsvereinbarung für {$companyName}.\n\nBitte drucken Sie das Dokument aus, unterschreiben Sie es und senden Sie es an uns zurück.\n\nBei Fragen stehen wir Ihnen gerne zur Verfügung.\n\nMit freundlichen Grüßen\nIhr DSGVO ADV Team";
    }

    /**
     * Get admin email HTML template
     */
    private function getAdminEmailTemplate(array $userData): string
    {
        return "
        <html>
        <head><title>Neue DSGVO-Auftragsverarbeitungsvereinbarung</title></head>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
            <h2>Neue DSGVO-Auftragsverarbeitungsvereinbarung</h2>
            <p>Eine neue Auftragsverarbeitungsvereinbarung wurde erstellt:</p>
            <ul>
                <li><strong>Firma:</strong> {$userData['firma']}</li>
                <li><strong>Ansprechpartner:</strong> {$userData['vorname']} {$userData['name']}</li>
                <li><strong>E-Mail:</strong> {$userData['email']}</li>
                <li><strong>Ort:</strong> {$userData['plz']} {$userData['ort']}</li>
                <li><strong>Erstellt am:</strong> " . date('d.m.Y H:i') . "</li>
            </ul>
            <p>Die Vereinbarung ist als PDF-Anhang beigefügt.</p>
        </body>
        </html>";
    }

    /**
     * Get admin email text template
     */
    private function getAdminEmailTextTemplate(array $userData): string
    {
        return "Neue DSGVO-Auftragsverarbeitungsvereinbarung\n\nEine neue Auftragsverarbeitungsvereinbarung wurde erstellt:\n\nFirma: {$userData['firma']}\nAnsprechpartner: {$userData['vorname']} {$userData['name']}\nE-Mail: {$userData['email']}\nOrt: {$userData['plz']} {$userData['ort']}\nErstellt am: " . date('d.m.Y H:i') . "\n\nDie Vereinbarung ist als PDF-Anhang beigefügt.";
    }

    /**
     * Log email sending to database
     */
    private function logEmailSent(string $email, string $type, string $status, string $errorMessage = null): void
    {
        try {
            $pdo = DatabaseConfig::getConnection();
            $stmt = $pdo->prepare("
                INSERT INTO email_logs (vereinbarung_id, empfaenger_email, empfaenger_typ, status, fehler_nachricht) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([null, $email, $type, $status, $errorMessage]);
        } catch (Exception $e) {
            Logger::logError("Failed to log email: " . $e->getMessage(), __FILE__, __LINE__);
        }
    }

    /**
     * Log email attempt
     * @param int $agreementId
     * @param string $email
     * @param string $type
     * @param string $status
     * @param string|null $errorMessage
     */
    private function logEmail(int $agreementId, string $email, string $type, string $status, ?string $errorMessage = null): void
    {
        try {
            $pdo = DatabaseConfig::getConnection();
            $stmt = $pdo->prepare("
                INSERT INTO email_logs (vereinbarung_id, empfaenger_email, empfaenger_typ, status, fehler_nachricht) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$agreementId, $email, $type, $status, $errorMessage]);
        } catch (Exception $e) {
            error_log("Failed to log email: " . $e->getMessage());
        }
    }

    /**
     * Send agreement emails to user and admin
     * @param array $userData
     * @return array
     */
    public function sendAgreementEmails(array $userData): array
    {
        try {
            $results = [];

            // Send email to user
            $pdfPath = $userData['pdfPath'] ?? $userData['pdf_path'] ?? '';
            $userResult = $this->sendUserEmail($userData['id'] ?? 0, $userData, $pdfPath);
            $results['user'] = $userResult;

            // Send email to admin
            $adminResult = $this->sendAdminEmail($userData['id'] ?? 0, $userData, $pdfPath);
            $results['admin'] = $adminResult;

            // Check if both emails were sent successfully
            $success = $userResult['success'] && $adminResult['success'];

            return [
                'success' => $success,
                'results' => $results,
                'error' => $success ? null : 'Some emails failed to send'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send email to user
     * @param int $agreementId
     * @param array $userData
     * @param string $pdfPath
     * @return array
     */
    private function sendUserEmail(int $agreementId, array $userData, string $pdfPath): array
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();

            $this->mailer->setFrom($this->config['from_address'], $this->config['from_name']);
            $this->mailer->addAddress($userData['email'], $userData['vorname'] . ' ' . $userData['name']);

            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Ihre DSGVO-Auftragsverarbeitungsvereinbarung';

            $body = $this->generateUserEmailBody($userData);
            $this->mailer->Body = $body;
            $this->mailer->AltBody = strip_tags($body);

            $result = $this->mailer->send();

            if ($result) {
                $this->logEmail($agreementId, $userData['email'], 'kunde', 'versendet');
                Logger::logMail("Email sent successfully to user: {$userData['email']} (Agreement ID: {$agreementId})", 'success');
                return ['success' => true];
            } else {
                $errorMsg = $this->mailer->ErrorInfo ?? 'Send failed';
                Logger::logMail("Email send failed (User): {$userData['email']} - {$errorMsg}", 'error');
                $this->logEmail($agreementId, $userData['email'], 'kunde', 'fehler', $errorMsg);
                return ['success' => false, 'error' => $errorMsg];
            }
        } catch (Exception $e) {
            $errorMsg = $e->getMessage();
            Logger::logMail("Email send exception (User): {$userData['email']} - {$errorMsg}", 'error');
            $this->logEmail($agreementId, $userData['email'], 'kunde', 'fehler', $errorMsg);
            return ['success' => false, 'error' => $errorMsg];
        }
    }

    /**
     * Send email to admin
     * @param int $agreementId
     * @param array $userData
     * @param string $pdfPath
     * @return array
     */
    private function sendAdminEmail(int $agreementId, array $userData, string $pdfPath): array
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();

            $this->mailer->setFrom($this->config['from_address'], $this->config['from_name']);
            $this->mailer->addAddress($this->config['admin_email'], 'Administrator');
            $this->mailer->addBCC('mlehmann@conrat.de', 'Martin Lehmann');

            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Neue DSGVO-Auftragsverarbeitungsvereinbarung - ' . $userData['firma'];

            $body = $this->generateAdminEmailBody($agreementId, $userData);
            $this->mailer->Body = $body;
            $this->mailer->AltBody = strip_tags($body);

            // Attach PDF
            $this->mailer->addAttachment($pdfPath, 'Auftragsverarbeitungsvereinbarung.pdf');

            $result = $this->mailer->send();

            if ($result) {
                $this->logEmail($agreementId, $this->config['admin_email'], 'admin', 'versendet');
                Logger::logMail("Email sent successfully to admin: {$this->config['admin_email']} (Agreement ID: {$agreementId})", 'success');
                return ['success' => true];
            } else {
                $errorMsg = $this->mailer->ErrorInfo ?? 'Send failed';
                Logger::logMail("Email send failed (Admin): {$this->config['admin_email']} - {$errorMsg}", 'error');
                $this->logEmail($agreementId, $this->config['admin_email'], 'admin', 'fehler', $errorMsg);
                return ['success' => false, 'error' => $errorMsg];
            }
        } catch (Exception $e) {
            $errorMsg = $e->getMessage();
            Logger::logMail("Email send exception (Admin): {$this->config['admin_email']} - {$errorMsg}", 'error');
            $this->logEmail($agreementId, $this->config['admin_email'], 'admin', 'fehler', $errorMsg);
            return ['success' => false, 'error' => $errorMsg];
        }
    }

    /**
     * Generate user email body
     * @param array $userData
     * @return string
     */
    private function generateUserEmailBody(array $userData): string
    {
        $config = EnvironmentConfig::loadConfig();
        $baseUrl = rtrim($config['base_url'] ?? '', '/');

        // Nutzereingaben escapen, bevor sie in den HTML-Mailbody eingebettet
        // werden - verhindert HTML-/Link-Injection ueber das Formular.
        $vorname = htmlspecialchars($userData['vorname'] ?? '', ENT_QUOTES, 'UTF-8');
        $name = htmlspecialchars($userData['name'] ?? '', ENT_QUOTES, 'UTF-8');
        $firma = htmlspecialchars($userData['firma'] ?? '', ENT_QUOTES, 'UTF-8');
        $ansprechpartner = htmlspecialchars($userData['ansprechpartner'] ?? '', ENT_QUOTES, 'UTF-8');
        $anschrift = htmlspecialchars($userData['anschrift'] ?? '', ENT_QUOTES, 'UTF-8');
        $plz = htmlspecialchars($userData['plz'] ?? '', ENT_QUOTES, 'UTF-8');
        $ort = htmlspecialchars($userData['ort'] ?? '', ENT_QUOTES, 'UTF-8');

        $downloadSection = '';
        if (!empty($userData['download_token'])) {
            $downloadUrl = $baseUrl . '/download_secure.php?token=' . urlencode($userData['download_token']);
            $downloadSection = "
        <p style='margin:1.5rem 0;'>
            <a href='{$downloadUrl}' style='background:#3498db;color:#fff;padding:12px 24px;border-radius:6px;text-decoration:none;font-weight:bold;display:inline-block;'>
                ⬇ Vereinbarung herunterladen
            </a>
        </p>
        <p style='font-size:0.85em;color:#666;'>Dieser Download-Link ist 24 Stunden gültig.</p>";
        }

        return "
        <h2>Ihre DSGVO-Auftragsverarbeitungsvereinbarung</h2>
        <p>Sehr geehrte/r {$vorname} {$name},</p>
        <p>vielen Dank für Ihre Anfrage. Ihre DSGVO-konforme Auftragsverarbeitungsvereinbarung wurde erfolgreich erstellt und ist als PDF-Anhang beigefügt.</p>
        {$downloadSection}
        <p><strong>Firmendaten:</strong><br>
        {$firma}<br>
        {$ansprechpartner}<br>
        {$anschrift}<br>
        {$plz} {$ort}</p>
        <p>Bei Fragen stehen wir Ihnen gerne zur Verfügung.</p>
        <p>Mit freundlichen Grüßen</p>
        <p>
            ConRat WebSolutions GmbH<br>
            Gartenstr. 4, 37281 Wanfried, Tel.: 05651 9529126<br>
            Geschäftsführer: Matthias Steube<br>
            Handelsregister: Amtsgericht Eschwege HRB 2809<br>
            USt.-ID-Nr.: DE 214 630 397
        </p>
        ";
    }

    /**
     * Generate admin email body
     * @param int $agreementId
     * @param array $userData
     * @return string
     */
    private function generateAdminEmailBody(int $agreementId, array $userData): string
    {
        $firma = htmlspecialchars($userData['firma'] ?? '', ENT_QUOTES, 'UTF-8');
        $ansprechpartner = htmlspecialchars($userData['ansprechpartner'] ?? '', ENT_QUOTES, 'UTF-8');
        $email = htmlspecialchars($userData['email'] ?? '', ENT_QUOTES, 'UTF-8');
        $anschrift = htmlspecialchars($userData['anschrift'] ?? '', ENT_QUOTES, 'UTF-8');
        $plz = htmlspecialchars($userData['plz'] ?? '', ENT_QUOTES, 'UTF-8');
        $ort = htmlspecialchars($userData['ort'] ?? '', ENT_QUOTES, 'UTF-8');

        return "
        <h2>Neue DSGVO-Auftragsverarbeitungsvereinbarung</h2>
        <p>Eine neue Auftragsverarbeitungsvereinbarung wurde erstellt:</p>
        <p><strong>Vereinbarungs-ID:</strong> {$agreementId}</p>
        <p><strong>Firmendaten:</strong><br>
        Firma: {$firma}<br>
        Ansprechpartner: {$ansprechpartner}<br>
        E-Mail: {$email}<br>
        Adresse: {$anschrift}, {$plz} {$ort}</p>
        <p>Die Vereinbarung ist als PDF-Anhang beigefügt.</p>
        ";
    }
}
