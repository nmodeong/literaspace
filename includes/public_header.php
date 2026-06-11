<?php require_once __DIR__ . '/helpers.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Perpustakaan') ?></title>
    <link rel="icon" type="image/x-icon" href="/perpustakaan/favicon.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= url('assets/css/style.css') ?>?v=3">
</head>
<body class="<?= e($bodyClass ?? '') ?>">
    <?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<nav class="navbar <?= ($bodyClass ?? '') === 'home-page' ? 'navbar-hero' : '' ?>">
<a class="brand" href="<?= url('index.php') ?>">
    <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:inline-block; vertical-align:middle; margin-right:8px;">
        <!-- Halaman kiri -->
        <path d="M16 7 C16 7 10 8 5 11 L5 26 C10 23 16 24 16 24 Z" fill="currentColor" opacity="0.85"/>
        <!-- Halaman kanan -->
        <path d="M16 7 C16 7 22 8 27 11 L27 26 C22 23 16 24 16 24 Z" fill="currentColor" opacity="0.6"/>
        <!-- Garis tengah (lipatan) -->
        <line x1="16" y1="7" x2="16" y2="24" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
        <!-- Garis halaman kiri -->
        <line x1="9" y1="13" x2="14" y2="12.5" stroke="currentColor" stroke-width="1" stroke-linecap="round" opacity="0.5"/>
        <line x1="9" y1="16" x2="14" y2="15.5" stroke="currentColor" stroke-width="1" stroke-linecap="round" opacity="0.5"/>
        <line x1="9" y1="19" x2="14" y2="18.5" stroke="currentColor" stroke-width="1" stroke-linecap="round" opacity="0.5"/>
        <!-- Garis halaman kanan -->
        <line x1="23" y1="13" x2="18" y2="12.5" stroke="currentColor" stroke-width="1" stroke-linecap="round" opacity="0.5"/>
        <line x1="23" y1="16" x2="18" y2="15.5" stroke="currentColor" stroke-width="1" stroke-linecap="round" opacity="0.5"/>
        <line x1="23" y1="19" x2="18" y2="18.5" stroke="currentColor" stroke-width="1" stroke-linecap="round" opacity="0.5"/>
    </svg>
    PERPUSTAKAAN MODERN
</a>    <button class="nav-toggle" onclick="document.body.classList.toggle('nav-open')">Menu</button>
    <div class="nav-links">
       <a href="<?= url('index.php') ?>" <?= $currentPage === 'index.php' ? 'class="active"' : '' ?>>Katalog</a>
<a href="<?= url('about.php') ?>" <?= $currentPage === 'about.php' ? 'class="active"' : '' ?>>Tentang Kami</a>
        <?php if (is_admin()): ?>
            <a href="<?= url('admin/dashboard.php') ?>">Admin</a>
        <?php elseif (is_member()): ?>
            <a href="<?= url('member/dashboard.php') ?>">Ruang Baca Saya</a>
        <?php endif; ?>
        <?php if (current_user()): ?>
            <a href="<?= url('auth/logout.php') ?>">Logout</a>
        <?php else: ?>
            <a href="<?= url('auth/login.php') ?>">Login</a>
            <a class="btn btn-small" href="<?= url('auth/register.php') ?>">Daftar</a>
        <?php endif; ?>
    </div>
</nav>
<main class="container">
<?php if ($msg = flash('success')): ?><div class="alert success"><?= e($msg) ?></div><?php endif; ?>
<?php if ($msg = flash('error')): ?><div class="alert error"><?= e($msg) ?></div><?php endif; ?>
