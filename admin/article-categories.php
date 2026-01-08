<?php
/**
 * Admin - Article Categories Management
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
        $description = sanitize($_POST['description']);
        $meta_title = sanitize($_POST['meta_title']);
        $meta_description = sanitize($_POST['meta_description']);
        $meta_keywords = sanitize($_POST['meta_keywords']);

        if ($name_ar) {
            $slug = uniqueSlug('article_categories', $name_en ?: $name_ar);

            $stmt = db()->prepare("INSERT INTO article_categories (name_ar, name_en, slug, description, meta_title, meta_description, meta_keywords, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, 1)");
            if ($stmt->execute([$name_ar, $name_en, $slug, $description, $meta_title, $meta_description, $meta_keywords])) {
                $message = 'تم إضافة التصنيف بنجاح';
            } else {
                $error = 'حدث خطأ أثناء الإضافة';
            }
        }
    } elseif ($action === 'edit' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        $name_ar = sanitize($_POST['name_ar']);
        $name_en = sanitize($_POST['name_en']);
        $description = sanitize($_POST['description']);
        $meta_title = sanitize($_POST['meta_title']);
        $meta_description = sanitize($_POST['meta_description']);
        $meta_keywords = sanitize($_POST['meta_keywords']);

        $slug = uniqueSlug('article_categories', $name_en ?: $name_ar, $id);

        $stmt = db()->prepare("UPDATE article_categories SET name_ar = ?, name_en = ?, slug = ?, description = ?, meta_title = ?, meta_description = ?, meta_keywords = ? WHERE id = ?");
        $stmt->execute([$name_ar, $name_en, $slug, $description, $meta_title, $meta_description, $meta_keywords, $id]);
        $message = 'تم تحديث التصنيف بنجاح';

    } elseif ($action === 'delete' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        $hasArticles = db()->prepare("SELECT COUNT(*) FROM articles WHERE category_id = ?");
        $hasArticles->execute([$id]);

        if ($hasArticles->fetchColumn() > 0) {
            $error = 'لا يمكن حذف هذا التصنيف لأنه مرتبط بمقالات';
        } else {
            $stmt = db()->prepare("DELETE FROM article_categories WHERE id = ?");
            $stmt->execute([$id]);
            $message = 'تم حذف التصنيف';
        }
    } elseif ($action === 'toggle' && isset($_POST['id'])) {
        $stmt = db()->prepare("UPDATE article_categories SET is_active = NOT is_active WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        $message = 'تم تحديث الحالة';
    }
}

// Get all categories
$categories = db()->query("
    SELECT c.*,
           (SELECT COUNT(*) FROM articles WHERE category_id = c.id) as articles_count
    FROM article_categories c
    ORDER BY c.sort_order, c.name_ar
")->fetchAll();

$pageTitle = 'تصنيفات المقالات';
include __DIR__ . '/includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3"><i class="fas fa-folder me-2"></i>تصنيفات المقالات</h1>
        <div>
            <a href="articles.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-right me-1"></i>العودة
            </a>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="fas fa-plus me-1"></i>إضافة تصنيف
            </button>
        </div>
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
                        <th>الاسم بالعربي</th>
                        <th>الاسم بالإنجليزي</th>
                        <th>المقالات</th>
                        <th>SEO</th>
                        <th>الحالة</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td><?= $cat['id'] ?></td>
                        <td><strong><?= htmlspecialchars($cat['name_ar']) ?></strong></td>
                        <td><?= htmlspecialchars($cat['name_en'] ?: '-') ?></td>
                        <td>
                            <span class="badge bg-secondary"><?= $cat['articles_count'] ?></span>
                        </td>
                        <td>
                            <?php if (!empty($cat['meta_title']) && !empty($cat['meta_description'])): ?>
                            <span class="badge bg-success"><i class="fas fa-check"></i></span>
                            <?php else: ?>
                            <span class="badge bg-warning"><i class="fas fa-exclamation"></i></span>
                            <?php endif; ?>
                        </td>
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
                            <?php if ($cat['articles_count'] == 0): ?>
                            <form method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد؟')">
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
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div class="modal-header">
                    <h5 class="modal-title">إضافة تصنيف جديد</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">الاسم بالعربي *</label>
                            <input type="text" name="name_ar" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">الاسم بالإنجليزي</label>
                            <input type="text" name="name_en" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الوصف</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                    <hr>
                    <h6 class="text-success"><i class="fas fa-search me-1"></i>إعدادات SEO</h6>
                    <div class="mb-3">
                        <label class="form-label">عنوان SEO</label>
                        <input type="text" name="meta_title" class="form-control" placeholder="مقالات عن [اسم التصنيف]">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">وصف SEO</label>
                        <textarea name="meta_description" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">كلمات مفتاحية</label>
                        <input type="text" name="meta_keywords" class="form-control" placeholder="كلمة1, كلمة2">
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
                    <h5 class="modal-title">تعديل التصنيف</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">الاسم بالعربي *</label>
                            <input type="text" name="name_ar" id="edit_name_ar" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">الاسم بالإنجليزي</label>
                            <input type="text" name="name_en" id="edit_name_en" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الوصف</label>
                        <textarea name="description" id="edit_description" class="form-control" rows="2"></textarea>
                    </div>
                    <hr>
                    <h6 class="text-success"><i class="fas fa-search me-1"></i>إعدادات SEO</h6>
                    <div class="mb-3">
                        <label class="form-label">عنوان SEO</label>
                        <input type="text" name="meta_title" id="edit_meta_title" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">وصف SEO</label>
                        <textarea name="meta_description" id="edit_meta_description" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">كلمات مفتاحية</label>
                        <input type="text" name="meta_keywords" id="edit_meta_keywords" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">حفظ</button>
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
    document.getElementById('edit_description').value = cat.description || '';
    document.getElementById('edit_meta_title').value = cat.meta_title || '';
    document.getElementById('edit_meta_description').value = cat.meta_description || '';
    document.getElementById('edit_meta_keywords').value = cat.meta_keywords || '';
    new bootstrap.Modal(document.getElementById('editModal')).show();
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
