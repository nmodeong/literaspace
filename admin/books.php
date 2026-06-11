<?php
$pageTitle = 'Kelola Buku';
require_once __DIR__ . '/../includes/admin_header.php';
// Fungsi upload ke Supabase Storage
function upload_to_supabase($fileKey, $bucket) {
    if (!isset($_FILES[$fileKey]) || $_FILES[$fileKey]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    $file = $_FILES[$fileKey];
    $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = 'img_' . uniqid() . mt_rand() . '.' . $ext;

    $supabaseUrl = rtrim(getenv('SUPABASE_URL') ?: 'https://iehvfnpjicvhvzglmpjz.supabase.co', '/');
    $supabaseKey = getenv('SUPABASE_KEY');
    $endpoint    = "{$supabaseUrl}/storage/v1/object/{$bucket}/{$filename}";

    $ch = curl_init($endpoint);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => file_get_contents($file['tmp_name']),
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . $supabaseKey,
            'Content-Type: ' . $file['type'],
            'x-upsert: true'
        ]
    ]);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_exec($ch);
    curl_close($ch);

    return ($code >= 200 && $code < 300)
        ? "{$supabaseUrl}/storage/v1/object/public/{$bucket}/{$filename}"
        : null;
}

// ====================== CRUD KATEGORI ======================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if (in_array($action, ['create_category', 'update_category', 'delete_category'])) {
        $cat_id   = (int) ($_POST['cat_id'] ?? 0);
        $cat_name = trim($_POST['name'] ?? '');

        if ($action === 'create_category' && $cat_name !== '') {
            $stmt = $pdo->prepare('INSERT INTO categories (name) VALUES (:name)');
            $stmt->execute(['name' => $cat_name]);
            flash('success', 'Kategori berhasil ditambahkan.');
        }

        if ($action === 'update_category' && $cat_id && $cat_name !== '') {
            $stmt = $pdo->prepare('UPDATE categories SET name = :name WHERE id = :id');
            $stmt->execute(['name' => $cat_name, 'id' => $cat_id]);
            flash('success', 'Kategori berhasil diperbarui.');
        }

        if ($action === 'delete_category' && $cat_id) {
            $check = $pdo->prepare('SELECT COUNT(*) FROM books WHERE category_id = :id');
            $check->execute(['id' => $cat_id]);
            if ($check->fetchColumn() > 0) {
                flash('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh buku.');
            } else {
                $stmt = $pdo->prepare('DELETE FROM categories WHERE id = :id');
                $stmt->execute(['id' => $cat_id]);
                flash('success', 'Kategori berhasil dihapus.');
            }
        }
        header('Location: ' . url('admin/books.php'));
        exit;
    }

    // ====================== CRUD BUKU ======================
    $action = $_POST['action'] ?? '';
    $id     = (int) ($_POST['id'] ?? 0);

    if ($action === 'delete') {
        $stmt = $pdo->prepare('delete from books where id = :id');
        $stmt->execute(['id' => $id]);
        flash('success', 'Buku berhasil dihapus.');
        header('Location: ' . url('admin/books.php'));
        exit;
    }

    $data = [
        'category_id' => $_POST['category_id'] ?: null,
        'title'       => trim($_POST['title'] ?? ''),
        'author'      => trim($_POST['author'] ?? ''),
        'publisher'   => trim($_POST['publisher'] ?? ''),
        'year'        => $_POST['year'] ?: null,
        'isbn'        => trim($_POST['isbn'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'stock'       => max(0, (int) ($_POST['stock'] ?? 0)),
    ];

     $cover = upload_to_supabase('cover', 'books');

    if ($action === 'create') {
        $data['available_stock'] = $data['stock'];
        $data['cover'] = $cover;
        $stmt = $pdo->prepare('
            insert into books (category_id, title, author, publisher, year, isbn, description, stock, available_stock, cover)
            values (:category_id, :title, :author, :publisher, :year, :isbn, :description, :stock, :available_stock, :cover)
        ');
        $stmt->execute($data);
        flash('success', 'Buku berhasil ditambahkan.');
    }

    if ($action === 'update') {
        $old = $pdo->prepare('select stock, available_stock, cover from books where id = :id');
        $old->execute(['id' => $id]);
        $book     = $old->fetch();
        $borrowed = max(0, (int) $book['stock'] - (int) $book['available_stock']);
        $data['available_stock'] = max(0, $data['stock'] - $borrowed);
        $data['cover'] = $cover ?: $book['cover'];
        $data['id']    = $id;
        $stmt = $pdo->prepare('
            update books set category_id=:category_id, title=:title, author=:author, publisher=:publisher,
            year=:year, isbn=:isbn, description=:description, stock=:stock, available_stock=:available_stock, cover=:cover
            where id=:id
        ');
        $stmt->execute($data);
        flash('success', 'Buku berhasil diperbarui.');
    }

    header('Location: ' . url('admin/books.php'));
    exit;
}

// Load data untuk edit
$editId   = (int) ($_GET['edit'] ?? 0);
$editBook = null;
if ($editId) {
    $stmt = $pdo->prepare('select * from books where id = :id');
    $stmt->execute(['id' => $editId]);
    $editBook = $stmt->fetch();
}

$editCat = null;
if (isset($_GET['edit_cat'])) {
    $stmt = $pdo->prepare('SELECT * FROM categories WHERE id = ?');
    $stmt->execute([(int)$_GET['edit_cat']]);
    $editCat = $stmt->fetch();
}

$categories = $pdo->query('select * from categories order by name')->fetchAll();

// Pagination & Daftar Buku
$page    = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 10;
$offset  = ($page - 1) * $perPage;
$total   = (int) $pdo->query('select count(*) from books')->fetchColumn();
$pagination = paginate($total, $page, $perPage);

$stmt = $pdo->prepare('
    select b.*, c.name as category
    from books b left join categories c on c.id = b.category_id
    order by b.created_at desc limit :limit offset :offset
');
$stmt->bindValue(':limit',  $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset,  PDO::PARAM_INT);
$stmt->execute();
$books = $stmt->fetchAll();
?>

<section class="page-title">
    <div>
        <p class="eyebrow">Inventaris</p>
        <h1>Kelola Buku</h1>
        <p class="muted">Atur koleksi, kategori, stok, dan informasi buku dalam satu tempat.</p>
    </div>
</section>

<div class="stats-grid compact-stats">
    <div class="stat-card"><span>Total Buku</span><strong><?= $total ?></strong><small>Koleksi terdata</small></div>
    <div class="stat-card"><span>Kategori</span><strong><?= count($categories) ?></strong><small>Rak klasifikasi</small></div>
    <div class="stat-card"><span>Halaman</span><strong><?= $page ?></strong><small>Dari <?= $pagination['total_pages'] ?> halaman</small></div>
</div>

<!-- ==================== KELOLA KATEGORI ==================== -->
<section class="panel">
    <div class="panel-head">
        <div>
            <p class="eyebrow">Kategori</p>
            <h2><?= $editCat ? 'Edit Kategori' : 'Tambah Kategori Baru' ?></h2>
        </div>
    </div>
    <form method="post" class="form-grid">
        <input type="hidden" name="action" value="<?= $editCat ? 'update_category' : 'create_category' ?>">
        <?php if ($editCat): ?>
            <input type="hidden" name="cat_id" value="<?= $editCat['id'] ?>">
        <?php endif; ?>
        <label>Nama Kategori
            <input type="text" name="name" value="<?= e($editCat['name'] ?? '') ?>" required>
        </label>
        <button class="btn" type="submit"><?= $editCat ? 'Simpan Perubahan' : 'Tambah Kategori' ?></button>
        <?php if ($editCat): ?><a class="btn btn-outline" href="<?= url('admin/books.php') ?>">Batal</a><?php endif; ?>
    </form>

    <div class="panel-head soft-head"><h2>Daftar Kategori</h2></div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nama Kategori</th>
                    <th>Jumlah Buku</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $cat):
                    $count = $pdo->prepare('SELECT COUNT(*) FROM books WHERE category_id = ?');
                    $count->execute([$cat['id']]);
                    $totalBook = $count->fetchColumn();
                ?>
                    <tr>
                        <td><strong><?= e($cat['name']) ?></strong></td>
                        <td><?= $totalBook ?> buku</td>
                        <td class="actions">
                            <div class="action-wrap">
                                <a class="btn btn-small btn-outline" href="?edit_cat=<?= $cat['id'] ?>">Edit</a>
                                <form method="post" onsubmit="return confirm('Hapus kategori ini?')">
                                    <input type="hidden" name="action" value="delete_category">
                                    <input type="hidden" name="cat_id" value="<?= $cat['id'] ?>">
                                    <button class="btn btn-small danger" type="submit">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<!-- ==================== CRUD BUKU ==================== -->
<section class="panel">
    <div class="panel-head">
        <div>
            <p class="eyebrow">Koleksi</p>
            <h2><?= $editBook ? 'Edit Buku' : 'Tambah Buku' ?></h2>
        </div>
    </div>
    <form method="post" enctype="multipart/form-data" class="form-grid two">
        <input type="hidden" name="action" value="<?= $editBook ? 'update' : 'create' ?>">
        <input type="hidden" name="id" value="<?= e($editBook['id'] ?? '') ?>">
        <label>Judul <input type="text" name="title" value="<?= e($editBook['title'] ?? '') ?>" required></label>
        <label>Pengarang <input type="text" name="author" value="<?= e($editBook['author'] ?? '') ?>" required></label>
        <label>Kategori
            <select name="category_id">
                <option value="">Umum</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['id'] ?>" <?= (($editBook['category_id'] ?? '') == $category['id']) ? 'selected' : '' ?>><?= e($category['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Penerbit <input type="text" name="publisher" value="<?= e($editBook['publisher'] ?? '') ?>"></label>
        <label>Tahun
            <select name="year">
                <option value="">Pilih Tahun</option>
                <?php
                $currentYear = date('Y');
                for ($y = $currentYear; $y >= 1900; $y--):
                    $selected = ($editBook['year'] == $y) ? 'selected' : '';
                ?>
                    <option value="<?= $y ?>" <?= $selected ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
        </label>
        <label>ISBN <input type="text" name="isbn" value="<?= e($editBook['isbn'] ?? '') ?>"></label>
        <label>Stok <input type="number" min="0" name="stock" value="<?= e($editBook['stock'] ?? 1) ?>" required></label>
        <label>Upload Cover <input type="file" name="cover" accept="image/*"></label>
        <label class="span-2">Deskripsi <textarea name="description" rows="4"><?= e($editBook['description'] ?? '') ?></textarea></label>
        <button class="btn" type="submit"><?= $editBook ? 'Simpan Perubahan' : 'Tambah Buku' ?></button>
        <?php if ($editBook): ?><a class="btn btn-outline" href="<?= url('admin/books.php') ?>">Batal</a><?php endif; ?>
    </form>
</section>

<!-- ==================== DAFTAR BUKU ==================== -->
<section class="panel">
    <div class="panel-head">
        <div>
            <p class="eyebrow">Data</p>
            <h2>Daftar Buku</h2>
        </div>
        <span class="badge"><?= $total ?> buku</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Cover</th>
                    <th>Judul</th>
                    <th>Kategori</th>
                    <th>Stok</th>
                    <th style="text-align: center; width: 200px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($books as $book): ?>
                    <tr>
                     <td><img class="thumb" src="<?= e($book['cover'] ? (str_starts_with($book['cover'], 'http') ? $book['cover'] : url($book['cover'])) : url('assets/css/placeholder-cover.svg')) ?>" alt=""></td>
                        <td><strong><?= e($book['title']) ?></strong><br><span class="muted"><?= e($book['author']) ?></span></td>
                        <td><?= e($book['category'] ?? 'Umum') ?></td>
                        <td><?= e($book['available_stock']) ?> / <?= e($book['stock']) ?></td>
                        <td class="actions">
                            <div class="action-wrap">
                                <a class="btn btn-small btn-outline" href="<?= url('admin/books.php?edit=' . $book['id']) ?>">Edit</a>
                                <form method="post" onsubmit="return confirm('Hapus buku ini?')">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= e($book['id']) ?>">
                                    <button class="btn btn-small danger" type="submit">Hapus</button>
                                </form>
                            </div>
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
