<?php
/**
 * 404 Page Not Found
 */
$pageTitle = 'الصفحة غير موجودة';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="min-h-screen bg-gray-50 flex items-center justify-center py-16">
    <div class="text-center px-4">
        <div class="text-9xl font-bold text-primary-600 mb-4">404</div>
        <h1 class="text-3xl font-bold text-gray-800 mb-4">الصفحة غير موجودة</h1>
        <p class="text-gray-600 mb-8 max-w-md mx-auto">
            عذراً، الصفحة التي تبحث عنها غير موجودة أو تم نقلها
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="/" class="bg-primary-600 text-white px-8 py-3 rounded-xl hover:bg-primary-700 transition font-bold">
                <i class="fa-solid fa-home ml-2"></i>الصفحة الرئيسية
            </a>
            <a href="/search" class="bg-gray-200 text-gray-800 px-8 py-3 rounded-xl hover:bg-gray-300 transition font-bold">
                <i class="fa-solid fa-search ml-2"></i>البحث عن عقار
            </a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
