<?php

/**
 * DSGVO Auftragsverarbeitungsvereinbarung Template
 * Template für die Generierung der DSGVO-konformen Auftragsverarbeitungsvereinbarung
 */

class AgreementTemplate
{

    /**
     * Generate the complete DSGVO agreement text
     * @param array $data User data for personalization
     * @return string Complete agreement text
     */
    public static function generateAgreementText($data)
    {
        $companyName = htmlspecialchars($data['firma'], ENT_QUOTES, 'UTF-8');
        $contactPerson = htmlspecialchars($data['ansprechpartner'], ENT_QUOTES, 'UTF-8');
        $address = htmlspecialchars($data['anschrift'], ENT_QUOTES, 'UTF-8');
        $postalCode = htmlspecialchars($data['plz'], ENT_QUOTES, 'UTF-8');
        $city = htmlspecialchars($data['ort'], ENT_QUOTES, 'UTF-8');
        $email = htmlspecialchars($data['email'], ENT_QUOTES, 'UTF-8');
        $currentDate = date('d.m.Y');
        $currentTime = date('H:i:s');
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        return "
<p><strong>zwischen</strong></p>

<p><strong>{$companyName}</strong><br>
{$contactPerson}<br>
{$address}<br>
{$postalCode} {$city}<br>
{$email}<br>
– nachfolgend \"Auftraggeber\" genannt –</p>

<p><strong>und</strong></p>

<p><strong>Auftragnehmer</strong><br>
– nachfolgend \"Auftragnehmer\" genannt –</p>

<p>gemeinsam auch \"die Parteien\" genannt.<br /></p>

<p><strong>Präambel</strong></p>
<p>Diese Anlage konkretisiert die Verpflichtungen der Vertragsparteien zum Datenschutz, die sich aus der im Hauptvertrag in ihren Einzelheiten beschriebenen Auftragsverarbeitung ergeben. Sie findet Anwendung auf alle Tätigkeiten, die mit dem Vertrag in Zusammenhang stehen und bei denen Beschäftigte des Auftragnehmers oder durch Beauftragte personenbezogene Daten des Auftraggebers verarbeiten.</p>

<p><strong>§ 1 Gegenstand, Dauer und Spezifizierung der Auftragsverarbeitung</strong></p>
<p>Im Einzelnen sind die Angaben in <strong>&nbsp;Anlage 1&nbsp; </strong> Bestandteil der Datenverarbeitung.</p>
<p>Die Laufzeit dieser Anlage richtet sich nach der Laufzeit des Vertrages, sofern sich aus den Bestimmungen dieser Anlage nicht darüber hinausgehende Verpflichtungen ergeben.</p>

<p><strong>§ 2 Anwendungsbereich und Verantwortlichkeit</strong></p>
<ol>
<li>Der Auftragnehmer verarbeitet personenbezogene Daten im Auftrag des Auftraggebers. Dies umfasst Tätigkeiten, die im Vertrag und in der Leistungsbeschreibung konkretisiert sind. Der Auftraggeber ist im Rahmen dieses Vertrages für die Einhaltung der gesetzlichen Bestimmungen der Datenschutzgesetze, insbesondere für die Rechtmäßigkeit der Datenweitergabe an den Auftragnehmer sowie für die Rechtmäßigkeit der Datenverarbeitung allein verantwortlich (»Verantwortlicher« im Sinne des Art. 4 Nr. 7 DSGVO).</li>
<li>Die Weisungen werden anfänglich durch diesen Vertrag festgelegt und können vom Auftraggeber danach in schriftlicher Form oder in einem elektronischen Format (Textform) an die vom Auftragnehmer bezeichnete Stelle durch einzelne Weisungen geändert, ergänzt oder ersetzt werden (Einzelweisung). Weisungen, die im Vertrag nicht vorgesehen sind, werden als Antrag auf Leistungsänderung behandelt. Mündliche Weisungen sind unverzüglich schriftlich oder in Textform zu bestätigen.</li>
</ol>

<p><strong>§ 3 Pflichten des Auftragnehmers</strong></p>
<ol>
<li>Der Auftragnehmer darf Daten von betroffenen Personen nur im Rahmen des Auftrages und der Weisungen des Auftraggebers verarbeiten, außer es liegt ein Ausnahmefall im Sinne des Artikel 28 Abs. 3 a) DSGVO vor. Der Auftragnehmer informiert den Auftraggeber unverzüglich, wenn er der Auffassung ist, dass eine Weisung gegen anwendbare Gesetze verstößt. Der Auftragnehmer darf die Umsetzung der Weisung solange aussetzen, bis sie vom Auftraggeber bestätigt oder abgeändert wurde.</li>
<li>Der Auftragnehmer wird in seinem Verantwortungsbereich die innerbetriebliche Organisation so gestalten, dass sie den besonderen Anforderungen des Datenschutzes gerecht wird. Er wird technische und organisatorische Maßnahmen gemäß <strong>&nbsp;Anlage 4&nbsp; </strong> zum angemessenen Schutz der Daten des Auftraggebers treffen, die den Anforderungen der Datenschutz-Grundverordnung (Art. 32 DSGVO) genügen. Der Auftragnehmer hat technische und organisatorische Maßnahmen zu treffen, die die Vertraulichkeit, Integrität, Verfügbarkeit und Belastbarkeit der Systeme und Dienste im Zusammenhang mit der Verarbeitung auf Dauer sicherstellen. Dem Auftraggeber sind diese technischen und organisatorischen Maßnahmen bekannt und er trägt die Verantwortung dafür, dass diese für die Risiken der zu verarbeitenden Daten ein angemessenes Schutzniveau bieten. Eine Änderung der getroffenen Sicherheitsmaßnahmen bleibt dem Auftragnehmer vorbehalten, wobei jedoch sichergestellt sein muss, dass das vertraglich vereinbarte Schutzniveau nicht unterschritten wird.</li>
<li>Der Auftragnehmer unterstützt soweit vereinbart den Auftraggeber im Rahmen seiner Möglichkeiten bei der Erfüllung der Anfragen und Ansprüche betroffenen Personen gem. Kapitel III der DSGVO sowie bei der Einhaltung der in Art. 33 bis 36 DSGVO genannten Pflichten.</li>
<li>Der Auftragnehmer gewährleistet, dass es den mit der Verarbeitung der Daten des Auftraggebers befassten Mitarbeiter und andere für den Auftragnehmer tätigen Personen untersagt ist, die Daten außerhalb der Weisung zu verarbeiten. Ferner gewährleistet der Auftragnehmer, dass sich die zur Verarbeitung der personenbezogenen Daten befugten Personen zur Vertraulichkeit verpflichtet haben oder einer angemessenen gesetzlichen Verschwiegenheitspflicht unterliegen. Die Vertraulichkeits-/ Verschwiegenheitspflicht besteht auch nach Beendigung des Auftrages fort.</li>
<li>Der Auftragnehmer unterrichtet den Auftraggeber über Verletzungen des Schutzes personenbezogener Daten des Auftraggebers spätestens innerhalb von 12 Stunden nach Kenntnisnahme. Die Meldung erfolgt über den definierten Kontaktweg: E-Mail an {$email} mit dem Betreff \"DSGVO-Verletzung - Dringend\" oder telefonisch unter der im Vertrag angegebenen Notfallnummer. Die Meldung muss mindestens folgende Informationen enthalten:
<ul>
<li><strong>Art der Verletzung:</strong> Beschreibung der Art der Verletzung des Schutzes personenbezogener Daten (z.B. unbefugter Zugriff, Datenverlust, unberechtigte Weitergabe, Verlust der Vertraulichkeit, Integrität oder Verfügbarkeit),</li>
<li><strong>Umfang der Verletzung:</strong> Angaben zum Umfang der betroffenen Daten (Anzahl der Datensätze, Kategorien der betroffenen Daten, Zeitraum der Verletzung),</li>
<li><strong>Betroffenheit:</strong> Beschreibung der betroffenen Personen (Kategorien und ungefähre Anzahl der betroffenen Personen, soweit möglich auch konkrete Angaben zu betroffenen Personen),</li>
<li><strong>Ergriffene Maßnahmen:</strong> Beschreibung der bereits ergriffenen oder geplanten Maßnahmen zur Behebung der Verletzung und zur Minderung möglicher nachteiliger Folgen für die betroffenen Personen.</li>
</ul>
<br><br>
Der Auftragnehmer trifft die erforderlichen Maßnahmen zur Sicherung der Daten und zur Minderung möglicher nachteiliger Folgen der betroffenen Personen und spricht sich hierzu unverzüglich mit dem Auftraggeber ab. Die Meldung hat alle Informationen zu enthalten, die erforderlich sind, damit der Auftraggeber seine Meldepflichten nach Art. 33 DSGVO (innerhalb von 72 Stunden nach Kenntnisnahme) erfüllen kann.</li>
<li>Der Auftragnehmer hat zur Klärung von Datenschutzfragen und zur Einhaltung der gesetzlichen Aufgaben nach der DS-GVO, dem BDSG (neu) und dem Hessischen Landesdatenschutzgesetz einen externen Datenschutzbeauftragten bestellt.</li>
<li>Der Auftragnehmer gewährleistet, seinen Pflichten nach Art. 32 Abs. 1 lit. d) DSGVO nachzukommen, ein Verfahren zur regelmäßigen Überprüfung der Wirksamkeit der technischen und organisatorischen Maßnahmen zur Gewährleistung der Sicherheit der Verarbeitung einzusetzen.</li>
<li>Der Auftragnehmer berichtigt oder löscht die vertragsgegenständlichen Daten, wenn der Auftraggeber dies anweist und dies vom Weisungsrahmen umfasst ist. Ist eine datenschutzkonforme Löschung oder eine entsprechende Einschränkung der Datenverarbeitung nicht möglich, übernimmt der Auftragnehmer die datenschutzkonforme Vernichtung von Datenträgern und sonstigen Materialien auf Grund einer Einzelbeauftragung durch den Auftraggeber oder gibt diese Datenträger an den Auftraggeber zurück, sofern nicht im Vertrag bereits vereinbart. In besonderen, vom Auftraggeber zu bestimmenden Fällen, erfolgt eine Aufbewahrung bzw. Übergabe, Vergütung und Schutzmaßnahmen hierzu sind gesondert zu vereinbaren, sofern nicht im Vertrag bereits vereinbart.</li>
<li>Daten, Datenträger sowie sämtliche sonstige Materialien sind nach Auftragsende auf Verlangen des Auftraggebers entweder herauszugeben oder zu löschen.</li>
<li>Im Falle einer Inanspruchnahme des Auftraggebers durch eine betroffene Person hinsichtlich etwaiger Ansprüche nach Art. 82 DSGVO, verpflichtet sich der Auftragnehmer, den Auftraggeber bei der Abwehr des Anspruches im Rahmen seiner Möglichkeiten zu unterstützen.</li>
<li>Der Auftragnehmer benennt dem Auftraggeber in <strong>&nbsp;Anlage 2&nbsp; </strong> die Person(en), die zum Empfang von Weisungen des Auftraggebers berechtigt sind. Für den Fall, dass sich die weisungsempfangsberechtigten Personen ändern, wird der Auftragnehmer dies dem Auftraggeber in Textform mitteilen.</li>
</ol>

