<?php
/**
 * Admin - Users Management
 */
require_once __DIR__ . '/../includes/functions.php';
startSession();
requireAdmin();

$message = '';
$error = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $name = sanitize($_POST['name']);
        $email = sanitize($_POST['email']);
        $phone = sanitize($_POST['phone']);
        $password = $_POST['password'];
        $role = $_POST['role'];

        if ($name && $phone && $password) {
            $stmt = db()->prepare("INSERT INTO users (name, email, phone, password, role, is_active, is_verified) VALUES (?, ?, ?, ?, ?, 1, 1)");
            if ($stmt->execute([$name, $email ?: null, $phone, hashPassword($password), $role])) {
                $message = 'تم إضافة المستخدم بنجاح';
            } else {
                $error = 'حدث خطأ أثناء الإضافة';
            }
        }
    } elseif ($action === 'delete' && isset($_POST['id'])) {
        $stmt = db()->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
        $stmt->execute([$_POST['id']]);
        $message = 'تم حذف المستخدم';
    } elseif ($action === 'toggle' && isset($_POST['id'])) {
        $stmt = db()->prepare("UPDATE users SET is_active = NOT is_active WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        $message = 'تم تحديث الحالة';
    }
}

// Get users
$users = db()->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();

$pageTitle = 'إدارة المستخدمين';
include __DIR__ . '/includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3"><i class="fas fa-users me-2"></i>إدارة المستخدمين</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="fas fa-plus me-1"></i>إضافة مستخدم
        </button>
    </div>

    <?php if ($message): ?>
    <div class="alert alert-success"><?= $message ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <table class="table table-hover datatable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>الهاتف</th>
                        <th>البريد</th>
                        <th>الدور</th>
                        <th>الحالة</th>
                        <th>التاريخ</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= $user['id'] ?></td>
                        <td><?= htmlspecialchars($user['name']) ?></td>
                        <td dir="ltr"><?= $user['phone'] ?></td>
                        <td><?= $user['email'] ?: '-' ?></td>
                        <td>
                            <span class="badge bg-<?= $user['role'] === 'admin' ? 'danger' : ($user['role'] === 'broker' ? 'warning' : 'info') ?>">
                                <?= $user['role'] === 'admin' ? 'مدير' : ($user['role'] === 'broker' ? 'وسيط' : 'مستخدم') ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-<?= $user['is_active'] ? 'success' : 'secondary' ?>">
                                <?= $user['is_active'] ? 'نشط' : 'معطل' ?>
                            </span>
                        </td>
                        <td><?= formatDate($user['created_at']) ?></td>
                        <td>
                            <?php if ($user['role'] !== 'admin'): ?>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="action" value="toggle">
                                <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-warning">
                                    <i class="fas fa-toggle-<?= $user['is_active'] ? 'on' : 'off' ?>"></i>
                                </button>
                            </form>
                            <form method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد؟')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div class="modal-header">
                    <h5 class="modal-title">إضافة مستخدم جديد</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">الاسم *</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">رقم الهاتف *</label>
                        <input type="tel" name="phone" class="form-control" required dir="ltr">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">البريد الإلكتروني</label>
                        <input type="email" name="email" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">كلمة المرور *</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الدور</label>
                        <select name="role" class="form-select">
                            <option value="user">مستخدم</option>
                            <option value="broker">وسيط</option>
                            <option value="admin">مدير</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">إضافة</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
