<?php
declare(strict_types=1);
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/includes/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('employees.php');
}
verify_csrf();

$username = (string) ($_POST['username'] ?? '');
$employees = read_employees();
$filtered = array_values(array_filter($employees, fn($e) => $e['username'] !== $username));

write_employees($filtered);
$_SESSION['flash'] = ['type' => 'success', 'message' => 'Akun karyawan berhasil dihapus.'];
redirect('employees.php');
