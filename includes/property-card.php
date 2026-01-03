<?php
/**
 * Property Card Component
 * Expects $property array to be passed
 */
$images = json_decode($property['images'] ?? '[]', true);
$mainImage = $property['featured_image'] ?? ($images[0] ?? 'https://via.placeholder.com/400x300?text=No+Image');
$propertyUrl = '/property/' . $property['slug'];
$isFavorite = false;

if (isLoggedIn()) {
    $favStmt = Database::getInstance()->getConnection()->prepare(
        "SELECT id FROM favorites WHERE user_id = ? AND property_id = ?"
    );
    $favStmt->execute([$_SESSION['user_id'], $property['id']]);
    $isFavorite = (bool)$favStmt->fetch();
}
?>
<div class="bg-white rounded-2xl shadow-lg overflow-hidden card-hover group" data-property-id="<?= $property['id'] ?>">
    <!-- Image -->
    <div class="relative h-52 overflow-hidden">
        <a href="<?= $propertyUrl ?>">
            <img src="<?= htmlspecialchars($mainImage) ?>" alt="<?= htmlspecialchars($property['title']) ?>" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
        </a>

        <!-- Badges -->
        <div class="absolute top-3 right-3 flex flex-col gap-2">
            <span class="bg-<?= $property['transaction_type'] === 'sale' ? 'green' : 'blue' ?>-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                <?= $property['transaction_type'] === 'sale' ? 'للبيع' : 'للإيجار' ?>
            </span>
            <?php if ($property['is_featured']): ?>
            <span class="bg-yellow-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                <i class="fa-solid fa-star ml-1"></i>مميز
            </span>
            <?php endif; ?>
        </div>

        <!-- Favorite Button -->
        <button onclick="toggleFavorite(<?= $property['id'] ?>, this)" class="absolute top-3 left-3 w-10 h-10 bg-white/90 rounded-full flex items-center justify-center hover:bg-white transition favorite-btn">
            <i class="fa-<?= $isFavorite ? 'solid' : 'regular' ?> fa-heart text-<?= $isFavorite ? 'red' : 'gray' ?>-500 text-lg"></i>
        </button>

        <!-- Category -->
        <div class="absolute bottom-3 right-3 bg-white/90 backdrop-blur px-3 py-1 rounded-full text-sm">
            <i class="fa-solid <?= $property['category_icon'] ?? 'fa-building' ?> text-primary-600 ml-1"></i>
            <?= htmlspecialchars($property['category_name'] ?? 'عقار') ?>
        </div>
    </div>

    <!-- Content -->
    <div class="p-5">
        <!-- Price -->
        <div class="text-2xl font-bold text-primary-600 mb-2">
            <?= number_format($property['price']) ?>
            <span class="text-sm font-normal text-gray-500">
                جنيه
                <?php if ($property['transaction_type'] === 'rent'): ?>
                / <?= $property['rent_period'] === 'yearly' ? 'سنوياً' : 'شهرياً' ?>
                <?php endif; ?>
            </span>
        </div>

        <!-- Title -->
        <h3 class="font-bold text-gray-800 mb-2 line-clamp-2">
            <a href="<?= $propertyUrl ?>" class="hover:text-primary-600 transition">
                <?= htmlspecialchars($property['title']) ?>
            </a>
        </h3>

        <!-- Location -->
        <p class="text-gray-500 text-sm mb-4">
            <i class="fa-solid fa-location-dot ml-1 text-primary-600"></i>
            <?= htmlspecialchars($property['location_name'] ?? $property['address'] ?? 'غير محدد') ?>
        </p>

        <!-- Specs -->
        <div class="flex items-center gap-4 text-gray-600 text-sm border-t pt-4">
            <div class="flex items-center gap-1">
                <i class="fa-solid fa-ruler-combined text-primary-600"></i>
                <span><?= number_format($property['area']) ?> م²</span>
            </div>
            <?php if ($property['bedrooms']): ?>
            <div class="flex items-center gap-1">
                <i class="fa-solid fa-bed text-primary-600"></i>
                <span><?= $property['bedrooms'] ?> غرف</span>
            </div>
            <?php endif; ?>
            <?php if ($property['bathrooms']): ?>
            <div class="flex items-center gap-1">
                <i class="fa-solid fa-bath text-primary-600"></i>
                <span><?= $property['bathrooms'] ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function toggleFavorite(propertyId, btn) {
    fetch('/api/favorites.php?action=toggle', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ property_id: propertyId })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const icon = btn.querySelector('i');
            if (data.is_favorite) {
                icon.classList.remove('fa-regular', 'text-gray-500');
                icon.classList.add('fa-solid', 'text-red-500');
            } else {
                icon.classList.remove('fa-solid', 'text-red-500');
                icon.classList.add('fa-regular', 'text-gray-500');
            }
            showToast(data.message, 'success');

            // Update count
            const countBadge = document.getElementById('favorites-count');
            if (countBadge) {
                countBadge.textContent = data.count;
                countBadge.classList.toggle('hidden', data.count === 0);
            }
            localStorage.setItem('favorites_count', data.count);
        } else {
            if (data.error.includes('تسجيل الدخول')) {
                window.location.href = '/login?redirect=' + encodeURIComponent(window.location.pathname);
            } else {
                showToast(data.error, 'error');
            }
        }
    })
    .catch(() => showToast('حدث خطأ', 'error'));
}
</script>
