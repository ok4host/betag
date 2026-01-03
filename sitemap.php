<?php
/**
 * Dynamic XML Sitemap Generator
 * يولد خريطة الموقع تلقائياً من قاعدة البيانات
 */

header('Content-Type: application/xml; charset=utf-8');

require_once __DIR__ . '/includes/functions.php';

$pdo = Database::getInstance()->getConnection();
$siteUrl = SITE_URL;

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">

    <!-- الصفحة الرئيسية -->
    <url>
        <loc><?= $siteUrl ?>/</loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>

    <!-- صفحات ثابتة -->
    <?php
    $staticPages = [
        ['url' => '/search', 'priority' => '0.9', 'changefreq' => 'daily'],
        ['url' => '/sale', 'priority' => '0.9', 'changefreq' => 'daily'],
        ['url' => '/rent', 'priority' => '0.9', 'changefreq' => 'daily'],
        ['url' => '/compounds', 'priority' => '0.8', 'changefreq' => 'weekly'],
        ['url' => '/new-projects', 'priority' => '0.8', 'changefreq' => 'weekly'],
        ['url' => '/about', 'priority' => '0.5', 'changefreq' => 'monthly'],
        ['url' => '/contact', 'priority' => '0.5', 'changefreq' => 'monthly'],
        ['url' => '/add-property', 'priority' => '0.7', 'changefreq' => 'monthly'],
    ];

    foreach ($staticPages as $page):
    ?>
    <url>
        <loc><?= $siteUrl . $page['url'] ?></loc>
        <changefreq><?= $page['changefreq'] ?></changefreq>
        <priority><?= $page['priority'] ?></priority>
    </url>
    <?php endforeach; ?>

    <!-- المناطق -->
    <?php
    $locations = $pdo->query("
        SELECT slug, updated_at FROM locations
        WHERE is_active = 1 AND type IN ('city', 'area')
    ")->fetchAll(PDO::FETCH_ASSOC);

    foreach ($locations as $loc):
    ?>
    <url>
        <loc><?= $siteUrl ?>/location/<?= $loc['slug'] ?></loc>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
    <?php endforeach; ?>

    <!-- الفئات -->
    <?php
    $categories = $pdo->query("SELECT slug FROM categories WHERE is_active = 1")->fetchAll(PDO::FETCH_ASSOC);

    foreach ($categories as $cat):
    ?>
    <url>
        <loc><?= $siteUrl ?>/category/<?= $cat['slug'] ?></loc>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
    <?php endforeach; ?>

    <!-- العقارات -->
    <?php
    $properties = $pdo->query("
        SELECT slug, featured_image, title, updated_at
        FROM properties
        WHERE status = 'active'
        ORDER BY created_at DESC
        LIMIT 10000
    ")->fetchAll(PDO::FETCH_ASSOC);

    foreach ($properties as $prop):
        $lastmod = date('Y-m-d', strtotime($prop['updated_at']));
    ?>
    <url>
        <loc><?= $siteUrl ?>/property/<?= $prop['slug'] ?></loc>
        <lastmod><?= $lastmod ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
        <?php if ($prop['featured_image']): ?>
        <image:image>
            <image:loc><?= htmlspecialchars($prop['featured_image']) ?></image:loc>
            <image:title><?= htmlspecialchars($prop['title']) ?></image:title>
        </image:image>
        <?php endif; ?>
    </url>
    <?php endforeach; ?>

</urlset>
