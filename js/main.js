/**
 * BeTaj - Real Estate Platform
 * Main JavaScript File
 */

// =====================
// Data
// =====================
const properties = [
    {
        id: 1,
        title: "شقة فاخرة للبيع في التجمع الخامس",
        slug: "luxury-apartment-new-cairo-1",
        location: "القاهرة الجديدة، حي النرجس",
        locationSlug: "new-cairo",
        price: 4500000,
        priceFormatted: "4,500,000",
        currency: "ج.م",
        type: "sale",
        typeLabel: "بيع",
        propertyType: "apartment",
        beds: 3,
        baths: 2,
        area: 185,
        image: "https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&q=80&w=1000",
        images: [
            "https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&q=80&w=1000",
            "https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&q=80&w=1000"
        ],
        featured: true,
        verified: true,
        description: "شقة فاخرة بتشطيب سوبر لوكس في أرقى مناطق التجمع الخامس، قريبة من جميع الخدمات.",
        amenities: ["مصعد", "جراج", "أمن", "حديقة"],
        agent: {
            name: "أحمد محمد",
            phone: "01012345678",
            whatsapp: "201012345678"
        },
        createdAt: "2025-01-01"
    },
    {
        id: 2,
        title: "فيلا مستقلة بحديقة خاصة",
        slug: "villa-beverly-hills-2",
        location: "الشيخ زايد، بيفرلي هيلز",
        locationSlug: "sheikh-zayed",
        price: 12000000,
        priceFormatted: "12,000,000",
        currency: "ج.م",
        type: "sale",
        typeLabel: "بيع",
        propertyType: "villa",
        beds: 5,
        baths: 4,
        area: 450,
        image: "https://images.unsplash.com/photo-1613977257363-707ba9348227?auto=format&fit=crop&q=80&w=1000",
        images: [
            "https://images.unsplash.com/photo-1613977257363-707ba9348227?auto=format&fit=crop&q=80&w=1000"
        ],
        featured: true,
        verified: true,
        description: "فيلا مستقلة فاخرة بحديقة خاصة وحمام سباحة في أفضل كمبوندات الشيخ زايد.",
        amenities: ["حمام سباحة", "حديقة خاصة", "جراج مزدوج", "أمن 24 ساعة"],
        agent: {
            name: "سارة أحمد",
            phone: "01098765432",
            whatsapp: "201098765432"
        },
        createdAt: "2025-01-02"
    },
    {
        id: 3,
        title: "مكتب إداري للإيجار في العاصمة",
        slug: "office-new-capital-3",
        location: "العاصمة الإدارية، الحي المالي",
        locationSlug: "new-capital",
        price: 45000,
        priceFormatted: "45,000",
        currency: "ج.م/شهري",
        type: "rent",
        typeLabel: "إيجار",
        propertyType: "office",
        beds: 0,
        baths: 1,
        area: 90,
        image: "https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&q=80&w=1000",
        images: [
            "https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&q=80&w=1000"
        ],
        featured: false,
        verified: true,
        description: "مكتب إداري بتشطيب كامل في قلب الحي المالي بالعاصمة الإدارية الجديدة.",
        amenities: ["تكييف مركزي", "إنترنت", "أمن", "مصعد"],
        agent: {
            name: "محمود علي",
            phone: "01123456789",
            whatsapp: "201123456789"
        },
        createdAt: "2025-01-03"
    },
    {
        id: 4,
        title: "شاليه يرى البحر مباشرة",
        slug: "chalet-north-coast-4",
        location: "الساحل الشمالي، سيدي عبد الرحمن",
        locationSlug: "sahel",
        price: 6200000,
        priceFormatted: "6,200,000",
        currency: "ج.م",
        type: "sale",
        typeLabel: "بيع",
        propertyType: "chalet",
        beds: 2,
        baths: 2,
        area: 120,
        image: "https://images.unsplash.com/photo-1515263487990-61b07816b324?auto=format&fit=crop&q=80&w=1000",
        images: [
            "https://images.unsplash.com/photo-1515263487990-61b07816b324?auto=format&fit=crop&q=80&w=1000"
        ],
        featured: true,
        verified: false,
        description: "شاليه فاخر بإطلالة مباشرة على البحر في أجمل قرى الساحل الشمالي.",
        amenities: ["إطلالة بحر", "حمام سباحة مشترك", "شاطئ خاص"],
        agent: {
            name: "كريم حسن",
            phone: "01234567890",
            whatsapp: "201234567890"
        },
        createdAt: "2025-01-01"
    },
    {
        id: 5,
        title: "دوبلكس تشطيب الترا سوبر لوكس",
        slug: "duplex-maadi-5",
        location: "المعادي، سرايات",
        locationSlug: "maadi",
        price: 8800000,
        priceFormatted: "8,800,000",
        currency: "ج.م",
        type: "sale",
        typeLabel: "بيع",
        propertyType: "duplex",
        beds: 4,
        baths: 3,
        area: 280,
        image: "https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&q=80&w=1000",
        images: [
            "https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&q=80&w=1000"
        ],
        featured: false,
        verified: true,
        description: "دوبلكس فاخر بتشطيب ألترا سوبر لوكس في قلب المعادي سرايات.",
        amenities: ["تراس", "مصعد خاص", "جراج", "حديقة"],
        agent: {
            name: "منى إبراهيم",
            phone: "01555555555",
            whatsapp: "201555555555"
        },
        createdAt: "2025-01-02"
    },
    {
        id: 6,
        title: "محل تجاري بموقع حيوي",
        slug: "shop-nasr-city-6",
        location: "مدينة نصر، عباس العقاد",
        locationSlug: "nasr-city",
        price: 75000,
        priceFormatted: "75,000",
        currency: "ج.م/شهري",
        type: "rent",
        typeLabel: "إيجار",
        propertyType: "commercial",
        beds: 0,
        baths: 1,
        area: 60,
        image: "https://images.unsplash.com/photo-1556740738-b6a63e27c4df?auto=format&fit=crop&q=80&w=1000",
        images: [
            "https://images.unsplash.com/photo-1556740738-b6a63e27c4df?auto=format&fit=crop&q=80&w=1000"
        ],
        featured: false,
        verified: true,
        description: "محل تجاري بموقع حيوي على شارع عباس العقاد الرئيسي.",
        amenities: ["واجهة زجاجية", "تكييف"],
        agent: {
            name: "عمرو سعيد",
            phone: "01666666666",
            whatsapp: "201666666666"
        },
        createdAt: "2025-01-03"
    }
];

