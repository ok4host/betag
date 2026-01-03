<?php
/**
 * Property Details Page
 */
require_once __DIR__ . '/../includes/functions.php';
$pdo = Database::getInstance()->getConnection();

// Get property by slug
$slug = $_GET['slug'] ?? '';
if (!$slug) {
    header('Location: /search');
    exit;
}

$stmt = $pdo->prepare("
    SELECT p.*, c.name_ar as category_name, c.icon as category_icon,
           l.name_ar as location_name, l.parent_id,
           u.name as owner_name, u.phone as owner_phone, u.company_name
    FROM properties p
    LEFT JOIN categories c ON p.category_id = c.id
    LEFT JOIN locations l ON p.location_id = l.id
    LEFT JOIN users u ON p.user_id = u.id
    WHERE p.slug = ? AND p.status = 'active'
");
$stmt->execute([$slug]);
$property = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$property) {
    http_response_code(404);
    include __DIR__ . '/404.php';
    exit;
}

// Increment views
$pdo->prepare("UPDATE properties SET views = views + 1 WHERE id = ?")->execute([$property['id']]);

// Parse data
$images = json_decode($property['images'] ?? '[]', true);
if ($property['featured_image']) {
    array_unshift($images, $property['featured_image']);
}
$images = array_unique($images);
if (empty($images)) {
    $images = ['https://via.placeholder.com/800x600?text=No+Image'];
}

$amenities = json_decode($property['amenities'] ?? '[]', true);
$amenityLabels = [
    'pool' => ['مسبح', 'fa-water-ladder'],
    'gym' => ['جيم', 'fa-dumbbell'],
    'garden' => ['حديقة', 'fa-tree'],
    'parking' => ['جراج', 'fa-square-parking'],
    'security' => ['أمن', 'fa-shield'],
    'elevator' => ['أسانسير', 'fa-elevator'],
    'ac' => ['تكييف', 'fa-snowflake'],
    'balcony' => ['بلكونة', 'fa-archway'],
    'kitchen' => ['مطبخ', 'fa-utensils'],
    'natural_gas' => ['غاز طبيعي', 'fa-fire-flame-simple'],
];

$finishingLabels = [
    'super-lux' => 'سوبر لوكس',
    'lux' => 'لوكس',
    'semi-finished' => 'نصف تشطيب',
    'not-finished' => 'بدون تشطيب'
];

// Check if favorite
$isFavorite = false;
if (isLoggedIn()) {
    $favStmt = $pdo->prepare("SELECT id FROM favorites WHERE user_id = ? AND property_id = ?");
    $favStmt->execute([$_SESSION['user_id'], $property['id']]);
    $isFavorite = (bool)$favStmt->fetch();
}

