<?php
$pageTitle = 'Sirkulasi Buku';
require_once __DIR__ . '/../includes/admin_header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = (int) ($_POST['id'] ?? 0);

    if ($action === 'approve') {
        $pdo->beginTransaction();
        $loanStmt = $pdo->prepare('select * from loans where id = :id for update');
        $loanStmt->execute(['id' => $id]);
        $loan = $loanStmt->fetch();

        if (!$loan) {
            $pdo->rollBack();
            flash('error', 'Data peminjaman tidak ditemukan.');
            header('Location: ' . url('admin/loans.php'));
            exit;
        }

        $bookStmt = $pdo->prepare('select available_stock from books where id = :id for update');
        $bookStmt->execute(['id' => $loan['book_id']]);
        $book = $bookStmt->fetch();

        if ($loan && $book && $book['available_stock'] > 0) {
            $pdo->prepare("update loans set status = 'approved', loan_date = current_date, due_date = current_date + interval '7 days' where id = :id")->execute(['id' => $id]);
            $pdo->prepare('update books set available_stock = available_stock - 1 where id = :id')->execute(['id' => $loan['book_id']]);
            $pdo->commit();
            flash('success', 'Peminjaman disetujui.');
        } else {
            $pdo->rollBack();
            flash('error', 'Stok buku tidak tersedia.');
        }
    }

    if ($action === 'return') {
        $loanStmt = $pdo->prepare('select * from loans where id = :id');
        $loanStmt->execute(['id' => $id]);
        $loan = $loanStmt->fetch();
        if ($loan) {
            $fine = calculate_fine($loan['due_date']);
            $pdo->prepare("update loans set status = 'returned', return_date = current_date, fine = :fine where id = :id")->execute(['fine' => $fine, 'id' => $id]);
            $pdo->prepare('update books set available_stock = available_stock + 1 where id = :id')->execute(['id' => $loan['book_id']]);
            flash('success', 'Pengembalian dikonfirmasi. Denda: Rp' . number_format($fine, 0, ',', '.'));
        }
    }

    header('Location: ' . url('admin/loans.php'));
    exit;
}

$loans = $pdo->query("
    select l.*, u.name as member_name, b.title
    from loans l
    join users u on u.id = l.user_id
    join books b on b.id = l.book_id
    order by l.created_at desc
")->fetchAll();

$loanStats = [
    'pending' => 0,
    'active' => 0,
    'returned' => 0,
    'fine' => 0,
];
foreach ($loans as $loan) {
    if ($loan['status'] === 'pending') {
        $loanStats['pending']++;
    }
    if (in_array($loan['status'], ['approved', 'borrowed'], true)) {
        $loanStats['active']++;
        $loanStats['fine'] += calculate_fine($loan['due_date']);
    }
    if ($loan['status'] === 'returned') {
        $loanStats['returned']++;
        $loanStats['fine'] += (int) $loan['fine'];
    }
}
?>

<section class="page-title">
    <div>
        <p class="eyebrow">Operasional</p>
        <h1>Sirkulasi Buku</h1>
        <p class="muted">Kelola persetujuan pinjam, pengembalian, dan denda berjalan.</p>
    </div>
</section>
<div class="stats-grid compact-stats">
    <div class="stat-card"><span>Menunggu</span><strong><?= $loanStats['pending'] ?></strong><small>Butuh konfirmasi admin</small></div>
    <div class="stat-card"><span>Aktif</span><strong><?= $loanStats['active'] ?></strong><small>Sedang dipinjam</small></div>
    <div class="stat-card"><span>Dikembalikan</span><strong><?= $loanStats['returned'] ?></strong><small>Riwayat selesai</small></div>
    <div class="stat-card danger"><span>Estimasi Denda</span><strong>Rp<?= number_format($loanStats['fine'], 0, ',', '.') ?></strong><small>Akumulasi saat ini</small></div>
</div>
<section class="panel">
    <div class="panel-head">
        <div>
            <p class="eyebrow">Antrian</p>
            <h2>Daftar Sirkulasi</h2>
        </div>
        <span class="badge"><?= count($loans) ?> transaksi</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Anggota</th><th>Buku</th><th>Pinjam</th><th>Jatuh Tempo</th><th>Status</th><th>Denda</th><th>Aksi</th></tr></thead>
            <tbody>
                <?php foreach ($loans as $loan): ?>
                    <?php $fine = calculate_fine($loan['due_date'], $loan['return_date']); ?>
                    <tr>
                        <td><?= e($loan['member_name']) ?></td>
                        <td><?= e($loan['title']) ?></td>
                        <td><?= e($loan['loan_date']) ?></td>
                        <td><?= e($loan['due_date']) ?></td>
                        <td><span class="badge"><?= e($loan['status']) ?></span></td>
                        <td>Rp<?= number_format($loan['status'] === 'returned' ? $loan['fine'] : $fine, 0, ',', '.') ?></td>
                        <td class="actions">
                            <?php if ($loan['status'] === 'pending'): ?>
                                <form method="post"><input type="hidden" name="action" value="approve"><input type="hidden" name="id" value="<?= e($loan['id']) ?>"><button class="btn btn-small" type="submit">Setujui</button></form>
                            <?php endif; ?>
                            <?php if (in_array($loan['status'], ['approved', 'borrowed'], true)): ?>
                                <form method="post"><input type="hidden" name="action" value="return"><input type="hidden" name="id" value="<?= e($loan['id']) ?>"><button class="btn btn-small btn-outline" type="submit">Kembalikan</button></form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$loans): ?>
                    <tr><td colspan="7">Belum ada data sirkulasi.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
