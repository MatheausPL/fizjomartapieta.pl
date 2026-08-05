<?php
// Wczytanie kodów skryptów z pliku data/opinie.json
$json_file = "../data/opinie.json";
if (!file_exists($json_file)) {
    $json_file = "data/opinie.json";
}
$op = [];
if (file_exists($json_file)) {
    $op = json_decode(file_get_contents($json_file), true) ?? [];
}

// OBSŁUGA AKCJI ZAPISU BEZPOŚREDNIO NA GÓRZE WIDOKU (DLA BEZPIECZEŃSTWA STRUKTURY)
if (isset($_GET['action']) && $_GET['action'] === 'save_opinie') {
    require_once "log_change.php";
    
    $google_script   = isset($_POST['google_script']) ? trim($_POST['google_script']) : '';
    $facebook_script = isset($_POST['facebook_script']) ? trim($_POST['facebook_script']) : '';
    
    // Zapisujemy kody w stanie surowym (bez uciekania htmlspecialchars), aby skrypty JS mogły się odpalić u użytkownika
    $op['google_script']   = $google_script;
    $op['facebook_script'] = $facebook_script;
    
    $changes = [
        "skrypt opinii Google" => ["old" => "zaktualizowano w bazie", "new" => "zaktualizowano w bazie"],
        "skrypt opinii Facebook" => ["old" => "zaktualizowano w bazie", "new" => "zaktualizowano w bazie"]
    ];
    
    file_put_contents($json_file, json_encode($op, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    log_change("Opinie", "Edycja", "Integracja Trustindex", $changes);
    
    header("Location: admin.php?page=opinie&status=success");
    exit;
}
?>

<style>
.admin-card { background: #ffffff; padding: 24px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 24px; border: 1px solid #e0e0e0; font-family: Arial, sans-serif; }
.admin-title { margin-top: 0; margin-bottom: 20px; color: #333333; font-size: 1.5rem; border-bottom: 2px solid #eaeaea; padding-bottom: 10px; font-weight: bold; }
.form-group { margin-bottom: 24px; }
.form-group label { display: block; font-weight: bold; margin-bottom: 8px; color: #444444; font-size: 0.95rem; }
.form-control { width: 100%; padding: 12px; border: 1px solid #cccccc; border-radius: 4px; box-sizing: border-box; font-family: monospace; font-size: 0.85rem; background: #f8f9fa; color: #1e293b; }
.form-control:focus { border-color: #0277bd; outline: none; background: #ffffff; }
.btn { display: inline-block; padding: 10px 20px; font-size: 0.9rem; font-weight: bold; border-radius: 4px; text-decoration: none; border: none; cursor: pointer; transition: background 0.2s; }
.btn-primary { background: #222222; color: #ffffff; }
.btn-primary:hover { background: #444444; }
.info-box { background: #f0f9ff; border-left: 4px solid #0277bd; padding: 15px; border-radius: 4px; margin-bottom: 24px; font-size: 0.9rem; color: #0369a1; line-height: 1.5; }
</style>

<div class="admin-card">
    <h2 class="admin-title">Automatyczne Opinie (Integracja Trustindex)</h2>
    
    <div class="info-box">
        <strong>💡 Instrukcja wdrożenia:</strong><br>
        1. Załóż darmowe konto na stronie <u>Trustindex.io</u> i wygeneruj widżet dla Google oraz dla Facebooka.<br>
        2. Skopiuj wygenerowany kod skryptu (cały znacznik <code>&lt;script&gt;...&lt;/script&gt;</code>) i wklej go w odpowiednie pola poniżej.<br>
        3. System automatycznie wyrenderuje obie niezależne karuzele opinii na stronie głównej Marty, odświeżając je co 24 godziny [sentry.io]!
    </div>

    <form method="POST" action="admin.php?page=opinie&action=save_opinie">
        
        <div class="form-group" style="border-left: 3px solid #d97706; padding-left: 15px;">
            <label for="code_google" style="color: #b45309;">🔴 Kod skryptu opinii Google (Trustindex):</label>
            <textarea id="code_google" name="google_script" class="form-control" rows="4" placeholder="Wklej tutaj cały znacznik <script src='https://trustindex.io?...' defer></script>..."><?= htmlspecialchars($op['google_script'] ?? '') ?></textarea>
        </div>

        <div class="form-group" style="border-left: 3px solid #1877f2; padding-left: 15px; margin-top: 30px;">
            <label for="code_facebook" style="color: #115dc0;">🔵 Kod skryptu opinii Facebook (Trustindex):</label>
            <textarea id="code_facebook" name="facebook_script" class="form-control" rows="4" placeholder="Wklej tutaj drugi znacznik <script src='https://trustindex.io?...' defer></script>..."><?= htmlspecialchars($op['facebook_script'] ?? '') ?></textarea>
        </div>

        <div style="border-top: 1px solid #eaeaea; padding-top: 20px; margin-top: 30px; text-align: right;">
            <button type="submit" class="btn btn-primary" style="background: #2e7d32;">Zapisz kody i uruchom widżety</button>
        </div>
    </form>
</div>

<!-- OBSŁUGA TOASTÓW SUKCESU -->
<?php if (isset($_GET['status'])): 
    $status = $_GET['status']; $bgColor = ($status === 'success') ? '#2e7d32' : '#c62828'; $icon = ($status === 'success') ? '✓' : '✕';
    $message = ($status === 'success') ? 'Kody widżetów opinii zostały pomyślnie zapisane!' : 'Wystąpił błąd zapisu.';
?>
    <div id="toast-notification" class="toast-popup" style="background: <?= $bgColor ?>;"><span class="toast-icon"><?= $icon ?></span><span class="toast-text"><?= $message ?></span></div>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const toast = document.getElementById("toast-notification");
        if (toast) {
            setTimeout(() => { toast.classList.add("show"); }, 100);
            setTimeout(() => { toast.classList.remove("show"); }, 4000);
        }
    });
    </script>
<?php endif; ?>
