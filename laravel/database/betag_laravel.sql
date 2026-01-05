-- =============================================
-- BeTaj Laravel Database
-- بي تاج - قاعدة بيانات جاهزة
-- =============================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- إنشاء قاعدة البيانات
CREATE DATABASE IF NOT EXISTS `betag_laravel` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `betag_laravel`;

-- =============================================
-- جدول المستخدمين
-- =============================================
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `email` varchar(255) NOT NULL,
    `email_verified_at` timestamp NULL DEFAULT NULL,
    `password` varchar(255) NOT NULL,
    `phone` varchar(20) DEFAULT NULL,
    `avatar` varchar(255) DEFAULT NULL,
    `role` enum('user','agent','admin') DEFAULT 'user',
    `status` enum('active','inactive','banned') DEFAULT 'active',
    `remember_token` varchar(100) DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- بيانات المستخدمين
INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `role`, `status`) VALUES
(1, 'مدير النظام', 'admin@betag.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '01000000000', 'admin', 'active'),
(2, 'أحمد محمد', 'ahmed@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '01100000001', 'agent', 'active'),
(3, 'سارة أحمد', 'sara@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '01200000002', 'user', 'active');

-- =============================================
-- جدول التصنيفات
-- =============================================
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name_ar` varchar(255) NOT NULL,
    `name_en` varchar(255) NOT NULL,
    `slug` varchar(255) NOT NULL,
    `icon` varchar(50) DEFAULT NULL,
    `description_ar` text DEFAULT NULL,
    `description_en` text DEFAULT NULL,
    `sort_order` int(11) DEFAULT 0,
    `is_active` tinyint(1) DEFAULT 1,
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `categories_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- بيانات التصنيفات
INSERT INTO `categories` (`id`, `name_ar`, `name_en`, `slug`, `icon`, `sort_order`) VALUES
(1, 'شقق', 'Apartments', 'apartments', 'fa-building', 1),
(2, 'فيلات', 'Villas', 'villas', 'fa-home', 2),
(3, 'تاون هاوس', 'Townhouses', 'townhouses', 'fa-house-user', 3),
(4, 'توين هاوس', 'Twin Houses', 'twin-houses', 'fa-house-chimney', 4),
(5, 'دوبلكس', 'Duplexes', 'duplexes', 'fa-layer-group', 5),
(6, 'بنتهاوس', 'Penthouses', 'penthouses', 'fa-city', 6),
(7, 'استوديو', 'Studios', 'studios', 'fa-door-open', 7),
(8, 'شاليهات', 'Chalets', 'chalets', 'fa-umbrella-beach', 8),
(9, 'مكاتب', 'Offices', 'offices', 'fa-briefcase', 9),
(10, 'محلات', 'Shops', 'shops', 'fa-store', 10),
(11, 'أراضي', 'Lands', 'lands', 'fa-map', 11);

-- =============================================
-- جدول المواقع
-- =============================================
DROP TABLE IF EXISTS `locations`;
CREATE TABLE `locations` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
    `name_ar` varchar(255) NOT NULL,
    `name_en` varchar(255) NOT NULL,
    `slug` varchar(255) NOT NULL,
    `type` enum('city','area','compound') DEFAULT 'area',
    `description_ar` text DEFAULT NULL,
    `description_en` text DEFAULT NULL,
    `image` varchar(255) DEFAULT NULL,
    `latitude` decimal(10,8) DEFAULT NULL,
    `longitude` decimal(11,8) DEFAULT NULL,
    `is_featured` tinyint(1) DEFAULT 0,
    `is_active` tinyint(1) DEFAULT 1,
    `sort_order` int(11) DEFAULT 0,
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `locations_slug_unique` (`slug`),
    KEY `locations_parent_id_foreign` (`parent_id`),
    CONSTRAINT `locations_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `locations` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- بيانات المواقع - المدن
INSERT INTO `locations` (`id`, `parent_id`, `name_ar`, `name_en`, `slug`, `type`, `is_featured`, `sort_order`) VALUES
(1, NULL, 'القاهرة', 'Cairo', 'cairo', 'city', 1, 1),
(2, NULL, 'الجيزة', 'Giza', 'giza', 'city', 1, 2),
(3, NULL, 'الإسكندرية', 'Alexandria', 'alexandria', 'city', 1, 3),
(4, NULL, 'الساحل الشمالي', 'North Coast', 'north-coast', 'city', 1, 4),
(5, NULL, 'العين السخنة', 'Ain Sokhna', 'ain-sokhna', 'city', 1, 5);

-- بيانات المواقع - المناطق
INSERT INTO `locations` (`id`, `parent_id`, `name_ar`, `name_en`, `slug`, `type`, `sort_order`) VALUES
(10, 1, 'القاهرة الجديدة', 'New Cairo', 'new-cairo', 'area', 1),
(11, 1, 'مدينة نصر', 'Nasr City', 'nasr-city', 'area', 2),
(12, 1, 'مصر الجديدة', 'Heliopolis', 'heliopolis', 'area', 3),
(13, 1, 'المعادي', 'Maadi', 'maadi', 'area', 4),
(14, 1, 'العاصمة الإدارية', 'New Capital', 'new-capital', 'area', 5),
(20, 2, 'الشيخ زايد', 'Sheikh Zayed', 'sheikh-zayed', 'area', 1),
(21, 2, 'السادس من أكتوبر', '6th of October', '6th-october', 'area', 2),
(22, 2, 'الهرم', 'Haram', 'haram', 'area', 3),
(23, 2, 'فيصل', 'Faisal', 'faisal', 'area', 4);

-- بيانات المواقع - الكمبوندات
INSERT INTO `locations` (`id`, `parent_id`, `name_ar`, `name_en`, `slug`, `type`, `description_ar`, `description_en`, `is_featured`, `sort_order`) VALUES
(100, 10, 'ماونتن فيو', 'Mountain View', 'mountain-view', 'compound', 'كمبوند ماونتن فيو أحد أرقى المشاريع السكنية في القاهرة الجديدة، يتميز بتصميمات معمارية فريدة ومساحات خضراء واسعة.', 'Mountain View is one of the most prestigious residential projects in New Cairo, featuring unique architectural designs and vast green spaces.', 1, 1),
(101, 10, 'هايد بارك', 'Hyde Park', 'hyde-park', 'compound', 'كمبوند هايد بارك يقدم تجربة سكنية متكاملة مع مرافق عالمية المستوى ومساحات خضراء شاسعة.', 'Hyde Park offers a complete residential experience with world-class facilities and vast green areas.', 1, 2),
(102, 10, 'ميفيدا', 'Mivida', 'mivida', 'compound', 'كمبوند ميفيدا من إعمار مصر، يجمع بين الفخامة والراحة في قلب القاهرة الجديدة.', 'Mivida by Emaar Egypt combines luxury and comfort in the heart of New Cairo.', 1, 3),
(103, 14, 'ميدتاون سكاي', 'Midtown Sky', 'midtown-sky', 'compound', 'كمبوند ميدتاون سكاي في العاصمة الإدارية الجديدة، تصميم عصري ومرافق متكاملة.', 'Midtown Sky compound in the New Administrative Capital, modern design and integrated facilities.', 1, 4),
(104, 20, 'سوديك ويست', 'Sodic West', 'sodic-west', 'compound', 'كمبوند سوديك ويست في الشيخ زايد، مجتمع متكامل بخدمات راقية.', 'Sodic West in Sheikh Zayed, an integrated community with upscale services.', 1, 5),
(105, 20, 'بالم هيلز', 'Palm Hills', 'palm-hills', 'compound', 'بالم هيلز أحد أشهر المشاريع في غرب القاهرة، يتميز بالمساحات الخضراء والتصميم الراقي.', 'Palm Hills is one of the most famous projects in West Cairo, featuring green spaces and elegant design.', 1, 6);

-- =============================================
-- جدول العقارات
-- =============================================
DROP TABLE IF EXISTS `properties`;
CREATE TABLE `properties` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` bigint(20) UNSIGNED DEFAULT NULL,
    `category_id` bigint(20) UNSIGNED DEFAULT NULL,
    `location_id` bigint(20) UNSIGNED DEFAULT NULL,
    `title_ar` varchar(255) NOT NULL,
    `title_en` varchar(255) NOT NULL,
    `slug` varchar(255) NOT NULL,
    `description_ar` text DEFAULT NULL,
    `description_en` text DEFAULT NULL,
    `type` enum('sale','rent') DEFAULT 'sale',
    `price` decimal(15,2) NOT NULL,
    `area` int(11) DEFAULT NULL,
    `bedrooms` tinyint(4) DEFAULT NULL,
    `bathrooms` tinyint(4) DEFAULT NULL,
    `floor` tinyint(4) DEFAULT NULL,
    `furnishing` enum('furnished','semi-furnished','unfurnished') DEFAULT 'unfurnished',
    `features` json DEFAULT NULL,
    `image` varchar(255) DEFAULT NULL,
    `gallery` json DEFAULT NULL,
    `video_url` varchar(255) DEFAULT NULL,
    `latitude` decimal(10,8) DEFAULT NULL,
    `longitude` decimal(11,8) DEFAULT NULL,
    `address` varchar(255) DEFAULT NULL,
    `is_featured` tinyint(1) DEFAULT 0,
    `is_approved` tinyint(1) DEFAULT 1,
    `status` enum('active','pending','sold','rented','inactive') DEFAULT 'active',
    `views` int(11) DEFAULT 0,
    `seo_title` varchar(255) DEFAULT NULL,
    `seo_description` text DEFAULT NULL,
    `seo_keywords` varchar(255) DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `properties_slug_unique` (`slug`),
    KEY `properties_user_id_foreign` (`user_id`),
    KEY `properties_category_id_foreign` (`category_id`),
    KEY `properties_location_id_foreign` (`location_id`),
    KEY `properties_type_index` (`type`),
    KEY `properties_status_index` (`status`),
    CONSTRAINT `properties_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
    CONSTRAINT `properties_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
    CONSTRAINT `properties_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- بيانات العقارات
INSERT INTO `properties` (`id`, `user_id`, `category_id`, `location_id`, `title_ar`, `title_en`, `slug`, `description_ar`, `description_en`, `type`, `price`, `area`, `bedrooms`, `bathrooms`, `floor`, `furnishing`, `features`, `is_featured`, `status`, `views`) VALUES
(1, 2, 1, 100, 'شقة فاخرة في ماونتن فيو', 'Luxury Apartment in Mountain View', 'luxury-apartment-mountain-view', 'شقة فاخرة بتشطيب سوبر لوكس في كمبوند ماونتن فيو، تتميز بإطلالة رائعة على المساحات الخضراء. تتكون من 3 غرف نوم وريسبشن كبير ومطبخ مجهز بالكامل.', 'Luxury apartment with super lux finishing in Mountain View compound, featuring a wonderful view of the green spaces. Consists of 3 bedrooms, large reception, and fully equipped kitchen.', 'sale', 4500000.00, 180, 3, 2, 5, 'semi-furnished', '["تكييف مركزي", "أمن 24 ساعة", "جراج خاص", "حمام سباحة", "نادي رياضي"]', 1, 'active', 245),
(2, 2, 1, 101, 'شقة للإيجار في هايد بارك', 'Apartment for Rent in Hyde Park', 'apartment-rent-hyde-park', 'شقة مفروشة بالكامل للإيجار في كمبوند هايد بارك، موقع متميز قريب من جميع الخدمات.', 'Fully furnished apartment for rent in Hyde Park compound, prime location near all services.', 'rent', 25000.00, 150, 2, 2, 3, 'furnished', '["تكييف", "إنترنت", "غسالة", "ثلاجة", "أمن"]', 1, 'active', 189),
(3, 2, 2, 102, 'فيلا مستقلة في ميفيدا', 'Standalone Villa in Mivida', 'standalone-villa-mivida', 'فيلا مستقلة فاخرة في كمبوند ميفيدا، تتكون من 4 غرف نوم مع حديقة خاصة وحمام سباحة.', 'Luxury standalone villa in Mivida compound, consists of 4 bedrooms with private garden and swimming pool.', 'sale', 15000000.00, 350, 4, 4, 0, 'semi-furnished', '["حديقة خاصة", "حمام سباحة", "جراج 2 سيارة", "غرفة خادمة", "تكييف مركزي"]', 1, 'active', 312),
(4, 2, 3, 104, 'تاون هاوس في سوديك ويست', 'Townhouse in Sodic West', 'townhouse-sodic-west', 'تاون هاوس كورنر بتشطيب كامل في سوديك ويست، 3 غرف نوم مع روف خاص.', 'Corner townhouse with full finishing in Sodic West, 3 bedrooms with private roof.', 'sale', 8500000.00, 220, 3, 3, 0, 'unfurnished', '["روف خاص", "حديقة", "جراج", "أمن 24 ساعة"]', 1, 'active', 178),
(5, 2, 1, 103, 'شقة في العاصمة الإدارية', 'Apartment in New Capital', 'apartment-new-capital', 'شقة بتشطيب كامل في ميدتاون سكاي بالعاصمة الإدارية الجديدة، فرصة استثمارية ممتازة.', 'Fully finished apartment in Midtown Sky in the New Administrative Capital, excellent investment opportunity.', 'sale', 2800000.00, 130, 2, 1, 7, 'unfurnished', '["تكييف", "أمن", "جراج", "مصاعد"]', 0, 'active', 156),
(6, 2, 2, 105, 'فيلا توين هاوس في بالم هيلز', 'Twin House Villa in Palm Hills', 'twin-house-palm-hills', 'توين هاوس في بالم هيلز بالشيخ زايد، تصميم عصري ومساحات واسعة.', 'Twin house in Palm Hills Sheikh Zayed, modern design and spacious areas.', 'sale', 12000000.00, 300, 4, 3, 0, 'unfurnished', '["حديقة", "جراج", "أمن", "نادي"]', 1, 'active', 201),
(7, 2, 1, 11, 'شقة للإيجار في مدينة نصر', 'Apartment for Rent in Nasr City', 'apartment-rent-nasr-city', 'شقة 3 غرف للإيجار في موقع متميز بمدينة نصر، قريبة من سيتي ستارز.', '3 bedroom apartment for rent in prime location in Nasr City, near City Stars.', 'rent', 12000.00, 140, 3, 1, 4, 'unfurnished', '["قريب من المترو", "قريب من المولات", "هادئة"]', 0, 'active', 134),
(8, 2, 6, 100, 'بنتهاوس فاخر في ماونتن فيو', 'Luxury Penthouse in Mountain View', 'luxury-penthouse-mountain-view', 'بنتهاوس فاخر بإطلالة بانورامية في ماونتن فيو، تشطيب ألترا سوبر لوكس.', 'Luxury penthouse with panoramic view in Mountain View, ultra super lux finishing.', 'sale', 9500000.00, 280, 4, 3, 10, 'semi-furnished', '["تراس كبير", "إطلالة بانورامية", "تكييف مركزي", "جاكوزي"]', 1, 'active', 267),
(9, 2, 8, 4, 'شاليه في الساحل الشمالي', 'Chalet in North Coast', 'chalet-north-coast', 'شاليه مميز في الساحل الشمالي، إطلالة مباشرة على البحر.', 'Distinguished chalet in North Coast, direct sea view.', 'sale', 3500000.00, 120, 2, 1, 0, 'furnished', '["إطلالة بحر", "حمام سباحة مشترك", "شاطئ خاص"]', 0, 'active', 198),
(10, 2, 1, 13, 'شقة في المعادي', 'Apartment in Maadi', 'apartment-maadi', 'شقة راقية في المعادي، منطقة هادئة وقريبة من جميع الخدمات.', 'Elegant apartment in Maadi, quiet area near all services.', 'rent', 18000.00, 160, 3, 2, 2, 'furnished', '["حديقة مشتركة", "أمن", "موقف سيارات"]', 0, 'active', 145);

-- =============================================
-- جدول طلبات التواصل
-- =============================================
DROP TABLE IF EXISTS `leads`;
CREATE TABLE `leads` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `property_id` bigint(20) UNSIGNED DEFAULT NULL,
    `name` varchar(255) NOT NULL,
    `email` varchar(255) NOT NULL,
    `phone` varchar(20) NOT NULL,
    `message` text DEFAULT NULL,
    `type` enum('property','contact','compound') DEFAULT 'property',
    `source` varchar(50) DEFAULT NULL,
    `status` enum('new','contacted','converted','closed') DEFAULT 'new',
    `notes` text DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `leads_property_id_foreign` (`property_id`),
    CONSTRAINT `leads_property_id_foreign` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- بيانات الطلبات
INSERT INTO `leads` (`id`, `property_id`, `name`, `email`, `phone`, `message`, `type`, `source`, `status`) VALUES
(1, 1, 'محمد علي', 'mohamed@example.com', '01012345678', 'أرغب في معرفة المزيد عن هذه الشقة', 'property', 'website', 'new'),
(2, 3, 'فاطمة أحمد', 'fatma@example.com', '01098765432', 'هل يمكن ترتيب زيارة للفيلا؟', 'property', 'website', 'contacted'),
(3, NULL, 'خالد محمود', 'khaled@example.com', '01155555555', 'أبحث عن شقة للإيجار في التجمع الخامس', 'contact', 'contact_page', 'new');

-- =============================================
-- جدول تصنيفات المقالات
-- =============================================
DROP TABLE IF EXISTS `article_categories`;
CREATE TABLE `article_categories` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name_ar` varchar(255) NOT NULL,
    `name_en` varchar(255) NOT NULL,
    `slug` varchar(255) NOT NULL,
    `description_ar` text DEFAULT NULL,
    `description_en` text DEFAULT NULL,
    `is_active` tinyint(1) DEFAULT 1,
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `article_categories_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- بيانات تصنيفات المقالات
INSERT INTO `article_categories` (`id`, `name_ar`, `name_en`, `slug`) VALUES
(1, 'نصائح الشراء', 'Buying Tips', 'buying-tips'),
(2, 'نصائح الإيجار', 'Renting Tips', 'renting-tips'),
(3, 'أخبار السوق', 'Market News', 'market-news'),
(4, 'الاستثمار العقاري', 'Real Estate Investment', 'real-estate-investment'),
(5, 'دليل المناطق', 'Area Guides', 'area-guides');

-- =============================================
-- جدول المقالات
-- =============================================
DROP TABLE IF EXISTS `articles`;
CREATE TABLE `articles` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `category_id` bigint(20) UNSIGNED DEFAULT NULL,
    `user_id` bigint(20) UNSIGNED DEFAULT NULL,
    `title_ar` varchar(255) NOT NULL,
    `title_en` varchar(255) NOT NULL,
    `slug` varchar(255) NOT NULL,
    `excerpt_ar` text DEFAULT NULL,
    `excerpt_en` text DEFAULT NULL,
    `content_ar` longtext DEFAULT NULL,
    `content_en` longtext DEFAULT NULL,
    `image` varchar(255) DEFAULT NULL,
    `views` int(11) DEFAULT 0,
    `is_featured` tinyint(1) DEFAULT 0,
    `is_published` tinyint(1) DEFAULT 1,
    `seo_title` varchar(255) DEFAULT NULL,
    `seo_description` text DEFAULT NULL,
    `seo_keywords` varchar(255) DEFAULT NULL,
    `published_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `articles_slug_unique` (`slug`),
    KEY `articles_category_id_foreign` (`category_id`),
    KEY `articles_user_id_foreign` (`user_id`),
    CONSTRAINT `articles_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `article_categories` (`id`) ON DELETE SET NULL,
    CONSTRAINT `articles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- بيانات المقالات
INSERT INTO `articles` (`id`, `category_id`, `user_id`, `title_ar`, `title_en`, `slug`, `excerpt_ar`, `excerpt_en`, `content_ar`, `content_en`, `is_featured`, `views`) VALUES
(1, 1, 1, '10 نصائح قبل شراء شقتك الأولى', '10 Tips Before Buying Your First Apartment', '10-tips-buying-first-apartment',
'دليل شامل للمشترين لأول مرة يتضمن أهم النصائح والإرشادات لاتخاذ قرار الشراء الصحيح.',
'A comprehensive guide for first-time buyers including the most important tips and guidelines for making the right purchase decision.',
'<h2>مقدمة</h2><p>شراء الشقة الأولى هو قرار مهم في حياة أي شخص. في هذا المقال نستعرض أهم النصائح التي يجب مراعاتها.</p><h3>1. حدد ميزانيتك</h3><p>قبل البدء في البحث، حدد الميزانية المتاحة لديك بدقة.</p><h3>2. اختر الموقع المناسب</h3><p>الموقع هو أهم عامل في تحديد قيمة العقار.</p><h3>3. تحقق من المستندات القانونية</h3><p>تأكد من سلامة جميع الأوراق والمستندات.</p>',
'<h2>Introduction</h2><p>Buying your first apartment is an important decision in anyones life. In this article, we review the most important tips to consider.</p><h3>1. Set Your Budget</h3><p>Before starting your search, determine your available budget accurately.</p><h3>2. Choose the Right Location</h3><p>Location is the most important factor in determining property value.</p><h3>3. Verify Legal Documents</h3><p>Make sure all papers and documents are in order.</p>',
1, 534),

(2, 4, 1, 'أفضل مناطق الاستثمار العقاري في مصر 2024', 'Best Real Estate Investment Areas in Egypt 2024', 'best-investment-areas-egypt-2024',
'تعرف على أفضل المناطق للاستثمار العقاري في مصر وأعلى العوائد المتوقعة.',
'Learn about the best areas for real estate investment in Egypt and the highest expected returns.',
'<h2>الاستثمار العقاري في مصر</h2><p>يعتبر الاستثمار العقاري من أفضل أنواع الاستثمار في مصر. إليك أفضل المناطق:</p><h3>1. العاصمة الإدارية الجديدة</h3><p>فرصة استثمارية واعدة مع توقعات بزيادة الأسعار.</p><h3>2. القاهرة الجديدة</h3><p>منطقة ناضجة مع طلب مستمر.</p><h3>3. الساحل الشمالي</h3><p>وجهة صيفية مميزة مع عوائد إيجارية عالية.</p>',
'<h2>Real Estate Investment in Egypt</h2><p>Real estate investment is one of the best types of investment in Egypt. Here are the best areas:</p><h3>1. New Administrative Capital</h3><p>A promising investment opportunity with expected price increases.</p><h3>2. New Cairo</h3><p>A mature area with continuous demand.</p><h3>3. North Coast</h3><p>A distinguished summer destination with high rental returns.</p>',
1, 423),

(3, 5, 1, 'دليل السكن في القاهرة الجديدة', 'Living Guide in New Cairo', 'living-guide-new-cairo',
'كل ما تحتاج معرفته عن السكن في القاهرة الجديدة من خدمات ومرافق ومواصلات.',
'Everything you need to know about living in New Cairo including services, facilities, and transportation.',
'<h2>القاهرة الجديدة</h2><p>تعتبر القاهرة الجديدة من أرقى المناطق السكنية في مصر.</p><h3>المميزات</h3><ul><li>بيئة هادئة وآمنة</li><li>مساحات خضراء واسعة</li><li>خدمات متكاملة</li><li>مدارس وجامعات دولية</li></ul><h3>الكمبوندات الشهيرة</h3><p>ماونتن فيو، هايد بارك، ميفيدا، وغيرها الكثير.</p>',
'<h2>New Cairo</h2><p>New Cairo is considered one of the most prestigious residential areas in Egypt.</p><h3>Advantages</h3><ul><li>Quiet and safe environment</li><li>Wide green spaces</li><li>Integrated services</li><li>International schools and universities</li></ul><h3>Famous Compounds</h3><p>Mountain View, Hyde Park, Mivida, and many more.</p>',
0, 312),

(4, 2, 1, 'حقوق وواجبات المستأجر والمالك', 'Rights and Duties of Tenant and Landlord', 'tenant-landlord-rights-duties',
'تعرف على حقوقك وواجباتك سواء كنت مستأجراً أو مالكاً للعقار.',
'Know your rights and duties whether you are a tenant or property owner.',
'<h2>عقد الإيجار</h2><p>عقد الإيجار هو الوثيقة الأساسية التي تحدد العلاقة بين المالك والمستأجر.</p><h3>حقوق المستأجر</h3><ul><li>السكن الآمن</li><li>الصيانة الدورية</li><li>الخصوصية</li></ul><h3>واجبات المستأجر</h3><ul><li>دفع الإيجار في موعده</li><li>الحفاظ على العقار</li><li>إخطار المالك بأي أعطال</li></ul>',
'<h2>Lease Agreement</h2><p>The lease agreement is the basic document that defines the relationship between landlord and tenant.</p><h3>Tenant Rights</h3><ul><li>Safe housing</li><li>Regular maintenance</li><li>Privacy</li></ul><h3>Tenant Duties</h3><ul><li>Pay rent on time</li><li>Maintain the property</li><li>Notify landlord of any damages</li></ul>',
0, 278);

-- =============================================
-- جدول المفضلة
-- =============================================
DROP TABLE IF EXISTS `favorites`;
CREATE TABLE `favorites` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` bigint(20) UNSIGNED NOT NULL,
    `property_id` bigint(20) UNSIGNED NOT NULL,
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `favorites_user_property_unique` (`user_id`, `property_id`),
    KEY `favorites_property_id_foreign` (`property_id`),
    CONSTRAINT `favorites_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `favorites_property_id_foreign` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- بيانات المفضلة
INSERT INTO `favorites` (`user_id`, `property_id`) VALUES
(3, 1),
(3, 3),
(3, 6);

-- =============================================
-- جدول الإعدادات
-- =============================================
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `key` varchar(255) NOT NULL,
    `value` text DEFAULT NULL,
    `type` varchar(50) DEFAULT 'text',
    `group` varchar(50) DEFAULT 'general',
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- بيانات الإعدادات
INSERT INTO `settings` (`key`, `value`, `type`, `group`) VALUES
('site_name_ar', 'بي تاج', 'text', 'general'),
('site_name_en', 'BeTaj', 'text', 'general'),
('site_description_ar', 'منصتك الموثوقة للعقارات في مصر', 'textarea', 'general'),
('site_description_en', 'Your trusted real estate platform in Egypt', 'textarea', 'general'),
('site_email', 'info@betag.com', 'email', 'contact'),
('site_phone', '+20 123 456 7890', 'text', 'contact'),
('site_whatsapp', '+201234567890', 'text', 'contact'),
('site_address_ar', 'القاهرة، مصر', 'text', 'contact'),
('site_address_en', 'Cairo, Egypt', 'text', 'contact'),
('facebook_url', 'https://facebook.com/betag', 'url', 'social'),
('twitter_url', 'https://twitter.com/betag', 'url', 'social'),
('instagram_url', 'https://instagram.com/betag', 'url', 'social'),
('linkedin_url', 'https://linkedin.com/company/betag', 'url', 'social'),
('youtube_url', 'https://youtube.com/betag', 'url', 'social'),
('working_hours_ar', 'السبت - الخميس: 9 صباحاً - 9 مساءً', 'text', 'contact'),
('working_hours_en', 'Sat - Thu: 9 AM - 9 PM', 'text', 'contact');

-- =============================================
-- جدول الجلسات (Laravel)
-- =============================================
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
    `id` varchar(255) NOT NULL,
    `user_id` bigint(20) UNSIGNED DEFAULT NULL,
    `ip_address` varchar(45) DEFAULT NULL,
    `user_agent` text DEFAULT NULL,
    `payload` longtext NOT NULL,
    `last_activity` int(11) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `sessions_user_id_index` (`user_id`),
    KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- جدول الكاش (Laravel)
-- =============================================
DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache` (
    `key` varchar(255) NOT NULL,
    `value` mediumtext NOT NULL,
    `expiration` int(11) NOT NULL,
    PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks` (
    `key` varchar(255) NOT NULL,
    `owner` varchar(255) NOT NULL,
    `expiration` int(11) NOT NULL,
    PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- جدول الوظائف (Laravel Queue)
-- =============================================
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `queue` varchar(255) NOT NULL,
    `payload` longtext NOT NULL,
    `attempts` tinyint(3) UNSIGNED NOT NULL,
    `reserved_at` int(10) UNSIGNED DEFAULT NULL,
    `available_at` int(10) UNSIGNED NOT NULL,
    `created_at` int(10) UNSIGNED NOT NULL,
    PRIMARY KEY (`id`),
    KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `uuid` varchar(255) NOT NULL,
    `connection` text NOT NULL,
    `queue` text NOT NULL,
    `payload` longtext NOT NULL,
    `exception` longtext NOT NULL,
    `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- =============================================
-- انتهى - BeTaj Database Ready!
-- كلمة المرور الافتراضية لجميع المستخدمين: password
-- =============================================
