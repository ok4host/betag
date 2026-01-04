<?php
/**
 * Admin - Add/Edit Article with Full SEO Control
 */
require_once __DIR__ . '/../includes/functions.php';
startSession();
requireAdmin();

$message = '';
$error = '';
$article = null;

// Check if editing
$editId = isset($_GET['id']) ? (int)$_GET['id'] : null;
if ($editId) {
    $stmt = db()->prepare("SELECT * FROM articles WHERE id = ?");
    $stmt->execute([$editId]);
    $article = $stmt->fetch();
    if (!$article) {
        redirect('articles.php');
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'title' => sanitize($_POST['title']),
        'excerpt' => sanitize($_POST['excerpt']),
        'content' => $_POST['content'], // Allow HTML
        'category_id' => !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null,
        'status' => $_POST['status'] ?? 'draft',
        'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
        'allow_comments' => isset($_POST['allow_comments']) ? 1 : 0,

        // SEO Fields
        'meta_title' => sanitize($_POST['meta_title']),
        'meta_description' => sanitize($_POST['meta_description']),
        'meta_keywords' => sanitize($_POST['meta_keywords']),
        'canonical_url' => sanitize($_POST['canonical_url']),
        'og_title' => sanitize($_POST['og_title']),
        'og_description' => sanitize($_POST['og_description']),
        'twitter_title' => sanitize($_POST['twitter_title']),
        'twitter_description' => sanitize($_POST['twitter_description']),
        'robots' => $_POST['robots'] ?? 'index, follow',
        'focus_keyword' => sanitize($_POST['focus_keyword']),
        'secondary_keywords' => sanitize($_POST['secondary_keywords']),
        'schema_type' => $_POST['schema_type'] ?? 'Article',
    ];

    // Calculate reading time (average 200 words per minute in Arabic)
    $wordCount = str_word_count(strip_tags($data['content']));
    $data['reading_time'] = max(1, ceil($wordCount / 200));

    // Validate
    if (empty($data['title'])) {
        $error = 'عنوان المقال مطلوب';
    } else {
        // Handle featured image upload
        $featuredImage = $article['featured_image'] ?? null;
        if (!empty($_FILES['featured_image']['name'])) {
            $uploadResult = uploadImage($_FILES['featured_image'], 'articles');
            if ($uploadResult['success']) {
                $featuredImage = $uploadResult['filename'];
            } else {
                $error = $uploadResult['error'];
            }
        }

        // Handle OG image upload
        $ogImage = $article['og_image'] ?? null;
        if (!empty($_FILES['og_image']['name'])) {
            $uploadResult = uploadImage($_FILES['og_image'], 'articles');
            if ($uploadResult['success']) {
                $ogImage = $uploadResult['filename'];
            }
        }

        if (!$error) {
            $publishedAt = null;
            if ($data['status'] === 'published' && empty($article['published_at'])) {
                $publishedAt = date('Y-m-d H:i:s');
            } elseif ($data['status'] === 'published') {
                $publishedAt = $article['published_at'];
            }

            if ($editId) {
                // Update
                $slug = uniqueSlug('articles', $data['title'], $editId);
                $sql = "UPDATE articles SET
                        title = ?, slug = ?, excerpt = ?, content = ?, category_id = ?,
                        status = ?, is_featured = ?, allow_comments = ?, reading_time = ?,
                        meta_title = ?, meta_description = ?, meta_keywords = ?, canonical_url = ?,
                        og_title = ?, og_description = ?, og_image = ?,
                        twitter_title = ?, twitter_description = ?,
                        robots = ?, focus_keyword = ?, secondary_keywords = ?, schema_type = ?,
                        featured_image = ?, published_at = COALESCE(?, published_at), updated_at = NOW()
                        WHERE id = ?";

                $stmt = db()->prepare($sql);
                $stmt->execute([
                    $data['title'], $slug, $data['excerpt'], $data['content'], $data['category_id'],
                    $data['status'], $data['is_featured'], $data['allow_comments'], $data['reading_time'],
                    $data['meta_title'], $data['meta_description'], $data['meta_keywords'], $data['canonical_url'],
                    $data['og_title'], $data['og_description'], $ogImage,
                    $data['twitter_title'], $data['twitter_description'],
                    $data['robots'], $data['focus_keyword'], $data['secondary_keywords'], $data['schema_type'],
                    $featuredImage, $publishedAt, $editId
                ]);
                $message = 'تم تحديث المقال بنجاح';

            } else {
                // Insert
                $slug = uniqueSlug('articles', $data['title']);
                $sql = "INSERT INTO articles
                        (title, slug, excerpt, content, category_id, author_id,
                         status, is_featured, allow_comments, reading_time,
                         meta_title, meta_description, meta_keywords, canonical_url,
                         og_title, og_description, og_image,
                         twitter_title, twitter_description,
                         robots, focus_keyword, secondary_keywords, schema_type,
                         featured_image, published_at, created_at, updated_at)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";

                $stmt = db()->prepare($sql);
                $stmt->execute([
                    $data['title'], $slug, $data['excerpt'], $data['content'], $data['category_id'],
                    $_SESSION['user_id'], $data['status'], $data['is_featured'], $data['allow_comments'],
                    $data['reading_time'], $data['meta_title'], $data['meta_description'], $data['meta_keywords'],
                    $data['canonical_url'], $data['og_title'], $data['og_description'], $ogImage,
                    $data['twitter_title'], $data['twitter_description'],
                    $data['robots'], $data['focus_keyword'], $data['secondary_keywords'], $data['schema_type'],
                    $featuredImage, $publishedAt
                ]);
                $newId = db()->lastInsertId();
                redirect('article-add.php?id=' . $newId . '&saved=1');
            }
        }
    }
}

