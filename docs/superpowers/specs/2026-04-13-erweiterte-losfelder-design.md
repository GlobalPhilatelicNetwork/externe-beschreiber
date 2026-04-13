# Erweiterte Losfelder — Design Spec

> Erweiterung der Losbeschreibung um Mehrfach-Kategorien, Erhaltungssymbole, Destinationen,
> Groupingcategory, Provenance, Katalogeinträge, Verpackung, EPos und Losart.
> Inkl. API-Erweiterung für Lookup-CRUD und angepasste Los-Endpunkte.

## Übersicht der Änderungen

### Neue Felder auf lots-Tabelle

| Feld | Typ | Pflicht | Beschreibung |
|---|---|---|---|
| lot_type | enum: single, collection | ja | Losart (Einzellos/Sammlung) |
| grouping_category_id | FK, nullable | nein | Feinere Kategorie-Aufgliederung |
| provenance | text, nullable | nein | Herkunft/Vorbesitzer (HTML, mehrzeilig) |
| epos | string, nullable | nein | Referenznummer des Einlieferers |

### Entfernte Felder von lots-Tabelle

| Feld | Grund |
|---|---|
| category_id | Wird Mehrfachauswahl via Pivot-Tabelle |
| catalog_type_id | Wird Mehrfach via lot_catalog_entries |
| catalog_number | Wird Mehrfach via lot_catalog_entries |

### Geänderte Felder

| Feld | Änderung |
|---|---|
| description | Wird von text zu HTML (Inline-Styling via HTML-Editor) |

## Datenmodell

### Pivot-Tabellen (Mehrfachauswahl)

**lot_category**

| Feld | Typ |
|---|---|
| lot_id | FK -> lots, cascade delete |
| category_id | FK -> categories |

Unique-Constraint auf (lot_id, category_id). Mindestens 1 Kategorie pro Los.

**lot_condition**

| Feld | Typ |
|---|---|
| lot_id | FK -> lots, cascade delete |
| condition_id | FK -> conditions |

Unique-Constraint auf (lot_id, condition_id). Mindestens 1 Erhaltung pro Los.

**lot_destination**

| Feld | Typ |
|---|---|
| lot_id | FK -> lots, cascade delete |
| destination_id | FK -> destinations |

Unique-Constraint auf (lot_id, destination_id). Optional.

### Detail-Tabellen (1:N mit eigenen Daten)

**lot_catalog_entries**

| Feld | Typ | Beschreibung |
|---|---|---|
| id | PK | |
| lot_id | FK -> lots, cascade delete | |
| catalog_type_id | FK -> catalog_types | Katalogtyp (Dropdown) |
| catalog_number | string | Katalognummer |

**lot_packages**

| Feld | Typ | Beschreibung |
|---|---|---|
| id | PK | |
| lot_id | FK -> lots, cascade delete | |
| pack_type_id | FK -> pack_types | Packtype (Dropdown) |
| pack_number | string | Packnummer |
| pack_note | string, nullable | Bemerkung zum Packstück |

### Neue Lookup-Tabellen

**conditions** (12 feste Symbole, per Seeder befüllt)

| Feld | Typ | Beschreibung |
|---|---|---|
| id | PK | |
| name | string | Kurzbezeichnung (z.B. "**", "*", "o") |
| image | string, nullable | Pfad zum Bild. Fallback auf name wenn leer |
| circuit_id | string | Referenz-ID zum externen System |

**grouping_categories** (per API befüllbar)

| Feld | Typ | Beschreibung |
|---|---|---|
| id | PK | |
| name_de | string | Deutscher Name |
| name_en | string | Englischer Name |

**destinations** (per API befüllbar)

| Feld | Typ | Beschreibung |
|---|---|---|
| id | PK | |
| name_de | string | Deutscher Name |
| name_en | string | Englischer Name |

**pack_types** (feste Liste, per Seeder)

| Feld | Typ | Beschreibung |
|---|---|---|
| id | PK | |
| name_de | string | Deutscher Name |
| name_en | string | Englischer Name |

### Bestehende Tabellen — Änderungen

**categories** — Keine Schema-Änderung. Bekommt API-CRUD-Endpunkte.

**catalog_types** — Keine Schema-Änderung. Bekommt API-CRUD-Endpunkte. Wird im Formular als Dropdown (nicht Filter-as-you-type) verwendet.

## Formular-Layout

