<?php
declare(strict_types=1);

function database(): PDO
{
    static $pdo;
    if ($pdo instanceof PDO) {
        return $pdo;
    }
    $pdo = new PDO(
        'mysql:host=mariadb;dbname=library;charset=utf8mb4',
        'user',
        'password',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
    $pdo->prepare("CREATE TABLE IF NOT EXISTS `user` (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(120) NOT NULL, email VARCHAR(190) NOT NULL UNIQUE, password VARCHAR(255) NULL, password_hash VARCHAR(255) NULL, rol VARCHAR(20) NOT NULL)")->execute();
    migratePasswords($pdo);
    $pdo->prepare("CREATE TABLE IF NOT EXISTS books (id INT AUTO_INCREMENT PRIMARY KEY, title VARCHAR(190) NOT NULL, author VARCHAR(190) NOT NULL, category VARCHAR(80) NOT NULL, year SMALLINT NOT NULL, status VARCHAR(20) NOT NULL DEFAULT 'Beschikbaar', description TEXT NOT NULL)")->execute();
    $pdo->prepare("CREATE TABLE IF NOT EXISTS loans (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL, book_id INT NOT NULL, returned_at DATETIME NULL)")->execute();
    seed($pdo);
    return $pdo;
}

function migratePasswords(PDO $pdo): void
{
    $column = $pdo->prepare("SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?");
    $column->execute(['user', 'password']);
    $hasLegacyPassword = (int) $column->fetchColumn() > 0;
    $column->execute(['user', 'password_hash']);
    $hasPasswordHash = (int) $column->fetchColumn() > 0;

    if (!$hasPasswordHash) {
        $pdo->prepare("ALTER TABLE `user` ADD COLUMN password_hash VARCHAR(255) NULL AFTER password")->execute();
    }
    if (!$hasLegacyPassword) {
        return;
    }

    $users = $pdo->query('SELECT id, password, password_hash FROM `user`')->fetchAll();
    $update = $pdo->prepare('UPDATE `user` SET password_hash = ?, password = ? WHERE id = ?');
    foreach ($users as $user) {
        $legacyPassword = (string) ($user['password'] ?? '');
        $existingHash = (string) ($user['password_hash'] ?? '');
        if ($legacyPassword !== '' && !password_get_info($legacyPassword)['algo']) {
            $hash = password_hash($legacyPassword, PASSWORD_DEFAULT);
            $update->execute([$hash, $hash, (int) $user['id']]);
        } elseif ($existingHash !== '') {
            $update->execute([$existingHash, $existingHash, (int) $user['id']]);
        }
    }
    $pdo->prepare('ALTER TABLE `user` MODIFY password VARCHAR(255) NULL')->execute();
}

function seed(PDO $pdo): void
{
    $user = $pdo->prepare('INSERT IGNORE INTO `user` (name,email,password_hash,rol) VALUES (?,?,?,?)');
    $user->execute(['Sophie de Vries', 'sophie@example.nl', password_hash('bibliotheek', PASSWORD_DEFAULT), 'Lid']);
    $user->execute(['Alex van Dijk', 'medewerker@library.nl', password_hash('admin123', PASSWORD_DEFAULT), 'Medewerker']);
    $count = $pdo->prepare('SELECT COUNT(*) FROM books');
    $count->execute();
    if ((int) $count->fetchColumn() === 0) {
        $book = $pdo->prepare('INSERT INTO books (title,author,category,year,status,description) VALUES (?,?,?,?,?,?)');
        foreach ([
            ['De avond is ongemak', 'Marieke Lucas Rijneveld', 'Literatuur', 2018, 'Beschikbaar', 'Een indringende roman over verlies, geloof en familie.'],
            ['Dune', 'Frank Herbert', 'Sciencefiction', 1965, 'Uitgeleend', 'Een episch verhaal over macht en overleven op Arrakis.'],
            ['De meeste mensen deugen', 'Rutger Bregman', 'Non-fictie', 2019, 'Beschikbaar', 'Een optimistische kijk op onze menselijke geschiedenis.'],
            ['Kruistocht in spijkerbroek', 'Thea Beckman', 'Jeugd', 1973, 'Beschikbaar', 'Dolf belandt tijdens een tijdreis in een middeleeuwse kinderkruistocht.'],
        ] as $row) {
            $book->execute($row);
        }
    }
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
function redirect(string $page = 'index.php'): never
{
    header('Location: ' . $page);
    exit;
}
function require_login(?string $role = null): void
{
    if (!isset($_SESSION['user']) || ($role !== null && $_SESSION['user']['role'] !== $role)) {
        http_response_code(403);
        exit('Geen toegang.');
    }
}
