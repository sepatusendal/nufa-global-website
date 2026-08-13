<?php
declare(strict_types=1);
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/includes/auth.php';
require_login();

$employees = read_employees();
$editUsername = $_GET['username'] ?? null;
$existing = $editUsername ? find_employee_by_username($employees, (string) $editUsername) : null;
$isEdit = $existing !== null;
$errors = [];

$values = $existing ?: [
    'name' => '',
    'username' => '',
    'email' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $name = trim((string) ($_POST['name'] ?? ''));
    $username = strtolower(trim((string) ($_POST['username'] ?? '')));
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    $originalUsername = $isEdit ? $existing['username'] : null;

    if ($name === '') $errors[] = 'Nama wajib diisi.';
    if ($username === '' || !preg_match('/^[a-z0-9._@-]+$/', $username)) {
        $errors[] = 'Username wajib diisi, huruf kecil/angka/titik/strip/@ saja, tanpa spasi.';
    }
    if (!$isEdit && $password === '') {
        $errors[] = 'Password wajib diisi untuk akun baru.';
    }
    if ($password !== '' && strlen($password) < 8) {
        $errors[] = 'Password minimal 8 karakter.';
    }

    $clash = find_employee_by_username($employees, $username);
    if ($clash && $clash['username'] !== $originalUsername) {
        $errors[] = 'Username ini sudah dipakai karyawan lain.';
    }

    $values = compact('name', 'username', 'email');

    if (!$errors) {
        $passwordHash = $isEdit ? $existing['password_hash'] : '';
        if ($password !== '') {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        }

        $newEntry = [
            'name' => $name,
            'username' => $username,
            'email' => $email,
            'password_hash' => $passwordHash,
            'created_at' => $isEdit ? ($existing['created_at'] ?? date('Y-m-d')) : date('Y-m-d'),
        ];

        if ($isEdit) {
            foreach ($employees as $i => $e) {
                if ($e['username'] === $originalUsername) {
                    $employees[$i] = $newEntry;
                    break;
                }
            }
        } else {
            $employees[] = $newEntry;
        }

        write_employees($employees);
        $_SESSION['flash'] = ['type' => 'success', 'message' => $isEdit ? 'Akun karyawan berhasil diperbarui.' : 'Akun karyawan baru berhasil dibuat.'];
        redirect('employees.php');
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title><?= $isEdit ? 'Edit' : 'Tambah' ?> Karyawan — Admin NUFA Global</title>
<link rel="icon" href="../assets/logo.png" type="image/png">
<link rel="stylesheet" href="assets/admin.css">
</head>
<body>
<header class="admin-header">
  <div class="admin-header-inner">
    <img src="../assets/logo.png" alt="NUFA Global Education" class="admin-logo">
    <span class="admin-title"><?= $isEdit ? 'Edit Karyawan' : 'Tambah Karyawan' ?></span>
    <div class="admin-header-right">
      <a href="employees.php">← Kembali</a>
    </div>
  </div>
</header>

<main class="admin-main admin-main-narrow">
  <?php if ($errors): ?>
    <div class="alert alert-error">
      <ul><?php foreach ($errors as $e): ?><li><?= h($e) ?></li><?php endforeach; ?></ul>
    </div>
  <?php endif; ?>

  <form method="post" class="admin-form">
    <?= csrf_field() ?>

    <label for="name">Nama Lengkap *</label>
    <input type="text" id="name" name="name" value="<?= h($values['name']) ?>" required>

    <label for="username">Username Login *</label>
    <input type="text" id="username" name="username" value="<?= h($values['username']) ?>" placeholder="rian@nufaglobaledu.com" required <?= $isEdit ? 'readonly' : '' ?>>
    <p class="admin-hint">Dipakai karyawan untuk login ke portal onboarding — biasanya email kerja karyawan. Huruf kecil, tanpa spasi.</p>

    <label for="email">Email Kerja</label>
    <input type="email" id="email" name="email" value="<?= h($values['email']) ?>" placeholder="rian@nufaglobaledu.com">

    <label for="password">Password <?= $isEdit ? '(kosongkan kalau tidak ingin ganti)' : '*' ?></label>
    <input type="password" id="password" name="password" autocomplete="new-password" placeholder="Minimal 8 karakter">

    <button type="submit" class="btn-primary"><?= $isEdit ? 'Simpan Perubahan' : 'Buat Akun' ?></button>
  </form>
</main>
</body>
</html>
