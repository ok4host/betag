<?php
/**
 * Admin - General Settings
 */
require_once __DIR__ . '/../includes/functions.php';
startSession();
requireAdmin();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'setting_') === 0) {
            $settingKey = str_replace('setting_', '', $key);
            $stmt = db()->prepare("UPDATE settings SET value = ? WHERE `key` = ?");
            $stmt->execute([sanitize($value), $settingKey]);
        }
    }
    $_SESSION['flash_message'] = 'تم حفظ الإعدادات بنجاح';
    redirect('settings.php');
}

$settings = getSettings();

$pageTitle = 'الإعدادات العامة';
include __DIR__ . '/includes/header.php';
?>

<div class="container-fluid py-4">
    <form method="POST">
        <div class="row g-4">
            <!-- Site Info -->
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-globe me-2"></i>معلومات الموقع</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">اسم الموقع</label>
                            <input type="text" name="setting_site_name" value="<?= htmlspecialchars($settings['site_name'] ?? '') ?>" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">الشعار النصي (Tagline)</label>
                            <input type="text" name="setting_site_tagline" value="<?= htmlspecialchars($settings['site_tagline'] ?? '') ?>" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">العنوان</label>
                            <input type="text" name="setting_address" value="<?= htmlspecialchars($settings['address'] ?? '') ?>" class="form-control">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-phone me-2"></i>معلومات التواصل</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">رقم الهاتف</label>
                            <input type="text" name="setting_contact_phone" value="<?= htmlspecialchars($settings['contact_phone'] ?? '') ?>" class="form-control" dir="ltr">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">رقم الواتساب (مع كود الدولة)</label>
                            <input type="text" name="setting_contact_whatsapp" value="<?= htmlspecialchars($settings['contact_whatsapp'] ?? '') ?>" class="form-control" dir="ltr" placeholder="201xxxxxxxxx">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">البريد الإلكتروني</label>
                            <input type="email" name="setting_contact_email" value="<?= htmlspecialchars($settings['contact_email'] ?? '') ?>" class="form-control" dir="ltr">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Social Media -->
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-share-alt me-2"></i>السوشيال ميديا</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label"><i class="fab fa-facebook text-primary me-1"></i> Facebook</label>
                            <input type="url" name="setting_facebook_url" value="<?= htmlspecialchars($settings['facebook_url'] ?? '') ?>" class="form-control" dir="ltr" placeholder="https://facebook.com/...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="fab fa-instagram text-danger me-1"></i> Instagram</label>
                            <input type="url" name="setting_instagram_url" value="<?= htmlspecialchars($settings['instagram_url'] ?? '') ?>" class="form-control" dir="ltr" placeholder="https://instagram.com/...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="fab fa-twitter text-info me-1"></i> Twitter</label>
                            <input type="url" name="setting_twitter_url" value="<?= htmlspecialchars($settings['twitter_url'] ?? '') ?>" class="form-control" dir="ltr" placeholder="https://twitter.com/...">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tracking & Analytics -->
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>أكواد التتبع</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Google Analytics ID</label>
                            <input type="text" name="setting_google_analytics_id" value="<?= htmlspecialchars($settings['google_analytics_id'] ?? '') ?>" class="form-control" dir="ltr" placeholder="G-XXXXXXXXXX">
                            <small class="text-muted">مثال: G-XXXXXXXXXX أو UA-XXXXXXXX-X</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Facebook Pixel ID</label>
                            <input type="text" name="setting_facebook_pixel_id" value="<?= htmlspecialchars($settings['facebook_pixel_id'] ?? '') ?>" class="form-control" dir="ltr" placeholder="XXXXXXXXXXXXXXXX">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Google Maps API Key</label>
                            <input type="text" name="setting_google_maps_api_key" value="<?= htmlspecialchars($settings['google_maps_api_key'] ?? '') ?>" class="form-control" dir="ltr">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-save me-2"></i> حفظ الإعدادات
            </button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
