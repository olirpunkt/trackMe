# TrackME - Requirements Spezifikation v1.0
**Datum:** 11. Februar 2026  
**Status:** Final für MVP-Entwicklung

---

## 1. Projektübersicht

### 1.1 Kurzbeschreibung
TrackME ist eine responsive Webapplikation zur täglichen Dokumentation von Allergiesymptomen. Die Anwendung ermöglicht eine strukturierte Erfassung von Symptomen, Wetterbedingungen, Medikamenteneinnahme und allgemeiner Verfassung mit anschließender Auswertung und Export-Funktionalität.

### 1.2 Zielgruppe
- **Primär:** Einzelnutzer (Single-User-Installation)
- **Verwendungszweck:** Persönliche Gesundheitsdokumentation, keine öffentliche Nutzung
- **Datenschutz:** Alle Daten bleiben auf eigenem Server (kein Cloud-Service)

### 1.3 Technische Basis
- **Backend:** PHP 8.5.2
- **Datenbank:** MariaDB
- **Frontend:** HTML5, Tailwind CSS (via CDN), Alpine.js
- **Charts:** Chart.js (Post-MVP)
- **Deployment:** Gehosteter Webspace (kein Docker/Container)
- **Optional:** Python/Ruby/Perl per CGI für spezielle Aufgaben

---

## 2. Funktionale Anforderungen

### 2.1 MVP (Minimum Viable Product) - Version 1.0

#### ✅ Pflichtfeatures für MVP:
1. Symptomerfassung
2. Wettererfassung
3. Ortseingabe (manuell, Stadt-Ebene)
4. Gesamtverfassung (Daily Mood)
5. Medikamentenverwaltung und -protokollierung
6. Einfache Tabellenauswertung
7. Pollenflugkalender (statisch)
8. CSV/JSON-Export

#### 🔮 Post-MVP Features (Version 1.1+):
- Graphische Auswertung (Liniendiagramme)
- DWD-Pollenflugdaten (API-Abruf)
- Geolocation-Button (automatische Ortserkennung)
- Progressive Web App (PWA) Funktionalität
- Offline-Fähigkeit

---

### 2.2 Datenerfassung

#### 2.2.1 Tageseintrag (Core Entity)
Ein Tageseintrag repräsentiert einen Tag und enthält:
- **Datum** (Pflicht)
- **Ort** (Stadt, Freitext, Pflicht)
- **Wetter** (Optional)
- **Gesamtverfassung** (Optional)
- **Freitext-Anmerkung** (Optional, max. 1000 Zeichen)

**Besonderheiten:**
- **Mehrfacherfassung pro Tag:** Möglich (z.B. morgens/abends unterschiedliche Symptome)
- **Retrospektive Erfassung:** Unbegrenzt möglich
- **Änderung bestehender Daten:** Jederzeit möglich

#### 2.2.2 Symptome

**Datenmodell:**
- **Organ/Körperteil** (z.B. Nase, Augen, Haut)
- **Symptom** (z.B. Niesen, Jucken)
- **Schweregrad** (0-3 Skala)

**Standard-Symptome (vordefiniert, anpassbar):**

| Körperteil | Symptome |
|------------|----------|
| Nase | Niesen, Schnupfen, verstopfte Nase |
| Augen | Jucken, Tränen, Rötung |
| Rachen | Kratzen, Schluckbeschwerden |
| Atemwege | Husten, Atemnot, Engegefühl |
| Haut | Jucken, Ausschlag, Rötung |

**Symptomskala:**

| Stufe | Bezeichnung | UI-Darstellung |
|-------|-------------|----------------|
| 0 | Keine | Grau/Inaktiv |
| 1 | Leicht | Gelb |
| 2 | Mittel | Orange |
| 3 | Stark | Rot |

**User Stories:**
- Als Nutzer kann ich **eigene Organe definieren** (z.B. "Magen-Darm")
- Als Nutzer kann ich **eigene Symptome hinzufügen** (z.B. "Kopfschmerzen")
- Als Nutzer kann ich **Standard-Symptome ausblenden**, die ich nicht brauche
- Als Nutzer kann ich pro Tag **mehrere Symptom-Einträge** erfassen

#### 2.2.3 Gesamtverfassung (Daily Mood)

**5-Stufen-Skala:**

