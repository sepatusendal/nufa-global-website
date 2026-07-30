<?php
declare(strict_types=1);
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/includes/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}
verify_csrf();

$slug = (string) ($_POST['slug'] ?? '');
$articles = read_articles();
$filtered = array_values(array_filter($articles, fn($a) => $a['slug'] !== $slug));

write_articles($filtered);
$_SESSION['flash'] = ['type' => 'success', 'message' => 'Berita berhasil dihapus.'];
redirect('index.php');
