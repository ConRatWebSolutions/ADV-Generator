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

<p><strong>ConRat WebSolutions GmbH</strong><br>
Gartenstr. 4<br>
37281 Wanfried<br>
–   nachfolgend ConRat (GmbH) genannt – –</p>



<p>
<strong>Präambel&nbsp;&nbsp;</strong>
</p>
<p>
    Diese Anlage konkretisiert die Verpflichtungen der Vertragsparteien zum Datenschutz, die sich aus der im Hauptvertrag in ihren Einzelheiten beschriebenen Auftragsverarbeitung ergeben. Sie findet Anwendung auf alle Tätigkeiten, die mit dem Vertrag in Zusammenhang stehen und bei denen Beschäftigte der ConRat GmbH oder durch Beauftragte personenbezogene Daten des Auftraggebers verarbeiten.&nbsp;
</p>
<p>
    &nbsp;
</p>
<p>
    <strong>§ 1&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Gegenstand, Dauer und Spezifizierung der Auftragsverarbeitung&nbsp;</strong>
</p>
<p>
    Im Einzelnen sind die Angaben in&nbsp;<strong>Anlage 1</strong>&nbsp;Bestandteil der Datenverarbeitung.&nbsp;&nbsp;
</p>
<p>
    Die Laufzeit dieser Anlage richtet sich nach der Laufzeit des Vertrages, sofern sich aus den Bestimmungen dieser Anlage nicht darüber hinausgehende Verpflichtungen ergeben.&nbsp;
</p>
<p>
    <strong>§ 2&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Anwendungsbereich und Verantwortlichkeit&nbsp;</strong>
</p>
<ol>
    <li>
        Die ConRat GmbH verarbeitet personenbezogene Daten im Auftrag des Auftraggebers. Dies umfasst Tätigkeiten, die im Vertrag und in der Leistungsbeschreibung konkretisiert sind. Der Auftraggeber ist im Rahmen dieses Vertrages für die Einhaltung der gesetzlichen Bestimmungen der Datenschutzgesetze, insbesondere für die Rechtmäßigkeit der Datenweitergabe an&nbsp;&nbsp;ConRat sowie für die Rechtmäßigkeit der Datenverarbeitung allein verantwortlich (»Verantwortlicher« im Sinne des Art. 4&nbsp;&nbsp;Nr. 7 DSGVO).&nbsp; 
    </li>
    <li>
        Die Weisungen werden anfänglich durch diesen Vertrag festgelegt und können vom Auftraggeber danach in schriftlicher Form oder in einem elektronischen Format (Textform) an die von ConRat bezeichnete Stelle durch einzelne Weisungen geändert, ergänzt oder ersetzt werden (Einzelweisung). Weisungen, die im Vertrag nicht vorgesehen sind, werden als Antrag auf Leistungsänderung behandelt. Mündliche Weisungen sind unverzüglich schriftlich oder in Textform zu bestätigen.&nbsp;
    </li>
</ol>
<p>
    <strong>§ 3&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Pflichten der ConRat GmbH&nbsp;</strong>
