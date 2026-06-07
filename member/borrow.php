<?php
$pageTitle = 'Ajukan Peminjaman';
require_once __DIR__ . '/../includes/member_header.php';

$selectedBook = (int) ($_GET['book_id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookId = (int) ($_POST['book_id'] ?? 0);
    $userId = current_user()['id'];

    $bookStmt = $pdo->prepare('select available_stock from books where id = :id');
    $bookStmt->execute(['id' => $bookId]);
    $book = $bookStmt->fetch();

    if ($book && $book['available_stock'] > 0) {
        $stmt = $pdo->prepare("
            insert into loans (user_id, book_id, loan_date, due_date, status)
            values (:user_id, :book_id, current_date, current_date + interval '7 days', 'pending')
        ");
        $stmt->execute(['user_id' => $userId, 'book_id' => $bookId]);
        flash('success', 'Pengajuan peminjaman dikirim. Tunggu konfirmasi admin.');
        header('Location: ' . url('member/dashboard.php'));
        exit;
    }

    flash('error', 'Buku tidak tersedia.');
}

$books = $pdo->query("
    select b.*, c.name as category
    from books b
    left join categories c on c.id = b.category_id
    where b.available_stock > 0
    order by b.title
")->fetchAll();
?>

<section class="page-title">
    <div>
        <p class="eyebrow">Katalog Tersedia</p>
        <h1>Ajukan Peminjaman Buku</h1>
        <p class="muted">Pilih buku yang tersedia, lalu tunggu konfirmasi dari admin.</p>
    </div>
</section>
<section class="panel">
    <div class="panel-head">
        <div>
            <p class="eyebrow">Form</p>
            <h2>Pengajuan Pinjam</h2>
        </div>
        <span class="badge"><?= count($books) ?> buku tersedia</span>
    </div>
    <form method="post" class="form-grid">
        <label>Pilih Buku
            <select name="book_id" required>
                <option value="">Pilih buku tersedia</option>
                <?php foreach ($books as $book): ?>
                    <option value="<?= $book['id'] ?>" <?= $selectedBook === (int) $book['id'] ? 'selected' : '' ?>>
                        <?= e($book['title']) ?> - <?= e($book['author']) ?> (<?= e($book['category'] ?? 'Umum') ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <button class="btn" type="submit">Ajukan Pinjam</button>
    </form>
</section>

<div class="section-head">
    <div>
        <p class="eyebrow">Pilihan</p>
        <h2>Buku Siap Dipinjam</h2>
    </div>
</div>
<div class="book-grid compact member-book-grid">
    <?php foreach ($books as $book): ?>
        <article class="book-card">
            <img src="<?= e($book['cover'] ? url($book['cover']) : url('assets/css/placeholder-cover.svg')) ?>" alt="">
            <div class="book-body">
                <span class="badge"><?= e($book['category'] ?? 'Umum') ?></span>
                <h3><?= e($book['title']) ?></h3>
                <p><?= e($book['author']) ?></p>
                <a class="btn btn-small btn-outline" href="<?= url('member/borrow.php?book_id=' . $book['id']) ?>">Pilih</a>
            </div>
        </article>
    <?php endforeach; ?>
    <?php if (!$books): ?>
        <div class="empty-state neutral">Belum ada buku tersedia untuk dipinjam.</div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/member_footer.php'; ?>
