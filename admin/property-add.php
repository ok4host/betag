<?php
/**
 * Admin - Add/Edit Property
 */
require_once __DIR__ . '/../includes/functions.php';
startSession();
requireAdmin();

$message = '';
$error = '';
$property = null;

// Check if editing
$editId = isset($_GET['id']) ? (int)$_GET['id'] : null;
if ($editId) {
    $stmt = db()->prepare("SELECT * FROM properties WHERE id = ?");
    $stmt->execute([$editId]);
    $property = $stmt->fetch();
    if (!$property) {
        redirect('properties.php');
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'title' => sanitize($_POST['title']),
        'description' => $_POST['description'], // Allow HTML
        'transaction_type' => $_POST['transaction_type'],
        'category_id' => (int)$_POST['category_id'],
        'location_id' => (int)$_POST['location_id'],
        'price' => (float)$_POST['price'],
        'currency' => $_POST['currency'] ?? 'EGP',
        'area' => (float)($_POST['area'] ?? 0),
        'bedrooms' => (int)($_POST['bedrooms'] ?? 0),
        'bathrooms' => (int)($_POST['bathrooms'] ?? 0),
        'floor' => (int)($_POST['floor'] ?? 0),
        'total_floors' => (int)($_POST['total_floors'] ?? 0),
        'furnishing' => $_POST['furnishing'] ?? 'unfurnished',
        'address' => sanitize($_POST['address']),
        'latitude' => !empty($_POST['latitude']) ? floatval($_POST['latitude']) : null,
        'longitude' => !empty($_POST['longitude']) ? floatval($_POST['longitude']) : null,
        'features' => isset($_POST['features']) ? json_encode($_POST['features']) : null,
        'status' => $_POST['status'] ?? 'active',
        'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
        'meta_title' => sanitize($_POST['meta_title']),
        'meta_description' => sanitize($_POST['meta_description']),
    ];

    // Validate
    if (empty($data['title']) || empty($data['category_id']) || empty($data['location_id'])) {
        $error = 'الرجاء ملء جميع الحقول المطلوبة';
    } else {
        // Handle image upload
        $featuredImage = $property['featured_image'] ?? null;
        if (!empty($_FILES['featured_image']['name'])) {
            $uploadResult = uploadImage($_FILES['featured_image'], 'properties');
            if ($uploadResult['success']) {
                $featuredImage = $uploadResult['filename'];
            } else {
                $error = $uploadResult['error'];
            }
        }

        if (!$error) {
            if ($editId) {
                // Update
                $slug = uniqueSlug('properties', $data['title'], $editId);
                $sql = "UPDATE properties SET
                        title = ?, slug = ?, description = ?, transaction_type = ?,
                        category_id = ?, location_id = ?, price = ?, currency = ?,
                        area = ?, bedrooms = ?, bathrooms = ?, floor = ?, total_floors = ?,
                        furnishing = ?, address = ?, latitude = ?, longitude = ?,
                        features = ?, status = ?, is_featured = ?, featured_image = ?,
                        meta_title = ?, meta_description = ?, updated_at = NOW()
                        WHERE id = ?";

                $stmt = db()->prepare($sql);
                $stmt->execute([
                    $data['title'], $slug, $data['description'], $data['transaction_type'],
                    $data['category_id'], $data['location_id'], $data['price'], $data['currency'],
                    $data['area'], $data['bedrooms'], $data['bathrooms'], $data['floor'], $data['total_floors'],
                    $data['furnishing'], $data['address'], $data['latitude'], $data['longitude'],
                    $data['features'], $data['status'], $data['is_featured'], $featuredImage,
                    $data['meta_title'], $data['meta_description'], $editId
                ]);
                $message = 'تم تحديث العقار بنجاح';
            } else {
                // Insert
                $slug = uniqueSlug('properties', $data['title']);
                $sql = "INSERT INTO properties
                        (user_id, title, slug, description, transaction_type, category_id, location_id,
                         price, currency, area, bedrooms, bathrooms, floor, total_floors, furnishing,
                         address, latitude, longitude, features, status, is_featured, featured_image,
                         meta_title, meta_description, created_at, updated_at)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";

                $stmt = db()->prepare($sql);
                $stmt->execute([
                    $_SESSION['user_id'], $data['title'], $slug, $data['description'], $data['transaction_type'],
                    $data['category_id'], $data['location_id'], $data['price'], $data['currency'],
                    $data['area'], $data['bedrooms'], $data['bathrooms'], $data['floor'], $data['total_floors'],
                    $data['furnishing'], $data['address'], $data['latitude'], $data['longitude'],
                    $data['features'], $data['status'], $data['is_featured'], $featuredImage,
                    $data['meta_title'], $data['meta_description']
                ]);
                $newId = db()->lastInsertId();
                $message = 'تم إضافة العقار بنجاح';
                redirect('property-add.php?id=' . $newId . '&saved=1');
            }
        }
    }
}

