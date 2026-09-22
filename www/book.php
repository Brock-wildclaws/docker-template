<?php
require_once __DIR__ . '/db.php';
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
$id = filter_var($_GET['id'] ?? '', FILTER_VALIDATE_INT);
if (!$id) { http_response_code(404); exit('Boek niet gevonden.'); }
$query = database()->prepare('SELECT id,title,author,category,year,description FROM books WHERE id = ?');
$query->execute([$id]);
$book = $query->fetch();
if (!$book) { http_response_code(404); exit('Boek niet gevonden.'); }
$title = $book['title'];
require __DIR__ . '/header.php';
?><section class="detail" style="max-width:760px;margin:45px 0 70px"><a href="index.php" style="color:var(--green);font:14px Arial">← Terug naar collectie</a><div style="width:70px;height:94px;display:grid;place-items:center;background:var(--green);color:#fff;font:bold 36px Georgia;box-shadow:8px 5px 0 var(--coral);margin:28px 0"><?= e(strtoupper(substr($book['title'],0,1))) ?></div><span class="eyebrow"><?= e($book['category']) ?></span><h1><?= e($book['title']) ?></h1><p class="meta"><?= e($book['author']) ?> · <?= e((string)$book['year']) ?></p><p style="font-size:19px;margin-top:30px"><?= e($book['description']) ?></p></section><?php require __DIR__ . '/footer.php'; ?>
