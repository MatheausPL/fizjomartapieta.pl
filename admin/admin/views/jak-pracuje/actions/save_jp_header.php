<?php
// Plik wywoływany przez: admin.php?page=jak-pracuje&action=save_jp_header
require_once "log_change.php";

$json_file = "../data/jak-pracuje.json";
if (!file_exists($json_file)) { $json_file = "data/jak-pracuje.json"; }

$data = json_decode(file_get_contents($json_file), true) ?? [];
$old_title = $data['title'] ?? '';
$old_subtitle = $data['subtitle'] ?? '';

$title = isset($_POST['title']) ? trim($_POST['title']) : '';
$subtitle = isset($_POST['subtitle']) ? trim($_POST['subtitle']) : '';

if ($title === '' || $subtitle === '') {
    header("Location: admin.php?page=jak-pracuje&status=error");
    exit;
}

$data['title'] = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
$data['subtitle'] = htmlspecialchars($subtitle, ENT_QUOTES, 'UTF-8');

file_put_contents($json_file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

$changes = [
    "tytuł sekcji" => ["old" => $old_title, "new" => $title],
    "opis wprowadzający" => ["old" => $old_subtitle, "new" => $subtitle]
];
log_change("Jak pracuję", "Edycja", "Nagłówek", $changes);

header("Location: admin.php?page=jak-pracuje&status=success");
exit;
