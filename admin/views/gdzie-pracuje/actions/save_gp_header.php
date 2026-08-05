<?php
// Plik wywoływany przez: admin.php?page=gdzie-pracuje&action=save_gp_header

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

// Kopia zapasowa do precyzyjnego wyliczenia logów zmian
$old_title    = $data['title'] ?? '';
$old_subtitle = $data['subtitle'] ?? '';

// Odbieranie i oczyszczanie danych metodą POST
$title    = isset($_POST['location_title']) ? trim($_POST['location_title']) : '';
$subtitle = isset($_POST['location_subtitle']) ? trim($_POST['location_subtitle']) : '';

// Walidacja – blokujemy zapis w przypadku pustych nagłówków
if ($title === '' || $subtitle === '') {
    header("Location: admin.php?page=gdzie-pracuje&status=error");
    exit;
}

// 3. NADPISANIE PARAMETRÓW NAGŁÓWKOWYCH GLOBALNYCH
$data['title']    = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
$data['subtitle'] = htmlspecialchars($subtitle, ENT_QUOTES, 'UTF-8');

// 4. PRZYGOTOWANIE I REJESTRACJA RAPORTU ZMIAN DLA CZŁOWIEKA
$changes = [
    "tytuł główny sekcji" => ["old" => $old_title, "new" => $title],
    "tekst wprowadzający" => ["old" => $old_subtitle, "new" => $subtitle]
];

log_change("Gdzie pracuję", "Edycja", "Nagłówki opisu", $changes);

// 5. ZAPIS DO PLIKU JSON I POWRÓT Z ZIELONYM TOASTEM SUKCESU
file_put_contents($json_file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

header("Location: admin.php?page=gdzie-pracuje&status=success");
exit;
