<?php
/**
 * BeTaj Real Estate Platform - Installation Script
 * قم بتشغيل هذا الملف مرة واحدة لإعداد قاعدة البيانات
 *
 * الاستخدام: افتح الرابط في المتصفح أو نفذ من سطر الأوامر
 * php install.php
 */

// منع إعادة التثبيت
$lockFile = __DIR__ . '/.installed';

// التحقق من الوصول
$isCLI = php_sapi_name() === 'cli';
if (!$isCLI) {
    header('Content-Type: text/html; charset=utf-8');
}

function output($message, $type = 'info') {
    global $isCLI;
    $colors = [
        'success' => $isCLI ? "\033[32m" : '<span style="color:green">',
        'error' => $isCLI ? "\033[31m" : '<span style="color:red">',
        'warning' => $isCLI ? "\033[33m" : '<span style="color:orange">',
        'info' => $isCLI ? "\033[36m" : '<span style="color:blue">'
    ];
    $reset = $isCLI ? "\033[0m" : '</span>';
    $icon = match($type) {
        'success' => '✓',
        'error' => '✗',
        'warning' => '⚠',
        default => '→'
    };

    if ($isCLI) {
        echo $colors[$type] . "$icon $message" . $reset . PHP_EOL;
    } else {
        echo $colors[$type] . "$icon $message" . $reset . "<br>";
    }
}

// بدء التثبيت
if (!$isCLI) {
    echo '<!DOCTYPE html><html dir="rtl"><head><meta charset="UTF-8"><title>تثبيت بي تاج</title>
    <style>body{font-family:Cairo,Arial,sans-serif;padding:40px;background:#f5f5f5;direction:rtl;}
    .container{max-width:800px;margin:0 auto;background:#fff;padding:30px;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,0.1);}
    h1{color:#2563eb;}.step{padding:10px 0;border-bottom:1px solid #eee;}
    .success{color:green;}.error{color:red;}.warning{color:orange;}.info{color:blue;}
    </style></head><body><div class="container"><h1>🏠 تثبيت منصة بي تاج العقارية</h1>';
}

echo PHP_EOL;
output("=== بدء عملية التثبيت ===", 'info');
echo PHP_EOL;

// 1. فحص متطلبات النظام
output("فحص متطلبات النظام...", 'info');

$requirements = [
    'PHP Version >= 7.4' => version_compare(PHP_VERSION, '7.4.0', '>='),
    'PDO Extension' => extension_loaded('pdo'),
    'PDO MySQL' => extension_loaded('pdo_mysql'),
    'cURL Extension' => extension_loaded('curl'),
    'JSON Extension' => extension_loaded('json'),
    'mbstring Extension' => extension_loaded('mbstring'),
    'Writable config/' => is_writable(__DIR__ . '/config'),
    'Writable uploads/' => is_dir(__DIR__ . '/uploads') ? is_writable(__DIR__ . '/uploads') : true,
];

$allPassed = true;
foreach ($requirements as $req => $passed) {
    if ($passed) {
        output("$req", 'success');
    } else {
        output("$req - مطلوب!", 'error');
        $allPassed = false;
    }
}

if (!$allPassed) {
    output("يرجى تثبيت المتطلبات الناقصة أولاً", 'error');
    exit(1);
}

// إنشاء مجلد uploads إذا لم يكن موجوداً
$uploadDirs = ['uploads', 'uploads/properties', 'uploads/avatars', 'uploads/temp'];
foreach ($uploadDirs as $dir) {
    $path = __DIR__ . '/' . $dir;
    if (!is_dir($path)) {
        mkdir($path, 0755, true);
        output("تم إنشاء مجلد $dir", 'success');
    }
}

echo PHP_EOL;

// 2. قراءة إعدادات قاعدة البيانات
output("إعداد قاعدة البيانات...", 'info');

require_once __DIR__ . '/config/database.php';

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    output("تم الاتصال بـ MySQL", 'success');
} catch (PDOException $e) {
    output("فشل الاتصال: " . $e->getMessage(), 'error');
    output("تأكد من إعدادات config/database.php", 'warning');
    exit(1);
}

// إنشاء قاعدة البيانات إذا لم تكن موجودة
try {
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    output("تم إنشاء/التحقق من قاعدة البيانات: " . DB_NAME, 'success');
    $pdo->exec("USE `" . DB_NAME . "`");
} catch (PDOException $e) {
    output("خطأ في إنشاء قاعدة البيانات: " . $e->getMessage(), 'error');
    exit(1);
}

echo PHP_EOL;

// 3. تنفيذ ملف schema.sql
output("إنشاء الجداول الأساسية...", 'info');

$schemaFile = __DIR__ . '/config/schema.sql';
if (file_exists($schemaFile)) {
    $sql = file_get_contents($schemaFile);
    try {
        $pdo->exec($sql);
        output("تم إنشاء الجداول الأساسية", 'success');
    } catch (PDOException $e) {
        // تجاهل أخطاء الجداول الموجودة
        if (strpos($e->getMessage(), 'already exists') === false) {
            output("تحذير: " . $e->getMessage(), 'warning');
        }
    }
} else {
    output("ملف schema.sql غير موجود!", 'error');
}

// 4. تنفيذ ملف schema_ai.sql
output("إنشاء جداول AI و Scraping...", 'info');

$schemaAiFile = __DIR__ . '/config/schema_ai.sql';
if (file_exists($schemaAiFile)) {
    $sql = file_get_contents($schemaAiFile);
    try {
        $pdo->exec($sql);
        output("تم إنشاء جداول AI و Scraping", 'success');
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'already exists') === false) {
            output("تحذير: " . $e->getMessage(), 'warning');
        }
    }
} else {
    output("ملف schema_ai.sql غير موجود!", 'warning');
}

