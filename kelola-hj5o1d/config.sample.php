<?php
// Salin file ini menjadi "config.php" (di folder yang sama) lalu isi kredensial kamu sendiri.
// JANGAN commit config.php ke git — sudah dimasukkan ke .gitignore.
//
// Cara bikin password_hash: jalankan di terminal (butuh PHP terinstall lokal)
//   php -r "echo password_hash('password_kamu_disini', PASSWORD_DEFAULT), PHP_EOL;"
// lalu copy hasilnya (diawali $2y$...) ke bawah ini.

return [
    'username' => 'admin',
    'password_hash' => '$2y$10$REPLACE_WITH_YOUR_OWN_HASH_FROM_php_-r_ABOVE',
];