<p><strong>§ 4 Pflichten des Auftraggebers</strong></p>
<ol>
<li>Der Auftraggeber hat den Auftragnehmer unverzüglich und vollständig zu informieren, wenn er in den Auftragsergebnissen Fehler oder Unregelmäßigkeiten bzgl. Datenschutz-rechtlicher Bestimmungen feststellt.</li>
<li>Im Falle einer Inanspruchnahme des Auftraggebers durch eine betroffene Person hinsichtlich etwaiger Ansprüche nach Art. 82 DSGVO, gilt § 3 Abs. 10 entsprechend.</li>
<li>Der Auftraggeber nennt dem Auftragnehmer in <strong>&nbsp;Anlage 2&nbsp; </strong> den Ansprechpartner für im Rahmen des Vertrages anfallende Datenschutzfragen.</li>
</ol>

<p><strong>§ 5 Anfragen betroffener Personen</strong></p>
<p>Wendet sich eine betroffene Person mit Forderungen zur Berichtigung, Löschung, oder Auskunft an den Auftragnehmer, wird dieser die betroffene Person an den Auftraggeber verweisen, sofern eine Zuordnung an den Auftraggeber nach Angaben der betroffenen Person möglich ist. Der Auftragnehmer leitet den Antrag der betroffenen Person unverzüglich an den Auftraggeber weiter und unterstützt den Auftraggeber im Rahmen seiner Möglichkeiten auf Weisung soweit vereinbart. Der Auftragnehmer haftet nicht, wenn das Ersuchen der betroffenen Person vom Auftraggeber nicht, nicht richtig oder nicht fristgerecht beantwortet wird.</p>

