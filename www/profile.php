<?php
require_once __DIR__ . '/db.php';
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
require_login();
$user = $_SESSION['user'];
$loans = [];
if ($user['role'] === 'Lid') {
    $query = database()->prepare('SELECT books.title,books.author FROM loans JOIN books ON books.id=loans.book_id WHERE loans.user_id=? AND loans.returned_at IS NULL');
    $query->execute([$user['id']]); $loans = $query->fetchAll();
}
$title = 'Mijn profiel'; require __DIR__ . '/header.php';
?><section class="detail" style="max-width:700px;margin:55px 0 70px"><span class="eyebrow">Persoonlijke omgeving</span><h1><?= e($user['name']) ?></h1><p class="meta"><?= e($user['email']) ?> · <?= e($user['role']) ?></p><hr><h2>Mijn gegevens</h2><p>Je profiel is alleen zichtbaar na een geldige login.</p><?php if ($user['role']==='Lid'): ?><h2 style="margin-top:35px">Mijn leningen</h2><?php if ($loans): ?><?php foreach ($loans as $loan): ?><p><?= e($loan['title']) ?> <span class="meta">door <?= e($loan['author']) ?></span></p><?php endforeach; ?><?php else: ?><p class="meta">Je hebt geen actieve leningen.</p><?php endif; ?><?php endif; ?></section><?php require __DIR__ . '/footer.php'; ?>
