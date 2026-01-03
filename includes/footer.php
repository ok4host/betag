    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300 py-12 border-t-4 border-primary-600">
        <div class="container mx-auto px-4 grid grid-cols-1 md:grid-cols-4 gap-8 text-right">
            <div>
                <div class="flex items-center gap-2 mb-4 text-white">
                    <div class="bg-primary-600 p-2 rounded-lg">
                        <i class="fa-solid fa-house text-xl"></i>
                    </div>
                    <span class="text-2xl font-bold"><?= htmlspecialchars($settings['site_name'] ?? 'بي تاج') ?></span>
                </div>
                <p class="text-sm leading-relaxed text-gray-400">
                    أفضل منصة عقارية في مصر والشرق الأوسط. نساعدك في اتخاذ قرارك العقاري بسهولة وثقة.
                </p>
                <div class="flex gap-3 mt-4">
                    <?php if (!empty($settings['facebook_url'])): ?>
                    <a href="<?= $settings['facebook_url'] ?>" target="_blank" class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center hover:bg-blue-500 transition">
                        <i class="fa-brands fa-facebook-f text-white"></i>
                    </a>
                    <?php endif; ?>
                    <?php if (!empty($settings['instagram_url'])): ?>
                    <a href="<?= $settings['instagram_url'] ?>" target="_blank" class="w-10 h-10 bg-gradient-to-br from-purple-600 to-pink-500 rounded-lg flex items-center justify-center hover:opacity-80 transition">
                        <i class="fa-brands fa-instagram text-white"></i>
                    </a>
                    <?php endif; ?>
                    <?php if (!empty($settings['twitter_url'])): ?>
                    <a href="<?= $settings['twitter_url'] ?>" target="_blank" class="w-10 h-10 bg-sky-500 rounded-lg flex items-center justify-center hover:bg-sky-400 transition">
                        <i class="fa-brands fa-twitter text-white"></i>
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <div>
                <h4 class="text-white font-bold mb-4">روابط سريعة</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="/search?location=new-cairo&type=sale" class="hover:text-primary-400 transition">شقق للبيع في التجمع</a></li>
                    <li><a href="/search?location=sheikh-zayed" class="hover:text-primary-400 transition">عقارات الشيخ زايد</a></li>
                    <li><a href="/search?location=new-capital" class="hover:text-primary-400 transition">العاصمة الإدارية</a></li>
                    <li><a href="/compounds" class="hover:text-primary-400 transition">دليل الكمبوندات</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-white font-bold mb-4">خدماتنا</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="/add-property" class="hover:text-primary-400 transition">أضف عقارك</a></li>
                    <li><a href="/new-projects" class="hover:text-primary-400 transition">مشاريع جديدة</a></li>
                    <li><a href="/about" class="hover:text-primary-400 transition">من نحن</a></li>
                    <li><a href="/contact" class="hover:text-primary-400 transition">اتصل بنا</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-white font-bold mb-4">تواصل معنا</h4>
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
                        <i class="fa-brands fa-whatsapp w-5"></i> واتساب
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center mt-12 pt-8 border-t border-gray-800 text-sm text-gray-500">
                <p>جميع الحقوق محفوظة © <?= date('Y') ?> <?= htmlspecialchars($settings['site_name'] ?? 'بي تاج') ?></p>
                <div class="flex gap-4 mt-4 md:mt-0">
                    <a href="/privacy" class="hover:text-gray-300">سياسة الخصوصية</a>
                    <a href="/terms" class="hover:text-gray-300">شروط الاستخدام</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Floating WhatsApp -->
    <?php if (!empty($settings['contact_whatsapp'])): ?>
    <a href="https://wa.me/<?= $settings['contact_whatsapp'] ?>" target="_blank"
       class="fixed bottom-6 left-6 bg-green-500 text-white w-14 h-14 rounded-full flex items-center justify-center shadow-lg hover:bg-green-600 transition z-40"
       style="animation: float 3s ease-in-out infinite;">
        <i class="fa-brands fa-whatsapp text-2xl"></i>
    </a>
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
    </style>
    <?php endif; ?>

    <!-- Toast Container -->
    <div id="toast-container" class="fixed top-4 left-1/2 -translate-x-1/2 z-50"></div>

    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
            const icon = this.querySelector('i');
            icon.classList.toggle('fa-bars');
            icon.classList.toggle('fa-xmark');
        });

        // Toast notification
        function showToast(message, type = 'success') {
            const colors = { success: 'bg-green-500', error: 'bg-red-500', info: 'bg-primary-600' };
            const toast = document.createElement('div');
            toast.className = `${colors[type]} text-white px-6 py-3 rounded-xl shadow-lg mb-2 animate-fade-in`;
            toast.innerHTML = `<i class="fa-solid ${type === 'success' ? 'fa-check-circle' : 'fa-info-circle'} mr-2"></i>${message}`;
            document.getElementById('toast-container').appendChild(toast);
            setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, 3000);
        }

        // Track events
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
            const count = localStorage.getItem('favorites_count') || 0;
            const badge = document.getElementById('favorites-count');
            if (badge && count > 0) {
                badge.textContent = count;
                badge.classList.remove('hidden');
            }
            <?php endif; ?>
        }
        updateFavoritesCount();
    </script>
</body>
</html>
