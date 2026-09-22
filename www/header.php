<?php
require_once __DIR__ . '/db.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
$user = $_SESSION['user'] ?? null;
$title = $title ?? 'Bladwijzer';
?><!doctype html>
<html lang="nl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?= e($title) ?> | Bladwijzer</title>
    <style>
        :root {
            --ink: #18231f;
            --muted: #66736e;
            --paper: #f5f3ec;
            --card: #fffdf8;
            --line: #dce2d9;
            --green: #176b54;
            --mint: #dceee5;
            --coral: #e86b4c
        }

        * {
            box-sizing: border-box
        }

        body {
            margin: 0;
            background: var(--paper);
            color: var(--ink);
            font: 16px/1.5 Georgia, serif
        }

        .wrap {
            max-width: 1120px;
            margin: auto;
            padding: 0 24px
        }

        header {
            background: var(--card);
            border-bottom: 1px solid var(--line)
        }

        .nav {
            min-height: 76px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px
        }

        .logo {
            color: var(--green);
            font: bold 28px Georgia;
            text-decoration: none
        }

        .logo b {
            color: var(--coral)
        }

        nav {
            display: flex;
            align-items: center;
            gap: 18px;
            font: 14px Arial
        }

        nav a {
            color: var(--ink);
            text-decoration: none
        }

        .pill {
            background: var(--mint);
            padding: 8px 12px;
            border-radius: 20px;
            color: var(--green)
        }

        h1 {
            font-size: clamp(42px, 6vw, 72px);
            line-height: .98;
            letter-spacing: -3px;
            margin: 14px 0 20px
        }

        h2 {
            font-size: 30px;
            margin: 0 0 12px
        }

        .eyebrow {
            color: var(--coral);
            text-transform: uppercase;
            letter-spacing: 2px;
            font: bold 12px Arial
        }

        .button,
        button {
            border: 0;
            border-radius: 4px;
            background: var(--green);
            color: #fff;
            padding: 12px 17px;
            font: 600 14px Arial;
            cursor: pointer;
            text-decoration: none;
            display: inline-block
        }

        .alt {
            background: var(--mint);
            color: var(--green)
        }

        input,
        select {
            border: 1px solid var(--line);
            border-radius: 4px;
            background: var(--card);
            padding: 12px 14px;
            font: 15px Arial;
            color: var(--ink)
        }

        .panel,
        .detail {
            background: var(--card);
            border: 1px solid var(--line);
            padding: 26px
        }

        .field {
            display: grid;
            gap: 6px;
            margin: 16px 0
        }

        label {
            font: 600 13px Arial
        }

        .meta {
            color: var(--muted);
            font: 13px Arial
        }

        .notice,
        .error {
            padding: 12px 15px;
            margin: 18px 0;
            font: 13px Arial
        }

        .notice {
            background: var(--mint);
            color: var(--green)
        }

        .error {
            background: #fbe3dd;
            color: #963c27
        }

        footer {
            border-top: 1px solid var(--line);
            padding: 25px 0;
            color: var(--muted);
            font: 13px Arial
        }

        @media(max-width:760px) {
            .wrap {
                padding: 0 18px
            }

            .nav {
                padding: 20px 0;
                align-items: flex-start;
                flex-direction: column
            }

            nav {
                flex-wrap: wrap
            }

            .detail {
                margin: 28px 0
            }
        }
    </style>
</head>

<body>
    <header>
        <div class="wrap nav"><a class="logo" href="index.php">blad<b>wijzer</b></a>
            <nav><a href="index.php">Collectie</a><?php if ($user && $user['role'] === 'Medewerker'): ?><a href="admin.php">Beheer</a><?php endif; ?><?php if ($user): ?><a class="pill" href="profile.php"><?= e($user['name']) ?></a><form method="post" action="logout.php"><button type="submit">Uitloggen</button></form><?php else: ?><a href="login.php">Inloggen</a><a href="register.php">Registreren</a><?php endif; ?></nav>
        </div>
    </header>
    <main class="wrap">
        <?php if (!empty($message)): ?>
            <div class="notice"><?= e($message) ?></div><?php endif; ?><?php if (!empty($error)): ?>
            <div class="error"><?= e($error) ?></div><?php endif; ?>