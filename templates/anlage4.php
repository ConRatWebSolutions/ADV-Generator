<?php

/**
 * Anlage 4 - Technische und organisatorische Maßnahmen nach Art. 32 DSGVO
 */

class Anlage4
{
    public static function getContent(): string
    {
        $currentDate = date('d.m.Y');
        return '<p>
    <strong>Stand: ' . $currentDate . '</strong>
</p>
<p>
    Der Auftragnehmer setzt folgende technische und organisatorische Maßnahmen zum Schutz der vertragsgegenständlichen personenbezogenen Daten um. Die Maßnahmen wurden im Einklang mit Art. 32 DSGVO festgelegt.
</p>
<p>
    <strong>Hinweis:</strong> Diese Maßnahmen unterliegen dem technischen Fortschritt und werden fortlaufend angepasst. Änderungen sind zulässig, sofern das vereinbarte Sicherheitsniveau nicht unterschritten wird. Der Auftraggeber wird über wesentliche Änderungen informiert.
</p>
<p>
    1. Pseudonymisierung
</p>
<p>
    Die vom Kunden in der Software eingegebenen sowie die vom System erhobenen Daten werden mit einer User-ID gespeichert. Diese ist ein Pseudonym für den jeweiligen Nutzer und lässt sich über eine Tabelle mit den eingetragenen Daten (mindestens E-Mail-Adresse) verknüpfen.
</p>
<p>
    2. Verschlüsselung
</p>
<p>
    Datenträger in den Geschäftsräumen mit personenbezogenen Daten werden entsprechend dem Stand der Technik verschlüsselt. Der Zugang zu Server-Systemen sowie die Datenübertragung zwischen einzelnen Servern erfolgt über verschlüsselte Verbindungen. Die Software ist durch den Kunden ausschließlich über verschlüsselte Internetverbindungen (https) nutzbar.&nbsp;
    Zugangsdaten, die der Kunde für verbundene Postfächer und Kalender hinterlegt (OAuth-Token sowie Benutzername und Passwort bei IMAP), werden ausschließlich verschlüsselt gespeichert (AES-256-GCM) und nie im Klartext abgelegt.
</p>
<p>
    3. Gewährleistung der Vertraulichkeit Zutrittskontrolle
</p>
<p>
    Einsatz einer Schliessanlage mit Zutrittsberechtigung. Besucher dürfen die Geschäftsräume nur in Begleitung berechtigter Mitarbeiter betreten. Die Zutrittskontrolle zum Rechenzentrum erfolgt auf Basis der technischen und organisatorischen Maßnahmen des Rechenzentrumsbetreibers DomainFactory GmbH sowie der IONOS SE.
</p>
<p>
    4. Zugangskontrolle
</p>
<p>
    Die Anmeldung an IT-Systemen erfolgt über mindestens 10-stellige Kennwörter mit Sonderzeichen, Ziffer und/oder Klein- /Großbuchstaben. Sofern möglich sind die Login-Daten personenbezogen und nur dem jeweiligen Mitarbeiter bekannt. Die IT-Systeme sind durch eine Firewall gesichert. Für Remote-Zugriffe werden personenbezogene VPN-Zugänge genutzt.
</p>
<p>
    5. Zugriffskontrolle
</p>
<p>
    Für die Zugriffskontrolle sind differenzierte Berechtigungen nach dem Rollenkonzept eingerichtet. Die Freigabe von Daten erfolgt nur an berechtigte Personen. Zugewiesene Berechtigungen werden durch die Administratoren regelmäßig überprüft und bei Entfall der Notwendigkeit entzogen.
</p>
<p>
    6. Trennungskontrolle
</p>
<p>
    Soweit die betrieblichen Abläufe eine getrennte Verarbeitung und Auswertung von Daten ermöglichen wird diese entsprechend eingerichtet. Produktiv- und Testsysteme nutzen generell getrennte Datenbanken. Für Kundendaten erfolgt eine logische Trennung auf Datenbankebene. Der Zugriff auf Produktivsysteme wird soweit wie möglich eingeschränkt.
</p>
<p>
    7. Gewährleistung der Integrität Weitergabekontrolle
</p>
<p>
    Zur Übertragung von Daten werden verschlüsselte Verbindungen entsprechend dem Stand der Technik eingesetzt. Die Remote-Einwahl in das interne Netzwerk erfolgt über VPN-Verbindungen.&nbsp;
</p>
<p>
    8. Eingabekontrolle
</p>
<p>
    Eingaben, Änderungen und Löschung von Produktivdaten sind nur durch Administratoren möglich.&nbsp;
</p>
<p>
    9. Gewährleistung der Verfügbarkeit
</p>
<p>
    Von Server-Systemen werden täglich Backups durchgeführt. Sicherungen werden zusätzlich räumlich getrennt an einem anderen Standort gespeichert. Auf allen Client-Rechnern ist Antiviren-Software installiert und wird fortlaufend aktualisiert. Die Gewährleistung der Verfügbarkeit auf Ebene des Rechenzentrums erfolgt darüber hinaus auf Basis der technischen und organisatorischen Maßnahmen des Rechenzentrumsbetreibers DomainFactory GmbH sowie der&nbsp;IONOS SE.
</p>
<p>
    10. Gewährleistung der Belastbarkeit der Systeme
</p>
<p>
    Um die Belastbarkeit der Systeme zu gewährleisten, setzen wir auf eine skalierbare Serverinfrastruktur und ein Monitoring um Trends und Lastspitzen zu erkennen und rechtzeitig darauf zu reagieren.&nbsp;Die Administratoren sind in der Lage, die unter 9. genannten Sicherungen zeitnah einzuspielen. Das Szenario wird in periodischen Abständen getestet.
</p>
<p>
    11. Verfahren regelmäßiger Überprüfung, Bewertung und Evaluierung der Wirksamkeit der technischen und organisatorischen Maßnahmen
</p>
<p>
    Unsere Mitarbeiter werden in regelmäßigen Abständen im Datenschutzrecht unterwiesen und sie sind vertraut mit den Verfahrensanweisungen und Benutzerrichtlinien für die Datenverarbeitung im Auftrag. Sie sind in einer gesonderten Vereinbarung dem Datengeheimnis verpflichtet.&nbsp;
</p>
<p>
    Eine Überprüfung der Wirksamkeit der Technischen Schutzmaßnamen wird mindestens jährlich durchgeführt. Dabei erfolgt auch eine Beurteilung der Angemessenheit des Schutzniveaus und gegebenenfalls eine Anpassung auf den aktuellen Stand der Technik, beispielsweise eine Umstellung auf neuere Verschlüsselungsverfahren.
</p>';
    }
}
