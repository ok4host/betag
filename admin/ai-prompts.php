<?php
/**
 * AI Prompts Management
 */
require_once '../includes/functions.php';
require_once '../includes/ai-service.php';
requireAdmin();

$ai = new AIService();
$message = '';
$error = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $data = [
            'id' => $_POST['id'] ?? null,
            'name' => sanitize($_POST['name']),
            'slug' => sanitize($_POST['slug']) ?: createSlug($_POST['name']),
            'category' => $_POST['category'],
            'prompt_template' => $_POST['prompt_template'],
            'system_instructions' => $_POST['system_instructions'] ?? '',
            'variables' => array_filter(array_map('trim', explode(',', $_POST['variables'] ?? ''))),
            'model' => $_POST['model'] ?? 'gpt-4',
            'temperature' => (float)($_POST['temperature'] ?? 0.7),
            'max_tokens' => (int)($_POST['max_tokens'] ?? 1000),
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ];

        if ($ai->savePrompt($data)) {
            $message = 'تم حفظ البرومبت بنجاح';
        } else {
            $error = 'حدث خطأ أثناء الحفظ';
        }
    } elseif ($action === 'delete' && !empty($_POST['id'])) {
        if ($ai->deletePrompt($_POST['id'])) {
            $message = 'تم حذف البرومبت';
        } else {
            $error = 'حدث خطأ أثناء الحذف';
        }
    } elseif ($action === 'test' && !empty($_POST['prompt_id'])) {
        $testVars = json_decode($_POST['test_variables'] ?? '{}', true);
        $prompt = $ai->getPrompt($_POST['prompt_id']);
        if ($prompt) {
            $result = $ai->generate($prompt['slug'], $testVars);
            if ($result['success']) {
                $testResult = $result['content'];
            } else {
                $error = $result['error'];
            }
        }
    }
}

// Get all prompts
$prompts = $ai->getAllPrompts();
$editPrompt = null;
if (!empty($_GET['edit'])) {
    foreach ($prompts as $p) {
        if ($p['id'] == $_GET['edit']) {
            $editPrompt = $p;
            break;
        }
    }
}

$categories = [
    'property_description' => 'وصف العقار',
    'seo_meta' => 'SEO Meta Tags',
    'blog_post' => 'مقال مدونة',
    'area_guide' => 'دليل المنطقة',
    'compound_description' => 'وصف الكمبوند',
    'email' => 'إيميل',
    'social_post' => 'منشور سوشيال',
    'custom' => 'مخصص'
];

