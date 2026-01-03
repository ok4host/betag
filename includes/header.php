<?php
/**
 * Site Header Template
 */
require_once __DIR__ . '/functions.php';

$settings = getSettings();
$siteUrl = SITE_URL;

// Get current page SEO
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$seo = getSeoSettings($currentPage) ?: [];

// Default SEO
$pageTitle = $pageTitle ?? $seo['meta_title'] ?? $settings['site_name'] . ' - ' . $settings['site_tagline'];
$pageDescription = $pageDescription ?? $seo['meta_description'] ?? 'أكبر منصة عقارية في مصر للبيع والإيجار';
$pageKeywords = $pageKeywords ?? $seo['meta_keywords'] ?? 'عقارات مصر، شقق للبيع، فلل، إيجار';
$ogImage = $ogImage ?? $seo['og_image'] ?? $siteUrl . '/images/og-image.jpg';
$canonicalUrl = $canonicalUrl ?? $siteUrl . $_SERVER['REQUEST_URI'];

// Start session for favorites
startSession();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Primary SEO -->
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <meta name="description" content="<?= htmlspecialchars($pageDescription) ?>">
    <meta name="keywords" content="<?= htmlspecialchars($pageKeywords) ?>">
    <meta name="author" content="<?= htmlspecialchars($settings['site_name'] ?? 'بي تاج') ?>">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?= htmlspecialchars($canonicalUrl) ?>">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= htmlspecialchars($canonicalUrl) ?>">
    <meta property="og:title" content="<?= htmlspecialchars($pageTitle) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($pageDescription) ?>">
    <meta property="og:image" content="<?= htmlspecialchars($ogImage) ?>">
    <meta property="og:locale" content="ar_EG">
    <meta property="og:site_name" content="<?= htmlspecialchars($settings['site_name'] ?? 'بي تاج') ?>">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars($pageTitle) ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($pageDescription) ?>">
    <meta name="twitter:image" content="<?= htmlspecialchars($ogImage) ?>">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/images/favicon.ico">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { 50: '#eff6ff', 100: '#dbeafe', 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8', 900: '#1e3a8a' }
                    }
                }
            }
        }
    </script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

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
        * { font-family: 'Cairo', sans-serif; }
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-5px); box-shadow: 0 15px 40px rgba(0,0,0,0.1); }
    </style>
</head>
<body class="bg-gray-50 text-right">

    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <a href="/" class="flex items-center gap-2">
                <div class="bg-primary-600 p-2 rounded-lg text-white">
                    <i class="fa-solid fa-house text-xl"></i>
                </div>
                <span class="text-2xl font-bold text-primary-900"><?= htmlspecialchars($settings['site_name'] ?? 'بي تاج') ?></span>
            </a>

            <nav class="hidden md:flex gap-8 items-center text-gray-600 font-medium">
                <a href="/" class="<?= $currentPage === 'index' ? 'text-primary-600 font-bold' : 'hover:text-primary-600' ?> transition">الرئيسية</a>
                <a href="/search?type=sale" class="hover:text-primary-600 transition">شقق للبيع</a>
                <a href="/search?type=rent" class="hover:text-primary-600 transition">شقق للإيجار</a>
                <a href="/compounds" class="<?= $currentPage === 'compounds' ? 'text-primary-600 font-bold' : 'hover:text-primary-600' ?> transition">دليل الكمبوندات</a>
                <a href="/new-projects" class="hover:text-primary-600 transition">مشاريع جديدة</a>
            </nav>

            <div class="hidden md:flex items-center gap-4">
                <?php if (isLoggedIn()): ?>
                <a href="/favorites" class="text-gray-600 hover:text-primary-600 relative">
                    <i class="fa-solid fa-heart text-xl"></i>
                    <span id="favorites-count" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center hidden">0</span>
                </a>
                <a href="/my-properties" class="text-gray-600 hover:text-primary-600">
                    <i class="fa-solid fa-building text-xl"></i>
                </a>
                <div class="dropdown relative">
                    <button class="text-gray-600 hover:text-primary-600" onclick="this.nextElementSibling.classList.toggle('hidden')">
                        <i class="fa-solid fa-user-circle text-xl"></i>
                    </button>
                    <div class="hidden absolute left-0 mt-2 bg-white rounded-lg shadow-lg py-2 w-48">
                        <a href="/profile" class="block px-4 py-2 hover:bg-gray-100">حسابي</a>
                        <a href="/my-properties" class="block px-4 py-2 hover:bg-gray-100">عقاراتي</a>
                        <a href="/favorites" class="block px-4 py-2 hover:bg-gray-100">المفضلة</a>
                        <hr class="my-2">
                        <a href="/logout" class="block px-4 py-2 text-red-600 hover:bg-gray-100">تسجيل خروج</a>
                    </div>
                </div>
                <?php else: ?>
                <a href="/login" class="text-primary-600 font-semibold hover:underline">تسجيل دخول</a>
                <?php endif; ?>
                <a href="/add-property" class="bg-primary-600 text-white px-5 py-2 rounded-lg hover:bg-primary-700 transition shadow-md">
                    <i class="fa-solid fa-plus ml-2"></i>أضف عقارك
                </a>
            </div>

            <button id="mobile-menu-btn" class="md:hidden text-gray-700 text-2xl">
                <i class="fa-solid fa-bars"></i>
            </button>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-white border-t p-4">
            <div class="flex flex-col gap-4 text-gray-700 font-medium">
                <a href="/">الرئيسية</a>
                <a href="/search?type=sale">شقق للبيع</a>
                <a href="/search?type=rent">شقق للإيجار</a>
                <a href="/compounds">دليل الكمبوندات</a>
                <hr>
                <?php if (isLoggedIn()): ?>
                <a href="/favorites"><i class="fa-solid fa-heart ml-2"></i>المفضلة</a>
                <a href="/my-properties"><i class="fa-solid fa-building ml-2"></i>عقاراتي</a>
                <a href="/logout" class="text-red-600">تسجيل خروج</a>
                <?php else: ?>
                <a href="/login" class="text-primary-600">تسجيل الدخول</a>
                <?php endif; ?>
                <a href="/add-property" class="bg-primary-600 text-white text-center py-3 rounded-lg">أضف عقارك</a>
            </div>
        </div>
    </header>

    <main>
