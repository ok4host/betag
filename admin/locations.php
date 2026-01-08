<?php
/**
 * Admin - Locations Management (المناطق والمدن والكومباوندات)
 */
require_once __DIR__ . '/../includes/functions.php';
startSession();
requireAdmin();

$message = '';
$error = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $name_ar = sanitize($_POST['name_ar']);
        $name_en = sanitize($_POST['name_en']);
        $type = $_POST['type'];
        $parent_id = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
        $description = sanitize($_POST['description']);
        $latitude = !empty($_POST['latitude']) ? floatval($_POST['latitude']) : null;
        $longitude = !empty($_POST['longitude']) ? floatval($_POST['longitude']) : null;

        if ($name_ar) {
            $slug = uniqueSlug('locations', $name_en ?: $name_ar);

            $stmt = db()->prepare("INSERT INTO locations (name_ar, name_en, slug, type, parent_id, description, latitude, longitude, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)");
            if ($stmt->execute([$name_ar, $name_en, $slug, $type, $parent_id, $description, $latitude, $longitude])) {
                $message = 'تم إضافة الموقع بنجاح';
            } else {
                $error = 'حدث خطأ أثناء الإضافة';
            }
        }
    } elseif ($action === 'edit' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        $name_ar = sanitize($_POST['name_ar']);
        $name_en = sanitize($_POST['name_en']);
        $type = $_POST['type'];
        $parent_id = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
        $description = sanitize($_POST['description']);
        $latitude = !empty($_POST['latitude']) ? floatval($_POST['latitude']) : null;
        $longitude = !empty($_POST['longitude']) ? floatval($_POST['longitude']) : null;

        $slug = uniqueSlug('locations', $name_en ?: $name_ar, $id);

        $stmt = db()->prepare("UPDATE locations SET name_ar = ?, name_en = ?, slug = ?, type = ?, parent_id = ?, description = ?, latitude = ?, longitude = ? WHERE id = ?");
        $stmt->execute([$name_ar, $name_en, $slug, $type, $parent_id, $description, $latitude, $longitude, $id]);
        $message = 'تم تحديث الموقع بنجاح';

    } elseif ($action === 'delete' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        // Check if has children
        $hasChildren = db()->prepare("SELECT COUNT(*) FROM locations WHERE parent_id = ?");
        $hasChildren->execute([$id]);

        if ($hasChildren->fetchColumn() > 0) {
            $error = 'لا يمكن حذف هذا الموقع لأنه يحتوي على مواقع فرعية';
        } else {
            // Check if has properties
            $hasProperties = db()->prepare("SELECT COUNT(*) FROM properties WHERE location_id = ?");
            $hasProperties->execute([$id]);

            if ($hasProperties->fetchColumn() > 0) {
                $error = 'لا يمكن حذف هذا الموقع لأنه مرتبط بعقارات';
            } else {
                $stmt = db()->prepare("DELETE FROM locations WHERE id = ?");
                $stmt->execute([$id]);
                $message = 'تم حذف الموقع';
            }
        }
    } elseif ($action === 'toggle' && isset($_POST['id'])) {
        $stmt = db()->prepare("UPDATE locations SET is_active = NOT is_active WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        $message = 'تم تحديث الحالة';
    }
}

