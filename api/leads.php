<?php
/**
 * API - Leads (Contact Form Submissions)
 */
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'error' => 'Method not allowed'], 405);
}

// Get input
$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

// Validate
$name = sanitize($input['name'] ?? '');
$phone = sanitize($input['phone'] ?? '');
$email = sanitize($input['email'] ?? '');
$message = sanitize($input['message'] ?? '');
$purpose = sanitize($input['purpose'] ?? 'general');
$propertyId = isset($input['property_id']) ? (int)$input['property_id'] : null;
$preferredArea = sanitize($input['preferred_area'] ?? '');
$source = sanitize($input['source'] ?? 'website');

// Validation
if (empty($name) || empty($phone)) {
    jsonResponse(['success' => false, 'error' => 'الاسم ورقم الهاتف مطلوبان'], 400);
}

// Validate phone format (Egyptian)
if (!preg_match('/^01[0-9]{9}$/', $phone)) {
    jsonResponse(['success' => false, 'error' => 'رقم الهاتف غير صحيح'], 400);
}

// Validate email if provided
if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(['success' => false, 'error' => 'البريد الإلكتروني غير صحيح'], 400);
}

// Check for duplicate (same phone in last hour)
$stmt = db()->prepare("SELECT id FROM leads WHERE phone = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)");
$stmt->execute([$phone]);
if ($stmt->fetch()) {
    jsonResponse(['success' => false, 'error' => 'تم استلام طلبك بالفعل، سنتواصل معك قريباً'], 429);
}

// Create lead
try {
    $sql = "INSERT INTO leads (property_id, name, phone, email, message, purpose, preferred_area, source)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = db()->prepare($sql);
    $result = $stmt->execute([
        $propertyId,
        $name,
        $phone,
        $email ?: null,
        $message ?: null,
        $purpose,
        $preferredArea ?: null,
        $source
    ]);

    if ($result) {
        $leadId = db()->lastInsertId();

        // TODO: Send notification email to admin
        // TODO: Send SMS/WhatsApp confirmation to user

        jsonResponse([
            'success' => true,
            'message' => 'تم استلام طلبك بنجاح! سنتواصل معك قريباً',
            'lead_id' => $leadId
        ]);
    } else {
        jsonResponse(['success' => false, 'error' => 'حدث خطأ، يرجى المحاولة مرة أخرى'], 500);
    }
} catch (Exception $e) {
    jsonResponse(['success' => false, 'error' => 'حدث خطأ في النظام'], 500);
}
