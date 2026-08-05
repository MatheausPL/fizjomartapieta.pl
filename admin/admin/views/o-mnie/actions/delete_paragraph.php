<?php
// Plik wywoływany przez: admin.php?page=o-mnie&action=delete_paragraph&id=X

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

// Pobranie i walidacja ID elementu do usunięcia
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

// Szukamy pozycji w płaskiej strukturze tablicy items
if ($id === null || !isset($data['items'][$id])) {
    header("Location: admin.php?page=o-mnie&status=error");
    exit;
}

// 3. KOPIA ZAPASOWA DANYCH PRZED USUNIĘCIEM (Do logów systemowych)
$deleted_item = $data['items'][$id];
$deleted_text = $deleted_item['text'] ?? '';

// 4. PRZYGOTOWANIE PACZKI ZMIAN (Wartość "new" zostaje pusta, bo element znika)
$changes = [
    "[Usunięto Akapit " . ($id + 1) . "]" => ["old" => $deleted_text, "new" => ""]
];

// 5. Usunięcie elementu z tablicy w pamięci PHP
unset($data['items'][$id]);

// 6. Przeindeksowanie tablicy od zera (naprawa luk w indeksach)
$data['items'] = array_values($data['items']);

// 7. Zapis zaktualizowanej struktury do pliku JSON
file_put_contents($json_file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// 8. Wywołanie rejestracji w dzienniku zmian
log_change("O mnie", "Usunięcie", "Akapit opisu", $changes);

// Powrót do panelu z zielonym powiadomieniem Toast o sukcesie
header("Location: admin.php?page=o-mnie&status=success");
exit;
