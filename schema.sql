-- ==========================================================
-- The N-NEWS Database Schema for thenewsdb
-- ==========================================================

CREATE DATABASE IF NOT EXISTS `thenewsdb` 
DEFAULT CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `thenewsdb`;

-- 1. Create Admin Table (Used by backend/ application)
CREATE TABLE IF NOT EXISTS `admin` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(150) NOT NULL DEFAULT 'Admin',
    `username` VARCHAR(100) NOT NULL UNIQUE,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `password` VARCHAR(255) NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `role` VARCHAR(50) DEFAULT 'admin',
    `is_active` TINYINT(1) DEFAULT 1,
    `last_login_at` DATETIME NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed default admin user (Username: admin, Password: admin123, MD5: 0192023a7bbd73250516f069df18b500)
INSERT INTO `admin` (`name`, `username`, `email`, `password`, `password_hash`, `role`, `is_active`)
VALUES ('Chief Editor', 'admin', 'admin@gmail.com', 'admin123', '0192023a7bbd73250516f069df18b500', 'admin', 1)
ON DUPLICATE KEY UPDATE `username` = VALUES(`username`);

-- 2. Create Categories Table
CREATE TABLE IF NOT EXISTS `categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name_malayalam` VARCHAR(255) NOT NULL,
    `name_english` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(191) NOT NULL UNIQUE,
    `description` TEXT NULL,
    `display_order` INT DEFAULT 0,
    `status` ENUM('active', 'inactive') DEFAULT 'active',
    `home_page` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_home_page` (`home_page`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert 12 Malayalam & English Categories
INSERT INTO `categories` (`name_malayalam`, `name_english`, `slug`, `description`, `display_order`, `status`, `home_page`)
VALUES
('പ്രാദേശിക വാർത്തകൾ', 'Local News', 'local', 'ഗ്രാമ-പഞ്ചായത്ത് പ്രാദേശിക വാർത്തകൾ', 1, 'active', 1),
('ജില്ലാ വാർത്തകൾ', 'District News', 'district', 'ജില്ലാതല പ്രധാന വിവരങ്ങൾ', 2, 'active', 1),
('സംസ്ഥാന വാർത്തകൾ', 'State News', 'state', 'കേരള സംസ്ഥാന പ്രധാന വാർത്തകൾ', 3, 'active', 1),
('ക്രൈം ഫയൽ', 'Crime File', 'crime', 'അന്വേഷണ റിപ്പോർട്ടുകൾ, കോടതി വാർത്തകൾ', 4, 'active', 1),
('ബിസിനസ് വാർത്തകൾ', 'Business News', 'business', 'വിപണി, സ്വർണ്ണവില, വാണിജ്യ വിശേഷങ്ങൾ', 5, 'active', 1),
('Genz കോളം', 'Gen Z Column', 'genz', 'പുതുതലമുറ ട്രെൻഡുകൾ, ഗാഡ്‌ജെറ്റുകൾ, സോഷ്യൽ മീഡിയ', 6, 'active', 1),
('സ്കൂൾ കോർണർ', 'School Corner', 'school', 'വിദ്യാഭ്യാസം, പരീക്ഷാ തീയതികൾ, ക്യാമ്പസ് വിശേഷങ്ങൾ', 7, 'active', 1),
('അറിയിപ്പുകൾ', 'Notices & Announcements', 'notices', 'സർക്കാർ വിജ്ഞാപനങ്ങൾ, പൊതു അറിയിപ്പുകൾ', 8, 'active', 1),
('ജോലി ഒഴിവുകൾ', 'Job Vacancies', 'jobs', 'പി.എസ്.സി, ബാങ്കിംഗ്, ഐ.ടി തൊഴിലവസരങ്ങൾ', 9, 'active', 1),
('എഴുത്തുപുര', 'Literature & Articles', 'literature', 'കഥ, കവിത, സാംസ്കാരിക ലേഖനങ്ങൾ', 10, 'active', 1),
('വിജയവഴിയിൽ', 'Success Stories', 'success', 'പ്രചോദനാത്മക അനുഭവങ്ങൾ, സംരംഭക വിജയങ്ങൾ', 11, 'active', 1),
('ലേഡീസ് കോർണർ', 'Ladies Corner', 'ladies', 'ആരോഗ്യം, ഫാഷൻ, പാചകം, വനിതാ സംരംഭങ്ങൾ', 12, 'active', 1)
ON DUPLICATE KEY UPDATE `name_malayalam` = VALUES(`name_malayalam`), `home_page` = VALUES(`home_page`);

-- 3. Create Homepage Items Table (Used by Homepage controller & Frontend)
CREATE TABLE IF NOT EXISTS `homepage_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(300) NOT NULL,
    `subtitle` VARCHAR(500) NULL,
    `description` TEXT NOT NULL,
    `reporter_name` VARCHAR(160) NOT NULL,
    `reporter_role` VARCHAR(160) NOT NULL DEFAULT 'Staff Reporter',
    `reporter_avatar` VARCHAR(500) NULL,
    `image_url` VARCHAR(1000) NULL,
    `image_caption` VARCHAR(500) NULL,
    `link_url` VARCHAR(1000) NULL,
    `link_text` VARCHAR(100) NULL,
    `display_order` INT DEFAULT 0,
    `status` ENUM('active', 'inactive') DEFAULT 'active',
    `is_featured` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_status` (`status`),
    INDEX `idx_display_order` (`display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Create Home Banner Table (Used by Home module to update banner, title, description, author)
