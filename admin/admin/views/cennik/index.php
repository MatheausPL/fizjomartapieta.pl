<?php
// Wczytanie czystego pliku z nowej lokalizacji i struktury
$cennik = json_decode(file_get_contents("../data/cennik.json"), true);

// OBSŁUGA STRONY EDYCJI PRZEDMIOTU
if (isset($_GET['edit'])) {
    include "edit_item.php";
    return;
}

// OBSŁUGA STRONY DODAWANIA NOWEGO PRZEDMIOTU
if (isset($_GET['add'])) {
    include "add_item.php";
    return;
}
?>



<!-- SEKCJA 1: FORMULARZ ZARZĄDZANIA NAGŁÓWKIEM -->
<div class="admin-card">
    <h2 class="admin-title">Nagłówek cennika</h2>
    
    <form method="POST" action="admin.php?page=cennik&action=save_header">
        <div class="form-group">
            <label for="title">Tytuł główny:</label>
            <input type="text" id="title" name="title" class="form-control" value="<?= htmlspecialchars($cennik['title'] ?? '') ?>" placeholder="Wpisz tytuł sekcji...">
        </div>

        <div class="form-group">
            <label for="subtitle">Podtytuł / Opis dodatkowy:</label>
            <textarea id="subtitle" name="subtitle" class="form-control" rows="3" placeholder="Wpisz krótki podtytuł lub informacje dodatkowe..."><?= htmlspecialchars($cennik['subtitle'] ?? '') ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Zapisz zmiany nagłówka</button>
    </form>
</div>

<!-- SEKCJA 2: TABELA Z POZYCJAMI CENNIKA -->
<div class="admin-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 2px solid #eaeaea; padding-bottom: 10px;">
        <h3 style="margin: 0; color: #333333; font-size: 1.3rem;">Pozycje cennika</h3>
        <a href="admin.php?page=cennik&add=true" class="btn btn-success">+ Dodaj nową pozycję</a>
    </div>

    <div class="admin-table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Nazwa usługi / produktu</th>
                    <th style="width: 120px;">Cena</th>
                    <th>Opis</th>
                    <th style="width: 160px; text-align: right;">Akcje</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $total_items = isset($cennik['items']) ? count($cennik['items']) : 0;
                if ($total_items > 0): 
                    foreach ($cennik['items'] as $i => $item): 
                ?>
                <tr>
                    <td style="font-weight: 600;">
                        <div style="display: inline-flex; flex-direction: column; vertical-align: middle; margin-right: 12px; gap: 2px;">
                            <?php if ($i > 0): ?>
                                <a href="admin.php?page=cennik&action=move_item&id=<?= $i ?>&direction=up" style="text-decoration: none; color: #777; font-size: 0.8rem; line-height: 1;" title="Przesuń w górę">▲</a>
                            <?php else: ?>
                                <span style="color: #ccc; font-size: 0.8rem; line-height: 1; visibility: hidden;">▲</span>
                            <?php endif; ?>

                            <?php if ($i < $total_items - 1): ?>
                                <a href="admin.php?page=cennik&action=move_item&id=<?= $i ?>&direction=down" style="text-decoration: none; color: #777; font-size: 0.8rem; line-height: 1;" title="Przesuń w dół">▼</a>
                            <?php else: ?>
                                <span style="color: #ccc; font-size: 0.8rem; line-height: 1; visibility: hidden;">▼</span>
                            <?php endif; ?>
                        </div>
                        <?= htmlspecialchars($item['name'] ?? '') ?>
                    </td>
                    <td style="color: #2e7d32; font-weight: bold;"><?= htmlspecialchars($item['price'] ?? '') ?></td>
                    <td style="color: #666666; font-size: 0.9rem;"><?= htmlspecialchars($item['desc'] ?? '') ?></td>
                    <td style="text-align: right;">
                        <div class="table-actions" style="justify-content: flex-end;">
                            <a href="admin.php?page=cennik&edit=<?= $i ?>" class="btn btn-sm btn-edit">Edytuj</a>
                            <button type="button" class="btn btn-sm btn-delete" onclick="openDeleteModal('admin.php?page=cennik&action=delete_item&id=<?= $i ?>')">Usuń</button>
                        </div>
                    </td>
                </tr>
                <?php 
                    endforeach; 
                else: 
                ?>
                    <tr>
                        <td colspan="4" style="text-align: center; color: #999999; padding: 30px;">Brak pozycji w cenniku. Dodaj pierwszą pozycję powyżej.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- SEKCJA 3: UNIKALNE OKNO MODALNE USUWANIA DLA CENNIKA -->
