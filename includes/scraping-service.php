<?php
/**
 * Data Scraping & Import Service
 */

require_once __DIR__ . '/functions.php';

class ScrapingService {
    private $pdo;
    private $aiService;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    /**
     * Get AI service instance
     */
    private function getAI() {
        if (!$this->aiService) {
            require_once __DIR__ . '/ai-service.php';
            $this->aiService = new AIService();
        }
        return $this->aiService;
    }

    /**
     * Get all sources
     */
    public function getSources($activeOnly = true) {
        $sql = "SELECT * FROM scraping_sources";
        if ($activeOnly) {
            $sql .= " WHERE is_active = 1";
        }
        $sql .= " ORDER BY name";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get source by ID
     */
    public function getSource($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM scraping_sources WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Save source
     */
    public function saveSource($data) {
        if (!empty($data['id'])) {
            $stmt = $this->pdo->prepare("
                UPDATE scraping_sources SET
                    name = ?, slug = ?, base_url = ?, source_type = ?,
                    scraping_config = ?, headers = ?, rate_limit = ?,
                    is_active = ?, updated_at = NOW()
                WHERE id = ?
            ");
            return $stmt->execute([
                $data['name'], $data['slug'], $data['base_url'], $data['source_type'],
                json_encode($data['scraping_config'] ?? []), json_encode($data['headers'] ?? []),
                $data['rate_limit'] ?? 5, $data['is_active'] ?? 1, $data['id']
            ]);
        } else {
            $stmt = $this->pdo->prepare("
                INSERT INTO scraping_sources (name, slug, base_url, source_type, scraping_config, headers, rate_limit, is_active)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            return $stmt->execute([
                $data['name'], $data['slug'], $data['base_url'], $data['source_type'],
                json_encode($data['scraping_config'] ?? []), json_encode($data['headers'] ?? []),
                $data['rate_limit'] ?? 5, $data['is_active'] ?? 1
            ]);
        }
    }

    /**
     * Import CSV data
     */
    public function importCSV($file, $sourceId, $mapping = []) {
        if (!file_exists($file)) {
            return ['success' => false, 'error' => 'File not found'];
        }

        $handle = fopen($file, 'r');
        if (!$handle) {
            return ['success' => false, 'error' => 'Cannot open file'];
        }

        // Get headers
        $headers = fgetcsv($handle);
        if (!$headers) {
            fclose($handle);
            return ['success' => false, 'error' => 'Empty file'];
        }

        // Default mapping if not provided
        if (empty($mapping)) {
            $mapping = $this->autoMapColumns($headers);
        }

        $imported = 0;
        $failed = 0;
        $errors = [];
        $rowNum = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNum++;

            try {
                $data = array_combine($headers, $row);
                $result = $this->saveScrapedData([
                    'source_id' => $sourceId,
                    'external_id' => $data[$mapping['external_id']] ?? null,
                    'data_type' => 'property',
                    'raw_data' => $data,
                    'parsed_data' => $this->parsePropertyData($data, $mapping)
                ]);

                if ($result) {
                    $imported++;
                } else {
                    $failed++;
                }
            } catch (Exception $e) {
                $failed++;
                $errors[] = "Row $rowNum: " . $e->getMessage();
            }
        }

        fclose($handle);

        return [
            'success' => true,
            'imported' => $imported,
            'failed' => $failed,
            'errors' => $errors
        ];
    }

    /**
     * Auto-map CSV columns
     */
    private function autoMapColumns($headers) {
        $mapping = [];
        $patterns = [
            'title' => ['title', 'عنوان', 'name', 'property_name'],
            'description' => ['description', 'وصف', 'desc', 'details'],
            'price' => ['price', 'سعر', 'cost', 'value'],
            'area' => ['area', 'مساحة', 'size', 'sqm', 'meter'],
            'bedrooms' => ['bedrooms', 'غرف', 'rooms', 'beds'],
            'bathrooms' => ['bathrooms', 'حمامات', 'baths'],
            'location' => ['location', 'موقع', 'address', 'city', 'area_name'],
            'property_type' => ['type', 'نوع', 'category', 'property_type'],
            'transaction_type' => ['transaction', 'بيع', 'sale', 'rent', 'for'],
            'phone' => ['phone', 'تليفون', 'mobile', 'contact'],
            'images' => ['images', 'صور', 'photos', 'gallery'],
            'external_id' => ['id', 'external_id', 'ref', 'reference']
        ];

        foreach ($headers as $header) {
            $headerLower = mb_strtolower(trim($header));
            foreach ($patterns as $field => $keywords) {
                foreach ($keywords as $keyword) {
                    if (strpos($headerLower, $keyword) !== false) {
                        $mapping[$field] = $header;
                        break 2;
                    }
                }
            }
        }

        return $mapping;
    }

    /**
     * Parse property data from raw data
     */
    private function parsePropertyData($data, $mapping) {
        $parsed = [];

        foreach ($mapping as $field => $column) {
            if (isset($data[$column])) {
                $value = trim($data[$column]);

                switch ($field) {
                    case 'price':
                    case 'area':
                        $parsed[$field] = (float)preg_replace('/[^\d.]/', '', $value);
                        break;
                    case 'bedrooms':
                    case 'bathrooms':
                        $parsed[$field] = (int)preg_replace('/[^\d]/', '', $value);
                        break;
                    case 'images':
                        $parsed[$field] = array_map('trim', explode(',', $value));
                        break;
                    case 'transaction_type':
                        $parsed[$field] = stripos($value, 'rent') !== false || stripos($value, 'إيجار') !== false ? 'rent' : 'sale';
                        break;
                    default:
                        $parsed[$field] = $value;
                }
            }
        }

        return $parsed;
    }

    /**
     * Save scraped data
     */
    public function saveScrapedData($data) {
        // Check for duplicates
        if (!empty($data['external_id'])) {
            $stmt = $this->pdo->prepare("
                SELECT id FROM scraped_data
                WHERE source_id = ? AND external_id = ?
            ");
            $stmt->execute([$data['source_id'], $data['external_id']]);
            if ($stmt->fetch()) {
                return false; // Duplicate
            }
        }

        $stmt = $this->pdo->prepare("
            INSERT INTO scraped_data (source_id, external_id, data_type, raw_data, parsed_data, status)
            VALUES (?, ?, ?, ?, ?, 'pending')
        ");
        return $stmt->execute([
            $data['source_id'],
            $data['external_id'] ?? null,
            $data['data_type'] ?? 'property',
            json_encode($data['raw_data']),
            json_encode($data['parsed_data'] ?? [])
        ]);
    }

    /**
     * Get scraped data
     */
    public function getScrapedData($sourceId = null, $status = null, $limit = 100) {
        $sql = "SELECT d.*, s.name as source_name
                FROM scraped_data d
                LEFT JOIN scraping_sources s ON d.source_id = s.id
                WHERE 1=1";
        $params = [];

        if ($sourceId) {
            $sql .= " AND d.source_id = ?";
            $params[] = $sourceId;
        }
        if ($status) {
            $sql .= " AND d.status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY d.scraped_at DESC LIMIT ?";
        $params[] = $limit;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Process scraped data and create properties
     */
    public function processScrapedData($dataId, $userId, $generateContent = false) {
        // Get scraped data
        $stmt = $this->pdo->prepare("SELECT * FROM scraped_data WHERE id = ?");
        $stmt->execute([$dataId]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return ['success' => false, 'error' => 'Data not found'];
        }

        $parsed = json_decode($data['parsed_data'], true);

        try {
            // Find or create location
            $locationId = $this->findOrCreateLocation($parsed['location'] ?? '');

            // Find or create category
            $categoryId = $this->findOrCreateCategory($parsed['property_type'] ?? 'apartment');

            // Generate AI description if requested
            $description = $parsed['description'] ?? '';
            if ($generateContent && empty($description)) {
                $aiResult = $this->getAI()->generate('property-description', [
                    'property_type' => $parsed['property_type'] ?? 'شقة',
                    'location' => $parsed['location'] ?? '',
                    'area' => $parsed['area'] ?? 0,
                    'bedrooms' => $parsed['bedrooms'] ?? 0,
                    'price' => number_format($parsed['price'] ?? 0),
                    'finishing' => $parsed['finishing'] ?? 'لوكس',
                    'amenities' => $parsed['amenities'] ?? []
                ]);
                if ($aiResult['success']) {
                    $description = $aiResult['content'];
                }
            }

            // Create property
            $stmt = $this->pdo->prepare("
                INSERT INTO properties
                (user_id, category_id, location_id, title, slug, description,
                 transaction_type, price, area, bedrooms, bathrooms, status, images)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?)
            ");

            $title = $parsed['title'] ?? 'عقار للبيع';
            $slug = $this->generateSlug($title);

            $stmt->execute([
                $userId,
                $categoryId,
                $locationId,
                $title,
                $slug,
                $description,
                $parsed['transaction_type'] ?? 'sale',
                $parsed['price'] ?? 0,
                $parsed['area'] ?? 0,
                $parsed['bedrooms'] ?? 0,
                $parsed['bathrooms'] ?? 1,
                json_encode($parsed['images'] ?? [])
            ]);

            $propertyId = $this->pdo->lastInsertId();

            // Update scraped data status
            $stmt = $this->pdo->prepare("
                UPDATE scraped_data SET
                    status = 'imported',
                    imported_entity_id = ?,
                    processed_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$propertyId, $dataId]);

            return ['success' => true, 'property_id' => $propertyId];

        } catch (Exception $e) {
            // Update status to failed
            $stmt = $this->pdo->prepare("
                UPDATE scraped_data SET
                    status = 'failed',
                    error_message = ?,
                    processed_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$e->getMessage(), $dataId]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Find or create location
     */
    private function findOrCreateLocation($name) {
        if (empty($name)) {
            return 1; // Default location
        }

        // Try to find existing
        $stmt = $this->pdo->prepare("SELECT id FROM locations WHERE name_ar LIKE ? OR name_en LIKE ? LIMIT 1");
        $stmt->execute(["%$name%", "%$name%"]);
        $location = $stmt->fetch();

        if ($location) {
            return $location['id'];
        }

        // Create new
        $slug = $this->generateSlug($name);
        $stmt = $this->pdo->prepare("INSERT INTO locations (name_ar, name_en, slug, type) VALUES (?, ?, ?, 'area')");
        $stmt->execute([$name, $name, $slug]);
        return $this->pdo->lastInsertId();
    }

    /**
     * Find or create category
     */
    private function findOrCreateCategory($type) {
        $typeMap = [
            'شقة' => 'apartment', 'apartment' => 'apartment',
            'فيلا' => 'villa', 'villa' => 'villa',
            'دوبلكس' => 'duplex', 'duplex' => 'duplex',
            'ستوديو' => 'studio', 'studio' => 'studio',
            'شاليه' => 'chalet', 'chalet' => 'chalet',
            'أرض' => 'land', 'land' => 'land',
            'محل' => 'commercial', 'commercial' => 'commercial',
            'مكتب' => 'office', 'office' => 'office'
        ];

        $slug = $typeMap[mb_strtolower($type)] ?? 'apartment';

        $stmt = $this->pdo->prepare("SELECT id FROM categories WHERE slug = ?");
        $stmt->execute([$slug]);
        $category = $stmt->fetch();

        return $category ? $category['id'] : 1;
    }

    /**
     * Generate unique slug
     */
    private function generateSlug($text) {
        $slug = preg_replace('/[^a-zA-Z0-9\x{0600}-\x{06FF}]+/u', '-', $text);
        $slug = trim($slug, '-');
        $slug = mb_strtolower($slug);

        // Add unique suffix
        $slug .= '-' . substr(uniqid(), -5);

        return $slug;
    }

    /**
     * Bulk process scraped data
     */
    public function bulkProcess($dataIds, $userId, $generateContent = false) {
        $results = ['success' => 0, 'failed' => 0, 'errors' => []];

        foreach ($dataIds as $dataId) {
            $result = $this->processScrapedData($dataId, $userId, $generateContent);
            if ($result['success']) {
                $results['success']++;
            } else {
                $results['failed']++;
                $results['errors'][] = "ID $dataId: " . $result['error'];
            }
        }

        return $results;
    }

    /**
     * Create scraping job
     */
    public function createJob($sourceId, $jobType = 'incremental', $parameters = [], $userId = null) {
        $stmt = $this->pdo->prepare("
            INSERT INTO scraping_jobs (source_id, job_type, parameters, status, created_by)
            VALUES (?, ?, ?, 'pending', ?)
        ");
        $stmt->execute([$sourceId, $jobType, json_encode($parameters), $userId]);
        return $this->pdo->lastInsertId();
    }

    /**
     * Get jobs
     */
    public function getJobs($sourceId = null, $status = null, $limit = 50) {
        $sql = "SELECT j.*, s.name as source_name
                FROM scraping_jobs j
                LEFT JOIN scraping_sources s ON j.source_id = s.id
                WHERE 1=1";
        $params = [];

        if ($sourceId) {
            $sql .= " AND j.source_id = ?";
            $params[] = $sourceId;
        }
        if ($status) {
            $sql .= " AND j.status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY j.created_at DESC LIMIT ?";
        $params[] = $limit;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Delete scraped data
     */
    public function deleteScrapedData($id) {
        $stmt = $this->pdo->prepare("DELETE FROM scraped_data WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Get statistics
     */
    public function getStats() {
        $stats = [];

        // Total scraped
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM scraped_data");
        $stats['total_scraped'] = $stmt->fetchColumn();

        // By status
        $stmt = $this->pdo->query("SELECT status, COUNT(*) as count FROM scraped_data GROUP BY status");
        $stats['by_status'] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        // By source
        $stmt = $this->pdo->query("
            SELECT s.name, COUNT(d.id) as count
            FROM scraping_sources s
            LEFT JOIN scraped_data d ON s.id = d.source_id
            GROUP BY s.id
        ");
        $stats['by_source'] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        return $stats;
    }
}
