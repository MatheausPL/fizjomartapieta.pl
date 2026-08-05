<?php
// Plik wywoływany przez: admin.php?page=jak-pracuje&action=delete_method&id=X
require_once "log_change.php";

$json_file = "../data/jak-pracuje.json";
if (!file_exists($json_file)) { $json_file = "data/jak-pracuje.json"; }

$data = json_decode(file_get_contents($json_file), true) ?? [];
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

if ($id === null || !isset($data['methods'][$id])) {
    header("Location: admin.php?page=jak-pracuje&status=error");
    exit;
}

$deleted_method = $data['methods'][$id];
$deleted_title = $deleted_method['title'] ?? '';
$deleted_desc  = $deleted_method['desc'] ?? '';

$changes = [
    "status metody" => ["old" => "widoczna na stronie", "new" => "[Usunięto bezpowrotnie]"],
    "opis przed usunięciem" => ["old" => $deleted_desc, "new" => ""]
];

unset($data['methods'][$id]);
$data['methods'] = array_values($data['methods']);

file_put_contents($json_file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

$object_label = "Usunięto metodę: " . mb_strimwidth($deleted_title, 0, 25, "...");
log_change("Jak pracuję", "Usunięcie", $object_label, $changes);

header("Location: admin.php?page=jak-pracuje&status=success");
exit;
