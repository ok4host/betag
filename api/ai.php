<?php
/**
 * AI Content Generation API
 */
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../includes/functions.php';
require_once '../includes/ai-service.php';

$ai = new AIService();
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

try {
    switch ($action) {

        // Generate content from prompt
        case 'generate':
            if ($method !== 'POST') {
                throw new Exception('Method not allowed', 405);
            }

            $data = json_decode(file_get_contents('php://input'), true);
            $promptSlug = $data['prompt'] ?? '';
            $variables = $data['variables'] ?? [];
            $options = $data['options'] ?? [];

            if (empty($promptSlug)) {
                throw new Exception('Prompt slug required', 400);
            }

            $result = $ai->generate($promptSlug, $variables, $options);
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
            break;

        // Generate property description
        case 'property-description':
            if ($method !== 'POST') {
                throw new Exception('Method not allowed', 405);
            }

            $data = json_decode(file_get_contents('php://input'), true);
            $propertyId = $data['property_id'] ?? 0;

            if ($propertyId) {
                $result = $ai->generatePropertyDescription($propertyId);
            } else {
                // Generate from provided data
                $result = $ai->generate('property-description', [
                    'property_type' => $data['property_type'] ?? 'شقة',
                    'location' => $data['location'] ?? '',
                    'area' => $data['area'] ?? 0,
                    'bedrooms' => $data['bedrooms'] ?? 0,
                    'price' => $data['price'] ?? 0,
                    'finishing' => $data['finishing'] ?? 'لوكس',
                    'amenities' => $data['amenities'] ?? []
                ]);
            }

            echo json_encode($result, JSON_UNESCAPED_UNICODE);
            break;

        // Generate SEO meta tags
        case 'seo-tags':
            if ($method !== 'POST') {
                throw new Exception('Method not allowed', 405);
            }

            $data = json_decode(file_get_contents('php://input'), true);

            if (!empty($data['property_id'])) {
                $result = $ai->generatePropertySEO($data['property_id']);
            } else {
                $result = $ai->generate('seo-meta-tags', [
                    'title' => $data['title'] ?? '',
                    'property_type' => $data['property_type'] ?? '',
                    'location' => $data['location'] ?? '',
                    'price' => $data['price'] ?? ''
                ]);
            }

            echo json_encode($result, JSON_UNESCAPED_UNICODE);
            break;

        // Generate area guide
        case 'area-guide':
            if ($method !== 'POST') {
                throw new Exception('Method not allowed', 405);
            }

            $data = json_decode(file_get_contents('php://input'), true);
            $areaName = $data['area_name'] ?? '';

            if (empty($areaName)) {
                throw new Exception('Area name required', 400);
            }

            $result = $ai->generate('area-guide', ['area_name' => $areaName], [
                'save' => true,
                'entity_type' => 'location',
                'content_type' => 'full_article'
            ]);

            echo json_encode($result, JSON_UNESCAPED_UNICODE);
            break;

        // Generate social media post
        case 'social-post':
            if ($method !== 'POST') {
                throw new Exception('Method not allowed', 405);
            }

            $data = json_decode(file_get_contents('php://input'), true);

            $result = $ai->generate('social-post', [
                'property_type' => $data['property_type'] ?? 'شقة',
                'location' => $data['location'] ?? '',
                'price' => $data['price'] ?? '',
                'features' => $data['features'] ?? []
            ]);

            echo json_encode($result, JSON_UNESCAPED_UNICODE);
            break;

        // Get all prompts
        case 'prompts':
            $category = $_GET['category'] ?? null;
            $prompts = $ai->getAllPrompts($category);

            // Remove sensitive data
            foreach ($prompts as &$p) {
                unset($p['system_instructions']);
            }

            echo json_encode(['success' => true, 'prompts' => $prompts], JSON_UNESCAPED_UNICODE);
            break;

        // Get content history
        case 'history':
            requireLogin();
            $entityType = $_GET['entity_type'] ?? null;
            $entityId = $_GET['entity_id'] ?? null;
            $history = $ai->getContentHistory($entityType, $entityId, 50);

            echo json_encode(['success' => true, 'history' => $history], JSON_UNESCAPED_UNICODE);
            break;

        default:
            echo json_encode([
                'success' => true,
                'endpoints' => [
                    'POST /api/ai.php?action=generate' => 'Generate content from prompt',
                    'POST /api/ai.php?action=property-description' => 'Generate property description',
                    'POST /api/ai.php?action=seo-tags' => 'Generate SEO meta tags',
                    'POST /api/ai.php?action=area-guide' => 'Generate area guide',
                    'POST /api/ai.php?action=social-post' => 'Generate social media post',
                    'GET /api/ai.php?action=prompts' => 'Get available prompts',
                    'GET /api/ai.php?action=history' => 'Get content history'
                ]
            ], JSON_UNESCAPED_UNICODE);
    }

} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
