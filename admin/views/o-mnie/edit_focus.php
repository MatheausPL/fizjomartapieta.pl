<?php
// Plik dołączany wewnątrz admin/views/o-mnie/index.php gdy isset($_GET['edit_f'])

// 1. Pobieramy ID edytowanego punktu z adresu URL i upewniamy się, że to liczba całkowita
$id = isset($_GET['edit_f']) && $_GET['edit_f'] !== '' ? intval($_GET['edit_f']) : null;

// 2. Bezpiecznie wyciągamy listę punktów skupienia z załadowanej już w index.php zmiennej $om
$focus_items = isset($om['focus_items']) && is_array($om['focus_items']) ? $om['focus_items'] : [];

// 3. PANCERNA WALIDACJA: Sprawdzamy czy dany numer indeksu (0, 1, 2...) fizycznie istnieje w tablicy
if ($id === null || !array_key_exists($id, $focus_items)) {
    echo "<div class='admin-card' style='background:#fff; padding:24px; border-radius:8px; border-left: 4px solid #c62828; box-shadow: 0 2px 4px rgba(0,0,0,0.05); font-family:Arial, sans-serif;'>
            <p style='color: #c62828; font-weight: bold; margin: 0;'>Błąd: Nie znaleziono punktu skupienia o podanym ID ($id).</p>
            <a href='admin.php?page=o-mnie' style='display:inline-block; margin-top:15px; background:#222; color:#fff; padding:10px 20px; text-decoration:none; font-weight:bold; border-radius:4px;'>Powrót do sekcji O mnie</a>
          </div>";
    return;
}

// 4. Pobieramy dane konkretnego punktu skupienia
$f_item = $focus_items[$id];
?>

<style>
/* Style w 100% zsynchronizowane ze stylistyką Twoich pozostałych formularzy */
.admin-card { 
    background: #ffffff; 
    padding: 24px; 
    border-radius: 8px; 
    box-shadow: 0 2px 4px rgba(0,0,0,0.05); 
    margin-bottom: 24px; 
    border: 1px solid #e0e0e0; 
    font-family: Arial, sans-serif; 
}
.admin-title { 
    margin-top: 0; 
    margin-bottom: 20px; 
    color: #333333; 
    font-size: 1.5rem; 
    border-bottom: 2px solid #eaeaea; 
    padding-bottom: 10px; 
}
.form-group { 
    margin-bottom: 16px; 
}
.form-group label { 
    display: block; 
    font-weight: bold; 
    margin-bottom: 6px; 
    color: #444444; 
    font-size: 0.9rem; 
}
.form-control { 
    width: 100%; 
    padding: 10px 12px; 
    border: 1px solid #cccccc; 
    border-radius: 4px; 
    box-sizing: border-box; 
    font-family: inherit; 
    font-size: 0.95rem; 
}
.form-control:focus { 
    border-color: #222222; 
    outline: none; 
}
.btn { 
    display: inline-block; 
    padding: 10px 20px; 
    font-size: 0.9rem; 
    font-weight: bold; 
    border-radius: 4px; 
    text-decoration: none; 
    border: none; 
    cursor: pointer;
}
.btn-primary { 
    background: #222222; 
    color: #ffffff; 
}
.btn-secondary { 
    background: #e0e0e0; 
    color: #333333; 
    margin-right: 10px; 
}
.form-actions { 
    border-top: 1px solid #eaeaea; 
    padding-top: 20px; 
    margin-top: 24px; 
}
</style>

<div class="admin-card">
    <h2 class="admin-title">Edytuj punkt skupienia</h2>

    <!-- Formularz kieruje akcję do pliku save_focus.php -->
    <form method="POST" action="admin.php?page=o-mnie&action=save_focus">
        
        <!-- Przekazujemy ID wiersza metodą POST, by skrypt zapisu wiedział, co zmodyfikować -->
        <input type="hidden" name="id" value="<?= $id ?>">

        <div class="form-group">
            <label for="f_text">Nazwa punktu skupienia:</label>
            <input type="text" id="f_text" name="text" class="form-control" value="<?= htmlspecialchars($f_item['text'] ?? '') ?>" required placeholder="np. Kinesiotaping sportowy">
        </div>

        <div class="form-actions">
            <a href="admin.php?page=o-mnie" class="btn btn-secondary">Anuluj</a>
            <button type="submit" class="btn btn-primary">Zapisz zmiany</button>
        </div>
    </form>
</div>
