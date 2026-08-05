<?php
session_start();

// Sprawdzenie logowania
if (!isset($_SESSION['auth'])) {
    header("Location: login.php");
    exit;
}

// Auto-wylogowanie po 15 minutach
$timeout = 900;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}
$_SESSION['last_activity'] = time();

// Zabezpieczenie parametru page (basename zapobiega wstrzykiwaniu ścieżek typu ../)
$page = isset($_GET['page']) ? basename($_GET['page']) : 'cennik';

// Routing akcji (np. admin.php?page=cennik&action=save_header)
if (isset($_GET['action'])) {
    $action = basename($_GET['action']); 
    $actionFile = "views/$page/actions/$action.php";

    if (file_exists($actionFile)) {
        include $actionFile;
        exit;
    } else {
        http_response_code(404);
        echo "<p>Nie znaleziono akcji: <strong>$action</strong></p>";
        exit;
    }
}

// Routing edycji (np. admin.php?page=cennik&edit=0)
if (isset($_GET['edit'])) {
    $editFile = "views/$page/edit_item.php";

    if (file_exists($editFile)) {
        include $editFile;
        exit;
    } else {
        http_response_code(404);
        echo "<p>Brak pliku edycji dla: <strong>$page</strong></p>";
        exit;
    }
}

// Ścieżka do widoku
$viewFile = "views/$page/index.php";
?>
<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<title>Panel administracyjny</title>
<style>
body { font-family: Arial, sans-serif; margin:0; background:#f5f5f5; }
.sidebar { width:200px; background:#222; color:white; height:100vh; float:left; padding:20px; box-sizing: border-box; }
.sidebar a { display:block; color:white; padding:10px; text-decoration:none; margin-bottom:5px; border-radius: 4px; }
.sidebar a:hover, .sidebar a.active { background:#444; }
.content { margin-left:220px; padding:20px; }
</style>
</head>
<body>

<div class="sidebar">
    <h3>Panel</h3>
    <a href="admin.php?page=o-mnie" class="<?php echo $page === 'o-mnie' ? 'active' : ''; ?>">O mnie</a>
	<a href="admin.php?page=pierwsza-wizyta" class="<?php echo $page === 'pierwsza-wizyta' ? 'active' : ''; ?>">Pierwsza wizyta</a>
    <a href="admin.php?page=jak-pracuje" class="<?php echo $page === 'jak-pracuje' ? 'active' : ''; ?>">Jak pracuję</a>
	<a href="admin.php?page=gdzie-pracuje" class="<?php echo $page === 'gdzie-pracuje' ? 'active' : ''; ?>">Gdzie pracuję</a>
    <a href="admin.php?page=opinie" class="<?php echo $page === 'opinie' ? 'active' : ''; ?>">Opinie</a>
	<a href="admin.php?page=cennik" class="<?php echo $page === 'cennik' ? 'active' : ''; ?>">Cennik</a>
    <a href="admin.php?page=kontakt" class="<?php echo $page === 'kontakt' ? 'active' : ''; ?>">Kontakt</a> 
	<a href="admin.php?page=logi" class="<?php echo $page === 'logi' ? 'active' : ''; ?>">Dziennik zmian</a> 
    <a href="logout.php" style="color: #ff6b6b; margin-top: 20px;">Wyloguj</a>
</div>

<div class="content">
    <?php
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            http_response_code(404);
            echo "<p>Nie znaleziono strony: <strong>$page</strong></p>";
        }
    ?>
</div>

</body>
</html>
