<?php
/**
 * Contact Page - صفحة اتصل بنا
 */

$pageTitle = $currentLang === 'ar' ? 'اتصل بنا | بي تاج' : 'Contact Us | BeTaj';
$pageDescription = $currentLang === 'ar' ? 'تواصل مع فريق بي تاج. نحن هنا لمساعدتك في كل ما يتعلق بالعقارات.' : 'Contact the BeTaj team. We\'re here to help you with everything related to real estate.';

require_once __DIR__ . '/../includes/header.php';

// Translations
$t = [
    'ar' => [
        'hero_title' => 'اتصل بنا',
        'hero_subtitle' => 'نحن هنا لمساعدتك. تواصل معنا بأي طريقة تناسبك',
        'contact_info' => 'معلومات التواصل',
        'hotline' => 'الخط الساخن',
        'whatsapp' => 'واتساب',
        'email' => 'البريد الإلكتروني',
        'address' => 'العنوان',
        'address_value' => 'القاهرة، مصر',
        'follow_us' => 'تابعنا على',
        'working_hours' => 'ساعات العمل',
        'sat_thu' => 'السبت - الخميس',
        'friday' => 'الجمعة',
        'closed' => 'مغلق',
        'send_message' => 'أرسل رسالة',
        'full_name' => 'الاسم الكامل',
        'phone' => 'رقم الهاتف',
        'subject' => 'الموضوع',
        'message' => 'الرسالة',
        'send_btn' => 'إرسال الرسالة',
        'subject_general' => 'استفسار عام',
        'subject_support' => 'دعم فني',
        'subject_partnership' => 'شراكة تجارية',
        'subject_complaint' => 'شكوى',
        'subject_suggestion' => 'اقتراح',
        'map_placeholder' => 'خريطة الموقع (Google Maps)',
        'success_msg' => 'تم إرسال رسالتك بنجاح! سنتواصل معك قريباً.',
    ],
    'en' => [
        'hero_title' => 'Contact Us',
        'hero_subtitle' => 'We\'re here to help. Reach out to us in any way that suits you',
        'contact_info' => 'Contact Information',
        'hotline' => 'Hotline',
        'whatsapp' => 'WhatsApp',
        'email' => 'Email',
        'address' => 'Address',
        'address_value' => 'Cairo, Egypt',
        'follow_us' => 'Follow Us',
        'working_hours' => 'Working Hours',
        'sat_thu' => 'Saturday - Thursday',
        'friday' => 'Friday',
        'closed' => 'Closed',
        'send_message' => 'Send a Message',
        'full_name' => 'Full Name',
        'phone' => 'Phone Number',
        'subject' => 'Subject',
        'message' => 'Message',
        'send_btn' => 'Send Message',
        'subject_general' => 'General Inquiry',
        'subject_support' => 'Technical Support',
        'subject_partnership' => 'Business Partnership',
        'subject_complaint' => 'Complaint',
        'subject_suggestion' => 'Suggestion',
        'map_placeholder' => 'Location Map (Google Maps)',
        'success_msg' => 'Your message has been sent successfully! We\'ll contact you soon.',
    ]
];

