# ملاحظات مهمة - منصة بي تاج العقارية

## 🚀 خطوات التشغيل

```bash
# 1. تأكد من إعداد قاعدة البيانات في config/database.php
# 2. افتح المتصفح على:
http://your-domain.com/install.php

# أو من سطر الأوامر:
php install.php
```

---

## 🔍 ملاحظات SEO

### ✅ ما تم تنفيذه:
1. **Meta Tags ديناميكية** - كل صفحة لها title, description, keywords من قاعدة البيانات
2. **Open Graph Tags** - للمشاركة على Facebook
3. **Twitter Cards** - للمشاركة على Twitter
4. **JSON-LD Schema** - Schema.org markup للعقارات
5. **Canonical URLs** - لمنع المحتوى المكرر
6. **Clean URLs** - روابط نظيفة بدون .php
7. **نظام SEO في الأدمن** - تعديل meta لكل صفحة

### ⚠️ يجب إضافتها:
1. **Sitemap.xml** - خريطة الموقع
2. **Robots.txt** - للتحكم في الزحف
3. **Breadcrumbs Schema** - للتنقل
4. **FAQ Schema** - للأسئلة الشائعة
5. **Organization Schema** - لمعلومات الشركة

### 📝 توصيات:
- أضف محتوى فريد لكل منطقة (Area Guides)
- استخدم AI لتوليد وصف فريد لكل عقار
- أضف صور بأسماء وصفية (alt tags)
- سرعة التحميل: استخدم CDN للصور

---

## 💻 ملاحظات برمجية

### ✅ ما تم تنفيذه:
1. **PDO مع Prepared Statements** - حماية من SQL Injection
2. **CSRF Protection** - جزئي (يحتاج تحسين)
3. **XSS Protection** - htmlspecialchars للمخرجات
4. **Password Hashing** - bcrypt للكلمات السرية
5. **Session Management** - إدارة جلسات آمنة
6. **Input Validation** - التحقق من المدخلات

### ⚠️ يجب إضافتها/تحسينها:

#### 1. CSRF Token (مهم جداً)
```php
// أضف في includes/functions.php
function generateCSRF() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRF($token) {
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

// استخدم في كل form:
<input type="hidden" name="csrf_token" value="<?= generateCSRF() ?>">
```

#### 2. Rate Limiting للـ API
```php
// أضف في api/*.php
function checkRateLimit($key, $limit = 60, $period = 60) {
    $cacheFile = sys_get_temp_dir() . "/rate_$key.json";
    $data = file_exists($cacheFile) ? json_decode(file_get_contents($cacheFile), true) : [];

    $now = time();
    $data = array_filter($data, fn($t) => $t > $now - $period);

    if (count($data) >= $limit) {
        return false;
    }

    $data[] = $now;
    file_put_contents($cacheFile, json_encode($data));
    return true;
}
```

#### 3. Error Logging
```php
// أضف في config/database.php
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');

// قم بإنشاء مجلد logs
mkdir('logs', 0755);
```

#### 4. Image Upload Validation
```php
// يجب التحقق من نوع الملف فعلياً
function validateImage($file) {
    $allowed = ['image/jpeg', 'image/png', 'image/webp'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    return in_array($mimeType, $allowed);
}
```

---

## 🐛 أخطاء محتملة وحلولها

### 1. خطأ في الاتصال بقاعدة البيانات
```
Error: SQLSTATE[HY000] [1045] Access denied
الحل: تأكد من بيانات الاتصال في config/database.php
```

### 2. صفحة 404 للروابط النظيفة
```
الحل: تأكد من تفعيل mod_rewrite في Apache
a2enmod rewrite
service apache2 restart
```

### 3. مشكلة في إرسال الإيميل
```
الحل: استخدم SMTP بدلاً من mail()
أو استخدم خدمة مثل SendGrid/Mailgun
```

### 4. مشكلة في رفع الصور
```
الحل: تأكد من:
- صلاحيات مجلد uploads (755)
- upload_max_filesize في php.ini
- post_max_size في php.ini
```

---

## 📁 هيكل الملفات

```
betag/
├── admin/                  # لوحة التحكم
│   ├── ai-prompts.php     # إدارة البرومبتات
│   ├── ai-settings.php    # إعدادات AI
│   ├── leads.php          # إدارة الطلبات
│   ├── properties.php     # إدارة العقارات
│   ├── scraping.php       # استيراد البيانات
│   ├── seo.php            # إعدادات SEO
│   └── settings.php       # الإعدادات العامة
│
├── api/                    # نقاط API
│   ├── ai.php             # توليد المحتوى
│   ├── favorites.php      # المفضلة
│   ├── leads.php          # الطلبات
│   └── properties.php     # العقارات
│
├── config/                 # الإعدادات
│   ├── database.php       # إعدادات قاعدة البيانات
│   ├── schema.sql         # الجداول الأساسية
│   └── schema_ai.sql      # جداول AI
│
├── includes/               # ملفات مشتركة
│   ├── ai-service.php     # خدمة AI
│   ├── email-service.php  # خدمة الإيميل
│   ├── functions.php      # الدوال المساعدة
│   ├── header.php         # Header
│   ├── footer.php         # Footer
│   └── property-card.php  # بطاقة العقار
│
├── pages/                  # الصفحات
│   ├── search.php         # البحث
│   ├── property.php       # تفاصيل العقار
│   ├── favorites.php      # المفضلة
│   └── 404.php            # صفحة الخطأ
│
├── index.php               # الصفحة الرئيسية
├── install.php             # سكريبت التثبيت
└── .htaccess               # إعدادات Apache
```

---

## ✨ ميزات إضافية مقترحة

1. **نظام Cache** - لتسريع الموقع
2. **PWA Support** - تطبيق ويب تقدمي
3. **Push Notifications** - إشعارات للمستخدمين
4. **Chat System** - محادثة مباشرة
5. **Property Comparison** - مقارنة العقارات
6. **Virtual Tours** - جولات افتراضية 360°
7. **Mortgage Calculator** - حاسبة التمويل
8. **Multi-language** - دعم لغات أخرى

---

## 📞 الدعم

للمساعدة أو الإبلاغ عن مشاكل:
- GitHub Issues
- Email: support@betag.com
