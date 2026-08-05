<?php
function log_change($section, $action_type, $object_type, $changes_array) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $user = $_SESSION['user'] ?? 'admin';
    $ip   = $_SERVER['REMOTE_ADDR'];
    $time = date('Y-m-d H:i:s');

    $section     = str_replace('|', '-', trim($section));
    $action_type = str_replace('|', '-', trim($action_type));
    $object_type = str_replace('|', '-', trim($object_type));

    // Zamieniamy całą tablicę zmian na jedną bezpieczną linię tekstu JSON
    $history_json = json_encode($changes_array, JSON_UNESCAPED_UNICODE);

    // Zapisujemy dokładnie 7 kolumn
    $line = "$time | $user | $ip | $section | $action_type | $object_type | $history_json\n";
    file_put_contents(__DIR__ . "/logs/changes.log", $line, FILE_APPEND);
}
