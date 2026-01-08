<?php
/**
 * Multi-Language System with SEO Support
 * نظام اللغات المتعددة مع دعم السيو
 */

class LanguageManager {

    const LANG_AR = 'ar';
    const LANG_EN = 'en';
    const DEFAULT_LANG = self::LANG_AR;
    const COOKIE_NAME = 'betag_lang';
    const COOKIE_EXPIRY = 31536000; // 1 year

    private static $translations = [];
    private static $currentLang = null;

    /**
     * Supported languages configuration
     */
    public static $languages = [
        'ar' => [
            'code' => 'ar',
            'name' => 'العربية',
            'name_en' => 'Arabic',
            'dir' => 'rtl',
            'locale' => 'ar_EG',
            'flag' => '🇪🇬',
            'hreflang' => 'ar'
        ],
        'en' => [
            'code' => 'en',
            'name' => 'English',
            'name_en' => 'English',
            'dir' => 'ltr',
            'locale' => 'en_US',
            'flag' => '🇺🇸',
            'hreflang' => 'en'
        ]
    ];

    /**
     * Initialize language system
     */
    public static function init(): string {
        // Priority: URL param > Cookie > Browser > Default
        $lang = self::detectLanguage();
        self::setLanguage($lang);
        return $lang;
    }

    /**
     * Detect language from various sources
     */
    public static function detectLanguage(): string {
        // 1. Check URL parameter
        if (isset($_GET['lang']) && self::isValidLanguage($_GET['lang'])) {
            return $_GET['lang'];
        }

        // 2. Check URL path (e.g., /en/property/...)
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        if (preg_match('#^/(ar|en)(/|$)#', $path, $matches)) {
            return $matches[1];
        }

        // 3. Check cookie
        if (isset($_COOKIE[self::COOKIE_NAME]) && self::isValidLanguage($_COOKIE[self::COOKIE_NAME])) {
            return $_COOKIE[self::COOKIE_NAME];
        }

        // 4. Check browser Accept-Language
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $browserLang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
            if (self::isValidLanguage($browserLang)) {
                return $browserLang;
            }
        }

