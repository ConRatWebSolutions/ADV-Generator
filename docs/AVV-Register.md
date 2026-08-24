# AVV-Register

Arbeitsgrundlage fuer die Abstimmung mit dem Datenschutzbeauftragten.
Eine Zeile je Punkt, mit Status und Fundstelle. Dieses Register ist die
einzige Stelle, an der der Umsetzungsstand gefuehrt wird.

## Regeln

1. **Ein Textstand, eine Nummer.** Jede inhaltliche Aenderung an `templates/`
   erhoeht `AvvVersion::VERSION` in `config/avv_version.php`. Die Nummer steht
   in jedem erzeugten PDF unter jeder Anlage.
2. **Kein Deploy ohne Basis.** `AvvVersion::BASIS` benennt das Abgleichdokument,
   auf dem der Textstand beruht. Liegt eine neuere Abgleichfassung vor, wird
   nicht ausgeliefert, bevor sie eingearbeitet ist.
3. **Freigabe bezieht sich auf eine Nummer**, nicht auf ein Datum. Solange
   `AvvVersion::STATUS` auf `entwurf` steht, ist die Fassung nicht freigegeben.

## Aktueller Stand

| | |
|---|---|
| Textstand | 2026-08-24.1 |
| Basis | AVV-Anlage-ConRat-AI-Abgleich-2026-08-22 |
| Status | entwurf, nicht freigegeben |
| Live ausgeliefert | ja, seit 24.08.2026 |

## Bekannte Luecke

Die Stellungnahme des DSB vom 24.08.2026 beantwortet **elf** Fragen und
verweist auf Musik-Generator, Herkunftsmessung sowie die Fragen 09 bis 11.
Die hier vorliegenden Abgleichfassungen vom 21.08. (9 Fragen) und 22.08.
(8 Fragen) enthalten diese Punkte nicht. Es fehlt mindestens eine neuere
Abgleichfassung sowie die Stellungnahme selbst (Ziffern 2, 5 und 6).
Bis beide vorliegen, ist das Register unvollstaendig.

## Offener Sachverhaltskonflikt

**Nano Banana.** Der Abgleich vom 22.08. stellt ausdruecklich fest, die
fruehere Angabe "Vertex AI, St. Ghislain, Belgien, konform" treffe nicht zu,
das Modell sei in der EU-Region nicht verfuegbar und werde global
angesprochen. Danach ist der Vertragstext auf "Global" gestellt worden.
Die Stellungnahme verlangt die Korrektur "einschliesslich Nano Banana (EU)",
also die Rueckkehr zur EU-Angabe. Beide Aussagen koennen nicht zugleich
zutreffen. Vor einer Textaenderung ist am Quellcode zu klaeren, ueber welchen
Endpunkt die beiden Modelle tatsaechlich laufen.

## Punkte

| Nr | Gegenstand | Status | Fundstelle |
|---|---|---|---|
| 01 | Aufbewahrung der Assistenten-Ergebnisse | offen, Entscheidung im Haus | Anlage 1, Abschnitt 12 |
| 02 | Zugangsdaten bei IMAP | offen, Entscheidung im Haus | Anlage 1, Abschnitt 2 und 6a |
| 03 | Grundlage fuer Drittlandtransfers | **umzusetzen**, SCC tragend statt Art. 49 | Anlage 1, Abschnitt 3 und 9 |
| 04 | Veroeffentlichungskanaele als Unterauftragnehmer | offen, Entscheidung im Haus | Anlage 3 |
| 05 | Eigener Crawler in Anlage 4 | offen, Entscheidung im Haus | Anlage 1, Abschnitt 6c; Anlage 4 |
| 06 | Meldefrist von zwoelf Stunden | offen, Entscheidung im Haus | Vertrag, § 3 Abs. 5 |
| 07 | Vorrangsatz zwischen den Produktbloecken | **umzusetzen**, Wortlaut vom DSB abwarten | Anlage 1, Kopf |
| 08 | Formalien im Generator | erledigt in 2026-08-24.1 | Vertragskopf, Anlage 2 |
| 09 | Musik-Generator, Zustimmungsschluessel | **umzusetzen**, Abgleich fehlt | Produkt `conrat-ai` und Anlage 1 |
| 10 | Google AI Studio, Vertragswerk und Firmierungen | **umzusetzen**, Nachweis erforderlich | Anlage 1, Abschnitt 8; Anlage 3 |
| 11 | noch nicht bekannt | Stellungnahme fehlt | |

## Ausserhalb des AVV

| Gegenstand | Frist | Ort |
|---|---|---|
| Art. 50 KI-VO, Kennzeichnung synthetischer Inhalte | 02.12.2026 | Produkt `conrat-ai` |
| Herkunftsmessung, ueberarbeiteter Wortlaut (Ziffer 5) | – | Datenschutzerklaerung conrat-ai.de |

### Kennzeichnung von Bildern, Sachstand

Die Kennzeichnung erfolgt nicht pauschal. Der Kunde entscheidet: er kann sie
beim Download je Bild waehlen oder dauerhaft voreinstellen. Fuer den AVV ist
das ohne Belang, fuer Art. 50 KI-VO ist zu pruefen, ob eine kundenseitig
abwaehlbare Kennzeichnung der Pflicht zur maschinenlesbaren Markierung
genuegt. Diese Pflicht trifft den Anbieter, nicht den Nutzer.
