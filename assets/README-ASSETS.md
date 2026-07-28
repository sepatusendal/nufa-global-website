# Panduan Media / Footage — NUFA Global Website

Website ini sudah disiapkan untuk video & foto dokumentasi asli. Sampai
file-nya diisi, semua slot media akan tampil sebagai kotak warna
(placeholder) yang aman — tidak ada gambar rusak/broken icon, dan hero
tetap terlihat premium lewat efek "aurora" gradient bergerak.

Tinggal simpan file dengan **nama persis** seperti di bawah ini ke folder
yang sesuai, lalu upload ulang ke Rumahweb. Tidak perlu edit kode apa pun.

---

## 0. Ganti Video Lewat URL (tanpa upload ulang file)

Buka `assets/site-config.js` — ada 3 field yang bisa diisi dengan **path lokal
atau URL video dari mana saja** (link langsung ke file .mp4, misalnya dari
CDN/hosting lain):

```js
window.SITE_CONFIG = {
  heroVideoUrl: 'assets/video/hero-loop.mp4',       // atau: 'https://domain-lain.com/video.mp4'
  heroVideoPoster: 'assets/hero-poster.jpg',
  companyProfileVideoUrl: 'assets/video/company-profile.mp4',
};
```

Tinggal edit nilainya, simpan, lalu upload ulang **file ini saja** ke
Rumahweb — tidak perlu sentuh HTML sama sekali. Berguna kalau kamu mau
gonta-ganti video hero dengan cepat tanpa upload file besar berkali-kali.

---

## 1. Video Hero (background sinematik di Beranda)

Taruh di: `assets/video/hero-loop.mp4`
Taruh juga: `assets/hero-poster.jpg` (gambar diam yang tampil sebelum video loading)

Rekomendasi konten (gaya Apple/Stripe/EdTech modern — sinematik, bukan raw footage):
- Cuplikan suasana kelas, siswa presentasi bahasa Inggris, guru native mengajar,
  online learning, dan momen kolaborasi/kerja sama internasional (jabat tangan,
  video call lintas negara, dll)
- Durasi 10–20 detik, di-loop (audio dibisukan otomatis, tidak perlu suara)
- Format MP4 (H.264), rasio 16:9, resolusi 1920x1080, ukuran idealnya di
  bawah 8MB (kompres dulu biar loading cepat di internet sekolah/rumah)
- Video akan otomatis diberi color grade gelap-navy (desaturate + darken)
  oleh CSS agar seragam dengan warna brand — jadi tidak masalah kalau
  footage asalnya terang/berwarna-warni

Juga ada tombol **"Tonton Video Profil"** di hero yang membuka video terpisah
lewat lightbox: taruh video company profile (durasi lebih panjang, boleh ada
narasi/musik) di `assets/video/company-profile.mp4`.

Kalau file-file ini belum ada, hero otomatis tampil dengan efek aurora
gradient bergerak (tanpa video) — tetap terlihat premium, tidak kosong/error.

---

## 2. Highlight Program di Beranda & Halaman Detail Program

Setiap program (English Course, English Camp, Immersion, Study Abroad,
Native English Speaker, Teacher Training) memakai **1 video + 3 foto**.
File yang sama dipakai di kartu highlight Beranda maupun di halaman detail
masing-masing program — jadi cukup siapkan satu set per program.

Nama file mengikuti pola: `program-<kode>.mp4` dan `program-<kode>-1.jpg`,
`-2.jpg`, `-3.jpg`. Kode per program:

| Program                     | Kode           | Video                                   | Foto (3)                                                                    |
|------------------------------|----------------|------------------------------------------|-------------------------------------------------------------------------------|
| English Course                | `course`       | `assets/video/program-course.mp4`         | `assets/gallery/program-course-1.jpg` / `-2.jpg` / `-3.jpg`                     |
| English Camp                   | `camp`         | `assets/video/program-camp.mp4`            | `assets/gallery/program-camp-1.jpg` / `-2.jpg` / `-3.jpg`                        |
| Immersion Program                | `immersion`  | `assets/video/program-immersion.mp4`        | `assets/gallery/program-immersion-1.jpg` / `-2.jpg` / `-3.jpg`                     |
| Study Abroad Australia             | `studyabroad`| `assets/video/program-studyabroad.mp4`       | `assets/gallery/program-studyabroad-1.jpg` / `-2.jpg` / `-3.jpg`                    |
| Native English Speaker               | `nes`      | `assets/video/program-nes.mp4`                | `assets/gallery/program-nes-1.jpg` / `-2.jpg` / `-3.jpg`                              |
| Teacher Training for English            | `teacher`| `assets/video/program-teacher.mp4`             | `assets/gallery/program-teacher-1.jpg` / `-2.jpg` / `-3.jpg`                            |

Rekomendasi: video 10–15 detik (MP4, 16:9, <8MB), foto landscape/square
1200px+ di sisi terpanjang, JPG kualitas 80%.

---

## 3. Galeri Umum (halaman Galeri terpisah)

Taruh di:
- `assets/video/gallery-01.mp4` → "Keseruan English Camp 2025"
- `assets/video/gallery-02.mp4` → "Cerita Siswa: Immersion Turki"
- `assets/gallery/immersion-01.jpg`, `class-01.jpg`, `teacher-01.jpg`,
  `camp-02.jpg`, `class-02.jpg`, `teacher-02.jpg`

Ini terpisah dari highlight per-program di atas — dipakai khusus di halaman
`gallery.html` untuk dokumentasi campuran semua kegiatan.

---

## 4. Menambah Item Galeri/Media Baru

Kalau mau tambah foto/video baru, cari blok `<div class="... gitem ...">`
di file HTML terkait, lalu copy salah satu blok yang ada dan ganti:

- `data-cat` → kategori (bebas, dipakai filter di halaman Galeri)
- `data-type` → `photo` atau `video`
- `data-title` → judul yang tampil di lightbox
- `data-cat-label` → label kategori yang tampil
- `data-src` → path file foto/video-nya

Kartu baru otomatis ikut sistem lightbox tanpa perlu sentuh file JS/CSS.

---

## 5. Kalau belum ada footage sama sekali

Website tetap tampil rapi dan premium:
- Hero: efek aurora gradient bergerak (bukan flat kosong)
- Highlight program & galeri: kartu gradient warna brand dengan ikon
  kamera 📷 atau clapper 🎬 sebagai penanda "slot media"

Jadi aman untuk di-launch duluan, footage-nya menyusul bertahap.
