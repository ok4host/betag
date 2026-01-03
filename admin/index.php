<?php
/**
 * Admin Dashboard
 */
require_once __DIR__ . '/../includes/functions.php';
startSession();
requireAdmin();

// Get stats
$stats = [
    'properties' => db()->query("SELECT COUNT(*) FROM properties")->fetchColumn(),
    'active_properties' => db()->query("SELECT COUNT(*) FROM properties WHERE status = 'active'")->fetchColumn(),
    'pending_properties' => db()->query("SELECT COUNT(*) FROM properties WHERE status = 'pending'")->fetchColumn(),
    'users' => db()->query("SELECT COUNT(*) FROM users WHERE role != 'admin'")->fetchColumn(),
    'leads' => db()->query("SELECT COUNT(*) FROM leads")->fetchColumn(),
    'new_leads' => db()->query("SELECT COUNT(*) FROM leads WHERE status = 'new'")->fetchColumn(),
    'views_today' => db()->query("SELECT COALESCE(SUM(views), 0) FROM properties WHERE DATE(updated_at) = CURDATE()")->fetchColumn(),
];

// Recent leads
$recentLeads = db()->query("SELECT * FROM leads ORDER BY created_at DESC LIMIT 5")->fetchAll();

// Recent properties
$recentProperties = db()->query("
    SELECT p.*, u.name as owner_name
    FROM properties p
    JOIN users u ON p.user_id = u.id
    ORDER BY p.created_at DESC LIMIT 5
")->fetchAll();

$pageTitle = 'لوحة التحكم';
include __DIR__ . '/includes/header.php';
?>

<div class="container-fluid py-4">
    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">العقارات</h6>
                            <h2 class="mb-0"><?= $stats['properties'] ?></h2>
                        </div>
                        <i class="fas fa-building fa-2x opacity-50"></i>
                    </div>
                    <small><?= $stats['pending_properties'] ?> في انتظار المراجعة</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">الطلبات</h6>
                            <h2 class="mb-0"><?= $stats['leads'] ?></h2>
                        </div>
                        <i class="fas fa-envelope fa-2x opacity-50"></i>
                    </div>
                    <small class="text-warning"><?= $stats['new_leads'] ?> طلب جديد</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">المستخدمين</h6>
                            <h2 class="mb-0"><?= $stats['users'] ?></h2>
                        </div>
                        <i class="fas fa-users fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">المشاهدات اليوم</h6>
                            <h2 class="mb-0"><?= $stats['views_today'] ?></h2>
                        </div>
                        <i class="fas fa-eye fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Recent Leads -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">أحدث الطلبات</h5>
                    <a href="leads.php" class="btn btn-sm btn-primary">عرض الكل</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>الاسم</th>
                                    <th>الهاتف</th>
                                    <th>الحالة</th>
                                    <th>التاريخ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentLeads as $lead): ?>
                                <tr>
                                    <td><?= htmlspecialchars($lead['name']) ?></td>
                                    <td dir="ltr"><?= $lead['phone'] ?></td>
                                    <td>
                                        <span class="badge bg-<?= $lead['status'] === 'new' ? 'danger' : 'secondary' ?>">
                                            <?= $lead['status'] === 'new' ? 'جديد' : $lead['status'] ?>
                                        </span>
                                    </td>
                                    <td><?= timeAgo($lead['created_at']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($recentLeads)): ?>
                                <tr><td colspan="4" class="text-center text-muted py-4">لا توجد طلبات</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Properties -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">أحدث العقارات</h5>
                    <a href="properties.php" class="btn btn-sm btn-primary">عرض الكل</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>العنوان</th>
                                    <th>المالك</th>
                                    <th>الحالة</th>
                                    <th>التاريخ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentProperties as $prop): ?>
                                <tr>
                                    <td>
                                        <a href="property-edit.php?id=<?= $prop['id'] ?>" class="text-decoration-none">
                                            <?= mb_substr(htmlspecialchars($prop['title']), 0, 30) ?>...
                                        </a>
                                    </td>
                                    <td><?= htmlspecialchars($prop['owner_name']) ?></td>
                                    <td>
                                        <?php
                                        $statusClass = ['pending' => 'warning', 'active' => 'success', 'rejected' => 'danger'];
                                        $statusText = ['pending' => 'معلق', 'active' => 'نشط', 'rejected' => 'مرفوض'];
                                        ?>
                                        <span class="badge bg-<?= $statusClass[$prop['status']] ?? 'secondary' ?>">
                                            <?= $statusText[$prop['status']] ?? $prop['status'] ?>
                                        </span>
                                    </td>
                                    <td><?= timeAgo($prop['created_at']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($recentProperties)): ?>
                                <tr><td colspan="4" class="text-center text-muted py-4">لا توجد عقارات</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