// Handle gallery upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['gallery_images'])) {
    $propId = $_POST['property_id'] ?? $editId;
    if ($propId) {
        foreach ($_FILES['gallery_images']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['gallery_images']['error'][$key] === UPLOAD_ERR_OK) {
                $file = [
                    'name' => $_FILES['gallery_images']['name'][$key],
                    'tmp_name' => $tmp_name,
                    'error' => $_FILES['gallery_images']['error'][$key],
                    'size' => $_FILES['gallery_images']['size'][$key]
                ];
                $result = uploadImage($file, 'properties');
                if ($result['success']) {
                    $stmt = db()->prepare("INSERT INTO property_images (property_id, image_url, sort_order) VALUES (?, ?, ?)");
                    $stmt->execute([$propId, $result['path'], 0]);
                }
            }
        }
    }
}

// Get categories and locations
$categories = db()->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY sort_order")->fetchAll();
$locations = db()->query("SELECT * FROM locations WHERE is_active = 1 ORDER BY type, name_ar")->fetchAll();

// Get gallery images if editing
$galleryImages = [];
if ($editId) {
    $galleryImages = db()->prepare("SELECT * FROM property_images WHERE property_id = ? ORDER BY sort_order");
    $galleryImages->execute([$editId]);
    $galleryImages = $galleryImages->fetchAll();
}

if (isset($_GET['saved'])) {
    $message = 'تم حفظ العقار بنجاح';
}

