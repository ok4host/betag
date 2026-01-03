<?php
/**
 * Helper Functions
 */

require_once __DIR__ . '/../config/db.php';

// =====================
// Security Functions
// =====================

function sanitize($input) {
    if (is_array($input)) {
        return array_map('sanitize', $input);
    }
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

function generateSlug($text) {
    // Transliterate Arabic to English-like slug
    $text = mb_strtolower($text, 'UTF-8');
    $text = preg_replace('/[^\p{L}\p{N}\s-]/u', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    $text = trim($text, '-');

    // If mostly Arabic, create timestamp-based slug
    if (preg_match('/[\x{0600}-\x{06FF}]/u', $text)) {
        return 'property-' . time() . '-' . rand(100, 999);
    }

    return $text ?: 'property-' . time();
}

function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// =====================
// Session Functions
// =====================

function startSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function isLoggedIn() {
    startSession();
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    startSession();
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function getCurrentUser() {
    if (!isLoggedIn()) return null;

    $stmt = db()->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /pages/login.html');
        exit;
    }
}

function requireAdmin() {
    if (!isAdmin()) {
        header('Location: /');
        exit;
    }
}

// =====================
// Database Helpers
// =====================

function getSettings() {
    static $settings = null;
    if ($settings === null) {
        $stmt = db()->query("SELECT `key`, `value` FROM settings");
        $settings = [];
        while ($row = $stmt->fetch()) {
            $settings[$row['key']] = $row['value'];
        }
    }
    return $settings;
}

function getSetting($key, $default = '') {
    $settings = getSettings();
    return $settings[$key] ?? $default;
}

function getSeoSettings($pageType) {
    $stmt = db()->prepare("SELECT * FROM seo_settings WHERE page_type = ?");
    $stmt->execute([$pageType]);
    return $stmt->fetch() ?: [];
}

// =====================
// Property Functions
// =====================

function getProperty($id) {
    $sql = "SELECT p.*, c.name_ar as category_name, c.slug as category_slug,
                   l.name_ar as location_name, l.slug as location_slug,
                   u.name as owner_name, u.phone as owner_phone
            FROM properties p
            JOIN categories c ON p.category_id = c.id
            JOIN locations l ON p.location_id = l.id
            JOIN users u ON p.user_id = u.id
            WHERE p.id = ? AND p.status = 'active'";

    $stmt = db()->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function getPropertyBySlug($slug) {
    $sql = "SELECT p.*, c.name_ar as category_name, c.slug as category_slug,
                   l.name_ar as location_name, l.slug as location_slug,
                   u.name as owner_name, u.phone as owner_phone
            FROM properties p
            JOIN categories c ON p.category_id = c.id
            JOIN locations l ON p.location_id = l.id
            JOIN users u ON p.user_id = u.id
            WHERE p.slug = ? AND p.status = 'active'";

    $stmt = db()->prepare($sql);
    $stmt->execute([$slug]);
    return $stmt->fetch();
}

function getFeaturedProperties($limit = 6) {
    $sql = "SELECT p.*, c.name_ar as category_name, l.name_ar as location_name
            FROM properties p
            JOIN categories c ON p.category_id = c.id
            JOIN locations l ON p.location_id = l.id
            WHERE p.status = 'active' AND p.is_featured = 1
            ORDER BY p.created_at DESC
            LIMIT ?";

    $stmt = db()->prepare($sql);
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}

function getLatestProperties($limit = 6) {
    $sql = "SELECT p.*, c.name_ar as category_name, l.name_ar as location_name
            FROM properties p
            JOIN categories c ON p.category_id = c.id
            JOIN locations l ON p.location_id = l.id
            WHERE p.status = 'active'
            ORDER BY p.created_at DESC
            LIMIT ?";

    $stmt = db()->prepare($sql);
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}

function searchProperties($filters = [], $page = 1, $perPage = 12) {
    $where = ["p.status = 'active'"];
    $params = [];

    if (!empty($filters['transaction_type'])) {
        $where[] = "p.transaction_type = ?";
        $params[] = $filters['transaction_type'];
    }

    if (!empty($filters['category'])) {
        $where[] = "c.slug = ?";
        $params[] = $filters['category'];
    }

    if (!empty($filters['location'])) {
        $where[] = "(l.slug = ? OR l.parent_id IN (SELECT id FROM locations WHERE slug = ?))";
        $params[] = $filters['location'];
        $params[] = $filters['location'];
    }

    if (!empty($filters['min_price'])) {
        $where[] = "p.price >= ?";
        $params[] = $filters['min_price'];
    }

    if (!empty($filters['max_price'])) {
        $where[] = "p.price <= ?";
        $params[] = $filters['max_price'];
    }

    if (!empty($filters['bedrooms'])) {
        $where[] = "p.bedrooms >= ?";
        $params[] = $filters['bedrooms'];
    }

    if (!empty($filters['q'])) {
        $where[] = "MATCH(p.title, p.description, p.address) AGAINST(? IN BOOLEAN MODE)";
        $params[] = $filters['q'] . '*';
    }

    $whereClause = implode(' AND ', $where);

    // Count total
    $countSql = "SELECT COUNT(*) FROM properties p
                 JOIN categories c ON p.category_id = c.id
                 JOIN locations l ON p.location_id = l.id
                 WHERE $whereClause";
    $stmt = db()->prepare($countSql);
    $stmt->execute($params);
    $total = $stmt->fetchColumn();

    // Get results
    $offset = ($page - 1) * $perPage;
    $orderBy = "p.is_featured DESC, p.created_at DESC";

    if (!empty($filters['sort'])) {
        switch ($filters['sort']) {
            case 'price_asc': $orderBy = "p.price ASC"; break;
            case 'price_desc': $orderBy = "p.price DESC"; break;
            case 'area_desc': $orderBy = "p.area DESC"; break;
            case 'newest': $orderBy = "p.created_at DESC"; break;
        }
    }

    $sql = "SELECT p.*, c.name_ar as category_name, c.slug as category_slug,
                   l.name_ar as location_name, l.slug as location_slug
            FROM properties p
            JOIN categories c ON p.category_id = c.id
            JOIN locations l ON p.location_id = l.id
            WHERE $whereClause
            ORDER BY $orderBy
            LIMIT ? OFFSET ?";

    $params[] = $perPage;
    $params[] = $offset;

    $stmt = db()->prepare($sql);
    $stmt->execute($params);

    return [
        'data' => $stmt->fetchAll(),
        'total' => $total,
        'pages' => ceil($total / $perPage),
        'current_page' => $page
    ];
}

function incrementPropertyViews($id) {
    $stmt = db()->prepare("UPDATE properties SET views = views + 1 WHERE id = ?");
    $stmt->execute([$id]);
}

// =====================
// Location Functions
// =====================

function getLocations($parentId = null, $type = null) {
    $where = ["is_active = 1"];
    $params = [];

    if ($parentId !== null) {
        $where[] = "parent_id = ?";
        $params[] = $parentId;
    } else {
        $where[] = "parent_id IS NULL";
    }

    if ($type) {
        $where[] = "type = ?";
        $params[] = $type;
    }

    $sql = "SELECT * FROM locations WHERE " . implode(' AND ', $where) . " ORDER BY name_ar";
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getCategories() {
    $stmt = db()->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY sort_order");
    return $stmt->fetchAll();
}

// =====================
// Lead Functions
// =====================

function createLead($data) {
    $sql = "INSERT INTO leads (property_id, name, phone, email, message, purpose, preferred_area, source)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = db()->prepare($sql);
    return $stmt->execute([
        $data['property_id'] ?? null,
        $data['name'],
        $data['phone'],
        $data['email'] ?? null,
        $data['message'] ?? null,
        $data['purpose'] ?? 'general',
        $data['preferred_area'] ?? null,
        $data['source'] ?? 'website'
    ]);
}

// =====================
// Image Functions
// =====================

function uploadImage($file, $folder = 'properties') {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Upload error'];
    }

    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'error' => 'File too large'];
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ALLOWED_EXTENSIONS)) {
        return ['success' => false, 'error' => 'Invalid file type'];
    }

    $filename = uniqid() . '_' . time() . '.' . $ext;
    $uploadPath = UPLOAD_PATH . $folder . '/' . $filename;

    if (!is_dir(dirname($uploadPath))) {
        mkdir(dirname($uploadPath), 0755, true);
    }

    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        return ['success' => true, 'filename' => $filename, 'path' => '/uploads/' . $folder . '/' . $filename];
    }

    return ['success' => false, 'error' => 'Failed to save file'];
}

