<?php
/**
 * Admin - Leads Management
 */
require_once __DIR__ . '/../includes/functions.php';
startSession();
requireAdmin();

// Handle status update
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $action = $_GET['action'];

    if ($action === 'update_status' && isset($_GET['status'])) {
        $status = sanitize($_GET['status']);
        db()->prepare("UPDATE leads SET status = ? WHERE id = ?")->execute([$status, $id]);
        $_SESSION['flash_message'] = 'تم تحديث الحالة';
    } elseif ($action === 'delete') {
        db()->prepare("DELETE FROM leads WHERE id = ?")->execute([$id]);
        $_SESSION['flash_message'] = 'تم حذف الطلب';
        $_SESSION['flash_type'] = 'warning';
    }
    redirect('leads.php');
}

// Filter
$where = "1=1";
$params = [];

if (!empty($_GET['status'])) {
    $where .= " AND l.status = ?";
    $params[] = $_GET['status'];
}

// Get leads
$sql = "SELECT l.*, p.title as property_title
        FROM leads l
        LEFT JOIN properties p ON l.property_id = p.id
        WHERE $where
        ORDER BY l.created_at DESC";

$stmt = db()->prepare($sql);
$stmt->execute($params);
$leads = $stmt->fetchAll();

$pageTitle = 'إدارة الطلبات';
include __DIR__ . '/includes/header.php';
?>

<div class="container-fluid py-4">
    <!-- Stats -->
    <div class="row g-3 mb-4">
        <?php
        $statuses = [
            'new' => ['label' => 'جديد', 'color' => 'danger', 'icon' => 'envelope'],
            'contacted' => ['label' => 'تم التواصل', 'color' => 'info', 'icon' => 'phone'],
            'qualified' => ['label' => 'مؤهل', 'color' => 'warning', 'icon' => 'star'],
            'converted' => ['label' => 'تم التحويل', 'color' => 'success', 'icon' => 'check-circle'],
        ];
        foreach ($statuses as $key => $stat):
            $count = db()->prepare("SELECT COUNT(*) FROM leads WHERE status = ?");
            $count->execute([$key]);
        ?>
        <div class="col-md-3">
            <div class="card border-<?= $stat['color'] ?> h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-<?= $stat['color'] ?> mb-0"><?= $stat['label'] ?></h6>
                        <h3 class="mb-0"><?= $count->fetchColumn() ?></h3>
                    </div>
                    <i class="fas fa-<?= $stat['icon'] ?> fa-2x text-<?= $stat['color'] ?> opacity-50"></i>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label">الحالة</label>
                    <select name="status" class="form-select">
                        <option value="">الكل</option>
                        <?php foreach ($statuses as $key => $stat): ?>
                        <option value="<?= $key ?>" <?= ($_GET['status'] ?? '') === $key ? 'selected' : '' ?>><?= $stat['label'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">من تاريخ</label>
                    <input type="date" name="date_from" class="form-control" value="<?= $_GET['date_from'] ?? '' ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">إلى تاريخ</label>
                    <input type="date" name="date_to" class="form-control" value="<?= $_GET['date_to'] ?? '' ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-1"></i> تصفية
                    </button>
                </div>
                <div class="col-md-4 text-end">
                    <div class="btn-group">
                        <a href="export-leads.php?<?= http_build_query($_GET) ?>&format=csv" class="btn btn-success">
                            <i class="fas fa-file-csv me-1"></i> تصدير CSV
                        </a>
                        <a href="export-leads.php?<?= http_build_query($_GET) ?>&format=xlsx" class="btn btn-info">
                            <i class="fas fa-file-excel me-1"></i> تصدير Excel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Leads Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">الطلبات (<?= count($leads) ?>)</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover datatable mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>الاسم</th>
                            <th>الهاتف</th>
                            <th>البريد</th>
                            <th>الغرض</th>
                            <th>العقار</th>
                            <th>الحالة</th>
                            <th>التاريخ</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($leads as $lead): ?>
                        <tr class="<?= $lead['status'] === 'new' ? 'table-warning' : '' ?>">
                            <td><?= $lead['id'] ?></td>
                            <td>
                                <strong><?= htmlspecialchars($lead['name']) ?></strong>
                                <?php if ($lead['message']): ?>
                                <br><small class="text-muted"><?= mb_substr($lead['message'], 0, 50) ?>...</small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="tel:<?= $lead['phone'] ?>" class="text-decoration-none" dir="ltr">
                                    <i class="fas fa-phone me-1"></i><?= $lead['phone'] ?>
                                </a>
                                <br>
                                <a href="https://wa.me/<?= preg_replace('/^0/', '20', $lead['phone']) ?>" target="_blank" class="text-success">
                                    <i class="fab fa-whatsapp"></i> واتساب
                                </a>
                            </td>
                            <td><?= $lead['email'] ?: '-' ?></td>
                            <td>
                                <span class="badge bg-<?= $lead['purpose'] === 'buy' ? 'success' : 'info' ?>">
                                    <?= $lead['purpose'] === 'buy' ? 'شراء' : ($lead['purpose'] === 'rent' ? 'إيجار' : 'عام') ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($lead['property_title']): ?>
                                <a href="property-edit.php?id=<?= $lead['property_id'] ?>" class="text-decoration-none">
                                    <?= mb_substr($lead['property_title'], 0, 30) ?>
                                </a>
                                <?php else: ?>
                                <span class="text-muted">طلب عام</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-<?= $statuses[$lead['status']]['color'] ?? 'secondary' ?> dropdown-toggle" data-bs-toggle="dropdown">
                                        <?= $statuses[$lead['status']]['label'] ?? $lead['status'] ?>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <?php foreach ($statuses as $key => $stat): ?>
                                        <li><a class="dropdown-item" href="?action=update_status&id=<?= $lead['id'] ?>&status=<?= $key ?>"><?= $stat['label'] ?></a></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </td>
                            <td>
                                <?= formatDate($lead['created_at'], 'd/m/Y H:i') ?>
                                <br><small class="text-muted"><?= timeAgo($lead['created_at']) ?></small>
                            </td>
                            <td>
                                <a href="#" onclick="confirmDelete('?action=delete&id=<?= $lead['id'] ?>', '<?= addslashes($lead['name']) ?>')" class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
