<?php
/**
 * Register Page - صفحة إنشاء حساب
 */

$pageTitle = $currentLang === 'ar' ? 'إنشاء حساب | بي تاج' : 'Register | BeTaj';
$pageDescription = $currentLang === 'ar' ? 'أنشئ حسابك في بي تاج للوصول إلى جميع المميزات' : 'Create your BeTaj account to access all features';

require_once __DIR__ . '/../includes/header.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: /' . $currentLang . '/');
    exit;
}

// Translations
$t = [
    'ar' => [
        'title' => 'إنشاء حساب جديد',
        'subtitle' => 'انضم إلينا وابدأ رحلة البحث عن عقارك المثالي',
        'name' => 'الاسم الكامل',
        'name_placeholder' => 'أدخل اسمك الكامل',
        'email' => 'البريد الإلكتروني',
        'email_placeholder' => 'أدخل بريدك الإلكتروني',
        'phone' => 'رقم الهاتف',
        'phone_placeholder' => 'أدخل رقم هاتفك',
        'password' => 'كلمة المرور',
        'password_placeholder' => 'أدخل كلمة المرور',
        'confirm_password' => 'تأكيد كلمة المرور',
        'confirm_placeholder' => 'أعد إدخال كلمة المرور',
        'terms' => 'أوافق على',
        'terms_link' => 'الشروط والأحكام',
        'privacy_link' => 'سياسة الخصوصية',
        'and' => 'و',
        'register_btn' => 'إنشاء حساب',
        'have_account' => 'لديك حساب بالفعل؟',
        'login' => 'تسجيل الدخول',
        'or' => 'أو',
        'error_email' => 'البريد الإلكتروني مسجل بالفعل',
        'error_password' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
        'error_match' => 'كلمات المرور غير متطابقة',
        'success' => 'تم إنشاء حسابك بنجاح',
    ],
    'en' => [
        'title' => 'Create New Account',
        'subtitle' => 'Join us and start your journey to find your ideal property',
        'name' => 'Full Name',
        'name_placeholder' => 'Enter your full name',
        'email' => 'Email',
        'email_placeholder' => 'Enter your email',
        'phone' => 'Phone Number',
        'phone_placeholder' => 'Enter your phone number',
        'password' => 'Password',
        'password_placeholder' => 'Enter password',
        'confirm_password' => 'Confirm Password',
        'confirm_placeholder' => 'Re-enter password',
        'terms' => 'I agree to the',
        'terms_link' => 'Terms & Conditions',
        'privacy_link' => 'Privacy Policy',
        'and' => 'and',
        'register_btn' => 'Create Account',
        'have_account' => 'Already have an account?',
        'login' => 'Login',
        'or' => 'or',
        'error_email' => 'Email is already registered',
        'error_password' => 'Password must be at least 8 characters',
        'error_match' => 'Passwords do not match',
        'success' => 'Account created successfully',
    ]
];

