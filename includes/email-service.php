<?php
/**
 * Email Notification Service
 */

require_once __DIR__ . '/functions.php';

class EmailService {
    private $settings;
    private $fromEmail;
    private $fromName;

    public function __construct() {
        $this->settings = getSettings();
        $this->fromEmail = $this->settings['contact_email'] ?? 'noreply@betag.com';
        $this->fromName = $this->settings['site_name'] ?? 'بي تاج';
    }

    /**
     * Send email
     */
    public function send($to, $subject, $body, $isHtml = true) {
        $headers = [
            'From: ' . $this->fromName . ' <' . $this->fromEmail . '>',
            'Reply-To: ' . $this->fromEmail,
            'X-Mailer: PHP/' . phpversion(),
            'MIME-Version: 1.0'
        ];

        if ($isHtml) {
            $headers[] = 'Content-Type: text/html; charset=UTF-8';
            $body = $this->wrapHtmlTemplate($body);
        } else {
            $headers[] = 'Content-Type: text/plain; charset=UTF-8';
        }

        return mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $body, implode("\r\n", $headers));
    }

    /**
     * Wrap content in HTML template
     */
    private function wrapHtmlTemplate($content) {
        $siteName = htmlspecialchars($this->settings['site_name'] ?? 'بي تاج');
        $year = date('Y');

        return <<<HTML
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Arial, sans-serif; background-color: #f5f5f5; margin: 0; padding: 20px; direction: rtl; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { background: #2563eb; color: white; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 28px; }
        .content { padding: 30px; color: #333; line-height: 1.8; }
        .footer { background: #f9f9f9; padding: 20px; text-align: center; color: #666; font-size: 12px; border-top: 1px solid #eee; }
        .btn { display: inline-block; background: #2563eb; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 10px 0; }
        .btn:hover { background: #1d4ed8; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 12px; text-align: right; border-bottom: 1px solid #eee; }
        th { background: #f9f9f9; font-weight: bold; }
        .highlight { background: #fff3cd; padding: 15px; border-radius: 5px; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{$siteName}</h1>
        </div>
        <div class="content">
            {$content}
        </div>
        <div class="footer">
            <p>&copy; {$year} {$siteName} - جميع الحقوق محفوظة</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Send new lead notification to admin
     */
    public function sendNewLeadNotification($lead) {
        $adminEmail = $this->settings['contact_email'];
        if (!$adminEmail) return false;

        $subject = 'طلب جديد - ' . ($lead['name'] ?? 'زائر');

        $propertyInfo = '';
        if (!empty($lead['property_id'])) {
            $pdo = Database::getInstance()->getConnection();
            $stmt = $pdo->prepare("SELECT title, slug FROM properties WHERE id = ?");
            $stmt->execute([$lead['property_id']]);
            $property = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($property) {
                $propertyInfo = "<tr><th>العقار</th><td><a href='" . SITE_URL . "/property/{$property['slug']}'>{$property['title']}</a></td></tr>";
            }
        }

        $purposeLabels = [
            'buy' => 'شراء',
            'rent' => 'إيجار',
            'general' => 'استفسار عام'
        ];

        $content = <<<HTML
<h2>طلب جديد!</h2>
<p>تم استلام طلب جديد من الموقع:</p>

<table>
    <tr><th>الاسم</th><td>{$lead['name']}</td></tr>
    <tr><th>الهاتف</th><td><a href="tel:{$lead['phone']}">{$lead['phone']}</a></td></tr>
    <tr><th>البريد الإلكتروني</th><td>{$lead['email']}</td></tr>
    <tr><th>الغرض</th><td>{$purposeLabels[$lead['purpose'] ?? 'general']}</td></tr>
    {$propertyInfo}
    <tr><th>الرسالة</th><td>{$lead['message']}</td></tr>
</table>

<div class="highlight">
    <strong>ميزانية العميل:</strong>
    <?php if (!empty($lead['budget_min']) || !empty($lead['budget_max'])): ?>
    من <?= number_format($lead['budget_min'] ?? 0) ?> إلى <?= number_format($lead['budget_max'] ?? 0) ?> جنيه
    <?php else: ?>
    غير محدد
    <?php endif; ?>
</div>

<p style="text-align: center;">
    <a href="<?= SITE_URL ?>/admin/leads.php" class="btn">عرض جميع الطلبات</a>
</p>
HTML;

        return $this->send($adminEmail, $subject, $content);
    }

    /**
     * Send lead confirmation to customer
     */
    public function sendLeadConfirmation($lead) {
        if (empty($lead['email'])) return false;

        $subject = 'تم استلام طلبك - ' . ($this->settings['site_name'] ?? 'بي تاج');

        $content = <<<HTML
<h2>مرحباً {$lead['name']}!</h2>

<p>شكراً لتواصلك معنا. تم استلام طلبك بنجاح وسيقوم فريقنا بالتواصل معك في أقرب وقت.</p>

<div class="highlight">
    <strong>تفاصيل طلبك:</strong><br>
    الهاتف: {$lead['phone']}<br>
    الرسالة: {$lead['message']}
</div>

<p>إذا كان لديك أي استفسار عاجل، يمكنك التواصل معنا على:</p>
<ul>
    <li>الهاتف: {$this->settings['contact_phone']}</li>
    <li>واتساب: <a href="https://wa.me/{$this->settings['contact_whatsapp']}">اضغط هنا</a></li>
</ul>

<p style="text-align: center;">
    <a href="<?= SITE_URL ?>" class="btn">تصفح المزيد من العقارات</a>
</p>
HTML;

        return $this->send($lead['email'], $subject, $content);
    }

    /**
     * Send property approval notification
     */
    public function sendPropertyApproved($property, $userEmail) {
        if (!$userEmail) return false;

        $subject = 'تم قبول عقارك - ' . ($this->settings['site_name'] ?? 'بي تاج');
        $propertyUrl = SITE_URL . '/property/' . $property['slug'];

        $content = <<<HTML
<h2>تهانينا!</h2>

<p>تم مراجعة وقبول عقارك على منصتنا. عقارك الآن متاح للجميع.</p>

<div class="highlight">
    <strong>{$property['title']}</strong><br>
    السعر: <?= number_format($property['price']) ?> جنيه
</div>

<p style="text-align: center;">
    <a href="{$propertyUrl}" class="btn">عرض العقار</a>
</p>

<p>نتمنى لك التوفيق في إتمام الصفقة!</p>
HTML;

        return $this->send($userEmail, $subject, $content);
    }

    /**
     * Send password reset email
     */
    public function sendPasswordReset($email, $resetToken) {
        $resetUrl = SITE_URL . '/reset-password.php?token=' . $resetToken;
        $subject = 'إعادة تعيين كلمة المرور - ' . ($this->settings['site_name'] ?? 'بي تاج');

        $content = <<<HTML
<h2>إعادة تعيين كلمة المرور</h2>

<p>تلقينا طلباً لإعادة تعيين كلمة المرور الخاصة بحسابك.</p>

<p>إذا قمت بهذا الطلب، اضغط على الزر أدناه:</p>

<p style="text-align: center;">
    <a href="{$resetUrl}" class="btn">إعادة تعيين كلمة المرور</a>
</p>

<p><small>هذا الرابط صالح لمدة 24 ساعة فقط.</small></p>

<p>إذا لم تطلب إعادة تعيين كلمة المرور، يمكنك تجاهل هذه الرسالة.</p>
HTML;

        return $this->send($email, $subject, $content);
    }

    /**
     * Send welcome email
     */
    public function sendWelcome($email, $name) {
        $subject = 'مرحباً بك في ' . ($this->settings['site_name'] ?? 'بي تاج');

        $content = <<<HTML
<h2>مرحباً {$name}!</h2>

<p>شكراً لانضمامك إلى منصتنا. نحن سعداء بوجودك معنا.</p>

<p>الآن يمكنك:</p>
<ul>
    <li>البحث عن عقار أحلامك</li>
    <li>إضافة عقاراتك للبيع أو الإيجار</li>
    <li>حفظ العقارات المفضلة</li>
    <li>التواصل مع البائعين مباشرة</li>
</ul>

<p style="text-align: center;">
    <a href="<?= SITE_URL ?>/search" class="btn">ابدأ البحث الآن</a>
</p>
HTML;

        return $this->send($email, $subject, $content);
    }
}

/**
 * Helper function to send notification
 */
function sendEmailNotification($type, $data) {
    $email = new EmailService();

    switch ($type) {
        case 'new_lead':
            $email->sendNewLeadNotification($data);
            $email->sendLeadConfirmation($data);
            break;
        case 'property_approved':
            $email->sendPropertyApproved($data['property'], $data['email']);
            break;
        case 'password_reset':
            $email->sendPasswordReset($data['email'], $data['token']);
            break;
        case 'welcome':
            $email->sendWelcome($data['email'], $data['name']);
            break;
    }
}