Exaktes Layout, Zeile für Zeile (keine Zwischenüberschriften):

**Zeile 1** (2/3 + 1/3): Kategorien (Mehrfach-Filter-as-you-type mit Chips) + Losart (Radio: Einzellos/Sammlung)

**Zeile 2** (2/3 + 1/3): Gruppe/Groupingcategory (Filter-as-you-type, Einfachauswahl) + Startpreis (EUR)

**Zeile 3** (100%): Losbeschreibung (HTML-Editor mit Toolbar: B, I, U, S, H2, Liste. 6 Zeilen initial)

**Zeile 4** (100%): Provenance (HTML-Editor mit Toolbar: B, I, U, S. 6 Zeilen initial)

**Zeile 5** (Label links, Buttons rechts): Erhaltung (12 flache Buttons, Toggle-Auswahl, Bild wenn vorhanden sonst Name. Min. 1 Pflicht)

**Zeile 6** (2/3 + 1/3): Destination (Mehrfach-Filter-as-you-type mit Chips) + EPos (Textfeld, optional)

**Zeile 7** (50% + 50%): Katalogeinträge (dynamisch: Katalogtyp-Dropdown + Katalognummer + Löschen, "+Katalogeintrag"-Button) + Verpackung (dynamisch einzeilig: Packtype-Dropdown + Nr. + Bemerkung + Löschen, "+Packstück"-Button)

**Zeile 8** (100%): Bemerkung (einzeilig, intern, optional)

**Buttons:** Abbrechen + Speichern & Nächstes

**Card-Header:** Lila, zeigt Lfd.Nr. + Einlieferer-Nummer + NID + Katalogpart.

Referenz-HTML: .superpowers/brainstorm/440-1776090335/content/formular-v6.html

## Losart

Feste Enum-Liste, nicht per API änderbar:

| Wert | Label DE | Label EN |
|---|---|---|
| single | Einzellos | Single Lot |
| collection | Sammlung | Collection |

## HTML-Editor

Für Losbeschreibung und Provenance wird ein Inline-HTML-Editor verwendet. Erlaubte Formatierungen:

- **Bold** (B)
- *Italic* (I)
- Underline (U)
- Strikethrough (S)
- H2-Überschrift (nur bei Losbeschreibung)
- Aufzählungsliste (nur bei Losbeschreibung)

Die Felder speichern HTML als String. Die API liefert und akzeptiert HTML.

Technisch: Livewire-kompatibel, z.B. Trix-Editor oder ein leichtgewichtiger contenteditable-Wrapper. Kein komplexes Framework wie CKEditor nötig.

## API-Erweiterung

### Neue Lookup-CRUD-Endpunkte

Alle unter /api/v1, authentifiziert mit X-API-Key.

**Voller CRUD (GET/POST/PUT/DELETE):**

- /api/v1/categories
- /api/v1/catalog-types
- /api/v1/grouping-categories
- /api/v1/destinations

Jeder Endpunkt:
- GET / — Liste aller Einträge
- POST / — Neuen Eintrag erstellen (name_de, name_en)
- PUT /{id} — Eintrag aktualisieren
- DELETE /{id} — Eintrag löschen (nur wenn nicht in Verwendung)

**Nur lesen (GET):**

- /api/v1/conditions — 12 Erhaltungssymbole (id, name, image, circuit_id)
- /api/v1/pack-types — Packtypen (id, name_de, name_en)
- /api/v1/lot-types — Losarten (Werte: single, collection mit Labels)

### Geänderte Los-Endpunkte

**GET /api/v1/consignments/{id}/lots und GET /api/v1/lots**

Response enthält neue verschachtelte Objekte:

```
{
  "id": 1,
  "consignment_id": 1,
  "sequence_number": 1,
  "lot_type": "single",
  "description": "<b>HTML</b> Beschreibung",
  "provenance": "Sammlung Müller",
  "epos": "E-123",
  "starting_price": "150.00",
  "notes": "intern",
  "grouping_category": { "id": 1, "name_de": "...", "name_en": "..." },
  "categories": [
    { "id": 1, "name_de": "Briefmarken", "name_en": "Stamps" }
  ],
  "conditions": [
    { "id": 1, "name": "**", "image": null, "circuit_id": "C001" }
  ],
  "destinations": [
    { "id": 1, "name_de": "Deutschland", "name_en": "Germany" }
  ],
  "catalog_entries": [
    { "id": 1, "catalog_type": { "id": 1, "name_de": "Michel" }, "catalog_number": "1-3" }
  ],
  "packages": [
    { "id": 1, "pack_type": { "id": 1, "name_de": "Karton" }, "pack_number": "14", "pack_note": "Oben links" }
  ]
}
```

