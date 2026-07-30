<?php
declare(strict_types=1);
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/includes/auth.php';
require_login();

$events = read_events();
$editSlug = $_GET['slug'] ?? null;
$existing = $editSlug ? find_by_slug($events, (string) $editSlug) : null;
$isEdit = $existing !== null;
$errors = [];

$values = $existing ?: [
    'slug' => '',
    'date' => '',
    'title' => '',
    'location' => '',
    'duration' => '',
    'excerpt' => '',
    'phA' => '#363F72',
    'phB' => '#4B60AC',
    'emoji' => '📅',
    'image' => '',
    'ctaLabel' => 'Daftar sekarang →',
    'ctaUrl' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $title = trim((string) ($_POST['title'] ?? ''));
    $date = trim((string) ($_POST['date'] ?? ''));
    $location = trim((string) ($_POST['location'] ?? ''));
    $duration = trim((string) ($_POST['duration'] ?? ''));
    $excerpt = trim((string) ($_POST['excerpt'] ?? ''));
    $phA = trim((string) ($_POST['phA'] ?? '#363F72'));
    $phB = trim((string) ($_POST['phB'] ?? '#4B60AC'));
    $emoji = trim((string) ($_POST['emoji'] ?? '📅'));
    $image = trim((string) ($_POST['image'] ?? ''));
    $ctaLabel = trim((string) ($_POST['ctaLabel'] ?? 'Daftar sekarang →'));
    $ctaUrl = trim((string) ($_POST['ctaUrl'] ?? ''));
    $slugInput = trim((string) ($_POST['slug'] ?? ''));

    if ($title === '') $errors[] = 'Judul wajib diisi.';
    if ($excerpt === '') $errors[] = 'Deskripsi singkat wajib diisi.';

    $originalSlug = $isEdit ? $existing['slug'] : null;
    $baseSlug = slugify($slugInput !== '' ? $slugInput : $title);
    $finalSlug = unique_slug($events, $baseSlug, $originalSlug);

    $values = compact('date', 'title', 'location', 'duration', 'excerpt', 'phA', 'phB', 'emoji', 'image', 'ctaLabel', 'ctaUrl');
    $values['slug'] = $finalSlug;

    if (!$errors) {
        $newEntry = [
            'slug' => $finalSlug,
            'date' => $date,
            'title' => $title,
            'location' => $location,
            'duration' => $duration,
            'excerpt' => $excerpt,
            'phA' => $phA ?: '#363F72',
            'phB' => $phB ?: '#4B60AC',
            'emoji' => $emoji ?: '📅',
            'image' => $image,
            'ctaLabel' => $ctaLabel ?: 'Daftar sekarang →',
            'ctaUrl' => $ctaUrl,
        ];

        if ($isEdit) {
            foreach ($events as $i => $e) {
                if ($e['slug'] === $originalSlug) {
                    $events[$i] = $newEntry;
                    break;
                }
            }
        } else {
            array_unshift($events, $newEntry);
        }

        write_events($events);
        $_SESSION['flash'] = ['type' => 'success', 'message' => $isEdit ? 'Event berhasil diperbarui.' : 'Event baru berhasil ditambahkan.'];
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
<title><?= $isEdit ? 'Edit' : 'Tambah' ?> Event — Admin NUFA Global</title>
<link rel="icon" href="../assets/logo.png" type="image/png">
<link rel="stylesheet" href="assets/admin.css">
</head>
<body>
<header class="admin-header">
  <div class="admin-header-inner">
    <img src="../assets/logo.png" alt="NUFA Global Education" class="admin-logo">
    <span class="admin-title"><?= $isEdit ? 'Edit Event' : 'Tambah Event' ?></span>
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

  <form method="post" class="admin-form">
    <?= csrf_field() ?>

    <label for="title">Nama Event *</label>
    <input type="text" id="title" name="title" value="<?= h($values['title']) ?>" required>

    <label for="slug">Slug — kosongkan biar otomatis dari judul</label>
    <input type="text" id="slug" name="slug" value="<?= h($values['slug']) ?>" placeholder="contoh: nama-event-ini">

    <div class="admin-form-row">
      <div>
        <label for="date">Tanggal</label>
        <input type="text" id="date" name="date" value="<?= h($values['date']) ?>" placeholder="20 Februari 2026">
      </div>
      <div>
        <label for="location">Lokasi</label>
        <input type="text" id="location" name="location" value="<?= h($values['location']) ?>" placeholder="Jakarta / Online">
      </div>
      <div>
        <label for="duration">Durasi</label>
        <input type="text" id="duration" name="duration" value="<?= h($values['duration']) ?>" placeholder="1 Hari / 2 Jam">
      </div>
    </div>

    <label for="excerpt">Deskripsi Singkat *</label>
    <textarea id="excerpt" name="excerpt" rows="2" required><?= h($values['excerpt']) ?></textarea>

    <div class="admin-form-row">
      <div>
        <label for="ctaLabel">Teks Tombol CTA</label>
        <input type="text" id="ctaLabel" name="ctaLabel" value="<?= h($values['ctaLabel']) ?>">
      </div>
      <div>
        <label for="ctaUrl">Link Tombol CTA (opsional)</label>
        <input type="text" id="ctaUrl" name="ctaUrl" value="<?= h($values['ctaUrl']) ?>" placeholder="https://wa.me/... atau link pendaftaran">
      </div>
    </div>
    <p class="admin-hint">Kalau link CTA dikosongkan, tombol cuma tampil sebagai teks (tidak bisa diklik).</p>

    <div class="admin-form-row">
      <div>
        <label for="image">URL/Path Foto (opsional)</label>
        <input type="text" id="image" name="image" value="<?= h($values['image']) ?>" placeholder="assets/gallery/nama-file.jpg">
      </div>
      <div>
        <label for="emoji">Emoji Ikon (kalau tanpa foto)</label>
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

    <button type="submit" class="btn-primary">Simpan Event</button>
  </form>
</main>
</body>
</html>
