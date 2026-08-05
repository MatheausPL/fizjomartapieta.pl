<?php
// Plik dołączany wewnątrz admin/views/pierwsza-wizyta/index.php gdy isset($_GET['edit_step'])

// 1. Pobieramy ID edytowanego kroku z adresu URL panelu i upewniamy się, że to liczba całkowita
$id = isset($_GET['edit_step']) && $_GET['edit_step'] !== '' ? intval($_GET['edit_step']) : null;

// 2. Bezpiecznie wyciągamy listę kroków z załadowanej już w index.php zmiennej $pv
$steps_list = isset($pv['steps']) && is_array($pv['steps']) ? $pv['steps'] : [];

// 3. PANCERNA WALIDACJA: Sprawdzamy czy dany numer indeksu (0, 1, 2...) fizycznie istnieje w tablicy
if ($id === null || !array_key_exists($id, $steps_list)) {
    echo "<div class='admin-card' style='background:#fff; padding:24px; border-radius:8px; border-left: 4px solid #c62828; box-shadow: 0 2px 4px rgba(0,0,0,0.05); font-family:Arial, sans-serif;'>
            <p style='color: #c62828; font-weight: bold; margin: 0;'>Błąd: Nie znaleziono etapu o podanym ID ($id).</p>
            <a href='admin.php?page=pierwsza-wizyta' style='display:inline-block; margin-top:15px; background:#222; color:#fff; padding:10px 20px; text-decoration:none; font-weight:bold; border-radius:4px;'>Powrót do sekcji Pierwsza wizyta</a>
          </div>";
    return;
}

// 4. Pobieramy dane konkretnego etapu
$step_item = $steps_list[$id];
$is_step_wide = isset($step_item['wide']) && $step_item['wide'] === true;

// NOWOŚĆ: Pobranie stanu checkboxa obsługi ikon graficznych dla edytowanego etapu
$has_step_icon = isset($step_item['has_icon']) && $step_item['has_icon'] === true;
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
    <h2 class="admin-title">Edytuj etap pierwszej wizyty</h2>

    <form method="POST" action="admin.php?page=pierwsza-wizyta&action=save_step">
        <input type="hidden" name="id" value="<?= $id ?>">

        <div class="form-group">
            <label for="step_title">Tytuł etapu / kroku:</label>
            <input type="text" id="step_title" name="title" class="form-control" value="<?= htmlspecialchars($step_item['title'] ?? '') ?>" required placeholder="np. Konsultacja porehabilitacyjna">
        </div>

        <div class="form-group">
            <label for="step_desc">Opis / Treść etapu:</label>
            <textarea id="step_desc" name="desc" class="form-control" rows="5" required placeholder="Wpisz szczegółowy opis..."><?= htmlspecialchars($step_item['desc'] ?? '') ?></textarea>
        </div>
        <!-- PRZEŁĄCZNIK SZEROKOŚCI -->
        <div class="form-group" style="background: #f8f9fa; padding: 12px; border-radius: 4px; border: 1px solid #e2e8f0; margin-top: 20px;">
            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; margin-bottom: 0;">
                <input type="checkbox" name="wide" value="1" style="width: 18px; height: 18px; cursor: pointer;" <?= $is_step_wide ? 'checked' : '' ?>>
                <span style="font-weight: bold; color: #334155; font-size: 0.92rem;">Wyświetl jako szeroki panel (na całą szerokość siatki)</span>
            </label>
        </div>

        <!-- REJESTRACJA STANDARDU: CHECKBOX WŁĄCZANIA IKONY Z AUTOMATYCZNYM STANEM Z JSON -->
        <div class="form-group" style="background: #f0f9ff; padding: 12px; border-radius: 4px; border: 1px solid #bae6fd; margin-top: 15px;">
            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; margin-bottom: 0;">
                <input type="checkbox" id="toggle_icon_checkbox" name="has_icon" value="1" style="width: 18px; height: 18px; cursor: pointer;" onchange="toggleIconBlock(this)" <?= $has_step_icon ? 'checked' : '' ?>>
                <span style="font-weight: bold; color: #0369a1; font-size: 0.92rem;">Włącz i dodaj ikonę graficzną do tego etapu</span>
            </label>
        </div>

        <!-- WRAPPER WPISYWANIA KODU SVG (STEROWANY PRZEZ PHP NA WYJŚCIU) -->
        <div id="svg_input_wrapper" class="form-group" style="padding-left: 10px; border-left: 3px solid #0369a1; margin-bottom: 20px; <?= $has_step_icon ? 'display: block;' : 'display: none;' ?>">
            <label for="step_svg">Surowy kod ikony SVG (tylko wnętrze) - opcjonalnie:</label>
            <textarea id="step_svg" name="svg_inner" class="form-control" rows="3" placeholder="Wklej tutaj ścieżki ikony LUB zostaw puste dla ikony domyślnej..."><?= htmlspecialchars($step_item['svg_inner'] ?? '') ?></textarea>
            <small style="color: #777; margin-top: 6px; display: block;">Wklej wyłącznie zawartość znajdującą się pomiędzy tagami &lt;svg&gt; a &lt;/svg&gt; (np. same tagi &lt;path&gt;).</small>
        </div>

        <div class="form-actions">
            <a href="admin.php?page=pierwsza-wizyta" class="btn btn-secondary">Anuluj</a>
            <button type="submit" class="btn btn-primary" style="background: #2e7d32;">Zapisz zmiany</button>
        </div>
    </form>
</div>

<script>
function toggleIconBlock(checkbox) {
    const wrapper = document.getElementById('svg_input_wrapper');
    const textarea = document.getElementById('step_svg');
    if (checkbox.checked) {
        wrapper.style.display = 'block';
    } else {
        wrapper.style.display = 'none';
        textarea.value = ''; // Czyszczenie pola po schowaniu wrappera
    }
}
</script>
