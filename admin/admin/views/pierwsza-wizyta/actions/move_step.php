<?php
// Plik wywoływany przez: admin.php?page=pierwsza-wizyta&action=move_step&id=X&direction=up|down

require_once "log_change.php";

// 1. USTALENIE POPRAWNEI ŚCIEŻKI DO PLIKU
$json_file = "../data/pierwsza-wizyta.json";

if (!file_exists($json_file)) {
    $json_file = "data/pierwsza-wizyta.json";
    if (!file_exists($json_file)) {
        header("Location: admin.php?page=pierwsza-wizyta&status=error");
        exit;
    }
}

// 2. WCZYTANIE I DEKODOWANIE AKTUALNYCH DANYCH
$data = json_decode(file_get_contents($json_file), true) ?? [];

// Pobranie i walidacja ID oraz kierunku przesunięcia
$id = isset($_GET['id']) ? intval($_GET['id']) : null;
$direction = isset($_GET['direction']) ? $_GET['direction'] : '';

if ($id === null || !isset($data['steps'][$id]) || !in_array($direction, ['up', 'down'])) {
    header("Location: admin.php?page=pierwsza-wizyta&status=error");
    exit;
}

// Wyliczamy indeks elementu, z którym chcemy dokonać zamiany
$target_id = ($direction === 'up') ? $id - 1 : $id + 1;

// Sprawdzamy, czy element docelowy istnieje w tablicy (zabezpieczenie przed wyjściem poza zakres)
if (!isset($data['steps'][$target_id])) {
    header("Location: admin.php?page=pierwsza-wizyta&status=error");
    exit;
}

// 3. POBRANIE NAZWY ETAPU DO LOGÓW PRZED ZAMIANĄ
$moved_title = $data['steps'][$id]['title'] ?? '';
$short_title = mb_strimwidth($moved_title, 0, 30, "...");

// 4. LOGIKA ZAMIANY MIEJSCAMI (Swap z użyciem zmiennej tymczasowej)
$temp = $data['steps'][$id];
$data['steps'][$id] = $data['steps'][$target_id];
$data['steps'][$target_id] = $temp;

// 5. PRZYGOTOWANIE RAPORTU ZMIAN DO LOGÓW (Ludzki format)
$changes = [
    "zmiana pozycji" => [
        "old" => "Pozycja " . ($id + 1), 
        "new" => "Pozycja " . ($target_id + 1)
    ]
];

// Zapis zaktualizowanej, płaskiej struktury do pliku JSON
file_put_contents($json_file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// 6. REJESTRACJA W LOGACH: Wstrzykujemy nazwę sortowanego kroku do kolumny "Element"
$object_label = "Kolejność: " . $short_title;
log_change("Pierwsza wizyta", "Sortowanie", $object_label, $changes);

// POPRAWIONE PRZEKIEROWANIE (Znak ? naprawia błąd 404)
header("Location: admin.php?page=pierwsza-wizyta&status=success");
exit;

