<?php
/**
 * TrackME - Database Configuration
 * 
 * Zentrale DB-Konfiguration und Hilfsfunktionen
 */

// DB-Credentials (Docker Development)
define('DB_HOST', 'db');
define('DB_NAME', 'trackme');
define('DB_USER', '');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Debug-Modus
define('DEBUG_MODE', true);
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

/**
 * Datenbankverbindung herstellen (Singleton)
 */
function getDB() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                DB_HOST,
                DB_NAME,
                DB_CHARSET
            );
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            
        } catch (PDOException $e) {
            if (DEBUG_MODE) {
                die('<h1>Datenbankfehler</h1><pre>' . htmlspecialchars($e->getMessage()) . '</pre>');
            } else {
                error_log('DB Connection Error: ' . $e->getMessage());
                die('Datenbankfehler. Bitte später erneut versuchen.');
            }
        }
    }
    
    return $pdo;
}

/**
 * Query ausführen
 */
function query($sql, $params = []) {
    $pdo = getDB();
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

/**
 * Einzelnes Ergebnis holen
 */
function fetchOne($sql, $params = []) {
    return query($sql, $params)->fetch();
}

/**
 * Alle Ergebnisse holen
 */
function fetchAll($sql, $params = []) {
    return query($sql, $params)->fetchAll();
}

/**
 * INSERT und ID zurückgeben
 */
function insert($sql, $params = []) {
    query($sql, $params);
    return getDB()->lastInsertId();
}

/**
 * HTML escapen (XSS-Schutz)
 */
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Redirect helper
 */
function redirect($page) {
    header("Location: ?page=" . urlencode($page));
    exit;
}
