<?php
session_start();
require "config.php";

if (!isset($_SESSION['tries'])) $_SESSION['tries'] = 0;
if ($_SESSION['tries'] >= 5) {
    die("Zbyt wiele prób logowania. Spróbuj za 10 minut.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['tries']++;

    $user = $_POST['user'] ?? '';
    $pass = $_POST['pass'] ?? '';

    if ($user === $ADMIN_USER && password_verify($pass, $ADMIN_HASH)) {
        $_SESSION['auth'] = true;
        $_SESSION['user'] = $user;            // potrzebne do logowania zmian
        $_SESSION['last_activity'] = time();  // potrzebne do auto-wylogowania
        $_SESSION['tries'] = 0;

        header("Location: admin.php");
        exit;
    }

    $error = "Niepoprawne dane logowania.";
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Logowanie</title>
<style>
body { font-family: Arial; background:#f0f0f0; }
.login-box { width:320px; margin:100px auto; padding:20px; background:white; border-radius:8px; box-shadow:0 0 10px rgba(0,0,0,.1); }
input { width:100%; padding:10px; margin:5px 0; box-sizing:border-box; }
button { width:100%; padding:10px; background:#007bff; color:white; border:none; cursor:pointer; }
button:hover { background:#0056b3; }
</style>
</head>
<body>
<div class="login-box">
<h2>Panel administracyjny</h2>
<?php if(isset($error)) echo "<p style='color:red'>$error</p>"; ?>
<form method="POST">
<input type="text" name="user" placeholder="Login">
<input type="password" name="pass" placeholder="Hasło">
<button type="submit">Zaloguj</button>
</form>
</div>
</body>
</html>
