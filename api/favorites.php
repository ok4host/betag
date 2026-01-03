<?php
/**
 * Favorites API
 */
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../includes/functions.php';

startSession();

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

try {
    $pdo = Database::getInstance()->getConnection();

    // Check if user is logged in
    if (!isLoggedIn()) {
        throw new Exception('يجب تسجيل الدخول أولاً', 401);
    }

    $userId = $_SESSION['user_id'];

    switch ($action) {

        // Add to favorites
        case 'add':
            if ($method !== 'POST') {
                throw new Exception('Method not allowed', 405);
            }

            $data = json_decode(file_get_contents('php://input'), true);
            $propertyId = (int)($data['property_id'] ?? 0);

            if (!$propertyId) {
                throw new Exception('Property ID required', 400);
            }

            // Check if property exists
            $stmt = $pdo->prepare("SELECT id FROM properties WHERE id = ?");
            $stmt->execute([$propertyId]);
            if (!$stmt->fetch()) {
                throw new Exception('العقار غير موجود', 404);
            }

            // Check if already in favorites
            $stmt = $pdo->prepare("SELECT id FROM favorites WHERE user_id = ? AND property_id = ?");
            $stmt->execute([$userId, $propertyId]);

            if ($stmt->fetch()) {
                echo json_encode(['success' => true, 'message' => 'موجود بالفعل في المفضلة']);
                break;
            }

            // Add to favorites
            $stmt = $pdo->prepare("INSERT INTO favorites (user_id, property_id) VALUES (?, ?)");
            $stmt->execute([$userId, $propertyId]);

            // Get updated count
            $count = getFavoritesCount($userId);

            echo json_encode([
                'success' => true,
                'message' => 'تمت الإضافة للمفضلة',
                'count' => $count
            ], JSON_UNESCAPED_UNICODE);
            break;

        // Remove from favorites
        case 'remove':
            if ($method !== 'POST' && $method !== 'DELETE') {
                throw new Exception('Method not allowed', 405);
            }

            $data = json_decode(file_get_contents('php://input'), true);
            $propertyId = (int)($data['property_id'] ?? $_GET['property_id'] ?? 0);

            if (!$propertyId) {
                throw new Exception('Property ID required', 400);
            }

            $stmt = $pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND property_id = ?");
            $stmt->execute([$userId, $propertyId]);

            $count = getFavoritesCount($userId);

            echo json_encode([
                'success' => true,
                'message' => 'تمت الإزالة من المفضلة',
                'count' => $count
            ], JSON_UNESCAPED_UNICODE);
            break;

        // Toggle favorite
        case 'toggle':
            if ($method !== 'POST') {
                throw new Exception('Method not allowed', 405);
            }

            $data = json_decode(file_get_contents('php://input'), true);
            $propertyId = (int)($data['property_id'] ?? 0);

            if (!$propertyId) {
                throw new Exception('Property ID required', 400);
            }

            // Check if exists
            $stmt = $pdo->prepare("SELECT id FROM favorites WHERE user_id = ? AND property_id = ?");
            $stmt->execute([$userId, $propertyId]);

            if ($stmt->fetch()) {
                // Remove
                $stmt = $pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND property_id = ?");
                $stmt->execute([$userId, $propertyId]);
                $isFavorite = false;
                $message = 'تمت الإزالة من المفضلة';
            } else {
                // Add
                $stmt = $pdo->prepare("INSERT INTO favorites (user_id, property_id) VALUES (?, ?)");
                $stmt->execute([$userId, $propertyId]);
                $isFavorite = true;
                $message = 'تمت الإضافة للمفضلة';
            }

            $count = getFavoritesCount($userId);

            echo json_encode([
                'success' => true,
                'is_favorite' => $isFavorite,
                'message' => $message,
                'count' => $count
            ], JSON_UNESCAPED_UNICODE);
            break;

        // Get favorites list
        case 'list':
        default:
            $page = max(1, (int)($_GET['page'] ?? 1));
            $limit = min(50, max(1, (int)($_GET['limit'] ?? 20)));
            $offset = ($page - 1) * $limit;

            // Get total count
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM favorites WHERE user_id = ?");
            $stmt->execute([$userId]);
            $total = $stmt->fetchColumn();

            // Get favorites with property details
            $stmt = $pdo->prepare("
                SELECT
                    f.id as favorite_id,
                    f.created_at as added_at,
                    p.*,
                    c.name_ar as category_name,
                    l.name_ar as location_name
                FROM favorites f
                JOIN properties p ON f.property_id = p.id
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN locations l ON p.location_id = l.id
                WHERE f.user_id = ? AND p.status = 'active'
                ORDER BY f.created_at DESC
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$userId, $limit, $offset]);
            $favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Format data
            foreach ($favorites as &$fav) {
                $fav['price_formatted'] = number_format($fav['price']) . ' جنيه';
                $fav['images'] = json_decode($fav['images'] ?? '[]', true);
                $fav['url'] = '/property/' . $fav['slug'];
            }

            echo json_encode([
                'success' => true,
                'favorites' => $favorites,
                'pagination' => [
                    'page' => $page,
                    'limit' => $limit,
                    'total' => $total,
                    'pages' => ceil($total / $limit)
                ]
            ], JSON_UNESCAPED_UNICODE);
            break;

        // Check if property is favorite
        case 'check':
            $propertyId = (int)($_GET['property_id'] ?? 0);

            if (!$propertyId) {
                throw new Exception('Property ID required', 400);
            }

            $stmt = $pdo->prepare("SELECT id FROM favorites WHERE user_id = ? AND property_id = ?");
            $stmt->execute([$userId, $propertyId]);

            echo json_encode([
                'success' => true,
                'is_favorite' => (bool)$stmt->fetch()
            ]);
            break;

        // Get favorites count
        case 'count':
            $count = getFavoritesCount($userId);
            echo json_encode(['success' => true, 'count' => $count]);
            break;
    }

} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

/**
 * Get favorites count for user
 */
function getFavoritesCount($userId) {
    $pdo = Database::getInstance()->getConnection();
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM favorites WHERE user_id = ?");
    $stmt->execute([$userId]);
    return (int)$stmt->fetchColumn();
}
