<?php
// Plik wywoływany przez: admin.php?page=cennik&action=save_item

require_once "log_change.php";

// ZMIANA: Ścieżka do nowego pliku cennika w folderze data/
$json_file = "../data/cennik.json";
if (!file_exists($json_file)) {
    header("Location: admin.php?page=cennik&status=error");
    exit;
}
$data = json_decode(file_get_contents($json_file), true);

$id = isset($_POST['id']) ? intval($_POST['id']) : null;

// ZMIANA: Walidacja ID bezpośrednio w płaskiej strukturze tablicy items
if ($id === null || !isset($data['items'][$id])) {
    header("Location: admin.php?page=cennik&status=error");
    exit;
}

$name  = isset($_POST['name']) ? trim($_POST['name']) : '';
$price = isset($_POST['price']) ? trim($_POST['price']) : '';
$desc  = isset($_POST['desc']) ? trim($_POST['desc']) : '';

if (empty($name) || empty($price)) {
    header("Location: admin.php?page=cennik&status=error");
    exit;
}

// ZMIANA: Kopia zapasowa danych pobierana bezpośrednio z głównego poziomu items
$old = $data['items'][$id];

// ZMIANA: Aktualizacja pozycji na głównym poziomie items
$data['items'][$id] = [
    "name"  => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
    "price" => htmlspecialchars($price, ENT_QUOTES, 'UTF-8'),
    "desc"  => htmlspecialchars($desc, ENT_QUOTES, 'UTF-8')
];

// Przygotowanie paczki zmian do logów
$changes = [
    "nazwa" => ["old" => $old['name'] ?? '', "new" => $name],
    "cena"  => ["old" => $old['price'] ?? '', "new" => $price],
    "opis"  => ["old" => $old['desc'] ?? '', "new" => $desc]
];

// Zapis zaktualizowanej płaskiej struktury do pliku JSON
file_put_contents($json_file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// Wywołanie logowania zmian
log_change("Cennik", "Edycja", "Pozycja", $changes);

header("Location: admin.php?page=cennik&status=success");
exit;
