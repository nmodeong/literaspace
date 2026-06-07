<?php
require_once __DIR__ . '/../includes/helpers.php';

$books = [
    [
        'title' => 'Laut Bercerita',
        'author' => 'Leila S. Chudori',
        'category' => 'Fiksi',
        'publisher' => 'Kepustakaan Populer Gramedia',
        'year' => 2017,
        'isbn' => '9786024246945',
        'stock' => 5,
        'color' => '#1f4d5a',
        'accent' => '#d4a857',
        'description' => 'Novel tentang keluarga, kehilangan, persahabatan, dan keberanian menghadapi masa gelap sejarah. Buku ini cocok untuk pembaca yang menyukai cerita emosional dengan latar sosial-politik yang kuat.',
    ],
    [
        'title' => 'Bumi Manusia',
        'author' => 'Pramoedya Ananta Toer',
        'category' => 'Fiksi',
        'publisher' => 'Lentera Dipantara',
        'year' => 1980,
        'isbn' => '9789799731234',
        'stock' => 4,
        'color' => '#7b2f2f',
        'accent' => '#f0c46c',
        'description' => 'Kisah Minke dan pergulatan manusia Hindia Belanda dalam menemukan martabat, pendidikan, cinta, dan identitas. Bacaan klasik Indonesia yang kaya konteks sejarah.',
    ],
    [
        'title' => 'Negeri 5 Menara',
        'author' => 'Ahmad Fuadi',
        'category' => 'Fiksi',
        'publisher' => 'Gramedia Pustaka Utama',
        'year' => 2009,
        'isbn' => '9789792248616',
        'stock' => 6,
        'color' => '#174a74',
        'accent' => '#f4b860',
        'description' => 'Cerita inspiratif tentang persahabatan, pendidikan, dan kekuatan mantra man jadda wajada. Cocok untuk pembaca remaja dan umum yang mencari motivasi.',
    ],
    [
        'title' => 'Atomic Habits',
        'author' => 'James Clear',
        'category' => 'Nonfiksi',
        'publisher' => 'Gramedia Pustaka Utama',
        'year' => 2018,
        'isbn' => '9786020633176',
        'stock' => 5,
        'color' => '#202b38',
        'accent' => '#5cc3a6',
        'description' => 'Panduan membangun kebiasaan kecil yang berdampak besar. Buku ini menjelaskan cara merancang lingkungan, identitas, dan sistem agar perubahan lebih mudah bertahan.',
    ],
    [
        'title' => 'Filosofi Teras',
        'author' => 'Henry Manampiring',
        'category' => 'Nonfiksi',
        'publisher' => 'Kompas',
        'year' => 2018,
        'isbn' => '9786024125189',
        'stock' => 4,
        'color' => '#2f5d50',
        'accent' => '#e7b75f',
        'description' => 'Pengantar filsafat Stoik dalam bahasa yang ringan dan relevan dengan kehidupan sehari-hari. Membantu pembaca mengelola emosi, ekspektasi, dan ketenangan batin.',
    ],
    [
        'title' => 'Sapiens',
        'author' => 'Yuval Noah Harari',
        'category' => 'Sejarah',
        'publisher' => 'Kepustakaan Populer Gramedia',
        'year' => 2011,
        'isbn' => '9786024244163',
        'stock' => 3,
        'color' => '#5f4b32',
        'accent' => '#d7b56d',
        'description' => 'Ikhtisar besar sejarah manusia dari pemburu-pengumpul sampai masyarakat modern. Buku ini menelusuri peran bahasa, mitos, pertanian, uang, dan sains.',
    ],
    [
        'title' => 'Algoritma dan Pemrograman',
        'author' => 'Rinaldi Munir',
        'category' => 'Teknologi',
        'publisher' => 'Informatika',
        'year' => 2016,
        'isbn' => '9786021514917',
        'stock' => 7,
        'color' => '#173c46',
        'accent' => '#16c7c8',
        'description' => 'Buku dasar untuk memahami logika algoritma, struktur kontrol, pemecahan masalah, dan konsep pemrograman. Cocok untuk mahasiswa dan pemula teknologi.',
    ],
    [
        'title' => 'Clean Code',
        'author' => 'Robert C. Martin',
        'category' => 'Teknologi',
        'publisher' => 'Prentice Hall',
        'year' => 2008,
        'isbn' => '9780132350884',
        'stock' => 3,
        'color' => '#111827',
        'accent' => '#9bd672',
        'description' => 'Panduan menulis kode yang mudah dibaca, dirawat, dan dikembangkan. Berisi prinsip praktis tentang fungsi, penamaan, class, testing, dan refactoring.',
    ],
    [
        'title' => 'Designing Data-Intensive Applications',
        'author' => 'Martin Kleppmann',
        'category' => 'Teknologi',
        'publisher' => 'O Reilly Media',
        'year' => 2017,
        'isbn' => '9781449373320',
        'stock' => 2,
        'color' => '#263f6a',
        'accent' => '#f26d5b',
        'description' => 'Referensi sistem data modern yang membahas reliabilitas, skalabilitas, replikasi, partisi, transaksi, stream processing, dan arsitektur aplikasi berbasis data.',
    ],
    [
        'title' => 'Sejarah Indonesia Modern',
        'author' => 'M. C. Ricklefs',
        'category' => 'Sejarah',
        'publisher' => 'Serambi',
        'year' => 2008,
        'isbn' => '9789790241151',
        'stock' => 4,
        'color' => '#6b3e26',
        'accent' => '#e2ad65',
        'description' => 'Kajian sejarah Indonesia modern yang menyajikan dinamika politik, sosial, budaya, dan perubahan masyarakat dari masa kolonial hingga kontemporer.',
    ],
    [
        'title' => 'The Psychology of Money',
        'author' => 'Morgan Housel',
        'category' => 'Nonfiksi',
        'publisher' => 'Harriman House',
        'year' => 2020,
        'isbn' => '9780857197689',
        'stock' => 5,
        'color' => '#214034',
        'accent' => '#d7c46a',
        'description' => 'Buku tentang perilaku, emosi, dan cara berpikir manusia terhadap uang. Menjelaskan kenapa keputusan finansial sering lebih psikologis daripada matematis.',
    ],
    [
        'title' => 'Pulang',
        'author' => 'Tere Liye',
        'category' => 'Fiksi',
        'publisher' => 'Republika',
        'year' => 2015,
        'isbn' => '9786020822129',
        'stock' => 5,
        'color' => '#2d334a',
        'accent' => '#dd8d55',
        'description' => 'Novel petualangan tentang perjalanan, keluarga, keberanian, dan arti pulang. Menggabungkan aksi dengan refleksi personal yang mudah dinikmati pembaca luas.',
    ],
];

