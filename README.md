# ADV Conrat

PHP Projekt für ADV Kunde-Conrat mit DDEV-Entwicklungsumgebung.

## Voraussetzungen

* [DDEV](https://ddev.readthedocs.io/en/stable/users/install/) installiert
* Git installiert

## Lokale Entwicklung einrichten

### 1. Projekt klonen
```bash
git clone <repository-url> adv
cd adv
```

### 2. DDEV-Umgebung starten
```bash
ddev start
```

### 3. Composer-Abhängigkeiten installieren
```bash
ddev composer install
```

### 4. Umgebungsvariablen einrichten
 
*Hinweis: Falls die .env-Dateien nicht vorhanden sind, bitte beim Entwickler anfragen.*

### 5. Datenbank importieren
```bash
ddev dump-db-fast
```

## Entwicklung starten

Nach erfolgreicher Einrichtung ist das Projekt unter der DDEV-URL erreichbar:
- Frontend: `https://geonaturpark.ddev.site`
- Backend: `https://geonaturpark.ddev.site/typo3`

## Nützliche DDEV-Befehle

```bash
ddev start          # Entwicklungsumgebung starten
ddev stop           # Entwicklungsumgebung stoppen
ddev restart        # Entwicklungsumgebung neu starten
ddev ssh auth           # SSH-Zugang zum Container

ddev dump-db     #Datenbank vom Server laden
ddev deploy     # Daten auf Server spielen


ddev composer <cmd> # Composer-Befehle ausführen
ddev exec <cmd>     # Befehle im Container ausführen
```

## Projektstruktur


- `config/` - TYPO3-Konfiguration
- `public/` - Web-Root
- `var/` - TYPO3-Cache und Logs

## Lizenz

GPL-2.0 or later
