<?php
/**
 * Export Leads to Excel/CSV
 */
require_once '../includes/functions.php';
requireAdmin();

$pdo = Database::getInstance()->getConnection();

// Get filter parameters
$status = $_GET['status'] ?? '';
$dateFrom = $_GET['date_from'] ?? '';
$dateTo = $_GET['date_to'] ?? '';
$purpose = $_GET['purpose'] ?? '';
$format = $_GET['format'] ?? 'csv';

// Build query
$sql = "SELECT
    l.id,
    l.name as 'الاسم',
    l.phone as 'الهاتف',
    l.email as 'البريد الإلكتروني',
    CASE l.purpose
        WHEN 'buy' THEN 'شراء'
        WHEN 'rent' THEN 'إيجار'
        ELSE 'استفسار عام'
    END as 'الغرض',
    l.preferred_area as 'المنطقة المفضلة',
    l.budget_min as 'الميزانية من',
    l.budget_max as 'الميزانية إلى',
    l.message as 'الرسالة',
    CASE l.status
        WHEN 'new' THEN 'جديد'
        WHEN 'contacted' THEN 'تم التواصل'
        WHEN 'qualified' THEN 'مؤهل'
        WHEN 'converted' THEN 'تم التحويل'
        WHEN 'closed' THEN 'مغلق'
        ELSE l.status
    END as 'الحالة',
    l.source as 'المصدر',
    l.notes as 'ملاحظات',
    p.title as 'العقار',
    l.created_at as 'تاريخ الإنشاء',
    l.updated_at as 'آخر تحديث'
FROM leads l
LEFT JOIN properties p ON l.property_id = p.id
WHERE 1=1";

$params = [];

if ($status) {
    $sql .= " AND l.status = ?";
    $params[] = $status;
}

if ($dateFrom) {
    $sql .= " AND DATE(l.created_at) >= ?";
    $params[] = $dateFrom;
}

if ($dateTo) {
    $sql .= " AND DATE(l.created_at) <= ?";
    $params[] = $dateTo;
}

if ($purpose) {
    $sql .= " AND l.purpose = ?";
    $params[] = $purpose;
}

$sql .= " ORDER BY l.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$leads = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($leads)) {
    header('Location: leads.php?error=no_data');
    exit;
}

// Generate filename
$filename = 'leads_' . date('Y-m-d_His');

if ($format === 'xlsx') {
    // Excel format using PhpSpreadsheet (if available) or fallback to HTML table
    $filename .= '.xlsx';

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    // Create HTML table that Excel can read
    echo '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            table { border-collapse: collapse; direction: rtl; }
            th, td { border: 1px solid #000; padding: 8px; text-align: right; }
            th { background-color: #4472C4; color: white; font-weight: bold; }
            tr:nth-child(even) { background-color: #D9E2F3; }
        </style>
    </head>
    <body>
        <table>';

    // Headers
    echo '<tr>';
    foreach (array_keys($leads[0]) as $header) {
        echo '<th>' . htmlspecialchars($header) . '</th>';
    }
    echo '</tr>';

    // Data
    foreach ($leads as $lead) {
        echo '<tr>';
        foreach ($lead as $value) {
            echo '<td>' . htmlspecialchars($value ?? '') . '</td>';
        }
        echo '</tr>';
    }

    echo '</table></body></html>';

} else {
    // CSV format
    $filename .= '.csv';

    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

    // Add BOM for Excel to recognize UTF-8
    echo "\xEF\xBB\xBF";

    $output = fopen('php://output', 'w');

    // Headers
    fputcsv($output, array_keys($leads[0]));

    // Data
    foreach ($leads as $lead) {
        fputcsv($output, $lead);
    }

    fclose($output);
}
