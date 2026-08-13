<?php
declare(strict_types=1);
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/includes/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('memos.php');
}
verify_csrf();

$storedName = (string) ($_POST['stored_name'] ?? '');
$memos = read_memos();
$filtered = array_values(array_filter($memos, fn($m) => $m['stored_name'] !== $storedName));

delete_memo_file($storedName);
write_memos($filtered);
$_SESSION['flash'] = ['type' => 'success', 'message' => 'Memo berhasil dihapus.'];
redirect('memos.php');
