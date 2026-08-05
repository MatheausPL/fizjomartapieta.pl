<style>
/* Style spójne z panelem i formularzem edycji */
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
}
.form-row { 
    display: flex; 
    gap: 20px; 
    margin-bottom: 16px; 
}
.form-row .form-group { 
    flex: 1; 
    margin-bottom: 0; 
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
}
.btn-primary { 
    background: #222222; 
    color: #ffffff; 
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
}
</style>

<div class="admin-card">
    <h2 class="admin-title">Dodaj nową pozycję do cennika</h2>

    <!-- Kierujemy akcję do admin.php, podając nazwę nowej akcji save_new_item -->
    <form method="POST" action="admin.php?page=cennik&action=save_new_item">

        <div class="form-row">
            <div class="form-group">
                <label for="item_name">Nazwa usługi / produktu:</label>
                <input type="text" id="item_name" name="name" class="form-control" required placeholder="np. Konsultacja psychologiczna">
            </div>
            
            <div class="form-group" style="max-width: 200px;">
                <label for="item_price">Cena:</label>
                <input type="text" id="item_price" name="price" class="form-control" required placeholder="np. 180 zł">
            </div>
        </div>

        <div class="form-group">
            <label for="item_desc">Opis usługi (opcjonalnie):</label>
            <textarea id="item_desc" name="desc" class="form-control" rows="4" placeholder="Wpisz krótki opis usługi, który wyświetli się na stronie..."></textarea>
        </div>

        <div class="form-actions">
            <a href="admin.php?page=cennik" class="btn btn-secondary">Anuluj</a>
            <button type="submit" class="btn btn-primary">Dodaj pozycję</button>
        </div>
    </form>
</div>
