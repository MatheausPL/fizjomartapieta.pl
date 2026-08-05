<?php
// Plik dołączany wewnątrz admin/views/gdzie-pracuje/index.php gdy isset($_GET['edit_card'])

// 1. Pobieramy ID edytowanej karty z adresu URL panelu i upewniamy się, że to liczba całkowita
$id = isset($_GET['edit_card']) && $_GET['edit_card'] !== '' ? intval($_GET['edit_card']) : null;

// 2. Bezpiecznie wyciągamy listę kart z załadowanej już w index.php zmiennej $gp
$cards_list = isset($gp['cards']) && is_array($gp['cards']) ? $gp['cards'] : [];

// 3. PANCERNA WALIDACJA: Sprawdzamy czy dany numer indeksu (0, 1, 2...) fizycznie istnieje w tablicy
if ($id === null || !array_key_exists($id, $cards_list)) {
    echo "<div class='admin-card' style='background:#fff; padding:24px; border-radius:8px; border-left: 4px solid #c62828; box-shadow: 0 2px 4px rgba(0,0,0,0.05); font-family:Arial, sans-serif;'>
            <p style='color: #c62828; font-weight: bold; margin: 0;'>Błąd: Nie znaleziono karty lokalizacji o podanym ID ($id).</p>
            <a href='admin.php?page=gdzie-pracuje' style='display:inline-block; margin-top:15px; background:#222; color:#fff; padding:10px 20px; text-decoration:none; font-weight:bold; border-radius:4px;'>Powrót do sekcji Gdzie pracuję</a>
          </div>";
    return;
}

// 4. Pobieramy dane konkretnej karty
$card_item = $cards_list[$id];
$is_card_wide = isset($card_item['wide']) && $card_item['wide'] === true;

