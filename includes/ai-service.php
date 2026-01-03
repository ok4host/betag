<?php
/**
 * AI Content Generation Service
 * Supports OpenAI, Anthropic, and other providers
 */

require_once __DIR__ . '/functions.php';

class AIService {
    private $pdo;
    private $settings;
    private $provider;
    private $apiKey;
    private $model;
    private $endpoint;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
        $this->loadSettings();
    }

    private function loadSettings() {
        $stmt = $this->pdo->query("SELECT * FROM ai_settings WHERE is_active = 1 LIMIT 1");
        $this->settings = $stmt->fetch(PDO::FETCH_ASSOC) ?: [
            'provider' => 'openai',
            'model' => 'gpt-4',
            'default_temperature' => 0.7,
            'max_tokens_per_request' => 2000
        ];

        $this->provider = $this->settings['provider'] ?? 'openai';
        $this->apiKey = $this->settings['api_key'] ?? '';
        $this->model = $this->settings['model'] ?? 'gpt-4';
        $this->endpoint = $this->settings['endpoint'] ?? null;
    }

    /**
     * Generate content using AI
     */
    public function generate($promptSlug, $variables = [], $options = []) {
        // Get prompt template
        $prompt = $this->getPrompt($promptSlug);
        if (!$prompt) {
            return ['success' => false, 'error' => 'Prompt not found'];
        }

        // Build the prompt with variables
        $fullPrompt = $this->buildPrompt($prompt['prompt_template'], $variables);
        $systemInstructions = $prompt['system_instructions'] ?? '';

        // Options
        $temperature = $options['temperature'] ?? $prompt['temperature'] ?? $this->settings['default_temperature'];
        $maxTokens = $options['max_tokens'] ?? $prompt['max_tokens'] ?? $this->settings['max_tokens_per_request'];

        // Call AI provider
        $startTime = microtime(true);
        $result = $this->callAI($fullPrompt, $systemInstructions, $temperature, $maxTokens);
        $generationTime = round(microtime(true) - $startTime, 2);

        if ($result['success']) {
            // Update usage count
            $this->updatePromptUsage($prompt['id']);

            // Save generated content if requested
            if (!empty($options['save'])) {
                $this->saveContent([
                    'prompt_id' => $prompt['id'],
                    'entity_type' => $options['entity_type'] ?? 'custom',
                    'entity_id' => $options['entity_id'] ?? null,
                    'input_data' => $variables,
                    'generated_content' => $result['content'],
                    'content_type' => $options['content_type'] ?? 'description',
                    'tokens_used' => $result['tokens_used'] ?? 0,
                    'generation_time' => $generationTime,
                    'created_by' => $options['user_id'] ?? null
                ]);
            }

            return [
                'success' => true,
                'content' => $result['content'],
                'tokens_used' => $result['tokens_used'] ?? 0,
                'generation_time' => $generationTime
            ];
        }

        return $result;
    }

    /**
     * Call AI provider API
     */
    private function callAI($prompt, $systemInstructions, $temperature, $maxTokens) {
        if (empty($this->apiKey)) {
            return ['success' => false, 'error' => 'API key not configured'];
        }

        switch ($this->provider) {
            case 'openai':
                return $this->callOpenAI($prompt, $systemInstructions, $temperature, $maxTokens);
            case 'anthropic':
                return $this->callAnthropic($prompt, $systemInstructions, $temperature, $maxTokens);
            default:
                return ['success' => false, 'error' => 'Unsupported provider'];
        }
    }

    /**
     * Call OpenAI API
     */
    private function callOpenAI($prompt, $systemInstructions, $temperature, $maxTokens) {
        $url = $this->endpoint ?: 'https://api.openai.com/v1/chat/completions';

        $messages = [];
        if (!empty($systemInstructions)) {
            $messages[] = ['role' => 'system', 'content' => $systemInstructions];
        }
        $messages[] = ['role' => 'user', 'content' => $prompt];

        $data = [
            'model' => $this->model,
            'messages' => $messages,
            'temperature' => (float)$temperature,
            'max_tokens' => (int)$maxTokens
        ];

        $response = $this->httpRequest($url, $data, [
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json'
        ]);

        if ($response['success']) {
            $body = json_decode($response['body'], true);
            if (isset($body['choices'][0]['message']['content'])) {
                return [
                    'success' => true,
                    'content' => $body['choices'][0]['message']['content'],
                    'tokens_used' => $body['usage']['total_tokens'] ?? 0
                ];
            }
            return ['success' => false, 'error' => $body['error']['message'] ?? 'Unknown error'];
        }

        return ['success' => false, 'error' => $response['error']];
    }

    /**
     * Call Anthropic API
     */
    private function callAnthropic($prompt, $systemInstructions, $temperature, $maxTokens) {
        $url = $this->endpoint ?: 'https://api.anthropic.com/v1/messages';

        $data = [
            'model' => $this->model ?: 'claude-3-opus-20240229',
            'max_tokens' => (int)$maxTokens,
            'messages' => [['role' => 'user', 'content' => $prompt]]
        ];

        if (!empty($systemInstructions)) {
            $data['system'] = $systemInstructions;
        }

        $response = $this->httpRequest($url, $data, [
            'x-api-key: ' . $this->apiKey,
            'anthropic-version: 2023-06-01',
            'Content-Type: application/json'
        ]);

        if ($response['success']) {
            $body = json_decode($response['body'], true);
            if (isset($body['content'][0]['text'])) {
                return [
                    'success' => true,
                    'content' => $body['content'][0]['text'],
                    'tokens_used' => ($body['usage']['input_tokens'] ?? 0) + ($body['usage']['output_tokens'] ?? 0)
                ];
            }
            return ['success' => false, 'error' => $body['error']['message'] ?? 'Unknown error'];
        }

        return ['success' => false, 'error' => $response['error']];
    }

    /**
     * HTTP request helper
     */
    private function httpRequest($url, $data, $headers) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 120
        ]);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($error) {
            return ['success' => false, 'error' => $error];
        }

        if ($httpCode >= 400) {
            $body = json_decode($response, true);
            return ['success' => false, 'error' => $body['error']['message'] ?? "HTTP $httpCode"];
        }

        return ['success' => true, 'body' => $response];
    }

    /**
     * Build prompt with variables
     */
    private function buildPrompt($template, $variables) {
        foreach ($variables as $key => $value) {
            if (is_array($value)) {
                $value = implode('، ', $value);
            }
            $template = str_replace('{' . $key . '}', $value, $template);
        }
        return $template;
    }

    /**
     * Get prompt by slug
     */
    public function getPrompt($slug) {
        $stmt = $this->pdo->prepare("SELECT * FROM ai_prompts WHERE slug = ? AND is_active = 1");
        $stmt->execute([$slug]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get all prompts
     */
    public function getAllPrompts($category = null) {
        $sql = "SELECT * FROM ai_prompts WHERE is_active = 1";
        $params = [];

        if ($category) {
            $sql .= " AND category = ?";
            $params[] = $category;
        }

        $sql .= " ORDER BY category, name";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Save or update prompt
     */
    public function savePrompt($data) {
        if (!empty($data['id'])) {
            $stmt = $this->pdo->prepare("
                UPDATE ai_prompts SET
                    name = ?, slug = ?, category = ?, prompt_template = ?,
                    system_instructions = ?, variables = ?, model = ?,
                    temperature = ?, max_tokens = ?, is_active = ?,
                    updated_at = NOW()
                WHERE id = ?
            ");
            return $stmt->execute([
                $data['name'], $data['slug'], $data['category'], $data['prompt_template'],
                $data['system_instructions'] ?? null, json_encode($data['variables'] ?? []),
                $data['model'] ?? 'gpt-4', $data['temperature'] ?? 0.7,
                $data['max_tokens'] ?? 1000, $data['is_active'] ?? 1, $data['id']
            ]);
        } else {
            $stmt = $this->pdo->prepare("
                INSERT INTO ai_prompts (name, slug, category, prompt_template, system_instructions, variables, model, temperature, max_tokens, is_active)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            return $stmt->execute([
                $data['name'], $data['slug'], $data['category'], $data['prompt_template'],
                $data['system_instructions'] ?? null, json_encode($data['variables'] ?? []),
                $data['model'] ?? 'gpt-4', $data['temperature'] ?? 0.7,
                $data['max_tokens'] ?? 1000, $data['is_active'] ?? 1
            ]);
        }
    }

    /**
     * Delete prompt
     */
    public function deletePrompt($id) {
        $stmt = $this->pdo->prepare("DELETE FROM ai_prompts WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Update prompt usage count
     */
    private function updatePromptUsage($promptId) {
        $stmt = $this->pdo->prepare("UPDATE ai_prompts SET usage_count = usage_count + 1 WHERE id = ?");
        $stmt->execute([$promptId]);
    }

    /**
     * Save generated content
     */
    private function saveContent($data) {
        $stmt = $this->pdo->prepare("
            INSERT INTO ai_content
            (prompt_id, entity_type, entity_id, input_data, generated_content, content_type, tokens_used, generation_time, created_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['prompt_id'], $data['entity_type'], $data['entity_id'],
            json_encode($data['input_data']), $data['generated_content'],
            $data['content_type'], $data['tokens_used'], $data['generation_time'],
            $data['created_by']
        ]);
    }

    /**
     * Get generated content history
     */
    public function getContentHistory($entityType = null, $entityId = null, $limit = 50) {
        $sql = "SELECT c.*, p.name as prompt_name
                FROM ai_content c
                LEFT JOIN ai_prompts p ON c.prompt_id = p.id
                WHERE 1=1";
        $params = [];

        if ($entityType) {
            $sql .= " AND c.entity_type = ?";
            $params[] = $entityType;
        }
        if ($entityId) {
            $sql .= " AND c.entity_id = ?";
            $params[] = $entityId;
        }

        $sql .= " ORDER BY c.created_at DESC LIMIT ?";
        $params[] = $limit;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Generate property description
     */
    public function generatePropertyDescription($propertyId) {
        // Get property data
        $stmt = $this->pdo->prepare("
            SELECT p.*, c.name_ar as category_name, l.name_ar as location_name
            FROM properties p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN locations l ON p.location_id = l.id
            WHERE p.id = ?
        ");
        $stmt->execute([$propertyId]);
        $property = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$property) {
            return ['success' => false, 'error' => 'Property not found'];
        }

        $amenities = json_decode($property['amenities'] ?? '[]', true);
        $finishingLabels = [
            'super-lux' => 'سوبر لوكس',
            'lux' => 'لوكس',
            'semi-finished' => 'نصف تشطيب',
            'not-finished' => 'بدون تشطيب'
        ];

        return $this->generate('property-description', [
            'property_type' => $property['category_name'],
            'location' => $property['location_name'],
            'area' => $property['area'],
            'bedrooms' => $property['bedrooms'],
            'price' => number_format($property['price']),
            'finishing' => $finishingLabels[$property['finishing']] ?? $property['finishing'],
            'amenities' => $amenities
        ], [
            'save' => true,
            'entity_type' => 'property',
            'entity_id' => $propertyId,
            'content_type' => 'description'
        ]);
    }

    /**
     * Generate SEO meta tags for property
     */
    public function generatePropertySEO($propertyId) {
        $stmt = $this->pdo->prepare("
            SELECT p.*, c.name_ar as category_name, l.name_ar as location_name
            FROM properties p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN locations l ON p.location_id = l.id
            WHERE p.id = ?
        ");
        $stmt->execute([$propertyId]);
        $property = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$property) {
            return ['success' => false, 'error' => 'Property not found'];
        }

        return $this->generate('seo-meta-tags', [
            'title' => $property['title'],
            'property_type' => $property['category_name'],
            'location' => $property['location_name'],
            'price' => number_format($property['price'])
        ], [
            'save' => true,
            'entity_type' => 'property',
            'entity_id' => $propertyId,
            'content_type' => 'meta_title'
        ]);
    }

    /**
     * Update AI settings
     */
    public function updateSettings($data) {
        $stmt = $this->pdo->prepare("
            UPDATE ai_settings SET
                provider = ?, api_key = ?, model = ?, endpoint = ?,
                default_temperature = ?, max_tokens_per_request = ?,
                monthly_budget = ?, is_active = ?, updated_at = NOW()
            WHERE id = 1
        ");
        return $stmt->execute([
            $data['provider'], $data['api_key'], $data['model'],
            $data['endpoint'] ?? null, $data['default_temperature'] ?? 0.7,
            $data['max_tokens_per_request'] ?? 2000, $data['monthly_budget'] ?? null,
            $data['is_active'] ?? 1
        ]);
    }

    /**
     * Get AI settings
     */
    public function getSettings() {
        return $this->settings;
    }
}
