<?php
/**
 * Data Scraping & Import Management
 */
require_once '../includes/functions.php';
require_once '../includes/scraping-service.php';
requireAdmin();

$scraper = new ScrapingService();
$message = '';
$error = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'import_csv':
            if (!empty($_FILES['csv_file']['tmp_name'])) {
                $sourceId = (int)$_POST['source_id'];
                $result = $scraper->importCSV($_FILES['csv_file']['tmp_name'], $sourceId);
                if ($result['success']) {
                    $message = "تم استيراد {$result['imported']} سجل بنجاح";
                    if ($result['failed'] > 0) {
                        $message .= " ({$result['failed']} فشل)";
                    }
                } else {
                    $error = $result['error'];
                }
            }
            break;

        case 'save_source':
            $data = [
                'id' => $_POST['id'] ?? null,
                'name' => sanitize($_POST['name']),
                'slug' => sanitize($_POST['slug']) ?: createSlug($_POST['name']),
                'base_url' => $_POST['base_url'],
                'source_type' => $_POST['source_type'],
                'rate_limit' => (int)$_POST['rate_limit'],
                'is_active' => isset($_POST['is_active']) ? 1 : 0,
                'scraping_config' => json_decode($_POST['scraping_config'] ?? '{}', true),
                'headers' => json_decode($_POST['headers'] ?? '{}', true)
            ];

            if ($scraper->saveSource($data)) {
                $message = 'تم حفظ المصدر بنجاح';
            } else {
                $error = 'حدث خطأ أثناء الحفظ';
            }
            break;

        case 'process':
            $dataIds = $_POST['data_ids'] ?? [];
            $generateContent = isset($_POST['generate_content']);
            $userId = $_SESSION['user_id'];

            if (!empty($dataIds)) {
                $result = $scraper->bulkProcess($dataIds, $userId, $generateContent);
                $message = "تم معالجة {$result['success']} سجل بنجاح";
                if ($result['failed'] > 0) {
                    $message .= " ({$result['failed']} فشل)";
                }
            }
            break;

        case 'delete_data':
            if (!empty($_POST['id'])) {
                $scraper->deleteScrapedData($_POST['id']);
                $message = 'تم حذف السجل';
            }
            break;
    }
}

$sources = $scraper->getSources(false);
$stats = $scraper->getStats();
$scrapedData = $scraper->getScrapedData(null, $_GET['status'] ?? null, 100);

