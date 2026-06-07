<?php
$pageTitle = 'Katalog Buku';
$bodyClass = 'home-page';
require_once __DIR__ . '/includes/public_header.php';

$q = trim($_GET['q'] ?? '');
$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 8;
$offset = ($page - 1) * $perPage;

$where = '';
$params = [];
if ($q !== '') {
    $where = "where lower(b.title) like lower(:q) or lower(b.author) like lower(:q) or lower(c.name) like lower(:q)";
    $params['q'] = '%' . $q . '%';
}

$countStmt = $pdo->prepare("select count(*) from books b left join categories c on c.id = b.category_id $where");
$countStmt->execute($params);
$total = (int) $countStmt->fetchColumn();
$memberTotal = (int) $pdo->query("select count(*) from users where role = 'member'")->fetchColumn();
$pagination = paginate($total, $page, $perPage);

$stmt = $pdo->prepare("
    select b.*, c.name as category
    from books b
    left join categories c on c.id = b.category_id
    $where
    order by b.created_at desc
    limit :limit offset :offset
");
foreach ($params as $key => $value) {
    $stmt->bindValue(':' . $key, $value);
}
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$books = $stmt->fetchAll();
?>

<section class="library-hero">
    <div class="hero-shade"></div>
    <div class="hero-inner">
    
        <h1>Jelajahi dunia lewat halaman</h1>
        <p>Temukan koleksi buku, katalog bacaan, dan sumber belajar yang tersimpan rapi dalam satu sistem perpustakaan modern.</p>

        <form class="hero-search live-search-form" method="get" action="<?= url('index.php') ?>" data-search-endpoint="<?= url('search_suggestions.php') ?>">
            <input type="text" name="q" value="<?= e($q) ?>" placeholder="Cari judul buku, pengarang, atau kategori..." autocomplete="off" aria-label="Cari buku">
            <button class="search-icon" type="submit" aria-label="Cari"></button>
            <button class="btn" type="submit">Cari</button>
            <div class="live-search-results" aria-live="polite"></div>
        </form>

        <div class="hero-stats">
            <span><strong><?= number_format($total) ?>+</strong> Buku</span>
            <span><strong><?= number_format($memberTotal) ?>+</strong> Anggota</span>
            <span><strong>24/7</strong> Katalog Online</span>
        </div>
    </div>
</section>
<!-- KOLEKSI SECTION -->
<section class="featured-collection" style="margin-top: -40px; position: relative; z-index: 5;">
    <div class="container">
        <div class="collection-card">
            <div class="collection-title" style="text-align: center; margin-bottom: 40px;">
                <p class="eyebrow">KOLEKSI</p>
                <h2 style="font-size: 2.8rem; margin: 16px 0;">Koleksi Buku Terpopuler</h2>                <span><?= number_format($total) ?> buku ditemukan</span>
            </div>

        <div class="book-grid showcase">
            <?php foreach ($books as $book): ?>
                <article class="book-card">
                    <a href="<?= url('book.php?id=' . $book['id']) ?>">
                        <img src="<?= e($book['cover'] ? url($book['cover']) : url('assets/css/placeholder-cover.svg')) ?>" alt="Cover <?= e($book['title']) ?>">
                    </a>
                    <div class="book-body">
                        <span class="badge"><?= e($book['category'] ?? 'Umum') ?></span>
                        <h3><?= e($book['title']) ?></h3>
                        <p class="author-line"><?= e($book['author']) ?></p>
                        <span class="status <?= $book['available_stock'] > 0 ? 'available' : 'empty' ?>">
                            <?= $book['available_stock'] > 0 ? 'Tersedia' : 'Tidak tersedia' ?>
                        </span>
                        <a class="btn btn-outline" href="<?= url('book.php?id=' . $book['id']) ?>">Detail Buku</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <?php if (!$books): ?>
            <div class="empty-state">Belum ada buku yang cocok dengan pencarian.</div>
        <?php endif; ?>

        <div class="pagination">
            <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                <a class="<?= $i === $page ? 'active' : '' ?>" href="?q=<?= urlencode($q) ?>&page=<?= $i ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
    </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/public_footer.php'; ?>