        return self::DEFAULT_LANG;
    }

    /**
     * Set current language
     */
    public static function setLanguage(string $lang): void {
        if (!self::isValidLanguage($lang)) {
            $lang = self::DEFAULT_LANG;
        }

        self::$currentLang = $lang;

        // Set cookie
        setcookie(self::COOKIE_NAME, $lang, time() + self::COOKIE_EXPIRY, '/');

        // Load translations
        self::loadTranslations($lang);
    }

    /**
     * Get current language
     */
    public static function getCurrentLanguage(): string {
        return self::$currentLang ?? self::DEFAULT_LANG;
    }

    /**
     * Get current language config
     */
    public static function getCurrentConfig(): array {
        return self::$languages[self::getCurrentLanguage()];
    }

    /**
     * Check if language is valid
     */
    public static function isValidLanguage(string $lang): bool {
        return isset(self::$languages[$lang]);
    }

    /**
     * Check if current language is RTL
     */
    public static function isRTL(): bool {
        return self::getCurrentConfig()['dir'] === 'rtl';
    }

    /**
     * Get text direction
     */
    public static function getDirection(): string {
        return self::getCurrentConfig()['dir'];
    }

    /**
     * Load translations file
     */
    private static function loadTranslations(string $lang): void {
        $file = __DIR__ . '/../lang/' . $lang . '.php';
        if (file_exists($file)) {
            self::$translations = include $file;
        }
    }

    /**
     * Translate a key
     */
    public static function translate(string $key, array $params = []): string {
        $text = self::$translations[$key] ?? $key;

        // Replace parameters
        foreach ($params as $param => $value) {
            $text = str_replace(':' . $param, $value, $text);
        }

        return $text;
    }

    /**
     * Get hreflang tags for SEO
     */
    public static function getHreflangTags(): string {
        $siteUrl = rtrim(SITE_URL ?? 'https://example.com', '/');
        $currentPath = $_SERVER['REQUEST_URI'];

        // Remove language prefix if exists
        $cleanPath = preg_replace('#^/(ar|en)#', '', $currentPath);

        $tags = '';
        foreach (self::$languages as $code => $config) {
            $url = $siteUrl . '/' . $code . $cleanPath;
            $tags .= '<link rel="alternate" hreflang="' . $config['hreflang'] . '" href="' . htmlspecialchars($url) . '">' . "\n    ";
        }

        // Add x-default (usually points to default language)
        $defaultUrl = $siteUrl . '/' . self::DEFAULT_LANG . $cleanPath;
        $tags .= '<link rel="alternate" hreflang="x-default" href="' . htmlspecialchars($defaultUrl) . '">';

        return $tags;
    }

    /**
     * Get language switcher HTML
     */
    public static function getSwitcher(): string {
        $currentLang = self::getCurrentLanguage();
        $currentConfig = self::$languages[$currentLang];
        $currentPath = $_SERVER['REQUEST_URI'];

        // Remove existing language prefix
        $cleanPath = preg_replace('#^/(ar|en)#', '', $currentPath);

        $html = '<div class="language-switcher relative">
            <button onclick="this.nextElementSibling.classList.toggle(\'hidden\')"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                <span class="text-lg">' . $currentConfig['flag'] . '</span>
                <span class="text-sm font-medium">' . $currentConfig['name'] . '</span>
                <i class="fa-solid fa-chevron-down text-xs"></i>
            </button>
            <div class="hidden absolute ' . ($currentLang === 'ar' ? 'left-0' : 'right-0') . ' mt-2 bg-white dark:bg-gray-800 rounded-lg shadow-lg py-2 w-40 z-50 border border-gray-200 dark:border-gray-700">';

        foreach (self::$languages as $code => $config) {
            $isActive = $code === $currentLang;
            $url = '/' . $code . $cleanPath;

            $html .= '<a href="' . htmlspecialchars($url) . '"
                         class="flex items-center gap-3 px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 ' . ($isActive ? 'bg-primary-50 dark:bg-primary-900' : '') . '">
                <span class="text-lg">' . $config['flag'] . '</span>
                <span class="text-sm">' . $config['name'] . '</span>
                ' . ($isActive ? '<i class="fa-solid fa-check text-primary-600 mr-auto"></i>' : '') . '
            </a>';
        }

        $html .= '</div></div>';

        return $html;
    }

    /**
     * Get URL for specific language
     */
    public static function getUrlForLanguage(string $lang): string {
        $currentPath = $_SERVER['REQUEST_URI'];
        $cleanPath = preg_replace('#^/(ar|en)#', '', $currentPath);
        return '/' . $lang . $cleanPath;
    }

    /**
     * Get canonical URL with language
     */
    public static function getCanonicalUrl(): string {
        $siteUrl = rtrim(SITE_URL ?? 'https://example.com', '/');
        $currentPath = $_SERVER['REQUEST_URI'];

        // Ensure language prefix
        if (!preg_match('#^/(ar|en)#', $currentPath)) {
            $currentPath = '/' . self::getCurrentLanguage() . $currentPath;
        }

        return $siteUrl . $currentPath;
    }

    /**
     * Get Open Graph locale
     */
    public static function getOgLocale(): string {
        return self::getCurrentConfig()['locale'];
    }

    /**
     * Get alternate locales for Open Graph
     */
    public static function getOgAlternateLocales(): array {
        $current = self::getCurrentLanguage();
        $alternates = [];

        foreach (self::$languages as $code => $config) {
            if ($code !== $current) {
                $alternates[] = $config['locale'];
            }
        }

        return $alternates;
    }
}

/**
 * Global translation helper function
 */
function __($key, array $params = []): string {
    return LanguageManager::translate($key, $params);
}

/**
 * Echo translation helper
 */
function _e($key, array $params = []): void {
    echo LanguageManager::translate($key, $params);
}