$pageTitle = $editId ? 'تعديل عقار' : 'إضافة عقار جديد';
include __DIR__ . '/includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">
            <i class="fas fa-<?= $editId ? 'edit' : 'plus' ?> me-2"></i>
            <?= $editId ? 'تعديل عقار' : 'إضافة عقار جديد' ?>
        </h1>
        <a href="properties.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-right me-1"></i>العودة للقائمة
        </a>
    </div>

    <?php if ($message): ?>
    <div class="alert alert-success"><?= $message ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="row">
            <!-- Main Info -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">المعلومات الأساسية</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">عنوان العقار *</label>
                            <input type="text" name="title" class="form-control" required
                                   value="<?= htmlspecialchars($property['title'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">الوصف</label>
                            <textarea name="description" class="form-control" rows="6"><?= htmlspecialchars($property['description'] ?? '') ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">نوع المعاملة *</label>
                                <select name="transaction_type" class="form-select" required>
                                    <option value="sale" <?= ($property['transaction_type'] ?? '') === 'sale' ? 'selected' : '' ?>>للبيع</option>
                                    <option value="rent" <?= ($property['transaction_type'] ?? '') === 'rent' ? 'selected' : '' ?>>للإيجار</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">الفئة *</label>
                                <select name="category_id" class="form-select" required>
                                    <option value="">اختر الفئة</option>
                                    <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= ($property['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['name_ar']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">الموقع *</label>
                                <select name="location_id" class="form-select" required>
                                    <option value="">اختر الموقع</option>
                                    <?php
                                    $currentType = '';
                                    foreach ($locations as $loc):
                                        if ($loc['type'] !== $currentType):
                                            if ($currentType) echo '</optgroup>';
                                            $types = ['governorate' => 'المحافظات', 'city' => 'المدن', 'area' => 'المناطق', 'compound' => 'الكومباوندات'];
                                            echo '<optgroup label="' . ($types[$loc['type']] ?? $loc['type']) . '">';
                                            $currentType = $loc['type'];
                                        endif;
                                    ?>
                                    <option value="<?= $loc['id'] ?>" <?= ($property['location_id'] ?? '') == $loc['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($loc['name_ar']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                    </optgroup>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">العنوان التفصيلي</label>
                                <input type="text" name="address" class="form-control"
                                       value="<?= htmlspecialchars($property['address'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Details -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">التفاصيل</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">السعر *</label>
                                <input type="number" name="price" class="form-control" required
                                       value="<?= $property['price'] ?? '' ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">العملة</label>
                                <select name="currency" class="form-select">
                                    <option value="EGP" <?= ($property['currency'] ?? 'EGP') === 'EGP' ? 'selected' : '' ?>>جنيه مصري</option>
                                    <option value="USD" <?= ($property['currency'] ?? '') === 'USD' ? 'selected' : '' ?>>دولار</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">المساحة (م²)</label>
                                <input type="number" name="area" class="form-control"
                                       value="<?= $property['area'] ?? '' ?>">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">غرف النوم</label>
                                <input type="number" name="bedrooms" class="form-control" min="0"
                                       value="<?= $property['bedrooms'] ?? 0 ?>">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">الحمامات</label>
                                <input type="number" name="bathrooms" class="form-control" min="0"
                                       value="<?= $property['bathrooms'] ?? 0 ?>">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">الطابق</label>
                                <input type="number" name="floor" class="form-control" min="0"
                                       value="<?= $property['floor'] ?? 0 ?>">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">إجمالي الطوابق</label>
                                <input type="number" name="total_floors" class="form-control" min="0"
                                       value="<?= $property['total_floors'] ?? 0 ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">التشطيب</label>
                            <select name="furnishing" class="form-select">
                                <option value="unfurnished" <?= ($property['furnishing'] ?? '') === 'unfurnished' ? 'selected' : '' ?>>بدون تشطيب</option>
                                <option value="semi" <?= ($property['furnishing'] ?? '') === 'semi' ? 'selected' : '' ?>>نصف تشطيب</option>
                                <option value="full" <?= ($property['furnishing'] ?? '') === 'full' ? 'selected' : '' ?>>تشطيب كامل</option>
                                <option value="furnished" <?= ($property['furnishing'] ?? '') === 'furnished' ? 'selected' : '' ?>>مفروش</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">المميزات</label>
                            <div class="row">
                                <?php
                                $allFeatures = ['parking' => 'موقف سيارات', 'garden' => 'حديقة', 'pool' => 'حمام سباحة',
                                               'security' => 'أمن', 'elevator' => 'مصعد', 'ac' => 'تكييف',
                                               'gym' => 'جيم', 'balcony' => 'بلكونة', 'view' => 'إطلالة مميزة'];
                                $propFeatures = $property['features'] ? json_decode($property['features'], true) : [];
                                foreach ($allFeatures as $key => $label):
                                ?>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="features[]"
                                               value="<?= $key ?>" id="feat_<?= $key ?>"
                                               <?= in_array($key, $propFeatures ?? []) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="feat_<?= $key ?>"><?= $label ?></label>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Location -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">الموقع على الخريطة</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">خط العرض (Latitude)</label>
                                <input type="text" name="latitude" class="form-control"
                                       value="<?= $property['latitude'] ?? '' ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">خط الطول (Longitude)</label>
                                <input type="text" name="longitude" class="form-control"
                                       value="<?= $property['longitude'] ?? '' ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SEO -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">إعدادات SEO</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">عنوان الصفحة (Meta Title)</label>
                            <input type="text" name="meta_title" class="form-control"
                                   value="<?= htmlspecialchars($property['meta_title'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">وصف الصفحة (Meta Description)</label>
                            <textarea name="meta_description" class="form-control" rows="2"><?= htmlspecialchars($property['meta_description'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Status -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">الحالة</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">حالة العقار</label>
                            <select name="status" class="form-select">
                                <option value="active" <?= ($property['status'] ?? '') === 'active' ? 'selected' : '' ?>>نشط</option>
                                <option value="pending" <?= ($property['status'] ?? '') === 'pending' ? 'selected' : '' ?>>معلق</option>
                                <option value="sold" <?= ($property['status'] ?? '') === 'sold' ? 'selected' : '' ?>>مباع</option>
                                <option value="rejected" <?= ($property['status'] ?? '') === 'rejected' ? 'selected' : '' ?>>مرفوض</option>
                            </select>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured"
                                   <?= ($property['is_featured'] ?? 0) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_featured">
                                <i class="fas fa-star text-warning me-1"></i>عقار مميز
                            </label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-1"></i><?= $editId ? 'حفظ التعديلات' : 'إضافة العقار' ?>
                        </button>
                    </div>
                </div>

                <!-- Featured Image -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">الصورة الرئيسية</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($property['featured_image'])): ?>
                        <img src="/uploads/properties/<?= $property['featured_image'] ?>" class="img-fluid rounded mb-3" alt="">
                        <?php endif; ?>
                        <input type="file" name="featured_image" class="form-control" accept="image/*">
                    </div>
                </div>

                <!-- Gallery -->
                <?php if ($editId): ?>
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">معرض الصور</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-2 mb-3">
                            <?php foreach ($galleryImages as $img): ?>
                            <div class="col-4">
                                <div class="position-relative">
                                    <img src="<?= $img['image_url'] ?>" class="img-fluid rounded" alt="">
                                    <a href="?id=<?= $editId ?>&delete_image=<?= $img['id'] ?>"
                                       class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1"
                                       onclick="return confirm('حذف هذه الصورة؟')">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <input type="file" name="gallery_images[]" class="form-control" accept="image/*" multiple>
                        <small class="text-muted">يمكنك اختيار عدة صور</small>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
