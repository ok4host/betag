<?php
/**
 * Dynamic XML Sitemap Generator with Multi-language Support
 * يولد خريطة الموقع تلقائياً من قاعدة البيانات مع دعم اللغات
 */

header('Content-Type: application/xml; charset=utf-8');

require_once __DIR__ . '/includes/functions.php';

$pdo = Database::getInstance()->getConnection();
$siteUrl = rtrim(SITE_URL, '/');

// Supported languages
$languages = ['ar', 'en'];
$defaultLang = 'ar';

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xhtml="http://www.w3.org/1999/xhtml"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">

<?php
/**
 * Helper function to output URL with hreflang alternates
 */
function outputUrl($siteUrl, $path, $changefreq, $priority, $languages, $lastmod = null, $images = []) {
    foreach ($languages as $lang) {
        $url = $siteUrl . '/' . $lang . $path;
        echo "    <url>\n";
        echo "        <loc>" . htmlspecialchars($url) . "</loc>\n";

        if ($lastmod) {
            echo "        <lastmod>" . $lastmod . "</lastmod>\n";
        }

        echo "        <changefreq>" . $changefreq . "</changefreq>\n";
        echo "        <priority>" . $priority . "</priority>\n";

        // Hreflang alternates
        foreach ($languages as $altLang) {
            $altUrl = $siteUrl . '/' . $altLang . $path;
            echo '        <xhtml:link rel="alternate" hreflang="' . $altLang . '" href="' . htmlspecialchars($altUrl) . '"/>' . "\n";
        }

        // x-default
        $defaultUrl = $siteUrl . '/ar' . $path;
        echo '        <xhtml:link rel="alternate" hreflang="x-default" href="' . htmlspecialchars($defaultUrl) . '"/>' . "\n";

        // Images
        foreach ($images as $image) {
            echo "        <image:image>\n";
            echo "            <image:loc>" . htmlspecialchars($image['loc']) . "</image:loc>\n";
            if (!empty($image['title'])) {
                echo "            <image:title>" . htmlspecialchars($image['title']) . "</image:title>\n";
            }
            echo "        </image:image>\n";
        }

        echo "    </url>\n";
    }
}

// الصفحة الرئيسية / Homepage
outputUrl($siteUrl, '/', 'daily', '1.0', $languages);

// صفحات ثابتة / Static pages
$staticPages = [
    ['url' => '/search', 'priority' => '0.9', 'changefreq' => 'daily'],
    ['url' => '/sale', 'priority' => '0.9', 'changefreq' => 'daily'],
    ['url' => '/rent', 'priority' => '0.9', 'changefreq' => 'daily'],
    ['url' => '/compounds', 'priority' => '0.8', 'changefreq' => 'weekly'],
    ['url' => '/new-projects', 'priority' => '0.8', 'changefreq' => 'weekly'],
    ['url' => '/blog', 'priority' => '0.8', 'changefreq' => 'daily'],
    ['url' => '/about', 'priority' => '0.5', 'changefreq' => 'monthly'],
    ['url' => '/contact', 'priority' => '0.5', 'changefreq' => 'monthly'],
    ['url' => '/add-property', 'priority' => '0.7', 'changefreq' => 'monthly'],
    ['url' => '/login', 'priority' => '0.3', 'changefreq' => 'monthly'],
    ['url' => '/register', 'priority' => '0.3', 'changefreq' => 'monthly'],
    ['url' => '/privacy', 'priority' => '0.3', 'changefreq' => 'yearly'],
    ['url' => '/terms', 'priority' => '0.3', 'changefreq' => 'yearly'],
];

foreach ($staticPages as $page) {
    outputUrl($siteUrl, $page['url'], $page['changefreq'], $page['priority'], $languages);
}

