<?php
require_once __DIR__ . '/includes/helpers.php';

header('Content-Type: application/json; charset=utf-8');

$q = trim($_GET['q'] ?? '');
if ($q === '') {
    echo json_encode([]);
    exit;
}

$stmt = $pdo->prepare("
    select b.id, b.title, b.author, b.cover, b.available_stock, coalesce(c.name, 'Umum') as category
    from books b
    left join categories c on c.id = b.category_id
    where lower(b.title) like lower(:prefix)
       or lower(b.author) like lower(:prefix)
       or lower(c.name) like lower(:prefix)
       or lower(b.title) like lower(:contains)
       or lower(b.author) like lower(:contains)
    order by
        case
            when lower(b.title) like lower(:prefix) then 1
            when lower(b.author) like lower(:prefix) then 2
            else 3
        end,
        b.title asc
    limit 6
");
$stmt->execute([
    'prefix' => $q . '%',
    'contains' => '%' . $q . '%',
]);

$results = array_map(function ($book) {
    return [
        'title' => $book['title'],
        'author' => $book['author'],
        'category' => $book['category'],
        'cover' => $book['cover'] ? url($book['cover']) : url('assets/css/placeholder-cover.svg'),
        'available' => (int) $book['available_stock'] > 0,
        'url' => url('book.php?id=' . $book['id']),
    ];
}, $stmt->fetchAll());

echo json_encode($results);
