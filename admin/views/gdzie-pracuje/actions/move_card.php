<?php
// Plik wywoływany przez: admin.php?page=gdzie-pracuje&action=move_card&id=X&direction=up|down

require_once "log_change.php";

// 1. USTALENIE POPRAWNEJ ŚCIEŻKI DO PLIKU DANYCH
$json_file = "../data/gdzie-pracuje.json";
if (!file_exists($json_file)) {
    $json_file = "data/gdzie-pracuje.json";
    if (!file_exists($json_file)) {
        header("Location: admin.php?page=gdzie-pracuje&status=error");
        exit;
    }
}

// 2. WCZYTANIE I DEKODOWANIE AKTUALNYCH DANYCH JSON
$data = json_decode(file_get_contents($json_file), true) ?? [];

// Pobranie i walidacja ID oraz kierunku przesunięcia z adresu URL (metoda GET)
$id = isset($_GET['id']) ? intval($_GET['id']) : null;
$direction = isset($_GET['direction']) ? $_GET['direction'] : '';

if ($id === null || !isset($data['cards'][$id]) || !in_array($direction, ['up', 'down'])) {
    header("Location: admin.php?page=gdzie-pracuje&status=error");
    exit;
}

// Wyliczamy indeks elementu, z którym chcemy dokonać zamiany
$target_id = ($direction === 'up') ? $id - 1 : $id + 1;

// Sprawdzamy, czy element docelowy fizycznie istnieje w tablicy (zabezpieczenie przed wyjściem poza zakres)
if (!isset($data['cards'][$target_id])) {
    header("Location: admin.php?page=gdzie-pracuje&status=error");
    exit;
}

// 3. POBRANIE NAZWY KARTY DO LOGÓW PRZED ZAMIANĄ MIEJSCAMI
$moved_title = $data['cards'][$id]['title'] ?? '';
$short_title = mb_strimwidth($moved_title, 0, 30, "...");

// 4. LOGIKA ZAMIANY MIEJSCAMI (Swap z użyciem zmiennej tymczasowej)
$temp = $data['cards'][$id];
$data['cards'][$id] = $data['cards'][$target_id];
$data['cards'][$target_id] = $temp;

// 5. PRZYGOTOWANIE RAPORTU ZMIAN DO LOGÓW
$changes = [
    "zmiana pozycji" => [
        "old" => "Pozycja " . ($id + 1), 
        "new" => "Pozycja " . ($target_id + 1)
    ]
];

// Zapis zaktualizowanej, płaskiej struktury do pliku JSON
file_put_contents($json_file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// 6. REJESTRACJA W LOGACH: Wstrzykujemy nazwę sortowanego elementu wprost do kolumny "Element"
$object_label = "Kolejność: " . $short_title;
log_change("Gdzie pracuję", "Sortowanie", $object_label, $changes);

// 7. Zsynchronizowane przekierowanie z poprawnym znakiem zapytania - BEZ BŁĘDU 404
header("Location: admin.php?page=gdzie-pracuje&status=success");
exit;
