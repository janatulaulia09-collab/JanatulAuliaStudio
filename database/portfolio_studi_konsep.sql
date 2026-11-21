-- Add transparent internal concept studies to the published service pages.
-- Safe to run more than once; existing projects are not overwritten.
UPDATE `services`
SET `description` = 'Kami merancang identitas visual yang konsisten agar karakter bisnis mudah dikenali di setiap titik kontak.',
    `deliverables` = CONCAT('Logo utama dan variasi', CHAR(10), 'Palet warna dan tipografi', CHAR(10), 'Panduan penggunaan identitas', CHAR(10), 'Aplikasi visual pada media pilihan')
WHERE `slug` = 'branding-identity'
  AND `description` LIKE 'Contoh deskripsi%';

UPDATE `services`
SET `description` = 'Kami membuat materi visual yang terarah dan siap digunakan untuk promosi, komunikasi, dan kebutuhan brand.',
    `deliverables` = CONCAT('Konsep dan arah visual', CHAR(10), 'Materi desain sesuai kebutuhan digital atau cetak', CHAR(10), 'File final siap publikasi', CHAR(10), 'Adaptasi ukuran dan format')
WHERE `slug` = 'desain-grafis'
  AND `description` LIKE 'Contoh deskripsi%';

UPDATE `services`
SET `description` = 'Kami menyusun konten digital yang konsisten, relevan, dan selaras dengan tujuan komunikasi bisnis.',
    `deliverables` = CONCAT('Pilar dan tema konten', CHAR(10), 'Kalender konten', CHAR(10), 'Arahan visual untuk feed, story, atau reels', CHAR(10), 'Copy dan materi sesuai ruang lingkup')
WHERE `slug` = 'konten-digital'
  AND `description` LIKE 'Contoh deskripsi%';

INSERT INTO `projects`
  (`category_id`, `created_by`, `title`, `slug`, `year`, `summary`, `description`, `cover_image`, `status`, `published_at`)
SELECT c.`id`, NULL, 'Rona Botanika — Identitas Visual', 'studi-konsep-rona-botanika', 2026,
       'Studi konsep internal: identitas botani dengan karakter hangat, organik, dan mudah dikenali.',
       'Studi konsep internal JanatulAulia Studio, bukan proyek klien. Eksplorasi ini merangkai logo, palet hijau hutan dan krem, tipografi editorial, serta aplikasi identitas pada kemasan untuk menunjukkan arah visual layanan Branding & Identity.',
       'assets/images/portfolio-branding-concept.svg', 'published', NOW()
FROM `project_categories` c
JOIN `services` s ON s.`slug` = c.`slug`
WHERE s.`slug` = 'branding-identity'
  AND s.`status` = 'published'
  AND NOT EXISTS (SELECT 1 FROM `projects` p WHERE p.`slug` = 'studi-konsep-rona-botanika')
LIMIT 1;

INSERT INTO `projects`
  (`category_id`, `created_by`, `title`, `slug`, `year`, `summary`, `description`, `cover_image`, `status`, `published_at`)
SELECT c.`id`, NULL, 'Ruang Rasa — Kampanye Grafis', 'studi-konsep-ruang-rasa', 2026,
       'Studi konsep internal: sistem poster dan materi kampanye untuk merayakan cerita rasa lokal.',
       'Studi konsep internal JanatulAulia Studio, bukan proyek klien. Sistem grafis ini mengeksplorasi hierarki tipografi, bentuk geometris, dan palet hangat untuk poster, materi sosial, serta kebutuhan cetak dalam layanan Desain Grafis.',
       'assets/images/portfolio-desain-grafis-concept.svg', 'published', NOW()
FROM `project_categories` c
JOIN `services` s ON s.`slug` = c.`slug`
WHERE s.`slug` = 'desain-grafis'
  AND s.`status` = 'published'
  AND NOT EXISTS (SELECT 1 FROM `projects` p WHERE p.`slug` = 'studi-konsep-ruang-rasa')
LIMIT 1;

INSERT INTO `projects`
  (`category_id`, `created_by`, `title`, `slug`, `year`, `summary`, `description`, `cover_image`, `status`, `published_at`)
SELECT c.`id`, NULL, 'Pagi Studio — Kalender Konten Digital', 'studi-konsep-pagi-studio', 2026,
       'Studi konsep internal: rangkaian konten media sosial dengan cerita brand yang konsisten.',
       'Studi konsep internal JanatulAulia Studio, bukan proyek klien. Rangkaian ini memperlihatkan arah kalender konten, format feed dan story, serta pendekatan visual yang konsisten untuk layanan Konten Digital.',
       'assets/images/portfolio-konten-digital-concept.svg', 'published', NOW()
FROM `project_categories` c
JOIN `services` s ON s.`slug` = c.`slug`
WHERE s.`slug` = 'konten-digital'
  AND s.`status` = 'published'
  AND NOT EXISTS (SELECT 1 FROM `projects` p WHERE p.`slug` = 'studi-konsep-pagi-studio')
LIMIT 1;

INSERT INTO `projects`
  (`category_id`, `created_by`, `title`, `slug`, `year`, `summary`, `description`, `cover_image`, `status`, `published_at`)
SELECT c.`id`, NULL, 'Kebun Kota — Identitas Urban Gardening', 'studi-konsep-kebun-kota', 2026,
       'Studi konsep internal: identitas urban gardening yang mengajak orang menanam dari rumah.',
       'Studi konsep internal JanatulAulia Studio, bukan proyek klien. Sistem identitas ini mengeksplorasi logo, label benih, palet hijau dan tanah, serta kemasan sederhana untuk menggambarkan layanan Branding & Identity.',
       'assets/images/portfolio-branding-kebun-kota.svg', 'published', NOW()
