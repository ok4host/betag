-- AI Content & Scraping System Tables
-- Run this AFTER the main schema.sql

SET NAMES utf8mb4;

-- --------------------------------------------------------
-- AI Prompts Table
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `ai_prompts` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `category` ENUM('property_description', 'seo_meta', 'blog_post', 'area_guide', 'compound_description', 'email', 'social_post', 'custom') DEFAULT 'custom',
    `prompt_template` TEXT NOT NULL,
    `system_instructions` TEXT NULL,
    `variables` JSON NULL COMMENT 'Available variables like {property_title}, {location}, etc.',
    `model` VARCHAR(50) DEFAULT 'gpt-4',
    `temperature` DECIMAL(2,1) DEFAULT 0.7,
    `max_tokens` INT DEFAULT 1000,
    `is_active` TINYINT(1) DEFAULT 1,
    `usage_count` INT UNSIGNED DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_category` (`category`),
    INDEX `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default prompts
INSERT INTO `ai_prompts` (`name`, `slug`, `category`, `prompt_template`, `system_instructions`, `variables`) VALUES
('وصف العقار', 'property-description', 'property_description',
'اكتب وصف احترافي لعقار بالمواصفات التالية:
- النوع: {property_type}
- الموقع: {location}
- المساحة: {area} متر
- عدد الغرف: {bedrooms}
- السعر: {price}
- التشطيب: {finishing}
- المميزات: {amenities}

اكتب وصف جذاب يتضمن:
1. مقدمة ملفتة للانتباه
2. وصف الموقع والمنطقة
3. مميزات العقار
4. خاتمة محفزة على التواصل',
'أنت خبير تسويق عقاري محترف. اكتب بالعربية الفصحى البسيطة. استخدم كلمات ملفتة ومؤثرة. الوصف يجب أن يكون بين 150-250 كلمة.',
'["property_type", "location", "area", "bedrooms", "price", "finishing", "amenities"]'),

('SEO Meta Tags', 'seo-meta-tags', 'seo_meta',
'اكتب meta title و meta description لصفحة عقار:
- العنوان: {title}
- النوع: {property_type}
- الموقع: {location}
- السعر: {price}

اكتب:
1. Meta Title (لا يزيد عن 60 حرف)
2. Meta Description (لا يزيد عن 160 حرف)
3. 5 كلمات مفتاحية',
'أنت خبير SEO. اكتب meta tags محسنة لمحركات البحث.',
'["title", "property_type", "location", "price"]'),

('دليل المنطقة', 'area-guide', 'area_guide',
'اكتب دليل شامل عن منطقة: {area_name}

يجب أن يتضمن:
1. نبذة عن المنطقة وتاريخها
2. الموقع الجغرافي والحدود
3. أنواع العقارات المتاحة
4. متوسط الأسعار للبيع والإيجار
5. المرافق والخدمات (مدارس، مستشفيات، مولات)
6. المواصلات والوصول
7. مميزات السكن في المنطقة
8. أفضل الكمبوندات أو المناطق الفرعية',
'أنت خبير عقارات في مصر. اكتب محتوى شامل ومفيد بين 500-800 كلمة.',
'["area_name"]'),

('وصف الكمبوند', 'compound-description', 'compound_description',
'اكتب وصف شامل لكمبوند: {compound_name}

المعلومات المتاحة:
- الموقع: {location}
- المطور: {developer}
- أنواع الوحدات: {unit_types}
- المساحات: {areas}
- الأسعار: {prices}
- المميزات: {amenities}

اكتب محتوى يتضمن:
1. نبذة عن الكمبوند
2. الموقع والوصول
3. أنواع الوحدات والمساحات
4. المرافق والخدمات
5. أنظمة السداد
6. مميزات الاستثمار',
'أنت خبير عقارات. اكتب محتوى احترافي وشامل.',
'["compound_name", "location", "developer", "unit_types", "areas", "prices", "amenities"]'),

('منشور سوشيال ميديا', 'social-post', 'social_post',
'اكتب منشور سوشيال ميديا لعقار:
- النوع: {property_type}
- الموقع: {location}
- السعر: {price}
- المميزات: {features}

اكتب منشور قصير وجذاب مع:
1. عنوان ملفت
2. نقاط سريعة
3. Call to Action
4. هاشتاجات مناسبة',
'اكتب بأسلوب تسويقي جذاب. استخدم الإيموجي بشكل مناسب.',
'["property_type", "location", "price", "features"]');