// المناطق / Locations
try {
    $locations = $pdo->query("
        SELECT slug, updated_at FROM locations
        WHERE is_active = 1 AND type IN ('city', 'area')
    ")->fetchAll(PDO::FETCH_ASSOC);

    foreach ($locations as $loc) {
        $lastmod = date('Y-m-d', strtotime($loc['updated_at']));
        outputUrl($siteUrl, '/location/' . $loc['slug'], 'weekly', '0.7', $languages, $lastmod);
    }
} catch (Exception $e) {}

// الفئات / Categories
try {
    $categories = $pdo->query("SELECT slug FROM categories WHERE is_active = 1")->fetchAll(PDO::FETCH_ASSOC);

    foreach ($categories as $cat) {
        outputUrl($siteUrl, '/category/' . $cat['slug'], 'weekly', '0.7', $languages);
    }
} catch (Exception $e) {}

// العقارات / Properties
try {
    $properties = $pdo->query("
        SELECT slug, featured_image, title, updated_at
        FROM properties
        WHERE status = 'active'
        ORDER BY created_at DESC
        LIMIT 10000
    ")->fetchAll(PDO::FETCH_ASSOC);

    foreach ($properties as $prop) {
        $lastmod = date('Y-m-d', strtotime($prop['updated_at']));
        $images = [];
        if ($prop['featured_image']) {
            $images[] = [
                'loc' => $siteUrl . '/uploads/properties/' . $prop['featured_image'],
                'title' => $prop['title']
            ];
        }
        outputUrl($siteUrl, '/property/' . $prop['slug'], 'weekly', '0.8', $languages, $lastmod, $images);
    }
} catch (Exception $e) {}

// تصنيفات المقالات / Article Categories
try {
    $articleCats = $pdo->query("SELECT slug FROM article_categories WHERE is_active = 1")->fetchAll(PDO::FETCH_ASSOC);

    foreach ($articleCats as $cat) {
        outputUrl($siteUrl, '/blog?category=' . $cat['slug'], 'weekly', '0.6', $languages);
    }
} catch (Exception $e) {}

// المقالات / Articles
try {
    $articles = $pdo->query("
        SELECT slug, featured_image, title, updated_at
        FROM articles
        WHERE status = 'published'
        ORDER BY published_at DESC
        LIMIT 5000
    ")->fetchAll(PDO::FETCH_ASSOC);

    foreach ($articles as $article) {
        $lastmod = date('Y-m-d', strtotime($article['updated_at']));
        $images = [];
        if ($article['featured_image']) {
            $images[] = [
                'loc' => $siteUrl . '/uploads/articles/' . $article['featured_image'],
                'title' => $article['title']
            ];
        }
        outputUrl($siteUrl, '/blog/' . $article['slug'], 'monthly', '0.7', $languages, $lastmod, $images);
    }
} catch (Exception $e) {}

// الصفحات الثابتة من قاعدة البيانات / Dynamic Pages
try {
    $pages = $pdo->query("SELECT slug, updated_at FROM pages WHERE is_active = 1")->fetchAll(PDO::FETCH_ASSOC);

    foreach ($pages as $page) {
        $lastmod = date('Y-m-d', strtotime($page['updated_at']));
        outputUrl($siteUrl, '/page/' . $page['slug'], 'monthly', '0.5', $languages, $lastmod);
    }
} catch (Exception $e) {}

// الكمبوندات / Compounds
try {
    $compounds = $pdo->query("
        SELECT slug, updated_at, featured_image, name_ar
        FROM locations
        WHERE type = 'compound' AND is_active = 1
        ORDER BY name_ar
    ")->fetchAll(PDO::FETCH_ASSOC);

    foreach ($compounds as $compound) {
        $lastmod = date('Y-m-d', strtotime($compound['updated_at']));
        $images = [];
        if (!empty($compound['featured_image'])) {
            $images[] = [
                'loc' => $siteUrl . '/uploads/compounds/' . $compound['featured_image'],
                'title' => $compound['name_ar']
            ];
        }
        outputUrl($siteUrl, '/location/' . $compound['slug'], 'weekly', '0.7', $languages, $lastmod, $images);
    }
} catch (Exception $e) {}
?>

</urlset>
