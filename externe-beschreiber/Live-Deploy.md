# Live-Deployment (All-Inkl via FTP)

## Ablauf

1. Geaenderte Dateien per FTP hochladen
2. Anhand der Tabelle unten pruefen, welche Befehle noetig sind
3. Befehle per SSH ausfuehren

## Was wurde geaendert? → Was muss ich tun?

| Aenderung | Befehl(e) auf dem Server |
|---|---|
| Nur PHP-Dateien (Controller, Models, Livewire, Views) | `php artisan optimize:clear` |
| Blade-Templates | `php artisan optimize:clear` |
| Routes (web.php, api.php) | `php artisan optimize:clear` |
| Config-Dateien (config/*.php) | `php artisan optimize:clear` |
| .env | `php artisan optimize:clear` |
| Migrations (database/migrations/*) | `php artisan migrate --force` |
| JS/CSS/Frontend-Assets (resources/js, resources/css) | `npm run build` |
| composer.json / neue PHP-Pakete | `composer install --no-dev --optimize-autoloader` |
| package.json / neue NPM-Pakete | `npm install && npm run build` |

## Kurzfassung

**90% der Faelle:** Nur PHP/Blade geaendert → nur Cache leeren:

```bash
php artisan optimize:clear
```

**Bei Datenbank-Aenderungen** (neue Migration-Dateien):

```bash
php artisan migrate --force
php artisan optimize:clear
```

**Bei Frontend-Aenderungen** (JS/CSS/Vite):

```bash
npm run build
php artisan optimize:clear
```

**Alles zusammen** (im Zweifel):

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
php artisan migrate --force
php artisan optimize:clear
```

## Hinweise

- `optimize:clear` leert alle Caches (Config, Routes, Views, Events) in einem Befehl
- `--force` bei migrate ist noetig, weil APP_ENV=production Migrationen sonst blockiert
- `--no-dev` bei composer install laesst Test-Pakete weg (spart Speicher)
- Nach dem Deploy pruefen: Seite im Browser aufrufen, ggf. Browser-Cache leeren
