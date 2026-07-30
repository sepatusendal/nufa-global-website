<?php
declare(strict_types=1);
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/includes/auth.php';
require_login();

$articles = read_articles();
$editSlug = $_GET['slug'] ?? null;
$existing = $editSlug ? find_by_slug($articles, (string) $editSlug) : null;
$isEdit = $existing !== null;
$errors = [];

$values = $existing ?: [
    'slug' => '',
    'date' => '',
    'category' => '',
    'title' => '',
    'excerpt' => '',
    'phA' => '#4B60AC',
    'phB' => '#8A9BE0',
    'emoji' => '📰',
    'image' => '',
    'content' => [],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $title = trim((string) ($_POST['title'] ?? ''));
    $date = trim((string) ($_POST['date'] ?? ''));
    $category = trim((string) ($_POST['category'] ?? ''));
    $excerpt = trim((string) ($_POST['excerpt'] ?? ''));
    $phA = trim((string) ($_POST['phA'] ?? '#4B60AC'));
    $phB = trim((string) ($_POST['phB'] ?? '#8A9BE0'));
    $emoji = trim((string) ($_POST['emoji'] ?? '📰'));
    $image = trim((string) ($_POST['image'] ?? ''));
    $contentRaw = (string) ($_POST['content'] ?? '');
    $slugInput = trim((string) ($_POST['slug'] ?? ''));

    $content = array_values(array_filter(array_map('trim', preg_split('/\n\s*\n/', $contentRaw)), fn($p) => $p !== ''));

    if ($title === '') $errors[] = 'Judul wajib diisi.';
    if ($excerpt === '') $errors[] = 'Ringkasan wajib diisi.';
    if (!$content) $errors[] = 'Isi artikel wajib diisi (minimal 1 paragraf).';

    $originalSlug = $isEdit ? $existing['slug'] : null;
    $baseSlug = slugify($slugInput !== '' ? $slugInput : $title);
    $finalSlug = unique_slug($articles, $baseSlug, $originalSlug);

    $upload = handle_media_upload('media_file', $finalSlug);
    if (!empty($upload['error'])) {
        $errors[] = $upload['error'];
    } elseif (!empty($upload['path'])) {
        $image = $upload['path'];
    }

    $values = compact('date', 'category', 'title', 'excerpt', 'phA', 'phB', 'emoji', 'image', 'content');
    $values['slug'] = $finalSlug;

    if (!$errors) {
        $newEntry = [
            'slug' => $finalSlug,
            'type' => 'news',
            'date' => $date,
            'category' => $category,
            'title' => $title,
            'excerpt' => $excerpt,
            'phA' => $phA ?: '#4B60AC',
            'phB' => $phB ?: '#8A9BE0',
            'emoji' => $emoji ?: '📰',
            'image' => $image,
            'content' => $content,
        ];

        if ($isEdit) {
            foreach ($articles as $i => $a) {
                if ($a['slug'] === $originalSlug) {
                    $articles[$i] = $newEntry;
                    break;
                }
            }
        } else {
            array_unshift($articles, $newEntry);
        }

        write_articles($articles);
        $_SESSION['flash'] = ['type' => 'success', 'message' => $isEdit ? 'Berita berhasil diperbarui.' : 'Berita baru berhasil ditambahkan.'];
        redirect('index.php');
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title><?= $isEdit ? 'Edit' : 'Tambah' ?> Berita — Admin NUFA Global</title>
<link rel="icon" href="../assets/logo.png" type="image/png">
<link rel="stylesheet" href="assets/admin.css">
</head>
<body>
<header class="admin-header">
  <div class="admin-header-inner">
    <img src="../assets/logo.png" alt="NUFA Global Education" class="admin-logo">
    <span class="admin-title"><?= $isEdit ? 'Edit Berita' : 'Tambah Berita' ?></span>
    <div class="admin-header-right">
      <a href="index.php">← Kembali ke Dashboard</a>
    </div>
  </div>
</header>

<main class="admin-main admin-main-narrow">
  <?php if ($errors): ?>
    <div class="alert alert-error">
      <ul><?php foreach ($errors as $e): ?><li><?= h($e) ?></li><?php endforeach; ?></ul>
    </div>
  <?php endif; ?>

  <form method="post" class="admin-form" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <label for="title">Judul Berita *</label>
    <input type="text" id="title" name="title" value="<?= h($values['title']) ?>" required>

    <label for="slug">Slug (URL) — kosongkan biar otomatis dari judul</label>
    <input type="text" id="slug" name="slug" value="<?= h($values['slug']) ?>" placeholder="contoh: judul-berita-ini">

    <div class="admin-form-row">
      <div>
        <label for="date">Tanggal</label>
        <input type="text" id="date" name="date" value="<?= h($values['date']) ?>" placeholder="12 Januari 2026">
      </div>
      <div>
        <label for="category">Kategori</label>
        <input type="text" id="category" name="category" value="<?= h($values['category']) ?>" placeholder="Kemitraan / Insight / Cerita Siswa">
      </div>
    </div>

    <label for="excerpt">Ringkasan Singkat (tampil di kartu listing) *</label>
    <textarea id="excerpt" name="excerpt" rows="2" required><?= h($values['excerpt']) ?></textarea>

    <label for="content">Isi Artikel Lengkap *</label>
    <p class="admin-hint">Pisahkan tiap paragraf dengan baris kosong (Enter dua kali).</p>
    <textarea id="content" name="content" rows="10" required><?= h(implode("\n\n", $values['content'])) ?></textarea>

    <label for="media_file">Upload Foto/Video (opsional)</label>
    <?php if (!empty($values['image'])): ?>
      <p class="admin-hint">Foto/video yang sekarang: <a href="../<?= h($values['image']) ?>" target="_blank"><?= h($values['image']) ?></a> — upload file baru buat ganti.</p>
    <?php endif; ?>
    <input type="file" id="media_file" name="media_file" accept="image/jpeg,image/png,image/webp,image/gif,video/mp4,video/webm,video/quicktime">
    <p class="admin-hint">Foto maks 8MB (jpg/png/webp/gif), video maks 60MB (mp4/webm/mov). Kalau upload file, ini dipakai dan mengabaikan kolom URL di bawah.</p>

    <div class="admin-form-row">
      <div>
        <label for="image">Atau isi URL/Path Foto Manual (opsional)</label>
        <input type="text" id="image" name="image" value="<?= h($values['image']) ?>" placeholder="assets/gallery/nama-file.jpg atau https://...">
      </div>
      <div>
        <label for="emoji">Emoji Ikon (kalau tanpa foto/video)</label>
        <input type="text" id="emoji" name="emoji" value="<?= h($values['emoji']) ?>">
      </div>
    </div>

    <div class="admin-form-row">
      <div>
        <label for="phA">Warna Gradient A (kalau tanpa foto)</label>
        <input type="text" id="phA" name="phA" value="<?= h($values['phA']) ?>">
      </div>
      <div>
        <label for="phB">Warna Gradient B (kalau tanpa foto)</label>
        <input type="text" id="phB" name="phB" value="<?= h($values['phB']) ?>">
      </div>
    </div>

    <button type="submit" class="btn-primary">Simpan Berita</button>
  </form>
</main>
</body>
</html>
