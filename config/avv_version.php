<?php
declare(strict_types=1);

/**
 * Inhaltsversion der AVV-Vorlagen.
 *
 * Wichtig: Diese Angaben beschreiben den Stand des VERTRAGSTEXTES, nicht den
 * Zeitpunkt der PDF-Erzeugung. Sie sind bei jeder inhaltlichen Aenderung an
 * templates/ mit anzupassen, damit eine ausgelieferte Fassung eindeutig einem
 * Commit und einem Abgleichdokument zugeordnet werden kann.
 */
final class AvvVersion
{
    /** Fortlaufende Inhaltsversion, Format JJJJ-MM-TT.N */
    public const VERSION = '2026-08-24.3';

    /** Datum der letzten inhaltlichen Aenderung am Vertragstext */
    public const CONTENT_DATE = '24.08.2026';

    /** Abgleichdokument, auf dem dieser Textstand beruht */
    public const BASIS = 'AVV-Anlage-ConRat-AI-Abgleich-2026-08-24, zweiter Durchgang';

    /** Freigabestand: 'entwurf' oder 'freigegeben' */
    public const STATUS = 'entwurf';

    /** Kurzkennung fuer Fusszeile und Anlagen, z. B. "Textstand 2026-08-24.1" */
    public static function label(): string
    {
        return 'Textstand ' . self::VERSION . ' vom ' . self::CONTENT_DATE;
    }
}
