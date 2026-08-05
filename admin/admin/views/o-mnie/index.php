<?php
$json_file = "../data/o-mnie.json";
if (!file_exists($json_file)) { $json_file = "data/o-mnie.json"; }
$om = [];
if (file_exists($json_file)) {
    $om = json_decode(file_get_contents($json_file), true) ?? [];
}
$paragraphs_items = isset($om['items']) && is_array($om['items']) ? $om['items'] : [];
$focus_items = isset($om['focus_items']) && is_array($om['focus_items']) ? $om['focus_items'] : [];

// OBSŁUGA PODSTRON DLA AKAPITÓW
if (isset($_GET['edit_p'])) { include "edit_paragraph.php"; return; }
if (isset($_GET['add_p'])) { include "add_paragraph.php"; return; }

// OBSŁUGA PODSTRON DLA PUNKTÓW SKUPIENIA
if (isset($_GET['edit_f'])) { include "edit_focus.php"; return; }
if (isset($_GET['add_f'])) { include "add_focus.php"; return; }

$photos_folder = "../img/photos/";
$available_photos = [];
if (is_dir($photos_folder)) {
    $files = array_diff(scandir($photos_folder), ['.', '..']);
    foreach ($files as $file) {
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) { $available_photos[] = "img/photos/" . $file; }
    }
}
?>
<div class="admin-card">
    <h2 class="admin-title">1. Treść wprowadzająca i akapity</h2>
    <form method="POST" action="admin.php?page=o-mnie&action=save_o_mnie">
        <input type="hidden" name="save_part" value="text_header">
        <div class="form-group">
            <label for="subtitle">Tag sekcji (mały nagłówek):</label>
            <input type="text" id="subtitle" name="subtitle" class="form-control" value="<?= htmlspecialchars($om['subtitle'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label for="header_text">Główny tekst wprowadzający:</label>
            <textarea id="header_text" name="header_text" class="form-control" rows="2"><?= htmlspecialchars($om['header_text'] ?? '') ?></textarea>
        </div>
        <div style="text-align: right; margin-bottom: 20px;">
            <button type="submit" class="btn btn-primary">Zapisz nagłówki tekstowe</button>
        </div>
    </form>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 30px; margin-bottom: 15px; border-top: 1px solid #eaeaea; padding-top: 20px;">
        <h3 style="margin: 0; color: #333333; font-size: 1.1rem;">Zarządzanie akapitami opisu</h3>
        <a href="admin.php?page=o-mnie&add_p=true" class="btn btn-success btn-sm">+ Dodaj nowy akapit</a>
    </div>
    <div class="admin-table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width: 70px; text-align: center;">Kolejność</th>
                    <th>Treść akapitu</th>
                    <th style="width: 150px; text-align: right;">Akcje</th>
                </tr>
            </thead>
            <tbody>
                <?php $total_p = count($paragraphs_items); if ($total_p > 0): foreach ($paragraphs_items as $i => $p_item): ?>
                <tr>
                    <td style="text-align: center; vertical-align: middle;">
                        <div style="display: inline-flex; gap: 8px;">
                            <?= $i > 0 ? '<a href="admin.php?page=o-mnie&action=move_paragraph&id='.$i.'&direction=up" style="text-decoration:none;color:#777;">▲</a>' : '<span style="color:#ccc;">▲</span>' ?>
                            <?= $i < $total_p - 1 ? '<a href="admin.php?page=o-mnie&action=move_paragraph&id='.$i.'&direction=down" style="text-decoration:none;color:#777;">▼</a>' : '<span style="color:#ccc;">▼</span>' ?>
                        </div>
                    </td>
                    <td style="color: #333333; font-size: 0.95rem; line-height: 1.4;"><?= nl2br(htmlspecialchars($p_item['text'] ?? '')) ?></td>
                    <td style="text-align: right; vertical-align: middle;">
                        <a href="admin.php?page=o-mnie&edit_p=<?= $i ?>" class="btn btn-sm btn-edit">Edytuj</a>
                        <button type="button" class="btn btn-sm btn-delete" onclick="openDeleteModal('admin.php?page=o-mnie&action=delete_paragraph&id=<?= $i ?>', 'akapit')">Usuń</button>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                    <tr><td colspan="3" style="text-align: center; color: #999; padding: 24px; font-style: italic;">Brak akapitów.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<div class="admin-card">
    <h2 class="admin-title">2. Zdjęcie i punkty skupienia</h2>
    <form method="POST" action="admin.php?page=o-mnie&action=save_o_mnie" enctype="multipart/form-data">
        <input type="hidden" name="save_part" value="visual_header">
        <div class="form-row" style="align-items:center; margin-bottom:24px; background:#f9f9f9; padding:15px; border-radius:6px; border:1px dashed #ccc;">
            <div style="flex:0 0 100px; text-align:center;">
                <img id="current-profile-preview" src="../<?= htmlspecialchars($om['image'] ?? 'img/photos/marta.webp') ?>" alt="Podgląd">
            </div>
            <div class="form-group" style="flex:1; margin-bottom:0;">
                <label for="image_file">Wgraj nowe zdjęcie z komputera LUB:</label>
                <div style="display:flex; gap:10px; margin-bottom:6px;">
                    <input type="file" id="image_file" name="image_file" class="form-control" style="padding:6px 12px;" onchange="clearGalleryChoice()">
                    <button type="button" class="btn btn-edit" style="white-space:nowrap; padding:10px;" onclick="openGalleryModal()">Wybierz z serwera</button>
                </div>
                <input type="hidden" id="chosen_gallery_image" name="chosen_gallery_image" value="">
                <small id="image-status-note" style="color:#777; display:block;">Obecny plik: <?= htmlspecialchars($om['image'] ?? 'img/photos/marta.webp') ?></small>
            </div>
        </div>
        <div class="form-group">
            <label for="focus_title">Tytuł listy skupienia (opis pod zdjęciem):</label>
            <input type="text" id="focus_title" name="focus_title" class="form-control" value="<?= htmlspecialchars($om['focus_title'] ?? '') ?>" required>
        </div>
        <div style="text-align: right; margin-bottom: 20px;">
            <button type="submit" class="btn btn-primary">Zapisz zdjęcie i tytuł listy</button>
        </div>
    </form>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 30px; margin-bottom: 15px; border-top: 1px solid #eaeaea; padding-top: 20px;">
        <h3 style="margin: 0; color: #333333; font-size: 1.1rem;">Zarządzanie punktami listy</h3>
        <a href="admin.php?page=o-mnie&add_f=true" class="btn btn-success btn-sm">+ Dodaj nowy punkt</a>
    </div>
    <div class="admin-table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width: 70px; text-align: center;">Kolejność</th>
                    <th>Nazwa punktu skupienia</th>
                    <th style="width: 150px; text-align: right;">Akcje</th>
                </tr>
            </thead>
            <tbody>
                <?php $total_f = count($focus_items); if ($total_f > 0): foreach ($focus_items as $i => $f_item): ?>
                <tr>
                    <td style="text-align: center; vertical-align: middle;">
                        <div style="display: inline-flex; gap: 8px;">
                            <?= $i > 0 ? '<a href="admin.php?page=o-mnie&action=move_focus&id='.$i.'&direction=up" style="text-decoration:none;color:#777;">▲</a>' : '<span style="color:#ccc;">▲</span>' ?>
                            <?= $i < $total_f - 1 ? '<a href="admin.php?page=o-mnie&action=move_focus&id='.$i.'&direction=down" style="text-decoration:none;color:#777;">▼</a>' : '<span style="color:#ccc;">▼</span>' ?>
                        </div>
                    </td>
                    <td style="color: #333333; font-weight: 600;"><?= htmlspecialchars($f_item['text'] ?? '') ?></td>
                    <td style="text-align: right; vertical-align: middle;">
                        <a href="admin.php?page=o-mnie&edit_f=<?= $i ?>" class="btn btn-sm btn-edit">Edytuj</a>
                        <button type="button" class="btn btn-sm btn-delete" onclick="openDeleteModal('admin.php?page=o-mnie&action=delete_focus&id=<?= $i ?>', 'punkt')">Usuń</button>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                    <tr><td colspan="3" style="text-align: center; color: #999; padding: 24px; font-style: italic;">Lista punktów skupienia jest obecnie pusta.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="p-delete-modal" class="p-modal-overlay">
    <div class="p-modal-box">
        <div style="font-size: 2rem; margin-bottom: 10px;">⚠️</div>
        <h3 id="modal-delete-title" style="margin: 0 0 10px 0; color: #222;">Potwierdź usunięcie</h3>
        <p id="modal-delete-text" style="color: #666; font-size: 0.95rem; margin-bottom: 20px;">Czy na pewno chcesz to usunąć?</p>
        <div style="display: flex; justify-content: center; gap: 12px;">
            <button type="button" class="btn-modal btn-modal-cancel" onclick="closeDeleteModal()">Anuluj</button>
            <a id="p-confirm-delete-btn" href="#" class="btn-modal btn-modal-confirm" style="background:#c62828; color:#fff;">Tak, usuń</a>
        </div>
    </div>
