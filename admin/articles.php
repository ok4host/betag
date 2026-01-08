<?php
/**
 * Admin - Articles Management (إدارة المقالات)
 */
require_once __DIR__ . '/../includes/functions.php';
startSession();
requireAdmin();

// Handle actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $action = $_GET['action'];

    switch ($action) {
        case 'publish':
            db()->prepare("UPDATE articles SET status = 'published', published_at = NOW() WHERE id = ?")->execute([$id]);
            $_SESSION['flash_message'] = 'تم نشر المقال بنجاح';
            break;
        case 'draft':
            db()->prepare("UPDATE articles SET status = 'draft' WHERE id = ?")->execute([$id]);
            $_SESSION['flash_message'] = 'تم تحويل المقال لمسودة';
            break;
        case 'feature':
            db()->prepare("UPDATE articles SET is_featured = 1 WHERE id = ?")->execute([$id]);
            $_SESSION['flash_message'] = 'تم تمييز المقال';
            break;
        case 'unfeature':
            db()->prepare("UPDATE articles SET is_featured = 0 WHERE id = ?")->execute([$id]);
            $_SESSION['flash_message'] = 'تم إلغاء تمييز المقال';
            break;
        case 'delete':
            db()->prepare("DELETE FROM articles WHERE id = ?")->execute([$id]);
            $_SESSION['flash_message'] = 'تم حذف المقال';
            $_SESSION['flash_type'] = 'warning';
            break;
    }
    redirect('articles.php');
}

// Filter
$where = "1=1";
$params = [];

if (!empty($_GET['status'])) {
    $where .= " AND a.status = ?";
    $params[] = $_GET['status'];
}

if (!empty($_GET['category'])) {
    $where .= " AND a.category_id = ?";
    $params[] = $_GET['category'];
}

if (!empty($_GET['search'])) {
    $where .= " AND (a.title LIKE ? OR a.content LIKE ?)";
    $params[] = '%' . $_GET['search'] . '%';
    $params[] = '%' . $_GET['search'] . '%';
}

// Get articles
$sql = "SELECT a.*, c.name_ar as category_name, u.name as author_name
        FROM articles a
        LEFT JOIN article_categories c ON a.category_id = c.id
        LEFT JOIN users u ON a.author_id = u.id
        WHERE $where
        ORDER BY a.created_at DESC";

$stmt = db()->prepare($sql);
$stmt->execute($params);
$articles = $stmt->fetchAll();

// Get categories for filter
$categories = db()->query("SELECT * FROM article_categories ORDER BY name_ar")->fetchAll();

$pageTitle = 'إدارة المقالات';
include __DIR__ . '/includes/header.php';
?>