| Stufe | Bezeichnung | Emoji | UI-Farbe |
|-------|-------------|-------|----------|
| 1 | Sehr schlecht | 😫 | Dunkelrot |
| 2 | Schlecht | 😕 | Orange |
| 3 | Mittel | 😐 | Gelb |
| 4 | Gut | 🙂 | Hellgrün |
| 5 | Sehr gut | 😊 | Grün |

**Eingabe:** Schnell-Auswahl (große Emoji-Buttons für Touch/Maus)

#### 2.2.4 Wetter

**Wetterzustand (Einfachauswahl):**

| Option | Icon | Wert |
|--------|------|------|
| Sonne | ☀️ | sunny |
| Bewölkt | ☁️ | cloudy |
| Regen | 🌧️ | rainy |
| Schnee | ❄️ | snowy |

**Temperatur:**
- **Eingabefeld:** Numerisch
- **Wertebereich:** -40°C bis +50°C
- **Datentyp:** Integer (Ganzzahl)
- **Validation:** Client-seitig + Server-seitig

**Optional (Post-MVP):**
- Automatischer Abruf von Wetterdaten via API (z.B. OpenWeather)

#### 2.2.5 Medikamente

**Medikamenten-Stammdaten (einmalig anlegen):**
- **Name** (Pflicht, z.B. "Cetirizin")
- **Dosierung** (Optional, z.B. "10mg")
- **Freitext-Notiz** (Optional, z.B. "Bei Bedarf")

**Einnahme-Protokollierung:**
- **Datum:** An welchem Tag
- **Eingenommen:** Ja/Nein (Boolean)
- **Uhrzeit:** NICHT im MVP (nur "heute eingenommen")

**User Stories:**
- Als Nutzer kann ich **mehrere Medikamente anlegen**
- Als Nutzer kann ich pro Tag **mehrere Medikamente** als eingenommen markieren
- Als Nutzer kann ich **Medikamente bearbeiten/löschen**

#### 2.2.6 Ortserfassung

**MVP:**
- **Manuell:** Freitexteingabe (Stadt-Name)
- **Granularität:** Stadt-Ebene ausreichend (z.B. "Stuttgart")

**Post-MVP:**
- **Geolocation-Button:** Automatische Ortserkennung via Browser Geolocation API
- **Reverse Geocoding:** Koordinaten → Stadt-Name

**Verwendung:**
- Anzeige in Tabellenauswertung
- Filter in Auswertungen (z.B. "Symptome in Berlin vs. München")
- Pollenflugdaten-Zuordnung

---

### 2.3 Pollenflugdaten

#### 2.3.1 MVP-Lösung: Statischer Pollenflugkalender

**Quelle:** 
- Pollenflugkalender vom Deutschen Wetterdienst (DWD)
- Stiftung Deutscher Polleninformationsdienst (PID)

**Pollenarten (alle vom DWD angebotenen):**
- Erle, Birke, Gräser, Roggen, Beifuß, Ambrosia, Hasel, Esche, etc.

**Datenhaltung:**
- **Format:** JSON oder SQL-Tabelle
- **Struktur:** 
  ```json
  {
    "month": 4,
    "pollenType": "Birke",
    "intensityLevel": "hoch"
  }
  ```
- **Update:** Einmal jährlich manuell aktualisieren

**Darstellung:**
- In Tabellenauswertung: Spalte "Pollenflug" zeigt relevante Pollen des Tages

#### 2.3.2 Post-MVP: API-Integration

**Option 1: DWD Geofachdaten (kostenfrei)**
- URL: `https://opendata.dwd.de/`
- **Vorteil:** Offizielle Messwerte
- **Nachteil:** Komplex, nicht immer tagesaktuell
- **Umsetzung:** PHP-Script holt Daten täglich, cached in DB

**Option 2: OpenWeather Pollen API**
- **Vorteil:** Einfach, gut dokumentiert
- **Nachteil:** Kostet ~40€/Monat für tägliche Abfragen
- **Nicht empfohlen** für Single-User

**Option 3: Pollenflug-Vorhersage (Übergangslösung)**
- Nutze Vorhersagedaten, ergänze später mit echten Messwerten
- Mehrere Quellen kombinierbar

**Retrospektive Pollenflug-Ergänzung:**
- Admin-Funktion: CSV-Import für historische Daten
- Script zum Nachladen von DWD-Archivdaten

