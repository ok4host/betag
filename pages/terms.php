<?php
/**
 * Terms & Conditions Page - صفحة الشروط والأحكام
 */

$pageTitle = $currentLang === 'ar' ? 'الشروط والأحكام | بي تاج' : 'Terms & Conditions | BeTaj';
$pageDescription = $currentLang === 'ar' ? 'اطلع على شروط وأحكام استخدام موقع بي تاج' : 'Read BeTaj\'s Terms and Conditions';

require_once __DIR__ . '/../includes/header.php';

// Translations
$t = [
    'ar' => [
        'title' => 'الشروط والأحكام',
        'last_updated' => 'آخر تحديث',
        'intro' => 'مرحباً بك في بي تاج. باستخدامك لموقعنا، فإنك توافق على الالتزام بهذه الشروط والأحكام. يرجى قراءتها بعناية.',
        'section1_title' => 'قبول الشروط',
        'section1_content' => 'باستخدامك لموقع بي تاج، فإنك توافق على هذه الشروط والأحكام وسياسة الخصوصية الخاصة بنا. إذا كنت لا توافق على أي من هذه الشروط، يرجى عدم استخدام الموقع.',
        'section2_title' => 'استخدام الموقع',
        'section2_content' => 'يجب استخدام الموقع للأغراض المشروعة فقط. يحظر استخدام الموقع بطريقة قد تضر بالموقع أو تعطل توفره أو إمكانية الوصول إليه.',
        'section3_title' => 'حسابات المستخدمين',
        'section3_content' => 'عند إنشاء حساب، أنت مسؤول عن الحفاظ على سرية معلومات حسابك وجميع الأنشطة التي تتم تحت حسابك.',
        'section4_title' => 'المحتوى',
        'section4_content' => 'أنت مسؤول عن أي محتوى تنشره على الموقع. يجب أن يكون المحتوى دقيقاً وقانونياً ولا ينتهك حقوق الآخرين.',
        'section5_title' => 'إخلاء المسؤولية',
        'section5_content' => 'المعلومات المقدمة على الموقع هي لأغراض إعلامية فقط. لا نضمن دقة أو اكتمال المعلومات العقارية المعروضة.',
        'section6_title' => 'حقوق الملكية الفكرية',
        'section6_content' => 'جميع المحتويات على الموقع، بما في ذلك النصوص والرسومات والشعارات، هي ملك لـ بي تاج أو مرخصيها ومحمية بموجب قوانين حقوق النشر.',
        'section7_title' => 'التعديلات',
        'section7_content' => 'نحتفظ بالحق في تعديل هذه الشروط في أي وقت. سيتم نشر التغييرات على هذه الصفحة مع تحديث تاريخ "آخر تحديث".',
        'section8_title' => 'القانون المعمول به',
        'section8_content' => 'تخضع هذه الشروط للقوانين المصرية. أي نزاعات تنشأ عن استخدام الموقع ستخضع للاختصاص القضائي الحصري للمحاكم المصرية.',
    ],
    'en' => [
        'title' => 'Terms & Conditions',
        'last_updated' => 'Last Updated',
        'intro' => 'Welcome to BeTaj. By using our website, you agree to comply with these terms and conditions. Please read them carefully.',
        'section1_title' => 'Acceptance of Terms',
        'section1_content' => 'By using BeTaj website, you agree to these terms and conditions and our privacy policy. If you do not agree to any of these terms, please do not use the site.',
        'section2_title' => 'Use of the Site',
        'section2_content' => 'The site must be used for lawful purposes only. It is prohibited to use the site in a way that may damage, disable, or impair the site.',
        'section3_title' => 'User Accounts',
        'section3_content' => 'When creating an account, you are responsible for maintaining the confidentiality of your account information and all activities under your account.',
        'section4_title' => 'Content',
        'section4_content' => 'You are responsible for any content you post on the site. Content must be accurate, legal, and not infringe on the rights of others.',
        'section5_title' => 'Disclaimer',
        'section5_content' => 'Information provided on the site is for informational purposes only. We do not guarantee the accuracy or completeness of the real estate information displayed.',
        'section6_title' => 'Intellectual Property Rights',
        'section6_content' => 'All content on the site, including text, graphics, and logos, is the property of BeTaj or its licensors and is protected by copyright laws.',
        'section7_title' => 'Modifications',
        'section7_content' => 'We reserve the right to modify these terms at any time. Changes will be posted on this page with an updated "Last Updated" date.',
        'section8_title' => 'Governing Law',
        'section8_content' => 'These terms are governed by Egyptian laws. Any disputes arising from the use of the site will be subject to the exclusive jurisdiction of Egyptian courts.',
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
                <?php for ($i = 1; $i <= 8; $i++): ?>
                <div>
                    <h2 class="text-xl font-bold text-gray-900 mb-3"><?= $text["section{$i}_title"] ?></h2>
                    <p class="text-gray-600 leading-relaxed"><?= $text["section{$i}_content"] ?></p>
                </div>
                <?php endfor; ?>
            </div>
        </div>
    </section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
