<?php
// Plik wywoływany przez: admin.php?page=kontakt&action=save_section

require_once "log_change.php";
require_once __DIR__ . "/../../../config.php"; // Centralna konfiguracja ikon

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

// Pobranie ID modyfikowanej karty oraz pancerna walidacja pozycji w tablicy
$id = isset($_POST['id']) ? intval($_POST['id']) : null;

if ($id === null || !isset($data['sections'][$id])) {
    header("Location: admin.php?page=kontakt&status=error");
    exit;
}

// Kopia zapasowa starej karty do precyzyjnego wyliczenia historii zmian
$old_section = $data['sections'][$id];
$old_title   = $old_section['title'] ?? '';
$old_wide    = isset($old_section['wide']) && $old_section['wide'] === true;
$old_has_ico = isset($old_section['has_icon']) && $old_section['has_icon'] === true;
$old_svg     = $old_section['svg_inner'] ?? '';

// Odbieranie nowych wartości nagłówkowych z formularza
$title     = isset($_POST['title']) ? trim($_POST['title']) : '';
$svg_inner = isset($_POST['svg_inner']) ? trim($_POST['svg_inner']) : '';
$wide      = isset($_POST['wide']) && $_POST['wide'] === '1';
$has_icon  = isset($_POST['has_icon']) && $_POST['has_icon'] === '1';

if ($title === '') {
    header("Location: admin.php?page=kontakt&status=error");
    exit;
}

// 3. SELEKTYWNA WALIDACJA I OCZYSZCZANIE KODU NOWEJ IKONY SVG
if ($has_icon === true) {
    if ($svg_inner === '') {
        $svg_inner = $config['default_svg']; // Gwiazda systemowa przy braku wpisu
        $log_icon_status = "włączona (domyślna gwiazda systemowa)";
    } else {
        if (preg_match('/<svg[^>]*>(.*?)<\/svg>/is', $svg_inner, $matches)) {
            if (!empty($matches)) { $svg_inner = $matches; }
        }
        $svg_inner = preg_replace('/<!--.*?-->/s', '', $svg_inner);
        $svg_inner = preg_replace('/<(script|iframe|style|html|body)\b[^>]*>(.*?)<\/\1>/is', "", $svg_inner);
        $svg_inner = str_replace('"', "'", $svg_inner);
        $svg_inner = trim($svg_inner);
        $log_icon_status = "włączona (własna oczyszczona ikona)";
    }
} else {
    $svg_inner = ''; // Jeśli wyłączono - pole tekstowe czyszczone do zera
    $log_icon_status = "wyłączona (czysty tekst bez ikony)";
}

// 4. MAPOWANIE I SELEKTYWNA WALIDACJA WIERSZY DANYCH KONTAKTOWYCH
$post_fields = isset($_POST['fields']) && is_array($_POST['fields']) ? $_POST['fields'] : [];
$clean_fields = [];

foreach ($post_fields as $row) {
    if (!is_array($row)) { continue; }

    $row_label = isset($row['label']) ? trim($row['label']) : '';
    $row_value = isset($row['value']) ? trim($row['value']) : '';
    
    if ($row_label === '' && $row_value === '') {
        continue;
    }
    
    $row_type  = isset($row['type']) ? trim($row['type']) : 'text';
    $link_val  = isset($row['link_value']) ? trim($row['link_value']) : '';

    // SELEKTYWNA WALIDACJA - Działa w zależności od przypisanego typu pola w wierszu
    if ($row_type === 'tel') {
        $link_val = str_replace(' ', '', $link_val); // Oczyszczanie numerów telefonów ze spacji
    } elseif ($row_type === 'email') {
        $link_val = filter_var($link_val, FILTER_SANITIZE_EMAIL); // Oczyszczanie tekstu maila
    } elseif ($row_type === 'link') {
        if (!filter_var($link_val, FILTER_VALIDATE_URL)) {
            $row_type = 'text';
            $link_val = '';
        }
    } else {
        $link_val = '';
    }

    $clean_fields[] = [
        "label"      => htmlspecialchars($row_label, ENT_QUOTES, 'UTF-8'),
        "value"      => htmlspecialchars($row_value, ENT_QUOTES, 'UTF-8'),
        "type"       => htmlspecialchars($row_type, ENT_QUOTES, 'UTF-8'),
        "link_value" => htmlspecialchars($link_val, ENT_QUOTES, 'UTF-8')
    ];
}

// 5. NADPISANIE MODYFIKOWANEJ KARTY W GŁÓWNEJ BAZIE JSON
$data['sections'][$id] = [
    "title"     => htmlspecialchars($title, ENT_QUOTES, 'UTF-8'),
    "has_icon"  => $has_icon,
    "svg_inner" => $svg_inner,
    "wide"      => $wide,
    "fields"    => $clean_fields
];

// 6. PRZYGOTOWANIE RAPORTU MÓWICEGO O ZMIANACH DLA DZIENNIKA LOGÓW
$changes = [
    "nazwa karty" => ["old" => $old_title, "new" => $title],
    "szeroki układ" => ["old" => $old_wide ? "tak" : "nie", "new" => $wide ? "tak" : "nie"],
    "liczba pozycji danych" => ["old" => count($old_section['fields'] ?? []), "new" => count($clean_fields)]
];

if ($old_has_ico !== $has_icon || $old_svg !== $svg_inner) {
    $changes["obsługa ikony nagłówka"] = ["old" => $old_has_ico ? "aktywna" : "wyłączona", "new" => $log_icon_status];
}

// Zapis uporządkowanego formatu JSON na serwerze z zachowaniem kodowania Unicode
file_put_contents($json_file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// Rejestracja zdarzenia w Twoim stronicowanym dzienniku logów
$object_label = "Modyfikacja: " . mb_strimwidth($old_title, 0, 25, "...");
log_change("Kontakt", "Edycja", $object_label, $changes);

// Powrót do widoku głównego z wywołaniem zielonego Toasta sukcesu
header("Location: admin.php?page=kontakt&status=success");
exit;
