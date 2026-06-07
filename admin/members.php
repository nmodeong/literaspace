<?php
$pageTitle = 'Keanggotaan';
require_once __DIR__ . '/../includes/admin_header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    
    // Perbaikan boolean
    $isActive = isset($_POST['is_active']) && $_POST['is_active'] === '1';

    if ($id > 0) {
        $stmt = $pdo->prepare("UPDATE users SET is_active = :is_active WHERE id = :id AND role = 'member'");
        
        // Perbaikan utama: Gunakan bindValue dengan PDO::PARAM_BOOL
        $stmt->bindValue(':is_active', $isActive, PDO::PARAM_BOOL);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        
        $stmt->execute();
        
        flash('success', 'Status anggota berhasil diperbarui.');
    } else {
        flash('error', 'ID anggota tidak valid.');
    }
    
    header('Location: ' . url('admin/members.php'));
    exit;
}

// ====================== BAGIAN BAWAH TETAP SAMA ======================
$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 10;
$offset = ($page - 1) * $perPage;
$total = (int) $pdo->query("select count(*) from users where role = 'member'")->fetchColumn();
$activeTotal = (int) $pdo->query("select count(*) from users where role = 'member' and is_active = true")->fetchColumn();
$inactiveTotal = max(0, $total - $activeTotal);
$pagination = paginate($total, $page, $perPage);

$stmt = $pdo->prepare("select * from users where role = 'member' order by created_at desc limit :limit offset :offset");
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$members = $stmt->fetchAll();
?>

<section class="page-title">
    <div>
        <p class="eyebrow">Direktori</p>
        <h1>Keanggotaan</h1>
        <p class="muted">Pantau akun anggota dan status akses peminjaman mereka.</p>
    </div>
</section>
<div class="stats-grid compact-stats">
    <div class="stat-card"><span>Total Anggota</span><strong><?= $total ?></strong><small>Terdaftar sebagai member</small></div>
    <div class="stat-card"><span>Aktif</span><strong><?= $activeTotal ?></strong><small>Dapat login dan meminjam</small></div>
    <div class="stat-card danger"><span>Nonaktif</span><strong><?= $inactiveTotal ?></strong><small>Akses sedang dibatasi</small></div>
</div>
<section class="panel">
    <div class="panel-head">
        <div>
            <p class="eyebrow">Data</p>
            <h2>Daftar Anggota</h2>
        </div>
        <span class="badge"><?= $pagination['total_pages'] ?> halaman</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Nama</th><th>Email</th><th>Telepon</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
                <?php foreach ($members as $member): ?>
                    <tr>
                        <td><?= e($member['name']) ?></td>
                        <td><?= e($member['email']) ?></td>
                        <td><?= e($member['phone'] ?: '-') ?></td>
                        <td>
                            <span class="badge <?= $member['is_active'] ? 'success' : 'danger' ?>">
                                <?= $member['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                            </span>
                        </td>
                        <td>
                            <form method="post">
                                <input type="hidden" name="id" value="<?= e($member['id']) ?>">
                                <input type="hidden" name="is_active" value="<?= $member['is_active'] ? '0' : '1' ?>">
                                <button class="btn btn-small <?= $member['is_active'] ? 'danger' : 'btn-outline' ?>" type="submit">
                                    <?= $member['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="pagination">
        <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
            <a class="<?= $i === $page ? 'active' : '' ?>" href="?page=<?= $i ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