---

### 2.4 Auswertung & Visualisierung

#### 2.4.1 MVP: Tabellenauswertung

**Struktur:**
- **Spalten:** Datum, Ort, Wetter, Stimmung, Symptome (gruppiert), Medikamente, Pollen
- **Filter:** Datumsbereich (von-bis)
- **Sortierung:** Nach Datum (auf-/absteigend)
- **Responsive:** Mobile horizontal scrollbar

**Beispiel-Tabelle:**

| Datum | Ort | Wetter | Stimmung | Symptome | Medikamente | Pollen |
|-------|-----|--------|----------|----------|-------------|--------|
| 10.02.26 | Stuttgart | ☀️ 18°C | 😊 5 | Nase(1), Augen(2) | Cetirizin | Birke: hoch |

#### 2.4.2 Post-MVP: Graphische Auswertung

**Chart-Typ:** Liniendiagramm (Chart.js)

**X-Achse:** Datum (skalierbar: Woche → Monat → Jahr)

**Y-Achsen:**
- **Links:** Symptom-Schweregrade (0-3), Stimmung (1-5)
- **Rechts:** Pollenflug-Intensität, Temperatur

**Features:**
- **Mehrere Kurven gleichzeitig:** Symptome + Wetter + Pollenflug überlagert
- **Interaktivität:** 
  - Zoom (Zeitraum-Auswahl)
  - Tooltip beim Hovern (Detailwerte)
  - Kurven ein-/ausblenden (Legende klickbar)
- **Farbschema:** Symptome farbcodiert nach Organ

**Filteroptionen:**
- Datumsbereich
- Symptom-Auswahl (Multiselect)
- Ort-Filter (wenn mehrere Orte erfasst)

---

### 2.5 Export-Funktionalität

#### 2.5.1 CSV-Export

**Struktur:**
```csv
Datum,Ort,Wetter,Temperatur,Stimmung,Organ,Symptom,Schweregrad,Medikament,Pollen_Birke,Pollen_Gräser,Anmerkung
2026-02-10,Stuttgart,sunny,18,5,Nase,Schnupfen,2,Cetirizin,hoch,mittel,"Morgens schlimmer"
```

**Features:**
- Vollständiger Datenexport (alle Einträge)
- Datumsbereich-Filter
- Encoding: UTF-8 mit BOM (Excel-kompatibel)

#### 2.5.2 JSON-Export

**Struktur:**
```json
{
  "export_date": "2026-02-11T10:30:00Z",
  "entries": [
    {
      "date": "2026-02-10",
      "location": "Stuttgart",
      "weather": {
        "condition": "sunny",
        "temperature": 18
      },
      "mood": 5,
      "symptoms": [
        {"organ": "Nase", "symptom": "Schnupfen", "severity": 2}
      ],
      "medications": ["Cetirizin"],
      "pollen": {"Birke": "hoch", "Gräser": "mittel"},
      "notes": "Morgens schlimmer"
    }
  ]
}
```

**Verwendung:**
- Import in andere Systeme
- Backup-Funktion

---

## 3. Nicht-funktionale Anforderungen

### 3.1 Usability

**Design-Prinzipien:**
- **Schlicht & Modern:** Keine verspielte Ästhetik, professionelles Design
- **Schnell-Eingabe:** Optimiert für tägliche Nutzung (< 2 Minuten pro Eintrag)
- **Touch-optimiert:** Große Buttons, keine Miniatur-UI-Elemente
- **Maus-kompatibel:** Auch am Desktop effizient bedienbar

**Responsive Design:**
- **Mobile First:** Primäre Entwicklung für Smartphone
- **Desktop:** Gleichwertig nutzbar (2-Spalten-Layout)
- **Breakpoints:** 
  - Mobile: < 640px
  - Tablet: 640px - 1024px
  - Desktop: > 1024px

### 3.2 Performance

**Ladezeiten:**
- **Initiales Laden:** < 2 Sekunden
- **Datenbank-Queries:** < 500ms
- **Chart-Rendering:** < 1 Sekunde (Post-MVP)

**Optimierungen:**
- Lazy Loading für Tabellen (Pagination)
- Gecachte Pollenflugdaten
- Minimiertes CSS/JS (Post-MVP: Build-Prozess)

### 3.3 Sicherheit & Datenschutz

