<?php
$pageTitle = 'Tentang Kami';
$bodyClass = 'home-page'; 
require_once __DIR__ . '/includes/public_header.php';

$aboutStats = [
    'books' => (int) $pdo->query('select count(*) from books')->fetchColumn(),
    'members' => (int) $pdo->query("select count(*) from users where role = 'member'")->fetchColumn(),
    'categories' => (int) $pdo->query('select count(*) from categories')->fetchColumn(),
];

$team = [
    [
        'name' => 'Nabil Modeong',
        'role' => 'Backend Developer & Database Designer',
        'photo' => 'assets/img/nabil.jpeg',
        'initial' => 'D1',
    ],
    [
        'name' => 'Satria Mandagi',
        'role' => 'Frontend Developer',
        'photo' => 'assets/img/team-2.jpg',
        'initial' => 'D2',
    ],
    [
        'name' => 'Arlen Tombey',
        'role' => 'Project Manager',
        'photo' => 'assets/img/team-3.jpg',
        'initial' => 'D3',
    ],
];
?>

<section class="about-hero">
    <div>
        <p class="eyebrow">Tentang Kami</p>
        <h1>Membangun akses baca yang rapi, modern, dan mudah digunakan.</h1>
        <p>Website perpustakaan ini dikembangkan untuk membantu pengunjung menemukan buku, anggota mengajukan peminjaman, dan admin mengelola koleksi dengan alur yang sederhana.</p>
        <div class="about-stats">
            <span><strong><?= number_format($aboutStats['books']) ?>+</strong>Buku</span>
            <span><strong><?= number_format($aboutStats['members']) ?>+</strong>Anggota</span>
            <span><strong><?= number_format($aboutStats['categories']) ?>+</strong>Kategori</span>
        </div>
    </div>
    <div class="about-orbit">
        <span>Katalog</span>
        <span>Sirkulasi</span>
        <span>Analitik</span>
    </div>
</section>

<section class="vision-grid">
    <article class="vision-card">
        <span>01</span>
        <h2>Visi</h2>
        <p>Menjadi sistem perpustakaan digital yang nyaman, informatif, dan mendukung budaya literasi melalui akses koleksi yang cepat serta tertata.</p>
    </article>
    <article class="vision-card">
        <span>02</span>
        <h2>Misi</h2>
        <p>Menyediakan katalog buku yang mudah dicari, mempercepat proses peminjaman, menjaga data anggota, dan membantu admin mengambil keputusan melalui laporan yang jelas.</p>
    </article>
    <article class="vision-card">
        <span>03</span>
        <h2>Nilai</h2>
        <p>Desain bersih, data yang terhubung, pengalaman pengguna yang responsif, dan kode yang simpel agar mudah dikembangkan lagi.</p>
    </article>
</section>

<section class="team-section">
    <div class="section-head">
        <div>
            <h2 class="eyebrow">Profil Tim</h2>
            <h2>Tim Pengembang</h2>
            <p>Kolaborasi kecil dengan fokus besar: sistem yang mudah dipakai, mudah dipahami, dan siap dikembangkan.</p>
        </div>
    </div>

    <div class="team-grid">
        <?php foreach ($team as $member): ?>
            <article class="team-card">
                <div class="team-photo">
                    <img src="<?= e(url($member['photo'])) ?>" alt="Foto <?= e($member['name']) ?>" onerror="this.style.display='none'; this.nextElementSibling.style.display='grid';">
                    <span><?= e($member['initial']) ?></span>
                </div>
                <h3><?= e($member['name']) ?></h3>
                <p><?= e($member['role']) ?></p>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/public_footer.php'; ?>
