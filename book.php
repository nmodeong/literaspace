<?php
$pageTitle = 'Detail Buku';
require_once __DIR__ . '/includes/public_header.php';

$id = (int) ($_GET['id'] ?? 0);
$stmt = $pdo->prepare("
    select b.*, c.name as category
    from books b
    left join categories c on c.id = b.category_id
    where b.id = :id
");
$stmt->execute(['id' => $id]);
$book = $stmt->fetch();

if (!$book) {
    flash('error', 'Buku tidak ditemukan.');
    header('Location: ' . url('index.php'));
    exit;
}
?>

<section class="detail-layout">
<img src="<?= e($book['cover'] ? (str_starts_with($book['cover'], 'http') ? $book['cover'] : url($book['cover'])) : url('assets/css/placeholder-cover.svg')) ?>" alt="Cover <?= e($book['title']) ?>">
    <div class="detail-content">
        <span class="badge"><?= e($book['category'] ?? 'Umum') ?></span>
        <h1><?= e($book['title']) ?></h1>
        <p class="muted">Oleh <?= e($book['author']) ?></p>
        <div class="meta-grid">
            <div><strong>Penerbit</strong><span><?= e($book['publisher'] ?: '-') ?></span></div>
            <div><strong>Tahun</strong><span><?= e($book['year'] ?: '-') ?></span></div>
            <div><strong>ISBN</strong><span><?= e($book['isbn'] ?: '-') ?></span></div>
            <div><strong>Stok</strong><span><?= e($book['available_stock']) ?> / <?= e($book['stock']) ?></span></div>
        </div>
        <p><?= nl2br(e($book['description'] ?: 'Deskripsi belum tersedia.')) ?></p>
        <div class="actions">
            <?php if (is_member()): ?>
                <a class="btn" href="<?= url('member/borrow.php?book_id=' . $book['id']) ?>">Ajukan Peminjaman</a>
            <?php else: ?>
                <a class="btn" href="<?= url('auth/login.php') ?>">Login untuk Meminjam</a>
            <?php endif; ?>
            <a class="btn btn-outline" href="<?= url('index.php') ?>">Kembali</a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/public_footer.php'; ?>
