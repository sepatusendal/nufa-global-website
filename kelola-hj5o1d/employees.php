<?php
declare(strict_types=1);
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/includes/auth.php';
require_login();

$employees = read_employees();
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title>Admin — Akun Karyawan NUFA Global</title>
<link rel="icon" href="../assets/logo.png" type="image/png">
<link rel="stylesheet" href="assets/admin.css">
</head>
<body>
<header class="admin-header">
  <div class="admin-header-inner">
    <img src="../assets/logo.png" alt="NUFA Global Education" class="admin-logo">
    <span class="admin-title">Admin — Akun Karyawan</span>
    <div class="admin-header-right">
      <a href="index.php">← Berita &amp; Event</a>
      <a href="memos.php">Memo Internal</a>
      <a href="logout.php" class="btn-outline">Logout</a>
    </div>
  </div>
</header>

<main class="admin-main">
  <?php if ($flash): ?>
    <div class="alert alert-<?= h($flash['type']) ?>"><?= h($flash['message']) ?></div>
  <?php endif; ?>

  <section class="admin-section">
    <div class="admin-section-head">
      <h2>Akun Karyawan — Onboarding Portal (<?= count($employees) ?>)</h2>
      <a href="employee-form.php" class="btn-primary">+ Tambah Karyawan</a>
    </div>
    <p class="admin-hint">Akun di sini dipakai karyawan untuk login ke portal onboarding (<code>/onboarding-14a60b/</code>). Ini terpisah dari login admin ini.</p>
    <?php if (!$employees): ?>
      <p class="admin-empty">Belum ada akun karyawan.</p>
    <?php else: ?>
      <table class="admin-table">
        <thead><tr><th>Nama</th><th>Username</th><th>Email</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($employees as $emp): ?>
          <tr>
            <td><?= h($emp['name'] ?? '') ?></td>
            <td><?= h($emp['username'] ?? '') ?></td>
            <td><?= h($emp['email'] ?? '') ?></td>
            <td class="admin-table-actions">
              <a href="employee-form.php?username=<?= urlencode($emp['username']) ?>">Edit</a>
              <form method="post" action="employee-delete.php" onsubmit="return confirm('Hapus akun karyawan ini? Karyawan tidak akan bisa login lagi.');">
                <?= csrf_field() ?>
                <input type="hidden" name="username" value="<?= h($emp['username']) ?>">
                <button type="submit" class="link-danger">Hapus</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </section>
</main>
</body>
</html>
