<?php
// Plik wywoływany przez: admin.php?page=jak-pracuje&action=move_method&id=X&direction=up|down
require_once "log_change.php";

$json_file = "../data/jak-pracuje.json";
if (!file_exists($json_file)) { $json_file = "data/jak-pracuje.json"; }

$data = json_decode(file_get_contents($json_file), true) ?? [];
$id = isset($_GET['id']) ? intval($_GET['id']) : null;
$direction = isset($_GET['direction']) ? $_GET['direction'] : '';

if ($id === null || !isset($data['methods'][$id]) || !in_array($direction, ['up', 'down'])) {
    header("Location: admin.php?page=jak-pracuje&status=error");
    exit;
}

$target_id = ($direction === 'up') ? $id - 1 : $id + 1;

if (!isset($data['methods'][$target_id])) {
    header("Location: admin.php?page=jak-pracuje&status=error");
    exit;
}

$moved_title = $data['methods'][$id]['title'] ?? '';
$short_title = mb_strimwidth($moved_title, 0, 30, "...");

$temp = $data['methods'][$id];
$data['methods'][$id] = $data['methods'][$target_id];
$data['methods'][$target_id] = $temp;

$changes = [
    "zmiana pozycji" => ["old" => "Pozycja " . ($id + 1), "new" => "Pozycja " . ($target_id + 1)]
];

file_put_contents($json_file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

$object_label = "Kolejność: " . $short_title;
log_change("Jak pracuję", "Sortowanie", $object_label, $changes);

header("Location: admin.php?page=jak-pracuje&status=success");
exit;
