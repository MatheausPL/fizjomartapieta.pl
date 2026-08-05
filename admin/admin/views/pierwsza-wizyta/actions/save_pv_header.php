<?php
// Plik wywoływany przez: admin.php?page=pierwsza-wizyta&action=save_pv_header

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

// Robimy kopię zapasową oryginalnych danych do śledzenia logów zmian
$old_title    = $data['title'] ?? '';
$old_subtitle = $data['subtitle'] ?? '';

// Oczyszczenie danych z formularza metodą POST
$title    = isset($_POST['title']) ? trim($_POST['title']) : '';
$subtitle = isset($_POST['subtitle']) ? trim($_POST['subtitle']) : '';

// Walidacja – zablokowanie zapisu pustych pól nagłówkowych
if ($title === '' || $subtitle === '') {
    header("Location: admin.php?page=pierwsza-wizyta&status=error");
    exit;
}

// 3. NADPISYWANIE DANYCH BEZPOŚREDNIO NA GŁÓWNYM POZIOMIE JSON
$data['title']    = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
$data['subtitle'] = htmlspecialchars($subtitle, ENT_QUOTES, 'UTF-8');

// Zapis zaktualizowanego pliku JSON
file_put_contents($json_file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// 4. PRZYGOTOWANIE I WYSŁANIE PAR ZMIAN DO DZIENNIKA LOGÓW
$changes = [
    "tytuł" => ["old" => $old_title, "new" => $title],
    "opis"  => ["old" => $old_subtitle, "new" => $subtitle]
];

log_change("Pierwsza wizyta", "Edycja", "Nagłówek", $changes);

// Powrót do panelu z zielonym powiadomieniem sukcesu
header("Location: admin.php?page=pierwsza-wizyta&status=success");
exit;
