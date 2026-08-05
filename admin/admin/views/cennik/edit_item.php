<?php
// 1. Pobieramy plik z poprawnej lokalizacji (folder data jest poziom wyżej względem admin.php)
$json_file = "../data/cennik.json";

if (!file_exists($json_file)) {
    echo "<div class='admin-card' style='background:#fff; padding:24px; border-radius:8px; border-left: 4px solid #c62828; font-family:Arial, sans-serif;'>
            <p style='color: #c62828; font-weight: bold; margin: 0;'>Błąd: Plik bazy danych nie istnieje.</p>
          </div>";
    return;
}

// 2. Dekodujemy plik JSON
$data = json_decode(file_get_contents($json_file), true) ?? [];

// 3. Pobieramy ID z adresu URL i upewniamy się, że to liczba całkowita
$id = isset($_GET['edit']) && $_GET['edit'] !== '' ? intval($_GET['edit']) : null;

// 4. Pobieramy tablicę usług bezpośrednio z płaskiej struktury items
$items = isset($data['items']) && is_array($data['items']) ? $data['items'] : [];

// 5. Sprawdzamy precyzyjnie czy klucz (indeks) istnieje w tablicy
if ($id === null || !array_key_exists($id, $items)) {
    echo "<div class='admin-card' style='background:#fff; padding:24px; border-radius:8px; border-left: 4px solid #c62828; box-shadow: 0 2px 4px rgba(0,0,0,0.05); font-family:Arial, sans-serif;'>
            <p style='color: #c62828; font-weight: bold; margin: 0;'>Błąd: Nie znaleziono pozycji o podanym ID ($id).</p>
            <a href='admin.php?page=cennik' style='display:inline-block; margin-top:15px; background:#222; color:#fff; padding:10px 20px; text-decoration:none; font-weight:bold; border-radius:4px;'>Powrót do cennika</a>
          </div>";
    return;
}

// 6. Pobieramy dane konkretnej usługi
$item = $items[$id];
?>


<style>
.admin-card { background: #ffffff; padding: 24px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 24px; border: 1px solid #e0e0e0; font-family: Arial, sans-serif; }
.admin-title { margin-top: 0; margin-bottom: 20px; color: #333333; font-size: 1.5rem; border-bottom: 2px solid #eaeaea; padding-bottom: 10px; }
.form-row { display: flex; gap: 20px; margin-bottom: 16px; }
.form-row .form-group { flex: 1; margin-bottom: 0; }
.form-group { margin-bottom: 16px; }
.form-group label { display: block; font-weight: bold; margin-bottom: 6px; color: #444444; font-size: 0.9rem; }
.form-control { width: 100%; padding: 10px 12px; border: 1px solid #cccccc; border-radius: 4px; box-sizing: border-box; font-family: inherit; font-size: 0.95rem; }
.form-control:focus { border-color: #222222; outline: none; }
.btn { display: inline-block; padding: 10px 20px; font-size: 0.9rem; font-weight: bold; border-radius: 4px; text-decoration: none; border: none; }
.btn-primary { background: #222222; color: #ffffff; }
.btn-secondary { background: #e0e0e0; color: #333333; margin-right: 10px; }
.form-actions { border-top: 1px solid #eaeaea; padding-top: 20px; margin-top: 24px; }
</style>

<div class="admin-card">
    <h2 class="admin-title">Edytuj pozycję cennika</h2>

    <form method="POST" action="admin.php?page=cennik&action=save_item">
        <!-- Ukryte pole przekazujące ID metody POST do pliku save_item.php -->
        <input type="hidden" name="id" value="<?= $id ?>">

        <div class="form-row">
            <div class="form-group">
                <label for="item_name">Nazwa usługi / produktu:</label>
                <input type="text" id="item_name" name="name" class="form-control" value="<?= htmlspecialchars($item['name'] ?? '') ?>" required placeholder="np. Konsultacja indywidualna">
            </div>
            
            <div class="form-group" style="max-width: 200px;">
                <label for="item_price">Cena:</label>
                <input type="text" id="item_price" name="price" class="form-control" value="<?= htmlspecialchars($item['price'] ?? '') ?>" required placeholder="np. 150 zł">
            </div>
        </div>

        <div class="form-group">
            <label for="item_desc">Opis usługi:</label>
            <textarea id="item_desc" name="desc" class="form-control" rows="4" placeholder="Wpisz krótki opis..."><?= htmlspecialchars($item['desc'] ?? '') ?></textarea>
        </div>

        <div class="form-actions">
            <a href="admin.php?page=cennik" class="btn btn-secondary">Anuluj</a>
            <button type="submit" class="btn btn-primary">Zapisz zmiany</button>
        </div>
    </form>
</div>
