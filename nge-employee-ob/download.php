<?php
declare(strict_types=1);
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/includes/auth.php';
require_login();

$storedName = (string) ($_GET['file'] ?? '');
if (!preg_match('/^[a-f0-9]{16}\.[a-z0-9]{1,10}$/', $storedName)) {
    http_response_code(404);
    die('File tidak ditemukan.');
}

$memo = null;
foreach (read_memos() as $m) {
    if (($m['stored_name'] ?? '') === $storedName) {
        $memo = $m;
        break;
    }
}
if (!$memo) {
    http_response_code(404);
    die('File tidak ditemukan.');
}

$path = MEMO_DIR . '/' . $storedName;
if (!is_file($path)) {
    http_response_code(404);
    die('File tidak ditemukan di server.');
}

header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename((string) ($memo['original_name'] ?? $storedName)) . '"');
header('Content-Length: ' . (string) filesize($path));
readfile($path);
exit;
