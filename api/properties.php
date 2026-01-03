<?php
/**
 * API - Properties Search
 */
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../includes/functions.php';

$action = $_GET['action'] ?? 'list';

switch ($action) {
    case 'list':
    case 'search':
        // Build filters from query params
        $filters = [
            'transaction_type' => sanitize($_GET['type'] ?? ''),
            'category' => sanitize($_GET['category'] ?? ''),
            'location' => sanitize($_GET['location'] ?? ''),
            'min_price' => (int)($_GET['min_price'] ?? 0),
            'max_price' => (int)($_GET['max_price'] ?? 0),
            'bedrooms' => (int)($_GET['bedrooms'] ?? 0),
            'q' => sanitize($_GET['q'] ?? ''),
            'sort' => sanitize($_GET['sort'] ?? 'newest'),
        ];

        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = min(50, max(1, (int)($_GET['per_page'] ?? 12)));

        $result = searchProperties($filters, $page, $perPage);

        // Format properties for JSON response
        $properties = array_map(function($p) {
            return [
                'id' => $p['id'],
                'title' => $p['title'],
                'slug' => $p['slug'],
                'price' => $p['price'],
                'price_formatted' => formatPrice($p['price']),
                'transaction_type' => $p['transaction_type'],
                'category' => $p['category_name'],
                'location' => $p['location_name'],
                'area' => $p['area'],
                'bedrooms' => $p['bedrooms'],
                'bathrooms' => $p['bathrooms'],
                'image' => $p['featured_image'] ? '/uploads/properties/' . $p['featured_image'] : null,
                'is_featured' => (bool)$p['is_featured'],
                'is_verified' => (bool)$p['is_verified'],
                'url' => '/property/' . $p['slug'],
            ];
        }, $result['data']);

        jsonResponse([
            'success' => true,
            'data' => $properties,
            'pagination' => [
                'total' => $result['total'],
                'pages' => $result['pages'],
                'current_page' => $result['current_page'],
                'per_page' => $perPage,
            ]
        ]);
        break;

    case 'featured':
        $limit = min(20, max(1, (int)($_GET['limit'] ?? 6)));
        $properties = getFeaturedProperties($limit);

        $data = array_map(function($p) {
            return [
                'id' => $p['id'],
                'title' => $p['title'],
                'slug' => $p['slug'],
                'price' => formatPrice($p['price']),
                'transaction_type' => $p['transaction_type'],
                'category' => $p['category_name'],
                'location' => $p['location_name'],
                'area' => $p['area'],
                'bedrooms' => $p['bedrooms'],
                'bathrooms' => $p['bathrooms'],
                'image' => $p['featured_image'] ? '/uploads/properties/' . $p['featured_image'] : null,
            ];
        }, $properties);

        jsonResponse(['success' => true, 'data' => $data]);
        break;

    case 'detail':
        $id = (int)($_GET['id'] ?? 0);
        $slug = sanitize($_GET['slug'] ?? '');

        if ($id) {
            $property = getProperty($id);
        } elseif ($slug) {
            $property = getPropertyBySlug($slug);
        } else {
            jsonResponse(['success' => false, 'error' => 'Property ID or slug required'], 400);
        }

        if (!$property) {
            jsonResponse(['success' => false, 'error' => 'Property not found'], 404);
        }

        // Increment views
        incrementPropertyViews($property['id']);

        // Parse JSON fields
        $property['amenities'] = json_decode($property['amenities'] ?? '[]', true);
        $property['images'] = json_decode($property['images'] ?? '[]', true);

        jsonResponse(['success' => true, 'data' => $property]);
        break;

    case 'track':
        $id = (int)($_GET['id'] ?? 0);
        $type = sanitize($_GET['type'] ?? '');

        if (!$id || !in_array($type, ['view', 'whatsapp', 'call'])) {
            jsonResponse(['success' => false, 'error' => 'Invalid request'], 400);
        }

        $column = $type === 'view' ? 'views' : ($type === 'whatsapp' ? 'whatsapp_clicks' : 'call_clicks');
        db()->prepare("UPDATE properties SET $column = $column + 1 WHERE id = ?")->execute([$id]);

        jsonResponse(['success' => true]);
        break;

    default:
        jsonResponse(['success' => false, 'error' => 'Unknown action'], 400);
}
