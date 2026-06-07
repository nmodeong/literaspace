<?php
require_once __DIR__ . '/../includes/helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');

    if ($name && $email && strlen($password) >= 6) {
        try {
            $stmt = $pdo->prepare('
                insert into users (name, email, password, phone, address, role)
                values (:name, :email, :password, :phone, :address, :role)
            ');
            $stmt->execute([
                'name' => $name,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'phone' => $phone,
                'address' => $address,
                'role' => 'member',
            ]);
            flash('success', 'Registrasi berhasil. Silakan login.');
            header('Location: ' . url('auth/login.php'));
            exit;
        } catch (PDOException $e) {
            flash('error', 'Email sudah digunakan.');
        }
    } else {
        flash('error', 'Nama, email, dan password minimal 6 karakter wajib diisi.');
    }
}

$pageTitle = 'Registrasi Anggota';
$bodyClass = 'auth-page';
require_once __DIR__ . '/../includes/public_header.php';
?>

<section class="auth-layout register-layout">
    <div class="auth-visual">
        <span class="eyebrow">Keanggotaan</span>
        <h1>Daftar sekali, akses katalog dan ajukan pinjaman kapan saja.</h1>
        <p>Profil anggota membantu admin memproses peminjaman dengan lebih cepat dan tertata.</p>
        <div class="auth-pills">
            <span>Gratis daftar</span>
            <span>Profil personal</span>
            <span>Riwayat pinjam</span>
        </div>
    </div>
    <div class="auth-card wide modern-auth">
        <span class="eyebrow">Daftar</span>
        <h1>Registrasi Anggota</h1>
        <p class="muted">Isi data utama untuk membuat akun anggota perpustakaan.</p>
        <form method="post" class="form-grid two">
            <label>Nama Lengkap
                <input type="text" name="name" placeholder="Nama lengkap" required>
            </label>
            <label>Email
                <input type="email" name="email" placeholder="nama@email.com" required>
            </label>
            <label>Password
                <input type="password" name="password" minlength="6" placeholder="Minimal 6 karakter" required>
            </label>
            <label>Telepon
                <input type="text" name="phone" placeholder="Nomor telepon">
            </label>
            <label class="span-2">Alamat
                <textarea name="address" rows="4" placeholder="Alamat tempat tinggal"></textarea>
            </label>
            <button class="btn" type="submit">Daftar</button>
        </form>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/public_footer.php'; ?>
