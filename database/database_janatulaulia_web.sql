CREATE DATABASE IF NOT EXISTS `janatulaulia_web`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `janatulaulia_web`;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 1;

-- -------------------------------------------------------------
-- 1. ADMIN USERS
-- Password wajib berupa hash dari password_hash() PHP.
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `admin_users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `role` VARCHAR(30) NOT NULL DEFAULT 'admin',
  `last_login` DATETIME NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_admin_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 2. PROJECT CATEGORIES
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `project_categories` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(80) NOT NULL,
  `slug` VARCHAR(100) NOT NULL,
  `description` TEXT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_project_categories_name` (`name`),
  UNIQUE KEY `uq_project_categories_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 3. PROJECTS / PORTOFOLIO
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `projects` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` INT UNSIGNED NOT NULL,
  `created_by` INT UNSIGNED NULL DEFAULT NULL,
  `title` VARCHAR(160) NOT NULL,
  `slug` VARCHAR(180) NOT NULL,
  `year` SMALLINT UNSIGNED NOT NULL,
  `summary` TEXT NOT NULL,
  `description` LONGTEXT NOT NULL,
  `cover_image` VARCHAR(255) NOT NULL,
  `status` ENUM('draft','published') NOT NULL DEFAULT 'draft',
  `published_at` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_projects_slug` (`slug`),
  KEY `idx_projects_category_id` (`category_id`),
  KEY `idx_projects_created_by` (`created_by`),
  KEY `idx_projects_status` (`status`),
  KEY `idx_projects_year` (`year`),
  CONSTRAINT `fk_projects_category`
    FOREIGN KEY (`category_id`) REFERENCES `project_categories` (`id`)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `fk_projects_admin`
    FOREIGN KEY (`created_by`) REFERENCES `admin_users` (`id`)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 4. PROJECT MEDIA / GALERI PORTOFOLIO
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `project_media` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `project_id` INT UNSIGNED NOT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `alt_text` VARCHAR(180) NULL DEFAULT NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_project_media_project_id` (`project_id`),
  CONSTRAINT `fk_project_media_project`
    FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 5. SERVICES / LAYANAN
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `services` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `created_by` INT UNSIGNED NULL DEFAULT NULL,
  `name` VARCHAR(120) NOT NULL,
  `slug` VARCHAR(150) NOT NULL,
  `description` TEXT NOT NULL,
  `deliverables` TEXT NOT NULL,
  `duration_label` VARCHAR(100) NULL DEFAULT NULL,
  `status` ENUM('draft','published') NOT NULL DEFAULT 'draft',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_services_slug` (`slug`),
  KEY `idx_services_created_by` (`created_by`),
  KEY `idx_services_status` (`status`),
  CONSTRAINT `fk_services_admin`
    FOREIGN KEY (`created_by`) REFERENCES `admin_users` (`id`)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 6. SERVICE FAQS
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `service_faqs` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `service_id` INT UNSIGNED NOT NULL,
  `question` VARCHAR(255) NOT NULL,
  `answer` TEXT NOT NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_service_faqs_service_id` (`service_id`),
  CONSTRAINT `fk_service_faqs_service`
    FOREIGN KEY (`service_id`) REFERENCES `services` (`id`)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 7. LEADS / PROJECT BRIEF
-- Lead tetap tersimpan bila layanan dihapus: service_id -> NULL.
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `leads` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `service_id` INT UNSIGNED NULL DEFAULT NULL,
  `name` VARCHAR(120) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `phone` VARCHAR(30) NOT NULL,
  `company` VARCHAR(150) NULL DEFAULT NULL,
  `goal` TEXT NULL,
  `target_audience` TEXT NULL,
  `scope` TEXT NULL,
  `notes` TEXT NULL,
  `budget_range` VARCHAR(80) NULL DEFAULT NULL,
  `deadline` DATE NULL DEFAULT NULL,
  `reference_url` VARCHAR(255) NULL DEFAULT NULL,
  `status` ENUM('New','Contacted','Qualified','Proposal','Won','Lost') NOT NULL DEFAULT 'New',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_leads_service_id` (`service_id`),
  KEY `idx_leads_status` (`status`),
  KEY `idx_leads_created_at` (`created_at`),
  KEY `idx_leads_email` (`email`),
  CONSTRAINT `fk_leads_service`
    FOREIGN KEY (`service_id`) REFERENCES `services` (`id`)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 8. ARTICLES / INSIGHT
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `articles` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `created_by` INT UNSIGNED NULL DEFAULT NULL,
  `title` VARCHAR(180) NOT NULL,
  `slug` VARCHAR(200) NOT NULL,
  `excerpt` TEXT NULL,
  `content` LONGTEXT NOT NULL,
  `cover_image` VARCHAR(255) NULL DEFAULT NULL,
  `tags` TEXT NULL,
  `status` ENUM('draft','published') NOT NULL DEFAULT 'draft',
  `published_at` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_articles_slug` (`slug`),
  KEY `idx_articles_created_by` (`created_by`),
  KEY `idx_articles_status` (`status`),
  KEY `idx_articles_published_at` (`published_at`),
  CONSTRAINT `fk_articles_admin`
    FOREIGN KEY (`created_by`) REFERENCES `admin_users` (`id`)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================
