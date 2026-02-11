<?php
/**
 * TrackME - History View (Verlauf)
 * Tabellenansicht aller Einträge
 */

// TODO: Filter-Funktionalität
$filterDate = $_GET['filter_date'] ?? '';

// Lade alle Einträge
$entries = fetchAll("
    SELECT 
        de.*,
        GROUP_CONCAT(DISTINCT st.symptom_name SEPARATOR ', ') as symptoms,
        GROUP_CONCAT(DISTINCT m.name SEPARATOR ', ') as medications
    FROM daily_entries de
    LEFT JOIN symptom_logs sl ON de.id = sl.daily_entry_id
    LEFT JOIN symptom_types st ON sl.symptom_type_id = st.id
    LEFT JOIN medication_logs ml ON de.id = ml.daily_entry_id
    LEFT JOIN medications m ON ml.medication_id = m.id
    GROUP BY de.id
    ORDER BY de.date DESC, de.time_of_day DESC
    LIMIT 50
");

?>

<div class="px-4 sm:px-0">
    
    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-3xl font-bold text-gray-900">Verlauf</h2>
        <p class="mt-2 text-gray-600">Übersicht aller bisherigen Einträge</p>
    </div>
    
    <!-- Tabelle -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        
        <?php if (count($entries) === 0): ?>
            <div class="p-6 text-center text-gray-500">
                Noch keine Einträge vorhanden. Gehe zum Dashboard und erfasse deinen ersten Tag!
            </div>
        <?php else: ?>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Datum</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Zeit</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ort</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wetter</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mood</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Symptome</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Medikamente</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($entries as $entry): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?= date('d.m.Y', strtotime($entry['date'])) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php
                                $timeLabels = [
                                    'morning' => '🌅 Morgens',
                                    'afternoon' => '☀️ Mittags',
                                    'evening' => '🌙 Abends'
                                ];
                                echo $timeLabels[$entry['time_of_day']] ?? $entry['time_of_day'];
                                ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?= e($entry['location']) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php if ($entry['weather_condition']): ?>
                                    <?php
                                    $weatherIcons = [
                                        'sunny' => '☀️',
                                        'cloudy' => '☁️',
                                        'rainy' => '🌧️',
                                        'snowy' => '❄️',
                                        'foggy' => '🌫️',
                                        'windy' => '💨'
                                    ];
                                    echo $weatherIcons[$entry['weather_condition']] ?? '';
                                    ?>
                                    <?= $entry['temperature'] ? $entry['temperature'] . '°C' : '' ?>
                                <?php else: ?>
                                    <span class="text-gray-400">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <?php if ($entry['mood']): ?>
                                    <?= ['😞', '😕', '😐', '🙂', '😊'][$entry['mood']-1] ?? '' ?>
                                <?php else: ?>
                                    <span class="text-gray-400">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                <?= $entry['symptoms'] ? e($entry['symptoms']) : '<span class="text-gray-400">Keine</span>' ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                <?= $entry['medications'] ? e($entry['medications']) : '<span class="text-gray-400">Keine</span>' ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
        <?php endif; ?>
        
    </div>
    
</div>