$text = $t[$currentLang];
?>

    <!-- Hero -->
    <section class="bg-gradient-to-<?= $isRTL ? 'l' : 'r' ?> from-primary-900 to-primary-700 text-white py-16">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-4xl font-bold mb-4"><?= $text['hero_title'] ?></h1>
            <p class="text-blue-100 text-lg"><?= $text['hero_subtitle'] ?></p>
        </div>
    </section>

    <!-- Contact Content -->
    <section class="container mx-auto px-4 py-12">
        <div class="max-w-5xl mx-auto grid md:grid-cols-2 gap-8">
            <!-- Contact Info -->
            <div class="space-y-6">
                <div class="bg-white rounded-2xl shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-6"><?= $text['contact_info'] ?></h2>

                    <div class="space-y-4">
                        <?php if (!empty($settings['contact_phone'])): ?>
                        <a href="tel:<?= $settings['contact_phone'] ?>" class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl hover:bg-primary-50 transition">
                            <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center">
                                <i class="fa-solid fa-phone text-primary-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500"><?= $text['hotline'] ?></p>
                                <p class="font-bold text-gray-900" dir="ltr"><?= $settings['contact_phone'] ?></p>
                            </div>
                        </a>
                        <?php endif; ?>

                        <?php if (!empty($settings['contact_whatsapp'])): ?>
                        <a href="https://wa.me/<?= $settings['contact_whatsapp'] ?>" class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl hover:bg-green-50 transition">
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fa-brands fa-whatsapp text-green-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500"><?= $text['whatsapp'] ?></p>
                                <p class="font-bold text-gray-900" dir="ltr">+<?= $settings['contact_whatsapp'] ?></p>
                            </div>
                        </a>
                        <?php endif; ?>

                        <?php if (!empty($settings['contact_email'])): ?>
                        <a href="mailto:<?= $settings['contact_email'] ?>" class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl hover:bg-blue-50 transition">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fa-solid fa-envelope text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500"><?= $text['email'] ?></p>
                                <p class="font-bold text-gray-900"><?= $settings['contact_email'] ?></p>
                            </div>
                        </a>
                        <?php endif; ?>

                        <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl">
                            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                                <i class="fa-solid fa-location-dot text-purple-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500"><?= $text['address'] ?></p>
                                <p class="font-bold text-gray-900"><?= $text['address_value'] ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Social -->
                <div class="bg-white rounded-2xl shadow-md p-6">
                    <h3 class="font-bold text-gray-900 mb-4"><?= $text['follow_us'] ?></h3>
                    <div class="flex gap-3">
                        <?php if (!empty($settings['facebook_url'])): ?>
                        <a href="<?= $settings['facebook_url'] ?>" target="_blank" class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center text-white hover:bg-blue-700 transition">
                            <i class="fa-brands fa-facebook-f text-xl"></i>
                        </a>
                        <?php endif; ?>
                        <?php if (!empty($settings['instagram_url'])): ?>
                        <a href="<?= $settings['instagram_url'] ?>" target="_blank" class="w-12 h-12 bg-gradient-to-br from-purple-600 to-pink-500 rounded-xl flex items-center justify-center text-white hover:opacity-90 transition">
                            <i class="fa-brands fa-instagram text-xl"></i>
                        </a>
                        <?php endif; ?>
                        <?php if (!empty($settings['twitter_url'])): ?>
                        <a href="<?= $settings['twitter_url'] ?>" target="_blank" class="w-12 h-12 bg-sky-500 rounded-xl flex items-center justify-center text-white hover:bg-sky-600 transition">
                            <i class="fa-brands fa-twitter text-xl"></i>
                        </a>
                        <?php endif; ?>
                        <?php if (!empty($settings['linkedin_url'])): ?>
                        <a href="<?= $settings['linkedin_url'] ?>" target="_blank" class="w-12 h-12 bg-blue-700 rounded-xl flex items-center justify-center text-white hover:bg-blue-800 transition">
                            <i class="fa-brands fa-linkedin-in text-xl"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Working Hours -->
                <div class="bg-white rounded-2xl shadow-md p-6">
                    <h3 class="font-bold text-gray-900 mb-4"><?= $text['working_hours'] ?></h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600"><?= $text['sat_thu'] ?></span>
                            <span class="font-medium">9:00 <?= $isRTL ? 'ص' : 'AM' ?> - 9:00 <?= $isRTL ? 'م' : 'PM' ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600"><?= $text['friday'] ?></span>
                            <span class="font-medium text-red-500"><?= $text['closed'] ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="bg-white rounded-2xl shadow-md p-6 md:p-8">
                <h2 class="text-xl font-bold text-gray-900 mb-6"><?= $text['send_message'] ?></h2>
                <form id="contact-form" action="/api/leads" method="POST" class="space-y-5">
                    <input type="hidden" name="source" value="contact_form">
                    <input type="hidden" name="lang" value="<?= $currentLang ?>">

                    <div>
                        <label class="block text-gray-700 font-semibold mb-2"><?= $text['full_name'] ?></label>
                        <input type="text" name="name" required
                            class="w-full border-2 border-gray-200 rounded-xl p-3 focus:border-primary-500 outline-none transition">
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-2"><?= $text['email'] ?></label>
                        <input type="email" name="email" required
                            class="w-full border-2 border-gray-200 rounded-xl p-3 focus:border-primary-500 outline-none transition" dir="ltr">
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-2"><?= $text['phone'] ?></label>
                        <input type="tel" name="phone"
                            class="w-full border-2 border-gray-200 rounded-xl p-3 focus:border-primary-500 outline-none transition" dir="ltr">
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-2"><?= $text['subject'] ?></label>
                        <select name="subject" class="w-full border-2 border-gray-200 rounded-xl p-3 focus:border-primary-500 outline-none bg-white">
                            <option value="general"><?= $text['subject_general'] ?></option>
                            <option value="support"><?= $text['subject_support'] ?></option>
                            <option value="partnership"><?= $text['subject_partnership'] ?></option>
                            <option value="complaint"><?= $text['subject_complaint'] ?></option>
                            <option value="suggestion"><?= $text['subject_suggestion'] ?></option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-2"><?= $text['message'] ?></label>
                        <textarea name="message" rows="5" required
                            class="w-full border-2 border-gray-200 rounded-xl p-3 focus:border-primary-500 outline-none transition resize-none"></textarea>
                    </div>

                    <button type="submit" class="w-full bg-primary-600 text-white py-3 rounded-xl font-bold hover:bg-primary-700 transition">
                        <i class="fa-solid fa-paper-plane <?= $isRTL ? 'ml-2' : 'mr-2' ?>"></i>
                        <?= $text['send_btn'] ?>
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- Map Placeholder -->
    <section class="container mx-auto px-4 pb-12">
        <div class="max-w-5xl mx-auto bg-gray-200 rounded-2xl h-64 flex items-center justify-center">
            <p class="text-gray-500"><?= $text['map_placeholder'] ?></p>
        </div>
    </section>

    <script>
        document.getElementById('contact-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const btn = this.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
            btn.disabled = true;

            try {
                const formData = new FormData(this);
                const response = await fetch('/api/leads', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.success) {
                    showToast('<?= $text['success_msg'] ?>', 'success');
                    this.reset();
                } else {
                    showToast(result.message || '<?= __('error') ?>', 'error');
                }
            } catch (error) {
                showToast('<?= __('error') ?>', 'error');
            }

            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    </script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
