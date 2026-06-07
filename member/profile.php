<?php
$pageTitle = 'Edit Profil';
require_once __DIR__ . '/../includes/member_header.php';

$userId = current_user()['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $photo = upload_image('photo', 'profiles');
    $data = [
        'id' => $userId,
        'name' => trim($_POST['name'] ?? ''),
        'phone' => trim($_POST['phone'] ?? ''),
        'address' => trim($_POST['address'] ?? ''),
    ];

    $current = $pdo->prepare('select photo from users where id = :id');
    $current->execute(['id' => $userId]);
    $old = $current->fetch();
    $data['photo'] = $photo ?: ($old['photo'] ?? null);

    $stmt = $pdo->prepare('update users set name = :name, phone = :phone, address = :address, photo = :photo where id = :id');
    $stmt->execute($data);
    $_SESSION['user']['name'] = $data['name'];
    flash('success', 'Profil berhasil diperbarui.');
    header('Location: ' . url('member/profile.php'));
    exit;
}

$stmt = $pdo->prepare('select * from users where id = :id');
$stmt->execute(['id' => $userId]);
$user = $stmt->fetch();
?>

<section class="page-title">
    <div>
        <p class="eyebrow">Akun</p>
        <h1>Edit Profil</h1>
        <p class="muted">Lengkapi identitas agar proses peminjaman lebih mudah diverifikasi.</p>
    </div>
</section>
<section class="panel profile-panel modern-profile">
    <div class="profile-summary">
        <img class="avatar" src="<?= e($user['photo'] ? url($user['photo']) : url('assets/css/placeholder-cover.svg')) ?>" alt="Foto profil">
        <h2><?= e($user['name']) ?></h2>
        <p class="muted"><?= e($user['email']) ?></p>
    </div>
    <form method="post" enctype="multipart/form-data" class="form-grid">
        <div class="panel-head form-heading">
            <div>
                <p class="eyebrow">Informasi</p>
                <h2>Data Anggota</h2>
            </div>
        </div>
        <label>Nama
            <input type="text" name="name" value="<?= e($user['name']) ?>" required>
        </label>
        <label>Telepon
            <input type="text" name="phone" value="<?= e($user['phone']) ?>">
        </label>
        <label>Alamat
            <textarea name="address" rows="4"><?= e($user['address']) ?></textarea>
        </label>
        <label>Foto Profil
            <input type="file" name="photo" accept="image/*">
        </label>
        <button class="btn" type="submit">Simpan Profil</button>
    </form>
</section>

<?php require_once __DIR__ . '/../includes/member_footer.php'; ?>
