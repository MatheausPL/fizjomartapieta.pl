<?php
// Plik wywoływany przez: admin.php?page=pierwsza-wizyta&action=save_step

require_once "log_change.php";
require_once __DIR__ . "/../../../config.php"; // DOPINAMY CENTRALNĄ KONFIGURACJĘ

// 1. USTALENIE POPRAWNEJ ŚCIEŻKI DO PLIKU
$json_file = "../data/pierwsza-wizyta.json";
if (!file_exists($json_file)) {
    $json_file = "data/pierwsza-wizyta.json";
    if (!file_exists($json_file)) { header("Location: admin.php?page=pierwsza-wizyta&status=error"); exit; }
}

$data = json_decode(file_get_contents($json_file), true) ?? [];
$id = isset($_POST['id']) ? intval($_POST['id']) : null;

if ($id === null || !isset($data['steps'][$id])) { header("Location: admin.php?page=pierwsza-wizyta&status=error"); exit; }

$title     = isset($_POST['title']) ? trim($_POST['title']) : '';
$svg_inner = isset($_POST['svg_inner']) ? trim($_POST['svg_inner']) : '';
$desc      = isset($_POST['desc']) ? trim($_POST['desc']) : '';
$wide      = isset($_POST['wide']) && $_POST['wide'] === '1';

// NOWOŚĆ: Odbieranie stanu checkboxa obsługi ikon
$has_icon  = isset($_POST['has_icon']) && $_POST['has_icon'] === '1';

if ($title === '' || $desc === '') { header("Location: admin.php?page=pierwsza-wizyta&status=error"); exit; }

$old_step    = $data['steps'][$id];
$old_title   = $old_step['title'] ?? '';
$old_desc    = $old_step['desc'] ?? '';
$old_wide    = isset($old_step['wide']) && $old_step['wide'] === true;
$old_has_ico = isset($old_step['has_icon']) && $old_step['has_icon'] === true;
$old_svg     = $old_step['svg_inner'] ?? '';

// WALIDACJA STATUSU STAREJ IKONY DLA LOGÓW
if ($old_has_ico === false) {
    $old_icon_status = "wyłączona (czysty tekst)";
} elseif ($old_svg === '' || $old_svg === $config['default_svg']) {
    $old_icon_status = "włączona (domyślna gwiazda systemowa)";
} else {
    $old_icon_status = "włączona (własna ikona SVG)";
}

// 3. INTELIGENTNA WALIDACJA I OCZYSZCZANIE NOWEGO KODU SVG (ZALEŻNA OD PARAMETRU HAS_ICON)
if ($has_icon === true) {
    if ($svg_inner !== '') {
        if (preg_match('/<svg[^>]*>(.*?)<\/svg>/is', $svg_inner, $matches)) { 
            if (!empty($matches[1])) { $svg_inner = $matches[1]; } 
        }
        $svg_inner = preg_replace('/<!--.*?-->/s', '', $svg_inner);
        $svg_inner = preg_replace('/<(script|iframe|style|html|body)\b[^>]*>(.*?)<\/\1>/is', "", $svg_inner);
        $svg_inner = preg_replace('/on\w+\s*=\s*".*?"/i', '', $svg_inner);
        $svg_inner = preg_replace('/on\w+\s*=\s*\'.*?\'/i', '', $svg_inner);
        $svg_inner = stripslashes($svg_inner);
        
        // PANCERNA UNIFIKACJA: Zamieniamy cudzysłowy na apostrofy, usuwając problem backslashy w JSON
        $svg_inner = str_replace('"', "'", $svg_inner);
        $svg_inner = trim($svg_inner);
        $new_icon_status = "włączona (własna ikona SVG)";
    } else {
        $svg_inner = $config['default_svg'];
        $new_icon_status = "włączona (domyślna gwiazda systemowa)";
    }
} else {
    $svg_inner = ''; // Jeśli odznaczono - czyścimy pole ścieżek graficznych
    $new_icon_status = "wyłączona (czysty tekst bez kontenera)";
}

// 4. AKTUALIZACJA DANYCH W TABLICY
$data['steps'][$id] = [
    "title"     => htmlspecialchars($title, ENT_QUOTES, 'UTF-8'),
    "has_icon"  => $has_icon, // Zapis nowej flagi logicznej
    "svg_inner" => $svg_inner,
    "desc"      => htmlspecialchars($desc, ENT_QUOTES, 'UTF-8'),
    "wide"      => $wide
];

// 5. BUDOWANIE RAPORTU LOGÓW
$changes = [
    "tytuł etapu" => ["old" => $old_title, "new" => $title],
    "treść opisu" => ["old" => $old_desc, "new" => $desc],
    "szeroki panel" => ["old" => $old_wide ? "tak, szeroki" : "nie, standardowy", "new" => $wide ? "tak, szeroki" : "nie, standardowy"]
];

if ($old_has_ico !== $has_icon || $old_svg !== $svg_inner) {
    $changes["obsługa ikony graficznej"] = ["old" => $old_icon_status, "new" => $new_icon_status];
}

// 6. BEZPIECZNY ZAPIS DO PLIKU JSON
file_put_contents($json_file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

$object_label = "Krok: " . mb_strimwidth($old_title, 0, 30, "...");
log_change("Pierwsza wizyta", "Edycja", $object_label, $changes);

header("Location: admin.php?page=pierwsza-wizyta&status=success");
exit;
