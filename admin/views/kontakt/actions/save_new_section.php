<?php
// Plik wywoływany przez: admin.php?page=kontakt&action=save_new_section

require_once "log_change.php";
require_once __DIR__ . "/../../../config.php"; // Centralna konfiguracja ikon

$json_file = "../data/kontakt.json";
if (!file_exists($json_file)) {
    $json_file = "data/kontakt.json";
    if (!file_exists($json_file)) {
        header("Location: admin.php?page=kontakt&status=error");
        exit;
    }
}

$data = json_decode(file_get_contents($json_file), true) ?? [];

// Odbieranie podstawowych nagłówków z formularza
$title     = isset($_POST['title']) ? trim($_POST['title']) : '';
$svg_inner = isset($_POST['svg_inner']) ? trim($_POST['svg_inner']) : '';
$wide      = isset($_POST['wide']) && $_POST['wide'] === '1';

// NOWOŚĆ: Pobranie stanu checkboxa obsługi ikon
$has_icon  = isset($_POST['has_icon']) && $_POST['has_icon'] === '1';

if ($title === '') {
    header("Location: admin.php?page=kontakt&status=error");
    exit;
}

// NOWOŚĆ: Logika przetwarzania ikony w zależności od flagi has_icon
if ($has_icon === true) {
    if ($svg_inner === '') {
        $svg_inner = $config['default_svg']; // Podstawienie zaokrąglonej gwiazdy z config.php
        $log_icon_status = "włączona (domyślna gwiazda systemowa)";
    } else {
        // Wycinanie i czyszczenie ścieżek kodu SVG pod kątem Integralności JSON
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
    $svg_inner = ''; // Jeśli wyłączono - czyścimy pole ścieżek do zera
    $log_icon_status = "wyłączona (czysty tekst bez kontenera)";
}

// 4. MAPOWANIE WIERSZY FIELDS (TABLICA WIELOWYMIAROWA)
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

    if ($row_type === 'tel') {
        $link_val = str_replace(' ', '', $link_val); 
    }

    $clean_fields[] = [
        "label"      => htmlspecialchars($row_label, ENT_QUOTES, 'UTF-8'),
        "value"      => htmlspecialchars($row_value, ENT_QUOTES, 'UTF-8'),
        "type"       => htmlspecialchars($row_type, ENT_QUOTES, 'UTF-8'),
        "link_value" => htmlspecialchars($link_val, ENT_QUOTES, 'UTF-8')
    ];
}

// 5. BUDOWANIE ZAKTUALIZOWANEJ STRUKTURY NOWEJ KARTY
$new_section = [
    "title"     => htmlspecialchars($title, ENT_QUOTES, 'UTF-8'),
    "has_icon"  => $has_icon, // Zapis logicznego true/false do bazy JSON
    "svg_inner" => $svg_inner,
    "wide"      => $wide,
    "fields"    => $clean_fields
];

if (!isset($data['sections']) || !is_array($data['sections'])) {
    $data['sections'] = [];
}
$data['sections'][] = $new_section;

// 6. RAPORT ZMIAN DO HISTORII DZIENNIKA ADMINISTRATORA
$new_index = count($data['sections']);
$changes = [
    "[Nowa Karta Kontaktu " . $new_index . "]" => ["old" => "[nowy element]", "new" => $title],
    "szeroki panel" => ["old" => "wyjściowo: standardowy", "new" => $wide ? "tak" : "nie"],
    "ikona nagłówka" => ["old" => "wyjściowo: brak", "new" => $log_icon_status],
    "liczba pozycji danych" => ["old" => "0", "new" => count($clean_fields)]
];

file_put_contents($json_file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
log_change("Kontakt", "Dodanie", "Karta informacji", $changes);

header("Location: admin.php?page=kontakt&status=success");
exit;