</div>

<div id="gallery-modal" class="gallery-modal-overlay">
    <div class="gallery-modal-box">
        <h3 class="modal-title" style="text-align:left; margin:0 0 10px 0; border-bottom:1px solid #eee; padding-bottom:8px;">Biblioteka zdjęć na serwerze</h3>
        <div class="gallery-grid">
            <?php if (!empty($available_photos)): foreach ($available_photos as $photo): ?>
                <div class="gallery-item" onclick="selectGalleryImage('<?= htmlspecialchars($photo) ?>', this)"><img src="../<?= htmlspecialchars($photo) ?>" alt="Zdjęcie"></div>
            <?php endforeach; else: ?><p style="grid-column:1/-1; color:#999; font-style:italic; padding:20px 0;">Katalog zdjęć jest pusty.</p><?php endif; ?>
        </div>
        <div class="modal-actions" style="display:flex; justify-content:flex-end; gap:12px; margin-top:20px; border-top:1px solid #eee; padding-top:15px;">
            <button type="button" class="btn-modal btn-modal-cancel" onclick="closeGalleryModal()">Anuluj</button>
            <button type="button" class="btn-modal btn-modal-confirm" onclick="confirmGalleryChoice()">Zatwierdź wybór</button>
        </div>
    </div>
</div>