<p><strong>§ 6 Nachweismöglichkeiten</strong></p>
<ol>
<li>Der Auftragnehmer weist dem Auftraggeber die Einhaltung der in diesem Vertrag niedergelegten Pflichten mit geeigneten Mitteln nach. Der Nachweis solcher Maßnahmen, die nicht nur den konkreten Auftrag betreffen, kann erfolgen durch
<ul>
<li>die Einhaltung genehmigter Verhaltensregeln gemäß Art. 40 DS-GVO;</li>
<li>die Zertifizierung nach einem genehmigten Zertifizierungsverfahren gemäß Art. 42 DS-GVO;</li>
<li>aktuelle Testate, Berichte oder Berichtsauszüge unabhängiger Instanzen (z.B. Wirtschaftsprüfer, Revision, Datenschutzbeauftragter, IT-Sicherheitsabteilung, Datenschutzauditoren, Qualitätsauditoren);</li>
<li>eine geeignete Zertifizierung durch IT-Sicherheits- oder Datenschutzaudit (z.B. nach BSI-Grundschutz).</li>
</ul>
</li>
<li>Sollten im Einzelfall Inspektionen durch den Auftraggeber oder einen von diesem beauftragten Prüfer erforderlich sein, werden diese zu den üblichen Geschäftszeiten ohne Störung des Betriebsablaufs nach Anmeldung unter Berücksichtigung einer angemessenen Vorlaufzeit durchgeführt. Der Auftragnehmer darf diese von der vorherigen Anmeldung mit angemessener Vorlaufzeit und von der Unterzeichnung einer Verschwiegenheitserklärung hinsichtlich der Daten anderer Kunden und der eingerichteten technischen und organisatorischen Maßnahmen abhängig machen. Sollte der durch den Auftraggeber beauftragte Prüfer in einem Wettbewerbsverhältnis zum Auftragnehmer stehen, hat der Auftragnehmer gegen diesen ein Einspruchsrecht.</li>
<li>Für die Unterstützung bei der Durchführung einer Inspektion darf der Auftragnehmer eine Vergütung verlangen, wenn dies im Vertrag vereinbart ist. Der Aufwand einer Inspektion ist für den Auftragnehmer grundsätzlich auf einen Tag pro Kalenderjahr begrenzt.</li>
<li>Sollte eine Datenschutzaufsichtsbehörde oder eine sonstige hoheitliche Aufsichtsbehörde des Auftraggebers eine Inspektion vornehmen, gilt grundsätzlich Absatz 2 entsprechend. Eine Unterzeichnung einer Verschwiegenheitsverpflichtung ist nicht erforderlich, wenn diese Aufsichtsbehörde einer berufsrechtlichen oder gesetzlichen Verschwiegenheit unterliegt, bei der ein Verstoß nach dem Strafgesetzbuch strafbewehrt ist.</li>
</ol>

