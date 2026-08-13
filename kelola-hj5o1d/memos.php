<?php
declare(strict_types=1);
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/includes/auth.php';
require_login();

$memos = read_memos();
$errors = [];
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $title = trim((string) ($_POST['title'] ?? ''));
    if ($title === '') $errors[] = 'Judul memo wajib diisi.';

    $upload = handle_memo_upload('memo_file');
    if (!empty($upload['error'])) {
        $errors[] = $upload['error'];
    } elseif (empty($upload['stored_name'])) {
        $errors[] = 'Pilih file memo untuk diupload.';
    }

    if (!$errors) {
        $memos[] = [
            'title' => $title,
            'stored_name' => $upload['stored_name'],
            'original_name' => $upload['original_name'],
            'uploaded_at' => date('Y-m-d'),
        ];
        write_memos($memos);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Memo berhasil diupload.'];
        redirect('memos.php');
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title>Admin — Memo Internal NUFA Global</title>
<link rel="icon" href="../assets/logo.png" type="image/png">
<link rel="stylesheet" href="assets/admin.css">
</head>
<body>
<header class="admin-header">
  <div class="admin-header-inner">
    <img src="../assets/logo.png" alt="NUFA Global Education" class="admin-logo">
    <span class="admin-title">Admin — Memo Internal</span>
    <div class="admin-header-right">
      <a href="index.php">← Berita &amp; Event</a>
      <a href="employees.php">Karyawan</a>
      <a href="logout.php" class="btn-outline">Logout</a>
    </div>
  </div>
</header>

<main class="admin-main">
  <?php if ($flash): ?>
    <div class="alert alert-<?= h($flash['type']) ?>"><?= h($flash['message']) ?></div>
  <?php endif; ?>
  <?php if ($errors): ?>
    <div class="alert alert-error">
      <ul><?php foreach ($errors as $e): ?><li><?= h($e) ?></li><?php endforeach; ?></ul>
    </div>
  <?php endif; ?>

  <section class="admin-section">
    <div class="admin-section-head">
      <h2>Upload Memo Baru</h2>
    </div>
    <form method="post" class="admin-form" enctype="multipart/form-data">
      <?= csrf_field() ?>
      <label for="title">Judul Memo *</label>
      <input type="text" id="title" name="title" placeholder="Contoh: SOP Cuti & Izin 2026" required>
      <label for="memo_file">File Memo *</label>
      <input type="file" id="memo_file" name="memo_file" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip" required>
      <p class="admin-hint">Format pdf/doc/docx/xls/xlsx/ppt/pptx/zip, maks 25MB. File TIDAK bisa diakses publik — hanya karyawan yang login ke portal onboarding yang bisa download.</p>
      <button type="submit" class="btn-primary">Upload</button>
    </form>
  </section>

  <section class="admin-section">
    <div class="admin-section-head">
      <h2>Daftar Memo (<?= count($memos) ?>)</h2>
    </div>
    <?php if (!$memos): ?>
      <p class="admin-empty">Belum ada memo yang diupload.</p>
    <?php else: ?>
      <table class="admin-table">
        <thead><tr><th>Judul</th><th>Nama File Asli</th><th>Tanggal Upload</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($memos as $m): ?>
          <tr>
            <td><?= h($m['title'] ?? '') ?></td>
            <td><?= h($m['original_name'] ?? '') ?></td>
            <td><?= h($m['uploaded_at'] ?? '') ?></td>
            <td class="admin-table-actions">
              <form method="post" action="memo-delete.php" onsubmit="return confirm('Hapus memo ini? Tidak bisa dibatalkan.');">
                <?= csrf_field() ?>
                <input type="hidden" name="stored_name" value="<?= h($m['stored_name']) ?>">
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
