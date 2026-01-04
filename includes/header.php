<?php
/**
 * Site Header Template with Multi-language & Theme Support
 */
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/seo-schemas.php';
require_once __DIR__ . '/theme.php';
require_once __DIR__ . '/language.php';

// Initialize language
$currentLang = LanguageManager::init();
$langConfig = LanguageManager::getCurrentConfig();
$isRTL = LanguageManager::isRTL();
$dir = LanguageManager::getDirection();

$settings = getSettings();
$siteUrl = SITE_URL;

// Get current page SEO
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$seo = getSeoSettings($currentPage) ?: [];

// Default SEO
$pageTitle = $pageTitle ?? $seo['meta_title'] ?? $settings['site_name'] . ' - ' . $settings['site_tagline'];
$pageDescription = $pageDescription ?? $seo['meta_description'] ?? __('site_tagline');
$pageKeywords = $pageKeywords ?? $seo['meta_keywords'] ?? 'عقارات مصر، شقق للبيع، فلل، إيجار';
$ogImage = $ogImage ?? $seo['og_image'] ?? $siteUrl . '/images/og-image.jpg';
$canonicalUrl = $canonicalUrl ?? LanguageManager::getCanonicalUrl();
$robots = $robots ?? 'index, follow';

// Start session for favorites
startSession();
?>
<!DOCTYPE html>
<html lang="<?= $currentLang ?>" dir="<?= $dir ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Primary SEO -->
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <meta name="description" content="<?= htmlspecialchars($pageDescription) ?>">
    <meta name="keywords" content="<?= htmlspecialchars($pageKeywords) ?>">
    <meta name="author" content="<?= htmlspecialchars($settings['site_name'] ?? 'بي تاج') ?>">
    <meta name="robots" content="<?= $robots ?>">
    <link rel="canonical" href="<?= htmlspecialchars($canonicalUrl) ?>">

    <!-- Hreflang Tags for Multi-language SEO -->
    <?= LanguageManager::getHreflangTags() ?>

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= htmlspecialchars($canonicalUrl) ?>">
    <meta property="og:title" content="<?= htmlspecialchars($pageTitle) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($pageDescription) ?>">
    <meta property="og:image" content="<?= htmlspecialchars($ogImage) ?>">
    <meta property="og:locale" content="<?= LanguageManager::getOgLocale() ?>">
    <?php foreach (LanguageManager::getOgAlternateLocales() as $altLocale): ?>
    <meta property="og:locale:alternate" content="<?= $altLocale ?>">
    <?php endforeach; ?>
    <meta property="og:site_name" content="<?= htmlspecialchars($settings['site_name'] ?? 'بي تاج') ?>">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars($pageTitle) ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($pageDescription) ?>">
    <meta name="twitter:image" content="<?= htmlspecialchars($ogImage) ?>">

    <!-- Schema.org JSON-LD -->
    <?= SeoSchemas::render(SeoSchemas::organization()) ?>
    <?= SeoSchemas::render(SeoSchemas::website()) ?>
    <?php if ($currentPage === 'index'): ?>
    <?= SeoSchemas::render(SeoSchemas::localBusiness()) ?>
    <?php endif; ?>
    <?php if (isset($pageSchemas) && is_array($pageSchemas)): ?>
    <?php foreach ($pageSchemas as $schema): ?>
    <?= SeoSchemas::render($schema) ?>
    <?php endforeach; ?>
    <?php endif; ?>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/images/favicon.ico">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: { 50: '#eff6ff', 100: '#dbeafe', 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8', 900: '#1e3a8a' }
                    }
                }
            }
        }
    </script>

    <!-- Theme CSS Variables -->
    <?= ThemeManager::getCssVariables() ?>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <?php if (!empty($settings['google_analytics_id'])): ?>
    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?= $settings['google_analytics_id'] ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '<?= $settings['google_analytics_id'] ?>');
    </script>
    <?php endif; ?>

    <?php if (!empty($settings['facebook_pixel_id'])): ?>
    <!-- Facebook Pixel -->
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '<?= $settings['facebook_pixel_id'] ?>');
        fbq('track', 'PageView');
    </script>
    <?php endif; ?>

    <style>
        * { font-family: <?= $isRTL ? "'Cairo'" : "'Inter'" ?>, sans-serif; }
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-5px); box-shadow: var(--shadow-lg); }

        /* RTL/LTR specific styles */
        <?php if (!$isRTL): ?>
        .mr-auto { margin-left: auto; margin-right: 0; }
        .ml-auto { margin-right: auto; margin-left: 0; }
        <?php endif; ?>

        /* Language switcher dropdown */
        .language-switcher .dropdown-menu {
            <?= $isRTL ? 'left: 0;' : 'right: 0;' ?>
        }
    </style>

    <!-- Theme JavaScript -->
    <?= ThemeManager::getJavaScript() ?>
