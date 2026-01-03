<?php
/**
 * Database Configuration
 * Change these values according to your cPanel MySQL settings
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'betag_db');
define('DB_USER', 'betag_user');
define('DB_PASS', 'your_password_here');
define('DB_CHARSET', 'utf8mb4');

// Site Configuration
define('SITE_URL', 'https://betag.com'); // Change to your domain
define('SITE_NAME', 'بي تاج');
define('ADMIN_EMAIL', 'admin@betag.com');

// Upload settings
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'webp']);

// Session settings
define('SESSION_LIFETIME', 86400 * 7); // 7 days

// Timezone
date_default_timezone_set('Africa/Cairo');

// Error reporting (set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);