**Single-User-Spezifika:**
- **Kein Login-System nötig** (Server ist privat)
- Optional: `.htaccess` Basic Auth als Basis-Schutz
- **DSGVO:** Nicht relevant (keine Drittnutzung)

**Datenintegrität:**
- Input-Validierung (Client + Server)
- SQL-Injection-Schutz (Prepared Statements)
- XSS-Schutz (HTML-Escaping)

### 3.4 Progressive Web App (PWA) - Post-MVP

**Features:**
- **Installierbar:** Add to Homescreen (Android/iOS/Desktop)
- **App-Icon:** Custom Icon für Homescreen
- **Offline-Fähigkeit:** Service Worker cached HTML/CSS/JS
- **Manifest.json:** PWA-Metadaten

**Nicht im MVP:**
- Offline-Dateneingabe (Sync bei Online-Status)

---

## 4. Datenmodell (Datenbank-Schema)

### 4.1 ER-Diagramm (Konzept)

```
┌─────────────────┐       ┌──────────────────┐
│  daily_entries  │───────│  symptom_logs    │
│  (Tageseinträge)│ 1:N   │  (Symptome)      │
└─────────────────┘       └──────────────────┘
        │
        │ 1:N
        ↓
┌─────────────────┐
│ medication_logs │
│ (Einnahmen)     │
└─────────────────┘
        │
        │ N:1
        ↓
┌─────────────────┐
│   medications   │
│  (Stammdaten)   │
└─────────────────┘

┌─────────────────┐
│  symptom_types  │
│  (Definitionen) │
└─────────────────┘

┌─────────────────┐
│  pollen_data    │
│  (Kalender)     │
└─────────────────┘
```

### 4.2 Tabellen-Definitionen

#### Tabelle: `daily_entries`
| Feld | Typ | Beschreibung |
|------|-----|--------------|
| id | INT AUTO_INCREMENT PRIMARY KEY | Eindeutige ID |
| date | DATE NOT NULL | Datum (YYYY-MM-DD) |
| time_of_day | ENUM('morning', 'afternoon', 'evening') | Tageszeit (für Mehrfacheinträge) |
| location | VARCHAR(255) NOT NULL | Ort (Stadt) |
| weather_condition | ENUM('sunny', 'cloudy', 'rainy', 'snowy') | Wetterzustand |
| temperature | INT | Temperatur in °C |
| mood | TINYINT(1) | Stimmung (1-5) |
| notes | TEXT | Freitext-Anmerkung |
| created_at | TIMESTAMP DEFAULT CURRENT_TIMESTAMP | Erstellungszeitpunkt |
| updated_at | TIMESTAMP ON UPDATE CURRENT_TIMESTAMP | Änderungszeitpunkt |

**Index:** UNIQUE(date, time_of_day) für Mehrfacheinträge

#### Tabelle: `symptom_types`
| Feld | Typ | Beschreibung |
|------|-----|--------------|
| id | INT AUTO_INCREMENT PRIMARY KEY | Eindeutige ID |
| organ | VARCHAR(100) NOT NULL | Organ/Körperteil |
| symptom_name | VARCHAR(100) NOT NULL | Symptom-Name |
| is_default | BOOLEAN DEFAULT 0 | Standard-Symptom |
| display_order | INT | Sortierreihenfolge |
| created_at | TIMESTAMP | Erstellungszeitpunkt |

**Index:** UNIQUE(organ, symptom_name)

#### Tabelle: `symptom_logs`
| Feld | Typ | Beschreibung |
|------|-----|--------------|
| id | INT AUTO_INCREMENT PRIMARY KEY | Eindeutige ID |
| daily_entry_id | INT NOT NULL | FK → daily_entries.id |
| symptom_type_id | INT NOT NULL | FK → symptom_types.id |
| severity | TINYINT(1) | Schweregrad (0-3) |
| created_at | TIMESTAMP | Erstellungszeitpunkt |

**Foreign Keys:**
- `daily_entry_id` → `daily_entries(id)` ON DELETE CASCADE
- `symptom_type_id` → `symptom_types(id)` ON DELETE RESTRICT