function ensure_category(PDO $pdo, string $name): int
{
    $stmt = $pdo->prepare('select id from categories where name = :name limit 1');
    $stmt->execute(['name' => $name]);
    $id = $stmt->fetchColumn();
    if ($id) {
        return (int) $id;
    }

    $stmt = $pdo->prepare('insert into categories (name) values (:name) returning id');
    $stmt->execute(['name' => $name]);
    return (int) $stmt->fetchColumn();
}

function make_cover(string $title, string $author, string $color, string $accent, string $fileName): string
{
    $safeTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
    $safeAuthor = htmlspecialchars($author, ENT_QUOTES, 'UTF-8');
    $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="720" height="1000" viewBox="0 0 720 1000">
  <defs>
    <linearGradient id="g" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0" stop-color="$color"/>
      <stop offset="1" stop-color="#10252c"/>
    </linearGradient>
    <filter id="shadow" x="-20%" y="-20%" width="140%" height="140%">
      <feDropShadow dx="0" dy="18" stdDeviation="18" flood-color="#000" flood-opacity=".28"/>
    </filter>
  </defs>
  <rect width="720" height="1000" rx="34" fill="url(#g)"/>
  <circle cx="590" cy="130" r="150" fill="$accent" opacity=".22"/>
  <circle cx="120" cy="850" r="210" fill="$accent" opacity=".16"/>
  <rect x="78" y="96" width="562" height="808" rx="28" fill="none" stroke="rgba(255,255,255,.22)" stroke-width="4"/>
  <path d="M150 230c78-42 150-42 216 0 66-42 138-42 216 0v410c-78-42-150-42-216 0-66-42-138-42-216 0z" fill="rgba(255,255,255,.16)" filter="url(#shadow)"/>
  <path d="M366 230v410" stroke="rgba(255,255,255,.34)" stroke-width="5"/>
  <text x="100" y="170" fill="$accent" font-family="Poppins, Arial, sans-serif" font-size="30" font-weight="800" letter-spacing="4">PERPUSTAKAAN</text>
  <foreignObject x="100" y="360" width="520" height="260">
    <div xmlns="http://www.w3.org/1999/xhtml" style="font-family:Poppins,Arial,sans-serif;color:white;font-size:58px;line-height:1.05;font-weight:800;word-wrap:break-word;">$safeTitle</div>
  </foreignObject>
  <text x="100" y="745" fill="rgba(255,255,255,.82)" font-family="Poppins, Arial, sans-serif" font-size="30" font-weight="700">$safeAuthor</text>
  <rect x="100" y="800" width="190" height="10" rx="5" fill="$accent"/>
</svg>
SVG;

    $path = __DIR__ . '/../uploads/covers/' . $fileName;
    file_put_contents($path, $svg);
    return 'uploads/covers/' . $fileName;
}

$inserted = 0;
$skipped = 0;

foreach ($books as $index => $book) {
    $check = $pdo->prepare('select id from books where title = :title and author = :author limit 1');
    $check->execute(['title' => $book['title'], 'author' => $book['author']]);
    if ($check->fetchColumn()) {
        $skipped++;
        continue;
    }

    $categoryId = ensure_category($pdo, $book['category']);
    $fileName = 'seed_book_' . str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) . '.svg';
    $cover = make_cover($book['title'], $book['author'], $book['color'], $book['accent'], $fileName);

    $stmt = $pdo->prepare('
        insert into books (category_id, title, author, publisher, year, isbn, description, cover, stock, available_stock)
        values (:category_id, :title, :author, :publisher, :year, :isbn, :description, :cover, :stock, :available_stock)
    ');
    $stmt->execute([
        'category_id' => $categoryId,
        'title' => $book['title'],
        'author' => $book['author'],
        'publisher' => $book['publisher'],
        'year' => $book['year'],
        'isbn' => $book['isbn'],
        'description' => $book['description'],
        'cover' => $cover,
        'stock' => $book['stock'],
        'available_stock' => $book['stock'],
    ]);
    $inserted++;
}

echo "Seed selesai. Ditambahkan: {$inserted}, dilewati: {$skipped}." . PHP_EOL;
