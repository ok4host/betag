<?php
/**
 * Profile Page - صفحة الملف الشخصي
 */

$pageTitle = $currentLang === 'ar' ? 'الملف الشخصي | بي تاج' : 'Profile | BeTaj';
$pageDescription = $currentLang === 'ar' ? 'إدارة معلومات حسابك في بي تاج' : 'Manage your BeTaj account information';

require_once __DIR__ . '/../includes/header.php';

// Require login
if (!isLoggedIn()) {
    header('Location: /' . $currentLang . '/login?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

// Translations
$t = [
    'ar' => [
        'title' => 'الملف الشخصي',
        'personal_info' => 'المعلومات الشخصية',
        'name' => 'الاسم الكامل',
        'email' => 'البريد الإلكتروني',
        'phone' => 'رقم الهاتف',
        'save' => 'حفظ التغييرات',
        'change_password' => 'تغيير كلمة المرور',
        'current_password' => 'كلمة المرور الحالية',
        'new_password' => 'كلمة المرور الجديدة',
        'confirm_password' => 'تأكيد كلمة المرور',
        'update_password' => 'تحديث كلمة المرور',
        'account_settings' => 'إعدادات الحساب',
        'notifications' => 'الإشعارات',
        'email_notifications' => 'إشعارات البريد الإلكتروني',
        'email_notifications_desc' => 'استلم تحديثات عن عقاراتك واستفسارات العملاء',
        'sms_notifications' => 'إشعارات الرسائل النصية',
        'sms_notifications_desc' => 'استلم رسائل نصية للاستفسارات المهمة',
        'newsletter' => 'النشرة البريدية',
        'newsletter_desc' => 'استلم آخر العروض والأخبار العقارية',
        'danger_zone' => 'منطقة الخطر',
        'delete_account' => 'حذف الحساب',
        'delete_account_desc' => 'بمجرد حذف حسابك، لا يمكن استرجاعه',
        'delete_btn' => 'حذف حسابي',
        'confirm_delete' => 'هل أنت متأكد من حذف حسابك؟ هذا الإجراء لا يمكن التراجع عنه.',
        'updated' => 'تم حفظ التغييرات بنجاح',
        'password_updated' => 'تم تحديث كلمة المرور بنجاح',
        'error_password' => 'كلمة المرور الحالية غير صحيحة',
        'error_match' => 'كلمات المرور غير متطابقة',
    ],
    'en' => [
        'title' => 'Profile',
        'personal_info' => 'Personal Information',
        'name' => 'Full Name',
        'email' => 'Email',
        'phone' => 'Phone Number',
        'save' => 'Save Changes',
        'change_password' => 'Change Password',
        'current_password' => 'Current Password',
        'new_password' => 'New Password',
        'confirm_password' => 'Confirm Password',
        'update_password' => 'Update Password',
        'account_settings' => 'Account Settings',
        'notifications' => 'Notifications',
        'email_notifications' => 'Email Notifications',
        'email_notifications_desc' => 'Receive updates about your properties and client inquiries',
        'sms_notifications' => 'SMS Notifications',
        'sms_notifications_desc' => 'Receive text messages for important inquiries',
        'newsletter' => 'Newsletter',
        'newsletter_desc' => 'Receive latest offers and real estate news',
        'danger_zone' => 'Danger Zone',
        'delete_account' => 'Delete Account',
        'delete_account_desc' => 'Once you delete your account, there is no going back',
        'delete_btn' => 'Delete My Account',
        'confirm_delete' => 'Are you sure you want to delete your account? This action cannot be undone.',
        'updated' => 'Changes saved successfully',
        'password_updated' => 'Password updated successfully',
        'error_password' => 'Current password is incorrect',
        'error_match' => 'Passwords do not match',
    ]
];

$text = $t[$currentLang];

// Get user data
$pdo = Database::getInstance()->getConnection();
$user = [];

try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $user = [
        'name' => $_SESSION['user_name'] ?? '',
        'email' => $_SESSION['user_email'] ?? '',
        'phone' => ''
    ];
}

$message = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_profile') {
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);

        try {
            $stmt = $pdo->prepare("UPDATE users SET name = ?, phone = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$name, $phone, $_SESSION['user_id']]);
            $_SESSION['user_name'] = $name;
            $user['name'] = $name;
            $user['phone'] = $phone;
            $message = $text['updated'];
        } catch (Exception $e) {
            $error = __('error');
        }
    } elseif ($action === 'update_password') {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if ($newPassword !== $confirmPassword) {
            $error = $text['error_match'];
        } elseif (!password_verify($currentPassword, $user['password'] ?? '')) {
            $error = $text['error_password'];
        } else {
            try {
                $stmt = $pdo->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?");
                $stmt->execute([password_hash($newPassword, PASSWORD_DEFAULT), $_SESSION['user_id']]);
                $message = $text['password_updated'];
            } catch (Exception $e) {
                $error = __('error');
            }
        }
    }
}
?>

    <section class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-8"><?= $text['title'] ?></h1>

        <?php if ($message): ?>
        <div class="bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded-xl mb-6">
            <i class="fa-solid fa-check-circle <?= $isRTL ? 'ml-2' : 'mr-2' ?>"></i>
            <?= $message ?>
        </div>
        <?php endif; ?>

        <?php if ($error): ?>
        <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl mb-6">
            <i class="fa-solid fa-exclamation-circle <?= $isRTL ? 'ml-2' : 'mr-2' ?>"></i>
            <?= $error ?>
        </div>
        <?php endif; ?>

        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Personal Info -->
                <div class="bg-white rounded-2xl shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-6"><?= $text['personal_info'] ?></h2>
                    <form method="POST" class="space-y-4">
                        <input type="hidden" name="action" value="update_profile">

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2"><?= $text['name'] ?></label>
                            <input type="text" name="name" value="<?= htmlspecialchars($user['name'] ?? '') ?>" required
                                class="w-full border-2 border-gray-200 rounded-xl p-3 focus:border-primary-500 outline-none transition">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2"><?= $text['email'] ?></label>
                            <input type="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" disabled
                                class="w-full border-2 border-gray-200 rounded-xl p-3 bg-gray-50 text-gray-500" dir="ltr">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2"><?= $text['phone'] ?></label>
                            <input type="tel" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                                class="w-full border-2 border-gray-200 rounded-xl p-3 focus:border-primary-500 outline-none transition" dir="ltr">
                        </div>

                        <button type="submit" class="bg-primary-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-primary-700 transition">
                            <?= $text['save'] ?>
                        </button>
                    </form>
                </div>

                <!-- Change Password -->
                <div class="bg-white rounded-2xl shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-6"><?= $text['change_password'] ?></h2>
                    <form method="POST" class="space-y-4">
                        <input type="hidden" name="action" value="update_password">

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2"><?= $text['current_password'] ?></label>
                            <input type="password" name="current_password" required
                                class="w-full border-2 border-gray-200 rounded-xl p-3 focus:border-primary-500 outline-none transition">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2"><?= $text['new_password'] ?></label>
                            <input type="password" name="new_password" required minlength="8"
                                class="w-full border-2 border-gray-200 rounded-xl p-3 focus:border-primary-500 outline-none transition">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2"><?= $text['confirm_password'] ?></label>
                            <input type="password" name="confirm_password" required minlength="8"
                                class="w-full border-2 border-gray-200 rounded-xl p-3 focus:border-primary-500 outline-none transition">
                        </div>

                        <button type="submit" class="bg-primary-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-primary-700 transition">
                            <?= $text['update_password'] ?>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Notifications -->
                <div class="bg-white rounded-2xl shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-6"><?= $text['notifications'] ?></h2>
                    <div class="space-y-4">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" checked class="rounded border-gray-300 mt-1">
                            <div>
                                <p class="font-medium text-gray-900"><?= $text['email_notifications'] ?></p>
                                <p class="text-sm text-gray-500"><?= $text['email_notifications_desc'] ?></p>
                            </div>
                        </label>

                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" class="rounded border-gray-300 mt-1">
                            <div>
                                <p class="font-medium text-gray-900"><?= $text['sms_notifications'] ?></p>
                                <p class="text-sm text-gray-500"><?= $text['sms_notifications_desc'] ?></p>
                            </div>
                        </label>

                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" checked class="rounded border-gray-300 mt-1">
                            <div>
                                <p class="font-medium text-gray-900"><?= $text['newsletter'] ?></p>
                                <p class="text-sm text-gray-500"><?= $text['newsletter_desc'] ?></p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Danger Zone -->
                <div class="bg-white rounded-2xl shadow-md p-6 border-2 border-red-100">
                    <h2 class="text-xl font-bold text-red-600 mb-4"><?= $text['danger_zone'] ?></h2>
                    <p class="text-gray-600 mb-4"><?= $text['delete_account_desc'] ?></p>
                    <button onclick="confirmDeleteAccount()" class="w-full bg-red-600 text-white py-3 rounded-xl font-bold hover:bg-red-700 transition">
                        <?= $text['delete_btn'] ?>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <script>
        function confirmDeleteAccount() {
            if (confirm('<?= $text['confirm_delete'] ?>')) {
                // Delete account logic
                fetch('/api/users?action=delete', { method: 'DELETE' })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            window.location.href = '/<?= $currentLang ?>/';
                        } else {
                            showToast(result.message || '<?= __('error') ?>', 'error');
                        }
                    });
            }
        }
    </script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
