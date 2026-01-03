<?php
/**
 * User Favorites Page
 */
require_once __DIR__ . '/../includes/functions.php';
startSession();

if (!isLoggedIn()) {
    header('Location: /login?redirect=/favorites');
    exit;
}

$pdo = Database::getInstance()->getConnection();
$userId = $_SESSION['user_id'];

// Get favorites
$stmt = $pdo->prepare("
    SELECT p.*, c.name_ar as category_name, c.icon as category_icon, l.name_ar as location_name,
           f.created_at as added_at
    FROM favorites f
    JOIN properties p ON f.property_id = p.id
    LEFT JOIN categories c ON p.category_id = c.id
    LEFT JOIN locations l ON p.location_id = l.id
    WHERE f.user_id = ? AND p.status = 'active'
    ORDER BY f.created_at DESC
");
$stmt->execute([$userId]);
$favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'المفضلة';
$currentPage = 'favorites';

require_once __DIR__ . '/../includes/header.php';
?>

<div class="bg-gray-50 min-h-screen py-8">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">
            <i class="fa-solid fa-heart text-red-500 ml-3"></i>المفضلة
        </h1>

        <?php if (empty($favorites)): ?>
        <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
            <i class="fa-regular fa-heart text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-2xl font-bold text-gray-600 mb-2">قائمة المفضلة فارغة</h3>
            <p class="text-gray-500 mb-6">ابدأ بإضافة العقارات التي تعجبك إلى المفضلة</p>
            <a href="/search" class="inline-block bg-primary-600 text-white px-6 py-3 rounded-xl hover:bg-primary-700 transition">
                <i class="fa-solid fa-search ml-2"></i>تصفح العقارات
            </a>
        </div>
        <?php else: ?>
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
            <p class="text-gray-600">لديك <strong><?= count($favorites) ?></strong> عقار في المفضلة</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($favorites as $property): ?>
            <div class="relative">
                <?php include __DIR__ . '/../includes/property-card.php'; ?>
                <div class="text-center mt-2 text-sm text-gray-500">
                    <i class="fa-regular fa-clock ml-1"></i>
                    أضيف <?= timeAgo($property['added_at']) ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
