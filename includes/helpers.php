<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';

function url($path = '')
{
    return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
}

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function current_user()
{
    return $_SESSION['user'] ?? null;
}

function is_admin()
{
    return current_user() && current_user()['role'] === 'admin';
}

function is_member()
{
    return current_user() && current_user()['role'] === 'member';
}

function require_login($role = null)
{
    if (!current_user()) {
        header('Location: ' . url('auth/login.php'));
        exit;
    }

    if ($role && current_user()['role'] !== $role) {
        header('Location: ' . url('index.php'));
        exit;
    }
}

function flash($key, $message = null)
{
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return;
    }

    if (!empty($_SESSION['flash'][$key])) {
        $value = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $value;
    }

    return null;
}

function upload_image($field, $folder)
{
    if (empty($_FILES[$field]['name'])) {
        return null;
    }

    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    $ext = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed, true)) {
        return null;
    }

    $fileName = uniqid('img_', true) . '.' . $ext;
    $targetDir = __DIR__ . '/../uploads/' . trim($folder, '/');

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $targetPath = $targetDir . '/' . $fileName;
    if (move_uploaded_file($_FILES[$field]['tmp_name'], $targetPath)) {
        return 'uploads/' . trim($folder, '/') . '/' . $fileName;
    }

    return null;
}

function calculate_fine($dueDate, $returnedAt = null)
{
    $endDate = $returnedAt ? new DateTime($returnedAt) : new DateTime();
    $due = new DateTime($dueDate);

    if ($endDate <= $due) {
        return 0;
    }

    return (int) $due->diff($endDate)->days * 1000;
}

function paginate($total, $page, $perPage)
{
    return [
        'page' => max(1, (int) $page),
        'per_page' => $perPage,
        'total_pages' => max(1, (int) ceil($total / $perPage)),
    ];
}
?>