// Get similar properties
$stmt = $pdo->prepare("
    SELECT p.*, c.name_ar as category_name, c.icon as category_icon, l.name_ar as location_name
    FROM properties p
    LEFT JOIN categories c ON p.category_id = c.id
    LEFT JOIN locations l ON p.location_id = l.id
    WHERE p.status = 'active' AND p.id != ? AND (p.category_id = ? OR p.location_id = ?)
    ORDER BY RAND()
    LIMIT 4
");
$stmt->execute([$property['id'], $property['category_id'], $property['location_id']]);
$similarProperties = $stmt->fetchAll(PDO::FETCH_ASSOC);

// SEO
$pageTitle = $property['meta_title'] ?: $property['title'];
$pageDescription = $property['meta_description'] ?: mb_substr(strip_tags($property['description']), 0, 160);
$ogImage = $images[0];
$canonicalUrl = SITE_URL . '/property/' . $property['slug'];

require_once __DIR__ . '/../includes/header.php';
?>

<!-- Property Details -->
<div class="bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <!-- Breadcrumb -->
        <nav class="text-sm text-gray-500 mb-6">
            <a href="/" class="hover:text-primary-600">الرئيسية</a>
            <i class="fa-solid fa-chevron-left mx-2 text-xs"></i>
            <a href="/search" class="hover:text-primary-600">العقارات</a>
            <i class="fa-solid fa-chevron-left mx-2 text-xs"></i>
            <span class="text-gray-700"><?= htmlspecialchars(mb_substr($property['title'], 0, 40)) ?></span>
        </nav>

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Main Content -->
            <div class="lg:w-2/3">
                <!-- Image Gallery -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8">
                    <div class="relative">
                        <img id="mainImage" src="<?= htmlspecialchars($images[0]) ?>" alt="<?= htmlspecialchars($property['title']) ?>" class="w-full h-96 md:h-[500px] object-cover">

                        <!-- Badges -->
                        <div class="absolute top-4 right-4 flex gap-2">
                            <span class="bg-<?= $property['transaction_type'] === 'sale' ? 'green' : 'blue' ?>-500 text-white px-4 py-2 rounded-full font-medium">
                                <?= $property['transaction_type'] === 'sale' ? 'للبيع' : 'للإيجار' ?>
                            </span>
                            <?php if ($property['is_featured']): ?>
                            <span class="bg-yellow-500 text-white px-4 py-2 rounded-full font-medium">
                                <i class="fa-solid fa-star ml-1"></i>مميز
                            </span>
                            <?php endif; ?>
                        </div>

                        <!-- Favorite -->
                        <button onclick="toggleFavorite(<?= $property['id'] ?>, this)" class="absolute top-4 left-4 w-12 h-12 bg-white/90 rounded-full flex items-center justify-center hover:bg-white transition shadow-lg">
                            <i class="fa-<?= $isFavorite ? 'solid' : 'regular' ?> fa-heart text-<?= $isFavorite ? 'red' : 'gray' ?>-500 text-2xl"></i>
                        </button>
                    </div>

                    <!-- Thumbnails -->
                    <?php if (count($images) > 1): ?>
                    <div class="p-4 flex gap-2 overflow-x-auto">
                        <?php foreach ($images as $i => $img): ?>
                        <img src="<?= htmlspecialchars($img) ?>" onclick="document.getElementById('mainImage').src='<?= htmlspecialchars($img) ?>'" class="w-24 h-20 object-cover rounded-lg cursor-pointer hover:opacity-75 transition <?= $i === 0 ? 'ring-2 ring-primary-500' : '' ?>">
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Property Info -->
                <div class="bg-white rounded-2xl shadow-lg p-8 mb-8">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">
                                <?= htmlspecialchars($property['title']) ?>
                            </h1>
                            <p class="text-gray-500 text-lg">
                                <i class="fa-solid fa-location-dot text-primary-600 ml-2"></i>
                                <?= htmlspecialchars($property['location_name']) ?>
                                <?php if ($property['address']): ?>
                                - <?= htmlspecialchars($property['address']) ?>
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="text-left">
                            <div class="text-3xl font-bold text-primary-600">
                                <?= number_format($property['price']) ?>
                            </div>
                            <div class="text-gray-500">
                                جنيه
                                <?php if ($property['transaction_type'] === 'rent'): ?>
                                / <?= $property['rent_period'] === 'yearly' ? 'سنوياً' : 'شهرياً' ?>
                                <?php endif; ?>
                            </div>
                            <?php if ($property['price_negotiable']): ?>
                            <span class="text-green-600 text-sm"><i class="fa-solid fa-check ml-1"></i>قابل للتفاوض</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Quick Specs -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 p-6 bg-gray-50 rounded-xl mb-8">
                        <div class="text-center">
                            <i class="fa-solid fa-ruler-combined text-primary-600 text-2xl mb-2"></i>
                            <div class="font-bold text-xl"><?= number_format($property['area']) ?></div>
                            <div class="text-gray-500 text-sm">متر مربع</div>
                        </div>
                        <div class="text-center">
                            <i class="fa-solid fa-bed text-primary-600 text-2xl mb-2"></i>
                            <div class="font-bold text-xl"><?= $property['bedrooms'] ?: '-' ?></div>
                            <div class="text-gray-500 text-sm">غرف نوم</div>
                        </div>
                        <div class="text-center">
                            <i class="fa-solid fa-bath text-primary-600 text-2xl mb-2"></i>
                            <div class="font-bold text-xl"><?= $property['bathrooms'] ?: '-' ?></div>
                            <div class="text-gray-500 text-sm">حمامات</div>
                        </div>
                        <div class="text-center">
                            <i class="fa-solid fa-paint-roller text-primary-600 text-2xl mb-2"></i>
                            <div class="font-bold text-xl"><?= $finishingLabels[$property['finishing']] ?? $property['finishing'] ?></div>
                            <div class="text-gray-500 text-sm">التشطيب</div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-8">
                        <h2 class="text-xl font-bold text-gray-800 mb-4">
                            <i class="fa-solid fa-file-lines text-primary-600 ml-2"></i>الوصف
                        </h2>
                        <div class="text-gray-600 leading-relaxed whitespace-pre-line">
                            <?= nl2br(htmlspecialchars($property['description'] ?: 'لا يوجد وصف')) ?>
                        </div>
                    </div>

                    <!-- Amenities -->
                    <?php if (!empty($amenities)): ?>
                    <div class="mb-8">
                        <h2 class="text-xl font-bold text-gray-800 mb-4">
                            <i class="fa-solid fa-check-circle text-primary-600 ml-2"></i>المميزات
                        </h2>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            <?php foreach ($amenities as $amenity): ?>
                            <?php if (isset($amenityLabels[$amenity])): ?>
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                                <i class="fa-solid <?= $amenityLabels[$amenity][1] ?> text-primary-600 text-xl"></i>
                                <span><?= $amenityLabels[$amenity][0] ?></span>
                            </div>
                            <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Stats -->
                    <div class="flex items-center gap-6 text-gray-500 text-sm border-t pt-6">
                        <span><i class="fa-solid fa-eye ml-1"></i> <?= number_format($property['views']) ?> مشاهدة</span>
                        <span><i class="fa-solid fa-calendar ml-1"></i> <?= formatDate($property['created_at'], 'd/m/Y') ?></span>
                        <span><i class="fa-solid fa-tag ml-1"></i> <?= $property['category_name'] ?></span>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:w-1/3">
                <!-- Contact Card -->
                <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-24">
                    <h3 class="font-bold text-lg text-gray-800 mb-4">تواصل مع المعلن</h3>

                    <?php if ($property['company_name']): ?>
                    <p class="text-gray-600 mb-2"><i class="fa-solid fa-building ml-2"></i><?= htmlspecialchars($property['company_name']) ?></p>
                    <?php endif; ?>
                    <p class="text-gray-600 mb-6"><i class="fa-solid fa-user ml-2"></i><?= htmlspecialchars($property['owner_name']) ?></p>

                    <div class="space-y-3 mb-6">
                        <a href="tel:<?= $property['owner_phone'] ?>" onclick="trackCall(<?= $property['id'] ?>)" class="w-full flex items-center justify-center gap-2 bg-primary-600 text-white py-4 rounded-xl hover:bg-primary-700 transition font-bold">
                            <i class="fa-solid fa-phone"></i>اتصل الآن
                        </a>
                        <a href="https://wa.me/<?= preg_replace('/^0/', '20', $property['owner_phone']) ?>?text=<?= urlencode('مرحباً، أنا مهتم بـ ' . $property['title']) ?>" onclick="trackWhatsApp(<?= $property['id'] ?>)" target="_blank" class="w-full flex items-center justify-center gap-2 bg-green-500 text-white py-4 rounded-xl hover:bg-green-600 transition font-bold">
                            <i class="fa-brands fa-whatsapp text-xl"></i>واتساب
                        </a>
                    </div>

                    <!-- Contact Form -->
                    <form id="propertyLeadForm" class="border-t pt-6">
                        <input type="hidden" name="property_id" value="<?= $property['id'] ?>">
                        <div class="mb-4">
                            <input type="text" name="name" required placeholder="الاسم *" class="w-full p-3 rounded-xl border-2 border-gray-200 focus:border-primary-500 focus:outline-none">
                        </div>
                        <div class="mb-4">
                            <input type="tel" name="phone" required placeholder="رقم الهاتف *" class="w-full p-3 rounded-xl border-2 border-gray-200 focus:border-primary-500 focus:outline-none" dir="ltr">
                        </div>
                        <div class="mb-4">
                            <textarea name="message" rows="3" placeholder="رسالتك..." class="w-full p-3 rounded-xl border-2 border-gray-200 focus:border-primary-500 focus:outline-none"></textarea>
                        </div>
                        <button type="submit" class="w-full bg-gray-800 text-white py-3 rounded-xl hover:bg-gray-900 transition font-bold">
                            <i class="fa-solid fa-paper-plane ml-2"></i>إرسال رسالة
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Similar Properties -->
        <?php if (!empty($similarProperties)): ?>
        <div class="mt-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-8">
                <i class="fa-solid fa-clone text-primary-600 ml-3"></i>عقارات مشابهة
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach ($similarProperties as $property): ?>
                <?php include __DIR__ . '/../includes/property-card.php'; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- JSON-LD Schema -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "RealEstateListing",
    "name": "<?= htmlspecialchars($property['title']) ?>",
    "description": "<?= htmlspecialchars(mb_substr($property['description'] ?? '', 0, 200)) ?>",
    "url": "<?= $canonicalUrl ?>",
    "image": <?= json_encode(array_slice($images, 0, 5)) ?>,
    "price": "<?= $property['price'] ?>",
    "priceCurrency": "EGP",
    "address": {
        "@type": "PostalAddress",
        "addressLocality": "<?= htmlspecialchars($property['location_name']) ?>",
        "addressCountry": "EG"
    },
    "floorSize": {
        "@type": "QuantitativeValue",
        "value": <?= $property['area'] ?>,
        "unitCode": "MTK"
    },
    "numberOfRooms": <?= $property['bedrooms'] ?: 0 ?>,
    "numberOfBathroomsTotal": <?= $property['bathrooms'] ?: 0 ?>
}
</script>

<script>
// Track calls
function trackCall(propertyId) {
    fetch('/api/properties.php?action=track', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ property_id: propertyId, type: 'call' })
    });
    trackEvent('call_click', { property_id: propertyId });
}

// Track WhatsApp
function trackWhatsApp(propertyId) {
    fetch('/api/properties.php?action=track', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ property_id: propertyId, type: 'whatsapp' })
    });
    trackEvent('whatsapp_click', { property_id: propertyId });
}

// Contact form
document.getElementById('propertyLeadForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const data = Object.fromEntries(formData);

    try {
        const response = await fetch('/api/leads.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            showToast('تم إرسال رسالتك بنجاح!', 'success');
            this.reset();
            trackEvent('lead_submit', { property_id: data.property_id });
        } else {
            showToast(result.error || 'حدث خطأ', 'error');
        }
    } catch (error) {
        showToast('حدث خطأ في الاتصال', 'error');
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
