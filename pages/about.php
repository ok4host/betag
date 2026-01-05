<?php
/**
 * About Page - صفحة من نحن
 */

$pageTitle = $currentLang === 'ar' ? 'من نحن | بي تاج - منصة العقارات الأولى في مصر' : 'About Us | BeTaj - Egypt\'s Leading Real Estate Platform';
$pageDescription = $currentLang === 'ar' ? 'تعرف على بي تاج، المنصة العقارية الرائدة في مصر. نساعدك في إيجاد منزل أحلامك بسهولة وثقة.' : 'Learn about BeTaj, Egypt\'s leading real estate platform. We help you find your dream home easily and confidently.';

require_once __DIR__ . '/../includes/header.php';

// Translations
$t = [
    'ar' => [
        'hero_title' => 'من نحن',
        'hero_subtitle' => 'نحن نؤمن بأن إيجاد المنزل المثالي يجب أن يكون تجربة سهلة وممتعة',
        'story_title' => 'قصتنا',
        'story_p1' => 'بدأت بي تاج في عام 2024 برؤية واضحة: جعل سوق العقارات في مصر أكثر شفافية وسهولة للجميع. لاحظنا أن البحث عن عقار في مصر كان تجربة مرهقة ومعقدة، مليئة بالمعلومات غير الدقيقة والوسطاء غير الموثوقين.',
        'story_p2' => 'قررنا تغيير ذلك. بنينا منصة تجمع بين التكنولوجيا الحديثة والخبرة العقارية لنقدم لك تجربة بحث سلسة ومعلومات دقيقة وموثوقة.',
        'story_p3' => 'اليوم، نخدم آلاف العملاء شهرياً ونساعدهم في إيجاد منازلهم المثالية، سواء للشراء أو الإيجار، في جميع أنحاء مصر.',
        'mission_title' => 'مهمتنا',
        'mission_text' => 'تسهيل رحلة البحث عن العقار من خلال توفير منصة شاملة وموثوقة تجمع بين البائعين والمشترين بطريقة شفافة وفعالة.',
        'vision_title' => 'رؤيتنا',
        'vision_text' => 'أن نكون المنصة العقارية الأولى والأكثر ثقة في الشرق الأوسط، حيث يجد كل شخص منزل أحلامه بسهولة.',
        'stats_title' => 'أرقامنا تتحدث',
        'stats_properties' => 'عقار متاح',
        'stats_clients' => 'عميل سعيد',
        'stats_compounds' => 'كمبوند',
        'stats_agents' => 'وسيط معتمد',
        'values_title' => 'قيمنا',
        'value_trust' => 'الثقة',
        'value_trust_text' => 'نبني علاقات طويلة الأمد مبنية على الصدق والشفافية',
        'value_innovation' => 'الابتكار',
        'value_innovation_text' => 'نستخدم أحدث التقنيات لتحسين تجربة البحث عن العقارات',
        'value_customer' => 'العميل أولاً',
        'value_customer_text' => 'كل قرار نتخذه يضع مصلحة العميل في المقام الأول',
        'cta_title' => 'هل أنت مستعد للبدء؟',
        'cta_subtitle' => 'ابدأ رحلة البحث عن منزلك المثالي الآن',
        'cta_search' => 'ابحث عن عقار',
        'cta_contact' => 'تواصل معنا',
    ],
    'en' => [
        'hero_title' => 'About Us',
        'hero_subtitle' => 'We believe that finding the perfect home should be an easy and enjoyable experience',
        'story_title' => 'Our Story',
        'story_p1' => 'BeTaj started in 2024 with a clear vision: to make the real estate market in Egypt more transparent and accessible to everyone. We noticed that searching for property in Egypt was a tiring and complex experience, full of inaccurate information and unreliable brokers.',
        'story_p2' => 'We decided to change that. We built a platform that combines modern technology with real estate expertise to provide you with a smooth search experience and accurate, reliable information.',
        'story_p3' => 'Today, we serve thousands of clients monthly and help them find their ideal homes, whether for purchase or rent, throughout Egypt.',
        'mission_title' => 'Our Mission',
        'mission_text' => 'To facilitate the property search journey by providing a comprehensive and reliable platform that connects sellers and buyers in a transparent and efficient manner.',
        'vision_title' => 'Our Vision',
        'vision_text' => 'To be the leading and most trusted real estate platform in the Middle East, where everyone can easily find their dream home.',
        'stats_title' => 'Our Numbers Speak',
        'stats_properties' => 'Available Properties',
        'stats_clients' => 'Happy Clients',
        'stats_compounds' => 'Compounds',
        'stats_agents' => 'Certified Agents',
        'values_title' => 'Our Values',
        'value_trust' => 'Trust',
        'value_trust_text' => 'We build long-term relationships based on honesty and transparency',
        'value_innovation' => 'Innovation',
        'value_innovation_text' => 'We use the latest technologies to improve the property search experience',
        'value_customer' => 'Customer First',
        'value_customer_text' => 'Every decision we make puts the customer\'s interest first',
        'cta_title' => 'Ready to Get Started?',
        'cta_subtitle' => 'Start your journey to find your perfect home now',
        'cta_search' => 'Search Property',
        'cta_contact' => 'Contact Us',
    ]
];

