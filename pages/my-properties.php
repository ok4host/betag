<?php
/**
 * My Properties Page - صفحة عقاراتي
 */

$pageTitle = $currentLang === 'ar' ? 'عقاراتي | بي تاج' : 'My Properties | BeTaj';
$pageDescription = $currentLang === 'ar' ? 'إدارة عقاراتك المعروضة على بي تاج' : 'Manage your properties listed on BeTaj';

require_once __DIR__ . '/../includes/header.php';

// Require login
if (!isLoggedIn()) {
    header('Location: /' . $currentLang . '/login?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

// Translations
$t = [
    'ar' => [
        'title' => 'عقاراتي',
        'add_property' => 'أضف عقار جديد',
        'all' => 'الكل',
        'active' => 'نشط',
        'pending' => 'قيد المراجعة',
        'sold' => 'تم البيع',
        'expired' => 'منتهي',
        'views' => 'مشاهدة',
        'inquiries' => 'استفسار',
        'edit' => 'تعديل',
        'delete' => 'حذف',
        'view' => 'عرض',
        'no_properties' => 'لم تقم بإضافة أي عقارات بعد',
        'start_adding' => 'ابدأ بإضافة عقارك الأول',
        'confirm_delete' => 'هل أنت متأكد من حذف هذا العقار؟',
        'deleted' => 'تم حذف العقار بنجاح',
        'sale' => 'للبيع',
        'rent' => 'للإيجار',
    ],
    'en' => [
        'title' => 'My Properties',
        'add_property' => 'Add New Property',
        'all' => 'All',
        'active' => 'Active',
        'pending' => 'Pending Review',
        'sold' => 'Sold',
        'expired' => 'Expired',
        'views' => 'views',
        'inquiries' => 'inquiries',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'view' => 'View',
        'no_properties' => 'You haven\'t added any properties yet',
        'start_adding' => 'Start by adding your first property',
        'confirm_delete' => 'Are you sure you want to delete this property?',
        'deleted' => 'Property deleted successfully',
        'sale' => 'For Sale',
        'rent' => 'For Rent',
    ]
];

$text = $t[$currentLang];

// Get user's properties
$pdo = Database::getInstance()->getConnection();
$properties = [];
$stats = ['total' => 0, 'active' => 0, 'pending' => 0, 'views' => 0, 'inquiries' => 0];

try {
    $stmt = $pdo->prepare("
        SELECT p.*, c.name_ar as category_name, l.name_ar as location_name
        FROM properties p
        LEFT JOIN categories c ON p.category_id = c.id
        LEFT JOIN locations l ON p.location_id = l.id
        WHERE p.user_id = ?
        ORDER BY p.created_at DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calculate stats
    foreach ($properties as $prop) {
        $stats['total']++;
        if ($prop['status'] === 'active') $stats['active']++;
        if ($prop['status'] === 'pending') $stats['pending']++;
        $stats['views'] += $prop['views_count'] ?? 0;
    }

    // Get inquiries count
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM leads WHERE property_id IN (SELECT id FROM properties WHERE user_id = ?)");
    $stmt->execute([$_SESSION['user_id']]);
    $stats['inquiries'] = $stmt->fetchColumn();
} catch (Exception $e) {
    // Demo data if database not available
}
?>

    <section class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900"><?= $text['title'] ?></h1>
                <p class="text-gray-500 mt-1"><?= __('welcome') ?>, <?= htmlspecialchars($_SESSION['user_name'] ?? '') ?></p>
            </div>
            <a href="/<?= $currentLang ?>/add-property" class="bg-primary-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-primary-700 transition inline-flex items-center gap-2">
                <i class="fa-solid fa-plus"></i>
                <?= $text['add_property'] ?>
            </a>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
            <div class="bg-white rounded-xl p-4 shadow-sm">
                <p class="text-3xl font-bold text-primary-600"><?= $stats['total'] ?></p>
                <p class="text-sm text-gray-500"><?= $text['all'] ?></p>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm">
                <p class="text-3xl font-bold text-green-600"><?= $stats['active'] ?></p>
                <p class="text-sm text-gray-500"><?= $text['active'] ?></p>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm">
                <p class="text-3xl font-bold text-yellow-600"><?= $stats['pending'] ?></p>
                <p class="text-sm text-gray-500"><?= $text['pending'] ?></p>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm">
                <p class="text-3xl font-bold text-blue-600"><?= number_format($stats['views']) ?></p>
                <p class="text-sm text-gray-500"><?= $text['views'] ?></p>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm">
                <p class="text-3xl font-bold text-purple-600"><?= $stats['inquiries'] ?></p>
                <p class="text-sm text-gray-500"><?= $text['inquiries'] ?></p>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="flex gap-2 mb-6 overflow-x-auto pb-2">
            <button class="property-filter active px-4 py-2 rounded-lg bg-primary-600 text-white font-medium" data-status="all">
                <?= $text['all'] ?>
            </button>
            <button class="property-filter px-4 py-2 rounded-lg bg-white text-gray-600 font-medium hover:bg-gray-100" data-status="active">
                <?= $text['active'] ?>
            </button>
            <button class="property-filter px-4 py-2 rounded-lg bg-white text-gray-600 font-medium hover:bg-gray-100" data-status="pending">
                <?= $text['pending'] ?>
            </button>
            <button class="property-filter px-4 py-2 rounded-lg bg-white text-gray-600 font-medium hover:bg-gray-100" data-status="sold">
                <?= $text['sold'] ?>
            </button>
        </div>

        <!-- Properties List -->
        <?php if (empty($properties)): ?>
        <div class="bg-white rounded-2xl shadow-md p-12 text-center">
            <i class="fa-solid fa-building text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-bold text-gray-700 mb-2"><?= $text['no_properties'] ?></h3>
            <p class="text-gray-500 mb-6"><?= $text['start_adding'] ?></p>
            <a href="/<?= $currentLang ?>/add-property" class="bg-primary-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-primary-700 transition inline-flex items-center gap-2">
                <i class="fa-solid fa-plus"></i>
                <?= $text['add_property'] ?>
            </a>
        </div>
        <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($properties as $property): ?>
            <div class="property-item bg-white rounded-xl shadow-sm overflow-hidden flex flex-col md:flex-row" data-status="<?= $property['status'] ?>">
                <div class="md:w-48 h-32 md:h-auto shrink-0">
                    <img src="<?= $property['featured_image'] ? '/uploads/properties/' . $property['featured_image'] : '/images/property-placeholder.jpg' ?>"
                         alt="<?= htmlspecialchars($property['title']) ?>"
                         class="w-full h-full object-cover">
                </div>
                <div class="flex-1 p-4 flex flex-col md:flex-row md:items-center gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="px-2 py-1 rounded text-xs font-medium
                                <?= $property['status'] === 'active' ? 'bg-green-100 text-green-700' : '' ?>
                                <?= $property['status'] === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' ?>
                                <?= $property['status'] === 'sold' ? 'bg-gray-100 text-gray-700' : '' ?>
                            ">
                                <?= $text[$property['status']] ?? $property['status'] ?>
                            </span>
                            <span class="px-2 py-1 rounded text-xs font-medium bg-primary-100 text-primary-700">
                                <?= $property['type'] === 'sale' ? $text['sale'] : $text['rent'] ?>
                            </span>
                        </div>
                        <h3 class="font-bold text-gray-900"><?= htmlspecialchars($property['title']) ?></h3>
                        <p class="text-sm text-gray-500">
                            <i class="fa-solid fa-location-dot <?= $isRTL ? 'ml-1' : 'mr-1' ?>"></i>
                            <?= htmlspecialchars($property['location_name'] ?? '') ?>
                        </p>
                        <p class="font-bold text-primary-600 mt-1"><?= number_format($property['price']) ?> <?= __('egp') ?></p>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-500">
                        <span><i class="fa-solid fa-eye <?= $isRTL ? 'ml-1' : 'mr-1' ?>"></i><?= number_format($property['views_count'] ?? 0) ?></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="/<?= $currentLang ?>/property/<?= $property['slug'] ?>" class="p-2 text-gray-500 hover:text-primary-600" title="<?= $text['view'] ?>">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                        <a href="/<?= $currentLang ?>/add-property?edit=<?= $property['id'] ?>" class="p-2 text-gray-500 hover:text-primary-600" title="<?= $text['edit'] ?>">
                            <i class="fa-solid fa-edit"></i>
                        </a>
                        <button onclick="deleteProperty(<?= $property['id'] ?>)" class="p-2 text-gray-500 hover:text-red-600" title="<?= $text['delete'] ?>">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </section>

    <script>
        // Filter properties
        document.querySelectorAll('.property-filter').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.property-filter').forEach(b => {
                    b.classList.remove('bg-primary-600', 'text-white', 'active');
                    b.classList.add('bg-white', 'text-gray-600');
                });
                this.classList.remove('bg-white', 'text-gray-600');
                this.classList.add('bg-primary-600', 'text-white', 'active');

                const status = this.dataset.status;
                document.querySelectorAll('.property-item').forEach(item => {
                    if (status === 'all' || item.dataset.status === status) {
                        item.style.display = 'flex';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });

        // Delete property
        async function deleteProperty(id) {
            if (!confirm('<?= $text['confirm_delete'] ?>')) return;

            try {
                const response = await fetch('/api/properties?action=delete&id=' + id, {
                    method: 'DELETE'
                });
                const result = await response.json();

                if (result.success) {
                    showToast('<?= $text['deleted'] ?>', 'success');
                    location.reload();
                } else {
                    showToast(result.message || '<?= __('error') ?>', 'error');
                }
            } catch (error) {
                showToast('<?= __('error') ?>', 'error');
            }
        }
    </script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
