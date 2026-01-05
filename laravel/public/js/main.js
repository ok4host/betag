/**
 * BeTaj Real Estate - Main JavaScript
 */

// Toggle Favorite
function toggleFavorite(propertyId) {
    const locale = document.documentElement.lang || 'ar';

    fetch(`/${locale}/favorites/${propertyId}/toggle`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const btn = event.target.closest('.btn-favorite');
            if (btn) {
                btn.classList.toggle('active', data.isFavorited);
            }
        }
    })
    .catch(error => console.error('Error:', error));
}

// Theme Toggle
function toggleTheme() {
    const body = document.body;
    const currentTheme = body.getAttribute('data-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

    body.setAttribute('data-theme', newTheme);
    localStorage.setItem('theme', newTheme);

    // Update server session (optional)
    fetch('/theme/' + newTheme);
}

// Initialize theme on page load
document.addEventListener('DOMContentLoaded', function() {
    // Load saved theme
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.body.setAttribute('data-theme', savedTheme);

    // Initialize tooltips
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

    // Initialize popovers
    const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
    const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href !== '#') {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });

    // Lazy load images
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    observer.unobserve(img);
                }
            });
        });

        document.querySelectorAll('img.lazy').forEach(img => {
            imageObserver.observe(img);
        });
    }
});

// Format price
function formatPrice(price, locale = 'ar') {
    const formatted = new Intl.NumberFormat(locale === 'ar' ? 'ar-EG' : 'en-EG').format(price);
    const currency = locale === 'ar' ? 'ج.م' : 'EGP';
    return `${formatted} ${currency}`;
}

// Share property
function shareProperty() {
    if (navigator.share) {
        navigator.share({
            title: document.title,
            url: window.location.href
        }).catch(console.error);
    } else {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(window.location.href)
            .then(() => {
                alert(document.documentElement.lang === 'ar' ? 'تم نسخ الرابط!' : 'Link copied!');
            })
            .catch(console.error);
    }
}

// Form validation
function validateForm(form) {
    let isValid = true;

    form.querySelectorAll('[required]').forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });

    // Email validation
    const emailField = form.querySelector('input[type="email"]');
    if (emailField && emailField.value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(emailField.value)) {
            emailField.classList.add('is-invalid');
            isValid = false;
        }
    }

    // Phone validation
    const phoneField = form.querySelector('input[type="tel"]');
    if (phoneField && phoneField.value) {
        const phoneRegex = /^[\d\s\-\+\(\)]{8,}$/;
        if (!phoneRegex.test(phoneField.value)) {
            phoneField.classList.add('is-invalid');
            isValid = false;
        }
    }

    return isValid;
}

// Auto-hide alerts
document.querySelectorAll('.alert:not(.alert-permanent)').forEach(alert => {
    setTimeout(() => {
        alert.classList.add('fade');
        setTimeout(() => alert.remove(), 150);
    }, 5000);
});

// Price range slider (if exists)
const priceRange = document.getElementById('priceRange');
if (priceRange) {
    priceRange.addEventListener('input', function() {
        document.getElementById('priceValue').textContent = formatPrice(this.value);
    });
}

// Console welcome message
console.log('%c🏠 BeTaj Real Estate', 'font-size: 24px; font-weight: bold; color: #1a5f7a;');
console.log('%cYour trusted real estate platform in Egypt', 'font-size: 14px; color: #666;');