</p>
<ol>
    <li>
        Die ConRat GmbH darf Daten von betroffenen Personen nur im Rahmen des Auftrages und der Weisungen des Auftraggebers verarbeiten außer es liegt ein Ausnahmefall im Sinne des Artikel 28 Abs. 3 a) DSGVO vor. ConRat informiert den Auftraggeber unverzüglich, wenn sie der Auffassung ist, dass eine Weisung gegen anwendbare Gesetze verstößt. ConRat darf die Umsetzung der Weisung solange aussetzen, bis sie vom Auftraggeber bestätigt oder abgeändert wurde. 
    </li>
    <li>
        ConRat wird in seinem Verantwortungsbereich die innerbetriebliche Organisation so gestalten, dass sie den besonderen Anforderungen des Datenschutzes gerecht wird. Er wird technische und organisatorische Maßnahmen gemäß&nbsp;<strong>Anlage 4</strong>&nbsp;zum angemessenen Schutz der Daten des Auftraggebers treffen, die den Anforderungen der Datenschutz--Grundverordnung (Art. 32 DSGVO) genügen. ConRat hat technische und organisatorische Maßnahmen zu treffen, die die Vertraulichkeit, Integrität, Verfügbarkeit und Belastbarkeit der Systeme und Dienste im Zusammenhang mit der Verarbeitung auf Dauer sicherstellen. Dem Auftraggeber sind diese technischen und organisatorischen Maßnahmen bekannt und er trägt die Verantwortung dafür, dass diese für die Risiken der zu verarbeitenden Daten ein angemessenes Schutzniveau bieten. Eine Änderung der getroffenen Sicherheitsmaßnahmen bleibt der ConRat GmbH vorbehalten, wobei jedoch sichergestellt sein muss, dass das vertraglich vereinbarte Schutzniveau nicht unterschritten wird.&nbsp; 
    </li>
    <li>
        ConRat unterstützt soweit vereinbart den Auftraggeber im Rahmen seiner Möglichkeiten bei der Erfüllung der Anfragen und Ansprüche betroffenen Personen gem. Kapitel III der DSGVO sowie bei der Einhaltung der in Art. 33 bis 36 DSGVO genannten Pflichten. 
    </li>
    <li>
        ConRat gewährleistet, dass es den mit der Verarbeitung der Daten des Auftraggebers befassten Mitarbeiter und andere für ConRat tätigen Personen untersagt ist, die Daten außerhalb der Weisung zu verarbeiten. Ferner gewährleistet ConRat, dass sich die zur Verarbeitung der personenbezogenen Daten befugten Personen zur Vertraulichkeit verpflichtet haben oder einer angemessenen gesetzlichen Verschwiegenheitspflicht unterliegen. Die Vertraulichkeits-/ Verschwiegenheitspflicht besteht auch nach Beendigung des Auftrages fort. 
    </li>
    <li>
        Die ConRat GmbH unterrichtet den Auftraggeber unverzüglich, wenn ihr Verletzungen des Schutzes personenbezogener Daten des Auftraggebers bekannt werden. ConRat trifft die erforderlichen Maßnahmen zur Sicherung der Daten und zur Minderung möglicher nachteiliger Folgen der betroffenen Personen und spricht sich hierzu unverzüglich mit dem Auftraggeber ab.&nbsp; 
    </li>
    <li>
        Die ConRat GmbH hat zur Klärung von Datenschutzfragen und zur Einhaltung der gesetzlichen Aufgaben nach der DS-GVO, dem BDSG (neu) und dem Hessischen Landesdatenschutzgesetz einen externen Datenschutzbeauftragten bestellt. 
    </li>
    <li>
        ConRat&nbsp;&nbsp;gewährleistet, seinen Pflichten nach Art. 32 Abs. 1 lit. d) DSGVO nachzukommen, ein Verfahren zur regelmäßigen Überprüfung der Wirksamkeit der technischen und organisatorischen Maßnahmen zur Gewährleistung der Sicherheit der Verarbeitung einzusetzen. 
    </li>
    <li>
        ConRat berichtigt oder löscht die vertragsgegenständlichen Daten, wenn der Auftraggeber dies anweist und dies vom Weisungsrahmen umfasst ist. Ist eine datenschutzkonforme Löschung oder eine entsprechende Einschränkung der Datenverarbeitung nicht möglich, übernimmt ConRat die datenschutzkonforme Vernichtung von Datenträgern und sonstigen Materialien auf Grund einer Einzelbeauftragung durch den Auftraggeber oder gibt diese Datenträger an den Auftraggeber zurück, sofern nicht im Vertrag bereits vereinbart. In besonderen, vom Auftraggeber zu bestimmenden Fällen, erfolgt eine Aufbewahrung bzw. Übergabe, Vergütung und Schutzmaßnahmen hierzu sind gesondert zu vereinbaren, sofern nicht im Vertrag bereits vereinbart. 
    </li>
    <li>
        Daten, Datenträger sowie sämtliche sonstige Materialien sind nach Auftragsende auf Verlangen des Auftraggebers entweder herauszugeben oder zu löschen. 
    </li>
    <li>
        Im Falle einer Inanspruchnahme des Auftraggebers durch eine betroffene Person hinsichtlich etwaiger Ansprüche nach Art. 82 DSGVO, verpflichtet sich der ConRat den Auftraggeber bei der Abwehr des Anspruches im Rahmen seiner Möglichkeiten zu unterstützen. 
    </li>
    <li>
        Die ConRat GmbH benennt dem Auftraggeber in&nbsp;<strong>Anlage 2</strong>&nbsp;die Person(en), die zum Empfang von Weisungen des Auftraggebers berechtigt sind. Für den Fall, dass sich die weisungsempfangsberechtigten Personen ändern, wird ConRat dies dem Auftraggeber in Textform mitteilen.&nbsp;<br>
        &nbsp;
    </li>
</ol>
<p>
    <strong>§ 4 Pflichten des Auftraggebers&nbsp;</strong>
