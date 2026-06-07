<?php
$pageTitle = 'Dashboard Anggota';
require_once __DIR__ . '/../includes/member_header.php';

$userId = current_user()['id'];
$stmt = $pdo->prepare("
    select l.*, b.title, b.author, b.cover
    from loans l
    join books b on b.id = l.book_id
    where l.user_id = :user_id
    order by l.created_at desc
");
$stmt->execute(['user_id' => $userId]);
$loans = $stmt->fetchAll();

$activeCount = 0;
$fineTotal = 0;
foreach ($loans as $loan) {
    if (in_array($loan['status'], ['pending', 'approved', 'borrowed'], true)) {
        $activeCount++;
        $fineTotal += calculate_fine($loan['due_date'], $loan['return_date']);
    } else {
        $fineTotal += (int) $loan['fine'];
    }
}
?>

<section class="page-title">
    <div>
        <p class="eyebrow">Dashboard Anggota</p>
        <h1>Halo, <?= e(current_user()['name']) ?></h1>
        <p class="muted">Pantau pinjaman aktif, riwayat baca, dan denda dalam satu halaman.</p>
    </div>
    <a class="btn" href="<?= url('member/borrow.php') ?>">Ajukan Pinjam</a>
</section>

<div class="stats-grid">
    <div class="stat-card"><span>Total Riwayat</span><strong><?= count($loans) ?></strong><small>Semua transaksi</small></div>
    <div class="stat-card"><span>Pinjaman Aktif</span><strong><?= $activeCount ?></strong><small>Sedang berjalan</small></div>
    <div class="stat-card danger"><span>Total Denda</span><strong>Rp<?= number_format($fineTotal, 0, ',', '.') ?></strong><small>Akumulasi tercatat</small></div>
</div>

<section class="panel data-panel">
    <div class="panel-head">
        <div>
            <p class="eyebrow">Aktivitas</p>
            <h2>Riwayat Peminjaman</h2>
        </div>
        <span class="badge"><?= count($loans) ?> transaksi</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Buku</th><th>Tanggal</th><th>Jatuh Tempo</th><th>Status</th><th>Denda</th></tr></thead>
            <tbody>
                <?php foreach ($loans as $loan): ?>
                    <tr>
                        <td><strong><?= e($loan['title']) ?></strong><br><span class="muted"><?= e($loan['author']) ?></span></td>
                        <td><?= e($loan['loan_date']) ?></td>
                        <td><?= e($loan['due_date']) ?></td>
                        <td><span class="badge"><?= e($loan['status']) ?></span></td>
                        <td>Rp<?= number_format($loan['status'] === 'returned' ? $loan['fine'] : calculate_fine($loan['due_date']), 0, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$loans): ?>
                    <tr><td colspan="5">Belum ada riwayat peminjaman.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/member_footer.php'; ?>
