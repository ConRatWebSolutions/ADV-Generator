<?php

/**
 * Anlage 3 - Unterauftragnehmer mit Beschreibung der Leistungen / Teilleistungen
 */

class Anlage3
{
    public static function getContent(): string
    {
        $currentDate = date('d.m.Y');
        return ' 
<p>
    <strong>Stand: ' . $currentDate . '</strong>
</p>
<p>
    Die nachfolgende Liste der Unterauftragnehmer ist Bestandteil dieser Vereinbarung. Änderungen und Ergänzungen werden dem Auftraggeber mitgeteilt. Der Auftraggeber kann der Änderung – innerhalb von zwei Wochen nach Mitteilung – aus einem datenschutzrechtlichen Grund widersprechen. Erfolgt kein Widerspruch innerhalb der Frist, gilt die Zustimmung zur Änderung als gegeben.
</p>
<br> Die Auflistung der eingesetzten Unterauftragnehmer kann sich von Zeit zu Zeit ändern. Die jeweils aktuelle Übersicht ist auf unserer Website unter https://www.some-solutions.de/dsgvo-verarbeiter abrufbar. Wir empfehlen, diese Seite in regelmäßigen Abständen einzusehen. </p> 
<p>
    
</p>
<p>
   - domainfactory GmbH,&nbsp;Oskar-Messter-Str. 33, 85737 Ismaning Deutschland<br>
    für das Webhosting unserer öffentlich zugänglichen Seite, sowie für das Webhosting des Kundenbereichs und für den Betrieb eines Mailservers&nbsp;<br>
    (TOM:&nbsp; https://www.df.eu/fileadmin/user_upload/DF_TOMs_DSGVO_V1.5_Deutsch.pdf).
</p>
<p>
    - IONOS SE, Elgendorfer Str. 57, 56410 Montabaur<br>
    für das Hosting der KI-Server<br>
    (TOM:&nbsp; https://www.ionos.de/terms-gtc/fileadmin/pdf/terms-gtc/DE/AVV/DE_AVV_TOM_v1.0.pdf).
</p>
<p>
    - Microsoft (Microsoft Azure)<br>
    für die Sprachmodelle des KI-Chats und des Dokumenten-Chats, die Bildmodelle, den Vektorspeicher des Wissensspeichers, die Sprachdienste (Spracheingabe und Vorlesen) sowie den Zugriff auf Postfach und Kalender bei Microsoft 365<br>
    Regionen: Frankfurt (DE), G&auml;vle (SE) sowie global bei den in Anlage 1 als global gef&uuml;hrten Modellen<br>
    (Grundlage: Microsoft Products and Services DPA mit EU-Standardvertragsklauseln).
</p>
<p>
    - Google (Google Cloud / Vertex AI und AI Studio)<br>
    f&uuml;r die Sprachmodelle der Gemini-Reihe, die Bildmodelle sowie den Postfachzugriff bei Gmail<br>
    Regionen: St. Ghislain (BE) sowie global (Vertex Global-Endpunkt und AI Studio)<br>
    (Grundlage: Google Cloud Data Processing Addendum mit EU-Standardvertragsklauseln).
</p>
<p>
    - Perplexity AI<br>
    f&uuml;r die KI-gest&uuml;tzte Webrecherche (RechercheMeister)<br>
    USA, Zero Data Retention<br>
    (Grundlage: Perplexity DPA mit EU-Standardvertragsklauseln, Modul 2).
</p>
<p>
    <em>Hinweis: F&uuml;r die Website-Erfassung (Lesen einzelner URLs und Erfassen ganzer Webseiten f&uuml;r den Wissensspeicher) ist kein Unterauftragnehmer eingetragen. Sie l&auml;uft auf einem selbst betriebenen Server in Deutschland ohne Beteiligung eines Dritten; siehe Anlage 1, Abschnitt 6c.</em>
</p>
 <p>
    - consentmanager GmbH, Eppendorfer Weg 183, 20253 Hamburg<br>
    für den Consent-Banner (Cookie-Banner).
</p>
 
<p>
    &nbsp;
</p>';
    }
}
