<?php
/**
 * Compounds Page - صفحة الكمبوندات
 */

$pageTitle = $currentLang === 'ar' ? 'دليل الكمبوندات | بي تاج' : 'Compounds Guide | BeTaj';
$pageDescription = $currentLang === 'ar' ? 'اكتشف أفضل الكمبوندات في مصر. تصفح مشاريع التجمع الخامس، الشيخ زايد، العاصمة الإدارية والساحل الشمالي.' : 'Discover the best compounds in Egypt. Browse projects in New Cairo, Sheikh Zayed, New Capital and North Coast.';

require_once __DIR__ . '/../includes/header.php';

// Translations
$t = [
    'ar' => [
        'hero_title' => 'دليل الكمبوندات',
        'hero_subtitle' => 'اكتشف أفضل الكمبوندات والمشاريع العقارية في مصر',
        'search_placeholder' => 'ابحث عن كمبوند...',
        'all_locations' => 'كل المناطق',
        'new_cairo' => 'التجمع الخامس',
        'sheikh_zayed' => 'الشيخ زايد',
        'new_capital' => 'العاصمة الإدارية',
        'north_coast' => 'الساحل الشمالي',
        'october' => '6 أكتوبر',
        'all_developers' => 'كل المطورين',
        'filter_title' => 'تصفية النتائج',
        'units' => 'وحدة',
        'starting_from' => 'تبدأ من',
        'view_details' => 'عرض التفاصيل',
        'available_units' => 'الوحدات المتاحة',
        'developer' => 'المطور',
        'location' => 'الموقع',
        'no_results' => 'لا توجد نتائج',
        'no_results_desc' => 'جرب تغيير معايير البحث',
        'popular_compounds' => 'أشهر الكمبوندات',
        'new_projects' => 'مشاريع جديدة',
        'load_more' => 'تحميل المزيد',
    ],
    'en' => [
        'hero_title' => 'Compounds Guide',
        'hero_subtitle' => 'Discover the best compounds and real estate projects in Egypt',
        'search_placeholder' => 'Search for a compound...',
        'all_locations' => 'All Locations',
        'new_cairo' => 'New Cairo',
        'sheikh_zayed' => 'Sheikh Zayed',
        'new_capital' => 'New Capital',
        'north_coast' => 'North Coast',
        'october' => '6th of October',
        'all_developers' => 'All Developers',
        'filter_title' => 'Filter Results',
        'units' => 'units',
        'starting_from' => 'Starting from',
        'view_details' => 'View Details',
        'available_units' => 'Available Units',
        'developer' => 'Developer',
        'location' => 'Location',
        'no_results' => 'No Results',
        'no_results_desc' => 'Try changing your search criteria',
        'popular_compounds' => 'Popular Compounds',
        'new_projects' => 'New Projects',
        'load_more' => 'Load More',
    ]
];

$text = $t[$currentLang];

// Get compounds from database
$pdo = Database::getInstance()->getConnection();
$compounds = [];

