<?php
// Wczytanie w pełni dynamicznej struktury z pliku data/kontakt.json
$json_file = "../data/kontakt.json";
if (!file_exists($json_file)) {
    $json_file = "data/kontakt.json";
}
$k = [];
if (file_exists($json_file)) {
    $k = json_decode(file_get_contents($json_file), true) ?? [];
}

// Bezpieczne wyciągnięcie tablicy sekcji
$sections = isset($k['sections']) && is_array($k['sections']) ? $k['sections'] : [];

// OBSŁUGA PODSTRON FORMULARZY DLA DYNAMICZNYCH KART KONTAKTU
if (isset($_GET['edit_section'])) {
    include "edit_section.php";
    return;
}
if (isset($_GET['add_section'])) {
    include "add_section.php";
    return;
}
?>

<!-- BLOK 1: SEKCJA TEKSTOWA (FORMULARZ NAGŁÓWKÓW GLOBALNYCH) -->
<div class="admin-card">
    <h2 class="admin-title">Nagłówek sekcji "Kontakt"</h2>
    <form method="POST" action="admin.php?page=kontakt&action=save_kontakt_header">
        <div class="form-group">
            <label for="title">Tytuł główny sekcji:</label>
            <input type="text" id="title" name="contact_title" class="form-control" value="<?= htmlspecialchars($k['title'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label for="subtitle">Opis / Tekst wprowadzający:</label>
            <textarea id="subtitle" name="contact_subtitle" class="form-control" rows="2" required><?= htmlspecialchars($k['subtitle'] ?? '') ?></textarea>
        </div>
        <div style="text-align: right;">
            <button type="submit" class="btn btn-primary">Zapisz nagłówek kontaktu</button>
        </div>
    </form>
</div>

<!-- BLOK 2: TABELA ZARZĄDZANIA KARTAMI KONTAKTOWYMI -->
<div class="admin-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 2px solid #eaeaea; padding-bottom: 10px;">
        <h3 style="margin: 0; color: #333333; font-size: 1.2rem;">Karty informacji i danych kontaktowych</h3>
        <a href="admin.php?page=kontakt&add_section=true" class="btn btn-success btn-sm">+ Dodaj nową kartę / sekcję</a>
    </div>

    <div class="admin-table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width: 70px; text-align: center;">Kolejność</th>
                    <th style="width: 60px; text-align: center;">Ikona</th>
                    <th>Tytuł karty informacyjnej</th>
                    <th style="width: 180px; text-align: center;">Zdefiniowane wiersze</th>
                    <th style="width: 100px; text-align: center;">Układ</th>
                    <th style="width: 150px; text-align: right;">Akcje</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $total_sections = count($sections);
                if ($total_sections > 0): 
                    foreach ($sections as $i => $section): 
                        $is_wide = isset($section['wide']) && $section['wide'] === true;
                        $fields_list = isset($section['fields']) && is_array($section['fields']) ? $section['fields'] : [];
                        $fields_count = count($fields_list);
                ?>
                <tr>
                    <td style="text-align: center; vertical-align: middle;">
                        <div style="display: inline-flex; gap: 8px;">
                            <?php if ($i > 0): ?>
                                <a href="admin.php?page=kontakt&action=move_section&id=<?= $i ?>&direction=up" style="text-decoration: none; color: #777;">▲</a>
                            <?php else: ?>
                                <span style="color: #ccc;">▲</span>
                            <?php endif; ?>
                            <?php if ($i < $total_sections - 1): ?>
                                <a href="admin.php?page=kontakt&action=move_section&id=<?= $i ?>&direction=down" style="text-decoration: none; color: #777;">▼</a>
                            <?php else: ?>
                                <span style="color: #ccc;">▼</span>
                            <?php endif; ?>
                        </div>
                    </td>
					<!-- Modyfikacja kolumny ikony wewnątrz wiersza tabeli w kontakt/index.php -->
					<td style="text-align: center; vertical-align: middle;">
    					<?php if (isset($section['has_icon']) && $section['has_icon'] === true): ?>
        					<span class="content__icon" style="display: inline-block; color: #0277bd;">
            					<svg xmlns="http://w3.org" width="20" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                				<?= $section['svg_inner'] ?? '' ?>
            					</svg>
        					</span>
    					<?php else: ?>
        					<span style="color: #94a3b8; font-size: 0.8rem; font-style: italic;">[Brak]</span>
					    <?php endif; ?>
					</td>
                    <td style="font-weight: 600; vertical-align: middle; color: #222;"><?= htmlspecialchars($section['title'] ?? '') ?></td>
                    <td style="text-align: center; vertical-align: middle;">
                        <span class="log-badge" style="min-width: 90px; background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; font-size: 0.8rem; padding: 4px 10px;">
                            <?= $fields_count ?> parametry
                        </span>
                    </td>
                    <td style="text-align: center; vertical-align: middle;">
                        <?= $is_wide ? '<span class="log-badge badge-blue" style="min-width: 70px;">Szeroki</span>' : '<span class="log-badge badge-default" style="min-width: 70px; color: #777;">Standard</span>' ?>
                    </td>
                    <td style="text-align: right; vertical-align: middle;">
                        <a href="admin.php?page=kontakt&edit_section=<?= $i ?>" class="btn btn-sm btn-edit">Edytuj</a>
                        <button type="button" class="btn btn-sm btn-delete" onclick="openSectionDeleteModal('admin.php?page=kontakt&action=delete_section&id=<?= $i ?>')">Usuń</button>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: #999999; padding: 30px; font-style: italic;">Brak zdefiniowanych kart kontaktowych. Dodaj pierwszą pozycję powyżej.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<!-- OKNO MODALNE DO BEZPIECZNEGO USUWANIA KARTY -->
