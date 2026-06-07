<?php
$pageTitle = 'Kelola Konten Web';
require_once __DIR__ . '/../includes/admin_header.php';

$contentStats = [
    'books' => (int) $pdo->query('select count(*) from books')->fetchColumn(),
    'categories' => (int) $pdo->query('select count(*) from categories')->fetchColumn(),
    'covers' => (int) $pdo->query("select count(*) from books where cover is not null and cover <> ''")->fetchColumn(),
    'members' => (int) $pdo->query("select count(*) from users where role = 'member'")->fetchColumn(),
];

$latestBooks = $pdo->query("
    select b.*, coalesce(c.name, 'Umum') as category
    from books b
    left join categories c on c.id = b.category_id
    order by b.created_at desc
    limit 4
")->fetchAll();
?>

<section class="page-title">
    <div>
        <p class="eyebrow">Frontend</p>
        <h1>Kelola Konten Web</h1>
        <p class="muted">Pantau konten yang tampil di katalog publik dan akses cepat ke halaman utama.</p>
    </div>
    <div class="actions">
        <a class="btn btn-outline" href="<?= url('index.php') ?>">Lihat Katalog</a>
        <a class="btn" href="<?= url('about.php') ?>">Lihat Tentang Kami</a>
    </div>
</section>

<div class="stats-grid compact-stats">
    <div class="stat-card"><span>Katalog</span><strong><?= $contentStats['books'] ?></strong><small>Buku tampil di website</small></div>
    <div class="stat-card"><span>Kategori</span><strong><?= $contentStats['categories'] ?></strong><small>Filter koleksi publik</small></div>
    <div class="stat-card"><span>Cover</span><strong><?= $contentStats['covers'] ?></strong><small>Buku dengan gambar</small></div>
    <div class="stat-card"><span>Anggota</span><strong><?= $contentStats['members'] ?></strong><small>Komunitas pembaca</small></div>
</div>

<div class="content-grid">
    <section class="panel web-preview">
        <div class="panel-head">
            <div>
                <p class="eyebrow">Preview</p>
                <h2>Halaman Katalog</h2>
            </div>
            <span class="badge">Publik</span>
        </div>
        <div class="preview-hero">
            <span>PERPUSTAKAAN MODEREN</span>
            <h3>Jelajahi dunia lewat halaman</h3>
            <p>Katalog buku, pencarian, statistik koleksi, dan detail peminjaman untuk anggota.</p>
        </div>
        <div class="quick-links">
            <a href="<?= url('admin/books.php') ?>">Kelola koleksi</a>
            <a href="<?= url('admin/members.php') ?>">Pantau anggota</a>
            <a href="<?= url('admin/reports.php') ?>">Lihat analitik</a>
        </div>
    </section>

    <section class="panel">
        <div class="panel-head">
            <div>
                <p class="eyebrow">Aset</p>
                <h2>Status Konten</h2>
            </div>
        </div>
        <div class="progress-list">
            <?php $coverRatio = $contentStats['books'] > 0 ? round(($contentStats['covers'] / $contentStats['books']) * 100) : 0; ?>
            <div class="progress-row">
                <div><span>Kelengkapan cover</span><strong><?= $coverRatio ?>%</strong></div>
                <i><b style="width: <?= $coverRatio ?>%"></b></i>
            </div>
            <div class="progress-row">
                <div><span>Kategori aktif</span><strong><?= $contentStats['categories'] ?> kategori</strong></div>
                <i><b style="width: <?= min(100, $contentStats['categories'] * 18) ?>%"></b></i>
            </div>
            <div class="progress-row">
                <div><span>Kesiapan katalog</span><strong><?= $contentStats['books'] > 0 ? 'Siap' : 'Belum siap' ?></strong></div>
                <i><b style="width: <?= $contentStats['books'] > 0 ? 100 : 12 ?>%"></b></i>
            </div>
        </div>
    </section>
</div>

<section class="panel">
    <div class="panel-head">
        <div>
            <p class="eyebrow">Konten terbaru</p>
            <h2>Buku Terakhir Ditambahkan</h2>
        </div>
        <a class="btn btn-small btn-outline" href="<?= url('admin/books.php') ?>">Kelola Buku</a>
    </div>
    <div class="book-grid compact admin-books-preview">
        <?php foreach ($latestBooks as $book): ?>
            <article class="book-card">
                <img src="<?= e($book['cover'] ? url($book['cover']) : url('assets/css/placeholder-cover.svg')) ?>" alt="Cover <?= e($book['title']) ?>">
                <div class="book-body">
                    <span class="badge"><?= e($book['category']) ?></span>
                    <h3><?= e($book['title']) ?></h3>
                    <p><?= e($book['author']) ?></p>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
    <?php if (!$latestBooks): ?><div class="empty-state neutral">Belum ada buku yang bisa dipreview.</div><?php endif; ?>
</section>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