echo PHP_EOL;

// 5. التحقق من إنشاء الجداول
output("التحقق من الجداول...", 'info');

$requiredTables = [
    'users', 'properties', 'categories', 'locations', 'leads',
    'pages', 'seo_settings', 'settings', 'favorites',
    'ai_prompts', 'ai_content', 'ai_settings',
    'scraping_sources', 'scraped_data', 'scraping_jobs'
];

$stmt = $pdo->query("SHOW TABLES");
$existingTables = $stmt->fetchAll(PDO::FETCH_COLUMN);

foreach ($requiredTables as $table) {
    if (in_array($table, $existingTables)) {
        output("جدول $table ✓", 'success');
    } else {
        output("جدول $table غير موجود!", 'error');
    }
}

echo PHP_EOL;

// 6. إنشاء ملف .htaccess للأمان في مجلدات حساسة
output("إعداد ملفات الأمان...", 'info');

$htaccessContent = "Order deny,allow\nDeny from all";
$protectedDirs = ['config', 'includes'];

foreach ($protectedDirs as $dir) {
    $htaccessPath = __DIR__ . "/$dir/.htaccess";
    if (!file_exists($htaccessPath)) {
        file_put_contents($htaccessPath, $htaccessContent);
        output("تم حماية مجلد $dir", 'success');
    }
}

echo PHP_EOL;

// 7. ملخص ما بعد التثبيت
output("=== اكتمل التثبيت ===", 'success');
echo PHP_EOL;

output("معلومات تسجيل الدخول للأدمن:", 'info');
output("البريد: admin@betag.com", 'info');
output("كلمة المرور: admin123", 'warning');
output("⚠️ قم بتغيير كلمة المرور فوراً!", 'warning');

echo PHP_EOL;

output("الخطوات التالية:", 'info');
output("1. افتح /admin لتسجيل الدخول للوحة التحكم", 'info');
output("2. أضف API Key للـ AI في /admin/ai-settings.php", 'info');
output("3. عدّل إعدادات الموقع في /admin/settings.php", 'info');
output("4. أضف بيانات Google Analytics و Facebook Pixel", 'info');
output("5. احذف هذا الملف install.php للأمان!", 'warning');

echo PHP_EOL;

// إنشاء ملف القفل
file_put_contents($lockFile, date('Y-m-d H:i:s'));

if (!$isCLI) {
    echo '<br><br><a href="/admin" style="background:#2563eb;color:#fff;padding:15px 30px;border-radius:10px;text-decoration:none;font-weight:bold;">
    → الذهاب للوحة التحكم</a>';
    echo '</div></body></html>';
}
