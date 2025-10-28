<?php
/**
 * DSGVO Auftragsverarbeitungsvereinbarung Template
 * Template für die Generierung der DSGVO-konformen Auftragsverarbeitungsvereinbarung
 */

class AgreementTemplate {
    
    /**
     * Generate the complete DSGVO agreement text
     * @param array $data User data for personalization
     * @return string Complete agreement text
     */
    public static function generateAgreementText($data) {
        $companyName = htmlspecialchars($data['firma'], ENT_QUOTES, 'UTF-8');
        $contactPerson = htmlspecialchars($data['ansprechpartner'], ENT_QUOTES, 'UTF-8');
        $address = htmlspecialchars($data['anschrift'], ENT_QUOTES, 'UTF-8');
        $postalCode = htmlspecialchars($data['plz'], ENT_QUOTES, 'UTF-8');
        $city = htmlspecialchars($data['ort'], ENT_QUOTES, 'UTF-8');
        $email = htmlspecialchars($data['email'], ENT_QUOTES, 'UTF-8');
        $currentDate = date('d.m.Y');
        $currentTime = date('H:i:s');
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        return "
 

<p><strong>zwischen</strong></p>

<p><strong>{$companyName}</strong><br>
{$contactPerson}<br>
{$address}<br>
{$postalCode} {$city}<br> 
{$email}<br>
– nachfolgend \"Auftraggeber\" genannt –</p>

<p><strong>und</strong></p>

<p><strong>ConRat WebSolutions GmbH</strong><br>
Gartenstr. 4<br>
37281 Wanfried<br>
– nachfolgend \"Auftragnehmer\" genannt –</p>

<p>gemeinsam auch \"die Parteien\" genannt.<br /></p>

<h3>1. Gegenstand und Dauer des Auftrags</h3>

<p><strong>1.1</strong> Gegenstand dieser Vereinbarung ist die Verarbeitung personenbezogener Daten im Auftrag gemäß Art. 28 DSGVO. Der Auftragnehmer erbringt für den Auftraggeber Dienstleistungen im Bereich [… z. B. Webhosting, E-Mail-Dienste, Domainverwaltung, Serverbetrieb, IT-Support …].</p>

<p><strong>1.2</strong> Die Verarbeitung personenbezogener Daten durch den Auftragnehmer erfolgt ausschließlich im Rahmen und zu den Zwecken der Erfüllung der vertraglich vereinbarten Leistungen.</p>

<p><strong>1.3</strong> Die Laufzeit dieser Vereinbarung entspricht der Laufzeit des Hauptvertrags über die zugrundeliegende Leistungserbringung. Sie endet automatisch mit Beendigung des Hauptvertrags, soweit keine weitergehenden Verpflichtungen nach dieser Vereinbarung bestehen.</p>

<h3>2. Art und Zweck der Verarbeitung, Art der Daten, Kategorien betroffener Personen</h3>

<p><strong>2.1 Art der Verarbeitung:</strong><br>
Erhebung, Speicherung, Organisation, Aufbewahrung, Anpassung, Auswertung, Auslesung, Nutzung, Übermittlung, Einschränkung, Löschung oder Vernichtung personenbezogener Daten – je nach Art der beauftragten Leistung.</p>

<p><strong>2.2 Zweck der Verarbeitung:</strong><br>
Erfüllung der im Hauptvertrag vereinbarten Dienstleistungen, insbesondere Bereitstellung, Betrieb und Wartung von IT-, Hosting-, Kommunikations- oder sonstigen Systemen.</p>

<p><strong>2.3 Art der personenbezogenen Daten:</strong><br>
Personenstammdaten, Kommunikationsdaten (z. B. E-Mail, Telefon), Vertragsdaten, Nutzungsdaten, Abrechnungs- und Zahlungsdaten sowie sonstige im Rahmen der Leistungserbringung verarbeitete personenbezogene Daten.</p>

<p><strong>2.4 Kategorien betroffener Personen:</strong><br>
Kunden, Interessenten, Beschäftigte, Lieferanten, Kontaktpersonen und sonstige durch die Verarbeitung betroffene natürliche Personen.</p>

<h3>3. Rechte und Pflichten des Auftraggebers</h3>

<p><strong>3.1</strong> Der Auftraggeber ist Verantwortlicher im Sinne des Art. 4 Nr. 7 DSGVO. Er bleibt hinsichtlich der Rechtmäßigkeit der Datenverarbeitung, der Wahrung der Betroffenenrechte und der Erfüllung seiner Pflichten nach der DSGVO verantwortlich.</p>

<p><strong>3.2</strong> Der Auftraggeber hat den Auftragnehmer unverzüglich zu informieren, wenn er Fehler oder Unregelmäßigkeiten bei der Verarbeitung personenbezogener Daten feststellt.</p>

<p><strong>3.3</strong> Der Auftraggeber ist berechtigt, Weisungen zur Art, zum Umfang und zu den Verfahren der Verarbeitung zu erteilen. Weisungen sind grundsätzlich schriftlich oder in einem elektronischen Format (z. B. E-Mail) zu erteilen.</p>

<h3>4. Pflichten des Auftragnehmers</h3>

<p><strong>4.1</strong> Der Auftragnehmer verarbeitet personenbezogene Daten ausschließlich auf dokumentierte Weisung des Auftraggebers, sofern er nicht nach dem Recht der Union oder der Mitgliedstaaten zu einer Verarbeitung verpflichtet ist. In einem solchen Fall informiert der Auftragnehmer den Auftraggeber vor der Verarbeitung, sofern das betreffende Recht eine solche Information nicht verbietet.</p>

<p><strong>4.2</strong> Der Auftragnehmer stellt sicher, dass sich die zur Verarbeitung befugten Personen zur Vertraulichkeit verpflichtet haben oder einer gesetzlichen Verschwiegenheitspflicht unterliegen (Art. 28 Abs. 3 lit. b DSGVO).</p>

<p><strong>4.3</strong> Der Auftragnehmer ergreift alle gemäß Art. 32 DSGVO erforderlichen technischen und organisatorischen Maßnahmen (TOM), um ein dem Risiko angemessenes Schutzniveau zu gewährleisten. Eine aktuelle Übersicht der TOM wird dem Auftraggeber auf Anfrage zur Verfügung gestellt.</p>

<p><strong>4.4</strong> Der Auftragnehmer unterstützt den Auftraggeber nach Art. 28 Abs. 3 lit. e DSGVO bei der Wahrung der Betroffenenrechte gemäß Kapitel III DSGVO sowie bei der Einhaltung der Pflichten gemäß Art. 32 bis 36 DSGVO (z. B. Meldung von Datenschutzverletzungen, Datenschutz-Folgenabschätzung).</p>

<p><strong>4.5</strong> Der Auftragnehmer informiert den Auftraggeber unverzüglich, wenn er der Ansicht ist, dass eine Weisung des Auftraggebers gegen Datenschutzvorschriften verstößt. Die Ausführung der Weisung wird bis zur Bestätigung oder Änderung durch den Auftraggeber ausgesetzt.</p>

<h3>5. Technische und organisatorische Maßnahmen (Art. 32 DSGVO)</h3>

<p><strong>5.1</strong> Der Auftragnehmer dokumentiert und implementiert geeignete technische und organisatorische Maßnahmen, um den Schutz der personenbezogenen Daten sicherzustellen.</p>

<p><strong>5.2</strong> Diese Maßnahmen unterliegen dem technischen Fortschritt und werden fortlaufend angepasst. Änderungen sind zulässig, sofern das vereinbarte Sicherheitsniveau nicht unterschritten wird.</p>

<h3>6. Unterauftragsverhältnisse (Subunternehmer)</h3>

<p><strong>6.1</strong> Der Auftraggeber stimmt zu, dass der Auftragnehmer zur Erfüllung seiner vertraglichen Verpflichtungen Unterauftragnehmer einsetzen darf.</p>

<p><strong>6.2</strong> Der Auftragnehmer informiert den Auftraggeber über beabsichtigte Änderungen hinsichtlich der Hinzuziehung oder Ersetzung von Unterauftragnehmern. Der Auftraggeber kann binnen 14 Tagen nach Mitteilung aus wichtigem Grund widersprechen.</p>

<p><strong>6.3</strong> Der Auftragnehmer hat mit den Unterauftragnehmern Verträge zu schließen, die den Anforderungen des Art. 28 DSGVO entsprechen und dieselben Datenschutzpflichten auferlegen wie diese Vereinbarung.</p>

<p><strong>6.4</strong> Eine Liste der jeweils aktuell eingesetzten Unterauftragnehmer ist auf Anfrage oder über [Link / Anhang] abrufbar.</p>

<h3>7. Unterstützung bei der Wahrung der Betroffenenrechte</h3>

<p>Der Auftragnehmer unterstützt den Auftraggeber im Rahmen seiner Möglichkeiten bei der Erfüllung der Rechte betroffener Personen nach Kapitel III DSGVO, insbesondere hinsichtlich Auskunft, Berichtigung, Löschung, Einschränkung der Verarbeitung und Datenübertragbarkeit.</p>

<h3>8. Kontrolle und Nachweise</h3>

<p><strong>8.1</strong> Der Auftraggeber ist berechtigt, die Einhaltung der in dieser Vereinbarung getroffenen Datenschutzpflichten regelmäßig zu überprüfen, auch durch Stichprobenkontrollen oder durch Beauftragung externer Prüfer.</p>

<p><strong>8.2</strong> Der Auftragnehmer stellt dem Auftraggeber auf Anfrage alle erforderlichen Informationen zum Nachweis der Einhaltung der Pflichten nach Art. 28 DSGVO zur Verfügung.</p>

<p><strong>8.3</strong> Kontrollen sind rechtzeitig anzukündigen (mindestens 3 Wochen vorher) und während der üblichen Geschäftszeiten ohne Störung des Betriebsablaufs durchzuführen.</p>

<h3>9. Löschung und Rückgabe von Daten</h3>

<p><strong>9.1</strong> Nach Abschluss der vereinbarten Leistungen oder auf Anweisung des Auftraggebers hat der Auftragnehmer alle personenbezogenen Daten, einschließlich etwaiger Kopien, datenschutzgerecht zu löschen oder an den Auftraggeber zurückzugeben, sofern keine gesetzliche Aufbewahrungspflicht entgegensteht.</p>

<p><strong>9.2</strong> Dokumentationen, die dem Nachweis der ordnungsgemäßen Verarbeitung dienen, sind entsprechend gesetzlicher Aufbewahrungspflichten zu sichern.</p>

<h3>10. Informationspflichten bei Verstößen</h3>

<p>Der Auftragnehmer informiert den Auftraggeber unverzüglich über Verletzungen des Schutzes personenbezogener Daten (Art. 33 DSGVO). Die Mitteilung hat alle Informationen zu enthalten, die erforderlich sind, damit der Auftraggeber seine Meldepflichten erfüllen kann.</p>

<h3>11. Gegenseitige Unterstützung (Art. 82 DSGVO)</h3>

<p>Im Falle von Ansprüchen betroffener Personen oder Dritter nach Art. 82 DSGVO verpflichten sich die Parteien, sich gegenseitig zu unterstützen und zur Aufklärung des zugrundeliegenden Sachverhalts beizutragen. Jede Partei informiert die andere unverzüglich über ihr bekannt gewordene Haftungsansprüche im Zusammenhang mit der Auftragsverarbeitung.</p>

<h3>12. Schriftform und Änderungen</h3>

<p>Änderungen und Ergänzungen dieser Vereinbarung einschließlich etwaiger Nebenabreden bedürfen der Schriftform oder einer gleichwertigen elektronischen Form. Dies gilt auch für einen Verzicht auf dieses Formerfordernis.</p>

<h3>13. Salvatorische Klausel, Vertragsbestandteile, Gerichtsstand</h3>

<p><strong>13.1</strong> Sollten einzelne Bestimmungen dieser Vereinbarung unwirksam oder undurchführbar sein oder werden, bleibt die Wirksamkeit der übrigen Bestimmungen unberührt. Die Parteien verpflichten sich, eine der unwirksamen Bestimmung möglichst nahekommende Regelung zu vereinbaren.</p>

<p><strong>13.2</strong> Sämtliche Anlagen zu dieser Vereinbarung – insbesondere die \"Technischen und organisatorischen Maßnahmen (TOM)\" sowie etwaige Listen der Unterauftragnehmer - sind Bestandteil dieses Vertrags.</p>

<p><strong>13.3</strong> Gerichtsstand für alle Streitigkeiten aus und im Zusammenhang mit dieser Vereinbarung ist, soweit gesetzlich zulässig, Eschwege (Deutschland).<br /><br /></p>

 
<p><br /><br /><strong>Anlage 1 – Technische und organisatorischen Maßnahmen nach Art. 32 DSGVO</strong><br />
<a href=\"https://www.some-solutions.de/dsgvo-tom\">https://www.some-solutions.de/dsgvo-tom</a></p>
<p><strong>Anlage 2 – Weitere Auftragsverarbeiter nach Art. 28 Abs. 2 DSGVO</strong><br />
<a href=\"https://www.some-solutions.de/dsgvo-verarbeiter\">https://www.some-solutions.de/dsgvo-verarbeiter</a></p>
 
<p><em>Diese Vereinbarung wurde am {$currentDate} um {$currentTime} mit der IP-Adresse: {$ipAddress} erstellt und ist sofort gültig. Der Vertrag wurde elektronisch geschlossen und ist ohne Unterschrift gültig.</em></p>

<p><em>Die Vereinbarung entspricht den Anforderungen der Datenschutz-Grundverordnung (DSGVO) und des Bundesdatenschutzgesetzes (BDSG).</em></p>
";
    }
    
