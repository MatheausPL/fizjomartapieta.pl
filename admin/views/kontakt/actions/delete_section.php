<?php
// Plik wywoływany przez: admin.php?page=kontakt&action=delete_section&id=X

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

// Pobranie i walidacja ID elementu do usunięcia z adresu URL (metoda GET)
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

// Szukamy pozycji w dynamicznej strukturze tablicy sections
if ($id === null || !isset($data['sections'][$id])) {
    header("Location: admin.php?page=kontakt&status=error");
    exit;
}

// 3. KOPIA ZAPASOWA DANYCH PRZED USUNIĘCIEM (Do szczegółowych logów systemowych)
$deleted_section = $data['sections'][$id];
$deleted_title   = $deleted_section['title'] ?? '';
$fields_count    = isset($deleted_section['fields']) && is_array($deleted_section['fields']) ? count($deleted_section['fields']) : 0;

// 4. PRZYGOTOWANIE PACZKI ZMIAN W CZYTELNYM DLA ADMINISTRATORA FORMACIE
$changes = [
    "status karty kontaktu" => ["old" => "aktywna na stronie", "new" => "[Usunięto bezpowrotnie]"],
    "usunięta liczba pozycji danych" => ["old" => $fields_count, "new" => "0"]
];

// 5. Usunięcie wybranej karty z tablicy w pamięci serwera PHP
unset($data['sections'][$id]);

// 6. Przeindeksowanie tablicy od zera (naprawa indeksów numerycznych 0, 1, 2...)
$data['sections'] = array_values($data['sections']);

// 7. Zapis zaktualizowanej struktury do pliku JSON z zachowaniem kodowania Unicode
file_put_contents($json_file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// 8. REJESTRACJA W LOGACH: Wstrzykujemy nazwę skasowanego elementu wprost do kolumny "Element"
$object_label = "Kasowanie: " . mb_strimwidth($deleted_title, 0, 25, "...");
log_change("Kontakt", "Usunięcie", $object_label, $changes);

// Powrót do widoku głównego sekcji z wywołaniem zielonego Toasta sukcesu
header("Location: admin.php?page=kontakt&status=success");
exit;
