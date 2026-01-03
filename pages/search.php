<?php
/**
 * Property Search Page
 */
$currentPage = 'search';

require_once __DIR__ . '/../includes/functions.php';
$pdo = Database::getInstance()->getConnection();

// Get filter parameters
$type = $_GET['type'] ?? '';
$category = $_GET['category'] ?? '';
$location = $_GET['location'] ?? '';
$priceMin = (int)($_GET['price_min'] ?? 0);
$priceMax = (int)($_GET['price_max'] ?? 0);
$areaMin = (int)($_GET['area_min'] ?? 0);
$areaMax = (int)($_GET['area_max'] ?? 0);
$bedrooms = $_GET['bedrooms'] ?? '';
$finishing = $_GET['finishing'] ?? '';
$featured = isset($_GET['featured']);
$sort = $_GET['sort'] ?? 'newest';
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 12;

// Build query
$where = ["p.status = 'active'"];
$params = [];

if ($type) {
    $where[] = "p.transaction_type = ?";
    $params[] = $type;
}

if ($category) {
    $where[] = "c.slug = ?";
    $params[] = $category;
}

if ($location) {
    $where[] = "(l.slug = ? OR l.parent_id = (SELECT id FROM locations WHERE slug = ?))";
    $params[] = $location;
    $params[] = $location;
}

if ($priceMin > 0) {
    $where[] = "p.price >= ?";
    $params[] = $priceMin;
}

if ($priceMax > 0) {
    $where[] = "p.price <= ?";
    $params[] = $priceMax;
}

if ($areaMin > 0) {
    $where[] = "p.area >= ?";
    $params[] = $areaMin;
}

if ($areaMax > 0) {
    $where[] = "p.area <= ?";
    $params[] = $areaMax;
}

if ($bedrooms && $bedrooms !== 'any') {
    if ($bedrooms === '4+') {
        $where[] = "p.bedrooms >= 4";
    } else {
        $where[] = "p.bedrooms = ?";
        $params[] = (int)$bedrooms;
    }
}

if ($finishing) {
    $where[] = "p.finishing = ?";
    $params[] = $finishing;
}

if ($featured) {
    $where[] = "p.is_featured = 1";
}

$whereClause = implode(' AND ', $where);

// Sorting
$orderBy = match($sort) {
    'price_asc' => 'p.price ASC',
    'price_desc' => 'p.price DESC',
    'area_asc' => 'p.area ASC',
    'area_desc' => 'p.area DESC',
    'oldest' => 'p.created_at ASC',
    default => 'p.created_at DESC'
};

// Count total results
$countStmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM properties p
    LEFT JOIN categories c ON p.category_id = c.id
    LEFT JOIN locations l ON p.location_id = l.id
    WHERE $whereClause
");
$countStmt->execute($params);
$totalResults = $countStmt->fetchColumn();
$totalPages = ceil($totalResults / $perPage);

