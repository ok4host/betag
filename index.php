<?php
/**
 * Homepage - BeTaj Real Estate Platform
 */
$pageTitle = null; // Will use SEO settings
$currentPage = 'index';

require_once __DIR__ . '/includes/header.php';

// Get featured properties
$pdo = Database::getInstance()->getConnection();

$stmt = $pdo->query("
    SELECT p.*, c.name_ar as category_name, c.icon as category_icon, l.name_ar as location_name
    FROM properties p
    LEFT JOIN categories c ON p.category_id = c.id
    LEFT JOIN locations l ON p.location_id = l.id
    WHERE p.status = 'active' AND p.is_featured = 1
    ORDER BY p.created_at DESC
    LIMIT 8
");
$featuredProperties = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get latest properties
$stmt = $pdo->query("
    SELECT p.*, c.name_ar as category_name, c.icon as category_icon, l.name_ar as location_name
    FROM properties p
    LEFT JOIN categories c ON p.category_id = c.id
    LEFT JOIN locations l ON p.location_id = l.id
    WHERE p.status = 'active'
    ORDER BY p.created_at DESC
    LIMIT 12
");
$latestProperties = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get categories
$categories = $pdo->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);

// Get popular locations
$locations = $pdo->query("
    SELECT l.*, COUNT(p.id) as property_count
    FROM locations l
    LEFT JOIN properties p ON l.id = p.location_id AND p.status = 'active'
    WHERE l.is_active = 1 AND l.type IN ('city', 'area')
    GROUP BY l.id
    HAVING property_count > 0
    ORDER BY property_count DESC
    LIMIT 8
")->fetchAll(PDO::FETCH_ASSOC);

// Stats
$totalProperties = $pdo->query("SELECT COUNT(*) FROM properties WHERE status = 'active'")->fetchColumn();
$totalSale = $pdo->query("SELECT COUNT(*) FROM properties WHERE status = 'active' AND transaction_type = 'sale'")->fetchColumn();
$totalRent = $pdo->query("SELECT COUNT(*) FROM properties WHERE status = 'active' AND transaction_type = 'rent'")->fetchColumn();
?>

<!-- Hero Section -->
<section class="relative bg-gradient-to-br from-primary-900 via-primary-800 to-primary-700 text-white overflow-hidden">
    <div class="absolute inset-0 bg-black/30"></div>
    <div class="absolute inset-0" style="background-image: url('https://images.unsplash.com/photo-1582407947304-fd86f028f716?w=1920'); background-size: cover; background-position: center; opacity: 0.3;"></div>

    <div class="container mx-auto px-4 py-20 md:py-32 relative z-10">
        <div class="text-center max-w-4xl mx-auto">
            <h1 class="text-4xl md:text-6xl font-extrabold mb-6 leading-tight">
                اكتشف بيتك الجديد
                <span class="text-yellow-400">في أفضل المواقع</span>
            </h1>
            <p class="text-xl md:text-2xl text-gray-200 mb-10">
                أكثر من <?= number_format($totalProperties) ?> عقار للبيع والإيجار في مصر
            </p>

            <!-- Search Form -->
            <form action="/search" method="GET" class="bg-white/95 backdrop-blur rounded-2xl p-6 shadow-2xl">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <select name="type" class="w-full p-4 rounded-xl border-2 border-gray-200 focus:border-primary-500 text-gray-700">
                            <option value="">نوع المعاملة</option>
                            <option value="sale">للبيع</option>
                            <option value="rent">للإيجار</option>
                        </select>
                    </div>
                    <div>
                        <select name="category" class="w-full p-4 rounded-xl border-2 border-gray-200 focus:border-primary-500 text-gray-700">
                            <option value="">نوع العقار</option>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['slug'] ?>"><?= htmlspecialchars($cat['name_ar']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <select name="location" class="w-full p-4 rounded-xl border-2 border-gray-200 focus:border-primary-500 text-gray-700">
                            <option value="">المنطقة</option>
                            <?php foreach ($locations as $loc): ?>
                            <option value="<?= $loc['slug'] ?>"><?= htmlspecialchars($loc['name_ar']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <button type="submit" class="w-full bg-primary-600 text-white p-4 rounded-xl hover:bg-primary-700 transition font-bold text-lg">
                            <i class="fa-solid fa-search ml-2"></i> ابحث الآن
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Stats Bar -->
    <div class="bg-white/10 backdrop-blur border-t border-white/20 py-6 relative z-10">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-3 gap-4 text-center">
                <div>
                    <div class="text-3xl font-bold text-yellow-400"><?= number_format($totalProperties) ?>+</div>
                    <div class="text-gray-200">عقار متاح</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-yellow-400"><?= number_format($totalSale) ?>+</div>
                    <div class="text-gray-200">للبيع</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-yellow-400"><?= number_format($totalRent) ?>+</div>
                    <div class="text-gray-200">للإيجار</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Property Categories -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-12 text-gray-800">
            <i class="fa-solid fa-building text-primary-600 ml-3"></i>تصفح حسب نوع العقار
        </h2>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <?php foreach ($categories as $cat): ?>
            <a href="/search?category=<?= $cat['slug'] ?>" class="bg-white rounded-2xl p-6 text-center shadow-lg hover:shadow-xl transition card-hover group">
                <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-primary-600 transition">
                    <i class="fa-solid <?= $cat['icon'] ?: 'fa-building' ?> text-2xl text-primary-600 group-hover:text-white transition"></i>
                </div>
                <h3 class="font-bold text-gray-800"><?= htmlspecialchars($cat['name_ar']) ?></h3>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Featured Properties -->
<?php if (!empty($featuredProperties)): ?>
<section class="py-16">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-10">
            <h2 class="text-3xl font-bold text-gray-800">
                <i class="fa-solid fa-star text-yellow-500 ml-3"></i>عقارات مميزة
            </h2>
            <a href="/search?featured=1" class="text-primary-600 hover:underline font-medium">
                عرض الكل <i class="fa-solid fa-arrow-left mr-1"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($featuredProperties as $property): ?>
            <?php include __DIR__ . '/includes/property-card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Popular Locations -->
<section class="py-16 bg-gray-900 text-white">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-12">
            <i class="fa-solid fa-location-dot text-primary-400 ml-3"></i>أشهر المناطق
        </h2>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <?php foreach ($locations as $loc): ?>
            <a href="/search?location=<?= $loc['slug'] ?>" class="relative rounded-2xl overflow-hidden group h-48">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent z-10"></div>
                <img src="https://source.unsplash.com/400x300/?egypt,<?= urlencode($loc['name_en'] ?? 'city') ?>" alt="<?= htmlspecialchars($loc['name_ar']) ?>" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                <div class="absolute bottom-0 right-0 left-0 p-4 z-20">
                    <h3 class="font-bold text-lg"><?= htmlspecialchars($loc['name_ar']) ?></h3>
                    <p class="text-gray-300 text-sm"><?= number_format($loc['property_count']) ?> عقار</p>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Latest Properties -->
<?php if (!empty($latestProperties)): ?>
<section class="py-16">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-10">
            <h2 class="text-3xl font-bold text-gray-800">
                <i class="fa-solid fa-clock text-primary-600 ml-3"></i>أحدث العقارات
            </h2>
            <a href="/search" class="text-primary-600 hover:underline font-medium">
                عرض الكل <i class="fa-solid fa-arrow-left mr-1"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($latestProperties as $property): ?>
            <?php include __DIR__ . '/includes/property-card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA Section -->
<section class="py-16 bg-primary-600 text-white">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-3xl md:text-4xl font-bold mb-6">هل لديك عقار للبيع أو الإيجار؟</h2>
        <p class="text-xl text-primary-100 mb-8 max-w-2xl mx-auto">
            أضف عقارك الآن مجاناً وابدأ في استقبال العروض من العملاء المهتمين
        </p>
        <a href="/add-property" class="inline-block bg-white text-primary-600 px-8 py-4 rounded-xl font-bold text-lg hover:bg-gray-100 transition shadow-lg">
            <i class="fa-solid fa-plus ml-2"></i> أضف عقارك مجاناً
        </a>
    </div>
</section>

<!-- Contact Form Section -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="max-w-2xl mx-auto">
            <h2 class="text-3xl font-bold text-center mb-4 text-gray-800">
                <i class="fa-solid fa-headset text-primary-600 ml-3"></i>تحتاج مساعدة؟
            </h2>
            <p class="text-center text-gray-600 mb-10">اترك بياناتك وسيتواصل معك أحد خبرائنا العقاريين</p>

            <form id="leadForm" class="bg-white rounded-2xl p-8 shadow-xl">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">الاسم *</label>
                        <input type="text" name="name" required class="w-full p-4 rounded-xl border-2 border-gray-200 focus:border-primary-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">رقم الهاتف *</label>
                        <input type="tel" name="phone" required class="w-full p-4 rounded-xl border-2 border-gray-200 focus:border-primary-500 focus:outline-none" dir="ltr">
                    </div>
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 font-medium mb-2">البريد الإلكتروني</label>
                    <input type="email" name="email" class="w-full p-4 rounded-xl border-2 border-gray-200 focus:border-primary-500 focus:outline-none">
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 font-medium mb-2">أبحث عن</label>
                    <select name="purpose" class="w-full p-4 rounded-xl border-2 border-gray-200 focus:border-primary-500">
                        <option value="buy">شراء عقار</option>
                        <option value="rent">إيجار عقار</option>
                        <option value="general">استفسار عام</option>
                    </select>
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 font-medium mb-2">الرسالة</label>
                    <textarea name="message" rows="4" class="w-full p-4 rounded-xl border-2 border-gray-200 focus:border-primary-500 focus:outline-none" placeholder="اكتب تفاصيل طلبك..."></textarea>
                </div>
                <button type="submit" class="w-full bg-primary-600 text-white p-4 rounded-xl hover:bg-primary-700 transition font-bold text-lg">
                    <i class="fa-solid fa-paper-plane ml-2"></i> أرسل طلبك
                </button>
            </form>
        </div>
    </div>
</section>

<script>
// Lead form submission
document.getElementById('leadForm')?.addEventListener('submit', async function(e) {
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
            showToast('تم إرسال طلبك بنجاح! سنتواصل معك قريباً', 'success');
            this.reset();
            trackEvent('lead_submit', { source: 'homepage' });
        } else {
            showToast(result.error || 'حدث خطأ، حاول مرة أخرى', 'error');
        }
    } catch (error) {
        showToast('حدث خطأ في الاتصال', 'error');
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
