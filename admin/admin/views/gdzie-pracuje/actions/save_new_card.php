<?php
// Plik wywoływany przez: admin.php?page=gdzie-pracuje&action=save_new_card

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

// Odbieranie i oczyszczanie podstawowych pól formularza
$title     = isset($_POST['title']) ? trim($_POST['title']) : '';
$svg_inner = isset($_POST['svg_inner']) ? trim($_POST['svg_inner']) : '';
$desc      = isset($_POST['desc']) ? trim($_POST['desc']) : '';
$wide      = isset($_POST['wide']) && $_POST['wide'] === '1';

// NOWOŚĆ: Pobranie stanu checkboxa obsługi ikon graficznych
$has_icon  = isset($_POST['has_icon']) && $_POST['has_icon'] === '1';

// Pancerne blokowanie zapisu w przypadku braku kluczowych tekstów
if ($title === '' || $desc === '') {
    header("Location: admin.php?page=gdzie-pracuje&status=error");
    exit;
}

// 3. INTELIGENTNE PRZETWARZANIE KODU IKONY SVG (ZALEŻNE OD PARAMETRU HAS_ICON)
if ($has_icon === true) {
    if ($svg_inner === '') {
        $svg_inner = $config['default_svg'];
        $log_icon_status = "włączona (domyślna gwiazda z konfiguracji globalnej)";
    } else {
        // Jeśli wklejono pełny kontener <svg>, wycinamy tylko to co w środku
        if (preg_match('/<svg[^>]*>(.*?)<\/svg>/is', $svg_inner, $matches)) {
            if (!empty($matches)) { $svg_inner = $matches[1]; }
        }
        // Filtrowanie niebezpiecznych tagów XML/HTML i zdarzeń JS
        $svg_inner = preg_replace('/<!--.*?-->/s', '', $svg_inner);
        $svg_inner = preg_replace('/<(script|iframe|style|html|body)\b[^>]*>(.*?)<\/\1>/is', "", $svg_inner);
        $svg_inner = preg_replace('/on\w+\s*=\s*".*?"/i', '', $svg_inner);
        $svg_inner = preg_replace('/on\w+\s*=\s*\'.*?\'/i', '', $svg_inner);
        
        // Unifikacja cudzysłowów na apostrofy dla bezwzględnego bezpieczeństwa JSON
        $svg_inner = str_replace('"', "'", $svg_inner);
        $svg_inner = trim($svg_inner);
        $log_icon_status = "włączona (własny oczyszczony kod ścieżek SVG)";
    }
} else {
    // Jeśli checkbox nie jest zaznaczony - bezwzględnie czyścimy kod grafiki do zera
    $svg_inner = '';
    $log_icon_status = "wyłączona (czysty tekst bez kontenera)";
}

// 4. MAPOWANIE DYNAMICZNYCH WIERSZY SPECYFIKACJI
$post_details = isset($_POST['details']) && is_array($_POST['details']) ? $_POST['details'] : [];
$clean_details = [];

foreach ($post_details as $row) {
    $row_label = isset($row['label']) ? trim($row['label']) : '';
    $row_value = isset($row['value']) ? trim($row['value']) : '';
    
    if ($row_label === '' && $row_value === '') {
        continue;
    }
    
    // Sprawdzenie obecności klucza is_link w wierszu tablicy $_POST
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

// 5. BUDOWANIE PACZKI NOWEJ KARTY I DOPISANIE JEJ DO GŁÓWNEJ TABLICY
$new_card = [
    "title"     => htmlspecialchars($title, ENT_QUOTES, 'UTF-8'),
    "has_icon"  => $has_icon, // Zapis logicznego true/false do bazy JSON
    "svg_inner" => $svg_inner,
    "desc"      => htmlspecialchars($desc, ENT_QUOTES, 'UTF-8'),
    "wide"      => $wide,
    "details"   => $clean_details
];

if (!isset($data['cards']) || !is_array($data['cards'])) {
    $data['cards'] = [];
}
$data['cards'][] = $new_card;

// 6. PRZYGOTOWANIE LOGÓW ZMIAN DLA NOWOCZESNEGO DZIENNIKA ADMINISTRATORA
$new_index = count($data['cards']);
$changes = [
    "[Nowa Lokalizacja " . $new_index . "]" => ["old" => "[nowy element]", "new" => $title],
    "krótki opis" => ["old" => "[nowy element]", "new" => $desc],
    "układ kafelka" => ["old" => "wyjściowo: standardowy", "new" => $wide ? "szeroki na całą siatkę" : "standardowy kwadrat"],
    "liczba wpisów specyfikacji" => ["old" => "0", "new" => count($clean_details)],
    "ikona graficzna" => ["old" => "[nowy element]", "new" => $log_icon_status]
];

// Zapis uporządkowanego, bezpiecznego kodu JSON na serwerze
file_put_contents($json_file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// Rejestracja zdarzenia w systemie logów
log_change("Gdzie pracuję", "Dodanie", "Lokalizacja działalności", $changes);

// Błyskawiczny powrót do widoku głównego z wyświetleniem zielonego Toasta sukcesu
header("Location: admin.php?page=gdzie-pracuje&status=success");
exit;
