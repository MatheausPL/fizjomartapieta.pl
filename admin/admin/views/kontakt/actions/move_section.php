<?php
// Plik wywoływany przez: admin.php?page=kontakt&action=move_section&id=X&direction=up|down

require_once "log_change.php";

// 1. USTALENIE POPRAWNEJ ŚCIEŻKI DO PLIKU DANYCH JSON
$json_file = "../data/kontakt.json";
if (!file_exists($json_file)) {
    $json_file = "data/kontakt.json";
    if (!file_exists($json_file)) {
        header("Location: admin.php?page=kontakt&status=error");
        exit;
    }
}

// 2. WCZYTANIE I DEKODOWANIE AKTUALNYCH DANYCH
$data = json_decode(file_get_contents($json_file), true) ?? [];

// Pobranie i walidacja ID oraz kierunku z adresu URL (metoda GET)
$id = isset($_GET['id']) ? intval($_GET['id']) : null;
$direction = isset($_GET['direction']) ? $_GET['direction'] : '';

if ($id === null || !isset($data['sections'][$id]) || !in_array($direction, ['up', 'down'])) {
    header("Location: admin.php?page=kontakt&status=error");
    exit;
}

// Wyliczamy indeks elementu sąsiedniego, z którym dokonamy zamiany miejscami
$target_id = ($direction === 'up') ? $id - 1 : $id + 1;

// Zabezpieczenie przed wyjściem poza granice tablicy numerycznej
if (!isset($data['sections'][$target_id])) {
    header("Location: admin.php?page=kontakt&status=error");
    exit;
}

// 3. POBRANIE NAZWY SORTOWANEJ KARTY DO LOGÓW PRZED PRZETASOWANIEM
$moved_title = $data['sections'][$id]['title'] ?? '';
$short_title = mb_strimwidth($moved_title, 0, 30, "...");

// 4. MECHANIZM ZAMIANY MIEJSCAMI (Swap przy użyciu zmiennej pomocniczej temp)
$temp = $data['sections'][$id];
$data['sections'][$id] = $data['sections'][$target_id];
$data['sections'][$target_id] = $temp;

// 5. PRZYGOTOWANIE LOGÓW ZMIAN DLA HISTORII DZIAŁANIA SYSTEMU
$changes = [
    "przesunięcie wariantu" => [
        "old" => "Pozycja nr " . ($id + 1),
        "new" => "Pozycja nr " . ($target_id + 1)
    ]
];

// Zapis uporządkowanego formatu do pliku JSON
file_put_contents($json_file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// Wywołanie rejestracji w dzienniku zmian administratora
$object_label = "Kolejność: " . $short_title;
log_change("Kontakt", "Sortowanie", $object_label, $changes);

// 6. PRZEKIEROWANIE Z ZASTOSOWANIEM POPRAWNEGO ZNAKU ZAPYTANIA - ZERO BŁĘDÓW 404
header("Location: admin.php?page=kontakt&status=success");
exit;