-- --------------------------------------------------------
-- AI Generated Content Table
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `ai_content` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `prompt_id` INT UNSIGNED NULL,
    `entity_type` ENUM('property', 'location', 'compound', 'page', 'blog', 'custom') DEFAULT 'custom',
    `entity_id` INT UNSIGNED NULL,
    `input_data` JSON NULL,
    `generated_content` LONGTEXT NOT NULL,
    `content_type` ENUM('description', 'meta_title', 'meta_description', 'keywords', 'full_article', 'social_post') DEFAULT 'description',
    `status` ENUM('draft', 'approved', 'published', 'rejected') DEFAULT 'draft',
    `tokens_used` INT UNSIGNED DEFAULT 0,
    `generation_time` DECIMAL(5,2) NULL COMMENT 'Time in seconds',
    `created_by` INT UNSIGNED NULL,
    `approved_by` INT UNSIGNED NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`prompt_id`) REFERENCES `ai_prompts`(`id`) ON DELETE SET NULL,
    INDEX `idx_entity` (`entity_type`, `entity_id`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Scraping Sources Table
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `scraping_sources` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `base_url` VARCHAR(500) NOT NULL,
    `source_type` ENUM('website', 'api', 'rss', 'social') DEFAULT 'website',
    `scraping_config` JSON NULL COMMENT 'CSS selectors or API endpoints',
    `headers` JSON NULL,
    `rate_limit` INT DEFAULT 5 COMMENT 'Requests per minute',
    `is_active` TINYINT(1) DEFAULT 1,
    `last_scraped_at` TIMESTAMP NULL,
    `total_items_scraped` INT UNSIGNED DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert example sources
INSERT INTO `scraping_sources` (`name`, `slug`, `base_url`, `source_type`, `scraping_config`) VALUES
('Manual Import', 'manual-import', 'manual://upload', 'api', '{"type": "csv_upload"}'),
('Property Feed', 'property-feed', 'https://example.com/feed', 'api', '{"endpoint": "/properties", "format": "json"}');

-- --------------------------------------------------------
-- Scraped Data Table
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `scraped_data` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `source_id` INT UNSIGNED NOT NULL,
    `external_id` VARCHAR(100) NULL,
    `data_type` ENUM('property', 'location', 'compound', 'price', 'contact') DEFAULT 'property',
    `raw_data` JSON NOT NULL,
    `parsed_data` JSON NULL,
    `status` ENUM('pending', 'processed', 'imported', 'failed', 'duplicate') DEFAULT 'pending',
    `imported_entity_id` INT UNSIGNED NULL COMMENT 'ID of the created property/location',
    `error_message` TEXT NULL,
    `scraped_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `processed_at` TIMESTAMP NULL,
    FOREIGN KEY (`source_id`) REFERENCES `scraping_sources`(`id`) ON DELETE CASCADE,
    INDEX `idx_source` (`source_id`),
    INDEX `idx_status` (`status`),
    INDEX `idx_external` (`external_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Scraping Jobs Table
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `scraping_jobs` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `source_id` INT UNSIGNED NOT NULL,
    `job_type` ENUM('full', 'incremental', 'single_page') DEFAULT 'incremental',
    `parameters` JSON NULL,
    `status` ENUM('pending', 'running', 'completed', 'failed', 'cancelled') DEFAULT 'pending',
    `items_found` INT UNSIGNED DEFAULT 0,
    `items_processed` INT UNSIGNED DEFAULT 0,
    `items_imported` INT UNSIGNED DEFAULT 0,
    `error_count` INT UNSIGNED DEFAULT 0,
    `started_at` TIMESTAMP NULL,
    `completed_at` TIMESTAMP NULL,
    `created_by` INT UNSIGNED NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`source_id`) REFERENCES `scraping_sources`(`id`) ON DELETE CASCADE,
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- AI Settings Table
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `ai_settings` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `provider` ENUM('openai', 'anthropic', 'google', 'local') DEFAULT 'openai',
    `api_key` VARCHAR(255) NULL,
    `model` VARCHAR(100) DEFAULT 'gpt-4',
    `endpoint` VARCHAR(500) NULL,
    `default_temperature` DECIMAL(2,1) DEFAULT 0.7,
    `max_tokens_per_request` INT DEFAULT 2000,
    `monthly_budget` DECIMAL(10,2) NULL,
    `current_month_usage` DECIMAL(10,2) DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `ai_settings` (`provider`, `model`) VALUES ('openai', 'gpt-4');
