<?php
// Wczytanie w pełni dynamicznej struktury z pliku gdzie-pracuje.json
$json_file = "../data/gdzie-pracuje.json";
if (!file_exists($json_file)) {
    $json_file = "data/gdzie-pracuje.json";
}
$gp = [];
if (file_exists($json_file)) {
    $gp = json_decode(file_get_contents($json_file), true) ?? [];
}
$cards = isset($gp['cards']) && is_array($gp['cards']) ? $gp['cards'] : [];

// OBSŁUGA PODSTRON FORMULARZY DLA DYNAMICZNYCH KART
if (isset($_GET['edit_card'])) {
    include "edit_card.php";
    return;
}
if (isset($_GET['add_card'])) {
    include "add_card.php";
    return;
}
?>

<!-- BLOK 1: SEKCJA TEKSTOWA (FORMULARZ NAGŁÓWKÓW GLOBALNYCH) -->
<div class="admin-card">
    <h2 class="admin-title">Nagłówek sekcji "Gdzie pracuję"</h2>
    
    <form method="POST" action="admin.php?page=gdzie-pracuje&action=save_gp_header">
        <div class="form-group">
            <label for="title">Tytuł główny sekcji:</label>
            <input type="text" id="title" name="location_title" class="form-control" value="<?= htmlspecialchars($gp['title'] ?? '') ?>" required placeholder="Wpisz tytuł sekcji...">
        </div>

        <div class="form-group">
            <label for="subtitle">Opis / Tekst wprowadzający:</label>
            <textarea id="subtitle" name="location_subtitle" class="form-control" rows="2" required placeholder="Wpisz opis pod nagłówkiem..."><?= htmlspecialchars($gp['subtitle'] ?? '') ?></textarea>
        </div>

        <div style="text-align: right;">
            <button type="submit" class="btn btn-primary">Zapisz nagłówek lokalizacji</button>
        </div>
    </form>
</div>

<!-- BLOK 2: TABELA ZARZĄDZANIA KARTAMI LOKALIZACJI -->
<div class="admin-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 2px solid #eaeaea; padding-bottom: 10px;">
        <h3 style="margin: 0; color: #333333; font-size: 1.2rem;">Karty lokalizacji i form działalności</h3>
        <a href="admin.php?page=gdzie-pracuje&add_card=true" class="btn btn-success btn-sm">+ Dodaj nową kartę / lokalizację</a>
    </div>

    <div class="admin-table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width: 70px; text-align: center;">Kolejność</th>
                    <th style="width: 60px; text-align: center;">Ikona</th>
                    <th>Tytuł lokalizacji</th>
                    <th>Krótki opis</th>
                    <th style="width: 140px; text-align: center;">Liczba parametrów</th>
                    <th style="width: 100px; text-align: center;">Układ</th>
                    <th style="width: 150px; text-align: right;">Akcje</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $total_cards = count($cards);
                if ($total_cards > 0): 
                    foreach ($cards as $i => $card): 
                        $is_wide = isset($card['wide']) && $card['wide'] === true;
                        $details_count = isset($card['details']) && is_array($card['details']) ? count($card['details']) : 0;
                        
                        // NOWOŚĆ: Wyciągnięcie flagi logicznej has_icon dla lokalizacji
                        $has_icon = isset($card['has_icon']) && $card['has_icon'] === true;
                ?>
                <tr>
                    <td style="text-align: center; vertical-align: middle;">
                        <div style="display: inline-flex; gap: 8px;">
                            <?php if ($i > 0): ?>
                                <a href="admin.php?page=gdzie-pracuje&action=move_card&id=<?= $i ?>&direction=up" style="text-decoration: none; color: #777;" title="Przesuń w górę">▲</a>
                            <?php else: ?>
                                <span style="color: #ccc; user-select: none;">▲</span>
                            <?php endif; ?>

                            <?php if ($i < $total_cards - 1): ?>
                                <a href="admin.php?page=gdzie-pracuje&action=move_card&id=<?= $i ?>&direction=down" style="text-decoration: none; color: #777;" title="Przesuń w dół">▼</a>
                            <?php else: ?>
                                <span style="color: #ccc; user-select: none;">▼</span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <!-- DYNAMICZNY WARUNEK: Miniatura SVG generuje się wyłącznie gdy has_icon to true -->
                    <td style="text-align: center; vertical-align: middle;">
                        <?php if ($has_icon): ?>
                            <span class="content__icon" style="display: inline-block; color: #0277bd;">
                                <svg xmlns="http://w3.org" width="20" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <?= $card['svg_inner'] ?? '' ?>
                                </svg>
                            </span>
                        <?php else: ?>
                            <span style="color: #94a3b8; font-size: 0.8rem; font-style: italic;">[Brak]</span>
                        <?php endif; ?>
                    </td>
                    <td style="font-weight: 600; vertical-align: middle; color: #222;"><?= htmlspecialchars($card['title'] ?? '') ?></td>
                    <td style="color: #666666; font-size: 0.9rem; line-height: 1.4;"><?= htmlspecialchars($card['desc'] ?? '') ?></td>
                    <td style="text-align: center; vertical-align: middle; font-weight: bold; color: #555;"><?= $details_count ?></td>
                    <td style="text-align: center; vertical-align: middle;">
                        <?php if ($is_wide): ?>
                            <span class="log-badge badge-blue" style="min-width: 70px;">Szeroki</span>
                        <?php else: ?>
                            <span class="log-badge badge-default" style="min-width: 70px; color: #777;">Standard</span>
                        <?php endif; ?>
                    </td>
                    <td style="text-align: right; vertical-align: middle;">
                        <a href="admin.php?page=gdzie-pracuje&edit_card=<?= $i ?>" class="btn btn-sm btn-edit">Edytuj</a>
                        <button type="button" class="btn btn-sm btn-delete" onclick="openCardDeleteModal('admin.php?page=gdzie-pracuje&action=delete_card&id=<?= $i ?>')">Usuń</button>
                    </td>
                </tr>
                <?php 
                    endforeach; 
                else: 
                ?>
                    <tr>
                        <td colspan="7" style="text-align: center; color: #999999; padding: 30px; font-style: italic;">Brak zdefiniowanych kart lokalizacji. Dodaj pierwszą pozycję powyżej.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- UNIKALNE OKNO MODALNE USUWANIA KARTY -->
