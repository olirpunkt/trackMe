<?php
/**
 * TrackME - Dashboard View
 * Symptomerfassung für heute
 */

// Aktuelles Datum
$today = date('Y-m-d');

// POST-Handling: Eintrag speichern
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_entry'])) {
    
    $timeOfDay = $_POST['time_of_day'] ?? 'morning';
    $location = trim($_POST['location'] ?? '');
    $weatherCondition = $_POST['weather_condition'] ?? null;
    $temperature = !empty($_POST['temperature']) ? (int)$_POST['temperature'] : null;
    $mood = !empty($_POST['mood']) ? (int)$_POST['mood'] : null;
    $notes = trim($_POST['notes'] ?? '');
    
    // Validierung
    $errors = [];
    if (empty($location)) {
        $errors[] = "Ort ist erforderlich.";
    }
    
    if (empty($errors)) {
        try {
            // Prüfe ob bereits Eintrag für heute + Tageszeit existiert
            $existing = fetchOne(
                "SELECT id FROM daily_entries WHERE date = ? AND time_of_day = ?",
                [$today, $timeOfDay]
            );
            
            if ($existing) {
                // UPDATE
                $entryId = $existing['id'];
                query(
                    "UPDATE daily_entries 
                     SET location = ?, weather_condition = ?, temperature = ?, mood = ?, notes = ?
                     WHERE id = ?",
                    [$location, $weatherCondition, $temperature, $mood, $notes, $entryId]
                );
                
                // Lösche alte Symptom-Logs für diesen Eintrag
                query("DELETE FROM symptom_logs WHERE daily_entry_id = ?", [$entryId]);
                
            } else {
                // INSERT
                $entryId = insert(
                    "INSERT INTO daily_entries (date, time_of_day, location, weather_condition, temperature, mood, notes)
                     VALUES (?, ?, ?, ?, ?, ?, ?)",
                    [$today, $timeOfDay, $location, $weatherCondition, $temperature, $mood, $notes]
                );
            }
            
            // Symptome speichern (nur die mit severity > 0)
            if (isset($_POST['symptoms']) && is_array($_POST['symptoms'])) {
                foreach ($_POST['symptoms'] as $symptomId => $severity) {
                    $severity = (int)$severity;
                    if ($severity > 0 && $severity <= 3) {
                        query(
                            "INSERT INTO symptom_logs (daily_entry_id, symptom_type_id, severity)
                             VALUES (?, ?, ?)",
                            [$entryId, $symptomId, $severity]
                        );
                    }
                }
            }
            
            // Medikamente speichern
            if (isset($_POST['medications']) && is_array($_POST['medications'])) {
                foreach ($_POST['medications'] as $medId) {
                    query(
                        "INSERT INTO medication_logs (daily_entry_id, medication_id, taken)
                         VALUES (?, ?, 1)",
                        [$entryId, $medId]
                    );
                }
            }
            
            $success = true;
            
        } catch (Exception $e) {
            $errors[] = "Fehler beim Speichern: " . $e->getMessage();
        }
    }
}

// Lade Symptome nach Organ gruppiert
$symptoms = fetchAll("SELECT * FROM symptom_types WHERE is_default = 1 ORDER BY organ, display_order");
$symptomsByOrgan = [];
foreach ($symptoms as $s) {
    $symptomsByOrgan[$s['organ']][] = $s;
}

// Lade Medikamente
$medications = fetchAll("SELECT * FROM medications ORDER BY name");

// Lade heutigen Eintrag (falls vorhanden)
$todayEntry = fetchOne(
    "SELECT * FROM daily_entries WHERE date = ? ORDER BY created_at DESC LIMIT 1",
    [$today]
);

// Lade gespeicherte Symptome für heute
$savedSymptoms = [];
if ($todayEntry) {
    $logs = fetchAll(
        "SELECT symptom_type_id, severity FROM symptom_logs WHERE daily_entry_id = ?",
        [$todayEntry['id']]
    );
    foreach ($logs as $log) {
        $savedSymptoms[$log['symptom_type_id']] = $log['severity'];
    }
}

// Lade gespeicherte Medikamente für heute
$savedMedications = [];
if ($todayEntry) {
    $logs = fetchAll(
        "SELECT medication_id FROM medication_logs WHERE daily_entry_id = ?",
        [$todayEntry['id']]
    );
    foreach ($logs as $log) {
        $savedMedications[] = $log['medication_id'];
    }
}
?>

