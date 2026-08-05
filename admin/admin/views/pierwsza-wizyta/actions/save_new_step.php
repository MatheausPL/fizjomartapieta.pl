<?php
// Plik wywoływany przez: admin.php?page=pierwsza-wizyta&action=save_new_step

require_once "log_change.php";
require_once __DIR__ . "/../../../config.php"; // Ładujemy centralną ikonę domyślną

// 1. USTALENIE POPRAWNEJ ŚCIEŻKI DO PLIKU
$json_file = "../data/pierwsza-wizyta.json";

if (!file_exists($json_file)) {
    $json_file = "data/pierwsza-wizyta.json";
    if (!file_exists($json_file)) {
        header("Location: admin.php?page=pierwsza-wizyta&status=error");
        exit;
    }
}

// 2. WCZYTANIE I DEKODOWANIE AKTUALNYCH DANYCH
$data = json_decode(file_get_contents($json_file), true) ?? [];

// Oczyszczenie danych z formularza metodą POST
$title     = isset($_POST['title']) ? trim($_POST['title']) : '';
$svg_inner = isset($_POST['svg_inner']) ? trim($_POST['svg_inner']) : '';
$desc      = isset($_POST['desc']) ? trim($_POST['desc']) : '';
$wide      = isset($_POST['wide']) && $_POST['wide'] === '1';

// NOWOŚĆ: Pobranie stanu checkboxa obsługi ikon graficznych
$has_icon  = isset($_POST['has_icon']) && $_POST['has_icon'] === '1';

// Walidacja – blokujemy zapis tylko w przypadku pustego tytułu lub opisu
if ($title === '' || $desc === '') {
    header("Location: admin.php?page=pierwsza-wizyta&status=error");
    exit;
}

// 3. INTELIGENTNA WALIDACJA KODU IKONY SVG (ZALEŻNA OD PARAMETRU HAS_ICON)
if ($has_icon === true) {
    if ($svg_inner !== '') {
        // Jeśli użytkownik wkleił cały tag <svg>...</svg>, wyciągamy wyłącznie jego wnętrze
        if (preg_match('/<svg[^>]*>(.*?)<\/svg>/is', $svg_inner, $matches)) {
            if (!empty($matches[1])) {
                $svg_inner = $matches[1];
            }
        }

        // Bezwzględne usuwanie komentarzy XML/HTML
        $svg_inner = preg_replace('/<!--.*?-->/s', '', $svg_inner);

        // Czyszczenie z potencjalnie niebezpiecznych tagów
        $svg_inner = preg_replace('/<(script|iframe|style|html|body)\b[^>]*>(.*?)<\/\1>/is', "", $svg_inner);

        // Usuwanie podejrzanych atrybutów JavaScript
        $svg_inner = preg_replace('/on\w+\s*=\s*".*?"/i', '', $svg_inner);
        $svg_inner = preg_replace('/on\w+\s*=\s*\'.*?\'/i', '', $svg_inner);

        // Zabezpieczenie przed podwójnym uciekaniem cudzysłowów przed zapisem do JSON
        $svg_inner = stripslashes($svg_inner);
        $svg_inner = str_replace('"', "'", $svg_inner); // Unifikacja na apostrofy dla zgodności JSON
        $svg_inner = trim($svg_inner);
        $log_icon_status = "włączona (własny kod SVG)";
    } else {
        // Fallback dla pustego pola tekstowego przy zaznaczonym checkboxie
        $svg_inner = $config['default_svg'];
        $log_icon_status = "włączona (domyślna gwiazda systemowa)";
    }
} else {
    // Jeśli checkbox nie jest zaznaczony - bezwzględnie czyścimy kod grafiki do zera
    $svg_inner = '';
    $log_icon_status = "wyłączona (czysty tekst bez kontenera)";
}

// 4. PRZYGOTOWANIE I DOPISANIE NOWEGO ETAPU DO BAZY JSON
$new_step = [
    "title"     => htmlspecialchars($title, ENT_QUOTES, 'UTF-8'),
    "has_icon"  => $has_icon, // Zapis logicznego true/false do bazy JSON
    "svg_inner" => $svg_inner, // Zapisuje czysty kod xml ścieżek
    "desc"      => htmlspecialchars($desc, ENT_QUOTES, 'UTF-8'),
    "wide"      => $wide
];

if (!isset($data['steps']) || !is_array($data['steps'])) {
    $data['steps'] = [];
}

$data['steps'][] = $new_step;

// 5. PRZYGOTOWANIE RAPORTU ZMIAN DO LOGÓW
$new_index = count($data['steps']);
$changes = [
    "[Nowy Etap " . $new_index . "]" => ["old" => "[nowy element]", "new" => $title],
    "treść opisu" => ["old" => "[nowy element]", "new" => $desc],
    "szeroki panel" => ["old" => "wyjściowo: standardowy", "new" => $wide ? "tak, szeroki" : "nie, standardowy"],
    "ikona graficzna" => ["old" => "[nowy element]", "new" => $log_icon_status]
];

// 6. BEZPIECZNY ZAPIS DO PLIKU
file_put_contents($json_file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// Wywołanie rejestracji w dzienniku zmian
log_change("Pierwsza wizyta", "Dodanie", "Etap wizyty", $changes);

// Powrót do panelu głównego z zielonym powiadomieniem Toast o sukcesie
header("Location: admin.php?page=pierwsza-wizyta&status=success");
exit;
