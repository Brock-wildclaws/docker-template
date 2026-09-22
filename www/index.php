<?php
require_once __DIR__ . '/db.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
$pdo = database();
$search = trim($_GET['q'] ?? '');
$category = trim($_GET['category'] ?? '');
$categories = $pdo->query('SELECT DISTINCT category FROM books ORDER BY category')->fetchAll(PDO::FETCH_COLUMN);
$sql = 'SELECT title,author,category,year FROM books WHERE (title LIKE ? OR author LIKE ?)';
$params = ['%' . $search . '%', '%' . $search . '%'];
if ($category !== '') {
    $sql .= ' AND category = ?';
    $params[] = $category;
}
$sql .= ' ORDER BY title';
$query = $pdo->prepare($sql);
$query->execute($params);
$books = $query->fetchAll();
$title = 'Collectie';
require __DIR__ . '/header.php';
?>
<section style="padding:70px 0 45px;display:grid;grid-template-columns:1.1fr .9fr;gap:45px;align-items:end">
    <div><span class="eyebrow">De collectie van vandaag</span>
        <h1>Lees iets dat blijft hangen.</h1>
        <p style="color:var(--muted);font-size:18px">Blader door verhalen, ideeën en werelden uit onze bibliotheek.</p>
    </div>
    <p style="border-left:3px solid var(--coral);padding:12px 0 12px 20px;color:var(--muted)">
        <strong><?= count($books) ?> boeken om te ontdekken</strong><br>Vind je volgende favoriet.</p>
</section>
<h2>Alle boeken</h2>
<form method="get" style="display:flex;gap:10px;flex-wrap:wrap;margin:14px 0 30px"><input style="flex:1;min-width:230px"
        type="search" name="q" placeholder="Zoek op titel of auteur..." value="<?= e($search) ?>"><select
        name="category">
        <option value="">Alle categorieën</option><?php foreach ($categories as $option): ?>
            <option value="<?= e($option) ?>" <?= $category === $option ? 'selected' : '' ?>><?= e($option) ?></option>
        <?php endforeach; ?>
    </select><button type="submit">Zoeken</button></form>
<section style="display:grid;grid-template-columns:repeat(3,1fr);gap:18px;padding-bottom:70px">
    <?php foreach ($books as $book): ?>
        <article class="panel" style="min-height:250px;display:flex;flex-direction:column">
            <div
                style="width:46px;height:62px;display:grid;place-items:center;background:var(--green);color:#fff;font:bold 24px Georgia;box-shadow:8px 5px 0 var(--coral);margin-bottom:25px">
                <?= e(strtoupper(substr($book['title'], 0, 1))) ?></div><span
                class="eyebrow"><?= e($book['category']) ?></span>
            <h3><?= e($book['title']) ?></h3>
            <p class="meta"><?= e($book['author']) ?> · <?= e((string) $book['year']) ?></p>
                <div style="margin-top:auto;padding-top:20px"></div>
        </article><?php endforeach; ?>
</section><?php require __DIR__ . '/footer.php'; ?>