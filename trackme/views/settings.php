<?php
/**
 * TrackME - Settings View
 * Verwaltung von Symptomen und Medikamenten
 */
?>

<div class="px-4 sm:px-0">
    
    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-3xl font-bold text-gray-900">Einstellungen</h2>
        <p class="mt-2 text-gray-600">Verwalte deine Symptome und Medikamente</p>
    </div>
    
    <!-- Medikamente -->
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">💊 Medikamente</h3>
        
        <?php
        $medications = fetchAll("SELECT * FROM medications ORDER BY name");
        ?>
        
        <?php if (count($medications) > 0): ?>
        <div class="space-y-2 mb-4">
            <?php foreach ($medications as $med): ?>
            <div class="flex items-center justify-between bg-gray-50 rounded-lg px-4 py-3">
                <div>
                    <span class="font-medium text-gray-800"><?= e($med['name']) ?></span>
                    <?php if ($med['dosage']): ?>
                        <span class="text-sm text-gray-500 ml-2"><?= e($med['dosage']) ?></span>
                    <?php endif; ?>
                </div>
                <button class="text-red-600 hover:text-red-800 text-sm">Löschen</button>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p class="text-gray-500 mb-4">Noch keine Medikamente angelegt.</p>
        <?php endif; ?>
        
        <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
            + Neues Medikament hinzufügen
        </button>
    </div>
    
    <!-- Eigene Symptome (Post-MVP) -->
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">🩺 Eigene Symptome</h3>
        <p class="text-gray-500 text-sm">
            <em>Feature kommt in Version 1.1 - Derzeit sind die Standard-Symptome aktiv.</em>
        </p>
    </div>
    
</div>