**POST /api/v1/consignments/{id}/lots** (wenn künftig Los-Erstellung per API nötig)

Request-Body:

```
{
  "lot_type": "single",
  "category_ids": [1, 2],
  "grouping_category_id": 1,
  "condition_ids": [1, 3],
  "destination_ids": [1],
  "description": "<b>HTML</b> Text",
  "provenance": "optional",
  "epos": "E-123",
  "starting_price": 150.00,
  "notes": "optional",
  "catalog_entries": [
    { "catalog_type_id": 1, "catalog_number": "1-3" },
    { "catalog_type_id": 2, "catalog_number": "A1-A3" }
  ],
  "packages": [
    { "pack_type_id": 1, "pack_number": "14", "pack_note": "Oben links" }
  ]
}
```

## Validierungsregeln

### Los erstellen/bearbeiten

| Feld | Regel |
|---|---|
| lot_type | Pflicht, enum: single, collection |
| category_ids | Pflicht, Array, min 1, jede ID muss in categories existieren |
| grouping_category_id | Optional, muss in grouping_categories existieren |
| condition_ids | Pflicht, Array, min 1, jede ID muss in conditions existieren |
| destination_ids | Optional, Array, jede ID muss in destinations existieren |
| description | Pflicht, String (HTML erlaubt) |
| provenance | Optional, String (HTML erlaubt) |
| epos | Optional, String, max 255 |
| starting_price | Pflicht, numeric, min 0 |
| notes | Optional, String, max 255 |
| catalog_entries | Optional, Array von Objekten |
| catalog_entries.*.catalog_type_id | Pflicht pro Eintrag, muss in catalog_types existieren |
| catalog_entries.*.catalog_number | Pflicht pro Eintrag, String, max 255 |
| packages | Optional, Array von Objekten |
| packages.*.pack_type_id | Pflicht pro Eintrag, muss in pack_types existieren |
| packages.*.pack_number | Pflicht pro Eintrag, String, max 255 |
| packages.*.pack_note | Optional pro Eintrag, String, max 255 |

## Migration bestehender Daten

Die bestehende Datenbank hat aktuell keine Produktionsdaten (nur Testdaten). Die Migration kann destruktiv sein:

1. Neue Tabellen erstellen (conditions, grouping_categories, destinations, pack_types, lot_category, lot_condition, lot_destination, lot_catalog_entries, lot_packages)
2. Neue Spalten zu lots hinzufügen (lot_type, grouping_category_id, provenance, epos)
3. Alte Spalten von lots entfernen (category_id, catalog_type_id, catalog_number)
4. description bleibt als text-Spalte (HTML wird als String gespeichert)

## Betroffene bestehende Dateien

### Muss geändert werden:

- app/Models/Lot.php — neue Relationships, fillable, entfernte Felder
- app/Livewire/LotForm.php — komplett überarbeiten für neues Layout
- resources/views/livewire/lot-form.blade.php — komplett überarbeiten
- resources/views/describer/lots/edit.blade.php — alle neuen Felder
- resources/views/describer/consignments/show.blade.php — Tabelle anpassen
- app/Http/Requests/StoreLotRequest.php — neue Validierungsregeln
- app/Http/Requests/UpdateLotRequest.php — neue Validierungsregeln
- app/Http/Controllers/Describer/LotController.php — Pivot-Sync, Detail-Tabellen
- app/Http/Controllers/Api/LotController.php — neue Felder in Response/Request
- routes/api.php — neue Lookup-Endpunkte
- database/factories/LotFactory.php — anpassen
- resources/lang/de/messages.php — neue Übersetzungen
- resources/lang/en/messages.php — neue Übersetzungen
- tests/ — alle Lot-bezogenen Tests anpassen

### Neue Dateien:

- Migrationen (9+ Dateien)
- Models: Condition, GroupingCategory, Destination, PackType, LotCatalogEntry, LotPackage
- Factories: für alle neuen Models
- Seeders: ConditionSeeder, PackTypeSeeder
- API Controller: LookupController (oder je einer pro Lookup)
- Tests: Lookup-API-Tests, erweiterte Lot-Tests
