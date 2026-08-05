<?php
// Plik dołączany wewnątrz admin/views/jak-pracuje/index.php gdy isset($_GET['edit_method'])

// 1. Pobieramy ID edytowanej metody z adresu URL i upewniamy się, że to liczba całkowita
$id = isset($_GET['edit_method']) && $_GET['edit_method'] !== '' ? intval($_GET['edit_method']) : null;

// 2. Bezpiecznie wyciągamy listę metod z załadowanej już w index.php zmiennej $jp
$methods_list = isset($jp['methods']) && is_array($jp['methods']) ? $jp['methods'] : [];

// 3. PANCERNA WALIDACJA: Sprawdzamy czy dany numer indeksu (0, 1, 2...) fizycznie istnieje w tablicy
if ($id === null || !array_key_exists($id, $methods_list)) {
    echo "<div class='admin-card' style='background:#fff; padding:24px; border-radius:8px; border-left: 4px solid #c62828; box-shadow: 0 2px 4px rgba(0,0,0,0.05); font-family:Arial, sans-serif;'>
            <p style='color: #c62828; font-weight: bold; margin: 0;'>Błąd: Nie znaleziono metody terapeutycznej o podanym ID ($id).</p>
            <a href='admin.php?page=jak-pracuje' style='display:inline-block; margin-top:15px; background:#222; color:#fff; padding:10px 20px; text-decoration:none; font-weight:bold; border-radius:4px;'>Powrót do sekcji Jak pracuję</a>
          </div>";
    return;
}

// 4. Pobieramy dane konkretnej metody
$method_item = $methods_list[$id];
$is_method_wide = isset($method_item['wide']) && $method_item['wide'] === true;

// NOWOŚĆ: Pobranie stanu checkboxa obsługi ikon graficznych dla edytowanej metody
$has_method_icon = isset($method_item['has_icon']) && $method_item['has_icon'] === true;
?>

<style>
/* Style w 100% zsynchronizowane ze stylistyką Twoich pozostałych formularzy */
.admin-card { background: #ffffff; padding: 24px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 24px; border: 1px solid #e0e0e0; font-family: Arial, sans-serif; }
.admin-title { margin-top: 0; margin-bottom: 20px; color: #333333; font-size: 1.5rem; border-bottom: 2px solid #eaeaea; padding-bottom: 10px; font-weight: bold; }
.form-group { margin-bottom: 16px; }
.form-group label { display: block; font-weight: bold; margin-bottom: 6px; color: #444444; font-size: 0.9rem; }
.form-control { width: 100%; padding: 10px 12px; border: 1px solid #cccccc; border-radius: 4px; box-sizing: border-box; font-family: inherit; font-size: 0.95rem; }
.form-control:focus { border-color: #222222; outline: none; }
.btn { display: inline-block; padding: 10px 20px; font-size: 0.9rem; font-weight: bold; border-radius: 4px; text-decoration: none; border: none; cursor: pointer; transition: background 0.2s; }
.btn-primary { background: #222222; color: #ffffff; }
.btn-primary:hover { background: #444444; }
.btn-secondary { background: #e0e0e0; color: #333333; margin-right: 10px; }
.form-actions { border-top: 1px solid #eaeaea; padding-top: 20px; margin-top: 24px; text-align: right; }
</style>

<div class="admin-card">
    <h2 class="admin-title">Edytuj metodę terapeutyczną</h2>

    <form method="POST" action="admin.php?page=jak-pracuje&action=save_method">
        <input type="hidden" name="id" value="<?= $id ?>">

        <div class="form-group">
            <label for="method_title">Nazwa metody / techniki:</label>
            <input type="text" id="method_title" name="title" class="form-control" value="<?= htmlspecialchars($method_item['title'] ?? '') ?>" required placeholder="np. Fala uderzeniowa (ESWT)">
        </div>

        <div class="form-group">
            <label for="method_desc">Opis / Charakterystyka metody:</label>
            <textarea id="method_desc" name="desc" class="form-control" rows="5" required placeholder="Wpisz szczegółowy opis..."><?= htmlspecialchars($method_item['desc'] ?? '') ?></textarea>
        </div>
        <!-- PRZEŁĄCZNIK SZEROKOŚCI PANELU -->
        <div class="form-group" style="background: #f8f9fa; padding: 12px; border-radius: 4px; border: 1px solid #e2e8f0; margin-top: 20px;">
            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; margin-bottom: 0;">
                <input type="checkbox" name="wide" value="1" style="width: 18px; height: 18px; cursor: pointer;" <?= $is_method_wide ? 'checked' : '' ?>>
                <span style="font-weight: bold; color: #334155; font-size: 0.92rem;">Wyświetl jako szeroki panel (na całą szerokość siatki)</span>
            </label>
        </div>

        <!-- REJESTRACJA STANDARDU: CHECKBOX WŁĄCZANIA IKONY Z ZAŁADOWANIEM STANU Z BAZY -->
        <div class="form-group" style="background: #f0f9ff; padding: 12px; border-radius: 4px; border: 1px solid #bae6fd; margin-top: 15px;">
            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; margin-bottom: 0;">
                <input type="checkbox" id="toggle_icon_checkbox" name="has_icon" value="1" style="width: 18px; height: 18px; cursor: pointer;" onchange="toggleIconBlock(this)" <?= $has_method_icon ? 'checked' : '' ?>>
                <span style="font-weight: bold; color: #0369a1; font-size: 0.92rem;">Włącz i dodaj ikonę graficzną do tej metody</span>
            </label>
        </div>

        <!-- WRAPPER WPISYWANIA KODU SVG (UKRYTY LUB WIDOCZNY ZALEŻNIE OD STANU) -->
        <div id="svg_input_wrapper" class="form-group" style="padding-left: 10px; border-left: 3px solid #0369a1; margin-bottom: 20px; <?= $has_method_icon ? 'display: block;' : 'display: none;' ?>">
            <label for="method_svg">Surowy kod ikony SVG (tylko wnętrze) - opcjonalnie:</label>
            <textarea id="method_svg" name="svg_inner" class="form-control" rows="3" placeholder="Wklej tutaj ścieżki ikony LUB zostaw puste dla ikony domyślnej..."><?= htmlspecialchars($method_item['svg_inner'] ?? '') ?></textarea>
            <small style="color: #777; margin-top: 6px; display: block;">Wklej wyłącznie zawartość znajdującą się pomiędzy tagami &lt;svg&gt; a &lt;/svg&gt; (np. same tagi &lt;path&gt;).</small>
        </div>

        <div class="form-actions">
            <a href="admin.php?page=jak-pracuje" class="btn btn-secondary">Anuluj</a>
            <button type="submit" class="btn btn-primary" style="background: #2e7d32; color: #ffffff;">Zapisz zmiany</button>
        </div>
    </form>
</div>

<script>
function toggleIconBlock(checkbox) {
    const wrapper = document.getElementById('svg_input_wrapper');
    const textarea = document.getElementById('method_svg');
    if (checkbox.checked) {
        wrapper.style.display = 'block';
    } else {
        wrapper.style.display = 'none';
        textarea.value = ''; // Czyszczenie pola przy odznaczeniu checkboxa
    }
}
</script>
