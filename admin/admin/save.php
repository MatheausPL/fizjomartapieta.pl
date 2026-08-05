<?php
session_start();
if (!isset($_SESSION['auth'])) {
    http_response_code(403);
    exit("Brak autoryzacji");
}

$title = trim($_POST['title'] ?? '');
$desc  = trim($_POST['desc'] ?? '');
$price = trim($_POST['price'] ?? '');

if ($title === '' || $price === '') {
    exit("Brak wymaganych pól");
}

$data = [
    "title" => $title,
    "desc"  => $desc,
    "price" => $price
];

file_put_contents("../content.json", json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

require "log_change.php";
log_change("cennik", $title, $price);

header("Location: admin.php?page=cennik");
exit;
