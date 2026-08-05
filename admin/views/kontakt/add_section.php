<style>
.admin-card { background: #ffffff; padding: 24px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 24px; border: 1px solid #e0e0e0; font-family: Arial, sans-serif; }
.admin-title { margin-top: 0; margin-bottom: 20px; color: #333333; font-size: 1.5rem; border-bottom: 2px solid #eaeaea; padding-bottom: 10px; font-weight: bold; }
.form-group { margin-bottom: 16px; }
.form-group label { display: block; font-weight: bold; margin-bottom: 6px; color: #444444; font-size: 0.9rem; }
.form-control { width: 100%; padding: 10px 12px; border: 1px solid #cccccc; border-radius: 4px; box-sizing: border-box; font-family: inherit; font-size: 0.95rem; }
.form-control:focus { border-color: #222222; outline: none; }
.btn { display: inline-block; padding: 10px 20px; font-size: 0.9rem; font-weight: bold; border-radius: 4px; text-decoration: none; border: none; cursor: pointer; transition: background 0.2s; }
.btn-primary { background: #222222; color: #ffffff; }
.btn-primary:hover { background: #444444; }
.btn-success { background: #2e7d32; color: #ffffff; }
.btn-success:hover { background: #1b5e20; }
.btn-secondary { background: #e0e0e0; color: #333333; margin-right: 10px; }
.form-actions { border-top: 1px solid #eaeaea; padding-top: 20px; margin-top: 24px; text-align: right; }
.contact-row { display: flex; gap: 12px; background: #fdfdfd; padding: 12px; border: 1px solid #cbd5e1; border-radius: 6px; margin-bottom: 10px; align-items: center; flex-wrap: wrap; }
</style>

<div class="admin-card">
    <h2 class="admin-title">Dodaj nową kartę / sekcję kontaktu</h2>

    <form method="POST" action="admin.php?page=kontakt&action=save_new_section">
        <div class="form-group">
            <label for="section_title">Tytuł karty informacyjnej:</label>
            <input type="text" id="section_title" name="title" class="form-control" required placeholder="np. Dane kontaktowe">
        </div>

        <!-- PRZEŁĄCZNIK UKŁADU SZEROKIEGO -->
        <div class="form-group" style="background: #f8f9fa; padding: 12px; border-radius: 4px; border: 1px solid #e2e8f0; margin-top: 20px;">
            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; margin-bottom: 0;">
                <input type="checkbox" name="wide" value="1" style="width: 18px; height: 18px; cursor: pointer;">
                <span style="font-weight: bold; color: #334155; font-size: 0.92rem;">Wyświetl jako szeroki panel (na całą szerokość siatki)</span>
            </label>
        </div>

        <!-- NOWOŚĆ: CHECKBOX DODAWANIA IKONY Z DYNAMICZNYM WYSÓWANIEM POLA -->
        <div class="form-group" style="background: #f0f9ff; padding: 12px; border-radius: 4px; border: 1px solid #bae6fd; margin-top: 15px;">
            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; margin-bottom: 0;">
                <input type="checkbox" id="toggle_icon_checkbox" name="has_icon" value="1" style="width: 18px; height: 18px; cursor: pointer;" onchange="toggleIconBlock(this)">
                <span style="font-weight: bold; color: #0369a1; font-size: 0.92rem;">Włącz i dodaj ikonę graficzną do tej karty</span>
            </label>
        </div>

        <!-- SEKCJA WPISYWANIA SVG (UKRYTA DOMYŚLNIE) -->
        <div id="svg_input_wrapper" class="form-group" style="display: none; padding-left: 10px; border-left: 3px solid #0369a1; margin-bottom: 20px;">
            <label for="card_svg">Surowy kod ikony SVG (tylko wnętrze) - opcjonalnie:</label>
            <textarea id="card_svg" name="svg_inner" class="form-control" rows="2" placeholder="Wklej ścieżki ikony LUB zostaw puste dla gwiazdy domyślnej..."></textarea>
            <small style="color: #777; margin-top: 6px; display: block;">Pozostawienie pustego pola przypisze zaokrągloną gwiazdę systemową z pliku config.</small>
        </div>

        <div class="form-group" style="margin-top: 30px; border-top: 1px dashed #cbd5e1; padding-top: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <label style="margin-bottom: 0; font-size: 1.05rem; color: #1e293b;">Wiersze danych i wartości (Lista):</label>
                <button type="button" class="btn" style="background: #0277bd; color: #fff; padding: 6px 14px; font-size: 0.85rem;" onclick="addContactFieldRow()">+ Dodaj wiersz danych</button>
            </div>

            <div id="contact-fields-container">
                <div class="contact-row">
                    <div style="flex: 2; min-width: 150px;">
                        <input type="text" name="fields[label]" class="form-control" placeholder="Etykieta" required>
                    </div>
                    <div style="flex: 3; min-width: 200px;">
                        <input type="text" name="fields[value]" class="form-control" placeholder="Wartość tekstowa" required>
                    </div>
                    <div style="flex: 1.5; min-width: 120px;">
                        <select name="fields[type]" class="form-control" onchange="handleTypeChange(this)" style="padding: 9px 10px;">
                            <option value="text">Zwykły tekst</option>
                            <option value="tel">Numer telefonu</option>
                            <option value="email">Adres E-mail</option>
                            <option value="link">Odnośnik URL</option>
                        </select>
                    </div>
                    <div style="flex: 2.5; min-width: 180px; display: none;" class="link-value-field">
                        <input type="text" name="fields[link_value]" class="form-control" placeholder="Wartość odnośnika...">
                    </div>
                    <div style="flex: 0 0 auto;">
                        <button type="button" class="btn" style="background: #c62828; color: #fff; padding: 8px 12px;" onclick="removeContactFieldRow(this)">Usuń</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <a href="admin.php?page=kontakt" class="btn btn-secondary">Anuluj</a>
            <button type="submit" class="btn btn-primary" style="background: #2e7d32;">Dodaj kartę kontaktu</button>
        </div>
    </form>
</div>

<script>
let fieldRowCounter = 1;

function addContactFieldRow() {
    const container = document.getElementById('contact-fields-container');
    const newRow = document.createElement('div');
    newRow.className = 'contact-row';
    
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

// NOWOŚĆ: Funkcja sterująca wysuwaniem bloku tekstowego SVG
function toggleIconBlock(checkbox) {
    const wrapper = document.getElementById('svg_input_wrapper');
    const textarea = document.getElementById('card_svg');
    if (checkbox.checked) {
        wrapper.style.display = 'block';
    } else {
        wrapper.style.display = 'none';
        textarea.value = ''; // czyścimy pole tekstowe przy odznaczeniu
    }
}
</script>