try {
    $sql = "SELECT * FROM locations WHERE type = 'compound' AND is_active = 1 ORDER BY name_ar";
    $compounds = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Demo data if database not available
    $compounds = [
        ['id' => 1, 'name_ar' => 'ماونتن فيو آي سيتي', 'name_en' => 'Mountain View iCity', 'slug' => 'mountain-view-icity', 'parent_name' => 'التجمع الخامس', 'developer' => 'ماونتن فيو', 'units_count' => 150, 'price_from' => 3500000],
        ['id' => 2, 'name_ar' => 'سوديك إيست', 'name_en' => 'Sodic East', 'slug' => 'sodic-east', 'parent_name' => 'التجمع الخامس', 'developer' => 'سوديك', 'units_count' => 89, 'price_from' => 4200000],
        ['id' => 3, 'name_ar' => 'زيد الشيخ زايد', 'name_en' => 'Zed Sheikh Zayed', 'slug' => 'zed-sheikh-zayed', 'parent_name' => 'الشيخ زايد', 'developer' => 'أورا ديفلوبرز', 'units_count' => 200, 'price_from' => 5000000],
        ['id' => 4, 'name_ar' => 'بالم هيلز القاهرة الجديدة', 'name_en' => 'Palm Hills New Cairo', 'slug' => 'palm-hills-new-cairo', 'parent_name' => 'التجمع الخامس', 'developer' => 'بالم هيلز', 'units_count' => 120, 'price_from' => 6500000],
        ['id' => 5, 'name_ar' => 'ميدتاون سولو', 'name_en' => 'Midtown Solo', 'slug' => 'midtown-solo', 'parent_name' => 'العاصمة الإدارية', 'developer' => 'بيتر هوم', 'units_count' => 180, 'price_from' => 2800000],
        ['id' => 6, 'name_ar' => 'بو آيلاند', 'name_en' => 'Bo Islands', 'slug' => 'bo-islands', 'parent_name' => 'العاصمة الإدارية', 'developer' => 'مصر إيطاليا', 'units_count' => 95, 'price_from' => 3200000],
    ];
}
?>

    <!-- Hero -->
    <section class="bg-gradient-to-<?= $isRTL ? 'l' : 'r' ?> from-primary-900 to-primary-700 text-white py-16">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4"><?= $text['hero_title'] ?></h1>
            <p class="text-blue-100 text-lg max-w-2xl mx-auto mb-8"><?= $text['hero_subtitle'] ?></p>

            <!-- Search -->
            <div class="max-w-3xl mx-auto">
                <div class="bg-white rounded-2xl p-2 flex flex-col md:flex-row gap-2">
                    <input type="text" id="search-input" placeholder="<?= $text['search_placeholder'] ?>"
                        class="flex-1 px-4 py-3 rounded-xl text-gray-800 outline-none">
                    <select id="location-filter" class="px-4 py-3 rounded-xl bg-gray-100 text-gray-700 outline-none">
                        <option value=""><?= $text['all_locations'] ?></option>
                        <option value="new-cairo"><?= $text['new_cairo'] ?></option>
                        <option value="sheikh-zayed"><?= $text['sheikh_zayed'] ?></option>
                        <option value="new-capital"><?= $text['new_capital'] ?></option>
                        <option value="north-coast"><?= $text['north_coast'] ?></option>
                        <option value="october"><?= $text['october'] ?></option>
                    </select>
                    <button onclick="filterCompounds()" class="bg-primary-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-primary-700 transition">
                        <i class="fa-solid fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Compounds Grid -->
    <section class="container mx-auto px-4 py-12">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Sidebar Filters -->
            <aside class="lg:w-72 shrink-0">
                <div class="bg-white rounded-2xl shadow-md p-6 sticky top-24">
                    <h3 class="font-bold text-gray-900 mb-4"><?= $text['filter_title'] ?></h3>

                    <!-- Locations -->
                    <div class="mb-6">
                        <h4 class="text-sm font-semibold text-gray-600 mb-3"><?= $text['location'] ?></h4>
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" value="new-cairo" class="location-checkbox rounded">
                                <span class="text-sm"><?= $text['new_cairo'] ?></span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" value="sheikh-zayed" class="location-checkbox rounded">
                                <span class="text-sm"><?= $text['sheikh_zayed'] ?></span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" value="new-capital" class="location-checkbox rounded">
                                <span class="text-sm"><?= $text['new_capital'] ?></span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" value="north-coast" class="location-checkbox rounded">
                                <span class="text-sm"><?= $text['north_coast'] ?></span>
                            </label>
                        </div>
                    </div>

                    <!-- Developers -->
                    <div class="mb-6">
                        <h4 class="text-sm font-semibold text-gray-600 mb-3"><?= $text['developer'] ?></h4>
                        <select class="w-full border rounded-lg p-2 text-sm">
                            <option><?= $text['all_developers'] ?></option>
                            <option>ماونتن فيو</option>
                            <option>سوديك</option>
                            <option>بالم هيلز</option>
                            <option>طلعت مصطفى</option>
                            <option>إعمار مصر</option>
                        </select>
                    </div>
                </div>
            </aside>

            <!-- Main Content -->
            <div class="flex-1">
                <div id="compounds-grid" class="grid md:grid-cols-2 xl:grid-cols-3 gap-6">
                    <?php foreach ($compounds as $compound): ?>
                    <div class="compound-card bg-white rounded-2xl shadow-md overflow-hidden card-hover">
                        <div class="relative">
                            <img src="/images/compound-placeholder.jpg" alt="<?= htmlspecialchars($compound[$currentLang === 'ar' ? 'name_ar' : 'name_en'] ?? $compound['name_ar']) ?>"
                                 class="w-full h-48 object-cover">
                            <span class="absolute top-3 <?= $isRTL ? 'right-3' : 'left-3' ?> bg-primary-600 text-white px-3 py-1 rounded-full text-sm font-medium">
                                <?= number_format($compound['units_count'] ?? 100) ?> <?= $text['units'] ?>
                            </span>
                        </div>
                        <div class="p-5">
                            <h3 class="font-bold text-gray-900 text-lg mb-2">
                                <?= htmlspecialchars($compound[$currentLang === 'ar' ? 'name_ar' : 'name_en'] ?? $compound['name_ar']) ?>
                            </h3>
                            <div class="flex items-center gap-2 text-gray-500 text-sm mb-3">
                                <i class="fa-solid fa-location-dot"></i>
                                <span><?= htmlspecialchars($compound['parent_name'] ?? ($currentLang === 'ar' ? 'القاهرة الجديدة' : 'New Cairo')) ?></span>
                            </div>
                            <div class="flex items-center gap-2 text-gray-500 text-sm mb-4">
                                <i class="fa-solid fa-building"></i>
                                <span><?= htmlspecialchars($compound['developer'] ?? 'N/A') ?></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs text-gray-400"><?= $text['starting_from'] ?></p>
                                    <p class="font-bold text-primary-600"><?= number_format($compound['price_from'] ?? 3000000) ?> <?= __('egp') ?></p>
                                </div>
                                <a href="/<?= $currentLang ?>/location/<?= $compound['slug'] ?? 'compound' ?>"
                                   class="bg-primary-50 text-primary-600 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-primary-100 transition">
                                    <?= $text['view_details'] ?>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>

                    <?php if (empty($compounds)): ?>
                    <div class="col-span-full text-center py-12">
                        <i class="fa-solid fa-building text-6xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500 text-lg"><?= $text['no_results'] ?></p>
                        <p class="text-gray-400"><?= $text['no_results_desc'] ?></p>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Load More -->
                <?php if (count($compounds) >= 6): ?>
                <div class="text-center mt-8">
                    <button class="bg-white border-2 border-primary-600 text-primary-600 px-8 py-3 rounded-xl font-bold hover:bg-primary-50 transition">
                        <?= $text['load_more'] ?>
                    </button>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <script>
        function filterCompounds() {
            const search = document.getElementById('search-input').value.toLowerCase();
            const location = document.getElementById('location-filter').value;
            const cards = document.querySelectorAll('.compound-card');

            cards.forEach(card => {
                const name = card.querySelector('h3').textContent.toLowerCase();
                const cardLocation = card.querySelector('.fa-location-dot').nextElementSibling.textContent.toLowerCase();

                const matchesSearch = name.includes(search);
                const matchesLocation = !location || cardLocation.includes(location.replace('-', ' '));

                card.style.display = matchesSearch && matchesLocation ? 'block' : 'none';
            });
        }

        document.getElementById('search-input').addEventListener('input', filterCompounds);
        document.getElementById('location-filter').addEventListener('change', filterCompounds);
    </script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
