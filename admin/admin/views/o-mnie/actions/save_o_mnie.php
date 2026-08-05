<?php
// Plik wywoływany przez: admin.php?page=o-mnie&action=save_o_mnie

require_once "log_change.php";

// 1. USTALENIE POPRAWNEJ ŚCIEŻKI DO PLIKU O-MNIE
$json_file = "../data/o-mnie.json";

if (!file_exists($json_file)) {
    $json_file = "data/o-mnie.json";
    if (!file_exists($json_file)) {
        header("Location: admin.php?page=o-mnie&status=error");
        exit;
    }
}

// 2. WCZYTANIE I DEKODOWANIE AKTUALNYCH DANYCH
$data = json_decode(file_get_contents($json_file), true) ?? [];

// Kopia zapasowa do precyzyjnego śledzenia logów zmian
$old_om = $data;

// ZMIANA: Odbieramy nowe wartości save_part z formularzy nagłówkowych
$save_part = $_POST['save_part'] ?? '';

if ($save_part === 'text_header') {
    // ----------------------------------------------------
    // BLOK 1: AKTUALIZACJA NAGŁÓWKÓW TEKSTOWYCH SEKCJI 1
    // ----------------------------------------------------
    $subtitle = isset($_POST['subtitle']) ? trim($_POST['subtitle']) : '';
    $header_text = isset($_POST['header_text']) ? trim($_POST['header_text']) : '';
    
    if (empty($subtitle)) {
        header("Location: admin.php?page=o-mnie&status=error");
        exit;
    }

    // Nadpisujemy dane bezpośrednio w płaskiej strukturze JSON (tablica items zostaje nienaruszona)
    $data['subtitle'] = htmlspecialchars($subtitle, ENT_QUOTES, 'UTF-8');
    $data['header_text'] = htmlspecialchars($header_text, ENT_QUOTES, 'UTF-8');

    // Przygotowanie raportu zmian do logów systemowych
    $changes = [
        "tag sekcji" => ["old" => $old_om['subtitle'] ?? '', "new" => $subtitle],
        "tekst wprowadzający" => ["old" => $old_om['header_text'] ?? '', "new" => $header_text]
    ];
    log_change("O mnie", "Edycja", "Nagłówki opisu", $changes);

} elseif ($save_part === 'visual_header') {
    // ----------------------------------------------------
    // BLOK 2: AKTUALIZACJA ZDJĘCIA I TYTUŁU LISTY SKUPIENIA
    // ----------------------------------------------------
    $focus_title = isset($_POST['focus_title']) ? trim($_POST['focus_title']) : '';
    
    if (empty($focus_title)) {
        header("Location: admin.php?page=o-mnie&status=error");
        exit;
    }

    // Zapisujemy tytuł listy bezpośrednio w płaskiej strukturze JSON
    $data['focus_title'] = htmlspecialchars($focus_title, ENT_QUOTES, 'UTF-8');

    // Inicjalizujemy zmianę zdjęcia jako pustą (nie zmieni się, jeśli nie wleci plik)
    $image_change = ["old" => $old_om['image'] ?? '', "new" => $old_om['image'] ?? ''];

    // --- OBSŁUGA WYBORU ZDJĘCIA Z SERWERA (GALERIA) ---
    $chosen_gallery_image = isset($_POST['chosen_gallery_image']) ? trim($_POST['chosen_gallery_image']) : '';
    if (!empty($chosen_gallery_image)) {
        $data['image'] = $chosen_gallery_image;
        $image_change["new"] = $chosen_gallery_image;
    }
    // --- OBSŁUGA WGRYWANIA NOWEGO PLIKU Z DYSKU (UPLOAD) ---
    elseif (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image_file']['tmp_name'];
        $fileExtension = strtolower(pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION));

        if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'webp'])) {
            $uploadFolder = "../img/photos/";
            if (!is_dir($uploadFolder)) {
                mkdir($uploadFolder, 0755, true);
            }

            // Generujemy unikalną nazwę pliku z czasem timestamp
            $newFileName = "marta_profile_" . time() . "." . $fileExtension;
            
            if (move_uploaded_file($fileTmpPath, $uploadFolder . $newFileName)) {
                $db_image_path = "img/photos/" . $newFileName;
                $data['image'] = $db_image_path;
                $image_change["new"] = $db_image_path;
            }
        }
    }
    
    // Przygotowanie raportu zmian do logów dla sekcji wizytówki
    $changes = [
        "ścieżka pliku graficznego" => ["old" => $image_change["old"], "new" => $image_change["new"]],
        "tytuł listy skupienia" => ["old" => $old_om['focus_title'] ?? '', "new" => $focus_title]
    ];
    log_change("O mnie", "Edycja", "Wizytówka i zdjęcie", $changes);
}

// 3. ZAPISUJEMY ZAKTUALIZOWANĄ PŁASKĄ STRUKTURĘ DO PLIKU O-MNIE.JSON
file_put_contents($json_file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// 4. POWRÓT DO PANELU Z ZIELONYM TOASTEM SUKCESU
header("Location: admin.php?page=o-mnie&status=success");
exit;
