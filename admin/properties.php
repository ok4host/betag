<?php
/**
 * Admin - Properties Management
 */
require_once __DIR__ . '/../includes/functions.php';
startSession();
requireAdmin();

// Handle actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $action = $_GET['action'];

    switch ($action) {
        case 'approve':
            db()->prepare("UPDATE properties SET status = 'active', published_at = NOW() WHERE id = ?")->execute([$id]);
            $_SESSION['flash_message'] = 'تم تفعيل العقار بنجاح';
            break;
        case 'reject':
            db()->prepare("UPDATE properties SET status = 'rejected' WHERE id = ?")->execute([$id]);
            $_SESSION['flash_message'] = 'تم رفض العقار';
            break;
        case 'feature':
            db()->prepare("UPDATE properties SET is_featured = 1 WHERE id = ?")->execute([$id]);
            $_SESSION['flash_message'] = 'تم تمييز العقار';
            break;
        case 'unfeature':
            db()->prepare("UPDATE properties SET is_featured = 0 WHERE id = ?")->execute([$id]);
            $_SESSION['flash_message'] = 'تم إلغاء التمييز';
            break;
        case 'delete':
            db()->prepare("DELETE FROM properties WHERE id = ?")->execute([$id]);
            $_SESSION['flash_message'] = 'تم حذف العقار';
            $_SESSION['flash_type'] = 'warning';
            break;
    }
    redirect('properties.php');
}

// Filter
$where = "1=1";
$params = [];

if (!empty($_GET['status'])) {
    $where .= " AND p.status = ?";
    $params[] = $_GET['status'];
}

if (!empty($_GET['type'])) {
    $where .= " AND p.transaction_type = ?";
    $params[] = $_GET['type'];
}

// Get properties
$sql = "SELECT p.*, c.name_ar as category_name, l.name_ar as location_name, u.name as owner_name, u.phone as owner_phone
        FROM properties p
        JOIN categories c ON p.category_id = c.id
        JOIN locations l ON p.location_id = l.id
        JOIN users u ON p.user_id = u.id
        WHERE $where
        ORDER BY p.created_at DESC";

$stmt = db()->prepare($sql);
$stmt->execute($params);
$properties = $stmt->fetchAll();

$pageTitle = 'إدارة العقارات';
include __DIR__ . '/includes/header.php';
?>

<div class="container-fluid py-4">
    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">الحالة</label>
                    <select name="status" class="form-select">
                        <option value="">الكل</option>
                        <option value="pending" <?= ($_GET['status'] ?? '') === 'pending' ? 'selected' : '' ?>>معلق</option>
                        <option value="active" <?= ($_GET['status'] ?? '') === 'active' ? 'selected' : '' ?>>نشط</option>
                        <option value="rejected" <?= ($_GET['status'] ?? '') === 'rejected' ? 'selected' : '' ?>>مرفوض</option>
                        <option value="sold" <?= ($_GET['status'] ?? '') === 'sold' ? 'selected' : '' ?>>مباع</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">النوع</label>
                    <select name="type" class="form-select">
                        <option value="">الكل</option>
                        <option value="sale" <?= ($_GET['type'] ?? '') === 'sale' ? 'selected' : '' ?>>بيع</option>
                        <option value="rent" <?= ($_GET['type'] ?? '') === 'rent' ? 'selected' : '' ?>>إيجار</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-1"></i> تصفية
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Properties Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">العقارات (<?= count($properties) ?>)</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover datatable mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>العنوان</th>
                            <th>النوع</th>
                            <th>الموقع</th>
                            <th>السعر</th>
                            <th>المالك</th>
                            <th>الحالة</th>
                            <th>المشاهدات</th>
                            <th>التاريخ</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($properties as $prop): ?>
                        <tr>
                            <td><?= $prop['id'] ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <?php if ($prop['featured_image']): ?>
                                    <img src="/uploads/properties/<?= $prop['featured_image'] ?>" alt="" width="50" height="40" class="rounded object-fit-cover">
                                    <?php endif; ?>
                                    <div>
                                        <a href="property-edit.php?id=<?= $prop['id'] ?>" class="text-decoration-none fw-bold">
                                            <?= mb_substr(htmlspecialchars($prop['title']), 0, 40) ?>
                                        </a>
                                        <?php if ($prop['is_featured']): ?>
                                        <span class="badge bg-warning text-dark"><i class="fas fa-star"></i></span>
                                        <?php endif; ?>
                                        <br>
                                        <small class="text-muted"><?= $prop['category_name'] ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-<?= $prop['transaction_type'] === 'sale' ? 'success' : 'info' ?>">
                                    <?= $prop['transaction_type'] === 'sale' ? 'بيع' : 'إيجار' ?>
                                </span>
                            </td>
                            <td><?= $prop['location_name'] ?></td>
                            <td class="fw-bold"><?= formatPrice($prop['price']) ?></td>
                            <td>
                                <span title="<?= $prop['owner_phone'] ?>"><?= $prop['owner_name'] ?></span>
                            </td>
                            <td>
                                <?php
                                $statusClass = ['pending' => 'warning', 'active' => 'success', 'rejected' => 'danger', 'sold' => 'secondary'];
                                $statusText = ['pending' => 'معلق', 'active' => 'نشط', 'rejected' => 'مرفوض', 'sold' => 'مباع'];
                                ?>
                                <span class="badge bg-<?= $statusClass[$prop['status']] ?? 'secondary' ?>">
                                    <?= $statusText[$prop['status']] ?? $prop['status'] ?>
                                </span>
                            </td>
                            <td><?= number_format($prop['views']) ?></td>
                            <td><?= formatDate($prop['created_at']) ?></td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                        إجراءات
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="property-edit.php?id=<?= $prop['id'] ?>"><i class="fas fa-edit me-2"></i>تعديل</a></li>
                                        <li><a class="dropdown-item" href="/property/<?= $prop['slug'] ?>" target="_blank"><i class="fas fa-eye me-2"></i>عرض</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <?php if ($prop['status'] === 'pending'): ?>
                                        <li><a class="dropdown-item text-success" href="?action=approve&id=<?= $prop['id'] ?>"><i class="fas fa-check me-2"></i>قبول</a></li>
                                        <li><a class="dropdown-item text-danger" href="?action=reject&id=<?= $prop['id'] ?>"><i class="fas fa-times me-2"></i>رفض</a></li>
                                        <?php endif; ?>
                                        <?php if ($prop['is_featured']): ?>
                                        <li><a class="dropdown-item" href="?action=unfeature&id=<?= $prop['id'] ?>"><i class="fas fa-star me-2"></i>إلغاء التمييز</a></li>
                                        <?php else: ?>
                                        <li><a class="dropdown-item" href="?action=feature&id=<?= $prop['id'] ?>"><i class="far fa-star me-2"></i>تمييز</a></li>
                                        <?php endif; ?>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-danger" href="#" onclick="confirmDelete('?action=delete&id=<?= $prop['id'] ?>', '<?= addslashes($prop['title']) ?>')"><i class="fas fa-trash me-2"></i>حذف</a></li>
                                    </ul>
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

<?php include __DIR__ . '/includes/footer.php'; ?>