// Get properties
$offset = ($page - 1) * $perPage;
$stmt = $pdo->prepare("
    SELECT p.*, c.name_ar as category_name, c.icon as category_icon, l.name_ar as location_name
    FROM properties p
    LEFT JOIN categories c ON p.category_id = c.id
    LEFT JOIN locations l ON p.location_id = l.id
    WHERE $whereClause
    ORDER BY $orderBy
    LIMIT $perPage OFFSET $offset
");
$stmt->execute($params);
$properties = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get categories and locations for filters
$categories = $pdo->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
$locations = $pdo->query("SELECT * FROM locations WHERE is_active = 1 AND type IN ('city', 'area') ORDER BY name_ar")->fetchAll(PDO::FETCH_ASSOC);

// SEO
$pageTitle = 'البحث عن عقارات';
if ($location) {
    $locName = $pdo->prepare("SELECT name_ar FROM locations WHERE slug = ?");
    $locName->execute([$location]);
    $locName = $locName->fetchColumn();
    if ($locName) $pageTitle .= ' في ' . $locName;
}
if ($type === 'sale') $pageTitle .= ' للبيع';
elseif ($type === 'rent') $pageTitle .= ' للإيجار';

$pageDescription = "ابحث في أكثر من " . number_format($totalResults) . " عقار " . ($type === 'sale' ? 'للبيع' : ($type === 'rent' ? 'للإيجار' : ''));

require_once __DIR__ . '/../includes/header.php';
?>

<div class="bg-gray-50 min-h-screen">
    <!-- Page Header -->
    <div class="bg-primary-600 text-white py-8">
        <div class="container mx-auto px-4">
            <h1 class="text-3xl font-bold mb-2">
                <i class="fa-solid fa-search ml-3"></i><?= htmlspecialchars($pageTitle) ?>
            </h1>
            <p class="text-primary-100">تم العثور على <?= number_format($totalResults) ?> عقار</p>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Filters Sidebar -->
            <div class="lg:w-1/4">
                <form method="GET" class="bg-white rounded-2xl shadow-lg p-6 sticky top-24">
                    <h3 class="font-bold text-lg mb-6 text-gray-800">
                        <i class="fa-solid fa-filter text-primary-600 ml-2"></i>تصفية النتائج
                    </h3>

                    <!-- Transaction Type -->
                    <div class="mb-6">
                        <label class="block text-gray-700 font-medium mb-2">نوع المعاملة</label>
                        <select name="type" class="w-full p-3 rounded-xl border-2 border-gray-200 focus:border-primary-500">
                            <option value="">الكل</option>
                            <option value="sale" <?= $type === 'sale' ? 'selected' : '' ?>>للبيع</option>
                            <option value="rent" <?= $type === 'rent' ? 'selected' : '' ?>>للإيجار</option>
                        </select>
                    </div>

                    <!-- Category -->
                    <div class="mb-6">
                        <label class="block text-gray-700 font-medium mb-2">نوع العقار</label>
                        <select name="category" class="w-full p-3 rounded-xl border-2 border-gray-200 focus:border-primary-500">
                            <option value="">الكل</option>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['slug'] ?>" <?= $category === $cat['slug'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name_ar']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Location -->
                    <div class="mb-6">
                        <label class="block text-gray-700 font-medium mb-2">المنطقة</label>
                        <select name="location" class="w-full p-3 rounded-xl border-2 border-gray-200 focus:border-primary-500">
                            <option value="">الكل</option>
                            <?php foreach ($locations as $loc): ?>
                            <option value="<?= $loc['slug'] ?>" <?= $location === $loc['slug'] ? 'selected' : '' ?>><?= htmlspecialchars($loc['name_ar']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Price Range -->
                    <div class="mb-6">
                        <label class="block text-gray-700 font-medium mb-2">السعر</label>
                        <div class="flex gap-2">
                            <input type="number" name="price_min" placeholder="من" value="<?= $priceMin ?: '' ?>" class="w-1/2 p-3 rounded-xl border-2 border-gray-200">
                            <input type="number" name="price_max" placeholder="إلى" value="<?= $priceMax ?: '' ?>" class="w-1/2 p-3 rounded-xl border-2 border-gray-200">
                        </div>
                    </div>

                    <!-- Area Range -->
                    <div class="mb-6">
                        <label class="block text-gray-700 font-medium mb-2">المساحة (م²)</label>
                        <div class="flex gap-2">
                            <input type="number" name="area_min" placeholder="من" value="<?= $areaMin ?: '' ?>" class="w-1/2 p-3 rounded-xl border-2 border-gray-200">
                            <input type="number" name="area_max" placeholder="إلى" value="<?= $areaMax ?: '' ?>" class="w-1/2 p-3 rounded-xl border-2 border-gray-200">
                        </div>
                    </div>

                    <!-- Bedrooms -->
                    <div class="mb-6">
                        <label class="block text-gray-700 font-medium mb-2">عدد الغرف</label>
                        <select name="bedrooms" class="w-full p-3 rounded-xl border-2 border-gray-200 focus:border-primary-500">
                            <option value="">الكل</option>
                            <option value="1" <?= $bedrooms === '1' ? 'selected' : '' ?>>1</option>
                            <option value="2" <?= $bedrooms === '2' ? 'selected' : '' ?>>2</option>
                            <option value="3" <?= $bedrooms === '3' ? 'selected' : '' ?>>3</option>
                            <option value="4+" <?= $bedrooms === '4+' ? 'selected' : '' ?>>4+</option>
                        </select>
                    </div>

                    <!-- Finishing -->
                    <div class="mb-6">
                        <label class="block text-gray-700 font-medium mb-2">التشطيب</label>
                        <select name="finishing" class="w-full p-3 rounded-xl border-2 border-gray-200 focus:border-primary-500">
                            <option value="">الكل</option>
                            <option value="super-lux" <?= $finishing === 'super-lux' ? 'selected' : '' ?>>سوبر لوكس</option>
                            <option value="lux" <?= $finishing === 'lux' ? 'selected' : '' ?>>لوكس</option>
                            <option value="semi-finished" <?= $finishing === 'semi-finished' ? 'selected' : '' ?>>نصف تشطيب</option>
                            <option value="not-finished" <?= $finishing === 'not-finished' ? 'selected' : '' ?>>بدون تشطيب</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full bg-primary-600 text-white p-3 rounded-xl hover:bg-primary-700 transition font-bold">
                        <i class="fa-solid fa-search ml-2"></i>بحث
                    </button>

                    <?php if (count($params) > 0): ?>
                    <a href="/search" class="block text-center text-gray-500 hover:text-primary-600 mt-4">
                        <i class="fa-solid fa-times ml-1"></i>مسح الفلاتر
                    </a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Results -->
            <div class="lg:w-3/4">
                <!-- Sort Bar -->
                <div class="bg-white rounded-xl shadow p-4 mb-6 flex justify-between items-center">
                    <span class="text-gray-600"><?= number_format($totalResults) ?> نتيجة</span>
                    <div class="flex items-center gap-4">
                        <span class="text-gray-600">ترتيب حسب:</span>
                        <select onchange="updateSort(this.value)" class="p-2 rounded-lg border border-gray-200">
                            <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>الأحدث</option>
                            <option value="oldest" <?= $sort === 'oldest' ? 'selected' : '' ?>>الأقدم</option>
                            <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>السعر: الأقل</option>
                            <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>السعر: الأعلى</option>
                            <option value="area_desc" <?= $sort === 'area_desc' ? 'selected' : '' ?>>المساحة: الأكبر</option>
                        </select>
                    </div>
                </div>

                <!-- Properties Grid -->
                <?php if (empty($properties)): ?>
                <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
                    <i class="fa-solid fa-search text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-2xl font-bold text-gray-600 mb-2">لا توجد نتائج</h3>
                    <p class="text-gray-500">جرب تغيير معايير البحث للحصول على نتائج</p>
                </div>
                <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($properties as $property): ?>
                    <?php include __DIR__ . '/../includes/property-card.php'; ?>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <div class="mt-8 flex justify-center">
                    <nav class="flex items-center gap-2">
                        <?php if ($page > 1): ?>
                        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>" class="px-4 py-2 rounded-lg bg-white shadow hover:bg-gray-50">
                            <i class="fa-solid fa-chevron-right"></i>
                        </a>
                        <?php endif; ?>

                        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"
                           class="px-4 py-2 rounded-lg <?= $i === $page ? 'bg-primary-600 text-white' : 'bg-white shadow hover:bg-gray-50' ?>">
                            <?= $i ?>
                        </a>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>" class="px-4 py-2 rounded-lg bg-white shadow hover:bg-gray-50">
                            <i class="fa-solid fa-chevron-left"></i>
                        </a>
                        <?php endif; ?>
                    </nav>
                </div>
                <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function updateSort(value) {
    const url = new URL(window.location);
    url.searchParams.set('sort', value);
    window.location = url.toString();
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
