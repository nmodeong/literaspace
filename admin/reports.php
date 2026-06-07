<?php
$pageTitle = 'Laporan dan Analitik';
require_once __DIR__ . '/../includes/admin_header.php';

$start = $_GET['start'] ?? date('Y-m-01');
$end = $_GET['end'] ?? date('Y-m-d');

$stmt = $pdo->prepare("
    select l.*, u.name as member_name, b.title
    from loans l
    join users u on u.id = l.user_id
    join books b on b.id = l.book_id
    where l.loan_date between :start and :end
    order by l.loan_date desc
");
$stmt->execute(['start' => $start, 'end' => $end]);
$reports = $stmt->fetchAll();

$reportStats = [
    'total' => count($reports),
    'fine' => 0,
    'returned' => 0,
    'active' => 0,
];
$statusBreakdown = [];
$dailyBreakdown = [];
foreach ($reports as $row) {
    $reportStats['fine'] += (int) $row['fine'];
    if ($row['status'] === 'returned') {
        $reportStats['returned']++;
    }
    if (in_array($row['status'], ['pending', 'approved', 'borrowed'], true)) {
        $reportStats['active']++;
    }
    $statusBreakdown[$row['status']] = ($statusBreakdown[$row['status']] ?? 0) + 1;
    $dailyBreakdown[$row['loan_date']] = ($dailyBreakdown[$row['loan_date']] ?? 0) + 1;
}
?>

<section class="page-title">
    <div>
        <p class="eyebrow">Insight</p>
        <h1>Laporan dan Analitik</h1>
        <p class="muted">Filter periode, lihat pola peminjaman, dan ringkas status transaksi.</p>
    </div>
</section>
<div class="stats-grid compact-stats">
    <div class="stat-card"><span>Total Transaksi</span><strong><?= $reportStats['total'] ?></strong><small>Dalam periode filter</small></div>
    <div class="stat-card"><span>Masih Aktif</span><strong><?= $reportStats['active'] ?></strong><small>Pending/approved/borrowed</small></div>
    <div class="stat-card"><span>Dikembalikan</span><strong><?= $reportStats['returned'] ?></strong><small>Selesai diproses</small></div>
    <div class="stat-card danger"><span>Total Denda</span><strong>Rp<?= number_format($reportStats['fine'], 0, ',', '.') ?></strong><small>Denda tercatat</small></div>
</div>

<section class="panel">
    <div class="panel-head">
        <div>
            <p class="eyebrow">Periode</p>
            <h2>Filter Laporan</h2>
        </div>
    </div>
    <form method="get" class="filter-bar">
        <label>Dari <input type="date" name="start" value="<?= e($start) ?>"></label>
        <label>Sampai <input type="date" name="end" value="<?= e($end) ?>"></label>
        <button class="btn" type="submit">Filter</button>
    </form>
</section>

<div class="analytics-grid two-cols">
    <section class="panel chart-panel">
        <div class="panel-head"><div><p class="eyebrow">Tren</p><h2>Transaksi per Tanggal</h2></div></div>
        <div class="bar-chart">
            <?php $maxDaily = max(1, ...array_values($dailyBreakdown ?: [0])); ?>
            <?php foreach ($dailyBreakdown as $date => $count): ?>
                <div class="bar-item">
                    <span style="height: <?= max(8, round(($count / $maxDaily) * 120)) ?>px"></span>
                    <strong><?= $count ?></strong>
                    <small><?= e(date('d M', strtotime($date))) ?></small>
                </div>
            <?php endforeach; ?>
            <?php if (!$dailyBreakdown): ?><div class="empty-state neutral">Tidak ada transaksi pada periode ini.</div><?php endif; ?>
        </div>
    </section>

    <section class="panel chart-panel">
        <div class="panel-head"><div><p class="eyebrow">Status</p><h2>Breakdown Status</h2></div></div>
        <div class="progress-list">
            <?php $maxStatus = max(1, ...array_values($statusBreakdown ?: [0])); ?>
            <?php foreach ($statusBreakdown as $status => $count): ?>
                <div class="progress-row">
                    <div><span><?= e(ucfirst($status)) ?></span><strong><?= $count ?> transaksi</strong></div>
                    <i><b style="width: <?= round(($count / $maxStatus) * 100) ?>%"></b></i>
                </div>
            <?php endforeach; ?>
            <?php if (!$statusBreakdown): ?><div class="empty-state neutral">Status belum tersedia.</div><?php endif; ?>
        </div>
    </section>
</div>

<section class="panel data-panel">
    <div class="panel-head">
        <div>
            <p class="eyebrow">Detail</p>
            <h2>Tabel Laporan</h2>
        </div>
        <span class="badge"><?= e($start) ?> - <?= e($end) ?></span>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Tanggal</th><th>Anggota</th><th>Buku</th><th>Status</th><th>Denda</th></tr></thead>
            <tbody>
                <?php foreach ($reports as $row): ?>
                    <tr>
                        <td><?= e($row['loan_date']) ?></td>
                        <td><?= e($row['member_name']) ?></td>
                        <td><?= e($row['title']) ?></td>
                        <td><span class="badge"><?= e($row['status']) ?></span></td>
                        <td>Rp<?= number_format($row['fine'], 0, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$reports): ?>
                    <tr><td colspan="5">Tidak ada laporan pada periode ini.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