<p><strong>§ 7 Subunternehmer (weitere Auftragsverarbeiter)</strong></p>
<ol>
<li>Der Auftraggeber stimmt zu, dass der Auftragnehmer Subunternehmer hinzuzieht. Vor Hinzuziehung oder Ersetzung der Subunternehmer informiert der Auftragnehmer den Auftraggeber. Der Auftraggeber kann der Änderung – innerhalb von zwei Wochen vor der Hinzuziehung oder Ersetzung – aus einem datenschutzrechtlichen Grund – gegenüber der vom Auftraggeber bezeichneten Stelle widersprechen. Erfolgt kein Widerspruch innerhalb der Frist gilt die Zustimmung zur Änderung als gegeben. Liegt ein wichtiger datenschutzrechtlicher Grund vor, und sofern eine einvernehmliche Lösungsfindung zwischen den Parteien nicht möglich ist, wird dem Auftraggeber ein Sonderkündigungsrecht eingeräumt.</li>
<li>Über die in <strong>&nbsp;Anlage 3&nbsp; </strong> aufgeführten Unterauftragnehmer wird mit Unterzeichnung dieses Vertrages die notwendige Information erteilt und Zustimmung seitens des Auftraggebers vorausgesetzt. Ergänzungen und Änderungen teilt der Auftragnehmer auf geeignete Weise mit. Aktualisierungsinformationen werden immer auch unter https://www.some-solutions.de/dsgvo-verarbeiter  erfolgen.</li>
<li>Der Auftragnehmer wird mit Subunternehmen im erforderlichen Umfang Vereinbarungen treffen, um angemessene Datenschutz- und Informationssicherheitsmaßnahmen zu gewährleisten.</li>
</ol>

