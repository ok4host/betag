<?php
/**
 * Add Property Page - صفحة إضافة عقار
 */

$pageTitle = $currentLang === 'ar' ? 'أضف عقارك | بي تاج' : 'Add Property | BeTaj';
$pageDescription = $currentLang === 'ar' ? 'أضف عقارك مجاناً على بي تاج وابدأ في استقبال العملاء' : 'List your property for free on BeTaj and start receiving inquiries';

require_once __DIR__ . '/../includes/header.php';

// Require login
if (!isLoggedIn()) {
    header('Location: /' . $currentLang . '/login?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

// Translations
$t = [
    'ar' => [
        'title' => 'أضف عقارك',
        'subtitle' => 'أضف عقارك مجاناً وابدأ في استقبال العملاء',
        'step1' => 'نوع العقار',
        'step2' => 'التفاصيل',
        'step3' => 'الصور',
        'step4' => 'النشر',
        'listing_type' => 'نوع العرض',
        'sale' => 'للبيع',
        'rent' => 'للإيجار',
        'property_type' => 'نوع العقار',
        'select_type' => 'اختر نوع العقار',
        'title_field' => 'عنوان الإعلان',
        'title_placeholder' => 'مثال: شقة 3 غرف في التجمع الخامس',
        'description' => 'الوصف',
        'description_placeholder' => 'اكتب وصفاً تفصيلياً للعقار...',
        'location' => 'الموقع',
        'select_location' => 'اختر المنطقة',
        'address' => 'العنوان التفصيلي',
        'address_placeholder' => 'الشارع، رقم المبنى...',
        'price' => 'السعر',
        'price_placeholder' => 'أدخل السعر',
        'area' => 'المساحة (م²)',
        'area_placeholder' => 'أدخل المساحة',
        'bedrooms' => 'غرف النوم',
        'bathrooms' => 'الحمامات',
        'floor' => 'الطابق',
        'features' => 'المميزات',
        'parking' => 'موقف سيارات',
        'garden' => 'حديقة',
        'pool' => 'حمام سباحة',
        'security' => 'أمن 24 ساعة',
        'elevator' => 'مصعد',
        'ac' => 'تكييف مركزي',
        'furnished' => 'مفروش',
        'upload_images' => 'صور العقار',
        'main_image' => 'الصورة الرئيسية',
        'drop_images' => 'اسحب الصور هنا أو انقر للرفع',
        'max_images' => 'حد أقصى 10 صور',
        'contact_info' => 'معلومات التواصل',
        'owner_name' => 'اسم المعلن',
        'owner_phone' => 'رقم الهاتف',
        'owner_whatsapp' => 'واتساب',
        'previous' => 'السابق',
        'next' => 'التالي',
        'publish' => 'نشر الإعلان',
        'save_draft' => 'حفظ كمسودة',
        'success' => 'تم إضافة العقار بنجاح',
        'error' => 'حدث خطأ، حاول مرة أخرى',
    ],
    'en' => [
        'title' => 'Add Your Property',
        'subtitle' => 'List your property for free and start receiving inquiries',
        'step1' => 'Property Type',
        'step2' => 'Details',
        'step3' => 'Photos',
        'step4' => 'Publish',
        'listing_type' => 'Listing Type',
        'sale' => 'For Sale',
        'rent' => 'For Rent',
        'property_type' => 'Property Type',
        'select_type' => 'Select property type',
        'title_field' => 'Listing Title',
        'title_placeholder' => 'Example: 3BR Apartment in New Cairo',
        'description' => 'Description',
        'description_placeholder' => 'Write a detailed description of the property...',
        'location' => 'Location',
        'select_location' => 'Select location',
        'address' => 'Detailed Address',
        'address_placeholder' => 'Street, building number...',
        'price' => 'Price',
        'price_placeholder' => 'Enter price',
        'area' => 'Area (sqm)',
        'area_placeholder' => 'Enter area',
        'bedrooms' => 'Bedrooms',
        'bathrooms' => 'Bathrooms',
        'floor' => 'Floor',
        'features' => 'Features',
        'parking' => 'Parking',
        'garden' => 'Garden',
        'pool' => 'Swimming Pool',
        'security' => '24/7 Security',
        'elevator' => 'Elevator',
        'ac' => 'Central AC',
        'furnished' => 'Furnished',
        'upload_images' => 'Property Photos',
        'main_image' => 'Main Image',
        'drop_images' => 'Drop images here or click to upload',
        'max_images' => 'Maximum 10 images',
        'contact_info' => 'Contact Information',
        'owner_name' => 'Advertiser Name',
        'owner_phone' => 'Phone Number',
        'owner_whatsapp' => 'WhatsApp',
        'previous' => 'Previous',
        'next' => 'Next',
        'publish' => 'Publish Listing',
        'save_draft' => 'Save as Draft',
        'success' => 'Property added successfully',
        'error' => 'An error occurred, please try again',
    ]
];

$text = $t[$currentLang];

// Get categories and locations
$pdo = Database::getInstance()->getConnection();
$categories = [];
$locations = [];

try {
    $categories = $pdo->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY name_ar")->fetchAll(PDO::FETCH_ASSOC);
    $locations = $pdo->query("SELECT * FROM locations WHERE is_active = 1 AND type IN ('city', 'area') ORDER BY name_ar")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Demo data
    $categories = [
        ['id' => 1, 'name_ar' => 'شقة', 'name_en' => 'Apartment'],
        ['id' => 2, 'name_ar' => 'فيلا', 'name_en' => 'Villa'],
        ['id' => 3, 'name_ar' => 'دوبلكس', 'name_en' => 'Duplex'],
        ['id' => 4, 'name_ar' => 'تاون هاوس', 'name_en' => 'Townhouse'],
        ['id' => 5, 'name_ar' => 'بنتهاوس', 'name_en' => 'Penthouse'],
        ['id' => 6, 'name_ar' => 'ستوديو', 'name_en' => 'Studio'],
    ];
    $locations = [
        ['id' => 1, 'name_ar' => 'التجمع الخامس', 'name_en' => 'New Cairo'],
        ['id' => 2, 'name_ar' => 'الشيخ زايد', 'name_en' => 'Sheikh Zayed'],
        ['id' => 3, 'name_ar' => 'العاصمة الإدارية', 'name_en' => 'New Capital'],
        ['id' => 4, 'name_ar' => '6 أكتوبر', 'name_en' => '6th of October'],
        ['id' => 5, 'name_ar' => 'المعادي', 'name_en' => 'Maadi'],
    ];
}
?>

    <section class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900"><?= $text['title'] ?></h1>
            <p class="text-gray-500 mt-2"><?= $text['subtitle'] ?></p>
        </div>

        <!-- Progress Steps -->
        <div class="max-w-3xl mx-auto mb-8">
            <div class="flex items-center justify-between">
                <div class="step-item active flex flex-col items-center" data-step="1">
                    <div class="w-10 h-10 rounded-full bg-primary-600 text-white flex items-center justify-center font-bold">1</div>
                    <span class="text-sm mt-2 text-primary-600 font-medium"><?= $text['step1'] ?></span>
                </div>
                <div class="flex-1 h-1 bg-gray-200 mx-2"><div class="step-progress h-full bg-primary-600 w-0 transition-all"></div></div>
                <div class="step-item flex flex-col items-center" data-step="2">
                    <div class="w-10 h-10 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center font-bold">2</div>
                    <span class="text-sm mt-2 text-gray-500"><?= $text['step2'] ?></span>
                </div>
                <div class="flex-1 h-1 bg-gray-200 mx-2"><div class="step-progress h-full bg-primary-600 w-0 transition-all"></div></div>
                <div class="step-item flex flex-col items-center" data-step="3">
                    <div class="w-10 h-10 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center font-bold">3</div>
                    <span class="text-sm mt-2 text-gray-500"><?= $text['step3'] ?></span>
                </div>
                <div class="flex-1 h-1 bg-gray-200 mx-2"><div class="step-progress h-full bg-primary-600 w-0 transition-all"></div></div>
                <div class="step-item flex flex-col items-center" data-step="4">
                    <div class="w-10 h-10 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center font-bold">4</div>
                    <span class="text-sm mt-2 text-gray-500"><?= $text['step4'] ?></span>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form id="add-property-form" action="/api/properties" method="POST" enctype="multipart/form-data" class="max-w-3xl mx-auto">
            <input type="hidden" name="user_id" value="<?= $_SESSION['user_id'] ?>">
            <input type="hidden" name="lang" value="<?= $currentLang ?>">

            <!-- Step 1: Property Type -->
            <div class="form-step active" data-step="1">
                <div class="bg-white rounded-2xl shadow-md p-6 md:p-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-6"><?= $text['listing_type'] ?></h2>

                    <div class="grid grid-cols-2 gap-4 mb-8">
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="sale" class="hidden peer" checked>
                            <div class="border-2 border-gray-200 rounded-xl p-4 text-center peer-checked:border-primary-600 peer-checked:bg-primary-50 transition">
                                <i class="fa-solid fa-tag text-3xl text-gray-400 peer-checked:text-primary-600 mb-2"></i>
                                <p class="font-bold"><?= $text['sale'] ?></p>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="rent" class="hidden peer">
                            <div class="border-2 border-gray-200 rounded-xl p-4 text-center peer-checked:border-primary-600 peer-checked:bg-primary-50 transition">
                                <i class="fa-solid fa-key text-3xl text-gray-400 peer-checked:text-primary-600 mb-2"></i>
                                <p class="font-bold"><?= $text['rent'] ?></p>
                            </div>
                        </label>
                    </div>

                    <h2 class="text-xl font-bold text-gray-900 mb-6"><?= $text['property_type'] ?></h2>
                    <div class="grid grid-cols-3 md:grid-cols-6 gap-3">
                        <?php foreach ($categories as $cat): ?>
                        <label class="cursor-pointer">
                            <input type="radio" name="category_id" value="<?= $cat['id'] ?>" class="hidden peer" <?= $cat['id'] == 1 ? 'checked' : '' ?>>
                            <div class="border-2 border-gray-200 rounded-xl p-3 text-center peer-checked:border-primary-600 peer-checked:bg-primary-50 transition">
                                <p class="text-sm font-medium"><?= htmlspecialchars($cat[$currentLang === 'ar' ? 'name_ar' : 'name_en']) ?></p>
                            </div>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Step 2: Details -->
            <div class="form-step hidden" data-step="2">
                <div class="bg-white rounded-2xl shadow-md p-6 md:p-8 space-y-6">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2"><?= $text['title_field'] ?> *</label>
                        <input type="text" name="title" required placeholder="<?= $text['title_placeholder'] ?>"
                            class="w-full border-2 border-gray-200 rounded-xl p-3 focus:border-primary-500 outline-none transition">
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-2"><?= $text['description'] ?> *</label>
                        <textarea name="description" rows="5" required placeholder="<?= $text['description_placeholder'] ?>"
                            class="w-full border-2 border-gray-200 rounded-xl p-3 focus:border-primary-500 outline-none transition resize-none"></textarea>
                    </div>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2"><?= $text['location'] ?> *</label>
                            <select name="location_id" required class="w-full border-2 border-gray-200 rounded-xl p-3 focus:border-primary-500 outline-none bg-white">
                                <option value=""><?= $text['select_location'] ?></option>
                                <?php foreach ($locations as $loc): ?>
                                <option value="<?= $loc['id'] ?>"><?= htmlspecialchars($loc[$currentLang === 'ar' ? 'name_ar' : 'name_en']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2"><?= $text['address'] ?></label>
                            <input type="text" name="address" placeholder="<?= $text['address_placeholder'] ?>"
                                class="w-full border-2 border-gray-200 rounded-xl p-3 focus:border-primary-500 outline-none transition">
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2"><?= $text['price'] ?> *</label>
                            <input type="number" name="price" required placeholder="<?= $text['price_placeholder'] ?>"
                                class="w-full border-2 border-gray-200 rounded-xl p-3 focus:border-primary-500 outline-none transition" dir="ltr">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2"><?= $text['area'] ?> *</label>
                            <input type="number" name="area" required placeholder="<?= $text['area_placeholder'] ?>"
                                class="w-full border-2 border-gray-200 rounded-xl p-3 focus:border-primary-500 outline-none transition" dir="ltr">
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2"><?= $text['bedrooms'] ?></label>
                            <select name="bedrooms" class="w-full border-2 border-gray-200 rounded-xl p-3 focus:border-primary-500 outline-none bg-white">
                                <?php for ($i = 1; $i <= 10; $i++): ?>
                                <option value="<?= $i ?>" <?= $i == 3 ? 'selected' : '' ?>><?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2"><?= $text['bathrooms'] ?></label>
                            <select name="bathrooms" class="w-full border-2 border-gray-200 rounded-xl p-3 focus:border-primary-500 outline-none bg-white">
                                <?php for ($i = 1; $i <= 6; $i++): ?>
                                <option value="<?= $i ?>" <?= $i == 2 ? 'selected' : '' ?>><?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2"><?= $text['floor'] ?></label>
                            <select name="floor" class="w-full border-2 border-gray-200 rounded-xl p-3 focus:border-primary-500 outline-none bg-white">
                                <option value="0"><?= $currentLang === 'ar' ? 'أرضي' : 'Ground' ?></option>
                                <?php for ($i = 1; $i <= 20; $i++): ?>
                                <option value="<?= $i ?>"><?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-3"><?= $text['features'] ?></label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="features[]" value="parking" class="rounded">
                                <span class="text-sm"><?= $text['parking'] ?></span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="features[]" value="garden" class="rounded">
                                <span class="text-sm"><?= $text['garden'] ?></span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="features[]" value="pool" class="rounded">
                                <span class="text-sm"><?= $text['pool'] ?></span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="features[]" value="security" class="rounded">
                                <span class="text-sm"><?= $text['security'] ?></span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="features[]" value="elevator" class="rounded">
                                <span class="text-sm"><?= $text['elevator'] ?></span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="features[]" value="ac" class="rounded">
                                <span class="text-sm"><?= $text['ac'] ?></span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="features[]" value="furnished" class="rounded">
                                <span class="text-sm"><?= $text['furnished'] ?></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 3: Photos -->
            <div class="form-step hidden" data-step="3">
                <div class="bg-white rounded-2xl shadow-md p-6 md:p-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-6"><?= $text['upload_images'] ?></h2>

                    <div id="dropzone" class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center cursor-pointer hover:border-primary-500 transition">
                        <i class="fa-solid fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
                        <p class="text-gray-600 font-medium"><?= $text['drop_images'] ?></p>
                        <p class="text-sm text-gray-400 mt-2"><?= $text['max_images'] ?></p>
                        <input type="file" name="images[]" id="images-input" multiple accept="image/*" class="hidden">
                    </div>

                    <div id="preview-container" class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6"></div>
                </div>
            </div>

            <!-- Step 4: Contact & Publish -->
            <div class="form-step hidden" data-step="4">
                <div class="bg-white rounded-2xl shadow-md p-6 md:p-8 space-y-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-6"><?= $text['contact_info'] ?></h2>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-2"><?= $text['owner_name'] ?></label>
                        <input type="text" name="owner_name" value="<?= htmlspecialchars($_SESSION['user_name'] ?? '') ?>"
                            class="w-full border-2 border-gray-200 rounded-xl p-3 focus:border-primary-500 outline-none transition">
                    </div>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2"><?= $text['owner_phone'] ?> *</label>
                            <input type="tel" name="owner_phone" required
                                class="w-full border-2 border-gray-200 rounded-xl p-3 focus:border-primary-500 outline-none transition" dir="ltr">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2"><?= $text['owner_whatsapp'] ?></label>
                            <input type="tel" name="owner_whatsapp"
                                class="w-full border-2 border-gray-200 rounded-xl p-3 focus:border-primary-500 outline-none transition" dir="ltr">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <div class="flex justify-between mt-6">
                <button type="button" id="prev-btn" class="hidden px-6 py-3 border-2 border-gray-300 rounded-xl font-bold text-gray-700 hover:bg-gray-100 transition">
                    <i class="fa-solid fa-arrow-<?= $isRTL ? 'right' : 'left' ?> <?= $isRTL ? 'ml-2' : 'mr-2' ?>"></i>
                    <?= $text['previous'] ?>
                </button>

                <button type="button" id="next-btn" class="<?= $isRTL ? 'mr-auto' : 'ml-auto' ?> px-6 py-3 bg-primary-600 text-white rounded-xl font-bold hover:bg-primary-700 transition">
                    <?= $text['next'] ?>
                    <i class="fa-solid fa-arrow-<?= $isRTL ? 'left' : 'right' ?> <?= $isRTL ? 'mr-2' : 'ml-2' ?>"></i>
                </button>

                <button type="submit" id="submit-btn" class="hidden px-8 py-3 bg-green-600 text-white rounded-xl font-bold hover:bg-green-700 transition">
                    <i class="fa-solid fa-check <?= $isRTL ? 'ml-2' : 'mr-2' ?>"></i>
                    <?= $text['publish'] ?>
                </button>
            </div>
        </form>
    </section>

    <script>
        let currentStep = 1;
        const totalSteps = 4;

        // Navigation
        document.getElementById('next-btn').addEventListener('click', () => {
            if (currentStep < totalSteps) {
                goToStep(currentStep + 1);
            }
        });

        document.getElementById('prev-btn').addEventListener('click', () => {
            if (currentStep > 1) {
                goToStep(currentStep - 1);
            }
        });

        function goToStep(step) {
            // Hide current step
            document.querySelector(`.form-step[data-step="${currentStep}"]`).classList.add('hidden');
            document.querySelector(`.step-item[data-step="${currentStep}"]`).classList.remove('active');

            // Show new step
            document.querySelector(`.form-step[data-step="${step}"]`).classList.remove('hidden');
            document.querySelector(`.step-item[data-step="${step}"]`).classList.add('active');

            // Update step items
            document.querySelectorAll('.step-item').forEach((item, i) => {
                const stepNum = i + 1;
                const circle = item.querySelector('div');
                const text = item.querySelector('span');

                if (stepNum < step) {
                    circle.classList.add('bg-primary-600', 'text-white');
                    circle.classList.remove('bg-gray-200', 'text-gray-500');
                    text.classList.add('text-primary-600');
                    text.classList.remove('text-gray-500');
                } else if (stepNum === step) {
                    circle.classList.add('bg-primary-600', 'text-white');
                    circle.classList.remove('bg-gray-200', 'text-gray-500');
                    text.classList.add('text-primary-600', 'font-medium');
                    text.classList.remove('text-gray-500');
                } else {
                    circle.classList.remove('bg-primary-600', 'text-white');
                    circle.classList.add('bg-gray-200', 'text-gray-500');
                    text.classList.remove('text-primary-600', 'font-medium');
                    text.classList.add('text-gray-500');
                }
            });

            // Update progress bars
            document.querySelectorAll('.step-progress').forEach((bar, i) => {
                if (i < step - 1) {
                    bar.style.width = '100%';
                } else {
                    bar.style.width = '0';
                }
            });

            currentStep = step;

            // Show/hide buttons
            document.getElementById('prev-btn').classList.toggle('hidden', currentStep === 1);
            document.getElementById('next-btn').classList.toggle('hidden', currentStep === totalSteps);
            document.getElementById('submit-btn').classList.toggle('hidden', currentStep !== totalSteps);

            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // Image upload
        const dropzone = document.getElementById('dropzone');
        const imagesInput = document.getElementById('images-input');
        const previewContainer = document.getElementById('preview-container');

        dropzone.addEventListener('click', () => imagesInput.click());

        dropzone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropzone.classList.add('border-primary-500', 'bg-primary-50');
        });

        dropzone.addEventListener('dragleave', () => {
            dropzone.classList.remove('border-primary-500', 'bg-primary-50');
        });

        dropzone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropzone.classList.remove('border-primary-500', 'bg-primary-50');
            handleFiles(e.dataTransfer.files);
        });

        imagesInput.addEventListener('change', (e) => {
            handleFiles(e.target.files);
        });

        function handleFiles(files) {
            previewContainer.innerHTML = '';
            Array.from(files).slice(0, 10).forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const div = document.createElement('div');
                    div.className = 'relative group';
                    div.innerHTML = `
                        <img src="${e.target.result}" class="w-full h-32 object-cover rounded-xl">
                        ${index === 0 ? `<span class="absolute top-2 ${isRTL ? 'right-2' : 'left-2'} bg-primary-600 text-white text-xs px-2 py-1 rounded"><?= $text['main_image'] ?></span>` : ''}
                    `;
                    previewContainer.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        }

        // Form submission
        document.getElementById('add-property-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('submit-btn');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
            btn.disabled = true;

            try {
                const formData = new FormData(e.target);
                const response = await fetch('/api/properties', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.success) {
                    showToast('<?= $text['success'] ?>', 'success');
                    setTimeout(() => {
                        window.location.href = '/<?= $currentLang ?>/my-properties';
                    }, 1500);
                } else {
                    showToast(result.message || '<?= $text['error'] ?>', 'error');
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            } catch (error) {
                showToast('<?= $text['error'] ?>', 'error');
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        });
    </script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