</head>
<body class="bg-gray-50 <?= $isRTL ? 'text-right' : 'text-left' ?>">

    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50 border-b border-gray-100">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <a href="/<?= $currentLang ?>/" class="flex items-center gap-2">
                <div class="bg-primary-600 p-2 rounded-lg text-white">
                    <i class="fa-solid fa-house text-xl"></i>
                </div>
                <span class="text-2xl font-bold text-primary-900"><?= htmlspecialchars($settings['site_name'] ?? __('site_name')) ?></span>
            </a>

            <nav class="hidden lg:flex gap-6 items-center text-gray-600 font-medium">
                <a href="/<?= $currentLang ?>/" class="<?= $currentPage === 'index' ? 'text-primary-600 font-bold' : 'hover:text-primary-600' ?> transition"><?= __('nav_home') ?></a>
                <a href="/<?= $currentLang ?>/search?type=sale" class="hover:text-primary-600 transition"><?= __('nav_apartments_sale') ?></a>
                <a href="/<?= $currentLang ?>/search?type=rent" class="hover:text-primary-600 transition"><?= __('nav_apartments_rent') ?></a>
                <a href="/<?= $currentLang ?>/compounds" class="<?= $currentPage === 'compounds' ? 'text-primary-600 font-bold' : 'hover:text-primary-600' ?> transition"><?= __('nav_compounds') ?></a>
                <a href="/<?= $currentLang ?>/new-projects" class="hover:text-primary-600 transition"><?= __('nav_new_projects') ?></a>
                <a href="/<?= $currentLang ?>/blog" class="<?= $currentPage === 'blog' ? 'text-primary-600 font-bold' : 'hover:text-primary-600' ?> transition"><?= __('nav_blog') ?></a>
            </nav>

            <div class="hidden md:flex items-center gap-3">
                <!-- Theme Toggle -->
                <?= ThemeManager::getToggleButton() ?>

                <!-- Language Switcher -->
                <?= LanguageManager::getSwitcher() ?>

                <?php if (isLoggedIn()): ?>
                <a href="/<?= $currentLang ?>/favorites" class="text-gray-600 hover:text-primary-600 relative p-2">
                    <i class="fa-solid fa-heart text-xl"></i>
                    <span id="favorites-count" class="absolute -top-1 -<?= $isRTL ? 'left' : 'right' ?>-1 bg-red-500 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center hidden">0</span>
                </a>
                <a href="/<?= $currentLang ?>/my-properties" class="text-gray-600 hover:text-primary-600 p-2">
                    <i class="fa-solid fa-building text-xl"></i>
                </a>
                <div class="dropdown relative">
                    <button class="text-gray-600 hover:text-primary-600 p-2" onclick="this.nextElementSibling.classList.toggle('hidden')">
                        <i class="fa-solid fa-user-circle text-xl"></i>
                    </button>
                    <div class="hidden absolute <?= $isRTL ? 'left-0' : 'right-0' ?> mt-2 bg-white rounded-lg shadow-lg py-2 w-48 border border-gray-100">
                        <a href="/<?= $currentLang ?>/profile" class="block px-4 py-2 hover:bg-gray-100"><?= __('profile') ?></a>
                        <a href="/<?= $currentLang ?>/my-properties" class="block px-4 py-2 hover:bg-gray-100"><?= __('my_properties') ?></a>
                        <a href="/<?= $currentLang ?>/favorites" class="block px-4 py-2 hover:bg-gray-100"><?= __('favorites') ?></a>
                        <hr class="my-2">
                        <a href="/<?= $currentLang ?>/logout" class="block px-4 py-2 text-red-600 hover:bg-gray-100"><?= __('logout') ?></a>
                    </div>
                </div>
                <?php else: ?>
                <a href="/<?= $currentLang ?>/login" class="text-primary-600 font-semibold hover:underline"><?= __('login') ?></a>
                <?php endif; ?>
                <a href="/<?= $currentLang ?>/add-property" class="bg-primary-600 text-white px-5 py-2 rounded-lg hover:bg-primary-700 transition shadow-md">
                    <i class="fa-solid fa-plus <?= $isRTL ? 'ml-2' : 'mr-2' ?>"></i><?= __('add_property') ?>
                </a>
            </div>

            <button id="mobile-menu-btn" class="lg:hidden text-gray-700 text-2xl p-2">
                <i class="fa-solid fa-bars"></i>
            </button>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden lg:hidden bg-white border-t p-4">
            <div class="flex flex-col gap-4 text-gray-700 font-medium">
                <a href="/<?= $currentLang ?>/"><?= __('nav_home') ?></a>
                <a href="/<?= $currentLang ?>/search?type=sale"><?= __('nav_apartments_sale') ?></a>
                <a href="/<?= $currentLang ?>/search?type=rent"><?= __('nav_apartments_rent') ?></a>
                <a href="/<?= $currentLang ?>/compounds"><?= __('nav_compounds') ?></a>
                <a href="/<?= $currentLang ?>/blog"><?= __('nav_blog') ?></a>
                <hr>

                <!-- Mobile Theme & Language -->
                <div class="flex items-center justify-between">
                    <span class="text-gray-500"><?= __('change_theme') ?></span>
                    <?= ThemeManager::getToggleButton() ?>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-500"><?= __('language') ?></span>
                    <div class="flex gap-2">
                        <?php foreach (LanguageManager::$languages as $code => $config): ?>
                        <a href="<?= LanguageManager::getUrlForLanguage($code) ?>"
                           class="px-3 py-1 rounded <?= $code === $currentLang ? 'bg-primary-600 text-white' : 'bg-gray-100' ?>">
                            <?= $config['flag'] ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <hr>

                <?php if (isLoggedIn()): ?>
                <a href="/<?= $currentLang ?>/favorites"><i class="fa-solid fa-heart <?= $isRTL ? 'ml-2' : 'mr-2' ?>"></i><?= __('favorites') ?></a>
                <a href="/<?= $currentLang ?>/my-properties"><i class="fa-solid fa-building <?= $isRTL ? 'ml-2' : 'mr-2' ?>"></i><?= __('my_properties') ?></a>
                <a href="/<?= $currentLang ?>/logout" class="text-red-600"><?= __('logout') ?></a>
                <?php else: ?>
                <a href="/<?= $currentLang ?>/login" class="text-primary-600"><?= __('login') ?></a>
                <?php endif; ?>
                <a href="/<?= $currentLang ?>/add-property" class="bg-primary-600 text-white text-center py-3 rounded-lg"><?= __('add_property') ?></a>
            </div>
        </div>
    </header>

    <main>

    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
            document.getElementById('mobile-menu')?.classList.toggle('hidden');
        });
    </script>