<p><strong>§ 8 Löschung und Rückgabe von Daten, Backup-Regelungen</strong></p>
<ol>
<li><strong>Löschfristen:</strong> Nach Abschluss der vereinbarten Leistungen oder auf Anweisung des Auftraggebers hat der Auftragnehmer personenbezogene Daten nach den jeweils einschlägigen gesetzlichen Vorgaben zu löschen oder an den Auftraggeber zurückzugeben, sofern keine gesetzlichen Aufbewahrungspflichten entgegenstehen.</li>
<li><strong>Ablauf der Löschung:</strong> Die Löschung erfolgt nach den gesetzlichen Vorgaben und orientiert sich an Art und Notwendigkeit der jeweils beauftragten Dienstleistung.
<ul>
<li>Vernichtung physischer Datenträger nach datenschutzgerechten Standards (z.B. DIN 66399),</li>
<li>Backups werden in der Regel täglich erstellt; auf den Hosting-Servern werden Sicherungen alle 3 Tage gelöscht.</li>
</ul>
</li>
<li><strong>Backup-Regelungen:</strong> Der Auftragnehmer erstellt regelmäßige Backups der verarbeiteten Daten. Diese Backups werden entsprechend den vorgenannten Regelungen aufbewahrt und anschließend automatisch gelöscht. Die Backup-Daten werden verschlüsselt gespeichert und unterliegen denselben Sicherheitsmaßnahmen wie die Produktivdaten.</li>
<li><strong>Ausnahmen:</strong> Dokumentationen, die dem Nachweis der ordnungsgemäßen Verarbeitung dienen, sind entsprechend gesetzlicher Aufbewahrungspflichten zu sichern. Diese werden getrennt von den personenbezogenen Daten aufbewahrt und unterliegen den gesetzlichen Aufbewahrungsfristen (in der Regel 10 Jahre).</li>
<li><strong>Rückgabeoption:</strong> Auf ausdrückliche Anweisung des Auftraggebers können Daten statt der Löschung auch in einem strukturierten, gängigen und maschinenlesbaren Format an den Auftraggeber zurückgegeben werden. Die Rückgabe erfolgt verschlüsselt und innerhalb von 14 Tagen nach Anweisung.</li>
</ol>

