    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300 py-12 border-t-4 border-primary-600">
        <div class="container mx-auto px-4 grid grid-cols-1 md:grid-cols-4 gap-8 <?= $isRTL ? 'text-right' : 'text-left' ?>">
            <div>
                <div class="flex items-center gap-2 mb-4 text-white">
                    <div class="bg-primary-600 p-2 rounded-lg">
                        <i class="fa-solid fa-house text-xl"></i>
                    </div>
                    <span class="text-2xl font-bold"><?= htmlspecialchars($settings['site_name'] ?? __('site_name')) ?></span>
                </div>
                <p class="text-sm leading-relaxed text-gray-400">
                    <?= __('footer_about_text') ?>
                </p>
                <div class="flex gap-3 mt-4">
                    <?php if (!empty($settings['facebook_url'])): ?>
                    <a href="<?= $settings['facebook_url'] ?>" target="_blank" rel="noopener" class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center hover:bg-blue-500 transition">
                        <i class="fa-brands fa-facebook-f text-white"></i>
                    </a>
                    <?php endif; ?>
                    <?php if (!empty($settings['instagram_url'])): ?>
                    <a href="<?= $settings['instagram_url'] ?>" target="_blank" rel="noopener" class="w-10 h-10 bg-gradient-to-br from-purple-600 to-pink-500 rounded-lg flex items-center justify-center hover:opacity-80 transition">
                        <i class="fa-brands fa-instagram text-white"></i>
                    </a>
                    <?php endif; ?>
                    <?php if (!empty($settings['twitter_url'])): ?>
                    <a href="<?= $settings['twitter_url'] ?>" target="_blank" rel="noopener" class="w-10 h-10 bg-sky-500 rounded-lg flex items-center justify-center hover:bg-sky-400 transition">
                        <i class="fa-brands fa-twitter text-white"></i>
                    </a>
                    <?php endif; ?>
                    <?php if (!empty($settings['youtube_url'])): ?>
                    <a href="<?= $settings['youtube_url'] ?>" target="_blank" rel="noopener" class="w-10 h-10 bg-red-600 rounded-lg flex items-center justify-center hover:bg-red-500 transition">
                        <i class="fa-brands fa-youtube text-white"></i>
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <div>
                <h4 class="text-white font-bold mb-4"><?= __('footer_links') ?></h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="/<?= $currentLang ?>/search?location=new-cairo&type=sale" class="hover:text-primary-400 transition"><?= $isRTL ? 'شقق للبيع في التجمع' : 'Apartments in New Cairo' ?></a></li>
                    <li><a href="/<?= $currentLang ?>/search?location=sheikh-zayed" class="hover:text-primary-400 transition"><?= $isRTL ? 'عقارات الشيخ زايد' : 'Sheikh Zayed Properties' ?></a></li>
                    <li><a href="/<?= $currentLang ?>/search?location=new-capital" class="hover:text-primary-400 transition"><?= $isRTL ? 'العاصمة الإدارية' : 'New Capital' ?></a></li>
                    <li><a href="/<?= $currentLang ?>/compounds" class="hover:text-primary-400 transition"><?= __('nav_compounds') ?></a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-white font-bold mb-4"><?= $isRTL ? 'خدماتنا' : 'Services' ?></h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="/<?= $currentLang ?>/add-property" class="hover:text-primary-400 transition"><?= __('add_property') ?></a></li>
                    <li><a href="/<?= $currentLang ?>/new-projects" class="hover:text-primary-400 transition"><?= __('nav_new_projects') ?></a></li>
                    <li><a href="/<?= $currentLang ?>/blog" class="hover:text-primary-400 transition"><?= __('nav_blog') ?></a></li>
                    <li><a href="/<?= $currentLang ?>/about" class="hover:text-primary-400 transition"><?= __('nav_about') ?></a></li>
                    <li><a href="/<?= $currentLang ?>/contact" class="hover:text-primary-400 transition"><?= __('nav_contact') ?></a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-white font-bold mb-4"><?= __('footer_contact') ?></h4>
                <div class="flex flex-col gap-3 text-sm">
                    <?php if (!empty($settings['contact_phone'])): ?>
                    <a href="tel:<?= $settings['contact_phone'] ?>" class="flex items-center gap-2 hover:text-primary-400 transition">
                        <i class="fa-solid fa-phone w-5"></i> <?= $settings['contact_phone'] ?>
                    </a>
                    <?php endif; ?>
                    <?php if (!empty($settings['contact_email'])): ?>
                    <a href="mailto:<?= $settings['contact_email'] ?>" class="flex items-center gap-2 hover:text-primary-400 transition">
                        <i class="fa-solid fa-envelope w-5"></i> <?= $settings['contact_email'] ?>
                    </a>
                    <?php endif; ?>
                    <?php if (!empty($settings['contact_whatsapp'])): ?>
                    <a href="https://wa.me/<?= $settings['contact_whatsapp'] ?>" class="flex items-center gap-2 hover:text-primary-400 transition">
                        <i class="fa-brands fa-whatsapp w-5"></i> <?= __('whatsapp') ?>
                    </a>
                    <?php endif; ?>
                </div>

                <!-- Newsletter -->
                <div class="mt-6">
                    <h5 class="text-white font-semibold mb-2"><?= __('footer_newsletter') ?></h5>
                    <p class="text-xs text-gray-400 mb-2"><?= __('footer_newsletter_text') ?></p>
                    <form action="/api/newsletter" method="POST" class="flex gap-2">
                        <input type="email" name="email" required
                               placeholder="<?= __('footer_newsletter_placeholder') ?>"
                               class="flex-1 px-3 py-2 rounded-lg bg-gray-800 border border-gray-700 text-white text-sm focus:border-primary-500 focus:outline-none">
                        <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition">
                            <i class="fa-solid fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center mt-12 pt-8 border-t border-gray-800 text-sm text-gray-500">
                <p><?= __('footer_rights') ?> © <?= date('Y') ?> <?= htmlspecialchars($settings['site_name'] ?? __('site_name')) ?></p>
                <div class="flex gap-4 mt-4 md:mt-0">
                    <a href="/<?= $currentLang ?>/privacy" class="hover:text-gray-300"><?= __('footer_privacy') ?></a>
                    <a href="/<?= $currentLang ?>/terms" class="hover:text-gray-300"><?= __('footer_terms') ?></a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Floating WhatsApp -->
    <?php if (!empty($settings['contact_whatsapp'])): ?>
    <a href="https://wa.me/<?= $settings['contact_whatsapp'] ?>" target="_blank" rel="noopener"
       class="fixed bottom-6 <?= $isRTL ? 'left-6' : 'right-6' ?> bg-green-500 text-white w-14 h-14 rounded-full flex items-center justify-center shadow-lg hover:bg-green-600 transition z-40 whatsapp-float">
        <i class="fa-brands fa-whatsapp text-2xl"></i>
    </a>
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        .whatsapp-float {
            animation: float 3s ease-in-out infinite;
        }
    </style>
    <?php endif; ?>

    <!-- Toast Container -->
    <div id="toast-container" class="fixed top-4 left-1/2 -translate-x-1/2 z-50"></div>

    <script>
        // Toast notification
        function showToast(message, type = 'success') {
            const colors = { success: 'bg-green-500', error: 'bg-red-500', info: 'bg-primary-600', warning: 'bg-yellow-500' };
            const icons = { success: 'fa-check-circle', error: 'fa-times-circle', info: 'fa-info-circle', warning: 'fa-exclamation-triangle' };
            const toast = document.createElement('div');
            toast.className = `${colors[type]} text-white px-6 py-3 rounded-xl shadow-lg mb-2 transform transition-all duration-300`;
            toast.innerHTML = `<i class="fa-solid ${icons[type]} <?= $isRTL ? 'ml-2' : 'mr-2' ?>"></i>${message}`;
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(-20px)';
            document.getElementById('toast-container').appendChild(toast);

            // Animate in
            setTimeout(() => {
                toast.style.opacity = '1';
                toast.style.transform = 'translateY(0)';
            }, 10);

            // Animate out
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(-20px)';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // Track events for analytics
        function trackEvent(name, params = {}) {
            if (typeof gtag !== 'undefined') gtag('event', name, params);
            if (typeof fbq !== 'undefined') fbq('trackCustom', name, params);
        }

        // API helper
        async function apiRequest(url, method = 'GET', data = null) {
            const options = {
                method,
                headers: { 'Content-Type': 'application/json' }
            };
            if (data) options.body = JSON.stringify(data);
            const response = await fetch(url, options);
            return response.json();
        }

        // Update favorites count
        async function updateFavoritesCount() {
            <?php if (isLoggedIn()): ?>
            try {
                const response = await apiRequest('/api/favorites?action=count');
                const badge = document.getElementById('favorites-count');
                if (badge && response.count > 0) {
                    badge.textContent = response.count;
                    badge.classList.remove('hidden');
                }
            } catch (e) {
                console.log('Could not fetch favorites count');
            }
            <?php endif; ?>
        }
        updateFavoritesCount();

        // Current language for JS
        const currentLang = '<?= $currentLang ?>';
        const isRTL = <?= $isRTL ? 'true' : 'false' ?>;

        // Translations for JS
        const translations = {
            loading: '<?= __('loading') ?>',
            success: '<?= __('success') ?>',
            error: '<?= __('error') ?>',
            confirm: '<?= __('confirm') ?>',
            cancel: '<?= __('cancel') ?>',
            add_to_favorites: '<?= __('add_to_favorites') ?>',
            remove_from_favorites: '<?= __('remove_from_favorites') ?>'
        };

        function __(key) {
            return translations[key] || key;
        }
    </script>
</body>
</html>
