<?php
/**
 * TrackME - Authentication Functions
 * Login, Logout, CSRF-Protection
 */

/**
 * Login-Versuch durchführen
 * 
 * @param string $username
 * @param string $password
 * @return array ['success' => bool, 'message' => string]
 */
function attemptLogin($username, $password) {
    // Validierung
    if (empty($username) || empty($password)) {
        return ['success' => false, 'message' => 'Bitte Username und Passwort eingeben.'];
    }
    
    // User aus DB holen
    $user = fetchOne(
        "SELECT id, username, password_hash FROM users WHERE username = ?",
        [$username]
    );
    
    // Username existiert nicht
    if (!$user) {
        return ['success' => false, 'message' => 'Ungültige Zugangsdaten.'];
    }
    
    // Password prüfen
    if (!password_verify($password, $user['password_hash'])) {
        return ['success' => false, 'message' => 'Ungültige Zugangsdaten.'];
    }
    
    // Login erfolgreich - Session setzen
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['logged_in'] = true;
    
    // Session-Fixation verhindern
    session_regenerate_id(true);
    
    return ['success' => true, 'message' => 'Login erfolgreich!'];
}

/**
 * User ausloggen
 */
function logout() {
    // Session-Variablen löschen
    $_SESSION = [];
    
    // Session-Cookie löschen
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    // Session zerstören
    session_destroy();
    
    // Neue Session starten (für Login-Seite)
    session_start();
}

/**
 * Prüfen ob User eingeloggt ist
 * 
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

/**
 * Redirect zu Login falls nicht eingeloggt
 * Für geschützte Seiten verwenden
 */
function requireLogin() {
    if (!isLoggedIn()) {
        redirect('login');
    }
}

/**
 * CSRF-Token generieren
 * 
 * @return string
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * CSRF-Token validieren
 * 
 * @param string $token
 * @return bool
 */
function validateCSRFToken($token) {
    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }
    
    // Timing-Safe Comparison
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Aktuell eingeloggten Username holen
 * 
 * @return string|null
 */
function getCurrentUsername() {
    return $_SESSION['username'] ?? null;
}