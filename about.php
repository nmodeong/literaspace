<?php
$pageTitle = 'Tentang Kami';
$bodyClass = 'home-page'; 
require_once __DIR__ . '/includes/public_header.php';

$aboutStats = [
    'books' => (int) $pdo->query('select count(*) from books')->fetchColumn(),
    'members' => (int) $pdo->query("select count(*) from users where role = 'member'")->fetchColumn(),
    'categories' => (int) $pdo->query('select count(*) from categories')->fetchColumn(),
];

// Satu entri foto kelompok
$team = [
    'photo' => 'assets/img/tim.jpeg',
    'caption' => 'Tim Pengembang',
    'members' => [
        ['name' => 'Arlen Tombey',   'role' => 'Project Manager'],
        ['name' => 'Satria Mandagi', 'role' => 'Frontend Developer'],
        ['name' => 'Nabil Modeong',  'role' => 'Backend Developer & Database Designer'],
       
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

    <div class="team-group-wrap">
        <div class="team-group-photo">
            <img
                src="<?= e(url($team['photo'])) ?>"
                alt="Foto Tim Pengembang"
                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
            >
            <!-- Fallback jika foto tidak ditemukan -->
            <div class="team-photo-fallback" style="display:none;">
                <span>📷</span>
                <p>Foto tim belum tersedia</p>
            </div>
        </div>

        <div class="team-group-info">
            <h3><?= e($team['caption']) ?></h3>
            <ul class="team-member-list">
                <?php foreach ($team['members'] as $member): ?>
                    <li>
                        <span class="member-name"><?= e($member['name']) ?></span>
                        <span class="member-role"><?= e($member['role']) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/public_footer.php'; ?>