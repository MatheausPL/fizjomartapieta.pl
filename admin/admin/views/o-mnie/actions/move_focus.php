<?php
// Plik wywoływany przez: admin.php?page=o-mnie&action=move_focus&id=X&direction=up|down

require_once "log_change.php";

// 1. USTALENIE POPRAWNEJ ŚCIEŻKI DO PLIKU
$json_file = "../data/o-mnie.json";

if (!file_exists($json_file)) {
    $json_file = "data/o-mnie.json";
    if (!file_exists($json_file)) {
        header("Location: admin.php?page=o-mnie&status=error");
        exit;
    }
}

// 2. WCZYTANIE I DEKODOWANIE AKTUALNYCH DANYCH
$data = json_decode(file_get_contents($json_file), true) ?? [];

// Pobranie i walidacja ID oraz kierunku przesunięcia
$id = isset($_GET['id']) ? intval($_GET['id']) : null;
$direction = isset($_GET['direction']) ? $_GET['direction'] : '';

if ($id === null || !isset($data['focus_items'][$id]) || !in_array($direction, ['up', 'down'])) {
    header("Location: admin.php?page=o-mnie&status=error");
    exit;
}

// Wyliczamy indeks elementu, z którym chcemy dokonać zamiany
$target_id = ($direction === 'up') ? $id - 1 : $id + 1;

// Sprawdzamy, czy element docelowy fizycznie istnieje w tablicy (zabezpieczenie przed wyjściem poza zakres)
if (!isset($data['focus_items'][$target_id])) {
    header("Location: admin.php?page=o-mnie&status=error");
    exit;
}

// 3. POBRANIE NAZWY PUNKTU DO LOGÓW PRZED ZAMIANĄ
$moved_text = $data['focus_items'][$id]['text'] ?? '';
$short_text = mb_strimwidth($moved_text, 0, 40, "...");

// 4. LOGIKA ZAMIANY MIEJSCAMI (Swap z użyciem zmiennej tymczasowej)
$temp = $data['focus_items'][$id];
$data['focus_items'][$id] = $data['focus_items'][$target_id];
$data['focus_items'][$target_id] = $temp;

// 5. PRZYGOTOWANIE RAPORTU ZMIAN DO LOGÓW (W zaawansowanym formacie old/new)
$changes = [
    "Przesunięcie punktu" => [
        "old" => "Pozycja " . ($id + 1) . " (" . $short_text . ")", 
        "new" => "Pozycja " . ($target_id + 1)
    ]
];

// Zapis zaktualizowanej, płaskiej struktury do pliku JSON
file_put_contents($json_file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// Wywołanie rejestracji w dzienniku zmian
log_change("O mnie", "Sortowanie", "Kolejność punktów skupienia", $changes);

// Powrót do panelu głównego z zielonym powiadomieniem Toast o sukcesie
header("Location: admin.php?page=o-mnie&status=success");
exit;