$pageTitle = 'إدارة البرومبتات AI';
require_once 'includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3"><i class="fas fa-robot me-2"></i>إدارة البرومبتات AI</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#promptModal">
            <i class="fas fa-plus me-1"></i>إضافة برومبت جديد
        </button>
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

    <!-- Prompts List -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="promptsTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الاسم</th>
                            <th>التصنيف</th>
                            <th>النموذج</th>
                            <th>الاستخدام</th>
                            <th>الحالة</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($prompts as $prompt): ?>
                        <tr>
                            <td><?= $prompt['id'] ?></td>
                            <td>
                                <strong><?= htmlspecialchars($prompt['name']) ?></strong>
                                <br><small class="text-muted"><?= $prompt['slug'] ?></small>
                            </td>
                            <td><span class="badge bg-info"><?= $categories[$prompt['category']] ?? $prompt['category'] ?></span></td>
                            <td><?= $prompt['model'] ?></td>
                            <td><span class="badge bg-secondary"><?= number_format($prompt['usage_count']) ?></span></td>
                            <td>
                                <?php if ($prompt['is_active']): ?>
                                <span class="badge bg-success">نشط</span>
                                <?php else: ?>
                                <span class="badge bg-danger">معطل</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" onclick="editPrompt(<?= htmlspecialchars(json_encode($prompt)) ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-success" onclick="testPrompt(<?= $prompt['id'] ?>, '<?= $prompt['slug'] ?>')">
                                        <i class="fas fa-play"></i>
                                    </button>
                                    <button class="btn btn-outline-danger" onclick="deletePrompt(<?= $prompt['id'] ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Prompt Modal -->
<div class="modal fade" id="promptModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="save">
                <input type="hidden" name="id" id="promptId">

                <div class="modal-header">
                    <h5 class="modal-title" id="promptModalTitle">إضافة برومبت جديد</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">الاسم *</label>
                            <input type="text" name="name" id="promptName" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Slug</label>
                            <input type="text" name="slug" id="promptSlug" class="form-control" placeholder="يتم إنشاؤه تلقائياً">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">التصنيف *</label>
                            <select name="category" id="promptCategory" class="form-select" required>
                                <?php foreach ($categories as $key => $label): ?>
                                <option value="<?= $key ?>"><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">النموذج</label>
                            <select name="model" id="promptModel" class="form-select">
                                <option value="gpt-4">GPT-4</option>
                                <option value="gpt-3.5-turbo">GPT-3.5 Turbo</option>
                                <option value="claude-3-opus-20240229">Claude 3 Opus</option>
                                <option value="claude-3-sonnet-20240229">Claude 3 Sonnet</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Temperature</label>
                            <input type="number" name="temperature" id="promptTemp" class="form-control" step="0.1" min="0" max="2" value="0.7">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Max Tokens</label>
                            <input type="number" name="max_tokens" id="promptTokens" class="form-control" value="1000">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">تعليمات النظام (System Instructions)</label>
                        <textarea name="system_instructions" id="promptSystem" class="form-control" rows="3" placeholder="التعليمات الأساسية للـ AI"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">قالب البرومبت *</label>
                        <textarea name="prompt_template" id="promptTemplate" class="form-control" rows="8" required placeholder="استخدم {variable_name} للمتغيرات"></textarea>
                        <small class="text-muted">استخدم {variable_name} لإضافة متغيرات مثل {property_type}, {location}, {price}</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">المتغيرات المتاحة (مفصولة بفاصلة)</label>
                        <input type="text" name="variables" id="promptVars" class="form-control" placeholder="property_type, location, price, area">
                    </div>

                    <div class="form-check">
                        <input type="checkbox" name="is_active" id="promptActive" class="form-check-input" checked>
                        <label class="form-check-label" for="promptActive">نشط</label>
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

<!-- Test Modal -->
<div class="modal fade" id="testModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="test">
                <input type="hidden" name="prompt_id" id="testPromptId">

                <div class="modal-header">
                    <h5 class="modal-title">اختبار البرومبت</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">المتغيرات (JSON)</label>
                        <textarea name="test_variables" id="testVariables" class="form-control" rows="5">{}</textarea>
                    </div>

                    <div id="testResult" class="d-none">
                        <label class="form-label">النتيجة:</label>
                        <div class="bg-light p-3 rounded" id="testResultContent"></div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-play me-1"></i>تشغيل
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Form -->
<form id="deleteForm" method="POST" class="d-none">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="id" id="deleteId">
</form>

<script>
function editPrompt(prompt) {
    document.getElementById('promptModalTitle').textContent = 'تعديل البرومبت';
    document.getElementById('promptId').value = prompt.id;
    document.getElementById('promptName').value = prompt.name;
    document.getElementById('promptSlug').value = prompt.slug;
    document.getElementById('promptCategory').value = prompt.category;
    document.getElementById('promptModel').value = prompt.model;
    document.getElementById('promptTemp').value = prompt.temperature;
    document.getElementById('promptTokens').value = prompt.max_tokens;
    document.getElementById('promptSystem').value = prompt.system_instructions || '';
    document.getElementById('promptTemplate').value = prompt.prompt_template;
    document.getElementById('promptVars').value = JSON.parse(prompt.variables || '[]').join(', ');
    document.getElementById('promptActive').checked = prompt.is_active == 1;

    new bootstrap.Modal(document.getElementById('promptModal')).show();
}

function testPrompt(id, slug) {
    document.getElementById('testPromptId').value = id;
    document.getElementById('testVariables').value = '{\n  "property_type": "شقة",\n  "location": "التجمع الخامس",\n  "area": "150",\n  "price": "2,500,000"\n}';
    document.getElementById('testResult').classList.add('d-none');
    new bootstrap.Modal(document.getElementById('testModal')).show();
}

function deletePrompt(id) {
    if (confirm('هل أنت متأكد من حذف هذا البرومبت؟')) {
        document.getElementById('deleteId').value = id;
        document.getElementById('deleteForm').submit();
    }
}

// Reset modal on close
document.getElementById('promptModal').addEventListener('hidden.bs.modal', function() {
    document.getElementById('promptModalTitle').textContent = 'إضافة برومبت جديد';
    this.querySelector('form').reset();
    document.getElementById('promptId').value = '';
});

$(document).ready(function() {
    $('#promptsTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/ar.json' },
        order: [[0, 'desc']]
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
