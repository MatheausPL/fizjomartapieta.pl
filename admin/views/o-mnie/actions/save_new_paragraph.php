<?php
// Plik wywoływany przez: admin.php?page=o-mnie&action=save_new_paragraph

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

// Oczyszczenie tekstu przesłanego z formularza
$text = isset($_POST['text']) ? trim($_POST['text']) : '';

// Walidacja – blokujemy zapis pustego akapitu
if ($text === '') {
    header("Location: admin.php?page=o-mnie&status=error");
    exit;
}

// 3. PRZYGOTOWANIE I DOPISANIE NOWEGO AKAPITU NA KONIEC TABLICY
$new_item = [
    "text" => htmlspecialchars($text, ENT_QUOTES, 'UTF-8')
];

if (!isset($data['items']) || !is_array($data['items'])) {
    $data['items'] = [];
}

$data['items'][] = $new_item;

// 4. PRZYGOTOWANIE ZAAWANSOWANEGO RAPORTU ZMIAN DO LOGÓW
// Wyliczamy numer nowego akapitu na podstawie długości tablicy
$new_index = count($data['items']);
$changes = [
    "[Nowy Akapit " . $new_index . "]" => ["old" => "", "new" => $text]
];

// Zapis zaktualizowanego pliku JSON
file_put_contents($json_file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// Wywołanie rejestracji w dzienniku zmian
log_change("O mnie", "Dodanie", "Akapit opisu", $changes);

// Powrót do panelu głównego z zielonym powiadomieniem Toast
header("Location: admin.php?page=o-mnie&status=success");
exit;
