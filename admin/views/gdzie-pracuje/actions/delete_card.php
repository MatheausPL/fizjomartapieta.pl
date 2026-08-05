<?php
// Plik wywoływany przez: admin.php?page=gdzie-pracuje&action=delete_card&id=X

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

// Pobranie i walidacja ID elementu do usunięcia z adresu URL (metoda GET)
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

// Szukamy pozycji w dynamicznej strukturze tablicy cards
if ($id === null || !isset($data['cards'][$id])) {
    header("Location: admin.php?page=gdzie-pracuje&status=error");
    exit;
}

// 3. KOPIA ZAPASOWA DANYCH PRZED USUNIĘCIEM (Do szczegółowych logów systemowych)
$deleted_card  = $data['cards'][$id];
$deleted_title = $deleted_card['title'] ?? '';
$deleted_desc  = $deleted_card['desc'] ?? '';
$details_count = isset($deleted_card['details']) && is_array($deleted_card['details']) ? count($deleted_card['details']) : 0;

// 4. PRZYGOTOWANIE PACZKI ZMIAN W CZYTELNYM DLA CZŁOWIEKA FORMACIE
$changes = [
    "status lokalizacji" => ["old" => "aktywna na stronie", "new" => "[Usunięto bezpowrotnie]"],
    "krótki opis przed usunięciem" => ["old" => $deleted_desc, "new" => ""],
    "usunięta liczba linii specyfikacji" => ["old" => $details_count, "new" => "0"]
];

// 5. Usunięcie wybranej karty z tablicy w pamięci serwera PHP
unset($data['cards'][$id]);

// 6. Przeindeksowanie tablicy od zera (naprawa indeksów numerycznych 0, 1, 2...)
$data['cards'] = array_values($data['cards']);

// 7. Zapis zaktualizowanej struktury z zachowaniem kodowania Unicode do pliku JSON
file_put_contents($json_file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// 8. REJESTRACJA W LOGACH: Wstrzykujemy nazwę skasowanego elementu wprost do kolumny "Element"
$object_label = "Kasowanie: " . mb_strimwidth($deleted_title, 0, 25, "...");
log_change("Gdzie pracuję", "Usunięcie", $object_label, $changes);

// Powrót do widoku głównego sekcji z wywołaniem zielonego Toasta sukcesu
header("Location: admin.php?page=gdzie-pracuje&status=success");
exit;