// Get all locations
$locations = db()->query("
    SELECT l.*, p.name_ar as parent_name,
           (SELECT COUNT(*) FROM properties WHERE location_id = l.id) as property_count,
           (SELECT COUNT(*) FROM locations WHERE parent_id = l.id) as children_count
    FROM locations l
    LEFT JOIN locations p ON l.parent_id = p.id
    ORDER BY l.type, l.name_ar
")->fetchAll();

// Get parent locations for dropdown
$parentLocations = db()->query("SELECT id, name_ar, type FROM locations WHERE type IN ('governorate', 'city') ORDER BY type, name_ar")->fetchAll();

$pageTitle = 'إدارة المناطق';
include __DIR__ . '/includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3"><i class="fas fa-map-marker-alt me-2"></i>إدارة المناطق والكومباوندات</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="fas fa-plus me-1"></i>إضافة موقع
        </button>
    </div>

    <?php if ($message): ?>
    <div class="alert alert-success"><?= $message ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <!-- Tabs for different types -->
    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#all">الكل (<?= count($locations) ?>)</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#governorates">المحافظات</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#cities">المدن</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#areas">الأحياء</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#compounds">الكومباوندات</a>
        </li>
    </ul>

    <div class="card">
        <div class="card-body">
            <table class="table table-hover datatable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الاسم بالعربي</th>
                        <th>الاسم بالإنجليزي</th>
                        <th>النوع</th>
                        <th>التابع لـ</th>
                        <th>العقارات</th>
                        <th>الحالة</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($locations as $loc): ?>
                    <tr data-type="<?= $loc['type'] ?>">
                        <td><?= $loc['id'] ?></td>
                        <td>
                            <strong><?= htmlspecialchars($loc['name_ar']) ?></strong>
                            <?php if ($loc['children_count'] > 0): ?>
                            <br><small class="text-muted"><?= $loc['children_count'] ?> موقع فرعي</small>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($loc['name_en'] ?: '-') ?></td>
                        <td>
                            <?php
                            $types = [
                                'governorate' => ['محافظة', 'danger'],
                                'city' => ['مدينة', 'primary'],
                                'area' => ['حي/منطقة', 'info'],
                                'compound' => ['كومباوند', 'success']
                            ];
                            $type = $types[$loc['type']] ?? ['غير محدد', 'secondary'];
                            ?>
                            <span class="badge bg-<?= $type[1] ?>"><?= $type[0] ?></span>
                        </td>
                        <td><?= $loc['parent_name'] ?: '-' ?></td>
                        <td>
                            <span class="badge bg-secondary"><?= $loc['property_count'] ?></span>
                        </td>
                        <td>
                            <span class="badge bg-<?= $loc['is_active'] ? 'success' : 'secondary' ?>">
                                <?= $loc['is_active'] ? 'نشط' : 'معطل' ?>
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" onclick="editLocation(<?= htmlspecialchars(json_encode($loc)) ?>)">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="action" value="toggle">
                                <input type="hidden" name="id" value="<?= $loc['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-warning">
                                    <i class="fas fa-toggle-<?= $loc['is_active'] ? 'on' : 'off' ?>"></i>
                                </button>
                            </form>
                            <?php if ($loc['property_count'] == 0 && $loc['children_count'] == 0): ?>
                            <form method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $loc['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div class="modal-header">
                    <h5 class="modal-title">إضافة موقع جديد</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">الاسم بالعربي *</label>
                            <input type="text" name="name_ar" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">الاسم بالإنجليزي</label>
                            <input type="text" name="name_en" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">النوع *</label>
                            <select name="type" class="form-select" required onchange="toggleParent(this)">
                                <option value="governorate">محافظة</option>
                                <option value="city">مدينة</option>
                                <option value="area" selected>حي/منطقة</option>
                                <option value="compound">كومباوند</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3" id="parentDiv">
                            <label class="form-label">التابع لـ</label>
                            <select name="parent_id" class="form-select">
                                <option value="">-- اختر --</option>
                                <?php foreach ($parentLocations as $parent): ?>
                                <option value="<?= $parent['id'] ?>" data-type="<?= $parent['type'] ?>">
                                    <?= $parent['name_ar'] ?> (<?= $parent['type'] === 'governorate' ? 'محافظة' : 'مدينة' ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الوصف</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">خط العرض (Latitude)</label>
                            <input type="text" name="latitude" class="form-control" placeholder="30.0444">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">خط الطول (Longitude)</label>
                            <input type="text" name="longitude" class="form-control" placeholder="31.2357">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">إضافة</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-header">
                    <h5 class="modal-title">تعديل الموقع</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">الاسم بالعربي *</label>
                            <input type="text" name="name_ar" id="edit_name_ar" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">الاسم بالإنجليزي</label>
                            <input type="text" name="name_en" id="edit_name_en" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">النوع *</label>
                            <select name="type" id="edit_type" class="form-select" required>
                                <option value="governorate">محافظة</option>
                                <option value="city">مدينة</option>
                                <option value="area">حي/منطقة</option>
                                <option value="compound">كومباوند</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">التابع لـ</label>
                            <select name="parent_id" id="edit_parent_id" class="form-select">
                                <option value="">-- اختر --</option>
                                <?php foreach ($parentLocations as $parent): ?>
                                <option value="<?= $parent['id'] ?>">
                                    <?= $parent['name_ar'] ?> (<?= $parent['type'] === 'governorate' ? 'محافظة' : 'مدينة' ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الوصف</label>
                        <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">خط العرض (Latitude)</label>
                            <input type="text" name="latitude" id="edit_latitude" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">خط الطول (Longitude)</label>
                            <input type="text" name="longitude" id="edit_longitude" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editLocation(loc) {
    document.getElementById('edit_id').value = loc.id;
    document.getElementById('edit_name_ar').value = loc.name_ar;
    document.getElementById('edit_name_en').value = loc.name_en || '';
    document.getElementById('edit_type').value = loc.type;
    document.getElementById('edit_parent_id').value = loc.parent_id || '';
    document.getElementById('edit_description').value = loc.description || '';
    document.getElementById('edit_latitude').value = loc.latitude || '';
    document.getElementById('edit_longitude').value = loc.longitude || '';

    new bootstrap.Modal(document.getElementById('editModal')).show();
}

function toggleParent(select) {
    const parentDiv = document.getElementById('parentDiv');
    if (select.value === 'governorate') {
        parentDiv.style.display = 'none';
    } else {
        parentDiv.style.display = 'block';
    }
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
