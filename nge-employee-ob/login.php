<?php

declare(strict_types=1);
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/includes/auth.php';

if (is_logged_in()) {
  redirect('index.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verify_csrf();
  $username = trim((string) ($_POST['username'] ?? ''));
  $password = (string) ($_POST['password'] ?? '');
  if (attempt_login($username, $password)) {
    redirect('index.php');
  }
  $error = 'Username atau password salah.';
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <title>Portal Onboarding — NUFA Global Education</title>
  <link rel="icon" href="../assets/logo.png" type="image/png">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@600;700;800;900&family=Manrope:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/onboard.css">
</head>

<body class="ob-login-body">
  <div class="ob-login-card">
    <img src="../assets/logo.png" alt="NUFA Global Education" class="ob-login-logo">
    <div class="ob-login-eyebrow">Employee Portal</div>
    <h1>Portal Onboarding</h1>
    <p>Login pakai username &amp; password yang diberikan HR/admin kamu.</p>
    <?php if ($error): ?>
      <div class="ob-alert" style="margin-bottom:16px;"><?= h($error) ?></div>
    <?php endif; ?>
    <form method="post" novalidate>
      <?= csrf_field() ?>
      <label for="username">Username</label>
      <input type="text" id="username" name="username" autocomplete="username" required autofocus placeholder="[yourname]@nufaglobaledu.com">
      <label for="password">Password</label>
      <input type="password" id="password" name="password" autocomplete="current-password" required placeholder="••••••••">
      <button type="submit" class="ob-btn ob-btn-primary">Masuk →</button>
    </form>
  </div>
</body>

</html>