<?php
/**
 * Admin - Static Pages Management
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
        $title = sanitize($_POST['title']);
        $slug = sanitize($_POST['slug']);
        $content = $_POST['content']; // Allow HTML
        $meta_title = sanitize($_POST['meta_title']);
        $meta_description = sanitize($_POST['meta_description']);
        $is_active = isset($_POST['is_active']) ? 1 : 0;

        if ($title && $slug) {
            $stmt = db()->prepare("INSERT INTO pages (title, slug, content, meta_title, meta_description, is_active) VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$title, $slug, $content, $meta_title, $meta_description, $is_active])) {
                $message = 'تم إضافة الصفحة بنجاح';
            } else {
                $error = 'حدث خطأ أثناء الإضافة';
            }
        }
    } elseif ($action === 'edit' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        $title = sanitize($_POST['title']);
        $slug = sanitize($_POST['slug']);
        $content = $_POST['content'];
        $meta_title = sanitize($_POST['meta_title']);
        $meta_description = sanitize($_POST['meta_description']);
        $is_active = isset($_POST['is_active']) ? 1 : 0;

        $stmt = db()->prepare("UPDATE pages SET title = ?, slug = ?, content = ?, meta_title = ?, meta_description = ?, is_active = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$title, $slug, $content, $meta_title, $meta_description, $is_active, $id]);
        $message = 'تم تحديث الصفحة بنجاح';

    } elseif ($action === 'delete' && isset($_POST['id'])) {
        $stmt = db()->prepare("DELETE FROM pages WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        $message = 'تم حذف الصفحة';

    } elseif ($action === 'toggle' && isset($_POST['id'])) {
        $stmt = db()->prepare("UPDATE pages SET is_active = NOT is_active WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        $message = 'تم تحديث الحالة';
    }
}

// Get all pages
$pages = db()->query("SELECT * FROM pages ORDER BY created_at DESC")->fetchAll();

// Check if editing
$editPage = null;
if (isset($_GET['edit'])) {
    $stmt = db()->prepare("SELECT * FROM pages WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editPage = $stmt->fetch();
}

$pageTitle = 'إدارة الصفحات';
include __DIR__ . '/includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3"><i class="fas fa-file-alt me-2"></i>إدارة الصفحات الثابتة</h1>
        <?php if (!$editPage): ?>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="fas fa-plus me-1"></i>إضافة صفحة
        </button>
        <?php else: ?>
        <a href="pages.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-right me-1"></i>العودة للقائمة
        </a>
        <?php endif; ?>
    </div>

    <?php if ($message): ?>
    <div class="alert alert-success"><?= $message ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <?php if ($editPage): ?>
    <!-- Edit Form -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">تعديل: <?= htmlspecialchars($editPage['title']) ?></h5>
        </div>
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" value="<?= $editPage['id'] ?>">

                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label">عنوان الصفحة *</label>
                            <input type="text" name="title" class="form-control" required
                                   value="<?= htmlspecialchars($editPage['title']) ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">المحتوى</label>
                            <textarea name="content" class="form-control" rows="15"><?= htmlspecialchars($editPage['content']) ?></textarea>
                            <small class="text-muted">يمكنك استخدام HTML</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">الرابط (Slug) *</label>
                            <input type="text" name="slug" class="form-control" required dir="ltr"
                                   value="<?= htmlspecialchars($editPage['slug']) ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">عنوان SEO</label>
                            <input type="text" name="meta_title" class="form-control"
                                   value="<?= htmlspecialchars($editPage['meta_title']) ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">وصف SEO</label>
                            <textarea name="meta_description" class="form-control" rows="3"><?= htmlspecialchars($editPage['meta_description']) ?></textarea>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                                   <?= $editPage['is_active'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_active">نشط</label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-1"></i>حفظ التعديلات
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php else: ?>
    <!-- Pages List -->
    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>العنوان</th>
                        <th>الرابط</th>
                        <th>الحالة</th>
                        <th>التاريخ</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pages)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="fas fa-file-alt fa-3x mb-3 d-block opacity-50"></i>
                            لا توجد صفحات بعد
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($pages as $page): ?>
                    <tr>
                        <td><?= $page['id'] ?></td>
                        <td><strong><?= htmlspecialchars($page['title']) ?></strong></td>
                        <td>
                            <a href="/page/<?= $page['slug'] ?>" target="_blank" class="text-muted">
                                /page/<?= $page['slug'] ?>
                            </a>
                        </td>
                        <td>
                            <span class="badge bg-<?= $page['is_active'] ? 'success' : 'secondary' ?>">
                                <?= $page['is_active'] ? 'نشط' : 'معطل' ?>
                            </span>
                        </td>
                        <td><?= formatDate($page['created_at']) ?></td>
                        <td>
                            <a href="?edit=<?= $page['id'] ?>" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="action" value="toggle">
                                <input type="hidden" name="id" value="<?= $page['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-warning">
                                    <i class="fas fa-toggle-<?= $page['is_active'] ? 'on' : 'off' ?>"></i>
                                </button>
                            </form>
                            <form method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $page['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div class="modal-header">
                    <h5 class="modal-title">إضافة صفحة جديدة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">عنوان الصفحة *</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">الرابط (Slug) *</label>
                            <input type="text" name="slug" class="form-control" required dir="ltr" placeholder="about-us">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">المحتوى</label>
                        <textarea name="content" class="form-control" rows="10"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">عنوان SEO</label>
                            <input type="text" name="meta_title" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">وصف SEO</label>
                            <textarea name="meta_description" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" id="add_is_active" checked>
                        <label class="form-check-label" for="add_is_active">نشط</label>
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

<?php include __DIR__ . '/includes/footer.php'; ?>
