<?php
/**
 * Test File - احذف هذا الملف بعد التأكد من عمل الموقع
 */

echo "<h1>✅ PHP يعمل بشكل صحيح!</h1>";
echo "<p>PHP Version: " . PHP_VERSION . "</p>";

// Test database connection
echo "<h2>اختبار قاعدة البيانات:</h2>";

$configFile = __DIR__ . '/config/database.php';
if (file_exists($configFile)) {
    echo "<p>✅ ملف database.php موجود</p>";
    require_once $configFile;

    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS
        );
        echo "<p style='color:green'>✅ الاتصال بقاعدة البيانات ناجح!</p>";
    } catch (PDOException $e) {
        echo "<p style='color:red'>❌ فشل الاتصال: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color:red'>❌ ملف database.php غير موجود!</p>";
}

// Test .htaccess
echo "<h2>اختبار mod_rewrite:</h2>";
if (function_exists('apache_get_modules')) {
    $modules = apache_get_modules();
    if (in_array('mod_rewrite', $modules)) {
        echo "<p style='color:green'>✅ mod_rewrite مفعل</p>";
    } else {
        echo "<p style='color:red'>❌ mod_rewrite غير مفعل!</p>";
    }
} else {
    echo "<p>⚠️ لا يمكن التحقق من mod_rewrite (قد يكون مفعل)</p>";
}

// List files
echo "<h2>الملفات الموجودة:</h2>";
echo "<pre>";
$files = glob(__DIR__ . '/*');
foreach ($files as $file) {
    echo basename($file) . (is_dir($file) ? '/' : '') . "\n";
}
echo "</pre>";

// Check important files
echo "<h2>فحص الملفات المهمة:</h2>";
$importantFiles = [
    'index.php',
    'install.php',
    '.htaccess',
    'config/database.php',
    'includes/functions.php'
];

foreach ($importantFiles as $file) {
    $exists = file_exists(__DIR__ . '/' . $file);
    $status = $exists ? '✅' : '❌';
    $color = $exists ? 'green' : 'red';
    echo "<p style='color:$color'>$status $file</p>";
}

echo "<hr>";
echo "<p><a href='install.php'>➡️ اذهب لصفحة التثبيت</a></p>";
echo "<p><a href='index.php'>➡️ اذهب للصفحة الرئيسية</a></p>";
?>