<div class="px-4 sm:px-0">
    
    <!-- Success Message -->
    <?php if (isset($success)): ?>
    <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
        <p class="text-green-800">✅ Eintrag erfolgreich gespeichert!</p>
    </div>
    <?php endif; ?>
    
    <!-- Error Messages -->
    <?php if (!empty($errors)): ?>
    <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
        <?php foreach ($errors as $error): ?>
            <p class="text-red-800">❌ <?= e($error) ?></p>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    
    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-3xl font-bold text-gray-900">Symptome erfassen</h2>
        <p class="mt-2 text-gray-600">Dokumentiere deine Symptome für <strong><?= date('d.m.Y') ?></strong></p>
    </div>
    
    <!-- Eingabe-Formular -->
    <form method="POST" class="space-y-6">
        
        <!-- Basis-Daten -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">📍 Basis-Informationen</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <!-- Tageszeit -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tageszeit</label>
                    <select name="time_of_day" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="morning" <?= ($todayEntry['time_of_day'] ?? 'morning') === 'morning' ? 'selected' : '' ?>>🌅 Morgens</option>
                        <option value="afternoon" <?= ($todayEntry['time_of_day'] ?? '') === 'afternoon' ? 'selected' : '' ?>>☀️ Mittags</option>
                        <option value="evening" <?= ($todayEntry['time_of_day'] ?? '') === 'evening' ? 'selected' : '' ?>>🌙 Abends</option>
                    </select>
                </div>
                
                <!-- Ort -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ort *</label>
                    <input type="text" name="location" 
                           value="<?= e($todayEntry['location'] ?? 'Stuttgart') ?>"
                           placeholder="z.B. Stuttgart"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           required>
                </div>
                
                <!-- Wetter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Wetter</label>
                    <select name="weather_condition" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Nicht angegeben</option>
                        <option value="sunny" <?= ($todayEntry['weather_condition'] ?? '') === 'sunny' ? 'selected' : '' ?>>☀️ Sonnig</option>
                        <option value="cloudy" <?= ($todayEntry['weather_condition'] ?? '') === 'cloudy' ? 'selected' : '' ?>>☁️ Bewölkt</option>
                        <option value="rainy" <?= ($todayEntry['weather_condition'] ?? '') === 'rainy' ? 'selected' : '' ?>>🌧️ Regnerisch</option>
                        <option value="snowy" <?= ($todayEntry['weather_condition'] ?? '') === 'snowy' ? 'selected' : '' ?>>❄️ Schnee</option>
                        <option value="foggy" <?= ($todayEntry['weather_condition'] ?? '') === 'foggy' ? 'selected' : '' ?>>🌫️ Neblig</option>
                        <option value="windy" <?= ($todayEntry['weather_condition'] ?? '') === 'windy' ? 'selected' : '' ?>>💨 Windig</option>
                    </select>
                </div>
                
                <!-- Temperatur -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Temperatur (°C)</label>
                    <input type="number" name="temperature" 
                           value="<?= e($todayEntry['temperature'] ?? '') ?>"
                           placeholder="z.B. 18"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                
            </div>
            
            <!-- Stimmung -->
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Gesamtverfassung (Mood)</label>
                <div class="flex items-center space-x-4">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="radio" name="mood" value="<?= $i ?>" 
                                   <?= ($todayEntry['mood'] ?? 0) == $i ? 'checked' : '' ?>
                                   class="w-5 h-5 text-blue-600">
                            <span class="text-2xl"><?= ['😞', '😕', '😐', '🙂', '😊'][$i-1] ?></span>
                            <span class="text-sm text-gray-600"><?= $i ?></span>
                        </label>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
        
        <!-- Symptome -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">🩺 Symptome (0-3 Skala)</h3>
            <p class="text-sm text-gray-600 mb-4">0 = Keine | 1 = Leicht | 2 = Mittel | 3 = Stark</p>
            
            <?php foreach ($symptomsByOrgan as $organ => $organSymptoms): ?>
            <div class="mb-6 last:mb-0">
                <h4 class="font-medium text-gray-800 mb-3"><?= e($organ) ?></h4>
                <div class="space-y-2">
                    <?php foreach ($organSymptoms as $symptom): ?>
                    <div class="flex items-center justify-between bg-gray-50 rounded-lg px-4 py-3">
                        <label class="text-sm text-gray-700"><?= e($symptom['symptom_name']) ?></label>
                        <div class="flex space-x-2">
                            <?php for ($i = 0; $i <= 3; $i++): ?>
                            <label class="cursor-pointer">
                                <input type="radio" 
                                       name="symptoms[<?= $symptom['id'] ?>]" 
                                       value="<?= $i ?>"
                                       <?= ($savedSymptoms[$symptom['id']] ?? 0) == $i ? 'checked' : '' ?>
                                       class="w-4 h-4">
                                <span class="ml-1 text-sm text-gray-600"><?= $i ?></span>
                            </label>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Medikamente -->
        <?php if (count($medications) > 0): ?>
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">💊 Medikamente (heute eingenommen)</h3>
            <div class="space-y-2">
                <?php foreach ($medications as $med): ?>
                <label class="flex items-center space-x-3 cursor-pointer bg-gray-50 rounded-lg px-4 py-3 hover:bg-gray-100 transition">
                    <input type="checkbox" 
                           name="medications[]" 
                           value="<?= $med['id'] ?>"
                           <?= in_array($med['id'], $savedMedications) ? 'checked' : '' ?>
                           class="w-5 h-5 text-blue-600 rounded">
                    <div>
                        <span class="font-medium text-gray-800"><?= e($med['name']) ?></span>
                        <?php if ($med['dosage']): ?>
                            <span class="text-sm text-gray-500 ml-2">(<?= e($med['dosage']) ?>)</span>
                        <?php endif; ?>
                    </div>
                </label>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Notizen -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">📝 Notizen</h3>
            <textarea name="notes" rows="4" 
                      placeholder="Zusätzliche Anmerkungen..."
                      class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"><?= e($todayEntry['notes'] ?? '') ?></textarea>
        </div>
        
        <!-- Submit Button -->
        <div class="flex justify-end">
            <button type="submit" name="save_entry"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-3 rounded-lg transition shadow-sm">
                💾 Eintrag speichern
            </button>
        </div>
        
    </form>
    
</div>
