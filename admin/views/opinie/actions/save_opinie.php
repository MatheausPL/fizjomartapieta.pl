<?php
// Plik wywoływany przez: admin.php?page=opinie&action=save_opinie

require_once "log_change.php";

// 1. USTALENIE POPRAWNEJ ŚCIEŻKI DO PLIKU DANYCH JSON
$json_file = "../data/opinie.json";
if (!file_exists($json_file)) {
    $json_file = "data/opinie.json";
    if (!file_exists($json_file)) {
        header("Location: admin.php?page=opinie&status=error");
        exit;
    }
}

// 2. WCZYTANIE I DEKODOWANIE AKTUALNYCH DANYCH
$data = json_decode(file_get_contents($json_file), true) ?? [];

// Kopia zapasowa do wygenerowania raportu zmian
$old_data = $data;

// 3. ODBIERANIE SUROWYCH KODÓW SKRYPTÓW Z FORMULARZA POST
// Zapisujemy kody w stanie surowym (bez htmlspecialchars), aby przeglądarka pacjenta mogła je poprawnie odczytać i uruchomić widżet
$google_script   = isset($_POST['google_script']) ? trim($_POST['google_script']) : '';
$facebook_script = isset($_POST['facebook_script']) ? trim($_POST['facebook_script']) : '';

// 4. MAPOWANIE NOWYCH WARTOŚCI W TABLICY BAZY DANYCH
$data['google_script']   = $google_script;
$data['facebook_script'] = $facebook_script;

// 5. BUDOWANIE RAPORTU LOGÓW ZMIAN DLA ADMINISTRATORA
$changes = [];
if (($old_data['google_script'] ?? '') !== $google_script) { 
    $changes["skrypt opinii Google"] = ["old" => "poprzednia konfiguracja", "new" => "zaktualizowano kod widżetu w panelu"]; 
}
if (($old_data['facebook_script'] ?? '') !== $facebook_script) { 
    $changes["skrypt opinii Facebook"] = ["old" => "poprzednia konfiguracja", "new" => "zaktualizowano kod widżetu w panelu"]; 
}

// Rejestrujemy zdarzenie w dzienniku audytu tylko w przypadku realnych modyfikacji
if (!empty($changes)) {
    log_change("Opinie", "Edycja", "Integracja zewnętrznych widżetów", $changes);
}

// 6. ZAPIS BEZPIECZNEJ PACZKI JSON NA SERWERZE SEOHOST
file_put_contents($json_file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// Błyskawiczne przekierowanie z powrotem do widoku opinii i wyświetlenie zielonego Toasta
header("Location: admin.php?page=opinie&status=success");
exit;