#### Tabelle: `medications`
| Feld | Typ | Beschreibung |
|------|-----|--------------|
| id | INT AUTO_INCREMENT PRIMARY KEY | Eindeutige ID |
| name | VARCHAR(255) NOT NULL | Medikamenten-Name |
| dosage | VARCHAR(100) | Dosierung (z.B. "10mg") |
| notes | TEXT | Freitext-Notizen |
| created_at | TIMESTAMP | Erstellungszeitpunkt |

#### Tabelle: `medication_logs`
| Feld | Typ | Beschreibung |
|------|-----|--------------|
| id | INT AUTO_INCREMENT PRIMARY KEY | Eindeutige ID |
| daily_entry_id | INT NOT NULL | FK → daily_entries.id |
| medication_id | INT NOT NULL | FK → medications.id |
| taken | BOOLEAN DEFAULT 1 | Eingenommen (immer true) |
| created_at | TIMESTAMP | Erstellungszeitpunkt |

**Foreign Keys:**
- `daily_entry_id` → `daily_entries(id)` ON DELETE CASCADE
- `medication_id` → `medications(id)` ON DELETE RESTRICT

#### Tabelle: `pollen_data` (MVP: statisch)
| Feld | Typ | Beschreibung |
|------|-----|--------------|
| id | INT AUTO_INCREMENT PRIMARY KEY | Eindeutige ID |
| pollen_type | VARCHAR(50) NOT NULL | Pollenart (z.B. "Birke") |
| month | TINYINT(2) NOT NULL | Monat (1-12) |
| intensity | ENUM('none', 'low', 'medium', 'high') | Intensität |

**Index:** UNIQUE(pollen_type, month)

---

## 5. Technische Architektur

### 5.1 Projekt-Struktur (MVC-Pattern)

```
/trackme/
├── index.php              # Entry Point
├── config/
│   └── database.php       # DB-Verbindung
├── controllers/
│   ├── DailyEntryController.php
│   ├── SymptomController.php
│   ├── MedicationController.php
│   └── ExportController.php
├── models/
│   ├── DailyEntry.php
│   ├── Symptom.php
│   └── Medication.php
├── views/
│   ├── layout.php         # Master-Layout (Header/Footer)
│   ├── dashboard.php      # Hauptseite (Schnell-Eingabe)
│   ├── history.php        # Tabellenauswertung
│   ├── settings.php       # Symptom-/Medikamenten-Verwaltung
│   └── components/        # Reusable UI-Komponenten
├── public/
│   ├── css/
│   │   └── app.css        # Custom Styles (zusätzlich zu Tailwind)
│   └── js/
│       ├── app.js         # Alpine.js Komponenten
│       └── chart.js       # Chart-Logik (Post-MVP)
├── api/                   # REST-API Endpoints (optional)
│   └── entries.php
└── sql/
    └── schema.sql         # DB-Setup-Script
```

### 5.2 Routing (Einfach, ohne Framework)

**Ansatz:** Query-Parameter basiert
```php
// index.php
$page = $_GET['page'] ?? 'dashboard';

switch($page) {
    case 'dashboard': include 'views/dashboard.php'; break;
    case 'history': include 'views/history.php'; break;
    case 'settings': include 'views/settings.php'; break;
    case 'export': include 'controllers/ExportController.php'; break;
    default: http_response_code(404);
}
```

**Alternative (cleaner):** Flight PHP Micro-Framework
```php
Flight::route('/', function() {
    Flight::render('dashboard');
});
```

### 5.3 Frontend-Stack

**HTML/CSS:**
- Tailwind CSS via CDN (keine Build-Tools für MVP)
- Custom CSS nur für spezielle Anpassungen

**JavaScript:**
- **Alpine.js:** Reaktive UI-Komponenten (z.B. Symptom-Slider)
- **Vanilla JS:** Form-Validierung, AJAX-Requests
- **Chart.js:** Diagramme (Post-MVP)

**Beispiel Alpine.js Komponente:**
```html
<div x-data="{ severity: 0 }">
  <input type="range" min="0" max="3" x-model="severity">
  <span x-text="['Keine', 'Leicht', 'Mittel', 'Stark'][severity]"></span>
</div>
```

### 5.4 API-Design (Optional für AJAX)

**Endpunkt:** `/api/entries.php`

**POST /api/entries** - Create
```json
{
  "date": "2026-02-10",
  "location": "Stuttgart",
  "mood": 5,
  "symptoms": [{"type_id": 1, "severity": 2}]
}
```

