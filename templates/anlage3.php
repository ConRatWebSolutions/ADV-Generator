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
    - consentmanager GmbH, Eppendorfer Weg 183, 20253 Hamburg<br>
    für den Consent-Banner (Cookie-Banner).
</p>
 
<p>
    &nbsp;
</p>';
    }
}