const cities = [
    { name: 'القاهرة الجديدة', slug: 'new-cairo', count: 5420, icon: 'fa-city' },
    { name: 'السادس من أكتوبر', slug: '6-october', count: 3210, icon: 'fa-building' },
    { name: 'العاصمة الإدارية', slug: 'new-capital', count: 2890, icon: 'fa-landmark' },
    { name: 'الشيخ زايد', slug: 'sheikh-zayed', count: 2150, icon: 'fa-home' },
    { name: 'مدينة نصر', slug: 'nasr-city', count: 1890, icon: 'fa-building-columns' },
    { name: 'المعادي', slug: 'maadi', count: 1560, icon: 'fa-tree-city' },
    { name: 'الساحل الشمالي', slug: 'sahel', count: 980, icon: 'fa-umbrella-beach' },
    { name: 'العين السخنة', slug: 'ain-sokhna', count: 750, icon: 'fa-water' }
];

const categories = [
    { name: 'شقق', slug: 'apartment', icon: 'fa-building', count: 25000 },
    { name: 'فلل', slug: 'villa', icon: 'fa-house-chimney', count: 5400 },
    { name: 'دوبلكس', slug: 'duplex', icon: 'fa-layer-group', count: 3200 },
    { name: 'شاليهات', slug: 'chalet', icon: 'fa-umbrella-beach', count: 2100 },
    { name: 'أراضي', slug: 'land', icon: 'fa-mountain-sun', count: 1800 },
    { name: 'محلات', slug: 'commercial', icon: 'fa-store', count: 1500 }
];

const locations = [
    'القاهرة الجديدة',
    'التجمع الخامس',
    'التجمع الأول',
    'الشيخ زايد',
    'السادس من أكتوبر',
    'العاصمة الإدارية',
    'مدينة نصر',
    'المعادي',
    'الساحل الشمالي',
    'العين السخنة',
    'الجيزة',
    'المهندسين',
    'الزمالك',
    'مصر الجديدة',
    'كمبوند ماونتن فيو',
    'كمبوند بالم هيلز',
    'كمبوند سوديك'
];

