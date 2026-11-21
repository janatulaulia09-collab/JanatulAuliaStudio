INSERT INTO articles
  (title, slug, excerpt, content, cover_image, tags, status, published_at)
SELECT
  'Memulai Identitas Brand yang Berkarakter',
  'memulai-identitas-brand-yang-berkarakter',
  'Cara menyusun fondasi identitas visual agar brand terlihat konsisten, relevan, dan mudah dikenali.',
  'Identitas brand yang kuat tidak lahir hanya dari logo. Ia mulai dari pemahaman tujuan, audiens, nilai, dan karakter yang ingin dirasakan oleh orang lain.\\n\\nMulailah dengan menjawab pertanyaan mendasar: siapa brand ini, untuk siapa brand ini hadir, dan apa yang membuatnya berbeda. Dari jawaban tersebut, identitas visual dapat diterjemahkan menjadi warna, tipografi, bahasa visual, dan pola komunikasi yang konsisten.',
  'assets/images/portfolio-branding-kebun-kota.svg',
  'branding, identitas visual, strategi',
  'published',
  NOW()
WHERE NOT EXISTS (SELECT 1 FROM articles WHERE slug='memulai-identitas-brand-yang-berkarakter');

INSERT INTO articles
  (title, slug, excerpt, content, cover_image, tags, status, published_at)
SELECT
  'Konten Sosial Media yang Lebih Bermakna',
  'konten-sosial-media-yang-lebih-bermakna',
  'Panduan praktis membangun konten digital yang tetap konsisten tanpa kehilangan suara brand.',
  'Konten yang efektif bukan sekadar hadir secara rutin. Setiap konten perlu memiliki peran dalam perjalanan audiens, mulai dari mengenal brand hingga mempertimbangkan dan memilih produk atau layanan.\\n\\nSusun pilar konten yang mencerminkan keahlian, cerita, edukasi, dan bukti hasil. Dengan ritme yang terukur, brand dapat tampil konsisten sekaligus tetap manusiawi dan relevan.',
  'assets/images/portfolio-konten-digital-loka-rasa.svg',
  'konten digital, sosial media, strategi',
  'published',
  NOW()
WHERE NOT EXISTS (SELECT 1 FROM articles WHERE slug='konten-sosial-media-yang-lebih-bermakna');

INSERT INTO articles
  (title, slug, excerpt, content, cover_image, tags, status, published_at)
SELECT
  'Mengubah Brief Proyek Menjadi Arahan Kreatif',
  'mengubah-brief-proyek-menjadi-arahan-kreatif',
  'Kenapa informasi yang jelas di awal membantu desain berjalan lebih terarah dan efisien.',
  'Project Brief yang baik membantu tim memahami tujuan, audiens, batasan, dan hasil yang diharapkan. Informasi ini mengurangi asumsi dan membuat proses kreatif memiliki arah yang lebih jelas.\\n\\nSaat menjawab brief, jelaskan masalah yang ingin diselesaikan, bukan hanya hasil visual yang diinginkan. Tambahkan referensi, target audiens, jadwal, dan data yang relevan agar keputusan desain dapat dibuat dengan argumentasi yang kuat.',
  'assets/images/portfolio-desain-grafis-musim-pangan.svg',
  'project brief, proses desain, kolaborasi',
  'published',
  NOW()
WHERE NOT EXISTS (SELECT 1 FROM articles WHERE slug='mengubah-brief-proyek-menjadi-arahan-kreatif');
