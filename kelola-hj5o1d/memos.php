<?php
declare(strict_types=1);
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/includes/auth.php';
require_login();

$memos = read_memos();
$employees = read_employees();
$errors = [];
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $title = trim((string) ($_POST['title'] ?? ''));
    if ($title === '') $errors[] = 'Judul memo wajib diisi.';

    $visibility = ($_POST['visibility'] ?? 'all') === 'specific' ? 'specific' : 'all';
    $targets = [];
    if ($visibility === 'specific') {
        $submitted = $_POST['target_employees'] ?? [];
        $validUsernames = array_map(fn($e) => strtolower((string) $e['username']), $employees);
        $targets = array_values(array_intersect(array_map('strtolower', (array) $submitted), $validUsernames));
        if (!$targets) {
            $errors[] = 'Pilih minimal 1 karyawan kalau memo ini khusus untuk karyawan tertentu.';
        }
    }

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
            'visibility' => $visibility,
            'employees' => $targets,
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
    <form method="post" class="admin-form" enctype="multipart/form-data" id="memo-form">
      <?= csrf_field() ?>
      <label for="title">Judul Memo *</label>
      <input type="text" id="title" name="title" placeholder="Contoh: SOP Cuti & Izin 2026" required>
      <label for="memo_file">File Memo *</label>
      <input type="file" id="memo_file" name="memo_file" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip" required>
      <p class="admin-hint">Format pdf/doc/docx/xls/xlsx/ppt/pptx/zip, maks 25MB. File TIDAK bisa diakses publik — hanya karyawan yang login ke portal onboarding yang bisa download.</p>

      <label>Siapa yang bisa lihat &amp; download memo ini? *</label>
      <div class="admin-form-row" style="margin-top:6px;">
        <label style="font-weight:400; display:flex; align-items:center; gap:6px; margin-top:0;">
          <input type="radio" name="visibility" value="all" id="vis-all" checked style="width:auto;"> Semua Karyawan
        </label>
        <label style="font-weight:400; display:flex; align-items:center; gap:6px; margin-top:0;">
          <input type="radio" name="visibility" value="specific" id="vis-specific" style="width:auto;"> Karyawan Tertentu
        </label>
      </div>

      <div id="target-employees-box" style="display:none; margin-top:10px; border:1px solid var(--a-line); border-radius:8px; padding:12px 14px; max-height:220px; overflow-y:auto;">
        <?php if (!$employees): ?>
          <p class="admin-hint" style="margin:0;">Belum ada akun karyawan. Buat dulu di menu <a href="employees.php">Karyawan</a>.</p>
        <?php else: ?>
          <?php foreach ($employees as $emp): ?>
            <label style="font-weight:400; display:flex; align-items:center; gap:8px; margin:6px 0;">
              <input type="checkbox" name="target_employees[]" value="<?= h($emp['username']) ?>" style="width:auto;">
              <?= h($emp['name'] ?? '') ?> <span class="admin-hint" style="margin:0;">(<?= h($emp['username']) ?>)</span>
            </label>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

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
        <thead><tr><th>Judul</th><th>Nama File Asli</th><th>Tanggal Upload</th><th>Ditujukan Untuk</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($memos as $m): ?>
          <?php
            $isSpecific = ($m['visibility'] ?? 'all') === 'specific';
            $targetNames = [];
            if ($isSpecific) {
                foreach ((array) ($m['employees'] ?? []) as $username) {
                    $emp = find_employee_by_username($employees, (string) $username);
                    $targetNames[] = $emp['name'] ?? $username;
                }
            }
          ?>
          <tr>
            <td><?= h($m['title'] ?? '') ?></td>
            <td><?= h($m['original_name'] ?? '') ?></td>
            <td><?= h($m['uploaded_at'] ?? '') ?></td>
            <td><?= $isSpecific ? h(implode(', ', $targetNames) ?: '(tidak ada)') : 'Semua Karyawan' ?></td>
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
<script>
(function () {
  var box = document.getElementById('target-employees-box');
  var radios = document.querySelectorAll('input[name="visibility"]');
  function sync() {
    box.style.display = document.getElementById('vis-specific').checked ? 'block' : 'none';
  }
  radios.forEach(function (r) { r.addEventListener('change', sync); });
  sync();
})();
</script>
</body>
</html>