<div id="card-delete-modal" class="gp-modal-overlay">
    <div class="gp-modal-box">
        <div style="font-size: 2rem; margin-bottom: 10px;">⚠️</div>
        <h3 style="margin: 0 0 10px 0; color: #222;">Potwierdź usunięcie lokalizacji</h3>
        <p style="color: #666; font-size: 0.95rem; margin-bottom: 20px;">Czy na pewno chcesz całkowicie skasować tę kartę wraz ze wszystkimi jej dynamicznymi parametrami i linkami?</p>
        <div style="display: flex; justify-content: center; gap: 12px;">
            <button type="button" class="btn-modal btn-modal-cancel" onclick="closeCardDeleteModal()">Anuluj</button>
            <a id="card-confirm-delete-btn" href="#" class="btn-modal btn-modal-confirm" style="background:#c62828; color:#fff;">Tak, usuń</a>
        </div>
    </div>
</div>

<!-- POWIADOMIENIA TOAST -->
<?php if (isset($_GET['status'])): 
    $status = $_GET['status'];
    $bgColor = ($status === 'success') ? '#2e7d32' : '#c62828';
    $icon = ($status === 'success') ? '✓' : '✕';
    $message = ($status === 'success') ? 'Operacja została pomyślnie zapisana w bazie!' : 'Wystąpił błąd systemu podczas przetwarzania.';
?>
    <div id="toast-notification" class="toast-popup" style="background: <?= $bgColor ?>;">
        <span class="toast-icon"><?= $icon ?></span>
        <span class="toast-text"><?= $message ?></span>
    </div>
<?php endif; ?>

<style>
.admin-card { background: #ffffff; padding: 24px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 24px; border: 1px solid #e0e0e0; font-family: Arial, sans-serif; }
.admin-title { margin-top: 0; margin-bottom: 20px; color: #333333; font-size: 1.5rem; border-bottom: 2px solid #eaeaea; padding-bottom: 10px; font-weight: bold; }
.form-group { margin-bottom: 16px; }
.form-group label { display: block; font-weight: bold; margin-bottom: 6px; color: #444444; font-size: 0.9rem; }
.form-control { width: 100%; padding: 10px 12px; border: 1px solid #cccccc; border-radius: 4px; box-sizing: border-box; font-family: inherit; font-size: 0.95rem; }
.form-control:focus { border-color: #222222; outline: none; }
.btn { display: inline-block; padding: 10px 20px; font-size: 0.9rem; font-weight: bold; border-radius: 4px; text-decoration: none; cursor: pointer; border: none; transition: background 0.2s; }
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

.gp-modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); display: flex; align-items: center; justify-content: center; z-index: 10010; opacity: 0; pointer-events: none; transition: opacity 0.3s ease; font-family: Arial, sans-serif; }
.gp-modal-overlay.show { opacity: 1; pointer-events: auto; }
.gp-modal-box { background: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.15); max-width: 400px; width: 90%; text-align: center; transform: scale(0.8); transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
.gp-modal-overlay.show .gp-modal-box { transform: scale(1); }
.btn-modal { padding: 10px 24px; font-size: 0.9rem; font-weight: bold; border-radius: 4px; text-decoration: none; border: none; cursor: pointer; transition: background 0.2s; display: inline-block; }
.btn-modal-cancel { background: #e0e0e0; color: #333333; }
.btn-modal-cancel:hover { background: #d5d5d5; }
.btn-modal-confirm { background: #c62828; color: #ffffff; }
.btn-modal-confirm:hover { background: #b71c1c; }

.toast-popup { position: fixed; bottom: 30px; right: 30px; color: white; padding: 16px 24px; border-radius: 6px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); font-family: Arial, sans-serif; font-size: 0.95rem; font-weight: bold; display: flex; align-items: center; z-index: 9999; transform: translateY(100px); opacity: 0; transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275), opacity 0.4s ease; }
.toast-popup.show { transform: translateY(0); opacity: 1; }
.toast-icon { font-size: 1.1rem; margin-right: 12px; background: rgba(255,255,255,0.2); width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; border-radius: 50%; }
</style>

<script>
function openCardDeleteModal(deleteUrl) {
    const modal = document.getElementById('card-delete-modal');
    document.getElementById('card-confirm-delete-btn').setAttribute('href', deleteUrl);
    modal.classList.add('show');
}
function closeCardDeleteModal() {
    document.getElementById('card-delete-modal').classList.remove('show');
}
document.getElementById('card-delete-modal').addEventListener('click', function(e) {
    if (e.target === this) { closeCardDeleteModal(); }
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