FROM `project_categories` c
JOIN `services` s ON s.`slug` = c.`slug`
WHERE s.`slug` = 'branding-identity'
  AND s.`status` = 'published'
  AND NOT EXISTS (SELECT 1 FROM `projects` p WHERE p.`slug` = 'studi-konsep-kebun-kota')
LIMIT 1;

INSERT INTO `projects`
  (`category_id`, `created_by`, `title`, `slug`, `year`, `summary`, `description`, `cover_image`, `status`, `published_at`)
SELECT c.`id`, NULL, 'Sela — Identitas Perawatan Kulit', 'studi-konsep-sela-skincare', 2026,
       'Studi konsep internal: identitas perawatan kulit dengan karakter tenang, bersih, dan personal.',
       'Studi konsep internal JanatulAulia Studio, bukan proyek klien. Eksplorasi ini mencakup wordmark, warna lembut, label kemasan, serta arah tipografi untuk menunjukkan pendekatan identitas yang konsisten.',
       'assets/images/portfolio-branding-sela-skincare.svg', 'published', NOW()
FROM `project_categories` c
JOIN `services` s ON s.`slug` = c.`slug`
WHERE s.`slug` = 'branding-identity'
  AND s.`status` = 'published'
  AND NOT EXISTS (SELECT 1 FROM `projects` p WHERE p.`slug` = 'studi-konsep-sela-skincare')
LIMIT 1;

INSERT INTO `projects`
  (`category_id`, `created_by`, `title`, `slug`, `year`, `summary`, `description`, `cover_image`, `status`, `published_at`)
SELECT c.`id`, NULL, 'Musim Pangan — Kampanye Pasar Lokal', 'studi-konsep-musim-pangan', 2026,
       'Studi konsep internal: sistem poster dan materi kampanye untuk pasar pangan lokal.',
       'Studi konsep internal JanatulAulia Studio, bukan proyek klien. Arah grafis ini menyatukan tipografi editorial, ilustrasi bahan pangan, dan palet alami untuk kebutuhan poster, market kit, serta media sosial.',
       'assets/images/portfolio-desain-grafis-musim-pangan.svg', 'published', NOW()
FROM `project_categories` c
JOIN `services` s ON s.`slug` = c.`slug`
WHERE s.`slug` = 'desain-grafis'
  AND s.`status` = 'published'
  AND NOT EXISTS (SELECT 1 FROM `projects` p WHERE p.`slug` = 'studi-konsep-musim-pangan')
LIMIT 1;

INSERT INTO `projects`
  (`category_id`, `created_by`, `title`, `slug`, `year`, `summary`, `description`, `cover_image`, `status`, `published_at`)
SELECT c.`id`, NULL, 'Ruang Baca — Poster Malam Cerita', 'studi-konsep-ruang-baca', 2026,
       'Studi konsep internal: poster acara literasi dengan sistem visual yang kuat dan mudah diterapkan.',
       'Studi konsep internal JanatulAulia Studio, bukan proyek klien. Sistem poster ini mengeksplorasi tipografi kontras, ilustrasi buku, dan format turunan untuk materi acara serta editorial.',
       'assets/images/portfolio-desain-grafis-ruang-baca.svg', 'published', NOW()
FROM `project_categories` c
JOIN `services` s ON s.`slug` = c.`slug`
WHERE s.`slug` = 'desain-grafis'
  AND s.`status` = 'published'
  AND NOT EXISTS (SELECT 1 FROM `projects` p WHERE p.`slug` = 'studi-konsep-ruang-baca')
LIMIT 1;

INSERT INTO `projects`
  (`category_id`, `created_by`, `title`, `slug`, `year`, `summary`, `description`, `cover_image`, `status`, `published_at`)
SELECT c.`id`, NULL, 'Tanah Kita — Kalender Cerita Kebun', 'studi-konsep-tanah-kita', 2026,
       'Studi konsep internal: rangkaian konten digital yang mengangkat cerita kebun dan hasil lokal.',
       'Studi konsep internal JanatulAulia Studio, bukan proyek klien. Contoh ini memperlihatkan pilar konten, kalender publikasi, format feed dan story, serta gaya visual yang konsisten.',
       'assets/images/portfolio-konten-digital-tanah-kita.svg', 'published', NOW()
FROM `project_categories` c
JOIN `services` s ON s.`slug` = c.`slug`
WHERE s.`slug` = 'konten-digital'
  AND s.`status` = 'published'
  AND NOT EXISTS (SELECT 1 FROM `projects` p WHERE p.`slug` = 'studi-konsep-tanah-kita')
LIMIT 1;

INSERT INTO `projects`
  (`category_id`, `created_by`, `title`, `slug`, `year`, `summary`, `description`, `cover_image`, `status`, `published_at`)
SELECT c.`id`, NULL, 'Loka Rasa — Kampanye Menu Musiman', 'studi-konsep-loka-rasa', 2026,
       'Studi konsep internal: kampanye media sosial untuk memperkenalkan menu musiman dan ceritanya.',
       'Studi konsep internal JanatulAulia Studio, bukan proyek klien. Rangkaian visual ini menunjukkan adaptasi satu ide kampanye ke format feed, story, dan promosi digital.',
       'assets/images/portfolio-konten-digital-loka-rasa.svg', 'published', NOW()
FROM `project_categories` c
JOIN `services` s ON s.`slug` = c.`slug`
WHERE s.`slug` = 'konten-digital'
  AND s.`status` = 'published'
  AND NOT EXISTS (SELECT 1 FROM `projects` p WHERE p.`slug` = 'studi-konsep-loka-rasa')
LIMIT 1;
