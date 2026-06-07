<?php
require_once __DIR__ . '/helpers.php';
require_login('admin');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Admin Perpustakaan') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= url('assets/css/style.css') ?>?v=3">
</head>
<body class="admin-shell">
<aside class="sidebar">
    <?php $currentAdminPage = basename($_SERVER['PHP_SELF']); ?>
    <a class="brand admin-brand" href="<?= url('admin/dashboard.php') ?>">
        <span class="brand-mark">P</span>
        <span>Admin Library<small>Pusat Operasi Perpustakaan</small></span>
    </a>
    <nav class="sidebar-nav">
        <a class="<?= $currentAdminPage === 'dashboard.php' ? 'active' : '' ?>" href="<?= url('admin/dashboard.php') ?>">Dashboard</a>
        <a class="<?= $currentAdminPage === 'books.php' ? 'active' : '' ?>" href="<?= url('admin/books.php') ?>">Kelola Buku</a>
        <a class="<?= $currentAdminPage === 'members.php' ? 'active' : '' ?>" href="<?= url('admin/members.php') ?>">Keanggotaan</a>
        <a class="<?= $currentAdminPage === 'loans.php' ? 'active' : '' ?>" href="<?= url('admin/loans.php') ?>">Sirkulasi Buku</a>
        <a class="<?= $currentAdminPage === 'content.php' ? 'active' : '' ?>" href="<?= url('admin/content.php') ?>">Kelola Konten Web</a>
        <a class="<?= $currentAdminPage === 'reports.php' ? 'active' : '' ?>" href="<?= url('admin/reports.php') ?>">Laporan dan Analitik</a>
    </nav>
    <div class="sidebar-footer">
        <a href="<?= url('index.php') ?>">Lihat Website</a>
        <a href="<?= url('auth/logout.php') ?>">Logout</a>
    </div>
</aside>
<main class="admin-content">
<?php if ($msg = flash('success')): ?><div class="alert success"><?= e($msg) ?></div><?php endif; ?>
<?php if ($msg = flash('error')): ?><div class="alert error"><?= e($msg) ?></div><?php endif; ?>