$text = $t[$currentLang];
?>

    <!-- Hero -->
    <section class="bg-gradient-to-<?= $isRTL ? 'l' : 'r' ?> from-primary-900 to-primary-700 text-white py-20">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4"><?= $text['hero_title'] ?></h1>
            <p class="text-blue-100 text-lg max-w-2xl mx-auto">
                <?= $text['hero_subtitle'] ?>
            </p>
        </div>
    </section>

    <!-- Story -->
    <section class="container mx-auto px-4 py-16">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-2xl shadow-lg p-8 md:p-12">
                <h2 class="text-2xl font-bold text-gray-900 mb-6"><?= $text['story_title'] ?></h2>
                <p class="text-gray-600 leading-relaxed mb-6"><?= $text['story_p1'] ?></p>
                <p class="text-gray-600 leading-relaxed mb-6"><?= $text['story_p2'] ?></p>
                <p class="text-gray-600 leading-relaxed"><?= $text['story_p3'] ?></p>
            </div>
        </div>
    </section>

    <!-- Mission & Vision -->
    <section class="bg-white py-16">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                <div class="bg-primary-50 rounded-2xl p-8">
                    <div class="w-14 h-14 bg-primary-600 rounded-xl flex items-center justify-center mb-4">
                        <i class="fa-solid fa-bullseye text-2xl text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3"><?= $text['mission_title'] ?></h3>
                    <p class="text-gray-600"><?= $text['mission_text'] ?></p>
                </div>
                <div class="bg-green-50 rounded-2xl p-8">
                    <div class="w-14 h-14 bg-green-600 rounded-xl flex items-center justify-center mb-4">
                        <i class="fa-solid fa-eye text-2xl text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3"><?= $text['vision_title'] ?></h3>
                    <p class="text-gray-600"><?= $text['vision_text'] ?></p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats -->
    <section class="container mx-auto px-4 py-16">
        <h2 class="text-2xl font-bold text-gray-900 text-center mb-12"><?= $text['stats_title'] ?></h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-4xl mx-auto">
            <div class="text-center bg-white rounded-2xl p-6 shadow-md">
                <p class="text-4xl font-bold text-primary-600 mb-2">+50K</p>
                <p class="text-gray-500"><?= $text['stats_properties'] ?></p>
            </div>
            <div class="text-center bg-white rounded-2xl p-6 shadow-md">
                <p class="text-4xl font-bold text-primary-600 mb-2">+10K</p>
                <p class="text-gray-500"><?= $text['stats_clients'] ?></p>
            </div>
            <div class="text-center bg-white rounded-2xl p-6 shadow-md">
                <p class="text-4xl font-bold text-primary-600 mb-2">+200</p>
                <p class="text-gray-500"><?= $text['stats_compounds'] ?></p>
            </div>
            <div class="text-center bg-white rounded-2xl p-6 shadow-md">
                <p class="text-4xl font-bold text-primary-600 mb-2">+500</p>
                <p class="text-gray-500"><?= $text['stats_agents'] ?></p>
            </div>
        </div>
    </section>

    <!-- Values -->
    <section class="bg-gray-900 text-white py-16">
        <div class="container mx-auto px-4">
            <h2 class="text-2xl font-bold text-center mb-12"><?= $text['values_title'] ?></h2>
            <div class="grid md:grid-cols-3 gap-8 max-w-4xl mx-auto">
                <div class="text-center">
                    <div class="w-16 h-16 bg-primary-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-handshake text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold mb-2"><?= $text['value_trust'] ?></h3>
                    <p class="text-gray-400 text-sm"><?= $text['value_trust_text'] ?></p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-rocket text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold mb-2"><?= $text['value_innovation'] ?></h3>
                    <p class="text-gray-400 text-sm"><?= $text['value_innovation_text'] ?></p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-yellow-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-heart text-2xl text-gray-900"></i>
                    </div>
                    <h3 class="text-lg font-bold mb-2"><?= $text['value_customer'] ?></h3>
                    <p class="text-gray-400 text-sm"><?= $text['value_customer_text'] ?></p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="container mx-auto px-4 py-16 text-center">
        <h2 class="text-2xl font-bold text-gray-900 mb-4"><?= $text['cta_title'] ?></h2>
        <p class="text-gray-500 mb-8"><?= $text['cta_subtitle'] ?></p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="/<?= $currentLang ?>/search" class="bg-primary-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-primary-700 transition">
                <?= $text['cta_search'] ?>
            </a>
            <a href="/<?= $currentLang ?>/contact" class="border-2 border-gray-300 text-gray-700 px-8 py-3 rounded-xl font-bold hover:border-primary-500 hover:text-primary-600 transition">
                <?= $text['cta_contact'] ?>
            </a>
        </div>
    </section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
