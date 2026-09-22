<?php
require_once __DIR__ . '/db.php';
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
$error = ''; $message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? ''); $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL); $password = (string)($_POST['password'] ?? ''); $confirmation = (string)($_POST['password_confirmation'] ?? '');
    if (strlen($name)<2 || !$email || strlen($password)<8 || $password !== $confirmation) { $error='Gebruik een naam, geldig e-mailadres, wachtwoord van minimaal 8 tekens en gelijke wachtwoorden.'; }
    else { try { $query=database()->prepare('INSERT INTO `user` (name,email,password_hash,rol) VALUES (?,?,?,?)'); $query->execute([$name,$email,password_hash($password,PASSWORD_DEFAULT),'Lid']); $message='Account aangemaakt. Je kunt nu inloggen.'; } catch (PDOException $exception) { $error='Dit e-mailadres is al geregistreerd.'; } }
}
$title='Registreren'; require __DIR__ . '/header.php';
?><section style="max-width:560px;margin:65px auto"><span class="eyebrow">Nieuw lid</span><h1>Word lid.</h1><p>Maak een account om je eigen bibliotheekomgeving te gebruiken.</p><form method="post" class="panel"><div class="field"><label for="name">Naam</label><input id="name" name="name" required value="<?= e($_POST['name'] ?? '') ?>"></div><div class="field"><label for="email">E-mailadres</label><input id="email" type="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>"></div><div class="field"><label for="password">Wachtwoord</label><input id="password" type="password" name="password" minlength="8" required></div><div class="field"><label for="password_confirmation">Herhaal wachtwoord</label><input id="password_confirmation" type="password" name="password_confirmation" minlength="8" required></div><button type="submit">Account aanmaken</button></form></section><?php require __DIR__ . '/footer.php'; ?>
