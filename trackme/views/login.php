<?php
/**
 * TrackME - Login View
 */

// Fehler aus Session holen (falls vorhanden)
$error = $_SESSION['login_error'] ?? null;
unset($_SESSION['login_error']);  // Nur einmal anzeigen

// CSRF-Token für Formular generieren
$csrfToken = generateCSRFToken();
?>

<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4">
    <div class="max-w-md w-full space-y-8">
        
        <!-- Logo/Header -->
        <div>
            <div class="flex justify-center">
                <span class="text-6xl">🩺</span>
            </div>
            <h2 class="mt-6 text-center text-3xl font-bold text-gray-900">
                TrackME Login
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Melde dich an um deine Symptome zu erfassen
            </p>
        </div>
        
        <!-- Error Message -->
        <?php if ($error): ?>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <p class="text-red-800 text-sm">
                ❌ <?= e($error) ?>
            </p>
        </div>
        <?php endif; ?>
        
        <!-- Login Form -->
        <form method="POST" class="mt-8 space-y-6">
            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
            
            <div class="space-y-4">
                <!-- Username -->
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                        Benutzername
                    </label>
                    <input 
                        id="username" 
                        name="username" 
                        type="text" 
                        required 
                        autofocus
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Dein Benutzername"
                    >
                </div>
                
                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Passwort
                    </label>
                    <input 
                        id="password" 
                        name="password" 
                        type="password" 
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Dein Passwort"
                    >
                </div>
            </div>
            
            <!-- Submit Button -->
            <div>
                <button 
                    type="submit" 
                    name="login"
                    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition"
                >
                    🔐 Anmelden
                </button>
            </div>
        </form>
        
        <!-- Footer -->
        <div class="text-center text-sm text-gray-500">
            TrackME v1.0 - Allergiesymptom-Tracking
        </div>
        
    </div>
</div>