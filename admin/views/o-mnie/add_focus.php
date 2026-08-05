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
    <h2 class="admin-title">Dodaj nowy punkt skupienia</h2>

    <!-- Formularz kieruje dane do save_new_focus.php -->
    <form method="POST" action="admin.php?page=o-mnie&action=save_new_focus">

        <div class="form-group">
            <label for="f_text">Nazwa punktu skupienia:</label>
            <input type="text" id="f_text" name="text" class="form-control" required placeholder="np. Terapia punktów spustowych">
            <small style="color: #777; margin-top: 6px; display: block;">Wpisz krótki, zwięzły punkt, który wyświetli się na liście pod zdjęciem Marty.</small>
        </div>

        <div class="form-actions">
            <a href="admin.php?page=o-mnie" class="btn btn-secondary">Anuluj</a>
            <button type="submit" class="btn btn-primary" style="background: #2e7d32; color: #ffffff;">Dodaj punkt</button>
        </div>
    </form>
</div>
