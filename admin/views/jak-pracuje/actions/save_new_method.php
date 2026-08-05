<?php
// Plik wywoływany przez: admin.php?page=jak-pracuje&action=save_new_method
require_once "log_change.php";
require_once __DIR__ . "/../../../config.php";

$json_file = "../data/jak-pracuje.json";
if (!file_exists($json_file)) { $json_file = "data/jak-pracuje.json"; }

$data = json_decode(file_get_contents($json_file), true) ?? [];

$title     = isset($_POST['title']) ? trim($_POST['title']) : '';
$svg_inner = isset($_POST['svg_inner']) ? trim($_POST['svg_inner']) : '';
$desc      = isset($_POST['desc']) ? trim($_POST['desc']) : '';
$wide      = isset($_POST['wide']) && $_POST['wide'] === '1';

// NOWOŚĆ: Pobranie stanu checkboxa obsługi ikon graficznych
$has_icon  = isset($_POST['has_icon']) && $_POST['has_icon'] === '1';

if ($title === '' || $desc === '') {
    header("Location: admin.php?page=jak-pracuje&status=error");
    exit;
}

// INTELIGENTNA WALIDACJA KODU IKONY SVG (ZALEŻNA OD PARAMETRU HAS_ICON)
if ($has_icon === true) {
    if ($svg_inner === '') {
        $svg_inner = $config['default_svg'];
        $log_icon = "włączona (domyślna gwiazda systemowa)";
    } else {
        if (preg_match('/<svg[^>]*>(.*?)<\/svg>/is', $svg_inner, $matches)) {
            if (!empty($matches[1])) { $svg_inner = $matches[1]; }
        }
        $svg_inner = preg_replace('/<!--.*?-->/s', '', $svg_inner);
        $svg_inner = preg_replace('/<(script|iframe|style|html|body)\b[^>]*>(.*?)<\/\1>/is', "", $svg_inner);
        $svg_inner = preg_replace('/on\w+\s*=\s*".*?"/i', '', $svg_inner);
        $svg_inner = preg_replace('/on\w+\s*=\s*\'.*?\'/i', '', $svg_inner);
        
        // PANCERNA UNIFIKACJA: Zamieniamy cudzysłowy na apostrofy, usuwając problem backslashy w JSON
        $svg_inner = str_replace('"', "'", $svg_inner);
        $svg_inner = trim($svg_inner);
        $log_icon = "włączona (własny oczyszczony kod SVG)";
    }
} else {
    // Jeśli checkbox nie jest zaznaczony - bezwzględnie czyścimy kod grafiki do zera
    $svg_inner = '';
    $log_icon = "wyłączona (czysty tekst bez kontenera)";
}

$new_method = [
    "title"     => htmlspecialchars($title, ENT_QUOTES, 'UTF-8'),
    "has_icon"  => $has_icon, // Zapis logicznego true/false do bazy JSON
    "svg_inner" => $svg_inner,
    "desc"      => htmlspecialchars($desc, ENT_QUOTES, 'UTF-8'),
    "wide"      => $wide
];

if (!isset($data['methods']) || !is_array($data['methods'])) { $data['methods'] = []; }
$data['methods'][] = $new_method;

$new_idx = count($data['methods']);
$changes = [
    "[Nowa Metoda " . $new_idx . "]" => ["old" => "[nowy element]", "new" => $title],
    "opis techniki" => ["old" => "[nowy element]", "new" => $desc],
    "szeroki panel" => ["old" => "wyjściowo: standardowy", "new" => $wide ? "tak, szeroki" : "nie, standardowy"],
    "ikona" => ["old" => "[nowy element]", "new" => $log_icon]
];

file_put_contents($json_file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
log_change("Jak pracuję", "Dodanie", "Metoda pracy", $changes);

header("Location: admin.php?page=jak-pracuje&status=success");
exit;
