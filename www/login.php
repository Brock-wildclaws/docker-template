<?php
require_once __DIR__ . '/db.php';
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $query = database()->prepare('SELECT id,name,email,password_hash,rol AS role FROM `user` WHERE email = ? LIMIT 1');
    $query->execute([$email ?: '']);
    $account = $query->fetch();
    if ($account && password_verify((string)($_POST['password'] ?? ''), $account['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = ['id'=>(int)$account['id'],'name'=>$account['name'],'email'=>$account['email'],'role'=>$account['role']];
        redirect();
    }
    $error = 'Ongeldige inloggegevens.';
}
$title = 'Inloggen'; require __DIR__ . '/header.php';
?><section style="max-width:520px;margin:70px auto"><span class="eyebrow">Mijn bladwijzer</span><h1>Welkom terug.</h1><p>Log in om je eigen omgeving te bekijken.</p><form method="post" class="panel"><div class="field"><label for="email">E-mailadres</label><input id="email" type="email" name="email" required></div><div class="field"><label for="password">Wachtwoord</label><input id="password" type="password" name="password" required></div><button type="submit">Inloggen</button><p class="hint">Demo lid: sophie@example.nl / bibliotheek<br>Demo medewerker: medewerker@library.nl / admin123</p></form><p><a href="register.php">Nog geen lid? Registreer hier.</a></p></section><?php require __DIR__ . '/footer.php'; ?>