<p><strong>§ 9 Informationspflichten, Schriftformklausel, Rechtswahl</strong></p>
<ol>
<li>Sollten die Daten des Auftraggebers beim Auftragnehmer durch Pfändung oder Beschlagnahme, durch ein Insolvenz- oder Vergleichsverfahren oder durch sonstige Ereignisse oder Maßnahmen Dritter gefährdet werden, so hat der Auftragnehmer den Auftraggeber unverzüglich darüber zu informieren. Der Auftragnehmer wird alle in diesem Zusammenhang Verantwortlichen unverzüglich darüber informieren, dass die Hoheit und das Eigentum an den Daten ausschließlich beim Auftraggeber als »Verantwortlicher« im Sinne der Datenschutz-Grundverordnung liegen.</li>
<li>Änderungen und Ergänzungen dieser Anlage und aller ihrer Bestandteile – einschließlich etwaiger Zusicherungen des Auftragnehmers – bedürfen einer schriftlichen Vereinbarung, die auch in einem elektronischen Format (Textform) erfolgen kann, und des ausdrücklichen Hinweises darauf, dass es sich um eine Änderung bzw. Ergänzung dieser Bedingungen handelt. Dies gilt auch für den Verzicht auf dieses Formerfordernis.</li>
<li>Bei etwaigen Widersprüchen gehen Regelungen dieser Anlage zum Datenschutz den Regelungen des Vertrages vor. Sollten einzelne Teile dieser Anlage unwirksam sein, so berührt dies die Wirksamkeit der Anlage im Übrigen nicht.</li>
<li>Es gilt deutsches Recht.</li>
<li>Als Gerichtsstand wird, soweit gesetzlich zulässig, Eschwege vereinbart.</li>
</ol>

<p><strong>§ 10 Haftung und Schadensersatz</strong></p>
<p>Auftraggeber und der Auftragnehmer haften gegenüber betroffenen Personen entsprechend der in Art. 82 DSGVO getroffenen Regelung.</p>

<p><br><br>
<strong>Anlage 1:</strong><br>
Gegenstand und Dauer der Verarbeitung, Kategorien von Daten und betroffenen Personen, Art und Zweck der Datenverarbeitung
</p>
<p>
<strong>Anlage 2:</strong><br>
Weisungsberechtigte Personen und Datenschutzbeauftragter
</p>
<p>
<strong>Anlage 3:</strong><br>
Unterauftragnehmer mit Beschreibung der Leistungen / Teilleistungen
</p>
<p>
<strong>Anlage 4:</strong><br>
Technische und organisatorische Maßnahmen nach Art. 32 DSGVO (vgl. auch § 3 Abs. 2)
</p>
<p><br><br>
<em>Erstellt am: {$currentDate}</em>
</p>
";
    }

    /**
     * Get agreement metadata
     * @param array $data User data
     * @return array Metadata for the agreement
     */
    public static function getAgreementMetadata($data)
    {
        return [
            'title' => 'Auftragsverarbeitungsvereinbarung nach Art. 28 DSGVO',
            'parties' => [
                'contractor' => [
                    'name' => 'Conrat WebSolutions GmbH',
                    'address' => 'Gartenstr. 4, 37281 Wanfried',
                    'email' => ''
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
    public static function validateAgreementData($data)
    {
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
