<?php
/**
 * Privacy Policy Page - صفحة سياسة الخصوصية
 */

$pageTitle = $currentLang === 'ar' ? 'سياسة الخصوصية | بي تاج' : 'Privacy Policy | BeTaj';
$pageDescription = $currentLang === 'ar' ? 'اطلع على سياسة الخصوصية الخاصة بموقع بي تاج' : 'Read BeTaj\'s Privacy Policy';

require_once __DIR__ . '/../includes/header.php';

// Translations
$t = [
    'ar' => [
        'title' => 'سياسة الخصوصية',
        'last_updated' => 'آخر تحديث',
        'intro' => 'نحن في بي تاج نحترم خصوصيتك ونلتزم بحماية بياناتك الشخصية. توضح هذه السياسة كيفية جمعنا واستخدامنا وحماية معلوماتك.',
        'section1_title' => 'المعلومات التي نجمعها',
        'section1_content' => 'نجمع المعلومات التي تقدمها لنا مباشرة، مثل الاسم والبريد الإلكتروني ورقم الهاتف عند التسجيل أو التواصل معنا. كما نجمع معلومات تلقائياً عن استخدامك للموقع مثل عنوان IP ونوع المتصفح.',
        'section2_title' => 'كيف نستخدم معلوماتك',
        'section2_content' => 'نستخدم معلوماتك لتقديم خدماتنا وتحسينها، والتواصل معك بخصوص العقارات والعروض، وتخصيص تجربتك على الموقع.',
        'section3_title' => 'مشاركة المعلومات',
        'section3_content' => 'لا نبيع معلوماتك الشخصية لأطراف ثالثة. قد نشارك معلوماتك مع مقدمي الخدمات الذين يساعدوننا في تشغيل الموقع، أو عندما يكون ذلك مطلوباً قانونياً.',
        'section4_title' => 'أمان البيانات',
        'section4_content' => 'نتخذ إجراءات أمنية مناسبة لحماية معلوماتك من الوصول غير المصرح به أو التعديل أو الإفصاح أو التدمير.',
        'section5_title' => 'حقوقك',
        'section5_content' => 'لديك الحق في الوصول إلى بياناتك الشخصية وتصحيحها أو حذفها. يمكنك أيضاً الاعتراض على معالجة بياناتك في بعض الحالات.',
        'section6_title' => 'ملفات تعريف الارتباط',
        'section6_content' => 'نستخدم ملفات تعريف الارتباط (الكوكيز) لتحسين تجربتك على الموقع. يمكنك التحكم في إعدادات الكوكيز من خلال متصفحك.',
        'section7_title' => 'التغييرات على هذه السياسة',
        'section7_content' => 'قد نقوم بتحديث هذه السياسة من وقت لآخر. سنخطرك بأي تغييرات جوهرية عبر البريد الإلكتروني أو إشعار على الموقع.',
        'contact_title' => 'اتصل بنا',
        'contact_content' => 'إذا كانت لديك أي أسئلة حول سياسة الخصوصية هذه، يرجى التواصل معنا عبر صفحة اتصل بنا.',
    ],
    'en' => [
        'title' => 'Privacy Policy',
        'last_updated' => 'Last Updated',
        'intro' => 'At BeTaj, we respect your privacy and are committed to protecting your personal data. This policy explains how we collect, use, and protect your information.',
        'section1_title' => 'Information We Collect',
        'section1_content' => 'We collect information you provide directly to us, such as name, email, and phone number when you register or contact us. We also automatically collect information about your use of the site such as IP address and browser type.',
        'section2_title' => 'How We Use Your Information',
        'section2_content' => 'We use your information to provide and improve our services, communicate with you about properties and offers, and personalize your experience on the site.',
        'section3_title' => 'Information Sharing',
        'section3_content' => 'We do not sell your personal information to third parties. We may share your information with service providers who help us operate the site, or when legally required.',
        'section4_title' => 'Data Security',
        'section4_content' => 'We take appropriate security measures to protect your information from unauthorized access, modification, disclosure, or destruction.',
        'section5_title' => 'Your Rights',
        'section5_content' => 'You have the right to access, correct, or delete your personal data. You can also object to the processing of your data in certain cases.',
        'section6_title' => 'Cookies',
        'section6_content' => 'We use cookies to improve your experience on the site. You can control cookie settings through your browser.',
        'section7_title' => 'Changes to This Policy',
        'section7_content' => 'We may update this policy from time to time. We will notify you of any material changes via email or a notice on the site.',
        'contact_title' => 'Contact Us',
        'contact_content' => 'If you have any questions about this Privacy Policy, please contact us through our contact page.',
    ]
];

$text = $t[$currentLang];
?>

    <!-- Hero -->
    <section class="bg-gradient-to-<?= $isRTL ? 'l' : 'r' ?> from-primary-900 to-primary-700 text-white py-16">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-4xl font-bold mb-4"><?= $text['title'] ?></h1>
            <p class="text-blue-100"><?= $text['last_updated'] ?>: <?= date('Y-m-d') ?></p>
        </div>
    </section>

    <!-- Content -->
    <section class="container mx-auto px-4 py-12">
        <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-lg p-8 md:p-12">
            <p class="text-gray-600 leading-relaxed mb-8"><?= $text['intro'] ?></p>

            <div class="space-y-8">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 mb-3"><?= $text['section1_title'] ?></h2>
                    <p class="text-gray-600 leading-relaxed"><?= $text['section1_content'] ?></p>
                </div>

                <div>
                    <h2 class="text-xl font-bold text-gray-900 mb-3"><?= $text['section2_title'] ?></h2>
                    <p class="text-gray-600 leading-relaxed"><?= $text['section2_content'] ?></p>
                </div>

                <div>
                    <h2 class="text-xl font-bold text-gray-900 mb-3"><?= $text['section3_title'] ?></h2>
                    <p class="text-gray-600 leading-relaxed"><?= $text['section3_content'] ?></p>
                </div>

                <div>
                    <h2 class="text-xl font-bold text-gray-900 mb-3"><?= $text['section4_title'] ?></h2>
                    <p class="text-gray-600 leading-relaxed"><?= $text['section4_content'] ?></p>
                </div>

                <div>
                    <h2 class="text-xl font-bold text-gray-900 mb-3"><?= $text['section5_title'] ?></h2>
                    <p class="text-gray-600 leading-relaxed"><?= $text['section5_content'] ?></p>
                </div>

                <div>
                    <h2 class="text-xl font-bold text-gray-900 mb-3"><?= $text['section6_title'] ?></h2>
                    <p class="text-gray-600 leading-relaxed"><?= $text['section6_content'] ?></p>
                </div>

                <div>
                    <h2 class="text-xl font-bold text-gray-900 mb-3"><?= $text['section7_title'] ?></h2>
                    <p class="text-gray-600 leading-relaxed"><?= $text['section7_content'] ?></p>
                </div>

                <div class="bg-primary-50 rounded-xl p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-3"><?= $text['contact_title'] ?></h2>
                    <p class="text-gray-600"><?= $text['contact_content'] ?></p>
                    <a href="/<?= $currentLang ?>/contact" class="inline-block mt-4 bg-primary-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-primary-700 transition">
                        <?= __('nav_contact') ?>
                    </a>
                </div>
            </div>
        </div>
    </section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