// =====================
// Format Functions
// =====================

function formatPrice($price, $currency = 'EGP') {
    return number_format($price, 0) . ' ' . ($currency === 'EGP' ? 'ج.م' : '$');
}

function formatDate($date, $format = 'd/m/Y') {
    return date($format, strtotime($date));
}

function timeAgo($datetime) {
    $time = strtotime($datetime);
    $diff = time() - $time;

    if ($diff < 60) return 'الآن';
    if ($diff < 3600) return floor($diff / 60) . ' دقيقة';
    if ($diff < 86400) return floor($diff / 3600) . ' ساعة';
    if ($diff < 2592000) return floor($diff / 86400) . ' يوم';
    return formatDate($datetime);
}

// =====================
// Response Functions
// =====================

function jsonResponse($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function redirect($url) {
    header("Location: $url");
    exit;
}

// =====================
// CSRF Protection
// =====================

function generateCSRF() {
    startSession();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRF($token) {
    startSession();
    if (empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

function csrfField() {
    return '<input type="hidden" name="csrf_token" value="' . generateCSRF() . '">';
}

// =====================
// Rate Limiting
// =====================

function checkRateLimit($key, $limit = 60, $period = 60) {
    $cacheFile = sys_get_temp_dir() . "/rate_" . md5($key) . ".json";

    $data = [];
    if (file_exists($cacheFile)) {
        $data = json_decode(file_get_contents($cacheFile), true) ?: [];
    }

    $now = time();
    $data = array_filter($data, fn($t) => $t > $now - $period);

    if (count($data) >= $limit) {
        return false;
    }

    $data[] = $now;
    file_put_contents($cacheFile, json_encode($data));
    return true;
}

// =====================
// Validation Functions
// =====================

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validatePhone($phone) {
    // Egyptian phone format
    return preg_match('/^01[0-9]{9}$/', $phone);
}

function validateImage($file) {
    if (!is_array($file) || !isset($file['tmp_name'])) {
        return false;
    }

    $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    return in_array($mimeType, $allowed);
}

// =====================
// Slug Functions
// =====================

function createSlug($text) {
    // Transliterate Arabic
    $text = mb_strtolower(trim($text), 'UTF-8');
    $text = preg_replace('/[^\p{L}\p{N}\s-]/u', '', $text);
    $text = preg_replace('/[\s_]+/', '-', $text);
    $text = preg_replace('/-+/', '-', $text);
    $text = trim($text, '-');

    // If Arabic, create unique slug
    if (preg_match('/[\x{0600}-\x{06FF}]/u', $text)) {
        return substr(md5($text . time()), 0, 8) . '-' . time();
    }

    return $text ?: 'item-' . time();
}

function uniqueSlug($table, $text, $excludeId = null) {
    $slug = createSlug($text);
    $originalSlug = $slug;
    $counter = 1;

    while (true) {
        $sql = "SELECT id FROM $table WHERE slug = ?";
        $params = [$slug];

        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }

        $stmt = db()->prepare($sql);
        $stmt->execute($params);

        if (!$stmt->fetch()) {
            return $slug;
        }

        $slug = $originalSlug . '-' . $counter;
        $counter++;
    }
}
