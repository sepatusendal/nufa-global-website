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
$events = read_events();
$filtered = array_values(array_filter($events, fn($e) => $e['slug'] !== $slug));

write_events($filtered);
$_SESSION['flash'] = ['type' => 'success', 'message' => 'Event berhasil dihapus.'];
redirect('index.php');
