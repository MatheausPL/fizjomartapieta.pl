<?php
// Plik wywoływany przez: admin.php?page=gdzie-pracuje&action=save_card

require_once "log_change.php";
require_once __DIR__ . "/../../../config.php"; // Centralna konfiguracja ikon

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

// 3. POBRANIE ID ORAZ PANCERNA WALIDACJA POZYCJI W TABLICY
$id = isset($_POST['id']) ? intval($_POST['id']) : null;

if ($id === null || !isset($data['cards'][$id])) {
    header("Location: admin.php?page=gdzie-pracuje&status=error");
    exit;
}

// Odbieranie i oczyszczanie podstawowych pól formularza
$title     = isset($_POST['title']) ? trim($_POST['title']) : '';
$svg_inner = isset($_POST['svg_inner']) ? trim($_POST['svg_inner']) : '';
$desc      = isset($_POST['desc']) ? trim($_POST['desc']) : '';
$wide      = isset($_POST['wide']) && $_POST['wide'] === '1';

// NOWOŚĆ: Odbieranie stanu checkboxa obsługi ikon graficznych
$has_icon  = isset($_POST['has_icon']) && $_POST['has_icon'] === '1';

if ($title === '' || $desc === '') {
    header("Location: admin.php?page=gdzie-pracuje&status=error");
    exit;
}

// 4. KOPIA ZAPASOWA STARYCH DANYCH KARTY (Do wyliczenia logów zmian)
$old_card    = $data['cards'][$id];
$old_title   = $old_card['title'] ?? '';
$old_desc    = $old_card['desc'] ?? '';
$old_wide    = isset($old_card['wide']) && $old_card['wide'] === true;
$old_has_ico = isset($old_card['has_icon']) && $old_card['has_icon'] === true;
$old_svg     = $old_card['svg_inner'] ?? '';

// WALIDACJA STATUSU STAREJ IKONY DLA CZYTELNOŚCI LOGÓW
if ($old_has_ico === false) {
    $old_icon_status = "wyłączona (czysty tekst)";
} elseif ($old_svg === '' || $old_svg === $config['default_svg']) {
    $old_icon_status = "włączona (domyślna gwiazda systemowa)";
} else {
    $old_icon_status = "włączona (własna ikona SVG)";
}

// 5. INTELIGENTNE PRZETWARZANIE KODU NOWEJ IKONY SVG (ZALEŻNE OD PARAMETRU HAS_ICON)
if ($has_icon === true) {
    if ($svg_inner === '') {
        $svg_inner = $config['default_svg'];
        $new_icon_status = "włączona (domyślna gwiazda systemowa)";
    } else {
        if (preg_match('/<svg[^>]*>(.*?)<\/svg>/is', $svg_inner, $matches)) {
            if (!empty($matches)) { $svg_inner = $matches[1]; }
        }
        $svg_inner = preg_replace('/<!--.*?-->/s', '', $svg_inner);
        $svg_inner = preg_replace('/<(script|iframe|style|html|body)\b[^>]*>(.*?)<\/\1>/is', "", $svg_inner);
        $svg_inner = preg_replace('/on\w+\s*=\s*".*?"/i', '', $svg_inner);
        $svg_inner = preg_replace('/on\w+\s*=\s*\'.*?\'/i', '', $svg_inner);
        
        // PANCERNA UNIFIKACJA: Zamieniamy cudzysłowy na apostrofy, usuwając problem backslashy w JSON
        $svg_inner = str_replace('"', "'", $svg_inner);
        $svg_inner = trim($svg_inner);
        $new_icon_status = "włączona (własna ikona SVG)";
    }
} else {
    // Jeśli checkbox nie jest zaznaczony - bezwzględnie czyścimy kod grafiki do zera
    $svg_inner = '';
    $new_icon_status = "wyłączona (czysty tekst bez kontenera)";
}

// 6. MAPOWANIE NOWYCH WIERSZY SPECYFIKACJI DETAILS
$post_details = isset($_POST['details']) && is_array($_POST['details']) ? $_POST['details'] : [];
$clean_details = [];

foreach ($post_details as $row) {
    $row_label = isset($row['label']) ? trim($row['label']) : '';
    $row_value = isset($row['value']) ? trim($row['value']) : '';
    
    if ($row_label === '' && $row_value === '') {
        continue;
    }
    
    $is_link  = isset($row['is_link']) && ($row['is_link'] === '1' || $row['is_link'] === 1);
    $link_url = isset($row['url']) ? trim($row['url']) : '';
    
    if ($is_link && !filter_var($link_url, FILTER_VALIDATE_URL)) {
        $link_url = '';
        $is_link = false;
    }

    $clean_details[] = [
        "label"   => htmlspecialchars($row_label, ENT_QUOTES, 'UTF-8'),
        "value"   => htmlspecialchars($row_value, ENT_QUOTES, 'UTF-8'),
        "is_link" => $is_link,
        "url"     => $link_url
    ];
}

// 7. ZAPIS ZAKTUALIZOWANEJ KARTY DO GŁÓWNEJ TABLICY
$data['cards'][$id] = [
    "title"     => htmlspecialchars($title, ENT_QUOTES, 'UTF-8'),
    "has_icon"  => $has_icon, // Zapis logicznej flagi true/false do bazy JSON
    "svg_inner" => $svg_inner,
    "desc"      => htmlspecialchars($desc, ENT_QUOTES, 'UTF-8'),
    "wide"      => $wide,
    "details"   => $clean_details
];

// 8. PRZYGOTOWANIE ZAAWANSOWANEGO RAPORTU DLA CZYTELNOŚCI LOGÓW
$changes = [
    "nazwa lokalizacji" => ["old" => $old_title, "new" => $title],
    "krótki opis" => ["old" => $old_desc, "new" => $desc],
    "szeroki układ" => ["old" => $old_wide ? "tak, szeroki" : "nie, standardowy", "new" => $wide ? "tak, szeroki" : "nie, standardowy"],
    "liczba wpisów specyfikacji" => ["old" => count($old_card['details'] ?? []), "new" => count($clean_details)]
];

if ($old_has_ico !== $has_icon || $old_svg !== $svg_inner) {
    $changes["obsługa ikony graficznej"] = ["old" => $old_icon_status, "new" => $new_icon_status];
}

// Zapis struktury do bazy danych JSON
file_put_contents($json_file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// Wywołanie rejestru w logach - bąbelek precyzyjnie wskaże nazwę edytowanego kafelka
$object_label = "Modyfikacja: " . mb_strimwidth($old_title, 0, 25, "...");
log_change("Gdzie pracuję", "Edycja", $object_label, $changes);

// Powrót z zielonym Toastem sukcesu
header("Location: admin.php?page=gdzie-pracuje&status=success");
exit;
