<?php
/**
 * Login Page - صفحة تسجيل الدخول
 */

$pageTitle = $currentLang === 'ar' ? 'تسجيل الدخول | بي تاج' : 'Login | BeTaj';
$pageDescription = $currentLang === 'ar' ? 'سجل دخولك للوصول إلى حسابك في بي تاج' : 'Login to access your BeTaj account';

require_once __DIR__ . '/../includes/header.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: /' . $currentLang . '/');
    exit;
}

// Translations
$t = [
    'ar' => [
        'title' => 'تسجيل الدخول',
        'subtitle' => 'أهلاً بعودتك! سجل دخولك للمتابعة',
        'email' => 'البريد الإلكتروني',
        'email_placeholder' => 'أدخل بريدك الإلكتروني',
        'password' => 'كلمة المرور',
        'password_placeholder' => 'أدخل كلمة المرور',
        'remember' => 'تذكرني',
        'forgot' => 'نسيت كلمة المرور؟',
        'login_btn' => 'تسجيل الدخول',
        'no_account' => 'ليس لديك حساب؟',
        'register' => 'إنشاء حساب جديد',
        'or' => 'أو',
        'login_google' => 'تسجيل الدخول بجوجل',
        'login_facebook' => 'تسجيل الدخول بفيسبوك',
        'error_invalid' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة',
        'success' => 'تم تسجيل الدخول بنجاح',
    ],
    'en' => [
        'title' => 'Login',
        'subtitle' => 'Welcome back! Login to continue',
        'email' => 'Email',
        'email_placeholder' => 'Enter your email',
        'password' => 'Password',
        'password_placeholder' => 'Enter your password',
        'remember' => 'Remember me',
        'forgot' => 'Forgot password?',
        'login_btn' => 'Login',
        'no_account' => 'Don\'t have an account?',
        'register' => 'Create new account',
        'or' => 'or',
        'login_google' => 'Login with Google',
        'login_facebook' => 'Login with Facebook',
        'error_invalid' => 'Invalid email or password',
        'success' => 'Logged in successfully',
    ]
];

$text = $t[$currentLang];
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';

    if ($email && $password) {
        try {
            $pdo = Database::getInstance()->getConnection();
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND status = 'active'");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];

                header('Location: /' . $currentLang . '/');
                exit;
            } else {
                $error = $text['error_invalid'];
            }
        } catch (Exception $e) {
            $error = $text['error_invalid'];
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

                <form method="POST" class="space-y-5">
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
                        <label class="block text-gray-700 font-semibold mb-2"><?= $text['password'] ?></label>
                        <div class="relative">
                            <i class="fa-solid fa-lock absolute top-1/2 -translate-y-1/2 <?= $isRTL ? 'right-4' : 'left-4' ?> text-gray-400"></i>
                            <input type="password" name="password" required
                                placeholder="<?= $text['password_placeholder'] ?>"
                                class="w-full border-2 border-gray-200 rounded-xl p-3 <?= $isRTL ? 'pr-12' : 'pl-12' ?> focus:border-primary-500 outline-none transition">
                            <button type="button" onclick="togglePassword(this)" class="absolute top-1/2 -translate-y-1/2 <?= $isRTL ? 'left-4' : 'right-4' ?> text-gray-400 hover:text-gray-600">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember" class="rounded border-gray-300">
                            <span class="text-sm text-gray-600"><?= $text['remember'] ?></span>
                        </label>
                        <a href="/<?= $currentLang ?>/forgot-password" class="text-sm text-primary-600 hover:underline">
                            <?= $text['forgot'] ?>
                        </a>
                    </div>

                    <button type="submit" class="w-full bg-primary-600 text-white py-3 rounded-xl font-bold hover:bg-primary-700 transition">
                        <?= $text['login_btn'] ?>
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

                <!-- Register Link -->
                <p class="text-center mt-6 text-gray-500">
                    <?= $text['no_account'] ?>
                    <a href="/<?= $currentLang ?>/register" class="text-primary-600 font-semibold hover:underline">
                        <?= $text['register'] ?>
                    </a>
                </p>
            </div>
        </div>
    </section>

    <script>
        function togglePassword(btn) {
            const input = btn.previousElementSibling;
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