</p>
<ol>
    <li>
        Der Auftraggeber hat ConRat unverzüglich und vollständig zu informieren, wenn er in den&nbsp;<br>
        Auftragsergebnissen Fehler oder Unregelmäßigkeiten bzgl. Datenschutz-rechtlicher Bestimmungen feststellt.&nbsp; 
    </li>
    <li>
        Im Falle einer Inanspruchnahme des Auftraggebers durch eine betroffene Person hinsichtlich etwaiger Ansprüche nach Art. 82 DSGVO, gilt § 3 Abs. 10 entsprechend. 
    </li>
    <li>
        Der Auftraggeber nennt der ConRat GmbH in&nbsp;<strong>Anlage 2</strong>&nbsp;den Ansprechpartner für im Rahmen des Vertrages anfallende Datenschutzfragen.&nbsp;
    </li>
</ol>
<p>
    <strong>§ 5 Anfragen betroffener Personen&nbsp;</strong>
</p>
<p>Wendet sich eine betroffene Person mit Forderungen zur Berichtigung, Löschung, oder Auskunft an ConRat, wird die ConRat GmbH die betroffene Person an den Auftraggeber verweisen, sofern eine Zuordnung an den Auftraggeber nach Angaben der betroffenen Person möglich ist. ConRat leitet den Antrag der betroffenen Person unverzüglich an den Auftraggeber weiter und unterstützt den Auftraggeber im Rahmen seiner Möglichkeiten auf Weisung soweit vereinbart. ConRat haftet nicht, wenn das Ersuchen der betroffenen Person vom Auftraggeber nicht, nicht richtig oder nicht fristgerecht beantwortet wird.&nbsp;
</p>
<p>
    <strong>§ 6 Nachweismöglichkeiten&nbsp;</strong>
</p>
<ol>
    <li>
        Die ConRat GmbH weist dem Auftraggeber die Einhaltung der in diesem Vertrag niedergelegten Pflichten mit geeigneten Mitteln nach. Der Nachweis solcher Maßnahmen, die nicht nur den konkreten Auftrag betreffen, kann erfolgen durch&nbsp; 
        <ul>
            <li>
                die Einhaltung genehmigter Verhaltensregeln gemäß Art. 40 DS-GVO; 
            </li>
            <li>
                die Zertifizierung nach einem genehmigten Zertifizierungsverfahren gemäß Art. 42 DS-GVO; 
            </li>
            <li>
                aktuelle Testate, Berichte oder Berichtsauszüge unabhängiger Instanzen (z.B. Wirtschaftsprüfer, Revision, Datenschutzbeauftragter, IT-Sicherheitsabteilung, Datenschutzauditoren, Qualitätsauditoren); 
            </li>
            <li>
                eine geeignete Zertifizierung durch IT-Sicherheits- oder Datenschutzaudit (z.B. nach BSIGrundschutz). 
            </li>
        </ul>
    </li>
    <li>
        Sollten im Einzelfall Inspektionen durch den Auftraggeber oder einen von diesem beauftragten Prüfer erforderlich sein, werden diese zu den üblichen Geschäftszeiten ohne Störung des Betriebsablaufs nach Anmeldung unter Berücksichtigung einer angemessenen Vorlaufzeit durchgeführt. Der Auftragnehmer darf diese von der vorherigen Anmeldung mit angemessener Vorlaufzeit und von der Unterzeichnung einer Verschwiegenheitserklärung hinsichtlich der Daten anderer Kunden und der eingerichteten technischen und organisatorischen Maßnahmen abhängig machen. Sollte der durch den Auftraggeber beauftragte Prüfer in einem Wettbewerbsverhältnis zu ConRat stehen, hat die ConRat GmbH gegen diesen ein Einspruchsrecht.
    </li>
    <li>
        Für die Unterstützung bei der Durchführung einer Inspektion darf die ConRat GmbH eine Vergütung verlangen, wenn dies im Vertrag vereinbart ist. Der Aufwand einer Inspektion ist für ConRat grundsätzlich auf einen Tag pro Kalenderjahr begrenzt.&nbsp; 
    </li>
    <li>
        Sollte eine Datenschutzaufsichtsbehörde oder eine sonstige hoheitliche Aufsichtsbehörde des Auftraggebers eine Inspektion vornehmen, gilt grundsätzlich Absatz 2 entsprechend. Eine Unterzeichnung einer Verschwiegenheitsverpflichtung ist nicht erforderlich, wenn diese Aufsichtsbehörde einer berufsrechtlichen oder gesetzlichen Verschwiegenheit unterliegt, bei der ein Verstoß nach dem Strafgesetzbuch strafbewehrt ist.&nbsp;&nbsp;
    </li>
</ol>
<p>
    <strong>§ 7&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Subunternehmer (weitere Auftragsverarbeiter)&nbsp;</strong>
