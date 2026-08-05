<style>
/* Style w 100% zsynchronizowane z szkieletem add_item.php w cenniku */
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
    font-weight: bold;
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
    transition: background 0.2s;
}
.btn-primary { 
    background: #222222; 
    color: #ffffff; 
}
.btn-primary:hover {
    background: #444444;
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
    text-align: right;
}
</style>

<div class="admin-card">
    <h2 class="admin-title">Dodaj nowy etap pierwszej wizyty</h2>

    <form method="POST" action="admin.php?page=pierwsza-wizyta&action=save_new_step">

        <div class="form-group">
            <label for="step_title">Tytuł etapu / kroku:</label>
            <input type="text" id="step_title" name="title" class="form-control" required placeholder="np. Konsultacja porehabilitacyjna">
        </div>

        <div class="form-group">
            <label for="step_desc">Opis / Treść etapu:</label>
            <textarea id="step_desc" name="desc" class="form-control" rows="5" required placeholder="Wpisz szczegółowy opis tego, co dzieje się podczas tego kroku wizyty..."></textarea>
        </div>

        <!-- SEKCJA: WYBÓR SZEROKOŚCI PANELU -->
        <div class="form-group" style="background: #f8f9fa; padding: 12px; border-radius: 4px; border: 1px solid #e2e8f0; margin-top: 20px;">
            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; margin-bottom: 0;">
                <input type="checkbox" name="wide" value="1" style="width: 18px; height: 18px; cursor: pointer;">
                <span style="font-weight: bold; color: #334155; font-size: 0.92rem;">Wyświetl jako szeroki panel (na całą szerokość siatki)</span>
            </label>
        </div>

        <!-- UZNANY STANDARD: CHECKBOX DLA IKONY Z WYSUWANYM BLOKIEM -->
        <div class="form-group" style="background: #f0f9ff; padding: 12px; border-radius: 4px; border: 1px solid #bae6fd; margin-top: 15px;">
            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; margin-bottom: 0;">
                <input type="checkbox" id="toggle_icon_checkbox" name="has_icon" value="1" style="width: 18px; height: 18px; cursor: pointer;" onchange="toggleIconBlock(this)">
                <span style="font-weight: bold; color: #0369a1; font-size: 0.92rem;">Włącz i dodaj ikonę graficzną do tego etapu</span>
            </label>
        </div>

        <!-- CHOWANY I WYSUWANY WRAPPER DLA KODU SVG -->
        <div id="svg_input_wrapper" class="form-group" style="display: none; padding-left: 10px; border-left: 3px solid #0369a1; margin-bottom: 20px;">
            <label for="step_svg">Surowy kod ikony SVG (tylko wnętrze) - opcjonalnie:</label>
            <textarea id="step_svg" name="svg_inner" class="form-control" rows="3" placeholder="Wklej tutaj ścieżki ikony LUB zostaw puste dla ikony domyślnej..."></textarea>
            <small style="color: #777; margin-top: 6px; display: block;">Wklej wyłącznie zawartość znajdującą się pomiędzy tagami &lt;svg&gt; a &lt;/svg&gt; (np. same tagi &lt;path&gt;).</small>
        </div>

        <div class="form-actions">
            <a href="admin.php?page=pierwsza-wizyta" class="btn btn-secondary">Anuluj</a>
            <button type="submit" class="btn btn-primary" style="background: #2e7d32;">Dodaj etap</button>
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
        textarea.value = ''; // Czyszczenie pola po odznaczeniu checkboxa
    }
}
</script>
