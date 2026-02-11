# TrackME
## Kurzbeschreibung
TrackME ist eine responsive Webapplikation zur täglichen Dokumentation von Symptomen. In der ersten Ausbaustufe handelt es sich um Allergiesymptome, spätere Ausweitung auf andere Symptome oder allgemein zu trackende Artefakte ist nicht ausgeschlossen. In dieser Version konzentrieren wir uns auf Allergiesymptome

## Detailliertere Beschreibung
Symptome können taggenau erfasst werden. Eine retrospektive Erfassung ist möglich, ebenso die Änderung bereits eingegebener Daten.
Außer Symptomen können die allgemeine Verfassung des Tages sowie das Wetter erfasst werden. Details siehe weiter unten. Freitextanmerkungen pro Tag sind möglich.
Der Ort der Erfassung kann manuell eingegeben werden. Sollte die Anwendung auf einem Device ausgeführt werden, das eine automatische Ortung erlaubt, kann diese dem Benutzer angeboten werden.

### Medikamente

Der Benutzer kann eigene Medikamente und Dosierungen definieren. Die Einnahme kann ebenfalls taggenau protokolliert werden.


### Auswertung 
Eine Auswertungsfunktionalität bereitet die eingegebenen Daten graphisch auf. Darin enthalten auf der x-Achse das Datum (skalierbar Woche -> Monat -> Jahr), auf den y-Achsen links und rechts Symptome, Wetter, Pollenflugdaten. Der Ort kann ebenfalls in die Auswertung einbezogen werden.

### Pollenflugdaten 

Die Pollenflugdaten können per Webservice vom deutschen Wetterdienst abgerufen werden. 

### Symptome

Der Anwender kann Organe und Symptome frei definieren. Pro Organ kann ein oder mehrere Symptome mit Schweregrad definiert werden.
Es ist ein Standardsatz an Organen (Augen, Nase, Haut, Hals, Lunge) vorhanden:

| Körperteil | Beschreibung |
|------------|--------------|
| Nase | Niesen, Schnupfen, verstopfte Nase |
| Augen | Jucken, Tränen, Rötung |
| Rachen | Kratzen, Schluckbeschwerden |
| Atemwege | Husten, Atemnot, Engegefühl |
| Haut | Jucken, Ausschlag, Rötung |

**Symptomskala**

| Stufe | Bezeichnung |
|-------|-------------|
| 0 | Keine |
| 1 | Leicht |
| 2 | Mittel |
| 3 | Stark |

### Gesamtverfassung

Die allgemeine Verfassung wird auf einer 5-stufigen Skala erfasst:

| Stufe | Bezeichnung | Emoji |
|-------|-------------|-------|
| 1 | Sehr schlecht | 😫 |
| 2 | Schlecht | 😕 |
| 3 | Mittel | 😐 |
| 4 | Gut | 🙂 |
| 5 | Sehr gut | 😊 |

### Wetter

**Wetterzustand (Einfachauswahl):**

| Option | Icon |
|--------|------|
| Sonne | ☀️ |
| Bewölkt | ☁️ |
| Regen | 🌧️ |
| Schnee | ❄️ |

**Temperatur:**
- Eingabefeld für Grad Celsius
- Wertebereich: -40°C bis +50°C
- Ganzzahlige Eingabe

### CSV-Export und JSON-Export

ein strukturierter Export der erfassten Daten ist möglich.