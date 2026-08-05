<?php
// Plik wywoływany przez: admin.php?page=pierwsza-wizyta&action=delete_step&id=X

require_once "log_change.php";

// 1. USTALENIE POPRAWNEJ ŚCIEŻKI DO PLIKU
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

// Pobranie i walidacja ID elementu do usunięcia
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

// Szukamy pozycji w płaskiej strukturze tablicy steps
if ($id === null || !isset($data['steps'][$id])) {
    header("Location: admin.php?page=pierwsza-wizyta&status=error");
    exit;
}

// 3. KOPIA ZAPASOWA DANYCH PRZED USUNIĘCIEM (Do szczegółowych logów)
$deleted_step = $data['steps'][$id];
$deleted_title = $deleted_step['title'] ?? '';
$deleted_desc  = $deleted_step['desc'] ?? '';

// 4. PRZYGOTOWANIE PACZKI ZMIAN
$changes = [
    "status etapu" => ["old" => "aktywny na stronie", "new" => "[Usunięto bezpowrotnie]"],
    "treść opisu przed usunięciem" => ["old" => $deleted_desc, "new" => ""]
];

// 5. Usunięcie elementu z tablicy w pamięci PHP
unset($data['steps'][$id]);

// 6. Przeindeksowanie tablicy od zera (naprawa luk w indeksach)
$data['steps'] = array_values($data['steps']);

// 7. Zapis zaktualizowanej struktury do pliku JSON
file_put_contents($json_file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// 8. REJESTRACJA W LOGACH: Wstrzykujemy nazwę usuwanego kroku do kolumny "Element"
$object_label = "Usunięto krok: " . mb_strimwidth($deleted_title, 0, 25, "...");
log_change("Pierwsza wizyta", "Usunięcie", $object_label, $changes);

// Powrót do panelu z zielonym powiadomieniem Toast o sukcesie
header("Location: admin.php?page=pierwsza-wizyta&status=success");
exit;
