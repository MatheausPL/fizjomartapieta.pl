<?php
// Plik wywoływany przez: admin.php?page=kontakt&action=save_kontakt_header

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

// Kopia zapasowa do precyzyjnego wyliczenia historii zmian w dzienniku
$old_title    = $data['title'] ?? '';
$old_subtitle = $data['subtitle'] ?? '';

// Odbieranie i oczyszczanie wartości tekstowych metodą POST
$title    = isset($_POST['contact_title']) ? trim($_POST['contact_title']) : '';
$subtitle = isset($_POST['contact_subtitle']) ? trim($_POST['contact_subtitle']) : '';

if ($title === '' || $subtitle === '') {
    header("Location: admin.php?page=kontakt&status=error");
    exit;
}

// 3. NADPISANIE PARAMETRÓW NAGŁÓWKOWYCH GLOBALNYCH
$data['title']    = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
$data['subtitle'] = htmlspecialchars($subtitle, ENT_QUOTES, 'UTF-8');

// 4. REJESTRACJA I PRZYGOTOWANIE LOGÓW ZMIAN DLA ADMINISTRATORA
$changes = [
    "tytuł główny kontaktu" => ["old" => $old_title, "new" => $title],
    "tekst wprowadzający kontaktu" => ["old" => $old_subtitle, "new" => $subtitle]
];

log_change("Kontakt", "Edycja", "Nagłówki opisu", $changes);

// 5. ZAPIS DO PLIKU JSON I NATYCHMIASTOWY POWRÓT Z ZIELONYM TOASTEM SUKCESU
file_put_contents($json_file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

header("Location: admin.php?page=kontakt&status=success");
exit;