**GET /api/entries?from=2026-01-01&to=2026-02-10** - Read

**PUT /api/entries/{id}** - Update

**DELETE /api/entries/{id}** - Delete

---

## 6. Entwicklungs-Roadmap

### Phase 1: MVP Development (4-6 Wochen)

**Woche 1-2: Backend Foundation**
- [ ] Datenbank-Setup (Schema erstellen)
- [ ] Model-Klassen (CRUD-Operationen)
- [ ] Seed-Data (Standard-Symptome)
- [ ] Basic Routing

**Woche 3-4: UI & Eingabe**
- [ ] Dashboard-View (Schnell-Eingabe)
- [ ] Symptom-Auswahl (Alpine.js Komponenten)
- [ ] Medikamenten-Verwaltung
- [ ] Form-Validierung

**Woche 5-6: Auswertung & Export**
- [ ] Tabellenauswertung
- [ ] Filter-Funktionalität
- [ ] CSV/JSON-Export
- [ ] Pollenflugkalender (statisch)

**Testing & Bugfixes:**
- [ ] Cross-Browser-Testing (Chrome, Firefox, Safari)
- [ ] Responsive Testing (Mobile/Desktop)
- [ ] Usability-Test

### Phase 2: Post-MVP (optional)

**Features:**
- [ ] Chart.js-Integration (Liniendiagramme)
- [ ] DWD-Pollenflug-API
- [ ] Geolocation-Button
- [ ] PWA-Manifest + Service Worker
- [ ] Offline-Fähigkeit

---

## 7. Testing-Strategie

### 7.1 Manuelle Tests (MVP)
- [ ] Tägliche Eingabe-Flow (< 2 Min.)
- [ ] Retrospektive Erfassung (1 Monat zurück)
- [ ] Änderung bestehender Einträge
- [ ] Export-Funktionalität (CSV öffnet in Excel)
- [ ] Responsive Design (iPhone, iPad, Desktop)

### 7.2 Automatisierte Tests (Post-MVP)
- PHPUnit für Model-Tests
- Selenium für UI-Tests

---

## 8. Open Questions & Entscheidungen

### 8.1 Geklärt ✅
- ✅ Single-User (kein Multi-Tenancy)
- ✅ Mehrfacheinträge pro Tag möglich
- ✅ Kein Login-System nötig
- ✅ Tailwind CSS + Alpine.js
- ✅ Pollenflugkalender für MVP
- ✅ Tabellen-Auswertung zuerst, Graphen später

### 8.2 Noch zu klären ❓
- **Backup-Strategie:** Manuell oder automatisch?
- **Farbschema:** Spezifische Farben/Präferenzen?
- **Pollenflugdaten-Update:** Wer pflegt den Kalender?
- **Medikamenten-Historie:** Sollen alte Medikamente archiviert oder gelöscht werden?

---

## 9. Anhang

### 9.1 Referenzen

**Pollenflug-Datenquellen:**
- DWD OpenData: https://opendata.dwd.de/
- Stiftung Deutscher Polleninformationsdienst: https://www.pollenstiftung.de/
- Pollenflug.de (Kalender): https://www.pollenflug.de/

**Technologie-Dokumentation:**
- Tailwind CSS: https://tailwindcss.com/
- Alpine.js: https://alpinejs.dev/
- Chart.js: https://www.chartjs.org/
- Flight PHP: https://flightphp.com/

**Design-Inspiration:**
- Bearable App (UI-Patterns)
- Google Fit (Schnell-Eingabe)

### 9.2 Glossar

| Begriff | Definition |
|---------|------------|
| MVP | Minimum Viable Product - minimale lauffähige Version |
| PWA | Progressive Web App - installierbare Webapp |
| CRUD | Create, Read, Update, Delete - Basis-Operationen |
| DWD | Deutscher Wetterdienst |
| API | Application Programming Interface |
| FK | Foreign Key (Fremdschlüssel) |

---

## 10. Änderungshistorie

| Version | Datum | Änderungen |
|---------|-------|------------|
| 1.0 | 11.02.2026 | Initiale Version basierend auf Requirement-Diskussion |

---

**Erstellt von:** Claude (Anthropic)  
**Auftraggeber:** TrackME-Entwickler  
**Nächster Schritt:** Datenbank-Schema erstellen + Projekt-Setup
