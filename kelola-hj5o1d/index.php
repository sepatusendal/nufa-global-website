<?php
declare(strict_types=1);
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/includes/auth.php';
require_login();

$articles = read_articles();
$events = read_events();
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title>Admin — Berita & Event NUFA Global</title>
<link rel="icon" href="../assets/logo.png" type="image/png">
<link rel="stylesheet" href="assets/admin.css">
</head>
<body>
<header class="admin-header">
  <div class="admin-header-inner">
    <img src="../assets/logo.png" alt="NUFA Global Education" class="admin-logo">
    <span class="admin-title">Admin — Berita & Event</span>
    <div class="admin-header-right">
      <span>Halo, <?= h($_SESSION['admin_username'] ?? '') ?></span>
      <a href="../news-events.html" target="_blank">Lihat halaman publik ↗</a>
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
      <h2>Berita &amp; Insight (<?= count($articles) ?>)</h2>
      <a href="article-form.php" class="btn-primary">+ Tambah Berita</a>
    </div>
    <?php if (!$articles): ?>
      <p class="admin-empty">Belum ada berita.</p>
    <?php else: ?>
      <table class="admin-table">
        <thead><tr><th>Tanggal</th><th>Judul</th><th>Kategori</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($articles as $a): ?>
          <tr>
            <td><?= h($a['date'] ?? '') ?></td>
            <td><?= h($a['title'] ?? '') ?></td>
            <td><?= h($a['category'] ?? '') ?></td>
            <td class="admin-table-actions">
              <a href="../article.html?slug=<?= urlencode($a['slug']) ?>" target="_blank">Lihat</a>
              <a href="article-form.php?slug=<?= urlencode($a['slug']) ?>">Edit</a>
              <form method="post" action="article-delete.php" onsubmit="return confirm('Hapus berita ini? Tidak bisa dibatalkan.');">
                <?= csrf_field() ?>
                <input type="hidden" name="slug" value="<?= h($a['slug']) ?>">
                <button type="submit" class="link-danger">Hapus</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </section>

  <section class="admin-section">
    <div class="admin-section-head">
      <h2>Event &amp; Kegiatan (<?= count($events) ?>)</h2>
      <a href="event-form.php" class="btn-primary">+ Tambah Event</a>
    </div>
    <?php if (!$events): ?>
      <p class="admin-empty">Belum ada event.</p>
    <?php else: ?>
      <table class="admin-table">
        <thead><tr><th>Tanggal</th><th>Judul</th><th>Lokasi</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($events as $e): ?>
          <tr>
            <td><?= h($e['date'] ?? '') ?></td>
            <td><?= h($e['title'] ?? '') ?></td>
            <td><?= h($e['location'] ?? '') ?></td>
            <td class="admin-table-actions">
              <a href="event-form.php?slug=<?= urlencode($e['slug']) ?>">Edit</a>
              <form method="post" action="event-delete.php" onsubmit="return confirm('Hapus event ini? Tidak bisa dibatalkan.');">
                <?= csrf_field() ?>
                <input type="hidden" name="slug" value="<?= h($e['slug']) ?>">
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