-- DATA AWAL UNTUK PENGUJIAN
-- Seluruh layanan contoh disimpan sebagai DRAFT agar tidak muncul
-- di website publik sebelum informasinya diperiksa dan disetujui.
-- =============================================================

INSERT IGNORE INTO `project_categories` (`name`, `slug`, `description`) VALUES
('Branding & Identity', 'branding-identity', 'Kategori contoh untuk proyek identitas brand.'),
('Desain Grafis', 'desain-grafis', 'Kategori contoh untuk proyek desain grafis.'),
('Konten Digital', 'konten-digital', 'Kategori contoh untuk proyek konten digital.');

INSERT IGNORE INTO `services`
(`name`, `slug`, `description`, `deliverables`, `duration_label`, `status`) VALUES
('Branding & Identity', 'branding-identity',
 'Contoh deskripsi layanan. Ganti dengan uraian resmi JanatulAulia Studio sebelum dipublikasikan.',
 'Contoh hasil: konsep identitas visual, logo, palet warna, dan tipografi. Sesuaikan dengan layanan sebenarnya.',
 NULL, 'draft'),
('Desain Grafis', 'desain-grafis',
 'Contoh deskripsi layanan desain grafis. Ganti dengan uraian resmi sebelum dipublikasikan.',
 'Contoh hasil: materi desain sesuai ruang lingkup yang disepakati bersama klien.',
 NULL, 'draft'),
('Konten Digital', 'konten-digital',
 'Contoh deskripsi layanan konten digital. Ganti dengan uraian resmi sebelum dipublikasikan.',
 'Contoh hasil: materi konten digital sesuai kebutuhan dan brief klien.',
 NULL, 'draft');

INSERT INTO `service_faqs` (`service_id`, `question`, `answer`, `sort_order`)
SELECT s.id,
       'Informasi apa yang perlu disiapkan sebelum konsultasi?',
       'Siapkan tujuan proyek, gambaran brand atau usaha, target audiens, referensi, serta kebutuhan utama. Jawaban ini merupakan contoh dan perlu disesuaikan oleh admin.',
       1
FROM `services` s
WHERE s.slug IN ('branding-identity', 'desain-grafis', 'konten-digital')
  AND NOT EXISTS (
    SELECT 1 FROM `service_faqs` f
    WHERE f.service_id = s.id
      AND f.question = 'Informasi apa yang perlu disiapkan sebelum konsultasi?'
  );

-- -------------------------------------------------------------
-- PEMBUATAN ADMIN PERTAMA
-- Jangan memakai password plaintext.
-- 1. Jalankan perintah ini di Command Prompt pada komputer XAMPP:
--    php -r "echo password_hash('GANTI_DENGAN_PASSWORD_KUAT', PASSWORD_DEFAULT), PHP_EOL;"
-- 2. Salin hasil hash, lalu jalankan query berikut di phpMyAdmin
--    setelah mengganti HASH_DARI_PHP dan email admin:
--
-- INSERT INTO admin_users (name, email, password_hash, role)
-- VALUES ('Administrator', 'admin@janatulaulia.local', 'HASH_DARI_PHP', 'admin');
--
-- Catatan: alamat email di atas hanya contoh lokal. Ganti dengan email
-- admin yang benar. Jangan mengirim hash/password ke website publik.
-- -------------------------------------------------------------

-- Pemeriksaan akhir: tampilkan daftar tabel database baru.
SHOW TABLES;
