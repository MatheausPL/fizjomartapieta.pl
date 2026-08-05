<?php
// Plik dołączany wewnątrz admin/views/kontakt/index.php gdy isset($_GET['edit_section'])

$id = isset($_GET['edit_section']) && $_GET['edit_section'] !== '' ? intval($_GET['edit_section']) : null;
$sections_list = isset($k['sections']) && is_array($k['sections']) ? $k['sections'] : [];

// PANCERNA WALIDACJA: Sprawdzamy czy dany numer indeksu fizycznie istnieje w tablicy
if ($id === null || !array_key_exists($id, $sections_list)) {
    echo "<div class='admin-card' style='background:#fff; padding:24px; border-radius:8px; border-left: 4px solid #c62828; box-shadow: 0 2px 4px rgba(0,0,0,0.05); font-family:Arial, sans-serif;'>
            <p style='color: #c62828; font-weight: bold; margin: 0;'>Błąd: Nie znaleziono karty kontaktu o podanym ID ($id).</p>
            <a href='admin.php?page=kontakt' style='display:inline-block; margin-top:15px; background:#222; color:#fff; padding:10px 20px; text-decoration:none; font-weight:bold; border-radius:4px;'>Powrót do sekcji Kontakt</a>
          </div>";
    return;
}

$section_item = $sections_list[$id];
$is_section_wide = isset($section_item['wide']) && $section_item['wide'] === true;
$has_section_icon = isset($section_item['has_icon']) && $section_item['has_icon'] === true;
$section_fields = isset($section_item['fields']) && is_array($section_item['fields']) ? $section_item['fields'] : [];
?>