<div id="section-delete-modal" class="ct-modal-overlay">
    <div class="ct-modal-box">
        <div style="font-size: 2rem; margin-bottom: 10px;">⚠️</div>
        <h3 style="margin: 0 0 10px 0; color: #222;">Potwierdź usunięcie karty</h3>
        <p style="color: #666; font-size: 0.95rem; margin-bottom: 20px;">Czy na pewno chcesz bezpowrotnie usunąć tę kartę kontaktową wraz ze wszystkimi jej danymi szczegółowymi?</p>
        <div style="display: flex; justify-content: center; gap: 12px;">
            <button type="button" class="btn-modal btn-modal-cancel" onclick="closeSectionDeleteModal()">Anuluj</button>
            <a id="section-confirm-delete-btn" href="#" class="btn-modal btn-modal-confirm" style="background:#c62828; color:#fff;">Tak, usuń</a>
        </div>
    </div>
</div>

<!-- DYNAMICZNE POWIADOMIENIA TOAST -->
<?php if (isset($_GET['status'])): 
    $status = $_GET['status']; $bgColor = ($status === 'success') ? '#2e7d32' : '#c62828'; $icon = ($status === 'success') ? '✓' : '✕';
    $message = ($status === 'success') ? 'Zmiany w sekcji kontaktu zostały pomyślnie zapisane!' : 'Wystąpił krytyczny błąd zapisu.';
?>
    <div id="toast-notification" class="toast-popup" style="background: <?= $bgColor ?>;"><span class="toast-icon"><?= $icon ?></span><span class="toast-text"><?= $message ?></span></div>
<?php endif; ?>

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
.btn-sm { padding: 6px 12px; font-size: 0.8rem; }
.btn-edit { background: #0277bd; color: #ffffff; margin-right: 5px; }
.btn-edit:hover { background: #01579b; }
.btn-delete { background: #c62828; color: #ffffff; }
.btn-delete:hover { background: #b71c1c; }
.admin-table-container { overflow-x: auto; }
.admin-table { width: 100%; border-collapse: collapse; background: #ffffff; text-align: left; }
.admin-table th { background: #f8f9fa; color: #555555; font-weight: bold; padding: 14px 16px; border-bottom: 2px solid #eaeaea; font-size: 0.9rem; }
.admin-table td { padding: 14px 16px; border-bottom: 1px solid #eaeaea; color: #333333; font-size: 0.95rem; }
.admin-table tr:hover { background: #fdfdfd; }
.log-badge { display: inline-block; padding: 4px 8px; font-size: 0.75rem; font-weight: bold; border-radius: 4px; text-align: center; min-width: 80px; }
.badge-blue { background: #e0f2fe; color: #0369a1; }
.badge-default { background: #f5f5f5; color: #666666; }
.ct-modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); display: flex; align-items: center; justify-content: center; z-index: 10010; opacity: 0; pointer-events: none; transition: opacity 0.3s ease; }
.ct-modal-overlay.show { opacity: 1; pointer-events: auto; }
.ct-modal-box { background: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.15); max-width: 400px; width: 90%; text-align: center; transform: scale(0.8); transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
.ct-modal-overlay.show .ct-modal-box { transform: scale(1); }
.btn-modal { padding: 10px 24px; font-size: 0.9rem; font-weight: bold; border-radius: 4px; text-decoration: none; border: none; cursor: pointer; transition: background 0.2s; display: inline-block; }
.btn-modal-cancel { background: #e0e0e0; color: #333333; }
.btn-modal-cancel:hover { background: #d5d5d5; }
.btn-modal-confirm { background: #c62828; color: #ffffff; }
.btn-modal-confirm:hover { background: #b71c1c; }
.toast-popup { position: fixed; bottom: 30px; right: 30px; color: white; padding: 16px 24px; border-radius: 6px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); font-family: Arial, sans-serif; font-size: 0.95rem; font-weight: bold; display: flex; align-items: center; z-index: 9999; transform: translateY(100px); opacity: 0; transition: transform 0.4s ease; }
.toast-popup.show { transform: translateY(0); opacity: 1; }
.toast-icon { font-size: 1.1rem; margin-right: 12px; background: rgba(255,255,255,0.2); width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; border-radius: 50%; }
</style>

<script>
function openSectionDeleteModal(deleteUrl) {
    const modal = document.getElementById('section-delete-modal');
    document.getElementById('section-confirm-delete-btn').setAttribute('href', deleteUrl);
    modal.classList.add('show');
}
function closeSectionDeleteModal() {
    document.getElementById('section-delete-modal').classList.remove('show');
}
document.getElementById('section-delete-modal').addEventListener('click', function(e) {
    if (e.target === this) { closeSectionDeleteModal(); }
});
document.addEventListener("DOMContentLoaded", function() {
    const toast = document.getElementById("toast-notification");
    if (!toast) return;
    setTimeout(() => { toast.classList.add("show"); }, 100);
    setTimeout(() => {
        toast.classList.remove("show");
        setTimeout(() => {
            const url = new URL(window.location);
            url.searchParams.delete('status');
            window.history.replaceState({}, document.title, url);
        }, 400);
    }, 4000);
});
</script>