// NOWOŚĆ: Pobranie stanu checkboxa obsługi ikon graficznych dla edytowanej karty lokalizacji
$has_card_icon = isset($card_item['has_icon']) && $card_item['has_icon'] === true;
$card_details = isset($card_item['details']) && is_array($card_item['details']) ? $card_item['details'] : [];
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
.spec-row { display: flex; gap: 12px; background: #fdfdfd; padding: 12px; border: 1px solid #cbd5e1; border-radius: 6px; margin-bottom: 10px; align-items: center; }
</style>

<div class="admin-card">
    <h2 class="admin-title">Edytuj kartę lokalizacji / działalności</h2>

    <form method="POST" action="admin.php?page=gdzie-pracuje&action=save_card">
        <!-- Przekazujemy ID wiersza metodą POST, by skrypt zapisu wiedział, co nadpisać -->
        <input type="hidden" name="id" value="<?= $id ?>">

        <div class="form-group">
            <label for="card_title">Tytuł lokalizacji / karty:</label>
            <input type="text" id="card_title" name="title" class="form-control" value="<?= htmlspecialchars($card_item['title'] ?? '') ?>" required placeholder="np. Konsultacje online lub Gabinet nr 2">
        </div>

        <div class="form-group">
            <label for="card_desc">Krótki opis:</label>
            <textarea id="card_desc" name="desc" class="form-control" rows="3" required placeholder="Wpisz krótki opis..."><?= htmlspecialchars($card_item['desc'] ?? '') ?></textarea>
        </div>

        <!-- PRZEŁĄCZNIK SZEROKOŚCI PANELU -->
        <div class="form-group" style="background: #f8f9fa; padding: 12px; border-radius: 4px; border: 1px solid #e2e8f0; margin-top: 20px;">
            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; margin-bottom: 0;">
                <input type="checkbox" name="wide" value="1" style="width: 18px; height: 18px; cursor: pointer;" <?= $is_card_wide ? 'checked' : '' ?>>
                <span style="font-weight: bold; color: #334155; font-size: 0.92rem;">Wyświetl jako szeroki panel (na całą szerokość siatki)</span>
            </label>
        </div>

        <!-- REJESTRACJA STANDARDU: CHECKBOX WŁĄCZANIA IKONY Z DYNAMICZNYM STANEM Z JSON -->
        <div class="form-group" style="background: #f0f9ff; padding: 12px; border-radius: 4px; border: 1px solid #bae6fd; margin-top: 15px;">
            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; margin-bottom: 0;">
                <input type="checkbox" id="toggle_icon_checkbox" name="has_icon" value="1" style="width: 18px; height: 18px; cursor: pointer;" onchange="toggleIconBlock(this)" <?= $has_card_icon ? 'checked' : '' ?>>
                <span style="font-weight: bold; color: #0369a1; font-size: 0.92rem;">Włącz i dodaj ikonę graficzną do tej karty</span>
            </label>
        </div>

        <!-- WRAPPER WPISYWANIA KODU SVG (Sterowany przez PHP na starcie) -->
        <div id="svg_input_wrapper" class="form-group" style="padding-left: 10px; border-left: 3px solid #0369a1; margin-bottom: 20px; <?= $has_card_icon ? 'display: block;' : 'display: none;' ?>">
            <label for="card_svg">Surowy kod ikony SVG (tylko wnętrze) - opcjonalnie:</label>
            <textarea id="card_svg" name="svg_inner" class="form-control" rows="2" placeholder="Wklej tutaj ścieżki ikony LUB zostaw puste dla gwiazdy domyślnej..."><?= htmlspecialchars($card_item['svg_inner'] ?? '') ?></textarea>
            <small style="color: #777; margin-top: 6px; display: block;">Wklej wyłącznie zawartość znajdującą się pomiędzy tagami &lt;svg&gt; a &lt;/svg&gt; (np. same tagi &lt;path&gt;).</small>
        </div>
        <!-- SEKCJA DYNAMICZNEJ SPECYFIKACJI (ODTWARZANIE ZAPISANYCH RZĘDÓW) -->
        <div class="form-group" style="margin-top: 30px; border-top: 1px dashed #cbd5e1; padding-top: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <label style="margin-bottom: 0; font-size: 1.05rem; color: #1e293b;">Parametry i szczegóły specyfikacji (Lista):</label>
                <button type="button" class="btn" style="background: #0277bd; color: #fff; padding: 6px 14px; font-size: 0.85rem;" onclick="addSpecificationRow()">+ Dodaj wiersz specyfikacji</button>
            </div>

            <div id="specification-container">
                <?php 
                $row_counter = 0;
                if (!empty($card_details)):
                    foreach ($card_details as $detail):
                        $is_row_link = isset($detail['is_link']) && $detail['is_link'] === true;
                ?>
                <div class="spec-row">
                    <div style="flex: 2; min-width: 120px;">
                        <input type="text" name="details[<?= $row_counter ?>][label]" class="form-control" value="<?= htmlspecialchars($detail['label'] ?? '') ?>" placeholder="Etykieta" required>
                    </div>
                    <div style="flex: 3; min-width: 150px;">
                        <input type="text" name="details[<?= $row_counter ?>][value]" class="form-control" value="<?= htmlspecialchars($detail['value'] ?? '') ?>" placeholder="Wartość" required>
                    </div>
                    <div style="flex: 1.2; text-align: center; min-width: 70px;">
                        <label style="display: inline-flex; align-items: center; gap: 4px; font-size: 0.85rem; font-weight: bold; color: #475569; margin-bottom: 0; cursor: pointer;">
                            <input type="checkbox" name="details[<?= $row_counter ?>][is_link]" value="1" onchange="toggleLinkInput(this)" style="cursor: pointer;" <?= $is_row_link ? 'checked' : '' ?>> Link?
                        </label>
                    </div>
                    <div style="flex: 2; min-width: 120px; <?= $is_row_link ? 'display: block;' : 'display: none;' ?>" class="link-url-field">
                        <input type="url" name="details[<?= $row_counter ?>][url]" class="form-control" value="<?= htmlspecialchars($detail['url'] ?? '') ?>" placeholder="Adres URL (https://...)" <?= $is_row_link ? 'required' : '' ?>>
                    </div>
                    <div style="flex: 0 0 auto;">
                        <button type="button" class="btn" style="background: #c62828; color: #fff; padding: 8px 12px;" onclick="removeSpecificationRow(this)">Usuń</button>
                    </div>
                </div>
                <?php 
                    $row_counter++;
                    endforeach;
                else: 
                ?>
                <div class="spec-row">
                    <div style="flex: 2; min-width: 120px;"><input type="text" name="details[0][label]" class="form-control" placeholder="Etykieta" required></div>
                    <div style="flex: 3; min-width: 150px;"><input type="text" name="fields[0][value]" class="form-control" placeholder="Wartość" required></div>
                    <div style="flex: 1.2; text-align: center; min-width: 70px;">
                        <label style="display: inline-flex; align-items: center; gap: 4px; font-size: 0.85rem; font-weight: bold; color: #475569; margin-bottom: 0; cursor: pointer;">
                            <input type="checkbox" name="details[0][is_link]" value="1" onchange="toggleLinkInput(this)"> Link?
                        </label>
                    </div>
                    <div style="flex: 2; display: none; min-width: 120px;" class="link-url-field"><input type="url" name="details[0][url]" class="form-control" placeholder="Adres URL"></div>
                    <div style="flex: 0 0 auto;"><button type="button" class="btn" style="background: #c62828; color: #fff; padding: 8px 12px;" onclick="removeSpecificationRow(this)">Usuń</button></div>
                </div>
                <?php 
                    $row_counter = 1;
                endif; 
                ?>
            </div>
        </div>

        <div class="form-actions">
            <a href="admin.php?page=gdzie-pracuje" class="btn btn-secondary">Anuluj</a>
            <button type="submit" class="btn btn-primary" style="background: #2e7d32; color: #ffffff;">Zapisz zmiany karty</button>
        </div>
    </form>
</div>

<script>
// Synchronizujemy licznik JS z porcją danych wygenerowaną wyżej przez PHP
let specRowCounter = <?= $row_counter ?>;

function addSpecificationRow() {
    const container = document.getElementById('specification-container');
    const newRow = document.createElement('div');
    newRow.className = 'spec-row';
    
    // Klasyczne, pancerne łączenie ciągów tekstowych stringów chroniące przed ubytkami
    newRow.innerHTML = 
        '<div style="flex: 2; min-width: 120px;">' +
            '<input type="text" name="details[' + specRowCounter + '][label]" class="form-control" placeholder="Etykieta" required>' +
        '</div>' +
        '<div style="flex: 3; min-width: 150px;">' +
            '<input type="text" name="details[' + specRowCounter + '][value]" class="form-control" placeholder="Wartość" required>' +
        '</div>' +
        '<div style="flex: 1.2; text-align: center; min-width: 70px;">' +
            '<label style="display: inline-flex; align-items: center; gap: 4px; font-size: 0.85rem; font-weight: bold; color: #475569; margin-bottom: 0; cursor: pointer;">' +
                '<input type="checkbox" name="details[' + specRowCounter + '][is_link]" value="1" onchange="toggleLinkInput(this)" style="cursor: pointer;"> Link?' +
            '</label>' +
        '</div>' +
        '<div style="flex: 2; display: none; min-width: 120px;" class="link-url-field">' +
            '<input type="url" name="details[' + specRowCounter + '][url]" class="form-control" placeholder="Adres URL (https://...)">' +
        '</div>' +
        '<div style="flex: 0 0 auto;">' +
            '<button type="button" class="btn" style="background: #c62828; color: #fff; padding: 8px 12px;" onclick="removeSpecificationRow(this)">Usuń</button>' +
        '</div>';
    
    container.appendChild(newRow);
    specRowCounter++;
}

function removeSpecificationRow(button) {
    const rows = document.querySelectorAll('.spec-row');
    if (rows.length > 1) {
        button.closest('.spec-row').remove();
    } else {
        alert("Karta musi posiadać przynajmniej jeden wiersz specyfikacji szczegółów.");
    }
}

function toggleLinkInput(checkbox) {
    const parentRow = checkbox.closest('.spec-row');
    const urlField = parentRow.querySelector('.link-url-field');
    const urlInput = urlField.querySelector('input');
    
    if (checkbox.checked) {
        urlField.style.display = 'block';
        urlInput.setAttribute('required', 'required');
    } else {
        urlField.style.display = 'none';
        urlInput.removeAttribute('required');
        urlInput.value = '';
    }
}

function toggleIconBlock(checkbox) {
    const wrapper = document.getElementById('svg_input_wrapper');
    const textarea = document.getElementById('card_svg');
    if (checkbox.checked) {
        wrapper.style.display = 'block';
    } else {
        wrapper.style.display = 'none';
        textarea.value = '';
    }
}
</script>
