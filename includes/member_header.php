<?php
require_once __DIR__ . '/helpers.php';
require_login('member');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Member Area') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= url('assets/css/style.css') ?>?v=3">
</head>
<body class="member-shell">
<nav class="navbar">
    <?php $currentMemberPage = basename($_SERVER['PHP_SELF']); ?>
    <a class="brand" href="<?= url('member/dashboard.php') ?>">Ruang Baca Saya</a>
    <button class="nav-toggle" onclick="document.body.classList.toggle('nav-open')">Menu</button>
    <div class="nav-links">
        <a href="<?= url('member/dashboard.php') ?>" <?= $currentMemberPage === 'dashboard.php' ? 'class="active"' : '' ?>>Dashboard</a>
        <a href="<?= url('member/borrow.php') ?>" <?= $currentMemberPage === 'borrow.php' ? 'class="active"' : '' ?>>Ajukan Pinjam</a>
        <a href="<?= url('member/profile.php') ?>" <?= $currentMemberPage === 'profile.php' ? 'class="active"' : '' ?>>Profil</a>
        <a href="<?= url('index.php') ?>">Katalog</a>
        <a href="<?= url('auth/logout.php') ?>">Logout</a>
    </div>
</nav>
<main class="container">
<?php if ($msg = flash('success')): ?><div class="alert success"><?= e($msg) ?></div><?php endif; ?>
<?php if ($msg = flash('error')): ?><div class="alert error"><?= e($msg) ?></div><?php endif; ?>