$text = $t[$currentLang];
$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (strlen($password) < 8) {
        $error = $text['error_password'];
    } elseif ($password !== $confirmPassword) {
        $error = $text['error_match'];
    } else {
        try {
            $pdo = Database::getInstance()->getConnection();

            // Check if email exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = $text['error_email'];
            } else {
                // Create user
                $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password, role, status, created_at) VALUES (?, ?, ?, ?, 'user', 'active', NOW())");
                $stmt->execute([$name, $email, $phone, password_hash($password, PASSWORD_DEFAULT)]);

                $userId = $pdo->lastInsertId();
                $_SESSION['user_id'] = $userId;
                $_SESSION['user_name'] = $name;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_role'] = 'user';

                header('Location: /' . $currentLang . '/');
                exit;
            }
        } catch (Exception $e) {
            $error = $text['error_email'];
        }
    }
}
?>

    <section class="min-h-[80vh] flex items-center justify-center py-12 px-4">
        <div class="max-w-md w-full">
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <!-- Logo -->
                <div class="text-center mb-8">
                    <a href="/<?= $currentLang ?>/" class="inline-flex items-center gap-2 mb-4">
                        <div class="bg-primary-600 p-2 rounded-lg text-white">
                            <i class="fa-solid fa-house text-xl"></i>
                        </div>
                        <span class="text-2xl font-bold text-primary-900"><?= __('site_name') ?></span>
                    </a>
                    <h1 class="text-2xl font-bold text-gray-900"><?= $text['title'] ?></h1>
                    <p class="text-gray-500 mt-2"><?= $text['subtitle'] ?></p>
                </div>

                <?php if ($error): ?>
                <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl mb-6">
                    <i class="fa-solid fa-exclamation-circle <?= $isRTL ? 'ml-2' : 'mr-2' ?>"></i>
                    <?= $error ?>
                </div>
                <?php endif; ?>

                <form method="POST" class="space-y-4">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2"><?= $text['name'] ?></label>
                        <div class="relative">
                            <i class="fa-solid fa-user absolute top-1/2 -translate-y-1/2 <?= $isRTL ? 'right-4' : 'left-4' ?> text-gray-400"></i>
                            <input type="text" name="name" required
                                placeholder="<?= $text['name_placeholder'] ?>"
                                class="w-full border-2 border-gray-200 rounded-xl p-3 <?= $isRTL ? 'pr-12' : 'pl-12' ?> focus:border-primary-500 outline-none transition">
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-2"><?= $text['email'] ?></label>
                        <div class="relative">
                            <i class="fa-solid fa-envelope absolute top-1/2 -translate-y-1/2 <?= $isRTL ? 'right-4' : 'left-4' ?> text-gray-400"></i>
                            <input type="email" name="email" required
                                placeholder="<?= $text['email_placeholder'] ?>"
                                class="w-full border-2 border-gray-200 rounded-xl p-3 <?= $isRTL ? 'pr-12' : 'pl-12' ?> focus:border-primary-500 outline-none transition"
                                dir="ltr">
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-2"><?= $text['phone'] ?></label>
                        <div class="relative">
                            <i class="fa-solid fa-phone absolute top-1/2 -translate-y-1/2 <?= $isRTL ? 'right-4' : 'left-4' ?> text-gray-400"></i>
                            <input type="tel" name="phone"
                                placeholder="<?= $text['phone_placeholder'] ?>"
                                class="w-full border-2 border-gray-200 rounded-xl p-3 <?= $isRTL ? 'pr-12' : 'pl-12' ?> focus:border-primary-500 outline-none transition"
                                dir="ltr">
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-2"><?= $text['password'] ?></label>
                        <div class="relative">
                            <i class="fa-solid fa-lock absolute top-1/2 -translate-y-1/2 <?= $isRTL ? 'right-4' : 'left-4' ?> text-gray-400"></i>
                            <input type="password" name="password" required minlength="8"
                                placeholder="<?= $text['password_placeholder'] ?>"
                                class="w-full border-2 border-gray-200 rounded-xl p-3 <?= $isRTL ? 'pr-12' : 'pl-12' ?> focus:border-primary-500 outline-none transition">
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-2"><?= $text['confirm_password'] ?></label>
                        <div class="relative">
                            <i class="fa-solid fa-lock absolute top-1/2 -translate-y-1/2 <?= $isRTL ? 'right-4' : 'left-4' ?> text-gray-400"></i>
                            <input type="password" name="confirm_password" required minlength="8"
                                placeholder="<?= $text['confirm_placeholder'] ?>"
                                class="w-full border-2 border-gray-200 rounded-xl p-3 <?= $isRTL ? 'pr-12' : 'pl-12' ?> focus:border-primary-500 outline-none transition">
                        </div>
                    </div>

                    <div class="flex items-start gap-2">
                        <input type="checkbox" name="terms" required class="rounded border-gray-300 mt-1">
                        <span class="text-sm text-gray-600">
                            <?= $text['terms'] ?>
                            <a href="/<?= $currentLang ?>/terms" class="text-primary-600 hover:underline"><?= $text['terms_link'] ?></a>
                            <?= $text['and'] ?>
                            <a href="/<?= $currentLang ?>/privacy" class="text-primary-600 hover:underline"><?= $text['privacy_link'] ?></a>
                        </span>
                    </div>

                    <button type="submit" class="w-full bg-primary-600 text-white py-3 rounded-xl font-bold hover:bg-primary-700 transition">
                        <?= $text['register_btn'] ?>
                    </button>
                </form>

                <!-- Social Login -->
                <div class="mt-6">
                    <div class="relative">
                        <hr class="border-gray-200">
                        <span class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white px-4 text-gray-400 text-sm">
                            <?= $text['or'] ?>
                        </span>
                    </div>

                    <div class="mt-6 grid grid-cols-2 gap-3">
                        <button class="flex items-center justify-center gap-2 border-2 border-gray-200 rounded-xl py-3 hover:bg-gray-50 transition">
                            <i class="fa-brands fa-google text-red-500"></i>
                            <span class="text-sm font-medium">Google</span>
                        </button>
                        <button class="flex items-center justify-center gap-2 border-2 border-gray-200 rounded-xl py-3 hover:bg-gray-50 transition">
                            <i class="fa-brands fa-facebook text-blue-600"></i>
                            <span class="text-sm font-medium">Facebook</span>
                        </button>
                    </div>
                </div>

                <!-- Login Link -->
                <p class="text-center mt-6 text-gray-500">
                    <?= $text['have_account'] ?>
                    <a href="/<?= $currentLang ?>/login" class="text-primary-600 font-semibold hover:underline">
                        <?= $text['login'] ?>
                    </a>
                </p>
            </div>
        </div>
    </section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