$pageTitle = 'استيراد البيانات';
require_once 'includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3"><i class="fas fa-database me-2"></i>استيراد البيانات</h1>
        <div>
            <button class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="fas fa-file-csv me-1"></i>استيراد CSV
            </button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#sourceModal">
                <i class="fas fa-plus me-1"></i>إضافة مصدر
            </button>
        </div>
    </div>

    <?php if ($message): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= $message ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?= $error ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h2><?= number_format($stats['total_scraped']) ?></h2>
                    <small>إجمالي البيانات</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body text-center">
                    <h2><?= number_format($stats['by_status']['pending'] ?? 0) ?></h2>
                    <small>في الانتظار</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h2><?= number_format($stats['by_status']['imported'] ?? 0) ?></h2>
                    <small>تم استيرادها</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <h2><?= number_format($stats['by_status']['failed'] ?? 0) ?></h2>
                    <small>فشلت</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Sources -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-server me-2"></i>مصادر البيانات</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <?php foreach ($sources as $source): ?>
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <h6><?= htmlspecialchars($source['name']) ?></h6>
                                <span class="badge bg-<?= $source['is_active'] ? 'success' : 'secondary' ?>">
                                    <?= $source['is_active'] ? 'نشط' : 'معطل' ?>
                                </span>
                            </div>
                            <small class="text-muted"><?= $source['source_type'] ?></small>
                            <p class="small mb-2"><?= htmlspecialchars(substr($source['base_url'], 0, 40)) ?>...</p>
                            <small class="text-muted">
                                إجمالي: <?= number_format($source['total_items_scraped']) ?>
                            </small>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Scraped Data -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-table me-2"></i>البيانات المستوردة</h5>
            <div>
                <select class="form-select form-select-sm d-inline-block w-auto" onchange="location.href='?status='+this.value">
                    <option value="">كل الحالات</option>
                    <option value="pending" <?= ($_GET['status'] ?? '') === 'pending' ? 'selected' : '' ?>>في الانتظار</option>
                    <option value="imported" <?= ($_GET['status'] ?? '') === 'imported' ? 'selected' : '' ?>>تم استيرادها</option>
                    <option value="failed" <?= ($_GET['status'] ?? '') === 'failed' ? 'selected' : '' ?>>فشلت</option>
                </select>
            </div>
        </div>
        <div class="card-body">
            <form method="POST" id="processForm">
                <input type="hidden" name="action" value="process">

                <div class="mb-3">
                    <div class="form-check form-check-inline">
                        <input type="checkbox" name="generate_content" id="generateContent" class="form-check-input">
                        <label class="form-check-label" for="generateContent">توليد محتوى بالـ AI</label>
                    </div>
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="fas fa-cog me-1"></i>معالجة المحدد
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover" id="dataTable">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="selectAll"></th>
                                <th>#</th>
                                <th>المصدر</th>
                                <th>البيانات</th>
                                <th>الحالة</th>
                                <th>التاريخ</th>
                                <th>إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($scrapedData as $data): ?>
                            <?php $parsed = json_decode($data['parsed_data'] ?: '{}', true); ?>
                            <tr>
                                <td>
                                    <?php if ($data['status'] === 'pending'): ?>
                                    <input type="checkbox" name="data_ids[]" value="<?= $data['id'] ?>">
                                    <?php endif; ?>
                                </td>
                                <td><?= $data['id'] ?></td>
                                <td><?= htmlspecialchars($data['source_name'] ?? '') ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($parsed['title'] ?? 'بدون عنوان') ?></strong>
                                    <br>
                                    <small class="text-muted">
                                        <?= $parsed['location'] ?? '' ?>
                                        <?php if (!empty($parsed['price'])): ?>
                                        - <?= number_format($parsed['price']) ?> جنيه
                                        <?php endif; ?>
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-<?php
                                        echo match($data['status']) {
                                            'pending' => 'warning',
                                            'imported' => 'success',
                                            'failed' => 'danger',
                                            'duplicate' => 'secondary',
                                            default => 'info'
                                        };
                                    ?>"><?= $data['status'] ?></span>
                                </td>
                                <td><?= date('Y/m/d H:i', strtotime($data['scraped_at'])) ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-info" onclick="viewData(<?= htmlspecialchars(json_encode($data)) ?>)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" onclick="deleteData(<?= $data['id'] ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="import_csv">

                <div class="modal-header">
                    <h5 class="modal-title">استيراد ملف CSV</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">المصدر</label>
                        <select name="source_id" class="form-select" required>
                            <?php foreach ($sources as $source): ?>
                            <option value="<?= $source['id'] ?>"><?= htmlspecialchars($source['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">ملف CSV</label>
                        <input type="file" name="csv_file" class="form-control" accept=".csv" required>
                        <small class="text-muted">الصف الأول يجب أن يحتوي على أسماء الأعمدة</small>
                    </div>

                    <div class="alert alert-info">
                        <strong>الأعمدة المدعومة:</strong><br>
                        title, description, price, area, bedrooms, bathrooms, location, property_type, transaction_type, phone, images
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-upload me-1"></i>استيراد
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Source Modal -->
<div class="modal fade" id="sourceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="save_source">
                <input type="hidden" name="id" id="sourceId">

                <div class="modal-header">
                    <h5 class="modal-title">إضافة مصدر</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">الاسم</label>
                        <input type="text" name="name" id="sourceName" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Slug</label>
                        <input type="text" name="slug" id="sourceSlug" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">URL</label>
                        <input type="text" name="base_url" id="sourceUrl" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">النوع</label>
                        <select name="source_type" id="sourceType" class="form-select">
                            <option value="website">Website</option>
                            <option value="api">API</option>
                            <option value="rss">RSS Feed</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Rate Limit (requests/min)</label>
                        <input type="number" name="rate_limit" id="sourceRate" class="form-control" value="5">
                    </div>

                    <div class="form-check">
                        <input type="checkbox" name="is_active" id="sourceActive" class="form-check-input" checked>
                        <label class="form-check-label" for="sourceActive">نشط</label>
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

<!-- View Data Modal -->
<div class="modal fade" id="viewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تفاصيل البيانات</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="viewContent"></div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Form -->
<form id="deleteForm" method="POST" class="d-none">
    <input type="hidden" name="action" value="delete_data">
    <input type="hidden" name="id" id="deleteId">
</form>

<script>
document.getElementById('selectAll').addEventListener('change', function() {
    document.querySelectorAll('input[name="data_ids[]"]').forEach(cb => cb.checked = this.checked);
});

function viewData(data) {
    const parsed = JSON.parse(data.parsed_data || '{}');
    const raw = JSON.parse(data.raw_data || '{}');

    let html = '<h6>البيانات المحللة:</h6><pre class="bg-light p-3">' + JSON.stringify(parsed, null, 2) + '</pre>';
    html += '<h6>البيانات الخام:</h6><pre class="bg-light p-3" style="max-height:300px;overflow:auto">' + JSON.stringify(raw, null, 2) + '</pre>';

    if (data.error_message) {
        html += '<div class="alert alert-danger mt-3">' + data.error_message + '</div>';
    }

    document.getElementById('viewContent').innerHTML = html;
    new bootstrap.Modal(document.getElementById('viewModal')).show();
}

function deleteData(id) {
    if (confirm('هل أنت متأكد من حذف هذا السجل؟')) {
        document.getElementById('deleteId').value = id;
        document.getElementById('deleteForm').submit();
    }
}

$(document).ready(function() {
    $('#dataTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/ar.json' },
        order: [[1, 'desc']],
        pageLength: 25
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
