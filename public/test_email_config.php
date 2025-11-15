<?php

/**
 * Test Email Configuration
 * Debug script to test email configuration on server
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: text/html; charset=utf-8');

echo "<h1>E-Mail-Konfiguration Test</h1>";

try {
    require_once __DIR__ . '/../config/environment.php';
    require_once __DIR__ . '/../config/email_config.php';

    // Load configuration
    $envConfig = EnvironmentConfig::loadConfig();
    $emailConfig = new EmailConfig();

    echo "<h2>Konfiguration:</h2>";
    echo "<pre>";
    echo "Environment: " . ($envConfig['environment'] ?? 'unknown') . "\n";
    echo "Mail Host: " . ($envConfig['mail_host'] ?? 'not set') . "\n";
    echo "Mail Port: " . ($envConfig['mail_port'] ?? 'not set') . "\n";
    echo "Mail Username: " . ($envConfig['mail_username'] ?? 'not set') . "\n";
    echo "Mail Encryption: " . ($envConfig['mail_encryption'] ?? 'not set') . "\n";
    echo "From Address: " . ($envConfig['mail_from_address'] ?? 'not set') . "\n";
    echo "From Name: " . ($envConfig['mail_from_name'] ?? 'not set') . "\n";
    echo "Admin Email: " . ($envConfig['admin_email'] ?? 'not set') . "\n";
    echo "</pre>";

    // Test PHPMailer connection
    echo "<h2>PHPMailer Test:</h2>";
    $mailer = $emailConfig->getMailer();

    echo "<pre>";
    echo "Host: " . $mailer->Host . "\n";
    echo "Port: " . $mailer->Port . "\n";
    echo "SMTPAuth: " . ($mailer->SMTPAuth ? 'true' : 'false') . "\n";
    echo "SMTPSecure: " . ($mailer->SMTPSecure ?: 'none') . "\n";
    echo "From: " . $mailer->From . "\n";
    echo "FromName: " . $mailer->FromName . "\n";
    echo "</pre>";

    // Test sending a simple email
    echo "<h2>E-Mail-Versendung Test:</h2>";
    echo "<p>Versuche Test-E-Mail zu senden...</p>";

    $testEmail =  'mlehmann@conrat.de';

    $mailer->clearAddresses();
    $mailer->clearAttachments();
    $mailer->addAddress($testEmail, 'Test Empfänger');
    $mailer->Subject = 'Test E-Mail von ADV-System';
    $mailer->Body = 'Dies ist eine Test-E-Mail vom ADV-System.';
    $mailer->AltBody = 'Dies ist eine Test-E-Mail vom ADV-System.';

    if ($mailer->send()) {
        echo "<p style='color: green;'>✅ Test-E-Mail erfolgreich gesendet an: {$testEmail}</p>";
    } else {
        echo "<p style='color: red;'>❌ Fehler beim Senden: " . htmlspecialchars($mailer->ErrorInfo) . "</p>";
    }

    // Check email logs
    echo "<h2>E-Mail-Logs (letzte 10 Einträge):</h2>";
    try {
        require_once __DIR__ . '/../config/database.php';
        $pdo = DatabaseConfig::getConnection();
        // Verwende versendet_am statt erstellt_am (korrekte Spalte laut Schema)
        $stmt = $pdo->query("SELECT * FROM email_logs ORDER BY versendet_am DESC LIMIT 10");
        $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($logs)) {
            echo "<p>Keine E-Mail-Logs gefunden.</p>";
        } else {
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>ID</th><th>Vereinbarung ID</th><th>Empfänger</th><th>Typ</th><th>Status</th><th>Fehler</th><th>Versendet am</th></tr>";
            foreach ($logs as $log) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($log['id'] ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($log['vereinbarung_id'] ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($log['empfaenger_email'] ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($log['empfaenger_typ'] ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($log['status'] ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($log['fehler_nachricht'] ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($log['versendet_am'] ?? '') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>Fehler beim Laden der E-Mail-Logs: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Fehler: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

echo "<hr>";
echo "<p><small>Test-URL mit E-Mail-Parameter: ?test_email=ihre@email.de</small></p>";
