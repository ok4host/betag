-- BeTaj Real Estate Database Schema
-- Run this in phpMyAdmin or MySQL CLI

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- --------------------------------------------------------
-- Users Table
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(255) NULL UNIQUE,
    `phone` VARCHAR(20) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('user', 'broker', 'admin') DEFAULT 'user',
    `avatar` VARCHAR(255) NULL,
    `company_name` VARCHAR(255) NULL,
    `is_verified` TINYINT(1) DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_phone` (`phone`),
    INDEX `idx_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Categories Table (Property Types)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `categories` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name_ar` VARCHAR(100) NOT NULL,
    `name_en` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `icon` VARCHAR(50) NULL,
    `sort_order` INT DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default categories
INSERT INTO `categories` (`name_ar`, `name_en`, `slug`, `icon`, `sort_order`) VALUES
('شقة', 'Apartment', 'apartment', 'fa-building', 1),
('فيلا', 'Villa', 'villa', 'fa-house-chimney', 2),
('دوبلكس', 'Duplex', 'duplex', 'fa-layer-group', 3),
('ستوديو', 'Studio', 'studio', 'fa-door-open', 4),
('شاليه', 'Chalet', 'chalet', 'fa-umbrella-beach', 5),
('أرض', 'Land', 'land', 'fa-mountain-sun', 6),
('محل تجاري', 'Commercial', 'commercial', 'fa-store', 7),
('مكتب', 'Office', 'office', 'fa-briefcase', 8);

-- --------------------------------------------------------
-- Locations Table
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `locations` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name_ar` VARCHAR(100) NOT NULL,
    `name_en` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `parent_id` INT UNSIGNED NULL,
    `type` ENUM('governorate', 'city', 'area', 'compound') DEFAULT 'area',
    `latitude` DECIMAL(10, 8) NULL,
    `longitude` DECIMAL(11, 8) NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    FOREIGN KEY (`parent_id`) REFERENCES `locations`(`id`) ON DELETE SET NULL,
    INDEX `idx_parent` (`parent_id`),
    INDEX `idx_type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default locations
INSERT INTO `locations` (`name_ar`, `name_en`, `slug`, `type`) VALUES
('القاهرة', 'Cairo', 'cairo', 'governorate'),
('الجيزة', 'Giza', 'giza', 'governorate'),
('الإسكندرية', 'Alexandria', 'alexandria', 'governorate');

INSERT INTO `locations` (`name_ar`, `name_en`, `slug`, `type`, `parent_id`) VALUES
('القاهرة الجديدة', 'New Cairo', 'new-cairo', 'city', 1),
('مدينة نصر', 'Nasr City', 'nasr-city', 'city', 1),
('المعادي', 'Maadi', 'maadi', 'city', 1),
('مصر الجديدة', 'Heliopolis', 'heliopolis', 'city', 1),
('الشيخ زايد', 'Sheikh Zayed', 'sheikh-zayed', 'city', 2),
('6 أكتوبر', '6th October', '6-october', 'city', 2),
('العاصمة الإدارية', 'New Capital', 'new-capital', 'city', 1);

-- --------------------------------------------------------
-- Properties Table
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `properties` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `category_id` INT UNSIGNED NOT NULL,
    `location_id` INT UNSIGNED NOT NULL,

    -- Basic Info
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `description` TEXT NULL,
    `address` VARCHAR(500) NULL,

    -- Transaction
    `transaction_type` ENUM('sale', 'rent') NOT NULL,
    `price` DECIMAL(15, 2) NOT NULL,
    `price_negotiable` TINYINT(1) DEFAULT 0,
    `rent_period` ENUM('monthly', 'yearly') NULL,

    -- Specs
    `area` INT UNSIGNED NOT NULL,
    `bedrooms` TINYINT UNSIGNED DEFAULT 0,
    `bathrooms` TINYINT UNSIGNED DEFAULT 1,
    `floor` VARCHAR(20) NULL,
    `finishing` ENUM('super-lux', 'lux', 'semi-finished', 'not-finished') DEFAULT 'lux',

    -- Amenities (stored as JSON)
    `amenities` JSON NULL,

    -- Images
    `featured_image` VARCHAR(255) NULL,
    `images` JSON NULL,

    -- SEO
    `meta_title` VARCHAR(255) NULL,
    `meta_description` TEXT NULL,
    `meta_keywords` VARCHAR(500) NULL,

    -- Status
    `status` ENUM('pending', 'active', 'sold', 'rented', 'rejected', 'expired') DEFAULT 'pending',
    `is_featured` TINYINT(1) DEFAULT 0,
    `is_verified` TINYINT(1) DEFAULT 0,
    `views` INT UNSIGNED DEFAULT 0,
    `whatsapp_clicks` INT UNSIGNED DEFAULT 0,
    `call_clicks` INT UNSIGNED DEFAULT 0,

    -- Timestamps
    `published_at` TIMESTAMP NULL,
    `expires_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`),
    FOREIGN KEY (`location_id`) REFERENCES `locations`(`id`),

    INDEX `idx_status` (`status`),
    INDEX `idx_transaction` (`transaction_type`),
    INDEX `idx_featured` (`is_featured`),
    INDEX `idx_price` (`price`),
    INDEX `idx_location` (`location_id`),
    FULLTEXT `ft_search` (`title`, `description`, `address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Leads Table (Inquiries)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `leads` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `property_id` INT UNSIGNED NULL,
    `name` VARCHAR(100) NOT NULL,
    `phone` VARCHAR(20) NOT NULL,
    `email` VARCHAR(255) NULL,
    `message` TEXT NULL,
    `purpose` ENUM('buy', 'rent', 'general') DEFAULT 'general',
    `preferred_area` VARCHAR(100) NULL,
    `budget_min` DECIMAL(15, 2) NULL,
    `budget_max` DECIMAL(15, 2) NULL,
    `source` VARCHAR(50) DEFAULT 'website',
    `status` ENUM('new', 'contacted', 'qualified', 'converted', 'closed') DEFAULT 'new',
    `notes` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (`property_id`) REFERENCES `properties`(`id`) ON DELETE SET NULL,
    INDEX `idx_status` (`status`),
    INDEX `idx_phone` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Pages Table (For static pages with SEO)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `pages` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `content` LONGTEXT NULL,
    `meta_title` VARCHAR(255) NULL,
    `meta_description` TEXT NULL,
    `meta_keywords` VARCHAR(500) NULL,
    `og_image` VARCHAR(255) NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- SEO Settings Table
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `seo_settings` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `page_type` VARCHAR(50) NOT NULL UNIQUE,
    `meta_title` VARCHAR(255) NULL,
    `meta_description` TEXT NULL,
    `meta_keywords` VARCHAR(500) NULL,
    `og_image` VARCHAR(255) NULL,
    `schema_markup` JSON NULL,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default SEO settings
