<?php
// Plik wywoływany przez: admin.php?page=cennik&action=move_item&id=X&direction=up|down

// Wczytanie content.json
$json_file = "../content.json";
if (!file_exists($json_file)) {
    header("Location: admin.php?page=cennik&status=error");
    exit;
}
$data = json_decode(file_get_contents($json_file), true);

$id = isset($_GET['id']) ? intval($_GET['id']) : null;
$direction = $_GET['direction'] ?? '';

// Sprawdzenie poprawności przesłanych parametrów
if ($id === null || !isset($data['cennik']['items'][$id]) || ($direction !== 'up' && $direction !== 'down')) {
    header("Location: admin.php?page=cennik&status=error");
    exit;
}

// Wyznaczenie indeksu elementu, z którym się zamienimy
$target_id = ($direction === 'up') ? $id - 1 : $id + 1;

// Upewniamy się, że element docelowy istnieje w tablicy (czy nie wychodzimy poza zakres)
if (isset($data['cennik']['items'][$target_id])) {
    
    // Klasyczna zamiana elementów miejscami
    $temp = $data['cennik']['items'][$id];
    $data['cennik']['items'][$id] = $data['cennik']['items'][$target_id];
    $data['cennik']['items'][$target_id] = $temp;

    // Przeindeksowanie tablicy od zera dla zachowania porządku numerycznego kluczy
    $data['cennik']['items'] = array_values($data['cennik']['items']);

    // Zapis do pliku JSON
    file_put_contents($json_file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
    // Powrót do cennika
    header("Location: admin.php?page=cennik");
    exit;
}

// Jeśli operacja była niemożliwa (np. pierwszy element próbowano dać w górę)
header("Location: admin.php?page=cennik&status=error");
exit;