<div class="container-fluid py-4">
    <!-- Stats -->
    <div class="row mb-4">
        <?php
        $stats = db()->query("SELECT
            COUNT(*) as total,
            SUM(status = 'published') as published,
            SUM(status = 'draft') as drafts,
            SUM(views) as total_views
            FROM articles")->fetch();
        ?>
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h3><?= number_format($stats['total'] ?? 0) ?></h3>
                    <p class="mb-0">إجمالي المقالات</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h3><?= number_format($stats['published'] ?? 0) ?></h3>
                    <p class="mb-0">منشورة</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h3><?= number_format($stats['drafts'] ?? 0) ?></h3>
                    <p class="mb-0">مسودات</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h3><?= number_format($stats['total_views'] ?? 0) ?></h3>
                    <p class="mb-0">إجمالي المشاهدات</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">البحث</label>
                    <input type="text" name="search" class="form-control" placeholder="عنوان المقال..."
                           value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">الحالة</label>
                    <select name="status" class="form-select">
                        <option value="">الكل</option>
                        <option value="published" <?= ($_GET['status'] ?? '') === 'published' ? 'selected' : '' ?>>منشور</option>
                        <option value="draft" <?= ($_GET['status'] ?? '') === 'draft' ? 'selected' : '' ?>>مسودة</option>
                        <option value="scheduled" <?= ($_GET['status'] ?? '') === 'scheduled' ? 'selected' : '' ?>>مجدول</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">التصنيف</label>
                    <select name="category" class="form-select">
                        <option value="">الكل</option>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= ($_GET['category'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name_ar']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-1"></i> بحث
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="article-add.php" class="btn btn-success w-100">
                        <i class="fas fa-plus me-1"></i> مقال جديد
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Articles Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">المقالات (<?= count($articles) ?>)</h5>
            <div>
                <a href="article-categories.php" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-folder me-1"></i>التصنيفات
                </a>
                <a href="article-tags.php" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-tags me-1"></i>الوسوم
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover datatable mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>العنوان</th>
                            <th>التصنيف</th>
                            <th>الكاتب</th>
                            <th>الحالة</th>
                            <th>المشاهدات</th>
                            <th>SEO</th>
                            <th>التاريخ</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($articles)): ?>
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <i class="fas fa-newspaper fa-3x text-muted mb-3 d-block"></i>
                                <p class="text-muted">لا توجد مقالات بعد</p>
                                <a href="article-add.php" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i>أضف أول مقال
                                </a>
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($articles as $article): ?>
                        <tr>
                            <td><?= $article['id'] ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <?php if ($article['featured_image']): ?>
                                    <img src="/uploads/articles/<?= $article['featured_image'] ?>" alt=""
                                         width="60" height="40" class="rounded object-fit-cover">
                                    <?php else: ?>
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                         style="width:60px;height:40px">
                                        <i class="fas fa-image text-muted"></i>
                                    </div>
                                    <?php endif; ?>
                                    <div>
                                        <a href="article-add.php?id=<?= $article['id'] ?>" class="fw-bold text-decoration-none">
                                            <?= mb_substr(htmlspecialchars($article['title']), 0, 50) ?>
                                        </a>
                                        <?php if ($article['is_featured']): ?>
                                        <i class="fas fa-star text-warning ms-1"></i>
                                        <?php endif; ?>
                                        <br>
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i><?= $article['reading_time'] ?? 0 ?> دقائق
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <?php if ($article['category_name']): ?>
                                <span class="badge bg-secondary"><?= $article['category_name'] ?></span>
                                <?php else: ?>
                                <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $article['author_name'] ?? '-' ?></td>
                            <td>
                                <?php
                                $statusClass = ['draft' => 'warning', 'published' => 'success', 'scheduled' => 'info', 'archived' => 'secondary'];
                                $statusText = ['draft' => 'مسودة', 'published' => 'منشور', 'scheduled' => 'مجدول', 'archived' => 'مؤرشف'];
                                ?>
                                <span class="badge bg-<?= $statusClass[$article['status']] ?? 'secondary' ?>">
                                    <?= $statusText[$article['status']] ?? $article['status'] ?>
                                </span>
                            </td>
                            <td><?= number_format($article['views']) ?></td>
                            <td>
                                <?php
                                $seoScore = 0;
                                if (!empty($article['meta_title'])) $seoScore += 25;
                                if (!empty($article['meta_description'])) $seoScore += 25;
                                if (!empty($article['focus_keyword'])) $seoScore += 25;
                                if (!empty($article['og_title'])) $seoScore += 25;

                                $seoClass = $seoScore >= 75 ? 'success' : ($seoScore >= 50 ? 'warning' : 'danger');
                                ?>
                                <div class="progress" style="width:60px;height:6px" title="<?= $seoScore ?>%">
                                    <div class="progress-bar bg-<?= $seoClass ?>" style="width:<?= $seoScore ?>%"></div>
                                </div>
                            </td>
                            <td>
                                <small><?= formatDate($article['published_at'] ?? $article['created_at']) ?></small>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                        إجراءات
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="dropdown-item" href="article-add.php?id=<?= $article['id'] ?>">
                                                <i class="fas fa-edit me-2"></i>تعديل
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="/blog/<?= $article['slug'] ?>" target="_blank">
                                                <i class="fas fa-eye me-2"></i>عرض
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <?php if ($article['status'] === 'draft'): ?>
                                        <li>
                                            <a class="dropdown-item text-success" href="?action=publish&id=<?= $article['id'] ?>">
                                                <i class="fas fa-check me-2"></i>نشر
                                            </a>
                                        </li>
                                        <?php else: ?>
                                        <li>
                                            <a class="dropdown-item" href="?action=draft&id=<?= $article['id'] ?>">
                                                <i class="fas fa-file me-2"></i>تحويل لمسودة
                                            </a>
                                        </li>
                                        <?php endif; ?>
                                        <?php if ($article['is_featured']): ?>
                                        <li>
                                            <a class="dropdown-item" href="?action=unfeature&id=<?= $article['id'] ?>">
                                                <i class="far fa-star me-2"></i>إلغاء التمييز
                                            </a>
                                        </li>
                                        <?php else: ?>
                                        <li>
                                            <a class="dropdown-item" href="?action=feature&id=<?= $article['id'] ?>">
                                                <i class="fas fa-star me-2"></i>تمييز
                                            </a>
                                        </li>
                                        <?php endif; ?>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item text-danger" href="#"
                                               onclick="confirmDelete('?action=delete&id=<?= $article['id'] ?>', '<?= addslashes($article['title']) ?>')">
                                                <i class="fas fa-trash me-2"></i>حذف
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