    /**
     * Get agreement metadata
     * @param array $data User data
     * @return array Metadata for the agreement
     */
    public static function getAgreementMetadata($data) {
        return [
            'title' => 'Auftragsverarbeitungsvereinbarung nach Art. 28 DSGVO',
            'parties' => [
                'contractor' => [
                    'name' => 'Conrat GmbH',
                    'address' => 'Musterstraße 123, 12345 Musterstadt',
                    'email' => 'mlehmann@conrat.de'
                ],
                'processor' => [
                    'name' => $data['firma'],
                    'address' => $data['anschrift'] . ', ' . $data['plz'] . ' ' . $data['ort'],
                    'email' => $data['email'],
                    'contact_person' => $data['ansprechpartner']
                ]
            ],
            'created_date' => date('Y-m-d'),
            'valid_from' => date('Y-m-d'),
            'version' => '1.0',
            'legal_basis' => 'Art. 28 DSGVO',
            'purpose' => 'Auftragsverarbeitung personenbezogener Daten',
            'data_categories' => [
                'Bestandsdaten',
                'Vertragsdaten', 
                'Kommunikationsdaten',
                'Technische Daten'
            ],
            'affected_persons' => [
                'Kunden',
                'Geschäftspartner',
                'Mitarbeiter',
                'Interessenten'
            ]
        ];
    }
    
    /**
     * Validate agreement data
     * @param array $data User data
     * @return array Validation result
     */
    public static function validateAgreementData($data) {
        $errors = [];
        
        // Required fields
        $requiredFields = ['firma', 'anschrift', 'plz', 'ort', 'email', 'ansprechpartner'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $errors[] = "Feld '{$field}' ist erforderlich.";
            }
        }
        
        // Email validation
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Ungültige E-Mail-Adresse.';
        }
        
        // Postal code validation
        if (!empty($data['plz']) && !preg_match('/^[0-9]{5}$/', $data['plz'])) {
            $errors[] = 'Ungültige Postleitzahl.';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
}