CREATE TABLE IF NOT EXISTS `home_banner` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `banner_image` VARCHAR(1000) NULL,
    `title` VARCHAR(300) NOT NULL,
    `subtitle` VARCHAR(500) NULL,
    `description` TEXT NOT NULL,
    `author` VARCHAR(160) NOT NULL DEFAULT 'Chief Editor',
    `author_role` VARCHAR(160) NOT NULL DEFAULT 'Special Correspondent',
    `badge` VARCHAR(100) NULL DEFAULT 'പ്രധാന വാർത്ത',
    `link_url` VARCHAR(500) NULL,
    `status` ENUM('active', 'inactive') DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default Home Banner Seed
INSERT INTO `home_banner` (`id`, `banner_image`, `title`, `subtitle`, `description`, `author`, `author_role`, `badge`, `link_url`, `status`)
VALUES (
    1,
    'https://images.unsplash.com/photo-1504711434969-e33886168f5c?auto=format&fit=crop&w=1600&q=80',
    'കേരളത്തിൽ ഡിജിറ്റൽ വിപ്ലവം: മുഴുവൻ തദ്ദേശ സ്ഥാപനങ്ങളും ഓൺലൈൻ സേവനത്തിലേക്ക്',
    'പൊതുജനങ്ങൾക്ക് സുതാര്യമായ സേവനം ഉറപ്പാക്കുന്ന സമഗ്ര ഭരണ പരിഷ്കാരം',
    'സംസ്ഥാനത്തെ മുഴുവൻ തദ്ദേശ സ്വയംഭരണ സ്ഥാപനങ്ങളിലെയും സേവനങ്ങൾ സമ്പൂർണ്ണമായി ഡിജിറ്റൈസ് ചെയ്തു. ജനങ്ങൾക്ക് സർക്കാർ ഓഫീസുകൾ കയറിയിറങ്ങാതെ സർട്ടിഫിക്കറ്റുകളും അനുമതികളും വീട്ടിലിരുന്ന് ലഭ്യമാകും.',
    'കെ. ആർ. നാരായണൻ',
    'ചീഫ് റിപ്പോർട്ടർ (Chief Reporter)',
    'പ്രധാന വാർത്ത (Lead Story)',
    '#',
    'active'
) ON DUPLICATE KEY UPDATE `title` = VALUES(`title`);

-- 5. Create Articles Table (Used by News controller & Frontend)
CREATE TABLE IF NOT EXISTS `articles` (
    `id` VARCHAR(64) PRIMARY KEY,
    `title` VARCHAR(300) NOT NULL,
    `subtitle` VARCHAR(500) NULL,
    `excerpt` VARCHAR(1000) NULL,
    `content_json` LONGTEXT NOT NULL,
    `category_id` INT NOT NULL,
    `author_name` VARCHAR(160) NOT NULL,
    `author_role` VARCHAR(160) NOT NULL DEFAULT 'Staff Reporter',
    `author_avatar` VARCHAR(500) NULL,
    `published_at` DATETIME NOT NULL,
    `read_time` VARCHAR(40) NOT NULL DEFAULT '3 min read',
    `image_url` VARCHAR(1000) NULL,
    `image_caption` VARCHAR(500) NULL,
    `tags_json` TEXT NULL,
    `status` VARCHAR(20) NOT NULL DEFAULT 'published',
    `is_breaking` TINYINT(1) DEFAULT 0,
    `is_featured` TINYINT(1) DEFAULT 0,
    `is_trending` TINYINT(1) DEFAULT 0,
    `views` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_category` (`category_id`),
    INDEX `idx_status` (`status`),
    INDEX `idx_published` (`published_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Create Today's Audio Table (Generated from today's live-news-broadcast articles)
CREATE TABLE IF NOT EXISTS `todays_audio` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `audio_date` DATE NOT NULL UNIQUE,
    `title` VARCHAR(300) NOT NULL,
    `script_text` LONGTEXT NOT NULL,
    `audio_url` VARCHAR(1000) NULL,
    `article_ids_json` TEXT NOT NULL,
    `article_count` INT NOT NULL DEFAULT 0,
    `duration_label` VARCHAR(40) NOT NULL DEFAULT '00:00',
    `status` ENUM('active', 'inactive') DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_audio_date` (`audio_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Create Newsletter Subscriptions Table
CREATE TABLE IF NOT EXISTS `newsletter_subscriptions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `status` ENUM('subscribed', 'unsubscribed') DEFAULT 'subscribed',
    `subscribed_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `unsubscribed_at` DATETIME NULL,
    `ip_address` VARCHAR(45) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
