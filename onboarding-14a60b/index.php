<?php
declare(strict_types=1);
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/includes/auth.php';
require_login();

$memos = read_memos();
$employeeName = $_SESSION['employee_name'] ?? '';
$employeeFirstName = trim((string) explode(' ', (string) $employeeName)[0]);
$employeeEmail = $_SESSION['employee_email'] ?? '';
$emailUser = $employeeEmail !== '' ? $employeeEmail : '[nama]@nufaglobaledu.com';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title>Onboarding — NUFA Global Education</title>
<link rel="icon" href="../assets/logo.png" type="image/png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Archivo:wght@600;700;800;900&family=Manrope:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/onboard.css">
</head>
<body>

<header class="ob-topbar">
  <div class="ob-topbar-inner">
    <img src="../assets/logo.png" alt="NUFA Global Education" class="ob-topbar-logo">
    <span class="ob-topbar-label">Onboarding</span>
    <div class="ob-topbar-right">
      <span><?= h($employeeName) ?></span>
      <a href="logout.php" class="ob-btn ob-btn-ghost">Logout</a>
    </div>
  </div>
</header>

<section class="ob-hero">
  <div class="ob-hero-inner">
    <div class="ob-eyebrow">Employee Onboarding</div>
    <h1>Selamat bergabung,<br><em><?= h($employeeFirstName !== '' ? $employeeFirstName : 'Team NUFA') ?>.</em></h1>
    <p>Semua yang perlu kamu tahu di minggu pertama — profil perusahaan, cara connect email kerja, dan dokumen internal — ada di satu halaman ini.</p>
  </div>
</section>

<nav class="ob-toc">
  <a href="#company">01 · Company Knowledge</a>
  <a href="#email">02 · Setup Email Kerja</a>
  <a href="#memo">03 · Memo Internal</a>
</nav>

