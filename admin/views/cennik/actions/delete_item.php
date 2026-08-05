<?php
// Plik wywoływany przez: admin.php?page=cennik&action=delete_item&id=X

require_once "log_change.php";

// ZMIANA: Ścieżka do nowego pliku cennika w folderze data/
$json_file = "../data/cennik.json";

if (!file_exists($json_file)) {
    header("Location: admin.php?page=cennik&status=error");
    exit;
}

$data = json_decode(file_get_contents($json_file), true);

// Pobranie i walidacja ID elementu do usunięcia
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

// ZMIANA: Walidacja ID bezpośrednio w płaskiej strukturze tablicy items
if ($id === null || !isset($data['items'][$id])) {
    header("Location: admin.php?page=cennik&status=error");
    exit;
}

// ZMIANA: Kopia zapasowa danych pobierana bezpośrednio z głównego poziomu items
$deleted_item = $data['items'][$id];

// Przygotowanie paczki zmian do logów (wartości "new" słusznie zostają puste)
$changes = [
    "nazwa" => ["old" => $deleted_item['name'] ?? '', "new" => ""],
    "cena"  => ["old" => $deleted_item['price'] ?? '', "new" => ""],
    "opis"  => ["old" => $deleted_item['desc'] ?? '', "new" => ""]
];

// ZMIANA: Usunięcie elementu z tablicy items bezpośrednio na głównym poziomie JSON
unset($data['items'][$id]);

// ZMIANA: Przeindeksowanie tablicy items od zera
$data['items'] = array_values($data['items']);

// Zapis zaktualizowanej, płaskiej struktury do pliku JSON
file_put_contents($json_file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// Wywołanie funkcji logującej
log_change("Cennik", "Usunięcie", "Pozycja", $changes);

header("Location: admin.php?page=cennik&status=success");
exit;
