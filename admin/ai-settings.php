<?php
/**
 * AI Settings Management
 */
require_once '../includes/functions.php';
require_once '../includes/ai-service.php';
requireAdmin();

$ai = new AIService();
$message = '';
$error = '';

// Handle save
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'provider' => $_POST['provider'],
        'api_key' => $_POST['api_key'],
        'model' => $_POST['model'],
        'endpoint' => $_POST['endpoint'] ?? null,
        'default_temperature' => (float)$_POST['default_temperature'],
        'max_tokens_per_request' => (int)$_POST['max_tokens_per_request'],
        'monthly_budget' => !empty($_POST['monthly_budget']) ? (float)$_POST['monthly_budget'] : null,
        'is_active' => isset($_POST['is_active']) ? 1 : 0
    ];

    if ($ai->updateSettings($data)) {
        $message = 'تم حفظ الإعدادات بنجاح';
    } else {
        $error = 'حدث خطأ أثناء الحفظ';
    }
}

$settings = $ai->getSettings();
$contentHistory = $ai->getContentHistory(null, null, 20);

$pageTitle = 'إعدادات AI';
require_once 'includes/header.php';
?>

<div class="container-fluid py-4">
    <h1 class="h3 mb-4"><i class="fas fa-cog me-2"></i>إعدادات AI</h1>

    <?php if ($message): ?>
    <div class="alert alert-success"><?= $message ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-key me-2"></i>إعدادات API</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">مزود الخدمة</label>
                            <select name="provider" class="form-select">
                                <option value="openai" <?= ($settings['provider'] ?? '') === 'openai' ? 'selected' : '' ?>>OpenAI (GPT)</option>
                                <option value="anthropic" <?= ($settings['provider'] ?? '') === 'anthropic' ? 'selected' : '' ?>>Anthropic (Claude)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">API Key</label>
                            <input type="password" name="api_key" class="form-control" value="<?= htmlspecialchars($settings['api_key'] ?? '') ?>" placeholder="sk-...">
                            <small class="text-muted">مفتاح API الخاص بك</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">النموذج</label>
                            <select name="model" class="form-select" id="modelSelect">
                                <optgroup label="OpenAI">
                                    <option value="gpt-4" <?= ($settings['model'] ?? '') === 'gpt-4' ? 'selected' : '' ?>>GPT-4</option>
                                    <option value="gpt-4-turbo" <?= ($settings['model'] ?? '') === 'gpt-4-turbo' ? 'selected' : '' ?>>GPT-4 Turbo</option>
                                    <option value="gpt-3.5-turbo" <?= ($settings['model'] ?? '') === 'gpt-3.5-turbo' ? 'selected' : '' ?>>GPT-3.5 Turbo</option>
                                </optgroup>
                                <optgroup label="Anthropic">
                                    <option value="claude-3-opus-20240229" <?= ($settings['model'] ?? '') === 'claude-3-opus-20240229' ? 'selected' : '' ?>>Claude 3 Opus</option>
                                    <option value="claude-3-sonnet-20240229" <?= ($settings['model'] ?? '') === 'claude-3-sonnet-20240229' ? 'selected' : '' ?>>Claude 3 Sonnet</option>
                                </optgroup>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Endpoint مخصص (اختياري)</label>
                            <input type="url" name="endpoint" class="form-control" value="<?= htmlspecialchars($settings['endpoint'] ?? '') ?>" placeholder="https://api.openai.com/v1/chat/completions">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Temperature الافتراضي</label>
                                <input type="number" name="default_temperature" class="form-control" step="0.1" min="0" max="2" value="<?= $settings['default_temperature'] ?? 0.7 ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Max Tokens</label>
                                <input type="number" name="max_tokens_per_request" class="form-control" value="<?= $settings['max_tokens_per_request'] ?? 2000 ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">الميزانية الشهرية ($)</label>
                            <input type="number" name="monthly_budget" class="form-control" step="0.01" value="<?= $settings['monthly_budget'] ?? '' ?>" placeholder="اختياري">
                        </div>

                        <div class="form-check mb-3">
                            <input type="checkbox" name="is_active" class="form-check-input" id="isActive" <?= ($settings['is_active'] ?? 1) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="isActive">تفعيل خدمة AI</label>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>حفظ الإعدادات
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <!-- Usage Stats -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>إحصائيات الاستخدام</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="h2 text-primary mb-0">
                                <?= number_format($settings['current_month_usage'] ?? 0, 2) ?>$
                            </div>
                            <small class="text-muted">استخدام الشهر</small>
                        </div>
                        <div class="col-4">
                            <div class="h2 text-success mb-0">
                                <?= count($contentHistory) ?>
                            </div>
                            <small class="text-muted">محتوى مولد</small>
                        </div>
                        <div class="col-4">
                            <div class="h2 text-info mb-0">
                                <?= number_format($settings['monthly_budget'] ?? 0, 2) ?>$
                            </div>
                            <small class="text-muted">الميزانية</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Content -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>آخر المحتوى المولد</h5>
                    <a href="ai-content.php" class="btn btn-sm btn-outline-primary">عرض الكل</a>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    <?php if (empty($contentHistory)): ?>
                    <p class="text-muted text-center">لا يوجد محتوى مولد بعد</p>
                    <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($contentHistory as $content): ?>
                        <div class="list-group-item px-0">
                            <div class="d-flex justify-content-between">
                                <small class="text-muted"><?= $content['prompt_name'] ?? 'Custom' ?></small>
                                <small class="text-muted"><?= date('Y/m/d H:i', strtotime($content['created_at'])) ?></small>
                            </div>
                            <p class="mb-1 small"><?= mb_substr(strip_tags($content['generated_content']), 0, 100) ?>...</p>
                            <span class="badge bg-<?= $content['status'] === 'approved' ? 'success' : 'secondary' ?>"><?= $content['status'] ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
