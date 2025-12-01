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
<p>
    <strong>Hosting</strong>
</p>
<p>
    domainfactory GmbH,&nbsp;Oskar-Messter-Str. 33, 85737 Ismaning Deutschland<br>
    für das Webhosting unserer öffentlich zugänglichen Seite, sowie für das Webhosting des Kundenbereichs und für den Betrieb eines Mailservers&nbsp;<br>
    (TOM:&nbsp;<a href="https://www.df.eu/fileadmin/user_upload/DF_TOMs_DSGVO_V1.5_Deutsch.pdf" target="_blank" rel="noopener noreferrer">https://www.df.eu/fileadmin/user_upload/DF_TOMs_DSGVO_V1.5_Deutsch.pdf</a>).
</p>
<p>
    IONOS SE, Elgendorfer Str. 57, 56410 Montabaur<br>
    für das Hosting der KI-Server<br>
    (TOM:&nbsp;<a href="https://www.ionos.de/terms-gtc/fileadmin/pdf/terms-gtc/DE/AVV/DE_AVV_TOM_v1.0.pdf" target="_blank" rel="noopener noreferrer">https://www.ionos.de/terms-gtc/fileadmin/pdf/terms-gtc/DE/AVV/DE_AVV_TOM_v1.0.pdf</a>).
</p>
<p>
    Aktualisierungsinformationen werden auch unter <a href="https://www.some-solutions.de/dsgvo-verarbeiter" target="_blank" rel="noopener noreferrer">https://www.some-solutions.de/dsgvo-verarbeiter</a> bereitgestellt.
</p>
<p>
    &nbsp;
</p>';
    }
}
