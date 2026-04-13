# Externe Beschreiber — Design Spec

> Web-App zur Erfassung von Auktionsbeschreibungen, losgelöst vom Backoffice.
> Subdomain von `hkwi.auction`, z.B. `beschreiber.hkwi.auction`.

## Tech-Stack

- **Backend:** Laravel (PHP) mit Blade-Templates und Livewire
- **Datenbank:** MySQL
- **Hosting:** Dedizierter Server bei All-Inkl. (PHP + MySQL, SSH möglich, kein Docker)
- **E-Mail:** SMTP über bestehendes Konto bei All-Inkl.

## Rollen

| Rolle           | Beschreibung                                                                             |
| --------------- | ---------------------------------------------------------------------------------------- |
| **Admin**       | 2 Nutzer. Verwaltet Beschreiber und Einlieferungen. Sieht alles.                         |
| **Beschreiber** | Externe Mitarbeiter. Sehen nur zugewiesene Einlieferungen. Erfassen und bearbeiten Lose. |

## Datenmodell

### users

| Feld                    | Typ               | Beschreibung      |
| ----------------------- | ----------------- | ----------------- |
| id                      | PK                |                   |
| name                    | string            | Vor- und Nachname |
| email                   | string, unique    | Login-Kennung     |
| password                | string, hashed    |                   |
| role                    | enum: admin, user |                   |
| locale                  | enum: de, en      | Sprachpräferenz   |
| created_at / updated_at | timestamps        |                   |

### consignments (Einlieferungen)

| Feld                    | Typ                | Beschreibung                                            |
| ----------------------- | ------------------ | ------------------------------------------------------- |
| id                      | PK                 |                                                         |
| consignor_number        | string             | Einlieferernummer, z.B. "7389123"                       |
| internal_nid            | string             | Referenz-ID aus dem Backoffice                          |
| start_number            | integer            | Startwert der Einlieferlaufnummer                       |
| next_number             | integer            | Nächste freie Laufnummer (wird bei jedem Los +1 erhöht) |
| catalog_part_id         | FK → catalog_parts | Standard: "Main Catalog"                                |
| user_id                 | FK → users         | Zugewiesener Beschreiber                                |
| status                  | enum: open, closed |                                                         |
| created_at / updated_at | timestamps         |                                                         |

### lots (Lose)

| Feld                    | Typ                | Beschreibung                               |
| ----------------------- | ------------------ | ------------------------------------------ |
| id                      | PK                 |                                            |
| consignment_id          | FK → consignments  |                                            |
| sequence_number         | integer            | Einlieferlaufnummer (automatisch vergeben) |
| category_id             | FK → categories    |                                            |
| description             | text               | Losbeschreibung (mehrzeilig)               |
| catalog_type_id         | FK → catalog_types |                                            |
| catalog_number          | string             | Katalognummer                              |
| starting_price          | decimal(10,2)      | Startpreis in EUR                          |
| notes                   | string, nullable   | Bemerkung (einzeilig)                      |
| created_at / updated_at | timestamps         |                                            |

### Lookup-Tabellen

Alle Lookup-Tabellen haben die gleiche Struktur:

| Feld    | Typ    | Beschreibung    |
| ------- | ------ | --------------- |
| id      | PK     |                 |
| name_de | string | Deutscher Name  |
| name_en | string | Englischer Name |

- **categories** — Loskategorien (z.B. Briefmarken, Briefe, Sammlungen)
- **catalog_types** — Katalogtypen (z.B. Michel, Scott, Yvert)
- **catalog_parts** — Katalogparts, zusätzlich `is_default` (boolean) für den Standardeintrag "Main Catalog"

## Architektur

### Zwei Layer

1. **Web (Session Auth)** — Blade/Livewire, Cookie-basierte Sessions. Für Admins und Beschreiber.
2. **API (API-Key Auth)** — JSON REST unter `/api/v1`. Header `X-API-Key`. Für das Dashboard (hk-dashboard).

### Web-Routen

**Alle (authentifiziert):**

- `POST /login`, `POST /logout`, `POST /password/change`

**Beschreiber (role: user):**

- `GET /consignments` — Liste eigener Einlieferungen
- `GET /consignments/{id}` — Detail + Lose
- `POST /consignments/{id}/lots` — Los anlegen
- `PUT /consignments/{id}/lots/{lot}` — Los bearbeiten
- `DELETE /consignments/{id}/lots/{lot}` — Los löschen

Lose können nur bearbeitet/gelöscht werden, solange die Einlieferung offen ist (Middleware-Check).

**Admin (role: admin):**

- `GET /admin/users` — Beschreiberverwaltung
- `POST /admin/users` — Beschreiber anlegen
- `PUT /admin/users/{id}` — Beschreiber bearbeiten
- `POST /admin/users/{id}/send-credentials` — Zugangsdaten per E-Mail senden
- `GET /admin/consignments` — Alle Einlieferungen (Filter nach Status, Beschreiber)
- `POST /admin/consignments` — Einlieferung anlegen
- `POST /admin/consignments/{id}/close` — Einlieferung schließen
- Admin kann auch alle Beschreiber-Routen nutzen (alle Einlieferungen einsehen + Lose)

### API-Routen (`/api/v1`, Header: `X-API-Key`)

**Lesen:**

- `GET /api/v1/consignments` — Alle Einlieferungen (Filter: status, consignor_number)
- `GET /api/v1/consignments/{id}` — Einzelne Einlieferung
- `GET /api/v1/consignments/{id}/lots` — Lose einer Einlieferung
- `GET /api/v1/lots?consignor_number=...` — Lose nach Einlieferernummer

