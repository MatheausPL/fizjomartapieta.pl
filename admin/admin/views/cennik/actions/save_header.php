<?php
// Plik wywoływany przez: admin.php?page=cennik&action=save_header

require_once "log_change.php";

// ZMIANA: Nowa ścieżka do pliku cennika w folderze data/
$json_file = "../data/cennik.json";
if (!file_exists($json_file)) {
    header("Location: admin.php?page=cennik&status=error");
    exit;
}
$data = json_decode(file_get_contents($json_file), true);

// ZMIANA: Pobieramy stare wartości bezpośrednio z tablicy głównej (bez klucza 'cennik')
$old_title    = $data['title'] ?? '';
$old_subtitle = $data['subtitle'] ?? '';

// ZMIANA: Nadpisujemy dane bezpośrednio na głównym poziomie pliku JSON
$data['title']    = $_POST['title'] ?? '';
$data['subtitle'] = $_POST['subtitle'] ?? '';

// Zapis zaktualizowanej, płaskiej struktury do pliku JSON
file_put_contents($json_file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// Przygotowanie paczki zmian dla zaawansowanego systemu logów
$changes = [
    "tytuł" => ["old" => $old_title, "new" => $_POST['title'] ?? ''],
    "opis"  => ["old" => $old_subtitle, "new" => $_POST['subtitle'] ?? '']
];

log_change("Cennik", "Edycja", "Nagłówek", $changes);

header("Location: admin.php?page=cennik&status=success");
exit;
