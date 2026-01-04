-- =====================================================
-- Blog & Articles System for SEO
-- =====================================================

-- Article Categories
CREATE TABLE IF NOT EXISTS `article_categories` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `name_ar` VARCHAR(100) NOT NULL,
    `name_en` VARCHAR(100),
    `slug` VARCHAR(100) UNIQUE NOT NULL,
    `description` TEXT,
    `meta_title` VARCHAR(200),
    `meta_description` VARCHAR(500),
    `meta_keywords` VARCHAR(500),
    `parent_id` INT DEFAULT NULL,
    `icon` VARCHAR(50),
    `image` VARCHAR(255),
    `sort_order` INT DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`parent_id`) REFERENCES `article_categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Articles Table
CREATE TABLE IF NOT EXISTS `articles` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) UNIQUE NOT NULL,
    `excerpt` TEXT,
    `content` LONGTEXT,
    `featured_image` VARCHAR(255),
    `category_id` INT,
    `author_id` INT,
    `status` ENUM('draft', 'published', 'scheduled', 'archived') DEFAULT 'draft',
    `is_featured` TINYINT(1) DEFAULT 0,
    `allow_comments` TINYINT(1) DEFAULT 1,
    `views` INT DEFAULT 0,
    `reading_time` INT DEFAULT 0,

    -- SEO Fields (Full Control)
    `meta_title` VARCHAR(200),
    `meta_description` VARCHAR(500),
    `meta_keywords` VARCHAR(500),
    `canonical_url` VARCHAR(500),
    `og_title` VARCHAR(200),
    `og_description` VARCHAR(500),
    `og_image` VARCHAR(255),
    `twitter_title` VARCHAR(200),
    `twitter_description` VARCHAR(500),
    `twitter_image` VARCHAR(255),
    `schema_type` VARCHAR(50) DEFAULT 'Article',
    `schema_data` JSON,
    `robots` VARCHAR(100) DEFAULT 'index, follow',
    `focus_keyword` VARCHAR(100),
    `secondary_keywords` VARCHAR(500),

    -- Publishing
    `published_at` TIMESTAMP NULL,
    `scheduled_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (`category_id`) REFERENCES `article_categories`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`author_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,

    INDEX `idx_status` (`status`),
    INDEX `idx_category` (`category_id`),
    INDEX `idx_published` (`published_at`),
    INDEX `idx_featured` (`is_featured`),
    FULLTEXT `ft_search` (`title`, `content`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Article Tags
CREATE TABLE IF NOT EXISTS `article_tags` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `name_ar` VARCHAR(50) NOT NULL,
    `name_en` VARCHAR(50),
    `slug` VARCHAR(50) UNIQUE NOT NULL,
    `meta_title` VARCHAR(200),
    `meta_description` VARCHAR(500),
    `articles_count` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Article to Tags Relation
CREATE TABLE IF NOT EXISTS `article_tag_relations` (
    `article_id` INT NOT NULL,
    `tag_id` INT NOT NULL,
    PRIMARY KEY (`article_id`, `tag_id`),
    FOREIGN KEY (`article_id`) REFERENCES `articles`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`tag_id`) REFERENCES `article_tags`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Related Articles
CREATE TABLE IF NOT EXISTS `related_articles` (
    `article_id` INT NOT NULL,
    `related_id` INT NOT NULL,
    `sort_order` INT DEFAULT 0,
    PRIMARY KEY (`article_id`, `related_id`),
    FOREIGN KEY (`article_id`) REFERENCES `articles`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`related_id`) REFERENCES `articles`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEO Redirects (301/302)
CREATE TABLE IF NOT EXISTS `seo_redirects` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `from_url` VARCHAR(500) NOT NULL,
    `to_url` VARCHAR(500) NOT NULL,
    `redirect_type` ENUM('301', '302') DEFAULT '301',
    `hits` INT DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_from` (`from_url`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEO Settings per Page Type
CREATE TABLE IF NOT EXISTS `seo_templates` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `page_type` VARCHAR(50) NOT NULL UNIQUE,
    `title_template` VARCHAR(255),
    `description_template` VARCHAR(500),
    `og_title_template` VARCHAR(255),
    `og_description_template` VARCHAR(500),
    `schema_template` JSON,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default SEO templates
INSERT INTO `seo_templates` (`page_type`, `title_template`, `description_template`) VALUES
('home', '{site_name} - {tagline}', '{site_description}'),
('property', '{title} | {transaction_type} في {location} | {site_name}', '{title} - {bedrooms} غرف، {area} متر، {price} - {site_name}'),
('search', '{transaction_type} عقارات في {location} | {site_name}', 'ابحث عن {category} {transaction_type} في {location}. أفضل العروض العقارية من {site_name}'),
('article', '{title} | مدونة {site_name}', '{excerpt}'),
('category', 'عقارات {category} | {site_name}', 'تصفح جميع {category} المتاحة للبيع والإيجار في {site_name}'),
('location', 'عقارات في {location} | {site_name}', 'اكتشف أفضل العقارات في {location}. شقق، فيلات، ومحلات للبيع والإيجار'),
('compound', 'كومباوند {name} | عقارات {location} | {site_name}', 'استكشف عقارات كومباوند {name} في {location}. أسعار ومواصفات وصور');

-- Internal Links Suggestions (for SEO)
CREATE TABLE IF NOT EXISTS `internal_links` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `keyword` VARCHAR(100) NOT NULL,
    `target_url` VARCHAR(500) NOT NULL,
    `target_title` VARCHAR(255),
    `link_count` INT DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_keyword` (`keyword`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- FAQ Schema for Properties/Articles
CREATE TABLE IF NOT EXISTS `faqs` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `entity_type` ENUM('property', 'article', 'category', 'location', 'page') NOT NULL,
    `entity_id` INT NOT NULL,
    `question` TEXT NOT NULL,
    `answer` TEXT NOT NULL,
    `sort_order` INT DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_entity` (`entity_type`, `entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
