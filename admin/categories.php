<?php
/**
 * Admin - Categories Management (إدارة فئات العقارات)
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
        $icon = sanitize($_POST['icon']);
        $description = sanitize($_POST['description']);
        $sort_order = (int)$_POST['sort_order'];

        if ($name_ar) {
            $slug = uniqueSlug('categories', $name_en ?: $name_ar);

            $stmt = db()->prepare("INSERT INTO categories (name_ar, name_en, slug, icon, description, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?, 1)");
            if ($stmt->execute([$name_ar, $name_en, $slug, $icon, $description, $sort_order])) {
                $message = 'تم إضافة الفئة بنجاح';
            } else {
                $error = 'حدث خطأ أثناء الإضافة';
            }
        }
    } elseif ($action === 'edit' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        $name_ar = sanitize($_POST['name_ar']);
        $name_en = sanitize($_POST['name_en']);
        $icon = sanitize($_POST['icon']);
        $description = sanitize($_POST['description']);
        $sort_order = (int)$_POST['sort_order'];

        $slug = uniqueSlug('categories', $name_en ?: $name_ar, $id);

        $stmt = db()->prepare("UPDATE categories SET name_ar = ?, name_en = ?, slug = ?, icon = ?, description = ?, sort_order = ? WHERE id = ?");
        $stmt->execute([$name_ar, $name_en, $slug, $icon, $description, $sort_order, $id]);
        $message = 'تم تحديث الفئة بنجاح';

    } elseif ($action === 'delete' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        // Check if has properties
        $hasProperties = db()->prepare("SELECT COUNT(*) FROM properties WHERE category_id = ?");
        $hasProperties->execute([$id]);

        if ($hasProperties->fetchColumn() > 0) {
            $error = 'لا يمكن حذف هذه الفئة لأنها مرتبطة بعقارات';
        } else {
            $stmt = db()->prepare("DELETE FROM categories WHERE id = ?");
            $stmt->execute([$id]);
            $message = 'تم حذف الفئة';
        }
    } elseif ($action === 'toggle' && isset($_POST['id'])) {
        $stmt = db()->prepare("UPDATE categories SET is_active = NOT is_active WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        $message = 'تم تحديث الحالة';
    }
}

// Get all categories
$categories = db()->query("
    SELECT c.*,
           (SELECT COUNT(*) FROM properties WHERE category_id = c.id) as property_count
    FROM categories c
    ORDER BY c.sort_order, c.name_ar
")->fetchAll();

$pageTitle = 'إدارة الفئات';
include __DIR__ . '/includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3"><i class="fas fa-tags me-2"></i>إدارة فئات العقارات</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="fas fa-plus me-1"></i>إضافة فئة
        </button>
    </div>

    <?php if ($message): ?>
    <div class="alert alert-success"><?= $message ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الأيقونة</th>
                        <th>الاسم بالعربي</th>
                        <th>الاسم بالإنجليزي</th>
                        <th>العقارات</th>
                        <th>الترتيب</th>
                        <th>الحالة</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td><?= $cat['id'] ?></td>
                        <td>
                            <?php if ($cat['icon']): ?>
                            <i class="<?= htmlspecialchars($cat['icon']) ?> fa-lg text-primary"></i>
                            <?php else: ?>
                            <i class="fas fa-home fa-lg text-muted"></i>
                            <?php endif; ?>
                        </td>
                        <td><strong><?= htmlspecialchars($cat['name_ar']) ?></strong></td>
                        <td><?= htmlspecialchars($cat['name_en'] ?: '-') ?></td>
                        <td>
                            <span class="badge bg-secondary"><?= $cat['property_count'] ?></span>
                        </td>
                        <td><?= $cat['sort_order'] ?></td>
                        <td>
                            <span class="badge bg-<?= $cat['is_active'] ? 'success' : 'secondary' ?>">
                                <?= $cat['is_active'] ? 'نشط' : 'معطل' ?>
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" onclick="editCategory(<?= htmlspecialchars(json_encode($cat)) ?>)">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="action" value="toggle">
                                <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-warning">
                                    <i class="fas fa-toggle-<?= $cat['is_active'] ? 'on' : 'off' ?>"></i>
                                </button>
                            </form>
                            <?php if ($cat['property_count'] == 0): ?>
                            <form method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $cat['id'] ?>">
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

    <!-- Icons Reference -->
    <div class="card mt-4">
        <div class="card-header">
            <h6 class="mb-0">أيقونات مقترحة</h6>
        </div>
        <div class="card-body">
            <div class="d-flex flex-wrap gap-3">
                <span class="badge bg-light text-dark p-2"><i class="fas fa-home me-1"></i> fas fa-home</span>
                <span class="badge bg-light text-dark p-2"><i class="fas fa-building me-1"></i> fas fa-building</span>
                <span class="badge bg-light text-dark p-2"><i class="fas fa-store me-1"></i> fas fa-store</span>
                <span class="badge bg-light text-dark p-2"><i class="fas fa-warehouse me-1"></i> fas fa-warehouse</span>
                <span class="badge bg-light text-dark p-2"><i class="fas fa-hotel me-1"></i> fas fa-hotel</span>
                <span class="badge bg-light text-dark p-2"><i class="fas fa-industry me-1"></i> fas fa-industry</span>
                <span class="badge bg-light text-dark p-2"><i class="fas fa-tree me-1"></i> fas fa-tree</span>
                <span class="badge bg-light text-dark p-2"><i class="fas fa-clinic-medical me-1"></i> fas fa-clinic-medical</span>
            </div>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div class="modal-header">
                    <h5 class="modal-title">إضافة فئة جديدة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">الاسم بالعربي *</label>
                        <input type="text" name="name_ar" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الاسم بالإنجليزي</label>
                        <input type="text" name="name_en" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الأيقونة (FontAwesome class)</label>
                        <input type="text" name="icon" class="form-control" placeholder="fas fa-home">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الوصف</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الترتيب</label>
                        <input type="number" name="sort_order" class="form-control" value="0">
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
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-header">
                    <h5 class="modal-title">تعديل الفئة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">الاسم بالعربي *</label>
                        <input type="text" name="name_ar" id="edit_name_ar" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الاسم بالإنجليزي</label>
                        <input type="text" name="name_en" id="edit_name_en" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الأيقونة (FontAwesome class)</label>
                        <input type="text" name="icon" id="edit_icon" class="form-control" placeholder="fas fa-home">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الوصف</label>
                        <textarea name="description" id="edit_description" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الترتيب</label>
                        <input type="number" name="sort_order" id="edit_sort_order" class="form-control">
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
function editCategory(cat) {
    document.getElementById('edit_id').value = cat.id;
    document.getElementById('edit_name_ar').value = cat.name_ar;
    document.getElementById('edit_name_en').value = cat.name_en || '';
    document.getElementById('edit_icon').value = cat.icon || '';
    document.getElementById('edit_description').value = cat.description || '';
    document.getElementById('edit_sort_order').value = cat.sort_order || 0;

    new bootstrap.Modal(document.getElementById('editModal')).show();
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
