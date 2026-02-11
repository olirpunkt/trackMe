<?php
/**
 * TrackME - Application Entry Point
 */

session_start();

// Lade Konfiguration
require_once __DIR__ . '/../config/database.php';

// Teste DB-Verbindung beim ersten Load
try {
    getDB();
} catch (Exception $e) {
    die('Datenbankverbindung fehlgeschlagen. Siehe README.md');
}

// Routing: Hole Page-Parameter
$page = $_GET['page'] ?? 'dashboard';

// ============================================
// EXPORT HANDLING (VOR HTML OUTPUT!)
// ============================================
if ($page === 'export' && isset($_GET['action'])) {
    
    // CSV-Export
    if ($_GET['action'] === 'download_csv') {
        $entries = fetchAll("
            SELECT 
                de.date,
                de.time_of_day,
                de.location,
                de.weather_condition,
                de.temperature,
                de.mood,
                de.notes,
                GROUP_CONCAT(DISTINCT CONCAT(st.organ, ': ', st.symptom_name, ' (', sl.severity, ')') SEPARATOR '; ') as symptoms,
                GROUP_CONCAT(DISTINCT m.name SEPARATOR ', ') as medications
            FROM daily_entries de
            LEFT JOIN symptom_logs sl ON de.id = sl.daily_entry_id
            LEFT JOIN symptom_types st ON sl.symptom_type_id = st.id
            LEFT JOIN medication_logs ml ON de.id = ml.daily_entry_id
            LEFT JOIN medications m ON ml.medication_id = m.id
            GROUP BY de.id
            ORDER BY de.date DESC, de.time_of_day
        ");
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="trackme_export_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM
        
        fputcsv($output, ['Datum', 'Tageszeit', 'Ort', 'Wetter', 'Temperatur', 'Stimmung', 'Symptome', 'Medikamente', 'Notizen'], ';');
        
        foreach ($entries as $entry) {
            fputcsv($output, [
                date('d.m.Y', strtotime($entry['date'])),
                $entry['time_of_day'],
                $entry['location'],
                $entry['weather_condition'] ?? '',
                $entry['temperature'] ?? '',
                $entry['mood'] ?? '',
                $entry['symptoms'] ?? '',
                $entry['medications'] ?? '',
                $entry['notes'] ?? ''
            ], ';');
        }
        
        fclose($output);
        exit;
    }
    
    // JSON-Export
    if ($_GET['action'] === 'download_json') {
        $entries = fetchAll("SELECT * FROM daily_entries ORDER BY date DESC, time_of_day");
        
        $exportData = [];
        foreach ($entries as $entry) {
            $symptoms = fetchAll(
                "SELECT st.organ, st.symptom_name, sl.severity 
                 FROM symptom_logs sl
                 JOIN symptom_types st ON sl.symptom_type_id = st.id
                 WHERE sl.daily_entry_id = ?",
                [$entry['id']]
            );
            
            $medications = fetchAll(
                "SELECT m.name, m.dosage
                 FROM medication_logs ml
                 JOIN medications m ON ml.medication_id = m.id
                 WHERE ml.daily_entry_id = ?",
                [$entry['id']]
            );
            
            $exportData[] = [
                'date' => $entry['date'],
                'time_of_day' => $entry['time_of_day'],
                'location' => $entry['location'],
                'weather' => [
                    'condition' => $entry['weather_condition'],
                    'temperature' => $entry['temperature']
                ],
                'mood' => $entry['mood'],
                'symptoms' => $symptoms,
                'medications' => $medications,
                'notes' => $entry['notes']
            ];
        }
        
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="trackme_export_' . date('Y-m-d') . '.json"');
        
        echo json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// Erlaubte Seiten (Whitelist)
$allowedPages = ['dashboard', 'history', 'settings', 'export'];
if (!in_array($page, $allowedPages)) {
    $page = 'dashboard';
}

// Titel-Mapping
$pageTitles = [
    'dashboard' => 'Dashboard',
    'history' => 'Verlauf',
    'settings' => 'Einstellungen',
    'export' => 'Export',
];

$pageTitle = $pageTitles[$page];
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> - TrackME</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <!-- Logo -->
                    <div class="flex-shrink-0 flex items-center">
                        <a href="?page=dashboard" class="flex items-center space-x-2">
                            <span class="text-3xl">🩺</span>
                            <h1 class="text-2xl font-bold text-blue-600">TrackME</h1>
                        </a>
                    </div>
                    
                    <!-- Navigation Links -->
                    <div class="hidden sm:ml-8 sm:flex sm:space-x-8">
                        <a href="?page=dashboard" 
                           class="<?= $page === 'dashboard' ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' ?> inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition">
                            📊 Dashboard
                        </a>
                        <a href="?page=history" 
                           class="<?= $page === 'history' ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' ?> inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition">
                            📅 Verlauf
                        </a>
                        <a href="?page=settings" 
                           class="<?= $page === 'settings' ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' ?> inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition">
                            ⚙️ Einstellungen
                        </a>
                        <a href="?page=export" 
                           class="<?= $page === 'export' ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' ?> inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition">
                            💾 Export
                        </a>
                    </div>
                </div>
                
                <!-- Datum-Anzeige -->
                <div class="flex items-center">
                    <span class="text-sm text-gray-500">
                        <?php
                        $weekdays = ['Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag'];
                        $months = ['', 'Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'];
                        echo $weekdays[date('w')] . ', ' . date('d') . '. ' . $months[date('n')] . ' ' . date('Y');
                        ?>
                    </span>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <?php
        // View laden
        $viewFile = __DIR__ . "/../views/{$page}.php";
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            echo '<div class="bg-white shadow rounded-lg p-6">';
            echo '<h2 class="text-2xl font-bold text-gray-900">Seite nicht gefunden</h2>';
            echo '<p class="mt-2 text-gray-600">Die View-Datei existiert nicht.</p>';
            echo '</div>';
        }
        ?>
    </main>

    <!-- Footer -->
    <footer class="bg-white mt-12 border-t">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <p class="text-center text-gray-500 text-sm">
                TrackME v1.0 MVP - Allergiesymptom-Tracking
            </p>
        </div>
    </footer>

</body>
</html>
