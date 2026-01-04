<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'لوحة التحكم' ?> - بي تاج</title>

    <!-- Bootstrap RTL -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <style>
        * {
            font-family: 'Cairo', sans-serif;
        }
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #1e3a8a 0%, #1e40af 100%);
            position: fixed;
            width: 260px;
            z-index: 1000;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            border-radius: 8px;
            margin: 4px 12px;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.15);
            color: #fff;
        }
        .sidebar .nav-link i {
            width: 24px;
        }
        .main-content {
            margin-right: 260px;
        }
        .top-navbar {
            background: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 15px 25px;
        }
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .card-header {
            background: #fff;
            border-bottom: 1px solid #eee;
            padding: 15px 20px;
        }
        .table th {
            font-weight: 600;
            color: #666;
        }
        .btn {
            border-radius: 8px;
        }
        .badge {
            font-weight: 500;
            padding: 6px 10px;
        }

        @media (max-width: 991px) {
            .sidebar {
                transform: translateX(100%);
                transition: transform 0.3s;
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-content {
                margin-right: 0;
            }
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<nav class="sidebar d-flex flex-column">
    <div class="p-4 text-white text-center border-bottom border-white border-opacity-25">
        <i class="fas fa-house-chimney fa-2x mb-2"></i>
        <h5 class="mb-0">بي تاج</h5>
        <small class="opacity-75">لوحة التحكم</small>
    </div>

    <ul class="nav flex-column mt-3" style="overflow-y:auto; max-height:calc(100vh - 200px);">
        <li class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>" href="index.php">
                <i class="fas fa-home"></i> الرئيسية
            </a>
        </li>

        <li class="nav-item mt-2">
            <small class="text-white-50 px-4">العقارات</small>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'properties.php' ? 'active' : '' ?>" href="properties.php">
                <i class="fas fa-building"></i> جميع العقارات
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'property-add.php' ? 'active' : '' ?>" href="property-add.php">
                <i class="fas fa-plus-circle"></i> إضافة عقار
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'categories.php' ? 'active' : '' ?>" href="categories.php">
                <i class="fas fa-tags"></i> الفئات
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'locations.php' ? 'active' : '' ?>" href="locations.php">
                <i class="fas fa-map-marker-alt"></i> المناطق
            </a>
        </li>

        <li class="nav-item mt-2">
            <small class="text-white-50 px-4">العملاء</small>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'leads.php' ? 'active' : '' ?>" href="leads.php">
                <i class="fas fa-envelope"></i> الطلبات
                <?php
                $newLeads = db()->query("SELECT COUNT(*) FROM leads WHERE status = 'new'")->fetchColumn();
                if ($newLeads > 0):
                ?>
                <span class="badge bg-danger float-start"><?= $newLeads ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'users.php' ? 'active' : '' ?>" href="users.php">
                <i class="fas fa-users"></i> المستخدمين
            </a>
        </li>

        <li class="nav-item mt-2">
            <small class="text-white-50 px-4">المدونة</small>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'articles.php' ? 'active' : '' ?>" href="articles.php">
                <i class="fas fa-newspaper"></i> المقالات
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'article-add.php' ? 'active' : '' ?>" href="article-add.php">
                <i class="fas fa-pen"></i> مقال جديد
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'article-categories.php' ? 'active' : '' ?>" href="article-categories.php">
                <i class="fas fa-folder"></i> تصنيفات المقالات
            </a>
        </li>

        <li class="nav-item mt-2">
            <small class="text-white-50 px-4">أدوات المحتوى</small>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'ai-prompts.php' ? 'active' : '' ?>" href="ai-prompts.php">
                <i class="fas fa-robot"></i> بروميتات AI
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'scraping.php' ? 'active' : '' ?>" href="scraping.php">
                <i class="fas fa-file-import"></i> استيراد البيانات
            </a>
        </li>

        <li class="nav-item mt-2">
            <small class="text-white-50 px-4">الإعدادات</small>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'seo.php' ? 'active' : '' ?>" href="seo.php">
                <i class="fas fa-search"></i> إعدادات SEO
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'pages.php' ? 'active' : '' ?>" href="pages.php">
                <i class="fas fa-file-alt"></i> الصفحات
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'ai-settings.php' ? 'active' : '' ?>" href="ai-settings.php">
                <i class="fas fa-brain"></i> إعدادات AI
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'settings.php' ? 'active' : '' ?>" href="settings.php">
                <i class="fas fa-cog"></i> الإعدادات العامة
            </a>
        </li>
    </ul>

    <div class="mt-auto p-3 border-top border-white border-opacity-25">
        <a href="logout.php" class="nav-link text-white-50">
            <i class="fas fa-sign-out-alt"></i> تسجيل الخروج
        </a>
    </div>
</nav>

<!-- Main Content -->
<div class="main-content">
    <!-- Top Navbar -->
    <div class="top-navbar d-flex justify-content-between align-items-center">
        <div>
            <button class="btn btn-link d-lg-none" onclick="document.querySelector('.sidebar').classList.toggle('show')">
                <i class="fas fa-bars fa-lg"></i>
            </button>
            <h5 class="d-inline mb-0"><?= $pageTitle ?? 'لوحة التحكم' ?></h5>
        </div>
        <div class="d-flex align-items-center gap-3">
            <a href="/" target="_blank" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-external-link-alt"></i> زيارة الموقع
            </a>
            <div class="dropdown">
                <button class="btn btn-link dropdown-toggle text-dark text-decoration-none" data-bs-toggle="dropdown">
                    <i class="fas fa-user-circle fa-lg"></i>
                    <?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-start">
                    <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i> الملف الشخصي</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> خروج</a></li>
                </ul>
            </div>
        </div>
    </div>
