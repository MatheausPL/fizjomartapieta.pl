<?php
// Plik wywoływany przez: admin.php?page=o-mnie&action=save_focus

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

// 3. POBRANIE ID ORAZ WALIDACJA METODĄ POST
$id = isset($_POST['id']) ? intval($_POST['id']) : null;

// Sprawdzamy obecność szukanego indeksu w płaskiej tablicy focus_items
if ($id === null || !isset($data['focus_items'][$id])) {
    header("Location: admin.php?page=o-mnie&status=error");
    exit;
}

// 4. OCZYSZCZENIE DANYCH Z FORMULARZA
$text = isset($_POST['text']) ? trim($_POST['text']) : '';

if ($text === '') {
    header("Location: admin.php?page=o-mnie&status=error");
    exit;
}

// 5. KOPIA ZAPASOWA STAREGO TEKSTU (Przed nadpisaniem)
$old_item = $data['focus_items'][$id];
$old_text = $old_item['text'] ?? '';

// 6. AKTUALIZACJA WŁAŚCIWEGO PUNKTU W PLIKU JSON
$data['focus_items'][$id] = [
    "text" => htmlspecialchars($text, ENT_QUOTES, 'UTF-8')
];

// 7. PRZYGOTOWANIE LOGU ZMIAN (Poprawiona, czysta składnia)
$changes = [
    "[Edycja Punktu Skupienia " . ($id + 1) . "]" => ["old" => $old_text, "new" => $text]
];

// Zapis zaktualizowanej, płaskiej struktury do pliku JSON
file_put_contents($json_file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// Wywołanie rejestracji w dzienniku zmian
log_change("O mnie", "Edycja", "Punkt listy skupienia", $changes);

// Powrót do panelu z zielonym powiadomieniem sukcesu
header("Location: admin.php?page=o-mnie&status=success");
exit;
