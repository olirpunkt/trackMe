<?php
/**
 * TrackME - Export View
 * CSV/JSON Export der Daten
 */

// Statistik
$totalEntries = fetchOne("SELECT COUNT(*) as count FROM daily_entries")['count'];
$dateRange = fetchOne("SELECT MIN(date) as first, MAX(date) as last FROM daily_entries");

?>

<div class="px-4 sm:px-0">
    
    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-3xl font-bold text-gray-900">Daten exportieren</h2>
        <p class="mt-2 text-gray-600">Exportiere deine Daten für externe Analysen</p>
    </div>
    
    <!-- Statistik -->
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">📊 Datenübersicht</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-blue-50 rounded-lg p-4">
                <p class="text-sm text-blue-600 font-medium">Einträge gesamt</p>
                <p class="text-3xl font-bold text-blue-900 mt-1"><?= $totalEntries ?></p>
            </div>
            
            <?php if ($dateRange['first']): ?>
            <div class="bg-green-50 rounded-lg p-4">
                <p class="text-sm text-green-600 font-medium">Erster Eintrag</p>
                <p class="text-xl font-bold text-green-900 mt-1">
                    <?= date('d.m.Y', strtotime($dateRange['first'])) ?>
                </p>
            </div>
            
            <div class="bg-purple-50 rounded-lg p-4">
                <p class="text-sm text-purple-600 font-medium">Letzter Eintrag</p>
                <p class="text-xl font-bold text-purple-900 mt-1">
                    <?= date('d.m.Y', strtotime($dateRange['last'])) ?>
                </p>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Export-Optionen -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <!-- CSV Export -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center space-x-3 mb-4">
                <span class="text-4xl">📊</span>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">CSV-Export</h3>
                    <p class="text-sm text-gray-600">Für Excel, Google Sheets, etc.</p>
                </div>
            </div>
            
            <ul class="text-sm text-gray-600 mb-4 space-y-1">
                <li>✅ Alle Einträge mit Symptomen</li>
                <li>✅ Excel-kompatibel (UTF-8 BOM)</li>
                <li>✅ Semikolon-getrennt</li>
            </ul>
            
            <?php if ($totalEntries > 0): ?>
            <a href="?page=export&action=download_csv" 
               class="block w-full bg-green-600 hover:bg-green-700 text-white text-center font-semibold px-4 py-3 rounded-lg transition">
                💾 CSV herunterladen
            </a>
            <?php else: ?>
            <button disabled class="block w-full bg-gray-300 text-gray-500 text-center font-semibold px-4 py-3 rounded-lg cursor-not-allowed">
                Keine Daten vorhanden
            </button>
            <?php endif; ?>
        </div>
        
        <!-- JSON Export -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center space-x-3 mb-4">
                <span class="text-4xl">🔧</span>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">JSON-Export</h3>
                    <p class="text-sm text-gray-600">Für externe Tools & Analysen</p>
                </div>
            </div>
            
            <ul class="text-sm text-gray-600 mb-4 space-y-1">
                <li>✅ Strukturierte Daten</li>
                <li>✅ Verschachtelte Symptome & Medikamente</li>
                <li>✅ Für Entwickler & APIs</li>
            </ul>
            
            <?php if ($totalEntries > 0): ?>
            <a href="?page=export&action=download_json" 
               class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center font-semibold px-4 py-3 rounded-lg transition">
                💾 JSON herunterladen
            </a>
            <?php else: ?>
            <button disabled class="block w-full bg-gray-300 text-gray-500 text-center font-semibold px-4 py-3 rounded-lg cursor-not-allowed">
                Keine Daten vorhanden
            </button>
            <?php endif; ?>
        </div>
        
    </div>
    
    <!-- Hinweis -->
    <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <p class="text-sm text-yellow-800">
            <strong>💡 Hinweis:</strong> Deine Daten werden lokal auf deinem Server gespeichert und niemals an Dritte weitergegeben.
            Die Export-Dateien enthalten alle deine persönlichen Gesundheitsdaten - behandle sie vertraulich!
        </p>
    </div>
    
</div>