// Get categories
$categories = db()->query("SELECT * FROM article_categories WHERE is_active = 1 ORDER BY name_ar")->fetchAll();

if (isset($_GET['saved'])) {
    $message = 'تم حفظ المقال بنجاح';
}

$pageTitle = $editId ? 'تعديل مقال' : 'إضافة مقال جديد';
include __DIR__ . '/includes/header.php';
?>

<style>
.seo-preview {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 15px;
    font-family: arial, sans-serif;
}
.seo-preview-title {
    color: #1a0dab;
    font-size: 18px;
    margin-bottom: 3px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.seo-preview-url {
    color: #006621;
    font-size: 14px;
    margin-bottom: 3px;
}
.seo-preview-desc {
    color: #545454;
    font-size: 13px;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.char-counter {
    font-size: 12px;
    color: #666;
}
.char-counter.warning { color: #f0ad4e; }
.char-counter.danger { color: #d9534f; }
</style>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">
            <i class="fas fa-<?= $editId ? 'edit' : 'plus' ?> me-2"></i>
            <?= $editId ? 'تعديل مقال' : 'إضافة مقال جديد' ?>
        </h1>
        <div>
            <?php if ($editId): ?>
            <a href="/blog/<?= $article['slug'] ?>" target="_blank" class="btn btn-outline-primary">
                <i class="fas fa-eye me-1"></i>عرض
            </a>
            <?php endif; ?>
            <a href="articles.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-right me-1"></i>العودة للقائمة
            </a>
        </div>
    </div>

    <?php if ($message): ?>
    <div class="alert alert-success"><?= $message ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Basic Info -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>محتوى المقال</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">عنوان المقال *</label>
                            <input type="text" name="title" id="title" class="form-control form-control-lg" required
                                   value="<?= htmlspecialchars($article['title'] ?? '') ?>"
                                   onkeyup="updateSeoPreview()">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">المقتطف (يظهر في قائمة المقالات)</label>
                            <textarea name="excerpt" id="excerpt" class="form-control" rows="3"
                                      onkeyup="updateSeoPreview()"><?= htmlspecialchars($article['excerpt'] ?? '') ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">المحتوى</label>
                            <textarea name="content" id="content" class="form-control" rows="20"><?= htmlspecialchars($article['content'] ?? '') ?></textarea>
                            <small class="text-muted">يمكنك استخدام HTML و Markdown</small>
                        </div>
                    </div>
                </div>

                <!-- SEO Settings -->
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-search me-2"></i>إعدادات SEO (التحكم الكامل)</h5>
                    </div>
                    <div class="card-body">
                        <!-- Google Preview -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">معاينة نتيجة Google</label>
                            <div class="seo-preview" dir="rtl">
                                <div class="seo-preview-title" id="seo-title-preview">
                                    <?= htmlspecialchars($article['meta_title'] ?? $article['title'] ?? 'عنوان المقال') ?>
                                </div>
                                <div class="seo-preview-url">
                                    <?= rtrim(getSetting('site_url', 'https://example.com'), '/') ?>/blog/<span id="seo-url-preview"><?= $article['slug'] ?? 'article-slug' ?></span>
                                </div>
                                <div class="seo-preview-desc" id="seo-desc-preview">
                                    <?= htmlspecialchars($article['meta_description'] ?? $article['excerpt'] ?? 'وصف المقال سيظهر هنا...') ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">الكلمة المفتاحية الرئيسية (Focus Keyword)</label>
                                <input type="text" name="focus_keyword" class="form-control"
                                       value="<?= htmlspecialchars($article['focus_keyword'] ?? '') ?>"
                                       placeholder="مثال: شقق للبيع في القاهرة">
                                <small class="text-muted">الكلمة التي تريد الترتيب عليها في Google</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">كلمات مفتاحية ثانوية</label>
                                <input type="text" name="secondary_keywords" class="form-control"
                                       value="<?= htmlspecialchars($article['secondary_keywords'] ?? '') ?>"
                                       placeholder="كلمة1, كلمة2, كلمة3">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">عنوان SEO (Meta Title)</label>
                            <input type="text" name="meta_title" id="meta_title" class="form-control"
                                   value="<?= htmlspecialchars($article['meta_title'] ?? '') ?>"
                                   onkeyup="updateSeoPreview()" maxlength="70">
                            <div class="d-flex justify-content-between">
                                <small class="text-muted">اتركه فارغاً لاستخدام عنوان المقال</small>
                                <span class="char-counter" id="title-counter">0/70</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">وصف SEO (Meta Description)</label>
                            <textarea name="meta_description" id="meta_description" class="form-control" rows="3"
                                      onkeyup="updateSeoPreview()" maxlength="160"><?= htmlspecialchars($article['meta_description'] ?? '') ?></textarea>
                            <div class="d-flex justify-content-between">
                                <small class="text-muted">155-160 حرف مثالي</small>
                                <span class="char-counter" id="desc-counter">0/160</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">الكلمات المفتاحية (Meta Keywords)</label>
                            <input type="text" name="meta_keywords" class="form-control"
                                   value="<?= htmlspecialchars($article['meta_keywords'] ?? '') ?>"
                                   placeholder="كلمة1, كلمة2, كلمة3">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">الرابط القانوني (Canonical URL)</label>
                            <input type="url" name="canonical_url" class="form-control" dir="ltr"
                                   value="<?= htmlspecialchars($article['canonical_url'] ?? '') ?>"
                                   placeholder="https://example.com/original-article">
                            <small class="text-muted">استخدمه إذا كان المحتوى منسوخاً من مصدر آخر</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">توجيهات الروبوتات (Robots)</label>
                            <select name="robots" class="form-select">
                                <option value="index, follow" <?= ($article['robots'] ?? '') === 'index, follow' ? 'selected' : '' ?>>
                                    index, follow (افتراضي - أرشفة وتتبع)
                                </option>
                                <option value="noindex, follow" <?= ($article['robots'] ?? '') === 'noindex, follow' ? 'selected' : '' ?>>
                                    noindex, follow (لا تؤرشف لكن تتبع الروابط)
                                </option>
                                <option value="index, nofollow" <?= ($article['robots'] ?? '') === 'index, nofollow' ? 'selected' : '' ?>>
                                    index, nofollow (أرشف لكن لا تتبع الروابط)
                                </option>
                                <option value="noindex, nofollow" <?= ($article['robots'] ?? '') === 'noindex, nofollow' ? 'selected' : '' ?>>
                                    noindex, nofollow (لا تؤرشف ولا تتبع)
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Social Media -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fab fa-facebook me-2"></i>مشاركة السوشيال ميديا</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-primary"><i class="fab fa-facebook me-1"></i> Facebook / Open Graph</h6>
                                <div class="mb-3">
                                    <label class="form-label">عنوان OG</label>
                                    <input type="text" name="og_title" class="form-control"
                                           value="<?= htmlspecialchars($article['og_title'] ?? '') ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">وصف OG</label>
                                    <textarea name="og_description" class="form-control" rows="2"><?= htmlspecialchars($article['og_description'] ?? '') ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">صورة OG (1200x630)</label>
                                    <?php if (!empty($article['og_image'])): ?>
                                    <div class="mb-2">
                                        <img src="/uploads/articles/<?= $article['og_image'] ?>" alt="" class="img-thumbnail" width="200">
                                    </div>
                                    <?php endif; ?>
                                    <input type="file" name="og_image" class="form-control" accept="image/*">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-info"><i class="fab fa-twitter me-1"></i> Twitter</h6>
                                <div class="mb-3">
                                    <label class="form-label">عنوان Twitter</label>
                                    <input type="text" name="twitter_title" class="form-control"
                                           value="<?= htmlspecialchars($article['twitter_title'] ?? '') ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">وصف Twitter</label>
                                    <textarea name="twitter_description" class="form-control" rows="2"><?= htmlspecialchars($article['twitter_description'] ?? '') ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Schema -->
                <div class="card mb-4">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="fas fa-code me-2"></i>Schema Markup</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">نوع Schema</label>
                            <select name="schema_type" class="form-select">
                                <option value="Article" <?= ($article['schema_type'] ?? '') === 'Article' ? 'selected' : '' ?>>Article</option>
                                <option value="BlogPosting" <?= ($article['schema_type'] ?? '') === 'BlogPosting' ? 'selected' : '' ?>>BlogPosting</option>
                                <option value="NewsArticle" <?= ($article['schema_type'] ?? '') === 'NewsArticle' ? 'selected' : '' ?>>NewsArticle</option>
                                <option value="HowTo" <?= ($article['schema_type'] ?? '') === 'HowTo' ? 'selected' : '' ?>>HowTo (دليل إرشادي)</option>
                                <option value="FAQPage" <?= ($article['schema_type'] ?? '') === 'FAQPage' ? 'selected' : '' ?>>FAQPage (أسئلة شائعة)</option>
                            </select>
                            <small class="text-muted">يساعد Google في فهم نوع المحتوى</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Publish -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">النشر</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">الحالة</label>
                            <select name="status" class="form-select">
                                <option value="draft" <?= ($article['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>مسودة</option>
                                <option value="published" <?= ($article['status'] ?? '') === 'published' ? 'selected' : '' ?>>منشور</option>
                                <option value="scheduled" <?= ($article['status'] ?? '') === 'scheduled' ? 'selected' : '' ?>>مجدول</option>
                            </select>
                        </div>

                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured"
                                   <?= ($article['is_featured'] ?? 0) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_featured">
                                <i class="fas fa-star text-warning me-1"></i>مقال مميز
                            </label>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="allow_comments" id="allow_comments"
                                   <?= ($article['allow_comments'] ?? 1) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="allow_comments">السماح بالتعليقات</label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 btn-lg">
                            <i class="fas fa-save me-1"></i><?= $editId ? 'حفظ التعديلات' : 'نشر المقال' ?>
                        </button>
                    </div>
                </div>

                <!-- Category -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">التصنيف</h5>
                    </div>
                    <div class="card-body">
                        <select name="category_id" class="form-select">
                            <option value="">-- اختر تصنيف --</option>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= ($article['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name_ar']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <a href="article-categories.php" class="btn btn-link btn-sm p-0 mt-2">
                            <i class="fas fa-plus me-1"></i>إضافة تصنيف جديد
                        </a>
                    </div>
                </div>

                <!-- Featured Image -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">الصورة البارزة</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($article['featured_image'])): ?>
                        <img src="/uploads/articles/<?= $article['featured_image'] ?>" class="img-fluid rounded mb-3" alt="">
                        <?php endif; ?>
                        <input type="file" name="featured_image" class="form-control" accept="image/*">
                        <small class="text-muted">الحجم المثالي: 1200x630 بكسل</small>
                    </div>
                </div>

                <!-- SEO Score -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>تقييم SEO</h5>
                    </div>
                    <div class="card-body" id="seo-checklist">
                        <div class="seo-check" data-check="title">
                            <i class="fas fa-circle text-muted me-2"></i>
                            <span>عنوان SEO محدد</span>
                        </div>
                        <div class="seo-check" data-check="description">
                            <i class="fas fa-circle text-muted me-2"></i>
                            <span>وصف SEO محدد</span>
                        </div>
                        <div class="seo-check" data-check="focus">
                            <i class="fas fa-circle text-muted me-2"></i>
                            <span>كلمة مفتاحية رئيسية</span>
                        </div>
                        <div class="seo-check" data-check="image">
                            <i class="fas fa-circle text-muted me-2"></i>
                            <span>صورة بارزة</span>
                        </div>
                        <div class="seo-check" data-check="content">
                            <i class="fas fa-circle text-muted me-2"></i>
                            <span>محتوى أكثر من 300 كلمة</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function updateSeoPreview() {
    const title = document.getElementById('meta_title').value || document.getElementById('title').value || 'عنوان المقال';
    const desc = document.getElementById('meta_description').value || document.getElementById('excerpt').value || 'وصف المقال سيظهر هنا...';

    document.getElementById('seo-title-preview').textContent = title.substring(0, 70);
    document.getElementById('seo-desc-preview').textContent = desc.substring(0, 160);

    // Update counters
    updateCounter('meta_title', 'title-counter', 70);
    updateCounter('meta_description', 'desc-counter', 160);
}

function updateCounter(inputId, counterId, max) {
    const input = document.getElementById(inputId);
    const counter = document.getElementById(counterId);
    const len = input.value.length;

    counter.textContent = len + '/' + max;
    counter.className = 'char-counter';

    if (len > max * 0.9) counter.classList.add('danger');
    else if (len > max * 0.7) counter.classList.add('warning');
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    updateSeoPreview();
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