INSERT INTO `seo_settings` (`page_type`, `meta_title`, `meta_description`) VALUES
('home', 'بي تاج | شقق وفلل للبيع والإيجار في مصر', 'أكبر منصة عقارية في مصر. ابحث عن شقق، فلل، أراضي ومحلات للبيع والإيجار.'),
('search', 'البحث عن عقارات | بي تاج', 'ابحث في أكثر من 50,000 عقار في مصر.'),
('compounds', 'دليل الكمبوندات في مصر | بي تاج', 'دليل شامل لأفضل الكمبوندات في مصر مع الأسعار والمميزات.'),
('new-projects', 'مشاريع جديدة | بي تاج', 'أحدث المشاريع العقارية تحت الإنشاء في مصر.');

-- --------------------------------------------------------
-- Site Settings Table
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `settings` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(100) NOT NULL UNIQUE,
    `value` TEXT NULL,
    `type` ENUM('text', 'textarea', 'number', 'boolean', 'json') DEFAULT 'text'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default settings
INSERT INTO `settings` (`key`, `value`, `type`) VALUES
('site_name', 'بي تاج', 'text'),
('site_tagline', 'اكتشف بيتك الجديد', 'text'),
('contact_phone', '19xxx', 'text'),
('contact_whatsapp', '201xxxxxxxxx', 'text'),
('contact_email', 'info@betag.com', 'text'),
('address', 'القاهرة، مصر', 'text'),
('facebook_url', '', 'text'),
('instagram_url', '', 'text'),
('twitter_url', '', 'text'),
('google_analytics_id', '', 'text'),
('facebook_pixel_id', '', 'text'),
('google_maps_api_key', '', 'text');

-- --------------------------------------------------------
-- Favorites Table
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `favorites` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `property_id` INT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`property_id`) REFERENCES `properties`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_favorite` (`user_id`, `property_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Admin user (password: admin123 - CHANGE THIS!)
-- --------------------------------------------------------
INSERT INTO `users` (`name`, `email`, `phone`, `password`, `role`, `is_verified`, `is_active`) VALUES
('Admin', 'admin@betag.com', '01000000000', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1, 1);

SET FOREIGN_KEY_CHECKS = 1;
