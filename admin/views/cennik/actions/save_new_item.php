<?php
// Plik wywoływany przez: admin.php?page=cennik&action=save_new_item

require_once "log_change.php";

// ZMIANA: Ścieżka do nowego pliku cennika w folderze data/
$json_file = "../data/cennik.json";
if (!file_exists($json_file)) {
    header("Location: admin.php?page=cennik&status=error");
    exit;
}
$data = json_decode(file_get_contents($json_file), true);

// Oczyszczenie danych z formularza
$name  = isset($_POST['name']) ? trim($_POST['name']) : '';
$price = isset($_POST['price']) ? trim($_POST['price']) : '';
$desc  = isset($_POST['desc']) ? trim($_POST['desc']) : '';

// Walidacja – zablokowanie zapisu pustych pól
if (empty($name) || empty($price)) {
    header("Location: admin.php?page=cennik&status=error");
    exit;
}

// Przygotowanie nowej pozycji (zabezpieczenie przed XSS)
$newItem = [
    "name"  => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
    "price" => htmlspecialchars($price, ENT_QUOTES, 'UTF-8'),
    "desc"  => htmlspecialchars($desc, ENT_QUOTES, 'UTF-8')
];

// ZMIANA: Sprawdzamy i tworzymy tablicę items bezpośrednio na głównym poziomie JSON
if (!isset($data['items']) || !is_array($data['items'])) {
    $data['items'] = [];
}

// Dopasowanie nowego elementu na koniec tablicy items
$data['items'][] = $newItem;

// Przygotowanie paczki zmian do logów (wartości "old" słusznie zostają puste)
$changes = [
    "nazwa" => ["old" => "", "new" => $name],
    "cena"  => ["old" => "", "new" => $price],
    "opis"  => ["old" => "", "new" => $desc]
];

// Zapis zaktualizowanej, płaskiej struktury do pliku JSON
file_put_contents($json_file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// Wywołanie funkcji logującej
log_change("Cennik", "Dodanie", "Pozycja", $changes);

header("Location: admin.php?page=cennik&status=success");
exit;
