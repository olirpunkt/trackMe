-- TrackME Database Schema
-- Version: 1.0 MVP
-- Created: 2026-02-11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- ============================================
-- Database Creation
-- ============================================

CREATE DATABASE IF NOT EXISTS `trackme` 
DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `trackme`;

-- ============================================
-- Table: daily_entries
-- Haupttabelle für tägliche Einträge
-- ============================================

CREATE TABLE `daily_entries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `time_of_day` enum('morning','afternoon','evening') NOT NULL DEFAULT 'morning',
  `location` varchar(255) NOT NULL,
  `weather_condition` enum('sunny','cloudy','rainy','snowy','foggy','windy') DEFAULT NULL,
  `temperature` int(11) DEFAULT NULL COMMENT 'Temperature in Celsius',
  `mood` tinyint(1) DEFAULT NULL COMMENT 'Mood scale 1-5',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_entry` (`date`,`time_of_day`),
  KEY `idx_date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: symptom_types
-- Symptom-Definitionen (Stammdaten)
-- ============================================

CREATE TABLE `symptom_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organ` varchar(100) NOT NULL COMMENT 'Body part/organ (e.g., Nose, Eyes)',
  `symptom_name` varchar(100) NOT NULL COMMENT 'Symptom name (e.g., Sneezing, Itching)',
  `is_default` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Pre-populated default symptom',
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_symptom` (`organ`,`symptom_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: symptom_logs
-- Tägliche Symptom-Erfassung (0-3 Skala)
-- ============================================

CREATE TABLE `symptom_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `daily_entry_id` int(11) NOT NULL,
  `symptom_type_id` int(11) NOT NULL,
  `severity` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Severity: 0=none, 1=mild, 2=moderate, 3=severe',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_symptom_daily_entry` (`daily_entry_id`),
  KEY `fk_symptom_type` (`symptom_type_id`),
  CONSTRAINT `fk_symptom_daily_entry` FOREIGN KEY (`daily_entry_id`) 
    REFERENCES `daily_entries` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_symptom_type` FOREIGN KEY (`symptom_type_id`) 
    REFERENCES `symptom_types` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: medications
-- Medikamenten-Stammdaten
-- ============================================

CREATE TABLE `medications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `dosage` varchar(100) DEFAULT NULL COMMENT 'e.g., 10mg, 2 tablets',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: medication_logs
-- Tägliche Medikamenten-Einnahme
-- ============================================

CREATE TABLE `medication_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `daily_entry_id` int(11) NOT NULL,
  `medication_id` int(11) NOT NULL,
  `taken` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Always 1 (taken)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_medlog_daily_entry` (`daily_entry_id`),
  KEY `fk_medication` (`medication_id`),
  CONSTRAINT `fk_medlog_daily_entry` FOREIGN KEY (`daily_entry_id`) 
    REFERENCES `daily_entries` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_medication` FOREIGN KEY (`medication_id`) 
    REFERENCES `medications` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: pollen_data
-- Statischer Pollenflugkalender (MVP)
-- ============================================

CREATE TABLE `pollen_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pollen_type` varchar(50) NOT NULL COMMENT 'e.g., Birke, Hasel, Gräser',
  `month` tinyint(2) NOT NULL COMMENT 'Month: 1-12',
  `intensity` enum('none','low','medium','high') NOT NULL DEFAULT 'none',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_pollen_month` (`pollen_type`,`month`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- SAMPLE DATA: Default Symptoms (5 Organs)
-- ============================================

INSERT INTO `symptom_types` (`organ`, `symptom_name`, `is_default`, `display_order`) VALUES
-- Nase (Nose)
('Nase', 'Niesen', 1, 1),
('Nase', 'Laufende Nase', 1, 2),
('Nase', 'Verstopfte Nase', 1, 3),
('Nase', 'Juckreiz', 1, 4),

-- Augen (Eyes)
('Augen', 'Juckreiz', 1, 10),
('Augen', 'Rötung', 1, 11),
('Augen', 'Tränen', 1, 12),
('Augen', 'Brennen', 1, 13),

-- Rachen (Throat)
('Rachen', 'Kratzen', 1, 20),
('Rachen', 'Schluckbeschwerden', 1, 21),
('Rachen', 'Trockenheit', 1, 22),

-- Atemwege (Airways)
('Atemwege', 'Husten', 1, 30),
('Atemwege', 'Kurzatmigkeit', 1, 31),
('Atemwege', 'Engegefühl Brust', 1, 32),
('Atemwege', 'Pfeifendes Atmen', 1, 33),

-- Haut (Skin)
('Haut', 'Juckreiz', 1, 40),
('Haut', 'Rötung', 1, 41),
('Haut', 'Ekzeme', 1, 42),
('Haut', 'Schwellung', 1, 43);

-- ============================================
-- SAMPLE DATA: Pollen Calendar (DWD Types)
-- Quelle: Deutscher Wetterdienst (DWD)
-- ============================================

INSERT INTO `pollen_data` (`pollen_type`, `month`, `intensity`) VALUES
-- Hasel (Hazelnut)
('Hasel', 1, 'low'),
('Hasel', 2, 'medium'),
('Hasel', 3, 'high'),
('Hasel', 4, 'low'),

-- Erle (Alder)
('Erle', 2, 'medium'),
('Erle', 3, 'high'),
('Erle', 4, 'medium'),

-- Birke (Birch)
('Birke', 3, 'low'),
('Birke', 4, 'high'),
('Birke', 5, 'high'),
('Birke', 6, 'low'),

-- Gräser (Grasses)
('Gräser', 5, 'medium'),
('Gräser', 6, 'high'),
('Gräser', 7, 'high'),
('Gräser', 8, 'medium'),
('Gräser', 9, 'low'),

-- Roggen (Rye)
('Roggen', 5, 'low'),
('Roggen', 6, 'medium'),
('Roggen', 7, 'low'),

-- Beifuß (Mugwort)
('Beifuß', 7, 'low'),
('Beifuß', 8, 'medium'),
('Beifuß', 9, 'low'),

-- Ambrosia (Ragweed)
('Ambrosia', 8, 'low'),
('Ambrosia', 9, 'medium'),
('Ambrosia', 10, 'low'),

-- Esche (Ash)
('Esche', 4, 'medium'),
('Esche', 5, 'low');

-- ============================================
-- SAMPLE DATA: Example Medications
-- ============================================

INSERT INTO `medications` (`name`, `dosage`, `notes`) VALUES
('Cetirizin', '10mg', 'Einmal täglich'),
('Loratadin', '10mg', 'Bei Bedarf'),
('Nasenspray (Kortisonhaltig)', '2 Sprühstöße', 'Morgens'),
('Augentropfen (Antihistaminika)', '1 Tropfen', 'Bei Bedarf');

-- ============================================
-- SAMPLE DATA: Example Entry (für Testing)
-- ============================================

-- Beispieleintrag von heute
INSERT INTO `daily_entries` (`date`, `time_of_day`, `location`, `weather_condition`, `temperature`, `mood`, `notes`) 
VALUES 
(CURDATE(), 'morning', 'Stuttgart', 'sunny', 18, 4, 'Leichte Symptome, aber gut geschlafen');

-- Zugehörige Symptome
INSERT INTO `symptom_logs` (`daily_entry_id`, `symptom_type_id`, `severity`) VALUES
(1, 1, 1),  -- Niesen: mild
(1, 2, 2),  -- Laufende Nase: moderate
(1, 5, 1);  -- Augenjucken: mild

-- Eingenommenes Medikament
INSERT INTO `medication_logs` (`daily_entry_id`, `medication_id`, `taken`) VALUES
(1, 1, 1);  -- Cetirizin genommen

-- User-Tabelle für Login-System
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

COMMIT;

-- ============================================
-- DONE! Database ready to use.
-- ============================================