// =====================
// DOM Ready
// =====================
document.addEventListener('DOMContentLoaded', function() {
    initMobileMenu();
    initSearchTabs();
    initLocationAutocomplete();
    renderProperties();
    renderCities();
    renderCategories();
    initLeadForm();
});

// =====================
// Mobile Menu
// =====================
function initMobileMenu() {
    const menuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');

    if (!menuBtn || !mobileMenu) return;

    menuBtn.addEventListener('click', () => {
        mobileMenu.classList.toggle('hidden');
        const icon = menuBtn.querySelector('i');
        if (mobileMenu.classList.contains('hidden')) {
            icon.classList.remove('fa-xmark');
            icon.classList.add('fa-bars');
        } else {
            icon.classList.remove('fa-bars');
            icon.classList.add('fa-xmark');
        }
    });

    // Close menu when clicking outside
    document.addEventListener('click', (e) => {
        if (!menuBtn.contains(e.target) && !mobileMenu.contains(e.target)) {
            mobileMenu.classList.add('hidden');
            const icon = menuBtn.querySelector('i');
            icon.classList.remove('fa-xmark');
            icon.classList.add('fa-bars');
        }
    });
}

// =====================
// Search Tabs
// =====================
function initSearchTabs() {
    const tabs = document.querySelectorAll('.search-tab');
    const searchTypeInput = document.getElementById('search-type');

    if (!tabs.length) return;

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            if (searchTypeInput) {
                searchTypeInput.value = tab.dataset.type;
            }
        });
    });
}

// =====================
// Location Autocomplete
// =====================
function initLocationAutocomplete() {
    const input = document.getElementById('location-input');
    const suggestions = document.getElementById('location-suggestions');

    if (!input || !suggestions) return;

    input.addEventListener('input', (e) => {
        const value = e.target.value.trim().toLowerCase();

        if (value.length < 2) {
            suggestions.classList.add('hidden');
            return;
        }

        const matches = locations.filter(loc =>
            loc.toLowerCase().includes(value)
        );

        if (matches.length === 0) {
            suggestions.classList.add('hidden');
            return;
        }

        suggestions.innerHTML = matches.map(match => `
            <div class="px-4 py-3 hover:bg-gray-100 cursor-pointer border-b border-gray-100 last:border-0 flex items-center gap-2" data-value="${match}">
                <i class="fa-solid fa-location-dot text-gray-400"></i>
                <span>${match}</span>
            </div>
        `).join('');

        suggestions.classList.remove('hidden');
    });

    suggestions.addEventListener('click', (e) => {
        const item = e.target.closest('[data-value]');
        if (item) {
            input.value = item.dataset.value;
            suggestions.classList.add('hidden');
        }
    });

    // Close suggestions when clicking outside
    document.addEventListener('click', (e) => {
        if (!input.contains(e.target) && !suggestions.contains(e.target)) {
            suggestions.classList.add('hidden');
        }
    });
}

// =====================
// Render Properties
// =====================
function renderProperties() {
    const grid = document.getElementById('properties-grid');
    if (!grid) return;

    grid.innerHTML = properties.map(prop => createPropertyCard(prop)).join('');
}

