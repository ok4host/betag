<?php
/**
 * Admin - SEO Settings
 */
require_once __DIR__ . '/../includes/functions.php';
startSession();
requireAdmin();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pageType = sanitize($_POST['page_type']);
    $metaTitle = sanitize($_POST['meta_title']);
    $metaDesc = sanitize($_POST['meta_description']);
    $metaKeywords = sanitize($_POST['meta_keywords']);
    $ogImage = sanitize($_POST['og_image']);

    $sql = "INSERT INTO seo_settings (page_type, meta_title, meta_description, meta_keywords, og_image)
            VALUES (?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
            meta_title = VALUES(meta_title),
            meta_description = VALUES(meta_description),
            meta_keywords = VALUES(meta_keywords),
            og_image = VALUES(og_image)";

    db()->prepare($sql)->execute([$pageType, $metaTitle, $metaDesc, $metaKeywords, $ogImage]);

    $_SESSION['flash_message'] = 'تم حفظ إعدادات SEO بنجاح';
    redirect('seo.php');
}

// Get all SEO settings
$seoSettings = db()->query("SELECT * FROM seo_settings ORDER BY page_type")->fetchAll();

// Page types
$pageTypes = [
    'home' => 'الصفحة الرئيسية',
    'search' => 'صفحة البحث',
    'search_sale' => 'عقارات للبيع',
    'search_rent' => 'عقارات للإيجار',
    'property' => 'تفاصيل العقار (افتراضي)',
    'compounds' => 'دليل الكمبوندات',
    'new-projects' => 'مشاريع جديدة',
    'about' => 'من نحن',
    'contact' => 'اتصل بنا',
];

$pageTitle = 'إعدادات SEO';
include __DIR__ . '/includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-8">
            <!-- SEO Settings List -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">إعدادات SEO للصفحات</h5>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addSeoModal">
                        <i class="fas fa-plus me-1"></i> إضافة صفحة
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>الصفحة</th>
                                    <th>العنوان (Title)</th>
                                    <th>الوصف</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($seoSettings as $seo): ?>
                                <tr>
                                    <td>
                                        <strong><?= $pageTypes[$seo['page_type']] ?? $seo['page_type'] ?></strong>
                                        <br><small class="text-muted"><?= $seo['page_type'] ?></small>
                                    </td>
                                    <td>
                                        <span class="<?= strlen($seo['meta_title']) > 60 ? 'text-danger' : '' ?>">
                                            <?= mb_substr($seo['meta_title'], 0, 50) ?>...
                                        </span>
                                        <br><small class="text-muted"><?= strlen($seo['meta_title']) ?>/60</small>
                                    </td>
                                    <td>
                                        <span class="<?= strlen($seo['meta_description']) > 160 ? 'text-danger' : '' ?>">
                                            <?= mb_substr($seo['meta_description'], 0, 60) ?>...
                                        </span>
                                        <br><small class="text-muted"><?= strlen($seo['meta_description']) ?>/160</small>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="editSeo(<?= htmlspecialchars(json_encode($seo)) ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- SEO Tips -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-lightbulb text-warning me-2"></i>نصائح SEO</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3">
                            <strong class="text-primary">العنوان (Title):</strong>
                            <br>50-60 حرف كحد أقصى. يجب أن يحتوي على الكلمة المفتاحية الرئيسية.
                        </li>
                        <li class="mb-3">
                            <strong class="text-primary">الوصف (Description):</strong>
                            <br>150-160 حرف كحد أقصى. وصف جذاب يحث على النقر.
                        </li>
                        <li class="mb-3">
                            <strong class="text-primary">الكلمات المفتاحية:</strong>
                            <br>5-10 كلمات مفتاحية مرتبطة بالمحتوى.
                        </li>
                        <li>
                            <strong class="text-primary">صورة OG:</strong>
                            <br>1200x630 بكسل للمشاركة على السوشيال ميديا.
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Tracking Codes -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-code me-2"></i>أكواد التتبع</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small">يمكنك إضافة أكواد التتبع من صفحة الإعدادات العامة:</p>
                    <ul class="mb-0">
                        <li>Google Analytics ID</li>
                        <li>Facebook Pixel ID</li>
                        <li>Google Tag Manager</li>
                    </ul>
                    <a href="settings.php" class="btn btn-outline-primary btn-sm mt-3">
                        <i class="fas fa-cog me-1"></i> الإعدادات
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit SEO Modal -->
<div class="modal fade" id="addSeoModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">إعدادات SEO</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">الصفحة</label>
                        <select name="page_type" id="seo_page_type" class="form-select" required>
                            <option value="">اختر الصفحة</option>
                            <?php foreach ($pageTypes as $key => $label): ?>
                            <option value="<?= $key ?>"><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">العنوان (Meta Title)</label>
                        <input type="text" name="meta_title" id="seo_meta_title" class="form-control" maxlength="70" required>
                        <small class="text-muted">الطول المثالي: 50-60 حرف</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الوصف (Meta Description)</label>
                        <textarea name="meta_description" id="seo_meta_description" class="form-control" rows="3" maxlength="200" required></textarea>
                        <small class="text-muted">الطول المثالي: 150-160 حرف</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الكلمات المفتاحية (Keywords)</label>
                        <input type="text" name="meta_keywords" id="seo_meta_keywords" class="form-control" placeholder="كلمة1، كلمة2، كلمة3">
                        <small class="text-muted">افصل بين الكلمات بفاصلة</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">صورة OG (للسوشيال ميديا)</label>
                        <input type="url" name="og_image" id="seo_og_image" class="form-control" placeholder="https://example.com/image.jpg">
                    </div>

                    <!-- Preview -->
                    <div class="p-3 bg-light rounded">
                        <p class="mb-1 text-muted small">معاينة نتيجة البحث:</p>
                        <div id="seo_preview" class="bg-white p-3 rounded border">
                            <div class="text-primary" style="font-size: 18px" id="preview_title">عنوان الصفحة</div>
                            <div class="text-success small" id="preview_url">https://betag.com/page</div>
                            <div class="text-muted small" id="preview_desc">وصف الصفحة سيظهر هنا...</div>
                        </div>
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
function editSeo(data) {
    document.getElementById('seo_page_type').value = data.page_type;
    document.getElementById('seo_meta_title').value = data.meta_title || '';
    document.getElementById('seo_meta_description').value = data.meta_description || '';
    document.getElementById('seo_meta_keywords').value = data.meta_keywords || '';
    document.getElementById('seo_og_image').value = data.og_image || '';
    updatePreview();
    new bootstrap.Modal(document.getElementById('addSeoModal')).show();
}

function updatePreview() {
    document.getElementById('preview_title').textContent = document.getElementById('seo_meta_title').value || 'عنوان الصفحة';
    document.getElementById('preview_desc').textContent = document.getElementById('seo_meta_description').value || 'وصف الصفحة سيظهر هنا...';
}

document.getElementById('seo_meta_title').addEventListener('input', updatePreview);
document.getElementById('seo_meta_description').addEventListener('input', updatePreview);
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
