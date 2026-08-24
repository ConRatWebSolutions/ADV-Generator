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
| Textstand | 2026-08-24.6 |
| Basis | AVV-Anlage-ConRat-AI-Abgleich-2026-08-24, zweiter Durchgang |
| Status | entwurf, nicht freigegeben |
| Live ausgeliefert | Textstand vor 2026-08-24.1, Deploy ausstehend |

## Was am 24.08.2026 wirklich passiert ist

Die sieben Abweichungen, die der Datenschutzbeauftragte gefunden hat, sind
**kein Textproblem, sondern ein Auslieferungsproblem**. Commit 269b814 hat den
zweiten Durchgang in die Vorlagen eingearbeitet, danach wurde nicht mehr
deployt. Der DSB hat den Abgleich vom 24.08. gegen ein Muster-PDF gelesen,
das den Stand davor zeigt. Genau dagegen wirkt die Textstand-Nummer: sie haette
den Versatz auf der ersten Seite sichtbar gemacht.

Der Sachverhaltskonflikt zu Nano Banana ist damit ebenfalls aufgeloest. Der
erste Durchgang hatte Nano Banana (EU) mit Nano Banana 2 verwechselt. Richtig
sind sechs Bildmodelle, vier davon mit EU-Garantie; global sind allein
Nano Banana 2 und Nano Banana Pro.

## Punkte

| Nr | Gegenstand | Status | Fundstelle |
|---|---|---|---|
| 01 | Aufbewahrung der Assistenten-Ergebnisse | offen, Entscheidung im Haus | Anlage 1, Abschnitt 12 |
| 02 | Zugangsdaten bei IMAP | offen, Entscheidung im Haus | Anlage 1, Abschnitt 2 und 6a |
| 03 | Grundlage fuer Drittlandtransfers | erledigt in 2026-08-24.2, SCC tragend | Anlage 1, Abschnitt 3 |
| 04 | Veroeffentlichungskanaele als Unterauftragnehmer | offen, Entscheidung im Haus | Anlage 3 |
| 05 | Eigener Crawler in Anlage 4 | offen, Entscheidung im Haus | Anlage 4 |
| 06 | Meldefrist von zwoelf Stunden | offen, Entscheidung im Haus | Vertrag, § 3 Abs. 5 |
| 07 | Vorrangsatz | erledigt in 269b814, Wortlaut und Ort noch zu bestaetigen | Anlage 1, Kopf |
| 08 | Formalien im Generator | erledigt in 2026-08-24.1 | Vertragskopf, Anlage 2 |
| 09 | Musik-Generator ohne eigene Zustimmung | erledigt, Produkt und Vertrag | Anlage 1, Abschnitt 6d und 9 |
| 10 | Firmierung und TOM der drei neuen Eintraege | erledigt in 2026-08-24.3 | Anlage 3 |
| 11 | Verweis auf die Verarbeiterliste | erledigt, Adresse und Seiteninhalt | Anlage 3, Vertrag § 7 Abs. 2 |

## Zusaetzlich erledigt in 2026-08-24.2

- **Google AI Studio.** Der Vertragsbezug wurde zurueckgenommen: das Google
  Cloud DPA gilt ausweislich der Pruefung des DSB nicht fuer AI Studio. Anlage 1
  und Anlage 3 sagen jetzt, dass die Grundlage fuer AI Studio noch zu belegen
  ist, und verweisen auf Nano Banana (EU) als EU-gebundene Alternative.
- **Gueltigkeitsdauer der Download-Adressen.** Die Zusage "1 Stunde" war im
  Quellcode nicht belegt und ist aus dem Vertrag entfernt. Die Signaturpruefung
  selbst ist belegt und bleibt beschrieben.

## Blocker vor der naechsten Auslieferung

Keine offenen technischen Punkte. Ausstehend sind allein die Bewertungen und
Entscheidungen zu den Fragen 01, 02, 04, 05, 06 und 07.

## Verarbeiterliste, Abgleich vom 24.08.2026

https://somesolutions.de/auftragsverarbeiter/ und Anlage 3 fuehren dieselben
sechs Verarbeiter. Neunzehn Einzelmerkmale maschinell gegengeprueft
(Firmierungen, Anschriften, Einsatzbereiche, Regionen, Vertragsgrundlagen,
TOM-Verweise, Hinweis zur Website-Erfassung): null Abweichungen. Die Seite
traegt das Standdatum 24.08.2026.

Bei kuenftigen Aenderungen sind beide Orte gemeinsam zu pflegen; die Seite ist
in Anlage 3 und in § 7 Abs. 2 als verbindliche Uebersicht benannt.

Randnotiz: das Wildcard-Zertifikat *.somesolutions.de laeuft am 11.09.2026 ab.

## Erledigt in 2026-08-24.5

**Google AI Studio entfaellt.** Nano Banana 2 und Nano Banana Pro laufen jetzt
ueber den Vertex-Global-Endpunkt statt ueber Google AI Studio; der Testaufruf
belegt HTTP 200 fuer beide Modelle unter locations/global, 404 in
europe-west1 und europe-west4. Damit gilt fuer alle Google-Dienste einheitlich
das Google Cloud Data Processing Addendum mit Standardvertragsklauseln. Der
Einwand des Datenschutzbeauftragten zu AI Studio ist gegenstandslos. Abschnitt
5, 8 und 9 der Anlage 1 sowie der Google-Eintrag in Anlage 3 sind angepasst,
AI Studio kommt im Vertrag nicht mehr vor.

Nebeneffekt: der Schluessel NEXT_PUBLIC_GOOGLE_GEMINI_API_KEY wird nirgends
mehr gelesen und ist aus CI und Deploy-Skripten entfernt. Die Zusage in
Abschnitt 10, dass API-Schluessel ausschliesslich serverseitig verarbeitet
werden, ist damit nicht mehr nur faktisch, sondern strukturell abgesichert.

## Erledigt in 2026-08-24.4

**Frage 09, Musik-Generator.** Der Musik-Generator hat einen eigenen,
kontogebundenen Zustimmungsschluessel (`lyria`). Der Waechter in
`src/app/api/vertex/generate-music/route.ts` sitzt vor Credits und
Kapazitaetsgrenze und antwortet ohne Zustimmung mit 409; ohne Einwilligung
ergeht keine Anfrage an Google und es wird nichts abgebucht. Geprueft wird die
Zustimmung des Handelnden, nicht die des Credit-Inhabers; Admins sind nicht
ausgenommen. Eine Zustimmung zu den Bildmodellen deckt Musik nicht ab.
8 Tests gruen, nachgefahren am 24.08.2026. Abschnitt 6d und 9 der Anlage 1
sind entsprechend geaendert, der Widerspruch zu Abschnitt 11 ist damit
aufgeloest.

**Frage 10 abgeschlossen.** Die Perplexity-DPA-Adresse ist bestaetigt und steht
neben dem TOM-Verweis in Anlage 3.

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