</p>
<ol>
    <li>
        Der Auftraggeber stimmt zu, dass der Auftragnehmer Subunternehmer hinzuzieht. Vor Hinzuziehung oder Ersetzung der Subunternehmer informiert der Auftragnehmer den Auftraggeber. Der Auftraggeber kann der Änderung – innerhalb von zwei Wochen vor der Hinzuziehung oder Ersetzung – aus einem datenschutzrechtlichen Grund – gegenüber der vom Auftraggeber bezeichneten Stelle widersprechen. Erfolgt kein Widerspruch innerhalb der Frist gilt die Zustimmung zur Änderung als gegeben. Liegt ein wichtiger datenschutzrechtlicher Grund vor, und sofern eine einvernehmliche Lösungsfindung zwischen den Parteien nicht möglich ist, wird dem Auftraggeber ein Sonderkündigungsrecht eingeräumt.
    </li>
    <li>
        Über die in&nbsp;<strong>Anlage 3</strong>&nbsp;aufgeführten Unterauftragnehmer wird mit Unterzeichnung dieses Vertrages die notwendige Information erteilt und Zustimmung seitens des Auftraggebers vorausgesetzt. Ergänzungen und Änderungen teilt ConRat auf geeignete Weise mit. Aktualisierungsinformationen werden immer auch unter&nbsp;<a href=\"https://www.some-solutions.de/dsgvo-verarbeiter\" target=\"_blank\" rel=\"noopener noreferrer\">https://www.some-solutions.de/dsgvo-verarbeiter</a>&nbsp;erfolgen.
    </li>
    <li>
        ConRat wird mit Subunternehmen im erforderlichen Umfang Vereinbarungen treffen, um angemessene Datenschutz- und Informationssicherheitsmaßnahmen zu gewährleisten.&nbsp;&nbsp;<br>
        &nbsp;
    </li>
</ol><p><br><br><br><br><br></p><p><br><br><br><br><br></p><p><br><br><br><br><br></p><p><br><br><br><br><br></p>
<p><br><br>
    <strong>§ 8 Informationspflichten, Schriftformklausel, Rechtswahl&nbsp;&nbsp;</strong>
</p>
<ol>
    <li>
        Sollten die Daten des Auftraggebers bei ConRat durch Pfändung oder Beschlagnahme, durch ein Insolvenz-&nbsp; 
        oder Vergleichsverfahren oder durch sonstige Ereignisse oder Maßnahmen Dritter gefährdet werden, so hat ConRat den Auftraggeber unverzüglich darüber zu informieren. ConRat wird alle in diesem Zusammenhang Verantwortlichen unverzüglich darüber informieren, dass die Hoheit und das Eigentum an den Daten ausschließlich beim Auftraggeber als »Verantwortlicher « im Sinne der Datenschutz-Grundverordnung liegen.&nbsp; 
    </li>
    <li>
        Änderungen und Ergänzungen dieser Anlage und aller ihrer Bestandteile – einschließlich etwaiger Zusicherungen der ConRat GmbH – bedürfen einer schriftlichen Vereinbarung, die auch in einem elektronischen Format (Textform) erfolgen kann, und des ausdrücklichen Hinweises darauf, dass es sich um eine Änderung bzw. Ergänzung dieser Bedingungen handelt. Dies gilt auch für den Verzicht auf dieses Formerfordernis. 
    </li>
    <li>
        Bei etwaigen Widersprüchen gehen Regelungen dieser Anlage zum Datenschutz den Regelungen des Vertrages vor. Sollten einzelne Teile dieser Anlage unwirksam sein, so berührt dies die Wirksamkeit der Anlage im Übrigen nicht. 
    </li>
    <li>
        Es gilt deutsches Recht.&nbsp;&nbsp;
    </li>
</ol>
<p>
    &nbsp;§ 9 Haftung und Schadensersatz&nbsp;&nbsp;
</p>
<p>
    Auftraggeber und die ConRat GmbH haften gegenüber betroffener Personen entsprechend der in Art. 82 DSGVO getroffenen Regelung.&nbsp;
</p>
<p>
    <br> </p><p> <br>  <br>
    <strong>Anlage 1:</strong>&nbsp;<br>
    Gegenstand und Dauer der Verarbeitung, Kategorien von Daten und betroffenen Personen, Art und Zweck der Datenverarbeitung &nbsp;
</p>
<p>
    <strong>Anlage 2:</strong>&nbsp;<br>
    Weisungsberechtigte Personen und Datenschutzbeauftragter&nbsp;
</p>
<p>
    <strong>Anlage 3:</strong>&nbsp;<br>
    Unterauftragnehmer mit Beschreibung der Leistungen / Teilleistungen&nbsp;
</p>
<p>
    <strong>Anlage 4:</strong>&nbsp;<br>
    Technische und organisatorische Maßnahmen nach Art. 32 DSGVO (vgl. auch § 3 Abs. 2)&nbsp;
</p>
<p>
    &nbsp;
</p>
<p>
 



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