<div id="delete-modal" class="modal-overlay-przyp">
    <div class="modal-box-przyp">
        <div class="modal-icon">⚠️</div>
        <h3 class="modal-title-przyp">Potwierdź usunięcie</h3>
        <p class="modal-text">Czy na pewno chcesz trwale usunąć tę pozycję z cennika? Tej operacji nie można cofnąć.</p>
        <div class="modal-actions">
            <button type="button" class="btn-modal btn-modal-cancel" onclick="closeDeleteModal()">Anuluj</button>
            <a id="confirm-delete-btn" href="#" class="btn-modal btn-modal-confirm">Tak, usuń</a>
        </div>
    </div>
</div>

<!-- SEKCJA 4: DYNAMICZNE POWIADOMIENIA TOAST -->
<?php if (isset($_GET['status'])): 
    $status = $_GET['status'];
    $bgColor = ($status === 'success') ? '#2e7d32' : '#c62828';
    $icon = ($status === 'success') ? '✓' : '✕';
    $message = ($status === 'success') ? 'Zmiany zostały zapisane!' : 'Wystąpił błąd podczas zapisu danych.';
?>
    <div id="toast-notification" class="toast-popup" style="background: <?= $bgColor ?>;">
        <span class="toast-icon"><?= $icon ?></span>
        <span class="toast-text"><?= $message ?></span>
    </div>
<?php endif; ?>

<!-- SEKCJA 5: STYLE CSS -->
<style>
.admin-card { background: #ffffff; padding: 24px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 24px; border: 1px solid #e0e0e0; font-family: Arial, sans-serif; }
.admin-title { margin-top: 0; margin-bottom: 20px; color: #333333; font-size: 1.5rem; border-bottom: 2px solid #eaeaea; padding-bottom: 10px; }
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
.table-actions { display: flex; }

/* STYLIZACJA MODALA CENNIKA - CAŁKOWICIE ODSEPAROWANA DLA PRZYWRÓCENIA WYGLĄDU */
.modal-overlay-przyp { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); display: flex; align-items: center; justify-content: center; z-index: 10000; opacity: 0; pointer-events: none; transition: opacity 0.3s ease; font-family: Arial, sans-serif; }
.modal-overlay-przyp.show { opacity: 1; pointer-events: auto; }
.modal-box-przyp { background: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.15); max-width: 400px; width: 90%; text-align: center; transform: scale(0.8); transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
.modal-overlay-przyp.show .modal-box-przyp { transform: scale(1); }
.modal-title-przyp { margin: 0 0 10px 0; color: #222222; font-size: 1.3rem; }
.modal-text { color: #666666; font-size: 0.95rem; line-height: 1.5; margin: 0 0 24px 0; }
.modal-actions { display: flex; justify-content: center; gap: 12px; }
.btn-modal { padding: 10px 24px; font-size: 0.9rem; font-weight: bold; border-radius: 4px; text-decoration: none; border: none; cursor: pointer; transition: background 0.2s; }
.btn-modal-cancel { background: #e0e0e0; color: #333333; }
.btn-modal-cancel:hover { background: #d5d5d5; }
.btn-modal-confirm { background: #c62828; color: #ffffff; }
.btn-modal-confirm:hover { background: #b71c1c; }

.toast-popup { position: fixed; bottom: 30px; right: 30px; color: white; padding: 16px 24px; border-radius: 6px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); font-family: Arial, sans-serif; font-size: 0.95rem; font-weight: bold; display: flex; align-items: center; z-index: 9999; transform: translateY(100px); opacity: 0; transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275), opacity 0.4s ease; }
.toast-popup.show { transform: translateY(0); opacity: 1; }
.toast-icon { font-size: 1.1rem; margin-right: 12px; background: rgba(255,255,255,0.2); width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; border-radius: 50%; }
</style>

<!-- SEKCJA 6: JAVASCRIPT -->
<script>
function openDeleteModal(deleteUrl) {
    const modal = document.querySelector('.modal-overlay-przyp');
    const confirmBtn = document.getElementById('confirm-delete-btn');
    confirmBtn.setAttribute('href', deleteUrl);
    modal.classList.add('show');
}

function closeDeleteModal() {
    const modal = document.querySelector('.modal-overlay-przyp');
    modal.classList.remove('show');
}

document.querySelector('.modal-overlay-przyp').addEventListener('click', function(e) {
    if (e.target === this) { closeDeleteModal(); }
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