<main class="ob-main">

  <section id="company" class="ob-section">
    <div class="ob-section-num">01</div>
    <div class="ob-section-head">
      <div class="ob-eyebrow" style="color:var(--indigo);">Company Knowledge</div>
      <h2>Kenalan sama NUFA Global Education</h2>
      <p><strong>PT NUFA Global Education</strong> adalah perusahaan pendidikan internasional yang membangun ekosistem pendidikan global — bahasa Inggris, pengalaman belajar internasional, pengembangan guru, dan kolaborasi institusi lintas negara.</p>
    </div>

    <div class="ob-section-body">
      <div class="ob-vm-grid">
        <div class="ob-vm-card vision">
          <span class="ob-vm-tag">Visi</span>
          <h4>Perusahaan pendidikan internasional terdepan</h4>
          <p>Menjadi perusahaan pendidikan internasional terdepan di Indonesia yang menghubungkan sekolah, pendidik, siswa, dan institusi global melalui solusi pendidikan yang inovatif, berkelanjutan, dan berdampak nyata.</p>
        </div>
        <div class="ob-vm-card mission">
          <span class="ob-vm-tag">Misi</span>
          <h4>5 komitmen utama kami</h4>
          <ul>
            <li>Membantu sekolah Indonesia meningkatkan kualitas pendidikan bertaraf internasional</li>
            <li>Mengembangkan kemampuan komunikasi bahasa Inggris secara aktif &amp; aplikatif</li>
            <li>Memberikan pengalaman belajar global melalui program internasional</li>
            <li>Menjembatani kolaborasi institusi pendidikan Indonesia dengan berbagai negara</li>
            <li>Mengembangkan teknologi pendidikan yang mendukung transformasi pembelajaran</li>
          </ul>
        </div>
      </div>

      <div class="ob-grow-band">
        <div class="ob-grow-letters"><span>G</span><span>R</span><span>O</span><span>W</span></div>
        <div class="ob-grow-head">
          <div class="ob-eyebrow">Core Values</div>
          <h3>Nilai yang tumbuh di setiap program kami</h3>
        </div>
        <div class="ob-grow-grid">
          <div class="ob-grow-card"><div class="ob-grow-letter">G</div><h4>Global Mindset</h4><p>Berpikir global, tetap menghargai nilai-nilai lokal.</p></div>
          <div class="ob-grow-card"><div class="ob-grow-letter">R</div><h4>Respect</h4><p>Menghormati keberagaman budaya, agama, cara belajar.</p></div>
          <div class="ob-grow-card"><div class="ob-grow-letter">O</div><h4>Opportunity</h4><p>Menciptakan peluang bagi siswa, guru, dan sekolah.</p></div>
          <div class="ob-grow-card"><div class="ob-grow-letter">W</div><h4>Wisdom</h4><p>Belajar sepanjang hayat dengan integritas.</p></div>
        </div>
      </div>

      <h3 class="ob-div-label">Tujuh Divisi Bisnis</h3>
      <div class="ob-div-grid">
        <div class="ob-div-card"><h5><span class="dotc"></span>1. School Partnership Division</h5><p>Membangun kerja sama dengan sekolah, yayasan, universitas, dan institusi pendidikan.</p></div>
        <div class="ob-div-card"><h5><span class="dotc"></span>2. Academic Division</h5><p>Mengembangkan kualitas akademik seluruh program NUFA Global.</p></div>
        <div class="ob-div-card"><h5><span class="dotc"></span>3. International Program Division</h5><p>Mengelola program internasional dari immersion hingga student exchange.</p></div>
        <div class="ob-div-card"><h5><span class="dotc"></span>4. Native English Speaker Division</h5><p>Mengelola proses Native English Speaker dari rekrutmen hingga evaluasi.</p></div>
        <div class="ob-div-card"><h5><span class="dotc"></span>5. Marketing &amp; Partnership Division</h5><p>Membangun kesadaran merek dan hubungan jangka panjang dengan mitra.</p></div>
        <div class="ob-div-card"><h5><span class="dotc"></span>6. Technology Division</h5><p>Membangun ekosistem digital yang menghubungkan semua pemangku kepentingan.</p></div>
        <div class="ob-div-card"><h5><span class="dotc"></span>7. Corporate Support Division</h5><p>Menopang operasional perusahaan — HR, Finance, Legal, Compliance.</p></div>
      </div>
    </div>
  </section>

  <section id="email" class="ob-section">
    <div class="ob-section-num">02</div>
    <div class="ob-section-head">
      <div class="ob-eyebrow" style="color:var(--coral);">Setup Email Kerja</div>
      <h2>Sambungkan email kantor ke Gmail app</h2>
      <p>Email kerja kamu di-hosting di domain <strong>nufaglobaledu.com</strong>, bukan Google Workspace — jadi disambungkan lewat IMAP/SMTP, bukan "Sign in with Google". ± 3 menit selesai.</p>
    </div>

    <div class="ob-section-body">
      <div class="ob-cred-card">
        <div>
          <span class="ob-vm-tag">Alamat Email Kamu</span>
          <div class="ob-cred-email" id="ob-email-value"><?= h($emailUser) ?></div>
          <div class="ob-cred-note">Password diberikan terpisah oleh admin/IT — hubungi admin kalau belum dapat.</div>
        </div>
        <button type="button" class="ob-copy-btn" data-copy="<?= h($emailUser) ?>">Copy Email</button>
      </div>

      <div class="ob-cta-row">
        <a href="https://mail.google.com/mail/u/0/#settings/accounts" target="_blank" rel="noopener" class="ob-btn ob-btn-primary">📧 Buka Gmail → Tambah Akun</a>
        <a href="https://webmail.nufaglobaledu.com" target="_blank" rel="noopener" class="ob-btn ob-btn-indigo">🌐 Atau Pakai Webmail Langsung</a>
      </div>
      <p class="ob-hint" style="margin-bottom:32px;">Tombol pertama langsung membuka halaman Gmail "Add another account" — tinggal ikuti step di bawah. Tombol kedua kalau tidak mau install apa-apa, cukup buka email lewat browser tanpa setup.</p>

      <div class="ob-steps">
        <div class="ob-step">
          <div class="ob-step-title">Klik tombol "Buka Gmail → Tambah Akun" di atas</div>
          <p>Atau manual di app Gmail: Menu ☰ → Settings → Add account → Other.</p>
        </div>
        <div class="ob-step">
          <div class="ob-step-title">Masukkan alamat email &amp; pilih tipe akun</div>
          <p>Ketik <code><?= h($emailUser) ?></code>, lalu pilih <strong>Personal (IMAP)</strong>.</p>
        </div>
        <div class="ob-step">
          <div class="ob-step-title">Isi Incoming server (IMAP)</div>
          <div class="ob-server-grid">
            <div class="ob-server-row"><b>Server</b><code>mail.nufaglobaledu.com</code><button type="button" class="ob-copy-btn light" data-copy="mail.nufaglobaledu.com">Copy</button></div>
            <div class="ob-server-row"><b>Port</b><code>993</code><button type="button" class="ob-copy-btn light" data-copy="993">Copy</button></div>
            <div class="ob-server-row"><b>Security</b><code>SSL/TLS</code></div>
          </div>
        </div>
        <div class="ob-step">
          <div class="ob-step-title">Isi Outgoing server (SMTP)</div>
          <div class="ob-server-grid">
            <div class="ob-server-row"><b>Server</b><code>mail.nufaglobaledu.com</code><button type="button" class="ob-copy-btn light" data-copy="mail.nufaglobaledu.com">Copy</button></div>
            <div class="ob-server-row"><b>Port</b><code>465</code><button type="button" class="ob-copy-btn light" data-copy="465">Copy</button></div>
            <div class="ob-server-row"><b>Security</b><code>SSL/TLS</code></div>
          </div>
        </div>
        <div class="ob-step">
          <div class="ob-step-title">Masukkan password &amp; tap Sign in / Next</div>
          <p>Gmail akan sync email dari server perusahaan — selesai, email kantor sudah bisa dibuka dari app Gmail kamu.</p>
        </div>
      </div>

      <p class="ob-hint">Stuck di tengah jalan? Screenshot langkah yang error dan kirim ke admin/IT — jangan coba-coba ganti setting lain.</p>
    </div>
  </section>

  <section id="memo" class="ob-section" style="padding-bottom:40px;">
    <div class="ob-section-num">03</div>
    <div class="ob-section-head">
      <div class="ob-eyebrow" style="color:var(--gold);">Memo Internal</div>
      <h2>Dokumen internal karyawan</h2>
      <p>SOP, kebijakan, dan dokumen internal lain yang perlu kamu baca. File ini tidak bisa diakses publik — hanya bisa didownload setelah login ke portal ini.</p>
    </div>

    <div class="ob-section-body">
      <?php if (!$memos): ?>
        <div class="ob-empty">Belum ada memo yang diupload admin.</div>
      <?php else: ?>
        <div class="ob-memo-list">
          <?php foreach ($memos as $m): ?>
            <?php $ext = strtoupper((string) pathinfo((string) ($m['original_name'] ?? ''), PATHINFO_EXTENSION)); ?>
            <div class="ob-memo-row">
              <div class="ob-memo-icon"><?= h($ext ?: 'DOC') ?></div>
              <div class="ob-memo-info">
                <div class="ob-memo-title"><?= h($m['title'] ?? '') ?></div>
                <div class="ob-memo-date"><?= h($m['uploaded_at'] ?? '') ?></div>
              </div>
              <a href="download.php?file=<?= urlencode($m['stored_name'] ?? '') ?>" class="ob-btn ob-btn-primary">Download</a>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </section>

</main>

<script>
document.querySelectorAll('.ob-copy-btn').forEach(function (btn) {
  btn.addEventListener('click', function () {
    var text = btn.getAttribute('data-copy') || '';
    navigator.clipboard.writeText(text).then(function () {
      var original = btn.textContent;
      btn.textContent = 'Copied ✓';
      setTimeout(function () { btn.textContent = original; }, 1500);
    });
  });
});
</script>
</body>
</html>