<style>
/* Style w 100% zsynchronizowane z resztą systemu */
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
.contact-row { display: flex; gap: 12px; background: #fdfdfd; padding: 12px; border: 1px solid #cbd5e1; border-radius: 6px; margin-bottom: 10px; align-items: center; flex-wrap: wrap; }
</style>

<div class="admin-card">
    <h2 class="admin-title">Edytuj kartę / sekcję kontaktu</h2>

    <form method="POST" action="admin.php?page=kontakt&action=save_section">
        <!-- Przekazujemy ukryte ID modyfikowanej karty -->
        <input type="hidden" name="id" value="<?= $id ?>">

        <div class="form-group">
            <label for="section_title">Tytuł karty informacyjnej:</label>
            <input type="text" id="section_title" name="title" class="form-control" value="<?= htmlspecialchars($section_item['title'] ?? '') ?>" required>
        </div>

        <div class="form-group" style="background: #f8f9fa; padding: 12px; border-radius: 4px; border: 1px solid #e2e8f0; margin-top: 20px;">
            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; margin-bottom: 0;">
                <input type="checkbox" name="wide" value="1" style="width: 18px; height: 18px; cursor: pointer;" <?= $is_section_wide ? 'checked' : '' ?>>
                <span style="font-weight: bold; color: #334155; font-size: 0.92rem;">Wyświetl jako szeroki panel (na całą szerokość siatki)</span>
            </label>
        </div>

        <div class="form-group" style="background: #f0f9ff; padding: 12px; border-radius: 4px; border: 1px solid #bae6fd; margin-top: 15px;">
            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; margin-bottom: 0;">
                <input type="checkbox" id="toggle_icon_checkbox" name="has_icon" value="1" style="width: 18px; height: 18px; cursor: pointer;" onchange="toggleIconBlock(this)" <?= $has_section_icon ? 'checked' : '' ?>>
                <span style="font-weight: bold; color: #0369a1; font-size: 0.92rem;">Włącz i dodaj ikonę graficzną do tej karty</span>
            </label>
        </div>

        <!-- SEKCJA WPISYWANIA SVG (Dostosowana automatycznie przez PHP) -->
        <div id="svg_input_wrapper" class="form-group" style="padding-left: 10px; border-left: 3px solid #0369a1; margin-bottom: 20px; <?= $has_section_icon ? 'display: block;' : 'display: none;' ?>">
            <label for="card_svg">Surowy kod ikony SVG (tylko wnętrze) - opcjonalnie:</label>
            <textarea id="card_svg" name="svg_inner" class="form-control" rows="2" placeholder="Wklej ścieżki ikony LUB zostaw puste dla gwiazdy domyślnej..."><?= htmlspecialchars($section_item['svg_inner'] ?? '') ?></textarea>
            <small style="color: #777; margin-top: 6px; display: block;">Pozostawienie pustego pola przypisze zaokrągloną gwiazdę systemową z pliku config.</small>
        </div>

        <div class="form-group" style="margin-top: 30px; border-top: 1px dashed #cbd5e1; padding-top: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <label style="margin-bottom: 0; font-size: 1.05rem; color: #1e293b;">Wiersze danych i wartości (Lista):</label>
                <button type="button" class="btn" style="background: #0277bd; color: #fff; padding: 6px 14px; font-size: 0.85rem;" onclick="addContactFieldRow()">+ Dodaj wiersz danych</button>
            </div>

            <div id="contact-fields-container">
                <?php 
                $row_counter = 0;
                if (!empty($section_fields)):
                    foreach ($section_fields as $field):
                        $current_type = $field['type'] ?? 'text';
                        $show_link_zone = ($current_type !== 'text');
                ?>
                <div class="contact-row">
                    <div style="flex: 2; min-width: 150px;">
                        <input type="text" name="fields[<?= $row_counter ?>][label]" class="form-control" value="<?= htmlspecialchars($field['label'] ?? '') ?>" placeholder="Etykieta" required>
                    </div>
                    <div style="flex: 3; min-width: 200px;">
                        <input type="text" name="fields[<?= $row_counter ?>][value]" class="form-control" value="<?= htmlspecialchars($field['value'] ?? '') ?>" placeholder="Wartość tekstowa" required>
                    </div>
                    <div style="flex: 1.5; min-width: 120px;">
                        <select name="fields[<?= $row_counter ?>][type]" class="form-control" onchange="handleTypeChange(this)" style="padding: 9px 10px;">
                            <option value="text" <?= $current_type === 'text' ? 'selected' : '' ?>>Zwykły tekst</option>
                            <option value="tel" <?= $current_type === 'tel' ? 'selected' : '' ?>>Numer telefonu</option>
                            <option value="email" <?= $current_type === 'email' ? 'selected' : '' ?>>Adres E-mail</option>
                            <option value="link" <?= $current_type === 'link' ? 'selected' : '' ?>>Odnośnik URL</option>
                        </select>
                    </div>
                    <div style="flex: 2.5; min-width: 180px; <?= $show_link_zone ? 'display: block;' : 'display: none;' ?>" class="link-value-field">
                        <input type="text" name="fields[<?= $row_counter ?>][link_value]" class="form-control" value="<?= htmlspecialchars($field['link_value'] ?? '') ?>" placeholder="Wartość odnośnika...">
                    </div>
                    <div style="flex: 0 0 auto;">
                        <button type="button" class="btn" style="background: #c62828; color: #fff; padding: 8px 12px;" onclick="removeContactFieldRow(this)">Usuń</button>
                    </div>
                </div>
                <?php 
                    $row_counter++;
                    endforeach;
                else: 
                ?>
                <div class="contact-row">
                    <div style="flex: 2; min-width: 150px;"><input type="text" name="fields[0][label]" class="form-control" placeholder="Etykieta" required></div>
                    <div style="flex: 3; min-width: 200px;"><input type="text" name="fields[0][value]" class="form-control" placeholder="Wartość" required></div>
                    <div style="flex: 1.5; min-width: 120px;">
                        <select name="fields[0][type]" class="form-control" onchange="handleTypeChange(this)"><option value="text">Zwykły tekst</option><option value="tel">Telefon</option><option value="email">E-mail</option><option value="link">URL</option></select>
                    </div>
                    <div style="flex: 2.5; min-width: 180px; display: none;" class="link-value-field"><input type="text" name="fields[0][link_value]" class="form-control"></div>
                    <div style="flex: 0 0 auto;"><button type="button" class="btn" style="background: #c62828; color: #fff; padding: 8px 12px;" onclick="removeContactFieldRow(this)">Usuń</button></div>
                </div>
                <?php 
                    $row_counter = 1;
                endif; 
                ?>
            </div>
        </div>
        <div class="form-actions">
            <a href="admin.php?page=kontakt" class="btn btn-secondary">Anuluj</a>
            <button type="submit" class="btn btn-primary" style="background: #2e7d32;">Zapisz zmiany karty</button>
        </div>
    </form>
</div>

<script>
// Synchronizujemy licznik JS z porcją danych wygenerowaną wyżej przez PHP
let fieldRowCounter = <?= $row_counter ?>;

function addContactFieldRow() {
    const container = document.getElementById('contact-fields-container');
    const newRow = document.createElement('div');
    newRow.className = 'contact-row';
    
    // Klasyczne, bezpieczne dla interpretacji PHP łączenie ciągów tekstowych stringów
    newRow.innerHTML = 
        '<div style="flex: 2; min-width: 150px;">' +
            '<input type="text" name="fields[' + fieldRowCounter + '][label]" class="form-control" placeholder="Etykieta" required>' +
        '</div>' +
        '<div style="flex: 3; min-width: 200px;">' +
            '<input type="text" name="fields[' + fieldRowCounter + '][value]" class="form-control" placeholder="Wartość tekstowa" required>' +
        '</div>' +
        '<div style="flex: 1.5; min-width: 120px;">' +
            '<select name="fields[' + fieldRowCounter + '][type]" class="form-control" onchange="handleTypeChange(this)" style="padding: 9px 10px;">' +
                '<option value="text">Zwykły tekst</option>' +
                '<option value="tel">Numer telefonu</option>' +
                '<option value="email">Adres E-mail</option>' +
                '<option value="link">Odnośnik URL</option>' +
            '</select>' +
        '</div>' +
        '<div style="flex: 2.5; min-width: 180px; display: none;" class="link-value-field">' +
            '<input type="text" name="fields[' + fieldRowCounter + '][link_value]" class="form-control" placeholder="Wartość odnośnika...">' +
        '</div>' +
        '<div style="flex: 0 0 auto;">' +
            '<button type="button" class="btn" style="background: #c62828; color: #fff; padding: 8px 12px;" onclick="removeContactFieldRow(this)">Usuń</button>' +
        '</div>';
        
    container.appendChild(newRow);
    fieldRowCounter++;
}

function removeContactFieldRow(button) {
    const rows = document.querySelectorAll('.contact-row');
    if (rows.length > 1) { button.closest('.contact-row').remove(); }
    else { alert("Karta kontaktu musi posiadać przynajmniej jeden wiersz informacji."); }
}

function handleTypeChange(selectElement) {
    const parentRow = selectElement.closest('.contact-row');
    const linkValueZone = parentRow.querySelector('.link-value-field');
    const linkInput = linkValueZone.querySelector('input');
    const selectedType = selectElement.value;
    
    if (selectedType === 'text') {
        linkValueZone.style.display = 'none';
        linkInput.value = '';
    } else {
        linkValueZone.style.display = 'block';
        if (selectedType === 'tel') { linkInput.setAttribute('placeholder', 'np. +48453482415'); }
        else if (selectedType === 'email') { linkInput.setAttribute('placeholder', 'np. marta@outlook.com'); }
        else if (selectedType === 'link') { linkInput.setAttribute('placeholder', 'np. https://...'); }
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