function createPropertyCard(prop) {
    const bedsText = prop.beds > 0 ? `${prop.beds} غرف` : '-';
    const bathsText = prop.baths > 0 ? `${prop.baths} حمام` : '-';

    return `
        <article class="bg-white rounded-2xl shadow-md overflow-hidden card-hover border border-gray-100 group flex flex-col h-full">
            <!-- Image -->
            <a href="/pages/property.html?id=${prop.id}" class="relative block h-56 overflow-hidden">
                <img src="${prop.image}" alt="${prop.title}" loading="lazy"
                     class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                <div class="absolute top-4 right-4 flex gap-2">
                    <span class="px-3 py-1 rounded-full text-xs font-bold text-white ${prop.type === 'sale' ? 'bg-green-600' : 'bg-purple-600'}">
                        ${prop.typeLabel}
                    </span>
                    ${prop.featured ? `
                    <span class="bg-yellow-500 px-3 py-1 rounded-full text-xs font-bold text-white flex items-center gap-1">
                        <i class="fa-solid fa-star text-xs"></i> مميز
                    </span>` : ''}
                    ${prop.verified ? `
                    <span class="bg-blue-600 px-3 py-1 rounded-full text-xs font-bold text-white flex items-center gap-1">
                        <i class="fa-solid fa-check text-xs"></i> موثق
                    </span>` : ''}
                </div>
                <button class="absolute bottom-4 left-4 bg-white w-10 h-10 flex items-center justify-center rounded-full text-gray-500 hover:text-red-500 hover:bg-red-50 transition shadow-md" onclick="event.preventDefault(); toggleFavorite(${prop.id}, this);" aria-label="إضافة للمفضلة">
                    <i class="fa-regular fa-heart text-lg"></i>
                </button>
            </a>

            <!-- Content -->
            <div class="p-5 flex-1 flex flex-col">
                <a href="/pages/property.html?id=${prop.id}">
                    <h3 class="text-lg font-bold text-gray-800 mb-2 line-clamp-1 group-hover:text-primary-600 transition">${prop.title}</h3>
                </a>

                <div class="flex items-center text-gray-500 text-sm mb-4">
                    <i class="fa-solid fa-location-dot ml-2 text-primary-500"></i>
                    ${prop.location}
                </div>

                <!-- Specs -->
                <div class="flex justify-between items-center border-t border-b border-gray-100 py-3 mb-4 text-gray-600 text-sm">
                    <div class="flex items-center gap-1" title="غرف النوم">
                        <i class="fa-solid fa-bed text-gray-400"></i>
                        <span>${bedsText}</span>
                    </div>
                    <div class="flex items-center gap-1" title="الحمامات">
                        <i class="fa-solid fa-bath text-gray-400"></i>
                        <span>${bathsText}</span>
                    </div>
                    <div class="flex items-center gap-1" title="المساحة">
                        <i class="fa-solid fa-maximize text-gray-400"></i>
                        <span>${prop.area} م²</span>
                    </div>
                </div>

                <!-- Price & Actions -->
                <div class="mt-auto">
                    <div class="text-xl font-bold text-primary-900 mb-4">
                        ${prop.priceFormatted} <span class="text-sm font-normal text-gray-500">${prop.currency}</span>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <a href="https://wa.me/${prop.agent.whatsapp}?text=مرحباً، أنا مهتم بـ: ${encodeURIComponent(prop.title)}"
                           target="_blank"
                           class="flex items-center justify-center gap-2 bg-green-500 hover:bg-green-600 text-white py-2.5 rounded-xl font-medium transition"
                           onclick="trackEvent('whatsapp_click', {property_id: ${prop.id}})">
                            <i class="fa-brands fa-whatsapp"></i> واتساب
                        </a>
                        <button class="flex items-center justify-center gap-2 border-2 border-primary-600 text-primary-600 hover:bg-primary-50 py-2.5 rounded-xl font-medium transition"
                                onclick="showPhone('${prop.agent.phone}', ${prop.id})">
                            <i class="fa-solid fa-phone"></i> اتصل
                        </button>
                    </div>
                </div>
            </div>
        </article>
    `;
}

// =====================
// Render Cities
// =====================
function renderCities() {
    const grid = document.getElementById('cities-grid');
    if (!grid) return;

    grid.innerHTML = cities.map(city => `
        <a href="/pages/search.html?location=${city.slug}" class="city-card relative group cursor-pointer overflow-hidden rounded-xl h-28 flex flex-col items-center justify-center p-4">
            <i class="fa-solid ${city.icon} text-2xl text-primary-600 group-hover:text-white mb-2 transition"></i>
            <span class="font-bold text-gray-800 group-hover:text-white z-10 transition">${city.name}</span>
            <span class="text-sm text-gray-500 group-hover:text-blue-100 transition">${city.count.toLocaleString()} عقار</span>
        </a>
    `).join('');
}

// =====================
// Render Categories
// =====================
function renderCategories() {
    const grid = document.getElementById('categories-grid');
    if (!grid) return;

    grid.innerHTML = categories.map(cat => `
        <a href="/pages/search.html?property_type=${cat.slug}" class="bg-gray-50 hover:bg-primary-50 border-2 border-gray-100 hover:border-primary-200 rounded-xl p-4 text-center transition group">
            <div class="w-12 h-12 bg-primary-100 group-hover:bg-primary-200 rounded-xl flex items-center justify-center mx-auto mb-3 transition">
                <i class="fa-solid ${cat.icon} text-xl text-primary-600"></i>
            </div>
            <h3 class="font-bold text-gray-800 group-hover:text-primary-700 transition">${cat.name}</h3>
            <p class="text-sm text-gray-500">${cat.count.toLocaleString()} عقار</p>
        </a>
    `).join('');
}

