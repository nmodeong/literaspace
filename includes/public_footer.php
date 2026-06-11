</main>
<footer class="site-footer">
    <div class="footer-inner">
        <div class="footer-brand">
            <a class="brand" href="<?= url('index.php') ?>">PERPUSTAKAAN MODERN</a>
            <p>Platform katalog dan sirkulasi buku yang membantu pengunjung, anggota, dan admin mengelola literasi dengan lebih rapi.</p>
        </div>
        <div class="footer-links">
            <h3>Navigasi</h3>
            <a href="<?= url('index.php') ?>">Katalog</a>
            <a href="<?= url('about.php') ?>">Tentang Kami</a>
            <a href="<?= url('auth/login.php') ?>">Login</a>
            <a href="<?= url('auth/register.php') ?>">Daftar</a>
        </div>
        <div class="footer-links">
            <h3>Layanan</h3>
            <span>Pencarian buku real-time</span>
            <span>Peminjaman anggota</span>
            <span>Dashboard admin</span>
            <span>Laporan analitik</span>
        </div>
        <div class="footer-card">
            <span>Jam Digital</span>
            <strong>24/7</strong>
            <p>Katalog online siap diakses kapan saja untuk menemukan bacaan berikutnya.</p>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; <?= date('Y') ?> Perpustakaan Moderen. Dikembangkan untuk kemajuan literasi.</p>
        <p>Dalam Tahap Pengembangan</p>
    </div>
</footer>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('.live-search-form');
    if (!form) return;

    const input = form.querySelector('input[name="q"]');
    const results = form.querySelector('.live-search-results');
    const endpoint = form.dataset.searchEndpoint;
    let controller = null;

    function closeResults() {
        results.classList.remove('active');
        results.innerHTML = '';
    }

    function render(items) {
        if (!items.length) {
            results.innerHTML = '<div class="live-search-empty">Buku belum ditemukan.</div>';
            results.classList.add('active');
            return;
        }

        results.innerHTML = items.map(function (item) {
            return `
                <a class="live-search-item" href="${item.url}">
                    <img src="${item.cover}" alt="">
                    <span>
                        <strong>${item.title}</strong>
                        <small>${item.author} - ${item.category}</small>
                    </span>
                    <em>${item.available ? 'Tersedia' : 'Kosong'}</em>
                </a>
            `;
        }).join('');
        results.classList.add('active');
    }

    input.addEventListener('input', function () {
        const q = input.value.trim();
        if (controller) controller.abort();
        if (!q) {
            closeResults();
            return;
        }

        controller = new AbortController();
        fetch(endpoint + '?q=' + encodeURIComponent(q), { signal: controller.signal })
            .then(function (response) { return response.json(); })
            .then(render)
            .catch(function (error) {
                if (error.name !== 'AbortError') closeResults();
            });
    });

    document.addEventListener('click', function (event) {
        if (!form.contains(event.target)) closeResults();
    });
});
</script>
</body>
</html>