<?php if (isset($_GET['status'])): 
    $status = $_GET['status']; $bgColor = ($status === 'success') ? '#2e7d32' : '#c62828'; $icon = ($status === 'success') ? '✓' : '✕';
    $message = ($status === 'success') ? 'Zmiany zostały pomyślnie zapisane!' : 'Wystąpił błąd operacji.';
?>
    <div id="toast-notification" class="toast-popup" style="background: <?= $bgColor ?>;"><span class="toast-icon"><?= $icon ?></span><span class="toast-text"><?= $message ?></span></div>
<?php endif; ?>
<style>
.admin-card { background:#ffffff; padding:24px; border-radius:8px; box-shadow:0 2px 4px rgba(0,0,0,0.05); margin-bottom:24px; border:1px solid #e0e0e0; font-family:Arial, sans-serif; }
.admin-title { margin-top:0; margin-bottom:20px; color:#333333; font-size:1.3rem; border-bottom:2px solid #eaeaea; padding-bottom:10px; font-weight:bold; }
.form-row { display:flex; gap:20px; margin-bottom:16px; }
.form-row .form-group { flex:1; margin-bottom:0; }
.form-group { margin-bottom:16px; }
.form-group label { display:block; font-weight:bold; margin-bottom:6px; color:#444444; font-size:0.9rem; }
.form-control { width:100%; padding:10px 12px; border:1px solid #cccccc; border-radius:4px; box-sizing:border-box; font-family:inherit; font-size:0.95rem; }
.form-control:focus { border-color:#222222; outline:none; }
.btn { display:inline-block; padding:10px 20px; font-size:0.9rem; font-weight:bold; border-radius:4px; text-decoration:none; cursor:pointer; border:none; transition:background 0.2s; }
.btn-primary { background:#222222; color:#ffffff; }
.btn-primary:hover { background:#444444; }
.btn-success { background:#2e7d32; color:#ffffff; }
.btn-success:hover { background:#1b5e20; }
.btn-edit { background:#0277bd; color:#ffffff; }
.btn-edit:hover { background:#01579b; }
.btn-sm { padding:6px 12px; font-size:0.8rem; }
.btn-delete { background:#c62828; color:#ffffff; }
.btn-delete:hover { background:#b71c1c; }
#current-profile-preview { width: 80px; height: 80px; aspect-ratio: 1/1; object-fit: cover; object-position: center; border-radius: 4px; border: 1px solid #ddd; display: block; margin: 0 auto; }
.admin-table-container { overflow-x: auto; margin-top: 10px; margin-bottom: 20px; }
.admin-table { width: 100%; border-collapse: collapse; background: #ffffff; text-align: left; }
.admin-table th { background: #f8f9fa; color: #555555; font-weight: bold; padding: 12px 14px; border-bottom: 2px solid #eaeaea; font-size: 0.9rem; }
.admin-table td { padding: 12px 14px; border-bottom: 1px solid #eaeaea; color: #333333; font-size: 0.92rem; }
.admin-table tr:hover { background: #fdfdfd; }
.p-modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); display: flex; align-items: center; justify-content: center; z-index: 10005; opacity: 0; pointer-events: none; transition: opacity 0.3s ease; font-family: Arial, sans-serif; }
.p-modal-overlay.show { opacity: 1; pointer-events: auto; }
.p-modal-box { background: #ffffff; padding: 25px; border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.15); max-width: 400px; width: 90%; text-align: center; transform: scale(0.8); transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
.p-modal-overlay.show .p-modal-box { transform: scale(1); }
.gallery-modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); display: flex; align-items: center; justify-content: center; z-index: 10000; opacity: 0; pointer-events: none; transition: opacity 0.3s ease; font-family: Arial, sans-serif; }
.gallery-modal-overlay.show { opacity: 1; pointer-events: auto; }
.gallery-modal-box { background: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.15); max-width: 600px; width: 90%; text-align: center; transform: scale(0.8); transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
.gallery-modal-overlay.show .gallery-modal-box { transform: scale(1); }
.gallery-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(110px, 1fr)); gap: 12px; max-height: 320px; overflow-y: auto; padding: 10px; border: 1px solid #e0e0e0; border-radius: 6px; background: #fcfcfc; margin-top: 15px; }
.gallery-item { cursor: pointer; border: 3px solid transparent; border-radius: 6px; overflow: hidden; transition: transform 0.15s, border-color 0.15s; box-sizing: border-box; background: #eee; aspect-ratio: 1/1; display: flex; align-items: center; justify-content: center; }
.gallery-item img { width: 100%; height: 100%; max-width: 100%; max-height: 100%; object-fit: cover; object-position: center; display: block; }
.gallery-item:hover { transform: scale(1.03); border-color: #ccc; }
.gallery-item.selected { border-color: #0277bd !important; box-shadow: 0 0 8px rgba(2,119,189,0.4); }
.toast-popup { position: fixed; bottom: 30px; right: 30px; color: white; padding: 16px 24px; border-radius: 6px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); font-family: Arial, sans-serif; font-size: 0.95rem; font-weight: bold; display: flex; align-items: center; z-index: 9999; transform: translateY(100px); opacity: 0; transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275), opacity 0.4s ease; }
.toast-popup.show { transform: translateY(0); opacity: 1; }
.toast-icon { font-size: 1.1rem; margin-right: 12px; background: rgba(255,255,255,0.2); width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; border-radius: 50%; }
.btn-modal { padding: 10px 24px; font-size: 0.9rem; font-weight: bold; border-radius: 4px; text-decoration: none; border: none; cursor: pointer; transition: background 0.2s; display: inline-block; }
.btn-modal-cancel { background: #e0e0e0; color: #333333; }
.btn-modal-cancel:hover { background: #d5d5d5; }
.btn-modal-confirm { background: #c62828; color: #ffffff; }
.btn-modal-confirm:hover { background: #b71c1c; }
</style>
<script>
let temporarySelectedSrc = "";
function openGalleryModal() { document.querySelector('.gallery-modal-overlay').classList.add('show'); }
function closeGalleryModal() { document.querySelector('.gallery-modal-overlay').classList.remove('show'); }
function selectGalleryImage(src, element) {
    document.querySelectorAll('.gallery-item').forEach(el => el.classList.remove('selected'));
    element.classList.add('selected');
    temporarySelectedSrc = src;
}
function confirmGalleryChoice() {
    if (temporarySelectedSrc !== "") {
        document.getElementById('chosen_gallery_image').value = temporarySelectedSrc;
        document.getElementById('current-profile-preview').src = "../" + temporarySelectedSrc;
        document.getElementById('image_file').value = "";
    }
    closeGalleryModal();
}
function clearGalleryChoice() {
    document.getElementById('chosen_gallery_image').value = "";
    document.querySelectorAll('.gallery-item').forEach(el => el.classList.remove('selected'));
    temporarySelectedSrc = "";
    const fileInput = document.getElementById('image_file');
    const previewImg = document.getElementById('current-profile-preview');
    if (fileInput.files && fileInput.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) { if (previewImg) previewImg.src = e.target.result; };
        reader.readAsDataURL(fileInput.files[0]);
    }
}
function openDeleteModal(deleteUrl, type) {
    const modal = document.getElementById('p-delete-modal');
    document.getElementById('p-confirm-delete-btn').setAttribute('href', deleteUrl);
    if(type === 'akapit') {
        document.getElementById('modal-delete-title').textContent = "Potwierdź usunięcie akapitu";
        document.getElementById('modal-delete-text').textContent = "Czy na pewno chcesz bezpowrotnie usunąć ten akapit opisu?";
    } else {
        document.getElementById('modal-delete-title').textContent = "Potwierdź usunięcie punktu";
        document.getElementById('modal-delete-text').textContent = "Czy na pewno chcesz bezpowrotnie usunąć ten punkt skupienia z listy?";
    }
    modal.classList.add('show');
}
function closeDeleteModal() { document.getElementById('p-delete-modal').classList.remove('show'); }

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
