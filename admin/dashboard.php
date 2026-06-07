<?php
$pageTitle = 'Dashboard Admin';
require_once __DIR__ . '/../includes/admin_header.php';

$stats = [
    'books' => (int) $pdo->query('select count(*) from books')->fetchColumn(),
    'members' => (int) $pdo->query("select count(*) from users where role = 'member'")->fetchColumn(),
    'active_loans' => (int) $pdo->query("select count(*) from loans where status in ('pending','approved','borrowed')")->fetchColumn(),
    'late_books' => (int) $pdo->query("select count(*) from loans where status in ('approved','borrowed') and due_date < current_date")->fetchColumn(),
    'available_stock' => (int) $pdo->query('select coalesce(sum(available_stock), 0) from books')->fetchColumn(),
    'total_stock' => (int) $pdo->query('select coalesce(sum(stock), 0) from books')->fetchColumn(),
];

$statusRows = $pdo->query("
    select status, count(*) as total
    from loans
    group by status
    order by total desc
")->fetchAll();

$trendRows = $pdo->query("
    select loan_date, count(*) as total
    from loans
    where loan_date >= current_date - interval '6 days'
    group by loan_date
    order by loan_date
")->fetchAll();

$categoryRows = $pdo->query("
    select coalesce(c.name, 'Umum') as category, count(b.id) as total
    from books b
    left join categories c on c.id = b.category_id
    group by coalesce(c.name, 'Umum')
    order by total desc
    limit 5
")->fetchAll();

$recentLoans = $pdo->query("
    select l.*, u.name as member_name, b.title
    from loans l
    join users u on u.id = l.user_id
    join books b on b.id = l.book_id
    order by l.created_at desc
    limit 8 
")->fetchAll();
?>

<section class="page-title">
    <div>
        <p class="eyebrow">Backend</p>
        <h1>Dashboard Admin</h1>
        <p class="muted">Pantau koleksi, anggota, dan pergerakan sirkulasi dari satu layar.</p>
    </div>
</section>

<div class="stats-grid">
    <div class="stat-card"><span>Total Buku</span><strong><?= $stats['books'] ?></strong><small>Koleksi terdaftar</small></div>
    <div class="stat-card"><span>Anggota</span><strong><?= $stats['members'] ?></strong><small>Akun member aktif/nonaktif</small></div>
    <div class="stat-card"><span>Peminjaman Aktif</span><strong><?= $stats['active_loans'] ?></strong><small>Pending, approved, borrowed</small></div>
    <div class="stat-card danger"><span>Buku Terlambat</span><strong><?= $stats['late_books'] ?></strong><small>Lewat jatuh tempo</small></div>
</div>

<div class="analytics-grid">
    <section class="panel chart-panel">
        <div class="panel-head">
            <div>
                <p class="eyebrow">Visualisasi</p>
                <h2>Status Sirkulasi</h2>
            </div>
        </div>
        <?php if ($statusRows): ?>
            <div class="donut-wrap">
                <?php $statusTotal = max(1, array_sum(array_map(fn($row) => (int) $row['total'], $statusRows))); ?>
                <div class="donut-chart" style="--p: <?= min(100, round(((int) $statusRows[0]['total'] / $statusTotal) * 100)) ?>%;"></div>
                <div class="chart-legend">
                    <?php foreach ($statusRows as $row): ?>
                        <span><i></i><?= e(ucfirst($row['status'])) ?> <strong><?= (int) $row['total'] ?></strong></span>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="empty-state neutral">Belum ada data peminjaman untuk divisualisasikan.</div>
        <?php endif; ?>
    </section>

    <section class="panel chart-panel">
        <div class="panel-head">
            <div>
                <p class="eyebrow">7 Hari</p>
                <h2>Tren Peminjaman</h2>
            </div>
        </div>
        <div class="bar-chart">
            <?php $maxTrend = max(1, ...array_map(fn($row) => (int) $row['total'], $trendRows ?: [['total' => 0]])); ?>
            <?php foreach ($trendRows as $row): ?>
                <div class="bar-item">
                    <span style="height: <?= max(8, round(((int) $row['total'] / $maxTrend) * 120)) ?>px"></span>
                    <strong><?= (int) $row['total'] ?></strong>
                    <small><?= e(date('d M', strtotime($row['loan_date']))) ?></small>
                </div>
            <?php endforeach; ?>
            <?php if (!$trendRows): ?><div class="empty-state neutral">Belum ada tren minggu ini.</div><?php endif; ?>
        </div>
    </section>

    <section class="panel chart-panel">
        <div class="panel-head">
            <div>
                <p class="eyebrow">Koleksi</p>
                <h2>Kategori Teratas</h2>
            </div>
        </div>
        <div class="progress-list">
            <?php $maxCategory = max(1, ...array_map(fn($row) => (int) $row['total'], $categoryRows ?: [['total' => 0]])); ?>
            <?php foreach ($categoryRows as $row): ?>
                <div class="progress-row">
                    <div><span><?= e($row['category']) ?></span><strong><?= (int) $row['total'] ?> buku</strong></div>
                    <i><b style="width: <?= round(((int) $row['total'] / $maxCategory) * 100) ?>%"></b></i>
                </div>
            <?php endforeach; ?>
            <?php if (!$categoryRows): ?><div class="empty-state neutral">Kategori belum tersedia.</div><?php endif; ?>
        </div>
    </section>
</div>

<section class="panel data-panel">
    <div class="panel-head">
        <div>
            <p class="eyebrow">Aktivitas</p>
            <h2>Peminjaman Terbaru</h2>
        </div>
        <span class="badge">Stok tersedia <?= $stats['available_stock'] ?> / <?= $stats['total_stock'] ?></span>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Anggota</th><th>Buku</th><th>Jatuh Tempo</th><th>Status</th><th>Denda</th></tr></thead>
            <tbody>
                <?php foreach ($recentLoans as $loan): ?>
                    <tr>
                        <td><?= e($loan['member_name']) ?></td>
                        <td><?= e($loan['title']) ?></td>
                        <td><?= e($loan['due_date']) ?></td>
                        <td><span class="badge"><?= e($loan['status']) ?></span></td>
                        <td>Rp<?= number_format(calculate_fine($loan['due_date'], $loan['return_date']), 0, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$recentLoans): ?>
                    <tr><td colspan="5">Belum ada peminjaman terbaru.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
