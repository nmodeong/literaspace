<?php
require_once __DIR__ . '/../includes/helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('select * from users where email = :email limit 1');
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password']) && $user['is_active']) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
        ];
        header('Location: ' . url($user['role'] === 'admin' ? 'admin/dashboard.php' : 'member/dashboard.php'));
        exit;
    }

    flash('error', 'Email, password, atau status akun tidak valid.');
}

$pageTitle = 'Login';
$bodyClass = 'auth-page';
require_once __DIR__ . '/../includes/public_header.php';
?>

<section class="auth-layout">
    <div class="auth-visual">
        <span class="eyebrow">Ruang Akses</span>
        <h1>Masuk ke perpustakaan digital yang lebih rapi.</h1>
        <p>Kelola pinjaman, pantau riwayat, dan jelajahi katalog dengan pengalaman yang nyaman.</p>
        <div class="auth-pills">
            <span>Katalog online</span>
            <span>Sirkulasi cepat</span>
            <span>Data aman</span>
        </div>
    </div>
    <div class="auth-card modern-auth">
        <span class="eyebrow">Login</span>
        <h1>Selamat datang</h1>
        <p class="muted">Gunakan email dan password yang terdaftar untuk masuk.</p>
        <form method="post" class="form-grid">
            <label>Email
                <input type="email" name="email" placeholder="nama@email.com" required>
            </label>
            <label>Password
                <input type="password" name="password" placeholder="Masukkan password" required>
            </label>
            <button class="btn" type="submit">Masuk</button>
        </form>
        <p class="muted">Belum punya akun? <a href="<?= url('auth/register.php') ?>">Daftar anggota</a></p>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/public_footer.php'; ?>
