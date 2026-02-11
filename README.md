# 🩺 TrackME - Allergy Symptom Tracker

Webbasierte Applikation zur täglichen Dokumentation von Allergiesymptomen.

![Version](https://img.shields.io/badge/version-1.0_MVP-blue)
![PHP](https://img.shields.io/badge/PHP-8.3-purple)
![License](https://img.shields.io/badge/license-MIT-green)

---

## 📋 Features (MVP v1.0)

✅ **Symptomerfassung**
- 19 vordefinierte Symptome (5 Organe: Nase, Augen, Rachen, Atemwege, Haut)
- 0-3 Schweregrad-Skala
- Mehrfacheinträge pro Tag (Morgens/Mittags/Abends)

✅ **Zusatzdaten**
- Wetter & Temperatur
- Ortseingabe
- Gesamtverfassung (Mood 1-5)
- Medikamenten-Tracking
- Freitext-Notizen

✅ **Auswertung**
- Tabellenansicht aller Einträge
- CSV-Export (Excel-kompatibel)
- JSON-Export (für externe Tools)

✅ **Pollenkalender**
- Statische DWD-Daten für 8 Pollenarten

---

## 🚀 Installation

### Voraussetzungen
- PHP 8.3+ mit PDO MySQL Extension
- MariaDB 11+ oder MySQL 8+
- Apache/Nginx Webserver

### Schritt 1: Repository klonen

```bash
git clone <repository-url> trackme
cd trackme
```

### Schritt 2: Datenbank erstellen

```sql
CREATE DATABASE trackme CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Dann SQL-Schema importieren:
```bash
mysql -u root -p trackme < schema.sql
```

### Schritt 3: Konfiguration

```bash
# Database-Config aus Template erstellen
cp config/database.example.php config/database.php

# Credentials anpassen
nano config/database.php
```

Trage deine DB-Credentials ein:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'trackme');
define('DB_USER', 'dein_user');
define('DB_PASS', 'dein_passwort');
```

### Schritt 4: Webserver konfigurieren

**Apache:**
```apache
<VirtualHost *:80>
    DocumentRoot "/pfad/zu/trackme/public"
    <Directory "/pfad/zu/trackme/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

**Nginx:**
```nginx
server {
    root /pfad/zu/trackme/public;
    index index.php;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
    }
}
```

### Schritt 5: Fertig!

Öffne die App im Browser: `http://deine-domain.de`

---

## 🐳 Docker Development (Optional)

Für lokale Entwicklung mit Docker:

```bash
# docker-compose.yml erstellen (siehe Dokumentation)
docker-compose up -d

# Browser öffnen
open http://localhost:8080
```

---

## 📁 Projektstruktur

```
trackme/
├── config/
│   ├── database.php           # DB-Config (nicht in Git!)
│   └── database.example.php   # Template für DB-Config
├── views/
│   ├── dashboard.php          # Symptom-Eingabe
│   ├── history.php            # Verlaufs-Tabelle
│   ├── settings.php           # Einstellungen
│   └── export.php             # CSV/JSON Export
├── public/
│   ├── index.php              # Entry Point & Router
│   └── .htaccess              # Apache-Config
├── schema.sql                 # Datenbank-Schema
├── .gitignore
└── README.md
```

---

## 🔒 Sicherheit

⚠️ **WICHTIG:**
- `config/database.php` wird von Git ignoriert (enthält Credentials!)
- Für Production: `DEBUG_MODE` auf `false` setzen
- `.htaccess` verhindert Zugriff auf sensible Dateien

---

## 🛣️ Roadmap (Post-MVP)

- [ ] Chart.js Integration (Symptom-Verläufe)
- [ ] DWD Pollenflug-API Integration
- [ ] Pollenkalender-Widget auf Dashboard
- [ ] Geolocation-Button
- [ ] PWA-Features (Offline-Modus)
- [ ] Medikamente hinzufügen/bearbeiten
- [ ] Eigene Symptome definieren
- [ ] Filter & Suche im Verlauf
- [ ] Einträge bearbeiten/löschen

---

## 📊 Datenbank-Schema

**6 Tabellen:**
- `daily_entries` - Tageseinträge
- `symptom_types` - Symptom-Definitionen
- `symptom_logs` - Symptom-Erfassung
- `medications` - Medikamenten-Stammdaten
- `medication_logs` - Einnahme-Protokoll
- `pollen_data` - Pollenflugkalender

Siehe `schema.sql` für Details.

---

## 🤝 Contributing

Dieses Projekt ist für den persönlichen Gebrauch gedacht. Bei Fragen oder Bug-Reports gerne ein Issue öffnen!

---

## 📝 License

MIT License - Siehe LICENSE Datei

---

## 👤 Autor

Entwickelt für persönliches Allergie-Tracking

---

## 🆘 Support

Bei Problemen:

1. Prüfe `config/database.php` Credentials
2. Prüfe ob `schema.sql` importiert wurde
3. Prüfe PHP-Error-Logs
4. Prüfe Apache/Nginx Error-Logs

**Common Issues:**
- "Cannot modify header information" → Cache leeren, Browser neu starten
- "Table doesn't exist" → `schema.sql` noch nicht importiert
- "Access denied" → DB-Credentials in `config/database.php` prüfen

---

**Viel Erfolg beim Tracking! 🩺**