// =====================
// Lead Form
// =====================
function initLeadForm() {
    const form = document.getElementById('lead-form');
    if (!form) return;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = new FormData(form);
        const data = Object.fromEntries(formData);

        // Validate phone
        if (!/^01[0-9]{9}$/.test(data.phone)) {
            showNotification('يرجى إدخال رقم هاتف صحيح', 'error');
            return;
        }

        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> جاري الإرسال...';
        submitBtn.disabled = true;

        try {
            // Simulate API call (replace with actual API)
            await new Promise(resolve => setTimeout(resolve, 1500));

            // Track conversion
            trackEvent('lead_submit', data);

            // Meta Pixel Lead Event
            if (typeof fbq !== 'undefined') {
                fbq('track', 'Lead', {
                    content_name: 'property_request',
                    content_category: data.purpose
                });
            }

            showNotification('تم استلام طلبك بنجاح! سنتواصل معك قريباً', 'success');
            form.reset();

        } catch (error) {
            showNotification('حدث خطأ، يرجى المحاولة مرة أخرى', 'error');
        } finally {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    });
}

// =====================
// Utilities
// =====================

// Toggle Favorite
function toggleFavorite(propertyId, btn) {
    const icon = btn.querySelector('i');
    const isFavorite = icon.classList.contains('fa-solid');

    if (isFavorite) {
        icon.classList.remove('fa-solid', 'text-red-500');
        icon.classList.add('fa-regular');
        btn.classList.remove('bg-red-50');
        showNotification('تم إزالة العقار من المفضلة');
    } else {
        icon.classList.remove('fa-regular');
        icon.classList.add('fa-solid', 'text-red-500');
        btn.classList.add('bg-red-50');
        showNotification('تم إضافة العقار للمفضلة');
        trackEvent('add_to_favorites', { property_id: propertyId });
    }

    // Save to localStorage
    let favorites = JSON.parse(localStorage.getItem('favorites') || '[]');
    if (isFavorite) {
        favorites = favorites.filter(id => id !== propertyId);
    } else {
        favorites.push(propertyId);
    }
    localStorage.setItem('favorites', JSON.stringify(favorites));
}

// Show Phone
function showPhone(phone, propertyId) {
    trackEvent('show_phone', { property_id: propertyId });

    // Create modal
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4';
    modal.innerHTML = `
        <div class="bg-white rounded-2xl p-6 max-w-sm w-full text-center">
            <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-phone text-2xl text-primary-600"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">رقم الهاتف</h3>
            <a href="tel:${phone}" class="text-2xl font-bold text-primary-600 block mb-4 hover:underline" dir="ltr">${phone}</a>
            <button onclick="this.closest('.fixed').remove()" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3 rounded-xl transition">
                إغلاق
            </button>
        </div>
    `;

    modal.addEventListener('click', (e) => {
        if (e.target === modal) modal.remove();
    });

    document.body.appendChild(modal);
}

// Show Notification
function showNotification(message, type = 'info') {
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        info: 'bg-primary-600'
    };

    const notification = document.createElement('div');
    notification.className = `fixed top-4 left-1/2 -translate-x-1/2 ${colors[type]} text-white px-6 py-3 rounded-xl shadow-lg z-50 animate-fade-in`;
    notification.innerHTML = `
        <div class="flex items-center gap-2">
            <i class="fa-solid ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'}"></i>
            <span>${message}</span>
        </div>
    `;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translate(-50%, -20px)';
        notification.style.transition = 'all 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Track Event (for Analytics & Ads)
function trackEvent(eventName, params = {}) {
    // Google Analytics
    if (typeof gtag !== 'undefined') {
        gtag('event', eventName, params);
    }

    // Meta Pixel
    if (typeof fbq !== 'undefined') {
        fbq('trackCustom', eventName, params);
    }

    // Console log for debugging
    console.log('Event tracked:', eventName, params);
}

// Format price
function formatPrice(price) {
    return new Intl.NumberFormat('ar-EG').format(price);
}

// Export for use in other files
window.BeTaj = {
    properties,
    cities,
    categories,
    locations,
    trackEvent,
    showNotification,
    formatPrice
};
