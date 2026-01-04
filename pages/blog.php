<?php
/**
 * Blog Page - Article Listing
 */
require_once __DIR__ . '/../includes/functions.php';

// Get category filter
$categorySlug = $_GET['category'] ?? null;
$category = null;
if ($categorySlug) {
    $stmt = db()->prepare("SELECT * FROM article_categories WHERE slug = ? AND is_active = 1");
    $stmt->execute([$categorySlug]);
    $category = $stmt->fetch();
}

// Pagination
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 12;
$offset = ($page - 1) * $perPage;

// Build query
$where = "a.status = 'published'";
$params = [];

if ($category) {
    $where .= " AND a.category_id = ?";
    $params[] = $category['id'];
}

// Search
$search = $_GET['q'] ?? '';
if ($search) {
    $where .= " AND (a.title LIKE ? OR a.content LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

// Get total count
$countSql = "SELECT COUNT(*) FROM articles a WHERE $where";
$stmt = db()->prepare($countSql);
$stmt->execute($params);
$totalArticles = $stmt->fetchColumn();
$totalPages = ceil($totalArticles / $perPage);

// Get articles
$sql = "SELECT a.*, c.name_ar as category_name, c.slug as category_slug, u.name as author_name
        FROM articles a
        LEFT JOIN article_categories c ON a.category_id = c.id
        LEFT JOIN users u ON a.author_id = u.id
        WHERE $where
        ORDER BY a.is_featured DESC, a.published_at DESC
        LIMIT $perPage OFFSET $offset";

$stmt = db()->prepare($sql);
$stmt->execute($params);
$articles = $stmt->fetchAll();

// Get all categories for sidebar
$categories = db()->query("
    SELECT c.*, COUNT(a.id) as articles_count
    FROM article_categories c
    LEFT JOIN articles a ON c.id = a.category_id AND a.status = 'published'
    WHERE c.is_active = 1
    GROUP BY c.id
    ORDER BY c.sort_order, c.name_ar
")->fetchAll();

// Get featured articles for slider
$featuredArticles = db()->query("
    SELECT * FROM articles
    WHERE status = 'published' AND is_featured = 1
    ORDER BY published_at DESC
    LIMIT 5
")->fetchAll();

// SEO
$seoTitle = $category
    ? ($category['meta_title'] ?: 'مقالات ' . $category['name_ar'])
    : getSetting('blog_title', 'المدونة العقارية');
$seoDescription = $category
    ? ($category['meta_description'] ?: 'اقرأ أحدث المقالات في ' . $category['name_ar'])
    : getSetting('blog_description', 'أحدث المقالات والأخبار العقارية');
$seoKeywords = $category['meta_keywords'] ?? getSetting('blog_keywords', 'مقالات عقارية, نصائح عقارية');

include __DIR__ . '/../includes/header.php';
?>

<!-- Page Header -->
<div class="bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-5 fw-bold mb-2">
                    <?php if ($category): ?>
                        <i class="fas fa-folder-open me-2"></i><?= htmlspecialchars($category['name_ar']) ?>
                    <?php elseif ($search): ?>
                        <i class="fas fa-search me-2"></i>نتائج البحث: <?= htmlspecialchars($search) ?>
                    <?php else: ?>
                        <i class="fas fa-newspaper me-2"></i>المدونة العقارية
                    <?php endif; ?>
                </h1>
                <p class="lead mb-0 opacity-75">
                    <?= $category ? htmlspecialchars($category['description']) : 'أحدث المقالات والنصائح العقارية' ?>
                </p>
            </div>
            <div class="col-lg-4">
                <form action="" method="GET" class="mt-3 mt-lg-0">
                    <div class="input-group">
                        <input type="text" name="q" class="form-control form-control-lg" placeholder="ابحث في المقالات..."
                               value="<?= htmlspecialchars($search) ?>">
                        <button class="btn btn-light" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Breadcrumb -->
<nav class="bg-light py-2">
    <div class="container">
        <ol class="breadcrumb mb-0" itemscope itemtype="https://schema.org/BreadcrumbList">
            <li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <a href="/" itemprop="item"><span itemprop="name">الرئيسية</span></a>
                <meta itemprop="position" content="1" />
            </li>
            <li class="breadcrumb-item <?= !$category ? 'active' : '' ?>" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <?php if ($category): ?>
                <a href="/blog" itemprop="item"><span itemprop="name">المدونة</span></a>
                <?php else: ?>
                <span itemprop="name">المدونة</span>
                <?php endif; ?>
                <meta itemprop="position" content="2" />
            </li>
            <?php if ($category): ?>
            <li class="breadcrumb-item active" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <span itemprop="name"><?= htmlspecialchars($category['name_ar']) ?></span>
                <meta itemprop="position" content="3" />
            </li>
            <?php endif; ?>
        </ol>
    </div>
</nav>

<div class="container py-5">
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <?php if (empty($articles)): ?>
            <div class="text-center py-5">
                <i class="fas fa-newspaper fa-4x text-muted mb-3"></i>
                <h3>لا توجد مقالات</h3>
                <p class="text-muted">لم نجد مقالات مطابقة لبحثك</p>
                <a href="/blog" class="btn btn-primary">عرض جميع المقالات</a>
            </div>
            <?php else: ?>

            <!-- Articles Grid -->
            <div class="row g-4">
                <?php foreach ($articles as $index => $article): ?>
                <div class="col-md-<?= $index === 0 ? '12' : '6' ?>">
                    <article class="card h-100 shadow-sm border-0 article-card">
                        <a href="/blog/<?= $article['slug'] ?>" class="text-decoration-none">
                            <?php if ($article['featured_image']): ?>
                            <img src="/uploads/articles/<?= $article['featured_image'] ?>"
                                 class="card-img-top" alt="<?= htmlspecialchars($article['title']) ?>"
                                 style="height:<?= $index === 0 ? '300px' : '180px' ?>;object-fit:cover">
                            <?php else: ?>
                            <div class="bg-light d-flex align-items-center justify-content-center"
                                 style="height:<?= $index === 0 ? '300px' : '180px' ?>">
                                <i class="fas fa-image fa-3x text-muted"></i>
                            </div>
                            <?php endif; ?>
                        </a>

                        <div class="card-body">
                            <?php if ($article['category_name']): ?>
                            <a href="/blog?category=<?= $article['category_slug'] ?>" class="badge bg-primary text-decoration-none mb-2">
                                <?= htmlspecialchars($article['category_name']) ?>
                            </a>
                            <?php endif; ?>

                            <h<?= $index === 0 ? '2' : '3' ?> class="card-title">
                                <a href="/blog/<?= $article['slug'] ?>" class="text-dark text-decoration-none">
                                    <?= htmlspecialchars($article['title']) ?>
                                </a>
                            </h<?= $index === 0 ? '2' : '3' ?>>

                            <?php if ($article['excerpt']): ?>
                            <p class="card-text text-muted">
                                <?= mb_substr(htmlspecialchars($article['excerpt']), 0, $index === 0 ? 200 : 100) ?>...
                            </p>
                            <?php endif; ?>

                            <div class="d-flex align-items-center text-muted small">
                                <?php if ($article['author_name']): ?>
                                <span class="me-3"><i class="fas fa-user me-1"></i><?= $article['author_name'] ?></span>
                                <?php endif; ?>
                                <span class="me-3"><i class="far fa-calendar me-1"></i><?= formatDate($article['published_at']) ?></span>
                                <span><i class="far fa-clock me-1"></i><?= $article['reading_time'] ?> دقائق</span>
                            </div>
                        </div>

                        <?php if ($article['is_featured']): ?>
                        <span class="position-absolute top-0 end-0 m-2 badge bg-warning text-dark">
                            <i class="fas fa-star"></i> مميز
                        </span>
                        <?php endif; ?>
                    </article>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <nav class="mt-5">
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $page - 1 ?><?= $categorySlug ? '&category=' . $categorySlug : '' ?>">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                    <?php endif; ?>

                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                    <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?><?= $categorySlug ? '&category=' . $categorySlug : '' ?>"><?= $i ?></a>
                    </li>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $page + 1 ?><?= $categorySlug ? '&category=' . $categorySlug : '' ?>">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
            <?php endif; ?>

            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Categories -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-folder me-2"></i>التصنيفات</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="/blog" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?= !$category ? 'active' : '' ?>">
                        جميع المقالات
                        <span class="badge bg-primary rounded-pill"><?= $totalArticles ?></span>
                    </a>
                    <?php foreach ($categories as $cat): ?>
                    <a href="/blog?category=<?= $cat['slug'] ?>"
                       class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?= $category && $category['id'] == $cat['id'] ? 'active' : '' ?>">
                        <?= htmlspecialchars($cat['name_ar']) ?>
                        <span class="badge bg-secondary rounded-pill"><?= $cat['articles_count'] ?></span>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Popular Articles -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-fire me-2"></i>الأكثر قراءة</h5>
                </div>
                <div class="card-body">
                    <?php
                    $popularArticles = db()->query("
                        SELECT id, title, slug, featured_image, views
                        FROM articles
                        WHERE status = 'published'
                        ORDER BY views DESC
                        LIMIT 5
                    ")->fetchAll();
                    ?>
                    <?php foreach ($popularArticles as $pop): ?>
                    <div class="d-flex mb-3">
                        <?php if ($pop['featured_image']): ?>
                        <img src="/uploads/articles/<?= $pop['featured_image'] ?>" alt=""
                             class="rounded me-3" style="width:60px;height:60px;object-fit:cover">
                        <?php endif; ?>
                        <div>
                            <a href="/blog/<?= $pop['slug'] ?>" class="text-dark text-decoration-none fw-bold small">
                                <?= mb_substr(htmlspecialchars($pop['title']), 0, 50) ?>
                            </a>
                            <div class="text-muted small">
                                <i class="fas fa-eye me-1"></i><?= number_format($pop['views']) ?> مشاهدة
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Newsletter -->
            <div class="card shadow-sm bg-primary text-white">
                <div class="card-body text-center py-4">
                    <i class="fas fa-envelope-open-text fa-3x mb-3"></i>
                    <h5>اشترك في نشرتنا البريدية</h5>
                    <p class="small opacity-75">احصل على أحدث المقالات والعروض العقارية</p>
                    <form class="mt-3">
                        <input type="email" class="form-control mb-2" placeholder="بريدك الإلكتروني">
                        <button type="submit" class="btn btn-light w-100">اشترك الآن</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.article-card:hover {
    transform: translateY(-5px);
    transition: transform 0.3s;
}
</style>

<?php include __DIR__ . '/../includes/footer.php'; ?>