**Schreiben:**

- `POST /api/v1/consignments` — Einlieferung anlegen
- `PUT /api/v1/consignments/{id}` — Einlieferung bearbeiten
- `POST /api/v1/users` — Beschreiber anlegen
- `PUT /api/v1/users/{id}` — Beschreiber bearbeiten

### Middleware

| Middleware                  | Zweck                                                      |
| --------------------------- | ---------------------------------------------------------- |
| `auth` (Laravel)            | Session-Authentifizierung                                  |
| `RoleMiddleware`            | Prüft Admin-Rolle für `/admin/*` Routen                    |
| `ApiKeyMiddleware`          | Validiert `X-API-Key` Header gegen `.env` Wert             |
| `ConsignmentOpenMiddleware` | Verhindert Los-Änderungen bei geschlossenen Einlieferungen |

## UI-Konzept

### Beschreiber-Ansicht

**Einlieferungsliste:**

- Karten mit Einlieferernummer, NID, Laufnummer-Start, Losanzahl, Status-Badge (Offen/Geschlossen)
- Nur eigene Einlieferungen sichtbar

**Los-Erfassung (Inline-Formular):**

- Tabelle aller Lose mit Lfd.Nr., Kategorie, Beschreibung (gekürzt), Kat.Typ, Kat.Nr., Startpreis, Edit-Button
- Formular unterhalb der Tabelle:
  - Zeile 1: **Kategorie** (volle Breite, Filter-as-you-type Dropdown)
  - Zeile 2: **Katalogtyp** (Filter-as-you-type), **Katalognummer**, **Startpreis** (3-spaltig)
  - Zeile 3: **Losbeschreibung** (Textarea, 4 Zeilen initial, resizable)
  - Zeile 4: **Bemerkung** (einzeilig, optional)
  - Buttons: "Abbrechen", "Speichern & Nächstes"
- Laufnummer wird automatisch vergeben und angezeigt
- "Speichern & Nächstes" speichert und öffnet sofort ein leeres Formular mit nächster Laufnummer
- Bei geschlossenen Einlieferungen: Formular und Edit-Buttons ausgeblendet

### Admin-Ansicht

**Navigation:** Beschreiber | Einlieferungen | DE/EN | Abmelden

**Beschreiberverwaltung:**

- Tabelle: Name, E-Mail, Rolle, Anzahl Einlieferungen
- 📧-Button zum erneuten Senden der Zugangsdaten
- Formular: Name, E-Mail, generiertes Passwort, Rolle
- Buttons: "Speichern" oder "Speichern & Zugangsdaten senden"

**Einlieferungen:**

- Filter nach Status und Beschreiber
- Tabelle: Einlieferernummer, NID, Beschreiber, Losanzahl, Status
- 👁️ zum Einsehen, 🔒 zum Schließen (nur bei offenen)
- Formular: Einlieferernummer, Interne NID, Startnummer, Katalogpart (Default: Main Catalog), Zuweisung an Beschreiber

## Authentifizierung & E-Mail

**Login:**

- E-Mail + Passwort, Laravel Session Auth
- Session-Timeout: 120 Minuten (Laravel Standard)
- Passwort-ändern-Funktion verfügbar, kein erzwungener Wechsel beim ersten Login

**Beschreiber-Anlage:**

- Admin vergibt generiertes Passwort
- "Speichern & Zugangsdaten senden" verschickt E-Mail via SMTP
- E-Mail enthält: Login-URL, E-Mail-Adresse, Passwort im Klartext (einmalig)
- Laravel Mailable, Templates in DE und EN

**API-Key:**

- Einzelner Key in `.env` (`API_KEY=...`)
- Custom `ApiKeyMiddleware` prüft Header `X-API-Key`
- Kein Rotation-System — bei Bedarf manuell ändern

## Mehrsprachigkeit

- Laravel `lang/de/` und `lang/en/` Dateien
- Sprachumschalter (DE/EN) in der Navigation
- Präferenz in `users.locale` gespeichert
- Lookup-Tabellen: `name_de` / `name_en`, Anzeige je nach aktivem Locale
- E-Mail-Templates in beiden Sprachen

## Deployment

- All-Inkl. dedizierter Server, Subdomain `beschreiber.hkwi.auction`
- `public/` als Document Root der Subdomain
- `.env` mit DB-Credentials, SMTP-Daten, API-Key direkt auf dem Server
- `php artisan migrate` per SSH
- Composer-Dependencies lokal bauen und hochladen (falls kein Composer auf dem Server)
- Seeder für Lookup-Daten und initialen Admin-Account

## Laravel-Projektstruktur

```
app/
  Models/         User, Consignment, Lot, Category, CatalogType, CatalogPart
  Http/
    Controllers/
      Auth/       LoginController
      Admin/      UserController, ConsignmentController
      Describer/  ConsignmentController, LotController
      Api/        ConsignmentController, LotController, UserController
    Middleware/   RoleMiddleware, ApiKeyMiddleware, ConsignmentOpenMiddleware
  Mail/           CredentialsMail
resources/
  views/
    layouts/      app.blade.php
    auth/         login.blade.php
    admin/        users/, consignments/
    describer/    consignments/, lots/
    emails/       credentials.blade.php
  lang/
    de/           auth.php, messages.php, validation.php
    en/           auth.php, messages.php, validation.php
routes/
  web.php         Session-Auth-Routen
  api.php         API-Key-Routen
database/
  migrations/     Alle Tabellen
  seeders/        LookupSeeder, AdminSeeder
```
